<?php
namespace CmsmastersElementor\Modules\TemplateSections\Traits;

use CmsmastersElementor\Base\Base_Document;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Site widget trait.
 *
 * Provides basic widget methods for site template document.
 *
 * @since 1.0.0
 */
trait Site_Widget {

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

		$global_keywords = array( 'site' );

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
		return array( Base_Document::SITE_WIDGETS_CATEGORY );
	}

}
