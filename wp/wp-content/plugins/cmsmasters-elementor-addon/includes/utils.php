<?php
namespace CmsmastersElementor;

use CmsmastersElementor\Plugin;

use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;
use Elementor\Core\Responsive\Responsive;
use Elementor\Controls_Stack; 
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Utils as Elementor_Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon utils handler class is responsible for different utility methods
 * used by current plugin.
 *
 * @since 1.0.0
 */
class Utils {

	const IP_LOCAL = '127.0.0.1';

	/**
	 * Addon menu icon.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 Fixed admin menu icon.
	 */
	const MENU_ICON = 'PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxOCIgaGVpZ2h0PSIxNCIgdmlld0JveD0iMCAwIDM0MiAyNjEiIGZpbGw9Im5vbmUiPjxnIGNsaXAtcGF0aD0idXJsKCNjbGlwMCkiPjxwYXRoIGQ9Ik0xNTAuMDM5IDE4MS45NzJMMTUwLjAzNyAxODEuOTczQzEzNS44MDIgMTkyLjE1NCAxMTkuMjc4IDE5Ny43MTUgMTAwLjQgMTk4LjYyMVYxODEuMDk3QzExNC41NyAxODAuOTc5IDEyNi45MzQgMTc3LjI4MSAxMzcuNzEzIDE3MC4wNjJDMTQ4LjI4MiAxNjIuOTgyIDE1Ni4xNTcgMTUzLjU3NyAxNjEuNDEgMTQxLjhIMTgwLjYzN0MxNzQuMTg3IDE1OC42NzMgMTYzLjk1OSAxNzIuMDQyIDE1MC4wMzkgMTgxLjk3MloiIHN0cm9rZT0iI2U2NWUyYyIgc3Ryb2tlLXdpZHRoPSI0Ii8+PHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xMDEuNSAxNzkuMUMxMDAuOSAxNzkuMSAxMDAuNCAxNzkuMSA5OS44IDE3OS4xQzc0LjMgMTc5LjEgNTQuOSAxNzEuNSA0MS41IDE1Ni4zQzI4LjcgMTQxLjkgMjIuMyAxMjMuMiAyMi4zIDEwMC40QzIyLjMgNzcuNSAyOC43IDU4LjkgNDEuNSA0NC41QzQ3LjggMzcuNCA1NS42IDMxLjggNjQuOSAyNy44Qzc0LjIgMjMuOCA4NS44IDIxLjcgOTkuOCAyMS43QzExMy44IDIxLjcgMTI2LjEgMjUuMyAxMzYuNyAzMi40QzE0Ny4zIDM5LjUgMTU1LjEgNDkuMSAxNjAuMiA2MS4xSDE4My42QzE3Ny4xIDQyLjYgMTY2LjMgMjcuOSAxNTEuMyAxNy4zQzEzNS4zIDUuNyAxMTYuNCAwIDk0LjcgMEM2Ni4xIDAgNDIuOSA5LjggMjUuMiAyOS4zQzguNCA0OC4xIDAgNzEuNyAwIDEwMC4zQzAgMTI4LjkgOC40IDE1Mi42IDI1LjIgMTcxLjNDNDMgMTkwLjggNjYuMSAyMDAuNiA5NC44IDIwMC42Qzk3LjEgMjAwLjYgOTkuMyAyMDAuNSAxMDEuNSAyMDAuNFYxNzkuMVoiIGZpbGw9IiNlNjVlMmMiLz48cGF0aCBkPSJNMjI1LjM0OCAxNTkuMTQ1TDIyNS4zMzYgMTU5LjE0QzIxNS45NjQgMTU1LjQzMSAyMDguMjAyIDE1MC4zNzQgMjAyLjExNCAxNDQuMjg2QzE5Ni4yNjMgMTM4LjQzNSAxOTMuMiAxMzAuMzg1IDE5My4yIDEyMEMxOTMuMiAxMDUuMjk4IDE5OC44MzMgOTMuMzYzNCAyMTAuMDcxIDg0LjA0MzhDMjE4LjAwNyA3Ny41NTQzIDIzMC45ODggNzMuNjY1NCAyNDUuNSA3MS43NjIzVjczVjg2Ljg1MDdDMjQyLjQ4NiA4Ny40NTY3IDIzNy4zNjEgODguNTY3NSAyMzUuNjk4IDg5LjA5MjhMMjM1LjY3OCA4OS4wOTlMMjM1LjY1OSA4OS4xMDU2QzIyOS4wMDkgOTEuMzU2MyAyMjMuNjUzIDk0LjE2MjMgMjE5Ljc5NSA5Ny40ODQ1TDIxOS43ODMgOTcuNDk0N0wyMTkuNzcxIDk3LjUwNTJDMjE1Ljk3MyAxMDAuODgxIDIxMy4zMjcgMTA0LjU3MiAyMTEuOTkzIDEwOC43OThDMjEwLjcyNyAxMTIuODA1IDIxMC4xIDExNy4wMDkgMjEwLjEgMTIxLjVDMjEwLjEgMTI2LjQzOSAyMTIuMTk1IDEzMC44MzIgMjE2LjIwNSAxMzQuNzMzQzIyMC4xMyAxMzguNTUyIDIyNS4xNjUgMTQxLjY3OCAyMzEuNDQyIDE0NC4yNTFMMjMxLjQ0NyAxNDQuMjUzQzIzMi40MTIgMTQ0LjY0NSAyMzMuNzk5IDE0NS4yNTMgMjM1LjM5MyAxNDUuOTUzQzIzNi40MDMgMTQ2LjM5NiAyMzcuNDk3IDE0Ni44NzcgMjM4LjYyIDE0Ny4zNjFDMjQwLjgyMiAxNDguMzEyIDI0My4xNDQgMTQ5LjI4NCAyNDUgMTQ5Ljk0NVYxNjYuMzA2QzIzOC44IDE2NC4zNDcgMjMwLjMwOSAxNjEuMTQ3IDIyNS4zNDggMTU5LjE0NVpNMjA2LjE1MSAyMDdIMTg4LjA5OEMxODcuOTQ5IDIwNi4wMzUgMTg3LjgxOSAyMDUuMDc3IDE4Ny43MDcgMjA0LjFIMjA1Ljc0QzIwNS44NDEgMjA1LjA5IDIwNS45ODIgMjA2LjA1NyAyMDYuMTUxIDIwN1oiIHN0cm9rZT0iI2Q1NDE4YiIgc3Ryb2tlLXdpZHRoPSI0Ii8+PHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0yODQuNCA5MC44QzMwMi42IDk1LjkgMzEyLjYgMTA2LjIgMzE0LjUgMTIxLjhIMzM2LjRDMzM1LjYgMTA2LjcgMzI5LjMgOTQuMyAzMTcuNCA4NC42QzMwNC4zIDczLjYgMjg1LjkgNjguMSAyNjIgNjguMUMyNTYuNSA2OC4xIDI1MS40IDY4LjQgMjQ2LjUgNjguOVY4OC45QzI1MC43IDg4LjMgMjU1LjQgODggMjYwLjQgODhDMjY5LjkgODcuOSAyNzcuOSA4OC45IDI4NC40IDkwLjhaTTMyMSAxNzEuNEMzMTQuOSAxNjcuNCAzMDggMTY0LjIgMzAwLjMgMTYxLjZDMjkyLjUgMTU5IDI4NC42IDE1Ni44IDI3Ni42IDE1NC44QzI2OC42IDE1Mi44IDI2MC42IDE1MC45IDI1Mi45IDE0OUMyNTAuNyAxNDguNSAyNDguNiAxNDcuOSAyNDYuNSAxNDcuM1YxNjguM0MyNDkuNSAxNjkuMSAyNTIuNSAxNjkuOCAyNTUuNiAxNzAuNUMyNjYuNyAxNzMgMjc3IDE3NS42IDI4Ni42IDE3OC41QzI5Ni4yIDE4MS4zIDMwNC4xIDE4NC45IDMxMC41IDE4OS4zQzMxNi44IDE5My43IDMyMCAxOTkuNiAzMjAgMjA3LjJDMzIwIDIyOS40IDMwMi40IDI0MC40IDI2Ny4yIDI0MC40QzI1MC4zIDI0MC40IDIzNi44IDIzNy41IDIyNi42IDIzMS42QzIxNC45IDIyNC44IDIwOC42IDIxNC45IDIwNy45IDIwMi4xSDE4NS43QzE4Ny40IDIyMC4zIDE5NC40IDIzNC4zIDIwNi43IDI0NC4xQzIyMC41IDI1NS4xIDI0MC42IDI2MC42IDI2Ni45IDI2MC42QzI5MC4yIDI2MC42IDMwOC40IDI1NiAzMjEuNiAyNDYuN0MzMzQuOCAyMzcuNCAzNDEuMyAyMjQgMzQxLjMgMjA2LjRDMzQxLjIgMTkxLjYgMzM0LjUgMTc5LjkgMzIxIDE3MS40WiIgZmlsbD0iI2Q1NDE4YiIvPjxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgY2xpcC1ydWxlPSJldmVub2RkIiBkPSJNMTAxLjggMTk5LjdWMTk5SDgwLjVWMTk5LjdIMTAxLjhaTTI2My40IDM4LjNIMjM4LjdMMTcyLjUgMTI3LjVMMTA1LjIgMzguM0g4MC41VjE5OUgxMDEuOFY3MC43TDE3Mi41IDE2MC4yTDI0Mi4xIDcwLjdWMjI2LjNMMjQ3LjUgMjI3LjVDMjUyLjggMjI4LjMgMjU4LjQgMjI4LjcgMjYzLjUgMjI4LjhMMjYzLjQgMzguM1oiIGZpbGw9IiMzMGFhY2YiLz48L2c+PGRlZnM+PGNsaXBQYXRoIGlkPSJjbGlwMCI+PHJlY3Qgd2lkdGg9IjM0MS4yIiBoZWlnaHQ9IjI2MC42IiBmaWxsPSJ3aGl0ZSIvPjwvY2xpcFBhdGg+PC9kZWZzPjwvc3ZnPg==';

	/**
	 * Get data if isset.
	 *
	 * Retrieves data with selected key if isset.
	 *
	 * @since 1.0.0
	 *
	 * @param array|object $data Data to check.
	 * @param string $key Data key to look for.
	 * @param string $else Default value if key is not found.
	 *
	 * @return mixed Data with selected key, default value otherwise.
	 */
	public static function get_if_isset( $data, $key, $else = '' ) {
		if ( 'object' === gettype( $data ) ) {
			return isset( $data->{$key} ) ? $data->{$key} : $else;
		}

		return isset( $data[ $key ] ) ? $data[ $key ] : $else;
	}

	/**
	 * Get data if not empty.
	 *
	 * Retrieves data with selected key if not empty.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Array of data.
	 * @param string $key Data key to look for.
	 * @param string $else Default value if key is empty.
	 *
	 * @return mixed Data with selected key, default value otherwise.
	 */
	public static function get_if_not_empty( $data, $key, $else = '' ) {
		return ( ! empty( $data[ $key ] ) ) ? $data[ $key ] : $else;
	}

	/**
	 * Check if value in array or equal.
	 *
	 * If `$haystack` is array - checks if `$needle` is in
	 * `$haystack` array, or if they are equal otherwise.
	 *
	 * @since 1.0.0
	 *
	 * @param string $needle Test value.
	 * @param mixed $haystack Array or value to check.
	 *
	 * @return bool True if `$needle` is in array or equal to
	 * `$haystack`, false otherwise.
	 */
	public static function in_array_or_equal( $needle, $haystack ) {
		if ( is_array( $haystack ) ) {
			return in_array( $needle, $haystack, true );
		}

		return $needle === $haystack;
	}

	public static function generate_html_tag( $name, $attributes = array(), $content = false ) {
		if ( is_array( $attributes ) ) {
			$attributes_array = array();

			foreach ( $attributes as $key => $value ) {
				$attributes_array[] = sprintf( "{$key}=\"%s\"", esc_attr( $value ) );
			}

			$attributes = sprintf( ' %s', implode( ' ', $attributes_array ) );
		} else {
			$attributes = " {$attributes}";
		}

		$tag = "<{$name}{$attributes}>";

		if ( $content ) {
			$tag .= "{$content}</{$name}>";
		}

		return $tag;
	}

	/**
	 * Get class name.
	 *
	 * Converts string to valid class name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name The name string.
	 *
	 * @return array The class name.
	 */
	public static function generate_class_name( $name ) {
		return str_replace( '-', '_', ucwords( $name, '-' ) );
	}

	/**
	 * Unset key by value.
	 *
	 * Unset array items by value.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param string $needle The searchable value.
	 * @param array $haystack The array to search.
	 *
	 * @return array The filtered array.
	 */
	public static function unset_items_by_value( $needle, $haystack ) {
		foreach ( array_keys( $haystack, $needle, true ) as $key ) {
			unset( $haystack[ $key ] );
		}

		return $haystack;
	}

	/**
	 * Check if request is ajax.
	 *
	 * Whether the current request is a WordPress ajax request.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if it's a WordPress ajax request, false otherwise.
	 */
	public static function is_ajax() {
		if ( function_exists( 'wp_doing_ajax' ) ) {
			return wp_doing_ajax();
		}

		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	/**
	 * Get breakpoints.
	 *
	 * Retrieve the responsive breakpoints.
	 *
	 * @since 1.1.0
	 *
	 * @return array Responsive breakpoints.
	 */
	public static function get_breakpoints() {
		$breakpoints_default_config = Breakpoints_Manager::get_default_config();
		$breakpoint_key_mobile = Breakpoints_Manager::BREAKPOINT_KEY_MOBILE;
		$breakpoint_key_tablet = Breakpoints_Manager::BREAKPOINT_KEY_TABLET;

		$breakpoints['mobile'] = $breakpoints_default_config[ $breakpoint_key_mobile ]['default_value'];
		$breakpoints['tablet'] = $breakpoints_default_config[ $breakpoint_key_tablet ]['default_value'];

		return $breakpoints;
	}

	/**
	 * Get devices.
	 *
	 * Retrieve the devices.
	 *
	 * @since 1.2.3
	 *
	 * @return array Devices.
	 */
	public static function get_devices() {
		$devices = array(
			'widescreen' => Breakpoints_Manager::BREAKPOINT_KEY_WIDESCREEN,
			'laptop' => Breakpoints_Manager::BREAKPOINT_KEY_LAPTOP,
			'tablet_extra' => Breakpoints_Manager::BREAKPOINT_KEY_TABLET_EXTRA,
			'tablet' => Breakpoints_Manager::BREAKPOINT_KEY_TABLET,
			'mobile_extra' => Breakpoints_Manager::BREAKPOINT_KEY_MOBILE_EXTRA,
			'mobile' => Breakpoints_Manager::BREAKPOINT_KEY_MOBILE,
		);

		return $devices;
	}

	/**
	 * Get devices args.
	 *
	 * @since 1.3.3
	 *
	 * @param array $args Device args.
	 *
	 * @return array Devices args.
	 */
	public static function get_devices_args( $args = array() ) {
		if ( empty( $args ) || ! is_array( $args ) ) {
			return $args;
		}

		$devices = self::get_devices();

		if ( empty( $devices ) ) {
			return $args;
		}

		$out_args = array();

		foreach ( $devices as $device ) {
			$out_args[ $device ] = self::parse_devices_args( $args, $device );
		}

		return $out_args;
	}

	/**
	 * Parse devices args.
	 *
	 * @since 1.3.3
	 *
	 * @param array $args Device args.
	 * @param string $device Device key.
	 *
	 * @return array Parsed args.
	 */
	public static function parse_devices_args( $args, $device ) {
		$out_args = array();

		foreach ( $args as $key => $value ) {
			$key = str_replace( '{{cmsmasters_device}}', $device, $key );

			if ( is_array( $value ) ) {
				$out_args[ $key ] = self::parse_devices_args( $value, $device );
			} else {
				$out_args[ $key ] = str_replace( '{{cmsmasters_device}}', $device, $value );
			}
		}

		return $out_args;
	}

	/**
	 * Check if request is preview.
	 *
	 * Whether the current request is elementor preview mode or
	 * WordPress preview.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return bool True if it's a preview request, false otherwise.
	 */
	public static function is_preview( $post_id = 0 ) {
		return self::is_preview_mode( $post_id ) || is_preview();
	}

	/**
	 * Check if request is preview mode.
	 *
	 * Whether the current request is elementor preview mode.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if it's an elementor preview mode, false otherwise.
	 */
	public static function is_preview_mode( $post_id = 0 ) {
		return Plugin::elementor()->preview->is_preview_mode( $post_id );
	}

	/**
	 * Check if request is edit mode.
	 *
	 * Whether the current request is elementor edit mode.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if it's an elementor edit mode, false otherwise.
	 */
	public static function is_edit_mode( $post_id = 0 ) {
		return Plugin::elementor()->editor->is_edit_mode( $post_id );
	}

	/**
	 * Check if Elementor Pro active.
	 *
	 * Checks whether the Elementor Pro plugin is currently active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if Elementor Pro is active, false otherwise.
	 */
	public static function is_pro() {
		return class_exists( 'ElementorPro\Plugin' );
	}

	/**
	 * Use Theme Builder.
	 *
	 * Checks whether the Elementor Pro Theme Builder templates
	 * needs to be in use on the website.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if Elementor Pro is active and Theme Builder
	 * templates is active, false otherwise.
	 */
	public static function use_theme_builder() {
		return self::is_pro() && 'elementor_pro' === self::get_theme_templates_type();
	}

	/**
	 * Get Theme Templates type.
	 *
	 * Retrieve Theme Templates type addon setting.
	 *
	 * @since 1.0.0
	 *
	 * @return string Theme Templates type.
	 */
	public static function get_theme_templates_type() {
		$templates_type = 'cmsmasters';

		if ( self::is_pro() ) {
			$templates_type = get_option( 'elementor_theme_templates_type', 'cmsmasters' );
		}

		return $templates_type;
	}

	/**
	 * Get redirect URL.
	 *
	 * Retrieves the redirect link URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Redirect link slug.
	 *
	 * @return string Redirect link URL.
	 */
	public static function get_redirect_url( $slug ) {
		return esc_url( "https://go.cmsmasters.net/{$slug}" );
	}

	/**
	 * Get WordPress Filesystem.
	 *
	 * Retrieve the instance of WordPress Filesystem class.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Filesystem_Base Instance of WordPress Filesystem class.
	 */
	public static function get_wp_filesystem() {
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';

			WP_Filesystem();
		}

		return $wp_filesystem;
	}

	/**
	 * Get activation link.
	 *
	 * Retrieve the link to theme activation.
	 *
	 * @since 1.0.0
	 *
	 * @param string $link URL to theme activation.
	 *
	 * @return string Theme activation link.
	 */
	public static function get_activation_link( $link ) {
		static $theme_name = false;

		if ( ! $theme_name ) {
			$theme_obj = wp_get_theme();

			if ( $theme_obj->parent() ) {
				$theme_obj = $theme_obj->parent();
			}

			$theme_name = sanitize_key( $theme_obj->get( 'Name' ) );
		}

		$link = add_query_arg( 'utm_term', $theme_name, $link );

		return $link;
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
	 * gets the current post type in the WordPress Admin
	 */
	public static function get_current_post_type() {
		global $post;
		// if we have a post so we can just get the post type from that
		if ( $post && $post->post_type ) {
			return $post->post_type;
		}

		global $typenow;
		// check the global $typenow - set in admin.php
		if ( $typenow ) {
			return $typenow;
		}

		global $current_screen;
		// check the global $current_screen object - set in screen.php
		if ( $current_screen && $current_screen->post_type ) {
			return $current_screen->post_type;
		}

		// and lastly check the post_type query string
		if ( isset( $_REQUEST['post_type'] ) ) {
			return sanitize_key( $_REQUEST['post_type'] );
		}

		// we do not know the post type!
		return null;
	}

	public static function strip_comments( $string, $comment = '//' ) {
		$pattern = sprintf( '![ \t]*%s.*[ \t]*[\r\n]!', $comment );

		return preg_replace( $pattern, '', $string );
	}

	public static function render_alert( $message, $type = 'warning', $admin_only = true ) {
		echo self::get_elementor_alert( $message, $type, $admin_only );
	}

	public static function get_elementor_alert( $message, $type = 'warning', $admin_only = true ) {
		if ( $admin_only && ! is_admin() ) {
			return;
		}

		return sprintf( '<div class="elementor-alert elementor-alert-%2$s">%1$s</div>', $message, esc_attr( $type ) );
	}

	/**
	 * Get public post types.
	 *
	 * Retrieve a list of all public post types names.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args_filter Arguments filter.
	 * @param bool $singular_label Arguments filter.
	 *
	 * @return array Array of public post types names.
	 */
	public static function get_public_post_types( $args_filter = array(), $singular_label = false ) {
		$args = array( 'show_in_nav_menus' => true );

		if ( ! empty( $args_filter['post_type'] ) ) {
			$args['name'] = $args_filter['post_type'];

			unset( $args_filter['post_type'] );
		}

		$args = wp_parse_args( $args, $args_filter );
		$post_type_objects = get_post_types( $args, 'objects' );
		$post_types = array();

		foreach ( $post_type_objects as $name => $object ) {
			$post_types[ $name ] = $singular_label ?
				$object->labels->singular_name :
				$object->label;
		}

		/**
		 * Public Post types
		 *
		 * Allow to filters the public post types Addon should work on.
		 *
		 * @since 1.0.0
		 *
		 * @param array $post_types Addon supported public post types.
		 */
		return apply_filters( 'cmsmasters_elementor/utils/get_public_post_types', $post_types );
	}

	/**
	 * Filter public post types.
	 *
	 * Filter a list of all public post types names.
	 *
	 * @since 1.0.0
	 *
	 * @param array $filter Post types filter.
	 *
	 * @return array Filtered array of public post types names.
	 */
	public static function filter_public_post_types( $filter = array(), $singular_label = false ) {
		$post_types_array = self::get_public_post_types( array(), $singular_label );
		$post_types = $post_types_array;

		if ( empty( $filter ) ) {
			$filter['exclude'] = array(
				'product',
				'tribe_events',
				'tribe_venue',
				'tribe_organizer',
			);
		}

		if ( isset( $filter['include'] ) ) {
			$include = explode( ',', $filter['include'] );

			foreach ( array_keys( $post_types_array ) as $post_type ) {
				if ( ! in_array( $post_type, $include, true ) ) {
					unset( $post_types[ $post_type ] );
				}
			}
		} elseif ( isset( $filter['exclude'] ) ) {
			$exclude = is_array( $filter['exclude'] ) ? $filter['exclude'] : explode( ',', $filter['exclude'] );

			foreach ( array_keys( $post_types_array ) as $post_type ) {
				if ( in_array( $post_type, $exclude, true ) ) {
					unset( $post_types[ $post_type ] );
				}
			}
		}

		return $post_types;
	}

	/**
	 * Check if search product.
	 *
	 * Checks whether the current query is WooCommerce product search.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if query is WooCommerce product search, false otherwise.
	 */
	public static function is_search_product() {
		return is_search() && 'product' === get_query_var( 'post_type' );
	}
	/**
	 * Check if search event.
	 *
	 * Checks whether the current query is tribe event search.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if query is tribe event search, false otherwise.
	 */
	public static function is_search_event() {
		return is_search() && (
			'tribe_events' === get_query_var( 'post_type' ) ||
			'tribe_venue' === get_query_var( 'post_type' ) ||
			'tribe_organizer' === get_query_var( 'post_type' )
		);
	}

	/**
	 * Get page title.
	 *
	 * Retrieve the page title based on the queried object.
	 *
	 * @since 1.0.0
	 *
	 * @return string Page title.
	 */
	public static function get_page_title( $context = true ) {
		if ( is_singular() ) {
			$title = get_the_title();

			if ( $context ) {
				$post_type_obj = get_post_type_object( get_post_type() );
				/* translators: Singular page title. 1: Post type name, 2: Singular title */
				$title = sprintf( '%1$s: %2$s', $post_type_obj->labels->singular_name, $title );
			}
		} elseif ( is_search() ) {
			/* translators: Search query page title. %s: Search query string */
			$title = sprintf( __( 'Search Results for: %s', 'cmsmasters-elementor' ), get_search_query() );

			if ( get_query_var( 'paged' ) ) {
				/* translators: Paged search query title part. %s: Page number */
				$title .= sprintf( __( '&nbsp;&ndash; Page %s', 'cmsmasters-elementor' ), get_query_var( 'paged' ) );
			}
		} elseif ( ! $context ) {
			if ( is_category() ) {
				$title = single_cat_title( '', false );
			} elseif ( is_tag() ) {
				$title = single_tag_title( '', false );
			} elseif ( is_author() ) {
				$title = '<span class="vcard">' . get_the_author() . '</span>';
			} elseif ( is_year() ) {
				$title = get_the_date( _x( 'Y', 'yearly archives date format', 'cmsmasters-elementor' ) );
			} elseif ( is_month() ) {
				$title = get_the_date( _x( 'F Y', 'monthly archives date format', 'cmsmasters-elementor' ) );
			} elseif ( is_day() ) {
				$title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'cmsmasters-elementor' ) );
			} elseif ( is_tax( 'post_format' ) ) {
				$title = get_the_archive_title();
			} elseif ( is_post_type_archive() ) {
				$title = post_type_archive_title( '', false );
			} elseif ( is_tax() ) {
				$title = single_term_title( '', false );
			} else {
				$title = get_the_archive_title();
			}
		} elseif ( $context ) {
			$title = get_the_archive_title();
		} else {
			$title = '';
		}

		/**
		 * Filters the page title.
		 *
		 * @since 1.0.0
		 *
		 * @param string $title Page title.
		 */
		$title = apply_filters( 'cmsmasters_elementor/utils/get_page_title', $title );

		return $title;
	}

	/**
	 * Get archive permalink.
	 *
	 * Retrieve the archive permalink based on the queried object.
	 *
	 * @since 1.0.0
	 *
	 * @return string Archive permalink.
	 */
	public static function get_the_archive_url( $query = '' ) {
		if ( is_search() ) {
			$url = get_search_link( $query );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$url = get_term_link( get_queried_object() );
		} elseif ( is_author() ) {
			$url = get_author_posts_url( get_queried_object_id() );
		} elseif ( is_year() ) {
			$url = get_year_link( get_query_var( 'year' ) );
		} elseif ( is_month() ) {
			$url = get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) );
		} elseif ( is_day() ) {
			$url = get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) );
		} elseif ( is_post_type_archive() ) {
			$url = get_post_type_archive_link( get_post_type() );
		} else {
			$url = '';
		}

		/**
		 * Filters the archive url.
		 *
		 * @since 1.0.0
		 *
		 * @param string $title Archive url.
		 */
		$url = apply_filters( 'cmsmasters_elementor/utils/get_the_archive_url', $url );

		return $url;
	}

	/**
	 * Set author data global variable.
	 *
	 * Retrieve user info and updates $authordata global object.
	 *
	 * @since 1.0.0
	 *
	 * @global object $authordata The author object for the current post
	 */
	public static function set_global_authordata() {
		global $authordata;

		if ( ! isset( $authordata->ID ) ) {
			$post = get_post();
			$authordata = get_userdata( $post->post_author ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
	}

	/**
	 * Get taxonomies.
	 *
	 * Retrieves a list of registered taxonomy names or objects.
	 *
	 * Used to overcome core bug when taxonomy is in more then one post type.
	 *
	 * @see https://core.trac.wordpress.org/ticket/27918
	 * @source https://core.trac.wordpress.org/attachment/ticket/27918/27918.3.diff function fix
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 * @param string $output
	 * @param string $operator
	 *
	 * @global array $wp_taxonomies The registered taxonomies.
	 *
	 * @return array A list of taxonomy names or objects.
	 */
	public static function get_taxonomies( $args = array(), $output = 'names', $operator = 'and' ) {
		global $wp_taxonomies;

		// Manage 'object_type' separately.
		if ( isset( $args['object_type'] ) ) {
			$object_type = (array) $args['object_type'];

			unset( $args['object_type'] );
		}

		$taxonomies = wp_filter_object_list( $wp_taxonomies, $args, $operator );

		if ( isset( $object_type ) ) {
			foreach ( $taxonomies as $tax => $tax_data ) {
				if ( ! array_intersect( $object_type, $tax_data->object_type ) ) {
					unset( $taxonomies[ $tax ] );
				}
			}
		}

		if ( 'names' === $output ) {
			$taxonomies = wp_list_pluck( $taxonomies, 'name' );
		}

		return $taxonomies;
	}

	/**
	 * Array flatten.
	 *
	 * Flattens a nested array.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param array $array The nested array.
	 *
	 * @return array Flattened array.
	 */
	public static function array_flatten( $array ) {
		if ( ! is_array( $array ) ) {
			return false;
		}

		$result = array();

		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$result = array_merge( $result, self::array_flatten( $value ) );
			} else {
				$result[ $key ] = $value;
			}
		}

		return $result;
	}

	/**
	 * Link pages.
	 *
	 * Prints out the link pages of the content block.
	 *
	 * @since 1.0.0
	 *
	 * @param Widget_Base $widget Base widget object.
	 */
	public static function link_pages( $widget = false ) {
		$pages_args = array(
			'before' => '<div class="page-links cmsmasters-page-links">' .
				'<span class="page-links-title cmsmasters-page-links-title">' . __( 'Pages:', 'cmsmasters-elementor' ) . '</span>',
			'after' => '</div>',
			'link_before' => '<span>',
			'link_after' => '</span>',
			'pagelink' => '<span class="screen-reader-text">' . __( 'Page', 'cmsmasters-elementor' ) . ' </span>%',
			'separator' => '<span class="screen-reader-text">, </span>',
		);

		/**
		 * Filters the link pages.
		 *
		 * @since 1.0.0
		 *
		 * @param string $pages_args Link pages arguments.
		 * @param Widget_Base $widget Base widget object.
		 */
		$pages_args = apply_filters( 'cmsmasters_elementor/utils/link_pages', $pages_args, $widget );

		$pages_args['echo'] = 1;

		wp_link_pages( $pages_args );
	}

	public static function find_widget_elements_by_id( $elements, $id ) {
		static $widget = null;

		foreach ( $elements as $element ) {
			if ( $id === $element['id'] ) {
				$widget = $element;

				break;
			} elseif ( isset( $element['elements'] ) ) {
				self::find_widget_elements_by_id( $element['elements'], $id );
			}
		}

		return $widget;
	}

	public static function get_client_ip_as_key() {
		return str_replace( '.', '_', self::get_client_ip() );
	}

	public static function get_client_ip() {
		$ip = self::IP_LOCAL;

		$server_ip_keys = array(
			'REMOTE_ADDR',
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
		);

		foreach ( $server_ip_keys as $key ) {
			if ( isset( $_SERVER[ $key ] ) && filter_var( $_SERVER[ $key ], FILTER_VALIDATE_IP ) ) {
				$ip = $_SERVER[ $key ];

				break;
			}
		}

		return apply_filters( 'cmsmasters_elementor/utils/client_ip', $ip );
	}

	public static function is_mailpoet_3() {
		return class_exists( '\MailPoet\Config\Initializer' );
	}

	public static function get_mailpoet_forms() {
		$is_mailpoet_3 = self::is_mailpoet_3();
		$options = array();

		if ( $is_mailpoet_3 ) {
			$forms_data = new \MailPoet\Models\Form();

			$forms = $forms_data::getPublished()->orderByAsc( 'name' )->findArray();
		} else {
			$model_forms = \WYSIJA::get( 'forms', 'model' );

			$model_forms->reset();

			$forms = $model_forms->getRows(
				array(
					'form_id',
					'name',
				)
			);
		}

		foreach ( $forms as $form ) {
			$form_id = $is_mailpoet_3 ? $form['id'] : $form['form_id'];

			$options[ $form_id ] = $form['name'] . " (#{$form['id']})";
		}

		return $options;
	}

	public static function get_taxonomy_options( $args = array() ) {
		$args_default = array(
			'show_in_nav_menus' => true,
		);

		$args = array_merge( $args_default, $args );

		$taxonomies = get_taxonomies( $args, 'objects' );

		return wp_list_pluck( $taxonomies, 'label', 'name' );
	}

	public static function short_number( $number ) {
		$abbrevs = array(
			12 => 'T',
			9 => 'B',
			6 => 'M',
			3 => 'K',
			0 => '',
		);

		foreach ( $abbrevs as $exponent => $abbrev ) {
			if ( abs( $number ) >= pow( 10, $exponent ) ) {
				$display = $number / pow( 10, $exponent );
				$decimals = ( $exponent >= 3 && round( $display ) < 100 ) ? 1 : 0;
				$number = number_format( $display, $decimals ) . $abbrev;
				break;
			}
		}

		return $number;
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public static function array_merge_recursive( $array1, $array2 ) {
		$merged = $array1;

		foreach ( $array2 as $key => & $value ) {
			if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
				$merged[ $key ] = self::array_merge_recursive( $merged[ $key ], $value );
			} elseif ( is_numeric( $key ) ) {
				if ( ! in_array( $value, $merged, true ) ) {
					$merged[] = $value;
				}
			} else {
				$merged[ $key ] = $value;
			}
		}

		return $merged;
	}


	/**
	 * Get current elementor document id
	 *
	 * @since 1.0.0
	 *
	 * @return int|false
	 */
	public static function get_document_id() {
		$document = Plugin::elementor()->documents->get_current();

		if ( $document ) {
			return $document->get_main_id();
		}

		return $document;
	}

	/**
	 * List of Published Elementor Blog templates.
	 *
	 * @since 1.0.0
	 *
	 * @return array Templates
	 */
	public static function get_templates_options( $template_type ) {
		$options = array();
		$templates = Plugin::elementor()->templates_manager->get_source( 'local' )->get_items( array( 'type' => $template_type ) );

		foreach ( $templates as $template ) {
			if ( empty( $template['template_id'] ) || ! self::check_template( $template['template_id'] ) ) {
				continue;
			}

			$options[ $template['template_id'] ] = $template['title'] . ' (#' . $template['template_id'] . ')';
		}

		return $options;
	}

	/**
	 * is status post is publish.
	 *
	 * @param int|WP_Post $post Optional. Post ID or post object. Defaults to global $post..
	 *
	 * @return bool True on success, false on failure.
	 */
	public static function is_publish( $post = null ) {
		if ( ! $post && null !== $post ) {
			return false;
		}

		$post = get_post( $post );

		if ( ! $post ) {
			return false;
		}

		return 'publish' === get_post_status( $post );
	}

	public static function get_current_function_of_hook( $priority = 10 ) {
		global $wp_filter;

		return current( $wp_filter[ current_filter() ]->callbacks[ $priority ] )['function'];
	}

	public static function get_ensure_upload_dir( $path ) {
		if ( file_exists( $path . '/index.php' ) ) {
			return $path;
		}

		wp_mkdir_p( $path );

		self::create_file(
			$path,
			'index.php',
			'<?php' . PHP_EOL .
			'// Silence is golden.'
		);

		self::create_file(
			$path,
			'.htaccess',
			'Options -Indexes' . PHP_EOL .
			'<ifModule mod_headers.c>' . PHP_EOL .
			"\t<Files *.*>" . PHP_EOL .
			"\t\tHeader set Content-Disposition attachment" . PHP_EOL .
			"\t</Files>" . PHP_EOL .
			'</IfModule>'
		);

		return $path;
	}

	public static function create_file( $path, $file, $content ) {
		$path_file = trailingslashit( $path ) . $file;

		if ( ! file_exists( $path_file ) ) {
			self::get_wp_filesystem()->put_contents( $path_file, $content );
		}
	}

	public static function upload_and_extract_zip() {
		$zip_file = self::upload_zip();

		if ( is_wp_error( $zip_file ) ) {
			return $zip_file;
		}

		$wp_filesystem = self::get_wp_filesystem();

		$extract_to = trailingslashit( get_temp_dir() . pathinfo( $zip_file, PATHINFO_FILENAME ) );
		$unzipped = self::unzip_archive_to( $zip_file, $extract_to );

		if ( is_wp_error( $unzipped ) ) {
			return $unzipped;
		}

		$source_files = array_keys( $wp_filesystem->dirlist( $extract_to ) ); // Find the right folder.

		if ( 0 === count( $source_files ) ) {
			return new \WP_Error( 'incompatible_archive', esc_html__( 'Incompatible archive', 'cmsmasters-elementor' ) );
		} elseif (
			1 === count( $source_files ) &&
			$wp_filesystem->is_dir( $extract_to . $source_files[0] )
		) {
			$directory = $extract_to . trailingslashit( $source_files[0] );
		} else {
			$directory = $extract_to;
		}

		return $directory;
	}

	private static function upload_zip() {
		$file = $_FILES['zip_upload'];
		$filename = $_FILES['zip_upload']['name'];
		$ext = pathinfo( $filename, PATHINFO_EXTENSION );

		if ( 'zip' !== $ext ) {
			unlink( $_FILES['zip_upload']['name'] );

			return new \WP_Error( 'unsupported_file', __( 'Only zip files are allowed', 'cmsmasters-elementor' ) );
		}

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		add_filter( 'upload_mimes', array( __CLASS__, 'upload_mimes' ) );

		$upload_result = wp_handle_upload( $file, array( 'test_form' => false ) ); // Handler upload archive file.

		remove_filter( 'upload_mimes', array( __CLASS__, 'upload_mimes' ) );

		if ( isset( $upload_result['error'] ) ) {
			unlink( $_FILES['zip_upload']['name'] );

			return new \WP_Error( 'upload_error', $upload_result['error'] );
		}

		return $upload_result['file'];
	}

	private static function unzip_archive_to( $file, $to ) {
		add_filter( 'upload_mimes', array( __CLASS__, 'upload_mimes' ) );

		$unzip_result = unzip_file( $file, $to );

		remove_filter( 'upload_mimes', array( __CLASS__, 'upload_mimes' ) );

		@unlink( $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

		return $unzip_result; // TRUE | WP_Error instance.
	}

	public static function upload_mimes( $mime_types ) {
		$mime_types['zip'] = 'application/zip';

		return $mime_types;
	}

	public static function extract_zip( $file, $to ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		return self::unzip_archive_to( $file, $to );
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public static function get_ob_html( $callback ) {
		if ( ! is_callable( $callback ) ) {
			return '';
		}

		ob_start();

		call_user_func( $callback );

		return ob_get_clean();
	}

	/**
	 * Render Icon
	 *
	 * Used to render Icon for \Elementor\Controls_Manager::ICONS
	 * @param array $icon Icon Type, Icon value
	 * @param array $attributes Icon HTML Attributes
	 * @param bool $with_wrap With wrapper or not
	 */
	public static function render_icon( $icon_setting, $attributes = array(), $with_wrap = true ) {
		if ( empty( $icon_setting['value'] ) ) {
			return;
		}

		if ( $with_wrap ) {
			echo '<span class="cmsmasters-wrap-icon">';
		}

		Icons_Manager::render_icon( $icon_setting, $attributes );

		if ( $with_wrap ) {
			echo '</span>';
		}
	}

	/**
	 * Render Icon
	 *
	 * Used to render Icon for \Elementor\Controls_Manager::ICONS
	 * @param array $icon Icon Type, Icon value
	 * @param array $attributes Icon HTML Attributes
	 * @param bool $with_wrap With wrapper or not
	 */
	public static function get_render_icon( $icon_setting, $attributes = array(), $with_wrap = true ) {
		return self::get_ob_html( function() use ( $icon_setting, $attributes, $with_wrap ) {
			self::render_icon( $icon_setting, $attributes, $with_wrap );
		} );
	}

	/**
	 * Get available image sizes.
	 *
	 * @since 1.0.0
	 *
	 * @return array Image sizes.
	 */
	public static function get_available_image_sizes() {
		$glob_sizes = wp_get_additional_image_sizes();
		$image_sizes = get_intermediate_image_sizes();
		$sizes = array();

		if ( is_array( $image_sizes ) && $image_sizes ) {
			foreach ( $image_sizes as $size ) {
				if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ), true ) ) {
					$sizes[ $size ] = array(
						'width' => get_option( "{$size}_size_w" ),
						'height' => get_option( "{$size}_size_h" ),
						'crop' => (bool) get_option( "{$size}_crop" ),
					);
				} elseif ( isset( $glob_sizes[ $size ] ) ) {
					$sizes[ $size ] = array(
						'width' => $glob_sizes[ $size ]['width'],
						'height' => $glob_sizes[ $size ]['height'],
						'crop' => $glob_sizes[ $size ]['crop'],
					);
				}

				if ( 0 === (int) $sizes[ $size ]['width'] || 0 === (int) $sizes[ $size ]['height'] ) {
					unset( $sizes[ $size ] );
				}
			}
		}

		return $sizes;
	}

	/**
	 * Prepare css var.
	 *
	 * @since 1.1.0
	 *
	 * @param string $control_id Control id.
	 * @param string $value Control selectors value.
	 *
	 * @return string Control id that prepared for css vars.
	 */
	public static function prepare_css_var( $control_id, $value = '' ) {
		$var_key = '--' . str_replace( '_', '-', $control_id );

		if ( empty( $value ) ) {
			return $var_key;
		}

		return $var_key . ': ' . $value . ';';
	}

	/**
	* Get Elementor active kit ID.
	*
	* @since 1.0.0
	*
	* @return string Elementor active kit ID.
	*/
	public static function get_active_kit() {
		$active_kit = get_option( 'elementor_active_kit', '' );

		return apply_filters( 'cmsmasters_translated_template_id', $active_kit );
	}

	/**
	* Get kit options.
	*
	* @since 1.0.0
	*
	* @return array Kit options.
	*/
	public static function get_kit_options() {
		$active_kit = self::get_active_kit();

		return get_post_meta( $active_kit, '_elementor_page_settings', true );
	}

	/**
	 * Get kit option.
	 *
	 * @since 1.0.0
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
	 * @since 1.0.0
	 *
	 * @return array default kits.
	 */
	public static function get_default_kits() {
		if ( ! defined( 'CMSMASTERS_OPTIONS_PREFIX' ) ) {
			return array();
		}

		return get_option( CMSMASTERS_OPTIONS_PREFIX . 'default_kits', array() );
	}

	/**
	 * Get default kit.
	 *
	 * @since 1.0.0
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
	 * Rewrite widgets external media url.
	 *
	 * @since 1.11.2
	 *
	 * @param string $type image/media.
	 * 		image - rewrite images in widgets.
	 * 		media - rewrite media in widgets.
	 */
	public static function rewrite_widgets_external_media_url( $type = 'image' ) {
		if ( 'media' === $type ) {
			$site_url = get_site_url();

			if (
				false !== strpos( $site_url, '.cmsmasters.net' ) ||
				false !== strpos( $site_url, '.seaside-themes.com' )
			) {
				return;
			}
		}

		global $wpdb;

		$meta_value_like = '"([^"]*\.(cmsmasters\.net|seaside-themes\.com)[^"]*uploads[^"]*)"';

		if ( 'image' === $type ) {
			$meta_value_like = '"id":"","url":' . $meta_value_like . ',"source":"url"';
		}
		
		$query = $wpdb->prepare(
			"SELECT `post_id`, `meta_value` FROM {$wpdb->postmeta} " .
			"WHERE `meta_key` = '_elementor_data' AND `meta_value` REGEXP %s;",
			$meta_value_like
		);
		$results = $wpdb->get_results( $query );

		if ( empty( $results ) ) {
			return;
		}

		$urls_to_replace = array();

		foreach ( $results as $row ) {
			$meta_value = $row->meta_value;

			if ( preg_match_all( '/' . $meta_value_like . '/', $meta_value, $matches ) ) {
				$urls_to_replace = array_merge( $urls_to_replace, $matches[1] );
			}
		}

		$urls_to_replace = array_unique( $urls_to_replace );

		foreach ( $urls_to_replace as $old_media_url ) {
			$old_media_url = esc_url( $old_media_url );

			$new_media_data = self::download_media( $old_media_url );

			$new_media_id = $new_media_data['id'];
			$new_media_url = ( 'image' === $type && empty( $new_media_data['url'] ) ? Elementor_Utils::get_placeholder_image_src() : $new_media_data['url'] );

			$old_media_url = trim( $old_media_url );
			$new_media_url = trim( $new_media_url );

			$old_string = str_replace( '/', '\\/', $old_media_url );
			$new_string = str_replace( '/', '\\/', $new_media_url );
			$replace_meta_value_like = '[%';

			if ( 'image' === $type ) {
				$old_string = '"id":"","url":"' . $old_string . '","source":"url"';
				$new_string = '"id":"' . $new_media_id . '","url":"' . $new_string . '"';
			}

			$rows_affected = $wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->postmeta} " .
					'SET `meta_value` = REPLACE(`meta_value`, %s, %s) ' .
					"WHERE `meta_key` = '_elementor_data' AND `meta_value` LIKE %s;",
					$old_string,
					$new_string,
					$replace_meta_value_like
				)
			);

			if ( false === $rows_affected ) {
				continue;
			}
		}
	}

	/**
	 * Download media.
	 *
	 * @since 1.11.2
	 *
	 * @param string $media_url Media URL.
	 *
	 * @return array media data.
	 */
	public static function download_media( $media_url ) {
		$tmp_file = download_url( $media_url );

		$out = array(
			'id' => '',
			'url' => '',
		);

		if ( is_wp_error( $tmp_file ) ) {
			return $out;
		}

		$file_array = array(
			'name' => basename( $media_url ),
			'tmp_name' => $tmp_file,
		);

		$new_media_id = media_handle_sideload( $file_array, 0 );

		if ( is_wp_error( $new_media_id ) ) {
			@unlink( $tmp_file );

			return $out;
		}

		$out['id'] = $new_media_id;
		$out['url'] = wp_get_attachment_url( $new_media_id );

		return $out;
	}

	/**
	 * Check template.
	 *
	 * @since 1.12.1
	 *
	 * @param mixed $post Post ID/Object.
	 *
	 * @return bool
	 */
	public static function check_template( $post = null ) {
		if ( ! isset( $post ) || empty( $post ) ) {
			return false;
		}

		$post = get_post( $post );

		if ( ! $post ) {
			return false;
		}

		if ( 'publish' !== $post->post_status || 'elementor_library' !== $post->post_type ) {
			return false;
		}

		return true;
	}

}
