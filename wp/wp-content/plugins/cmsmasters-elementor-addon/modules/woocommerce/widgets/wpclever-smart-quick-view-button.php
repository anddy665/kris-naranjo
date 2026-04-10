<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Modules\Woocommerce\Widgets\Wpclever\WpcleverSmartButtonBase\Wpclever_Smart_Button_Base;

use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Wpclever_Smart_Quick_View_Button extends Wpclever_Smart_Button_Base {

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
		return 'cmsmasters-wpclever-quick-view-button';
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
		return __( 'Wpclever Smart Quick View Button', 'cmsmasters-elementor' );
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
		return 'cmsicon-quickview-button';
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
			'quick view',
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
		$obj = new \WPCleverWoosq();

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
		return esc_url( admin_url( 'admin.php?page=wpclever-woosq&tab=localization' ) );
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
		$default_icon['value'] = 'far fa-eye';
		$default_icon['library'] = 'fa-regular';

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
		if ( ! class_exists( 'WPCleverWoosq' ) ) {
			return;
		}

		$this->wpclever_smart_quick_view_render();
	}

	public function wpclever_smart_quick_view_render() {

		if ( ! class_exists( 'WPCleverWoosq' ) ) {
			return;
		}

		$woosq = $this->get_obj();
		$product_attrs = $this->get_product_attrs();
		$prefix_class = $this->cmsmasters_class_prefix();
		$settings = $this->get_settings_for_display();

		$attrs = array(
			'text' => $woosq::localization( 'button', __( 'Quick view', 'cmsmasters-elementor' ) ),
			'id' => $product_attrs['product_id'],
			'type' => $woosq::get_setting( 'button_type', 'button' ),
			'effect' => $woosq::get_setting( 'effect', 'mfp-3d-unfold' ),
			'context' => 'default',
		);

		add_filter( 'woosq_button_html', function ( $output ) use ( $attrs ) {
			return $this->quick_view_button_html( $output, $attrs );
		}, 11, 1 );

		$shortcode = "[woosq 
					id=\"{$attrs['id']}\" 
					text=\"{$attrs['text']}\" 
					context=\"{$attrs['context']}\"
					effect=\"{$attrs['effect']}\"
					type=\"{$attrs['type']}\"]";

		$shortcode = do_shortcode( shortcode_unautop( $shortcode ) );

		$this->add_render_attribute( 'wpclever-quick-view-wapper', 'class', array(
			$prefix_class,
			"{$prefix_class}__wrapper",
			"{$prefix_class}__button-{$settings['wpclever_button_alignment']}",
		) );

		echo "<div {$this->get_render_attribute_string( 'wpclever-quick-view-wapper' )}>{$shortcode}</div>";
	}

	public function quick_view_button_html( $output, $attrs ) {
		$settings = $this->get_settings_for_display();
		$prefix_class = $this->cmsmasters_class_prefix();

		$tag = 'a';

		$is_button = 'button' === $settings['wpclever_items_type'];
		$is_link = 'link' === $settings['wpclever_items_type'];
		$is_icon = 'icon' === $settings['wpclever_items_type'];

		$this->add_render_attribute( 'wpclever-quick-view', 'class', array(
			'woosq-btn',
			'woosq-btn-' . $attrs['id'] . '',
			"{$prefix_class}__general",
		) );

		if ( isset( $settings['wpclever_normal_icon'] ) && ! empty( $settings['wpclever_normal_icon']['value'] ) ) {
			$this->add_render_attribute( 'wpclever-quick-view', 'class', array(
				'woosq-btn-has-icon',
			) );
		}

		$this->add_render_attribute( 'wpclever-quick-view', 'data-id', array(
			$attrs['id'],
		) );

		$this->add_render_attribute( 'wpclever-quick-view', 'data-effect', array(
			$attrs['effect'],
		) );

		$this->add_render_attribute( 'wpclever-quick-view', 'data-context', array(
			$attrs['context'],
		) );

		if ( $is_link ) {
			$this->add_render_attribute( 'wpclever-quick-view', 'class', array(
				"{$prefix_class}__link",
			) );
		}

		if ( $is_button ) {
			$tag = 'button';

			$this->add_render_attribute( 'wpclever-quick-view', 'class', array(
				"{$prefix_class}__button",
			) );
		}

		if ( $is_icon ) {
			$this->add_render_attribute( 'wpclever-quick-view', 'class', array(
				"{$prefix_class}__icon",
			) );
		}

		if ( $is_link || $is_icon ) {
			$this->add_render_attribute( 'wpclever-quick-view', 'href', array(
				'?quick-view=' . $attrs['id'],
			) );
		}

		if ( isset( $settings['wpclever_normal_icon'] ) && ! empty( $settings['wpclever_normal_icon']['value'] ) ) {
			$icon_align = $settings['wpclever_icon_align'];
			$icon_reverse = $settings['wpclever_icon_reverse'];

			$this->add_render_attribute( 'wpclever-quick-view', 'class', array(
				"{$prefix_class}__icon-{$icon_align}",
				"{$prefix_class}__icon-{$icon_reverse}",
			) );
		}

		if ( $this->is_editor() ) {
			$this->add_render_attribute( 'wpclever-quick-view', 'disabled', array(
				'disabled',
			) );
		}

		$icon = $this->render_icon( true, false, false );
		$text_html = "<span class='woosq-btn-text'>{$attrs['text']}</span>";

		if ( 'icon' === $settings['wpclever_items_type'] ) {
			$text_html = '';
		}

		$output = '<' . $tag . ' ' . $this->get_render_attribute_string( 'wpclever-quick-view' ) . '>' . $icon . $text_html . '</' . $tag . '>';

		return $output;
	}
}
