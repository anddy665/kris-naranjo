<?php
namespace PediatricClinicSpace\Kits\Settings\Heading;

use PediatricClinicSpace\Kits\Controls\Controls_Manager as CmsmastersControls;
use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Heading settings.
 */
class Heading extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'heading';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Heading', 'pediatric-clinic' );
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
			'notice',
			array(
				'raw' => esc_html__( "If you use a 'Header' template, then the settings will not be applied, if you set the template to sitewide, then these settings will be hidden.", 'pediatric-clinic' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'render_type' => 'ui',
			)
		);

		$this->add_control(
			'alignment',
			array(
				'label' => esc_html__( 'Alignment', 'pediatric-clinic' ),
				'label_block' => false,
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'initial' => array(
						'title' => esc_html__( 'Wide', 'pediatric-clinic' ),
						'icon' => 'eicon-text-align-justify',
					),
					'center' => array(
						'title' => esc_html__( 'Centered', 'pediatric-clinic' ),
						'icon' => 'eicon-text-align-center',
					),
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'alignment' ) . ': {{VALUE}};',
				),
				'toggle' => true,
			)
		);

		$this->add_control(
			'height',
			array(
				'label' => esc_html__( 'Height', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'height' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_controls_group( 'container', self::CONTROLS_CONTAINER );

		$this->add_controls_group( 'content', self::CONTROLS_CONTENT );
	}

}
