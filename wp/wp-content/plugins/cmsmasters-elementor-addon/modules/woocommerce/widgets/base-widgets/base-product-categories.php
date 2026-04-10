<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets\Base_Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;


use Elementor\Controls_Manager;
use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;
use Elementor\Group_Control_Image_Size;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Base_Product_Categories extends Base_Widget {

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
			'widget-cmsmasters-woocommerce',
		);
	}

	/**
	 * LazyLoad widget use control.
	 *
	 * @since 1.11.1
	 *
	 * @return bool true - with control, false - without control.
	 */
	public function lazyload_widget_use_control() {
		return true;
	}

	public function get_base_widget_class() {
		return 'cmsmasters-woo-product-categories';
	}

	public function get_widget_selector() {
		return '.' . $this->get_base_widget_class();
	}

	protected function get_devices_default_args() {
		$devices_required = array();

		foreach ( Breakpoints_Manager::get_default_config() as $breakpoint_name => $breakpoint_config ) {
			$devices_required[ $breakpoint_name ] = array( 'required' => false );
		}

		return $devices_required;
	}

	protected function register_controls() {
		parent::register_controls();

		$this->register_product_categories_controls_content();

		$this->register_product_categories_query_controls_content();

		$this->register_product_categories_controls_style();

		$this->register_product_categories_image_controls_style();

		$this->register_product_categories_title_controls_style();
	}

	/**
	 * Register product category content controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.11.0
	 */
	protected function register_product_categories_controls_content() {
		$this->start_controls_section(
			'product_categories_section_content',
			array(
				'label' => esc_html__( 'Categories', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'product_categories_number',
			array(
				'label' => esc_html__( 'Number of categories', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '4',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register product category query content controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.11.0
	 */
	protected function register_product_categories_query_controls_content() {
		$this->start_controls_section(
			'product_categories_query_section_content',
			array(
				'label' => esc_html__( 'Query', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'source',
			array(
				'label' => esc_html__( 'Source', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => esc_html__( 'Show All', 'cmsmasters-elementor' ),
					'by_id' => esc_html__( 'Manual Selection', 'cmsmasters-elementor' ),
					'by_parent' => esc_html__( 'By Parent', 'cmsmasters-elementor' ),
					'current_subcategories' => esc_html__( 'Current Subcategories', 'cmsmasters-elementor' ),
				),
				'label_block' => true,
			)
		);

		$categories = get_terms( 'product_cat' );

		$options = array();

		foreach ( $categories as $category ) {
			$options[ $category->term_id ] = $category->name;
		}

		$this->add_control(
			'categories',
			array(
				'label' => esc_html__( 'Categories', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $options,
				'default' => array(),
				'label_block' => true,
				'multiple' => true,
				'condition' => array( 'source' => 'by_id' ),
			)
		);

		$parent_options = array( '0' => esc_html__( 'Only Top Level', 'cmsmasters-elementor' ) ) + $options;

		$this->add_control(
			'parent',
			array(
				'label' => esc_html__( 'Parent', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $parent_options,
				'default' => '0',
				'condition' => array( 'source' => 'by_parent' ),
			)
		);

		$this->add_control(
			'hide_empty',
			array(
				'label' => esc_html__( 'Hide Empty', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => array( 'source' => '' ),
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label' => esc_html__( 'Order By', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'name' => esc_html__( 'Name', 'cmsmasters-elementor' ),
					'slug' => esc_html__( 'Slug', 'cmsmasters-elementor' ),
					'description' => esc_html__( 'Description', 'cmsmasters-elementor' ),
					'count' => esc_html__( 'Count', 'cmsmasters-elementor' ),
				),
				'default' => 'name',
			)
		);

		$this->add_control(
			'order',
			array(
				'label' => esc_html__( 'Order', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'asc' => esc_html__( 'ASC', 'cmsmasters-elementor' ),
					'desc' => esc_html__( 'DESC', 'cmsmasters-elementor' ),
				),
				'default' => 'desc',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register product category style controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.11.0
	 */
	protected function register_product_categories_controls_style() {
		$this->start_controls_section(
			'product_categories_section_style',
			array(
				'label' => esc_html__( 'Categories', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'product_categories_row_align',
			array(
				'label' => esc_html__( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'selectors_dictionary' => array(
					'flex-start' => '--cmsmasters-product-categories-row-align: flex-start; --cmsmasters-product-categories-text-align: left;',
					'center' => '--cmsmasters-product-categories-row-align: center; --cmsmasters-product-categories-text-align: center;',
					'flex-end' => '--cmsmasters-product-categories-row-align: flex-end; --cmsmasters-product-categories-text-align: right;',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '{{VALUE}};',
				),
				'condition' => array( 'product_categories_title_count_position' => 'row' ),
			)
		);

		$this->add_responsive_control(
			'product_categories_column_align',
			array(
				'label' => esc_html__( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'selectors_dictionary' => array(
					'flex-start' => '--cmsmasters-product-categories-column-align: flex-start; --cmsmasters-product-categories-text-align: left;',
					'center' => '--cmsmasters-product-categories-column-align: center; --cmsmasters-product-categories-text-align: center;',
					'flex-end' => '--cmsmasters-product-categories-column-align: flex-end; --cmsmasters-product-categories-text-align: right;',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '{{VALUE}};',
				),
				'condition' => array( 'product_categories_title_count_position' => 'column' ),
			)
		);

		$this->start_controls_tabs( 'product_categories_tabs' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$this->start_controls_tab(
				"product_categories_{$main_key}_tab",
				array( 'label' => $label )
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BACKGROUND_GROUP,
				array( 'name' => "product_categories_{$main_key}" )
			);

			$this->add_control(
				"product_categories_{$main_key}_border_color",
				array(
					'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-product-categories-{$main_key}-border-color: {{VALUE}};",
					),
					'condition' => array( 'product_categories_bd_border!' => 'none' ),
				)
			);

			$this->add_control(
				"product_categories_{$main_key}_border_radius",
				array(
					'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
						'em',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-product-categories-{$main_key}-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array( 'name' => "product_categories_{$main_key}" )
			);

			if ( 'hover' === $main_key ) {
				$this->add_control(
					"product_categories_{$main_key}_transition_duration",
					array(
						'label' => esc_html__( 'Transition Duration', 'cmsmasters-elementor' ) . ' (ms)',
						'type' => Controls_Manager::SLIDER,
						'range' => array(
							'px' => array(
								'min' => 0,
								'max' => 1000,
								'step' => 100,
							),
						),
						'selectors' => array(
							'{{WRAPPER}}' => "--cmsmasters-product-categories-{$main_key}-transition-duration: {{SIZE}}ms;",
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'product_categories_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-product-categories-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'product_categories_bd',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array( 'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ) ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register product category image style controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.11.0
	 */
	protected function register_product_categories_image_controls_style() {
		$this->start_controls_section(
			'product_categories_image_section_style',
			array(
				'label' => esc_html__( 'Image', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'product_categories_image',
				'default' => 'full',
				'exclude' => array( 'custom' ),
			)
		);

		$this->add_responsive_control(
			'product_categories_image_spacing',
			array(
				'label' => esc_html__( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-product-categories-image-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'product_categories_image_tabs' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$this->start_controls_tab(
				"product_categories_image_{$main_key}_tab",
				array( 'label' => $label )
			);

			$this->add_control(
				"product_categories_image_{$main_key}_border_color",
				array(
					'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-product-categories-image-{$main_key}-border-color: {{VALUE}};",
					),
					'condition' => array( 'product_categories_image_bd_border!' => 'none' ),
				)
			);

			$this->add_control(
				"product_categories_image_{$main_key}_border_radius",
				array(
					'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
						'em',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-product-categories-image-{$main_key}-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}",
					),
				)
			);

			if ( 'hover' === $main_key ) {
				$this->add_responsive_control(
					"product_categories_image_{$main_key}_scale",
					array(
						'label' => esc_html__( 'Scale', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => array( 'px' ),
						'range' => array(
							'px' => array(
								'min' => 0.8,
								'max' => 1.2,
								'step' => 0.05,
							),
						),
						'selectors' => array(
							'{{WRAPPER}}' => "--cmsmasters-product-categories-image-{$main_key}-scale: {{SIZE}}",
						),
					)
				);
			}

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array( 'name' => "product_categories_image_{$main_key}" )
			);

			$this->add_group_control(
				CmsmastersControls::VARS_CSS_FILTER_GROUP,
				array( 'name' => "product_categories_image_{$main_key}" )
			);

			if ( 'hover' === $main_key ) {
				$this->add_control(
					"product_categories_image_{$main_key}_transition_duration",
					array(
						'label' => esc_html__( 'Transition Duration', 'cmsmasters-elementor' ) . ' (ms)',
						'type' => Controls_Manager::SLIDER,
						'range' => array(
							'px' => array(
								'min' => 0,
								'max' => 1000,
								'step' => 100,
							),
						),
						'selectors' => array(
							'{{WRAPPER}}' => "--cmsmasters-product-categories-image-{$main_key}-transition-duration: {{SIZE}}ms;",
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'product_categories_image_bd',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array( 'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ) ),
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register product category title style controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.11.0
	 */
	protected function register_product_categories_title_controls_style() {
		$this->start_controls_section(
			'product_categories_title_section_style',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array( 'name' => 'product_categories_title_typography' )
		);

		$this->start_controls_tabs( 'product_categories_title_tabs' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$this->start_controls_tab(
				"product_categories_title_{$main_key}_tab",
				array( 'label' => $label )
			);

			$this->add_control(
				"product_categories_title_{$main_key}_color",
				array(
					'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-product-categories-title-{$main_key}-color: {{VALUE}}",
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
				array( 'name' => "product_categories_title_{$main_key}" )
			);

			if ( 'hover' === $main_key ) {
				$this->add_control(
					"product_categories_title_{$main_key}_transition_duration",
					array(
						'label' => esc_html__( 'Transition Duration', 'cmsmasters-elementor' ) . ' (ms)',
						'type' => Controls_Manager::SLIDER,
						'range' => array(
							'px' => array(
								'min' => 0,
								'max' => 1000,
								'step' => 100,
							),
						),
						'selectors' => array(
							'{{WRAPPER}}' => "--cmsmasters-product-categories-title-{$main_key}-transition-duration: {{SIZE}}ms;",
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'product_categories_title_count_heading',
			array(
				'label' => esc_html__( 'Product Count', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'product_categories_title_count_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'row' => array( 'title' => __( 'Inline', 'cmsmasters-elementor' ) ),
					'column' => array( 'title' => __( 'Block', 'cmsmasters-elementor' ) ),
				),
				'label_block' => false,
				'toggle' => false,
				'default' => 'row',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-product-categories-title-count-position: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'product_categories_title_count_vertical_align',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'baseline' => array( 'title' => __( 'Baseline', 'cmsmasters-elementor' ) ),
					'flex-start' => array( 'title' => __( 'Top', 'cmsmasters-elementor' ) ),
					'center' => array( 'title' => __( 'Center', 'cmsmasters-elementor' ) ),
					'flex-end' => array( 'title' => __( 'Bottom', 'cmsmasters-elementor' ) ),
				),
				'label_block' => true,
				'toggle' => false,
				'default' => 'baseline',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-product-categories-title-count-vertical-align: {{VALUE}};',
				),
				'condition' => array( 'product_categories_title_count_position' => 'row' ),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array( 'name' => 'product_categories_title_count_typography' )
		);

		$this->add_control(
			'product_categories_title_count_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-product-categories-title-count-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'product_categories_title_count_spacing',
			array(
				'label' => esc_html__( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-product-categories-title-count-spacing: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'product_categories_title_count_additional_heading',
			array(
				'label' => esc_html__( 'Product Count Additional', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'product_categories_title_count_additional',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'product_categories_title_count_additional_plural',
			array(
				'label' => __( 'Text for Plural', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::TEXT,
				'condition' => array( 'product_categories_title_count_additional!' => '' ),
			)
		);

		$this->add_control(
			'product_categories_title_count_additional_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'before' => array( 'title' => __( 'Before', 'cmsmasters-elementor' ) ),
					'after' => array( 'title' => __( 'After', 'cmsmasters-elementor' ) ),
				),
				'label_block' => false,
				'toggle' => false,
				'default' => 'before',
				'condition' => array( 'product_categories_title_count_additional!' => '' ),
			)
		);

		$this->add_responsive_control(
			'product_categories_title_count_additional_spacing',
			array(
				'label' => esc_html__( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-product-categories-title-count-additional-spacing: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'product_categories_title_count_additional!' => '' ),
			)
		);

		$this->add_control(
			'product_categories_title_count_additional_brackets',
			array(
				'label' => esc_html__( 'Brackets', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array( 'product_categories_title_count_additional!' => '' ),
			)
		);

		$this->end_controls_section();
	}

	protected function cmsmasters_product_category_thumbnail( $category ) {
		$settings = $this->get_settings_for_display();

		$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
		$product_categories_image_size = ( isset( $settings['product_categories_image_size'] ) ? $settings['product_categories_image_size'] : '' );

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, $product_categories_image_size );
			$image = $image[0];
			$image_srcset = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $thumbnail_id, $product_categories_image_size ) : false;
			$image_sizes = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $thumbnail_id, $product_categories_image_size ) : false;
		} else {
			$image = wc_placeholder_img_src();
			$image_srcset = false;
			$image_sizes = false;
		}

		if ( $image ) {
			echo '<figure class="' . $this->get_base_widget_class() . '__image">';

				$image = str_replace( ' ', '%20', $image );

				echo '<img 
					src="' . esc_url( $image ) . '" 
					alt="' . esc_attr( $category->name ) . '"
					' . ( $image_srcset ? ' srcset="' . esc_attr( $image_srcset ) . '"' : '' ) . '
					' . ( $image_sizes ? ' sizes="' . esc_attr( $image_sizes ) . '"' : '' ) . '
				/>';
		}

		echo '</figure>';
	}

	protected function cmsmasters_product_category_count( $category ) {
		$settings = $this->get_settings_for_display();

		$count = $category->count;

		if ( $count && $count > 0 ) {
			$html = '';
			$count_brackets = '';
			$additional = ( isset( $settings['product_categories_title_count_additional'] ) ? $settings['product_categories_title_count_additional'] : '' );
			$additionals = ( isset( $settings['product_categories_title_count_additional_plural'] ) ? $settings['product_categories_title_count_additional_plural'] : '' );
			$additional_brackets = ( isset( $settings['product_categories_title_count_additional_brackets'] ) ? $settings['product_categories_title_count_additional_brackets'] : '' );

			$additionals = ( '' !== $additionals ? $additionals : $additional );

			if ( '' !== $additional ) {
				if ( $additional_brackets ) {
					$count_brackets = '(' . $count . ')';
				} else {
					$count_brackets = $count;
				}

				$additional = ( $count > 1 ? $additionals : $additional );

				$additional_position = ( isset( $settings['product_categories_title_count_additional_position'] ) ? $settings['product_categories_title_count_additional_position'] : '' );

				if ( 'after' === $additional_position ) {
					$html = $count_brackets . '<span class="cmsmasters_product_category_count_after_additional">' . esc_html( $additional ) . '</span>';
				} else {
					$html = '<span class="cmsmasters_product_category_count_before_additional">' . esc_html( $additional ) . '</span>' . $count_brackets;
				}

				$count = sprintf( $html, $count );
			} else {
				$count = '(' . $count . ')';
			}

			echo wp_kses_post( '<mark class="cmsmasters-woo-product-categories__count">' . $count . '</mark>' );
		}
	}

	protected function cmsmasters_product_category_title( $category ) {
		echo '<h2 class="cmsmasters-woo-product-categories__title">';

			echo esc_html( $category->name );

			$this->cmsmasters_product_category_count( $category );

		echo '</h2>';
	}

	protected function get_product_category( $category ) {
		echo '<div class="product-category product">';

			$category_term = get_term( $category, 'product_cat' );
			$category_name = ( ! $category_term || is_wp_error( $category_term ) ) ? '' : $category_term->name;

			echo '<a 
				href="' . esc_url( get_term_link( $category, 'product_cat' ) ) . '" 
				aria-label="' . sprintf( esc_attr__( 'Visit product category %1$s', 'cmsmasters-elementor' ), esc_attr( $category_name ) ) . '"' .
			'>';

				$this->cmsmasters_product_category_thumbnail( $category );

				$this->cmsmasters_product_category_title( $category );

			echo '</a>';

		echo '</div>';
	}

	protected function get_product_categories() {
		$settings = $this->get_settings();

		$atts = array(
			'limit' => $settings['product_categories_number'],
			'orderby' => $settings['orderby'],
			'order' => $settings['order'],
			'columns' => '1',
			'hide_empty' => ( 'yes' === $settings['hide_empty'] ) ? 1 : 0,
			'parent' => '',
			'ids' => '',
		);

		if ( 'by_id' === $settings['source'] ) {
			$atts['ids'] = implode( ',', $settings['categories'] );
		} elseif ( 'by_parent' === $settings['source'] ) {
			$atts['parent'] = $settings['parent'];
		} elseif ( 'current_subcategories' === $settings['source'] ) {
			$atts['parent'] = get_queried_object_id();
		}

		$ids = array_filter( array_map( 'trim', explode( ',', $atts['ids'] ) ) );
		$hide_empty = ( true === $atts['hide_empty'] || 'true' === $atts['hide_empty'] || 1 === $atts['hide_empty'] || '1' === $atts['hide_empty'] ) ? 1 : 0;

		// Get terms and workaround WP bug with parents/pad counts.
		$args = array(
			'orderby' => $atts['orderby'],
			'order' => $atts['order'],
			'hide_empty' => $hide_empty,
			'include' => $ids,
			'pad_counts' => true,
			'child_of' => $atts['parent'],
		);

		$product_categories = get_terms( array_merge( array( 'taxonomy' => 'product_cat' ), $args ) );

		if ( '' !== $atts['parent'] ) {
			$product_categories = wp_list_filter(
				$product_categories,
				array( 'parent' => $atts['parent'] )
			);
		}

		if ( $hide_empty ) {
			foreach ( $product_categories as $key => $category ) {
				if ( 0 === $category->count ) {
					unset( $product_categories[ $key ] );
				}
			}
		}

		$atts['limit'] = '-1' === $atts['limit'] ? null : intval( $atts['limit'] );

		if ( $atts['limit'] ) {
			$product_categories = array_slice( $product_categories, 0, $atts['limit'] );
		}

		return $product_categories;
	}
}
