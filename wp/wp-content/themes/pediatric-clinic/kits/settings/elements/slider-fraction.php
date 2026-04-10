<?php
namespace PediatricClinicSpace\Kits\Settings\Elements;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Slider Fraction settings.
 */
class Slider_Fraction extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'slider_fraction';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Slider Fraction', 'pediatric-clinic' );
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

		$this->add_var_group_control( '', self::VAR_TYPOGRAPHY );

		$this->add_control(
			'colors_text',
			array(
				'label' => esc_html__( 'Text', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'colors_text' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'colors_bg',
			array(
				'label' => esc_html__( 'Background', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'colors_bg' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'colors_bd',
			array(
				'label' => esc_html__( 'Border', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'colors_bd' ) . ': {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'border_border!' ) => 'none',
				),
			)
		);

		$this->add_var_group_control( '', self::VAR_BORDER, array(
			'fields_options' => array(
				'width' => array( 'label' => esc_html__( 'Border Width', 'pediatric-clinic' ) ),
			),
			'exclude' => array( 'color' ),
		) );

		$this->add_control(
			'bd_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'max' => 100,
						'min' => 0,
					),
					'%' => array(
						'max' => 50,
						'min' => 0,
					),
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'bd_radius' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'spacing',
			array(
				'label' => esc_html__( 'Spacing', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 30,
						'min' => 0,
					),
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'spacing' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'padding',
			array(
				'label' => esc_html__( 'Padding', 'pediatric-clinic' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'padding_top' ) . ': {{TOP}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'padding_right' ) . ': {{RIGHT}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'padding_bottom' ) . ': {{BOTTOM}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'padding_left' ) . ': {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'margin',
			array(
				'label' => esc_html__( 'Margin', 'pediatric-clinic' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'margin_top' ) . ': {{TOP}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'margin_right' ) . ': {{RIGHT}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'margin_bottom' ) . ': {{BOTTOM}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'margin_left' ) . ': {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'jc',
			array(
				'label' => esc_html__( 'Horizontal Alignment', 'pediatric-clinic' ),
				'label_block' => false,
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'pediatric-clinic' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'pediatric-clinic' ),
						'icon' => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'pediatric-clinic' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'toggle' => true,
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'jc' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'ai',
			array(
				'label' => esc_html__( 'Vertical Alignment', 'pediatric-clinic' ),
				'label_block' => false,
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'pediatric-clinic' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'pediatric-clinic' ),
						'icon' => ' eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'pediatric-clinic' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'toggle' => true,
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'ai' ) . ': {{VALUE}};',
				),
			)
		);
	}

}
