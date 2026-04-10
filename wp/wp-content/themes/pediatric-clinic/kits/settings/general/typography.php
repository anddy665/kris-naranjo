<?php
namespace PediatricClinicSpace\Kits\Settings\General;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Typography settings.
 */
class Typography extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the settings toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'typography';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Typography', 'pediatric-clinic' );
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$this->add_var_group_control( 'base', self::VAR_TYPOGRAPHY, array(
			'label' => esc_html__( 'Base Font', 'pediatric-clinic' ),
		) );

		$this->add_var_group_control( 'h1', self::VAR_TYPOGRAPHY, array(
			'label' => esc_html__( 'H1 Font', 'pediatric-clinic' ),
		) );

		$this->add_var_group_control( 'h2', self::VAR_TYPOGRAPHY, array(
			'label' => esc_html__( 'H2 Font', 'pediatric-clinic' ),
		) );

		$this->add_var_group_control( 'h3', self::VAR_TYPOGRAPHY, array(
			'label' => esc_html__( 'H3 Font', 'pediatric-clinic' ),
		) );

		$this->add_var_group_control( 'h4', self::VAR_TYPOGRAPHY, array(
			'label' => esc_html__( 'H4 Font', 'pediatric-clinic' ),
		) );

		$this->add_var_group_control( 'h5', self::VAR_TYPOGRAPHY, array(
			'label' => esc_html__( 'H5 Font', 'pediatric-clinic' ),
		) );

		$this->add_var_group_control( 'h6', self::VAR_TYPOGRAPHY, array(
			'label' => esc_html__( 'H6 Font', 'pediatric-clinic' ),
		) );
	}

}
