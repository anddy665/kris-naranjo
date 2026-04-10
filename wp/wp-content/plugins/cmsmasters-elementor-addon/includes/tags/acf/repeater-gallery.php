<?php
namespace CmsmastersElementor\Tags\ACF;

use CmsmastersElementor\Acf_Utils;
use CmsmastersElementor\Tags\ACF\Gallery;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters repeater gallery.
 *
 * Retrieves repeater gallery field data from an advanced custom field.
 *
 * @since 1.2.0
 */
class Repeater_Gallery extends Gallery {

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
		return __( 'Repeater Gallery Field', 'cmsmasters-elementor' );
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
	}

	/**
	* Get value.
	*
	* Returns out the value of the dynamic data tag.
	*
	* @since 1.2.0
	*/
	public function get_value( array $options = array() ) {
		list( , $repeater_values ) = array_pad( Acf_Utils::get_key_field( $this ), 2, false );

		$values_array = array();

		foreach ( $repeater_values as $value ) {
			if ( is_array( $value ) && ! empty( $value ) ) {
				$gallery = array();

				foreach ( $value as $image ) {
					$gallery[] = array( 'id' => $image['ID'] );
				}

				$values_array[] = $gallery;
			}
		}

		return wp_json_encode( $values_array );
	}

}
