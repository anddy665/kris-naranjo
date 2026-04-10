<?php
namespace PediatricClinicSpace\Kits\Settings\Archive;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Archive Content settings.
 */
class Content extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'archive_content';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Content', 'pediatric-clinic' );
	}

	/**
	 * Get control ID prefix.
	 *
	 * Retrieve the control ID prefix.
	 *
	 * @return string Control ID prefix.
	 */
	protected static function get_control_id_prefix() {
		return parent::get_control_id_prefix() . '_archive';
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
			'conditions' => array(
				'relation' => 'or',
				'terms' => array(
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => $this->get_control_id_parameter( '', 'type' ),
								'operator' => '===',
								'value' => 'large',
							),
							array(
								'name' => $this->get_control_id_parameter( '', 'large_elements' ),
								'operator' => 'contains',
								'value' => 'content',
							),
						),
					),
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => $this->get_control_id_parameter( '', 'type' ),
								'operator' => '===',
								'value' => 'grid',
							),
							array(
								'name' => $this->get_control_id_parameter( '', 'grid_elements' ),
								'operator' => 'contains',
								'value' => 'content',
							),
						),
					),
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => $this->get_control_id_parameter( '', 'type' ),
								'operator' => '===',
								'value' => 'compact',
							),
							array(
								'name' => $this->get_control_id_parameter( '', 'compact_elements' ),
								'operator' => 'contains',
								'value' => 'content',
							),
						),
					),
				),
			),
		);
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$this->add_controls_group( 'content', self::CONTROLS_POST_CONTENT );
	}

}
