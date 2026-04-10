<?php
namespace CmsmastersElementor\Modules\Weather;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\Settings\Settings_Page;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends Base_Module {
	const OPTION_NAME_API_KEY = 'open_weather_map_api_key';
	const OPTION_NAME_LOCATION_DEFAULT = 'location_default';

	public function get_name() {
		return 'cmsmasters_weather';
	}

	protected function init_actions() {
		if ( is_admin() ) {
			add_action( 'elementor/admin/after_create_settings/' . Settings_Page::PAGE_ID, array( $this, 'register_admin_fields' ), 100 );
		}
	}

	public function get_widgets() {
		return array(
			'Weather',
		);
	}

	/**
	 * Register CMSMasters fields in dashboard.
	 *
	 * Fired by `elementor/admin/after_create_settings/cmsmasters` Cmsmasters action hook.
	 *
	 * @since 1.4.0
	 *
	 * @param Settings_Page $settings Cmsmasters "Settings" page in WordPress dashboard.
	 */
	public function register_admin_fields( Settings_Page $settings ) {
		$settings->add_section( 'cmsmasters', 'weather', array(
			'callback' => function () {
				echo '<br><hr><br>' .
				'<h2>' . esc_html__( 'Open Weather', 'cmsmasters-elementor' ) . '</h2>';
			},
			'fields' => array(
				self::OPTION_NAME_LOCATION_DEFAULT => array(
					'label' => __( 'Default Location', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
						'std' => __( 'New York, United States of America', 'cmsmasters-elementor' ),
						'desc' => __( 'City, Country', 'cmsmasters-elementor' ),
					),
				),
				self::OPTION_NAME_API_KEY => array(
					'label' => __( 'API Key', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
						'desc' => sprintf( __( 'Open Weather %s', 'cmsmasters-elementor' ) . '.',
							sprintf(
								'<a href="%1$s" target="_blank">%2$s</a>',
								'https://openweathermap.org/appid',
								__( 'API Key', 'cmsmasters-elementor' )
							)
						),
					),
				),
			),
		) );
	}

}
