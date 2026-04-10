<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Modules\Slider\Classes\Slider;
use CmsmastersElementor\Modules\Woocommerce\Module as WooModule;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;
use CmsmastersElementor\Modules\Woocommerce\Widgets\Base_Widgets\Base_Products;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Product_Related extends Base_Products {

	use Woo_Singular_Widget;

	protected $slider;

	public function get_title() {
		return __( 'Related Products', 'cmsmasters-elementor' );
	}

	public function get_icon() {
		return 'cmsicon-related-products';
	}

	public function get_unique_keywords() {
		return array(
			'related',
			'upsell',
			'cross-sells',
			'similar',
		);
	}

	/**
	 * Get scripts dependencies.
	 *
	 * Retrieve the list of scripts dependencies the widget requires.
	 *
	 * @since 1.16.0 Added dependency of connecting swiper script after elementor 3.27 version.
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'swiper' );
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
	 *
	 * Initializing the Addon `related products` widget class.
	 *
	 * @since 1.0.0
	 *
	 * @throws \Exception If arguments are missing when initializing a
	 * full widget instance.
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		$this->slider = new Slider( $this );
	}

	/**
	 * @since 1.0.0
	 */
	protected function register_controls() {
		parent::register_controls();

		$this->register_section_content();

		$this->slider->register_section_content();
		$this->slider->register_sections_style();

		$this->update_controls_slider();
	}

	/**
	 * @since 1.0.0
	 */
	protected function register_sections_style_product() {
		$this->register_section_style_header();
		parent::register_sections_style_product();
	}

	/**
	 * Update related controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function update_controls_slider() {
		$this->update_control(
			$this->slider->get_control_prefix( 'slider_type' ),
			array(
				'separator' => 'before',
			)
		);

		$this->update_control(
			$this->slider->get_control_prefix( 'slider_infinite' ),
			array(
				'default' => '',
			)
		);

		$this->update_control(
			$this->slider->get_control_prefix( 'slider_per_view' ),
			array(
				'default' => '4',
			)
		);
	}

	/**
	 * Register related controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_section_content() {
		$products_per_page = array(
			'' => esc_html__( 'Default', 'cmsmasters-elementor' ),
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
			'7' => '7',
			'8' => '8',
			'9' => '9',
			'10' => '10',
		);

		$this->start_injection(
			array(
				'of' => WooModule::CONTROL_TEMPLATE_NAME,
			)
		);

		$this->add_control(
			'products_query',
			array(
				'label' => __( 'Query', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'related',
				'options' => array(
					'related' => esc_html__( 'Related', 'cmsmasters-elementor' ),
					'upsells' => esc_html__( 'Upsells', 'cmsmasters-elementor' ),
					'cross-sells' => esc_html__( 'Cross-sells', 'cmsmasters-elementor' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label' => __( 'Products Per Page', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $products_per_page,
			)
		);

		$this->add_control(
			'title_switcher',
			array(
				'label' => __( 'Show Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'related_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => static::get_default_related_title(),
				'condition' => array(
					'products_query' => 'related',
					'title_switcher!' => '',
				),
			)
		);

		$this->add_control(
			'upsells_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => static::get_default_upsells_title(),
				'condition' => array(
					'products_query' => 'upsells',
					'title_switcher!' => '',
				),
			)
		);

		$this->add_control(
			'cross_sells_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => static::get_default_cross_sells_title(),
				'condition' => array(
					'products_query' => 'cross-sells',
					'title_switcher!' => '',
				),
			)
		);

		$this->slider->register_controls_content_per_view();

		$this->add_control(
			'orderby',
			array(
				'label' => __( 'Order By', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'date' => __( 'Date', 'cmsmasters-elementor' ),
					'title' => __( 'Title', 'cmsmasters-elementor' ),
					'price' => __( 'Price', 'cmsmasters-elementor' ),
					'popularity' => __( 'Popularity', 'cmsmasters-elementor' ),
					'rating' => __( 'Rating', 'cmsmasters-elementor' ),
					'rand' => __( 'Random', 'cmsmasters-elementor' ),
					'menu_order' => __( 'Menu Order', 'cmsmasters-elementor' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'order',
			array(
				'label' => __( 'Order', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'asc' => __( 'ASC', 'cmsmasters-elementor' ),
					'desc' => __( 'DESC', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->end_injection();
	}

	/**
	 * Register related controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_section_style_header() {
		$this->start_controls_section(
			'section_header_style',
			array(
				'label' => __( 'Header', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'header_title',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-related__title',
			)
		);

		$this->add_control(
			'header_title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-related__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'header_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-related__title',
			)
		);

		$this->add_responsive_control(
			'header_align',
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
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-related__title' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'header_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-related__header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'header_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-related__header' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'header_border',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-related__header',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get default related title.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_default_related_title() {
		return apply_filters( 'woocommerce_product_related_products_heading', __( 'Related Products', 'cmsmasters-elementor' ) );
	}

	/**
	 * Get default upsells title.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_default_upsells_title() {
		return apply_filters( 'woocommerce_product_upsells_products_heading', __( 'You may also like…', 'cmsmasters-elementor' ) );
	}

	/**
	 * Get default s_sells title.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_default_cross_sells_title() {
		return apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'You may be interested in…', 'cmsmasters-elementor' ) );
	}

	/**
	 * Get current filter name by type.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_loop_heading_action_name() {
		switch ( $this->get_settings_for_display( 'products_query' ) ) {
			case 'related':
				return 'woocommerce_product_related_products_heading';
			case 'upsells':
				return 'woocommerce_product_upsells_products_heading';
			case 'cross-sells':
				return 'woocommerce_product_cross_sells_products_heading';
		}
	}

	/**
	 * Get products title.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_related_title() {
		$settings = $this->get_settings_for_display();
		$title = '';

		if ( $settings['title_switcher'] ) {
			switch ( $settings['products_query'] ) {
				case 'related':
					$title = ! empty( $settings['related_title'] ) ? $settings['related_title'] : $this->get_default_related_title();

					break;
				case 'upsells':
					$title = ! empty( $settings['upsells_title'] ) ? $settings['upsells_title'] : $this->get_default_upsells_title();

					break;
				case 'cross-sells':
					$title = ! empty( $settings['cross_sells_title'] ) ? $settings['cross_sells_title'] : $this->get_default_cross_sells_title();

					break;
			}
		}

		return $title;
	}

	/**
	 * Before rendering products.
	 *
	 * @since 1.0.0
	 */
	protected function render_products_before() {
		echo '<div class="woocommerce elementor-widget-cmsmasters-woo-products-inner">';

		if ( ! $this->is_custom_template() ) {
			$settings = $this->get_settings_for_display();

			if ( ! $settings['show_onsale_flash'] ) {
				add_filter( 'woocommerce_sale_flash', '__return_empty_string' );
			}

			if ( ! $settings['heading_button_enable'] ) {
				add_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string' );
			}
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function render_products() {
		$settings = $this->get_settings_for_display();
		$template_id = $this->get_template_id();
		$is_custom_template = 'custom' === $settings['template_layout'] && $template_id;
		$order = CmsmastersUtils::get_if_not_empty( $settings, 'order', 'desc' );
		$orderby = CmsmastersUtils::get_if_not_empty( $settings, 'orderby', 'rand' );
		$posts_per_page = CmsmastersUtils::get_if_not_empty( $settings, 'posts_per_page', 8 );
		$title = $this->get_related_title();

		if ( $is_custom_template ) {
			WooModule::set_template_id_content_product( $template_id );
		}

		if ( 'related' === $settings['products_query'] && 'upsells' === $settings['products_query'] ) {
			$columns = 4;
		} else {
			$columns = 2;
		}

		$args = array(
			'posts_per_page' => $posts_per_page,
			'columns' => $columns,
			'orderby' => $orderby,
			'order' => $order,
		);

		$heading_action_name = $this->get_loop_heading_action_name();

		add_filter( $heading_action_name, '__return_empty_string' );

		ob_start();

		switch ( $settings['products_query'] ) {
			case 'related':
				woocommerce_related_products( $args );

				break;
			case 'upsells':
				woocommerce_upsell_display( ...array_values( $args ) );

				break;
			case 'cross-sells':
				woocommerce_cross_sell_display( ...array_values( $args ) );

				break;
		}

		$related_products_html = ob_get_clean();

		remove_filter( $heading_action_name, '__return_empty_string' );

		if ( $is_custom_template ) {
			WooModule::remove_template_id_content_product();
		}

		if ( ! $related_products_html ) {
			return;
		}

		if ( $title ) {
			echo '<div class="elementor-widget-cmsmasters-woo-product-related__header">' .
				'<h3 class="elementor-widget-cmsmasters-woo-product-related__title">' . esc_html( $title ) . '</h3>' .
			'</div>';
		}

		$this->slider->render_root( function () use ( $related_products_html ) {
			Utils::print_unescaped_internal_string( $related_products_html );

			$this->slider->render_interface();
		} );
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
				'field' => 'related_title',
				'type' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'upsells_title',
				'type' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'cross_sells_title',
				'type' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
