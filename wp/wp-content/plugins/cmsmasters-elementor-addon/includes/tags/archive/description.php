<?php
namespace CmsmastersElementor\Tags\Archive;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Archive\Traits\Archive_Group;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters description.
 *
 * Retrieves the description for an author, post type, or term archive.
 *
 * @since 1.0.0
 */
class Description extends Tag {

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
		return 'description';
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
		return __( 'Description', 'cmsmasters-elementor' );
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.0.0
	* @since 1.16.4 Fixed render archive description.
	*
	* @return void Tag render result.
	*/
	public function render() {
		$archive_description = get_the_archive_description();

		if ( empty( $archive_description ) ) {
			return '';
		}

		echo wp_kses_post( $archive_description );
	}

}
