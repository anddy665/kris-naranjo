<?php
namespace PediatricClinicSpace\Kits\Settings\Footer;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Footer Copyright settings.
 */
class Copyright extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'footer_copyright';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Copyright', 'pediatric-clinic' );
	}

	/**
	 * Get control ID prefix.
	 *
	 * Retrieve the control ID prefix.
	 *
	 * @return string Control ID prefix.
	 */
	protected static function get_control_id_prefix() {
		return parent::get_control_id_prefix() . '_footer';
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
			'condition' => array( $this->get_control_id_parameter( '', 'elements' ) => 'copyright' ),
		);
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		/* translators: Theme settings copyright control placeholder. %s: Current year */
		$copyright_placeholder = sprintf( esc_html__( 'cmsmasters © %d / All Rights Reserved', 'pediatric-clinic' ), get_the_date( 'Y' ) );

		$this->add_control(
			'copyright_text',
			array(
				'label_block' => true,
				'show_label' => false,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => $copyright_placeholder,
			)
		);

		$this->add_var_group_control( 'copyright', self::VAR_TYPOGRAPHY );

		$this->add_control(
			'copyright_color',
			array(
				'label' => esc_html__( 'Color', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'copyright_color' ) . ': {{VALUE}};',
				),
			)
		);
	}

}
