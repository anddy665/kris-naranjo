<?php
namespace CmsmastersElementor\Modules\TableOfContents\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Utils as Breakpoints_Manager;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Table_Of_Contents extends Base_Widget {

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.12.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Table Of Contents', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.12.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		// return 'cmsicon-table-of-contents';
		return 'eicon-table-of-contents';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.12.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'table',
			'contents',
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
			'widget-cmsmasters-table-of-contents',
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

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-table-of-contents';
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.12.0
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'table_of_contents',
			array( 'label' => esc_html__( 'Table of Contents', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'title',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'label_block' => true,
				'default' => esc_html__( 'Table of Contents', 'cmsmasters-elementor' ),
			)
		);

		$this->start_controls_tabs( 'include_exclude_tags' );

		$this->start_controls_tab(
			'include',
			array( 'label' => esc_html__( 'Include', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'headings_by_tags',
			array(
				'label' => esc_html__( 'Anchors By Tags', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
				'default' => array(
					'h2',
					'h3',
					'h4',
					'h5',
					'h6',
				),
				'label_block' => true,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'container',
			array(
				'label' => esc_html__( 'Container', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'ai' => array( 'active' => false ),
				'label_block' => true,
				'description' => esc_html__( 'This control confines the Table of Contents to heading elements under a specific container', 'cmsmasters-elementor' ),
				'frontend_available' => true,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'exclude',
			array( 'label' => esc_html__( 'Exclude', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'exclude_headings_by_selector',
			array(
				'label' => esc_html__( 'Anchors By Selector', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'description' => esc_html__( 'CSS selectors, in a comma-separated list', 'cmsmasters-elementor' ),
				'default' => array(),
				'label_block' => true,
				'frontend_available' => true,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'marker_view',
			array(
				'label' => esc_html__( 'Marker View', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'numbers' => esc_html__( 'Numbers', 'cmsmasters-elementor' ),
					'icon' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				),
				'default' => 'numbers',
				'separator' => 'before',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'number_type',
			array(
				'label' => esc_html__( 'Number Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'decimal' => esc_html__( 'Decimal', 'cmsmasters-elementor' ),
					'decimal-leading-zero' => esc_html__( 'Decimal Leading Zero', 'cmsmasters-elementor' ),
					'upper-latin' => esc_html__( 'Uppercase Latin', 'cmsmasters-elementor' ),
					'lower-latin' => esc_html__( 'Lowercase Latin', 'cmsmasters-elementor' ),
					'upper-roman' => esc_html__( 'Uppercase Roman', 'cmsmasters-elementor' ),
					'lower-roman' => esc_html__( 'Lowercase Roman', 'cmsmasters-elementor' ),
					'lower-greek' => esc_html__( 'Greek', 'cmsmasters-elementor' ),
				),
				'default' => 'decimal',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-table-of-content-item-counter-type: {{VALUE}};',
				),
				'condition' => array( 'marker_view' => 'numbers' ),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'exclude_inline_options' => array( 'svg' ),
				'recommended' => array(
					'fa-solid' => array(
						'circle',
						'dot-circle',
						'square-full',
					),
					'fa-regular' => array(
						'circle',
						'dot-circle',
						'square-full',
					),
				),
				'default' => array(
					'value' => 'fas fa-circle',
					'library' => 'fa-solid',
				),
				'skin' => 'inline',
				'label_block' => false,
				'frontend_available' => true,
				'condition' => array( 'marker_view' => 'icon' ),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label' => esc_html__( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'before' => array( 'title' => esc_html__( 'Before', 'cmsmasters-elementor' ) ),
					'after' => array( 'title' => esc_html__( 'After', 'cmsmasters-elementor' ) ),
				),
				'default' => 'before',
				'toggle' => false,
				'label_block' => false,
				'selectors_dictionary' => array(
					'before' => '--toc-list-item-icon-position: row; --toc-list-item-align: flex-start;',
					'after' => '--toc-list-item-icon-position: row-reverse; --toc-list-item-align: space-between;',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '{{VALUE}}',
				),
				'condition' => array( 'icon[value]!' => '' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'additional_options',
			array( 'label' => esc_html__( 'Additional Options', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'word_wrap',
			array(
				'label' => esc_html__( 'Word Wrap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'ellipsis',
				'prefix_class' => 'cmsmasters-content-wrap-',
			)
		);

		$this->add_control(
			'minimize_box',
			array(
				'label' => esc_html__( 'Minimize Box', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'expand_icon',
			array(
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'recommended' => array(
					'fa-solid' => array(
						'chevron-down',
						'angle-down',
						'angle-double-down',
						'caret-down',
						'caret-square-down',
					),
					'fa-regular' => array(
						'caret-square-down',
					),
				),
				'default' => array(
					'value' => 'fas fa-chevron-down',
					'library' => 'fa-solid',
				),
				'skin' => 'inline',
				'label_block' => false,
				'condition' => array( 'minimize_box' => 'yes' ),
			)
		);

		$this->add_control(
			'collapse_icon',
			array(
				'label' => esc_html__( 'Minimize Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'recommended' => array(
					'fa-solid' => array(
						'chevron-up',
						'angle-up',
						'angle-double-up',
						'caret-up',
						'caret-square-up',
					),
					'fa-regular' => array(
						'caret-square-up',
					),
				),
				'default' => array(
					'value' => 'fas fa-chevron-up',
					'library' => 'fa-solid',
				),
				'skin' => 'inline',
				'label_block' => false,
				'condition' => array( 'minimize_box' => 'yes' ),
			)
		);

		$breakpoints = Breakpoints_Manager::get_breakpoints();

		$minimized_on_options = array();

		foreach ( $breakpoints as $breakpoint_key => $breakpoint ) {
			if ( 'widescreen' === $breakpoint_key ) {
				continue;
			}

			$minimized_on_options[ $breakpoint_key ] = sprintf(
				esc_html__( '%1$s (%2$s %3$dpx)', 'cmsmasters-elementor' ),
				ucfirst( $breakpoint_key ),
				'<',
				$breakpoint
			);
		}

		$minimized_on_options['desktop'] = esc_html__( 'Desktop (or smaller)', 'cmsmasters-elementor' );

		$this->add_control(
			'minimized_on',
			array(
				'label' => esc_html__( 'Minimized On', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $minimized_on_options,
				'default' => 'tablet',
				'frontend_available' => true,
				'condition' => array( 'minimize_box!' => '' ),
			)
		);

		$this->add_control(
			'hierarchical_view',
			array(
				'label' => esc_html__( 'Hierarchical View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'collapse_subitems',
			array(
				'label' => esc_html__( 'Collapse Subitems', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'The "Collapse" option should only be used if the Table of Contents is made sticky', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array( 'hierarchical_view' => 'yes' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'box_style',
			array(
				'label' => esc_html__( 'Box', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'box_loader_color',
			array(
				'label' => esc_html__( 'Loader Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-box-spinner-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_min_height',
			array(
				'label' => esc_html__( 'Min Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'rem',
					'vh',
					'custom',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'frontend_available' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-box-min-height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
					'rem',
					'vw',
					'custom',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-box-padding-top: {{TOP}}{{UNIT}}; --toc-box-padding-right: {{RIGHT}}{{UNIT}}; --toc-box-padding-bottom: {{BOTTOM}}{{UNIT}}; --toc-box-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'header_style',
			array(
				'label' => esc_html__( 'Header', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'header_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-header-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-header-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-header-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-header-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-header-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-header-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-header-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-header-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-header-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'header_text_color',
			array(
				'label' => esc_html__( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-header-text-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'header_background_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-header-background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'header_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-header-border-color: {{VALUE}}',
				),
				'condition' => array(
					'minimize_box' => 'yes',
					'header_border_type!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'header_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'rem',
					'custom',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-header-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'header_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
					'rem',
					'vw',
					'custom',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-header-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'header_border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-header-border-type: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_border_width',
			array(
				'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-header-border-top-width: {{TOP}}{{UNIT}}; --toc-header-border-right-width: {{RIGHT}}{{UNIT}}; --toc-header-border-bottom-width: {{BOTTOM}}{{UNIT}}; --toc-header-border-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'header_border_type!' => array(
						'',
						'none',
					),
				),
			)
		);

		$this->add_control(
			'header_toggle_heading',
			array(
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'minimize_box' => 'yes' ),
			)
		);

		$this->start_controls_tabs(
			'header_toggle_tabs',
			array(
				'condition' => array( 'minimize_box' => 'yes' ),
			)
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'minimize' => __( 'Minimize', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$this->start_controls_tab(
				"header_toggle_{$main_key}_tab",
				array( 'label' => $label )
			);

			$this->add_control(
				"header_toggle_{$main_key}_color",
				array(
					'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--toc-header-toggle-color-{$main_key}: {{VALUE}};",
					),
					'condition' => array( 'minimize_box' => 'yes' ),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'header_toggle_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'rem',
					'custom',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 30,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-header-icon-size: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'minimize_box' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'header_toggle_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'rem',
					'custom',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 30,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-header-icon-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'minimize_box' => 'yes' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'list_style',
			array(
				'label' => esc_html__( 'List', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'list_max_height',
			array(
				'label' => esc_html__( 'Max Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'rem',
					'vh',
					'custom',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-max-height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'list_item_space_between',
			array(
				'label' => esc_html__( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-space-between: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'list_item_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
					'rem',
					'vw',
					'custom',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-padding-top: {{TOP}}{{UNIT}}; --toc-list-item-padding-right: {{RIGHT}}{{UNIT}}; --toc-list-item-padding-bottom: {{BOTTOM}}{{UNIT}}; --toc-list-item-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'list_item_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'separator' => 'before',
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->start_controls_tabs( 'item_text_style' );

		$this->start_controls_tab( 'normal',
			array( 'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'list_item_text_color_normal',
			array(
				'label' => esc_html__( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-text-color-normal: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'list_item_border_color_normal',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-normal-border-color: {{VALUE}}',
				),
				'condition' => array( 'header_border_type!' => 'none' ),
			)
		);

		$marker_conditions = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'marker_view',
					'operator' => '===',
					'value' => 'numbers',
				),
				array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'marker_view',
							'operator' => '===',
							'value' => 'icon',
						),
						array(
							'name' => 'icon[value]',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			),
		);

		$this->add_control(
			'list_item_marker_color_normal',
			array(
				'label' => esc_html__( 'Marker Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-marker-color-normal: {{VALUE}}',
				),
				'conditions' => $marker_conditions,
			)
		);

		$this->add_control(
			'list_item_text_underline_normal',
			array(
				'label' => esc_html__( 'Underline', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-text-decoration-normal: underline',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'list_item_text_shadow_normal',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-text-shadow-normal: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover',
			array( 'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'list_item_text_color_hover',
			array(
				'label' => esc_html__( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-text-color-hover: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'list_item_border_color_hover',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-hover-border-color: {{VALUE}}',
				),
				'condition' => array( 'header_border_type!' => 'none' ),
			)
		);

		$this->add_control(
			'list_item_marker_color_hover',
			array(
				'label' => esc_html__( 'Marker Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-marker-color-hover: {{VALUE}}',
				),
				'conditions' => $marker_conditions,
			)
		);

		$this->add_control(
			'list_item_text_underline_hover',
			array(
				'label' => esc_html__( 'Underline', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-text-decoration-hover: underline',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'list_item_text_shadow_hover',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-text-shadow-hover: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'active',
			array( 'label' => esc_html__( 'Active', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'list_item_text_color_active',
			array(
				'label' => esc_html__( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-text-color-active: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'list_item_border_color_active',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-active-border-color: {{VALUE}}',
				),
				'condition' => array( 'header_border_type!' => 'none' ),
			)
		);

		$this->add_control(
			'list_item_marker_color_active',
			array(
				'label' => esc_html__( 'Marker Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-marker-color-active: {{VALUE}}',
				),
				'conditions' => $marker_conditions,
			)
		);

		$this->add_control(
			'list_item_marker_rotate_active',
			array(
				'label' => esc_html__( 'Rotate', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'deg',
					'turn',
				),
				'range' => array(
					'deg' => array(
						'min' => 0,
						'max' => 360,
						'step' => 5,
					),
					'turn' => array(
						'min' => -0.5,
						'max' => 0.5,
						'step' => 0.01,
					),
				),
				'default' => array( 'unit' => 'deg' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-marker-rotate-active: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'marker_view' => 'icon',
					'icon[value]!' => '',
				),
			)
		);

		$this->add_control(
			'list_item_text_underline_active',
			array(
				'label' => esc_html__( 'Underline', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-text-decoration-active: underline',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'list_item_text_shadow_active',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-text-shadow-active: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'list_item_border_type',
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-border-type: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'list_item_border_width',
			array(
				'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-border-top-width: {{TOP}}{{UNIT}}; --toc-list-item-border-right-width: {{RIGHT}}{{UNIT}}; --toc-list-item-border-bottom-width: {{BOTTOM}}{{UNIT}}; --toc-list-item-border-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'list_item_border_type!' => array(
						'',
						'none',
					),
				),
			)
		);

		$this->add_control(
			'list_item_marker_heading',
			array(
				'label' => esc_html__( 'Marker', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $marker_conditions,
			)
		);

		$this->add_control(
			'list_item_marker_vertical_align',
			array(
				'label' => esc_html__( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'baseline' => esc_html__( 'Baseline', 'cmsmasters-elementor' ),
					'flex-start' => esc_html__( 'Top', 'cmsmasters-elementor' ),
					'center' => esc_html__( 'Middle', 'cmsmasters-elementor' ),
					'flex-end' => esc_html__( 'Bottom', 'cmsmasters-elementor' ),
				),
				'default' => 'baseline',
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-marker-vertical-align: {{VALUE}};',
				),
				'conditions' => $marker_conditions,
			)
		);

		$this->add_responsive_control(
			'list_item_marker_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'rem',
					'custom',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-marker-size: {{SIZE}}{{UNIT}}',
				),
				'conditions' => $marker_conditions,
			)
		);

		$this->add_responsive_control(
			'list_item_marker_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'rem',
					'custom',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-marker-gap: {{SIZE}}{{UNIT}}',
				),
				'conditions' => $marker_conditions,
			)
		);

		$this->add_control(
			'list_item_child_list_heading',
			array(
				'label' => esc_html__( 'Child List', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'hierarchical_view' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'list_item_child_list_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-child-list-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-child-list-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-child-list-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-child-list-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-child-list-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-child-list-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-child-list-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-child-list-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--toc-list-item-child-list-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'separator' => 'before',
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'list_item_child_list_top_gap',
			array(
				'label' => esc_html__( 'Top Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'rem',
					'custom',
				),
				'default' => array(
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-child-list-top-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'hierarchical_view' => 'yes' ),
			)
		);

		$this->add_control(
			'list_item_child_list_side_gap',
			array(
				'label' => esc_html__( 'Side Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'rem',
					'custom',
				),
				'default' => array(
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-child-list-side-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'hierarchical_view' => 'yes' ),
			)
		);

		$this->add_control(
			'list_item_child_list_side_padding',
			array(
				'label' => esc_html__( 'Side Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'rem',
					'custom',
				),
				'default' => array(
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-child-list-side-padding: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'hierarchical_view' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'list_item_child_list_space_between',
			array(
				'label' => esc_html__( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--toc-list-item-child-list-space-between: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'hierarchical_view' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get Custom Border Type Options
	 *
	 * Return a set of border options to be used in different WooCommerce widgets.
	 *
	 * This will be used in cases where the Group Border Control could not be used.
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	public static function get_custom_border_type_options() {
		return array(
			'' => esc_html__( 'Default', 'cmsmasters-elementor' ),
			'none' => esc_html__( 'None', 'cmsmasters-elementor' ),
			'solid' => esc_html__( 'Solid', 'cmsmasters-elementor' ),
			'double' => esc_html__( 'Double', 'cmsmasters-elementor' ),
			'dotted' => esc_html__( 'Dotted', 'cmsmasters-elementor' ),
			'dashed' => esc_html__( 'Dashed', 'cmsmasters-elementor' ),
			'groove' => esc_html__( 'Groove', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.12.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'header', 'class', $this->get_widget_class() . '__header' );

		$toc_id = $this->get_widget_class() . '__' . $this->get_id();

		$this->add_render_attribute(
			'body',
			array(
				'id' => $toc_id,
				'class' => $this->get_widget_class() . '__body',
			)
		);

		if ( $settings['collapse_subitems'] ) {
			$this->add_render_attribute( 'body', 'class', $this->get_widget_class() . '__list-items-collapsible' );
		}

		if ( 'yes' === $settings['minimize_box'] ) {
			$this->add_render_attribute(
				'expand-button',
				array(
					'class' => $this->get_widget_class() . '__toggle-button cmsmasters-toggle-button-expand',
					'role' => 'button',
					'tabindex' => '0',
					'aria-controls' => $toc_id,
					'aria-expanded' => 'true',
					'aria-label' => esc_html__( 'Open table of contents', 'cmsmasters-elementor' ),
				)
			);
			$this->add_render_attribute(
				'collapse-button',
				array(
					'class' => $this->get_widget_class() . '__toggle-button cmsmasters-toggle-button-collapse',
					'role' => 'button',
					'tabindex' => '0',
					'aria-controls' => $toc_id,
					'aria-expanded' => 'true',
					'aria-label' => esc_html__( 'Close table of contents', 'cmsmasters-elementor' ),
				)
			);
		}

		$title = ( isset( $settings['title'] ) ? $settings['title'] : '' );

		if ( '' !== $title ) {
			echo '<div ' . $this->get_render_attribute_string( 'header' ) . '>' .
				'<h5 class="' . $this->get_widget_class() . '__header-title">' .
					esc_html( $title ) .
				'</h5>';

				if ( 'yes' === $settings['minimize_box'] ) {
					echo '<div ' . $this->get_render_attribute_string( 'expand-button' ) . '>';

						Icons_Manager::render_icon(
							$settings['expand_icon'],
							array( 'aria-hidden' => 'true' )
						);

					echo '</div>';

					echo '<div ' . $this->get_render_attribute_string( 'collapse-button' ) . '>';

						Icons_Manager::render_icon(
							$settings['collapse_icon'],
							array( 'aria-hidden' => 'true' )
						);

					echo '</div>';
				}

			echo '</div>';
		}

		echo '<div ' . $this->get_render_attribute_string( 'body' ) . '>' .
			'<div class="' . $this->get_widget_class() . '__spinner">';

				Icons_Manager::render_icon(
					array(
						'library' => 'eicons',
						'value' => 'eicon-loading',
					),
					array(
						'class' => array(
							$this->get_widget_class() . '__spinner-inner',
							'eicon-animation-spin',
						),
						'aria-hidden' => 'true',
					)
				);

			echo '</div>' .
		'</div>';
	}

	/**
	 * Get Frontend Settings
	 *
	 * In the TOC widget, this implementation is used to pass a pre-rendered version of the icon to the front end,
	 * which is required in case the FontAwesome SVG experiment is active.
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	public function get_frontend_settings() {
		$frontend_settings = parent::get_frontend_settings();

		if ( Plugin::$instance->experiments->is_feature_active( 'e_font_icon_svg' ) && ! empty( $frontend_settings['icon']['value'] ) ) {
			$frontend_settings['icon']['rendered_tag'] = Icons_Manager::render_font_icon( $frontend_settings['icon'], array( 'aria-hidden' => 'true' ) );
		}

		return $frontend_settings;
	}
}
