<?php
namespace PediatricClinicSpace\TemplateFunctions;

use PediatricClinicSpace\Core\Utils\Utils;
use PediatricClinicSpace\TemplateFunctions\General_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Main Elements handler class is responsible for different methods in templates.
 */
class Main_Elements {

	/**
	 * Main wrapper start.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Main wrapper start HTML.
	 */
	public static function main_wrapper_start( $atts = array() ) {
		$req_vars = array(
			'add_classes' => array(),
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		$parent_class = 'cmsmasters-main';

		$add_classes = ( ! empty( $add_classes ) ? ' ' . implode( ' ', $add_classes ) : '' );

		$out = '<main id="main" class="' . esc_attr( $parent_class . $add_classes ) . ' site-main">' .
		'<div class="' . esc_attr( $parent_class ) . '__outer">' .
		'<div class="' . esc_attr( $parent_class ) . '__inner">' .
		'<div class="cmsmasters-content-wrap">' .
		'<div class="cmsmasters-content">';

		return $out;
	}

	/**
	 * Main wrapper end.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Main wrapper end HTML.
	 */
	public static function main_wrapper_end( $atts = array() ) {
		$req_vars = array(
			'sidebar' => true,
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		$out = '</div>'; // .cmsmasters-content

		if ( $sidebar ) {
			ob_start();

			get_sidebar();

			$sidebar_out = ob_get_clean();

			if ( ! empty( $sidebar_out ) ) {
				$out .= $sidebar_out;
			}
		}

		$out .= '</div>' . // .cmsmasters-content-wrap
		'</div>' . // .cmsmasters-main__inner
		'</div>' . // .cmsmasters-main__outer
		'</main>'; // .cmsmasters-main

		return $out;
	}

	/**
	 * Get main layout.
	 *
	 * @return string main layout.
	 */
	public static function get_main_layout( $page_id = false ) {
		$layout = '';

		if ( ! $page_id ) {
			$page_id = General_Elements::get_singular_id();
		}

		if ( is_404() || is_attachment() ) {
			$layout = 'fullwidth';
		} elseif ( is_archive() || is_home() ) {
			$layout = Utils::get_kit_option( 'cmsmasters_archive_layout', 'r-sidebar' );
		} elseif ( is_search() ) {
			$layout = Utils::get_kit_option( 'cmsmasters_search_layout', 'r-sidebar' );
		} elseif ( is_single() ) {
			$layout = Utils::get_kit_option( 'cmsmasters_single_layout', 'r-sidebar' );
		} else {
			$layout = Utils::get_kit_option( 'cmsmasters_main_layout', 'r-sidebar' );
		}

		if ( is_admin() && $page_id ) {
			if (
				'page' === get_post_type( $page_id ) ||
				'attachment' === get_post_type( $page_id )
			) {
				$layout = Utils::get_kit_option( 'cmsmasters_main_layout', 'r-sidebar' );
			} else {
				$layout = Utils::get_kit_option( 'cmsmasters_single_layout', 'r-sidebar' );
			}
		}

		if ( $page_id ) {
			$singular_layout = get_post_meta( $page_id, 'cmsmasters_layout', true );

			if ( false !== $singular_layout && '' !== $singular_layout && 'default' !== $singular_layout ) {
				$layout = $singular_layout;
			}
		}

		if ( '' === $layout ) {
			$layout = Utils::get_kit_option( 'cmsmasters_main_layout', 'r-sidebar' );
		}

		if ( 'fullwidth' !== $layout ) {
			if ( is_home() || is_archive() ) {
				if ( 
					! is_active_sidebar( 'sidebar_archive' ) &&
					! is_active_sidebar( 'sidebar_default' )
				) {
					$layout = 'fullwidth';
				}
			} elseif ( is_search() ) {
				if (
					! is_active_sidebar( 'sidebar_search' ) &&
					! is_active_sidebar( 'sidebar_default' )
				) {
					$layout = 'fullwidth';
				}
			} else {
				if ( ! is_active_sidebar( 'sidebar_default' ) ) {
					$layout = 'fullwidth';
				}
			}
		}

		$layout = apply_filters( 'cmsmasters_layout_filter', $layout );

		return $layout;
	}

}
