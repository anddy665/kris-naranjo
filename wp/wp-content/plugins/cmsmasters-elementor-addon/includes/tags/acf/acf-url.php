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
 * CMSMasters url.
 *
 * Retrieves url field data from an advanced custom field.
 *
 * @since 1.0.0
 */
class ACF_URL extends Data_Tag {

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
		return __( 'URL Field', 'cmsmasters-elementor' );
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
			array( 'label' => __( 'Fallback', 'cmsmasters-elementor' ) )
		);
	}

	/**
	* Get value.
	*
	* Returns out the value of the dynamic data tag.
	*
	* @since 1.0.0
	* @since 1.1.0 Code refactoring, added supported ACF Pro field types render.
	* @since 1.2.0 Fixed undefined offset.
	*/
	public function get_value( array $options = array() ) {
		list( $field, $value ) = array_pad( Acf_Utils::get_key_field( $this ), 2, false );

		if ( $field && ! empty( $field['type'] ) ) {
			if ( is_array( $value ) && isset( $value[0] ) && 'url' !== $field['type'] ) {
				$value = $value[0];
			}

			if ( $value ) {
				if ( ! isset( $field['return_format'] ) ) {
					$field['return_format'] = isset( $field['save_format'] ) ? $field['save_format'] : '';
				}

				switch ( $field['type'] ) {
					case 'email':
						if ( $value ) {
							$value = 'mailto:' . $value;
						}

						break;
					case 'image':
					case 'file':
						switch ( $field['return_format'] ) {
							case 'object':
							case 'array':
								$value = $value['url'];

								break;
							case 'id':
								if ( 'image' === $field['type'] ) {
									$src = wp_get_attachment_image_src( $value, 'full' );

									$value = $src[0];
								} else {
									$value = wp_get_attachment_url( $value );
								}

								break;
						}

						break;
					case 'post_object':
					case 'relationship':
						$value = get_permalink( $value );

						break;
					case 'taxonomy':
						$value = get_term_link( $value, $field['taxonomy'] );

						break;
					case 'oembed':
						preg_match( '/src="(.+?)"/', $value, $src_matches );

						$value = $src_matches[1];

						break;
				}
			}
		}

		if ( empty( $value ) && $this->get_settings( 'fallback' ) ) {
			$value = $this->get_settings( 'fallback' );
		}

		$value = is_string( $value ) ? $value : '';

		return wp_kses_post( $value );
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
			'email',
			'image',
			'file',
			'page_link',
			'post_object',
			'relationship',
			'taxonomy',
			'url',

			// ACF Pro
			'oembed',
		);
	}

}
