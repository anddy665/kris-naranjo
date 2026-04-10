<?php
namespace CmsmastersFramework\Admin\Options;

use CmsmastersFramework\Core\Utils\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Options Utils handler class is responsible for different methods on theme options.
 *
 * @since 1.0.0
 */
class Options_Utils {

	/**
	 * Menu slug.
	 *
	 * @since 1.0.0
	 */
	const MENU_SLUG = 'cmsmasters-options';

	/**
	 * Menu icon.
	 *
	 * @since 1.0.0
	 */
	const MENU_ICON = 'PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxOCIgaGVpZ2h0PSIxNCIgdmlld0JveD0iMCAwIDM0MiAyNjEiIGZpbGw9Im5vbmUiPjxnIGNsaXAtcGF0aD0idXJsKCNjbGlwMCkiPjxwYXRoIGQ9Ik0xNTAuMDM5IDE4MS45NzJMMTUwLjAzNyAxODEuOTczQzEzNS44MDIgMTkyLjE1NCAxMTkuMjc4IDE5Ny43MTUgMTAwLjQgMTk4LjYyMVYxODEuMDk3QzExNC41NyAxODAuOTc5IDEyNi45MzQgMTc3LjI4MSAxMzcuNzEzIDE3MC4wNjJDMTQ4LjI4MiAxNjIuOTgyIDE1Ni4xNTcgMTUzLjU3NyAxNjEuNDEgMTQxLjhIMTgwLjYzN0MxNzQuMTg3IDE1OC42NzMgMTYzLjk1OSAxNzIuMDQyIDE1MC4wMzkgMTgxLjk3MloiIHN0cm9rZT0iI2U2NWUyYyIgc3Ryb2tlLXdpZHRoPSI0Ii8+PHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xMDEuNSAxNzkuMUMxMDAuOSAxNzkuMSAxMDAuNCAxNzkuMSA5OS44IDE3OS4xQzc0LjMgMTc5LjEgNTQuOSAxNzEuNSA0MS41IDE1Ni4zQzI4LjcgMTQxLjkgMjIuMyAxMjMuMiAyMi4zIDEwMC40QzIyLjMgNzcuNSAyOC43IDU4LjkgNDEuNSA0NC41QzQ3LjggMzcuNCA1NS42IDMxLjggNjQuOSAyNy44Qzc0LjIgMjMuOCA4NS44IDIxLjcgOTkuOCAyMS43QzExMy44IDIxLjcgMTI2LjEgMjUuMyAxMzYuNyAzMi40QzE0Ny4zIDM5LjUgMTU1LjEgNDkuMSAxNjAuMiA2MS4xSDE4My42QzE3Ny4xIDQyLjYgMTY2LjMgMjcuOSAxNTEuMyAxNy4zQzEzNS4zIDUuNyAxMTYuNCAwIDk0LjcgMEM2Ni4xIDAgNDIuOSA5LjggMjUuMiAyOS4zQzguNCA0OC4xIDAgNzEuNyAwIDEwMC4zQzAgMTI4LjkgOC40IDE1Mi42IDI1LjIgMTcxLjNDNDMgMTkwLjggNjYuMSAyMDAuNiA5NC44IDIwMC42Qzk3LjEgMjAwLjYgOTkuMyAyMDAuNSAxMDEuNSAyMDAuNFYxNzkuMVoiIGZpbGw9IiNlNjVlMmMiLz48cGF0aCBkPSJNMjI1LjM0OCAxNTkuMTQ1TDIyNS4zMzYgMTU5LjE0QzIxNS45NjQgMTU1LjQzMSAyMDguMjAyIDE1MC4zNzQgMjAyLjExNCAxNDQuMjg2QzE5Ni4yNjMgMTM4LjQzNSAxOTMuMiAxMzAuMzg1IDE5My4yIDEyMEMxOTMuMiAxMDUuMjk4IDE5OC44MzMgOTMuMzYzNCAyMTAuMDcxIDg0LjA0MzhDMjE4LjAwNyA3Ny41NTQzIDIzMC45ODggNzMuNjY1NCAyNDUuNSA3MS43NjIzVjczVjg2Ljg1MDdDMjQyLjQ4NiA4Ny40NTY3IDIzNy4zNjEgODguNTY3NSAyMzUuNjk4IDg5LjA5MjhMMjM1LjY3OCA4OS4wOTlMMjM1LjY1OSA4OS4xMDU2QzIyOS4wMDkgOTEuMzU2MyAyMjMuNjUzIDk0LjE2MjMgMjE5Ljc5NSA5Ny40ODQ1TDIxOS43ODMgOTcuNDk0N0wyMTkuNzcxIDk3LjUwNTJDMjE1Ljk3MyAxMDAuODgxIDIxMy4zMjcgMTA0LjU3MiAyMTEuOTkzIDEwOC43OThDMjEwLjcyNyAxMTIuODA1IDIxMC4xIDExNy4wMDkgMjEwLjEgMTIxLjVDMjEwLjEgMTI2LjQzOSAyMTIuMTk1IDEzMC44MzIgMjE2LjIwNSAxMzQuNzMzQzIyMC4xMyAxMzguNTUyIDIyNS4xNjUgMTQxLjY3OCAyMzEuNDQyIDE0NC4yNTFMMjMxLjQ0NyAxNDQuMjUzQzIzMi40MTIgMTQ0LjY0NSAyMzMuNzk5IDE0NS4yNTMgMjM1LjM5MyAxNDUuOTUzQzIzNi40MDMgMTQ2LjM5NiAyMzcuNDk3IDE0Ni44NzcgMjM4LjYyIDE0Ny4zNjFDMjQwLjgyMiAxNDguMzEyIDI0My4xNDQgMTQ5LjI4NCAyNDUgMTQ5Ljk0NVYxNjYuMzA2QzIzOC44IDE2NC4zNDcgMjMwLjMwOSAxNjEuMTQ3IDIyNS4zNDggMTU5LjE0NVpNMjA2LjE1MSAyMDdIMTg4LjA5OEMxODcuOTQ5IDIwNi4wMzUgMTg3LjgxOSAyMDUuMDc3IDE4Ny43MDcgMjA0LjFIMjA1Ljc0QzIwNS44NDEgMjA1LjA5IDIwNS45ODIgMjA2LjA1NyAyMDYuMTUxIDIwN1oiIHN0cm9rZT0iI2Q1NDE4YiIgc3Ryb2tlLXdpZHRoPSI0Ii8+PHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0yODQuNCA5MC44QzMwMi42IDk1LjkgMzEyLjYgMTA2LjIgMzE0LjUgMTIxLjhIMzM2LjRDMzM1LjYgMTA2LjcgMzI5LjMgOTQuMyAzMTcuNCA4NC42QzMwNC4zIDczLjYgMjg1LjkgNjguMSAyNjIgNjguMUMyNTYuNSA2OC4xIDI1MS40IDY4LjQgMjQ2LjUgNjguOVY4OC45QzI1MC43IDg4LjMgMjU1LjQgODggMjYwLjQgODhDMjY5LjkgODcuOSAyNzcuOSA4OC45IDI4NC40IDkwLjhaTTMyMSAxNzEuNEMzMTQuOSAxNjcuNCAzMDggMTY0LjIgMzAwLjMgMTYxLjZDMjkyLjUgMTU5IDI4NC42IDE1Ni44IDI3Ni42IDE1NC44QzI2OC42IDE1Mi44IDI2MC42IDE1MC45IDI1Mi45IDE0OUMyNTAuNyAxNDguNSAyNDguNiAxNDcuOSAyNDYuNSAxNDcuM1YxNjguM0MyNDkuNSAxNjkuMSAyNTIuNSAxNjkuOCAyNTUuNiAxNzAuNUMyNjYuNyAxNzMgMjc3IDE3NS42IDI4Ni42IDE3OC41QzI5Ni4yIDE4MS4zIDMwNC4xIDE4NC45IDMxMC41IDE4OS4zQzMxNi44IDE5My43IDMyMCAxOTkuNiAzMjAgMjA3LjJDMzIwIDIyOS40IDMwMi40IDI0MC40IDI2Ny4yIDI0MC40QzI1MC4zIDI0MC40IDIzNi44IDIzNy41IDIyNi42IDIzMS42QzIxNC45IDIyNC44IDIwOC42IDIxNC45IDIwNy45IDIwMi4xSDE4NS43QzE4Ny40IDIyMC4zIDE5NC40IDIzNC4zIDIwNi43IDI0NC4xQzIyMC41IDI1NS4xIDI0MC42IDI2MC42IDI2Ni45IDI2MC42QzI5MC4yIDI2MC42IDMwOC40IDI1NiAzMjEuNiAyNDYuN0MzMzQuOCAyMzcuNCAzNDEuMyAyMjQgMzQxLjMgMjA2LjRDMzQxLjIgMTkxLjYgMzM0LjUgMTc5LjkgMzIxIDE3MS40WiIgZmlsbD0iI2Q1NDE4YiIvPjxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgY2xpcC1ydWxlPSJldmVub2RkIiBkPSJNMTAxLjggMTk5LjdWMTk5SDgwLjVWMTk5LjdIMTAxLjhaTTI2My40IDM4LjNIMjM4LjdMMTcyLjUgMTI3LjVMMTA1LjIgMzguM0g4MC41VjE5OUgxMDEuOFY3MC43TDE3Mi41IDE2MC4yTDI0Mi4xIDcwLjdWMjI2LjNMMjQ3LjUgMjI3LjVDMjUyLjggMjI4LjMgMjU4LjQgMjI4LjcgMjYzLjUgMjI4LjhMMjYzLjQgMzguM1oiIGZpbGw9IiMzMGFhY2YiLz48L2c+PGRlZnM+PGNsaXBQYXRoIGlkPSJjbGlwMCI+PHJlY3Qgd2lkdGg9IjM0MS4yIiBoZWlnaHQ9IjI2MC42IiBmaWxsPSJ3aGl0ZSIvPjwvY2xpcFBhdGg+PC9kZWZzPjwvc3ZnPg==';

	/**
	 * Pages.
	 *
	 * @since 1.0.0
	 */
	public static $pages = array(
		'demos',
		'license',
		'license-data',
		'installation-log',
		'image-sizes',
		'generate-child',
		// 'example',
	);

	/**
	 * Main page.
	 *
	 * @since 1.0.0
	 */
	public static $main_page = 'demos';

	/**
	 * Get field value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Field id.
	 * @param string $sub_id Field sub id.
	 * @param mixed $std Field std property.
	 *
	 * @return mixed Field value.
	 */
	public static function get_field_value( $id, $sub_id = '', $std = '' ) {
		$options = Utils::get_theme_options();

		if (
			! isset( $options[ $id ] ) ||
			( is_array( $std ) && ! is_array( $options[ $id ] ) )
		) {
			$value = $std;
		} else {
			$value = $options[ $id ];

			if ( '' !== $sub_id ) {
				if ( ! isset( $value[ $sub_id ] ) ) {
					$value = $std;
				} else {
					$value = $value[ $sub_id ];
				}
			}
		}

		return $value;
	}

	/**
	 * Check validate value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Field id.
	 * @param string $sub_id Field sub id.
	 * @param mixed $input Field input data.
	 * @param array $args Field args.
	 *
	 * @return mixed Field value.
	 */
	public static function check_validate_input( $id = '', $sub_id = '', $input = array(), $args = array() ) {
		if (
			( '' === $sub_id && ! isset( $input[ $id ] ) ) ||
			( '' !== $sub_id && ! isset( $input[ $id ][ $sub_id ] ) )
		) {
			return '0';
		}

		if ( '' !== $sub_id ) {
			$input_val = $input[ $id ][ $sub_id ];
		} else {
			$input_val = $input[ $id ];
		}

		if ( true === $args['not_empty'] ) {
			if (
				is_array( $args['std'] ) &&
				(
					! is_array( $input_val ) ||
					( is_array( $input_val ) && empty( $input_val ) )
				)
			) {
				$input_val = $args['std'];
			} elseif ( ! is_array( $args['std'] ) && '' === $input_val ) {
				$input_val = $args['std'];
			}
		}

		return $input_val;
	}

	/**
	 * Get field label.
	 *
	 * @since 1.0.0
	 *
	 * @param string $label Field label.
	 * @param string $id Field id.
	 *
	 * @return string Field label HTML.
	 */
	public static function get_field_label( $label = '', $id = '' ) {
		$label = ( '' !== $label ? '<label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>' : '' );

		return $label;
	}

	/**
	 * Get field description.
	 *
	 * @since 1.0.0
	 *
	 * @param string $desc Field description.
	 *
	 * @return string Field description HTML.
	 */
	public static function get_field_desc( $desc = '' ) {
		$desc = ( '' !== $desc ? '<span class="description">' . $desc . '</span>' : '' );

		return $desc;
	}

	/**
	 * Get field postfix.
	 *
	 * @since 1.0.0
	 *
	 * @param string $desc Field postfix.
	 *
	 * @return string Field postfix HTML.
	 */
	public static function get_field_postfix( $postfix = '' ) {
		$postfix = ( '' !== $postfix ? '<span class="cmsmasters-options-field-postfix">' . $postfix . '</span>' : '' );

		return $postfix;
	}

	/**
	 * Get options message content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Message content.
	 * @param string $class Message class.
	 * @param string $option Option id.
	 *
	 * @return string Message content.
	 */
	public static function get_message_content( $content, $class, $option = '' ) {
		$error_option = '';
		$error_link = '';

		if ( '' !== $option ) {
			$error_option = ' data-option="' . esc_attr( $option ) . '"';
			$error_link = '<a href="#' . esc_attr( $option ) . '">' . esc_html__( 'Field error', 'cmsmasters-framework' ) . '</a>. ';
		}

		return '<div class="notice cmsmasters-options-notice ' . $class . '"' . $error_option . '>' .
			'<p>' . $error_link . $content . '</p>' .
		'</div>';
	}

	/**
	 * Get current theme options admin page.
	 *
	 * @since 1.0.0
	 */
	public static function get_admin_page() {
		if ( Utils::is_ajax() ) {
			if ( isset( $_POST['action'] ) ) {
				switch ( $_POST['action'] ) {
					case 'cmsmasters_activate_license':
					case 'cmsmasters_deactivate_license':
						return 'license';

						break;
					case 'cmsmasters_update_license_data':
						return 'license-data';

						break;
					case 'cmsmasters_generate_child':
						return 'generate-child';

						break;
					case 'cmsmasters_apply_demo':
						return 'demos';

						break;
				}
			}
		}

		global $pagenow;

		$cur_page = ( isset( $_GET['page'] ) ) ? trim( $_GET['page'] ) : '';

		if ( 'options.php' === $pagenow && isset( $_POST['_wp_http_referer'] ) ) {
			$parts = explode( 'page=', $_POST['_wp_http_referer'] );

			if ( isset( $parts[1] ) ) {
				$page = $parts[1];
				$t = strpos( $page, '&' );

				if ( false !== $t ) {
					$page = substr( $parts[1], 0, $t );
				}

				$cur_page = trim( $page );
			} else {
				$cur_page = '';
			}
		}

		if ( '' === $cur_page && false === strpos( $cur_page, self::MENU_SLUG ) ) {
			return '';
		}

		$out = '';

		if ( self::MENU_SLUG === $cur_page ) {
			$out = self::$main_page;
		} else {
			$out = substr( $cur_page, strlen( self::MENU_SLUG ) + 1 );
		}

		return $out;
	}

}
