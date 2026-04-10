<?php
namespace PediatricClinicSpace\Kits\Settings\Footer;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Footer Logo settings.
 */
class Logo extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'footer_logo';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Logo', 'pediatric-clinic' );
	}

	/**
	 * Get control ID prefix.
	 *
	 * Retrieve the control ID prefix.
	 *
	 * @return string Control ID prefix.
	 */
	protected static function get_control_id_prefix() {
		return parent::get_control_id_prefix() . '_footer';
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
			'condition' => array( $this->get_control_id_parameter( '', 'elements' ) => 'logo' ),
		);
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$this->add_control(
			'logo_image',
			array(
				'show_label' => false,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			)
		);

		$this->add_control(
			'logo_retina_toggle',
			array(
				'label' => esc_html__( 'Retina Logo', 'pediatric-clinic' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array( $this->get_control_id_parameter( '', 'logo_image[url]!' ) => '' ),
			)
		);

		$this->start_popover();

		$this->add_control(
			'logo_retina_image',
			array(
				'show_label' => false,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array( $this->get_control_id_parameter( '', 'logo_retina_toggle' ) => 'yes' ),
			)
		);

		$this->end_popover();

		$this->add_control(
			'logo_second_toggle',
			array(
				'label' => esc_html__( 'Second Logo Image', 'pediatric-clinic' ),
				'description' => sprintf(
					'%1$s <a href="https://docs.cmsmasters.net/mode-switcher/" target="_blank">%2$s</a>.',
					__( 'Image that will be applied when using the', 'pediatric-clinic' ),
					__( 'Mode Switcher', 'pediatric-clinic' )
				),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array( $this->get_control_id_parameter( '', 'logo_image[url]!' ) => '' ),
			)
		);

		$this->start_popover();

		$this->add_control(
			'logo_image_second',
			array(
				'label' => esc_html__( 'Second Logo Image', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array( $this->get_control_id_parameter( '', 'logo_second_toggle' ) => 'yes' ),
			)
		);

		$this->add_control(
			'logo_retina_image_second',
			array(
				'label' => esc_html__( 'Second Retina Logo Image', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array( $this->get_control_id_parameter( '', 'logo_second_toggle' ) => 'yes' ),
			)
		);

		$this->end_popover();
	}

}
