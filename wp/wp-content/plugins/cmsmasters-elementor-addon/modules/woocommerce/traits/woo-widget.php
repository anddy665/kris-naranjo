<?php
namespace CmsmastersElementor\Modules\Woocommerce\Traits;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Widget;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * WooCommerce widget trait.
 *
 * Provides basic widget methods for WooCommerce template document.
 *
 * @since 1.0.0
 */
trait Woo_Widget {

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
		return sprintf( '%1$swoo-%2$s', Base_Widget::WIDGET_NAME_PREFIX, self::get_widget_class_name() );
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
		return Base_Widget::WIDGET_NAME_PREFIX . 'woo-';
	}

	/**
	 * Get group name.
	 *
	 * @since 1.6.5
	 *
	 * @return string Group name.
	 */
	public function get_group_name() {
		return 'cmsmasters-woocommerce';
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
		$parent_keywords = parent::get_global_keywords();

		$global_keywords = array(
			'woocommerce',
			'shop',
			'store',
		);

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
		return array( Base_Document::WOO_WIDGETS_CATEGORY );
	}

}
