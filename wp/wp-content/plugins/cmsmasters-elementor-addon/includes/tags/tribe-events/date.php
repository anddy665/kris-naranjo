<?php
namespace CmsmastersElementor\Tags\TribeEvents;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\TribeEvents\Traits\Tribe_Events_Group;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters date.
 *
 * Retrieve the event date.
 *
 * @since 1.13.0
 */
class Date extends Tag {

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
		return 'event-date';
	}

	/**
	* Get tag date.
	*
	* Returns the date of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag date.
	*/
	public static function tag_title() {
		return __( 'Event Date', 'cmsmasters-elementor' );
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

		$short_schedule_details = $event_data->short_schedule_details;

		if ( $short_schedule_details ) {
			echo wp_kses_post( $short_schedule_details );
		}
	}

}
