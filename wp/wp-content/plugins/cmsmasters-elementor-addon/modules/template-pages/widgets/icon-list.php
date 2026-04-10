<?php
namespace CmsmastersElementor\Modules\TemplatePages\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as AddonControlsManager;
use CmsmastersElementor\Plugin as CmsmastersPlugin;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon icon list widget.
 *
 * Addon widget that displays a bullet list with any chosen icons and texts.
 *
 * @since 1.2.0
 */
class Icon_List extends Base_Widget {

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.2.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Icon List', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.2.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-bullet-list';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.2.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'icon list',
			'icon',
			'list',
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
			'widget-cmsmasters-icon-list',
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
		return '.cmsmasters-widget-icon-list-item';
	}

	/**
	 * Get the class name ot the block for the text animation types 'Line by Line', 'Word by Word', 'Char by Char', etc.
	 * and names of animation types suitable for this widget.
	 *
	 * @since 1.18.0
	 */
	public function get_text_animation_class() {
		return array( 'cmsmasters-widget-icon-list-item' => 'sequental, random, line, word, char' );
	}

	/**
	 * Get HTML wrapper class.
	 *
	 * Retrieve the widget container class.
	 *
	 * Can be used to override the container class for specific widgets.
	 *
	 * @since 1.2.0
	 *
	 * @return string Widget container class.
	 */
	protected function get_html_wrapper_class() {
		$parent_classes = explode( ' ', parent::get_html_wrapper_class() );
		$widget_class = 'cmsmasters-widget-icon-list';

		if ( ! in_array( $widget_class, $parent_classes, true ) ) {
			$parent_classes[] = $widget_class;
		}

		return implode( ' ', $parent_classes );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.2.0
	 */
	protected function register_controls() {
		$this->register_list_content_section();

		$this->register_list_style_section();

		$this->register_item_style_section();

		$this->register_value_style_section();

		$this->register_icon_style_section();

		$this->register_title_style_section();
	}

	/**
	 * Register widget list content section.
	 *
	 * Adds icon list widget `list content` settings section controls.
	 *
	 * @since 1.2.0
	 * @since 1.3.3 Added "Position" control for value item.
	 * @since 1.18.0 Added `Entrance Animation` and `Entrance Animation Text` hidden control for entrance animation.
	 * @since 1.22.0 Added `Starting Number` control for marker type numeric.
	 */
	protected function register_list_content_section() {
		$this->start_controls_section(
			'section_list',
			array( 'label' => __( 'Icon List', 'cmsmasters-elementor' ) )
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
				'default' => 'yes',
			)
		);

		$this->add_control(
			'data_type',
			array(
				'label' => __( 'Data Type', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => AddonControlsManager::CHOOSE_TEXT,
				'options' => array(
					'static' => __( 'Static', 'cmsmasters-elementor' ),
					'dynamic' => __( 'Dynamic', 'cmsmasters-elementor' ),
				),
				'default' => 'static',
			)
		);

		$this->register_list_static_content();

		$this->register_list_dynamic_content();

		$this->add_control(
			'items_heading',
			array(
				'label' => __( 'Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'item_layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => AddonControlsManager::CHOOSE_TEXT,
				'options' => array(
					'row' => __( 'Row', 'cmsmasters-elementor' ),
					'column' => __( 'Column', 'cmsmasters-elementor' ),
				),
				'default' => 'row',
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-widget-layout-',
			)
		);

		$this->add_responsive_control(
			'items_align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
					'stretch' => array(
						'title' => __( 'Stretch', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-stretch',
					),
				),
				'default' => 'stretch',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-widget%s-align-',
				'condition' => array( 'item_layout' => 'row' ),
			)
		);

		$this->add_responsive_control(
			'items_align_column',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'left',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-widget%s-align-column-',
				'condition' => array( 'item_layout' => 'column' ),
			)
		);

		$this->add_control(
			'item_direction',
			array(
				'label' => __( 'Direction', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => AddonControlsManager::CHOOSE_TEXT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'reverse' => __( 'Reverse', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'prefix_class' => 'cmsmasters-widget-direction-',
			)
		);

		$this->add_control(
			'value_heading',
			array(
				'label' => __( 'Value', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'item_layout' => 'column' ),
			)
		);

		$this->add_control(
			'value_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => AddonControlsManager::CHOOSE_TEXT,
				'options' => array(
					'bottom' => __( 'Bottom', 'cmsmasters-elementor' ),
					'inline' => __( 'Inline', 'cmsmasters-elementor' ),
				),
				'default' => 'bottom',
				'label_block' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-value-position-',
				'condition' => array( 'item_layout' => 'column' ),
			)
		);

		$this->add_control(
			'marker_heading',
			array(
				'label' => __( 'Marker', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'global_marker',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => AddonControlsManager::CHOOSE_TEXT,
				'options' => array(
					'icon' => __( 'Icon', 'cmsmasters-elementor' ),
					'numeric' => __( 'Numeric', 'cmsmasters-elementor' ),
				),
				'default' => 'icon',
				'prefix_class' => 'cmsmasters-widget-marker-element-',
			)
		);

		$this->add_control(
			'global_marker_starting_number',
			array(
				'label' => __( 'Starting Number', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-starting-number: {{VALUE}}',
				),
				'condition' => array( 'global_marker' => 'numeric' ),
			)
		);

		$this->add_control(
			'global_icon',
			array(
				'label' => __( 'Global Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-check',
					'library' => 'fa-solid',
				),
				'skin' => 'inline',
				'condition' => array( 'global_marker' => 'icon' ),
			)
		);

		$this->add_control(
			'marker_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => AddonControlsManager::CHOOSE_TEXT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'stacked' => __( 'Stacked', 'cmsmasters-elementor' ),
					'framed' => __( 'Framed', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'prefix_class' => 'cmsmasters-widget-marker-view-',
			)
		);

		$this->add_control(
			'marker_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => AddonControlsManager::CHOOSE_TEXT,
				'options' => array(
					'circle' => __( 'Circle', 'cmsmasters-elementor' ),
					'square' => __( 'Square', 'cmsmasters-elementor' ),
				),
				'default' => 'circle',
				'condition' => array( 'marker_view!' => 'default' ),
				'prefix_class' => 'cmsmasters-widget-marker-shape-',
			)
		);

		$this->add_control(
			'link_click',
			array(
				'label' => __( 'Apply Link To:', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => AddonControlsManager::CHOOSE_TEXT,
				'options' => array(
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => __( 'Apply link to item text only', 'cmsmasters-elementor' ),
					),
					'value' => array(
						'title' => __( 'Value', 'cmsmasters-elementor' ),
						'description' => __( 'Apply link to item value only', 'cmsmasters-elementor' ),
					),
					'full_width' => array(
						'title' => __( 'Full Width', 'cmsmasters-elementor' ),
						'description' => __( 'Apply link to all item elements', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'text',
				'render_type' => 'template',
				'separator' => 'before',
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

		$this->add_control(
			'title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => false,
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'List Title', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label' => __( 'HTML Tag', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				),
				'default' => 'h3',
				'condition' => array( 'title!' => '' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget list static content section.
	 *
	 * Adds icon list widget static content settings controls.
	 *
	 * @since 1.2.0
	 */
	protected function register_list_static_content() {
		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'List Item', 'cmsmasters-elementor' ),
				'default' => __( 'List Item', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'value',
			array(
				'label' => __( 'Value', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Item Value', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'icon_type',
			array(
				'label' => __( 'Icon Type', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => AddonControlsManager::CHOOSE_TEXT,
				'options' => array(
					'global' => __( 'Global', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'global',
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label' => __( 'Custom Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'condition' => array( 'icon_type' => 'custom' ),
			)
		);

		$repeater->add_control(
			'text_nowrap',
			array(
				'label' => __( 'Prevent Text Wrapping', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors_dictionary' => array(
					'yes' => 'nowrap',
					'' => 'normal;',
				),
				'default' => '',
				'render_type' => 'ui',
				'description' => __( 'Display text in a single line without wrapping.', 'cmsmasters-elementor' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-icon-list-item-text-inner{{CURRENT_ITEM}}' => '--cmsmasters-text-nowrap: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'icon_list',
			array(
				'label' => __( 'Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => array(
					array(
						'text' => __( 'List Item #1', 'cmsmasters-elementor' ),
					),
					array(
						'text' => __( 'List Item #2', 'cmsmasters-elementor' ),
					),
					array(
						'text' => __( 'List Item #3', 'cmsmasters-elementor' ),
					),
				),
				'title_field' => '<span class="cmsmasters-repeat-item-num"></span>. {{{ text }}} {{{ elementor.helpers.renderIcon( this, icon, {}, "i", "panel" ) }}}',
				'condition' => array( 'data_type' => 'static' ),
			)
		);
	}

	/**
	 * Register widget list dynamic content section.
	 *
	 * Adds icon list widget dynamic content settings controls.
	 *
	 * @since 1.2.0
	 */
	protected function register_list_dynamic_content() {
		$this->add_control(
			'dynamic_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
					'default' => CmsmastersPlugin::elementor()->dynamic_tags->tag_data_to_tag_text( null, 'cmsmasters-acf-repeater-text' ),
				),
				'condition' => array( 'data_type' => 'dynamic' ),
			)
		);

		$this->add_control(
			'dynamic_value',
			array(
				'label' => __( 'Value', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
					'default' => CmsmastersPlugin::elementor()->dynamic_tags->tag_data_to_tag_text( null, 'cmsmasters-acf-repeater-text' ),
				),
				'condition' => array( 'data_type' => 'dynamic' ),
			)
		);

		$this->add_control(
			'dynamic_link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
					'default' => CmsmastersPlugin::elementor()->dynamic_tags->tag_data_to_tag_text( null, 'cmsmasters-acf-repeater-url' ),
				),
				'condition' => array( 'data_type' => 'dynamic' ),
			)
		);
	}

	/**
	 * Register widget list style section.
	 *
	 * Adds icon list widget `list style` settings section controls.
	 *
	 * @since 1.2.0
	 */
	protected function register_list_style_section() {
		$this->start_controls_section(
			'section_list_style',
			array(
				'label' => __( 'List', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'space_between',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array( 'max' => 50 ),
					'em' => array( 'max' => 5 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-items-gap: calc({{SIZE}}{{UNIT}}/2)',
				),
			)
		);

		$this->add_control(
			'columns_heading',
			array(
				'label' => __( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label' => __( 'Count', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 6,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-columns-count: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'columns_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range' => array(
					'px' => array( 'max' => 100 ),
					'em' => array( 'max' => 5 ),
					'%' => array( 'max' => 30 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-columns-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'columns!' => '' ),
			)
		);

		$this->add_control(
			'columns_rule_style',
			array(
				'label' => __( 'Separator Style', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-columns-rule-style: {{VALUE}}',
				),
				'condition' => array( 'columns!' => '' ),
			)
		);

		$this->add_responsive_control(
			'columns_rule_weight',
			array(
				'label' => __( 'Separator Weight', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-columns-rule-weight: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'columns!' => '',
					'columns_rule_style!' => '',
				),
			)
		);

		$this->add_control(
			'columns_rule_color',
			array(
				'label' => __( 'Separator Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-columns-rule-color: {{VALUE}}',
				),
				'condition' => array(
					'columns!' => '',
					'columns_rule_style!' => '',
				),
			)
		);

		$this->add_control(
			'divider',
			array(
				'label' => __( 'Divider', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-icon-list-item:not(:last-child):after' => 'content: ""',
				),
			)
		);

		$this->add_control(
			'divider_style',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
				),
				'default' => 'solid',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-items-divider-style: {{VALUE}}',
				),
				'condition' => array( 'divider' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'divider_weight',
			array(
				'label' => __( 'Weight', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-items-divider-weight: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'divider' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'divider_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'default' => array( 'unit' => '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-items-divider-width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'divider' => 'yes' ),
			)
		);

		$this->add_control(
			'divider_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-items-divider-color: {{VALUE}}',
				),
				'condition' => array( 'divider' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget item style section.
	 *
	 * Adds icon list widget `item style` settings section controls.
	 *
	 * @since 1.2.0
	 * @since 1.5.1 Fixed text shadow in list item.
	 */
	protected function register_item_style_section() {
		$this->start_controls_section(
			'section_item_style',
			array(
				'label' => __( 'Item', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'item_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-icon-list-item, {{WRAPPER}} .cmsmasters-widget-icon-list-item > a',
			)
		);

		$this->start_controls_tabs( 'item_colors' );

		$this->start_controls_tab(
			'item_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'item_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_link_color',
			array(
				'label' => __( 'Link Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-link-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'item_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'item_hover_color',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_link_hover_color',
			array(
				'label' => __( 'Link Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-link-hover-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'text_indent',
			array(
				'label' => __( 'Indent', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array( 'max' => 50 ),
					'em' => array( 'max' => 5 ),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-text-indent: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-icon-list-item-text',
			)
		);

		$this->add_control(
			'text_vertical_align',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-vertical-align: {{VALUE}};',
				),
				'condition' => array( 'item_layout' => 'row' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget value style section.
	 *
	 * Adds icon list widget `value style` settings section controls.
	 *
	 * @since 1.2.0
	 */
	protected function register_value_style_section() {
		$this->start_controls_section(
			'section_value_style',
			array(
				'label' => __( 'Value', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'value_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-icon-list-item-value, {{WRAPPER}} .cmsmasters-widget-icon-list-item-value > a',
			)
		);

		$this->start_controls_tabs( 'value_colors' );

		$this->start_controls_tab(
			'value_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'value_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-value-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'value_link_color',
			array(
				'label' => __( 'Link Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-value-link-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'value_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'value_hover_color',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-value-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'value_link_hover_color',
			array(
				'label' => __( 'Link Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-value-link-hover-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'value_indent',
			array(
				'label' => __( 'Indent', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array( 'max' => 200 ),
					'em' => array( 'max' => 10 ),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-value-indent: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'item_layout',
							'operator' => '=',
							'value' => 'row',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'item_layout',
									'operator' => '=',
									'value' => 'column',
								),
								array(
									'name' => 'value_position',
									'operator' => '!==',
									'value' => 'inline',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'value_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array( 'max' => 50 ),
					'em' => array( 'max' => 5 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-value-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'item_layout' => 'column' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget icon style section.
	 *
	 * Adds icon list widget `icon style` settings section controls.
	 *
	 * @since 1.2.0
	 */
	protected function register_icon_style_section() {
		$this->start_controls_section(
			'section_icon_style',
			array(
				'label' => __( 'Marker', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'number_type',
			array(
				'label' => __( 'Number Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'decimal' => __( 'Decimal', 'cmsmasters-elementor' ),
					'decimal-leading-zero' => __( 'Decimal Leading Zero', 'cmsmasters-elementor' ),
					'upper-latin' => __( 'Uppercase Latin', 'cmsmasters-elementor' ),
					'lower-latin' => __( 'Lowercase Latin', 'cmsmasters-elementor' ),
					'upper-roman' => __( 'Uppercase Roman', 'cmsmasters-elementor' ),
					'lower-roman' => __( 'Lowercase Roman', 'cmsmasters-elementor' ),
					'lower-greek' => __( 'Greek', 'cmsmasters-elementor' ),
				),
				'default' => 'decimal',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-counter-type: {{VALUE}};',
				),
				'condition' => array( 'global_marker' => 'numeric' ),
			)
		);

		$this->add_control(
			'number_prefix',
			array(
				'label' => __( 'Number Prefix', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-counter-prefix: \'{{VALUE}}\';',
				),
				'condition' => array( 'global_marker' => 'numeric' ),
			)
		);

		$this->add_control(
			'number_suffix',
			array(
				'label' => __( 'Number Suffix', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-counter-suffix: \'{{VALUE}}\';',
				),
				'condition' => array( 'global_marker' => 'numeric' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'number_typography',
				'exclude' => array( 'line_height' ),
				'selector' => '{{WRAPPER}} .cmsmasters-widget-icon-list-item-icon > span:before',
				'condition' => array( 'global_marker' => 'numeric' ),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array( 'min' => 7 ),
					'em' => array( 'min' => 0.5 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-icon-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'global_marker' => 'icon' ),
			)
		);

		$this->add_control(
			'icon_vertical_align',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-icon-vertical-align: {{VALUE}};',
				),
			)
		);

		$this->start_controls_tabs( 'icon_colors' );

		$this->start_controls_tab(
			'icon_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'icon_color',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-icon-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_secondary_color',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-icon-secondary-color: {{VALUE}};',
				),
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'icon_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-icon-list-item .cmsmasters-widget-icon-list-item-icon > span',
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_control(
			'icon_rotate',
			array(
				'label' => __( 'Rotate', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default' => array( 'unit' => 'deg' ),
				'range' => array(
					'deg' => array(
						'min' => 0,
						'max' => 360,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-icon-rotate: rotate({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'icon_hover_color',
			array(
				'label' => __( 'Primary Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-icon-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_secondary_color',
			array(
				'label' => __( 'Secondary Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-icon-hover-secondary-color: {{VALUE}};',
				),
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'icon_hover_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-icon-list-item:hover .cmsmasters-widget-icon-list-item-icon > span',
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_control(
			'icon_rotate_hover',
			array(
				'label' => __( 'Rotate', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default' => array( 'unit' => 'deg' ),
				'range' => array(
					'deg' => array(
						'min' => 0,
						'max' => 360,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-icon-rotate-hover: rotate({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'icon_wrapper_size',
			array(
				'label' => __( 'Wrapper Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 20,
						'max' => 200,
					),
					'em' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-icon-wrapper: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'icon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 0,
						'max' => 3,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-icon-padding: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'icon_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
					'em' => array(
						'min' => 0,
						'max' => 3,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-icon-border-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'marker_view' => 'framed' ),
			)
		);

		$this->add_responsive_control(
			'icon_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-icon-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'icon_self_align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-item-icon-alignment: {{VALUE}};',
				),
				'condition' => array( 'global_marker' => 'icon' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget title style section.
	 *
	 * Adds icon list widget `title style` settings section controls.
	 *
	 * @since 1.2.0
	 * @since 1.3.0 Added `Alignment` control for title.
	 */
	protected function register_title_style_section() {
		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'title!' => '' ),
			)
		);

		$this->add_responsive_control(
			'title_align',
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
					'{{WRAPPER}} .cmsmasters-widget-icon-list-title' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-icon-list-title',
			)
		);

		$this->start_controls_tabs( 'title_colors' );

		$this->start_controls_tab(
			'title_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-title-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'title_hover_color',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-title-hover-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-icon-list-title',
			)
		);

		$this->add_responsive_control(
			'title_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range' => array(
					'px' => array( 'max' => 200 ),
					'em' => array( 'max' => 10 ),
					'%' => array( 'max' => 100 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-list-title-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.2.0
	 * @since 1.14.1 Fixed applying a reference to a value if that value is empty.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( '_wrapper', array(
			'data-separate-animation-selector' => $this->get_separate_animation_selector(),
			'data-text-animation-class' => $this->get_text_animation_class(),
		) );

		$base_class = 'cmsmasters-widget-icon-list';
		$item_class = "{$base_class}-item";

		$this->add_render_attribute( 'icon_list', 'class', "{$base_class}-items" );
		$this->add_render_attribute( 'list_item_text_wrap', 'class', "{$item_class}-text-wrap" );

		if ( ! empty( $settings['title'] ) ) {
			$this->add_render_attribute( 'list_title', 'class', "{$base_class}-title" );

			$tag = $settings['title_tag'];

			echo '<' . Utils::validate_html_tag( $tag ) . ' ' . $this->get_render_attribute_string( 'list_title' ) . '>' .
				esc_html( $settings['title'] ) .
			'</' . Utils::validate_html_tag( $tag ) . '>';
		}

		$list_data = $this->get_list_data( $settings );

		$link_type = $settings['link_click'];
		$is_dynamic = 'dynamic' === $settings['data_type'];
		?>
		<ul <?php echo $this->get_render_attribute_string( 'icon_list' ); ?>>
			<?php
			foreach ( $list_data as $index => $item ) {
				$repeater_item_setting_key = $this->get_repeater_setting_key( 'item', 'icon_list', $index );
			
				$this->add_render_attribute( $repeater_item_setting_key, 'class', $item_class );

				$repeater_text_setting_key = $this->get_repeater_setting_key( 'text', 'icon_list', $index );

				$this->add_render_attribute( $repeater_text_setting_key, 'class', array(
					"{$item_class}-text-inner",
					"elementor-repeater-item-{$item['_id']}",
				) );

				$this->add_render_attribute( 'text', 'class', "{$item_class}-text" );

				if ( ! $is_dynamic ) {
					$this->add_inline_editing_attributes( $repeater_text_setting_key );
				}

				list( $has_icon, $icon ) = $this->get_list_item_icon( $item );

				if ( $has_icon ) {
					$repeater_icon_setting_key = $this->get_repeater_setting_key( 'icon', 'icon_list', $index );

					$this->add_render_attribute( $repeater_icon_setting_key, 'class', "{$item_class}-icon" );
					$this->add_render_attribute( $repeater_item_setting_key, 'class', 'active-icon-item' );
				}

				if ( ! empty( $item['value'] ) ) {
					$repeater_value_setting_key = $this->get_repeater_setting_key( 'value', 'icon_list', $index );

					$this->add_render_attribute( $repeater_value_setting_key, 'class', "{$item_class}-value" );

					if ( ! $is_dynamic ) {
						$this->add_inline_editing_attributes( $repeater_value_setting_key );
					}
				}

				$has_item_link = ! empty( $item['link']['url'] );

				if ( $has_item_link ) {
					$link_key = "link_{$index}";

					$this->add_link_attributes( $link_key, $item['link'] );

					switch ( $link_type ) {
						case 'full_width':
							$this->add_render_attribute( $repeater_item_setting_key, 'class', 'active-link-item' );

							break;
						case 'text':
							if ( ! empty( $item['text'] ) ) {
								$this->add_render_attribute( $repeater_text_setting_key, 'class', 'active-link-item' );
							}

							break;
						case 'value':
							if ( ! empty( $item['value'] ) ) {
								$this->add_render_attribute( $repeater_value_setting_key, 'class', 'active-link-item' );
							}

							break;
					}
				}
				?>
				<li <?php echo $this->get_render_attribute_string( $repeater_item_setting_key ); ?>>
				<?php
				if ( $has_item_link && 'full_width' === $link_type ) {
					echo '<a ' . $this->get_render_attribute_string( $link_key ) . '>';
				}
				?>
				<span <?php echo $this->get_render_attribute_string( 'list_item_text_wrap' ); ?>>
					<?php if ( $has_icon ) { ?>
						<span <?php echo $this->get_render_attribute_string( $repeater_icon_setting_key ); ?>>
							<span>
								<?php if ( $icon ) { ?>
									<?php Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) ); ?>
								<?php } ?>
							</span>
						</span>
					<?php } ?>
					<span <?php echo $this->get_render_attribute_string( $repeater_text_setting_key ); ?>>
						<span <?php echo $this->get_render_attribute_string( 'text' ); ?>>
							<?php
							if ( $has_item_link && 'text' === $link_type ) {
								echo '<a ' . $this->get_render_attribute_string( $link_key ) . '>' .
									wp_kses_post( $item['text'] ) .
								'</a>';
							} else {
								echo wp_kses_post( $item['text'] );
							}
							?>
						</span>
						<?php
						if ( 'column' === $settings['item_layout'] && 'inline' === $settings['value_position'] ) {
							$this->get_item_value( $item, $index );
						}
						?>
					</span>
				</span>
				<?php
				if ( 'row' === $settings['item_layout'] || 'inline' !== $settings['value_position'] ) {
					$this->get_item_value( $item, $index );
				}

				if ( $has_item_link && 'full_width' === $link_type ) {
					echo '</a>';
				}
				?>
				</li>
			<?php } ?>
		</ul>
		<?php
	}

	/**
	 * Get item value.
	 *
	 * Retrieves icon item value.
	 *
	 * @since 1.3.3
	 */
	protected function get_item_value( $item, $index ) {
		$settings = $this->get_settings_for_display();

		if ( ! empty( $item['value'] ) ) {
			$repeater_value_setting_key = $this->get_repeater_setting_key( 'value', 'icon_list', $index );

			$this->add_render_attribute( $repeater_value_setting_key, 'class', "cmsmasters-widget-icon-list-item-value" );

			if ( ! 'dynamic' === $settings['data_type'] ) {
				$this->add_inline_editing_attributes( $repeater_value_setting_key );
			}
		}

		if ( ! empty( $item['value'] ) ) {
			echo '<span ' . $this->get_render_attribute_string( $repeater_value_setting_key ) . '>';

			$has_item_link = ! empty( $item['link']['url'] );
			$link_type = $settings['link_click'];
			$link_key = "link_{$index}";

			if ( $has_item_link && 'value' === $link_type ) {
				echo '<a ' . $this->get_render_attribute_string( $link_key ) . '>' .
					wp_kses_post( $item['value'] ) .
				'</a>';
			} else {
				echo wp_kses_post( $item['value'] );
			}

			echo '</span>';
		}
	}

	/**
	 * Get list data.
	 *
	 * Retrieves icon list data.
	 *
	 * @since 1.2.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array Widget list data array.
	 */
	protected function get_list_data( $settings ) {
		$is_dynamic = 'dynamic' === $settings['data_type'];

		if ( $is_dynamic ) {
			$dynamic_links_params = $settings['dynamic_link'];
			$dynamic_links_urls = array_shift( $dynamic_links_params );

			$list_data = array_map(
				function ( $text, $value, $link ) use ( $dynamic_links_params ) {
					return array(
						'text' => $text,
						'value' => $value,
						'link' => array_merge( array( 'url' => $link ), $dynamic_links_params ),
					);
				},
				json_decode( $settings['dynamic_text'] ),
				json_decode( $settings['dynamic_value'] ),
				json_decode( $dynamic_links_urls )
			);
		} else {
			$list_data = $settings['icon_list'];
		}

		return $list_data;
	}

	/**
	 * Get list item icon.
	 *
	 * Retrieves list item icon.
	 *
	 * @since 1.2.0
	 *
	 * @param array $item Widget list item.
	 *
	 * @return array Widget list item icon array.
	 */
	protected function get_list_item_icon( $item ) {
		$settings = $this->get_settings_for_display();

		if ( 'numeric' === $settings['global_marker'] ) {
			return array( true, false );
		}

		if ( isset( $item['icon_type'] ) && 'custom' === $item['icon_type'] ) {
			$icon = ( isset( $item['icon'] ) && ! empty( $item['icon']['value'] ) ) ? $item['icon'] : array();
		} else {
			$icon = $settings['global_icon'];
		}

		$has_icon = isset( $icon['value'] ) && ! empty( $icon['value'] );

		return array( $has_icon, $icon );
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
				'field' => 'title',
				'type' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'dynamic_text',
				'type' => esc_html__( 'Dynamic Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'dynamic_value',
				'type' => esc_html__( 'Dynamic Value', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			'dynamic_link' => array(
				'field' => 'url',
				'type' => esc_html__( 'Dynamic Link', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'number_prefix',
				'type' => esc_html__( 'Number Prefix', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'number_suffix',
				'type' => esc_html__( 'Number Suffix', 'cmsmasters-elementor' ),
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
			'icon_list' => array(
				array(
					'field' => 'text',
					'type' => esc_html__( 'Text', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'value',
					'type' => esc_html__( 'Value', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				'link' => array(
					'field' => 'url',
					'type' => esc_html__( 'Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
			),
		);
	}
}
