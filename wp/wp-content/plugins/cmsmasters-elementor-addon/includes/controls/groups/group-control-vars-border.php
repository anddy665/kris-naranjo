<?php
namespace CmsmastersElementor\Controls\Groups;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Group_Control_Border;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon border vars control.
 *
 * Customized Elementor `border` control with css vars in selectors.
 *
 * @since 1.1.0
 */
class Group_Control_Vars_Border extends Group_Control_Border {

	/**
	 * Get border control type.
	 *
	 * Retrieve the control type, in this case `border`.
	 *
	 * @since 1.1.0
	 * @access public
	 * @static
	 *
	 * @return string Control type.
	 */
	public static function get_type() {
		return CmsmastersControls::VARS_BORDER_GROUP;
	}

	/**
	 * Prepare fields.
	 *
	 * Process border control fields before adding them to `add_control()`.
	 *
	 * @since 1.1.0
	 * @access protected
	 *
	 * @param array $fields Border control fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {
		$prefix = '--' . str_replace( '_', '-', $this->get_controls_prefix() );

		array_walk( $fields, function( &$field, $field_name ) use ( $prefix ) {
			switch ( $field_name ) {
				case 'border':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}style: {{VALUE}};",
					);

					break;

				case 'width':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}width-top: {{TOP}}{{UNIT}};" .
							"{$prefix}width-right: {{RIGHT}}{{UNIT}};" .
							"{$prefix}width-bottom: {{BOTTOM}}{{UNIT}};" .
							"{$prefix}width-left: {{LEFT}}{{UNIT}};",
					);

					break;

				case 'color':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}color: {{VALUE}};",
					);

					break;
			}
		} );

		return parent::prepare_fields( $fields );
	}

}
