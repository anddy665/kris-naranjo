<?php
namespace PediatricClinicSpace\Kits\Traits\Base;

use PediatricClinicSpace\Kits\Traits\VarGroupControls;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Base trait for css variables group controls.
 */
trait Var_Group_Control {

	use VarGroupControls\Background,
		VarGroupControls\Border,
		VarGroupControls\Typography,
		VarGroupControls\Box_Shadow,
		VarGroupControls\Text_Shadow;

	/**
	 * Add var group control.
	 *
	 * Creates group control with predefined css variables.
	 *
	 * @param string $key Control key.
	 * @param string $type Control type.
	 * @param array $args Control arguments.
	 */
	protected function add_var_group_control( $key = '', $type = '', $args = array() ) {
		if ( '' === $type ) {
			return;
		}

		call_user_func( array( $this, "var_group_control_{$type}" ), $key, $args );
	}

}
