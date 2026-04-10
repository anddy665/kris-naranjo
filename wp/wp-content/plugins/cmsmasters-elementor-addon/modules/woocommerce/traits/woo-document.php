<?php
namespace CmsmastersElementor\Modules\Woocommerce\Traits;

use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * WooCommerce document trait.
 *
 * Provides basic document methods for WooCommerce template document.
 *
 * @since 1.0.0
 */
trait Woo_Document {

	/**
	 * Get editor widgets prefix.
	 *
	 * Retrieve editor panel Addon widgets prefix.
	 *
	 * @since 1.0.0
	 *
	 * @return string Editor panel widgets prefix.
	 */
	public static function get_editor_widgets_prefix() {
		return 'cmsmasters-woo';
	}

	/**
	 * Add body classes.
	 *
	 * Filters body classes for the `style` controls selector.
	 *
	 * Fires by `body_class` WordPress filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $body_classes Body classes array.
	 *
	 * @return array Filtered body classes array.
	 */
	public function filter_body_classes( $body_classes ) {
		$body_classes = parent::filter_body_classes( $body_classes );

		$is_preview_mode = Utils::is_preview_mode( $this->get_main_id() );

		if ( get_the_ID() === $this->get_main_id() || $is_preview_mode ) {
			$body_classes[] = 'woocommerce';
		}

		return $body_classes;
	}

}
