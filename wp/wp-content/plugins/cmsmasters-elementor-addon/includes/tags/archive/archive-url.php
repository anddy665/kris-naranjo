<?php
namespace CmsmastersElementor\Tags\Archive;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Archive\Traits\Archive_Group;
use CmsmastersElementor\Utils;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters archive url.
 *
 * Retrieve the archive permalink.
 *
 * @since 1.0.0
 */
class Archive_URL extends Data_Tag {

	use Base_Tag, Archive_Group;

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
	* @return array Tag value.
	*/
	public function get_value( array $options = array() ) {
		if ( is_search() ) {
			return Utils::get_the_archive_url( get_search_query() );
		}

		return Utils::get_the_archive_url();
	}

}
