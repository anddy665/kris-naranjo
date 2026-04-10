<?php
namespace CmsmastersElementor\Modules\ContactForm\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\ContactForm\Widgets\Base\Base_Form;
use CmsmastersElementor\Plugin as CmsmastersPlugin;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class CMS_Forminator extends Base_Form {

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.1.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return 'cmsmasters-forminator';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.1.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Forminator', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.1.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-wpforms';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.1.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'contact',
			'form',
			'email',
			'wp',
			'forminator',
			'forms',
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
	 * @since 1.1.0
	 */
	protected function register_controls() {
		parent::register_controls();

		if ( empty( $this->get_select_contact_form() ) ) {
			return;
		}

		$this->inject_forminator_section();
		$this->forminator_after_controls();
	}

	/**
	 * Get controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.1.0
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	public function forminator_after_controls() {
		$this->start_controls_section(
			'section_upload_style',
			array(
				'label' => __( 'Upload Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$upload = '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-button.forminator-button-upload';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_upload',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => "{$upload}",
				'separator' => 'after',
			)
		);

		$this->start_controls_tabs( 'fields_upload' );

		$colors_upload = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors_upload as $key_sub => $label_sub ) {
			if ( 'normal' === $key_sub ) {
				$upload_state = $upload;
				$forminator_form_buttons = "{$upload}:before";
			} else {
				$upload_state = "{$upload}:hover";
				$forminator_form_buttons = "{$upload}:after";
			}

			$this->start_controls_tab(
				"upload_{$key_sub}",
				array(
					'label' => $label_sub,
				)
			);

			$this->add_control(
				"upload_text_color_{$key_sub}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$upload_state}" => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::BUTTON_BACKGROUND_GROUP,
				array(
					'name' => "upload_background_color_{$key_sub}",
					'selector' => "{$upload_state}, {$forminator_form_buttons}",
					'prefix' => 'elementor-widget-cmsmasters-contact-form__bg-',
				)
			);

			$this->add_control(
				"upload_border_color_{$key_sub}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$upload_state}" => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'border_upload_border!' => array(
							'none',
						),
					),
				)
			);

			if ( 'normal' === $key_sub ) {
				$this->add_responsive_control(
					'upload_radius',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'em',
							'%',
						),
						'selectors' => array(
							"{$upload}" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			} else {
				$this->add_responsive_control(
					"upload_radius_{$key_sub}",
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'em',
							'%',
						),
						'selectors' => array(
							"{$upload_state}" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_control(
					"button_text_decoration_{$key_sub}",
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
							"{$upload_state}" => 'text-decoration: {{VALUE}};',
						),
					)
				);
			}

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "upload_text_shadow_{$key_sub}",
					'selector' => "{$upload_state}",
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "upload_box_shadow_{$key_sub}",
					'selector' => "{$upload_state}",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'upload_width',
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
					"{$upload}" => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'upload_min_h',
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
					"{$upload}" => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_upload',
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
				'selector' => "{$upload}",
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'upload_form_margin',
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
					"{$upload}" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'upload_form_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					"{$upload}" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'calendar_style_nav',
			array(
				'label' => __( 'Calendar Navigation', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$calendar_selector = 'html body#cmsmasters_body div.ui-datepicker[data-widget-id="cms-{{ID}}"]';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'calendar_nav_font',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $calendar_selector . ' .ui-datepicker-header .ui-datepicker-title select',
			)
		);

		$this->start_controls_tabs( 'calendar_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'hover' === $key ) ? ':hover' : '';

			$this->start_controls_tab(
				"calendar_nav_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			if ( 'normal' === $key ) {
				$this->add_control(
					"calendar_nav_bg_color_{$key}",
					array(
						'label' => __( 'Conteiner Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => array(
							$calendar_selector . ' .ui-datepicker-header' => 'background-color: {{VALUE}} !important;',
						),
					)
				);

				$this->add_control(
					"calendar_nav_bd_color_{$key}",
					array(
						'label' => __( 'Conteiner Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => array(
							$calendar_selector . ' .ui-datepicker-header' => 'border-color: {{VALUE}} !important;',
						),
					)
				);
			}

			$this->add_control(
				"calendar_nav_icon_color_{$key}",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$calendar_selector . ' .ui-datepicker-header .ui-corner-all' . $state . '' => 'color: {{VALUE}} !important;',
					),
				)
			);

			$this->add_control(
				"calendar_nav_icon_bg_color_{$key}",
				array(
					'label' => __( 'Icon Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$calendar_selector . ' .ui-datepicker-header .ui-corner-all' . $state . '' => 'background-color: {{VALUE}} !important;',
					),
				)
			);

			$this->add_control(
				"calendar_nav_icon_bd_color_{$key}",
				array(
					'label' => __( 'Icon Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$calendar_selector . ' .ui-datepicker-header .ui-corner-all' . $state . '' => 'border-color: {{VALUE}} !important;',
					),
				)
			);

			$this->add_control(
				"calendar_nav_select_color_{$key}",
				array(
					'label' => __( 'Select Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$calendar_selector . ' .ui-datepicker-header .ui-datepicker-title select' . $state . '' => 'color: {{VALUE}} !important;',
					),
				)
			);

			$this->add_control(
				"calendar_nav_select_bg_color_{$key}",
				array(
					'label' => __( 'Select Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$calendar_selector . ' .ui-datepicker-header .ui-datepicker-title select' . $state . '' => 'background-color: {{VALUE}} !important;',
					),
				)
			);

			$this->add_control(
				"calendar_nav_select_bd_color_{$key}",
				array(
					'label' => __( 'Select Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$calendar_selector . ' .ui-datepicker-header .ui-datepicker-title select' . $state . '' => 'border-color: {{VALUE}} !important;',
					),
				)
			);

			$this->end_controls_tab();

		}

		$this->end_controls_tabs();

		$this->add_control(
			'heading_calendar_nav_container',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Navigation Container', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_calendar_nav_container',
				'selector' => $calendar_selector . ' .ui-datepicker-header',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'border_radius_calendar_nav_container',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					$calendar_selector . ' .ui-datepicker-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_calendar_nav_icon',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Navigation Icon', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_calendar_nav_icon',
				'selector' => $calendar_selector . ' .ui-datepicker-header .ui-corner-all',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'border_radius_calendar_nav_icon',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					$calendar_selector . ' .ui-datepicker-header .ui-corner-all' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_calendar_nav_select',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Navigation Select', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_calendar_nav_select',
				'selector' => $calendar_selector . ' .ui-datepicker-header .ui-datepicker-title select',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'border_radius_calendar_nav_select',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					$calendar_selector . ' .ui-datepicker-header .ui-datepicker-title select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'calendar_style_table',
			array(
				'label' => __( 'Calendar Table', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'calendar_table_font',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $calendar_selector . ' .ui-datepicker-calendar thead tr th, .ui-datepicker[data-widget-id="cms-{{ID}}"] .ui-datepicker-calendar tbody tr td a',
			)
		);

		$this->start_controls_tabs( 'calendar_tab_table' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'active' => __( 'Active', 'cmsmasters-elementor' ),
			'current' => __( 'Current', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'hover' === $key ) ? ':hover' : '';

			if ( 'hover' === $key ) {
				$state = '.ui-state-hover';
			} elseif ( 'active' === $key ) {
				$state = '.ui-state-active';
			} elseif ( 'current' === $key ) {
				$state = '.ui-state-highlight';
			} else {
				$state = '';
			}

			$this->start_controls_tab(
				"calendar_table_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			if ( 'normal' === $key ) {
				$this->add_control(
					"calendar_table_bg_color_{$key}",
					array(
						'label' => __( 'Table Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => array(
							$calendar_selector . ' .ui-datepicker-calendar tbody tr td' => 'background-color: {{VALUE}} !important;',
						),
					)
				);

				$this->add_control(
					"calendar_table_bd_color_{$key}",
					array(
						'label' => __( 'Table Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => array(
							$calendar_selector . ' .ui-datepicker-calendar' => 'border-color: {{VALUE}} !important;',
						),
					)
				);

				$this->add_control(
					"calendar_table_head_color_{$key}",
					array(
						'label' => __( 'Head Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => array(
							$calendar_selector . ' .ui-datepicker-calendar thead tr th' => 'color: {{VALUE}} !important;',
						),
					)
				);

				$this->add_control(
					"calendar_table_head_bg_color_{$key}",
					array(
						'label' => __( 'Head Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => array(
							$calendar_selector . ' .ui-datepicker-calendar thead th' => 'background-color: {{VALUE}} !important;',
						),
					)
				);
			}

			$this->add_control(
				"calendar_table_cell_color_{$key}",
				array(
					'label' => __( 'Table Cell Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$calendar_selector . ' .ui-datepicker-calendar tbody tr td a' . $state . '' => 'color: {{VALUE}} !important',
					),
				)
			);

			$this->add_control(
				"calendar_table_cell_bg_color_{$key}",
				array(
					'label' => __( 'Table Cell Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$calendar_selector . ' .ui-datepicker-calendar tbody tr td a' . $state . '' => 'background-color: {{VALUE}} !important',
					),
				)
			);

			$this->add_control(
				"calendar_table_cell_bd_color_{$key}",
				array(
					'label' => __( 'Table Cell Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$calendar_selector . ' .ui-datepicker-calendar tbody tr td a' . $state . '' => 'border-color: {{VALUE}} !important',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'heading_calendar_table',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Table', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_calendar_table',
				'selector' => $calendar_selector . ' .ui-datepicker-calendar',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'border_radius_calendar_table',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					$calendar_selector . ' .ui-datepicker-calendar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_calendar_cell',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Table Cell', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_calendar_table_cell',
				'selector' => $calendar_selector . ' .ui-datepicker-calendar tbody tr td a',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'border_radius_calendar_table_cell',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					$calendar_selector . ' .ui-datepicker-calendar tbody tr td a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_select_dropdown_style',
			array(
				'label' => __( 'Dropdown List', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$dropdown_container = 'html body#cmsmasters_body .select2-container.forminator-select[data-widget-id="cms-{{ID}}"] .forminator-select-dropdown[class*="forminator-dropdown--"]';
		$dropdown_option = "{$dropdown_container} .select2-results .select2-results__options .select2-results__option";
		$dropdown_search = "{$dropdown_container} .select2-search--dropdown .select2-search__field";

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'select_dropdown_font',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => "{$dropdown_option}",
			)
		);

		$this->start_controls_tabs( 'select_dropdown_tabs' );

		$colors_select_dropdown = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'selected' => __( 'Selected', 'cmsmasters-elementor' ),
		);

		foreach ( $colors_select_dropdown as $key_select_dropdown => $label_select_dropdown ) {

			if ( 'hover' === $key_select_dropdown ) {
				$state = ':hover';
			} elseif ( 'selected' === $key_select_dropdown ) {
				$state = '.select2-results__option--highlighted';
			} else {
				$state = '';
			}

			$this->start_controls_tab(
				"select_dropdown_{$key_select_dropdown}",
				array(
					'label' => $label_select_dropdown,
				)
			);

			if ( 'normal' === $key_select_dropdown ) {
				$this->add_control(
					"select_dropdown_container_bg_color_{$key_select_dropdown}",
					array(
						'label' => __( 'Container Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$dropdown_container => 'background-color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					"select_dropdown_container_bd_color_{$key_select_dropdown}",
					array(
						'label' => __( 'Container Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$dropdown_container => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					"select_dropdown_search_color_{$key_select_dropdown}",
					array(
						'label' => __( 'Search Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$dropdown_search => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					"select_dropdown_search_bg_color_{$key_select_dropdown}",
					array(
						'label' => __( 'Search Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$dropdown_search => 'background-color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					"select_dropdown_search_bd_color_{$key_select_dropdown}",
					array(
						'label' => __( 'Search Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$dropdown_search => 'border-color: {{VALUE}};',
						),
					)
				);
			}

			$this->add_control(
				"select_dropdown_option_color_{$key_select_dropdown}",
				array(
					'label' => __( 'Option Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$dropdown_option . $state => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"select_dropdown_option_bg_color_{$key_select_dropdown}",
				array(
					'label' => __( 'Option Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$dropdown_option . $state => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"select_dropdown_option_bd_color_{$key_select_dropdown}",
				array(
					'label' => __( 'Option Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$dropdown_option . $state => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'dropdown_container_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Container', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_select_dropdown_container',
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
				'selector' => $dropdown_container,
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'border_radius_select_dropdown_container',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					$dropdown_container => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'dropdown_search_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Search', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_select_dropdown_search',
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
				'selector' => $dropdown_search,
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'border_radius_select_dropdown_search',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					$dropdown_search => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'dropdown_option_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Option', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_select_dropdown_option',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'selector' => $dropdown_option,
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'border_radius_select_dropdown_option',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					$dropdown_option => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_select_m_style',
			array(
				'label' => __( 'Select Multiple', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$selector_container = "#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-multiselect";

		$selector_option = "{$selector_container} .forminator-option";

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'select_m_font',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $selector_option,
			)
		);

		$this->start_controls_tabs( 'select_m_tabs' );

		$colors_select_m = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'selected' => __( 'Selected', 'cmsmasters-elementor' ),
			'error' => __( 'Error', 'cmsmasters-elementor' ),
		);

		foreach ( $colors_select_m as $key_select_m => $label_select_m ) {

			if ( 'error' === $key_select_m ) {
				$state = '.forminator-has_error';
			} elseif ( 'hover' === $key_select_m ) {
				$state = ':hover';
			} elseif ( 'selected' === $key_select_m ) {
				$state = '.forminator-is_checked';
			} else {
				$state = '';
			}

			$this->start_controls_tab(
				"select_m_{$key_select_m}",
				array(
					'label' => $label_select_m,
				)
			);

			if ( 'normal' === $key_select_m || 'error' === $key_select_m ) {
				$this->add_control(
					"select_m_container_bg_color_{$key_select_m}",
					array(
						'label' => __( 'Container Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							"#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field{$state} .forminator-multiselect" => 'background-color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					"select_m_container_bd_color_{$key_select_m}",
					array(
						'label' => __( 'Container Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							"#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field{$state} .forminator-multiselect" => 'border-color: {{VALUE}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name' => "select_m_container_box_shadow_{$key_select_m}",
						'selector' => "#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field{$state} .forminator-multiselect",
					)
				);
			}

			if ( 'error' !== $key_select_m ) {
				$this->add_control(
					"select_m_option_color_{$key_select_m}",
					array(
						'label' => __( 'Option Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector_option . $state => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					"select_m_option_bg_color_{$key_select_m}",
					array(
						'label' => __( 'Option Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector_option . $state => 'background-color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					"select_m_option_bd_color_{$key_select_m}",
					array(
						'label' => __( 'Option Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector_option . $state => 'border-color: {{VALUE}};',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'container_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Container', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_select_m_container',
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
				'selector' => $selector_container,
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'border_radius_select_m_container',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					$selector_container => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'select_m_container_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					$selector_container => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_control(
			'option_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Option', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_select_m_option',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'selector' => $selector_option,
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'border_radius_select_m_option',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					$selector_option => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'select_m_option_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					$selector_option => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_error_fields_styles',
			array(
				'label' => __( 'Error Message Fields', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_error_fields',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-error-message',
			)
		);

		$this->add_control(
			'color_error_fields',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-error-message' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'color_bg_error_fields',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-error-message' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'color_bd_error_fields',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-error-message' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'error_alignment_fields',
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
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-error-message' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_error_fields',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'selector' => '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-error-message',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'error_fields_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-error-message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'error_fields_margin',
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
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-error-message' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'error_fields_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-error-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_response_styles',
			array(
				'label' => __( 'Response', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_response',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form  .forminator-response-message p',
			)
		);

		$this->start_controls_tabs( 'response_tabs' );

		$colors = array(
			'loading' => __( 'Loading', 'cmsmasters-elementor' ),
			'success' => __( 'Success', 'cmsmasters-elementor' ),
			'error' => __( 'Error', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {

			$response = ( 'error' === $key ) ? 'error' : 'success';

			if ( 'error' === $key ) {
				$response = 'error';
			} elseif ( 'success' === $key ) {
				$response = 'success';
			} elseif ( 'loading' === $key ) {
				$response = 'loading';
			}

			$this->start_controls_tab(
				"response_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"color_response_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form  .forminator-response-message.forminator-{$response} p" => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"color_bg_response_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form  .forminator-response-message.forminator-{$response}" => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"color_bd_response_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form  .forminator-response-message.forminator-{$response}" => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'response_alignment',
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
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form  .forminator-response-message' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_response',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'selector' => '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form  .forminator-response-message',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'response_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form  .forminator-response-message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'response_padding',
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
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form  .forminator-response-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	public function inject_forminator_section() {
		$this->start_injection(
			array(
				'of' => 'section_labels_style',
				'at' => 'end',
				'type' => 'section',
			)
		);

		$this->start_controls_section(
			'section_description_fields_styles',
			array(
				'label' => __( 'Description Fields', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_description',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-description,
				#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-description a',
			)
		);

		$this->add_control(
			'color_description',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'color_description_link',
			array(
				'label' => __( 'Color Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-description a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'color_description_link_hover',
			array(
				'label' => __( 'Hover Color Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-description a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'color_bg_description_fields',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-description,
					#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-description' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'color_bd_description_fields',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-description,
					#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-description' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_description_fields',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'selector' => '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-description,
				#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-description',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'description_fields_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-description,
					#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-description' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'description_fields_margin',
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
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-description,
					#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'description_fields_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-description,
					#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->end_injection();
	}

		/**
	 * Get Contact forms.
	 *
	 * Retrieve Forminator plugin forms list.
	 *
	 * @since 1.1.0
	 * @since 1.2.4 Fixed display of the number of records.
	 *
	 * @return array Plugin forms.
	 */
	public function get_select_contact_form() {
		$options = array();

		$forminator_list = get_posts(
			array(
				'post_type' => 'forminator_forms',
				'numberposts' => -1,
			)
		);

		if ( ! empty( $forminator_list ) && ! is_wp_error( $forminator_list ) ) {
			foreach ( $forminator_list as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
		}

		return $options;
	}

	/**
	 * Get Plugin form Name.
	 *
	 * Retrieve Forminator plugin name.
	 *
	 * @since 1.1.0
	 *
	 * @return string Plugin name.
	 */
	public function get_form_name() {
		return $this->get_title() . ': ';
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.1.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_input( $state = '' ) {
		return "#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field{$state} .forminator-input";
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.1.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_select( $state = '', $type = 'single' ) {

		$state_hover = '';
		$state_active = '';
		$state_error = '';

		if ( '.forminator-is_hover' === $state ) {
			$state_hover = ':hover';
		} elseif ( '.forminator-is_active' === $state ) {
			$state_active = '.select2-container--open';
		} elseif ( '.forminator-has_error' === $state ) {
			$state_error = '.forminator-has_error';
		}

		$select = "#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field{$state_error} select.forminator-select2+.forminator-select{$state_active} .selection span.select2-selection--single{$state_hover}";

		if ( 'single' !== $type ) {
			$select = "#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field{$state_error} select.forminator-select2+.forminator-select{$state_active} .selection span.select2-selection--multiple{$state_hover}";
		}

		return $select;
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.1.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_textarea( $state = '' ) {
		return "#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field{$state} .forminator-textarea";
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.1.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_submit() {
		return '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-button';
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.1.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_submit_hover() {
		return '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-button:hover';
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.1.0
	 *
	 * @return string The widget selector.
	 */
	public function get_radio_checkbox_desc() {
		return '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-checkbox span:last-child, #cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-radio span:last-child';
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.1.0
	 *
	 * @return string The widget selector.
	 */
	public function get_label_form() {
		return '#cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-field .forminator-label, #cmsmasters_body {{WRAPPER}} .elementor-widget-cmsmasters-contact-form__wrapper.elementor-widget-cmsmasters-contact-form .elementor-widget-cmsmasters-contact-form__inner .forminator-ui.forminator-custom-form .forminator-row .forminator-label';
	}

	/**
	 * Get Plugin admin url.
	 *
	 * Retrieve Forminator plugin admin url.
	 *
	 * @since 1.1.0
	 *
	 * @return string Plugin admin url.
	 */
	public function get_url() {
		return esc_url( admin_url( 'admin.php?page=forminator' ) );
	}

	/**
	 * Get shortcode widget.
	 *
	 * Retrieve the widget shortcode.
	 *
	 * @since 1.1.0
	 *
	 * @return string The widget shortcode.
	 */
	public function get_shortcode() {
		$form_id = $this->get_form_id();
		return "[forminator_form id=\"{$form_id}\"]";
	}

	/**
	 * Render widget.
	 *
	 * Outputs the widget HTML code on the frontend.
	 *
	 * @since 1.1.0
	 */
	public function render() {
		$form_id = $this->get_form_id();
		$form_id   = ! empty( $form_id ) ? (string) (int) $form_id : 0;


		if ( empty( $this->get_select_contact_form() ) || ! $form_id ) {
			echo '<span>' . __( 'Please select a form', 'cmsmasters-elementor' ) . '</span>';
			return;
		}

		$is_editor = CmsmastersPlugin::elementor()->editor->is_edit_mode();

		if ( $is_editor ) {
			parent::start_html();

			$form_obj = new \Forminator_GFBlock_Forms();
			echo $form_obj->preview_block( array(
				'module_id' => $form_id,
			) );

			parent::end_html();

			echo '<span class="cms-forminator-editor cms-forminator-' . esc_attr( $form_id ) . '">' . esc_html( $form_id ) . '</span>';
		} else {
			parent::render();
		}
	}

	/**
	 * Get form id.
	 *
	 * Retrieve the form id.
	 *
	 * @since 1.1.0
	 *
	 * @return string The id form.
	 */
	public function get_form_id() {
		$settings = $this->get_settings_for_display();
		$form_id = $settings['form_list'];

		return $form_id;
	}
}
