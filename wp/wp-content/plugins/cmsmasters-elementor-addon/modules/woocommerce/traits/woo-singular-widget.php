<?php
namespace CmsmastersElementor\Modules\Woocommerce\Traits;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Widget;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * WooCommerce Singular widget trait.
 *
 * Provides basic widget methods for WooCommerce Singular template document.
 *
 * @since 1.0.0
 */
trait Woo_Singular_Widget {

	use Woo_Widget {
		get_name_prefix as woo_get_name_prefix;
		get_global_keywords as woo_get_global_keywords;
	}

	/**
	 * Get widget name prefix.
	 *
	 * Retrieve the widget name prefix.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget name prefix.
	 */
	public function get_name_prefix() {
		return $this->woo_get_name_prefix() . 'product-';
	}

	/**
	 * Get global widget keywords.
	 *
	 * Retrieve the list of global keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget global keywords.
	 */
	public function get_global_keywords() {
		$parent_keywords = $this->woo_get_global_keywords();

		$global_keywords = array( 'product' );

		return array_merge( $parent_keywords, $global_keywords );
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the widget categories.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( Base_Document::WOO_SINGULAR_WIDGETS_CATEGORY );
	}

}
