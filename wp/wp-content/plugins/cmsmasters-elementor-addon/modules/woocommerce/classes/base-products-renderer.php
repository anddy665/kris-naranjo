<?php
namespace CmsmastersElementor\Modules\Woocommerce\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSmasters Products shortcode class.
 *
 * An abstract class to register new woocommerce renderer.
 *
 * @since 1.0.0
 */
abstract class Base_Products_Renderer extends \WC_Shortcode_Products {

	const DEFAULT_COLUMNS_AND_ROWS = 4;

	/**
	 * @since 1.0.0
	 */
	public function get_content() {
		$result = $this->get_query_results();

		if ( empty( $result->total ) ) {
			return '';
		}

		add_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

		return parent::get_content();
	}

	/**
	 * @since 1.0.0
	 */
	protected function parse_attributes( $attributes ) {
		return apply_filters(
			'cmsmasters_woocommerce_shortcode_products_attributes',
			parent::parse_attributes( $attributes )
		);
	}

}
