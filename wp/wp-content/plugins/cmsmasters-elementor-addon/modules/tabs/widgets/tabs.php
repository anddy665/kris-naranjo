<?php
namespace CmsmastersElementor\Modules\Tabs\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Animation\Classes\Animation as AnimationModule;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Traits\Extendable_Widget;
use CmsmastersElementor\Plugin as CmsmastersPlugin;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Tabs extends Base_Widget {

	use Extendable_Widget;

	/**
	 * Get extendable widget class.
	 *
	 * Retrieve the extendable widget container class.
	 *
	 * @since 1.0.0
	 *
	 * @return string Extendable widget container class.
	 */
	public function get_extendable_widget_class() {
		return 'cmsmasters-widget-tabs';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Tabs', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-tabs';
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
		return array(
			'tabs',
			'tours',
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
			'widget-cmsmasters-tabs',
		);
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
		return '.cmsmasters-tabs-list-item, .cmsmasters-accordion-item-wrap';
	}

	/**
	 * Get the class name ot the block for the text animation types 'Line by Line', 'Word by Word', 'Char by Char', etc.
	 * and names of animation types suitable for this widget.
	 *
	 * @since 1.18.0
	 */
	public function get_text_animation_class() {
		return array( 'cmsmasters-tabs-list-item, cmsmasters-accordion-item-wrap' => 'sequental, random' );
	}

	/**
	 * Set the group for the animation items.
	 *
	 * @since 1.18.0
	 */
	public function get_group_animation() {
		return true;
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added background gradient for tab list.
	 * @since 1.3.0 Fixed error with responsive controls in elementor 3.4.0, Fixed 'width' control,
	 * fixed selectors for style controls.
	 * @since 1.3.3 Fixed tab list wrapper.
	 * @since 1.3.7 Fixed aligment & position elements in tabs.
	 * @since 1.4.0 Fixed applying background for tab link.
	 * @since 1.5.1 Fixed border radius for tabs list item.
	 * @since 1.7.5 Added `Custom ID` control for anchor tag.
	 * @since 1.11.9 Fixed display of container templates in saved sections.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 * @since 1.16.4 Added `Close Active Tab on Click` control for active tab.
	 * @since 1.18.0 Added `Entrance Animation` and `Entrance Animation Text` hidden control for entrance animation.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_tabs',
			array(
				'label' => __( 'Tabs', 'cmsmasters-elementor' ),
			)
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

		$repeater = new Repeater();

		$content_types = array(
			'tab-content' => array(
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
				'default' => 'tab-content',
				'toggle' => false,
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'tab_content',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'default' => __( 'Tab Content', 'cmsmasters-elementor' ),
				'placeholder' => __( 'Tab Content', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::WYSIWYG,
				'show_label' => false,
				'condition' => array( 'content_type' => 'tab-content' ),
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
					'raw' => sprintf( __( '<strong>There are no saved sections in your site.</strong><br>Go to the <a href="%s" target="_blank">Saved Section</a> to create one.', 'cmsmasters-elementor' ), admin_url( 'edit.php?post_type=elementor_library&tabs_group=library&elementor_library_type=section' ) ),
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
			'tab_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Tab', 'cmsmasters-elementor' ),
				'placeholder' => __( 'Tab Title', 'cmsmasters-elementor' ),
				'label_block' => true,
				'separator' => 'before',
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'tab_subtitle',
			array(
				'label' => __( 'Subtitle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Tab Subtitle', 'cmsmasters-elementor' ),
				'label_block' => true,
				'dynamic' => array(
					'active' => true,
				),
				'condition' => array( 'tab_title!' => '' ),
			)
		);

		$repeater->add_control(
			'tab_icon',
			array(
				'label' => __( 'Tab Icon', 'cmsmasters-elementor' ),
				'description' => __( 'Isn`t applied to accordion', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
			)
		);

		$repeater->add_control(
			'tab_custom_id',
			array(
				'label' => __( 'Custom ID', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Tab Custom ID', 'cmsmasters-elementor' ),
				'description' => __( 'Custom ID will be added as an anchor tag. For example, if you add ‘test’ as your custom ID, the link will become like the following: https://www.example.com/#test and it will open the respective tab directly.', 'cmsmasters-elementor' ),
				'label_block' => true,
				'frontend_available' => true,
				'separator' => 'before',
				'condition' => array( 'tab_title!' => '' ),
			)
		);

		$this->add_control(
			'tabs',
			array(
				'label' => __( 'Tabs Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'frontend_available' => true,
				'default' => array(
					array(
						'tab_title' => __( 'Tab #01', 'cmsmasters-elementor' ),
						'tab_content' => __( 'This is tab #01. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'cmsmasters-elementor' ),
					),
					array(
						'tab_title' => __( 'Tab #02', 'cmsmasters-elementor' ),
						'tab_content' => __( 'This is tab #02. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'cmsmasters-elementor' ),
					),
				),
				'title_field' => '<# if ( \'Tab\' === tab_title ) { #> {{{ tab_title }}} #<span class="cmsmasters-repeat-item-num"></span> {{{ tab_subtitle }}} <# } else { #> {{{ tab_title }}} <span class="cmsmasters-repeat-item-num hidden"></span> {{{ tab_subtitle }}} <# } #>',
			)
		);

		$this->add_control(
			'tabs_type',
			array(
				'label' => __( 'List Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'horizontal' => array(
						'title' => __( 'Horizontal', 'cmsmasters-elementor' ),
					),
					'vertical' => array(
						'title' => __( 'Vertical', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'horizontal',
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-tabs-type-',
				'frontend_available' => true,
				'toggle' => false,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'tab_list_item_width',
			array(
				'label' => __( 'Vertical List Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'default' => array(
					'unit' => '%',
					'size' => 25,
				),
				'tablet_default' => array(
					'unit' => '%',
					'size' => 35,
				),
				'mobile_default' => array(
					'unit' => '%',
					'size' => 45,
				),
				'range' => array(
					'%' => array(
						'min' => 10,
						'max' => 50,
					),
					'px' => array(
						'min' => 100,
						'max' => 400,
						'step' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-tabs-type-vertical .cmsmasters-tabs-list-wrapper' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'tabs_type' => 'vertical' ),
			)
		);

		$this->add_control(
			'tab_list_mode',
			array(
				'label' => __( 'Mode', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inline' => array(
						'title' => __( 'Inline', 'cmsmasters-elementor' ),
					),
					'justify' => array(
						'title' => __( 'Justify', 'cmsmasters-elementor' ),
					),
					'column' => array(
						'title' => __( 'Column', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'inline',
				'toggle' => false,
				'selectors_dictionary' => array(
					'inline' => '',
					'justify' => 'flex-direction: row;',
					'column' => 'flex-direction: column;',
				),
				'prefix_class' => 'cmsmasters-tab-list-mode-',
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-tabs-type-horizontal .cmsmasters-tabs-list' => '{{VALUE}}',
					'{{WRAPPER}}.cmsmasters-tabs-type-horizontal.cmsmasters-tab-list-mode-justify .cmsmasters-tabs-list li' => 'flex-grow: 1; flex-basis: 0;',
				),
				'condition' => array( 'tabs_type' => 'horizontal' ),
			)
		);

		$this->add_control(
			'tab_list_item_alignment',
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
				'label_block' => false,
				'default' => 'left',
				'toggle' => false,
				'selectors_dictionary' => array(
					'center' => 'text-align: center; justify-content: center;',
					'left' => 'text-align: left; justify-content: flex-start;',
					'right' => 'text-align: right; justify-content: flex-end;',
				),
				'prefix_class' => 'cmsmasters-list-item-alignment-',
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-tabs-type-horizontal.cmsmasters-tab-list-mode-inline .cmsmasters-tabs-list,
					{{WRAPPER}} .cmsmasters-tabs-list .cmsmasters-tabs-list-item a' => '{{VALUE}};',
					'{{WRAPPER}}.cmsmasters-tabs-type-horizontal.cmsmasters-tab-list-mode-inline .cmsmasters-tabs-list a' => 'justify-content: center; text-align: center;',
					'(tablet-){{WRAPPER}}.cmsmasters-tabs-type-vertical.cmsmasters-tab-list-mode-inline[class*="cmsmasters-tabs-responsive"].cmsmasters-type-responsive-horizontal .cmsmasters-tabs-list' => '{{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_list_item_style_icon_position',
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
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
				),
				'label_block' => false,
				'default' => 'left',
				'selectors_dictionary' => array(
					'top' => 'flex-direction: column;',
					'left' => 'flex-direction: row;',
					'right' => 'flex-direction: row-reverse;',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-tabs-list .cmsmasters-tabs-list-item a .cmsmasters-tab-title__text-wrap-outer,' .
					'{{WRAPPER}} .cmsmasters-accordion-item-wrap .cmsmasters-tabs-list-item a .cmsmasters-tab-title__text-wrap-outer' => '{{VALUE}}',
				),
				'toggle' => false,
				'prefix_class' => 'cmsmasters-icon-position-',
			)
		);

		$this->add_control(
			'tab_list_item_style_icon_alignment',
			array(
				'label' => __( 'Icon Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'label_block' => false,
				'default' => 'center',
				'selectors_dictionary' => array(
					'top' => 'align-self: flex-start;',
					'center' => 'align-self: center;',
					'bottom' => 'align-self: flex-end;',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-tabs-list .cmsmasters-tabs-list-item a .cmsmasters-tab-icon,' .
					'{{WRAPPER}} .cmsmasters-accordion-item-wrap .cmsmasters-tabs-list-item a .cmsmasters-tab-title__text-wrap-outer .cmsmasters-tab-icon' => '{{VALUE}}',
				),
				'toggle' => false,
				'condition' => array( 'tab_list_item_style_icon_position!' => 'top' ),
			)
		);

		$this->add_control(
			'tab_list_position',
			array(
				'label' => __( 'List Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'label_block' => false,
				'default' => 'start',
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
					),
				),
				'toggle' => false,
				'prefix_class' => 'cmsmasters-tabs-position-',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label' => __( 'Title HTML Tag', 'cmsmasters-elementor' ),
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
				),
				'default' => 'h6',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'default_tab',
			array(
				'label' => __( 'Active Tab', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'label_block' => false,
				'frontend_available' => true,
				'default' => 1,
			)
		);

		$this->add_control(
			'closed_active_tab',
			array(
				'label' => __( 'Close Active Tab on Click', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'frontend_available' => true,
				'condition' => array( 'default_tab[size]!' => 0 ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tabs_responsive',
			array(
				'label' => __( 'Responsive', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'tabs_responsive',
			array(
				'label' => __( 'Responsive View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'' => array(
						'title' => __( 'Disabled', 'cmsmasters-elementor' ),
					),
					'tablet' => array(
						'title' => __( 'On Tablet', 'cmsmasters-elementor' ),
						'description' => __( 'Responsive starts on tablet and lower', 'cmsmasters-elementor' ),
					),
					'mobile' => array(
						'title' => __( 'On Mobile', 'cmsmasters-elementor' ),
						'description' => __( 'Responsive starts on mobile', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => true,
				'toggle' => false,
				'default' => 'mobile',
				'frontend_available' => true,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-tabs-responsive-',
				'condition' => array( 'tabs_type!' => '' ),
			)
		);

		$this->add_control(
			'tabs_responsive_type',
			array(
				'label' => __( 'Switch to', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'horizontal' => array(
						'title' => __( 'Horizontal', 'cmsmasters-elementor' ),
						'description' => __( 'Changes vertical tabs type to horizontal', 'cmsmasters-elementor' ),
					),
					'accordion' => array(
						'title' => __( 'Accordion', 'cmsmasters-elementor' ),
						'description' => __( 'Changes vertical tabs type to accordion', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'toggle' => false,
				'default' => 'accordion',
				'frontend_available' => true,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-type-responsive-',
				'condition' => array(
					'tabs_type' => 'vertical',
					'tabs_responsive!' => '',
				),
			)
		);

		$responsive_accordion = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'tabs_type',
					'operator' => '===',
					'value' => 'vertical',
				),
				array(
					'name' => 'tabs_responsive_type',
					'operator' => '===',
					'value' => 'accordion',
				),
			),
		);

		$responsive_type = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'tabs_type',
					'operator' => '===',
					'value' => 'horizontal',
				),
				$responsive_accordion,
			),
		);

		$this->add_control(
			'tabs_responsive_choose',
			array(
				'label' => __( 'Accordion Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'accordion' => array(
						'title' => __( 'Accordion', 'cmsmasters-elementor' ),
					),
					'toggle' => array(
						'title' => __( 'Toggle', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'toggle' => false,
				'default' => 'accordion',
				'render_type' => 'template',
				'frontend_available' => true,
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'tabs_responsive',
							'operator' => '!==',
							'value' => '',
						),
						$responsive_type,
					),
				),
			)
		);

		$responsive_horizontal = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'tabs_type',
					'operator' => '===',
					'value' => 'vertical',
				),
				array(
					'name' => 'tabs_responsive_type',
					'operator' => '===',
					'value' => 'horizontal',
				),
			),
		);

		$this->add_control(
			'tab_list_mode_vertical',
			array(
				'label' => __( 'Mode', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inline' => array(
						'title' => __( 'Inline', 'cmsmasters-elementor' ),
					),
					'justify' => array(
						'title' => __( 'Justify', 'cmsmasters-elementor' ),
					),
					'column' => array(
						'title' => __( 'Column', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'inline',
				'toggle' => false,
				'selectors_dictionary' => array(
					'inline' => '',
					'justify' => 'flex-direction: row;',
					'column' => 'flex-direction: column;',
				),
				'prefix_class' => 'cmsmasters-tab-list-mode-',
				'selectors' => array(
					'(tablet-){{WRAPPER}}.cmsmasters-tabs-type-vertical.cmsmasters-type-responsive-horizontal.cmsmasters-tabs-responsive-tablet .cmsmasters-tabs-list' => '{{VALUE}}',
					'(mobile){{WRAPPER}}.cmsmasters-tabs-type-vertical.cmsmasters-type-responsive-horizontal[class*="cmsmasters-tabs-responsive"] .cmsmasters-tabs-list' => '{{VALUE}}',
					'(tablet-){{WRAPPER}}.cmsmasters-tabs-type-vertical.cmsmasters-tab-list-mode-justify.cmsmasters-type-responsive-horizontal.cmsmasters-tabs-responsive-tablet .cmsmasters-tabs-list li' => 'flex-grow: 1; flex-basis: 0;',
					'(mobile){{WRAPPER}}.cmsmasters-tabs-type-vertical.cmsmasters-tab-list-mode-justify.cmsmasters-type-responsive-horizontal[class*="cmsmasters-tabs-responsive"] .cmsmasters-tabs-list li' => 'flex-grow: 1; flex-basis: 0;',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'tabs_responsive',
							'operator' => '!==',
							'value' => '',
						),
						$responsive_horizontal,
					),
				),
			)
		);

		$responsive_type_not_epmty = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'tabs_responsive',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'tabs_responsive_choose',
					'operator' => '!==',
					'value' => '',
				),
				$responsive_type,
			),
		);

		$this->add_control(
			'accordion_icon_heading',
			array(
				'label' => __( 'Accordion Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $responsive_type_not_epmty,
			)
		);

		$this->add_control(
			'accordion_icon_tabs_enable',
			array(
				'label' => __( 'Enable Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'render_type' => 'template',
				'conditions' => $responsive_type_not_epmty,
			)
		);

		/* Start Tab Title Tabs */
		$this->start_controls_tabs(
			'accordion_icon_tabs',
			array(
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'accordion_icon_tabs_enable',
							'operator' => '===',
							'value' => 'yes',
						),
						$responsive_type_not_epmty,
					),
				),
			)
		);

			$this->start_controls_tab(
				'accordion_icon_tabs_normal',
				array(
					'label' => __( 'Normal', 'cmsmasters-elementor' ),
				)
			);

			$this->add_control(
				'accordion_icon',
				array(
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'default' => array(
						'value' => 'fas fa-plus',
						'library' => 'fa-solid',
					),
				)
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'accordion_icon_tabs_active',
				array(
					'label' => __( 'Active', 'cmsmasters-elementor' ),
				)
			);

			$this->add_control(
				'accordion_icon_active',
				array(
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'default' => array(
						'value' => 'fas fa-minus',
						'library' => 'fa-solid',
					),
				)
			);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'accordion_item_style_icon_position',
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
				'label_block' => false,
				'toggle' => false,
				'default' => 'right',
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-accordion-icon-position-',
				'selectors_dictionary' => array(
					'left' => 'flex-direction: row; justify-content: space-between',
					'right' => 'flex-direction: row-reverse; justify-content: space-between;',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-accordion-item a' => 'display: flex; align-items: center; {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'tabs_responsive',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'accordion_icon[value]',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'accordion_icon_tabs_enable',
							'operator' => '===',
							'value' => 'yes',
						),
						$responsive_type,
					),
				),
			)
		);

		$this->add_control(
			'accordion_item_style_alignment',
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
				'label_block' => false,
				'toggle' => false,
				'default' => 'left',
				'selectors_dictionary' => array(
					'left' => 'justify-content: flex-start; align-items: flex-start;',
					'center' => 'justify-content: center; align-items: center;',
					'right' => 'justify-content: flex-end; align-items: flex-end;',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-accordion-item-wrap .cmsmasters-tab-title__text-wrap-outer' => '{{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'tabs_responsive',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'accordion_icon_tabs_enable',
							'operator' => '===',
							'value' => '',
						),
						$responsive_type,
					),
				),
			)
		);

		$this->add_control(
			'accordion_icon_view',
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
				'prefix_class' => 'cmsmasters-accordion-icon-view-',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'tabs_responsive',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'accordion_icon[value]',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'accordion_icon_tabs_enable',
							'operator' => '===',
							'value' => 'yes',
						),
						$responsive_type,
					),
				),
			)
		);

		$this->add_control(
			'accordion_icon_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'square' => array( 'title' => __( 'Square', 'cmsmasters-elementor' ) ),
					'circle' => array( 'title' => __( 'Circle', 'cmsmasters-elementor' ) ),
				),
				'default' => 'square',
				'label_block' => false,
				'prefix_class' => 'cmsmasters-accordion-icon-shape-',
				'condition' => array( 'accordion_icon_view!' => 'default' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tabs_style',
			array(
				'label' => __( 'Tabs List', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'tabs_gap_between',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--tabs-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$responsive_horizontal_or_horizontal = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'tabs_type',
					'operator' => '=',
					'value' => 'horizontal',
				),
				$responsive_horizontal,
			),
		);

		$this->add_control(
			'tab_list_ver_alignment',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'description' => __( 'Isn`t applied to accordion<br>The setting applies to tabs that aren`t equal in height to others', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
					'stretch' => array(
						'title' => __( 'Stretch', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-stretch',
					),
				),
				'label_block' => false,
				'default' => 'bottom',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-list-ver-align-',
				'conditions' => $responsive_horizontal_or_horizontal,
			)
		);

		$this->add_control(
			'tab_list_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-tabs-list-wrapper' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'tab_list_border',
				'selector' => '{{WRAPPER}} .cmsmasters-tabs-list-wrapper',
				'fields_options' => array(
					'border' => array( 'separator' => 'before' ),
				),
			)
		);

		$this->add_responsive_control(
			'tab_list_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-tabs-list-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'tab_list_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-tabs-list-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'tab_list_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-tabs-list-wrapper',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tab_list_item_style',
			array(
				'label' => __( 'List Item', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'tab_list_item_gap_between',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'%' => array(
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--tabs-list-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'tab_list_item_title_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-tabs-list-item a, {{WRAPPER}} .cmsmasters-tabs-list-item .cmsmasters-tab-title__text',
			)
		);

		/* Start Tab Title Tabs */
		$this->start_controls_tabs(
			'tab_list_item_tabs',
			array( 'separator' => 'before' )
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'active' => __( 'Active', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$default_color = ( 'active' === $main_key ? '#e9e9e9' : '' );
			$main = '{{WRAPPER}} .cmsmasters-tabs-list-item' . ( 'active' === $main_key ? '.active-tab a' : ' a' );

			$selector = $main . ( 'hover' === $main_key ? ':hover' : '' );
			$subtitle_selector = $selector . ' .cmsmasters-tab-subtitle-text';
			$background_selector = $main . ( 'normal' === $main_key ? ':before' : ':after' );

			/* Start Tab Title Tab */
			$this->start_controls_tab(
				"tab_list_item_tab_{$main_key}",
				array(
					'label' => $label,
				)
			);

				$this->add_control(
					"tab_list_item_color_{$main_key}",
					array(
						'label' => __( 'Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							"{$selector},
							{$selector} .cmsmasters-tab-title__text" => 'color: {{VALUE}}',
						),
					)
				);

				$this->add_control(
					"tab_list_item_subtitle_color_{$main_key}",
					array(
						'label' => __( 'Subtitle Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$subtitle_selector => 'color: {{VALUE}}',
						),
					)
				);

				$this->add_control(
					"tab_list_item_bg_group_{$main_key}_background",
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
					"tab_list_item_bg_{$main_key}",
					array(
						'label' => __( 'Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'default' => $default_color,
						'selectors' => array(
							$background_selector => '--button-bg-color: {{VALUE}}; ' .
								'background: var( --button-bg-color );',
						),
						'condition' => array(
							"tab_list_item_bg_group_{$main_key}_background" => array(
								'color',
								'gradient',
							),
						),
					)
				);

				$this->add_control(
					"tab_list_item_bg_group_{$main_key}_color_stop",
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
							"tab_list_item_bg_group_{$main_key}_background" => array( 'gradient' ),
						),
						'of_type' => 'gradient',
					)
				);

				$this->add_control(
					"tab_list_item_bg_group_{$main_key}_color_b",
					array(
						'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'default' => '#f2295b',
						'render_type' => 'ui',
						'condition' => array(
							"tab_list_item_bg_group_{$main_key}_background" => array( 'gradient' ),
						),
						'of_type' => 'gradient',
					)
				);

				$this->add_control(
					"tab_list_item_bg_group_{$main_key}_color_b_stop",
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
							"tab_list_item_bg_group_{$main_key}_background" => array( 'gradient' ),
						),
						'of_type' => 'gradient',
					)
				);

				$this->add_control(
					"tab_list_item_bg_group_{$main_key}_gradient_type",
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
							"tab_list_item_bg_group_{$main_key}_background" => array( 'gradient' ),
						),
						'of_type' => 'gradient',
					)
				);

				$this->add_control(
					"tab_list_item_bg_group_{$main_key}_gradient_angle",
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
							$background_selector => 'background-color: transparent; ' .
								"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{tab_list_item_bg_group_{$main_key}_color_stop.SIZE}}{{tab_list_item_bg_group_{$main_key}_color_stop.UNIT}}, {{tab_list_item_bg_group_{$main_key}_color_b.VALUE}} {{tab_list_item_bg_group_{$main_key}_color_b_stop.SIZE}}{{tab_list_item_bg_group_{$main_key}_color_b_stop.UNIT}})",
						),
						'condition' => array(
							"tab_list_item_bg_group_{$main_key}_background" => array( 'gradient' ),
							"tab_list_item_bg_group_{$main_key}_gradient_type" => 'linear',
						),
						'separator' => 'after',
						'of_type' => 'gradient',
					)
				);

				$this->add_control(
					"tab_list_item_bg_group_{$main_key}_gradient_position",
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
							$background_selector => 'background-color: transparent; ' .
								"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{tab_list_item_bg_group_{$main_key}_color_stop.SIZE}}{{tab_list_item_bg_group_{$main_key}_color_stop.UNIT}}, {{tab_list_item_bg_group_{$main_key}_color_b.VALUE}} {{tab_list_item_bg_group_{$main_key}_color_b_stop.SIZE}}{{tab_list_item_bg_group_{$main_key}_color_b_stop.UNIT}})",
						),
						'condition' => array(
							"tab_list_item_bg_group_{$main_key}_background" => array( 'gradient' ),
							"tab_list_item_bg_group_{$main_key}_gradient_type" => 'radial',
						),
						'separator' => 'after',
						'of_type' => 'gradient',
					)
				);

				$this->add_control(
					"tab_list_item_border_{$main_key}",
					array(
						'label' => __( 'Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'default' => $default_color,
						'selectors' => array(
							$selector => 'border-color: {{VALUE}}',
						),
						'condition' => array( 'tab_list_item_border_border!' => '' ),
					)
				);

				$this->add_responsive_control(
					"tab_list_item_padding_{$main_key}",
					array(
						'label' => __( 'Padding', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'default' => array(
							'top' => 10,
							'bottom' => 10,
							'left' => 20,
							'right' => 20,
						),
						'selectors' => array(
							$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
							'{{WRAPPER}}' => '--list-item-padding-bottom: {{BOTTOM}}{{UNIT}}; ' .
								'--list-item-padding-top: {{TOP}}{{UNIT}}; ' .
								'--list-item-padding-left: {{LEFT}}{{UNIT}}; ' .
								'--list-item-padding-right: {{RIGHT}}{{UNIT}};',
						),
					)
				);

				$this->add_responsive_control(
					"tab_list_item_hor_margin_{$main_key}",
					array(
						'label' => __( 'Gap', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px' ),
						'allowed_dimensions' => 'vertical',
						'placeholder' => array(
							'top' => '',
							'right' => 'auto',
							'bottom' => '',
							'left' => 'auto',
						),
						'selectors' => array(
							$selector => 'margin-top: {{TOP}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
						),
						'conditions' => $responsive_horizontal_or_horizontal,
					)
				);

				$this->add_responsive_control(
					"tab_list_item_ver_margin_{$main_key}",
					array(
						'label' => __( 'Gap', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array( 'px' ),
						'allowed_dimensions' => 'horizontal',
						'placeholder' => array(
							'top' => 'auto',
							'right' => '',
							'bottom' => 'auto',
							'left' => '',
						),
						'selectors' => array(
							$selector => 'margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}};',
						),
						'condition' => array( 'tabs_type' => 'vertical' ),
					)
				);

				$this->add_group_control(
					Group_Control_Text_Shadow::get_type(),
					array(
						'name' => "tab_list_item_text_shadow_{$main_key}",
						'separator' => 'before',
						'selector' => $selector,
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name' => "tab_list_item_box_shadow_{$main_key}",
						'selector' => $selector,
					)
				);

			if ( 'hover' === $main_key ) {
				$this->add_control(
					'tab_list_transition',
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'default' => array( 'size' => 0.3 ),
						'range' => array(
							'px' => array(
								'max' => 3,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							'{{WRAPPER}} .cmsmasters-tabs-list-item,
							{{WRAPPER}} .cmsmasters-tabs-list-item a:before,
							{{WRAPPER}} .cmsmasters-tabs-list-item a:after,
							{{WRAPPER}} .cmsmasters-tabs-list-item a,
							{{WRAPPER}} .cmsmasters-tabs-list-item .cmsmasters-tab-title__text' => 'transition: all {{SIZE}}s',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'tab_list_item_border',
				'exclude' => array( 'color' ),
				'selector' => '{{WRAPPER}} .cmsmasters-tabs-list-item a',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
						'separator' => 'before',
					),
					'width' => array(
						'default' => array(
							'top' => 1,
							'bottom' => 1,
							'left' => 1,
							'right' => 1,
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'tab_list_item_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-tabs-list-item,' .
					'{{WRAPPER}} .cmsmasters-tabs-list-item a,' .
					'{{WRAPPER}} .cmsmasters-tabs-list-item a:before,' .
					'{{WRAPPER}} .cmsmasters-tabs-list-item a:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'tab_list_style_subtitle',
			array(
				'label' => __( 'Subtitle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'tab_list_item_subtitle_gap_between',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'max' => 50,
					),
					'%' => array(
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-tab-subtitle-text' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'tab_list_item_subtitle_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-tabs .cmsmasters-tabs-list-item .cmsmasters-tab-subtitle-text',
			)
		);

		$this->end_controls_section();

		foreach ( array(
			'tab' => __( 'Tab Icon', 'cmsmasters-elementor' ),
			'accordion' => __( 'Accordion Icon', 'cmsmasters-elementor' ),
		) as $root_key => $label ) {
			$this->start_controls_section(
				"section_style_{$root_key}_icon",
				array(
					'label' => $label,
					'tab' => Controls_Manager::TAB_STYLE,
				)
			);

			if ( 'tab' === $root_key ) {
				$icons_selector = '{{WRAPPER}} .cmsmasters-tabs-list-item:not(.cmsmasters-accordion-item)';
				$icons_selector_alt = '{{WRAPPER}} .cmsmasters-accordion-item';

				$icon_class = ' .cmsmasters-tab-icon';
				$icon_class_alt = ' .cmsmasters-tab-title__text-wrap-outer .cmsmasters-tab-icon';
			} else {
				$icons_selector = '{{WRAPPER}} .cmsmasters-accordion-item';
				$icons_selector_alt = '';

				$icon_class = ' a > .cmsmasters-tab-icon';
				$icon_class_alt = '';
			}

			if ( 'tab' === $root_key ) {
				$this->add_control(
					'tab_icon_view',
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
						'prefix_class' => 'cmsmasters-icon-view-',
					)
				);

				$this->add_control(
					'tab_icon_shape',
					array(
						'label' => __( 'Shape', 'cmsmasters-elementor' ),
						'type' => CmsmastersControls::CHOOSE_TEXT,
						'options' => array(
							'square' => array( 'title' => __( 'Square', 'cmsmasters-elementor' ) ),
							'circle' => array( 'title' => __( 'Circle', 'cmsmasters-elementor' ) ),
						),
						'default' => 'square',
						'label_block' => false,
						'prefix_class' => 'cmsmasters-icon-shape-',
						'condition' => array( 'tab_icon_view!' => 'default' ),
					)
				);
			}

			$this->add_responsive_control(
				"tab_list_item_style_{$root_key}_icon_size",
				array(
					'label' => __( 'Size', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em' ),
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
						"{$icons_selector}{$icon_class}" => 'font-size: {{SIZE}}{{UNIT}};',
						"{$icons_selector_alt}{$icon_class_alt}" => 'font-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			if ( 'tab' === $root_key ) {
				$this->add_responsive_control(
					"tab_list_item_style_{$root_key}_icon_gap",
					array(
						'label' => __( 'Spacing', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => array( 'px', '%' ),
						'default' => array(
							'size' => 10,
							'unit' => 'px',
						),
						'range' => array(
							'px' => array(
								'max' => 50,
							),
						),
						'selectors' => array(
							'{{WRAPPER}}' => '--icon-gap: {{SIZE}}{{UNIT}}',
						),
					)
				);
			}

			$this->start_controls_tabs( "tabs_{$root_key}_icon_style" );

			foreach ( array(
				'normal' => __( 'Normal', 'cmsmasters-elementor' ),
				'hover' => __( 'Hover', 'cmsmasters-elementor' ),
				'active' => __( 'Active', 'cmsmasters-elementor' ),
			) as $main_key => $label ) {
				if ( 'normal' === $main_key ) {
					if ( 'tab' === $root_key ) {
						$icon_selector = "{$icons_selector}{$icon_class}, {$icons_selector_alt}{$icon_class_alt}";
					} else {
						$icon_selector = "{$icons_selector}{$icon_class}";
					}
				} elseif ( 'hover' === $main_key ) {
					if ( 'tab' === $root_key ) {
						$icon_selector = "{$icons_selector}:hover{$icon_class}, {$icons_selector_alt}:hover{$icon_class_alt}";
					} else {
						$icon_selector = "{$icons_selector}:hover{$icon_class}";
					}
				} elseif ( 'active' === $main_key ) {
					if ( 'tab' === $root_key ) {
						$icon_selector = "{$icons_selector}.active-tab{$icon_class}, {$icons_selector_alt}.active-tab{$icon_class_alt}";
					} else {
						$icon_selector = "{$icons_selector}.active-tab{$icon_class}";
					}
				}

				/* Start Tab Title Tab */
				$this->start_controls_tab(
					"tab_list_item_{$root_key}_icon_{$main_key}",
					array(
						'label' => $label,
					)
				);

					$this->add_control(
						"tab_list_{$root_key}_icon_color_{$main_key}",
						array(
							'label' => __( 'Color', 'cmsmasters-elementor' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => array(
								$icon_selector => 'color: {{VALUE}}; border-color: {{VALUE}};',
							),
						)
					);

					$this->add_control(
						"tab_list_{$root_key}_icon_background_{$main_key}",
						array(
							'label' => __( 'Background Color', 'cmsmasters-elementor' ),
							'type' => Controls_Manager::COLOR,
							'selectors' => array(
								$icon_selector => 'background-color: {{VALUE}}',
							),
							'condition' => array( $root_key . '_icon_view!' => 'default' ),
						)
					);

					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						array(
							'name' => "tab_list_{$root_key}_icon_box_shadow_{$main_key}",
							'selector' => $icon_selector,
							'condition' => array( $root_key . '_icon_view!' => 'default' ),
						)
					);

				if ( 'hover' === $main_key ) {
					if ( 'tab' === $root_key ) {
						$transition_selector = "{$icons_selector}{$icon_class}, {$icons_selector_alt}{$icon_class_alt}";
					} else {
						$transition_selector = "{$icons_selector}{$icon_class}";
					}
					$this->add_control(
						"tab_list_{$root_key}_icon_transition",
						array(
							'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
							'type' => Controls_Manager::SLIDER,
							'default' => array( 'size' => 0.3 ),
							'range' => array(
								'px' => array(
									'max' => 3,
									'step' => 0.1,
								),
							),
							'selectors' => array(
								$transition_selector => 'transition: all {{SIZE}}s',
							),
						)
					);
				}

				$this->end_controls_tab();
			}

			$this->end_controls_tabs();

			$this->add_control(
				$root_key . '_icon_hr',
				array(
					'type' => Controls_Manager::DIVIDER,
					'style' => 'thick',
					'condition' => array( $root_key . '_icon_view!' => 'default' ),
				)
			);

			$this->add_responsive_control(
				$root_key . '_icon_padding',
				array(
					'label' => __( 'Padding', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px' ),
					'selectors' => array(
						"{$icons_selector}{$icon_class}" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						"{$icons_selector_alt}{$icon_class_alt}" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition' => array(
						$root_key . '_icon_view!' => 'default',
						$root_key . '_icon_shape!' => 'circle',
					),
				)
			);

			$this->add_responsive_control(
				$root_key . '_icon_icon_padding',
				array(
					'label' => __( 'Padding', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 5,
							'max' => 50,
						),
					),
					'selectors' => array(
						"{$icons_selector}{$icon_class}" => 'padding: {{SIZE}}{{UNIT}}',
						"{$icons_selector_alt}{$icon_class_alt}" => 'padding: {{SIZE}}{{UNIT}}',
					),
					'condition' => array(
						$root_key . '_icon_view!' => 'default',
						$root_key . '_icon_shape' => 'circle',
					),
				)
			);

			$this->add_control(
				$root_key . '_icon_framed_border_width',
				array(
					'label' => __( 'Border Width', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px' ),
					'selectors' => array(
						"{$icons_selector}{$icon_class}" => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						"{$icons_selector_alt}{$icon_class_alt}" => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition' => array( $root_key . '_icon_view' => 'framed' ),
				)
			);

			$this->add_control(
				$root_key . '_icon_border_radius',
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						"{$icons_selector}{$icon_class}" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						"{$icons_selector_alt}{$icon_class_alt}" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition' => array( $root_key . '_icon_view!' => 'default' ),
				)
			);

			$this->end_controls_section();
		}

		$this->start_controls_section(
			'section_tab_content_style',
			array(
				'label' => __( 'Content Wrapper', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'tab_content_text_alignment',
				array(
					'label' => __( 'Text Alignment', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'label_block' => false,
					'default' => 'left',
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
					'toggle' => false,
					'separator' => 'before',
					'selectors' => array(
						'{{WRAPPER}} .cmsmasters-tab' => 'text-align: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'tab_content_typography',
					'selector' => '{{WRAPPER}} .cmsmasters-tab',
				)
			);

			$this->add_control(
				'tab_content_text_color',
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .cmsmasters-tab' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'tab_content_bg_color',
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .cmsmasters-tab' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name' => 'tab_content_wrapper_border',
					'selector' => '{{WRAPPER}} .cmsmasters-tabs .cmsmasters-tab',
				)
			);

			$this->add_responsive_control(
				'tab_content_wrapper_border_radius',
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors' => array(
						'{{WRAPPER}} .cmsmasters-tabs .cmsmasters-tab' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				'tab_content_wrapper_padding',
				array(
					'label' => __( 'Padding', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'{{WRAPPER}} .cmsmasters-tabs .cmsmasters-tab' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => 'tab_content_wrapper_box_shadow',
					'selector' => '{{WRAPPER}} .cmsmasters-tabs .cmsmasters-tab',
				)
			);

		$this->end_controls_section();

		AnimationModule::register_sections_controls( $this );
	}

	/**
	 * Adds template to content of some tab.
	 *
	 * Adds template from various choices, that available from select control.
	 *
	 * @since 1.0.0
	 * @since 1.11.1 Optimized loading CSS in templates.
	 * @since 1.12.1 Add checking template.
	 */
	public function get_widget_template( $template_id, $type ) {
		if ( ! CmsmastersUtils::check_template( $template_id ) ) {
			if ( is_admin() ) {
				if ( 'section' === $type ) {
					$message = esc_html__( 'Please choose your saved section or container template!', 'cmsmasters-elementor' );
				} elseif ( 'template' === $type ) {
					$message = esc_html__( 'Please choose your saved page template!', 'cmsmasters-elementor' );
				} else {
					$message = esc_html__( 'Please choose your saved global widget!', 'cmsmasters-elementor' );
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
	 * @since 1.0.0
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
	 * @since 1.0.0
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
	 * Get template IDs.
	 *
	 * @since 1.11.1
	 * @since 1.12.1 Add checking template.
	 * @since 1.16.4 Fixed display widget when tabs is empty.
	 *
	 * @return array Template IDs.
	 */
	public function get_template_ids() {
		$template_ids = array();

		$tabs = $this->get_settings_for_display( 'tabs' );

		if ( empty( $tabs ) ) {
			return array();
		}

		foreach ( $tabs as $item ) {
			if ( ! in_array( $item['content_type'], array( 'section', 'template', 'widget' ), true ) ) {
				continue;
			}

			$content_type = $item['content_type'];
			$template_id = ( ! empty( $item[ "saved_{$content_type}" ] ) ? $item[ "saved_{$content_type}" ] : '' );

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
	 * @since 1.0.0
	 * @since 1.11.1 Optimized loading CSS in templates.
	 */
	public function render() {
		$this->add_render_attribute( '_wrapper', array(
			'data-separate-animation-selector' => $this->get_separate_animation_selector(),
			'data-text-animation-class' => $this->get_text_animation_class(),
		) );

		$template_ids = $this->get_template_ids();

		if ( $this->get_group_animation() ) {
			$this->add_render_attribute( '_wrapper', array(
				'data-group-animation' => 'true',
			) );
		}

		if ( ! empty( $template_ids ) && 'enable' !== $this->lazyload_widget_get_status() ) {
			/** @var Addon $addon */
			$addon = CmsmastersPlugin::instance();

			$addon->frontend->print_template_css( $template_ids, $this->get_id() );
		}

		$tabs = $this->get_settings_for_display( 'tabs' );

		echo '<div class="cmsmasters-tabs">';
			$this->print_tabs_list( $tabs );
			$this->print_tabs_content( $tabs );
		echo '</div>';
	}

	/**
	 * Print tabs list.
	 *
	 * Retrieves tabs list.
	 *
	 * @since 1.0.0
	 * @since 1.3.2 Fixed tab title wrapper.
	 * @since 1.3.7 Fixed empty elements.
	 * @since 1.7.5 Added custom data id attribute for anchor tag.
	 */
	private function print_tabs_list( $tabs ) {
		$animation_class = AnimationModule::get_animation_class();

		$settings = $this->get_active_settings();

		$id_int = substr( $this->get_id_int(), 0, 3 );

		echo '<div class="cmsmasters-tabs-list-wrapper">
			<ul class="cmsmasters-tabs-list" role="tablist">';

				foreach ( $tabs as $index => $item ) {
					$tab_count = $index + 1;

					$tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );

					$this->add_render_attribute( $tab_title_setting_key, array(
						'id' => 'cmsmasters-tabs-list-item_' . esc_attr( $id_int ) . esc_attr( $tab_count ),
						'class' => array( 'cmsmasters-tabs-list-item', esc_attr( $animation_class ) ),
						'data-tab' => esc_attr( $tab_count ),
						'role' => 'tab',
						'aria-selected' => 'true',
						'tabindex' => '0',
					) );

					$tab_custom_id = ( isset( $item['tab_custom_id'] ) ? $item['tab_custom_id'] : '' );

					if ( '' !== $tab_custom_id ) {
						if ( '#' !== substr( $tab_custom_id, 0, 1 ) ) {
							$tab_custom_id = '#' . $tab_custom_id;
						}

						$this->add_render_attribute( $tab_title_setting_key, array( 'tab_custom_id' => esc_attr( $tab_custom_id ) ) );
					}

					echo '<li ' . $this->get_render_attribute_string( $tab_title_setting_key ) . '>' .
						'<a href="" class="cmsmasters-tab-title" tabindex="-1">
							<div class="cmsmasters-tab-title__text-wrap-outer">';

								if ( 'svg' !== $item['tab_icon']['library'] ) {
									echo $this->print_icon( $item ); // XSS ok.
								} else {
									echo '<span class="cmsmasters-tab-icon svg">';
										Icons_Manager::render_icon( $item['tab_icon'], array( 'aria-hidden' => 'true' ) );
									echo '</span>';
								}

								$tab_title = ( isset( $item['tab_title'] ) ? $item['tab_title'] : '' );
								$tab_subtitle = ( isset( $item['tab_subtitle'] ) ? $item['tab_subtitle'] : '' );

								if ( '' !== $tab_title || '' !== $tab_subtitle ) {
									echo '<div class="cmsmasters-tab-title__text-wrap">';

										if ( '' !== $tab_title ) {
											$title_tag = $settings['title_tag'];

											$this->add_render_attribute( $tab_title_setting_key . '_text', 'class', array(
												'cmsmasters-tab-title__text',
												( 'Tab' === $tab_title ) ? 'default' : '',
											) );

											echo '<' . Utils::validate_html_tag( $title_tag ) . ' ' . $this->get_render_attribute_string( $tab_title_setting_key . '_text' ) . '>' .
												wp_kses_post( $tab_title ) .
											'</' . Utils::validate_html_tag( $title_tag ) . '>';
										}

										if ( '' !== $tab_subtitle ) {
											echo '<span class="cmsmasters-tab-subtitle-text">' .
												wp_kses_post( $tab_subtitle ) .
											'</span>';
										}

									echo '</div>';
								}

							echo '</div>' .
						'</a>' .
					'</li>';
				}

			echo '</ul>
		</div>';
	}

	/**
	 * Get tabs list icon.
	 *
	 * Retrieves tabs list icon or svg icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Retrieves tabs list icon or svg icon.
	 */
	private function print_icon( $item ) {
		$settings = $this->get_active_settings();

		$tab_icon = ( '' !== $item['tab_icon']['value'] ? ' ' . $item['tab_icon']['value'] : '' );
		$tab_list_item_style_icon_position = ' icon-align-' . $settings['tab_list_item_style_icon_position'];

		$html = '';

		if ( '' !== $item['tab_icon']['value'] ) {
			$html = '<span class="cmsmasters-tab-icon' . esc_attr( $tab_icon ) . esc_attr( $tab_list_item_style_icon_position ) . '" aria-hidden="true"></span>';
		}

		return $html;
	}

	/**
	 * Get tabs content.
	 *
	 * Retrieves tabs content.
	 *
	 * @since 1.0.0
	 */
	private function print_tabs_content( $tabs ) {
		$id_int = substr( $this->get_id_int(), 0, 3 );

		echo '<div class="cmsmasters-tabs-wrap">';
		foreach ( $tabs as $index => $item ) {
			$tab_count = $index + 1;

			$tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', esc_attr( $index ) );

			$this->add_render_attribute( $tab_content_setting_key, array(
				'id' => 'cmsmasters-tab-content_' . esc_attr( $id_int ) . esc_attr( $tab_count ),
				'class' => 'cmsmasters-tab',
				'data-tab' => esc_attr( $tab_count ),
			) );

			$this->print_accordion_start( $id_int, $index, $item );

			echo '<div ' . $this->get_render_attribute_string( $tab_content_setting_key ) . '>';
			switch ( $item['content_type'] ) {
				case 'section':
				case 'template':
				case 'widget':
					$content_type = $item['content_type'];

					$template_id = ( ! empty( $item[ "saved_{$content_type}" ] ) ? $item[ "saved_{$content_type}" ] : '' );

					echo $this->get_widget_template( esc_html( $template_id ), esc_html( $content_type ) );

					break;
				case 'tab-content':
					echo $this->parse_text_editor( wp_kses_post( $item['tab_content'] ) );

					break;
			}
			echo '</div>';

			$this->print_accordion_end();
		}
		echo '</div>';
	}

	/**
	 * Print accordion html.
	 *
	 * Retrieves accordion html start.
	 *
	 * @since 1.0.0
	 * @since 1.3.2 Fixed tab title wrapper.
	 * @since 1.3.7 Fixed empty elements.
	 * @since 1.18.4 Fixed application title html tag in accordion.
	 */
	private function print_accordion_start( $id_int, $index, $item ) {
		$settings = $this->get_active_settings();

		if ( '' === $settings['tabs_responsive'] ) {
			return;
		}

		$animation_class = AnimationModule::get_animation_class();

		$tab_count = $index + 1;

		echo '<div class="cmsmasters-accordion-item-wrap" role="tabpanel">';
			$accordion_title_setting_key = $this->get_repeater_setting_key( 'accordion_title', 'tabs', $index );

			$this->add_render_attribute( $accordion_title_setting_key, array(
				'id' => 'cmsmasters-accordion-item-' . esc_attr( $id_int ) . esc_attr( $tab_count ),
				'class' => array( 'cmsmasters-tabs-list-item', 'cmsmasters-accordion-item', esc_attr( $animation_class ) ),
				'data-tab' => esc_attr( $tab_count ),
				'role' => 'tab',
				'aria-selected' => 'true',
				'tabindex' => '0',
			) );

			echo '<div ' . $this->get_render_attribute_string( $accordion_title_setting_key ) . '>' .
				'<a href="" class="cmsmasters-tab-title" tabindex="-1">';
					( 'yes' === $settings['accordion_icon_tabs_enable'] ) ? $this->print_accordion_icon( $settings, false ) : '';
					echo '<div class="cmsmasters-tab-title__text-wrap-outer">';

		if ( 'svg' !== $item['tab_icon']['library'] ) {
			echo $this->print_icon( $item );
		} else {
			echo '<span class="cmsmasters-tab-icon svg">';
				Icons_Manager::render_icon( $item['tab_icon'], array( 'aria-hidden' => 'true' ) );
			echo '</span>';
		}

		$tab_title = ( isset( $item['tab_title'] ) ? $item['tab_title'] : '' );
		$tab_subtitle = ( isset( $item['tab_subtitle'] ) ? $item['tab_subtitle'] : '' );

		if ( '' !== $tab_title || '' !== $tab_subtitle ) {
			echo '<div class="cmsmasters-tab-title__text-wrap">';
				$title_tag = $settings['title_tag'];

				if ( '' !== $tab_title ) {
					$this->add_render_attribute( $accordion_title_setting_key . '_text', 'class', array(
						'cmsmasters-tab-title__text',
						( 'Tab' === $tab_title ) ? 'default' : '',
					) );

					echo '<' . Utils::validate_html_tag( $title_tag ) . ' ' . $this->get_render_attribute_string( $accordion_title_setting_key . '_text' ) . '>' .
						wp_kses_post( $tab_title ) .
					'</' . Utils::validate_html_tag( $title_tag ) . '>';
				}

				if ( '' !== $tab_subtitle ) {
					echo '<span class="cmsmasters-tab-subtitle-text">' .
						wp_kses_post( $tab_subtitle ) .
					'</span>';
				}

			echo '</div>';
		}

					echo '</div>' .
				'</a>' .
			'</div>';
	}

	/**
	 * Print accordion html.
	 *
	 * Retrieves accordion html end.
	 *
	 * @since 1.0.0
	 */
	private function print_accordion_end() {
		if ( '' === $this->get_settings( 'tabs_responsive' ) ) {
			return;
		} else {
			echo '</div>';
		}
	}

	/**
	 * Get accordion icon.
	 *
	 * Retrieves accordion icon or svg icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Retrieves accordion icon or svg icon.
	 * @since 1.3.0 Fixed render icons for Woo data tabs.
	 */
	public function print_accordion_icon( $settings, $is_woo ) {
		$settings = $this->get_active_settings();

		$accordion_icon = ( '' !== $settings['accordion_icon']['value'] ) ? ' ' . $settings['accordion_icon']['value'] : '';
		$accordion_icon_active = ( '' !== $settings['accordion_icon_active']['value'] ) ? ' ' . $settings['accordion_icon_active']['value'] : ' ' . $settings['accordion_icon']['value'];
		$accordion_item_style_icon_position = ' icon-align-' . $settings['accordion_item_style_icon_position'];
		$accordion_icon_closed = '';
		$accordion_icon_open = '';

		if ( $is_woo ) {
			ob_start();
		}

		if ( '' !== $settings['accordion_icon']['value'] ) {
			$accordion_icon_closed = '<span class="cmsmasters-tab-icon cmsmasters-accordion-closed' . esc_attr( $accordion_icon ) . esc_attr( $accordion_item_style_icon_position ) . '"></span>';
			$accordion_icon_open = '<span class="cmsmasters-tab-icon cmsmasters-accordion-opened' . esc_attr( $accordion_icon_active ) . esc_attr( $accordion_item_style_icon_position ) . '"></span>';

			if ( 'svg' === $settings['accordion_icon']['library'] ) {
				$accordion_icon_closed = '';

				echo '<span class="cmsmasters-tab-icon svg cmsmasters-accordion-closed">';

					Icons_Manager::render_icon(
						$settings['accordion_icon'],
						array(
							'aria-hidden' => 'true',
							'aria-label' => 'Closed',
						)
					);

				echo '</span>';
			}

			if ( 'svg' === $settings['accordion_icon_active']['library'] ) {
				$accordion_icon_open = '';

				echo '<span class="cmsmasters-tab-icon svg cmsmasters-accordion-opened">';

					Icons_Manager::render_icon(
						$settings['accordion_icon_active'],
						array(
							'aria-hidden' => 'true',
							'aria-label' => 'Opened',
						)
					);

				echo '</span>';
			}
		}

		echo wp_kses_post( $accordion_icon_closed . $accordion_icon_open );

		if ( $is_woo ) {
			return ob_get_clean();
		}
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
			'tabs' => array(
				array(
					'field' => 'tab_title',
					'type' => esc_html__( 'Tab Title', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'tab_subtitle',
					'type' => esc_html__( 'Tab Subtitle', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'tab_content',
					'type' => esc_html__( 'Tab Contetnt', 'cmsmasters-elementor' ),
					'editor_type' => 'VISUAL',
				),
			),
		);
	}
}
