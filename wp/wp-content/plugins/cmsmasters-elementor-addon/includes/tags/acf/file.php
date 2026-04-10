<?php
namespace CmsmastersElementor\Tags\ACF;

use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters file.
 *
 * Retrieves file field data from an advanced custom field.
 *
 * @since 1.0.0
 */
class File extends Image {

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
		return 'file';
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
		return __( 'File Field', 'cmsmasters-elementor' );
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
		return array( TagsModule::MEDIA_CATEGORY );
	}

	/**
	* Get supported fields.
	*
	* Returns an array of tag supported fields.
	*
	* @since 1.0.0
	*
	* @return array Supported tag fields.
	*/
	public function get_supported_fields() {
		return array( 'file' );
	}

}
