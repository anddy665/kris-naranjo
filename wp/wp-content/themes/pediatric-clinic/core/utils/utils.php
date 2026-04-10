<?php
namespace PediatricClinicSpace\Core\Utils;

use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;
use Elementor\Core\Responsive\Responsive;
use Elementor\Controls_Stack;
use Elementor\Plugin as Elementor_Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Utils handler class is responsible for different utility methods.
 */
class Utils {

	/**
	 * Kit options.
	 */
	private static $kit_options;

	/**
	 * Default kits.
	 */
	private static $default_kits;

	/**
	 * Get theme options.
	 *
	 * @return array Theme options.
	 */
	public static function get_theme_options() {
		return get_option( 'cmsmasters_pediatric-clinic_options', array() );
	}

	/**
	 * Get theme option.
	 *
	 * @param string $key Option key.
	 * @param mixed $def Default value for option.
	 *
	 * @return mixed Theme option.
	 */
	public static function get_theme_option( $key, $def = false ) {
		$options = self::get_theme_options();

		if ( isset( $options[ $key ] ) ) {
			return $options[ $key ];
		}

		return $def;
	}

	/**
	 * Get Elementor active kit ID.
	 *
	 * @return string Elementor active kit ID.
	 */
	public static function get_active_kit() {
		$active_kit = get_option( 'elementor_active_kit', '' );

		if ( ! empty( $active_kit ) && did_action( 'wpml_loaded' ) ) {
			$post_type = get_post_type( $active_kit );

			$active_kit = apply_filters( 'wpml_object_id', $active_kit, $post_type, true );
		}

		return $active_kit;
	}

	/**
	 * Get kit options.
	 *
	 * @return array Kit options.
	 */
	public static function get_kit_options() {
		if ( ! self::$kit_options ) {
			$active_kit = self::get_active_kit();

			self::$kit_options = get_post_meta( $active_kit, '_elementor_page_settings', true );
		}

		return self::$kit_options;
	}

	/**
	 * Get kit option.
	 *
	 * @param string $key Option key.
	 * @param mixed $def Default value for option.
	 *
	 * @return mixed Kit option.
	 */
	public static function get_kit_option( $key, $def = false ) {
		$options = self::get_kit_options();

		if ( isset( $options[ $key ] ) ) {
			return $options[ $key ];
		}

		return self::get_default_kit( $key, $def );
	}

	/**
	 * Gets default kits.
	 *
	 * @return array default kits.
	 */
	public static function get_default_kits() {
		if ( ! self::$default_kits ) {
			self::$default_kits = get_option( 'cmsmasters_pediatric-clinic_default_kits', array() );
		}

		return self::$default_kits;
	}

	/**
	 * Get default kit.
	 *
	 * @param string $key kit key.
	 * @param string $def kit default.
	 *
	 * @return string default kit.
	 */
	public static function get_default_kit( $key, $def = '' ) {
		$kits = self::get_default_kits();

		if ( isset( $kits[ $key ] ) ) {
			return $kits[ $key ];
		}

		return $def;
	}

	/**
	 * Check if developer mode is enabled.
	 *
	 * @return bool
	 */
	public static function is_dev_mode() {
		return (
			defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ||
			defined( 'CMSMASTERS_DEVELOPER_MODE' ) && CMSMASTERS_DEVELOPER_MODE ||
			defined( 'ELEMENTOR_TESTS' ) && ELEMENTOR_TESTS
		);
	}

	/**
	 * Get upload dir parameter.
	 *
	 * Retrieve the upload URL/path in right way (works with SSL).
	 *
	 * @param string $name Upload dir parameter name (basedir or baseurl).
	 * @param string $subfolder Upload dir parameter address subfolder.
	 *
	 * @return string Upload dir parameter address.
	 */
	public static function get_upload_dir_parameter( $name, $subfolder = '' ) {
		$upload_dir = wp_upload_dir();
		$address = $upload_dir[ $name ];
		$urls = array( 'url', 'baseurl' );

		if ( in_array( $name, $urls, true ) && is_ssl() ) {
			$address = str_replace( 'http://', 'https://', $address );
		}

		return $address . $subfolder;
	}

	/**
	 * Has custom breakpoints.
	 */
	public static function has_custom_breakpoints() {
		$result = false;

		if ( did_action( 'elementor/loaded' ) ) {
			$result = version_compare( ELEMENTOR_VERSION, '3.2.0', '>=' ) ?
				Elementor_Plugin::$instance->breakpoints->has_custom_breakpoints() :
				Responsive::has_custom_breakpoints();
		}

		return $result;
	}

	/**
	 * Get breakpoints.
	 */
	public static function get_breakpoints() {
		$breakpoints = array(
			'tablet' => 1025,
			'tablet_max' => 1024,
			'mobile' => 768,
			'mobile_max' => 767,
		);

		if ( self::has_custom_breakpoints() ) {
			if ( version_compare( ELEMENTOR_VERSION, '3.2.0', '>=' ) ) {
				$custom_breakpoints = Elementor_Plugin::$instance->breakpoints->get_breakpoints_config();

				$breakpoints['tablet'] = $custom_breakpoints['tablet']['value'] + 1;
				$breakpoints['tablet_max'] = $custom_breakpoints['tablet']['value'];
				$breakpoints['mobile'] = $custom_breakpoints['mobile']['value'] + 1;
				$breakpoints['mobile_max'] = $custom_breakpoints['mobile']['value'];
			} else {
				$custom_breakpoints = Responsive::get_breakpoints();

				$breakpoints['tablet'] = $custom_breakpoints['lg'];
				$breakpoints['tablet_max'] = $custom_breakpoints['lg'] - 1;
				$breakpoints['mobile'] = $custom_breakpoints['md'];
				$breakpoints['mobile_max'] = $custom_breakpoints['md'] - 1;
			}
		}

		return $breakpoints;
	}

	/**
	 * Get devices.
	 *
	 * Retrieve the devices with BC for Elementor version < 3.2.0.
	 *
	 * @return array Divaces.
	 */
	public static function get_devices() {
		if ( version_compare( ELEMENTOR_VERSION, '3.2.0', '>=' ) ) {
			$tablet = Breakpoints_Manager::BREAKPOINT_KEY_TABLET;
			$mobile = Breakpoints_Manager::BREAKPOINT_KEY_MOBILE;
		} else {
			$tablet = Controls_Stack::RESPONSIVE_TABLET;
			$mobile = Controls_Stack::RESPONSIVE_MOBILE;
		}

		$devices = array(
			'tablet' => $tablet,
			'mobile' => $mobile,
		);

		return $devices;
	}

	/**
	 * Render Elementor icon.
	 * Used to render Icon for \Elementor\Controls_Manager::ICONS
	 *
	 * @param array $icon             Icon Type, Icon value
	 * @param array $attributes       Icon HTML Attributes
	 * @param string $tag             Icon HTML tag, defaults to <i>
	 *
	 * @return string Icon html
	 */
	public static function render_icon( $icon, $attributes = array(), $tag = 'i' ) {
		$out = '';

		if ( class_exists( '\Elementor\Icons_Manager' ) ) {
			ob_start();

			\Elementor\Icons_Manager::render_icon( $icon, $attributes, $tag );

			$out = ob_get_clean();
		}

		return $out;
	}

	/**
	 * Get HTML in link.
	 *
	 * @param string $html Input HTML.
	 * @param array $settings Array of link settings.
	 *
	 * @return string HTML in link.
	 */
	public static function get_html_in_link( $html, $settings ) {
		if ( ! isset( $settings['url'] ) || '' === $settings['url'] ) {
			return $html;
		}

		return '<a ' . self::get_link_attributes( $settings ) . '>' . $html . '</a>';
	}

	/**
	 * Get link attributes.
	 *
	 * @param array $settings Array of link settings.
	 *
	 * @return string Link attributes.
	 */
	public static function get_link_attributes( $settings ) {
		$attributes = array();

		if ( ! empty( $settings['url'] ) ) {
			$attributes[] = 'href="' . esc_url( $settings['url'] ) . '"';
		}

		if ( ! empty( $settings['is_external'] ) ) {
			$attributes[] = 'target="_blank"';
		}

		if ( ! empty( $settings['nofollow'] ) ) {
			$attributes[] = 'rel="nofollow"';
		}

		if ( ! empty( $settings['custom_attributes'] ) ) {
			// Custom URL attributes should come as a string of comma-delimited key|value pairs
			$attributes = array_merge( $attributes, self::parse_custom_attributes( $settings['custom_attributes'] ) );
		}

		if ( empty( $attributes ) ) {
			return '';
		}

		$attributes = implode( ' ', $attributes );

		return $attributes;
	}

	/**
	 * Parse attributes that come as a string of comma-delimited key|value pairs.
	 * Removes Javascript events and unescaped `href` attributes.
	 *
	 * @param string $attributes_string
	 *
	 * @param string $delimiter Default comma `,`.
	 *
	 * @return array
	 */
	public static function parse_custom_attributes( $attributes_string, $delimiter = ',' ) {
		$attributes = explode( $delimiter, $attributes_string );
		$result = array();

		foreach ( $attributes as $attribute ) {
			$attr_key_value = explode( '|', $attribute );

			$attr_key = mb_strtolower( $attr_key_value[0] );

			// Remove any not allowed characters.
			preg_match( '/[-_a-z0-9]+/', $attr_key, $attr_key_matches );

			if ( empty( $attr_key_matches[0] ) ) {
				continue;
			}

			$attr_key = $attr_key_matches[0];

			// Avoid Javascript events and unescaped href.
			if ( 'href' === $attr_key || 'on' === substr( $attr_key, 0, 2 ) ) {
				continue;
			}

			if ( isset( $attr_key_value[1] ) ) {
				$attr_value = '="' . trim( $attr_key_value[1] ) . '"';
			} else {
				$attr_value = '';
			}

			$result[] = $attr_key . $attr_value;
		}

		return $result;
	}

}
