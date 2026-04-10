<?php
namespace CmsmastersElementor\Tags\Post;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Post\Traits\Post_Group;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters post url.
 *
 * Retrieves the full permalink for the current post.
 *
 * @since 1.0.0
 */
class Post_URL extends Data_Tag {

	use Base_Tag, Post_Group;

	/**
	* Get tag name.
	*
	* Returns the name of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag name.
	*/
	public static function tag_name() {
		return 'url';
	}

	/**
	* Get tag title.
	*
	* Returns the title of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag title.
	*/
	public static function tag_title() {
		return __( 'URL', 'cmsmasters-elementor' );
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
		return array( TagsModule::URL_CATEGORY );
	}

	/**
	* Get value.
	*
	* Returns out the value of the dynamic data tag.
	*
	* @since 1.0.0
	*
	* @param array $options Dynamic data tag options.
	*
	* @return array Tag value.
	*/
	public function get_value( array $options = array() ) {
		return get_permalink();
	}

}
