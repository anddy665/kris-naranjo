<?php
namespace CmsmastersElementor\Modules\Popup\Documents;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TemplateDocuments\Base\Section_Document;
use CmsmastersElementor\Modules\TemplatePreview\Traits\Preview_Type;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Popup extends Section_Document {

	use Preview_Type;

	/**
	 * Get editor widgets panel config.
	 *
	 * Modifies editor widgets panel config, such as widgets settings or
	 * elements categories.
	 *
	 * @since 1.0.0
	 * @since 1.19.3 Added `Add To Cart Button` widget display in the Popup template.
	 *
	 * @return array Modified editor panel config.
	 */
	public static function get_editor_panel_config() {
		$config = parent::get_editor_panel_config();

		$config['widgets_settings']['cmsmasters-woo-product-add-to-cart-button'] = array( 'show_in_panel' => true );

		return $config;
	}

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @since 1.9.0
	 *
	 * @return string Document name.
	 */
	public function get_name() {
		return 'cmsmasters_popup';
	}

	/**
	 * Get document title.
	 *
	 * Retrieve the document title.
	 *
	 * @since 1.9.0
	 *
	 * @return string Document title.
	 */
	public static function get_title() {
		return __( 'Popup', 'cmsmasters-elementor' );
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Document constructor.
	 *
	 * Initializing the Addon Entry document.
	 *
	 * @since 1.11.6
	 *
	 * @param array $data Class initial data.
	 */
	public function __construct( array $data = array() ) {
		if ( $data ) {
			add_filter( 'cmsmasters_elementor/documents/container_attributes', array( $this, 'add_icon_to_attributes' ) );
		}

		parent::__construct( $data );
	}

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @since 1.9.0
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['location_type'] = 'disabled';
		$properties['locations_category'] = 'disabled';

		$properties = apply_filters( 'cmsmasters_elementor/documents/cmsmasters_popup/get_properties', $properties );

		return $properties;
	}

	/**
	 * @since 1.9.0
	 */
	public function get_initial_config() {
		$config = parent::get_initial_config();

		$config['container'] = '.cmsmasters-widget-template-modal .dialog-widget-content .cmsmasters-widget-template-popup';

		return $config;
	}

	/**
	 * Register document controls.
	 *
	 * Used to add new controls to section documents settings.
	 *
	 * @since 1.1.0
	 * @since 1.10.0 Added padding control for overlay.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'cms_popup_general',
			array(
				'label' => esc_html__( 'Popup', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_responsive_control(
			'cms_width',
			array(
				'label' => esc_html__( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 100,
						'max' => 1000,
					),
					'%' => array(
						'min' => 10,
						'max' => 100,
					),
					'vh' => array(
						'min' => 10,
						'max' => 100,
					),
					'vw' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'size_units' => array( 'px', '%', 'vh', 'vw' ),
				'default' => array(
					'size' => 500,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-popup-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cms_height',
			array(
				'label' => esc_html__( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 100,
						'max' => 1000,
					),
					'vh' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'size_units' => array( 'px', 'vh', 'custom' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-popup-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cms_horizontal_position',
			array(
				'label' => esc_html__( 'Horizontal Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'center',
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-h-position: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'cms_vertical_position',
			array(
				'label' => esc_html__( 'Vertical Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'center',
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => esc_html__( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-v-position: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'cms_entrance_animation',
			array(
				'label' => esc_html__( 'Entrance Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ANIMATION,
				'frontend_available' => true,
				'prefix_class' => 'animated ',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'cms_entrance_animation_duration',
			array(
				'label' => esc_html__( 'Animation Duration', 'cmsmasters-elementor' ) . ' (sec)',
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 1.2,
				),
				'range' => array(
					'px' => array(
						'min' => 0.1,
						'max' => 10,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--animation-duration: {{SIZE}}s',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'cms_entrance_animation',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cms_section_close_button_settings',
			array(
				'label' => esc_html__( 'Close Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_control(
			'cms_close_button_position',
			array(
				'label' => esc_html__( 'Position', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'' => array(
						'title' => esc_html__( 'Inside', 'cmsmasters-elementor' ),
					),
					'outside' => array(
						'title' => esc_html__( 'Outside', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'outside',
				'render_type' => 'template',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'cms_close_button_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'far fa-times-circle',
					'library' => 'regular',
				),
				'skin' => 'inline',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'apply_preview_icon_settings',
			array(
				'label_block' => true,
				'show_label' => false,
				'type' => Controls_Manager::BUTTON,
				'text' => __( 'Apply & Reload', 'cmsmasters-elementor' ),
				'event' => 'cmsmasters:preview_manager:apply_preview',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cmsmaster_section_advanced',
			array(
				'label' => esc_html__( 'Additional', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_control(
			'cms_overlay',
			array(
				'label' => esc_html__( 'Hide Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'No', 'cmsmasters-elementor' ),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => 'background-color: transparent !important;',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'cms_prevent_close_on_background_click',
			array(
				'label' => esc_html__( 'Prevent Closing on Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'cms_prevent_scroll',
			array(
				'label' => esc_html__( 'Disable Page Scrolling', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'cms_multiple_popup',
			array(
				'label' => esc_html__( 'Allow Multiple Popups', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Use the setting if you have several popups on a page and want them to be displayed when the popup template is active. When enabled the popup template doesnt close other popups.', 'cmsmasters-elementor' ),
				'default' => '',
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		parent::register_controls();

		$this->remove_control( 'section_page_style' );

		$this->start_controls_section(
			'cms_section_page_style',
			array(
				'label' => esc_html__( 'Popup', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'  => 'cms_popup_background',
				'selector' => '{{WRAPPER}}.elementor[data-elementor-type="cmsmasters_popup"] .elementor-section-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'cms_popup_box_shadow',
				'selector' => '{{WRAPPER}}.elementor[data-elementor-type="cmsmasters_popup"] .elementor-section-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'  => 'cms_popup_border',
				'selector' => '{{WRAPPER}}.elementor[data-elementor-type="cmsmasters_popup"] .elementor-section-wrap',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'cms_popup_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-popup-bdr: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'cms_popup_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-popup-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'cms_popup_margin',
			array(
				'label' => esc_html__( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-popup-mrg: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cms_section_overlay',
			array(
				'label' => esc_html__( 'Overlay', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'cms_overlay!' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'cms_overlay_background',
				'types' => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}}',
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
					'color' => array(
						'default' => 'rgba(0,0,0,.8)',
					),
				),
			)
		);

		$this->add_responsive_control(
			'cms_overlay_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-overlay-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'cms_section_close_button',
			array(
				'label' => esc_html__( 'Close Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'cms_close_button_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 100,
						'min' => 0,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-close-size: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->start_controls_tabs( 'close_button_style_tabs' );

		$this->start_controls_tab(
			'tab_x_button_normal',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'close_button_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-colose-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'close_button_background_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-colose-bg-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'close_button_bd_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-colose-bd-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_x_button_hover',
			array(
				'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'close_button_hover_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-colose-color-hover: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'close_button_hover_background_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-colose-bg-color-hover: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'close_button_hover_bd_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-colose-bd-color-hover: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'cms_close_button_vertical',
			array(
				'label' => esc_html__( 'Vertical Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range' => array(
					'%' => array(
						'max' => 100,
						'min' => 0,
						'step' => 0.1,
					),
					'px' => array(
						'max' => 500,
						'min' => -500,
					),
				),
				'default' => array(
					'unit' => '%',
				),
				'tablet_default' => array(
					'unit' => '%',
				),
				'mobile_default' => array(
					'unit' => '%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-colose-v-position: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'cms_close_button_horizontal',
			array(
				'label' => esc_html__( 'Horizontal Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range' => array(
					'%' => array(
						'max' => 100,
						'min' => 0,
						'step' => 0.1,
					),
					'px' => array(
						'max' => 500,
						'min' => -500,
					),
				),
				'default' => array(
					'unit' => '%',
				),
				'tablet_default' => array(
					'unit' => '%',
				),
				'mobile_default' => array(
					'unit' => '%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-colose-h-position: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'  => 'cms_close_border',
				'selector' => '{{WRAPPER}}.elementor[data-elementor-type="cmsmasters_popup"] .cmsmasters-popup-close',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'cms_close_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-close-bdr: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'cms_close_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-close-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Register Elementor Section document controls.
		 *
		 * Used to add new controls to the Elementor section document settings.
		 *
		 * Fires after Elementor registers the document controls.
		 *
		 * @since 1.9.0
		 *
		 * @param Section_Document $this Section base document instance.
		 */
		do_action( 'cmsmasters_elementor/documents/cmsmasters_popup/register_controls', $this );
	}

	/**
	 * Check edit mode.
	 *
	 * Checks if document opened in edit mode.
	 *
	 * @since 1.9.0
	 */
	public function is_edit() {
		if ( ! is_admin() ) {
			return false;
		}

		$document_id = Utils::get_document_id();

		return $document_id && $document_id === $this->get_main_id();
	}

	/**
	 * @since 1.9.0
	 *
	 * @return array Document preview type options.
	 */
	public static function get_preview_type_options() {
		return array_merge(
			array( '' => __( 'Select preview', 'cmsmasters-elementor' ) ),
			static::get_preview_type_options_choices()
		);
	}

	/**
	 * @since 1.9.0
	 */
	public static function get_preview_type_options_choices() {
		$preview_type_choices = self::get_singular_preview_type_options_choices( false );

		unset( $preview_type_choices['error_404'] );
		unset( $preview_type_choices['singular']['options']['singular/attachment'] );

		return $preview_type_choices;
	}

	/**
	 * Get remote library config.
	 *
	 * Retrieves Addon remote templates library config.
	 *
	 * @since 1.9.0
	 *
	 * @return array Addon templates library config.
	 */
	protected function get_remote_library_config() {
		$config = parent::get_remote_library_config();

		$config['category'] = str_replace( 'cmsmasters_', '', $this->get_name() );

		return $config;
	}

	/**
	 * Added icon popup close to attributes.
	 *
	 * @since 1.11.6
	 *
	 * @return array attributes.
	 */
	public function add_icon_to_attributes( $attributes ) {
		$icon = $this->get_close_icon();
		$attributes['data-cms-icon'] = (string) $icon;

		return $attributes;
	}

	public function get_close_icon() {
		$settings = $this->get_settings_for_display();

		return Utils::get_render_icon(
			$settings['cms_close_button_icon'],
			array(
				'aria-hidden' => 'true',
				'aria-label' => 'Popup Close',
			),
			$with_wrap = false
		);
	}
}
