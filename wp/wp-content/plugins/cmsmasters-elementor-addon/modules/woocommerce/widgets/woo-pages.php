<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Woocommerce\Module as CmsmastersWoo;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Widget;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Woo_Pages extends Base_Widget {

	use Woo_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.8.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'WooCommerce Pages', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.8.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-woocommerce-pages';
	}

	/**
	 * On export product meta.
	 *
	 * When exporting data, check if the product is not using page template and
	 * exclude it from the exported Elementor data.
	 *
	 * @since 1.8.0
	 *
	 * @param array $element_data Element data.
	 *
	 * @return array Element data to be exported.
	 */
	public function on_export( $element ) {
		unset( $element['settings']['product_id'] );

		return $element;
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.8.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'woocommerce',
			'shop',
			'store',
			'cart',
			'checkout',
			'account',
			'order tracking',
			'shortcode',
			'product',
			'page',
		);
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the widget categories.
	 *
	 * @since 1.8.0
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( Base_Document::WOO_WIDGETS_CATEGORY );
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
			'widget-cmsmasters-woocommerce',
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
	 * @since 1.8.0
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_product',
			array( 'label' => esc_html__( 'Element', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'element',
			array(
				'label' => esc_html__( 'Page', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => '— ' . esc_html__( 'Select', 'cmsmasters-elementor' ) . ' —',
					'woocommerce_cart' => esc_html__( 'Cart Page', 'cmsmasters-elementor' ),
					'product_page' => esc_html__( 'Single Product Page', 'cmsmasters-elementor' ),
					'woocommerce_checkout' => esc_html__( 'Checkout Page', 'cmsmasters-elementor' ),
					'woocommerce_order_tracking' => esc_html__( 'Order Tracking Form', 'cmsmasters-elementor' ),
					'woocommerce_my_account' => esc_html__( 'My Account', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->add_control(
			'product_id',
			array(
				'label' => esc_html__( 'Product', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::QUERY,
				'options' => array(),
				'label_block' => true,
				'autocomplete' => array(
					'object' => Query_Manager::POST_OBJECT,
					'query' => array(
						'post_type' => array( 'product' ),
					),
				),
				'condition' => array( 'element' => array( 'product_page' ) ),
			)
		);

		$this->end_controls_section();
	}

	private function get_shortcode() {
		$settings = $this->get_settings();

		switch ( $settings['element'] ) {
			case '':
				return '';

				break;
			case 'product_page':
				if ( ! empty( $settings['product_id'] ) ) {
					$product_data = get_post( $settings['product_id'] );
					$product = ! empty( $product_data ) && in_array( $product_data->post_type, array( 'product', 'product_variation' ) ) ? wc_setup_product_data( $product_data ) : false;
				}

				if ( empty( $product ) && current_user_can( 'manage_options' ) ) {
					return esc_html__( 'Please set a valid product', 'cmsmasters-elementor' );
				}

				$this->add_render_attribute( 'shortcode', 'id', $settings['product_id'] );

				break;
			case 'woocommerce_cart':
			case 'woocommerce_checkout':
			case 'woocommerce_order_tracking':
				break;
		}

		$shortcode = sprintf(
			'[%s %s]',
			esc_html( $settings['element'] ),
			$this->get_render_attribute_string( 'shortcode' )
		);

		return $shortcode;
	}

	protected function render() {
		$shortcode = $this->get_shortcode();

		if ( empty( $shortcode ) ) {
			return;
		}

		CmsmastersWoo::add_products_post_class_filter();

		$html = do_shortcode( $shortcode );

		if ( 'woocommerce_checkout' === $this->get_settings( 'element' ) && '<div class="woocommerce"></div>' === $html ) {
			$html = '<div class="woocommerce">' . esc_html__( 'Your cart is currently empty.', 'cmsmasters-elementor' ) . '</div>';
		}

		Utils::print_unescaped_internal_string( $html );

		CmsmastersWoo::remove_products_post_class_filter();
	}

	public function render_plain_content() {
		Utils::print_unescaped_internal_string( $this->get_shortcode() );
	}
}
