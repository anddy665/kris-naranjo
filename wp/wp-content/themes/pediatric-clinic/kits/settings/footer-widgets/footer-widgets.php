<?php
namespace PediatricClinicSpace\Kits\Settings\FooterWidgets;

use PediatricClinicSpace\Kits\Controls\Controls_Manager as CmsmastersControls;
use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Footer Widgets settings.
 */
class Footer_Widgets extends Settings_Tab_Base {

	/**
	 * Get toggle name.
	 *
	 * Retrieve the toggle name.
	 *
	 * @return string Toggle name.
	 */
	public static function get_toggle_name() {
		return 'footer_widgets';
	}

	/**
	 * Get title.
	 *
	 * Retrieve the toggle title.
	 */
	public function get_title() {
		return esc_html__( 'Footer Widgets', 'pediatric-clinic' );
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
				'raw' => esc_html__( "If you use a 'Footer' template, then the settings will not be applied, if you set the template to sitewide, then these settings will be hidden.", 'pediatric-clinic' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'render_type' => 'ui',
			)
		);

		$this->add_control(
			'visibility',
			array(
				'label' => esc_html__( 'Visibility', 'pediatric-clinic' ),
				'label_block' => false,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'show' => esc_html__( 'Show On All Devices', 'pediatric-clinic' ),
					'hide_tablet' => esc_html__( 'Hide On Tablet And Less', 'pediatric-clinic' ),
					'hide_mobile' => esc_html__( 'Hide On Mobile', 'pediatric-clinic' ),
					'hide' => esc_html__( 'Hide On All Devices', 'pediatric-clinic' ),
				),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'visibility' ),
					'show'
				),
			)
		);

		$default_visibility_args = array(
			'condition' => array( $this->get_control_id_parameter( '', 'visibility!' ) => 'hide' ),
		);

		$this->add_control(
			'columns',
			array_merge_recursive(
				$default_visibility_args,
				array(
					'label' => esc_html__( 'Columns', 'pediatric-clinic' ),
					'label_block' => true,
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'1' => array(
							'title' => '1',
							'description' => esc_html__( 'One', 'pediatric-clinic' ),
						),
						'2' => array(
							'title' => '2',
							'description' => esc_html__( 'Two', 'pediatric-clinic' ),
						),
						'3' => array(
							'title' => '3',
							'description' => esc_html__( 'Three', 'pediatric-clinic' ),
						),
						'4' => array(
							'title' => '4',
							'description' => esc_html__( 'Four', 'pediatric-clinic' ),
						),
						'5' => array(
							'title' => '5',
							'description' => esc_html__( 'Five', 'pediatric-clinic' ),
						),
					),
					'toggle' => false,
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( '', 'columns' ),
						'4'
					),
				)
			)
		);

		$this->start_controls_tabs(
			'responsive_tabs',
			array_merge_recursive(
				$default_visibility_args,
				array(
					'condition' => array( $this->get_control_id_parameter( '', 'columns' ) => array( '2', '3', '4', '5' ) ),
				)
			)
		);

		$this->start_controls_tab(
			'responsive_desktop_tab',
			array(
				'label' => esc_html__( 'Desktop', 'pediatric-clinic' ),
			)
		);

		$this->add_control(
			'layout_2',
			array(
				'label' => esc_html__( 'Layout', 'pediatric-clinic' ),
				'label_block' => false,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'1212' => '1/2 + 1/2',
					'1323' => '1/3 + 2/3',
					'2313' => '2/3 + 1/3',
					'1434' => '1/4 + 3/4',
					'3414' => '3/4 + 1/4',
					'1545' => '1/5 + 4/5',
					'4515' => '4/5 + 1/5',
					'2535' => '2/5 + 3/5',
					'3525' => '3/5 + 2/5',
				),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'layout_2' ),
					'1212'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => '2',
				),
			)
		);

		$this->add_control(
			'layout_3',
			array(
				'label' => esc_html__( 'Layout', 'pediatric-clinic' ),
				'label_block' => false,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'131313' => '1/3 + 1/3 + 1/3',
					'121414' => '1/2 + 1/4 + 1/4',
					'141214' => '1/4 + 1/2 + 1/4',
					'141412' => '1/4 + 1/4 + 1/2',
					'152525' => '1/5 + 2/5 + 2/5',
					'251525' => '2/5 + 1/5 + 2/5',
					'252515' => '2/5 + 2/5 + 1/5',
					'151535' => '1/5 + 1/5 + 3/5',
					'153515' => '1/5 + 3/5 + 1/5',
					'351515' => '3/5 + 1/5 + 1/5',
				),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'layout_3' ),
					'131313'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => '3',
				),
			)
		);

		$this->add_control(
			'layout_4',
			array(
				'label' => esc_html__( 'Layout', 'pediatric-clinic' ),
				'label_block' => false,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'14141414' => '1/4 + 1/4 + 1/4 + 1/4',
					'15151525' => '1/5 + 1/5 + 1/5 + 2/5',
					'15152515' => '1/5 + 1/5 + 2/5 + 1/5',
					'15251515' => '1/5 + 2/5 + 1/5 + 1/5',
					'25151515' => '2/5 + 1/5 + 1/5 + 1/5',
				),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'layout_4' ),
					'14141414'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => '4',
				),
			)
		);

		$this->add_control(
			'layout_5',
			array(
				'label_block' => true,
				'show_label' => false,
				'raw' => esc_html__( 'Layout for this columns count will be', 'pediatric-clinic' ) . '<br />1/5 + 1/5 + 1/5 + 1/5 + 1/5',
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert',
				'render_type' => 'ui',
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => '5',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'responsive_tablet_tab',
			array(
				'label' => esc_html__( 'Tablet', 'pediatric-clinic' ),
			)
		);

		$this->add_control(
			'tablet_layout_from_2',
			array(
				'label' => esc_html__( 'Columns in Row', 'pediatric-clinic' ),
				'label_block' => true,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'1' => array(
						'title' => '1',
						'description' => esc_html__( 'One', 'pediatric-clinic' ),
					),
					'2' => array(
						'title' => '2',
						'description' => esc_html__( 'Two', 'pediatric-clinic' ),
					),
				),
				'toggle' => false,
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'tablet_layout_from_2' ),
					'2'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => '2',
				),
			)
		);

		$this->add_control(
			'tablet_layout_from_3',
			array(
				'label' => esc_html__( 'Columns in Row', 'pediatric-clinic' ),
				'label_block' => true,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'1' => array(
						'title' => '1',
						'description' => esc_html__( 'One', 'pediatric-clinic' ),
					),
					'2' => array(
						'title' => '2',
						'description' => esc_html__( 'Two', 'pediatric-clinic' ),
					),
					'3' => array(
						'title' => '3',
						'description' => esc_html__( 'Three', 'pediatric-clinic' ),
					),
				),
				'toggle' => false,
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'tablet_layout_from_3' ),
					'3'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => '3',
				),
			)
		);

		$this->add_control(
			'tablet_layout_from_4',
			array(
				'label' => esc_html__( 'Columns in Row', 'pediatric-clinic' ),
				'label_block' => true,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'1' => array(
						'title' => '1',
						'description' => esc_html__( 'One', 'pediatric-clinic' ),
					),
					'2' => array(
						'title' => '2',
						'description' => esc_html__( 'Two', 'pediatric-clinic' ),
					),
					'3' => array(
						'title' => '3',
						'description' => esc_html__( 'Three', 'pediatric-clinic' ),
					),
					'4' => array(
						'title' => '4',
						'description' => esc_html__( 'Four', 'pediatric-clinic' ),
					),
				),
				'toggle' => false,
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'tablet_layout_from_4' ),
					'2'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => '4',
				),
			)
		);

		$this->add_control(
			'tablet_layout_from_5',
			array(
				'label' => esc_html__( 'Columns in Row', 'pediatric-clinic' ),
				'label_block' => true,
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'1' => array(
						'title' => '1',
						'description' => esc_html__( 'One', 'pediatric-clinic' ),
					),
					'2' => array(
						'title' => '2',
						'description' => esc_html__( 'Two', 'pediatric-clinic' ),
					),
					'3' => array(
						'title' => '3',
						'description' => esc_html__( 'Three', 'pediatric-clinic' ),
					),
					'4' => array(
						'title' => '4',
						'description' => esc_html__( 'Four', 'pediatric-clinic' ),
					),
					'5' => array(
						'title' => '5',
						'description' => esc_html__( 'Five', 'pediatric-clinic' ),
					),
				),
				'toggle' => false,
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'tablet_layout_from_5' ),
					'3'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => '5',
				),
			)
		);

		$this->add_control(
			'tablet_columns_reverse',
			array(
				'label' => esc_html__( 'Reverse Columns', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'No', 'pediatric-clinic' ),
				'label_on' => esc_html__( 'Yes', 'pediatric-clinic' ),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'tablet_columns_reverse' ),
					'no'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => array( '2', '3', '4', '5' ),
				),
			)
		);

		$this->add_control(
			'tablet_hide_1',
			array(
				'label' => esc_html__( 'Hide "Footer 1" widgets area', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'No', 'pediatric-clinic' ),
				'label_on' => esc_html__( 'Yes', 'pediatric-clinic' ),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'tablet_hide_1' ),
					'no'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => array( '2', '3', '4', '5' ),
				),
			)
		);

		$this->add_control(
			'tablet_hide_2',
			array(
				'label' => esc_html__( 'Hide "Footer 2" widgets area', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'No', 'pediatric-clinic' ),
				'label_on' => esc_html__( 'Yes', 'pediatric-clinic' ),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'tablet_hide_2' ),
					'no'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => array( '2', '3', '4', '5' ),
				),
			)
		);

		$this->add_control(
			'tablet_hide_3',
			array(
				'label' => esc_html__( 'Hide "Footer 3" widgets area', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'No', 'pediatric-clinic' ),
				'label_on' => esc_html__( 'Yes', 'pediatric-clinic' ),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'tablet_hide_3' ),
					'no'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => array( '3', '4', '5' ),
				),
			)
		);

		$this->add_control(
			'tablet_hide_4',
			array(
				'label' => esc_html__( 'Hide "Footer 4" widgets area', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'No', 'pediatric-clinic' ),
				'label_on' => esc_html__( 'Yes', 'pediatric-clinic' ),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'tablet_hide_4' ),
					'no'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => array( '4', '5' ),
				),
			)
		);

		$this->add_control(
			'tablet_hide_5',
			array(
				'label' => esc_html__( 'Hide "Footer 5" widgets area', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'No', 'pediatric-clinic' ),
				'label_on' => esc_html__( 'Yes', 'pediatric-clinic' ),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'tablet_hide_5' ),
					'no'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => '5',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'responsive_mobile_tab',
			array(
				'label' => esc_html__( 'Mobile', 'pediatric-clinic' ),
			)
		);

		$this->add_control(
			'mobile_columns_reverse',
			array(
				'label' => esc_html__( 'Reverse Columns', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'No', 'pediatric-clinic' ),
				'label_on' => esc_html__( 'Yes', 'pediatric-clinic' ),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'mobile_columns_reverse' ),
					'no'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => array( '2', '3', '4', '5' ),
				),
			)
		);

		$this->add_control(
			'mobile_hide_1',
			array(
				'label' => esc_html__( 'Hide "Footer 1" widgets area', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'No', 'pediatric-clinic' ),
				'label_on' => esc_html__( 'Yes', 'pediatric-clinic' ),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'mobile_hide_1' ),
					'no'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => array( '2', '3', '4', '5' ),
				),
			)
		);

		$this->add_control(
			'mobile_hide_2',
			array(
				'label' => esc_html__( 'Hide "Footer 2" widgets area', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'No', 'pediatric-clinic' ),
				'label_on' => esc_html__( 'Yes', 'pediatric-clinic' ),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'mobile_hide_2' ),
					'no'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => array( '2', '3', '4', '5' ),
				),
			)
		);

		$this->add_control(
			'mobile_hide_3',
			array(
				'label' => esc_html__( 'Hide "Footer 3" widgets area', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'No', 'pediatric-clinic' ),
				'label_on' => esc_html__( 'Yes', 'pediatric-clinic' ),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'mobile_hide_3' ),
					'no'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => array( '3', '4', '5' ),
				),
			)
		);

		$this->add_control(
			'mobile_hide_4',
			array(
				'label' => esc_html__( 'Hide "Footer 4" widgets area', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'No', 'pediatric-clinic' ),
				'label_on' => esc_html__( 'Yes', 'pediatric-clinic' ),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'mobile_hide_4' ),
					'no'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => array( '4', '5' ),
				),
			)
		);

		$this->add_control(
			'mobile_hide_5',
			array(
				'label' => esc_html__( 'Hide "Footer 5" widgets area', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => esc_html__( 'No', 'pediatric-clinic' ),
				'label_on' => esc_html__( 'Yes', 'pediatric-clinic' ),
				'default' => $this->get_default_setting(
					$this->get_control_name_parameter( '', 'mobile_hide_5' ),
					'no'
				),
				'condition' => array(
					$this->get_control_id_parameter( '', 'columns' ) => '5',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'columns_gap',
			array(
				'label' => esc_html__( 'Columns Gap', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
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
					'vw',
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'columns_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'widgets_gap',
			array(
				'label' => esc_html__( 'Widgets Gap Between', 'pediatric-clinic' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					':root' => '--' . $this->get_control_prefix_parameter( '', 'widgets_gap' ) . ': {{SIZE}}{{UNIT}};',
				),
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
