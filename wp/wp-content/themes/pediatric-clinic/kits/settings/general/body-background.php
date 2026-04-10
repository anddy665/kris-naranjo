<?php
namespace PediatricClinicSpace\Kits\Settings\General;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Body Background settings.
 */
class Body_Background extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the settings toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'body_background';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Body Background', 'pediatric-clinic' );
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$this->add_var_group_control( 'body', self::VAR_BACKGROUND, array(
			'fields_options' => array(
				'background' => array(
					'description' => esc_html__( 'Note: The background set in the Main Container, Header and Footer will overlay the Body Background.', 'pediatric-clinic' ),
				),
			),
		) );

		$this->add_control(
			'mobile_theme_color',
			array(
				'label' => esc_html__( 'Mobile Browser Background', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'description' => esc_html__( 'The `theme-color` meta tag will only be available in supported browsers and devices.', 'pediatric-clinic' ),
				'separator' => 'before',
			)
		);
	}

}
