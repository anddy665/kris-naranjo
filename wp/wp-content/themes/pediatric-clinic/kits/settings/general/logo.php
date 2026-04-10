<?php
namespace PediatricClinicSpace\Kits\Settings\General;

use PediatricClinicSpace\Kits\Controls\Controls_Manager as CmsmastersControls;
use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Logo settings.
 */
class Logo extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'logo';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Logo', 'pediatric-clinic' );
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
			'type',
			array(
				'label' => esc_html__( 'Type', 'pediatric-clinic' ),
				'label_block' => false,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'image' => esc_html__( 'Image', 'pediatric-clinic' ),
					'text' => esc_html__( 'Text', 'pediatric-clinic' ),
				),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'type' ),
					'image'
				),
				'toggle' => false,
			)
		);

		$this->add_control(
			'image',
			array(
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array(
					$this->get_control_id_parameter( '', 'type' ) => 'image',
				),
			)
		);

		$this->add_control(
			'retina_toggle',
			array(
				'label' => esc_html__( 'Retina Image', 'pediatric-clinic' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array(
					$this->get_control_id_parameter( '', 'type' ) => 'image',
					$this->get_control_id_parameter( '', 'image[url]!' ) => '',
				),
			)
		);

		$this->start_popover();

		$this->add_control(
			'retina_image',
			array(
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array(
					$this->get_control_id_parameter( '', 'type' ) => 'image',
					$this->get_control_id_parameter( '', 'image[url]!' ) => '',
					$this->get_control_id_parameter( '', 'retina_toggle' ) => 'yes',
				),
			)
		);

		$this->end_popover();

		$this->add_control(
			'second_toggle',
			array(
				'label' => esc_html__( 'Second Logo Image', 'pediatric-clinic' ),
				'description' => sprintf(
					'%1$s <a href="https://docs.cmsmasters.net/mode-switcher/" target="_blank">%2$s</a>.',
					__( 'Image that will be applied when using the', 'pediatric-clinic' ),
					__( 'Mode Switcher', 'pediatric-clinic' )
				),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array(
					$this->get_control_id_parameter( '', 'type' ) => 'image',
					$this->get_control_id_parameter( '', 'image[url]!' ) => '',
				),
			)
		);

		$this->start_popover();

		$this->add_control(
			'image_second',
			array(
				'label' => esc_html__( 'Second Logo Image', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array(
					$this->get_control_id_parameter( '', 'type' ) => 'image',
					$this->get_control_id_parameter( '', 'image[url]!' ) => '',
					$this->get_control_id_parameter( '', 'second_toggle' ) => 'yes',
				),
			)
		);

		$this->add_control(
			'retina_image_second',
			array(
				'label' => esc_html__( 'Second Retina Logo Image', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'condition' => array(
					$this->get_control_id_parameter( '', 'type' ) => 'image',
					$this->get_control_id_parameter( '', 'image[url]!' ) => '',
					$this->get_control_id_parameter( '', 'second_toggle' ) => 'yes',
				),
			)
		);

		$this->end_popover();

		$this->start_controls_tabs(
			'text_tabs',
			array(
				'condition' => array(
					$this->get_control_id_parameter( '', 'type' ) => 'text',
				),
			)
		);

		$this->start_controls_tab(
			'text_title_tab',
			array(
				'label' => esc_html__( 'Title', 'pediatric-clinic' ),
			)
		);

		$this->add_control(
			'title_text',
			array(
				'label_block' => true,
				'show_label' => false,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => ( get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : esc_html__( 'Site logo', 'pediatric-clinic' ) ),
			)
		);

		$this->add_var_group_control( 'title', self::VAR_TYPOGRAPHY );

		$this->add_control(
			'title_colors_text',
			array(
				'label' => esc_html__( 'Color', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'title_colors_text' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_colors_hover',
			array(
				'label' => esc_html__( 'Hover', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'title_colors_hover' ) . ': {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'text_subtitle_tab',
			array(
				'label' => esc_html__( 'Subtitle', 'pediatric-clinic' ),
			)
		);

		$this->add_control(
			'subtitle_text',
			array(
				'label_block' => true,
				'show_label' => false,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Logo subtitle', 'pediatric-clinic' ),
			)
		);

		$this->add_var_group_control( 'subtitle', self::VAR_TYPOGRAPHY );

		$this->add_control(
			'subtitle_colors_text',
			array(
				'label' => esc_html__( 'Color', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'subtitle_colors_text' ) . ': {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'subtitle_colors_hover',
			array(
				'label' => esc_html__( 'Hover', 'pediatric-clinic' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'subtitle_colors_hover' ) . ': {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'apply_settings',
			array(
				'label_block' => true,
				'show_label' => false,
				'type' => Controls_Manager::BUTTON,
				'text' => esc_html__( 'Save & Reload', 'pediatric-clinic' ),
				'event' => 'cmsmasters:theme_settings:apply_settings',
				'separator' => 'before',
			)
		);
	}

}
