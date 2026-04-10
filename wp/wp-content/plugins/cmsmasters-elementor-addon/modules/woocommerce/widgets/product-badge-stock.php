<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;
use CmsmastersElementor\Modules\Woocommerce\Widgets\Product_Badge_Base;

use Elementor\Controls_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Product_Badge_Stock extends Product_Badge_Base {

	use Woo_Singular_Widget;

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return 'cmsmasters-woo-badge-stock';
	}

	public function get_title() {
		return __( 'Product Badge Stock', 'cmsmasters-elementor' );
	}

	public function get_icon() {
		return 'cmsicon-product-badge-stock';
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
	 * @since 1.3.8 Fixed hide controls for stock text.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_product_Badge',
			array(
				'label' => __( 'Settings', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'in_stock_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'In Stock', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'show_in_stock',
			array(
				'label' => __( 'Show In Stock', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
			)
		);

		if ( 'low_amount' === get_option( 'woocommerce_stock_format' ) ) {
			$low_stock_text = __( 'Low Amount Text', 'cmsmasters-elementor' );
			/* translators: Addon %s: stock amount */
			$low_stock_placeholder = __( 'Only %s left in stock', 'cmsmasters-elementor' );
			$description_low = __( 'You can use %s instead of products` stock amount.', 'cmsmasters-elementor' );

			$this->add_control(
				'low_stock_text',
				array(
					'label' => $low_stock_text,
					'type' => Controls_Manager::TEXT,
					'placeholder' => $low_stock_placeholder,
					'description' => $description_low,
					'dynamic' => array(
						'active' => true,
					),
					'condition' => array(
						'show_in_stock' => 'yes',
					),
				)
			);
		} else {

			$stock_text = __( 'Default Text', 'cmsmasters-elementor' );

			if ( '' === get_option( 'woocommerce_stock_format' ) ) {
				/* translators: Addon %s: stock amount */
				$stock_placeholder = __( '%s in stock', 'cmsmasters-elementor' );
				$description = __( 'You can use %s instead of products` stock amount.', 'cmsmasters-elementor' );
			} else {
				$stock_placeholder = __( 'In stock', 'cmsmasters-elementor' );
				$description = '';
			}

			$this->add_control(
				'in_stock_text',
				array(
					'label' => $stock_text,
					'type' => Controls_Manager::TEXT,
					'placeholder' => $stock_placeholder,
					'description' => $description,
					'dynamic' => array(
						'active' => true,
					),
					'condition' => array(
						'show_in_stock' => 'yes',
					),
				)
			);
		}

		$this->add_control(
			'out_stock_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Out Of Stock', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'out_stock_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'out of stock', 'cmsmasters-elementor' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->end_controls_section();

		parent::register_controls();
	}

	public function get_badge( $product ) {
		$settings = $this->get_settings_for_display();
		$availability = $product->get_availability();

		$this->add_render_attribute( 'cmsmasters_badge-wrapper', 'class', 'elementor-widget-cmsmasters-woo-badge__wrapper' );
		$this->add_render_attribute( 'cmsmasters_badge-wrapper', 'class', 'cmsmasters-woo-badge' );

		if ( 'default' === $settings['type_style'] ) {
			if ( ! empty( $settings['cmsmasters_badge_position'] ) ) {
				$this->add_render_attribute( 'cmsmasters_badge-wrapper', 'class', 'cmsmasters-woo-badge-' . $settings['cmsmasters_badge_position'] );
			}

			if ( ! empty( $settings['cmsmasters_badge_type'] ) ) {
				$this->add_render_attribute( 'cmsmasters_badge-wrapper', 'class', 'cmsmasters-woo-badge-type-' . $settings['cmsmasters_badge_type'] );
			}
		}

		if ( 'in-stock' === $availability['class'] ) {
			if ( $settings['show_in_stock'] ) {
				$this->get_stock( $availability );
			}
		} elseif ( 'out-of-stock' === $availability['class'] ) {
			$this->get_out_of_stock( $availability );
		}
	}

	public function get_out_of_stock( $availability ) {
		$settings = $this->get_settings_for_display();
		$text = ( '' === $settings['out_stock_text'] ) ? $availability['availability'] : $settings['out_stock_text'];

		$this->add_render_attribute( 'cmsmasters_badge-wrapper', 'class', 'elementor-widget-cmsmasters-woo-badge__out-stock' );

		echo '<div ' . $this->get_render_attribute_string( 'cmsmasters_badge-wrapper' ) . '>
			<div class="elementor-widget-cmsmasters-woo-badge__inner cmsmasters-woo-badge-inner">' .
			wp_kses_post( apply_filters( 'woocommerce_stock_html', '<span class="cmsmasters-woo-badge-inner-text">' . $text . '</span>', $availability['availability'] ) ) .
			'</div>
		</div>';
	}

	public function get_stock( $availability ) {
		$text = $this->text_in_stock();

		$this->add_render_attribute( 'cmsmasters_badge-wrapper', 'class', 'elementor-widget-cmsmasters-woo-badge__in-stock' );

		echo '<div ' . $this->get_render_attribute_string( 'cmsmasters_badge-wrapper' ) . '>
			<div class="elementor-widget-cmsmasters-woo-badge__inner cmsmasters-woo-badge-inner">' .
			wp_kses_post( apply_filters( 'woocommerce_stock_html', '<span class="cmsmasters-woo-badge-inner-text">' . $text . '</span>', $availability['availability'] ) ) .
			'</div>
		</div>';
	}

	public function text_in_stock() {
		$product = wc_get_product();
		$settings = $this->get_settings_for_display();
		$stock_amount = $product->get_stock_quantity();
		$low_stock_amount = $product->get_low_stock_amount();
		$stock_quantity = wc_format_stock_quantity_for_display( $stock_amount, $product );

		switch ( get_option( 'woocommerce_stock_format' ) ) {
			case 'low_amount':
				if ( $stock_amount <= get_option( 'woocommerce_notify_low_stock_amount' ) || $stock_amount <= $low_stock_amount ) {
					/* translators: %s: stock amount */
					$display = ( '' !== $settings['low_stock_text'] ) ? sprintf( $settings['low_stock_text'], $stock_quantity ) : sprintf( __( 'Only %s left in stock', 'cmsmasters-elementor' ), $stock_quantity );
				}
				break;
			case '':
				/* translators: %s: stock amount */
				$display = ( '' !== $settings['in_stock_text'] ) ? sprintf( $settings['in_stock_text'], $stock_quantity ) : sprintf( __( '%s in stock', 'cmsmasters-elementor' ), $stock_quantity );
				break;
			case
				$display = ( '' !== $settings['in_stock_text'] ) ? $settings['in_stock_text'] : __( 'In stock', 'cmsmasters-elementor' );
		}

		return $display;
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
				'field' => 'low_stock_text',
				'type' => esc_html__( 'Low Amount Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'in_stock_text',
				'type' => esc_html__( 'In Stock Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'out_stock_text',
				'type' => esc_html__( 'Out of Stock Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
