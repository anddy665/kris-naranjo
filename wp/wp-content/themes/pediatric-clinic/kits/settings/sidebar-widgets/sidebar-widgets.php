<?php
namespace PediatricClinicSpace\Kits\Settings\SidebarWidgets;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Sidebar Widgets settings.
 */
class Sidebar_Widgets extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'sidebar_widgets';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Widget Container', 'pediatric-clinic' );
	}

	/**
	 * Get control ID prefix.
	 *
	 * Retrieve the control ID prefix.
	 *
	 * @return string Control ID prefix.
	 */
	protected static function get_control_id_prefix() {
		$toggle_name = self::get_toggle_name();

		return parent::get_control_id_prefix() . "_{$toggle_name}";
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$this->add_controls_group( 'box', self::CONTROLS_CONTAINER_BOX, array(
			'popover' => false,
			'excludes' => array(
				'alignment',
				'margin',
			),
		) );

		$this->add_responsive_control(
			'box_margin',
			array(
				'label' => esc_html__( 'Margin', 'pediatric-clinic' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
				),
				'allowed_dimensions' => array( 'top', 'bottom' ),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'box_margin_top' ) . ': {{TOP}}{{UNIT}};' .
						'--' . $this->get_control_prefix_parameter( '', 'box_margin_bottom' ) . ': {{BOTTOM}}{{UNIT}};',
				),
			)
		);
	}

}
