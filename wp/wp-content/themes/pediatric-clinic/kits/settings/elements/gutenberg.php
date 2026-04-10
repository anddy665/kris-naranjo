<?php
namespace PediatricClinicSpace\Kits\Settings\Elements;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Gutenberg settings.
 */
class Gutenberg extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'gutenberg';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Gutenberg', 'pediatric-clinic' );
	}

	/**
	 * Get control ID prefix.
	 *
	 * Retrieve the control ID prefix.
	 *
	 * @return string Control ID prefix.
	 */
	protected static function get_control_id_prefix() {
		$toggle_name = self::get_toggle_name();

		return parent::get_control_id_prefix() . "_{$toggle_name}";
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$this->add_control(
			'columns_heading_control',
			array(
				'label' => esc_html__( 'Columns', 'pediatric-clinic' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'columns_gap',
			array(
				'label' => esc_html__( 'Gap Between', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'size_units' => array(
					'%',
					'px',
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'columns_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'gallery_columns_heading_control',
			array(
				'label' => esc_html__( 'Gallery Columns', 'pediatric-clinic' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'gallery_columns_gap',
			array(
				'label' => esc_html__( 'Gap Between', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'size_units' => array(
					'%',
					'px',
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'gallery_columns_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);
	}

}
