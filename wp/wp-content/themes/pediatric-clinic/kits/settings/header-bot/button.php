<?php
namespace PediatricClinicSpace\Kits\Settings\HeaderBot;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Header Bot Button settings.
 */
class Button extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'header_bot_button';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Button', 'pediatric-clinic' );
	}

	/**
	 * Get control ID prefix.
	 *
	 * Retrieve the control ID prefix.
	 *
	 * @return string Control ID prefix.
	 */
	protected static function get_control_id_prefix() {
		return parent::get_control_id_prefix() . '_header_bot';
	}

	/**
	 * Get toggle conditions.
	 *
	 * Retrieve the settings toggle conditions.
	 *
	 * @return array Toggle conditions.
	 */
	protected function get_toggle_conditions() {
		return array(
			'condition' => array(
				$this->get_control_id_parameter( '', 'visibility' ) => 'yes',
				$this->get_control_id_parameter( '', 'type' ) => 'wide',
				$this->get_control_id_parameter( '', 'add_content_elements' ) => 'button',
			),
		);
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$this->add_controls_group( 'button', self::CONTROLS_BUTTON_ICON, array(
			'states' => array(
				'normal' => esc_html__( 'Normal', 'pediatric-clinic' ),
				'hover' => esc_html__( 'Hover', 'pediatric-clinic' ),
			),
		) );

		$this->add_control(
			'button_apply_settings',
			array(
				'label_block' => true,
				'show_label' => false,
				'type' => Controls_Manager::BUTTON,
				'text' => esc_html__( 'Save & Reload', 'pediatric-clinic' ),
				'event' => 'cmsmasters:theme_settings:apply_settings',
				'separator' => 'before',
			)
		);
	}

}
