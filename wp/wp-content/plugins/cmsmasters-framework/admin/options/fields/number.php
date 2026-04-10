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
class Number {

	/**
	 * Render field.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field_args Field args.
	 */
	public static function render( $field_args = array() ) {
		$field_args = wp_parse_args( $field_args, array(
			'min' => '',
			'max' => '',
			'step' => '',
		) );

		foreach ( $field_args as $field_key => $field_value ) {
			$$field_key = $field_value;
		}

		echo '<div class="cmsmasters-options-field-' . esc_attr( $type ) . '">' .
			Options_Utils::get_field_label( $label, $id ) .
			'<input' .
				' class="small-text"' .
				' type="number"' .
				' id="' . esc_attr( $id ) . '"' .
				' name="' . esc_attr( $name ) . '"' .
				' value="' . esc_attr( $value ) . '"' .
				' min="' . esc_attr( $min ) . '"' .
				' max="' . esc_attr( $max ) . '"' .
				' step="' . esc_attr( $step ) . '"' .
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
		$out = '';

		$value = trim( $value );

		$out = ( is_numeric( $value ) ? $value : esc_html__( 'Number!', 'cmsmasters-framework' ) );

		if ( false === is_numeric( $value ) ) {
			add_settings_error(
				$id,
				'cmsmasters_txt_numeric_error',
				esc_html__( 'Expecting a Numeric value! Please fix.', 'cmsmasters-framework' ),
				'error'
			);
		}

		return $out;
	}

}
