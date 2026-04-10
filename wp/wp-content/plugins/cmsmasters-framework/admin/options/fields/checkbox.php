<?php
namespace CmsmastersFramework\Admin\Options\Fields;

use CmsmastersFramework\Admin\Options\Options_Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Handler class is responsible for options field.
 *
 * @since 1.0.0
 */
class Checkbox {

	/**
	 * Render field.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field_args Field args.
	 */
	public static function render( $field_args = array() ) {
		foreach ( $field_args as $field_key => $field_value ) {
			$$field_key = $field_value;
		}

		echo '<div class="cmsmasters-options-field-' . esc_attr( $type ) . '">' .
			Options_Utils::get_field_label( $label, $id ) .
			'<input' .
				' class="checkbox"' .
				' type="checkbox"' .
				' id="' . esc_attr( $id ) . '"' .
				' name="' . esc_attr( $name ) . '"' .
				' value="1"' .
				checked( $value, '1', false ) .
			'/>' .
			Options_Utils::get_field_postfix( $postfix ) .
			Options_Utils::get_field_desc( $desc ) .
		'</div>';
	}

	/**
	 * Validate field value.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Field id.
	 * @param string $value Field value.
	 * @param array $args Field args.
	 *
	 * @return string Validated value.
	 */
	public static function validate( $id, $value, $args ) {
		return ( isset( $value ) && '1' === $value ? '1' : '0' );
	}

}
