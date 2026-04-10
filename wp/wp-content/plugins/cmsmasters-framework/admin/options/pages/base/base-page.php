<?php
namespace CmsmastersFramework\Admin\Options\Pages\Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Handler class is responsible for different methods on theme options pages.
 *
 * @since 1.0.0
 */
class Base_Page {

	/**
	 * Get page title.
	 *
	 * @since 1.0.0
	 */
	public static function get_page_title() {
		return '';
	}

	/**
	 * Get menu title.
	 *
	 * @since 1.0.0
	 */
	public static function get_menu_title() {
		return '';
	}

	/**
	 * Visibility Status.
	 *
	 * @since 1.0.0
	 */
	public static function get_visibility_status() {
		return true;
	}

}
