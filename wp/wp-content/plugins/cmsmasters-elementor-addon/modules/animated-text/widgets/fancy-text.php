<?php
namespace CmsmastersElementor\Modules\AnimatedText\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Settings\Kit_Globals;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Elementor fancy text widget.
 *
 * Elementor widget lets you easily embed and promote any public
 * fancy text on your website.
 *
 * @since 1.0.0
 */
class Fancy_Text extends Base_Widget {

	/**
	 * Get widget name.
	 *
	 * Retrieve fancy text widget name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmsmasters-fancy-text';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve fancy text widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Fancy Text', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve fancy text widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-fancy-text';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array( 'fancy text', 'animated text' );
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
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the widget requires.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget script dependencies.
	 */
	public function get_script_depends() {
		return array_merge( array(
			'typed',
			'vticker',
			'morphext',
		), parent::get_script_depends() );
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
			'widget-cmsmasters-fancy-text',
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
	 * Register fancy text widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Fixed repeater fields error.
	 * Added repeater items custom styling controls.
	 *
	 * @return void Widget controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'fancy_text_content',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_style',
			array(
				'label' => __( 'Custom Styling', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
			)
		);

		$repeater->add_control(
			'item_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array( 'item_style' => 'yes' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__list-item{{CURRENT_ITEM}}' => 'color: {{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'item_background_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array( 'item_style' => 'yes' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__list-item{{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
				),
			)
		);

		$repeater->add_control(
			'item_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array( 'item_style' => 'yes' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__list-item{{CURRENT_ITEM}}' => 'border-color: {{VALUE}};',
				),
			)
		);

		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'item_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__list-item{{CURRENT_ITEM}}',
			)
		);

		$this->add_control(
			'fancy_text_list',
			array(
				'label' => __( 'Fancy Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'default' => array(
					array( 'item_text' => __( 'First string', 'cmsmasters-elementor' ) ),
					array( 'item_text' => __( 'Second string', 'cmsmasters-elementor' ) ),
					array( 'item_text' => __( 'Third string', 'cmsmasters-elementor' ) ),
				),
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ item_text }}}',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'before_text_content',
			array(
				'label' => __( 'Before Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
				'default' => __( 'This is the', 'cmsmasters-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'after_text_content',
			array(
				'label' => __( 'After Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
				'default' => __( 'of the sentence.', 'cmsmasters-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'fancy_tag',
			array(
				'label' => __( 'Tag', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'h1' => __( 'H1', 'cmsmasters-elementor' ),
					'h2' => __( 'H2', 'cmsmasters-elementor' ),
					'h3' => __( 'H3', 'cmsmasters-elementor' ),
					'h4' => __( 'H4', 'cmsmasters-elementor' ),
					'h5' => __( 'H5', 'cmsmasters-elementor' ),
					'h6' => __( 'H6', 'cmsmasters-elementor' ),
					'div' => __( 'div', 'cmsmasters-elementor' ),
					'p' => __( 'p', 'cmsmasters-elementor' ),
				),
				'default' => 'h3',
			)
		);

		$this->add_responsive_control(
			'fancy_text_align',
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
				'default' => 'center',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__fancy-text-wrapper' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'animation_effect',
			array(
				'label' => esc_html__( 'Animation Effect', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CUSTOM_ANIMATION,
				'default' => 'typing',
				'frontend_available' => true,
				'render_type' => 'template',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'inline_elements',
			array(
				'label' => __( 'Inline Elements', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'cmsmasters-text-inline-',
				'condition' => array(
					'animation_effect' => 'none',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_settings',
			array(
				'label' => __( 'Additional', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'fancy_text_loop',
			array(
				'label' => __( 'Loop', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => array(
					'animation_effect!' => 'slide',
				),
			)
		);

		$this->add_control(
			'slide_up_speed',
			array(
				'label' => __( 'Animation Speed', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 200,
				'step' => 10,
				'description' => __( 'Set a duration value in milliseconds for slide up effect.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array(
					'animation_effect' => 'slide',
				),
			)
		);

		$this->add_control(
			'fancy_text_delay_on_change',
			array(
				'label' => __( 'Pause Time', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'description' => __( 'Set the time (in milliseconds) Fancy Text should stay visible before changing.', 'cmsmasters-elementor' ),
				'default' => 2500,
				'step' => 100,
				'frontend_available' => true,
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'animation_effect',
							'operator' => '!=',
							'value' => 'typing',
						),
						array(
							'name' => 'animation_effect',
							'operator' => '!=',
							'value' => 'slide',
						),
					),
				),
			)
		);

		$this->add_control(
			'fancy_text_type_speed',
			array(
				'label' => __( 'Start Speed', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 150,
				'description' => __( 'Set typing effect speed in milliseconds.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array(
					'animation_effect' => 'typing',
				),
			)
		);

		$this->add_control(
			'fancy_text_back_speed',
			array(
				'label' => __( 'Back Speed', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 150,
				'description' => __( 'Set a speed for backspace effect in milliseconds.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array(
					'animation_effect' => 'typing',
				),
			)
		);

		$this->add_control(
			'fancy_text_start_delay',
			array(
				'label' => __( 'Start Delay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 30,
				'description' => __( 'If you set it on 5000 milliseconds, the first word/string will appear after 5 seconds.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array(
					'animation_effect' => 'typing',
				),
			)
		);

		$this->add_control(
			'fancy_text_back_delay',
			array(
				'label' => __( 'Back Delay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 30,
				'description' => __( 'If you set it on 5000 milliseconds, the word/string will remain visible for 5 seconds before backspace effect.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array(
					'animation_effect' => 'typing',
				),
			)
		);

		$this->add_control(
			'fancy_text_show_cursor',
			array(
				'label' => __( 'Show Cursor', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => array(
					'animation_effect' => 'typing',
				),
			)
		);

		$this->add_control(
			'fancy_text_cursor_text',
			array(
				'label' => __( 'Cursor Mark', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '|',
				'frontend_available' => true,
				'condition' => array(
					'animation_effect' => 'typing',
					'fancy_text_show_cursor' => 'yes',
				),
			)
		);

		$this->add_control(
			'slide_up_pause_time',
			array(
				'label' => __( 'Pause Time', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 3000,
				'step' => 100,
				'description' => __( 'How long should the word/string stay visible? Set a value in milliseconds.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array(
					'animation_effect' => 'slide',
				),
			)
		);

		$this->add_control(
			'slide_up_hover_pause',
			array(
				'label' => __( 'Pause on Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'If you enabled this option, the slide will be paused when mouseover.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array(
					'animation_effect' => 'slide',
				),
			)
		);

		$this->add_control(
			'animated_scroll',
			array(
				'label' => __( 'Delayed Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'description' => __( 'If enabled animation starts when a page is scrolled to the widget (not on the page load).', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'animated_distance',
			array(
				'label' => __( 'Distance', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 60,
				),
				'range' => array(
					'px' => array(
						'min' => 50,
						'max' => 300,
						'step' => 1,
					),
				),
				'description' => __( 'Indicates a distance from a bottom of a browser window when animation should start.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'condition' => array(
					'animated_scroll' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'fancy_text_style_tab',
			array(
				'label' => __( 'Fancy Text', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'fancy_text_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__list-item' => 'color: {{VALUE}};',
				),
				'global' => array(
					'default' => Kit_Globals::COLOR_ACCENT,
				),
			)
		);

		$this->add_control(
			'fancy_text_background_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__list-item' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'fancy_text_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__list-item' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'animation_effect!' => 'slide',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'fancy_text_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__list-item',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'text_shadow_fancy',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__list-item',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_ftext',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__list-item',
				'separator' => 'before',
				'exclude' => array( 'color' ),
				'condition' => array(
					'animation_effect!' => 'slide',
				),
			)
		);

		$this->add_responsive_control(
			'ftext_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__list-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'animation_effect!' => 'slide',
				),
			)
		);

		$this->add_responsive_control(
			'fancy_text_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__list-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'animation_effect!' => 'slide',
				),
			)
		);

		$this->add_control(
			'slide_up_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => '0',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'separator' => 'before',
				'frontend_available' => true,
				'condition' => array(
					'animation_effect' => 'slide',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'fancy_cursor_text_style_tab',
			array(
				'label' => __( 'Cursor Text', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'fancy_text_show_cursor' => 'yes',
					'animation_effect' => 'typing',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'text_cursor_typography',
				'selector' => '{{WRAPPER}} .typed-cursor',
			)
		);

		$this->add_control(
			'fancy_text_cursor_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .typed-cursor' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'fancy_text_cursor_background',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .typed-cursor' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'text_style_tab',
			array(
				'label' => __( 'Before/After', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__after-text span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__before-text span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__after-text span' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__before-text span' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'text_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__after-text span, {{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__before-text span',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__after-text span, {{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__after-text span',
			)
		);

		$this->add_responsive_control(
			'text_padding_after',
			array(
				'label' => esc_html__( 'Padding After Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__after-text span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'text_padding_before',
			array(
				'label' => esc_html__( 'Padding Before Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-fancy-text__before-text span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render image scroll widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Added repeater items custom styling.
	 * @since 1.3.1 Fixed validation errors.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( 'slide' === $settings['animation_effect'] ) {
			$this->add_render_attribute( 'prefix', 'class', 'elementor-widget-cmsmasters-fancy-text__text-span-align' );
			$this->add_render_attribute( 'suffix', 'class', 'elementor-widget-cmsmasters-fancy-text__text-span-align' );
		}

		$this->add_render_attribute( 'wrapper', array(
			'class' => 'elementor-widget-cmsmasters-fancy-text__fancy-text-wrapper',
		) );

		if ( $settings['animated_scroll'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'elementor-widget-cmsmasters-fancy-text__scroll-animated' );
		}

		$tag = $settings['fancy_tag'];

		echo '<' . Utils::validate_html_tag( $tag ) . ' ' . $this->get_render_attribute_string( 'wrapper' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( ! empty( $settings['before_text_content'] ) ) {
			echo '<span class="elementor-widget-cmsmasters-fancy-text__before-text">
				<span ' . $this->get_render_attribute_string( 'prefix' ) . '>' . wp_kses_post( $settings['before_text_content'] ) . '</span>
			</span>';
		}

		if ( 'typing' === $settings['animation_effect'] ) {
			echo '<span class="elementor-widget-cmsmasters-fancy-text__fancy-text fancy-text-hidden"></span>';
		} elseif ( 'slide' === $settings['animation_effect'] ) {
			echo '<span class="elementor-widget-cmsmasters-fancy-text__fancy-text">
				<ul class="elementor-widget-cmsmasters-fancy-text__list-items">';

			foreach ( $settings['fancy_text_list'] as $index => $item ) {
				$repeater_item_setting_key = $this->get_repeater_setting_key( 'item', 'list-item', $index );

				$this->add_render_attribute( $repeater_item_setting_key, 'class', array(
					'elementor-widget-cmsmasters-fancy-text__list-item',
					'elementor-repeater-item-' . $item['_id'],
				) );

				if ( ! empty( $item['item_text'] ) ) {
					echo '<li ' . $this->get_render_attribute_string( $repeater_item_setting_key ) . '>' . esc_html( $item['item_text'] ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}

				echo '</ul>
			</span>';
		} else {
			$strings_list = '';

			foreach ( $settings['fancy_text_list'] as $index => $item ) {
				$repeater_item_setting_key = $this->get_repeater_setting_key( 'item', 'list-item', $index );

				$this->add_render_attribute( $repeater_item_setting_key, 'class', array(
					'elementor-widget-cmsmasters-fancy-text__list-item',
					'elementor-repeater-item-' . $item['_id'],
				) );

				$strings_list .= '<span ' . $this->get_render_attribute_string( $repeater_item_setting_key ) . '>' . esc_html( $item['item_text'] ) . '</span>, ';
			}

			$none = 'none' === $settings['animation_effect'] ? esc_attr( ' none' ) : '';

			echo '<span class="elementor-widget-cmsmasters-fancy-text__fancy-text fancy-text-hidden' . $none . '">' . wp_kses_post( rtrim( $strings_list, ', ' ) ) . '</span>';
		}

		if ( ! empty( $settings['after_text_content'] ) ) {
			echo '<span class="elementor-widget-cmsmasters-fancy-text__after-text">
				<span ' . $this->get_render_attribute_string( 'suffix' ) . '>' . wp_kses_post( $settings['after_text_content'] ) . '</span>
			</span>';
		}

		echo '</' . Utils::validate_html_tag( $tag ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render fancy text widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Added repeater items custom styling.
	 * @since 1.3.1 Fixed validation errors.
	 */
	protected function content_template() {
		?>
		<#
		var cursorText = settings.fancy_text_cursor_text;
		var cursorTextEscaped = cursorText.replace( /'/g, "\\'" );
		var fancyTextSettings = {};

		if ( 'slide' === settings.animation_effect ) {
			view.addRenderAttribute( 'prefix', 'class', 'elementor-widget-cmsmasters-fancy-text__text-span-align' );
			view.addRenderAttribute( 'suffix', 'class', 'elementor-widget-cmsmasters-fancy-text__text-span-align' );
		}

		view.addRenderAttribute( 'wrapper', 'class', 'elementor-widget-cmsmasters-fancy-text__fancy-text-wrapper' );

		if ( settings.animated_scroll ) {
			view.addRenderAttribute( 'wrapper', 'class', 'elementor-widget-cmsmasters-fancy-text__scroll-animated' );
		}

		var tag = settings.fancy_tag;
		#>
		<{{{ tag }}} {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
		<# if ( '' !== settings.before_text_content ) { #>
			<span class="elementor-widget-cmsmasters-fancy-text__before-text">
				<span {{{ view.getRenderAttributeString( 'prefix' ) }}}>{{{ settings.before_text_content }}}</span>
			</span>
		<# } #>
		<# if ( 'typing' === settings.animation_effect ) { #>
			<span class="elementor-widget-cmsmasters-fancy-text__fancy-text fancy-text-hidden"></span>
		<# } else if ( 'slide' === settings.animation_effect ) { #>
			<div class="elementor-widget-cmsmasters-fancy-text__fancy-text">
				<ul class="elementor-widget-cmsmasters-fancy-text__list-items">
					<# _.each ( settings.fancy_text_list, ( item, index ) => {
						var repeaterItem = view.getRepeaterSettingKey( 'item', 'list_item', index );

						view.addRenderAttribute( repeaterItem, {
							'class': [
								'elementor-widget-cmsmasters-fancy-text__list-item',
								'elementor-repeater-item-' + item._id,
							],
						} );

						if ( '' !== item.item_text ) #>
							<li {{{ view.getRenderAttributeString( repeaterItem ) }}}>{{{ item.item_text }}}</li>
					<# } ); #>
					</ul>
			</div>
		<# } else {
			var strings_list = '';

			_.each ( settings.fancy_text_list, ( item, index ) => {
				var repeaterItem = view.getRepeaterSettingKey( 'item', 'list_item', index );

				view.addRenderAttribute( repeaterItem, {
					'class': [
						'elementor-widget-cmsmasters-fancy-text__list-item',
						'elementor-repeater-item-' + item._id,
					],
				} );

				strings_list += '<span ' + view.getRenderAttributeString( repeaterItem ) + '>' + item.item_text + '</span>, ';
			} ); #>
			<span class="elementor-widget-cmsmasters-fancy-text__fancy-text fancy-text-hidden">{{{ strings_list.replace( /, $/, '' ) }}}</span>
		<# } #>
		<# if ( '' !== settings.after_text_content ) { #>
			<span class="elementor-widget-cmsmasters-fancy-text__after-text">
				<span {{{ view.getRenderAttributeString( 'suffix' ) }}}>{{{ settings.after_text_content }}}</span>
			</span>
		<# } #>
		</{{{ tag }}}>
		<?php
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
				'field' => 'before_text_content',
				'type' => esc_html__( 'Before Text', 'cmsmasters-elementor' ),
				'editor_type' => 'AREA',
			),
			array(
				'field' => 'after_text_content',
				'type' => esc_html__( 'After Text', 'cmsmasters-elementor' ),
				'editor_type' => 'AREA',
			),
			array(
				'field' => 'fancy_text_cursor_text',
				'type' => esc_html__( 'Cursor Mark', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}

	/**
	 * Get fields_in_item config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields in item config.
	 */
	public static function get_wpml_fields_in_item() {
		return array(
			'fancy_text_list' => array(
				array(
					'field' => 'item_text',
					'type' => esc_html__( 'Item Title', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
			),
		);
	}
}
