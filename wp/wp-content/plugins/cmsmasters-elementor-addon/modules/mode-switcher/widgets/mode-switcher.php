<?php
namespace CmsmastersElementor\Modules\ModeSwitcher\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Mode switcher widget.
 *
 * @since 1.10.0
 */
class Mode_Switcher extends Base_Widget {

	/**
	 * Widget settings for display.
	 *
	 * @since 1.10.0
	 *
	 * @var string Widget settings for display.
	 */
	protected $settings;

	/**
	 * Widget selector.
	 *
	 * @since 1.10.0
	 *
	 * @var string widget selector.
	 */
	protected $widget_selector;

	/**
	 * Horizontal text parts.
	 *
	 * @since 1.10.0
	 */
	protected $h_start;
	protected $h_end;

	/**
	 * Conditions.
	 *
	 * @since 1.10.0
	 */
	protected $button_text_conditions = array();
	protected $button_icon_conditions = array();
	protected $toggle_text_conditions = array();
	protected $toggle_icon_conditions = array();

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.10.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Mode Switcher', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.10.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-dual-button';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.10.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'dark',
			'light',
			'mode',
			'switcher',
		);
	}

	/**
	 * Specifying caching of the widget by default.
	 *
	 * @since 1.14.0
	 */
	protected function is_dynamic_content(): bool {
		return false;
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
			'widget-cmsmasters-mode-switcher',
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
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.10.0
	 */
	protected function register_controls() {
		$this->set_controls_properties();

		$this->controls_section_content_switcher();

		$this->controls_section_style_button();

		$this->controls_section_style_toggle();
	}

	/**
	 * Set controls properties.
	 *
	 * @since 1.10.0
	 */
	protected function set_controls_properties() {
		$this->h_start = is_rtl() ? 'right' : 'left';
		$this->h_end = ! is_rtl() ? 'right' : 'left';

		$this->button_text_conditions = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'button_main_text',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'button_second_text',
					'operator' => '!==',
					'value' => '',
				),
			),
		);

		$this->button_icon_conditions = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'button_main_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'button_second_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
			),
		);

		$this->toggle_text_conditions = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'toggle_main_text',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'toggle_second_text',
					'operator' => '!==',
					'value' => '',
				),
			),
		);

		$this->toggle_icon_conditions = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'toggle_main_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'toggle_second_icon[value]',
					'operator' => '!==',
					'value' => '',
				),
			),
		);
	}

	/**
	 * Switcher content controls.
	 *
	 * @since 1.10.0
	 */
	protected function controls_section_content_switcher() {
		$this->start_controls_section(
			'section_content_switcher',
			array(
				'label' => __( 'Switcher', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'toggle' => array(
						'title' => __( 'Toggle', 'cmsmasters-elementor' ),
					),
					'single-button' => array(
						'title' => __( 'Single Button', 'cmsmasters-elementor' ),
					),
					'two-buttons' => array(
						'title' => __( 'Two Buttons', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'toggle',
				'toggle' => false,
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_start}",
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_end}",
					),
				),
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'alignment', '{{VALUE}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'elements_gap',
			array(
				'label' => __( 'Elements Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'elements_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'type' => 'two-buttons',
				),
			)
		);

		$this->add_control(
			'button_states_divider_control',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => array(
					'type!' => 'toggle',
				),
			)
		);

		$this->add_control(
			'button_states_content_heading_control',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array(
					'type!' => 'toggle',
				),
			)
		);

		$this->add_responsive_control(
			'single_button_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'single_button_width', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'type' => 'single-button',
				),
			)
		);

		$this->add_responsive_control(
			'single_button_text_alignment',
			array(
				'label' => __( 'Text Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_start}",
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_end}",
					),
				),
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'single_button_text_alignment', '{{VALUE}}' ),
				),
				'condition' => array(
					'type' => 'single-button',
				),
			)
		);

		$this->start_controls_tabs(
			'button_states_tabs',
			array(
				'condition' => array(
					'type!' => 'toggle',
				),
			)
		);

		$button_states = array(
			'second' => __( 'Second', 'cmsmasters-elementor' ),
			'main' => __( 'Main', 'cmsmasters-elementor' ),
		);

		foreach ( $button_states as $state_key => $state_label ) {
			$this->start_controls_tab(
				"button_states_{$state_key}_tab",
				array( 'label' => $state_label )
			);

			$this->add_control(
				"button_{$state_key}_text",
				array(
					'label' => __( 'Text', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				"button_{$state_key}_icon",
				array(
					'label' => __( 'Icon', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::ICONS,
					'default' => array(
						'value' => ( 'main' === $state_key ? 'fas fa-sun' : 'fas fa-moon' ),
						'library' => 'fa-solid',
					),
				)
			);

			$this->add_responsive_control(
				"button_{$state_key}_icon_position",
				array(
					'label' => __( 'Icon Position', 'cmsmasters-elementor' ),
					'label_block' => false,
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'row' => array(
							'title' => __( 'Before', 'cmsmasters-elementor' ),
							'icon' => "eicon-h-align-{$this->h_start}",
						),
						'row-reverse' => array(
							'title' => __( 'After', 'cmsmasters-elementor' ),
							'icon' => "eicon-h-align-{$this->h_end}",
						),
					),
					'toggle' => true,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "button_{$state_key}_icon_position", '{{VALUE}}' ),
					),
					'condition' => array(
						"button_{$state_key}_icon[value]!" => '',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'toggle_states_divider_control',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => array(
					'type' => 'toggle',
				),
			)
		);

		$this->add_control(
			'toggle_states_content_heading_control',
			array(
				'label' => __( 'Toggle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array(
					'type' => 'toggle',
				),
			)
		);

		$this->start_controls_tabs(
			'toggle_states_tabs',
			array(
				'condition' => array(
					'type' => 'toggle',
				),
			)
		);

		$toggle_states = array(
			'second' => __( 'Second', 'cmsmasters-elementor' ),
			'main' => __( 'Main', 'cmsmasters-elementor' ),
		);

		foreach ( $toggle_states as $state_key => $state_label ) {
			$this->start_controls_tab(
				"toggle_states_{$state_key}_tab",
				array( 'label' => $state_label )
			);

			$this->add_responsive_control(
				"toggle_{$state_key}_alignment",
				array(
					'label' => __( 'Alignment', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'start' => array(
							'title' => __( 'Start', 'cmsmasters-elementor' ),
							'icon' => "eicon-text-align-{$this->h_start}",
						),
						'center' => array(
							'title' => __( 'Center', 'cmsmasters-elementor' ),
							'icon' => 'eicon-text-align-center',
						),
						'end' => array(
							'title' => __( 'End', 'cmsmasters-elementor' ),
							'icon' => "eicon-text-align-{$this->h_end}",
						),
					),
					'toggle' => true,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "toggle_{$state_key}_alignment", '{{VALUE}}' ),
					),
				)
			);

			$this->add_control(
				"toggle_{$state_key}_text",
				array(
					'label' => __( 'Text', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				"toggle_{$state_key}_icon",
				array(
					'label' => __( 'Icon', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::ICONS,
					'default' => array(
						'value' => ( 'main' === $state_key ? 'fas fa-sun' : 'fas fa-moon' ),
						'library' => 'fa-solid',
					),
				)
			);

			$this->add_control(
				"toggle_{$state_key}_icon_position",
				array(
					'label' => __( 'Icon Position', 'cmsmasters-elementor' ),
					'label_block' => false,
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'row' => array(
							'title' => __( 'Before', 'cmsmasters-elementor' ),
							'icon' => "eicon-h-align-{$this->h_start}",
						),
						'row-reverse' => array(
							'title' => __( 'After', 'cmsmasters-elementor' ),
							'icon' => "eicon-h-align-{$this->h_end}",
						),
					),
					'toggle' => true,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "toggle_{$state_key}_icon_position", '{{VALUE}}' ),
					),
					'condition' => array(
						"toggle_{$state_key}_icon[value]!" => '',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Button style controls.
	 *
	 * @since 1.10.0
	 */
	protected function controls_section_style_button() {
		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'type!' => 'toggle',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'button_text',
				'conditions' => $this->button_text_conditions,
			)
		);

		$button_style_states = array(
			'normal' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			'hover' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
			'active' => esc_html__( 'Active', 'cmsmasters-elementor' ),
		);

		$this->start_controls_tabs( 'button_style_states_tabs' );

		foreach ( $button_style_states as $state_key => $state_label ) {
			$tab_args = array(
				'label' => $state_label,
			);

			if ( 'active' === $state_key ) {
				$tab_args['condition'] = array(
					'type!' => 'single-button',
				);
			}

			$this->start_controls_tab(
				"button_style_states_{$state_key}_tab",
				$tab_args
			);

			$this->add_control(
				"button_{$state_key}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "button_{$state_key}_color", '{{VALUE}}' ),
					),
					'conditions' => $this->button_text_conditions,
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BACKGROUND_GROUP,
				array(
					'name' => "button_{$state_key}_bg",
					'types' => array( 'classic', 'gradient' ),
					'exclude' => array( 'color', 'image' ),
				)
			);

			$this->start_injection( array( 'of' => "button_{$state_key}_bg_background" ) );

			$this->add_control(
				"button_{$state_key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "button_{$state_key}_bg_color", '{{VALUE}}' ),
					),
				)
			);

			$this->end_injection();

			$this->add_control(
				"button_{$state_key}_bd_color",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "button_{$state_key}_bd_color", '{{VALUE}}' ),
					),
					'condition' => array(
						'button_bd_border!' => 'none',
					),
				)
			);

			$this->add_control(
				"button_{$state_key}_bd_radius",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "button_{$state_key}_bd_radius", '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}' ),
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array(
					'name' => "button_{$state_key}",
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
				array(
					'name' => "button_{$state_key}",
					'conditions' => $this->button_text_conditions,
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'button_bd',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array( 'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ) ),
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'button_padding_top', '{{TOP}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'button_padding_right', '{{RIGHT}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'button_padding_bottom', '{{BOTTOM}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'button_padding_left', '{{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->add_control(
			'button_icon_divider_control',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'conditions' => $this->button_icon_conditions,
			)
		);

		$this->add_control(
			'button_icon_heading_control',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'conditions' => $this->button_icon_conditions,
			)
		);

		$this->add_responsive_control(
			'button_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'button_icon_size', '{{SIZE}}{{UNIT}}' ),
				),
				'conditions' => $this->button_icon_conditions,
			)
		);

		$button_icon_style_states = array(
			'normal' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			'hover' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
			'active' => esc_html__( 'Active', 'cmsmasters-elementor' ),
		);

		$this->start_controls_tabs(
			'button_icon_style_states_tabs',
			array(
				'conditions' => $this->button_icon_conditions,
			)
		);

		foreach ( $button_icon_style_states as $state_key => $state_label ) {
			$tab_args = array(
				'label' => $state_label,
			);

			if ( 'active' === $state_key ) {
				$tab_args['condition'] = array(
					'type!' => 'single-button',
				);
			}

			$this->start_controls_tab(
				"button_icon_style_states_{$state_key}_tab",
				$tab_args
			);

			$this->add_control(
				"button_icon_{$state_key}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "button_icon_{$state_key}_color", '{{VALUE}}' ),
					),
				)
			);

			$this->add_control(
				"button_icon_{$state_key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "button_icon_{$state_key}_bg_color", '{{VALUE}}' ),
					),
				)
			);

			$this->add_control(
				"button_icon_{$state_key}_bd_color",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "button_icon_{$state_key}_bd_color", '{{VALUE}}' ),
					),
					'condition' => array(
						'button_icon_bd_border!' => 'none',
					),
				)
			);

			$this->add_control(
				"button_icon_{$state_key}_bd_radius",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "button_icon_{$state_key}_bd_radius", '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}' ),
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array(
					'name' => "button_icon_{$state_key}",
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
				array(
					'name' => "button_icon_{$state_key}",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'button_icon_bd',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array( 'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ) ),
				),
				'separator' => 'before',
				'conditions' => $this->button_icon_conditions,
			)
		);

		$this->add_responsive_control(
			'button_icon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'button_icon_padding_top', '{{TOP}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'button_icon_padding_right', '{{RIGHT}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'button_icon_padding_bottom', '{{BOTTOM}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'button_icon_padding_left', '{{LEFT}}{{UNIT}}' ),
				),
				'conditions' => $this->button_icon_conditions,
			)
		);

		$this->add_responsive_control(
			'button_icon_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'button_icon_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						$this->button_icon_conditions,
						$this->button_text_conditions,
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Toggle style controls.
	 *
	 * @since 1.10.0
	 */
	protected function controls_section_style_toggle() {
		$this->start_controls_section(
			'section_style_toggle',
			array(
				'label' => __( 'Toggle', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'type' => 'toggle',
				),
			)
		);

		$this->add_responsive_control(
			'toggle_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range' => array(
					'px' => array(
						'min' => 50,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'toggle_width', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'toggle_height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'toggle_height', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'toggle_text',
				'conditions' => $this->toggle_text_conditions,
			)
		);

		$toggle_style_states = array(
			'second' => esc_html__( 'Second', 'cmsmasters-elementor' ),
			'main' => esc_html__( 'Main', 'cmsmasters-elementor' ),
		);

		$this->start_controls_tabs( 'toggle_style_states_tabs' );

		foreach ( $toggle_style_states as $state_key => $state_label ) {
			$this->start_controls_tab(
				"toggle_style_states_{$state_key}_tab",
				array(
					'label' => $state_label,
				)
			);

			$this->add_control(
				"toggle_{$state_key}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "toggle_{$state_key}_color", '{{VALUE}}' ),
					),
					'conditions' => $this->toggle_text_conditions,
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BACKGROUND_GROUP,
				array(
					'name' => "toggle_{$state_key}_bg",
					'types' => array( 'classic', 'gradient' ),
					'exclude' => array( 'color', 'image' ),
				)
			);

			$this->start_injection( array( 'of' => "toggle_{$state_key}_bg_background" ) );

			$this->add_control(
				"toggle_{$state_key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "toggle_{$state_key}_bg_color", '{{VALUE}}' ),
					),
				)
			);

			$this->end_injection();

			$this->add_control(
				"toggle_{$state_key}_bd_color",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "toggle_{$state_key}_bd_color", '{{VALUE}}' ),
					),
					'condition' => array(
						'toggle_bd_border!' => 'none',
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array(
					'name' => "toggle_{$state_key}",
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
				array(
					'name' => "toggle_{$state_key}",
					'conditions' => $this->toggle_text_conditions,
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'toggle_bd',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array( 'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ) ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			"toggle_bd_radius",
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "toggle_bd_radius", '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'toggle_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'toggle_padding_top', '{{TOP}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'toggle_padding_right', '{{RIGHT}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'toggle_padding_bottom', '{{BOTTOM}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'toggle_padding_left', '{{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->add_control(
			'toggle_indicator_divider_control',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_control(
			'toggle_indicator_heading_control',
			array(
				'label' => __( 'Indicator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'toggle_indicator_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'toggle_indicator_width', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'toggle_indicator_height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'toggle_indicator_height', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'toggle_indicator_horizontal_offset',
			array(
				'label' => __( 'Horizontal Offset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'toggle_indicator_horizontal_offset', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$toggle_indicator_style_states = array(
			'second' => esc_html__( 'Second', 'cmsmasters-elementor' ),
			'main' => esc_html__( 'Main', 'cmsmasters-elementor' ),
		);

		$this->start_controls_tabs( 'toggle_indicator_style_states_tabs' );

		foreach ( $toggle_indicator_style_states as $state_key => $state_label ) {
			$this->start_controls_tab(
				"toggle_indicator_style_states_{$state_key}_tab",
				array(
					'label' => $state_label,
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BACKGROUND_GROUP,
				array(
					'name' => "toggle_indicator_{$state_key}_bg",
					'types' => array( 'classic', 'gradient' ),
					'exclude' => array( 'color', 'image' ),
				)
			);

			$this->start_injection( array( 'of' => "toggle_indicator_{$state_key}_bg_background" ) );

			$this->add_control(
				"toggle_indicator_{$state_key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "toggle_indicator_{$state_key}_bg_color", '{{VALUE}}' ),
					),
				)
			);

			$this->end_injection();

			$this->add_control(
				"toggle_indicator_{$state_key}_bd_color",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "toggle_indicator_{$state_key}_bd_color", '{{VALUE}}' ),
					),
					'condition' => array(
						'toggle_indicator_bd_border!' => 'none',
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array(
					'name' => "toggle_indicator_{$state_key}",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'toggle_indicator_bd',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array( 'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ) ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			"toggle_indicator_bd_radius",
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "toggle_indicator_bd_radius", '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->add_control(
			'toggle_icon_divider_control',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'conditions' => $this->toggle_icon_conditions,
			)
		);

		$this->add_control(
			'toggle_icon_heading_control',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'conditions' => $this->toggle_icon_conditions,
			)
		);

		$this->add_responsive_control(
			'toggle_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'toggle_icon_size', '{{SIZE}}{{UNIT}}' ),
				),
				'conditions' => $this->toggle_icon_conditions,
			)
		);

		$toggle_icon_style_states = array(
			'second' => esc_html__( 'Second', 'cmsmasters-elementor' ),
			'main' => esc_html__( 'Main', 'cmsmasters-elementor' ),
		);

		$this->start_controls_tabs(
			'toggle_icon_style_states_tabs',
			array(
				'conditions' => $this->toggle_icon_conditions,
			)
		);

		foreach ( $toggle_icon_style_states as $state_key => $state_label ) {
			$this->start_controls_tab(
				"toggle_icon_style_states_{$state_key}_tab",
				array(
					'label' => $state_label,
				)
			);

			$this->add_control(
				"toggle_icon_{$state_key}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "toggle_icon_{$state_key}_color", '{{VALUE}}' ),
					),
				)
			);

			$this->add_control(
				"toggle_icon_{$state_key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "toggle_icon_{$state_key}_bg_color", '{{VALUE}}' ),
					),
				)
			);

			$this->add_control(
				"toggle_icon_{$state_key}_bd_color",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "toggle_icon_{$state_key}_bd_color", '{{VALUE}}' ),
					),
					'condition' => array(
						'toggle_icon_bd_border!' => 'none',
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array(
					'name' => "toggle_icon_{$state_key}",
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
				array(
					'name' => "toggle_icon_{$state_key}",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'toggle_icon_bd',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array( 'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ) ),
				),
				'separator' => 'before',
				'conditions' => $this->toggle_icon_conditions,
			)
		);

		$this->add_control(
			"toggle_icon_bd_radius",
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "toggle_icon_bd_radius", '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}' ),
				),
				'conditions' => $this->toggle_icon_conditions,
			)
		);

		$this->add_responsive_control(
			'toggle_icon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'toggle_icon_padding_top', '{{TOP}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'toggle_icon_padding_right', '{{RIGHT}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'toggle_icon_padding_bottom', '{{BOTTOM}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'toggle_icon_padding_left', '{{LEFT}}{{UNIT}}' ),
				),
				'conditions' => $this->toggle_icon_conditions,
			)
		);

		$this->add_responsive_control(
			'toggle_icon_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'toggle_icon_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						$this->toggle_icon_conditions,
						$this->toggle_text_conditions,
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.10.0
	 */
	protected function render() {
		$this->settings = $this->get_settings_for_display();
		$this->widget_selector = $this->get_html_wrapper_class();

		$this->add_render_attribute( 'container', 'class', array(
			$this->widget_selector . '__container',
			$this->widget_selector . '-type-' . esc_attr( $this->settings['type'] ),
		) );

		$container_state = 'main';

		if (
			isset( $_COOKIE['cmsmasters_mode_switcher_state'] ) &&
			'second' === $_COOKIE['cmsmasters_mode_switcher_state']
		) {
			$container_state = 'second';
		}

		$this->add_render_attribute( 'container', 'data-state', $container_state );

		echo '<div class="' . $this->widget_selector . '__wrapper">
			<div ' . $this->get_render_attribute_string( 'container' ) . '>';

				if ( 'single-button' === $this->settings['type'] ) {
					Utils::print_unescaped_internal_string( $this->render_switcher_single_button() ); // XSS ok.
				} elseif ( 'two-buttons' === $this->settings['type'] ) {
					Utils::print_unescaped_internal_string( $this->render_switcher_button( 'main' ) ); // XSS ok.
					Utils::print_unescaped_internal_string( $this->render_switcher_button( 'second' ) ); // XSS ok.
				} else {
					Utils::print_unescaped_internal_string( $this->render_switcher_toggle() ); // XSS ok.
				}

			echo '</div>
		</div>';
	}

	/**
	 * Render switcher single button.
	 *
	 * Retrieve the switcher single button.
	 *
	 * @since 1.10.0
	 *
	 * @return string Switcher single button HTML.
	 */
	protected function render_switcher_single_button() {
		$out = '';
		$button_class = "{$this->widget_selector}__button";

		$states = array(
			'main',
			'second',
		);

		foreach ( $states as $state ) {
			$button_out = '';
			$icon_state = $state;

			if ( empty( $this->settings[ "button_{$state}_icon" ]['value'] ) ) {
				if ( 'main' === $state ) {
					$icon_state = 'second';
				} else {
					$icon_state = 'main';
				}
			}

			if ( ! empty( $this->settings[ "button_{$icon_state}_icon" ]['value'] ) ) {
				$single_button_icon_att = array( 'aria-hidden' => 'true' );

				if ( empty( $this->settings[ "button_{$state}_text" ] ) ) {
					$single_button_icon_att = array_merge(
						$single_button_icon_att,
						array( 'aria-label' => ucwords( $state ) . ' Button' ),
					);
				}

				$button_out .= '<span class="' . $button_class . '-icon">' .
					CmsmastersUtils::get_render_icon( $this->settings[ "button_{$icon_state}_icon" ], $single_button_icon_att ) .
				'</span>';
			}

			if ( ! empty( $this->settings[ "button_{$state}_text" ] ) ) {
				$button_out .= '<span class="' . $button_class . '-text">' .
					esc_html( $this->settings[ "button_{$state}_text" ] ) .
				'</span>';
			}

			if ( empty( $button_out ) ) {
				$button_out = '<span class="' . $button_class . '-text">' .
					( 'main' === $state ? esc_html__( 'Light', 'cmsmasters-elementor' ) : esc_html__( 'Dark', 'cmsmasters-elementor' ) ) .
				'</span>';
			}

			$out .= '<div class="' . $button_class . '-item" data-mode="' . $state . '" role="button" tabindex="0">' . $button_out . '</div>';
		}

		return '<div class="' . $button_class . '">
			<div class="' . $button_class . '-inner">' . $out . '</div>
		</div>';
	}

	/**
	 * Render switcher button.
	 *
	 * Retrieve the switcher button.
	 *
	 * @since 1.10.0
	 *
	 * @return string Switcher button HTML.
	 */
	protected function render_switcher_button( $state = 'main' ) {
		$out = '';
		$button_class = "{$this->widget_selector}__button";

		if ( ! empty( $this->settings[ "button_{$state}_icon" ]['value'] ) ) {
			$button_icon_att = array( 'aria-hidden' => 'true' );

			if ( empty( $this->settings[ "button_{$state}_text" ] ) ) {
				$button_icon_att = array_merge(
					$button_icon_att,
					array( 'aria-label' => ucwords( $state ) . ' Button' ),
				);
			}

			$out .= '<span class="' . $button_class . '-icon">' .
				CmsmastersUtils::get_render_icon( $this->settings[ "button_{$state}_icon" ], $button_icon_att ) .
			'</span>';
		}

		if ( ! empty( $this->settings[ "button_{$state}_text" ] ) ) {
			$out .= '<span class="' . $button_class . '-text">' .
				esc_html( $this->settings[ "button_{$state}_text" ] ) .
			'</span>';
		}

		if ( 'two-buttons' === $this->settings['type'] && empty( $out ) ) {
			$out = '<span class="' . $button_class . '-text">' .
				( 'main' === $state ? esc_html__( 'Light', 'cmsmasters-elementor' ) : esc_html__( 'Dark', 'cmsmasters-elementor' ) ) .
			'</span>';
		}

		if ( ! empty( $out ) ) {
			$out = '<div class="' . $button_class . '" data-mode="' . $state . '">' . $out . '</div>';
		}

		return $out;
	}

	/**
	 * Render switcher toggle.
	 *
	 * Retrieve the widget switcher toggle.
	 *
	 * @since 1.10.0
	 *
	 * @return string Switcher toggle HTML.
	 */
	protected function render_switcher_toggle() {
		if ( 'toggle' !== $this->settings['type'] ) {
			return '';
		}

		$out = '';
		$toggle_class = "{$this->widget_selector}__toggle";
		$states = array(
			'main',
			'second',
		);

		foreach ( $states as $state ) {
			$state_out = '';

			if ( ! empty( $this->settings[ "toggle_{$state}_icon" ]['value'] ) ) {
				$toggle_icon_att = array( 'aria-hidden' => 'true' );

				if ( empty( $this->settings[ "toggle_{$state}_text" ] ) ) {
					$toggle_icon_att = array_merge(
						$toggle_icon_att,
						array( 'aria-label' => ucwords( $state ) . ' Toggle' ),
					);
				}

				$state_out .= '<span class="' . $toggle_class . '-icon">' .
					CmsmastersUtils::get_render_icon( $this->settings[ "toggle_{$state}_icon" ], $toggle_icon_att ) .
				'</span>';
			}

			if ( ! empty( $this->settings[ "toggle_{$state}_text" ] ) ) {
				$state_out .= '<span class="' . $toggle_class . '-text">' .
					esc_html( $this->settings[ "toggle_{$state}_text" ] ) .
				'</span>';
			}

			$out .= '<div class="' . $toggle_class . '-item" data-mode="' . $state . '" role="button" tabindex="0">' . $state_out . '</div>';
		}

		return '<div class="' . $toggle_class . '">
			<div class="' . $toggle_class . '-inner">' . $out . '</div>
			<span class="' . $toggle_class . '-indicator">
				<span class="' . $toggle_class . '-indicator-inner"></span>
			</span>
		</div>';
	}

	/**
	 * Render button widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.10.0
	 */
	protected function content_template() {}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.10.0
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			array(
				'field' => 'text',
				'type' => esc_html__( 'Button Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			'link' => array(
				'field' => 'url',
				'type' => esc_html__( 'Button Link', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'button_description',
				'type' => esc_html__( 'Button Description Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'button_css_id',
				'type' => esc_html__( 'Button CSS ID', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
