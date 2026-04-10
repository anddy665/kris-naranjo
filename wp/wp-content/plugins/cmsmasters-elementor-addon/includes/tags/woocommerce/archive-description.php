<?php
namespace CmsmastersElementor\Tags\Woocommerce;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Woocommerce\Traits\Woo_Group;
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
class Archive_Description extends Tag {

	use Base_Tag, Woo_Group;

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
		return 'archive-description';
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
		return __( 'Archive Description', 'cmsmasters-elementor' );
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.0.0
	* @since 1.15.0 Fixed woocommerce archive description message when empty.
	*
	* @return void Tag render result.
	*/
	public function render() {
		$woocommerce_archive_description = do_action( 'woocommerce_archive_description' );

		if ( empty( $woocommerce_archive_description ) ) {
			return '';
		}

		echo wp_kses_post( $woocommerce_archive_description );
	}

}
