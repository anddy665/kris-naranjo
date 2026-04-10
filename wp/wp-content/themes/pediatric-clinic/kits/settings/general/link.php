<?php
namespace PediatricClinicSpace\Kits\Settings\General;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Link settings.
 */
class Link extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'link';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Link', 'pediatric-clinic' );
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
			'colors_normal',
			array(
				'label' => esc_html__( 'Color', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'colors_normal' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'colors_hover',
			array(
				'label' => esc_html__( 'Hover', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'colors_hover' ) . ': {{VALUE}};',
				),
				'separator' => 'after',
			)
		);

		$this->add_control(
			'notice',
			array(
				'raw' => sprintf(
					'<strong>%1$s</strong> %2$s',
					__( 'Please note:', 'pediatric-clinic' ),
					__( 'The settings below will be applied to the links inside paragraphs only.', 'pediatric-clinic' )
				),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'render_type' => 'ui',
			)
		);

		$typo_weight_options = [
			'' => esc_html__( 'Default', 'pediatric-clinic' ),
		];

		foreach ( array_merge( [ 'normal', 'bold' ], range( 100, 900, 100 ) ) as $weight ) {
			$typo_weight_options[ $weight ] = ucfirst( $weight );
		}

		$this->add_control(
			'font_weight',
			array(
				'label' => esc_html__( 'Font Weight', 'pediatric-clinic' ),
				'type' => Controls_Manager::SELECT,
				'options' => $typo_weight_options,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'font_weight' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'font_style',
			array(
				'label' => esc_html__( 'Font Style', 'pediatric-clinic' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => esc_html__( 'Default', 'pediatric-clinic' ),
					'normal' => _x( 'Normal', 'Typography Control', 'pediatric-clinic' ),
					'italic' => _x( 'Italic', 'Typography Control', 'pediatric-clinic' ),
					'oblique' => _x( 'Oblique', 'Typography Control', 'pediatric-clinic' ),
				),
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'font_style' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'letter_spacing',
			array(
				'label' => _x( 'Letter Spacing', 'Typography Control', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => -5,
						'max' => 10,
						'step' => 0.1,
					),
				),
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'letter_spacing' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'word_spacing',
			array(
				'label' => _x( 'Word Spacing', 'Typography Control', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => -5,
						'max' => 10,
						'step' => 0.1,
					),
				),
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'word_spacing' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'text_decoration_heading_control',
			array(
				'label' => esc_html__( 'Text Decoration', 'pediatric-clinic' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'text_decoration_line',
			array(
				'label' => esc_html__( 'Type', 'pediatric-clinic' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => _x( 'None', 'Typography Control', 'pediatric-clinic' ),
					'underline' => _x( 'Underline', 'Typography Control', 'pediatric-clinic' ),
					'overline' => _x( 'Overline', 'Typography Control', 'pediatric-clinic' ),
					'line-through' => _x( 'Line Through', 'Typography Control', 'pediatric-clinic' ),
					'underline overline' => _x( 'Underline, Overline', 'Typography Control', 'pediatric-clinic' ),
				),
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'text_decoration_line' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_decoration_colors_normal',
			array(
				'label' => esc_html__( 'Color', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'text_decoration_colors_normal' ) . ': {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'text_decoration_line!' ) => '',
				),
			)
		);

		$this->add_control(
			'text_decoration_colors_hover',
			array(
				'label' => esc_html__( 'Hover', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'text_decoration_colors_hover' ) . ': {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'text_decoration_line!' ) => '',
				),
			)
		);

		$this->add_control(
			'text_decoration_style',
			array(
				'label' => esc_html__( 'Style', 'pediatric-clinic' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => _x( 'Solid', 'Typography Control', 'pediatric-clinic' ),
					'double' => _x( 'Double', 'Typography Control', 'pediatric-clinic' ),
					'dotted' => _x( 'Dotted', 'Typography Control', 'pediatric-clinic' ),
					'dashed' => _x( 'Dashed', 'Typography Control', 'pediatric-clinic' ),
					'wavy' => _x( 'Wavy', 'Typography Control', 'pediatric-clinic' ),
				),
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'text_decoration_style' ) . ': {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'text_decoration_line!' ) => '',
				),
			)
		);

		$this->add_responsive_control(
			'text_decoration_thickness',
			array(
				'label' => _x( 'Thickness', 'Typography Control', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
						'step' => 1,
					),
					'em' => array(
						'min' => 0.1,
						'max' => 1,
						'step' => 0.1,
					),
				),
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'text_decoration_thickness' ) . ': {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'text_decoration_line!' ) => '',
				),
			)
		);

		$this->add_control(
			'text_underline_position',
			array(
				'label' => esc_html__( 'Underline Position', 'pediatric-clinic' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => _x( 'Auto', 'Typography Control', 'pediatric-clinic' ),
					'under' => _x( 'Under', 'Typography Control', 'pediatric-clinic' ),
					'from-font' => _x( 'From Font', 'Typography Control', 'pediatric-clinic' ),
				),
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'text_underline_position' ) . ': {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'text_decoration_line' ) => array(
						'underline',
						'underline overline',
					),
				),
			)
		);

		$this->add_responsive_control(
			'text_underline_offset',
			array(
				'label' => _x( 'Underline Offset', 'Typography Control', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => -50,
						'max' => 50,
						'step' => 1,
					),
					'em' => array(
						'min' => -1,
						'max' => 1,
						'step' => 0.05,
					),
				),
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'text_underline_offset' ) . ': {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'text_decoration_line' ) => array(
						'underline',
						'underline overline',
					),
				),
			)
		);
	}

}
