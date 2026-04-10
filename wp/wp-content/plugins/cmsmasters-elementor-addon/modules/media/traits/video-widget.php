<?php
namespace CmsmastersElementor\Modules\Media\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Video widget trait.
 *
 * Provides basic widget methods for video widgets.
 *
 * @since 1.0.0
 */
trait Video_Widget {

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
			'video',
			'player',
			'embed',
			'youtube',
		);

		return array_merge( $parent_keywords, $global_keywords );
	}

}
