<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Modules\Woocommerce\Widgets\Wpclever\WpcleverSmartButtonBase\Wpclever_Smart_Button_Base;

use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Wpclever_Smart_Compare_Button extends Wpclever_Smart_Button_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.11.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return 'cmsmasters-wpclever-compare-button';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.11.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Wpclever Smart Compare Button', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.11.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-compare-button';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.11.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'compare',
			'button',
			'wpclever',
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
	 * Get plugin object.
	 *
	 * @since 1.11.0
	 *
	 * @return object plugin object.
	 */
	public function get_obj() {
		$obj = new \WPCleverWoosc();

		return $obj;
	}

	/**
	 * Get admin url.
	 *
	 * Retrieve plugin admin url.
	 *
	 * @since 1.11.0
	 *
	 * @return string Admin url.
	 */
	public function localization_url() {
		return esc_url( admin_url( 'admin.php?page=wpclever-woosc&tab=localization' ) );
	}

	/**
	 * Get widget icon state.
	 *
	 * Retrieve the widget icon state.
	 *
	 * @since 1.11.0
	 *
	 * @return array
	 */
	public function state_icon_control() {
		return array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'active' => __( 'Active', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * Get widget default icons.
	 *
	 * Retrieve the widget default icons.
	 *
	 * @since 1.11.0
	 *
	 * @return array
	 */
	public function default_icon_control( $item ) {
		$default_icon = array();
		$default_icon['value'] = 'far fa-chart-bar';
		$default_icon['library'] = 'fa-regular';

		if ( 'active' === $item ) {
			$default_icon['value'] = 'far fa-check-circle';
			$default_icon['library'] = 'fa-regular';
		}

		return $default_icon;
	}

	/**
	 * Render buttons.
	 *
	 * @since 1.11.0
	 *
	 * @return string HTML
	 */
	public function render_button() {
		if ( ! class_exists( 'WPCleverWoosc' ) ) {
			return;
		}

		$this->wpclever_smart_compare_render();
	}

	public function wpclever_smart_compare_render() {

		if ( ! class_exists( 'WPCleverWoosc' ) ) {
			return;
		}

		$woosc = $this->get_obj();
		$product_attrs = $this->get_product_attrs();
		$prefix_class = $this->cmsmasters_class_prefix();
		$settings = $this->get_settings_for_display();

		$attrs = array(
			'id'   => $product_attrs['product_id'],
			'type' => $woosc::get_setting( 'button_type', 'button' ),
		);

		add_filter( 'woosc_button_html', function ( $output ) use ( $woosc ) {
			return $this->compare_button_html( $output, $woosc );
		}, 11, 1 );

		$shortcode = "[woosc
					id=\"{$attrs['id']}\"
					type=\"{$attrs['type']}\"]";

		$shortcode = do_shortcode( shortcode_unautop( $shortcode ) );

		$this->add_render_attribute( 'wpclever-compare-wapper', 'class', array(
			$prefix_class,
			"{$prefix_class}__wrapper",
			"{$prefix_class}__button-{$settings['wpclever_button_alignment']}",
		) );

		echo "<div {$this->get_render_attribute_string( 'wpclever-compare-wapper' )}>{$shortcode}</div>";
	}

	public function compare_button_html( $output, $woosc ) {
		$product_attrs = $this->get_product_attrs();
		$settings = $this->get_settings_for_display();
		$prefix_class = $this->cmsmasters_class_prefix();

		$tag = 'a';

		$is_button = 'button' === $settings['wpclever_items_type'];
		$is_link = 'link' === $settings['wpclever_items_type'];
		$is_icon = 'icon' === $settings['wpclever_items_type'];

		$product_id = $product_attrs['product_id'];
		$product_name = $product_attrs['product_name'];
		$product_image = $product_attrs['product_image'];

		$this->add_render_attribute( 'wpclever-compare', 'class', array(
			'woosc-btn',
			'woosc-btn-' . $product_id . '',
			"{$prefix_class}__general",
		) );

		if ( isset( $settings['wpclever_normal_icon'] ) && ! empty( $settings['wpclever_normal_icon']['value'] ) ) {
			$this->add_render_attribute( 'wpclever-compare', 'class', array(
				'woosc-btn-has-icon',
			) );
		}

		$this->add_render_attribute( 'wpclever-compare', 'data-id', array(
			$product_id,
		) );

		$this->add_render_attribute( 'wpclever-compare', 'data-product_name', array(
			$product_name,
		) );

		$this->add_render_attribute( 'wpclever-compare', 'data-product_image', array(
			$product_image,
		) );

		if ( $is_link ) {
			$this->add_render_attribute( 'wpclever-compare', 'class', array(
				"{$prefix_class}__link",
			) );
		}

		if ( $is_button ) {
			$tag = 'button';

			$this->add_render_attribute( 'wpclever-compare', 'class', array(
				"{$prefix_class}__button",
			) );
		}

		if ( $is_icon ) {
			$this->add_render_attribute( 'wpclever-compare', 'class', array(
				"{$prefix_class}__icon",
			) );
		}

		if ( $is_link || $is_icon ) {
			$this->add_render_attribute( 'wpclever-compare', 'href', array(
				'?add-to-compare=' . $product_id,
			) );
		}

		if ( isset( $settings['wpclever_normal_icon'] ) && ! empty( $settings['wpclever_normal_icon']['value'] ) ) {
			$icon_align = $settings['wpclever_icon_align'];
			$icon_reverse = $settings['wpclever_icon_reverse'];

			$this->add_render_attribute( 'wpclever-compare', 'class', array(
				"{$prefix_class}__icon-{$icon_align}",
				"{$prefix_class}__icon-{$icon_reverse}",
			) );
		}

		if ( $this->is_editor() ) {
			$this->add_render_attribute( 'wpclever-compare', 'disabled', array(
				'disabled',
			) );
		}

		$text = $woosc::localization( 'button', esc_html__( 'Compare', 'cmsmasters-elementor' ) );
		$icon = $this->render_icon( 'compare', true, true, false );
		$text_html = "<span class='woosc-btn-text'>{$text}</span>";

		if ( 'icon' === $settings['wpclever_items_type'] ) {
			$text_html = '';
		}

		$output = '<' . $tag . ' ' . $this->get_render_attribute_string( 'wpclever-compare' ) . '>' . $icon . $text_html . '</' . $tag . '>';

		return $output;
	}
}
