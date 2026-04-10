<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Modules\Woocommerce\Widgets\Wpclever\CompareWishlistBase\Compare_Wishlist_Counter_Base;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Wpclever_Smart_Compare_Counter extends Compare_Wishlist_Counter_Base {
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
		return __( 'Wpclever Smart Compare Counter', 'cmsmasters-elementor' );
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
		return 'cmsicon-compare-counter';
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

	public function cmsmasters_class_prefix() {
		return 'elementor-widget-cmsmasters-wpclever-compare-counter';
	}

	public function individual_class() {
		$class = array(
			'wrapper' => 'site-header-compare',
			'trigger' => 'woosc-menu',
			'link' => 'header-compare',
		);

		return $class;
	}

	public function get_obj() {
		$obj = new \WPCleverWoosc();

		return $obj;
	}

	public function default_text() {
		return __( 'Compare', 'cmsmasters-elementor' );
	}

	public function default_icon() {
		return array(
			'value' => 'far fa-chart-bar',
			'library' => 'fa-regular',
		);
	}
}
