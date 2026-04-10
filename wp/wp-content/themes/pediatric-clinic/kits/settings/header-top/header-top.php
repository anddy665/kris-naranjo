<?php
namespace PediatricClinicSpace\Kits\Settings\HeaderTop;

use PediatricClinicSpace\Kits\Controls\Controls_Manager as CmsmastersControls;
use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Header Top settings.
 */
class Header_Top extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'header_top';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Header Top', 'pediatric-clinic' );
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
			'visibility',
			array(
				'label' => esc_html__( 'Visibility', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'Hide', 'pediatric-clinic' ),
				'label_on' => esc_html__( 'Show', 'pediatric-clinic' ),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'visibility' ),
					'no'
				),
			)
		);

		$default_visibility_args = array(
			'condition' => array( $this->get_control_id_parameter( '', 'visibility' ) => 'yes' ),
		);

		$this->add_control(
			'alignment',
			array_merge_recursive(
				$default_visibility_args,
				array(
					'label' => esc_html__( 'Alignment', 'pediatric-clinic' ),
					'label_block' => false,
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'space-between' => array(
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
					'toggle' => false,
				)
			)
		);

		$this->add_control(
			'height',
			array_merge_recursive(
				$default_visibility_args,
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
			)
		);

		$this->add_control(
			'elements_heading_control',
			array_merge_recursive(
				$default_visibility_args,
				array(
					'label' => esc_html__( 'Elements Order', 'pediatric-clinic' ),
					'type' => Controls_Manager::HEADING,
				)
			)
		);

		$this->add_control(
			'elements',
			array_merge_recursive(
				$default_visibility_args,
				array(
					'label_block' => true,
					'show_label' => false,
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => CmsmastersControls::SELECTIZE,
					'options' => array(
						'info' => esc_html__( 'Short Info', 'pediatric-clinic' ),
						'html' => esc_html__( 'Custom HTML', 'pediatric-clinic' ),
						'social' => esc_html__( 'Social Icons', 'pediatric-clinic' ),
						'nav' => esc_html__( 'Navigation', 'pediatric-clinic' ),
					),
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( '', 'elements' ),
						array( 'nav' )
					),
					'multiple' => true,
				)
			)
		);

		$this->add_responsive_control(
			'elements_gap',
			array_merge_recursive(
				$default_visibility_args,
				array(
					'label' => esc_html__( 'Gap Between', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
						'%' => array(
							'min' => 0,
							'max' => 100,
						),
						'vw' => array(
							'min' => 0,
							'max' => 10,
						),
					),
					'size_units' => array(
						'px',
						'%',
						'vw',
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( '', 'elements_gap' ) . ': {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						'elements!' => '',
					),
				)
			)
		);

		$this->add_responsive_control(
			'z_index',
			array_merge_recursive(
				$default_visibility_args,
				array(
					'label' => esc_html__( 'Z-Index', 'pediatric-clinic' ),
					'type' => Controls_Manager::NUMBER,
					'min' => 0,
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( '', 'z_index' ) . ': {{VALUE}};',
					),
				)
			)
		);

		$this->add_controls_group( 'container', self::CONTROLS_CONTAINER, $default_visibility_args );

		$this->add_controls_group( 'content', self::CONTROLS_CONTENT, $default_visibility_args );

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
