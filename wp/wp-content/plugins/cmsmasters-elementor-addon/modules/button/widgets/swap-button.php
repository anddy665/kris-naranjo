<?php
/**
 * Swap Button widget.
 *
 * @package Cmsmasters_Elementor_Addon
 */

namespace CmsmastersElementor\Modules\Button\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Settings\Kit_Globals;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Addon swap button widget.
 *
 * Addon widget that displays swap button.
 *
 * @since 1.21.0
 */
class Swap_Button extends Base_Widget {

	/**
	 * Get widget name.
	 *
	 * Retrieve swap button widget name.
	 *
	 * @since 1.21.0
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmsmasters-swap-button';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve swap button widget title.
	 *
	 * @since 1.21.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Swap Button', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve swap button widget icon.
	 *
	 * @since 1.21.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-swap-button';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.21.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'button',
			'swap',
		);
	}

	/**
	 * Specifying caching of the widget by default.
	 *
	 * @since 1.21.0
	 */
	protected function is_dynamic_content(): bool {
		return false;
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.21.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array( 'widget-cmsmasters-swap-button' );
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.21.0
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Get widget class.
	 *
	 * @since 1.21.0
	 */
	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-swap-button';
	}

	/**
	 * Get widget selector.
	 *
	 * @since 1.21.0
	 */
	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.21.0
	 */
	protected function register_controls() {
		$this->register_swap_button_content_controls();

		$this->register_swap_button_animation_content_controls();

		$this->register_swap_button_label_style_controls();

		$this->register_swap_button_icon_style_controls();
	}

	/**
	 * Register swap button content controls.
	 *
	 * @since 1.21.0
	 */
	protected function register_swap_button_content_controls() {
		$this->start_controls_section(
			'section_swap_button',
			array( 'label' => esc_html__( 'Button', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'text',
			array(
				'label' => esc_html__( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Click here', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'link',
			array(
				'label' => esc_html__( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
				'placeholder' => esc_html__( 'https://your-link.com', 'cmsmasters-elementor' ),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label' => esc_html__( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-swap-button-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_heading',
			array(
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'icon_normal',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'exclude_inline_options' => array( 'none' ),
				'default' => array(
					'value' => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'icon_active',
			array(
				'label' => esc_html__( 'Active', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'exclude_inline_options' => array( 'none' ),
				'default' => array(
					'value' => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
				'render_type' => 'template',
			)
		);

		$breakpoints = CmsmastersUtils::get_breakpoints();

		$this->add_control(
			'icon_breakpoints',
			array(
				'label' => __( 'Hide On', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					/* translators: Tablet breakpoint %d: number in pixels. */
					'tablet' => sprintf( __( 'Tablet (< %dpx)', 'cmsmasters-elementor' ), $breakpoints['tablet'] + 1 ),
					/* translators: Mobile breakpoint %d: number in pixels. */
					'mobile' => sprintf( __( 'Mobile (< %dpx)', 'cmsmasters-elementor' ), $breakpoints['mobile'] + 1 ),
				),
				'default' => 'none',
				'description' => 'Hide icons on resolutions below chosen',
				'prefix_class' => 'cmsmasters-icon-disable-',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'button_css_id',
			array(
				'label' => esc_html__( 'Button ID', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => '',
				'title' => esc_html__( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'cmsmasters-elementor' ),
				'description' => esc_html__( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'view',
			array(
				'label' => esc_html__( 'View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register swap button animation content controls.
	 *
	 * @since 1.21.0
	 */
	protected function register_swap_button_animation_content_controls() {
		$this->start_controls_section(
			'section_swap_button_animation',
			array( 'label' => esc_html__( 'Animation', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'swap_button_animation',
			array(
				'label' => esc_html__( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'scale' => esc_html__( 'Scale', 'cmsmasters-elementor' ),
					'shrink' => esc_html__( 'Shrink', 'cmsmasters-elementor' ),
				),
				'default' => 'scale',
				'prefix_class' => 'cmsmasters-swap-button-animation-',
			)
		);

		$this->add_responsive_control(
			'swap_button_animation_duration',
			array(
				'label' => __( 'Animation Duration', 'cmsmasters-elementor' ) . ' (ms)',
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 800,
						'step' => 50,
					),
				),
				'default' => array(
					'size' => '300',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-swap-button-animation-duration: {{SIZE}}ms;',
				),
			)
		);

		$this->add_control(
			'reverse',
			array(
				'label' => __( 'Reverse', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'cmsmasters-swap-button-reverse-',
				'render_type' => 'template',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register swap button label style controls.
	 *
	 * @since 1.21.0
	 */
	protected function register_swap_button_label_style_controls() {
		$widget_selector = $this->get_widget_selector();

		$this->start_controls_section(
			'label_style_section',
			array(
				'label' => esc_html__( 'Label', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'label_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-swap-button-label-font-family: {{VALUE}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-swap-button-label-font-weight: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-swap-button-label-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-swap-button-label-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-swap-button-label-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-swap-button-label-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-swap-button-label-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-swap-button-label-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-swap-button-label-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->start_controls_tabs( 'label_tabs' );

		foreach ( array(
			'normal' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			'hover' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
		) as $state => $label ) {
			$this->start_controls_tab(
				"label_tab_{$state}",
				array( 'label' => $label )
			);

			$this->add_control(
				"label_color_{$state}",
				array(
					'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-swap-button-label-color-{$state}: {{VALUE}};",
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::BUTTON_BACKGROUND_GROUP,
				array(
					'name' => "label_bg_{$state}",
					'selector' => '{{WRAPPER}}',
				)
			);

			$this->update_control(
				"label_bg_{$state}_background",
				array( 'render_type' => 'template' )
			);

			$this->update_control(
				"label_bg_{$state}_color",
				array(
					'render_type' => 'template',
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-swap-button-label-bg-color-{$state}: {{VALUE}};",
					),
				)
			);

			$this->update_control(
				"label_bg_{$state}_gradient_angle",
				array(
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-swap-button-label-bg-image-{$state}: linear-gradient({{SIZE}}{{UNIT}}, var( --cmsmasters-swap-button-label-bg-color-{$state} ) {{label_bg_{$state}_color_stop.SIZE}}{{label_bg_{$state}_color_stop.UNIT}}, {{label_bg_{$state}_color_b.VALUE}} {{label_bg_{$state}_color_b_stop.SIZE}}{{label_bg_{$state}_color_b_stop.UNIT}});",
					),
				)
			);

			$this->update_control(
				"label_bg_{$state}_gradient_position",
				array(
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-swap-button-label-bg-image-{$state}: radial-gradient(at {{VALUE}}, var( --cmsmasters-swap-button-label-bg-color-{$state} ) {{label_bg_{$state}_color_stop.SIZE}}{{label_bg_{$state}_color_stop.UNIT}}, {{label_bg_{$state}_color_b.VALUE}} {{label_bg_{$state}_color_b_stop.SIZE}}{{label_bg_{$state}_color_b_stop.UNIT}});",
					),
				)
			);

			$this->add_control(
				"label_border_color_{$state}",
				array(
					'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-swap-button-label-border-color-{$state}: {{VALUE}};",
					),
					'condition' => array( 'label_border_border!' => 'none' ),
				)
			);

			$this->add_control(
				"label_border_radius_{$state}",
				array(
					'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-swap-button-label-border-radius-{$state}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			if ( 'hover' === $state ) {
				$this->add_control(
					"label_text_decoration_{$state}",
					array(
						'label' => esc_html__( 'Text Decoration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SELECT,
						'options' => array(
							'' => esc_html__( 'Default', 'cmsmasters-elementor' ),
							'none' => esc_html__( 'None', 'cmsmasters-elementor' ),
							'underline' => esc_html__( 'Underline', 'cmsmasters-elementor' ),
							'overline' => esc_html__( 'Overline', 'cmsmasters-elementor' ),
							'line-through' => esc_html__( 'Line Through', 'cmsmasters-elementor' ),
						),
						'selectors' => array(
							'{{WRAPPER}}' => '--cmsmasters-swap-button-label-text-decoration-hover: {{VALUE}};',
						),
					)
				);
			}

			$this->add_group_control(
				CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
				array( 'name' => "label_{$state}" )
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'label_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'vw',
					'vh',
					'custom',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-swap-button-label-padding-top: {{TOP}}{{UNIT}};
						--cmsmasters-swap-button-label-padding-right: {{RIGHT}}{{UNIT}};
						--cmsmasters-swap-button-label-padding-bottom: {{BOTTOM}}{{UNIT}};
						--cmsmasters-swap-button-label-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'label_border',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'prefix_class' => 'cmsmasters-label-border-',
					),
				),
				'render_type' => 'template',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register swap button icon style controls.
	 *
	 * @since 1.21.0
	 */
	protected function register_swap_button_icon_style_controls() {
		$widget_selector = $this->get_widget_selector();

		$this->start_controls_section(
			'icon_style_section',
			array(
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'icon_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'vw',
					'vh',
					'custom',
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
				'default' => array(
					'unit' => 'px',
					'size' => '1',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-swap-button-icon-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'icon_tabs' );

		foreach ( array(
			'normal' => esc_html__( 'Normal Icon', 'cmsmasters-elementor' ),
			'active' => esc_html__( 'Active Icon', 'cmsmasters-elementor' ),
		) as $state => $label ) {
			$this->start_controls_tab(
				"icon_tab_{$state}",
				array( 'label' => $label )
			);

			$this->add_responsive_control(
				"icon_size_{$state}",
				array(
					'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array(
						'px',
						'em',
						'%',
						'vw',
						'vh',
						'custom',
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
						'{{WRAPPER}}' => "--cmsmasters-swap-button-icon-size-{$state}: {{SIZE}}{{UNIT}};",
					),
				)
			);

			$this->add_control(
				"icon_color_{$state}",
				array(
					'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-swap-button-icon-color-{$state}: {{VALUE}};",
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::BUTTON_BACKGROUND_GROUP,
				array(
					'name' => "icon_bg_{$state}",
					'selector' => '{{WRAPPER}}',
				)
			);

			$this->update_control(
				"icon_bg_{$state}_background",
				array( 'render_type' => 'template' )
			);

			$this->update_control(
				"icon_bg_{$state}_color",
				array(
					'render_type' => 'template',
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-swap-button-icon-bg-color-{$state}: {{VALUE}};",
					),
				)
			);

			$this->update_control(
				"icon_bg_{$state}_gradient_angle",
				array(
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-swap-button-icon-bg-image-{$state}: linear-gradient({{SIZE}}{{UNIT}}, var( --cmsmasters-swap-button-icon-bg-color-{$state} ) {{icon_bg_{$state}_color_stop.SIZE}}{{icon_bg_{$state}_color_stop.UNIT}}, {{icon_bg_{$state}_color_b.VALUE}} {{icon_bg_{$state}_color_b_stop.SIZE}}{{icon_bg_{$state}_color_b_stop.UNIT}});",
					),
				)
			);

			$this->update_control(
				"icon_bg_{$state}_gradient_position",
				array(
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-swap-button-icon-bg-image-{$state}: radial-gradient(at {{VALUE}}, var( --cmsmasters-swap-button-icon-bg-color-{$state} ) {{icon_bg_{$state}_color_stop.SIZE}}{{icon_bg_{$state}_color_stop.UNIT}}, {{icon_bg_{$state}_color_b.VALUE}} {{icon_bg_{$state}_color_b_stop.SIZE}}{{icon_bg_{$state}_color_b_stop.UNIT}});",
					),
				)
			);

			$this->add_control(
				"icon_border_color_{$state}",
				array(
					'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-swap-button-icon-border-color-{$state}: {{VALUE}};",
					),
					'condition' => array( 'icon_border_border!' => 'none' ),
				)
			);

			$this->add_control(
				"icon_border_radius_{$state}",
				array(
					'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-swap-button-icon-border-radius-{$state}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_responsive_control(
				"icon_rotate_{$state}",
				array(
					'label' => esc_html__( 'Rotate', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 360,
						),
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-swap-button-icon-rotate-{$state}: {{SIZE}}deg;",
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'icon_border',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get widget control value.
	 *
	 * Get button widget control value.
	 *
	 * @param array $control Widget control.
	 * @since 1.21.0
	 */
	protected function get_control_value( $control ) {
		$settings = $this->get_settings_for_display();

		$value = ( isset( $settings[ $control ] ) ? $settings[ $control ] : '' );

		return $value;
	}

	/**
	 * Get color value.
	 *
	 * @param string $control_id Control ID.
	 * @return string Color value.
	 * @since 1.21.2
	 */
	protected function get_color_value( $control_id ) {
		$settings = $this->get_settings_for_display();
		$globals  = $this->get_settings( '__globals__' );

		$color = isset( $settings[ $control_id ] ) ? $settings[ $control_id ] : '';

		if ( empty( $color ) && ! empty( $globals[ $control_id ] ) ) {
			$id = $globals[ $control_id ];

			$color = 'var(--e-global-color-' . str_replace( 'globals/colors?id=', '', $id ) . ')';
		}

		return $color;
	}

	/**
	 * Render button icon.
	 *
	 * Render button widget icon.
	 *
	 * @param array $direction Widget icon.
	 * @since 1.21.0
	 */
	protected function render_button_icon( $direction ) {
		$this->set_render_attribute(
			'icon',
			'class',
			array(
				$this->get_widget_class() . '__icon',
				'cmsmaster-swap-button-icon-' . esc_attr( $direction ),
			)
		);

		$reverse = $this->get_control_value( 'reverse' );
		$icon_value = 'icon_' . $direction;

		if ( ! empty( $reverse ) && 'yes' === $reverse ) {
			if ( 'normal' === $direction ) {
				$icon_value = 'icon_active';
			}

			if ( 'active' === $direction ) {
				$icon_value = 'icon_normal';
			}
		}

		$icon = $this->get_control_value( $icon_value );

		if ( ! empty( $icon ) && ! empty( $icon['value'] ) ) {
			echo '<span ' . $this->get_render_attribute_string( 'icon' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );

			echo '</span>';
		}
	}

	/**
	 * Render button text.
	 *
	 * Render button widget text.
	 *
	 * @since 1.21.0
	 */
	protected function render_text() {
		$text = $this->get_control_value( 'text' );

		echo '<span class="' . $this->get_widget_class() . '__text">' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			( ! empty( $text ) ? esc_html( $text ) : esc_html__( 'Click here', 'cmsmasters-elementor' ) ) .
		'</span>';
	}

	/**
	 * Set element background class.
	 *
	 * @since 1.21.0
	 */
	protected function set_element_bg_class() {
		$config = array(
			'label' => array(
				'normal' => array(
					'color' => 'label_bg_normal_color',
					'background' => 'label_bg_normal_background',
				),
				'hover' => array(
					'color' => 'label_bg_hover_color',
					'background' => 'label_bg_hover_background',
				),
			),
			'icon' => array(
				'normal' => array(
					'color' => 'icon_bg_normal_color',
					'background' => 'icon_bg_normal_background',
				),
				'active' => array(
					'color' => 'icon_bg_active_color',
					'background' => 'icon_bg_active_background',
				),
			),
		);

		foreach ( $config as $element => $states ) {
			foreach ( $states as $state => $controls ) {
				$color = $this->get_color_value( $controls['color'] );
				$background = $this->get_control_value( $controls['background'] );

				if ( empty( $color ) ) {
					continue;
				}

				if ( in_array( $background, array( 'color', 'gradient' ), true ) ) {
					$this->add_render_attribute(
						'button',
						'class',
						sprintf(
							'cmsmasters-swap-button-%s-bg-%s-%s',
							$element,
							$state,
							$background
						)
					);
				}
			}
		}
	}

	/**
	 * Render button widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.21.0
	 */
	protected function render() {
		$this->add_render_attribute(
			'button',
			array(
				'class' => array( $this->get_widget_class() . '__button' ),
				'role' => 'button',
				'tabindex' => '0',
			)
		);

		$this->set_element_bg_class();

		$link = $this->get_control_value( 'link' );

		if ( ! empty( $link['url'] ) && ! empty( $link['url'] ) ) {
			$this->add_link_attributes( 'button', $link );
		}

		$button_css_id = $this->get_control_value( 'button_css_id' );

		if ( ! empty( $button_css_id ) ) {
			$this->add_render_attribute( 'button', 'id', $button_css_id );
		}

		echo '<a ' . $this->get_render_attribute_string( 'button' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			$this->render_button_icon( 'active' );

			$this->render_text();

			$this->render_button_icon( 'normal' );

		echo '</a>';
	}

	/**
	 * Render button widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.21.0
	 */
	protected function content_template() {
		?>
		<#
		var iconLeftHTML = elementor.helpers.renderIcon( view, settings.icon_normal, { 'aria-hidden': true }, 'i' , 'object' );
		var iconRightHTML = elementor.helpers.renderIcon( view, settings.icon_active, { 'aria-hidden': true }, 'i' , 'object' );

		var addedClasses = [];

		if ( '' !== settings.label_bg_normal_color ) {
			if ( 'color' === settings.label_bg_normal_background ) {
				addedClasses.push( 'cmsmasters-swap-button-label-bg-normal-color' );
			} else if ( 'gradient' === settings.label_bg_normal_background ) {
				addedClasses.push( 'cmsmasters-swap-button-label-bg-normal-gradient' );
			}
		}

		if ( '' !== settings.label_bg_hover_color ) {
			if ( 'color' === settings.label_bg_hover_background ) {
				addedClasses.push( 'cmsmasters-swap-button-label-bg-hover-color' );
			} else if ( 'gradient' === settings.label_bg_hover_background ) {
				addedClasses.push( 'cmsmasters-swap-button-label-bg-hover-gradient' );
			}
		}

		if ( '' !== settings.icon_bg_normal_color ) {
			if ( 'color' === settings.icon_bg_normal_background ) {
				addedClasses.push( 'cmsmasters-swap-button-icon-bg-normal-color' );
			} else if ( 'gradient' === settings.icon_bg_normal_background ) {
				addedClasses.push( 'cmsmasters-swap-button-icon-bg-normal-gradient' );
			}
		}

		if ( '' !== settings.icon_bg_active_color ) {
			if ( 'color' === settings.icon_bg_active_background ) {
				addedClasses.push( 'cmsmasters-swap-button-icon-bg-active-color' );
			} else if ( 'gradient' === settings.icon_bg_active_background ) {
				addedClasses.push( 'cmsmasters-swap-button-icon-bg-active-gradient' );
			}
		}

		var addedClassesString = addedClasses.join( ' ' );

		#><a id="{{ settings.button_css_id }}" class="elementor-widget-cmsmasters-swap-button__button {{ addedClassesString }}" href="{{ settings.link.url }}" role="button" tabindex=0><#

			if ( settings.icon_normal && '' !== settings.icon_normal.value ) {
				#><span class="elementor-widget-cmsmasters-swap-button__icon cmsmaster-swap-button-icon-active"><#
					if ( settings.reverse && 'yes' === settings.reverse ) {
						if ( iconLeftHTML.rendered ) {
							#>{{{ iconLeftHTML.value }}}<#
						}
					} else {
						if ( iconRightHTML.rendered ) {
							#>{{{ iconRightHTML.value }}}<#
						}
					}
				#></span><#
			}

			#><span class="elementor-widget-cmsmasters-swap-button__text"><#
				if ( '' !== settings.text ) {
					#>{{{ settings.text }}}<#
				} else {
					#>Click here<#
				}
			#></span><#

			if ( settings.icon_active && '' !== settings.icon_active.value ) {
				#><span class="elementor-widget-cmsmasters-swap-button__icon cmsmaster-swap-button-icon-normal"><#
					if ( settings.reverse && 'yes' === settings.reverse ) {
						if ( iconRightHTML.rendered ) {
							#>{{{ iconRightHTML.value }}}<#
						}
					} else {
						if ( iconLeftHTML.rendered ) {
							#>{{{ iconLeftHTML.value }}}<#
						}
					}
				#></span><#
			}#>

		</a>
		<?php
	}
}
