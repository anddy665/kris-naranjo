<?php
namespace CmsmastersElementor\Controls\Groups;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Group_Control_Typography;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon typography vars control.
 *
 * Customized Elementor `typography` control with css vars in selectors.
 *
 * @since 1.1.0
 */
class Group_Control_Vars_Typography extends Group_Control_Typography {

	/**
	 * Get typography control type.
	 *
	 * Retrieve the control type, in this case `typography`.
	 *
	 * @since 1.1.0
	 * @access public
	 * @static
	 *
	 * @return string Control type.
	 */
	public static function get_type() {
		return CmsmastersControls::VARS_TYPOGRAPHY_GROUP;
	}

	/**
	 * Prepare fields.
	 *
	 * Process typography control fields before adding them to `add_control()`.
	 *
	 * @since 1.1.0
	 * @access protected
	 *
	 * @param array $fields Typography control fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {
		$prefix = '--' . $this->get_controls_prefix();

		array_walk( $fields, function( &$field, $field_name ) use ( $prefix ) {
			if ( in_array( $field_name, [ 'typography', 'popover_toggle' ], true ) ) {
				return;
			}

			$selector_value = ! empty( $field['selector_value'] ) ? $field['selector_value'] : $field_name . ': {{VALUE}};';

			$selector_value = str_replace( '_', '-', $prefix . $selector_value );

			$field['selector_value'] = $selector_value;
		} );

		return parent::prepare_fields( $fields );
	}

}
