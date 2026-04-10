<?php
namespace CmsmastersElementor\Tags\Site;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Site\Traits\Site_Group;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters logo.
 *
 * Retrieves the site logo.
 *
 * @since 1.0.0
 */
class Logo extends Data_Tag {

	use Base_Tag, Site_Group;

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
		return 'logo';
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
		return __( 'Logo', 'cmsmasters-elementor' );
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
		return array( TagsModule::IMAGE_CATEGORY );
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
		$custom_logo_id = get_theme_mod( 'custom_logo' );

		if ( $custom_logo_id ) {
			$attachment_image_src = wp_get_attachment_image_src( $custom_logo_id, 'full' );
			$url = $attachment_image_src[0];
		} else {
			$url = Utils::get_placeholder_image_src();
		}

		return array(
			'id' => $custom_logo_id,
			'url' => $url,
		);
	}

}
