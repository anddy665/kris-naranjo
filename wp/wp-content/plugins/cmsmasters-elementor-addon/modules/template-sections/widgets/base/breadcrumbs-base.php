<?php
namespace CmsmastersElementor\Modules\TemplateSections\Widgets\Base;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Traits\Extendable_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

use RankMath\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Breadcrumbs_Base extends Base_Widget {

	use Extendable_Widget;

	/**
	 * Get extendable widget class.
	 *
	 * Retrieve the extendable widget container class.
	 *
	 * @since 1.0.0
	 *
	 * @return string Extendable widget container class.
	 */
	public function get_extendable_widget_class() {
		return 'cmsmasters-widget-breadcrumbs';
	}

	/**
	 * Get Yoast options name.
	 *
	 * The check `wpseo_titles` option and get options name.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed assignment `breadcrumbs_enabled` variable. Added check on empty.
	 */
	public function is_breadcrumbs_enabled( $options_name ) {
		$breadcrumbs_enabled = current_theme_supports( 'yoast-seo-breadcrumbs' );

		if ( ! $breadcrumbs_enabled ) {
			// The check for option 'wpseo_internallinks' is a BC fix for old versions of Yoast (<7.0.0).
			// In this version Yoast changed the DB key for the breadcrumbs options to 'wpseo_titles'.
			$options = get_option( 'wpseo_internallinks' );

			if ( empty( $options ) ) {
				$options = get_option( 'wpseo_titles' );
			}

			$breadcrumbs_enabled = ( isset( $options[ $options_name ] ) ? $options[ $options_name ] : '' );
		}

		return $breadcrumbs_enabled;
	}

	abstract protected function get_widget_class();

	protected function register_section_breadcrumbs_settings_start() {
		$this->start_controls_section(
			'section_breadcrumbs_settings',
			array( 'label' => __( 'General Settings', 'cmsmasters-elementor' ) )
		);
	}

	/**
	 * Register breadcrumbs widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed `Alignment` control on responsive.
	 */
	protected function register_section_breadcrumbs_settings_end() {
		if (
			( function_exists( 'yoast_breadcrumb' ) && $this->is_breadcrumbs_enabled( 'breadcrumbs-enable' ) ) ||
			( function_exists( 'rank_math_the_breadcrumbs' ) && Helper::get_settings( 'general.breadcrumbs' ) )
		) {
			$condition_cms = array( 'source' => 'cmsmasters' );
		} else {
			$condition_cms = array();
		}

		$this->add_responsive_control(
			'alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
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
				'prefix_class' => 'cmsmasters-breadcrumbs-alignment%s-',
			)
		);

		$this->add_control(
			'homepage_type',
			array(
				'label' => __( 'Homepage', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array( 'title' => __( 'None', 'cmsmasters-elementor' ) ),
					'text' => array( 'title' => __( 'Text', 'cmsmasters-elementor' ) ),
					'icon' => array( 'title' => __( 'Icon', 'cmsmasters-elementor' ) ),
				),
				'default' => 'text',
				'label_block' => false,
				'toggle' => false,
				'separator' => 'before',
				'condition' => $condition_cms,
			)
		);

		$this->add_control(
			'homepage_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Home', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => false,
				'condition' => array_merge( $condition_cms, array( 'homepage_type' => 'text' ) ),
			)
		);

		$this->add_control(
			'homepage_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-home',
					'library' => 'fa-solid',
				),
				'show_label' => false,
				'condition' => array_merge( $condition_cms, array( 'homepage_type' => 'icon' ) ),
			)
		);

		$this->add_control(
			'separator_type',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array( 'title' => __( 'None', 'cmsmasters-elementor' ) ),
					'text' => array( 'title' => __( 'Text', 'cmsmasters-elementor' ) ),
					'icon' => array( 'title' => __( 'Icon', 'cmsmasters-elementor' ) ),
				),
				'default' => 'text',
				'label_block' => false,
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-separator-type-',
				'condition' => $condition_cms,
			)
		);

		$this->add_control(
			'custom_separator',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => '/',
				'description' => 'Max length 3 symbols.',
				'label_block' => true,
				'show_label' => false,
				'condition' => array_merge( $condition_cms, array( 'separator_type' => 'text' ) ),
			)
		);

		$this->add_control(
			'icon_separator',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'recommended' => array(
					'fa-regular' => array(
						'arrow-alt-circle-left',
						'arrow-alt-circle-right',
					),
					'fa-solid' => array(
						'fa-angle-right',
						'fa-angle-left',
						'arrow-alt-circle-left',
						'arrow-alt-circle-right',
						'arrow-circle-left',
						'arrow-circle-right',
						'arrow-left',
						'arrow-right',
						'long-arrow-alt-left',
						'long-arrow-alt-right',
					),
				),
				'default' => array(
					'value' => 'fas fa-angle-right',
					'library' => 'fa-solid',
				),
				'show_label' => false,
				'condition' => array_merge( $condition_cms, array( 'separator_type' => 'icon' ) ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_section_additional_options_start() {
		if (
			( function_exists( 'yoast_breadcrumb' ) && $this->is_breadcrumbs_enabled( 'breadcrumbs-enable' ) ) ||
			( function_exists( 'rank_math_the_breadcrumbs' ) && Helper::get_settings( 'general.breadcrumbs' ) )
		) {
			$condition_cms = array( 'source' => 'cmsmasters' );
		} else {
			$condition_cms = array();
		}

		$this->start_controls_section(
			'section_additional_options',
			array( 'label' => __( 'Additional Options', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'prefix_show',
			array(
				'label' => __( 'Breadcrumbs prefix', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'no',
				'condition' => $condition_cms,
			)
		);

		$this->add_control(
			'prefix_label',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Browse:', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => false,
				'condition' => array_merge( $condition_cms, array( 'prefix_show' => 'yes' ) ),
			)
		);
	}

	/**
	 * Register additional options controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.12.1 Added `Single Post Subcategories` control.
	 */
	protected function register_section_additional_options_end() {
		if (
			( function_exists( 'yoast_breadcrumb' ) && $this->is_breadcrumbs_enabled( 'breadcrumbs-enable' ) ) ||
			( function_exists( 'rank_math_the_breadcrumbs' ) && Helper::get_settings( 'general.breadcrumbs' ) )
		) {
			$condition_cms = array( 'source' => 'cmsmasters' );
		} else {
			$condition_cms = array();
		}

		$this->add_control(
			'hide_on_front_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => $condition_cms,
			)
		);

		$this->add_control(
			'hide_on_front',
			array(
				'label' => __( 'Hide Breadcrumbs on Front Page', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'show_subcategories',
			array(
				'label' => __( 'Single Post Subcategories', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => $condition_cms,
			)
		);

		$this->end_controls_section();
	}

	protected function register_section_breadcrumbs_style() {
		if (
			( function_exists( 'yoast_breadcrumb' ) && $this->is_breadcrumbs_enabled( 'breadcrumbs-enable' ) ) ||
			( function_exists( 'rank_math_the_breadcrumbs' ) && Helper::get_settings( 'general.breadcrumbs' ) )
		) {
			$condition_cms = array( 'source' => 'cmsmasters' );
		} else {
			$condition_cms = array();
		}

		$this->start_controls_section(
			'breadcrumbs_style',
			array(
				'label' => __( 'Breadcrumbs', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'breadcrumbs_vertical_alignment',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-breadcrumbs__content,
					{{WRAPPER}}.cmsmasters-breadcrumbs-type-yoast .cmsmasters-widget-breadcrumbs__content,
					{{WRAPPER}}.cmsmasters-breadcrumbs-type-yoast .cmsmasters-widget-breadcrumbs__content > span > span,
					{{WRAPPER}}.cmsmasters-breadcrumbs-type-yoast .cmsmasters-widget-breadcrumbs__content > span > span span,
					{{WRAPPER}}.cmsmasters-breadcrumbs-type-rank .cmsmasters-widget-breadcrumbs__content .rank-math-breadcrumb > p' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'breadcrumbs_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-breadcrumbs__content,
					{{WRAPPER}} .cmsmasters-widget-breadcrumbs__content a',
			)
		);

		// Current Item
		$this->add_control(
			'bbreadcrumbs_link_heading',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'breadcrumbs_link_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-breadcrumbs__content a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'breadcrumbs_link_hover_color',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-breadcrumbs__content a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'breadcrumbs_hover_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-breadcrumbs__content a' => 'transition-duration: {{SIZE}}s',
				),
			)
		);

		$this->add_responsive_control(
			'breadcrumbs_link_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 30 ),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-separator-type-none .cmsmasters-widget-breadcrumbs__content a' => 'margin-right: {{SIZE}}{{UNIT}}',
				),
				'condition' => array_merge( $condition_cms, array( 'separator_type' => 'none' ) ),
			)
		);

		if (
			$this->is_breadcrumbs_enabled( 'breadcrumbs-enable' ) &&
			'' === $this->is_breadcrumbs_enabled( 'breadcrumbs-sep' )
		) {
			$this->add_responsive_control(
				'breadcrumbs_link_gap_yoast',
				array(
					'label' => __( 'Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array( 'max' => 30 ),
					),
					'selectors' => array(
						'{{WRAPPER}}.cmsmasters-breadcrumbs-type-yoast .cmsmasters-widget-breadcrumbs__content a' => 'margin-right: {{SIZE}}{{UNIT}}',
					),
					'condition' => array( 'source' => 'yoast' ),
				)
			);
		}

		if (
			function_exists( 'rank_math_the_breadcrumbs' ) &&
			Helper::get_settings( 'general.breadcrumbs' ) &&
			'' === Helper::get_settings( 'general.breadcrumbs_separator' )
		) {
			$this->add_responsive_control(
				'breadcrumbs_link_gap_rank',
				array(
					'label' => __( 'Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array( 'max' => 30 ),
					),
					'selectors' => array(
						'{{WRAPPER}}.cmsmasters-breadcrumbs-type-rank .cmsmasters-widget-breadcrumbs__content a' => 'margin-right: {{SIZE}}{{UNIT}}',
					),
					'condition' => array( 'source' => 'rank' ),
				)
			);
		}

		// Current Item
		$this->add_control(
			'breadcrumbs_current_item_heading',
			array(
				'label' => __( 'Current Item', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'breadcrumbs_current_item_typography',
				'selector' => '{{WRAPPER}}:not(.cmsmasters-breadcrumbs-type-yoast) .cmsmasters-widget-breadcrumbs__content > span:not([class]),
					{{WRAPPER}}.cmsmasters-breadcrumbs-type-yoast .cmsmasters-widget-breadcrumbs__content .breadcrumb_last,
					{{WRAPPER}}.cmsmasters-breadcrumbs-type-rank .cmsmasters-widget-breadcrumbs__content .last',
			)
		);

		$this->add_control(
			'breadcrumbs_current_item_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}:not(.cmsmasters-breadcrumbs-type-yoast) .cmsmasters-widget-breadcrumbs__content > span:not([class]),
					{{WRAPPER}}.cmsmasters-breadcrumbs-type-yoast .cmsmasters-widget-breadcrumbs__content .breadcrumb_last,
					{{WRAPPER}}.cmsmasters-breadcrumbs-type-rank .cmsmasters-widget-breadcrumbs__content .last' => 'color: {{VALUE}};',
				),
			)
		);

		// Separator
		$this->add_control(
			'breadcrumbs_sep_heading',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array_merge( $condition_cms, array( 'separator_type!' => 'none' ) ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'breadcrumbs_sep_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-breadcrumbs__sep',
				'condition' => array_merge( $condition_cms, array( 'separator_type' => 'text' ) ),
			)
		);

		$this->add_control(
			'breadcrumbs_sep_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-breadcrumbs__sep' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'condition' => array_merge( $condition_cms, array( 'separator_type!' => 'none' ) ),
			)
		);

		$this->add_responsive_control(
			'breadcrumbs_sep_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 12,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-breadcrumbs__sep' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-widget-breadcrumbs__sep svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array_merge( $condition_cms, array( 'separator_type' => 'icon' ) ),
			)
		);

		if (
			$this->is_breadcrumbs_enabled( 'breadcrumbs-enable' ) &&
			'' !== $this->is_breadcrumbs_enabled( 'breadcrumbs-sep' )
		) {
			$this->add_control(
				'breadcrumbs_sep_heading_yoast',
				array(
					'label' => __( 'Separator', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'source' => 'yoast' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'breadcrumbs_sep_typography_yoast',
					'selector' => '{{WRAPPER}}.cmsmasters-breadcrumbs-type-yoast .cmsmasters-widget-breadcrumbs__content > span > span',
					'condition' => array( 'source' => 'yoast' ),
				)
			);

			$this->add_control(
				'breadcrumbs_sep_color_yoast',
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}.cmsmasters-breadcrumbs-type-yoast .cmsmasters-widget-breadcrumbs__content > span > span' => 'color: {{VALUE}};',
					),
					'condition' => array( 'source' => 'yoast' ),
				)
			);

			$this->add_responsive_control(
				'breadcrumbs_sep_gap_yoast',
				array(
					'label' => __( 'Horizontal Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px' ),
					'allowed_dimensions' => 'horizontal',
					'placeholder' => array(
						'top' => 'auto',
						'right' => '',
						'bottom' => 'auto',
						'left' => '',
					),
					'selectors' => array(
						'{{WRAPPER}}' => '--separator-left-gap: {{LEFT}}{{UNIT}}; --separator-right-gap: {{RIGHT}}{{UNIT}};',
					),
					'condition' => array( 'source' => 'yoast' ),
				)
			);
		}

		if (
			function_exists( 'rank_math_the_breadcrumbs' ) &&
			Helper::get_settings( 'general.breadcrumbs' ) &&
			'' !== Helper::get_settings( 'general.breadcrumbs_separator' )
		) {
			$this->add_control(
				'breadcrumbs_sep_heading_rank',
				array(
					'label' => __( 'Separator', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'source' => 'rank' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'breadcrumbs_sep_typography_rank',
					'selector' => '{{WRAPPER}}.cmsmasters-breadcrumbs-type-rank .cmsmasters-widget-breadcrumbs__content .separator',
					'condition' => array( 'source' => 'rank' ),
				)
			);

			$this->add_control(
				'breadcrumbs_sep_color_rank',
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}.cmsmasters-breadcrumbs-type-rank .cmsmasters-widget-breadcrumbs__content .separator' => 'color: {{VALUE}};',
					),
					'condition' => array( 'source' => 'rank' ),
				)
			);

			$this->add_responsive_control(
				'breadcrumbs_sep_gap_rank',
				array(
					'label' => __( 'Horizontal Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px' ),
					'allowed_dimensions' => 'horizontal',
					'placeholder' => array(
						'top' => 'auto',
						'right' => '',
						'bottom' => 'auto',
						'left' => '',
					),
					'selectors' => array(
						'{{WRAPPER}}' => '--separator-left-gap: {{LEFT}}{{UNIT}}; --separator-right-gap: {{RIGHT}}{{UNIT}};',
					),
					'condition' => array( 'source' => 'rank' ),
				)
			);
		}

		$this->add_responsive_control(
			'breadcrumbs_sep_gap',
			array(
				'label' => __( 'Horizontal Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'allowed_dimensions' => 'horizontal',
				'placeholder' => array(
					'top' => 'auto',
					'right' => '',
					'bottom' => 'auto',
					'left' => '',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--separator-left-gap: {{LEFT}}{{UNIT}}; --separator-right-gap: {{RIGHT}}{{UNIT}};',
				),
				'condition' => array_merge( $condition_cms, array( 'separator_type!' => 'none' ) ),
			)
		);

		// Homepage
		$this->add_control(
			'breadcrumbs_homepage_icon_heading',
			array(
				'label' => __( 'Homepage Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array_merge( $condition_cms, array( 'homepage_type' => 'icon' ) ),
			)
		);

		$this->add_control(
			'breadcrumbs_homepage_icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-breadcrumbs__content .cmsmasters-home' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cmsmasters-widget-breadcrumbs__content .cmsmasters-home svg' => 'fill: {{VALUE}};',
				),
				'condition' => array_merge( $condition_cms, array( 'homepage_type' => 'icon' ) ),
			)
		);

		$this->add_control(
			'breadcrumbs_homepage_icon_hover_color',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-breadcrumbs__content .cmsmasters-home:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cmsmasters-widget-breadcrumbs__content .cmsmasters-home:hover svg' => 'fill: {{VALUE}};',
				),
				'condition' => array_merge( $condition_cms, array( 'homepage_type' => 'icon' ) ),
			)
		);

		$this->add_responsive_control(
			'breadcrumbs_homepage_icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 12,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-breadcrumbs__content .cmsmasters-home' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-widget-breadcrumbs__content .cmsmasters-home svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array_merge( $condition_cms, array( 'homepage_type' => 'icon' ) ),
			)
		);

		// Prefix
		if (
			$this->is_breadcrumbs_enabled( 'breadcrumbs-enable' ) &&
			'' !== $this->is_breadcrumbs_enabled( 'breadcrumbs-prefix' )
		) {
			$this->add_control(
				'breadcrumbs_prefix_heading_yoast',
				array(
					'label' => __( 'Prefix', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'source' => 'yoast' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'breadcrumbs_prefix_typography_yoast',
					'selector' => '{{WRAPPER}}.cmsmasters-breadcrumbs-type-yoast .cmsmasters-widget-breadcrumbs__content',
					'condition' => array( 'source' => 'yoast' ),
				)
			);

			$this->add_control(
				'breadcrumbs_prefix_color_yoast',
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}.cmsmasters-breadcrumbs-type-yoast .cmsmasters-widget-breadcrumbs__content' => 'color: {{VALUE}};',
					),
					'condition' => array( 'source' => 'yoast' ),
				)
			);

			$this->add_responsive_control(
				'breadcrumbs_prefix_gap_yoast',
				array(
					'label' => __( 'Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'selectors' => array(
						'body:not(.rtl) {{WRAPPER}}.cmsmasters-breadcrumbs-type-yoast .cmsmasters-widget-breadcrumbs__content > span' => 'margin-left: {{SIZE}}{{UNIT}};',
						'body.rtl {{WRAPPER}}.cmsmasters-breadcrumbs-type-yoast .cmsmasters-widget-breadcrumbs__content > span' => 'margin-right: {{SIZE}}{{UNIT}};',
					),
					'condition' => array( 'source' => 'yoast' ),
				)
			);
		}

		if (
			function_exists( 'rank_math_the_breadcrumbs' ) &&
			Helper::get_settings( 'general.breadcrumbs' ) &&
			'' !== Helper::get_settings( 'general.breadcrumbs_prefix' )
		) {
			$this->add_control(
				'breadcrumbs_prefix_heading_rank',
				array(
					'label' => __( 'Prefix', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array( 'source' => 'rank' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'breadcrumbs_prefix_typography_rank',
					'selector' => '{{WRAPPER}}.cmsmasters-breadcrumbs-type-rank .label',
					'condition' => array( 'source' => 'rank' ),
				)
			);

			$this->add_control(
				'breadcrumbs_prefix_color_rank',
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}.cmsmasters-breadcrumbs-type-rank .label' => 'color: {{VALUE}};',
					),
					'condition' => array( 'source' => 'rank' ),
				)
			);

			$this->add_responsive_control(
				'breadcrumbs_prefix_gap_rank',
				array(
					'label' => __( 'Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'selectors' => array(
						'body:not(.rtl) {{WRAPPER}}.cmsmasters-breadcrumbs-type-rank .label' => 'margin-right: {{SIZE}}{{UNIT}};',
						'body.rtl {{WRAPPER}}.cmsmasters-breadcrumbs-type-rank .label' => 'margin-right: {{SIZE}}{{UNIT}};',
					),
					'condition' => array( 'source' => 'rank' ),
				)
			);
		}

		$this->add_control(
			'breadcrumbs_prefix_heading',
			array(
				'label' => __( 'Prefix', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array_merge( $condition_cms, array( 'prefix_show' => 'yes' ) ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'breadcrumbs_prefix_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-breadcrumbs__prefix',
				'condition' => array_merge( $condition_cms, array( 'prefix_show' => 'yes' ) ),
			)
		);

		$this->add_control(
			'breadcrumbs_prefix_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-breadcrumbs__prefix' => 'color: {{VALUE}};',
				),
				'condition' => array_merge( $condition_cms, array( 'prefix_show' => 'yes' ) ),
			)
		);

		$this->add_responsive_control(
			'breadcrumbs_prefix_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'body:not(.rtl) {{WRAPPER}} .cmsmasters-widget-breadcrumbs__prefix' => 'margin-right: {{SIZE}}{{UNIT}};',
					'body.rtl {{WRAPPER}} .cmsmasters-widget-breadcrumbs__prefix' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array_merge( $condition_cms, array( 'prefix_show' => 'yes' ) ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render breadcrumbs widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! is_front_page() || ( ! $settings['hide_on_front'] && is_front_page() ) ) {
			echo '<div class="cmsmasters-widget-breadcrumbs__container">' .
				'<div class="cmsmasters-widget-breadcrumbs__content">';

			$breadcrumbs = 'elementor-widget-cmsmasters-breadcrumbs';
			$woo_breadcrumbs = 'elementor-widget-cmsmasters-woo-breadcrumbs';
			$source = ( isset( $settings['source'] ) ? $settings['source'] : '' );

			if (
				( $breadcrumbs === $this->get_widget_class() &&
					(
						! $this->is_breadcrumbs_enabled( 'breadcrumbs-enable' ) ||
						( $this->is_breadcrumbs_enabled( 'breadcrumbs-enable' ) && 'cmsmasters' === $source )
					)
				) ||
				$woo_breadcrumbs === $this->get_widget_class()
			) {
				$this->get_prefix();
			}

					$this->get_breadcrumbs();

				echo '</div>' .
			'</div>';
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @return Yoast Seo breadcrumbs
	 */
	public function get_yoast_seo() {
		if ( function_exists( 'yoast_breadcrumb' ) && $this->is_breadcrumbs_enabled( 'breadcrumbs-enable' ) ) {
			return yoast_breadcrumb();
		}
	}

	/**
	 * Render homepage value output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed assignment `homepage_icon_value` variable. Added check on empty.
	 */
	public function get_homepage() {
		$settings = $this->get_settings_for_display();

		$homepage_type = ( isset( $settings['homepage_type'] ) ? $settings['homepage_type'] : '' );
		$homepage_text = ( isset( $settings['homepage_text'] ) ? $settings['homepage_text'] : '' );
		$homepage_icon_value = ( isset( $settings['homepage_icon']['value'] ) ? $settings['homepage_icon']['value'] : '' );
		$homepage_aria_label = ( 'icon' === $homepage_type ? ' aria-label="Home page"' : '' );

		if ( 'none' !== $homepage_type ) {
			echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="cmsmasters-home"' . $homepage_aria_label . '>';

			if ( 'text' === $homepage_type ) {
				echo ( '' !== $homepage_text ? esc_html( $homepage_text ) : esc_html__( 'Home', 'cmsmasters-elementor' ) );
			} elseif ( 'icon' === $homepage_type ) {
				if ( '' !== $homepage_icon_value ) {
					Icons_Manager::render_icon( $settings['homepage_icon'], array( 'aria-hidden' => 'true' ) );
				} else {
					Icons_Manager::render_icon(
						array(
							'value' => 'fas fa-home',
							'library' => 'fa-solid',
						),
						array( 'aria-hidden' => 'true' )
					);
				}
			}

			echo '</a>';
		}
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.0.0
	 * @since 1.16.0 Fixed style dependencies.
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		$style_depends = array(
			'widget-cmsmasters-breadcrumbs',
		);

		if ( Icons_Manager::is_migration_allowed() ) {
			$style_depends = array_merge( $style_depends, array(
				'elementor-icons-fa-solid',
				'elementor-icons-fa-brands',
				'elementor-icons-fa-regular',
			) );
		}

		return $style_depends;
	}

	/**
	 * Render breadcrumbs widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @since 1.3.8 Fixed deprecated function _content_template.
	 */
	protected function content_template() {}
}
