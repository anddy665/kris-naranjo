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
class Constructor {

	/**
	 * Render field.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field_args Field args.
	 */
	public static function render( $field_args = array() ) {
		$field_args = wp_parse_args( $field_args, array(
			'view' => 'horizontal',
			'items' => array(),
		) );

		foreach ( $field_args as $field_key => $field_value ) {
			$$field_key = $field_value;
		}

		$parent_class = "cmsmasters-options-field-{$type}";

		echo '<div class="' . esc_attr( $parent_class ) . ' cmsmasters-' . esc_attr( $view ) . '">' .
			'<div class="' . esc_attr( $parent_class ) . '-inner">';

		foreach ( $items as $item_key => $item_args ) {
			if ( ! isset( $value[ $item_key ] ) ) {
				$value[ $item_key ] = $std[ $item_key ];
			}

			$item_args = wp_parse_args( $item_args, array(
				'id' => $id . '_' . $item_key,
				'name' => $name . '[' . $item_key . ']',
				'value' => $value[ $item_key ],
				'label' => '',
				'type' => 'text',
				'subtype' => '',
				'desc' => '',
				'postfix' => '',
			) );

			$class_name = __NAMESPACE__ . '\\' . ucwords( str_replace( '-', '_', $item_args['type'] ), '_' );

			if ( ! class_exists( $class_name ) ) {
				continue;
			}

			$class_name::render( $item_args );
		}

			echo '</div>' .
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
		$out = $value;

		foreach ( $args['items'] as $item_id => $item_args ) {
			$item_args = wp_parse_args( $item_args, array(
				'subtype' => '',
				'not_empty' => false,
				'std' => $args['std'][ $item_id ],
			) );

			$class_name = __NAMESPACE__ . '\\' . ucwords( str_replace( '-', '_', $item_args['type'] ), '_' );

			$input_val = Options_Utils::check_validate_input( $item_id, '', $value, $item_args );

			if ( ! class_exists( $class_name ) ) {
				$out[ $item_id ] = $input_val;

				continue;
			}

			$out[ $item_id ] = $class_name::validate( $id . '_' . $item_id, $input_val, $item_args );
		}

		return $out;
	}

}
