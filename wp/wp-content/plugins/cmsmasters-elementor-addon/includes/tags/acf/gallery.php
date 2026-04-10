<?php
namespace CmsmastersElementor\Tags\ACF;

use CmsmastersElementor\Acf_Utils;
use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\ACF\Traits\ACF_Group;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters gallery.
 *
 * Retrieves gallery field data from an advanced custom field.
 *
 * @since 1.0.0
 */
class Gallery extends Data_Tag {

	use Base_Tag, ACF_Group;

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
		return 'gallery';
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
		return __( 'Gallery Field', 'cmsmasters-elementor' );
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
	* Register controls.
	*
	* Registers the controls of the dynamic tag.
	*
	* @since 1.0.0
	* @since 1.1.0 Code refactoring.
	*/
	protected function register_controls() {
		Acf_Utils::add_key_control( $this );
	}

	/**
	* Get value.
	*
	* Returns out the value of the dynamic data tag.
	*
	* @since 1.0.0
	* @since 1.1.0 Code refactoring.
	* @since 1.2.0 Fixed undefined offset.
	*/
	public function get_value( array $options = array() ) {
		list( , $value ) = array_pad( Acf_Utils::get_key_field( $this ), 2, false );

		$gallery = array();

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $image ) {
				$gallery[] = array( 'id' => $image['ID'] );
			}
		}

		return $gallery;
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
		return array( 'gallery' );
	}

}
