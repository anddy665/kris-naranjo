<?php

namespace CmsmastersElementor\Modules\Hotspot\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Hotspot extends Base_Widget {

	protected $hotspot_class = '.elementor-widget-cmsmasters-hotspot';
	protected $hotspot_selector = 'elementor-widget-cmsmasters-hotspot';

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.8.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Hotspot', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.8.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-hotspot';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.8.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array( 'image', 'tooltip', 'CTA', 'dot' );
	}

	/**
	 * Specifying caching of the widget by default.
	 *
	 * @since 1.14.0
	 */
	protected function is_dynamic_content(): bool {
		return false;
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.16.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'widget-cmsmasters-hotspot',
		);
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
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * Should be inherited and register new controls using `add_control()`,
	 * `add_responsive_control()` and `add_group_control()`, inside control
	 * wrappers like `start_controls_section()`, `start_controls_tabs()` and
	 * `start_controls_tab()`.
	 *
	 * @since 1.8.0
	 * @since 1.10.1 Fixed image settings.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {

		/**
		 * Image Section
		 */
		$this->start_controls_section(
			'section_image',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'image',
			array(
				'label' => __( 'Choose Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => array(
					'active' => true,
				),
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'image',
				'default' => 'large',
				'separator' => 'none',
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-background-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			)
		);

		$this->end_controls_section();

		/**
		 * Section Hotspot
		 */
		$this->start_controls_section(
			'hotspot_section',
			array(
				'label' => __( 'Hotspot', 'cmsmasters-elementor' ),
			)
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'hotspot_repeater' );

		$repeater->start_controls_tab(
			'hotspot_content_tab',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'hotspot_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'hotspot_link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'hotspot_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'far fa-dot-circle',
					'library' => 'fa-regular',
				),
				'skin' => 'inline',
				'label_block' => false,
			)
		);

		$repeater->add_control(
			'hotspot_icon_active',
			array(
				'label' => __( 'Icon Active', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'default' => array(
					'value' => 'far fa-check-circle',
					'library' => 'fa-regular',
				),
				'label_block' => false,
				'condition' => array(
					'hotspot_icon[value]!' => '',
				),
			)
		);

		$repeater->add_control(
			'hotspot_custom_size',
			array(
				'label' => __( 'Custom Hotspot Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'default' => 'no',
				'description' => __( 'Set custom Hotspot size that will only affect this specific hotspot.', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'hotspot_width',
			array(
				'label' => __( 'Min Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-hotspot-min-width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'hotspot_custom_size' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'hotspot_height',
			array(
				'label' => __( 'Min Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-hotspot-min-height: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'hotspot_custom_size' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'hotspot_tooltip_content',
			array(
				'render_type' => 'template',
				'label' => __( 'Tooltip Content', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __( 'Add Your Tooltip Text Here', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'hotspot_button_switcher',
			array(
				'label' => __( 'Show Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'default' => 'no',
			)
		);

		$repeater->add_control(
			'hotspot_button_link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'label_block' => false,
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'condition' => array(
					'hotspot_button_switcher' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'hotspot_button_label',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Button', 'cmsmasters-elementor' ),
				'default' => '',
				'label_block' => false,
				'dynamic' => array(
					'active' => true,
				),
				'condition' => array(
					'hotspot_button_switcher' => 'yes',
				),
			)
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'hotspot_position_tab',
			array(
				'label' => __( 'POSITION', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'hotspot_horizontal',
			array(
				'label' => __( 'Horizontal Orientation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => is_rtl() ? 'right' : 'left',
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'toggle' => false,
			)
		);

		$repeater->add_responsive_control(
			'hotspot_offset_x',
			array(
				'label' => __( 'Offset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default' => array(
					'unit' => '%',
					'size' => '50',
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' =>
							'{{hotspot_horizontal.VALUE}}: {{SIZE}}%; --cmsmasters-hotspot-translate-x: {{SIZE}}%;',
				),
			)
		);

		$repeater->add_control(
			'hotspot_vertical',
			array(
				'label' => __( 'Vertical Orientation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'default' => 'top',
				'toggle' => false,
			)
		);

		$repeater->add_responsive_control(
			'hotspot_offset_y',
			array(
				'label' => __( 'Offset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default' => array(
					'unit' => '%',
					'size' => '50',
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' =>
							'{{hotspot_vertical.VALUE}}: {{SIZE}}%; --cmsmasters-hotspot-translate-y: {{SIZE}}%;',
				),
			)
		);

		$repeater->add_control(
			'hotspot_tooltip_position',
			array(
				'label' => __( 'Custom Tooltip Properties', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'default' => 'no',
				'description' => __( 'Set custom Tooltip opening that will only affect this specific hotspot.', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'hotspot_heading',
			array(
				'label' => __( 'Box', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array(
					'hotspot_tooltip_position' => 'yes',
				),
			)
		);

		$repeater->add_responsive_control(
			'hotspot_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'right' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'bottom' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'left' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
					'top' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'default' => 'top',
				'toggle' => false,
				'selectors' => array(
					"body {{WRAPPER}} {{CURRENT_ITEM}} {$this->hotspot_class}__tooltip-position" => 'right: auto;bottom: auto;left: auto;top: auto;{{VALUE}}: calc(100% + var(--cmsmasters-tooltip-gap, 10px));',
					"body {{WRAPPER}} {{CURRENT_ITEM}} {$this->hotspot_class}__tooltip-position{$this->hotspot_class}__custom-position-yes" => 'right: auto;bottom: auto;left: auto;top: auto;{{VALUE}}: calc(100% + var(--cmsmasters-tooltip-gap, 10px));',
					"body {{WRAPPER}} {{CURRENT_ITEM}} {$this->hotspot_class}__tooltip-position{$this->hotspot_class}__custom-position-yes.default" => 'right: auto; bottom: auto; left: auto; top: auto; {{VALUE}}: calc(100% + 30px + var(--cmsmasters-tooltip-gap, 10px));',
					"body {{WRAPPER}} {{CURRENT_ITEM}}{$this->hotspot_class}__active {$this->hotspot_class}__tooltip-position{$this->hotspot_class}__custom-position-yes.default" => 'right: auto; bottom: auto; left: auto; top: auto; {{VALUE}}: calc(100% + var(--cmsmasters-tooltip-gap, 10x));',
				),
				'condition' => array(
					'hotspot_tooltip_position' => 'yes',
				),
				'render_type' => 'template',
			)
		);

		$repeater->add_responsive_control(
			'hotspot_tooltip_width',
			array(
				'label' => __( 'Min Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 2000,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					"{{WRAPPER}} {{CURRENT_ITEM}} {$this->hotspot_class}__tooltip" => 'min-width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'hotspot_tooltip_position' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'hotspot_tooltip_text_wrap',
			array(
				'label' => __( 'Text Wrap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-white-space: normal',
				),
				'condition' => array(
					'hotspot_tooltip_position' => 'yes',
				),
			)
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'hotspot',
			array(
				'label' => __( 'Hotspot', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ hotspot_label }}}',
				'default' => array(
					array(
						// Default #1 circle
					),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'hotspot_icon_position',
			array(
				'label' => __( 'Icon Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'separator' => 'before',
				'default' => 'left',
			)
		);

		$this->add_control(
			'animation_type',
			array(
				'label' => __( 'Animation Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'infinity' => __( 'Infinity', 'cmsmasters-elementor' ),
					'hover' => __( 'Hover', 'cmsmasters-elementor' ),
				),
				'default' => 'infinity',
				'toggle' => false,
				'label_block' => false,
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'hotspot_animation',
			array(
				'label' => __( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'cmsmasters-soft-beat' => __( 'Soft Beat', 'cmsmasters-elementor' ),
					'cmsmasters-expand' => __( 'Expand', 'cmsmasters-elementor' ),
				),
				'render_type' => 'template',
				'description' => __( 'In order for the expansion animation to work, you need to set the box color', 'cmsmasters-elementor' ),
				'default' => 'cmsmasters-soft-beat',
				'condition' => array(
					'animation_type' => 'infinity',
				),
			)
		);

		$this->add_control(
			'hover_hotspot_animation',
			array(
				'label' => __( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
				'label_block' => false,
				'render_type' => 'template',
				'condition' => array(
					'animation_type' => 'hover',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Tooltip Section
		 */
		$this->start_controls_section(
			'tooltip_section',
			array(
				'label' => __( 'Tooltip', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'tooltip_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'top',
				'toggle' => false,
				'options' => array(
					'right' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'bottom' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'left' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
					'top' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'render_type' => 'template',
				'selectors' => array(
					"{{WRAPPER}} {$this->hotspot_class}__tooltip-position" => 'right: auto; bottom: auto; left: auto; top: auto; {{VALUE}}: calc(100% + var(--cmsmasters-tooltip-gap, 10px));',
					"{{WRAPPER}} {$this->hotspot_class}__tooltip-position.default" => 'right: auto; bottom: auto; left: auto; top: auto; {{VALUE}}: calc(100% + 30px + var(--cmsmasters-tooltip-gap, 10px));',
					"{{WRAPPER}} {$this->hotspot_class}__active {$this->hotspot_class}__tooltip-position.default" => 'right: auto; bottom: auto; left: auto; top: auto; {{VALUE}}: calc(100% + var(--cmsmasters-tooltip-gap, 10px));',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'tooltip_arrow',
			array(
				'label' => __( 'Arrow', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_responsive_control(
			'tooltip_trigger',
			array(
				'label' => __( 'Trigger', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'mouseenter' => __( 'Hover', 'cmsmasters-elementor' ),
					'click' => __( 'Click', 'cmsmasters-elementor' ),
					'none' => __( 'Always', 'cmsmasters-elementor' ),
				),
				'description' => __( 'If the hotspot has a link, the hover setting for touch devices controls the appearance of the tooltip but prevents the link from being followed.', 'cmsmasters-elementor' ),
				'label_block' => false,
				'default' => 'mouseenter',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'tooltip_animation',
			array(
				'label' => __( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'fade-in-out' => __( 'Fade In/Out', 'cmsmasters-elementor' ),
					'fade-grow' => __( 'Fade Grow', 'cmsmasters-elementor' ),
					'fade-direction' => __( 'Fade By Direction', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'placeholder' => __( 'Enter your image caption', 'cmsmasters-elementor' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'tooltip_animation_duration',
			array(
				'label' => __( 'Duration (ms)', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10000,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-tooltip-transition-duration: {{SIZE}}ms;',
				),
			)
		);

		$this->end_controls_section();

		/*************
		 * Style Tab
		 ************/

		/**
		 * Section Style Image
		 */
		$this->start_controls_section(
			'section_style_image',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'image_effects' );

		$state = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $state as $key => $label ) {
			$this->start_controls_tab(
				"normal_{$key}",
				array(
					'label' => $label,
				)
			);

			$state = ( 'hover' === $key ) ? ':hover' : '';

			$this->add_control(
				"opacity_{$key}",
				array(
					'label' => __( 'Opacity', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'max' => 1,
							'min' => 0.10,
							'step' => 0.01,
						),
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-opacity-{$key}: {{SIZE}};",
					),
				)
			);

			$this->add_group_control(
				Group_Control_Css_Filter::get_type(),
				array(
					'name' => "css_filters_{$key}",
					'fields_options' => array(
						'blur' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--cmsmasters-image-{$key}-css-filters: brightness( {{brightness.SIZE}}% ) contrast( {{contrast.SIZE}}% ) saturate( {{saturate.SIZE}}% ) blur( {{blur.SIZE}}px ) hue-rotate( {{hue.SIZE}}deg );",
							),
						),
					),
					'selector' => '{{WRAPPER}}',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "image_box_shadow_normal_{$key}",
					'exclude' => array(
						'box_shadow_position',
					),
					'fields_options' => array(
						'box_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--cmsmasters-image-{$key}-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};",
							),
						),
					),
					'selector' => "{{WRAPPER}}",
				)
			);

			if ( 'hover' === $key ) {
				$this->add_control(
					'background_hover_transition',
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'range' => array(
							'px' => array(
								'max' => 3,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							'{{WRAPPER}}' => '--cmsmasters-img-transition-duration: {{SIZE}}s',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'separator_panel_style',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_responsive_control(
			'width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => '%',
				),
				'tablet_default' => array(
					'unit' => '%',
				),
				'mobile_default' => array(
					'unit' => '%',
				),
				'size_units' => array( '%', 'px', 'vw' ),
				'range' => array(
					'%' => array(
						'min' => 1,
						'max' => 100,
					),
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
					'vw' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-container-width: {{SIZE}}{{UNIT}}; --cmsmasters-image-width: 100%;',
				),
			)
		);

		$this->add_responsive_control(
			'space',
			array(
				'label' => __( 'Max Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => '%',
				),
				'tablet_default' => array(
					'unit' => '%',
				),
				'mobile_default' => array(
					'unit' => '%',
				),
				'size_units' => array( '%', 'px', 'vw' ),
				'range' => array(
					'%' => array(
						'min' => 1,
						'max' => 100,
					),
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
					'vw' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-container-max-width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
				),
				'tablet_default' => array(
					'unit' => 'px',
				),
				'mobile_default' => array(
					'unit' => 'px',
				),
				'size_units' => array( 'px', 'vh' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 500,
					),
					'vh' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-container-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'object-fit',
			array(
				'label' => __( 'Object Fit', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'fill' => __( 'Fill', 'cmsmasters-elementor' ),
					'cover' => __( 'Cover', 'cmsmasters-elementor' ),
					'contain' => __( 'Contain', 'cmsmasters-elementor' ),
				),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-object-fit: {{VALUE}};',
				),
				'condition' => array(
					'height[size]!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'image_border',
				'fields_options' => array(
					'border' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-image-border-style: {{VALUE}};',
						),
					),
					'width' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-image-border-top-width: {{TOP}}{{UNIT}}; --cmsmasters-image-border-right-width: {{RIGHT}}{{UNIT}}; --cmsmasters-image-border-bottom-width: {{BOTTOM}}{{UNIT}}; --cmsmasters-image-border-left-width: {{LEFT}}{{UNIT}};',
						),
					),
					'color' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-image-border-color: {{VALUE}};',
						),
					),
				),
				'separator' => 'before',
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_responsive_control(
			'image_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-image-border-radius : {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Section Style Hotspot
		 */
		$this->start_controls_section(
			'section_style_hotspot',
			array(
				'label' => __( 'Hotspot', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'style_typography',
				'selector' => "{{WRAPPER}} {$this->hotspot_class}__label",
			)
		);

		$this->start_controls_tabs( 'style_hotspot_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$this->start_controls_tab(
				"style_hotspot_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"style_hotspot_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-hotspot-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"style_hotspot_box_color_{$key}",
				array(
					'label' => __( 'Box Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-hotspot-box-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"style_hotspot_bd_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-hotspot-bd-color-{$key}: {{VALUE}};",
					),
				)
			);

			$state = ( 'hover' === $key ) ? ':hover' : '';

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "style_hotspot_box_shadow_{$key}",
					'selector' => "
						{{WRAPPER}} {$this->hotspot_class}__wrapper {$this->hotspot_class}__button{$state}
					",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'style_hotspot_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 300,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', '%' ),
				'default' => array(
					'unit' => 'px',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-hotspot-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hotspot_icon_spacing',
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default' => array(
					'unit' => 'px',
				),
				'selectors' => array(
					"{{WRAPPER}}" => '--cmsmasters-icon-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'style_border_hotspot',
				'selector' => "
					{{WRAPPER}} {$this->hotspot_class}__wrapper {$this->hotspot_class}__button",
				'separator' => 'before',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'style_hotspot_width',
			array(
				'label' => __( 'Min Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					),
				),
				'separator' => 'before',
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-hotspot-min-width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'style_hotspot_height',
			array(
				'label' => __( 'Min Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-hotspot-min-height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'style_hotspot_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-hotspot-padding: {{SIZE}}{{UNIT}};',
				),
				'default' => array(
					'unit' => 'px',
				),
			)
		);

		$this->add_control(
			'style_hotspot_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-hotspot-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'default' => array(
					'unit' => 'px',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Section Style Tooltip
		 */
		$this->start_controls_section(
			'section_style_tooltip',
			array(
				'label' => __( 'Tooltip', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'style_tooltip_typography',
				'selector' => "{{WRAPPER}} {$this->hotspot_class}__tooltip",
			)
		);

		$this->add_control(
			'style_tooltip_text_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-tooltip-text-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'style_tooltip_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-tooltip-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'style_tooltip_arrow_color',
			array(
				'label' => __( 'Arrow Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-tooltip-arrow-color: {{VALUE}}',
				),
				'condition' => array(
					'tooltip_arrow' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'style_tooltip_box_shadow',
				'selector' => "{{WRAPPER}} {$this->hotspot_class}__tooltip",
			)
		);

		$this->add_responsive_control(
			'style_tooltip_align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-tooltip-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'style_tooltip_gap',
			array(
				'label' => __( 'Box Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-tooltip-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'style_tooltip_width',
			array(
				'label' => __( 'Box Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 2000,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-tooltip-min-width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'style_tooltip_arrow_size',
			array(
				'label' => __( 'Arrow Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-tooltip-arrow-size: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'tooltip_arrow' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'style_tooltip_border',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-hotspot__tooltip',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'style_tooltip_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-tooltip-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'style_tooltip_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default' => array(
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-tooltip-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'hotspot_tooltip_button_style',
			array(
				'label' => __( 'Tooltip Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$selector = "{{WRAPPER}} .elementor-widget-cmsmasters-hotspot__tooltip-button";

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'hotspot_tooltip_button_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $selector,
			)
		);

		$this->start_controls_tabs( 'hotspot_tooltip_button_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {

			$this->start_controls_tab(
				"hotspot_tooltip_button_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$element = ( 'hover' === $key ) ? ':after' : ':before';
			$state = ( 'hover' === $key ) ? ':hover' : '';

			$this->add_control(
				"hotspot_tooltip_button_text_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-tooltip-btn-text-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"hotspot_tooltip_button_bg_{$key}_background",
				array(
					'label' => __( 'Background Type', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'color' => array(
							'title' => __( 'Color', 'cmsmasters-elementor' ),
							'icon' => 'eicon-paint-brush',
						),
						'gradient' => array(
							'title' => __( 'Gradient', 'cmsmasters-elementor' ),
							'icon' => 'eicon-barcode',
						),
					),
					'default' => 'color',
					'toggle' => false,
					'render_type' => 'ui',
				)
			);

			$this->add_control(
				"hotspot_tooltip_button_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$selector . $element => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"hotspot_tooltip_button_bg_{$key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"hotspot_tooltip_button_bg_{$key}_color_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 0,
					),
					'render_type' => 'ui',
					'condition' => array(
						"hotspot_tooltip_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"hotspot_tooltip_button_bg_{$key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"hotspot_tooltip_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"hotspot_tooltip_button_bg_{$key}_color_b_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'render_type' => 'ui',
					'condition' => array(
						"hotspot_tooltip_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"hotspot_tooltip_button_bg_{$key}_gradient_type",
				array(
					'label' => __( 'Type', 'cmsmasters-elementor' ),
					'label_block' => false,
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'linear' => __( 'Linear', 'cmsmasters-elementor' ),
						'radial' => __( 'Radial', 'cmsmasters-elementor' ),
					),
					'default' => 'linear',
					'render_type' => 'ui',
					'condition' => array(
						"hotspot_tooltip_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"hotspot_tooltip_button_bg_{$key}_gradient_angle",
				array(
					'label' => __( 'Angle', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'deg' ),
					'default' => array(
						'unit' => 'deg',
						'size' => 180,
					),
					'range' => array(
						'deg' => array( 'step' => 10 ),
					),
					'selectors' => array(
						$selector . $element => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{hotspot_tooltip_button_bg_{$key}_color_stop.SIZE}}{{hotspot_tooltip_button_bg_{$key}_color_stop.UNIT}}, {{hotspot_tooltip_button_bg_{$key}_color_b.VALUE}} {{hotspot_tooltip_button_bg_{$key}_color_b_stop.SIZE}}{{hotspot_tooltip_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"hotspot_tooltip_button_bg_{$key}_background" => array( 'gradient' ),
						"hotspot_tooltip_button_bg_{$key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"hotspot_tooltip_button_bg_{$key}_gradient_position",
				array(
					'label' => __( 'Position', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => array(
						'center center' => __( 'Center Center', 'cmsmasters-elementor' ),
						'center left' => __( 'Center Left', 'cmsmasters-elementor' ),
						'center right' => __( 'Center Right', 'cmsmasters-elementor' ),
						'top center' => __( 'Top Center', 'cmsmasters-elementor' ),
						'top left' => __( 'Top Left', 'cmsmasters-elementor' ),
						'top right' => __( 'Top Right', 'cmsmasters-elementor' ),
						'bottom center' => __( 'Bottom Center', 'cmsmasters-elementor' ),
						'bottom left' => __( 'Bottom Left', 'cmsmasters-elementor' ),
						'bottom right' => __( 'Bottom Right', 'cmsmasters-elementor' ),
					),
					'default' => 'center center',
					'selectors' => array(
						$selector . $element => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{hotspot_tooltip_button_bg_{$key}_color_stop.SIZE}}{{hotspot_tooltip_button_bg_{$key}_color_stop.UNIT}}, {{hotspot_tooltip_button_bg_{$key}_color_b.VALUE}} {{hotspot_tooltip_button_bg_{$key}_color_b_stop.SIZE}}{{hotspot_tooltip_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"hotspot_tooltip_button_bg_{$key}_background" => array( 'gradient' ),
						"hotspot_tooltip_button_bg_{$key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"hotspot_tooltip_button_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-tooltip-btn-bd-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_responsive_control(
				"hotspot_tooltip_button_border_radius_{$key}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-tooltip-btn-bdr-color-{$key}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "hotspot_tooltip_button_shadow_text_{$key}",
					'selector' => $selector . $state,
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "hotspot_tooltip_button_shadow_{$key}",
					'selector' => $selector . $state,
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'button_align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'separator' => 'before',
				'default' => '',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'hotspot_tooltip_border_button',
				'separator' => 'before',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => __( 'None', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
					),
					'width' => array(
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'selector' => $selector,
			)
		);

		$this->add_responsive_control(
			'hotspot_tooltip_button_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-tooltip-btn-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hotspot_tooltip_button_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-tooltip-btn-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render google maps widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.8.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$is_tooltip_direction_animation = "slide-direction" === $settings['tooltip_animation'] || "fade-direction" === $settings['tooltip_animation'];

		echo '<div class="' . $this->hotspot_selector . '__container">';

		// Main Image
		Group_Control_Image_Size::print_attachment_image_html( $settings, 'image', 'image' );

		// Hotspot
		foreach ( $settings['hotspot'] as $key => $hotspot ) {
			$is_circle = ! $hotspot['hotspot_label'] && ! $hotspot['hotspot_icon']['value'];
			$is_only_icon = ! $hotspot['hotspot_label'] && $hotspot['hotspot_icon']['value'];
			$is_only_label = $hotspot['hotspot_label'] && ! $hotspot['hotspot_icon']['value'];
			$hotspot_position_x = '%' === $hotspot['hotspot_offset_x']['unit'] ? "{$this->hotspot_selector}__position-" . $hotspot['hotspot_horizontal'] : '';
			$hotspot_position_y = '%' === $hotspot['hotspot_offset_y']['unit'] ? "{$this->hotspot_selector}__position-" . $hotspot['hotspot_vertical'] : '';
			$is_hotspot_link = ! empty( $hotspot['hotspot_link']['url'] );
			$hotspot_element_tag = $is_hotspot_link ? 'a' : 'div';

			// hotspot attributes
			$hotspot_repeater_setting_key = $this->get_repeater_setting_key( 'hotspot', 'hotspots', $key );
			$this->add_render_attribute(
				$hotspot_repeater_setting_key, array(
					'class' => array(
						"{$this->hotspot_selector}__wrapper",
						'elementor-repeater-item-' . $hotspot['_id'],
						$hotspot_position_x,
						$hotspot_position_y,
						$is_hotspot_link ? "{$this->hotspot_selector}__link" : '',
					),
				)
			);

			if ( $is_circle ) {
				$this->add_render_attribute( $hotspot_repeater_setting_key, 'class', "{$this->hotspot_selector}__circle" );
			}

			if ( $is_hotspot_link ) {
				$this->add_link_attributes( $hotspot_repeater_setting_key, $hotspot['hotspot_link'] );
			}

			// hotspot trigger attributes
			$trigger_repeater_setting_key = $this->get_repeater_setting_key( 'trigger', 'hotspots', $key );
			$this->add_render_attribute(
				$trigger_repeater_setting_key, array(
					'class' => array(
						"{$this->hotspot_selector}__button",
					),
				)
			);

			if ( $is_only_icon ) {
				$this->add_render_attribute( $trigger_repeater_setting_key, 'class', "{$this->hotspot_selector}__icon-only" );
			}

			if ( $is_only_label ) {
				$this->add_render_attribute(
					$trigger_repeater_setting_key, array(
						'class' => array(
							"{$this->hotspot_selector}__button-label-only",
						),
					)
				);
			}

			if ( $hotspot['hotspot_label'] ) {
				$this->add_render_attribute(
					$trigger_repeater_setting_key, array(
						'class' => array(
							"{$this->hotspot_selector}__button-label",
							"{$this->hotspot_selector}__button-icon-{$settings['hotspot_icon_position']}",
						),
					)
				);
			}

			if ( 'infinity' === $settings['animation_type'] ) {
				$this->add_render_attribute(
					$trigger_repeater_setting_key, array(
						'class' => array(
							$settings['hotspot_animation'],
						),
					)
				);
			} elseif ( 'hover' === $settings['animation_type'] ) {
				$this->add_render_attribute(
					$trigger_repeater_setting_key, array(
						'class' => array(
							"elementor-animation-{$settings['hover_hotspot_animation']}",
						),
					)
				);
			}

			if ( ! empty( $hotspot['hotspot_icon_active']['value'] ) ) {
				$this->add_render_attribute(
					$trigger_repeater_setting_key, array(
						'class' => array(
							"{$this->hotspot_selector}__button-has-icon-active",
						),
					)
				);
			}

			//direction mask attributes
			$direction_mask_repeater_setting_key = $this->get_repeater_setting_key( 'hotspot-direction-mask', 'hotspots', $key );
			$this->add_render_attribute(
				$direction_mask_repeater_setting_key, array(
					'class' => array(
						"{$this->hotspot_selector}__direction-mask",
						"{$this->hotspot_selector}__tooltip-position-{$settings['tooltip_position']}",
						"{$this->hotspot_selector}__tooltip-position-custom-{$hotspot['hotspot_position']}",
						( $is_tooltip_direction_animation ) ? "{$this->hotspot_selector}__tooltip-position" : '',
					),
				)
			);

			//tooltip attributes
			$tooltip_position = ( $is_tooltip_direction_animation && $hotspot['hotspot_tooltip_position'] && $hotspot['hotspot_position'] ) ? "{$this->hotspot_selector}__override-tooltip-animation-from-" . $hotspot['hotspot_position'] : '';
			$tooltip_position_custom = ( $hotspot['hotspot_tooltip_position'] ) ? "{$this->hotspot_selector}__custom-position-yes" : '';
			$tooltip_repeater_setting_key = $this->get_repeater_setting_key( 'tooltip', 'hotspots', $key );
			$this->add_render_attribute(
				$tooltip_repeater_setting_key, array(
					'class' => array(
						"{$this->hotspot_selector}__tooltip",
						( ! $is_tooltip_direction_animation ) ? "{$this->hotspot_selector}__tooltip-position" : '',
						$settings['tooltip_animation'],
						"{$this->hotspot_selector}__tooltip-position-{$settings['tooltip_position']}",
						"{$this->hotspot_selector}__tooltip-position-custom-{$hotspot['hotspot_position']}",
						$tooltip_position,
						$tooltip_position_custom,
						"{$this->hotspot_selector}__tooltip-arrow-{$settings['tooltip_arrow']}",
						"{$this->hotspot_selector}__tooltip-arrow-{$settings['tooltip_position']}",
						"{$this->hotspot_selector}__tooltip-arrow-custom-{$hotspot['hotspot_position']}",
					),
				)
			);

			$tag = $hotspot_element_tag;

			echo "<{$tag} {$this->get_render_attribute_string( $hotspot_repeater_setting_key )}>
				<div {$this->get_render_attribute_string( $trigger_repeater_setting_key )}>";

					if ( $is_circle ) {
						echo "<div class='{$this->hotspot_selector}__outer-circle'></div>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo "<div class='{$this->hotspot_selector}__inner-circle'></div>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {

						if ( $hotspot['hotspot_icon']['value'] || $hotspot['hotspot_icon_active']['value'] ) {
							echo "<div class='{$this->hotspot_selector}__icon-wrapper'>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

								if ( $hotspot['hotspot_icon']['value'] ) {
									echo "<div class='{$this->hotspot_selector}__icon'>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										Icons_Manager::render_icon( $hotspot['hotspot_icon'], array( 'aria-hidden' => 'true' ) );
									echo "</div>";

									if ( $hotspot['hotspot_icon_active']['value'] ) {
										echo "<div class='{$this->hotspot_selector}__icon {$this->hotspot_selector}__icon-active'>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											Icons_Manager::render_icon( $hotspot['hotspot_icon_active'], array( 'aria-hidden' => 'true' ) );
										echo "</div>";
									}
								}

							echo "</div>";
						}

						if ( ! empty( $hotspot['hotspot_label'] ) ) {
							echo "<div class='{$this->hotspot_selector}__label'>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo esc_html( $hotspot['hotspot_label'] );
							echo "</div>";
						}
					}
				echo "</div>";

				if ( $hotspot['hotspot_tooltip_content'] ) {
					if ( $is_tooltip_direction_animation ) {
						echo "<div {$this->get_render_attribute_string( $direction_mask_repeater_setting_key )}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}

						echo "<div {$this->get_render_attribute_string( $tooltip_repeater_setting_key )}>
							{$this->parse_text_editor( $hotspot['hotspot_tooltip_content'] )}
							{$this->render_button( $hotspot )}
						</div>";

					if ( $is_tooltip_direction_animation ) {
						echo "</div>";
					}
				}

			echo "</{$tag}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>';
	}

	/**
	 * Get button.
	 *
	 * Retrieve button.
	 *
	 * @since 1.8.0
	 *
	 */
	protected function render_button( $hotspot ) {
		if ( 'yes' === $hotspot['hotspot_button_switcher'] ) {
			ob_start();

			$settings = $this->get_settings_for_display();

			$this->add_render_attribute( 'button', 'class', array(
				"{$this->hotspot_selector}__tooltip-button",
				'cmsmasters-theme-button',
			) );

			if ( ! empty( $hotspot['hotspot_button_link']['url'] ) ) {
				$this->add_link_attributes( 'buttont', $hotspot['hotspot_button_link'] );
			}

			$button_text = ( empty( $hotspot['hotspot_button_label'] ) ) ? __( 'Click Here', 'cmsmasters-elementor' ) : esc_html( $hotspot['hotspot_button_label'] );

			if ( ! empty( $button_text ) ) {
				echo "<div class='{$this->hotspot_selector}__tooltip-button-wrapper {$this->hotspot_selector}__tooltip-button-align-{$settings['button_align']}'>
					<a {$this->get_render_attribute_string( 'button' )}>{$button_text}</a>
				</div>";
			}

			return ob_get_clean();
		}
	}

	/**
	 * Render Hotspot widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since  1.8.0
	 * @access protected
	 */
	protected function content_template() {
		?>
		<div class="elementor-widget-cmsmasters-hotspot__container">
		<#
		const image = {
			id: settings.image.id,
			url: settings.image.url,
			size: settings.image_size,
			dimension: settings.image_custom_dimension,
			model: view.getEditModel()
		};

		const imageUrl = elementor.imagesManager.getImageUrl( image );

		#>
		<img src="{{ imageUrl }}" title="" alt="">
		<#
		const isTooltipDirectionAnimation = (settings.tooltip_animation==='slide-direction' || settings.tooltip_animation==='fade-direction' ) ? true : false;

		_.each( settings.hotspot, ( hotspot, index ) => {
			const iconHTML = elementor.helpers.renderIcon( view, hotspot.hotspot_icon, {}, 'i' , 'object' );
			const iconHTMLActive = elementor.helpers.renderIcon( view, hotspot.hotspot_icon_active, {}, 'i' , 'object' );

			const isCircle = !hotspot.hotspot_label && !hotspot.hotspot_icon.value;
			const isOnlyIcon = !hotspot.hotspot_label && hotspot.hotspot_icon.value;
			const isOnlyLabel = hotspot.hotspot_label && !hotspot.hotspot_icon.value;
			const hotspotPositionX = '%' === hotspot.hotspot_offset_x.unit ? 'elementor-widget-cmsmasters-hotspot__position-' + hotspot.hotspot_horizontal : '';
			const hotspotPositionY = '%' === hotspot.hotspot_offset_y.unit ? 'elementor-widget-cmsmasters-hotspot__position-' + hotspot.hotspot_vertical : '';
			const hotspotLink = hotspot.hotspot_link.url;
			const hotspotElementTag = hotspotLink ? 'a': 'div';

			// hotspot attributes
			const hotspotRepeaterSettingKey = view.getRepeaterSettingKey( 'hotspot', 'hotspots', index );
			view.addRenderAttribute( hotspotRepeaterSettingKey, {
				'class' : [
					'elementor-widget-cmsmasters-hotspot__wrapper',
					'elementor-repeater-item-' + hotspot._id,
					hotspotPositionX,
					hotspotPositionY,
					hotspotLink ? 'elementor-widget-cmsmasters-hotspot__link' : '',
				]
			});

			if ( isCircle ) {
				view.addRenderAttribute( hotspotRepeaterSettingKey, 'class', 'elementor-widget-cmsmasters-hotspot__circle' );
			}

			// hotspot trigger attributes
			const triggerRepeaterSettingKey = view.getRepeaterSettingKey( 'trigger', 'hotspots', index );
			view.addRenderAttribute(triggerRepeaterSettingKey, {
				'class' : [
					'elementor-widget-cmsmasters-hotspot__button',
				]
			});

			if ( isOnlyIcon ) {
				view.addRenderAttribute( triggerRepeaterSettingKey, 'class', 'elementor-widget-cmsmasters-hotspot__icon-only' );
			}

			if ( isOnlyLabel ) {
				view.addRenderAttribute( triggerRepeaterSettingKey, 'class', 'elementor-widget-cmsmasters-hotspot__label-only' );
			}

			if ( hotspot.hotspot_label ) {
				view.addRenderAttribute( triggerRepeaterSettingKey, {
					'class' : [
						'elementor-widget-cmsmasters-hotspot__button-label',
						'elementor-widget-cmsmasters-hotspot__button-icon-' + settings.hotspot_icon_position,
					]
				});
			}

			if ( 'infinity' === settings.animation_type ) {
				view.addRenderAttribute( triggerRepeaterSettingKey, {
					'class' : [
						settings.hotspot_animation,
					]
				});
			}

			if ( 'hover' === settings.animation_type ) {
				view.addRenderAttribute( triggerRepeaterSettingKey, {
					'class' : [
						'elementor-animation-' + settings.hover_hotspot_animation,
					]
				});
			}

			if ( '' !== hotspot.hotspot_icon_active.value ) {
				view.addRenderAttribute( triggerRepeaterSettingKey, {
					'class' : [
						'elementor-widget-cmsmasters-hotspot__button-has-icon-active',
					]
				});
			}

			//direction mask attributes
			const directionMaskRepeaterSettingKey = view.getRepeaterSettingKey( 'hotspot-direction-mask', 'hotspots', index );
			view.addRenderAttribute(directionMaskRepeaterSettingKey, {
				'class' : [
					'elementor-widget-cmsmasters-hotspot__direction-mask',
					'elementor-widget-cmsmasters-hotspot__tooltip-position-' + settings.tooltip_position,
					'elementor-widget-cmsmasters-hotspot__tooltip-position-custom-' + hotspot.hotspot_position,
					( isTooltipDirectionAnimation ) ? 'elementor-widget-cmsmasters-hotspot__tooltip-position' : '',
				]
			});

			//tooltip attributes
			const tooltipPosition = ( hotspot.hotspot_tooltip_position ) ? 'elementor-widget-cmsmasters-hotspot__custom-position-yes' : '';
			const tooltipCustomPosition = ( isTooltipDirectionAnimation && hotspot.hotspot_tooltip_position && hotspot.hotspot_position ) ? 'elementor-widget-cmsmasters-hotspot__override-tooltip-animation-from-' + hotspot.hotspot_position : '';
			const tooltipRepeaterSettingKey = view.getRepeaterSettingKey('tooltip', 'hotspots', index);
			view.addRenderAttribute( tooltipRepeaterSettingKey, {
				'class': [
					'elementor-widget-cmsmasters-hotspot__tooltip',
					'elementor-widget-cmsmasters-hotspot__show-tooltip',
					( !isTooltipDirectionAnimation ) ? 'elementor-widget-cmsmasters-hotspot__tooltip-position' : '',
					settings.tooltip_animation,
					tooltipCustomPosition,
					'elementor-widget-cmsmasters-hotspot__tooltip-position-' + settings.tooltip_position,
					'elementor-widget-cmsmasters-hotspot__tooltip-arrow-' + settings.tooltip_arrow,
					'elementor-widget-cmsmasters-hotspot__tooltip-arrow-' + settings.tooltip_position,
				],
			});

			if ( hotspot.hotspot_tooltip_position ) {
				view.addRenderAttribute( tooltipRepeaterSettingKey, {
				'class': [
					tooltipPosition,
					'elementor-widget-cmsmasters-hotspot__tooltip-position-custom-' + hotspot.hotspot_position,
					'elementor-widget-cmsmasters-hotspot__tooltip-arrow-custom-' + hotspot.hotspot_position,
				],
			});
			}

			#>
			<{{{ hotspotElementTag }}} {{{ view.getRenderAttributeString( hotspotRepeaterSettingKey ) }}}>

					<?php // Hotspot Trigger ?>
					<div {{{ view.getRenderAttributeString( triggerRepeaterSettingKey ) }}}>
						<# if ( isCircle ) { #>
						<div class="elementor-widget-cmsmasters-hotspot__outer-circle"></div>
						<div class="elementor-widget-cmsmasters-hotspot__inner-circle"></div>
						<# } else { #>
						<# if (hotspot.hotspot_icon.value || hotspot.hotspot_icon_active.value ){ #>
						<div class="elementor-widget-cmsmasters-hotspot__icon-wrapper">
						<# if (hotspot.hotspot_icon.value ){ #>
						<div class="elementor-widget-cmsmasters-hotspot__icon">{{{ iconHTML.value }}}</div>
						<# } #>
						<# if (hotspot.hotspot_icon_active.value ){ #>
						<div class="elementor-widget-cmsmasters-hotspot__icon elementor-widget-cmsmasters-hotspot__icon-active">{{{ iconHTMLActive.value }}}</div>
						<# } #>
						</div>
						<# } #>
						<# if ( hotspot.hotspot_label ){ #>
						<div class="elementor-widget-cmsmasters-hotspot__label">{{{ hotspot.hotspot_label }}}</div>
						<# } #>
						<# } #>
					</div>

					<?php // Hotspot Tooltip ?>
					<# if( hotspot.hotspot_tooltip_content && ! ( 'click' === settings.tooltip_trigger && hotspotLink ) ){ #>
					<# if( isTooltipDirectionAnimation ){ #>
					<div {{{ view.getRenderAttributeString( directionMaskRepeaterSettingKey ) }}}>
						<# } #>
						<div {{{ view.getRenderAttributeString( tooltipRepeaterSettingKey ) }}}>
							{{{ hotspot.hotspot_tooltip_content }}}

							<# if( 'yes' === hotspot.hotspot_button_switcher ){
							const buttonText = ( '' === hotspot.hotspot_button_label ) ? 'Click Here' : hotspot.hotspot_button_label;

							if( '' !== buttonText ){ #>
								<div class="elementor-widget-cmsmasters-hotspot__tooltip-button-wrapper elementor-widget-cmsmasters-hotspot__tooltip-button-align-{{{settings.button_align}}}">
								<a href="#" class="cmsmasters-theme-button elementor-widget-cmsmasters-hotspot__tooltip-button" >{{{ buttonText }}}</a>
								</div>
							<# } #>
							<# } #>

						</div>
						<# if( isTooltipDirectionAnimation ){ #>
					</div>
					<# } #>
					<# } #>

			</{{{ hotspotElementTag }}}>
		<# }); #>
		</div>
		<?php
	}
}
