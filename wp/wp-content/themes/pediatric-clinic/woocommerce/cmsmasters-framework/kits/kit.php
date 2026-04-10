<?php
namespace PediatricClinicSpace\Woocommerce\CmsmastersFramework\Kits;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Theme style class.
 *
 * Adds settings for theme style.
 */
class Kit {

	const KIT_NAMESPACE = __NAMESPACE__;

	/**
	 * Addon kit document class constructor.
	 *
	 * Initializing the Addon kit document initial file class.
	 *
	 * @param array $data Document data.
	 */
	public function __construct() {
		add_filter( 'cmsmasters_theme_kit_sections', array( $this, 'add_kit_sections' ) );
	}

	/**
	 * Add kit sections.
	 *
	 * @param array $sections Kit sections.
	 *
	 * @return array Kit sections.
	 */
	public function add_kit_sections( $sections = array() ) {
		$sections['woocommerce'] = self::KIT_NAMESPACE . '\Settings\Woocommerce\Section';

		return $sections;
	}

}
