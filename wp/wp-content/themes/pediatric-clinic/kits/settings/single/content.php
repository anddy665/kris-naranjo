<?php
namespace PediatricClinicSpace\Kits\Settings\Single;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Single Content settings.
 */
class Content extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'single_content';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Content', 'pediatric-clinic' );
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

		return parent::get_control_id_prefix() . '_single';
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
				$this->get_control_id_parameter( '', 'elements' ) => 'content',
			),
		);
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$this->add_responsive_control(
			'content_box_width',
			array(
				'label' => esc_html__( 'Max Width', 'pediatric-clinic' ),
				'description' => esc_html__( 'Maximum content width (%), applies only when the layout is fullwidth.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'size_units' => array(
					'%',
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'content_box_width' ) . ': {{SIZE}}%;',
				),
			)
		);

		$this->add_responsive_control(
			'content_box_padding',
			array(
				'label' => esc_html__( 'Padding', 'pediatric-clinic' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'content_box_padding_top' ) . ': {{TOP}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'content_box_padding_right' ) . ': {{RIGHT}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'content_box_padding_bottom' ) . ': {{BOTTOM}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'content_box_padding_left' ) . ': {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_box_margin',
			array(
				'label' => esc_html__( 'Margin', 'pediatric-clinic' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'placeholder' => array(
					'top' => '',
					'right' => 'auto',
					'bottom' => '',
					'left' => 'auto',
				),
				'allowed_dimensions' => array( 'top', 'bottom' ),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'content_box_margin_top' ) . ': {{TOP}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'content_box_margin_bottom' ) . ': {{BOTTOM}}{{UNIT}};',
				),
			)
		);
	}

}
