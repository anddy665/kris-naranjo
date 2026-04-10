<?php
namespace PediatricClinicSpace\Kits\Settings\Elements;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Slider Progressbar settings.
 */
class Slider_Progressbar extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'slider_progressbar';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Slider Progressbar', 'pediatric-clinic' );
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
			'notice',
			array(
				'raw' => esc_html__( 'Used in: more posts, single post gallery, archive post gallery.', 'pediatric-clinic' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'render_type' => 'ui',
			)
		);

		$this->add_control(
			'normal_bg',
			array(
				'label' => esc_html__( 'Normal Background', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'normal_bg' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'fill_bg',
			array(
				'label' => esc_html__( 'Fill Background', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'fill_bg' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_var_group_control( '', self::VAR_BOX_SHADOW );

		$this->add_responsive_control(
			'thickness',
			array(
				'label' => esc_html__( 'Thickness', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 10,
						'min' => 2,
					),
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'thickness' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'bd_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 100,
						'min' => 0,
					),
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'bd_radius' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_heading_control',
			array(
				'label' => esc_html__( 'Container', 'pediatric-clinic' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'container_margin',
			array(
				'label' => esc_html__( 'Margin', 'pediatric-clinic' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'container_margin_top' ) . ': {{TOP}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'container_margin_right' ) . ': {{RIGHT}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'container_margin_bottom' ) . ': {{BOTTOM}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'container_margin_left' ) . ': {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_position',
			array(
				'label' => esc_html__( 'Position', 'pediatric-clinic' ),
				'label_block' => false,
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => esc_html__( 'Default', 'pediatric-clinic' ),
					'flex-start' => esc_html__( 'Start', 'pediatric-clinic' ),
					'flex-end' => esc_html__( 'End', 'pediatric-clinic' ),
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'container_position' ) . ': {{VALUE}};',
				),
			)
		);
	}

}
