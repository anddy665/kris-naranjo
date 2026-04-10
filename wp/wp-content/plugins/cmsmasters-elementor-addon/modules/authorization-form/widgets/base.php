<?php
namespace CmsmastersElementor\Modules\AuthorizationForm\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


abstract class Base extends Base_Widget {

	/**
	 * Get group name.
	 *
	 * @since 1.6.5
	 *
	 * @return string Group name.
	 */
	public function get_group_name() {
		return 'cmsmasters-authorization-form';
	}

	/**
	 * @since 1.24.0
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.16.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'widget-cmsmasters-authorization-form',
		);
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		$this->style_controls();
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added group control 'BUTTON_BACKGROUND_GROUP', added gradient for button,
	 * added 'text-decoration' on hover for button, added border none for button and fields,
	 * added 'border-radius' on hover.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function style_controls() {
		$form_name = $this->form_name();

		$this->start_controls_section(
			'section_logged_in_message',
			array(
				'label' => __( 'Logged in Message', 'cmsmasters-elementor' ),
				'condition' => array(
					'show_logged_in_message' => 'yes',
				),
			)
		);

		$this->add_control(
			'text_logged_in_message',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Text', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'link_logged_in_message',
			array(
				'label' => __( 'Link Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Link Text', 'cmsmasters-elementor' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_labels',
			array(
				'label' => __( 'Labels', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'label',
							'operator' => '===',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__wrapper label',
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__wrapper .elementor-widget-cmsmasters-' . $form_name . '-form__field-type-text label' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__wrapper .elementor-widget-cmsmasters-' . $form_name . '-form__field-type-checkbox label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'label_gap',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => '5',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__field-type-text > label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'label' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_field_style',
			array(
				'label' => __( 'Fields', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'field_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__field-group .elementor-widget-cmsmasters-' . $form_name . '-form__field',
			)
		);

		$this->start_controls_tabs( 'field_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'focus' => __( 'Focus', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'focus' === $key ) ? ':focus' : '';
			$selector = "{{WRAPPER}} .elementor-widget-cmsmasters-{$form_name}-form__field-group .elementor-widget-cmsmasters-{$form_name}-form__field{$state}";

			$this->start_controls_tab(
				"field_form_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"field_text_color_{$key}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
					),
				)
			);

			if ( 'normal' === $key ) {
				$this->add_control(
					'field_placeholder',
					array(
						'label' => __( 'Placeholder Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__field-group .elementor-widget-cmsmasters-' . $form_name . '-form__field::-webkit-input-placeholder' => 'color: {{VALUE}};',
							'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__field-group .elementor-widget-cmsmasters-' . $form_name . '-form__field::-ms-input-placeholder' => 'color: {{VALUE}};',
							'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__field-group .elementor-widget-cmsmasters-' . $form_name . '-form__field::placeholder' => 'color: {{VALUE}};',
						),
						'condition' => array(
							'placeholder' => 'yes',
						),
					)
				);
			}

			$this->add_control(
				"field_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"field_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'border_field_border!' => array(
							'none',
						),
					),
				)
			);

			if ( 'normal' === $key ) {
				$this->add_control(
					'field_border_radius',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__field-group .elementor-widget-cmsmasters-' . $form_name . '-form__field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			} else {
				$this->add_control(
					'field_border_radius_focus',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__field-group .elementor-widget-cmsmasters-' . $form_name . '-form__field:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			}

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "box_shadow_{$key}",
					'selector' => $selector,
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_field',
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
						'prefix_class' => 'cmsmasters-' . $form_name . '-form__fields-border-',
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
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__field-group .elementor-widget-cmsmasters-' . $form_name . '-form__field',
				'separator' => 'before',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'field_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__field-group .elementor-widget-cmsmasters-' . $form_name . '-form__field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__button',
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'hover' === $key ) ? ':hover' : '';
			$selector = "{{WRAPPER}} .elementor-widget-cmsmasters-{$form_name}-form__button{$state}";

			$this->start_controls_tab(
				"button_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$element = ( 'hover' === $key ) ? ':after' : ':before';

			$this->add_control(
				"button_text_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'separator' => 'before',
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"button_bg_{$key}_background",
				array(
					'label' => __( 'Background Type', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'color' => array(
							'title' => __( 'Color', 'cmsmasters-elementor' ),
							'icon' => 'eicon-paint-brush',
						),
						'gradient' => array(
							'title' => __( 'Gradient', 'cmsmasters-elementor' ),
							'icon' => 'eicon-barcode',
						),
					),
					'default' => 'color',
					'toggle' => false,
					'render_type' => 'ui',
				)
			);

			$this->add_control(
				"button_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						"{{WRAPPER}} .elementor-widget-cmsmasters-{$form_name}-form__button{$element}" => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"button_bg_{$key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"button_bg_{$key}_color_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 0,
					),
					'render_type' => 'ui',
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_color_b_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'render_type' => 'ui',
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_gradient_type",
				array(
					'label' => __( 'Type', 'cmsmasters-elementor' ),
					'label_block' => false,
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'linear' => __( 'Linear', 'cmsmasters-elementor' ),
						'radial' => __( 'Radial', 'cmsmasters-elementor' ),
					),
					'default' => 'linear',
					'render_type' => 'ui',
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_gradient_angle",
				array(
					'label' => __( 'Angle', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'deg' ),
					'default' => array(
						'unit' => 'deg',
						'size' => 180,
					),
					'range' => array(
						'deg' => array( 'step' => 10 ),
					),
					'selectors' => array(
						"{{WRAPPER}} .elementor-widget-cmsmasters-{$form_name}-form__button{$element}" => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{button_bg_{$key}_color_stop.SIZE}}{{button_bg_{$key}_color_stop.UNIT}}, {{button_bg_{$key}_color_b.VALUE}} {{button_bg_{$key}_color_b_stop.SIZE}}{{button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
						"button_bg_{$key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_gradient_position",
				array(
					'label' => __( 'Position', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => array(
						'center center' => __( 'Center Center', 'cmsmasters-elementor' ),
						'center left' => __( 'Center Left', 'cmsmasters-elementor' ),
						'center right' => __( 'Center Right', 'cmsmasters-elementor' ),
						'top center' => __( 'Top Center', 'cmsmasters-elementor' ),
						'top left' => __( 'Top Left', 'cmsmasters-elementor' ),
						'top right' => __( 'Top Right', 'cmsmasters-elementor' ),
						'bottom center' => __( 'Bottom Center', 'cmsmasters-elementor' ),
						'bottom left' => __( 'Bottom Left', 'cmsmasters-elementor' ),
						'bottom right' => __( 'Bottom Right', 'cmsmasters-elementor' ),
					),
					'default' => 'center center',
					'selectors' => array(
						"{{WRAPPER}} .elementor-widget-cmsmasters-{$form_name}-form__button{$element}" => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{button_bg_{$key}_color_stop.SIZE}}{{button_bg_{$key}_color_stop.UNIT}}, {{button_bg_{$key}_color_b.VALUE}} {{button_bg_{$key}_color_b_stop.SIZE}}{{button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
						"button_bg_{$key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$selector => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'button_border_border!' => array(
							'none',
						),
					),
				)
			);

			if ( 'normal' === $key ) {
				$this->add_responsive_control(
					'button_border_radius',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			} else {
				$this->add_responsive_control(
					'button_border_radius_hover',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors' => array(
							$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_control(
					"button_text_decoration_{$key}",
					array(
						'label' => __( 'Text Decoration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SELECT,
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => _x( 'None', 'Typography Control', 'cmsmasters-elementor' ),
							'underline' => _x( 'Underline', 'Typography Control', 'cmsmasters-elementor' ),
							'overline' => _x( 'Overline', 'Typography Control', 'cmsmasters-elementor' ),
							'line-through' => _x( 'Line Through', 'Typography Control', 'cmsmasters-elementor' ),
						),
						'default' => '',
						'selectors' => array(
							$selector => 'text-decoration: {{VALUE}};',
						),
					)
				);
			}

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "button_shadow_text_{$key}",
					'selector' => $selector,
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "button_box_shadow_{$key}",
					'selector' => $selector,
				)
			);

			if ( 'hover' === $key ) {
				$this->add_control(
					'button_hover_animation',
					array(
						'label' => __( 'Animation', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::HOVER_ANIMATION,
						'separator' => 'before',
					)
				);
			}

			$this->end_controls_tab();

		}

		$this->end_controls_tabs();

		$this->add_control(
			'align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
					'stretch' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'toggle' => false,
				'prefix_class' => 'cmsmasters-' . $form_name . '-form__button-align-',
				'default' => 'start',
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), array(
				'name' => 'button_border',
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
						'prefix_class' => 'cmsmasters-' . $form_name . '-form__button-border-',
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
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__button',
			)
		);

		$this->add_responsive_control(
			'button_text_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_logged',
			array(
				'label' => __( 'Logged in Message', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_logged_in_message' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'message_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__logged-in-message',
			)
		);

		$this->add_control(
			'message_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__logged-in-message' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'message_link_color',
			array(
				'label' => __( 'Link Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__logged-in-message a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'message_link_color_hover',
			array(
				'label' => __( 'Link Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__logged-in-message a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'message_link_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__logged-in-message' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'message_link_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__logged-in-message' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_message',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__logged-in-message',
				'separator' => 'before',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_control(
			'message_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__logged-in-message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'message_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-' . $form_name . '-form__logged-in-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get Logged Message
	 *
	 * Retrieve Logged Message HTML.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function render_logged_message( $logout_redirect ) {
		$settings = $this->get_settings_for_display();
		$form_name = $this->form_name();

		if ( 'yes' === $settings['show_logged_in_message'] ) {
			$backend_class = '';

			if ( Plugin::$instance->editor->is_edit_mode() ) {
				$backend_class = ' elementor-widget-cmsmasters-' . $form_name . '-form__hide-logged';
			}

			$current_user = wp_get_current_user();

			if ( empty( $settings['text_logged_in_message'] ) ) {
				$text_logged = __( 'You are now Logged in as', 'cmsmasters-elementor' );
			} else {
				$text_logged = esc_html( $settings['text_logged_in_message'] );
			}

			if ( empty( $settings['link_logged_in_message'] ) ) {
				$link_text_logged = __( 'Logout', 'cmsmasters-elementor' );
			} else {
				$link_text_logged = esc_html( $settings['link_logged_in_message'] );
			}

			echo '<div class="elementor-widget-cmsmasters-' . esc_attr( $form_name ) . '-form__' . esc_attr( $form_name ) . ' elementor-widget-cmsmasters-' . esc_attr( $form_name ) . '-form__logged-in-message' . esc_attr( $backend_class ) . '">' .
				/* translators: Addon 'Logged in message' info. %1$s: Current user name, %2$s: Page where the transition will be completed */
				sprintf( '' . $text_logged . ' %1$s (<a href="%2$s">' . $link_text_logged . '</a>)', esc_html( $current_user->display_name ), esc_url( wp_logout_url( $logout_redirect ) ) ) .
			'</div>';
		}
	}

	/**
	 * Get Form Name.
	 *
	 * Retrieve form name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Form name.
	 */
	abstract protected function form_name();

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {}
}
