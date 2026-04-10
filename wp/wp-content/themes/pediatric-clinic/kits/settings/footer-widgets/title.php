<?php
namespace PediatricClinicSpace\Kits\Settings\FooterWidgets;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Footer Widgets Title settings.
 */
class Title extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'footer_widgets_title';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Widget Title', 'pediatric-clinic' );
	}

	/**
	 * Get control ID prefix.
	 *
	 * Retrieve the control ID prefix.
	 *
	 * @return string Control ID prefix.
	 */
	protected static function get_control_id_prefix() {
		return parent::get_control_id_prefix() . '_footer_widgets';
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
			'condition' => array( $this->get_control_id_parameter( '', 'visibility!' ) => 'hide' ),
		);
	}

	/**
	 * Register toggle controls.
	 *
	 * Registers the controls of the kit settings tab toggle.
	 */
	protected function register_toggle_controls() {
		$this->add_var_group_control( 'title', self::VAR_TYPOGRAPHY );

		$this->add_control(
			'title_colors_heading_control',
			array(
				'label' => esc_html__( 'Colors', 'pediatric-clinic' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'title_colors_color',
			array(
				'label' => esc_html__( 'Color', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'title_colors_color' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_colors_link',
			array(
				'label' => esc_html__( 'Link', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'title_colors_link' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_colors_hover',
			array(
				'label' => esc_html__( 'Link Hover', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'title_colors_hover' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_box_heading_control',
			array(
				'label' => esc_html__( 'Container', 'pediatric-clinic' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_controls_group( 'title_box', self::CONTROLS_CONTAINER_BOX, array(
			'popover' => false,
		) );
	}

}
