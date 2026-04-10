<?php
namespace CmsmastersElementor\Controls\Groups;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Utils;

use Elementor\Group_Control_Background;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon background vars control.
 *
 * Customized Elementor `background` control with css vars in selectors.
 *
 * @since 1.1.0
 */
class Group_Control_Vars_Background extends Group_Control_Background {

	/**
	 * Get background control type.
	 *
	 * Retrieve the control type, in this case `background`.
	 *
	 * @since 1.1.0
	 * @access public
	 * @static
	 *
	 * @return string Control type.
	 */
	public static function get_type() {
		return CmsmastersControls::VARS_BACKGROUND_GROUP;
	}

	/**
	 * Prepare fields.
	 *
	 * Process background control fields before adding them to `add_control()`.
	 *
	 * @since 1.1.0
	 * @since 1.2.3 Fixed error with responsive controls in elementor 3.4.0.
	 * @since 1.3.3 Changed responsive controls for Elementor custom breakpoints.
	 * @access protected
	 *
	 * @param array $fields Background control fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {
		$prefix = '--' . str_replace( '_', '-', $this->get_controls_prefix() );

		array_walk( $fields, function( &$field, $field_name ) use ( $prefix ) {
			switch ( $field_name ) {
				case 'color':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}color: {{VALUE}};",
					);

					break;

				case 'gradient_angle':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}color: transparent;" .
						"{$prefix}image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});",
					);

					break;

				case 'gradient_position':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}color: transparent;" .
						"{$prefix}image: radial-gradient(at {{VALUE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});",
					);

					break;

				case 'image':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}image: url(\"{{URL}}\");",
					);

					break;

				case 'position':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}position: {{VALUE}};",
					);

					break;

				case 'xpos':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}position: {{SIZE}}{{UNIT}} {{ypos.SIZE}}{{ypos.UNIT}};",
					);

					$field['device_args'] = Utils::get_devices_args( array(
						'selectors' => array(
							'{{SELECTOR}}' => "{$prefix}position: {{SIZE}}{{UNIT}} {{ypos_{{cmsmasters_device}}.SIZE}}{{ypos_{{cmsmasters_device}}.UNIT}};",
						),
					) );

					break;

				case 'ypos':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}position: {{xpos.SIZE}}{{xpos.UNIT}} {{SIZE}}{{UNIT}};",
					);

					$field['device_args'] = Utils::get_devices_args( array(
						'selectors' => array(
							'{{SELECTOR}}' => "{$prefix}position: {{xpos_{{cmsmasters_device}}.SIZE}}{{xpos_{{cmsmasters_device}}.UNIT}} {{SIZE}}{{UNIT}};",
						),
					) );

					break;

				case 'attachment':
					$field['selectors'] = array(
						'(desktop+){{SELECTOR}}' => "{$prefix}attachment: {{VALUE}};",
					);

					break;

				case 'repeat':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}repeat: {{VALUE}};",
					);

					break;

				case 'size':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}size: {{VALUE}};",
					);

					break;

				case 'bg_width':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}size: {{SIZE}}{{UNIT}} auto;",
					);

					$field['device_args'] = Utils::get_devices_args( array(
						'selectors' => array(
							'{{SELECTOR}}' => "{$prefix}size: {{SIZE}}{{UNIT}} auto;",
						),
					) );

					break;

				case 'video_fallback':
					$field['selectors'] = array(
						'{{SELECTOR}}' => "{$prefix}: url(\"{{URL}}\") 50% 50%;" .
						"{$prefix}size: cover;",
					);

					break;
			}
		} );

		return parent::prepare_fields( $fields );
	}

}
