<?php
namespace CmsmastersElementor\Tags\TribeEvents;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\TribeEvents\Traits\Tribe_Events_Group;

use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters organizer website.
 *
 * Retrieve the organizer website.
 *
 * @since 1.13.0
 */
class Organizer_Website extends Tag {

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
		return 'organizer-website';
	}

	/**
	* Get tag organizer website.
	*
	* Returns the organizer website of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag organizer website.
	*/
	public static function tag_title() {
		return __( 'Organizer Website', 'cmsmasters-elementor' );
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.13.0
	* @since 1.19.4 Added additional validation for empty organizer.
	*
	* @return void Tag render result.
	*/
	public function render() {
		$event = tribe_get_event();

		if ( ! $event || empty( $event->organizers ) || ! isset( $event->organizers[0] ) ) {
			return;
		}

		$organizer = $event->organizers[0];

		$website = $organizer->website;

		if ( $website ) {
			echo wp_kses_post( $website );
		}
	}
}
