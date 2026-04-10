<?php
namespace CmsmastersElementor\Modules\AjaxWidget;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\AjaxWidget\Classes\Ajax_Action_Handler;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * CMSMasters Elementor ajax-widget module.
 *
 * @since 1.0.0
 */
final class Module extends Base_Module {

	private static $is_active_ajax = false;

	/**
	 * Get module name.
	 *
	 * Retrieve the CMSMasters Ajax Widget module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters_ajax_widget';
	}

	/**
	 * Add actions initialization.
	 *
	 * Register actions for the Ajax Widget module.
	 *
	 * @since 1.0.0
	 */
	protected function init_actions() {
		add_action( 'cmsmasters_elementor/ajax_widget/before', array( __CLASS__, 'on_ajax_before' ) );
		add_action( 'cmsmasters_elementor/ajax_widget/after', array( __CLASS__, 'on_ajax_after' ) );
		add_action( 'cmsmasters_elementor/init', array( $this, 'cmsmasters_init' ) );
	}

	/**
	 * Add filters initialization.
	 *
	 * Register filters for the Ajax Widget module.
	 *
	 * @since 1.0.0
	 */
	protected function init_filters() {
		add_filter( 'cmsmasters_elementor/frontend/settings', array( $this, 'frontend_settings' ) );
	}

	/**
	 * Event before widget ajax.
	 *
	 * @since 1.0.0
	 */
	public static function on_ajax_before() {
		self::$is_active_ajax = true;
	}

	/**
	 * Event after widget ajax.
	 *
	 * @since 1.0.0
	 */
	public static function on_ajax_after() {
		self::$is_active_ajax = false;
	}

	/**
	 * On Cmsmasters-Elementor init.
	 *
	 * Add editor template for the settings
	 *
	 * Fired by `cmsmasters_elementor/init` action.
	 *
	 * @since 1.0.0
	 */
	public function cmsmasters_init() {
		/**
		 * Register ajax widget.
		 *
		 * @since 1.0.0
		 *
		 * @param Module $this Module instance.
		 */
		do_action( 'cmsmasters_elementor/ajax_widget/register', $this );
	}

	/**
	 * Filter frontend settings.
	 *
	 * Filters the Addon settings for elementor frontend.
	 *
	 * Fired by `cmsmasters_elementor/frontend/settings` Addon action hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Frontend settings.
	 *
	 * @return array Filtered frontend settings.
	 */
	public function frontend_settings( $settings ) {
		return array_replace_recursive( array(
			'nonces' => array(
				'ajax_widget' => wp_create_nonce( $this->get_nonce_name() ),
			),
		), $settings );
	}

	/**
	 * Get nonce code name.
	 *
	 * Retrieve the ajax widget nonce name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Nonce name.
	 */
	public function get_nonce_name() {
		return $this->get_name();
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 * @since 1.17.4 Added check nonce trigger.
	 */
	public function add_handler( $action_name, $callback, $check_nonce = true ) {
		return new Ajax_Action_Handler( $action_name, $callback, $check_nonce );
	}

	/**
	 * Check if ajax-handler is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_active_ajax() {
		return self::$is_active_ajax;
	}

}
