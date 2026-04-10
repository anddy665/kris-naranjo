<?php
namespace CmsmastersElementor\Controls\Groups;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Group_Control_Box_Shadow;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon box shadow vars control.
 *
 * Customized Elementor `box-shadow` control with css vars in selectors.
 *
 * @since 1.1.0
 */
class Group_Control_Vars_Box_Shadow extends Group_Control_Box_Shadow {

	/**
	 * Get box shadow control type.
	 *
	 * Retrieve the control type, in this case `box-shadow`.
	 *
	 * @since 1.1.0
	 * @access public
	 * @static
	 *
	 * @return string Control type.
	 */
	public static function get_type() {
		return CmsmastersControls::VARS_BOX_SHADOW_GROUP;
	}

	/**
	 * Prepare fields.
	 *
	 * Process box shadow control fields before adding them to `add_control()`.
	 *
	 * @since 1.1.0
	 * @access protected
	 *
	 * @param array $fields Box shadow control fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {
		$prefix = '--' . str_replace( '_', '-', $this->get_controls_prefix() );

		array_walk( $fields, function( &$field, $field_name ) use ( $prefix ) {
			switch ( $field_name ) {
				case 'box_shadow':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};",
					);

					break;
			}
		} );

		return parent::prepare_fields( $fields );
	}

}
