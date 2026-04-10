<?php
namespace PediatricClinicSpace\Kits\Settings\Archive;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Archive Post settings.
 */
class Post extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'archive_post';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Post', 'pediatric-clinic' );
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
		$this->add_responsive_control(
			'gap',
			array(
				'label' => esc_html__( 'Gap Between', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'%' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'box_heading_control',
			array(
				'label' => esc_html__( 'Container', 'pediatric-clinic' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_controls_group( '', self::CONTROLS_CONTAINER_BOX, array(
			'popover' => false,
			'excludes' => array( 'margin' ),
		) );
	}

}
