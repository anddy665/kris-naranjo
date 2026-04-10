<?php
namespace PediatricClinicSpace\Kits\Settings\Footer;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Footer Navigation settings.
 */
class Nav extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'footer_nav';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Navigation', 'pediatric-clinic' );
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
			'condition' => array( $this->get_control_id_parameter( '', 'elements' ) => 'nav' ),
		);
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$this->add_var_group_control( 'nav', self::VAR_TYPOGRAPHY );

		$this->add_control(
			'nav_colors_heading_control',
			array(
				'label' => esc_html__( 'Colors', 'pediatric-clinic' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'nav_colors_normal',
			array(
				'label' => esc_html__( 'Normal', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'nav_colors_normal' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_colors_hover',
			array(
				'label' => esc_html__( 'Hover', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'nav_colors_hover' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_colors_current',
			array(
				'label' => esc_html__( 'Current', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'nav_colors_current' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'nav_colors_divider',
			array(
				'label' => esc_html__( 'Divider', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'nav_colors_divider' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'nav_gap',
			array(
				'label' => esc_html__( 'Gap Between', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'vw' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'size_units' => array(
					'px',
					'%',
					'vw',
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'nav_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);
	}

}
