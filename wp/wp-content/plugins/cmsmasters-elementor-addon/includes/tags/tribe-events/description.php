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
class Description extends Tag {

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
		return 'event-description';
	}

	/**
	* Get tag description.
	*
	* Returns the description of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag description.
	*/
	public static function tag_title() {
		return __( 'Event Description', 'cmsmasters-elementor' );
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

		$post_excerpt = $event_data->post_excerpt;

		if ( $post_excerpt ) {
			echo $post_excerpt;
		}
	}

}
