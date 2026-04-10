<?php
namespace CmsmastersFramework\Admin\Options\Pages;

use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\Utils;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Demos handler class is responsible for different methods on demos theme options page.
 *
 * @since 1.0.0
 */
class Demos extends Base\Base_Page {

	/**
	 * Page constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_cmsmasters_apply_demo', array( $this, 'ajax_apply_demo' ) );

		add_action( 'admin_notices', array( $this, 'page_info_notice' ), 9 );
	}

	/**
	 * Get page title.
	 *
	 * @since 1.0.0
	 */
	public static function get_page_title() {
		return esc_attr__( 'Designs', 'cmsmasters-framework' );
	}

	/**
	 * Get menu title.
	 *
	 * @since 1.0.0
	 */
	public static function get_menu_title() {
		return esc_attr__( 'Designs', 'cmsmasters-framework' );
	}

	/**
	 * Render page content.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		$parent_class = 'cmsmasters-options-demos';

		echo '<div class="' . esc_attr( $parent_class ) . '">' .
			'<div class="' . esc_attr( $parent_class ) . '-notice">' .
				'<div class="' . esc_attr( $parent_class ) . '-notice__inner">' .
					'<div class="' . esc_attr( $parent_class ) . '-notice__message"></div>' .
					'<button type="button" class="' . esc_attr( $parent_class ) . '-notice__button cmsmasters-button-spinner">' . esc_html__( 'Finish', 'cmsmasters-framework' ) . '</button>' .
				'</div>' .
			'</div>' .
			'<ul class="' . esc_attr( $parent_class ) . '__list">';

		$demos_list = array();

		$data = API_Requests::get_request( 'get-demos-list' );

		if ( is_wp_error( $data ) ) {
			Logger::error( $data->get_error_message() );
		} else {
			$demos_list = $data;
		}

		foreach ( $demos_list as $demo_key => $demo_args ) {
			$name = ( isset( $demo_args['name'] ) ? $demo_args['name'] : false );
			$preview_url = ( isset( $demo_args['preview_url'] ) ? $demo_args['preview_url'] : false );
			$preview_img_url = ( isset( $demo_args['preview_img_url'] ) ? $demo_args['preview_img_url'] : false );

			if ( 'demos' === CMSMASTERS_THEME_IMPORT_TYPE ) {
				$active_demo = Utils::get_demo();
			} else {
				$active_demo = Utils::get_demo_kit();
			}

			echo '<li class="' . esc_attr( $parent_class ) . '__item' . ( $active_demo === $demo_key ? ' cmsmasters-active' : '' ) . '">' .
				'<figure class="' . esc_attr( $parent_class ) . '__item-image">' .
					'<span class="dashicons dashicons-format-image"></span>' .
					( $preview_img_url ? '<img src="' . esc_url( $preview_img_url ) . '" />' : '' ) .
					( $preview_url ? '<a href="' . esc_url( $preview_url ) . '" target="_blank" class="' . esc_attr( $parent_class ) . '__item-preview"><span title="' . esc_attr( $name ) . '">' . esc_html__( 'Demo Preview', 'cmsmasters-framework' ) . '</span></a>' : '' ) .
				'</figure>' .
				'<div class="' . esc_attr( $parent_class ) . '__item-info">' .
					( $name ? '<h3 class="' . esc_attr( $parent_class ) . '__item-title">' . esc_html( $name ) . '</h3>' : '' ) .
					'<div class="' . esc_attr( $parent_class ) . '__item-buttons">' .
						'<a href="' . esc_url( get_admin_url() . 'admin.php?page=cmsmasters-options-license' ) . '" class="button cmsmasters-button-spinner' . ( API_Requests::check_token_status() ? ' cmsmasters-demo-apply-button' : '' ) . '" data-demo-key="' . esc_attr( $demo_key ) . '">' . esc_html__( 'Apply', 'cmsmasters-framework' ) . '</a>' .
						'<span class="' . esc_attr( $parent_class ) . '__item-status-active"></span>' .
					'</div>' .
				'</div>' .
			'</li>';
		}

			echo '</ul>' .
		'</div>';
	}

	/**
	 * Apply demo.
	 *
	 * @since 1.0.0
	 */
	public function ajax_apply_demo() {
		if ( ! check_ajax_referer( 'cmsmasters_options_nonce', 'nonce' ) ) {
			wp_send_json( array(
				'success' => false,
				'code' => 'invalid_nonce',
				'message' => esc_html__( 'Yikes! Demo activation failed. Please try again.', 'cmsmasters-framework' ),
			) );
		}

		if ( ! isset( $_POST['demo_key'] ) ) {
			wp_send_json( array(
				'success' => false,
				'code' => 'empty_demo_data',
				'message' => esc_html__( 'Empty demo data.', 'cmsmasters-framework' ),
			) );
		}

		if ( ! did_action( 'cmsmasters_set_backup_options' ) ) {
			do_action( 'cmsmasters_set_backup_options', false );
		}

		$demo_key = $_POST['demo_key'];

		if ( 'demos' === CMSMASTERS_THEME_IMPORT_TYPE ) {
			Utils::set_demo( $demo_key );
		} else {
			Utils::set_demo_kit( $demo_key );
		}

		if ( ! did_action( 'cmsmasters_set_import_status' ) ) {
			do_action( 'cmsmasters_set_import_status', 'pending' );
		}

		if ( ! did_action( 'cmsmasters_set_apply_demo_status' ) ) {
			do_action( 'cmsmasters_set_apply_demo_status', 'pending' );
		}

		do_action( 'cmsmasters_remove_temp_data' );

		update_option( 'cmsmasters_apply_demo_notice_visibility', 'show', false );

		wp_send_json( array(
			'success' => true,
			'message' => wp_kses_post(
				'<h3>' . __( 'New design is now applied!', 'cmsmasters-framework' ) . '</h3>' .
				'<p>' . __( 'Click Finish and allow a few seconds after that for new appearance to be applied (do not reload your page).', 'cmsmasters-framework' ) . '</p>' .
				( 'demos' === CMSMASTERS_THEME_IMPORT_TYPE ? '<p>' . sprintf(
					__( 'We recommend that you run a %1$sRegenerate Thumbnails%2$s tool to resize existing images for your new design.', 'cmsmasters-framework' ),
					'<a href="' . esc_url( 'https://wordpress.org/plugins/regenerate-thumbnails/' ) . '" target="_blank">',
					'</a>'
				) . '</p>' : '' )
			),
		) );
	}

	/**
	 * Page info notice.
	 *
	 * @since 1.0.0
	 */
	public function page_info_notice() {
		if ( 'demos' === CMSMASTERS_THEME_IMPORT_TYPE ) {
			$text = '<p><strong>' . esc_html__( 'Important!', 'cmsmasters-framework' ) . '</strong> ' . esc_html__( 'Every time you apply a new design, it changes the Global Colors, Global Fonts and Theme Settings, and also imports templates from another demo to the Template Library and some data to your database. Not a big deal if used in moderation, but if you just want to test different designs, it may be a good idea to do this on a test site.', 'cmsmasters-framework' ) . '</p>' .
			'<p>' . esc_html__( "Please note that changing the theme design will not change the appearance of your website to look exactly like the demo website, as it won't import the content from the new demo. If you want to use a different demo content, you'll need to do a fresh WordPress installation (clear the database) and import the desired demo content.", 'cmsmasters-framework' );
		} else {
			$text = '<p><strong>' . esc_html__( 'Applying the design will change only the design system (Global Colors, Global Fonts settings) and Theme Settings.', 'cmsmasters-framework' ) . '</strong></p>' .
			'<p>' .
				sprintf(
					__( 'You can read about this, along with more information and tips on switching design concepts, in our docs article - %1$sopen article%2$s.', 'cmsmasters-framework' ),
					'<a href="' . esc_url( 'https://docs.cmsmasters.net/how-to-switch-from-one-design-concept-to-another/' ) . '" target="_blank">',
					'</a>'
				) .
			'</p>';
		}

		echo '<div class="notice notice-info">' . $text . '</div>';
	}

}
