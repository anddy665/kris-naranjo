<?php
namespace CmsmastersElementor\Modules\ContactForm\Widgets\Base;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\ContactForm\Widgets\Interfaces\Form_Interface;
use CmsmastersElementor\Plugin;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


abstract class Base_Form extends Base_Widget implements Form_Interface {

	/**
	 * Get group name.
	 *
	 * @since 1.6.5
	 *
	 * @return string Group name.
	 */
	public function get_group_name() {
		return 'cmsmasters-contact-form';
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
			'widget-cmsmasters-contact-form',
		);
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.0.0
	 * @since 1.0.2 Added button Text Shadow group control & fixed rows gap control styles.
	 * @since 1.1.0 Fixed gradient for button, fixed border none for button & fields,
	 * added 'text-decoration' on hover for button, added 'border-radius' on hover,
	 * added separate controls for the forminator form.
	 * @since 1.3.8 Added control margin for checkbox & radio buttons.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {
		$form = $this->get_name();

		if ( empty( $this->get_select_contact_form() ) ) {
			$this->start_controls_section(
				'section_form',
				array(
					'label' => __( 'Contact Form', 'cmsmasters-elementor' ),
				)
			);

			$this->add_control(
				'warning_form',
				array(
					'raw' => '<strong>' . $this->get_form_name() . '</strong>' . __( 'You don’t have ready-made forms. First create a form. ', 'cmsmasters-elementor' ) . '<a href="' . $this->get_url() . '" target="_blank">' . __( 'Go to the form creation page', 'cmsmasters-elementor' ) . '</a>',
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'render_type' => 'ui',
				)
			);

			$this->end_controls_section();

			return;
		}

		$this->start_controls_section(
			'section_form',
			array(
				'label' => __( 'Contact Form', 'cmsmasters-elementor' ),
			)
		);

		$form_list = $this->get_select_contact_form();
		$form_list_keys = array_keys( $form_list );

		$this->add_control(
			'form_list',
			array(
				'label' => __( 'Select Form', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => $form_list,
				'default' => array_reverse( $form_list_keys )[0],
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_fields_style',
			array(
				'label' => __( 'Form Fields', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$input = $this->get_selector_input( false );
		$textarea = $this->get_selector_textarea( false );
		$select = $this->get_selector_select( false );
		$select_multiple = $this->get_selector_select( false, 'multiple' );
		$font_select_2 = $select . ' .select2-selection__rendered';
		$font_multiple_select_2 = $select_multiple . ' .select2-selection__placeholder';
		$prefix = '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-row .forminator-field .forminator-input-with-prefix .forminator-prefix';
		$suffix = '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-row .forminator-field .forminator-input-with-suffix .forminator-suffix';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_fields',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => "{$input}, {$select}, {$textarea}, {$font_select_2}, {$select_multiple}, {$font_multiple_select_2}, {$prefix}, {$suffix}",
				'separator' => 'after',
			)
		);

		$this->start_controls_tabs( 'fields_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'focus' => __( 'Focus', 'cmsmasters-elementor' ),
		);

		if ( 'cmsmasters-forminator' === $form ) {
			$colors['error'] = __( 'Error', 'cmsmasters-elementor' );
		}

		foreach ( $colors as $key => $label ) {

			if ( 'focus' === $key ) {
				$state = ':focus';
			} elseif ( 'hover' === $key ) {
				$state = ':hover';
			} else {
				$state = '';
			}

			if ( 'cmsmasters-forminator' === $form ) {
				if ( 'focus' === $key ) {
					$state = '.forminator-is_active';
				} elseif ( 'hover' === $key ) {
					$state = '.forminator-is_hover';
				} elseif ( 'error' === $key ) {
					$state = '.forminator-has_error';
				} else {
					$state = '';
				}
			}

			$input_state = $this->get_selector_input( $state );
			$textarea_state = $this->get_selector_textarea( $state );
			$select_state = $this->get_selector_select( $state );
			$select_multiple_state = $this->get_selector_select( $state, 'multiple' );
			$select2_state_color = $select_state . ' .select2-selection__rendered';
			$select2_multiple_state_color = $select_multiple_state . ' .select2-selection__placeholder';
			$selector_arrow = $select_state . ' .select2-selection__arrow';
			$selector_multiple_arrow = $select_multiple_state . ' .select2-selection__arrow';

			$this->start_controls_tab(
				"fields_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"color_fields_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$input_state}, {$textarea_state}, {$select_state}, {$select_multiple_state}, {$select2_multiple_state_color}, {$select2_state_color}" => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$input_state}, {$textarea_state}, {$select_multiple_state}, {$select_state}" => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$input_state}, {$textarea_state}, {$select_multiple_state}, {$select_state}" => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'border_fields_border!' => array(
							'none',
						),
					),
				)
			);

			if ( 'cmsmasters-forminator' === $form ) {
				$this->add_control(
					"icon_fields_color_{$key}",
					array(
						'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							"#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-row .forminator-field{$state} .forminator-input-with-icon span" => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					"suffix_prefix_fields_color_{$key}",
					array(
						'label' => __( 'Prefix/Suffix Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							"#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-row .forminator-field{$state} .forminator-input-with-suffix .forminator-suffix" => 'color: {{VALUE}};',
							"#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-row .forminator-field{$state} .forminator-input-with-prefix .forminator-prefix" => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					"selector_arrow_color_{$key}",
					array(
						'label' => __( 'Select Arrow Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							"{$selector_arrow}, {$selector_multiple_arrow}" => 'color: {{VALUE}};',
						),
					)
				);
			}

			if ( 'normal' === $key ) {
				$this->add_responsive_control(
					'field_radius',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'em',
							'%',
						),
						'selectors' => array(
							"{$input}, {$textarea}, {$select_multiple}, {$select}" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			} else {
				$this->add_responsive_control(
					"field_radius_{$key}",
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'em',
							'%',
						),
						'selectors' => array(
							"{$input_state}, {$textarea_state}, {$select_multiple_state}, {$select_state}" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			}

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "fields_form_box_shadow_{$key}",
					'selector' => "{$input_state}, {$textarea_state}, {$select_multiple_state}, {$select_state}",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		if ( 'cmsmasters-forminator' !== $form ) {
			$this->add_control(
				'fields_alignment',
				array(
					'label' => __( 'Fields Alignment', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'label_block' => false,
					'options' => array(
						'flex-start' => array(
							'title' => __( 'Left', 'cmsmasters-elementor' ),
							'icon' => 'eicon-text-align-left',
						),
						'center' => array(
							'title' => __( 'Center', 'cmsmasters-elementor' ),
							'icon' => 'eicon-text-align-center',
						),
						'flex-end' => array(
							'title' => __( 'Right', 'cmsmasters-elementor' ),
							'icon' => 'eicon-text-align-right',
						),
					),
					'default' => '',
					'separator' => 'before',
					'selectors' => array(
						"{$input}, {$textarea}, {$select_multiple}, {$select}" => 'align-self:{{VALUE}}',
					),
				)
			);
		}

		$this->add_control(
			'fields_text_alignment',
			array(
				'label' => __( 'Fields Text Alignment', 'cmsmasters-elementor' ),
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
				'separator' => 'before',
				'prefix_class' => 'elementor-widget-cmsmasters-contact-form__fields-align-',
			)
		);

		$this->add_responsive_control(
			'fields_row_gap',
			array(
				'label' => __( 'Row Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} form .wpcf7-form-control-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
					'#cmsmasters_body {{WRAPPER}} form .wpforms-field' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
					'#cmsmasters_body {{WRAPPER}} form .wpforms-field > ul > li' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
					'#cmsmasters_body {{WRAPPER}} form .wpforms-submit-container' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
					'#cmsmasters_body {{WRAPPER}} form .wpcf7-submit' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
					'#cmsmasters_body {{WRAPPER}} form' => 'margin-bottom: -{{SIZE}}{{UNIT}} !important;',
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-row:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		if ( 'cmsmasters-forminator' === $form ) {
			$this->add_responsive_control(
				'icon_fields_gap',
				array(
					'label' => __( 'Icon Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 200,
						),
					),
					'separator' => 'before',
					'selectors' => array(
						'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-row .forminator-field .forminator-input-with-icon .forminator-input' => 'padding-left: {{SIZE}}{{UNIT}} !important;',
					),
				)
			);

			$this->add_responsive_control(
				'suffix_prefix_fields_gap',
				array(
					'label' => __( 'Prefix/Suffix Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 200,
						),
					),
					'separator' => 'before',
					'selectors' => array(
						'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-row .forminator-field .forminator-input-with-prefix .forminator-input' => 'padding-left: {{SIZE}}{{UNIT}} !important;',
						'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-row .forminator-field .forminator-input-with-suffix .forminator-input' => 'padding-right: {{SIZE}}{{UNIT}} !important;',
					),
				)
			);
		}

		$this->add_responsive_control(
			'textarea_min_h',
			array(
				'label' => __( 'Textarea Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 100,
						'max' => 900,
					),
					'em' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					"{$textarea}" => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_fields',
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
				'selector' => "{$input}, {$textarea}, {$select_multiple}, {$select}",
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'fields_form_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					"{$input}, {$textarea}, {$select_multiple}, {$select}" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-input-with-suffix .forminator-input' => 'padding-right: calc({{RIGHT}}{{UNIT}} + 60px);',
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-input-with-suffix .forminator-suffix' => 'right: {{RIGHT}}{{UNIT}};',
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-input-with-prefix .forminator-input' => 'padding-left: calc({{LEFT}}{{UNIT}} + 60px);',
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-input-with-prefix .forminator-prefix' => 'left: {{LEFT}}{{UNIT}};',
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-input-with-icon .forminator-input' => 'padding-left: calc({{LEFT}}{{UNIT}} + 30px);',
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-input-with-icon span' => 'left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_labels_style',
			array(
				'label' => __( 'Labels', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_labels',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $this->get_label_form(),
			)
		);

		$this->add_control(
			'color_labels',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->get_label_form() => 'color: {{VALUE}};',
				),
			)
		);

		if ( 'cmsmasters-contact-form-seven' !== $form ) {
			$this->add_control(
				'color_asterisk',
				array(
					'label' => __( 'Color Asterisk', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-label .forminator-required' => 'color: {{VALUE}};',
						'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner div.wpforms-container-full .wpforms-form .wpforms-required-label' => 'color: {{VALUE}};',
					),
				)
			);
		}

		$this->add_control(
			'labels_alignment',
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
				'separator' => 'before',
				'prefix_class' => 'elementor-widget-cmsmasters-contact-form__label-align-',
			)
		);

		$this->add_responsive_control(
			'labels_form_margin',
			array(
				'label' => __( 'Row Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					$this->get_label_form() => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'labels_form_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					$this->get_label_form() => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$description_style = $this->get_radio_checkbox_desc();

		$this->start_controls_section(
			'section_radio_chexbox_style',
			array(
				'label' => __( 'Radio Button & Checkbox', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		if ( 'cmsmasters-forminator' === $form ) {

			$this->add_control(
				'checked_heading',
				array(
					'type' => Controls_Manager::HEADING,
					'label' => __( 'Button', 'cmsmasters-elementor' ),
				)
			);

			$this->start_controls_tabs( 'checked_tabs' );

			$colors_checked = array(
				'normal' => __( 'Normal', 'cmsmasters-elementor' ),
				'checked' => __( 'Checked', 'cmsmasters-elementor' ),
				'error' => __( 'Error', 'cmsmasters-elementor' ),
			);

			foreach ( $colors_checked as $key_checked => $label_checked ) {

				$state_checked = '';
				$state_error = '';

				if ( 'checked' === $key_checked ) {
					$state_checked = 'input:checked + ';
				} elseif ( 'error' === $key_checked ) {
					$state_error = '.forminator-has_error';
				}

				$selector = "#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field{$state_error} .forminator-checkbox {$state_checked} span[aria-hidden],
				#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field{$state_error} .forminator-radio {$state_checked} span[aria-hidden]";

				$this->start_controls_tab(
					"checked_button_{$key_checked}",
					array(
						'label' => $label_checked,
					)
				);

				if ( 'checked' === $key_checked ) {
					$this->add_control(
						"checked_button_color_{$key_checked}",
						array(
							'label' => __( 'Color', 'cmsmasters-elementor' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => array(
								'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-checkbox span[aria-hidden]' => 'color: {{VALUE}};',
								'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-radio span[aria-hidden]:before' => 'background-color: {{VALUE}};',
							),
						)
					);
				}

				$this->add_control(
					"checked_button_background_color_{$key_checked}",
					array(
						'label' => __( 'Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => 'background-color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					"checked_button_border_color_{$key_checked}",
					array(
						'label' => __( 'Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => 'border-color: {{VALUE}};',
						),
						'separator' => 'after',
					)
				);

				$this->end_controls_tab();
			}

			$this->end_controls_tabs();
		}

		$this->add_control(
			'label_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Description', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_radio_checkbox',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $description_style,
			)
		);

		$this->add_control(
			'color_radio_checkbox',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$description_style => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'radio_checkbox_form_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					$description_style => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		if ( 'cmsmasters-forminator' === $form ) {
			$this->add_control(
				'wrapper_heading',
				array(
					'type' => Controls_Manager::HEADING,
					'label' => __( 'Wrapper', 'cmsmasters-elementor' ),
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'wrapper_radio_checkbox_form_margin',
				array(
					'label' => __( 'Margin', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
						'%',
					),
					'selectors' => array(
						'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-checkbox,
						#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-radio' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_placeholder_style',
			array(
				'label' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_placeholder',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => 'selector',
			)
		);

		$typography_placeholder = Plugin::elementor()->controls_manager->get_control_groups( Group_Control_Typography::get_type() )->get_fields();
		$placeholder_selectors = array(
			'#cmsmasters_body {{WRAPPER}} ::-webkit-input-placeholder',
			'#cmsmasters_body {{WRAPPER}} ::-ms-input-placeholder',
			'#cmsmasters_body {{WRAPPER}} ::placeholder',
			'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-input::-webkit-input-placeholder',
			'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-input::placeholder',
			'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-textarea::-webkit-input-placeholder',
			'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-textarea::placeholder',
			'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field select.forminator-select2+.forminator-select .selection span.select2-selection--single .select2-selection__rendered .select2-selection__placeholder',
		);
		$placeholder_selectors_properties = array_combine( $placeholder_selectors, array( 'color: {{VALUE}};', 'color: {{VALUE}};', 'color: {{VALUE}};', 'color: {{VALUE}};', 'color: {{VALUE}};', 'color: {{VALUE}};', 'color: {{VALUE}};', 'color: {{VALUE}};' ) );

		foreach ( $typography_placeholder as $control_name => $control ) {
			$control_name = "typography_placeholder_{$control_name}";
			$control = $this->get_controls( $control_name );
			$selector = $control['selectors']['selector'];

			$this->update_control(
				$control_name,
				array(
					'selectors' => array_combine( $placeholder_selectors, array( $selector, $selector, $selector, $selector, $selector, $selector, $selector, $selector ) ),
				)
			);
		}

		$this->add_control(
			'color_placeholder',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => $placeholder_selectors_properties,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_submit_style',
			array(
				'label' => __( 'Submit Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$submit = $this->get_selector_submit();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_submit',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => "{$submit}",
				'separator' => 'after',
			)
		);

		$this->start_controls_tabs( 'fields_submit' );

		$colors_submit = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors_submit as $key_sub => $label_sub ) {
			if ( 'normal' === $key_sub ) {
				$submit_state = $this->get_selector_submit();
				$wp_form_buttons = '#cmsmasters_body {{WRAPPER}} div.wpforms-container-full .wpforms-form .wpforms-field button[type=submit]:before, #cmsmasters_body {{WRAPPER}} div.wpforms-container .wpforms-form div.wpforms-submit-container button[type=submit]:before';
				$forminator_form_buttons = '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-button:before';
			} else {
				$submit_state = $this->get_selector_submit_hover();
				$wp_form_buttons = '#cmsmasters_body {{WRAPPER}} div.wpforms-container-full .wpforms-form .wpforms-field button[type=submit]:before, #cmsmasters_body {{WRAPPER}} div.wpforms-container .wpforms-form div.wpforms-submit-container button[type=submit]:after';
				$forminator_form_buttons = '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-button:after';
			}

			$this->start_controls_tab(
				"submit_{$key_sub}",
				array(
					'label' => $label_sub,
				)
			);

			$this->add_control(
				"submit_text_color_{$key_sub}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$submit_state}" => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::BUTTON_BACKGROUND_GROUP,
				array(
					'name' => "submit_background_color_{$key_sub}",
					'selector' => "{$submit_state}, {$wp_form_buttons}, {$forminator_form_buttons}",
					'prefix' => 'elementor-widget-cmsmasters-contact-form__bg-',
				)
			);

			$this->add_control(
				"submit_border_color_{$key_sub}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$submit_state}" => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'border_submit_border!' => array(
							'none',
						),
					),
				)
			);

			if ( 'normal' === $key_sub ) {
				$this->add_responsive_control(
					'submit_radius',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'em',
							'%',
						),
						'selectors' => array(
							"{$submit}" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			} else {
				$this->add_responsive_control(
					"submit_radius_{$key_sub}",
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'em',
							'%',
						),
						'selectors' => array(
							"{$submit_state}" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
							"{$submit_state}" => 'text-decoration: {{VALUE}};',
						),
					)
				);
			}

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "submit_text_shadow_{$key_sub}",
					'selector' => "{$submit_state}",
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "submit_box_shadow_{$key_sub}",
					'selector' => "{$submit_state}",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		if ( 'cmsmasters-wp-form' === $form ) {
			$this->add_control(
				'button_alignment',
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
					'separator' => 'before',
					'selectors' => array(
						'#cmsmasters_body {{WRAPPER}} div.wpforms-container-full .wpforms-submit-container' => 'text-align: {{VALUE}};',
					),
				)
			);
		}

		$this->add_responsive_control(
			'submit_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 500,
					),
					'em' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					"{$submit}" => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'submit_min_h',
			array(
				'label' => __( 'Min Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 300,
					),
					'em' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'selectors' => array(
					"{$submit}" => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_submit',
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
								'default',
							),
						),
					),
				),
				'selector' => "{$submit}",
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'submit_form_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					"{$submit}" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'submit_form_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					"{$submit}" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget.
	 *
	 * Outputs the widget HTML code on the frontend.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		if ( empty( $this->get_select_contact_form() ) ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		if ( empty( $settings['form_list'] ) ) {
			return;
		}

		$form = $this->get_name();
		$shortcode = do_shortcode( $this->get_shortcode() );

		$is_editor = Plugin::elementor()->editor->is_edit_mode();

		$this->start_html();

		if ( 'cmsmasters-forminator' === $form && $is_editor ) {
			return;
		} else {
			echo $shortcode;
		}

		$this->end_html();
	}

	/**
	 * Start widget HTML.
	 *
	 * Gets the start of the HTML widget.
	 *
	 * @since 1.1.0
	 */
	public function start_html() {
		$form = $this->get_name();

		$this->add_render_attribute( 'wrapper', 'class', array(
			'elementor-widget-cmsmasters-contact-form__wrapper',
			'elementor-widget-cmsmasters-contact-form',
		) );

		$widget_id = $this->get_id();

		$this->add_render_attribute( 'wrapper', 'id', array(
			"cmsmasters-widget-form-{$widget_id}",
		) );

		$is_editor = Plugin::elementor()->editor->is_edit_mode();

		if ( $is_editor ) {
			$this->add_render_attribute( 'wrapper', 'class', array(
				"elementor-widget-{$form}-editor",
			) );
		}

		if ( 'cmsmasters-forminator' === $form ) {
			$form_id = $this->get_form_id();
			$form_api = \Forminator_API::get_form( $form_id );
			$form_settings = $form_api->settings;

			if ( ! isset( $form_settings['cform-color-settings'] ) || empty( $form_settings['cform-color-settings'] ) || NULL === $form_settings['cform-color-settings'] ) {
				$this->add_render_attribute( 'wrapper', 'class', array(
					'cmsmasters-default-color',
				) );
			}

			if ( ! isset( $form_settings['form-font-family'] ) || empty( $form_settings['form-font-family'] ) || NULL === $form_settings['form-font-family'] ) {
				$this->add_render_attribute( 'wrapper', 'class', array(
					'cmsmasters-default-font',
				) );
			}
		}

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>
			<div class="elementor-widget-cmsmasters-contact-form__inner">';
	}

	/**
	 * End widget HTML.
	 *
	 * Gets the end of the HTML widget.
	 *
	 * @since 1.1.0
	 */
	public function end_html() {
		echo '</div>
		</div>';
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {}
}
