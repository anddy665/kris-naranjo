<?php
namespace PediatricClinicSpace\Kits\Traits\VarGroupControls;

use Elementor\Group_Control_Text_Shadow;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Text Shadow trait.
 *
 * Allows to use a group control text-shadow with css vars.
 */
trait Text_Shadow {

	/**
	 * Add text-shadow group control with css vars.
	 *
	 * @param string $key Control key.
	 * @param array $args Control args.
	 */
	protected function var_group_control_text_shadow( $key = '', $args = array() ) {
		list( $name, $prefix ) = $this->get_control_parameters( $key, 'text_shadow' );

		$default_args = array(
			'name' => $name,
			'fields_options' => array(
				'text_shadow' => array(
					'selectors' => array(
						':root' => "--{$prefix}-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};",
					),
				),
			),
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array_replace_recursive( $default_args, $args )
		);
	}

}
