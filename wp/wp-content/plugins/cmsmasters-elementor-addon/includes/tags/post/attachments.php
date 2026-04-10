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
 * CMSMasters attachments.
 *
 * Retrieves media attached to the post.
 *
 * @since 1.0.0
 */
class Attachments extends Data_Tag {

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
		return 'attachments';
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
		return __( 'Attached Images', 'cmsmasters-elementor' );
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
		return array( TagsModule::GALLERY_CATEGORY );
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
		$attached_media = get_attached_media( 'image' );
		$attached_images = array();

		foreach ( $attached_media as $image ) {
			$attached_images[] = array( 'id' => $image->ID );
		}

		return $attached_images;
	}

}
