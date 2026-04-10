<?php
namespace CmsmastersElementor\Tags\Site;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Site\Traits\Site_Group;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters user profile picture.
 *
 * Retrieves the avatar of the current user.
 *
 * @since 1.0.0
 */
class User_Profile_Picture extends Data_Tag {

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
		return 'user-profile-picture';
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
		return __( 'User Profile Picture', 'cmsmasters-elementor' );
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
		$src = get_avatar_url( (int) get_current_user_id() );

		return array(
			'id' => '',
			'url' => $src,
		);
	}

}
