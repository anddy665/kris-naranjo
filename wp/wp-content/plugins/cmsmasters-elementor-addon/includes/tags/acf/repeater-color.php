<?php
namespace CmsmastersElementor\Tags\ACF;

use CmsmastersElementor\Acf_Utils;
use CmsmastersElementor\Tags\ACF\Color;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon repeater color.
 *
 * Retrieves repeater color field data from an advanced custom field.
 *
 * @since 1.2.0
 */
class Repeater_Color extends Color {

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
		return __( 'Repeater Color Field', 'cmsmasters-elementor' );
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
				'type' => Controls_Manager::COLOR,
				'dynamic' => false,
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
		list( , $repeater_values ) = array_pad( Acf_Utils::get_key_field( $this ), 2, false );

		$values_array = array();

		$fallback = $this->get_settings( 'fallback' );

		foreach ( $repeater_values as $value ) {
			if ( ! empty( $value ) ) {
				$values_array[] = $value;
			} elseif ( $fallback ) {
				$values_array[] = $fallback;
			}
		}

		return wp_json_encode( $values_array );
	}

}
