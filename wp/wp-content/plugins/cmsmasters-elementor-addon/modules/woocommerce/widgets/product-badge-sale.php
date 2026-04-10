<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;
use CmsmastersElementor\Modules\Woocommerce\Widgets\Product_Badge_Base;

use Elementor\Controls_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Badge_Sale extends Product_Badge_Base {

	use Woo_Singular_Widget;

	public function get_name() {
		return 'cmsmasters-woo-badge-sale';
	}

	public function get_title() {
		return __( 'Product Badge Sale', 'cmsmasters-elementor' );
	}

	public function get_icon() {
		return 'cmsicon-product-badge-sale';
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
	 * Register widget content section.
	 *
	 * Adds widget content settings controls.
	 *
	 * @since 1.0.0
	 * @since 1.10.1 Fixed sale discount update at the time of change product variations.
	 * Added `Discount Rounding` control for sale discount.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_product_Badge',
			array(
				'label' => __( 'Settings', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'type_sale',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'text' => __( 'Text', 'cmsmasters-elementor' ),
					'number' => __( 'Percent', 'cmsmasters-elementor' ),
					'both' => __( 'Both', 'cmsmasters-elementor' ),
				),
				'default' => 'text',
				'label_block' => false,
			)
		);

		$this->add_control(
			'sale_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Sale!', 'cmsmasters-elementor' ),
				'dynamic' => array(
					'active' => true,
				),
				'condition' => array(
					'type_sale!' => 'number',
				),
			)
		);

		$this->add_control(
			'discount_rounding',
			array(
				'label' => __( 'Discount Rounding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'cmsmasters-discoun-rounding-',
			)
		);

		$this->end_controls_section();

		parent::register_controls();
	}

	public function get_badge( $product ) {
		$settings = $this->get_settings_for_display();

		$availability = $product->get_availability();

		if ( $product->is_on_sale() && 'out-of-stock' !== $availability['class'] ) {
			$this->add_render_attribute( 'cmsmasters_badge-wrapper', 'class', 'elementor-widget-cmsmasters-woo-badge__wrapper' );
			$this->add_render_attribute( 'cmsmasters_badge-wrapper', 'class', 'elementor-widget-cmsmasters-woo-badge__sale' );
			$this->add_render_attribute( 'cmsmasters_badge-wrapper', 'class', 'cmsmasters-woo-badge' );

			if ( 'default' === $settings['type_style'] ) {
				if ( ! empty( $settings['cmsmasters_badge_position'] ) ) {
					$this->add_render_attribute( 'cmsmasters_badge-wrapper', 'class', 'cmsmasters-woo-badge-' . $settings['cmsmasters_badge_position'] );
				}

				if ( ! empty( $settings['cmsmasters_badge_type'] ) ) {
					$this->add_render_attribute( 'cmsmasters_badge-wrapper', 'class', 'cmsmasters-woo-badge-type-' . $settings['cmsmasters_badge_type'] );
				}
			}

			echo '<div ' . $this->get_render_attribute_string( 'cmsmasters_badge-wrapper' ) . '>
				<div class="elementor-widget-cmsmasters-woo-badge__inner cmsmasters-woo-badge-inner">';
					$this->get_sale( $product );
				echo '</div>
			</div>';
		}
	}

	protected function get_sale( $product ) {
		$settings = $this->get_settings_for_display();
		$sale_percentage = $this->get_sale_percentage( $product );
		$sale = ( '' !== $settings['sale_text'] ) ? $settings['sale_text'] : __( 'Sale!', 'cmsmasters-elementor' );

		if ( 'number' === $settings['type_sale'] ) {
			$sale = $sale_percentage;
		} elseif ( 'text' === $settings['type_sale'] ) {
			$sale = $sale;
		} else {
			$sale = $sale . ' ' . $sale_percentage;
		}

		echo '<span class="cmsmasters-onsale cmsmasters-woo-badge-inner-text">' . wp_kses_post( $sale ) . '</span>';
	}

	protected function get_sale_percentage( $product ) {
		$settings = $this->get_settings_for_display();

		if ( $product->is_type( 'variable' ) ) {
			$price = (int) $product->get_variation_price();
			$regular_price = (int) $product->get_variation_regular_price();
		} else {
			$price = (int) $product->get_price();
			$regular_price = (int) $product->get_regular_price();
		}

		if ( empty( $regular_price ) || 0 === $regular_price ) {
			return;
		}

		$discount_rounding = ( isset( $settings['discount_rounding'] ) ? $settings['discount_rounding'] : '' );

		$count = ( $discount_rounding ? 0 : 1 );

		$discount = round( ( $regular_price - $price ) / $regular_price * 100, $count );

		$discount = '-' . (string) $discount . '%';

		return $discount;
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
				'field' => 'sale_text',
				'type' => esc_html__( 'Sale Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
