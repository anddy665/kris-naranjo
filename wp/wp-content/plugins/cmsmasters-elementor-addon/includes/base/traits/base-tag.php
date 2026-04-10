<?php
namespace CmsmastersElementor\Base\Traits;

use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


trait Base_Tag {

	/**
	* Get name.
	*
	* Returns the name of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag name.
	*/
	public function get_name() {
		/* translators: Dynamic tags name. 1: Tag group name, 2: Tag name */
		return sprintf( 'cmsmasters-%1$s-%2$s', static::group_name(), static::tag_name() );
	}

	/**
	* Get title.
	*
	* Returns the title of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag title.
	*/
	public function get_title() {
		/* translators: Dynamic tags title. 1: Tag group title, 2: Tag title */
		return sprintf( __( '%1$s %2$s', 'cmsmasters-elementor' ), static::group_title(), static::tag_title() );
	}

	/**
	* Get categories.
	*
	* Returns an array of dynamic tag categories.
	*
	* @since 1.0.0
	*
	* @return array Tag categories.
	*/
	public function get_categories() {
		return array( TagsModule::TEXT_CATEGORY );
	}

}
