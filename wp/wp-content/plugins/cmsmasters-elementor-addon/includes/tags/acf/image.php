<?php
namespace CmsmastersElementor\Tags\ACF;

use CmsmastersElementor\Acf_Utils;
use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\ACF\Traits\ACF_Group;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters image.
 *
 * Retrieves image field data from an advanced custom field.
 *
 * @since 1.0.0
 */
class Image extends Data_Tag {

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
		return 'image';
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
		return __( 'Image Field', 'cmsmasters-elementor' );
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

		$this->add_control(
			'fallback',
			array(
				'label' => __( 'Fallback', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
			)
		);
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
		list( $field, $value ) = array_pad( Acf_Utils::get_key_field( $this ), 2, false );

		$image_data = array(
			'id' => null,
			'url' => '',
		);

		if ( $field && is_array( $field ) ) {
			if ( isset( $field['save_format'] ) ) {
				$field['return_format'] = $field['save_format'];
			}

			switch ( $field['return_format'] ) {
				case 'url':
					$value = array(
						'id' => 0,
						'url' => $value,
					);

					break;
				case 'id':
					$src = wp_get_attachment_image_src( $value, $field['preview_size'] );

					$value = array(
						'id' => $value,
						'url' => $src[0],
					);

					break;
			}
		}

		if ( empty( $value ) && $this->get_settings( 'fallback' ) ) {
			$value = $this->get_settings( 'fallback' );
		}

		if ( ! empty( $value ) && is_array( $value ) ) {
			$image_data['id'] = $value['id'];
			$image_data['url'] = $value['url'];
		}

		return $image_data;
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
		return array( 'image' );
	}

}
