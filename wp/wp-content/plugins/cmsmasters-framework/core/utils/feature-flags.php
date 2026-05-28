<?php
namespace CmsmastersFramework\Core\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Feature Flags handler.
 *
 * Fetches feature flags from the API, caches them with a 3-level strategy
 * (transient → backup option → fail-open), tracks module enabled state,
 * and manages per-module grace periods when modules transition from
 * enabled to disabled.
 *
 * @since 1.0.17
 */
class Feature_Flags {

	const TRANSIENT_KEY = 'cmsmasters_feature_flags';
	const BACKUP_OPTION = 'cmsmasters_feature_flags_backup';
	const ENABLED_OPTION = 'cmsmasters_feature_flags_enabled';
	const GRACE_OPTION = 'cmsmasters_feature_flags_grace';

	const CACHE_TTL_SUCCESS = 24 * 60 * 60;
	const CACHE_TTL_FAILURE = 1 * 60 * 60;
	const BACKUP_MAX_AGE = 1 * 7 * 24 * 60 * 60;
	const DEFAULT_GRACE_PERIOD = 1 * 7 * 24 * 60 * 60;

	/** @var array|null */
	private static $flags = null;

	/** @var array|null */
	private static $variables = null;

	/** @var array|null */
	private static $enabled_modules = null;

	/** @var array|null */
	private static $grace_data = null;

	/** @var bool */
	private static $dirty = false;

	/** @var bool */
	private static $backup_dirty = false;

	/** @var bool */
	private static $shutdown_scheduled = false;

	/** @var bool|null */
	private static $is_migration = null;

	/**
	 * Check if a feature is enabled.
	 *
	 * @since 1.0.17
	 *
	 * @param string $feature_name Feature flag name.
	 *
	 * @return bool
	 */
	public static function is_enabled( $feature_name ) {
		$flags = self::get_flags();

		// No flag defined = no restriction.
		if ( ! isset( $flags[ $feature_name ] ) ) {
			self::track_module( $feature_name, true );
			self::end_grace( $feature_name );

			return true;
		}

		// All flagged modules require a valid license.
		if ( ! API_Requests::check_token_status() ) {
			return self::check_grace( $feature_name );
		}

		$flag = $flags[ $feature_name ];

		if ( self::evaluate_flag( $flag ) ) {
			self::track_module( $feature_name, true );
			self::end_grace( $feature_name );

			return true;
		}

		return self::check_grace( $feature_name );
	}

	/**
	 * Get per-module grace notices for rendering in admin.
	 *
	 * Eagerly evaluates all flags first so grace data is populated
	 * for features that haven't been checked via their natural code path
	 * (e.g. widget flags on non-Elementor admin pages).
	 *
	 * Returns only modules that have an active grace AND a notice configured.
	 *
	 * @since 1.0.17
	 *
	 * @return array Array of module_name => notice data (text, level, dismiss).
	 */
	public static function get_grace_notices() {
		self::evaluate_all_flags();

		$grace = self::get_grace_data();
		$now = time();
		$notices = array();

		foreach ( $grace as $module => $expiry ) {
			if ( $expiry <= $now ) {
				continue;
			}

			$notice = self::get_module_grace_notice( $module, $expiry );

			if ( null === $notice ) {
				continue;
			}

			$notices[ $module ] = $notice;
		}

		return $notices;
	}

	/**
	 * Evaluate all flags from the API to populate grace data.
	 *
	 * Ensures that flags which wouldn't otherwise be checked on the current
	 * request (e.g. widget flags outside Elementor editor) still have their
	 * grace state updated so notices can appear on any admin page.
	 *
	 * @since 1.0.17
	 */
	private static function evaluate_all_flags() {
		foreach ( self::get_flags() as $feature_name => $flag ) {
			self::is_enabled( $feature_name );
		}
	}

	/**
	 * Save accumulated state changes to the database.
	 *
	 * Called on shutdown to batch all writes.
	 *
	 * @since 1.0.17
	 */
	public static function save_state() {
		if ( self::$dirty ) {
			if ( null !== self::$enabled_modules ) {
				update_option( self::ENABLED_OPTION, self::$enabled_modules, false );
			}

			if ( null !== self::$grace_data ) {
				update_option( self::GRACE_OPTION, self::$grace_data, false );
			}
		}

		if ( self::$backup_dirty ) {
			self::update_backup();
		}
	}

	/**
	 * Clear all cached feature flags data.
	 *
	 * @since 1.0.17
	 */
	public static function clear_cache() {
		delete_transient( self::TRANSIENT_KEY );

		self::$flags = null;
		self::$variables = null;
	}

	/**
	 * Get feature flags from the 3-level cache.
	 *
	 * Level 1: transient (hot cache).
	 * Level 2: fresh API response.
	 * Level 3: backup option (up to 1 week old).
	 * Level 4: empty array (fail-open).
	 *
	 * @since 1.0.17
	 *
	 * @return array Flags array (empty on total failure — fail-open).
	 */
	private static function get_flags() {
		if ( null !== self::$flags ) {
			return self::$flags;
		}

		// Level 1: Transient.
		$cached = get_transient( self::TRANSIENT_KEY );

		if ( false !== $cached && is_array( $cached ) ) {
			self::$flags = $cached;

			return self::$flags;
		}

		// Level 2: API.
		$api_data = self::fetch_from_api();

		if ( null !== $api_data ) {
			self::$flags = $api_data['flags'];
			self::$variables = $api_data['variables'];

			set_transient( self::TRANSIENT_KEY, self::$flags, self::CACHE_TTL_SUCCESS );

			self::$backup_dirty = true;
			self::schedule_save();

			return self::$flags;
		}

		// Level 3: Backup (up to 1 week old).
		$backup = self::get_backup_data();

		if ( null !== $backup ) {
			self::$flags = $backup['flags'];
			self::$variables = isset( $backup['variables'] ) ? $backup['variables'] : array();

			set_transient( self::TRANSIENT_KEY, self::$flags, self::CACHE_TTL_FAILURE );

			return self::$flags;
		}

		// Total failure: fail-open.
		self::$flags = array();

		set_transient( self::TRANSIENT_KEY, self::$flags, self::CACHE_TTL_FAILURE );

		return self::$flags;
	}

	/**
	 * Fetch flags from the API via get_theme_config.
	 *
	 * @since 1.0.17
	 *
	 * @return array|null Array with 'flags' and 'variables' keys, or null on failure.
	 */
	private static function fetch_from_api() {
		$response = API_Requests::get_theme_config();

		if ( empty( $response ) || ! isset( $response['feature_flags'] ) || ! is_array( $response['feature_flags'] ) ) {
			return null;
		}

		return array(
			'flags' => $response['feature_flags'],
			'variables' => array(
				'{{themeforest_purchase_link}}' => isset( $response['themeforest_purchase_link'] ) ? $response['themeforest_purchase_link'] : '',
				'{{theme_name}}' => isset( $response['theme_name'] ) ? $response['theme_name'] : '',
				'{{upgrade_info_url}}' => isset( $response['upgrade_info_url'] ) ? $response['upgrade_info_url'] : '',
				'{{sales_link}}' => isset( $response['sales_link'] ) ? $response['sales_link'] : '',
			),
		);
	}

	/**
	 * Get backup data from the persistent option if it hasn't expired.
	 *
	 * @since 1.0.17
	 *
	 * @return array|null Backup data or null if missing / older than BACKUP_MAX_AGE.
	 */
	private static function get_backup_data() {
		$backup = get_option( self::BACKUP_OPTION, null );

		if (
			! is_array( $backup ) ||
			! isset( $backup['flags'] ) ||
			! isset( $backup['timestamp'] )
		) {
			return null;
		}

		if ( ( time() - $backup['timestamp'] ) > self::BACKUP_MAX_AGE ) {
			return null;
		}

		return $backup;
	}

	/**
	 * Persist current flags and variables to the backup option.
	 *
	 * @since 1.0.17
	 */
	private static function update_backup() {
		update_option( self::BACKUP_OPTION, array(
			'flags' => self::$flags,
			'variables' => self::$variables,
			'timestamp' => time(),
		), false );
	}

	/**
	 * Evaluate a single flag against its enabled state and conditions.
	 *
	 * @since 1.0.17
	 *
	 * @param array $flag Flag data (enabled, conditions).
	 *
	 * @return bool True if the flag passes.
	 */
	private static function evaluate_flag( $flag ) {
		if ( empty( $flag['enabled'] ) ) {
			return false;
		}

		if ( ! empty( $flag['conditions'] ) && is_array( $flag['conditions'] ) ) {
			return self::evaluate_conditions( $flag['conditions'] );
		}

		return true;
	}

	/**
	 * Evaluate all conditions (AND-based).
	 *
	 * @since 1.0.17
	 *
	 * @param array $conditions Conditions to evaluate.
	 *
	 * @return bool True if all conditions pass.
	 */
	private static function evaluate_conditions( $conditions ) {
		foreach ( $conditions as $key => $value ) {
			if ( ! self::evaluate_single_condition( $key, $value ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Evaluate a single condition against the current runtime state.
	 *
	 * Supports: theme_version, addon_version, framework_version, source_code.
	 * source_code accepts a string (exact match) or array (any match).
	 * Unknown keys pass through (return true).
	 *
	 * @since 1.0.17
	 *
	 * @param string $key   Condition key.
	 * @param mixed  $value Condition value (string, array, or version string).
	 *
	 * @return bool True if the condition passes.
	 */
	private static function evaluate_single_condition( $key, $value ) {
		switch ( $key ) {
			case 'theme_version':
				return defined( 'CMSMASTERS_THEME_VERSION' )
					&& self::compare_version( \CMSMASTERS_THEME_VERSION, $value );

			case 'addon_version':
				return defined( 'CMSMASTERS_ELEMENTOR_VERSION' )
					&& self::compare_version( \CMSMASTERS_ELEMENTOR_VERSION, $value );

			case 'framework_version':
				return defined( 'CMSMASTERS_FRAMEWORK_VERSION' )
					&& self::compare_version( \CMSMASTERS_FRAMEWORK_VERSION, $value );

			case 'source_code':
				$token_data = API_Requests::get_token_data( false );
				$local_source = isset( $token_data['source_code'] ) ? $token_data['source_code'] : '';

				if ( is_array( $value ) ) {
					return in_array( $local_source, $value, true );
				}

				return $local_source === $value;

			default:
				return true;
		}
	}

	/**
	 * Parse and compare a semver condition (e.g. ">=1.2.0").
	 *
	 * @since 1.0.17
	 *
	 * @param string $actual           Actual version to check.
	 * @param string $condition_string Condition with operator (e.g. ">=1.2.0").
	 *
	 * @return bool True if the version satisfies the condition.
	 */
	private static function compare_version( $actual, $condition_string ) {
		if ( preg_match( '/^([><=!]+)\s*(.+)$/', $condition_string, $matches ) ) {
			return version_compare( $actual, trim( $matches[2] ), $matches[1] );
		}

		return version_compare( $actual, $condition_string, '==' );
	}

	/**
	 * Check grace period for a feature that evaluated as disabled.
	 *
	 * If the feature was previously enabled (or this is a migration from
	 * a version without feature flags), a grace period is started.
	 *
	 * @since 1.0.17
	 *
	 * @param string $feature_name Feature name.
	 *
	 * @return bool True if the feature is in an active grace period.
	 */
	private static function check_grace( $feature_name ) {
		$grace = self::get_grace_data();
		$now = time();

		// Already in grace.
		if ( isset( $grace[ $feature_name ] ) ) {
			if ( $grace[ $feature_name ] > $now ) {
				return true;
			}

			// Grace expired.
			self::end_grace( $feature_name );
			self::track_module( $feature_name, false );

			return false;
		}

		// Not in grace — should we start one?
		$should_grace = false;

		if ( self::is_migration_request() ) {
			$should_grace = true;
		} else {
			$enabled = self::get_enabled_modules();
			$should_grace = ! empty( $enabled[ $feature_name ] );
		}

		if ( ! $should_grace ) {
			self::track_module( $feature_name, false );

			return false;
		}

		// Start grace with per-module period.
		return self::start_grace( $feature_name );
	}

	/**
	 * Start grace period for a feature.
	 *
	 * Returns false if the per-module grace_period is 0 (instant disable).
	 *
	 * @since 1.0.17
	 *
	 * @param string $feature_name Feature name.
	 *
	 * @return bool True if grace was started, false if instant disable.
	 */
	private static function start_grace( $feature_name ) {
		$period = self::get_module_grace_period( $feature_name );

		if ( 0 === $period ) {
			self::track_module( $feature_name, false );

			return false;
		}

		$grace = self::get_grace_data();
		$grace[ $feature_name ] = time() + $period;

		self::$grace_data = $grace;
		self::$dirty = true;
		self::schedule_save();

		return true;
	}

	/**
	 * End grace period for a feature.
	 *
	 * @since 1.0.17
	 *
	 * @param string $feature_name Feature name.
	 */
	private static function end_grace( $feature_name ) {
		$grace = self::get_grace_data();

		if ( ! isset( $grace[ $feature_name ] ) ) {
			return;
		}

		unset( $grace[ $feature_name ] );

		self::$grace_data = $grace;
		self::$dirty = true;
		self::schedule_save();
	}

	/**
	 * Track a module's enabled/disabled state in the persistent option.
	 *
	 * Used later by check_grace() to decide whether to start a grace period.
	 *
	 * @since 1.0.17
	 *
	 * @param string $feature_name Feature name.
	 * @param bool   $enabled      Whether the module is currently enabled.
	 */
	private static function track_module( $feature_name, $enabled ) {
		$modules = self::get_enabled_modules();
		$current = isset( $modules[ $feature_name ] ) ? $modules[ $feature_name ] : false;

		if ( $current === $enabled ) {
			return;
		}

		$modules[ $feature_name ] = $enabled;

		self::$enabled_modules = $modules;
		self::$dirty = true;
		self::schedule_save();
	}

	/**
	 * Get grace period in seconds for a specific module.
	 *
	 * Uses per-flag grace_period, or DEFAULT_GRACE_PERIOD constant as fallback.
	 *
	 * @since 1.0.17
	 *
	 * @param string $feature_name Feature name.
	 *
	 * @return int Grace period in seconds. 0 = instant disable.
	 */
	private static function get_module_grace_period( $feature_name ) {
		$flags = self::get_flags();

		if ( isset( $flags[ $feature_name ]['grace_period'] ) ) {
			return self::parse_duration_value( $flags[ $feature_name ]['grace_period'] );
		}

		return self::DEFAULT_GRACE_PERIOD;
	}

	/**
	 * Get grace notice data for a specific module, with variables resolved.
	 *
	 * Each flag must define its own grace_notice. Missing or null = silent grace.
	 *
	 * @since 1.0.17
	 *
	 * @param string $feature_name Feature name.
	 * @param int    $expiry       Grace expiry timestamp.
	 *
	 * @return array|null Notice data (text, level, dismiss) or null if silent.
	 */
	private static function get_module_grace_notice( $feature_name, $expiry ) {
		$flags = self::get_flags();

		if ( ! isset( $flags[ $feature_name ]['grace_notice'] ) ) {
			return null;
		}

		$notice_template = $flags[ $feature_name ]['grace_notice'];

		if ( empty( $notice_template['text'] ) ) {
			return null;
		}

		// Resolve template variables.
		$days_remaining = max( 1, (int) ceil( ( $expiry - time() ) / ( 24 * 60 * 60 ) ) );
		$module_label = self::get_module_label( $feature_name );

		$variables = self::get_variables();

		$replacements = array_merge( $variables, array(
			'{{module_name}}' => $module_label,
			'{{grace_expiry_date}}' => wp_date( get_option( 'date_format' ), $expiry ),
			'{{days_remaining}}' => $days_remaining,
		) );

		$text = str_replace(
			array_keys( $replacements ),
			array_values( $replacements ),
			$notice_template['text']
		);

		return array(
			'text' => $text,
			'level' => isset( $notice_template['level'] ) ? $notice_template['level'] : 'warning',
			'dismiss' => isset( $notice_template['dismiss'] ) ? $notice_template['dismiss'] : false,
		);
	}

	/**
	 * Get a human-readable label for a module.
	 *
	 * Uses the 'label' field from flag data if available, otherwise the slug.
	 *
	 * @since 1.0.17
	 *
	 * @param string $feature_name Feature name (slug).
	 *
	 * @return string
	 */
	private static function get_module_label( $feature_name ) {
		$flags = self::get_flags();

		if ( isset( $flags[ $feature_name ]['label'] ) ) {
			return $flags[ $feature_name ]['label'];
		}

		return $feature_name;
	}

	/**
	 * Get grace data (lazy-loaded from the persistent option).
	 *
	 * @since 1.0.17
	 *
	 * @return array Grace data array (module_name => expiry_timestamp).
	 */
	private static function get_grace_data() {
		if ( null === self::$grace_data ) {
			$data = get_option( self::GRACE_OPTION, array() );
			self::$grace_data = is_array( $data ) ? $data : array();
		}

		return self::$grace_data;
	}

	/**
	 * Get enabled modules state (lazy-loaded from the persistent option).
	 *
	 * @since 1.0.17
	 *
	 * @return array Enabled modules array (module_name => bool).
	 */
	private static function get_enabled_modules() {
		if ( null === self::$enabled_modules ) {
			$data = get_option( self::ENABLED_OPTION, array() );
			self::$enabled_modules = is_array( $data ) ? $data : array();
		}

		return self::$enabled_modules;
	}

	/**
	 * Get template variables (from memory or backup).
	 *
	 * @since 1.0.17
	 *
	 * @return array
	 */
	private static function get_variables() {
		if ( null === self::$variables ) {
			$backup = get_option( self::BACKUP_OPTION, null );

			if ( is_array( $backup ) && isset( $backup['variables'] ) ) {
				self::$variables = $backup['variables'];
			}
		}

		return is_array( self::$variables ) ? self::$variables : array();
	}

	/**
	 * Parse a duration value (string or numeric) to seconds.
	 *
	 * Supports: "7d" (days), "2w" (weeks), "24h" (hours), "0" (instant),
	 * or raw seconds as integer.
	 *
	 * @since 1.0.17
	 *
	 * @param mixed $value Duration value.
	 *
	 * @return int Seconds. Falls back to DEFAULT_GRACE_PERIOD if unparseable.
	 */
	private static function parse_duration_value( $value ) {
		if ( is_numeric( $value ) ) {
			return (int) $value;
		}

		if ( ! is_string( $value ) ) {
			return self::DEFAULT_GRACE_PERIOD;
		}

		if ( ! preg_match( '/^(\d+)\s*([hdw])$/i', trim( $value ), $matches ) ) {
			return self::DEFAULT_GRACE_PERIOD;
		}

		$amount = (int) $matches[1];
		$unit = strtolower( $matches[2] );

		$multipliers = array(
			'h' => 60 * 60,
			'd' => 24 * 60 * 60,
			'w' => 7 * 24 * 60 * 60,
		);

		return $amount * $multipliers[ $unit ];
	}

	/**
	 * Check whether this is a migration request.
	 *
	 * Returns true on the first run after updating from a version without
	 * feature flags (enabled_modules option missing, but token_data exists).
	 * Cached per-request.
	 *
	 * @since 1.0.17
	 *
	 * @return bool True if migration is detected.
	 */
	private static function is_migration_request() {
		if ( null !== self::$is_migration ) {
			return self::$is_migration;
		}

		$raw = get_option( self::ENABLED_OPTION );

		if ( false !== $raw ) {
			self::$is_migration = false;

			return false;
		}

		$token_data = API_Requests::get_token_data( false );

		if ( empty( $token_data ) ) {
			self::$is_migration = false;

			return false;
		}

		self::$is_migration = true;

		return true;
	}

	/**
	 * Register the shutdown hook to save state, once per request.
	 *
	 * @since 1.0.17
	 */
	private static function schedule_save() {
		if ( self::$shutdown_scheduled ) {
			return;
		}

		add_action( 'shutdown', array( __CLASS__, 'save_state' ) );

		self::$shutdown_scheduled = true;
	}
}
