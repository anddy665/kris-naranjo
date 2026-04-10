<?php
namespace CmsmastersElementor\Tags\Post\Traits;

use CmsmastersElementor\Tags_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Post group trait.
 *
 * Allows the dynamic tag to inherit the group name and title.
 *
 * @since 1.0.0
 */
trait Post_Group {

	/**
	* Get group name.
	*
	* Returns the name of the dynamic tag group.
	*
	* @since 1.0.0
	*
	* @return string Group name.
	*/
	public static function group_name() {
		return Tags_Manager::POST_GROUP;
	}

	/**
	* Get group title.
	*
	* Returns the title of the dynamic tag group.
	*
	* @since 1.0.0
	*
	* @return string Group title.
	*/
	public static function group_title() {
		return __( 'Post', 'cmsmasters-elementor' );
	}

	/**
	* Get group.
	*
	* Returns the group of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag group.
	*/
	public function get_group() {
		return self::group_name();
	}

}
