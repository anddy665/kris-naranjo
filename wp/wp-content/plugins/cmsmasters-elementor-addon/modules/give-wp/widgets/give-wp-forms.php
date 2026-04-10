<?php
namespace CmsmastersElementor\Modules\GiveWp\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Plugin as CmsmastersPlugin;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Give_WP_Forms extends Give_WP_Base {

	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.6.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return 'cmsmasters-give-wp-forms';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.6.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'GiveWP Legacy Forms - Deprecated', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.6.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-form';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.6.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'form',
		);
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.6.0
	 * @since 1.7.2 Fixed currency gap.
	 */
	protected function register_controls() {

		if ( empty( $this->get_select_form() ) ) {
			$this->error_section();

			return;
		}

		$this->start_controls_section(
			'section_form',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'warning_form',
			array(
				'raw' => '<strong>' . $this->get_title() . '</strong>' . __( ' Settings in this widget apply only to the Legacy forms created with the Option-Based Editor.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type' => 'ui',
			)
		);

		$form_list = $this->get_select_form();
		$form_list_keys = array_keys( $form_list );

		if ( empty( $this->get_select_form() ) ) {
			$default = '';
		} else {
			$default = array_reverse( $form_list_keys )[0];
		}

		$this->add_control(
			'form_list',
			array(
				'label' => __( 'Select Form', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => $form_list,
				'default' => $default,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'display_style',
			array(
				'label' => __( 'Display Style', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'onpage' => array(
						'title' => __( 'On Page', 'cmsmasters-elementor' ),
					),
					'button' => array(
						'title' => __( 'Button', 'cmsmasters-elementor' ),
					),
					'modal' => array(
						'title' => __( 'Modal', 'cmsmasters-elementor' ),
					),
					'reveal' => array(
						'title' => __( 'Reveal', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'onpage',
				'toggle' => true,
				'render_type' => 'template',
				'label_block' => true,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_content',
			array(
				'label' => __( 'Display Content', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array(
						'title' => __( 'Hide', 'cmsmasters-elementor' ),
					),
					'above' => array(
						'title' => __( 'Above', 'cmsmasters-elementor' ),
					),
					'below' => array(
						'title' => __( 'Below', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'none',
				'toggle' => false,
				'render_type' => 'template',
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_title',
			array(
				'label' => __( 'Show Heading', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_goal',
			array(
				'label' => __( 'Show Goal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'continue_button_title',
			array(
				'label' => __( 'Reveal Button Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'Enter your text', 'cmsmasters-elementor' ),
				'label_block' => true,
				'separator' => 'before',
				'condition' => array(
					'display_style!' => 'onpage',
				),
			)
		);

		$this->add_responsive_control(
			'give_forms_section_gap',
			array(
				'label' => esc_html__( 'Section Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'rem' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'size_units' => array(
					'%',
					'px',
					'rem',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_forms_section_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'give_heading_section',
			array(
				'label' => __( 'Heading', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_title!' => '',
					'display_style!' => 'button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'give_heading_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap .give-form-title',
			)
		);

		$this->add_control(
			'give_heading_color',
			array(
				'label' => esc_html__( 'Heading Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_heading_color' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'give_separator_color',
			array(
				'label' => esc_html__( 'Separator Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_separator_color' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'give_separator_width',
			array(
				'label' => esc_html__( 'Separator Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_separator_width' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'give_separator_gap',
			array(
				'label' => esc_html__( 'Separator Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'rem' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'size_units' => array(
					'%',
					'px',
					'rem',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_separator_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'give_goal',
			array(
				'label' => __( 'Goal', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_goal!' => '',
					'display_style!' => 'button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'give_goal_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}} .give-form-wrap .give-goal-progress .raised, #cmsmasters_body {{WRAPPER}} .give-form-wrap .give-goal-progress .raised .income',
			)
		);

		$this->update_control(
			'give_goal_typography_font_size',
			array(
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give-goal-title-font-size' ) . ': {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'give_goal_color_title',
			array(
				'label' => esc_html__( 'Title Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_goal_color_title' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'give_progress_bar_color',
			array(
				'label' => esc_html__( 'Progress Bar Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_progress_bar_color' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'give_progress_bar_bg_color',
			array(
				'label' => esc_html__( 'Progress Bar Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_progress_bar_bg_color' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'give_progress_gap',
			array(
				'label' => esc_html__( 'Progress Bar Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'rem' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'size_units' => array(
					'%',
					'px',
					'rem',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_progress_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'give_content',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'display_style!' => 'button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'give_content_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap .give-form-content-wrap',
			)
		);

		$this->add_control(
			'give_content_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_content_color' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'give_content_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'rem' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'size_units' => array(
					'%',
					'px',
					'rem',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_content_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'give_titles',
			array(
				'label' => __( 'Titles', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'display_style!' => 'button',
					'display_style!' => 'modal',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'give_titles_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form legend',
			)
		);

		$this->add_control(
			'give_titles_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_titles_color' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'give_titles_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'rem' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'size_units' => array(
					'%',
					'px',
					'rem',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_titles_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'give_fields',
			array(
				'label' => __( 'Fields', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'display_style!' => 'button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'give_fields_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form input:not([type=button]):not([type=checkbox]):not([type=file]):not([type=hidden]):not([type=image]):not([type=radio]):not([type=reset]):not([type=submit]):not([type=color]):not([type=range]), #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form select, #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form textarea, #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form .give-amount-top',
			)
		);

		$this->start_controls_tabs( 'give_fields_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'focus' => __( 'Focus', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {

			$this->start_controls_tab(
				"fields_{$key}",
				array(
					'label' => $label,
				)
			);

			$state = 'normal';

			if ( 'focus' === $key ) {
				$state = 'focus';
			}

			$this->add_control(
				"give_fields_bg_color_{$key}",
				array(
					'label' => __( 'Background', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', "give_fields_{$state}_colors_bg" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"give_fields_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', "give_fields_{$state}_colors_color" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"give_fields_bd_color_{$key}",
				array(
					'label' => __( 'Border', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', "give_fields_{$state}_colors_bd" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				"give_field_bd_radius_{$key}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', "give_fields_{$state}_bd_radius" ) . ': {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "give_fields_box_shadow_{$key}",
					'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form input:not([type=button]):not([type=checkbox]):not([type=file]):not([type=hidden]):not([type=image]):not([type=radio]):not([type=reset]):not([type=submit]):not([type=color]):not([type=range]), #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form select, #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form textarea, #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form .give-amount-top',
				)
			);

			$this->end_controls_tab();

		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'give_border_fields',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => __( 'None', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
					),
					'width' => array(
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form input:not([type=button]):not([type=checkbox]):not([type=file]):not([type=hidden]):not([type=image]):not([type=radio]):not([type=reset]):not([type=submit]):not([type=color]):not([type=range]), #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form select, #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form textarea, #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form .give-amount-top',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'give_fields_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_fields_padding_top' ) . ': {{TOP}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'give_fields_padding_right' ) . ': {{RIGHT}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'give_fields_padding_bottom' ) . ': {{BOTTOM}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'give_fields_padding_left' ) . ': {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'give_placeholder_color',
			array(
				'label' => esc_html__( 'Placeholder Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_fields_placeholder_color' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'give_currency_gap',
			array(
				'label' => esc_html__( 'Currency Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'rem' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'size_units' => array(
					'px',
					'rem',
				),
				'separator' => 'before',
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'give_currency_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'give_fields_row_gap',
			array(
				'label' => esc_html__( 'Row Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'rem' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'size_units' => array(
					'%',
					'px',
					'rem',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_fields_row_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'give_fields_column_gap',
			array(
				'label' => esc_html__( 'Column Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'rem' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'size_units' => array(
					'%',
					'px',
					'rem',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_fields_column_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'give_labels',
			array(
				'label' => __( 'Labels', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'display_style!' => 'button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'give_labels_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form label',
			)
		);

		$this->add_control(
			'give_labels_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_labels_color' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'give_labels_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'rem' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'size_units' => array(
					'%',
					'px',
					'rem',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_labels_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'give_donation_total',
			array(
				'label' => __( 'Donation Total', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'display_style!' => 'button',
					'display_style!' => 'modal',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'give_donation_total_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form .give-donation-submit .give-donation-total-label, #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form .give-donation-submit .give-final-total-amount',
			)
		);

		$this->add_control(
			'give_donation_total_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_donation_total_color' ) . ': {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'give_amount_button_section',
			array(
				'label' => __( 'Amount Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'display_style!' => 'button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'give_amount_button_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap button.give-donation-level-btn',
			)
		);

		$this->start_controls_tabs( 'give_amount_button_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {

			$this->start_controls_tab(
				"give_amount_button_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$state = 'normal';

			if ( 'hover' === $key ) {
				$state = 'hover';
			}

			$this->add_control(
				"give_amount_button_bg_color_{$key}",
				array(
					'label' => __( 'Background', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', "give_amount_button_{$state}_colors_bg" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"give_amount_button_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', "give_amount_button_{$state}_colors_color" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"give_amount_button_bd_color_{$key}",
				array(
					'label' => __( 'Border', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', "give_amount_button_{$state}_colors_bd" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				"give_amount_bd_radius_{$key}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', "give_amount_button_{$state}_bd_radius" ) . ': {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "give_amount_button_box_shadow_{$key}",
					'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap button.give-donation-level-btn',
				)
			);

			$this->end_controls_tab();

		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'give_border_amount_button',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => __( 'None', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
					),
					'width' => array(
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap button.give-donation-level-btn',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'give_amount_button_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_amount_button_padding_top' ) . ': {{TOP}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'give_amount_button_padding_right' ) . ': {{RIGHT}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'give_amount_button_padding_bottom' ) . ': {{BOTTOM}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'give_amount_button_padding_left' ) . ': {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'give_amount_button_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'rem' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'size_units' => array(
					'%',
					'px',
					'rem',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_amount_button_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'give_donate_button_section',
			array(
				'label' => __( 'Donation Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'default' => '',
				'separator' => 'after',
				'selectors' => array(
					"#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap .give-submit-button-wrap,
					#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap form.give-form, #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap.give-display-button-only" => 'text-align :{{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'give_donate_button_typography',
				'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap input[type="submit"], #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap input[type="button"], #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap .give-submit, #cmsmasters_body {{WRAPPER}} .give-form-wrap button.give-btn',
			)
		);

		$this->start_controls_tabs( 'give_donate_button_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {

			$this->start_controls_tab(
				"give_donate_button_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$state = 'normal';

			if ( 'hover' === $key ) {
				$state = 'hover';
			}

			$this->add_control(
				"give_donate_button_bg_color_{$key}",
				array(
					'label' => __( 'Background', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', "give_donate_button_{$state}_colors_bg" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"give_donate_button_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', "give_donate_button_{$state}_colors_color" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"give_donate_button_bd_color_{$key}",
				array(
					'label' => __( 'Border', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', "give_donate_button_{$state}_colors_bd" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				"give_donate_button_bd_radius_{$key}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', "give_donate_button_{$state}_bd_radius" ) . ': {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "give_donate_button_box_shadow_{$key}",
					'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap input[type="submit"], #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap input[type="button"], #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap .give-submit, #cmsmasters_body {{WRAPPER}} .give-form-wrap button.give-btn',
				)
			);

			$this->end_controls_tab();

		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'give_border_donate_button',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => __( 'None', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
					),
					'width' => array(
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'selector' => '#cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap input[type="submit"], #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap input[type="button"], #cmsmasters_body {{WRAPPER}} .cmsmasters-give-wp-widget .give-form-wrap .give-submit, #cmsmasters_body {{WRAPPER}} .give-form-wrap button.give-btn',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'give_donate_button_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $this->get_control_prefix_parameter( '', 'give_donate_button_padding_top' ) . ': {{TOP}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'give_donate_button_padding_right' ) . ': {{RIGHT}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'give_donate_button_padding_bottom' ) . ': {{BOTTOM}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'give_donate_button_padding_left' ) . ': {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get shortcode widget.
	 *
	 * Retrieve the widget shortcode.
	 *
	 * @since 1.6.0
	 *
	 * @return string The widget shortcode.
	 */
	public function get_shortcode() {

		if ( empty( $this->get_select_form() ) ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		$form_id = $this->get_form_id();
		$display_style = $settings['display_style'];
		$show_title = ( 'yes' === $settings['show_title'] ) ? 'true' : 'false';
		$show_goal = ( 'yes' === $settings['show_goal'] ) ? 'true' : 'false';
		$show_content = $settings['show_content'];
		$continue_button_title = ( '' === $settings['continue_button_title'] ) ? __( 'Donation Now', 'cmsmasters-elementor' ) : $settings['continue_button_title'];

		return "[give_form
				id=\"{$form_id}\"
				display_style=\"{$display_style}\"
				show_title=\"{$show_title}\"
				show_goal=\"{$show_goal}\"
				show_content=\"{$show_content}\"
				continue_button_title=\"{$continue_button_title}\"]";
	}

	/**
	 * Get form id.
	 *
	 * Retrieve the form id.
	 *
	 * @since 1.6.0
	 *
	 * @return string The id form.
	 */
	public function get_form_id() {
		$settings = $this->get_settings_for_display();
		$form_id = $settings['form_list'];

		return $form_id;
	}

	/**
	 * Get form content.
	 *
	 * Retrieve the form content.
	 *
	 * @since 1.6.0
	 *
	 * @return string The form content.
	 */
	public function editor_content() {
		$settings = $this->get_settings_for_display();
		$form_id = $settings['form_list'];

		$content = give_get_meta( $form_id, '_give_form_content', true );

		return $content;
	}

	/**
	 * add filter.
	 *
	 * @since 1.6.0
	 *
	 * @return string form content.
	 */
	public function add_filter_for_editor_content() {
		$is_editor = CmsmastersPlugin::elementor()->editor->is_edit_mode();
		$filter_content = give_is_setting_enabled( give_get_option( 'the_content_filter' ) );

		if ( $is_editor && $filter_content ) {
			add_filter( 'the_content', array( $this, 'editor_content' ) );
		}
	}

	protected function get_control_prefix_parameter( $key = '', $suffix = '' ) {

		if ( '' === $key ) {
			$control_key = 'cmsmasters_forms_';
		} else {
			$control_key = $key;
		}

		$control_prefix = $control_key . $suffix;

		return str_replace( '_', '-', $control_prefix );
	}
}
