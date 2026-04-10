<?php
namespace CmsmastersFramework\Admin\Options\Pages;

use CmsmastersFramework\Core\Utils\Logger;
use CmsmastersFramework\Core\Utils\File_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Generate_Child handler class is responsible for different methods on child theme generation options page.
 *
 * @since 1.0.0
 */
class Generate_Child extends Base\Base_Page {

	/**
	 * Page constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_cmsmasters_generate_child', array( $this, 'ajax_generate_child' ) );
	}

	/**
	 * Get page title.
	 *
	 * @since 1.0.0
	 */
	public static function get_page_title() {
		return esc_attr__( 'Generate Child Theme', 'cmsmasters-framework' );
	}

	/**
	 * Get menu title.
	 *
	 * @since 1.0.0
	 */
	public static function get_menu_title() {
		return esc_attr__( 'Generate Child Theme', 'cmsmasters-framework' );
	}

	/**
	 * Visibility Status.
	 *
	 * @since 1.0.0
	 */
	public static function get_visibility_status() {
		if ( is_child_theme() ) {
			return false;
		}

		$theme = wp_get_theme();
		$name = $theme . ' Child';
		$slug = sanitize_title( $name );
		$path = get_theme_root() . '/' . $slug;

		if ( file_exists( $path ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Render page content.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		echo '<div class="cmsmasters-options-generate-child">
			<div class="cmsmasters-options-generate-child__text">
				<p>' .
					'<strong>' . esc_html__( 'You can create a child theme here.', 'cmsmasters-framework' ) . '</strong>' . '<br />' .
					esc_html__( 'Use a child theme if you plan to modify theme files or add custom functionality.', 'cmsmasters-framework' ) . '<br />' .
					esc_html__( 'It helps keep your changes safe when the main theme is updated.', 'cmsmasters-framework' ) . '<br />' .
					esc_html__( 'Just click the button below to generate the child theme. After it’s created, activate the child theme to start using it.', 'cmsmasters-framework' ) . '<br />' .
					sprintf(
						esc_html__( 'You can read more about the child themes in %1$s.', 'cmsmasters-framework' ),
						'<a href="' . esc_url( 'https://developer.wordpress.org/themes/advanced-topics/child-themes/' ) . '" target="_blank">' .
							esc_html__( 'this article', 'cmsmasters-framework' ) .
						'</a>'
					) .
				'</p>
			</div>
			<div class="cmsmasters-options-generate-child__button-wrap">
				<button type="button" class="button cmsmasters-button-spinner" data-action="generate-child">' . esc_html__( 'Generate Child Theme', 'cmsmasters-framework' ) . '</button>
				<span class="cmsmasters-notice"></span>
			</div>
		</div>';
	}

	/**
	 * Ajax update license data.
	 *
	 * @since 1.0.0
	 */
	public function ajax_generate_child() {
		if ( ! check_ajax_referer( 'cmsmasters_options_nonce', 'nonce' ) ) {
			wp_send_json( array(
				'success' => false,
				'code' => 'invalid_nonce',
				'message' => esc_html__( 'Yikes! Child theme generation failed. Please try again.', 'cmsmasters-framework' ),
			) );
		}

		$result = File_Manager::generate_child();
		
		if ( ! $result ) {
			wp_send_json(
				array(
					'success' => false,
					'message' => esc_html__( 'Yikes! Child theme generation failed. Please try again.', 'cmsmasters-framework' ),
				)
			);
		}

		wp_send_json( array(
			'success' => true,
			'message' => 'Awesome. Your child theme has already been generated. You can activated it.',
		) );
	}

}
