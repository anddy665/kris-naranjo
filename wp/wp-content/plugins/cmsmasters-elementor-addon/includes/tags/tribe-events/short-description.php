<?php
namespace CmsmastersElementor\Tags\TribeEvents;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\TribeEvents\Traits\Tribe_Events_Group;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters short description.
 *
 * Retrieves the brief event description.
 *
 * @since 1.13.0
 */
class Short_Description extends Tag {

	use Base_Tag, Tribe_Events_Group;

	/**
	* Get tag name.
	*
	* Returns the name of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag name.
	*/
	public static function tag_name() {
		return 'event-short-description';
	}

	/**
	* Get tag short description.
	*
	* Returns the short description of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag short description.
	*/
	public static function tag_title() {
		return __( 'Event Short Description', 'cmsmasters-elementor' );
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return void Tag render result.
	*/
	public function render() {
		$event_data = tribe_get_event();

		if ( ! $event_data ) {
			return;
		}

		$excerpt = $event_data->excerpt;

		if ( $excerpt ) {
			echo $excerpt;
		}
	}

}
