<?php
namespace PediatricClinicSpace\Kits\Settings\General;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Container settings.
 */
class Container extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the settings toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'container';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Global Container', 'pediatric-clinic' );
	}

	/**
	 * Get control ID prefix.
	 *
	 * Retrieve the control ID prefix.
	 *
	 * @return string Control ID prefix.
	 */
	protected static function get_control_id_prefix() {
		return parent::get_control_id_prefix() . '_global';
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$prefix = $this->get_css_var_prefix();

		$content_key = "{$prefix}-content";

		$this->add_control(
			'container_notice',
			array(
				'raw' => esc_html__( 'Global settings that are used as defaults for the container and content in Header Top, Header Middle, Header Bottom, Main, Heading, Breadcrumbs (new row type), Footer Widgets, Footer.', 'pediatric-clinic' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'render_type' => 'ui',
			)
		);

		$this->add_responsive_control(
			'container_width',
			array(
				'label' => esc_html__( 'Container Max Width', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 300,
						'max' => 2000,
						'step' => 10,
					),
					'%' => array(
						'min' => 10,
						'max' => 100,
					),
					'vw' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'size_units' => array(
					'px',
					'%',
					'vw',
				),
				'selectors' => array(
					':root' => "--{$prefix}-container-width: {{SIZE}}{{UNIT}};",
				),
			)
		);

		$this->add_responsive_control(
			'content_width',
			array(
				'label' => esc_html__( 'Content Max Width', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 300,
						'max' => 2000,
						'step' => 10,
					),
					'%' => array(
						'min' => 10,
						'max' => 100,
					),
					'vw' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'size_units' => array(
					'px',
					'%',
					'vw',
				),
				'selectors' => array(
					':root' => "--{$content_key}-width: {{SIZE}}{{UNIT}};",
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label' => esc_html__( 'Content Horizontal Padding', 'pediatric-clinic' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'allowed_dimensions' => array( 'left', 'right' ),
				'selectors' => array(
					':root' => "--{$content_key}-padding-left: {{LEFT}}{{UNIT}};" .
						"--{$content_key}-padding-right: {{RIGHT}}{{UNIT}};",
				),
			)
		);
	}

}
