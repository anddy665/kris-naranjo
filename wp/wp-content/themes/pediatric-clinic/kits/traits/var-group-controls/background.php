<?php
namespace PediatricClinicSpace\Kits\Traits\VarGroupControls;

use PediatricClinicSpace\Core\Utils\Utils;

use Elementor\Group_Control_Background;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Background trait.
 *
 * Allows to use a group control background with css vars.
 */
trait Background {

	/**
	 * Add background group control with css vars.
	 *
	 * @param string $key Control key.
	 * @param array $args Control arguments.
	 */
	protected function var_group_control_background( $key = '', $args = array() ) {
		$devices = Utils::get_devices();

		list( $name, $prefix ) = $this->get_control_parameters( $key, 'background', 'bg' );

		$default_args = array(
			'name' => $name,
			'types' => array(
				'classic',
				'gradient',
			),
			'fields_options' => array(
				'background' => array(
					'frontend_available' => true,
				),
				'color' => array(
					'dynamic' => array(),
					'selectors' => array(
						':root' => "--{$prefix}-color: {{VALUE}};",
					),
				),
				'color_b' => array(
					'dynamic' => array(),
					'default' => '',
				),
				'gradient_angle' => array(
					'selectors' => array(
						':root' => "--{$prefix}-color: transparent;" .
							"--{$prefix}-repeat: repeat;" .
							"--{$prefix}-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});",
					),
				),
				'gradient_position' => array(
					'selectors' => array(
						':root' => "--{$prefix}-color: transparent;" .
							"--{$prefix}-repeat: repeat;" .
							"--{$prefix}-image: radial-gradient(at {{VALUE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}});",
					),
				),
				'image' => array(
					'selectors' => array(
						':root' => "--{$prefix}-image: url(\"{{URL}}\");",
					),
				),
				'position' => array(
					'selectors' => array(
						':root' => "--{$prefix}-position: {{VALUE}};",
					),
				),
				'xpos' => array(
					'selectors' => array(
						':root' => "--{$prefix}-position: {{SIZE}}{{UNIT}} {{ypos.SIZE}}{{ypos.UNIT}};",
					),
					'device_args' => array(
						$devices['tablet'] => array(
							'selectors' => array(
								':root' => "--{$prefix}-position: {{SIZE}}{{UNIT}} {{ypos_tablet.SIZE}}{{ypos_tablet.UNIT}};",
							),
						),
						$devices['mobile'] => array(
							'selectors' => array(
								':root' => "--{$prefix}-position: {{SIZE}}{{UNIT}} {{ypos_mobile.SIZE}}{{ypos_mobile.UNIT}};",
							),
						),
					),
				),
				'ypos' => array(
					'selectors' => array(
						':root' => "--{$prefix}-position: {{xpos.SIZE}}{{xpos.UNIT}} {{SIZE}}{{UNIT}};",
					),
					'device_args' => array(
						$devices['tablet'] => array(
							'selectors' => array(
								':root' => "--{$prefix}-position: {{xpos_tablet.SIZE}}{{xpos_tablet.UNIT}} {{SIZE}}{{UNIT}};",
							),
						),
						$devices['mobile'] => array(
							'selectors' => array(
								':root' => "--{$prefix}-position: {{xpos_mobile.SIZE}}{{xpos_mobile.UNIT}} {{SIZE}}{{UNIT}};",
							),
						),
					),
				),
				'attachment' => array(
					'selectors' => array(
						':root' => "--{$prefix}-attachment: {{VALUE}};",
					),
				),
				'repeat' => array(
					'selectors' => array(
						':root' => "--{$prefix}-repeat: {{VALUE}};",
					),
				),
				'size' => array(
					'selectors' => array(
						':root' => "--{$prefix}-size: {{VALUE}};",
					),
				),
				'bg_width' => array(
					'selectors' => array(
						':root' => "--{$prefix}-size: {{SIZE}}{{UNIT}} auto;",
					),
					'device_args' => array(
						$devices['tablet'] => array(
							'selectors' => array(
								':root' => "--{$prefix}-size: {{SIZE}}{{UNIT}} auto;",
							),
						),
						$devices['mobile'] => array(
							'selectors' => array(
								':root' => "--{$prefix}-size: {{SIZE}}{{UNIT}} auto;",
							),
						),
					),
				),
			),
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array_replace_recursive( $default_args, $args )
		);
	}

}
