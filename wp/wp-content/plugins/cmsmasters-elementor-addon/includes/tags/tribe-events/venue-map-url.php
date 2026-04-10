<?php
namespace CmsmastersElementor\Tags\TribeEvents;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\TribeEvents\Traits\Tribe_Events_Group;

use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters venue map url.
 *
 * Retrieve the venue map url.
 *
 * @since 1.13.0
 */
class Venue_Map_Url extends Tag {

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
		return 'venue-map-url';
	}

	/**
	* Get tag venue map url.
	*
	* Returns the venue map url of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag venue map url.
	*/
	public static function tag_title() {
		return __( 'Venue Map Url', 'cmsmasters-elementor' );
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.13.0
	* @since 1.19.4 Added additional validation for empty venues.
	*
	* @return void Tag render result.
	*/
	public function render() {
		$event = tribe_get_event();

		if ( ! $event || empty( $event->venues ) || ! isset( $event->venues[0] ) ) {
			return;
		}

		$venue = $event->venues[0];

		$directions_link = $venue->directions_link;

		if ( $directions_link ) {
			echo wp_kses_post( $directions_link );
		}
	}
}
