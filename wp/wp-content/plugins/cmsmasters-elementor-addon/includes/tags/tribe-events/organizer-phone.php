<?php
namespace CmsmastersElementor\Tags\TribeEvents;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\TribeEvents\Traits\Tribe_Events_Group;

use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters organizer phone.
 *
 * Retrieve the organizer phone.
 *
 * @since 1.13.0
 */
class Organizer_Phone extends Tag {

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
		return 'organizer-phone';
	}

	/**
	* Get tag organizer phone.
	*
	* Returns the organizer phone of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag organizer phone.
	*/
	public static function tag_title() {
		return __( 'Organizer Phone', 'cmsmasters-elementor' );
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

		$phone = $organizer->phone;

		if ( $phone ) {
			echo wp_kses_post( $phone );
		}
	}
}
