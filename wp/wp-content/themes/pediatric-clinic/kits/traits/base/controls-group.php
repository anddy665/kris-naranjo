<?php
namespace PediatricClinicSpace\Kits\Traits\Base;

use PediatricClinicSpace\Kits\Traits\ControlsGroups;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Base trait for controls groups.
 */
trait Controls_Group {

	use ControlsGroups\Archive,
		ControlsGroups\Button_Icon,
		ControlsGroups\Buttons,
		ControlsGroups\Container,
		ControlsGroups\Container_Box,
		ControlsGroups\Content,
		ControlsGroups\Custom_HTML,
		ControlsGroups\Icon,
		ControlsGroups\Nav_Burger_Container,
		ControlsGroups\Nav_Dropdown_Container,
		ControlsGroups\Nav_Dropdown_Item,
		ControlsGroups\Nav_Item,
		ControlsGroups\Nav_Title_Item,
		ControlsGroups\Post_Content,
		ControlsGroups\Post_Media,
		ControlsGroups\Post_Meta,
		ControlsGroups\Post_Title,
		ControlsGroups\Quotes,
		ControlsGroups\Short_Info,
		ControlsGroups\Slider,
		ControlsGroups\Social_Icons,
		ControlsGroups\States;

	/**
	 * Add controls group.
	 *
	 * Creates predefined group of controls.
	 *
	 * @param string $key Controls key.
	 * @param string $type Controls type.
	 * @param array $args Controls arguments.
	 */
	protected function add_controls_group( $key = '', $type = '', $args = array() ) {
		if ( '' === $type ) {
			return;
		}

		call_user_func( array( $this, "controls_group_{$type}" ), $key, $args );
	}

}
