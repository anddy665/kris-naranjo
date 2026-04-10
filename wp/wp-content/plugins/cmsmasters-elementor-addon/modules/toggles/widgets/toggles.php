<?php
namespace CmsmastersElementor\Modules\Toggles\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Plugin as CmsmastersPlugin;
use CmsmastersElementor\Traits\Extendable_Widget;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Toggles extends Base_Widget {

	use Extendable_Widget;

	/**
	 * Get extendable widget class.
	 *
	 * Retrieve the extendable widget container class.
	 *
	 * @since 1.3.0
	 *
	 * @return string Extendable widget container class.
	 */
	public function get_extendable_widget_class() {
		return 'cmsmasters-widget-toggles';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.3.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Toggles', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.3.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-toggle';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.3.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'toggles',
			'accordion',
		);
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
			'widget-cmsmasters-toggles',
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
	 * Get the selector for the animation type 'Item by item'.
	 *
	 * @since 1.18.0
	 */
	public function get_separate_animation_selector() {
		return '.elementor-widget-cmsmasters-toggles__item';
	}

	/**
	 * Get the class name ot the block for the text animation types 'Line by Line', 'Word by Word', 'Char by Char', etc.
	 * and names of animation types suitable for this widget.
	 *
	 * @since 1.18.0
	 */
	public function get_text_animation_class() {
		return array( 'elementor-widget-cmsmasters-toggles__item' => 'sequental, random' );
	}

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-toggles';
	}

	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	/**
	 * LazyLoad widget use control.
	 *
	 * @since 1.11.1
	 *
	 * @return bool true - with control, false - without control.
	 */
	public function lazyload_widget_use_control() {
		return true;
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.3.0
	 * @since 1.3.3 Added `Active Toggle` control for first load.
	 * @since 1.7.5 Added `Custom ID` control for anchor tag.
	 * @since 1.8.0 Added `Border Radius` control for toggle title on hover/active state.
	 * @since 1.11.9 Fixed display of container templates in saved sections.
	 * @since 1.18.0 Added `Entrance Animation` and `Entrance Animation Text` hidden control for entrance animation.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$widget_selector = $this->get_widget_selector();

		$widget_item = $widget_selector . '__item';
		$widget_title = $widget_selector . '__title';
		$widget_title_text = $widget_selector . '__title-text';
		$widget_item_icon = $widget_selector . '__item-icon';
		$widget_icon = $widget_selector . '__trigger';
		$widget_content = $widget_selector . '__content';

		$this->start_controls_section(
			'section_title',
			array( 'label' => esc_html__( 'Toggles', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'entrance_animation',
			array(
				'label' => __( 'Entrance Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'entrance_animation_text',
			array(
				'label' => __( 'Entrance Animation Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => '',
			)
		);

		$this->add_control(
			'type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'toggles' => array(
						'title' => __( 'Toggles', 'cmsmasters-elementor' ),
					),
					'accordion' => array(
						'title' => __( 'Accordion', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'toggles',
				'render_type' => 'template',
				'frontend_available' => true,
				'toggle' => false,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'toggle_title',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Toggles Title', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$content_types = array(
			'toggle-content' => array(
				'title' => __( 'Content', 'cmsmasters-elementor' ),
			),
			'section' => array(
				'title' => __( 'Section', 'cmsmasters-elementor' ),
				'description' => __( 'Saved Section', 'cmsmasters-elementor' ),
			),
			'template' => array(
				'title' => __( 'Page', 'cmsmasters-elementor' ),
				'description' => __( 'Saved Page Template', 'cmsmasters-elementor' ),
			),
		);

		if ( CmsmastersUtils::is_pro() ) {
			$content_types['widget'] = array(
				'title' => __( 'Widget', 'cmsmasters-elementor' ),
				'description' => __( 'Saved Global Widget', 'cmsmasters-elementor' ),
			);
		}

		$repeater->add_control(
			'content_type',
			array(
				'label' => __( 'Content Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => $content_types,
				'default' => 'toggle-content',
				'separator' => 'before',
				'toggle' => false,
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'toggle_content',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'default' => __( 'Toggle Content', 'cmsmasters-elementor' ),
				'placeholder' => __( 'Toggle Content', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::WYSIWYG,
				'show_label' => false,
				'condition' => array( 'content_type' => 'toggle-content' ),
			)
		);

		if ( '' !== $this->get_page_template_options( 'section' ) || '' !== $this->get_page_template_options( 'container' ) ) {
			$repeater->add_control(
				'saved_section',
				array(
					'label' => __( 'Choose Section', 'cmsmasters-elementor' ),
					'label_block' => true,
					'show_label' => false,
					'type' => CmsmastersControls::QUERY,
					'autocomplete' => array(
						'object' => Query_Manager::TEMPLATE_OBJECT,
						'query' => array(
							'meta_query' => array(
								array(
									'key' => Document::TYPE_META_KEY,
									'value' => array(
										'section',
										'container',
									),
								),
							),
						),
					),
					'condition' => array( 'content_type' => 'section' ),
				)
			);
		} else {
			$repeater->add_control(
				'saved_section_notice',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw' => sprintf( __( '<strong>There are no saved sections or containers in your site.</strong><br>Go to the <a href="%s" target="_blank">Saved Section</a> to create one.', 'cmsmasters-elementor' ), admin_url( 'edit.php?post_type=elementor_library&tabs_group=library&elementor_library_type=section' ) ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'condition' => array( 'content_type' => 'section' ),
				)
			);
		}

		if ( '' !== $this->get_page_template_options( 'page' ) ) {
			$repeater->add_control(
				'saved_template',
				array(
					'label' => __( 'Choose Template', 'cmsmasters-elementor' ),
					'label_block' => true,
					'show_label' => false,
					'type' => CmsmastersControls::QUERY,
					'autocomplete' => array(
						'object' => Query_Manager::TEMPLATE_OBJECT,
						'query' => array(
							'meta_query' => array(
								array(
									'key' => Document::TYPE_META_KEY,
									'value' => 'page',
								),
							),
						),
					),
					'condition' => array( 'content_type' => 'template' ),
				)
			);
		} else {
			$repeater->add_control(
				'saved_template_notice',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw' => sprintf( __( '<strong>There are no templates in your site.</strong><br>Go to the <a href="%s" target="_blank">Saved Templates</a> to create one.', 'cmsmasters-elementor' ), admin_url( 'edit.php?post_type=elementor_library&tabs_group=library&elementor_library_type=page' ) ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'condition' => array( 'content_type' => 'template' ),
				)
			);
		}

		if ( '' !== $this->get_page_template_options( 'widget' ) ) {
			$repeater->add_control(
				'saved_widget',
				array(
					'label' => __( 'Choose Widget', 'cmsmasters-elementor' ),
					'label_block' => true,
					'show_label' => false,
					'type' => CmsmastersControls::QUERY,
					'autocomplete' => array(
						'object' => Query_Manager::TEMPLATE_OBJECT,
						'query' => array(
							'meta_query' => array(
								array(
									'key' => Document::TYPE_META_KEY,
									'value' => 'widget',
								),
							),
						),
					),
					'export' => false,
					'condition' => array( 'content_type' => 'widget' ),
				)
			);
		} else {
			$repeater->add_control(
				'saved_widget_notice',
				array(
					'type' => Controls_Manager::RAW_HTML,
					'raw' => sprintf( __( '<strong>There are no saved global widgets in your site.</strong><br>Go to the <a href="%s" target="_blank">Global Widget</a> to create one.', 'cmsmasters-elementor' ), admin_url( 'edit.php?post_type=elementor_library&tabs_group=library&elementor_library_type=widget' ) ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'condition' => array( 'content_type' => 'widget' ),
				)
			);
		}

		$repeater->add_control(
			'item_icon',
			array(
				'label' => esc_html__( 'Item Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'separator' => 'before',
				'skin' => 'inline',
				'label_block' => false,
			)
		);

		$repeater->add_control(
			'toggle_custom_id',
			array(
				'label' => __( 'Custom ID', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Tab Custom ID', 'cmsmasters-elementor' ),
				'description' => __( 'Custom ID will be added as an anchor tag. For example, if you add ‘test’ as your custom ID, the link will become like the following: https://www.example.com/#test and it will open the respective tab directly.', 'cmsmasters-elementor' ),
				'label_block' => true,
				'frontend_available' => true,
				'separator' => 'before',
				'condition' => array( 'toggle_title!' => '' ),
			)
		);

		$this->add_control(
			'toggles',
			array(
				'label' => esc_html__( 'Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'frontend_available' => true,
				'default' => array(
					array(
						'toggle_title' => esc_html__( 'Item #1', 'cmsmasters-elementor' ),
						'toggle_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'cmsmasters-elementor' ),
					),
					array(
						'toggle_title' => esc_html__( 'Item #2', 'cmsmasters-elementor' ),
						'toggle_content' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'cmsmasters-elementor' ),
					),
				),
				'title_field' => '{{{ toggle_title }}}',
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

		$this->add_control(
			'default_toggle',
			array(
				'label' => __( 'Active Toggle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'label_block' => false,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'title_heading',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'title_alignment',
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
				'prefix_class' => 'cmsmasters-title-alignment%s-',
				'default' => 'left',
				'label_block' => false,
				'toggle' => false,
			)
		);

		$this->add_control(
			'title_html_tag',
			array(
				'label' => esc_html__( 'Title HTML Tag', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				),
				'default' => 'h6',
			)
		);

		$this->add_control(
			'item_icon_position',
			array(
				'label' => __( 'Item Icon Position', 'cmsmasters-elementor' ),
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
				'toggle' => false,
				'label_block' => false,
				'default' => 'left',
				'prefix_class' => 'cmsmasters-item-icon-position-',
			)
		);

		$this->add_control(
			'icon_heading',
			array(
				'label' => __( 'Trigger', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'trigger_icon',
			array(
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-plus',
					'library' => 'fa-solid',
				),
				'recommended' => array(
					'fa-solid' => array(
						'plus',
						'plus-square',
						'plus-circle',
						'chevron-down',
						'angle-down',
						'angle-double-down',
						'caret-down',
						'caret-square-down',
					),
					'fa-regular' => array(
						'plus-square',
						'caret-square-down',
					),
				),
				'skin' => 'inline',
				'label_block' => false,
			)
		);

		$this->add_control(
			'trigger_active_icon',
			array(
				'label' => esc_html__( 'Active Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon_active',
				'default' => array(
					'value' => 'fas fa-minus',
					'library' => 'fa-solid',
				),
				'recommended' => array(
					'fa-solid' => array(
						'minus',
						'minus-square',
						'minus-circle',
						'chevron-up',
						'angle-up',
						'angle-double-up',
						'caret-up',
						'caret-square-up',
					),
					'fa-regular' => array(
						'minus-square',
						'caret-square-up',
					),
				),
				'skin' => 'inline',
				'label_block' => false,
				'condition' => array( 'trigger_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'faq_schema',
			array(
				'label' => esc_html__( 'FAQ Schema', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_toggle_style',
			array(
				'label' => esc_html__( 'Toggle', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'toggle_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_item => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'toggle_space_between',
			array(
				'label' => esc_html__( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_item . ' + ' . $widget_item => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'toggle_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
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
				'selector' => '{{WRAPPER}} ' . $widget_item,
			)
		);

		$this->add_responsive_control(
			'toggle_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_item => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'toggle_color_tabs',
			array()
		);

		$this->start_controls_tab(
			'toggle_color_tab_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'toggle_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_item => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_bd_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_item => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'toggle_border_border!' => 'none' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'toggle_box_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_item,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'toggle_color_tab_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'toggle_bg_color_hover',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_item . ':hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_bd_color_hover',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_item . ':hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'toggle_border_border!' => 'none' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'toggle_box_shadow_hover',
				'selector' => '{{WRAPPER}} ' . $widget_item . ':hover',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'toggle_color_tab_active',
			array( 'label' => __( 'Active', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'toggle_bg_color_active',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_item . '.active-toggle:hover, ' .
					'{{WRAPPER}} ' . $widget_item . '.active-toggle' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_bd_color_active',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_item . '.active-toggle:hover, ' .
					'{{WRAPPER}} ' . $widget_item . '.active-toggle' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'toggle_box_shadow_active',
				'selector' => '{{WRAPPER}} ' . $widget_item . '.active-toggle:hover, {{WRAPPER}} ' . $widget_item . '.active-toggle',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'toggle_last_item_heading',
			array(
				'label' => __( 'Last Item', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'toggle_last_item_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
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
				'selector' => '{{WRAPPER}} ' . $widget_item . ':last-of-type',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} ' . $widget_title,
			)
		);

		$this->start_controls_tabs(
			'title_colors_tabs',
			array()
		);

		$this->start_controls_tab(
			'title_colors_tab_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'title_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title . ', ' .
					'{{WRAPPER}} ' . $widget_title . ' a' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_bg',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_bd_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'title_border_border!' => '' ),
			)
		);

		$this->add_responsive_control(
			'title_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'title_box_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_title,
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'title_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_title_text,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_colors_tab_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title . ':hover, ' .
					'{{WRAPPER}} ' . $widget_title . ':hover a' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_background_hover',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title . ':hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_bd_color_hover',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title . ':hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'title_border_border!' => '' ),
			)
		);

		$this->add_responsive_control(
			'title_border_radius_hover',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title . ':hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'title_box_shadow_hover',
				'selector' => '{{WRAPPER}} ' . $widget_title . ':hover',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'title_text_shadow_hover',
				'selector' => '{{WRAPPER}} ' . $widget_title . ':hover ' . $widget_title_text,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_colors_tab_active',
			array( 'label' => __( 'Active', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'title_color_active',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .active-toggle ' . $widget_title . ', ' .
					'{{WRAPPER}} .active-toggle ' . $widget_title . ' a, ' .
					'{{WRAPPER}} .active-toggle ' . $widget_title . ':hover, ' .
					'{{WRAPPER}} .active-toggle ' . $widget_title . ':hover a' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_background_active',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .active-toggle ' . $widget_title . ', ' .
					'{{WRAPPER}} .active-toggle ' . $widget_title . ':hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_bd_color_active',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .active-toggle ' . $widget_title . ', ' .
					'{{WRAPPER}} .active-toggle ' . $widget_title . ':hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'title_border_border!' => '' ),
			)
		);

		$this->add_responsive_control(
			'title_border_radius_active',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .active-toggle ' . $widget_title . ', ' .
					'{{WRAPPER}} .active-toggle ' . $widget_title . ':hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'title_box_shadow_active',
				'selector' => '{{WRAPPER}} .active-toggle ' . $widget_title . ', {{WRAPPER}} .active-toggle ' . $widget_title . ':hover',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'title_text_shadow_active',
				'selector' => '{{WRAPPER}} .active-toggle ' . $widget_title . ':hover ' . $widget_title_text . ', {{WRAPPER}} .active-toggle ' . $widget_title_text,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'title_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'title_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_title,
			)
		);

		$this->add_control(
			'item_icon_style_heading',
			array(
				'label' => __( 'Item Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'item_icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 80,
					),
					'em' => array(
						'min' => 0.8,
						'max' => 2,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_item_icon => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $widget_item_icon . ' svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_icon_space',
			array(
				'label' => esc_html__( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-item-icon-position-left ' . $widget_item_icon => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-item-icon-position-right ' . $widget_item_icon => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_trigger_icon_style',
			array(
				'label' => esc_html__( 'Trigger', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'trigger_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'trigger_icon_view',
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
				'prefix_class' => 'cmsmasters-trigger-icon-view-',
			)
		);

		$this->add_responsive_control(
			'trigger_icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 80,
					),
					'em' => array(
						'min' => 0.8,
						'max' => 2,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--trigger-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'trigger_icon_colors_tabs',
			array()
		);

		$this->start_controls_tab(
			'trigger_icon_colors_tab_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'trigger_icon_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_icon => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'trigger_icon_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_icon => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'trigger_icon_view!' => 'default' ),
			)
		);

		$this->add_control(
			'trigger_icon_bd_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_icon => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'trigger_icon_view' => 'framed' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'trigger_icon_colors_tab_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'trigger_icon_color_hover',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title . ':hover ' . $widget_icon => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'trigger_icon_bg_color_hover',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title . ':hover ' . $widget_icon => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'trigger_icon_view!' => 'default' ),
			)
		);

		$this->add_control(
			'trigger_icon_bd_color_hover',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title . ':hover ' . $widget_icon => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'trigger_icon_view' => 'framed' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'trigger_icon_colors_tab_active',
			array( 'label' => __( 'Active', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'trigger_icon_color_active',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title . '.active-toggle ' . $widget_icon => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'trigger_icon_bg_color_active',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title . '.active-toggle ' . $widget_icon => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'trigger_icon_view!' => 'default' ),
			)
		);

		$this->add_control(
			'trigger_icon_bd_color_active',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_title . '.active-toggle ' . $widget_icon => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'trigger_icon_view' => 'framed' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'trigger_icon_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--trigger-icon-padding-top: {{TOP}}{{UNIT}};
						--trigger-icon-padding-right: {{RIGHT}}{{UNIT}};
						--trigger-icon-padding-bottom: {{BOTTOM}}{{UNIT}};
						--trigger-icon-padding-left: {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'trigger_icon_view!' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'trigger_icon_bd',
			array(
				'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_icon => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}' => '--trigger-icon-border-top: {{TOP}}{{UNIT}};
						--trigger-icon-border-right: {{RIGHT}}{{UNIT}};
						--trigger-icon-border-bottom: {{BOTTOM}}{{UNIT}};
						--trigger-icon-border-left: {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'trigger_icon_view' => 'framed' ),
			)
		);

		$this->add_responsive_control(
			'trigger_icon_bd_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_icon => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'trigger_icon_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'trigger_icon_box_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_icon,
				'condition' => array( 'trigger_icon_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'trigger_icon_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_icon,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_toggle_style_content',
			array(
				'label' => esc_html__( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_alignment',
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
				'default' => 'left',
				'label_block' => false,
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_content => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} ' . $widget_content,
			)
		);

		$this->add_control(
			'content_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_content => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'content_link_hover',
			array(
				'label' => esc_html__( 'Link Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_content . ' a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'content_background_color',
			array(
				'label' => esc_html__( 'Background', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_content => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_content => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_content => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'content_border_width',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
					),
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} ' . $widget_item . ' ' . $widget_content,
			)
		);

		$this->add_responsive_control(
			'content_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $widget_item . ' ' . $widget_content => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'content_box_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_content,
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'content_shadow',
				'selector' => '{{WRAPPER}} ' . $widget_content,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get template IDs.
	 *
	 * @since 1.11.1
	 * @since 1.12.1 Add checking template.
	 *
	 * @return array Template IDs.
	 */
	public function get_template_ids() {
		$template_ids = array();

		$toggles = $this->get_settings_for_display( 'toggles' );

		foreach ( $toggles as $item ) {
			if ( ! in_array( $item['content_type'], array( 'section', 'template', 'widget' ) ) ) {
				continue;
			}

			$template_id = $item[ "saved_{$item['content_type']}" ];

			if ( ! CmsmastersUtils::check_template( $template_id ) ) {
				continue;
			}

			$template_ids[] = $template_id;
		}

		return $template_ids;
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.3.0
	 * @since 1.3.3 Fixed aria labelledby attribute for toggle title.
	 * @since 1.5.1 Fixed display of widget template, page and section.
	 * @since 1.7.5 Added custom data id attribute for anchor tag.
	 * @since 1.11.1 Optimized loading CSS in templates.
	 * @since 1.19.2 Fixed accessibility for toggle title.
	 */
	public function render() {
		$this->add_render_attribute( '_wrapper', array(
			'data-separate-animation-selector' => $this->get_separate_animation_selector(),
			'data-text-animation-class' => $this->get_text_animation_class(),
		) );

		$template_ids = $this->get_template_ids();

		if ( ! empty( $template_ids ) && 'enable' !== $this->lazyload_widget_get_status() ) {
			/** @var Addon $addon */
			$addon = CmsmastersPlugin::instance();

			$addon->frontend->print_template_css( $template_ids, $this->get_id() );
		}

		$settings = $this->get_settings_for_display();

		$id_int = substr( $this->get_id_int(), 0, 3 );

		echo '<div class="' . $this->get_widget_class() . '__list">';

		foreach ( $settings['toggles'] as $index => $item ) {
			$tab_count = $index + 1;

			$toggle_title_setting_key = $this->get_repeater_setting_key( 'toggle_title', 'toggles', $index );

			$toggle_content_setting_key = $this->get_repeater_setting_key( 'toggle_content', 'toggles', $index );

			$this->add_render_attribute( $toggle_title_setting_key, array(
				'id' => 'elementor-tab-title-' . $id_int . $tab_count,
				'class' => array( $this->get_widget_class() . '__title' ),
				'data-tab' => $tab_count,
				'aria-expanded' => 'false',
				'aria-controls' => 'elementor-widget-cmsmasters-toggles__content-' . $id_int . $tab_count,
				'tabindex' => '0',
			) );

			$this->add_render_attribute( $toggle_content_setting_key, array(
				'id' => $this->get_widget_class() . '__content-' . $id_int . $tab_count,
				'class' => array( $this->get_widget_class() . '__content', 'elementor-clearfix' ),
				'data-tab' => $tab_count,
			) );

			$this->add_inline_editing_attributes( $toggle_content_setting_key, 'advanced' );

			$custom_id = '';
			$toggle_custom_id = ( isset( $item['toggle_custom_id'] ) ? $item['toggle_custom_id'] : '' );

			if ( '' !== $toggle_custom_id ) {
				if ( '#' !== substr( $toggle_custom_id, 0, 1 ) ) {
					$toggle_custom_id = '#' . $toggle_custom_id;
				}

				$custom_id = ' toggle_custom_id="' . $toggle_custom_id . '"';
			}

			echo '<div class="' . $this->get_widget_class() . '__item"' . esc_attr( $custom_id ) . '>';
				echo '<' . Utils::validate_html_tag( $settings['title_html_tag'] ) . ' ' . $this->get_render_attribute_string( $toggle_title_setting_key ) . '>';

					$enable_trigger_icon = ( isset( $settings['trigger_icon'] ) && ! empty( $settings['trigger_icon']['value'] ) );

					echo '<a class="' .
						$this->get_widget_class() . '__title-link' .
						( $enable_trigger_icon ? ' cmsmasters_enable_trigger_icon" ' : '" ' ) .
						'href="#" ' .
						'tabindex="-1"' .
					'>';

						$this->get_item_icon( $item );

						echo '<span class="' . $this->get_widget_class() . '__title-text">' .
							esc_html( $item['toggle_title'] ) .
						'</span>';
					echo '</a>';

					$this->get_trigger_icon();

				echo '</' . Utils::validate_html_tag( $settings['title_html_tag'] ) . '>';

				echo '<div ' . $this->get_render_attribute_string( $toggle_content_setting_key ) . '>';

			switch ( $item['content_type'] ) {
				case 'section':
				case 'template':
				case 'widget':
					$template_id = $item[ "saved_{$item['content_type']}" ];

					echo $this->get_widget_template( $template_id, esc_html( $item['content_type'] ) );

					break;
				case 'toggle-content':
					echo $this->parse_text_editor( $item['toggle_content'] );

					break;
			}

				echo '</div>' .
			'</div>';
		}

		if ( isset( $settings['faq_schema'] ) && 'yes' === $settings['faq_schema'] ) {
			$json = array(
				'@context' => 'https://schema.org',
				'@type' => 'FAQPage',
				'mainEntity' => array(),
			);

			foreach ( $settings['toggles'] as $index => $item ) {
				$json['mainEntity'][] = array(
					'@type' => 'Question',
					'name' => wp_strip_all_tags( $item['toggle_title'] ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text' => $this->parse_text_editor( $item['toggle_content'] ),
					),
				);
			}
			?> <script type="application/ld+json"><?php echo wp_json_encode( $json ); ?></script> <?php
		}

		echo '</div>';
	}

	/**
	 * Get to item icon of some tab.
	 *
	 * @since 1.3.0
	 */
	public function get_item_icon( $item ) {
		$icon = '';

		if ( isset( $item['item_icon'] ) && ! empty( $item['item_icon']['value'] ) ) {
			$icon = $item['item_icon'];
		}

		if ( empty( $icon ) ) {
			return;
		}

		echo '<span class="' . $this->get_widget_class() . '__item-icon">';
			Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
		echo '</span>';
	}

	/**
	 * Get to trigger icon of some tab.
	 *
	 * @since 1.3.0
	 */
	public function get_trigger_icon() {
		$settings = $this->get_settings_for_display();

		$enable_active_icon = false;

		if ( isset( $settings['trigger_icon'] ) && ! empty( $settings['trigger_icon']['value'] ) ) {
			echo '<span class="' . $this->get_widget_class() . '__trigger">';

				echo '<span class="' . $this->get_widget_class() . '__trigger-closed">';

					Icons_Manager::render_icon(
						$settings['trigger_icon'],
						array(
							'aria-hidden' => 'true',
							'aria-label' => 'Closed',
						)
					);

				echo '</span>';

			if ( isset( $settings['trigger_active_icon'] ) && ! empty( $settings['trigger_active_icon']['value'] ) ) {
				$enable_active_icon = true;
			}

				$trigger_active_icon = ( $enable_active_icon ? $settings['trigger_active_icon'] : $settings['trigger_icon'] );

				echo '<span class="' . $this->get_widget_class() . '__trigger-opened">';

					Icons_Manager::render_icon(
						$trigger_active_icon,
						array(
							'aria-hidden' => 'true',
							'aria-label' => 'Opened',
						)
					);

				echo '</span>';

			echo '</span>';
		}
	}

	/**
	 * Adds template to content of some tab.
	 *
	 * Adds template from various choices, that available from select control.
	 *
	 * @since 1.3.0
	 * @since 1.11.1 Optimized loading CSS in templates.
	 * @since 1.12.1 Add checking template.
	 */
	public function get_widget_template( $template_id, $type ) {
		if ( ! CmsmastersUtils::check_template( $template_id ) ) {
			if ( is_admin() ) {
				if ( 'section' === $type ) {
					$message = __( 'Please choose your saved section or container template!', 'cmsmasters-elementor' );
				} elseif ( 'template' === $type ) {
					$message = __( 'Please choose your saved page template!', 'cmsmasters-elementor' );
				} else {
					$message = __( 'Please choose your saved global widget!', 'cmsmasters-elementor' );
				}

				CmsmastersUtils::render_alert( esc_html( $message ) );
			}

			return;
		}

		/** @var Addon $addon */
		$addon = CmsmastersPlugin::instance();

		return $addon->frontend->get_widget_template( $template_id );
	}

	/**
	 * Get Saved Widgets
	 *
	 * @param string $type Type.
	 *
	 * @since 1.3.0
	 *
	 * @return string
	 */
	public function get_page_template_options( $type = '' ) {
		$page_templates = $this->get_page_templates( $type );

		if ( count( $page_templates ) ) {
			foreach ( $page_templates as $id => $name ) {
				$options[ $id ] = $name;
			}
		} else {
			$options = '';
		}

		return $options;
	}

	/**
	 * Get page template to content of some tab.
	 *
	 * @since 1.3.0
	 */
	public function get_page_templates( $type = '' ) {
		$args = array(
			'post_type' => 'elementor_library',
			'posts_per_page' => -1,
		);

		if ( $type ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'elementor_library_type',
					'field' => 'slug',
					'terms' => $type,
				),
			);
		}

		$page_templates = get_posts( $args );

		$options = array();

		if ( ! empty( $page_templates ) && ! is_wp_error( $page_templates ) ) {
			foreach ( $page_templates as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
		}

		return $options;
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
			'toggles' => array(
				array(
					'field' => 'toggle_title',
					'type' => esc_html__( 'Toggle Title', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'toggle_content',
					'type' => esc_html__( 'Toggle Contetnt', 'cmsmasters-elementor' ),
					'editor_type' => 'VISUAL',
				),
			),
		);
	}
}
