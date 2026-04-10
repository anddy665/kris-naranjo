<?php
namespace CmsmastersElementor\Modules\TribeEvents\Traits;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Modules\TribeEvents\Traits\Tribe_Events_Widget;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Tribe Events Archive widget trait.
 *
 * Provides basic widget methods for Tribe Events Archive template document.
 *
 * @since 1.0.0
 */
trait Tribe_Events_Archive_Widget {

	use Tribe_Events_Widget {
		get_name_prefix as tribe_events_get_name_prefix;
		get_global_keywords as tribe_events_get_global_keywords;
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
		return $this->tribe_events_get_name_prefix() . 'archive-';
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
		$parent_keywords = $this->tribe_events_get_global_keywords();

		$global_keywords = array( 'tribe-events' );

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
		return array( Base_Document::TRIBE_EVENTS_ARCHIVE_WIDGETS_CATEGORY );
	}

}
