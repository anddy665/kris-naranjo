<?php
namespace CmsmastersElementor\Modules\BeforeAfter\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Before_After extends Base_Widget {

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.7.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Before & After', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.7.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-before-after';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.7.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'image',
			'before',
			'after',
			'slider',
		);
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
	 * Retrieve the list of scripts the before after widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.7.0
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array_merge( array(
			'move',
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
			'widget-cmsmasters-before-after',
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

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-before-after';
	}

	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.7.0
	 * @since 1.11.8 Added `Border Radius` control for Before & After container.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		/* Tab Content */
		$this->section_before_after();
		$this->section_overlay();
		$this->section_handle();

		/* Tab Style */
		$this->section_style_container();
		$this->section_style_overlay();
		$this->section_style_handle();
	}

	protected function section_before_after() {
		$widget_selector = $this->get_widget_selector();

		$this->start_controls_section(
			'section_before_after',
			array( 'label' => esc_html__( 'Before & After', 'cmsmasters-elementor' ) )
		);

		$this->start_controls_tabs( 'before_after_images_tabs' );

		$this->start_controls_tab(
			'before_image_tab',
			array( 'label' => esc_html__( 'Before', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'before_image',
			array(
				'label' => esc_html__( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'before_image_css_filters',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__image-wrap img:first-child',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'after_image_tab',
			array( 'label' => esc_html__( 'After', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'after_image',
			array(
				'label' => esc_html__( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'after_image_css_filters',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__image-wrap img:nth-child(2)',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'thumbnail',
				'default' => 'full',
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'before_image[id]',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'after_image[id]',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			)
		);

		$this->add_control(
			'default_offset',
			array(
				'label' => esc_html__( 'Default Offset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
						'step' => 5,
					),
				),
				'default' => array(
					'unit' => '%',
					'size' => '50',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'orientation',
			array(
				'label' => esc_html__( 'Orientation', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'horizontal' => esc_html__( 'Horizontal', 'cmsmasters-elementor' ),
					'vertical' => esc_html__( 'Vertical', 'cmsmasters-elementor' ),
				),
				'default' => 'horizontal',
				'label_block' => false,
				'toggle' => false,
				'frontend_available' => true,
				'prefix_class' => 'cmsmasters-orientation-',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'move',
			array(
				'label' => esc_html__( 'Move On', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'click' => esc_html__( 'Pull/Click', 'cmsmasters-elementor' ),
					'on_hover' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
				),
				'default' => 'click',
				'label_block' => false,
				'toggle' => false,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'overlay',
			array(
				'label' => esc_html__( 'Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'No', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'frontend_available' => true,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'handle',
			array(
				'label' => esc_html__( 'Handle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'No', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	protected function section_overlay() {
		$widget_selector = $this->get_widget_selector();

		$this->start_controls_section(
			'section_overlay',
			array(
				'label' => esc_html__( 'Overlay', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'overlay' => 'yes' ),
			)
		);

		$this->add_control(
			'visibility',
			array(
				'label' => esc_html__( 'Visibility', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'hover' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
					'always' => esc_html__( 'Always', 'cmsmasters-elementor' ),
				),
				'default' => 'hover',
				'label_block' => false,
				'toggle' => false,
				'selectors_dictionary' => array(
					'hover' => 'opacity: 0;',
					'always' => 'opacity: 1;',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__overlay-before-label_wrap,' .
					'{{WRAPPER}} ' . $widget_selector . '__overlay-after-label_wrap' => '{{VALUE}}',
				),
			)
		);

		$this->start_controls_tabs(
			'overlay_items_tabs',
			array( 'condition' => array( 'overlay' => 'yes' ) )
		);

		$overlay_items = array(
			'before',
			'after',
		);

		foreach ( $overlay_items as $item ) {
			$this->start_controls_tab(
				$item . '_overlay_tab',
				array(
					'label' => esc_html( ucfirst( $item ) ),
					'condition' => array( 'overlay' => 'yes' ),
				)
			);

			$this->add_control(
				$item . '_label_icon_horizontal_alignment',
				array(
					'label' => esc_html__( 'Horizontal Alignment', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'left' => array(
							'title' => esc_html__( 'Left', 'cmsmasters-elementor' ),
							'icon' => 'eicon-h-align-left',
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'cmsmasters-elementor' ),
							'icon' => 'eicon-h-align-center',
						),
						'right' => array(
							'title' => esc_html__( 'Right', 'cmsmasters-elementor' ),
							'icon' => 'eicon-h-align-right',
						),
					),
					'default' => 'center',
					'selectors_dictionary' => array(
						'left' => 'left: 0; right: auto;',
						'center' => 'left: 0; right: 0;',
						'right' => 'left: auto; right: 0;',
					),
					'selectors' => array(
						'{{WRAPPER}} ' . $widget_selector . '__overlay-' . $item . '-label_wrap' => '{{VALUE}}',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'overlay',
								'operator' => '===',
								'value' => 'yes',
							),
							array(
								'name' => 'orientation',
								'operator' => '===',
								'value' => 'vertical',
							),
							array(
								'relation' => 'or',
								'terms' => array(
									array(
										'name' => $item . '_label',
										'operator' => '!==',
										'value' => '',
									),
									array(
										'name' => $item . '_icon[value]!',
										'operator' => '!==',
										'value' => '',
									),
								),
							),
						),
					),
				)
			);

			$this->add_control(
				$item . '_label_icon_vertical_alignment',
				array(
					'label' => esc_html__( 'Vertical Alignment', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'top' => array(
							'title' => esc_html__( 'Top', 'cmsmasters-elementor' ),
							'icon' => 'eicon-v-align-top',
						),
						'middle' => array(
							'title' => esc_html__( 'Middle', 'cmsmasters-elementor' ),
							'icon' => 'eicon-v-align-middle',
						),
						'bottom' => array(
							'title' => esc_html__( 'Bottom', 'cmsmasters-elementor' ),
							'icon' => 'eicon-v-align-bottom',
						),
					),
					'default' => 'middle',
					'selectors_dictionary' => array(
						'top' => 'top: 0; bottom: auto;',
						'middle' => 'top: 0; bottom: 0;',
						'bottom' => 'top: auto; bottom: 0;',
					),
					'selectors' => array(
						'{{WRAPPER}} ' . $widget_selector . '__overlay-' . $item . '-label_wrap' => '{{VALUE}}',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'overlay',
								'operator' => '===',
								'value' => 'yes',
							),
							array(
								'name' => 'orientation',
								'operator' => '===',
								'value' => 'horizontal',
							),
							array(
								'relation' => 'or',
								'terms' => array(
									array(
										'name' => $item . '_label',
										'operator' => '!==',
										'value' => '',
									),
									array(
										'name' => $item . '_icon[value]!',
										'operator' => '!==',
										'value' => '',
									),
								),
							),
						),
					),
				)
			);

			$this->add_control(
				$item . '_label',
				array(
					'label' => esc_html__( 'Label', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => ( 'before' === $item ? esc_html__( 'Before', 'cmsmasters-elementor' ) : esc_html__( 'After', 'cmsmasters-elementor' ) ),
					'condition' => array( 'overlay' => 'yes' ),
				)
			);

			$this->add_control(
				$item . '_icon',
				array(
					'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::ICONS,
					'label_block' => false,
					'skin' => 'inline',
					'condition' => array( 'overlay' => 'yes' ),
				)
			);

			$this->add_control(
				$item . '_icon_position',
				array(
					'label' => esc_html__( 'Icon Position', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'left' => array(
							'title' => esc_html__( 'Left', 'cmsmasters-elementor' ),
							'icon' => 'eicon-h-align-left',
						),
						'top' => array(
							'title' => esc_html__( 'Top', 'cmsmasters-elementor' ),
							'icon' => 'eicon-v-align-top',
						),
						'right' => array(
							'title' => esc_html__( 'Right', 'cmsmasters-elementor' ),
							'icon' => 'eicon-h-align-right',
						),
						'bottom' => array(
							'title' => esc_html__( 'Bottom', 'cmsmasters-elementor' ),
							'icon' => 'eicon-v-align-bottom',
						),
					),
					'default' => ( 'before' === $item ? 'left' : 'right' ),
					'selectors_dictionary' => array(
						'left' => 'row-reverse',
						'top' => 'column-reverse',
						'right' => 'row',
						'bottom' => 'column',
					),
					'toggle' => false,
					'prefix_class' => 'cmsmasters-' . $item . '-icon-position-',
					'render_type' => 'template',
					'selectors' => array(
						'{{WRAPPER}} ' . $widget_selector . '__overlay-' . $item . '-label' => 'flex-direction: {{VALUE}};',
					),
					'condition' => array(
						'overlay' => 'yes',
						$item . '_icon[value]!' => '',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function section_handle() {
		$this->start_controls_section(
			'section_handle',
			array(
				'label' => esc_html__( 'Handle', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'handle' => 'yes' ),
			)
		);

		$this->add_control(
			'imitation_handle',
			array(
				'label' => esc_html__( 'Imitation Handle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'No', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'handle_type',
			array(
				'label' => esc_html__( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'icons' => array(
						'title' => esc_html__( 'Icons', 'cmsmasters-elementor' ),
					),
					'label' => array(
						'title' => esc_html__( 'Label', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'icons',
				'label_block' => false,
				'condition' => array( 'handle' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'handle_label',
			array(
				'label' => esc_html__( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Drag', 'cmsmasters-elementor' ),
				'condition' => array(
					'handle' => 'yes',
					'handle_type' => 'label',
				),
			)
		);

		$this->add_control(
			'handle_icon_type',
			array(
				'label' => esc_html__( 'Icon Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => array(
						'title' => esc_html__( 'Default', 'cmsmasters-elementor' ),
					),
					'custom' => array(
						'title' => esc_html__( 'Custom', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'default',
				'label_block' => false,
				'condition' => array(
					'handle' => 'yes',
					'handle_type' => 'icons',
				),
			)
		);

		$this->add_control(
			'handle_icon',
			array(
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'condition' => array(
					'handle' => 'yes',
					'handle_type' => 'icons',
					'handle_icon_type' => 'custom',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function section_style_container() {
		$this->start_controls_section(
			'section_style_container',
			array(
				'label' => esc_html__( 'Before & After', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'container_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--container-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function section_style_overlay() {
		$widget_selector = $this->get_widget_selector();

		$label_icon_conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'overlay',
					'operator' => '===',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'before_label',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'before_icon[value]',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'after_label',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'after_icon[value]',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			),
		);

		$this->start_controls_section(
			'section_style_overlay',
			array(
				'label' => esc_html__( 'Overlay', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'conditions' => $label_icon_conditions,
			)
		);

		$label_conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'overlay',
					'operator' => '===',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'before_label',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'after_label',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			),
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'overlay_label_typography',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__overlay-before-label, {{WRAPPER}} ' . $widget_selector . '__overlay-after-label',
				'conditions' => $label_conditions,
			)
		);

		$this->add_control(
			'overlay_label_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__overlay-before-label,' .
					'{{WRAPPER}} ' . $widget_selector . '__overlay-after-label' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'conditions' => $label_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'overlay_label_bg_color',
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'exclude' => array( 'image' ),
				'fields_options' => array(
					'color' => array(
						'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__overlay-before-label:before, {{WRAPPER}} ' . $widget_selector . '__overlay-after-label:before',
				'conditions' => $label_icon_conditions,
			)
		);

		$this->add_control(
			'overlay_label_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__overlay-before-label,' .
					'{{WRAPPER}} ' . $widget_selector . '__overlay-after-label' => 'border-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'overlay',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'before_label',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'after_label',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'before_icon[value]',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'after_icon[value]!',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'name' => 'overlay_label_border_border',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'overlay_label_hor_gap',
			array(
				'label' => esc_html__( 'Horizontal Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'%' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'size_units' => array(
					'px',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--overlay-label-icon-hor-gap: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $label_icon_conditions,
			)
		);

		$this->add_responsive_control(
			'overlay_label_ver_gap',
			array(
				'label' => esc_html__( 'Vertical Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'%' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--overlay-label-icon-ver-gap: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $label_icon_conditions,
			)
		);

		$this->add_responsive_control(
			'overlay_label_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__overlay-before-label,' .
					'{{WRAPPER}} ' . $widget_selector . '__overlay-after-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $label_icon_conditions,
			)
		);

		$this->add_control(
			'overlay_label_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__overlay-before-label,' .
					'{{WRAPPER}} ' . $widget_selector . '__overlay-after-label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $label_icon_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'overlay_label_border',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__overlay-before-label, {{WRAPPER}} ' . $widget_selector . '__overlay-after-label',
				'conditions' => $label_icon_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'overlay_label_box_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__overlay-before-label, {{WRAPPER}} ' . $widget_selector . '__overlay-after-label',
				'conditions' => $label_icon_conditions,
			)
		);

		$icon_conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'overlay',
					'operator' => '===',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'before_icon[value]',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'after_icon[value]!',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			),
		);

		$this->add_control(
			'overlay_icon_heading',
			array(
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $icon_conditions,
			)
		);

		$this->add_control(
			'overlay_icon_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__overlay-before-label-icon,' .
					'{{WRAPPER}} ' . $widget_selector . '__overlay-after-label-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'conditions' => $icon_conditions,
			)
		);

		$this->add_responsive_control(
			'overlay_icon_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__overlay-before-label-icon,' .
					'{{WRAPPER}} ' . $widget_selector . '__overlay-after-label-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__overlay-before-label-icon svg,',
					'{{WRAPPER}} ' . $widget_selector . '__overlay-after-label-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $icon_conditions,
			)
		);

		$this->add_responsive_control(
			'overlay_icon_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--overlay-icon-gap: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $icon_conditions,
			)
		);

		$this->end_controls_section();
	}

	protected function section_style_handle() {
		$widget_selector = $this->get_widget_selector();

		$handle_conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'handle',
					'operator' => '===',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'handle_type',
									'operator' => '===',
									'value' => 'label',
								),
								array(
									'name' => 'handle_label',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'handle_type',
									'operator' => '===',
									'value' => 'icons',
								),
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'relation' => 'and',
											'terms' => array(
												array(
													'name' => 'handle_icon_type',
													'operator' => '===',
													'value' => 'custom',
												),
												array(
													'name' => 'handle_icon[value]',
													'operator' => '!==',
													'value' => '',
												),
											),
										),
										array(
											'name' => 'handle_icon_type',
											'operator' => '===',
											'value' => 'default',
										),
									),
								),
							),
						),
					),
				),
			),
		);

		$handle_border_conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'handle',
					'operator' => '===',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'handle_type',
									'operator' => '===',
									'value' => 'label',
								),
								array(
									'name' => 'handle_label',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'handle_type',
									'operator' => '===',
									'value' => 'icons',
								),
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'relation' => 'and',
											'terms' => array(
												array(
													'name' => 'handle_icon_type',
													'operator' => '===',
													'value' => 'custom',
												),
												array(
													'name' => 'handle_icon[value]',
													'operator' => '!==',
													'value' => '',
												),
											),
										),
										array(
											'name' => 'handle_icon_type',
											'operator' => '===',
											'value' => 'default',
										),
									),
								),
							),
						),
					),
				),
				array(
					'name' => 'handle_border_border',
					'operator' => '!==',
					'value' => 'none',
				),
			),
		);

		$this->start_controls_section(
			'section_style_handle',
			array(
				'label' => esc_html__( 'Handle', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array( 'handle' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'handle_label_typography',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__handle',
				'condition' => array(
					'handle' => 'yes',
					'handle_type' => 'label',
					'handle_label!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'handle_icon_size',
			array(
				'label' => esc_html__( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__handle-before-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__handle-before-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_selector . '__handle-left-arrow' => 'border-width: {{SIZE}}{{UNIT}}; margin-right: calc( {{SIZE}}{{UNIT}} / 3 * 2)',
					'{{WRAPPER}} ' . $widget_selector . '__handle-right-arrow' => 'border-width: {{SIZE}}{{UNIT}}; margin-left: calc( {{SIZE}}{{UNIT}} / 3 * 2)',
					'{{WRAPPER}} ' . $widget_selector . '__handle-up-arrow' => 'border-width: {{SIZE}}{{UNIT}}; margin-bottom: calc( {{SIZE}}{{UNIT}} / 3 * 2)',
					'{{WRAPPER}} ' . $widget_selector . '__handle-down-arrow' => 'border-width: {{SIZE}}{{UNIT}}; margin-top: calc( {{SIZE}}{{UNIT}} / 3 * 2)',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'handle',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'handle_type',
							'operator' => '===',
							'value' => 'icons',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'handle_icon_type',
											'operator' => '===',
											'value' => 'custom',
										),
										array(
											'name' => 'handle_icon[value]',
											'operator' => '!==',
											'value' => '',
										),
									),
								),
								array(
									'name' => 'handle_icon_type',
									'operator' => '===',
									'value' => 'default',
								),
							),
						),
					),
				),
			)
		);

		$this->start_controls_tabs(
			'handle_tabs',
			array( 'conditions' => $handle_conditions )
		);

		$this->start_controls_tab(
			'handle_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
				'conditions' => $handle_conditions,
			)
		);

		$this->add_control(
			'handle_normal_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__handle' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__handle ' . $widget_selector . '__handle-left-arrow' => 'border-right-color: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__handle ' . $widget_selector . '__handle-right-arrow' => 'border-left-color: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__handle ' . $widget_selector . '__handle-up-arrow' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__handle ' . $widget_selector . '__handle-down-arrow' => 'border-top-color: {{VALUE}};',
				),
				'conditions' => $handle_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'handle_normal_bg_color',
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'exclude' => array( 'image' ),
				'fields_options' => array(
					'color' => array(
						'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__handle',
				'conditions' => $handle_conditions,
			)
		);

		$this->add_control(
			'handle_normal_bd_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__handle:not(.handle_empty)' => 'border-color: {{VALUE}};',
				),
				'conditions' => $handle_border_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'handle_normal_box_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__handle:not(.handle_empty)',
				'conditions' => $handle_conditions,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'handle_hover_tab',
			array(
				'label' => esc_html__( 'Hover/Active', 'cmsmasters-elementor' ),
				'conditions' => $handle_conditions,
			)
		);

		$this->add_control(
			'handle_hover_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__handle:hover' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__handle:hover ' . $widget_selector . '__handle-left-arrow' => 'border-right-color: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__handle:hover ' . $widget_selector . '__handle-right-arrow' => 'border-left-color: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__handle:hover ' . $widget_selector . '__handle-up-arrow' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} ' . $widget_selector . '__handle:hover ' . $widget_selector . '__handle-down-arrow' => 'border-top-color: {{VALUE}};',
				),
				'conditions' => $handle_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'handle_hover_bg_color',
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'exclude' => array( 'image' ),
				'fields_options' => array(
					'color' => array(
						'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__handle:hover',
				'conditions' => $handle_conditions,
			)
		);

		$this->add_control(
			'handle_hover_bd_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__handle:hover' => 'border-color: {{VALUE}};',
				),
				'conditions' => $handle_border_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'handle_hover_box_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__handle:not(.handle_empty):hover',
				'conditions' => $handle_conditions,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'handle_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 30,
						'max' => 100,
					),
				),
				'render_type' => 'template',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--handle-size: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $handle_conditions,
			)
		);

		$this->add_responsive_control(
			'handle_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--handle-gap: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $handle_conditions,
			)
		);

		$this->add_control(
			'handle_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__handle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $handle_conditions,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'handle_border',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => _x( 'Default', 'Border Control', 'cmsmasters-elementor' ),
							'none' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => '',
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__handle',
				'conditions' => $handle_conditions,
			)
		);

		$this->add_control(
			'handle_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'conditions' => $handle_conditions,
			)
		);

		$this->add_control(
			'handle_divider_heading',
			array(
				'label' => esc_html__( 'Divider', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array( 'handle' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'handle_divider_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 15,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--handle-divider-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'handle' => 'yes' ),
			)
		);

		$this->start_controls_tabs(
			'handle_divider_tabs',
			array(
				'condition' => array(
					'handle' => 'yes',
					'handle_divider_size[size]!' => '0',
				),
			)
		);

		$this->start_controls_tab(
			'handle_divider_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
				'condition' => array(
					'handle' => 'yes',
					'handle_divider_size[size]!' => '0',
				),
			)
		);

		$this->add_control(
			'handle_divider_normal_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__handle:before,' .
					'{{WRAPPER}} ' . $widget_selector . '__handle:after' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'handle' => 'yes',
					'handle_divider_size[size]!' => '0',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'handle_divider_normal_box_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__handle:before, {{WRAPPER}} ' . $widget_selector . '__handle:after',
				'condition' => array(
					'handle' => 'yes',
					'handle_divider_size[size]!' => '0',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'handle_divider_hover_tab',
			array(
				'label' => esc_html__( 'Hover/Active', 'cmsmasters-elementor' ),
				'condition' => array(
					'handle' => 'yes',
					'handle_divider_size[size]!' => '0',
				),
			)
		);

		$this->add_control(
			'handle_hover_normal_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_selector . '__handle:hover:before,' .
					'{{WRAPPER}} ' . $widget_selector . '__handle:hover:after' => 'background: {{VALUE}};',
				),
				'condition' => array(
					'handle' => 'yes',
					'handle_divider_size[size]!' => '0',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'handle_hover_normal_box_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_selector . '__handle:hover:before, ' . $widget_selector . '__handle:hover:after',
				'condition' => array(
					'handle' => 'yes',
					'handle_divider_size[size]!' => '0',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	protected function get_render_overlay( $settings ) {
		$overlay = ( isset( $settings['overlay'] ) && 'yes' === $settings['overlay'] ? $settings['overlay'] : '' );

		if ( $overlay ) {
			echo '<div class="' . $this->get_widget_class() . '__overlay">';

			$overlay_items = array(
				'before',
				'after',
			);

			foreach ( $overlay_items as $item ) {
				$item_label = ( isset( $settings[ $item . '_label' ] ) && ! empty( $settings[ $item . '_label' ] ) ? $settings[ $item . '_label' ] : '' );
				$item_icon = ( isset( $settings[ $item . '_icon' ] ) && ! empty( $settings[ $item . '_icon' ]['value'] ) ? $settings[ $item . '_icon' ] : '' );

				if ( $item_label || $item_icon ) {
					echo '<div class="' . $this->get_widget_class() . '__overlay-' . esc_attr( $item ) . '-label_wrap">' .
						'<div class="' . $this->get_widget_class() . '__overlay-' . esc_attr( $item ) . '-label">';

					if ( $item_label ) {
						echo '<div class="' . $this->get_widget_class() . '__overlay-' . esc_attr( $item ) . '-label-text">' .
							esc_html( $item_label ) .
						'</div>';
					}

					if ( $item_icon ) {
						$item_icon_att = array( 'aria-hidden' => 'true' );

						if ( ! $item_label ) {
							$item_icon_att = array_merge(
								$item_icon_att,
								array( 'aria-label' => ucfirst( $item ) ),
							);
						}

						echo '<div class="' . $this->get_widget_class() . '__overlay-' . esc_attr( $item ) . '-label-icon">';
							Icons_Manager::render_icon( $item_icon, $item_icon_att );
						echo '</div>';
					}

						echo '</div>' .
					'</div>';
				}
			}

			echo '</div>';
		}
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	protected function get_render_handle( $settings ) {
		$handle = ( isset( $settings['handle'] ) && 'yes' === $settings['handle'] ? $settings['handle'] : '' );
		$handle_type = ( isset( $settings['handle_type'] ) ? $settings['handle_type'] : '' );
		$handle_label = ( isset( $settings['handle_label'] ) ? $settings['handle_label'] : '' );
		$handle_icon_type = ( isset( $settings['handle_icon_type'] ) ? $settings['handle_icon_type'] : '' );
		$handle_icon = ( isset( $settings['handle_icon'] ) && '' !== $settings['handle_icon']['value'] ? $settings['handle_icon'] : '' );
		$orientation = ( isset( $settings['orientation'] ) ? $settings['orientation'] : '' );
		$handle_empty = '';

		if (
			( 'icons' === $handle_type && 'custom' === $handle_icon_type && empty( $handle_icon ) ) ||
			( 'label' === $handle_type && empty( $handle_label ) )
		) {
			$handle_empty = ' handle_empty';
		}

		if ( $handle ) {
			echo '<div class="' . $this->get_widget_class() . '__handle' . esc_attr( $handle_empty ) . '">';

			if ( 'icons' === $handle_type ) {
				if ( 'custom' === $handle_icon_type && $handle_icon ) {
					echo '<div class="' . $this->get_widget_class() . '__handle-before-icon">';
						Icons_Manager::render_icon( $handle_icon, array( 'aria-hidden' => 'true' ) );
					echo '</div>';
				} elseif ( 'default' === $handle_icon_type ) {
					echo '<span class="' . $this->get_widget_class() . '__handle-' . ( ( 'vertical' === $orientation ) ? 'up' : 'left' ) . '-arrow"></span>';
					echo '<span class="' . $this->get_widget_class() . '__handle-' . ( ( 'vertical' === $orientation ) ? 'down' : 'right' ) . '-arrow"></span>';
				}
			} elseif ( 'label' === $handle_type && '' !== $handle_label ) {
				echo '<div class="' . $this->get_widget_class() . '__handle-label">' .
					esc_html( $handle_label ) .
				'</div>';
			}

			echo '</div>';
		}
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$orientation = ( $settings['orientation'] ? $settings['orientation'] : '' );

		$this->add_render_attribute( 'wrapper', 'class', array(
			$this->get_widget_class() . '__wrapper',
			( ! empty( $orientation ) ? ' ' . $this->get_widget_class() . '__' . $orientation : '' ),
		) );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '">' .
			'<div class="' . $this->get_widget_class() . '__container" id="' . $this->get_widget_class() . '__container-' . esc_attr( $this->get_id() ) . '">' .
				'<div class="' . $this->get_widget_class() . '__image-wrap">' .
					Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'before_image' ) .
					Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'after_image' ) .
				'</div>';

				$this->get_render_overlay( $settings );

				$this->get_render_handle( $settings );

			echo '</div>' .
		'</div>';
	}

	protected function content_template() {}
}
