<?php
namespace CmsmastersElementor\Tags\TribeEvents;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\TribeEvents\Traits\Tribe_Events_Group;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters event image url.
 *
 * Retrieves the image url for the current event.
 *
 * @since 1.13.0
 */
class Image_URL extends Data_Tag {

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
		return 'event-image-url';
	}

	/**
	* Get tag image url.
	*
	* Returns the image url of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag image url.
	*/
	public static function tag_title() {
		return __( 'Event Image URL', 'cmsmasters-elementor' );
	}

	/**
	* Get categories.
	*
	* Returns an array of dynamic tag categories.
	*
	* @since 1.13.0
	*
	* @return array Tag categories.
	*/
	public function get_categories() {
		return array( TagsModule::URL_CATEGORY );
	}

	/**
	* Get value.
	*
	* Returns out the value of the dynamic data tag.
	*
	* @since 1.13.0
	*
	* @param array $options Dynamic data tag options.
	*
	* @return array Tag value.
	*/
	public function get_value( array $options = array() ) {
		$event_data = tribe_get_event();

		if ( ! $event_data ) {
			return array();
		}

		$image_id = get_post_thumbnail_id( $event_data->ID );

		$attachment_image_src = wp_get_attachment_image_src( $image_id, 'full' );

		if ( ! $attachment_image_src ) {
			return;
		}

		return $attachment_image_src[0];
	}

}
