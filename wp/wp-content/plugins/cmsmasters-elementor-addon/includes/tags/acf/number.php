<?php
namespace CmsmastersElementor\Tags\ACF;

use CmsmastersElementor\Acf_Utils;
use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\ACF\Traits\ACF_Group;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters number.
 *
 * Retrieves number field data from an advanced custom field.
 *
 * @since 1.0.0
 */
class Number extends Tag {

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
		return 'number';
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
		return __( 'Number Field', 'cmsmasters-elementor' );
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
		return array(
			TagsModule::NUMBER_CATEGORY,
			TagsModule::POST_META_CATEGORY,
		);
	}

	/**
	* Get panel template setting key.
	*
	* Returns the tag key using a Backbone JavaScript template.
	*
	* @since 1.0.0
	*
	* @return array Tag key.
	*/
	public function get_panel_template_setting_key() {
		return 'key';
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
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.0.0
	* @since 1.1.0 Code refactoring.
	* @since 1.2.0 Fixed undefined offset.
	*/
	public function render() {
		list( , $value ) = array_pad( Acf_Utils::get_key_field( $this ), 2, false );

		echo wp_kses_post( $value );
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
		return array( 'number' );
	}
}
