<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Blog\Module as BlogModule;
use CmsmastersElementor\Modules\Woocommerce\Classes\Base_Products_Renderer;
use CmsmastersElementor\Modules\Woocommerce\Classes\Current_Query_Renderer;
use CmsmastersElementor\Modules\Woocommerce\Classes\Products_Renderer;
use CmsmastersElementor\Modules\Woocommerce\Module as WooModule;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Widget;
use CmsmastersElementor\Modules\Woocommerce\Widgets\Base_Widgets\Base_Products;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Modules\Slider\Classes\Slider;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Products widget.
 *
 * @since 1.11.0
 */
class Products_Slider extends Base_Products {

	use Woo_Widget;


	/**
	 * Products shortcode instance.
	 *
	 * @since 1.11.0
	 *
	 * @var Base_Products_Renderer
	 */
	protected $shortcode_object;

	/**
	 * Slider instance.
	 *
	 * @since 1.11.0
	 *
	 * @var Slider
	 */
	protected $slider;

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the widget requires.
	 *
	 * @since 1.11.0
	 * @since 1.16.0 Added dependency of connecting swiper script after elementor 3.27 version.
	 */
	public function get_script_depends() {
		return array_merge( array(
			'swiper',
			'imagesloaded',
		), parent::get_script_depends() );
	}

	/**
	 * @since 1.11.0
	 */
	public function get_title() {
		return __( 'Products Slider', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.11.0
	 */
	public function get_icon() {
		return 'cmsicon-product-slider';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.11.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'products',
			'carousel',
			'slider',
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.15.3 Added dependency of connecting swiper styles for widgets with swiper slider after elementor 3.26 version.
	 * @since 1.16.0 Fixed style dependencies.
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array_merge( array(
			'e-swiper',
		), parent::get_style_depends() );
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
	 * @since 1.11.0
	 * @access protected
	 */
	protected function get_html_wrapper_class() {
		return parent::get_html_wrapper_class() . ' elementor-widget-cmsmasters-woo-products-slider';
	}

	/**
	 * Register shop widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.11.0
	 * @since 1.11.3 Added `Width` control for slider arrows container.
	 */
	protected function register_controls() {
		$this->register_main_content_controls();

		$this->register_query_controls();

		$this->slider->register_section_content();

		$this->slider->register_section_style_style_layout();

		parent::register_sections_style_product();

		$this->slider->register_section_style_arrows();

		$this->start_injection(
			array(
				'of' => $this->slider->get_control_prefix( 'slider_arrows_align_position' ),
				'at' => 'before',
			)
		);

		$this->add_responsive_control(
			'slider_arrows_container_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'unit' => '%' ),
				'size_units' => array(
					'px',
					'%',
					'vw',
					'vh',
				),
				'range' => array(
					'px' => array(
						'max' => 1920,
						'min' => 320,
					),
					'%' => array(
						'max' => 100,
						'min' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-slider-arrows-container-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( $this->slider->get_control_prefix( 'slider_arrows_align_position' ) => '' ),
			)
		);

		$this->end_injection();

		$this->slider->register_section_style_bullets();

		$this->slider->register_section_style_fraction();

		$this->slider->register_section_style_progressbar();

		$this->slider->register_section_style_scrollbar();
	}

	/**
	 * Shop Widget constructor.
	 *
	 * Initializing the widget shop class.
	 *
	 * @since 1.11.0
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		$this->slider = new Slider( $this );

		parent::__construct( $data, $args );
	}

	protected function register_main_content_controls() {
		parent::register_main_content_controls();

		$this->start_injection( array(
			'of' => WooModule::CONTROL_TEMPLATE_NAME,
		) );

		$this->add_control(
			'products_pre_page',
			array(
				'label' => __( 'Products', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
				'min' => 1,
				'render_type' => 'template',
				'condition' => array( Products_Renderer::QUERY_CONTROL_NAME . '_post_type!' => 'current_query' ),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label' => __( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 1,
			)
		);

		$this->add_responsive_control(
			'pagination_show',
			array(
				'label' => __( 'Pagination', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'no',
			)
		);

		$this->add_responsive_control(
			'allow_order',
			array(
				'label' => __( 'Sorting', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'no',
			)
		);

		$this->add_responsive_control(
			'show_result_count',
			array(
				'label' => __( 'Show Result Count', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'no',
			)
		);

		$this->slider->register_controls_content_per_view();

		$this->end_injection();
	}

	/**
	 * Register shop widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.11.0
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
	 * Get products shortcode object.
	 *
	 * @since 1.11.0
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
		remove_action( 'pre_get_posts', array( $this, 'set_wp_query' ) );
	}

	public function get_order_by_name() {
		return "cmsmasters-orderby-{$this->get_id()}";
	}

	/**
	 * @since 1.11.0
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
	 * @since 1.11.0
	 */
	protected function render_products() {
		$settings = $this->get_settings_for_display();

		$is_custom_template = $this->is_custom_template();
		$paged_old = CmsmastersUtils::get_if_isset( $_GET, 'product-page' );
		$orderby = CmsmastersUtils::get_if_isset( $_GET, $this->get_order_by_name() );
		$orderby_old = CmsmastersUtils::get_if_isset( $_GET, 'orderby' );

		if ( $orderby ) {
			$_GET['orderby'] = $orderby;
		}

		add_action( 'pre_get_posts', array( $this, 'set_wp_query' ) );

		$shortcode = $this->get_shortcode_object();

		if ( $is_custom_template ) {
			WooModule::set_template_id_content_product( $this->get_template_id() );
		}

		// Render
		$content = $shortcode->get_content();

		if ( $is_custom_template ) {
			WooModule::remove_template_id_content_product();
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

		$this->slider->render_root( function () use ( $content ) {
			Utils::print_unescaped_internal_string( $content );

			$this->slider->render_interface();
		} );
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.11.0
	 */
	public function render_plain_content() {}
}
