<?php
namespace CmsmastersElementor\Modules\IconFonts;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\IconFonts\Types\Local;
use CmsmastersElementor\Modules\Settings\Settings_Page;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Module extends Base_Module {

	const MODULE_DIR = __DIR__;
	const MODULE_NAMESPACE = __NAMESPACE__;

	public static $namespace = '';

	public function get_name() {
		return 'icon-fonts';
	}

	public function __construct() {
		$this->add_component( 'local', new Local() );

		parent::__construct();

		/**
		 * Addon icon fonts module loaded.
		 *
		 * Fires after the icons font module was fully loaded and instantiated.
		 *
		 * @since 1.0.0
		 *
		 * @param Module $this An instance of icon fonts module.
		 */
		do_action( 'cmsmasters_elementor/icon_fonts/loaded', $this );
	}

	/**
	 * Init actions.
	 *
	 * @since 1.17.4
	 */
	protected function init_actions() {
		// Ajax
		add_action( 'wp_ajax_cmsmasters_regenerate_local_icons', [ $this, 'ajax_regenerate_local_icons' ] );

		if ( is_admin() ) {
			add_action( 'elementor/admin/after_create_settings/' . Settings_Page::PAGE_ID, array( $this, 'register_admin_fields' ), 100 );
		}
	}

	protected function init_filters() {
		// Admin
		add_filter( 'cmsmasters_elementor/admin/settings', array( $this, 'filter_admin_settings' ) );
	}

	/**
	 * Get local icons.
	 *
	 * Retrieve the local icons module component.
	 *
	 * @return Local
	 */
	public function get_local_icons() {
		return $this->get_component( 'local' );
	}

	public function filter_admin_settings( $settings ) {
		$settings = array_replace_recursive( $settings, array(
			'i18n' => array(
				'iconsUploadEmptyNotice' => __( 'You need to upload an icons set to publish.', 'cmsmasters-elementor' ),
			),
		) );

		return $settings;
	}

	/**
	 * Register CMSMasters fields in dashboard.
	 *
	 * Fired by `elementor/admin/after_create_settings/cmsmasters` Cmsmasters action hook.
	 *
	 * @since 1.17.4
	 *
	 * @param Settings_Page $settings Cmsmasters "Settings" page in WordPress dashboard.
	 */
	public function register_admin_fields( Settings_Page $settings ) {
		$settings->add_section( 'tools', 'icon-fonts', array(
			'callback' => function () {
				echo '<h2>' . esc_html__( 'Local Icons Regeneration', 'cmsmasters-elementor' ) . '</h2>';
			},
			'fields' => array(
				'icon-fonts-regenerate' => array(
					'label' => __( 'Regenerate Local Icons', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'raw_html',
						'html' => sprintf( '<button data-nonce="%s" class="button elementor-button-spinner" id="cmsmasters-regenerate-local-icons-button">%s</button>', wp_create_nonce( 'cmsmasters-regenerate-local-icons-nonce' ), esc_html__( 'Regenerate Local Icons', 'cmsmasters-elementor' ) ),
						'desc' => esc_html__( "Run Local Icons regeneration to restore your previously used icons after changing the website domain.", 'cmsmasters-elementor' ),
					),
				),
			),
		) );
	}

	/**
	 * Ajax Regenerate local icons.
	 *
	 * @since 1.17.4
	 */
	public function ajax_regenerate_local_icons() {
		check_ajax_referer( 'cmsmasters-regenerate-local-icons-nonce', '_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Permission denied' );
		}

		Local::regenerate_local_icons();

		wp_send_json_success();
	}

}
