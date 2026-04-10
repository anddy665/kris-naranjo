<?php
/**
 * Split Button widget.
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
 * Addon split button widget.
 *
 * Addon widget that displays split button.
 *
 * @since 1.22.0
 */
class Split_Button extends Base_Widget {

	/**
	 * Get widget name.
	 *
	 * Retrieve split button widget name.
	 *
	 * @since 1.22.0
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmsmasters-split-button';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve split button widget title.
	 *
	 * @since 1.22.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Split Button', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve split button widget icon.
	 *
	 * @since 1.22.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-split-button';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.22.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'button',
			'split',
		);
	}

	/**
	 * Specifying caching of the widget by default.
	 *
	 * @since 1.22.0
	 */
	protected function is_dynamic_content(): bool {
		return false;
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.22.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array( 'widget-cmsmasters-split-button' );
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.22.0
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Get widget class.
	 *
	 * @since 1.22.0
	 */
	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-split-button';
	}

	/**
	 * Get widget selector.
	 *
	 * @since 1.22.0
	 */
	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.22.0
	 */
	protected function register_controls() {
		$this->register_split_button_content_controls();

		$this->register_split_button_label_style_controls();

		$this->register_split_button_icon_style_controls();
	}

	/**
	 * Register split button content controls.
	 *
	 * @since 1.22.0
	 */
	protected function register_split_button_content_controls() {
		$this->start_controls_section(
			'section_split_button',
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
					'{{WRAPPER}}' => '--cmsmasters-split-button-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'split_button_animation',
			array(
				'label' => esc_html__( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => esc_html__( 'None', 'cmsmasters-elementor' ),
					'rotate' => esc_html__( 'Rotate', 'cmsmasters-elementor' ),
					'slide' => esc_html__( 'Slide', 'cmsmasters-elementor' ),
				),
				'default' => 'slide',
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-split-button-animation-',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'split_button_animation_slide_angle',
			array(
				'label' => esc_html__( 'Angle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'top-left' => esc_html__( 'Top Left', 'cmsmasters-elementor' ),
					'top-center' => esc_html__( 'Top Center', 'cmsmasters-elementor' ),
					'top-right' => esc_html__( 'Top Right', 'cmsmasters-elementor' ),
					'right-center' => esc_html__( 'Right Center', 'cmsmasters-elementor' ),
					'right-bottom' => esc_html__( 'Right Bottom', 'cmsmasters-elementor' ),
					'bottom-center' => esc_html__( 'Bottom Center', 'cmsmasters-elementor' ),
					'bottom-left' => esc_html__( 'Bottom Left', 'cmsmasters-elementor' ),
					'left-center' => esc_html__( 'Left Center', 'cmsmasters-elementor' ),
				),
				'default' => 'top-right',
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-split-button-animation-slide-angle-',
				'condition' => array( 'split_button_animation' => 'slide' ),
			)
		);

		$this->add_responsive_control(
			'split_button_animation_duration',
			array(
				'label' => esc_html__( 'Animation Duration', 'cmsmasters-elementor' ) . ' (ms)',
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 800,
						'step' => 50,
					),
				),
				'default' => array( 'size' => '300' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-split-button-animation-duration: {{SIZE}}ms;',
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

		$this->add_responsive_control(
			'icon_rotate_normal',
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
				'default' => array( 'size' => '315' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-split-button-icon-rotate-normal: {{SIZE}}deg;',
				),
			)
		);

		$this->add_control(
			'icon_active',
			array(
				'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'exclude_inline_options' => array( 'none' ),
				'default' => array(
					'value' => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
				'render_type' => 'template',
				'condition' => array( 'split_button_animation!' => 'rotate' ),
			)
		);

		$this->add_responsive_control(
			'icon_rotate_active',
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
				'default' => array( 'size' => '315' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-split-button-icon-rotate-active: {{SIZE}}deg;',
				),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label' => esc_html__( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'row-reverse' => array( 'title' => esc_html__( 'Before', 'cmsmasters-elementor' ) ),
					'row' => array( 'title' => esc_html__( 'After', 'cmsmasters-elementor' ) ),
				),
				'default' => 'row',
				'toggle' => false,
				'label_block' => false,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-split-button-icon-position: {{VALUE}};',
				),
			)
		);

		$breakpoints = CmsmastersUtils::get_breakpoints();

		$this->add_control(
			'icon_breakpoints',
			array(
				'label' => esc_html__( 'Hide On', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => esc_html__( 'None', 'cmsmasters-elementor' ),
					/* translators: Tablet breakpoint %d: number in pixels. */
					'tablet' => sprintf( esc_html__( 'Tablet (< %dpx)', 'cmsmasters-elementor' ), $breakpoints['tablet'] + 1 ),
					/* translators: Mobile breakpoint %d: number in pixels. */
					'mobile' => sprintf( esc_html__( 'Mobile (< %dpx)', 'cmsmasters-elementor' ), $breakpoints['mobile'] + 1 ),
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
				'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'cmsmasters-elementor' ),
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
	 * Register split button label style controls.
	 *
	 * @since 1.22.0
	 */
	protected function register_split_button_label_style_controls() {
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
							'{{SELECTOR}}' => '--cmsmasters-split-button-label-font-family: {{VALUE}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-split-button-label-font-weight: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-split-button-label-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-split-button-label-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-split-button-label-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-split-button-label-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-split-button-label-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-split-button-label-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-split-button-label-word-spacing: {{SIZE}}{{UNIT}}',
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
						'{{WRAPPER}}' => "--cmsmasters-split-button-label-color-{$state}: {{VALUE}};",
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
						'{{WRAPPER}}' => "--cmsmasters-split-button-label-bg-color-{$state}: {{VALUE}};",
					),
				)
			);

			$this->update_control(
				"label_bg_{$state}_gradient_angle",
				array(
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-split-button-label-bg-image-{$state}: linear-gradient({{SIZE}}{{UNIT}}, var( --cmsmasters-split-button-label-bg-color-{$state} ) {{label_bg_{$state}_color_stop.SIZE}}{{label_bg_{$state}_color_stop.UNIT}}, {{label_bg_{$state}_color_b.VALUE}} {{label_bg_{$state}_color_b_stop.SIZE}}{{label_bg_{$state}_color_b_stop.UNIT}});",
					),
				)
			);

			$this->update_control(
				"label_bg_{$state}_gradient_position",
				array(
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-split-button-label-bg-image-{$state}: radial-gradient(at {{VALUE}}, var( --cmsmasters-split-button-label-bg-color-{$state} ) {{label_bg_{$state}_color_stop.SIZE}}{{label_bg_{$state}_color_stop.UNIT}}, {{label_bg_{$state}_color_b.VALUE}} {{label_bg_{$state}_color_b_stop.SIZE}}{{label_bg_{$state}_color_b_stop.UNIT}});",
					),
				)
			);

			$this->add_control(
				"label_border_color_{$state}",
				array(
					'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-split-button-label-border-color-{$state}: {{VALUE}};",
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
						'{{WRAPPER}}' => "--cmsmasters-split-button-label-border-radius-{$state}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
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
							'{{WRAPPER}}' => '--cmsmasters-split-button-label-text-decoration-hover: {{VALUE}};',
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
					'{{WRAPPER}}' => '--cmsmasters-split-button-label-padding-top: {{TOP}}{{UNIT}};
						--cmsmasters-split-button-label-padding-right: {{RIGHT}}{{UNIT}};
						--cmsmasters-split-button-label-padding-bottom: {{BOTTOM}}{{UNIT}};
						--cmsmasters-split-button-label-padding-left: {{LEFT}}{{UNIT}};',
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
	 * Register split button icon style controls.
	 *
	 * @since 1.22.0
	 */
	protected function register_split_button_icon_style_controls() {
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
					'{{WRAPPER}}' => '--cmsmasters-split-button-icon-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'icon_tabs' );

		foreach ( array(
			'normal' => esc_html__( 'Normal Icon', 'cmsmasters-elementor' ),
			'active' => esc_html__( 'Hover Icon', 'cmsmasters-elementor' ),
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
						'{{WRAPPER}}' => "--cmsmasters-split-button-icon-size-{$state}: {{SIZE}}{{UNIT}};",
					),
				)
			);

			$this->add_control(
				"icon_color_{$state}",
				array(
					'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-split-button-icon-color-{$state}: {{VALUE}};",
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
						'{{WRAPPER}}' => "--cmsmasters-split-button-icon-bg-color-{$state}: {{VALUE}};",
					),
				)
			);

			$this->update_control(
				"icon_bg_{$state}_gradient_angle",
				array(
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-split-button-icon-bg-image-{$state}: linear-gradient({{SIZE}}{{UNIT}}, var( --cmsmasters-split-button-icon-bg-color-{$state} ) {{icon_bg_{$state}_color_stop.SIZE}}{{icon_bg_{$state}_color_stop.UNIT}}, {{icon_bg_{$state}_color_b.VALUE}} {{icon_bg_{$state}_color_b_stop.SIZE}}{{icon_bg_{$state}_color_b_stop.UNIT}});",
					),
				)
			);

			$this->update_control(
				"icon_bg_{$state}_gradient_position",
				array(
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-split-button-icon-bg-image-{$state}: radial-gradient(at {{VALUE}}, var( --cmsmasters-split-button-icon-bg-color-{$state} ) {{icon_bg_{$state}_color_stop.SIZE}}{{icon_bg_{$state}_color_stop.UNIT}}, {{icon_bg_{$state}_color_b.VALUE}} {{icon_bg_{$state}_color_b_stop.SIZE}}{{icon_bg_{$state}_color_b_stop.UNIT}});",
					),
				)
			);

			$this->add_control(
				"icon_border_color_{$state}",
				array(
					'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-split-button-icon-border-color-{$state}: {{VALUE}};",
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
						'{{WRAPPER}}' => "--cmsmasters-split-button-icon-border-radius-{$state}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
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
	 * @since 1.22.0
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
	 * @since 1.22.0
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
	 * Render button text.
	 *
	 * Render button widget text.
	 *
	 * @since 1.22.0
	 */
	protected function render_text() {
		$text = $this->get_control_value( 'text' );

		echo '<span class="' . $this->get_widget_class() . '__text">' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			( ! empty( $text ) ? esc_html( $text ) : esc_html__( 'Click here', 'cmsmasters-elementor' ) ) .
		'</span>';
	}

	/**
	 * Render button icon.
	 *
	 * Render button widget icon.
	 *
	 * @param array $direction Widget icon.
	 * @since 1.22.0
	 */
	protected function render_button_icon( $direction ) {
		$this->set_render_attribute(
			'icon',
			'class',
			array(
				$this->get_widget_class() . '__icon',
				'cmsmaster-split-button-icon-' . esc_attr( $direction ),
			)
		);

		$icon = $this->get_control_value( 'icon_' . $direction );

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
	 * @since 1.22.0
	 */
	protected function render_button_icons() {
		$this->add_render_attribute( 'icon-wrapper', 'class', $this->get_widget_class() . '__icon-wrapper' );

		echo '<span ' . $this->get_render_attribute_string( 'icon-wrapper' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			$this->render_button_icon( 'normal' );

			$split_button_animation = $this->get_control_value( 'split_button_animation' );

		if ( ! empty( $split_button_animation ) && 'rotate' !== $split_button_animation ) {
			$this->render_button_icon( 'active' );
		}

		echo '</span>';
	}

	/**
	 * Set element background class.
	 *
	 * @since 1.22.0
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
							'cmsmasters-split-button-%s-bg-%s-%s',
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
	 * @since 1.22.0
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

			$this->render_text();

			$this->render_button_icons();

		echo '</a>';
	}

	/**
	 * Render button widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.22.0
	 */
	protected function content_template() {
		?>
		<#
		var iconNormalHTML = elementor.helpers.renderIcon( view, settings.icon_normal, { 'aria-hidden': true }, 'i' , 'object' );
		var iconActiveHTML = elementor.helpers.renderIcon( view, settings.icon_active, { 'aria-hidden': true }, 'i' , 'object' );

		var addedClasses = [];

		if ( '' !== settings.label_bg_normal_color ) {
			if ( 'color' === settings.label_bg_normal_background ) {
				addedClasses.push( 'cmsmasters-split-button-label-bg-normal-color' );
			} else if ( 'gradient' === settings.label_bg_normal_background ) {
				addedClasses.push( 'cmsmasters-split-button-label-bg-normal-gradient' );
			}
		}

		if ( '' !== settings.label_bg_hover_color ) {
			if ( 'color' === settings.label_bg_hover_background ) {
				addedClasses.push( 'cmsmasters-split-button-label-bg-hover-color' );
			} else if ( 'gradient' === settings.label_bg_hover_background ) {
				addedClasses.push( 'cmsmasters-split-button-label-bg-hover-gradient' );
			}
		}

		if ( '' !== settings.icon_bg_normal_color ) {
			if ( 'color' === settings.icon_bg_normal_background ) {
				addedClasses.push( 'cmsmasters-split-button-icon-bg-normal-color' );
			} else if ( 'gradient' === settings.icon_bg_normal_background ) {
				addedClasses.push( 'cmsmasters-split-button-icon-bg-normal-gradient' );
			}
		}

		if ( '' !== settings.icon_bg_active_color ) {
			if ( 'color' === settings.icon_bg_active_background ) {
				addedClasses.push( 'cmsmasters-split-button-icon-bg-active-color' );
			} else if ( 'gradient' === settings.icon_bg_active_background ) {
				addedClasses.push( 'cmsmasters-split-button-icon-bg-active-gradient' );
			}
		}

		var addedClassesString = addedClasses.join( ' ' );

		#><a id="{{ settings.button_css_id }}" class="elementor-widget-cmsmasters-split-button__button {{ addedClassesString }}" href="{{ settings.link.url }}" role="button" tabindex=0><#

			#><span class="elementor-widget-cmsmasters-split-button__text"><#
				if ( '' !== settings.text ) {
					#>{{{ settings.text }}}<#
				} else {
					#>Click here<#
				}
			#></span><#

			if ( settings.icon_normal && '' !== settings.icon_normal.value && settings.icon_active && '' !== settings.icon_active.value ) {
				#><span class="elementor-widget-cmsmasters-split-button__icon-wrapper"><#

					if ( settings.icon_normal && '' !== settings.icon_normal.value ) {
						#><span class="elementor-widget-cmsmasters-split-button__icon cmsmaster-split-button-icon-normal"><#
							if ( iconNormalHTML.rendered ) {
								#>{{{ iconNormalHTML.value }}}<#
							}
						#></span><#
					}

					if ( settings.icon_active && '' !== settings.icon_normal.value && 'rotate' !== settings.split_button_animation ) {
						#><span class="elementor-widget-cmsmasters-split-button__icon cmsmaster-split-button-icon-active"><#
							if ( iconActiveHTML.rendered ) {
								#>{{{ iconActiveHTML.value }}}<#
							}
						#></span><#
					}

				#></span><#
			}#>

		</a>
		<?php
	}
}
