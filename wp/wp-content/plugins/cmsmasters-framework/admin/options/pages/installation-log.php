<?php
namespace CmsmastersFramework\Admin\Options\Pages;

use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Installation_Log handler class is responsible for different methods on installation-log theme options page.
 *
 * @since 1.0.0
 */
class Installation_Log extends Base\Base_Page {

	/**
	 * Get page title.
	 *
	 * @since 1.0.0
	 */
	public static function get_page_title() {
		return esc_attr__( 'Installation Log', 'cmsmasters-framework' );
	}

	/**
	 * Get menu title.
	 *
	 * @since 1.0.0
	 */
	public static function get_menu_title() {
		return esc_attr__( 'Installation Log', 'cmsmasters-framework' );
	}

	/**
	 * Visibility Status.
	 *
	 * @since 1.0.0
	 */
	public static function get_visibility_status() {
		if ( 'run' !== get_option( CMSMASTERS_OPTIONS_PREFIX . 'installation_status' ) ) {
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
		echo '<div class="cmsmasters-options-installation-log">
			<div class="cmsmasters-options-installation-log__text">
				<p>' .
					esc_html__( 'The installation log is a record of the activities and events that occur during the theme installation process.', 'cmsmasters-framework' ) .
					'<br />' .
					esc_html__( 'It captures a wide range of information, including errors and warnings, and serves as a diagnostic tool for identifying errors that occur on a website.', 'cmsmasters-framework' ) .
				'</p>
			</div>
			<div class="cmsmasters-options-installation-log__button-wrap">
				<a href="' . esc_url( Logger::get_theme_log_url() ) . '" class="button" download>' . esc_html__( 'Download Installation Log', 'cmsmasters-framework' ) . '</a>
			</div>
		</div>';
	}

}
