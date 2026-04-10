<?php
namespace PediatricClinicSpace\Kits\Settings\HeaderTop;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Header Top Navigation Title Item settings.
 */
class Nav_Title_Item extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'header_top_nav_title_item';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Navigation Title Item', 'pediatric-clinic' );
	}

	/**
	 * Get control ID prefix.
	 *
	 * Retrieve the control ID prefix.
	 *
	 * @return string Control ID prefix.
	 */
	protected static function get_control_id_prefix() {
		return parent::get_control_id_prefix() . '_header_top';
	}

	/**
	 * Get toggle conditions.
	 *
	 * Retrieve the settings toggle conditions.
	 *
	 * @return array Toggle conditions.
	 */
	protected function get_toggle_conditions() {
		return array(
			'condition' => array(
				$this->get_control_id_parameter( '', 'visibility' ) => 'yes',
				$this->get_control_id_parameter( '', 'elements' ) => 'nav',
			),
		);
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$this->add_controls_group( 'nav_title_item', self::CONTROLS_NAV_TITLE_ITEM, array(
			'states' => array(
				'normal' => esc_html__( 'Normal', 'pediatric-clinic' ),
				'hover' => esc_html__( 'Hover', 'pediatric-clinic' ),
				'current' => esc_html__( 'Current', 'pediatric-clinic' ),
			),
		) );
	}

}
