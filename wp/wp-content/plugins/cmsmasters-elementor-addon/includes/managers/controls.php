<?php
namespace CmsmastersElementor;

use CmsmastersElementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters controls manager.
 *
 * CMSMasters controls manager handler class is responsible for registering and
 * managing plugin regular controls and the group controls.
 *
 * @since 1.0.0
 * @final
 */
final class Controls_Manager {

	/**
	 * WP Query control id.
	 *
	 * @since 1.0.0
	 */
	const CHOOSE_TEXT = 'choose_text';

	/**
	 * WordPress Query control id.
	 *
	 * @since 1.0.0
	 */
	const QUERY = 'wp_query';

	/**
	 * Locations Repeater control id.
	 *
	 * @since 1.0.0
	 */
	const LOCATIONS_REPEATER = 'locations_repeater';

	/**
	 * Custom Repeater control id.
	 *
	 * @since 1.0.0
	 */
	const CUSTOM_REPEATER = 'custom_repeater';

	/**
	 * Custom Animation control id.
	 *
	 * @since 1.0.0
	 */
	const CUSTOM_ANIMATION = 'custom_animation';

	/**
	 * Selectize control id.
	 *
	 * @since 1.0.0
	 */
	const SELECTIZE = 'selectize';

	/**
	 * Query control group id.
	 *
	 * @since 1.0.0
	 */
	const QUERY_GROUP = 'query';

	/**
	 * Related query control group id.
	 *
	 * @since 1.0.0
	 */
	const QUERY_RELATED_GROUP = 'query_related';

	/**
	 * Post Types control group id.
	 *
	 * @since 1.0.0
	 */
	const FLEX_ALIGN_GROUP = 'flex_align';

	/**
	 * Date format control group id.
	 *
	 * @since 1.0.0
	 */
	const DATE_FORMAT_GROUP = 'format_date';

	/**
	 * Time format control group id.
	 *
	 * @since 1.0.0
	 */
	const TIME_FORMAT_GROUP = 'format_time';

	/**
	 * Colors control group id.
	 *
	 * @since 1.0.0
	 */
	const COLORS_GROUP = 'colors';

	/**
	 * Button background control group id.
	 *
	 * @since 1.1.0
	 */
	const BUTTON_BACKGROUND_GROUP = 'button_background';

	/**
	 * Typography vars control group id.
	 *
	 * @since 1.1.0
	 */
	const VARS_TYPOGRAPHY_GROUP = 'vars_typography';

	/**
	 * Background vars control group id.
	 *
	 * @since 1.1.0
	 */
	const VARS_BACKGROUND_GROUP = 'vars_background';

	/**
	 * Border vars control group id.
	 *
	 * @since 1.1.0
	 */
	const VARS_BORDER_GROUP = 'vars_border';

	/**
	 * Box Shadow vars control group id.
	 *
	 * @since 1.1.0
	 */
	const VARS_BOX_SHADOW_GROUP = 'vars_box_shadow';

	/**
	 * Text background control group id.
	 *
	 * @since 1.17.0
	 */
	const VARS_TEXT_BACKGROUND_GROUP = 'vars_text_background';

	/**
	 * Text Shadow vars control group id.
	 *
	 * @since 1.1.0
	 */
	const VARS_TEXT_SHADOW_GROUP = 'vars_text_shadow';

	/**
	 * CSS Filter vars control group id.
	 *
	 * @since 1.1.0
	 */
	const VARS_CSS_FILTER_GROUP = 'vars_css_filter';

	/**
	 * Addon custom controls list.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $controls = array();

	/**
	 * Addon custom control groups list.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $control_groups = array();

	/**
	 * Controls manager constructor.
	 *
	 * Initializing the Addon controls manager.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->set_controls();
		$this->set_control_groups();

		$this->init_actions();
	}

	/**
	 * Set controls.
	 *
	 * Retrieves Addon custom controls list.
	 *
	 * @since 1.0.0
	 */
	private function set_controls() {
		$this->controls = array(
			self::CHOOSE_TEXT,

			self::QUERY,

			self::LOCATIONS_REPEATER,
			self::CUSTOM_REPEATER,

			self::CUSTOM_ANIMATION,

			self::SELECTIZE,
		);
	}

	/**
	 * Set control groups.
	 *
	 * Retrieves Addon custom control groups list.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added `BUTTON_BACKGROUND_GROUP`, `VARS_TYPOGRAPHY_GROUP`, `VARS_BACKGROUND_GROUP`, `VARS_BORDER_GROUP`, `VARS_BOX_SHADOW_GROUP`, `VARS_TEXT_SHADOW_GROUP`, `VARS_CSS_FILTER_GROUP` control groups.
	 * @since 1.17.0 Added `VARS_TEXT_BACKGROUND_GROUP`.
	 */
	private function set_control_groups() {
		$this->control_groups = array(
			self::QUERY_GROUP,
			self::QUERY_RELATED_GROUP,

			self::FLEX_ALIGN_GROUP,

			self::DATE_FORMAT_GROUP,
			self::TIME_FORMAT_GROUP,

			self::COLORS_GROUP,
			self::BUTTON_BACKGROUND_GROUP,

			self::VARS_TYPOGRAPHY_GROUP,
			self::VARS_BACKGROUND_GROUP,
			self::VARS_BORDER_GROUP,
			self::VARS_BOX_SHADOW_GROUP,
			self::VARS_TEXT_BACKGROUND_GROUP,
			self::VARS_TEXT_SHADOW_GROUP,
			self::VARS_CSS_FILTER_GROUP,
		);
	}

	/**
	 * Register actions.
	 *
	 * Register Addon controls manager init actions.
	 *
	 * @since 1.0.0
	 * @since 1.6.5 Fixed elementor deprecation.
	 */
	private function init_actions() {
		add_action( 'elementor/controls/register', array( $this, 'register_controls' ) );
		add_action( 'elementor/controls/register', array( $this, 'register_groups' ) );
	}

	/**
	 * Register Addon controls.
	 *
	 * This method extends a list of all the supported controls by initializing
	 * each one of appropriate control files.
	 *
	 * Fired by `elementor/controls/register` Elementor plugin action hook.
	 *
	 * @since 1.0.0
	 * @since 1.6.5 Fixed elementor deprecation.
	 */
	public function register_controls() {
		$controls_manager = Plugin::elementor()->controls_manager;

		foreach ( $this->controls as $control_id ) {
			$class_name = __NAMESPACE__ . '\Controls\Control_' . ucwords( $control_id, '_' );

			$controls_manager->register( new $class_name() );
		}
	}

	/**
	 * Register Addon control groups.
	 *
	 * This method extends a list of all the supported control groups by initializing
	 * each one of appropriate control group files.
	 *
	 * Fired by `elementor/controls/register` Elementor plugin action hook.
	 *
	 * @since 1.0.0
	 * @since 1.6.5 Fixed elementor deprecation.
	 */
	public function register_groups() {
		$controls_manager = Plugin::elementor()->controls_manager;

		foreach ( $this->control_groups as $group_id ) {
			$class_name = __NAMESPACE__ . '\Controls\Groups\Group_Control_' . ucwords( $group_id, '_' );

			$controls_manager->add_group_control( $group_id, new $class_name() );
		}
	}

}