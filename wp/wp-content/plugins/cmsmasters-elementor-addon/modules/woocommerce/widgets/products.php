<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Blog\Classes\Border_Columns;
use CmsmastersElementor\Modules\Blog\Classes\Pagination;
use CmsmastersElementor\Modules\Blog\Module as BlogModule;
use CmsmastersElementor\Modules\Woocommerce\Classes\Base_Products_Renderer;
use CmsmastersElementor\Modules\Woocommerce\Classes\Current_Query_Renderer;
use CmsmastersElementor\Modules\Woocommerce\Classes\Products_Renderer;
use CmsmastersElementor\Modules\Woocommerce\Module as WooModule;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Widget;
use CmsmastersElementor\Modules\Woocommerce\Widgets\Base_Widgets\Base_Products;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Products widget.
 *
 * @since 1.0.0
 */
class Products extends Base_Products {

	use Woo_Widget;

	/**
	 * Pagination instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Pagination
	 */
	protected $pagination;


	/**
	 * Products shortcode instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Base_Products_Renderer
	 */
	protected $shortcode_object;

	/**
	 * Border Columns instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Border_Columns
	 */
	private $border_columns;

	/**
	 * @since 1.0.0
	 */
	public function get_script_depends() {
		return array_merge( array( 'imagesloaded' ), parent::get_script_depends() );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Products', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_icon() {
		return 'cmsicon-products';
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
		return array( 'products' );
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
	 * Get HTML wrapper class.
	 *
	 * Retrieve the widget container class. Can be used to override the
	 * container class for specific widgets.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function get_html_wrapper_class() {
		return parent::get_html_wrapper_class() . ' elementor-widget-cmsmasters-woo-products-grid';
	}

	/**
	 * Register shop widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		parent::register_controls();

		$this->register_query_controls();
		$this->register_section_header();
		$this->register_section_layout_style();

		$this->pagination->register_controls_content();
		$this->pagination->register_controls_style();
	}

	/**
	 * Shop Widget constructor.
	 *
	 * Initializing the widget shop class.
	 *
	 * @since 1.1.0
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		$this->pagination = new Pagination( $this, Products_Renderer::QUERY_CONTROL_NAME );
		$this->border_columns = new Border_Columns( $this );

		parent::__construct( $data, $args );
	}

	/**
	 * Register content shop widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.10.1 Added new "Layout" functionality and style controls for it in the widget header.
	 * @since 1.11.1 Added warning if included wbw product filter.
	 */
	protected function register_main_content_controls() {
		parent::register_main_content_controls();

		$this->start_injection( array(
			'of' => 'section_content',
		) );

		$this->warning_product_filter();

		$this->end_injection();

		$this->start_injection( array(
			'of' => 'template_layout',
		) );

		$this->add_control(
			'products_pre_page',
			array(
				'label' => __( 'Products', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => Products_Renderer::DEFAULT_COLUMNS_AND_ROWS * Products_Renderer::DEFAULT_COLUMNS_AND_ROWS,
				'render_type' => 'template',
				'condition' => array(
					Products_Renderer::QUERY_CONTROL_NAME . '_post_type!' => 'current_query',
				),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label' => __( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 12,
				'default' => Products_Renderer::DEFAULT_COLUMNS_AND_ROWS,
				'tablet_default' => 3,
				'mobile_default' => 1,
				'frontend_available' => true,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-columns: {{VALUE}}',
				),
				'min_affected_device' => array(
					Controls_Stack::RESPONSIVE_DESKTOP => Controls_Stack::RESPONSIVE_TABLET,
					Controls_Stack::RESPONSIVE_TABLET => Controls_Stack::RESPONSIVE_TABLET,
				),
			)
		);

		$this->end_injection();

		$this->start_injection( array(
			'of' => WooModule::CONTROL_TEMPLATE_NAME,
		) );

		$this->add_control(
			'masonry',
			array(
				'label' => __( 'Masonry', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'render_type' => 'ui',
				'frontend_available' => true,
				'return_value' => 'masonry',
				'prefix_class' => 'cmsmasters--',
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'columns',
							'operator' => '!==',
							'value' => '1',
						),
					),
				),
			)
		);

		$this->add_control(
			'show_layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition' => array( 'pagination_show' => 'yes' ),
			)
		);

		$this->add_control(
			'allow_order',
			array(
				'label' => __( 'Sorting', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => array( 'pagination_show' => 'yes' ),
			)
		);

		$this->add_control(
			'show_result_count',
			array(
				'label' => __( 'Show Result Count', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => array( 'pagination_show' => 'yes' ),
			)
		);

		$this->add_control(
			'layout_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Layout', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'pagination_show' => 'yes',
					'show_layout' => 'yes',
				),
			)
		);

		$this->add_control(
			'layout_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => 'View',
				'condition' => array(
					'pagination_show' => 'yes',
					'show_layout' => 'yes',
				),
			)
		);

		$this->add_control(
			'layout_only_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'left' => array( 'title' => __( 'Left', 'cmsmasters-elementor' ) ),
					'center' => array( 'title' => __( 'Center', 'cmsmasters-elementor' ) ),
					'right' => array( 'title' => __( 'Right', 'cmsmasters-elementor' ) ),
				),
				'selectors_dictionary' => array(
					'left' => 'flex-start',
					'center' => 'center',
					'right' => 'flex-end',
				),
				'default' => 'left',
				'label_block' => false,
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-layout-only-position: {{VALUE}};',
				),
				'condition' => array(
					'pagination_show' => 'yes',
					'show_layout' => 'yes',
					'allow_order!' => 'yes',
					'show_result_count!' => 'yes',
				),
			)
		);

		$this->add_control(
			'layout_one_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'left' => array( 'title' => __( 'Left', 'cmsmasters-elementor' ) ),
					'right' => array( 'title' => __( 'Right', 'cmsmasters-elementor' ) ),
				),
				'selectors_dictionary' => array(
					'left' => '-1',
					'right' => '1',
				),
				'default' => 'left',
				'label_block' => false,
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-layout-position: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pagination_show',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'show_layout',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'allow_order',
											'operator' => '===',
											'value' => 'yes',
										),
										array(
											'name' => 'show_result_count',
											'operator' => '!==',
											'value' => 'yes',
										),
									),
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'allow_order',
											'operator' => '!==',
											'value' => 'yes',
										),
										array(
											'name' => 'show_result_count',
											'operator' => '===',
											'value' => 'yes',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'layout_full_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'left' => array( 'title' => __( 'Left', 'cmsmasters-elementor' ) ),
					'with_count' => array( 'title' => __( 'With Count', 'cmsmasters-elementor' ) ),
					'center' => array( 'title' => __( 'Center', 'cmsmasters-elementor' ) ),
					'with_sort' => array( 'title' => __( 'With Sorting', 'cmsmasters-elementor' ) ),
				),
				'selectors_dictionary' => array(
					'left' => '-1',
					'with_count' => '',
					'center' => '',
					'with_sort' => '',
				),
				'default' => 'with_sort',
				'label_block' => true,
				'toggle' => false,
				'prefix_class' => 'cmsmasters-layout-position-',
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-layout-position: {{VALUE}};',
				),
				'condition' => array(
					'pagination_show' => 'yes',
					'show_layout' => 'yes',
					'allow_order' => 'yes',
					'show_result_count' => 'yes',
				),
			)
		);

		$this->add_control(
			'layout_align',
			array(
				'label' => __( 'Align', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left' => '-1',
					'right' => '1',
				),
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-layout-align: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pagination_show',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'show_layout',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'allow_order',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'show_result_count',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'layout_full_position',
									'operator' => '===',
									'value' => 'with_count',
								),
								array(
									'name' => 'layout_full_position',
									'operator' => '===',
									'value' => 'with_sort',
								),
							),
						),
					),
				),
			)
		);

		$this->end_injection();
	}

	/**
	 * Register shop widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_query_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_group_control(
			CmsmastersControls::QUERY_GROUP,
			array(
				'name' => Products_Renderer::QUERY_CONTROL_NAME,
				'post_type' => 'product',
				'presets' => array(
					'include',
					'exclude',
					'order',
				),
				'fields_options' => array(
					'post_type' => array(
						'default' => 'product',
						'options' => array(
							'product' => __( 'Latest Products', 'cmsmasters-elementor' ),
							'sale' => __( 'Sale', 'cmsmasters-elementor' ),
							'featured' => __( 'Featured', 'cmsmasters-elementor' ),
							'manual_selection' => __( 'Manual Selection', 'cmsmasters-elementor' ),
							'current_query' => __( 'Current Query', 'cmsmasters-elementor' ),
						),
					),
					'orderby' => array(
						'default' => 'date',
						'options' => array(
							'date' => __( 'Date', 'cmsmasters-elementor' ),
							'title' => __( 'Title', 'cmsmasters-elementor' ),
							'price' => __( 'Price', 'cmsmasters-elementor' ),
							'popularity' => __( 'Popularity', 'cmsmasters-elementor' ),
							'rating' => __( 'Rating', 'cmsmasters-elementor' ),
							'reviews_count' => __( 'Reviews Number', 'cmsmasters-elementor' ),
							'rand' => __( 'Random', 'cmsmasters-elementor' ),
							'menu_order' => __( 'Menu Order', 'cmsmasters-elementor' ),
						),
					),
				),
				'exclude' => array(
					'posts_per_page',
					'author_query',
					'selected_authors',
					'ignore_sticky_posts',
					'prevent_duplicates',
					'offset',
					'filter_id',
					'current_author',
					'related_fallback',
					'fallback_posts_in',
				),
			)
		);

		$this->update_control(
			'query_posts_in',
			array(
				'autocomplete' => array(
					'object' => Query_Manager::POST_OBJECT,
					'query' => array( 'post_type' => 'product' ),
					'display' => 'detailed',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register shop widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.3.3 Fixed on breakpoints.
	 */
	protected function register_section_layout_style() {
		$this->start_controls_section(
			'section_style_layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'columns_gap',
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
				'size_units' => array( 'px' ),
				'frontend_available' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-gap-column: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'columns!' => 1,
				),
			)
		);

		$this->add_responsive_control(
			'rows_gap',
			array(
				'label' => __( 'Rows Gap', 'cmsmasters-elementor' ),
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
				'size_units' => array( 'px' ),
				'frontend_available' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-gap-row: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->border_columns->add_controls();

		$this->add_responsive_control(
			'border_horizontal_width',
			array(
				'label' => __( 'Horizontal Border Width', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}} li.product::after' => 'border-bottom-width: {{SIZE}}{{UNIT}}; border-style: {{border_columns_type.VALUE}}; bottom: calc(-1 * (var(--cmsmasters-gap-row) / 2) - ({{SIZE}}{{UNIT}} / 2));',
				),
			)
		);

		$this->add_control(
			'border_row_color',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 'true',
				'selectors' => array(
					'{{WRAPPER}} li.product::after' => ' border-color: {{border_columns_color.VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->end_injection();
	}

	/**
	 * Register shop widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.11.5 Fixed issues for Elementor 3.18.0.
	 */
	protected function register_section_header() {
		$conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'pagination_show',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'show_layout',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'allow_order',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'show_result_count',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			),
		);

		$this->start_controls_section(
			'header_section_style',
			array(
				'label' => __( 'Header', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => $conditions,
			)
		);

		$this->start_controls_tabs( 'header_style' );

		$this->start_controls_tab(  // Container
			'header_style_container',
			array(
				'label' => __( 'Container', 'cmsmasters-elementor' ),
				'conditions' => $conditions,
			)
		);

		$this->add_control(
			'header_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-woo-products__header' => 'background-color: {{VALUE}};',
				),
				'conditions' => $conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'header_border',
				'selector' => '{{WRAPPER}} .cmsmasters-woo-products__header',
				'conditions' => $conditions,
			)
		);

		$this->add_responsive_control(
			'header_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-woo-products__header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'conditions' => $conditions,
			)
		);

		$this->add_responsive_control(
			'header_bdrs',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-woo-products__header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'conditions' => $conditions,
			)
		);

		$this->add_responsive_control(
			'header_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-woo-products__header' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'conditions' => $conditions,
			)
		);

		$this->end_controls_tab(); // Container

		$this->start_controls_tab( // Count
			'header_style_count',
			array(
				'label' => __( 'Count', 'cmsmasters-elementor' ),
				'condition' => array(
					'show_result_count!' => '',
				),
				'conditions' => $conditions,
			)
		);

		$this->add_control(
			'header_count_text',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-result-count' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'show_result_count!' => '',
				),
				'conditions' => $conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'header_count',
				'selector' => '{{WRAPPER}} .woocommerce-result-count',
				'condition' => array(
					'show_result_count!' => '',
				),
				'conditions' => $conditions,
			)
		);

		$this->end_controls_tab(); // Count

		$this->start_controls_tab( // Sorting
			'header_style_sorting',
			array(
				'label' => __( 'Sorting', 'cmsmasters-elementor' ),
				'condition' => array(
					'allow_order!' => '',
				),
				'conditions' => $conditions,
			)
		);

		$this->add_control(
			'header_ordering_text',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-ordering select' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'allow_order!' => '',
				),
				'conditions' => $conditions,
			)
		);

		$this->add_control(
			'header_ordering_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-ordering select' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'allow_order!' => '',
				),
				'conditions' => $conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'header_ordering',
				'selector' => '{{WRAPPER}} .woocommerce-ordering select',
				'condition' => array(
					'allow_order!' => '',
				),
				'conditions' => $conditions,
			)
		);

		$this->add_control(
			'header_ordering_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-ordering select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => array(
					'allow_order!' => '',
				),
				'conditions' => $conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'header_ordering',
				'selector' => '{{WRAPPER}} .woocommerce-ordering select',
				'condition' => array(
					'allow_order!' => '',
				),
				'conditions' => $conditions,
			)
		);

		$this->add_responsive_control(
			'header_ordering',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-ordering select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'conditions' => $conditions,
			)
		);

		$this->end_controls_tab(); // Sorting

		$layout_condition = array(
			'pagination_show' => 'yes',
			'show_layout' => 'yes',
		);

		$this->start_controls_tab(  // Layout
			'header_style_layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'condition' => $layout_condition,
			)
		);

		$this->add_responsive_control(
			'header_layout_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 100 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--header-layout-gap: {{SIZE}}{{UNIT}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pagination_show',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'show_layout',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'allow_order',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'show_result_count',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'layout_full_position',
									'operator' => '===',
									'value' => 'with_count',
								),
								array(
									'name' => 'layout_full_position',
									'operator' => '===',
									'value' => 'with_sort',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'header_layout_label_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Label', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array_merge( $layout_condition, array( 'layout_label!' => '' ) ),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'header_layout_label_typography',
				'condition' => array_merge( $layout_condition, array( 'layout_label!' => '' ) ),
			)
		);

		$this->add_control(
			'header_layout_label_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--header-layout-label-color: {{VALUE}};',
				),
				'condition' => array_merge( $layout_condition, array( 'layout_label!' => '' ) ),
			)
		);

		$this->add_responsive_control(
			'header_layout_label_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--header-layout-label-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => array_merge( $layout_condition, array( 'layout_label!' => '' ) ),
			)
		);

		$this->add_control(
			'header_layout_items_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pagination_show',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'show_layout',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'layout_label',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'allow_order',
											'operator' => '===',
											'value' => 'yes',
										),
										array(
											'name' => 'show_result_count',
											'operator' => '===',
											'value' => 'yes',
										),
										array(
											'name' => 'layout_full_position',
											'operator' => '!==',
											'value' => 'center',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'header_layout_items_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Items', 'cmsmasters-elementor' ),
				'condition' => $layout_condition,
			)
		);

		$this->add_control(
			'header_layout_items_normal_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--header-layout-items-color: {{VALUE}};',
				),
				'condition' => $layout_condition,
			)
		);

		$this->add_control(
			'header_layout_items_normal_border-color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--header-layout-items-normal-border-color: {{VALUE}};',
				),
				'condition' => array_merge( $layout_condition, array( 'header_layout_items_bd_border!' => 'none' ) ),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array( 'name' => 'header_layout_items_normal' )
		);

		$this->add_control(
			'header_layout_items_hover_active_color',
			array(
				'label' => __( 'Hover/Active Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--header-layout-items-hover-color: {{VALUE}};',
				),
				'condition' => $layout_condition,
			)
		);

		$this->add_control(
			'header_layout_items_hover_border-color',
			array(
				'label' => __( 'Hover/Active Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--header-layout-items-hover-border-color: {{VALUE}};',
				),
				'condition' => array_merge( $layout_condition, array( 'header_layout_items_bd_border!' => 'none' ) ),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array(
				'name' => 'header_layout_items_hover',
				'label' => esc_html_x( 'Hover/Active Box Shadow', 'Box Shadow Control', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'header_layout_items_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--header-layout-items-size: {{SIZE}}{{UNIT}}',
				),
				'condition' => $layout_condition,
			)
		);

		$this->add_responsive_control(
			'header_layout_items_gap_between',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--header-layout-items-gap-between: {{SIZE}}{{UNIT}}',
				),
				'condition' => $layout_condition,
			)
		);

		$this->add_control(
			'header_layout_items_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--header-layout-items-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'header_layout_items_bd',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array( 'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ) ),
				),
			)
		);

		$this->end_controls_tab(); // Layout

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get products shortcode object.
	 *
	 * @since 1.0.0
	 * @since 1.7.2 Fixed sorting in `Products Archive` widget.
	 *
	 * @return Products_Renderer|Current_Query_Renderer
	 */
	protected function get_shortcode_object() {
		if ( ! $this->shortcode_object ) {
			$settings = $this->get_settings();

			if ( 'current_query' === $settings[ Products_Renderer::QUERY_CONTROL_NAME . '_post_type' ] ) {
				$this->shortcode_object = new Current_Query_Renderer( $settings, 'current_query' );
			} else {
				$this->shortcode_object = new Products_Renderer( $settings, 'products' );
			}
		}

		return $this->shortcode_object;
	}

	/**
	 * Set the WordPress query object.
	 *
	 * @param \WP_Query $wp_query
	 */
	public function set_wp_query( \WP_Query $wp_query ) {
		$this->pagination->set_wp_query( $wp_query );

		remove_action( 'pre_get_posts', array( $this, 'set_wp_query' ) );
	}

	public function get_order_by_name() {
		return "cmsmasters-orderby-{$this->get_id()}";
	}

	/**
	 * Generates the final HTML on the frontend.
	 *
	 * @since 1.0.0
	 * @since 1.11.3 Fixed ajax rendering.
	 *
	 * @param array $ajax_vars
	 */
	public function render_ajax( $ajax_vars = array() ) {
		$query_vars = isset( $ajax_vars['query_vars'] ) ?
			BlogModule::get_allowed_query_vars( $ajax_vars['query_vars'] ) :
			array();

		$attributes = isset( $ajax_vars['attributes'] ) ? $ajax_vars['attributes'] : array();

		foreach ( $attributes as $attribute_key => $attribute_value ) {
			if ( false !== strpos( $attribute_key, 'cmsmasters-' ) ) {
				continue;
			}

			$_GET[ $attribute_key ] = $attribute_value;
		}

		// Filter Query
		if ( $query_vars ) {
			$callback_products_query = function ( $wc_query_vars ) use ( $query_vars ) {
				return array_merge( $wc_query_vars, $query_vars );
			};

			add_filter( 'woocommerce_shortcode_products_query', $callback_products_query );
		}

		// Filter attributes
		if ( $attributes ) {
			$callback_products_attributes = function ( $wc_attributes ) use ( $attributes ) {
				return array_merge( $wc_attributes, $attributes );
			};

			add_filter( 'cmsmasters_woocommerce_shortcode_products_attributes', $callback_products_attributes );
		}

		$this->render_products();

		if ( $attributes ) {
			remove_filter( 'cmsmasters_woocommerce_shortcode_products_attributes', $callback_products_attributes );
		}

		if ( $query_vars ) {
			remove_filter( 'woocommerce_shortcode_products_query', $callback_products_query );
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function render() {
		if ( WC()->session && function_exists( 'wc_print_notices' ) ) {
			wc_print_notices();
		}

		// For Products_Renderer.
		if ( ! isset( $GLOBALS['post'] ) ) {
			$GLOBALS['post'] = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}

		parent::render();
	}

	/**
	 * @since 1.10.1
	 */
	public function render_ordering_wrap_start() {
		echo '<div class="cmsmasters-woo-products__header-ordering-wrap">';
	}

	/**
	 * @since 1.10.1
	 */
	public function render_ordering_wrap_end() {
		echo '</div>';
	}

	/**
	 * @since 1.10.1
	 */
	public function render_header_layout() {
		$settings = $this->get_settings_for_display();

		$layout_label = ( isset( $settings['layout_label'] ) ? $settings['layout_label'] : '' );

		echo '<div class="cmsmasters-woo-products__header-layout">';

		if ( $layout_label ) {
			echo '<span class="cmsmasters-woo-products__header-layout-label">' .
				esc_html( $layout_label ) .
			'</span>';
		}

			echo '<span class="cmsmasters-woo-products__header-layout-columns">' .
				'<span class="cmsmasters-woo-products__header-layout-column cmsicon-column-1" role="button" aria-label="One column" tabindex="0" data-layout="1"></span>' .
				'<span class="cmsmasters-woo-products__header-layout-column cmsicon-column-2" role="button" aria-label="Two column" tabindex="0" data-layout="2"></span>' .
				'<span class="cmsmasters-woo-products__header-layout-column cmsicon-column-3" role="button" aria-label="Three column" tabindex="0" data-layout="3"></span>' .
				'<span class="cmsmasters-woo-products__header-layout-column cmsicon-column-4" role="button" aria-label="Four column" tabindex="0" data-layout="4"></span>' .
				'<span class="cmsmasters-woo-products__header-layout-column cmsicon-column-5" role="button" aria-label="Four column" tabindex="0" data-layout="5"></span>' .
				'<span class="cmsmasters-woo-products__header-layout-column cmsicon-column-6" role="button" aria-label="Four column" tabindex="0" data-layout="6"></span>' .
			'</span>' .
		'</div>';
	}

	private static $header_layout_rendered = false;

	/**
	 * @since 1.0.0
	 * @since 1.10.1 Added new "Layout" functionality for it in the widget header.
	 */
	protected function render_products() {
		$settings = $this->get_settings_for_display();
		$is_custom_template = $this->is_custom_template();
		$is_header = $settings['pagination_show'] && ( $settings['allow_order'] || $settings['show_result_count'] );
		$paged = $this->pagination->get_paged();
		$paged_old = Utils::get_if_isset( $_GET, 'product-page' );
		$orderby = Utils::get_if_isset( $_GET, $this->get_order_by_name() );
		$orderby_old = Utils::get_if_isset( $_GET, 'orderby' );

		if ( $paged ) {
			$_GET['product-page'] = $paged;
		}

		if ( $orderby ) {
			$_GET['orderby'] = $orderby;
		}

		add_action( 'pre_get_posts', array( $this, 'set_wp_query' ) );

		$shortcode = $this->get_shortcode_object();

		// Pagination args add
		if ( $settings['pagination_show'] ) {
			add_filter( 'woocommerce_pagination_args', array( $this->pagination, 'get_pagination_args' ) );
		}

		if ( $is_header ) {
			$header_templates = null;

			$callback_header = function ( $template_name ) use ( &$header_templates, $settings ) {
				$show_layout = ( isset( $settings['show_layout'] ) ? $settings['show_layout'] : '' );
				$show_result_count = ( isset( $settings['show_result_count'] ) ? $settings['show_result_count'] : '' );
				$allow_order = ( isset( $settings['allow_order'] ) ? $settings['allow_order'] : '' );
				$layout_full_position = ( isset( $settings['layout_full_position'] ) ? $settings['layout_full_position'] : '' );

				if ( null === $header_templates ) {
					if ( $allow_order ) {
						$header_templates[] = 'loop/orderby.php';
					}

					if ( $show_result_count ) {
						$header_templates[] = 'loop/result-count.php';
					}

					if ( in_array( $template_name, $header_templates, true ) ) {
						echo '<div class="cmsmasters-woo-products__header">';

						if ( ! self::$header_layout_rendered ) {
							if ( $show_layout ) {
								if ( $show_result_count && $allow_order ) {
									if ( 'with_count' === $layout_full_position ) {
										$this->render_ordering_wrap_start();

										add_action( 'woocommerce_before_shop_loop', array( $this, 'render_header_layout' ), 21 );

										add_action( 'woocommerce_before_shop_loop', array( $this, 'render_ordering_wrap_end' ), 22 );
									}

									if ( 'center' === $layout_full_position || 'left' === $layout_full_position ) {
										add_action( 'woocommerce_before_shop_loop', array( $this, 'render_header_layout' ), 25 );
									}

									if ( 'with_sort' === $layout_full_position ) {
										add_action( 'woocommerce_before_shop_loop', array( $this, 'render_ordering_wrap_start' ), 28 );

										add_action( 'woocommerce_before_shop_loop', array( $this, 'render_header_layout' ), 29 );

										add_action( 'woocommerce_before_shop_loop', array( $this, 'render_ordering_wrap_end' ), 31 );
									}
								}

								if ( ! $show_result_count || ! $allow_order ) {
									$this->render_header_layout();
								}
							}

							self::$header_layout_rendered = true;
						}
					}
				}

				if ( is_array( $header_templates ) && empty( $header_templates ) && ! $show_layout ) {
					return;
				}

				if ( in_array( $template_name, $header_templates, true ) ) {
					$header_templates = array_diff( $header_templates, array( $template_name ) );
				}

				add_action( 'woocommerce_after_template_part', function ( $template_name_after ) use ( $template_name, $header_templates ) {
					if ( empty( $header_templates ) && $template_name_after === $template_name ) {
						echo '</div>';
					}

					remove_action( 'woocommerce_after_template_part', Utils::get_current_function_of_hook() );
				} );

				// Remove action
				if ( is_array( $header_templates ) && empty( $header_templates ) ) {
					remove_action( 'woocommerce_before_template_part', Utils::get_current_function_of_hook() );
				}
			};

			add_action( 'woocommerce_before_template_part', $callback_header );
		}

		$show_layout = ( isset( $settings['show_layout'] ) ? $settings['show_layout'] : '' );
		$show_result_count = ( isset( $settings['show_result_count'] ) ? $settings['show_result_count'] : '' );
		$allow_order = ( isset( $settings['allow_order'] ) ? $settings['allow_order'] : '' );

		if ( $show_layout && ! $show_result_count && ! $allow_order ) {
			echo '<div class="cmsmasters-woo-products__header">';

			$this->render_header_layout();

			echo '</div>';
		}

		if ( $is_custom_template ) {
			WooModule::set_template_id_content_product( $this->get_template_id() );
		}

		// Render
		$content = $shortcode->get_content();

		if ( $is_custom_template ) {
			WooModule::remove_template_id_content_product();
		}

		if ( $is_header ) {
			remove_action( 'woocommerce_before_template_part', $callback_header );
		}

		// Pagination args remove
		if ( $settings['pagination_show'] ) {
			remove_filter( 'woocommerce_pagination_args', array( $this->pagination, 'get_pagination_args' ) );
		}

		if ( $paged_old ) {
			$_GET['product-page'] = $paged_old;
		} elseif ( isset( $_GET['product-page'] ) ) {
			unset( $_GET['product-page'] );
		}

		if ( $orderby_old ) {
			$_GET['orderby'] = $orderby_old;
		} elseif ( isset( $_GET['orderby'] ) ) {
			unset( $_GET['orderby'] );
		}

		echo apply_filters( 'cmsmasters_elementor/modules/widgets/products/content', $content );
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {}

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
				'field' => 'pagination_infinite_scroll_text',
				'type' => esc_html__( 'Infinite Scroll Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_text_next',
				'type' => esc_html__( 'Next Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_text_prev',
				'type' => esc_html__( 'Previous Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_load_more_text_normal',
				'type' => esc_html__( 'Normal Button Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_load_more_text_loading',
				'type' => esc_html__( 'Loading Button Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
