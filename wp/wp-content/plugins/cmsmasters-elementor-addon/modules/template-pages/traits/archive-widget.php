<?php
namespace CmsmastersElementor\Modules\TemplatePages\Traits;

use CmsmastersElementor\Base\Base_Document;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Archive widget trait.
 *
 * Provides basic widget methods for archive template document.
 *
 * @since 1.0.0
 */
trait Archive_Widget {

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

		$global_keywords = array( 'archive' );

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
		return array( Base_Document::ARCHIVE_WIDGETS_CATEGORY );
	}

}
