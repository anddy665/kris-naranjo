<?php
namespace CmsmastersElementor\Tags\ACF;

use CmsmastersElementor\Acf_Utils;
use CmsmastersElementor\Tags\ACF\Number;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon repeater number.
 *
 * Retrieves repeater number field data from an advanced custom field.
 *
 * @since 1.2.0
 */
class Repeater_Number extends Number {

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
		return __( 'Repeater Number Field', 'cmsmasters-elementor' );
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
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.2.0
	*/
	public function render() {
		list( , $repeater_values ) = array_pad( Acf_Utils::get_key_field( $this ), 2, false );

		$values_array = array();

		foreach ( $repeater_values as $value ) {
			$values_array[] = $value;
		}

		echo wp_json_encode( array_map(
			function( $result_item ) {
				return wp_kses_post( $result_item );
			},
			$values_array
		) );
	}

}
