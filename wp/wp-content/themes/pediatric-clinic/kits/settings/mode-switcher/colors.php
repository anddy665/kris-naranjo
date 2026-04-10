<?php
namespace PediatricClinicSpace\Kits\Settings\ModeSwitcher;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;
use Elementor\Plugin as Elementor_Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Mode Switcher Colors settings.
 */
class Colors extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'mode_switcher_colors';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Colors', 'pediatric-clinic' );
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
		$this->add_control(
			'heading_control',
			array(
				'label' => __( 'Colors to Change', 'pediatric-clinic' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$kit = Elementor_Plugin::$instance->kits_manager->get_active_kit_for_frontend();

		$system_items = $kit->get_settings_for_display( 'system_colors' );
		$custom_items = $kit->get_settings_for_display( 'custom_colors' );

		if ( ! $system_items ) {
			$system_items = array();
		}

		if ( ! $custom_items ) {
			$custom_items = array();
		}

		$global_colors_list = array_merge( $system_items, $custom_items );

		foreach ( $global_colors_list as $index => $item ) {
			$this->add_control(
				"color_{$item['_id']}",
				array(
					'label' => $item['title'],
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"html.cmsmasters-mode-switcher-active" => "--e-global-color-{$item['_id']}: {{VALUE}}",
					),
					'global' => array(
						'active' => false,
					),
				)
			);
		}
	}

}
