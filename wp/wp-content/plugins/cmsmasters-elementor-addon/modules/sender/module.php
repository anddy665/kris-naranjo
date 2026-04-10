<?php
namespace CmsmastersElementor\Modules\Sender;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\AjaxWidget\Module as AjaxWidgetModule;
use CmsmastersElementor\Modules\AjaxWidget\Classes\Ajax_Action_Handler;
use CmsmastersElementor\Modules\Sender\Widgets\Sender;
use CmsmastersElementor\Modules\Settings\Settings_Page;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters Elementor sender module.
 *
 * @since 1.15.0
 */
class Module extends Base_Module {

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.15.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-sender';
	}

	/**
	 * Module activation.
	 *
	 * Check if module is active.
	 *
	 * @since 1.17.1 Fixed display Sender widget when there is no Sender plugin activated.
	 *
	 * @return bool
	 */
	public static function is_active() {
		return class_exists( 'Sender_Automated_Emails' );
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.15.0
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		return array( 'Sender' );
	}

	/**
	 * Enqueue admin assets.
	 */
	public function enqueue_admin_assets() {
		// Scripts
		wp_enqueue_script(
			'sender_form_option',
			$this->get_js_assets_url( 'frontend' ),
			array( 'jquery' ),
			'1.0.0',
			true
		);

		wp_localize_script( 'sender_form_option', 'sender_forms_api', array(
			'api' => get_option( 'sender_api_key' ),
		) );
	}

	/**
	 * Register admin fields.
	 *
	 * Register api fields for sender widget.
	 *
	 * @since 1.15.0
	 *
	 */
	public function register_admin_fields( Settings_Page $settings ) {
		$settings->add_section( 'cmsmasters', 'sender', array(
			'callback' => function () {
				echo '<br><hr><br><h2>' . esc_html__( 'Sender', 'cmsmasters-elementor' ) . '</h2>';
			},
			'fields' => array(
				'sender_api_key' => array(
					'label' => __( 'API Key', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
						'desc' => sprintf( __( 'Sender', 'cmsmasters-elementor' ) . '. %s.',
							sprintf( '<a href="%1$s" target="_blank">%2$s</a>',
								'https://app.sender.net/settings/tokens',
								__( 'Get API Key', 'cmsmasters-elementor' )
							)
						),
					),
				),
			),
		) );
	}

	/**
	 * Admin modules constructor.
	 *
	 * Run modules for admin.
	 */
	protected function init_actions() {
		add_action( 'elementor/frontend/after_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		if ( is_admin() ) {
			add_action( 'elementor/admin/after_create_settings/' . Settings_Page::PAGE_ID, array( $this, 'register_admin_fields' ), 100 );
		}
	}
}
