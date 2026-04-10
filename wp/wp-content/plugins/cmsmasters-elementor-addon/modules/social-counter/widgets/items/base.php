<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Items;

use CmsmastersElementor\Utils;

use Elementor\Core\Base\Base_Object;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon base social counter class.
 *
 * @since 1.0.0
 */
abstract class Base extends Base_Object {

	const OPTION_NAME = 'cmsmasters_social_counter';

	/**
	 * Cache.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $cache = array();

	/**
	 * Number of social.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $numbers;

	/**
	 * Base social constructor.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param array $settings Widget settings.
	 */
	public function __construct( $settings ) {
		$this->set_settings( $settings );
		$this->ensure_cache();
		$this->ensure_type();
		$this->ensure_url();
		$this->ensure_title();
	}

	/**
	 * Get name.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP Strict Standards.
	 *
	 * @return string
	 */
	public static function get_name() {}

	/**
	 * Get label.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP Strict Standards.
	 *
	 * @return string
	 */
	public static function get_label() {}

	/**
	 * Get default icon.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP Strict Standards.
	 *
	 * @return array
	 */
	public static function get_default_icon() {
		return array(
			'value' => '',
			'library' => '',
		);
	}

	/**
	 * Get remote amount of social data.
	 *
	 * @since 1.0.0
	 *
	 * @return int|false
	 */
	abstract protected function get_numbers_remote();

	/**
	 * Get cache expiration time.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	protected static function get_cache_expire() {
		// return HOUR_IN_SECONDS * 12;
		return 1;
	}

	/**
	 * Get default data type.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_type_default() {
		return array_keys( static::get_types() )[0];
	}

	/**
	 * Get types of numbers data.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_types() {
		return array(
			'followers' => __( 'Followers', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * Performs an HTTP request using the GET method and returns its json response.
	 *
	 * @param string $url  URL to retrieve.
	 * @param array $args Optional. Request arguments. Default empty array.
	 *
	 * @return array|false
	 */
	protected static function get_result_json( $url, $args = array() ) {
		return json_decode( static::get_result( $url, $args ), true );
	}

	/**
	 * Performs an HTTP request using the GET method and returns its response.
	 *
	 * @param string $url  URL to retrieve.
	 * @param array $args Optional. Request arguments. Default empty array.
	 *
	 * @return array|false
	 */
	protected static function get_result( $url, $args = array() ) {
		$args = array_merge( array(
			'sslverify' => false,
			'timeout' => 60,
		), $args );

		$remote = wp_remote_get( $url, $args );

		if ( is_wp_error( $remote ) || 200 !== wp_remote_retrieve_response_code( $remote ) ) {
			return false;
		}

		return wp_remote_retrieve_body( $remote );
	}

	/**
	 * Gets the name of the class the static method is called in.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_class_full_name() {
		return get_called_class();
	}

	/**
	 * Evaluates the given XPath expression.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html
	 * @param string $xpath
	 */
	protected static function get_html_by_xpath( $html, $xpath ) {
		$document = new \DOMDocument();

		libxml_use_internal_errors( true );
		$document->loadHTML( $html );
		libxml_use_internal_errors( false );

		$dom_xpath = new \DOMXpath( $document );
		$dom_query = $dom_xpath->query( $xpath );

		if ( empty( $dom_query[0]->nodeValue ) ) {
			return;
		}

		return $dom_query[0]->nodeValue;
	}

	/**
	 * Ensure social cache.
	 *
	 * @since 1.0.0
	 */
	private function ensure_cache() {
		if ( self::$cache ) {
			return;
		}

		self::$cache = get_option( self::OPTION_NAME, self::$cache, array() );
	}

	/**
	 * Ensure social url.
	 *
	 * @since 1.0.0
	 */
	private function ensure_url() {
		if ( ! $this->get_settings( 'url' ) ) {
			$this->set_settings( 'url', get_option( "elementor_{$this->get_name()}_url" ) );
		}
	}

	/**
	 * Ensure social type.
	 *
	 * @since 1.0.0
	 */
	private function ensure_type() {
		if ( ! $this->get_settings( 'type' ) ) {
			$this->set_settings( 'type', static::get_type_default() );
		}
	}

	/**
	 * Ensure social title.
	 *
	 * @since 1.0.0
	 */
	private function ensure_title() {
		if ( ! $this->get_settings( 'title' ) ) {
			$this->set_settings( 'title', $this->get_title_default() );
		}
	}

	/**
	 * Ensure numbers.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function ensure_numbers() {
		// if ( $this->check_numbers( $this->numbers ) ) {
		// 	return;
		// }

		$this->numbers = $this->get_numbers_remote();

		// set to cache.
		// if ( $this->check_numbers( $this->numbers ) ) {
			$this->update_numbers( (int) $this->numbers );
		// }
	}

	/**
	 * Get social url.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_profile_url() {
		return preg_replace( '/\/$/', '', trim( $this->get_settings( 'url' ) ) );
	}

	/**
	 * Get icon of settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array|false
	 */
	public function get_icon() {
		return $this->get_settings( 'icon' );
	}

	/**
	 * Get type of settings.
	 *
	 * @since 1.0.0
	 *
	 * @return string|false
	 */
	public function get_type() {
		return $this->get_settings( 'type' );
	}

	/**
	 * Get label by type.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_type_label() {
		return Utils::get_if_isset( static::get_types(), $this->get_type(), '' );
	}

	/**
	 * Get cache id.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_cache_id() {
		return static::get_name() . md5(
			join(
				'',
				array(
					$this->get_username(),
					$this->get_type(),
				)
			)
		);
	}

	/**
	 * Get title default.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_title_default() {
		return $this->get_type_label();
	}

	/**
	 * Get title.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->get_settings( 'title' );
	}

	/**
	 * Get username.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_username() {
		preg_match( '/(?:[^\/]+)$/', $this->get_profile_url(), $matches );

		if ( empty( $matches[0] ) ) {
			return '';
		}

		return $matches[0];
	}

	/**
	 * Check if cache is not expired.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function check_cache_expire() {
		if ( ! $this->cache_exist() ) {
			return false;
		}

		return self::$cache[ $this->get_cache_id() ]['time'] > ( time() - static::get_cache_expire() );
	}

	/**
	 * Update numbers.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public function update_numbers( $numbers ) {
		self::$cache[ $this->get_cache_id() ] = array(
			'numbers' => $numbers,
			'time' => time(),
		);

		$this->update_cache();
	}

	/**
	 * Check if cache is exist.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function cache_exist() {
		return isset( self::$cache[ $this->get_cache_id() ]['numbers'] );
	}

	/**
	 * Check numbers from cache.
	 *
	 * @since 1.0.0
	 *
	 * @return int|false
	 */
	public function get_numbers_cache() {
		if ( ! $this->cache_exist() ) {
			return;
		}

		$cache_name = $this->get_cache_id();

		if ( ! $this->check_numbers( self::$cache[ $cache_name ]['numbers'] ) ) {
			return;
		}

		return (int) self::$cache[ $cache_name ]['numbers'];
	}

	/**
	 * Save cache to wp_option.
	 *
	 * @since 1.0.0
	 */
	private function update_cache() {
		update_option( self::OPTION_NAME, self::$cache );
	}

	/**
	 * Get numbers.
	 *
	 * @since 1.0.0
	 *
	 * @return int|false
	 */
	public function get_numbers() {
		// $this->ensure_numbers();

		$this->numbers = $this->get_numbers_remote();

		return $this->numbers;
	}

	/**
	 * Check if number valid.
	 *
	 * @param bool
	 */
	public function check_numbers( $numbers ) {
		// if numbers is true or zero
		return ( $numbers || 0 === $numbers );
	}

	/**
	 * Check if before render.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function check_pre_show() {
		return ( $this->get_profile_url() && $this->get_type() );
	}

}
