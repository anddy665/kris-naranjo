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
 * CMSMasters text.
 *
 * Retrieves text field data from an advanced custom field.
 *
 * @since 1.0.0
 */
class Text extends Tag {

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
		return 'text';
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
		return __( 'Field', 'cmsmasters-elementor' );
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
			TagsModule::TEXT_CATEGORY,
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
	* @since 1.1.0 Code refactoring, added supported ACF Pro field types render.
	* @since 1.2.0 Fixed undefined offset.
	*/
	public function render() {
		list( $field, $value, $meta_key ) = array_pad( Acf_Utils::get_key_field( $this ), 3, false );

		if ( $field && ! empty( $field['type'] ) ) {
			switch ( $field['type'] ) {
				case 'radio':
					if ( isset( $field['choices'][ $value ] ) ) {
						$value = $field['choices'][ $value ];
					}

					break;
				case 'select':
					$values = (array) $value;

					foreach ( $values as $key => $item ) {
						if ( isset( $field['choices'][ $item ] ) ) {
							$values[ $key ] = $field['choices'][ $item ];
						}
					}

					$value = implode( ', ', $values );

					break;
				case 'checkbox':
					$values = array();

					foreach ( (array) $value as $item ) {
						if ( isset( $field['choices'][ $item ] ) ) {
							$values[] = $field['choices'][ $item ];
						} else {
							$values[] = $item;
						}
					}

					$value = implode( ', ', $values );

					break;
				case 'oembed':
					$value = $this->get_queried_object_meta( $meta_key );

					break;
				case 'google_map':
					$meta = $this->get_queried_object_meta( $meta_key );
					$value = isset( $meta['address'] ) ? $meta['address'] : '';

					break;
			}
		}

		echo wp_kses_post( $value );
	}

	/**
	* Get supported fields.
	*
	* Returns an array of tag supported fields.
	*
	* @since 1.0.0
	* @since 1.1.0 Added supported ACF Pro field types.
	*
	* @return array Supported tag fields.
	*/
	public function get_supported_fields() {
		return array(
			'text',
			'textarea',
			'number',
			'email',
			'password',
			'wysiwyg',
			'select',
			'checkbox',
			'radio',
			'true_false',

			// ACF Pro
			'color_picker',
			'date_picker',
			'time_picker',
			'date_time_picker',
			'oembed',
			'google_map',
		);
	}

	/**
	 * Get queried object meta.
	 *
	 * Retrieves selected meta field value by current data type.
	 *
	 * @since 1.1.0
	 *
	 * @param string $meta_key Meta field key.
	 *
	 * @return string Selected meta field value.
	 */
	protected function get_queried_object_meta( $meta_key ) {
		$value = '';

		if ( is_singular() ) {
			$value = get_post_meta( get_the_ID(), $meta_key, true );
		} elseif ( is_tax() || is_category() || is_tag() ) {
			$value = get_term_meta( get_queried_object_id(), $meta_key, true );
		}

		return $value;
	}

}
