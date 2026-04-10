<?php
namespace CmsmastersElementor\Tags\TribeEvents;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\TribeEvents\Traits\Tribe_Events_Group;

use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters organizer title.
 *
 * Retrieve the organizer title.
 *
 * @since 1.13.0
 */
class Organizer_Title extends Tag {

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
		return 'organizer-title';
	}

	/**
	* Get tag organizer title.
	*
	* Returns the organizer title of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag organizer title.
	*/
	public static function tag_title() {
		return __( 'Organizer Title', 'cmsmasters-elementor' );
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

		$post_title = $organizer->post_title;

		if ( $post_title ) {
			echo wp_kses_post( $post_title );
		}
	}
}
