<?php
namespace CmsmastersElementor\Modules\TemplatePages\Documents\Base;

use CmsmastersElementor\Modules\TemplateDocuments\Base\Page_Document;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Archive & Singular documents.
 *
 * An abstract class that provides the needed properties and methods to
 * manage and handle Archive & Singular documents in inheriting classes.
 *
 * @since 1.0.0
 */
abstract class Archive_Singular_Document extends Page_Document {

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @since 1.0.0
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['location_exclude'] = array(
			// WooCommerce
			'woocommerce',
			'product_archive',
			'product_search',
			'shop_page',
			'product',
			// Tribe Events
			'tribe_events_archive',
			'post_tag',
			'tribe_events_cat',
		);

		$properties['locations_category'] = 'parent';

		$properties = apply_filters( 'cmsmasters_elementor/documents/archive_singular/get_properties', $properties );

		return $properties;
	}

	/**
	 * Register document controls.
	 *
	 * Used to add new controls to page documents settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		parent::register_controls();

		/**
		 * Register Archive and Singular document controls.
		 *
		 * Used to add new controls to the Archive and Singular document settings.
		 *
		 * Fires after Elementor registers the document controls.
		 *
		 * @since 1.0.0
		 *
		 * @param Archive_Singular_Document $this Archive and Singular base document instance.
		 */
		do_action( 'cmsmasters_elementor/documents/archive_singular/register_controls', $this );
	}

}
