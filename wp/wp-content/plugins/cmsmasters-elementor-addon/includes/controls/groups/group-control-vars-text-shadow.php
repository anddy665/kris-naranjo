<?php
namespace CmsmastersElementor\Controls\Groups;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Group_Control_Text_Shadow;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon text shadow vars control.
 *
 * Customized Elementor `text-shadow` control with css vars in selectors.
 *
 * @since 1.1.0
 */
class Group_Control_Vars_Text_Shadow extends Group_Control_Text_Shadow {

	/**
	 * Get text shadow control type.
	 *
	 * Retrieve the control type, in this case `text-shadow`.
	 *
	 * @since 1.1.0
	 * @access public
	 * @static
	 *
	 * @return string Control type.
	 */
	public static function get_type() {
		return CmsmastersControls::VARS_TEXT_SHADOW_GROUP;
	}

	/**
	 * Prepare fields.
	 *
	 * Process text shadow control fields before adding them to `add_control()`.
	 *
	 * @since 1.1.0
	 * @access protected
	 *
	 * @param array $fields Text shadow control fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {
		$prefix = '--' . str_replace( '_', '-', $this->get_controls_prefix() );

		array_walk( $fields, function( &$field, $field_name ) use ( $prefix ) {
			switch ( $field_name ) {
				case 'text_shadow':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};",
					);

					break;
			}
		} );

		return parent::prepare_fields( $fields );
	}

}
