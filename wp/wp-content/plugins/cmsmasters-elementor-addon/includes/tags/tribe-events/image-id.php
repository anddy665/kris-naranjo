<?php
namespace CmsmastersElementor\Tags\TribeEvents;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\TribeEvents\Traits\Tribe_Events_Group;

use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters Tribe Events image id.
 *
 * Retrieves the image id for the current event.
 *
 * @since 1.13.0
 */
class Image_ID extends Tag {

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
		return 'event-image-id';
	}

	/**
	* Get tag image id.
	*
	* Returns the image id of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag image id.
	*/
	public static function tag_title() {
		return __( 'Event Image ID', 'cmsmasters-elementor' );
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
			return array();
		}

		$image_id = get_post_thumbnail_id( $event_data->ID );

		if ( $image_id ) {
			echo $image_id;
		}
	}

}
