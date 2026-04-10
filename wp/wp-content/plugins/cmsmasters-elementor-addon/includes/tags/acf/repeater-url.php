<?php
namespace CmsmastersElementor\Tags\ACF;

use CmsmastersElementor\Acf_Utils;
use CmsmastersElementor\Tags\ACF\ACF_URL;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon repeater url.
 *
 * Retrieves repeater url field data from an advanced custom field.
 *
 * @since 1.2.0
 */
class Repeater_URL extends ACF_URL {

	/**
	* Get tag name.
	*
	* Returns the name of the dynamic tag.
	*
	* @since 1.2.0
	*
	* @return string Tag name.
	*/
	public static function tag_name() {
		return 'repeater-' . parent::tag_name();
	}

	/**
	* Get tag title.
	*
	* Returns the title of the dynamic tag.
	*
	* @since 1.2.0
	*
	* @return string Tag title.
	*/
	public static function tag_title() {
		return __( 'Repeater URL Field', 'cmsmasters-elementor' );
	}

	/**
	* Register controls.
	*
	* Registers the controls of the dynamic tag.
	*
	* @since 1.2.0
	*/
	protected function register_controls() {
		Acf_Utils::add_key_control( $this, true );

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
	* @since 1.2.0
	*/
	public function get_value( array $options = array() ) {
		list( $field, $repeater_values ) = array_pad( Acf_Utils::get_key_field( $this ), 2, false );

		$values_array = array();

		if ( $field && ! empty( $field['type'] ) ) {
			if ( ! isset( $field['return_format'] ) ) {
				$field['return_format'] = isset( $field['save_format'] ) ? $field['save_format'] : '';
			}

			$fallback = $this->get_settings( 'fallback' );

			foreach ( $repeater_values as $value ) {
				if ( is_array( $value ) && isset( $value[0] ) ) {
					$value = $value[0];
				}

				if ( $value ) {
					switch ( $field['type'] ) {
						case 'email':
							if ( $value ) {
								$values_array[] = 'mailto:' . $value;
							}

							break;
						case 'image':
						case 'file':
							switch ( $field['return_format'] ) {
								case 'object':
								case 'array':
									$values_array[] = $value['url'];

									break;
								case 'id':
									if ( 'image' === $field['type'] ) {
										$src = wp_get_attachment_image_src( $value, 'full' );

										$values_array[] = $src[0];
									} else {
										$values_array[] = wp_get_attachment_url( $value );
									}

									break;
							}

							break;
						case 'post_object':
						case 'relationship':
							$values_array[] = get_permalink( $value );

							break;
						case 'taxonomy':
							$values_array[] = get_term_link( $value, $field['taxonomy'] );

							break;
						case 'oembed':
							preg_match( '/src="(.+?)"/', $value, $src_matches );

							$values_array[] = $src_matches[1];

							break;
						default:
							$values_array[] = $value;
					}
				} elseif ( $fallback ) {
					$values_array[] = $fallback;
				}
			}
		}

		return wp_json_encode( array_map(
			function( $result_item ) {
				return wp_kses_post( $result_item );
			},
			$values_array
		) );
	}

}
