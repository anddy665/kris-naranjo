<?php
namespace CmsmastersElementor\Modules\Blog\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Blog\Classes\Border_Columns;
use CmsmastersElementor\Modules\Blog\Widgets\Base_Blog\Base_Blog_Customizable;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Addon blog grid widget.
 *
 * Addon widget that displays blog grid.
 *
 * @since 1.0.0
 */
class Blog_Grid extends Base_Blog_Customizable {

	/**
	 * Border Columns instance.
	 *
	 * @var Border_Columns
	 */
	private $border_columns;


	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Posts Grid', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_icon() {
		return 'cmsicon-posts-grid';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array_unique(
			array_merge(
				parent::get_unique_keywords(),
				array(
					'grig',
					'masonry',
				)
			)
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
	 * @since 1.0.0
	 */
	public function register_controls() {
		parent::register_controls();

		$this->injection_section_content_layout();

		$this->injection_section_marker_content();

		$this->injection_section_style_layout();

		$this->injection_marker_style_section();
	}

	/**
	 * Register blog grid controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function get_controls_to_hide_for_template() {
		$controls_ids = parent::get_controls_to_hide_for_template();

		$controls_ids[] = 'image_ratio';
		$controls_ids[] = 'image_ratio_switcher';

		return $controls_ids;
	}

	/**
	 * Blog Grid Widget constructor.
	 *
	 * Initializing the widget blog grid class.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		$this->border_columns = new Border_Columns( $this );

		parent::__construct( $data, $args );
	}

	/**
	 * Register blog grid controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function injection_section_content_layout() {
		$default_columns = 3;

		$this->start_injection(
			array(
				'of' => 'alignment',
				'at' => 'before',
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label' => __( 'Posts', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => $default_columns * $default_columns,
				'min' => 1,
				'separator' => 'before',
				'condition' => array(
					self::QUERY_CONTROL_PREFIX . '_post_type!' => 'current_query',
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label' => __( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => $default_columns,
				'mobile_default' => '1',
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors' => array(
					$this->get_blog_selector() => '--cmsmasters-blog-columns: {{VALUE}}',
				),
				'frontend_available' => true,
			)
		);

		$this->end_injection();

		$this->start_injection(
			array(
				'of' => 'alignment',
				'at' => 'after',
			)
		);

		$this->add_control(
			'post_inner_position_v',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'middle' => array(
						'title' => __( 'Middle', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'prefix_class' => 'cmsmasters-blog-grid-inner__align-v-',
				'condition' => array(
					'masonry' => '',
				),
			)
		);

		$this->add_control(
			'masonry',
			array(
				'label' => __( 'Masonry', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'return_value' => 'masonry',
				'prefix_class' => 'cmsmasters--',
				'render_type' => 'ui',
				'frontend_available' => true,
				'condition' => array(
					'columns!' => '1',
				),
			)
		);

		$this->end_injection();

		$this->start_injection(
			array(
				'of' => 'thumbnail_show',
			)
		);

		$this->end_injection();

		$this->update_control(
			'image_ratio',
			array(
				'condition' => array(
					'masonry' => '',
				),
			),
			array(
				'recursive' => true,
			)
		);

		$this->update_control(
			'image_ratio_switcher',
			array(
				'condition' => array(
					'masonry' => '',
				),
			),
			array(
				'recursive' => true,
			)
		);
	}

	/**
	 * Register marker controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.4.0
	 */
	protected function injection_section_marker_content() {
		$this->start_injection(
			array(
				'at' => 'before',
				'of' => 'section_query',
			)
		);

		$this->start_controls_section(
			'section_marker',
			array(
				'label' => __( 'Marker', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'global_marker',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'icon' => __( 'Icon', 'cmsmasters-elementor' ),
					'numeric' => __( 'Numeric', 'cmsmasters-elementor' ),
				),
				'render_type' => 'template',
				'default' => 'icon',
				'prefix_class' => 'cmsmasters-blog-grid-marker__marker-',
			)
		);

		$this->add_control(
			'global_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => '',
					'library' => '',
				),
				'recommended' => array(
					'fa-solid' => array(
						'circle',
						'square',
						'square-full',
						'check',
						'check-circle',
						'minus',
					),
					'fa-regular' => array(
						'circle',
					),
				),
				'skin' => 'inline',
				'condition' => array( 'global_marker' => 'icon' ),
			)
		);

		$this->add_control(
			'number_type',
			array(
				'label' => __( 'Number Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'decimal' => __( 'Decimal', 'cmsmasters-elementor' ),
					'decimal-leading-zero' => __( 'Decimal Leading Zero', 'cmsmasters-elementor' ),
					'upper-roman' => __( 'Roman', 'cmsmasters-elementor' ),
				),
				'default' => 'decimal',
				'condition' => array(
					'global_marker' => 'numeric',
				),
			)
		);

		$this->add_control(
			'numb_symbol',
			array(
				'label' => __( 'Number Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( '.', 'cmsmasters-elementor' ),
				'label_block' => false,
				'show_label' => true,
				'render_type' => 'template',
				'condition' => array(
					'global_marker' => 'numeric',
				),
			)
		);

		$this->add_control(
			'marker_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'stacked' => __( 'Stacked', 'cmsmasters-elementor' ),
					'framed' => __( 'Framed', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'prefix_class' => 'cmsmasters-blog-grid-marker__view-',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'global_marker',
									'operator' => '===',
									'value' => 'icon',
								),
								array(
									'name' => 'global_icon[value]',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'name' => 'global_marker',
							'operator' => '===',
							'value' => 'numeric',
						),
					),
				),
			)
		);

		$this->add_control(
			'marker_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'circle' => __( 'Circle', 'cmsmasters-elementor' ),
					'square' => __( 'Square', 'cmsmasters-elementor' ),
				),
				'default' => 'circle',
				'prefix_class' => 'cmsmasters-blog-grid-marker__shape-',
				'condition' => array( 'marker_view!' => 'default' ),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'global_marker',
									'operator' => '===',
									'value' => 'icon',
								),
								array(
									'name' => 'global_icon[value]',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'name' => 'global_marker',
							'operator' => '===',
							'value' => 'numeric',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'items_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-blog-grid-marker__position%s-',
			)
		);

		$this->add_responsive_control(
			'item_position_v',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'middle' => array(
						'title' => __( 'Middle', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'prefix_class' => 'cmsmasters-blog-grid-marker__align-v%s-',
				'condition' => array(
					'items_position!' => 'top',
				),
			)
		);

		$this->add_responsive_control(
			'item_position_h',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'prefix_class' => 'cmsmasters-blog-grid-marker__align-h%s-',
				'condition' => array(
					'items_position' => 'top',
				),
			)
		);

		$this->end_controls_section();

		$this->end_injection();
	}

	/**
	 * Register blog grid controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 * @since 1.3.3 Fixed on breakpoints.
	 */
	protected function injection_section_style_layout() {
		$this->start_injection(
			array(
				'at' => 'before',
				'of' => 'section_style_post',
			)
		);

		$this->start_controls_section(
			'section_style_layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'post_gap_column',
			array(
				'label' => __( 'Columns Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 150,
					),
					'%' => array(
						'min' => 0,
						'max' => 25,
						'step' => 0.5,
					),
				),
				'size_units' => array( 'px', '%', 'vw', 'vh' ),
				'frontend_available' => true,
				'selectors' => array(
					$this->get_blog_selector() => '--cmsmasters-blog-gap-column: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'columns!' => '1',
				),
			)
		);

		$this->add_responsive_control(
			'post_gap_row',
			array(
				'label' => __( 'Rows Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 150,
					),
				),
				'size_units' => array( 'px', 'vw', 'vh' ),
				'frontend_available' => true,
				'selectors' => array(
					$this->get_blog_selector() => '--cmsmasters-blog-gap-row: {{SIZE}}px;',
				),
			)
		);

		$this->add_responsive_control(
			'layout_post_space',
			array(
				'label' => __( 'Posts Container Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__posts-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'borders_heading',
			array(
				'label' => __( 'Separators', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->border_columns->add_controls();

		$this->update_control(
			'border_columns_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
			)
		);

		$this->update_control(
			'border_columns_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
			)
		);

		$this->update_control(
			'border_vertical_width',
			array(
				'label' => __( 'Vertical Width', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'border_horizontal_width',
			array(
				'label' => __( 'Horizontal Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 5,
					),
				),
				'render_type' => 'ui',
				'size_units' => array( 'px' ),
				'frontend_available' => true,
				'condition' => array(
					'border_columns_type!' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post::after' => 'border-bottom-width: {{SIZE}}{{UNIT}}; border-style: {{border_columns_type.VALUE}};',
				),
			)
		);

		$this->add_control(
			'border_row_color',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 'true',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post::after' => ' border-color: {{border_columns_color.VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->end_injection();
	}

	/**
	 * Register widget marker style section.
	 *
	 * Adds blog grid widget `marker style` settings section controls.
	 *
	 * @since 1.4.0
	 */
	protected function injection_marker_style_section() {
		$this->start_controls_section(
			'section_icon_style',
			array(
				'label' => __( 'Marker', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'global_marker',
									'operator' => '===',
									'value' => 'icon',
								),
								array(
									'name' => 'global_icon[value]',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'name' => 'global_marker',
							'operator' => '===',
							'value' => 'numeric',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'number_min_width',
			array(
				'label' => __( 'Min Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog .cmsmasters-blog__post-marker-wrapper' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-blog .cmsmasters-blog__post-marker-wrapper + .cmsmasters-blog__post-inner' => 'width: calc( 100% - {{SIZE}}{{UNIT}} );',
				),
				'condition' => array( 'global_marker' => 'numeric' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'number_typography',
				'exclude' => array( 'line_height' ),
				'selector' => '{{WRAPPER}} .cmsmasters-blog .cmsmasters-blog__post-marker',
				'condition' => array( 'global_marker' => 'numeric' ),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array( 'min' => 5 ),
					'em' => array( 'min' => 0.5 ),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog .cmsmasters-blog__post-marker' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'global_marker' => 'icon' ),
			)
		);

		$this->start_controls_tabs( 'icon_colors' );

		$this->start_controls_tab(
			'icon_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'icon_color',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-blog-grid-marker-icon-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_secondary_color',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-blog-grid-marker-icon-secondary-color: {{VALUE}};',
				),
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'icon_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-blog .cmsmasters-blog__post-marker',
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_control(
			'icon_rotate',
			array(
				'label' => __( 'Rotate', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default' => array( 'unit' => 'deg' ),
				'range' => array(
					'deg' => array(
						'min' => 0,
						'max' => 360,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-blog-grid-marker-icon-rotate: rotate({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-blog-grid-marker-icon-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_secondary_color',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-blog-grid-marker-icon-hover-secondary-color: {{VALUE}};',
				),
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'icon_hover_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-blog .cmsmasters-blog__post:hover .cmsmasters-blog__post-marker',
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_control(
			'icon_rotate_hover',
			array(
				'label' => __( 'Rotate', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default' => array( 'unit' => 'deg' ),
				'range' => array(
					'deg' => array(
						'min' => 0,
						'max' => 360,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-blog-grid-marker-icon-rotate-hover: rotate({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'icon_wrapper_size',
			array(
				'label' => __( 'Wrapper Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 20,
						'max' => 200,
					),
					'em' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-blog-grid-marker-icon-wrapper: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'icon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 0,
						'max' => 3,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-blog-grid-marker-icon-padding: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'icon_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 0,
						'max' => 3,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-blog-grid-marker-icon-border-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'marker_view' => 'framed' ),
			)
		);

		$this->add_responsive_control(
			'icon_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-blog-grid-marker-icon-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'icon_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog-grid .cmsmasters-blog__post .cmsmasters-blog__post-marker-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * post markers.
	 *
	 * @since 1.4.0
	 */
	public function marker_post() {
		$settings = $this->get_settings_for_display();

		$is_icon = ( 'icon' === $settings['global_marker'] && '' !== $settings['global_icon']['value'] );

		if ( 'numeric' === $settings['global_marker'] || $is_icon ) {
			echo '<div class="cmsmasters-blog__post-marker-wrapper">' .
				'<span class="cmsmasters-blog__post-marker">';

			if ( $is_icon ) {
				$this->get_icon_post();
			} else {
				$this->get_number_post();
			}

				echo '</span>' .
			'</div>';
		}
	}

	/**
	 * Get Icon.
	 *
	 * @since 1.4.0
	 */
	public function get_icon_post() {
		$settings = $this->get_settings_for_display();

		Icons_Manager::render_icon( $settings['global_icon'], array( 'aria-hidden' => 'true' ) );
	}

	/**
	 * Get number.
	 *
	 * @since 1.4.0
	 */
	public function get_number_post() {
		$settings = $this->get_settings_for_display();
		$wp_query = $this->get_query();

		$current_post = $wp_query->current_post;
		$all_pages = $wp_query->query_vars['posts_per_page'];
		$current_page = $this->get_paged_var();

		$number_post = 1 + $current_post + $all_pages * $current_page - $all_pages;

		if ( 'decimal-leading-zero' === $settings['number_type'] ) {
			$zero = 0;

			if ( 9 < $number_post ) {
				$zero = '';
			}

			$number_post = $zero . $number_post;
		}

		if ( 'upper-roman' === $settings['number_type'] ) {
			$number_post = $this->number_to_roman( $number_post, true );
		}

		if ( '' !== $settings['numb_symbol'] ) {
			$symbol = $settings['numb_symbol'];

			$number_post = $number_post . $symbol;
		}

		echo esc_html( $number_post );
	}

	/**
	 * Get paged post.
	 *
	 * @since 1.4.0
	 */
	public function get_paged_var() {
		$wp_query = $this->get_query();

		$paged = 1;

		if ( $wp_query->get( 'paged' ) ) {
			$paged = $wp_query->get( 'paged' );
		} elseif ( isset( $wp_query->query['paged'] ) ) {
			$paged = $wp_query->query['paged'];
		}

		return max( 1, (int) $paged );
	}

	/**
	 * Translation from Arbian to Roman numerals.
	 *
	 * @since 1.4.0
	 */
	public function number_to_roman( $num, $is_upper = true ) {
		$n = intval( $num );
		$res = '';

		$roman_numerals = array(
			'M' => 1000,
			'CM' => 900,
			'D' => 500,
			'CD' => 400,
			'C' => 100,
			'XC' => 90,
			'L' => 50,
			'XL' => 40,
			'X' => 10,
			'IX' => 9,
			'V' => 5,
			'IV' => 4,
			'I' => 1,
		);

		foreach ( $roman_numerals as $roman => $number ) {
			$matches = intval( $n / $number );
			$res .= str_repeat( $roman, $matches );
			$n = $n % $number;
		}

		if ( $is_upper ) {
			return $res;
		} else {
			return strtolower( $res );
		}
	}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			array(
				'field' => 'read_more_text',
				'type' => esc_html__( 'Read More Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'meta_data_top_author_prefix',
				'type' => esc_html__( 'Author Prefix', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'meta_data_top_date_date_format_custom',
				'type' => esc_html__( 'Date Format', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'meta_data_bottom_author_prefix',
				'type' => esc_html__( 'Author Prefix', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'meta_data_bottom_date_date_format_custom',
				'type' => esc_html__( 'Date Format', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'header_title',
				'type' => esc_html__( 'Header Title Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'filter_default_text',
				'type' => esc_html__( 'All Items Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'nothing_found_message',
				'type' => esc_html__( 'Nothing Found Message', 'cmsmasters-elementor' ),
				'editor_type' => 'AREA',
			),
			array(
				'field' => 'pagination_load_more_text_normal',
				'type' => esc_html__( 'Load More Text (Normal state)', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_load_more_text_loading',
				'type' => esc_html__( 'Load More Text (Loading state)', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_infinite_scroll_text',
				'type' => esc_html__( 'Infinite Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_text_prev',
				'type' => esc_html__( 'Pagination Previous Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_text_next',
				'type' => esc_html__( 'Pagination Next Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'separator_meta_data_top_content',
				'type' => esc_html__( 'Top Meta Data Content', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'separator_taxonomy_meta_data_top_content',
				'type' => esc_html__( 'Top Taxonomy Content', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'separator_meta_data_bottom_content',
				'type' => esc_html__( 'Bottom Meta Data Content', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'separator_taxonomy_meta_data_bottom_content',
				'type' => esc_html__( 'Bottom Taxonomy Content', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'separator_filter_content',
				'type' => esc_html__( 'Separator Content', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
