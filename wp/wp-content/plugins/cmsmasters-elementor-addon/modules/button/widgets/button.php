<?php
namespace CmsmastersElementor\Modules\Button\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon button widget.
 *
 * Addon widget that displays button.
 *
 * @since 1.0.0
 */
class Button extends Base_Widget {

	/**
	 * Get widget name.
	 *
	 * Retrieve button widget name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmsmasters-button';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve button widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Button', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve button widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-button';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array( 'button' );
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
			'widget-cmsmasters-button',
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
	 * Get button sizes.
	 *
	 * Retrieve an array of button sizes for the button widget.
	 *
	 * @since 1.0.0
	 *
	 * @return array An array containing button sizes.
	 */
	public static function get_button_sizes() {
		return array(
			'xs' => __( 'Extra Small', 'cmsmasters-elementor' ),
			'sm' => __( 'Small', 'cmsmasters-elementor' ),
			'md' => __( 'Medium', 'cmsmasters-elementor' ),
			'lg' => __( 'Large', 'cmsmasters-elementor' ),
			'xl' => __( 'Extra Large', 'cmsmasters-elementor' ),
		);
	}

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-button';
	}

	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added gradient group control on normal state and gradient group
	 * control, `Border Radius`, `Text Shadow`, `Box Shadow` controls on hover state.
	 * Added `Border Color` control on normal state and `Border Color`, `Border Radius`,
	 * `Text Decoration` controls on hover state for button icon. Added 'em', '%' and 'vw'
	 * size units for `Size`, `Padding`, `Spacing` and `Gap` controls. Added `Spacing` control
	 * for icon on hover.
	 * @since 1.2.0 Fixed `Spacing` control for icon. Changed `Min Width` to responsive control.
	 * Enabled `Spacing` and `Min Width` controls for `Alignment` justify. Changed `Padding` and
	 * `Border Width` control for icon to responsive. Fixed display to the `Arrangement` control.
	 * @since 1.3.3 Added value top to the "Position" control.
	 * @since 1.6.0 Fixed application of color for the icon.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {
		$widget_selector = $this->get_widget_selector();

		$this->start_controls_section(
			'section_button',
			array( 'label' => __( 'Button', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Click here', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
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
					'justify' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'default' => '',
				'prefix_class' => 'cmsmasters-button%s-align-',
			)
		);

		$this->add_control(
			'size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => self::get_button_sizes(),
				'style_transfer' => true,
			)
		);

		$this->add_control(
			'button_icon_heading_cont',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'selected_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'show_label' => false,
			)
		);

		$this->add_control(
			'icon_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => array( 'title' => __( 'Default', 'cmsmasters-elementor' ) ),
					'stacked' => array( 'title' => __( 'Stacked', 'cmsmasters-elementor' ) ),
					'framed' => array( 'title' => __( 'Framed', 'cmsmasters-elementor' ) ),
				),
				'default' => 'default',
				'label_block' => false,
				'render_type' => 'template',
				'condition' => array( 'selected_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'button_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'square' => array( 'title' => __( 'Square', 'cmsmasters-elementor' ) ),
					'circle' => array( 'title' => __( 'Circle', 'cmsmasters-elementor' ) ),
				),
				'default' => 'square',
				'label_block' => false,
				'render_type' => 'template',
				'condition' => array(
					'selected_icon[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'icon_align',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'left' => array( 'title' => __( 'Before', 'cmsmasters-elementor' ) ),
					'top' => array( 'title' => __( 'Top', 'cmsmasters-elementor' ) ),
					'right' => array( 'title' => __( 'After', 'cmsmasters-elementor' ) ),
				),
				'default' => 'left',
				'toggle' => false,
				'label_block' => false,
				'condition' => array( 'selected_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'button_icon_arrangement',
			array(
				'label' => __( 'Arrangement', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'description' => __( 'Applies only for Justified Alignment.', 'cmsmasters-elementor' ),
				'options' => array(
					'together' => array( 'title' => __( 'Together', 'cmsmasters-elementor' ) ),
					'side' => array( 'title' => __( 'Side', 'cmsmasters-elementor' ) ),
				),
				'default' => 'together',
				'label_block' => false,
				'prefix_class' => 'cmsmasters-icon-arrangement-',
				'condition' => array( 'selected_icon[value]!' => '' ),
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

		$this->add_control(
			'button_description_heading',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_description',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'label_block' => true,
				'show_label' => false,
			)
		);

		$this->add_control(
			'button_description_block',
			array(
				'label' => __( 'Display Block', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => array(
					'align!' => 'justify',
					'button_description!' => '',
				),
			)
		);

		$this->add_control(
			'button_css_id',
			array(
				'label' => __( 'Button ID', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => '',
				'title' => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'cmsmasters-elementor' ),
				'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography',
				'fields_options' => array(
					'text_decoration' => array(
						'selectors' => array(
							'{{WRAPPER}}' => '--button-text-decoration: {{VALUE}};',
						),
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__button,
					{{WRAPPER}} ' . $widget_selector . '__button:not([href]):not([tabindex])',
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'button_text_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cmsmasters-icon-view-default ' . $widget_selector . '__icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}} .cmsmasters-icon-view-stacked ' . $widget_selector . '__icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .cmsmasters-icon-view-framed ' . $widget_selector . '__icon' => 'color: {{VALUE}}; fill: {{VALUE}}; border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background_background',
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
			'background_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button' => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
					'{{WRAPPER}} .cmsmasters-icon-view-stacked ' . $widget_selector . '__icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'condition' => array(
					'button_background_background' => array(
						'color',
						'gradient',
					),
				),
			)
		);

		$this->add_control(
			'button_background_color_stop',
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
					'button_background_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_background_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'button_background_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_background_color_b_stop',
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
					'button_background_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_background_gradient_type',
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
					'button_background_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_background_gradient_angle',
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
					'{{WRAPPER}} ' . $widget_selector . '__button' => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{button_background_color_stop.SIZE}}{{button_background_color_stop.UNIT}}, {{button_background_color_b.VALUE}} {{button_background_color_b_stop.SIZE}}{{button_background_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'button_background_background' => array( 'gradient' ),
					'button_background_gradient_type' => 'linear',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_background_gradient_position',
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
					'{{WRAPPER}} ' . $widget_selector . '__button' => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{button_background_color_stop.SIZE}}{{button_background_color_stop.UNIT}}, {{button_background_color_b.VALUE}} {{button_background_color_b_stop.SIZE}}{{button_background_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'button_background_background' => array( 'gradient' ),
					'button_background_gradient_type' => 'radial',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'button_border_border!' => 'none' ),
			)
		);

		$this->add_control(
			'button_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__button',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__text',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'hover_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button:hover,
					{{WRAPPER}} ' . $widget_selector . '__button:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .cmsmasters-icon-view-default:hover ' . $widget_selector . '__icon,
					{{WRAPPER}} .cmsmasters-icon-view-default:focus ' . $widget_selector . '__icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}} .cmsmasters-icon-view-stacked:hover ' . $widget_selector . '__icon,
					{{WRAPPER}} .cmsmasters-icon-view-stacked:focus ' . $widget_selector . '__icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .cmsmasters-icon-view-framed:hover ' . $widget_selector . '__icon,
					{{WRAPPER}} .cmsmasters-icon-view-framed:focus ' . $widget_selector . '__icon' => 'color: {{VALUE}}; fill: {{VALUE}}; border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background_hover_background',
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
			'button_background_hover_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button:hover,
					{{WRAPPER}} ' . $widget_selector . '__button:focus' => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
					'{{WRAPPER}} .cmsmasters-icon-view-stacked:hover ' . $widget_selector . '__icon,
					{{WRAPPER}} .cmsmasters-icon-view-stacked:focus ' . $widget_selector . '__icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'condition' => array(
					'button_background_hover_background' => array(
						'color',
						'gradient',
					),
				),
			)
		);

		$this->add_control(
			'button_background_hover_color_stop',
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
					'button_background_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_background_hover_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'button_background_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_background_hover_color_b_stop',
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
					'button_background_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_background_hover_gradient_type',
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
					'button_background_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_background_hover_gradient_angle',
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
					'{{WRAPPER}} ' . $widget_selector . '__button:hover,
	 				{{WRAPPER}} ' . $widget_selector . '__button:focus' => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{button_background_hover_color_stop.SIZE}}{{button_background_hover_color_stop.UNIT}}, {{button_background_hover_color_b.VALUE}} {{button_background_hover_color_b_stop.SIZE}}{{button_background_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'button_background_hover_background' => array( 'gradient' ),
					'button_background_hover_gradient_type' => 'linear',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_background_hover_gradient_position',
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
					'{{WRAPPER}} ' . $widget_selector . '__button:hover,
	 				{{WRAPPER}} ' . $widget_selector . '__button:focus' => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{button_background_hover_color_stop.SIZE}}{{button_background_hover_color_stop.UNIT}}, {{button_background_hover_color_b.VALUE}} {{button_background_hover_color_b_stop.SIZE}}{{button_background_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'button_background_hover_background' => array( 'gradient' ),
					'button_background_hover_gradient_type' => 'radial',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_hover_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button:hover,
					{{WRAPPER}} ' . $widget_selector . '__button:focus' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'button_border_border!' => 'none' ),
			)
		);

		$this->add_control(
			'button_hover_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button:hover, ' .
					'{{WRAPPER}} ' . $widget_selector . '__button:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_hover_text_decoration',
			array(
				'label' => __( 'Text Decoration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'underline' => __( 'Underline', 'cmsmasters-elementor' ),
					'overline' => __( 'Overline', 'cmsmasters-elementor' ),
					'line-through' => __( 'Line Through', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--button-hover-text-decoration: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'button_hover_box_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__button:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'button_hover_text_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__button:hover ' . $widget_selector . '__text',
			)
		);

		$this->add_control(
			'hover_animation',
			array(
				'label' => __( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'button_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_responsive_control(
			'button_width',
			array(
				'label' => __( 'Min Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 50,
						'max' => 1000,
					),
					'%' => array(
						'min' => 20,
					),
					'em' => array(
						'max' => 30,
					),
				),
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button-container-inner:not(.cmsmasters-with-percentage) ' . $widget_selector . '__button' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__button-container-inner.cmsmasters-with-percentage' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'text_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'button_border',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => __( 'None', 'cmsmasters-elementor' ),
							'solid' => __( 'Solid', 'cmsmasters-elementor' ),
							'double' => __( 'Double', 'cmsmasters-elementor' ),
							'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
							'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
							'groove' => __( 'Groove', 'cmsmasters-elementor' ),
						),
						'default' => '',
					),
					'width' => array(
						'label' => __( 'Border Width', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__button',
			)
		);

		$this->add_control(
			'button_icon_heading',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'selected_icon[value]!' => '' ),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'selected_icon[value]!' => '' ),
			)
		);

		$this->start_controls_tabs(
			'tabs_button_icon_style',
			array( 'condition' => array( 'selected_icon[value]!' => '' ) )
		);

		$this->start_controls_tab(
			'tab_button_icon_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'button_icon_color',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button.cmsmasters-icon-view-default ' . $widget_selector . '__icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__button.cmsmasters-icon-view-stacked ' . $widget_selector . '__icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__button.cmsmasters-icon-view-framed ' . $widget_selector . '__icon' => 'color: {{VALUE}}; fill: {{VALUE}}; border-color: {{VALUE}};',
				),
				'condition' => array( 'selected_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'button_icon_bg_color',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button ' . $widget_selector . '__icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__button.cmsmasters-icon-view-stacked ' . $widget_selector . '__icon' => 'fill: {{VALUE}}; color: {{VALUE}};',
				),
				'condition' => array(
					'selected_icon[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'button_icon_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}  ' . $widget_selector . '__button.cmsmasters-icon-view-framed span' . $widget_selector . '__icon' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'selected_icon[value]!' => '',
					'icon_view' => 'framed',
				),
			)
		);

		$this->add_control(
			'button_icon_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'selected_icon[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'icon_indent',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--icon-indent: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'selected_icon[value]!' => '' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_icon_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'button_icon_hover_color',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button.cmsmasters-icon-view-default:hover ' . $widget_selector . '__icon,
					{{WRAPPER}} ' . $widget_selector . '__button.cmsmasters-icon-view-default:focus ' . $widget_selector . '__icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__button.cmsmasters-icon-view-stacked:hover ' . $widget_selector . '__icon,
					{{WRAPPER}} ' . $widget_selector . '__button.cmsmasters-icon-view-stacked:focus ' . $widget_selector . '__icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__button.cmsmasters-icon-view-framed:hover ' . $widget_selector . '__icon,
					{{WRAPPER}} ' . $widget_selector . '__button.cmsmasters-icon-view-framed:focus ' . $widget_selector . '__icon' => 'color: {{VALUE}}; fill: {{VALUE}}; border-color: {{VALUE}};',
				),
				'condition' => array( 'selected_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'button_icon_bg_hover_color',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button:hover ' . $widget_selector . '__icon,
					{{WRAPPER}} ' . $widget_selector . '__button:focus ' . $widget_selector . '__icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__button.cmsmasters-icon-view-stacked:hover ' . $widget_selector . '__icon,
					{{WRAPPER}} ' . $widget_selector . '__button.cmsmasters-icon-view-stacked:focus ' . $widget_selector . '__icon' => 'fill: {{VALUE}}; color: {{VALUE}};',
				),
				'condition' => array(
					'selected_icon[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'button_icon_hover_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__icon' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__button.cmsmasters-icon-view-framed:hover span' . $widget_selector . '__icon' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'selected_icon[value]!' => '',
					'icon_view' => 'framed',
				),
			)
		);

		$this->add_control(
			'button_icon_hover_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button:hover  ' . $widget_selector . '__icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'selected_icon[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'button_icon_hover_indent',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__button:hover .cmsmasters-align-icon-right ' . $widget_selector . '__icon + ' . $widget_selector . '__text' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__button:hover .cmsmasters-align-icon-top ' . $widget_selector . '__icon + ' . $widget_selector . '__text' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__button:hover .cmsmasters-align-icon-left ' . $widget_selector . '__icon + ' . $widget_selector . '__text' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'selected_icon[value]!' => '',
					'button_icon_arrangement' => 'together',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'button_icon_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => array(
					'selected_icon[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'button_icon_square_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'selected_icon[value]!' => '',
					'icon_view!' => 'default',
					'button_shape' => 'square',
				),
			)
		);

		$this->add_responsive_control(
			'button_icon_circle_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__icon' => 'padding: {{SIZE}}{{UNIT}}; width: calc( 1em + ( {{SIZE}}{{UNIT}} * 2 ) ); height: calc( 1em + ( {{SIZE}}{{UNIT}} * 2 ) );',
				),
				'condition' => array(
					'selected_icon[value]!' => '',
					'icon_view!' => 'default',
					'button_shape' => 'circle',
				),
			)
		);

		$this->add_responsive_control(
			'button_icon_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'selected_icon[value]!' => '',
					'icon_view' => 'framed',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_description_style',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'button_description!' => '' ),
			)
		);

		$this->add_responsive_control(
			'button_description_align',
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
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__description-text' => 'text-align: {{VALUE}};',
				),
				'condition' => array( 'button_description!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'button_description_typography',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__description',
				'condition' => array( 'button_description!' => '' ),
			)
		);

		$this->add_control(
			'button_description_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__description' => 'color: {{VALUE}};',
				),
				'condition' => array( 'button_description!' => '' ),
			)
		);

		$this->add_control(
			'button_description_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__description-text' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'button_description!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'button_description_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'color' => array(
						'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__description-text',
			)
		);

		$this->add_control(
			'button_description_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__description-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_description_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__description' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'button_description!' => '' ),
			)
		);

		$this->add_responsive_control(
			'button_description_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__description-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'button_description!' => '' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render button widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['link'] );
			$this->add_render_attribute( 'button', 'class', 'cmsmasters-button-link' );
		}

		$this->add_render_attribute( 'button', 'class', array(
			$this->get_widget_class() . '__button',
			'cmsmasters-icon-view-' . $settings['icon_view'],
			'cmsmasters-icon-shape-' . $settings['button_shape'],
		) );

		$this->add_render_attribute( 'button', 'role', 'button' );

		if ( ! empty( $settings['button_css_id'] ) ) {
			$this->add_render_attribute( 'button', 'id', $settings['button_css_id'] );
		}

		if ( ! empty( $settings['size'] ) ) {
			$this->add_render_attribute( 'button', 'class', 'cmsmasters-button-size-' . $settings['size'] );
		}

		if ( $settings['hover_animation'] ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}

		$button_width_unit = ( isset( $settings['button_width'] ) && '%' === $settings['button_width']['unit'] ? ' cmsmasters-with-percentage' : '' );

		echo '<div class="' . $this->get_widget_class() . '__button-container">' .
			'<div class="' . $this->get_widget_class() . '__button-container-inner' . esc_attr( $button_width_unit ) . '">' .
				'<a ' . $this->get_render_attribute_string( 'button' ) . ' tabindex="0">';
					$this->render_text();
				echo '</a>';

		$button_description_block = $settings['button_description_block'];

		if ( '' === $button_description_block ) {
			$this->render_description();
		}

			echo '</div>';

		if ( '' !== $button_description_block ) {
			$this->render_description();
		}

		echo '</div>';
	}

	/**
	 * Render button widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 */
	protected function content_template() {
		?>
		<#
		view.addRenderAttribute( 'text', 'class', 'elementor-widget-cmsmasters-button__text' );
		var iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' );
		var button_width_unit = ( '%' === settings.button_width.unit ? ' cmsmasters-with-percentage' : '' );

		#><div class="elementor-widget-cmsmasters-button__button-container">
			<div class="elementor-widget-cmsmasters-button__button-container-inner{{{button_width_unit}}}">
				<a id="{{ settings.button_css_id }}" class="elementor-widget-cmsmasters-button__button cmsmasters-icon-shape-{{ settings.button_shape }} cmsmasters-icon-view-{{ settings.icon_view }} cmsmasters-button-size-{{ settings.size }} elementor-animation-{{ settings.hover_animation }} {{{ buttonBg }}}" href="{{ settings.link.url }}" role="button">
					<span class="elementor-widget-cmsmasters-button__content-wrapper cmsmasters-align-icon-{{ settings.icon_align }}"><#

						if ( settings.icon || '' !== settings.selected_icon.value ) {
							#><span class="elementor-widget-cmsmasters-button__icon"><#
								if ( iconHTML.rendered ) {
									#>{{{ iconHTML.value }}}<#
								}
							#></span><#
						}

						#><span {{{ view.getRenderAttributeString( 'text' ) }}}><#
							if ( '' !== settings.text ) {
								#>{{{ settings.text }}}<#
							} else {
								#>Click here<#
							}
						#></span>
					</span>
				</a><#

				if ( '' !== settings.button_description && 'yes' !== settings.button_description_block ) {
					var buttonBg = ( '' !== settings.button_description_bg_color ? ' description_bg_enable' : '' );

					#><div class="elementor-widget-cmsmasters-button__description{{{ buttonBg }}}">
						<div class="elementor-widget-cmsmasters-button__description-text">{{{ settings.button_description }}}</div>
					</div><#
				}
			#></div><#
			if ( '' !== settings.button_description && 'yes' === settings.button_description_block ) {
				var buttonBg = ( '' !== settings.button_description_bg_color ? ' description_bg_enable' : '' );

				#><div class="elementor-widget-cmsmasters-button__description{{{ buttonBg }}}">
					<div class="elementor-widget-cmsmasters-button__description-text">{{{ settings.button_description }}}</div>
				</div><#
			}
		#></div>
		<?php
	}

	/**
	 * Render button text.
	 *
	 * Render button widget text.
	 *
	 * @since 1.0.0
	 */
	protected function render_text() {
		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'text', 'none' );

		$selected_icon = $settings['selected_icon'];

		echo '<span class="' . $this->get_widget_class() . '__content-wrapper cmsmasters-align-icon-' . esc_attr( $settings['icon_align'] ) . '">';

		if ( ! empty( $settings['icon'] ) || ! empty( $selected_icon['value'] ) ) {
			echo '<span class="' . $this->get_widget_class() . '__icon">';

				Icons_Manager::render_icon( $selected_icon, array( 'aria-hidden' => 'true' ) );

			echo '</span>';
		}

			$text = $settings['text'];

			echo '<span class="' . $this->get_widget_class() . '__text">' .
				( ! empty( $text ) ? esc_html( $text ) : esc_html__( 'Click here', 'cmsmasters-elementor' ) ) .
			'</span>' .
		'</span>';
	}

	/**
	 * Render button description.
	 *
	 * Render button widget description.
	 *
	 * @since 1.0.0
	 */
	protected function render_description() {
		$settings = $this->get_settings_for_display();

		$button_description = ( isset( $settings['button_description'] ) && '' !== $settings['button_description'] ? $settings['button_description'] : '' );

		if ( $button_description ) {
			echo '<div class="' . $this->get_widget_class() . '__description' . ( '' !== $settings['button_description_bg_color'] ? ' description_bg_enable' : '' ) . '">' .
				'<div class="' . $this->get_widget_class() . '__description-text">' .
					esc_html( $button_description ) .
				'</div>' .
			'</div>';
		}
	}

	public function on_import( $element ) {
		return Icons_Manager::on_import_migration( $element, 'icon', 'selected_icon' );
	}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			array(
				'field' => 'text',
				'type' => esc_html__( 'Button Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			'link' => array(
				'field' => 'url',
				'type' => esc_html__( 'Button Link', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'button_description',
				'type' => esc_html__( 'Button Description Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'button_css_id',
				'type' => esc_html__( 'Button CSS ID', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
