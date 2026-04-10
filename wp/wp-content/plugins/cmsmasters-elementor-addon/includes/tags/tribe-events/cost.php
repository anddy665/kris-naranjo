<?php
namespace CmsmastersElementor\Tags\TribeEvents;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\TribeEvents\Traits\Tribe_Events_Group;

use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters cost.
 *
 * Retrieve the event cost.
 *
 * @since 1.13.0
 */
class Cost extends Tag {

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
		return 'event-cost';
	}

	/**
	* Get tag cost.
	*
	* Returns the cost of the dynamic tag.
	*
	* @since 1.13.0
	*
	* @return string Tag cost.
	*/
	public static function tag_title() {
		return __( 'Event Cost', 'cmsmasters-elementor' );
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

		$cost = $event_data->cost;

		if ( $cost ) {
			echo wp_kses_post( $cost );
		}
	}

}
