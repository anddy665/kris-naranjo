<?php
namespace PediatricClinicSpace\Kits\Traits\VarGroupControls;

use Elementor\Group_Control_Box_Shadow;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Box Shadow trait.
 *
 * Allows to use a group control box-shadow with css vars.
 */
trait Box_Shadow {

	/**
	 * Add box-shadow group control with css vars.
	 *
	 * @param string $key Control key.
	 * @param array $args Control arguments.
	 */
	protected function var_group_control_box_shadow( $key = '', $args = array() ) {
		list( $name, $prefix ) = $this->get_control_parameters( $key, 'box_shadow' );

		$default_args = array(
			'name' => $name,
			'fields_options' => array(
				'box_shadow' => array(
					'selectors' => array(
						':root' => "--{$prefix}-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};",
					),
				),
			),
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array_replace_recursive( $default_args, $args )
		);
	}

}
