<?php
namespace CmsmastersElementor\Tags\ACF;

use CmsmastersElementor\Acf_Utils;
use CmsmastersElementor\Tags\ACF\Image;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters repeater image.
 *
 * Retrieves repeater image field data from an advanced custom field.
 *
 * @since 1.2.0
 */
class Repeater_Image extends Image {

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
		return __( 'Repeater Image Field', 'cmsmasters-elementor' );
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
	* @since 1.2.0
	*/
	public function get_value( array $options = array() ) {
		list( $field, $repeater_values ) = array_pad( Acf_Utils::get_key_field( $this ), 2, false );

		$values_array = array();

		if ( $field && is_array( $field ) ) {
			if ( isset( $field['save_format'] ) ) {
				$field['return_format'] = $field['save_format'];
			}

			$fallback = $this->get_settings( 'fallback' );

			foreach ( $repeater_values as $value ) {
				switch ( $field['return_format'] ) {
					case 'url':
						$values_array[] = array(
							'id' => 0,
							'url' => $value,
						);

						break;
					case 'id':
						$src = wp_get_attachment_image_src( $value, $field['preview_size'] );

						$values_array[] = array(
							'id' => $value,
							'url' => $src[0],
						);

						break;
					default:
						if ( ! empty( $value ) ) {
							$values_array[] = $value;
						} elseif ( $fallback ) {
							$values_array[] = $fallback;
						}
				}
			}
		}

		$images_array = array();

		if ( ! empty( $values_array ) ) {
			foreach ( $values_array as $key => $values_item ) {
				if ( is_array( $values_item ) ) {
					$images_array[ $key ]['id'] = $values_item['id'];
					$images_array[ $key ]['url'] = $values_item['url'];
				}
			}
		}

		if ( empty( $images_array ) ) {
			$images_array[] = array(
				'id' => null,
				'url' => '',
			);
		}

		return wp_json_encode( $images_array );
	}

}
