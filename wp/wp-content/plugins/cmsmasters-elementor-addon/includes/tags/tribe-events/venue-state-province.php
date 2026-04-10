<?php
namespace CmsmastersElementor\Tags\TribeEvents;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\TribeEvents\Traits\Tribe_Events_Group;

use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters venue state province.
 *
 * Retrieve the venue state province.
 *
 * @since 1.13.0
 */
class Venue_State_Province extends Tag {

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
		return 'venue-state-province';
	}

	/**
	* Get tag venue state province.
	*
	* Returns the venue state province of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag venue state province.
	*/
	public static function tag_title() {
		return __( 'Venue State Province', 'cmsmasters-elementor' );
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

		$state_province = $venue->state_province;

		if ( $state_province ) {
			echo wp_kses_post( $state_province );
		}
	}
}
