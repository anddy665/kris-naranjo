<?php
namespace CmsmastersElementor\Modules\Sitemap\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor sitemap widget.
 *
 * Elementor widget that displays an HTML sitemap.
 *
 */
class Sitemap extends Base_Widget {

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
		return __( 'Sitemap', 'cmsmasters-elementor' );
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
		return 'eicon-sitemap';
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
			'sitemap',
			'link',
			'menu',
			'map',
			'site',
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
			'widget-cmsmasters-sitemap',
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
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.3.0
	 */
	protected function register_controls() {
		$this->register_sitemap_tab();
		$this->register_style_tab();
	}

	/**
	 * Register widget list content section.
	 *
	 * Adds sitemap widget `sitemap content` settings section controls.
	 *
	 * @since 1.3.0
	 */
	protected function register_sitemap_tab() {
		$this->start_controls_section(
			'section',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->register_post_type_controls();
		$this->register_general_controls();

		$this->end_controls_section();

		$this->register_additional_settings_section();
	}

	/**
	 * Register widget sitemap list content section.
	 *
	 * Adds sitemap widget content settings controls.
	 *
	 * @since 1.3.0
	 */
	protected function register_post_type_controls() {
		$repeater = new Repeater();
		$supported_taxonomies = array();

		$public_types = CmsmastersUtils::get_public_post_types();

		foreach ( $public_types as $type => $title ) {
			$taxonomies = get_object_taxonomies( $type, 'objects' );
			foreach ( $taxonomies as $key => $tax ) {
				if ( ! in_array( $tax->name, $supported_taxonomies ) ) {
					$label = $tax->label;
					$supported_taxonomies[ $tax->name ] = $label;
				}
			}
		}

		$repeater->add_control(
			'type_selector',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'post_type',
				'options' => array(
					'post_type' => __( 'Post Type', 'cmsmasters-elementor' ),
					'taxonomy' => __( 'Taxonomy', 'cmsmasters-elementor' ),
				),
			)
		);

		$repeater->add_control(
			'source_post_type',
			array(
				'label' => __( 'Source', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'page',
				'options' => $public_types,
				'condition' => array(
					'type_selector' => 'post_type',
				),
			)
		);

		$repeater->add_control(
			'source_taxonomy',
			array(
				'label' => __( 'Source', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => $supported_taxonomies,
				'condition' => array(
					'type_selector' => 'taxonomy',
				),
			)
		);

		$repeater->add_control(
			'type_list',
			array(
				'label' => __( 'Type List', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom', 'cmsmasters-elementor' ),
				),
				'render_type' => 'template',
				'default' => 'default',
			)
		);

		$repeater->add_control(
			'count_items',
			array(
				'label' => __( 'Count Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'description' => __( 'Number of displayed items', 'cmsmasters-elementor' ),
				'min' => 1,
				'placeholder' => __( 'All', 'cmsmasters-elementor' ),
				'condition' => array(
					'type_list' => 'default',
				),
			)
		);

		$repeater->add_control(
			'offset_items',
			array(
				'label' => __( 'Offset Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'placeholder' => __( '0', 'cmsmasters-elementor' ),
				'description' => __( 'Number of items to skip', 'cmsmasters-elementor' ),
				'condition' => array(
					'type_list' => 'default',
					'count_items!' => '',
				),
			)
		);

		$repeater->add_control(
			'tems_include',
			array(
				'label' => __( 'Terms Include', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => true,
				'type' => CmsmastersControls::QUERY,
				'description' => __( 'Terms are items in a taxonomy. The available taxonomies are: Categories, Tags, Formats and custom taxonomies.', 'cmsmasters-elementor' ),
				'options' => array(),
				'multiple' => true,
				'autocomplete' => array(
					'object' => Query_Manager::CPT_TAX_OBJECT,
					'display' => 'detailed',
				),
				'export' => false,
				'condition' => array(
					'type_selector' => 'taxonomy',
					'type_list' => 'custom',
				),
			)
		);

		$repeater->add_control(
			'post_include',
			array(
				'label' => __( 'Post Include', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => true,
				'type' => CmsmastersControls::QUERY,
				'description' => __( 'Find and select a post to include.', 'cmsmasters-elementor' ),
				'options' => array(),
				'multiple' => true,
				'autocomplete' => array(
					'object' => Query_Manager::POST_OBJECT,
					'display' => 'detailed',
				),
				'export' => false,
				'condition' => array(
					'type_selector' => 'post_type',
					'type_list' => 'custom',
				),
			)
		);

		$repeater->add_control(
			'orderby_post_type',
			array(
				'label' => __( 'Order By', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'post_date',
				'options' => array(
					'post_date' => __( 'Date', 'cmsmasters-elementor' ),
					'post_title' => __( 'Title', 'cmsmasters-elementor' ),
					'menu_order' => __( 'Menu Order', 'cmsmasters-elementor' ),
					'rand' => __( 'Random', 'cmsmasters-elementor' ),
				),
				'condition' => array(
					'type_selector' => 'post_type',
				),
			)
		);

		$repeater->add_control(
			'orderby_taxonomy',
			array(
				'label' => __( 'Order By', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => array(
					'id' => __( 'ID', 'cmsmasters-elementor' ),
					'name' => __( 'Name', 'cmsmasters-elementor' ),
				),
				'condition' => array(
					'type_selector' => 'taxonomy',
				),
			)
		);

		$repeater->add_control(
			'order',
			array(
				'label' => __( 'Order', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => array(
					'asc' => __( 'ASC', 'cmsmasters-elementor' ),
					'desc' => __( 'DESC', 'cmsmasters-elementor' ),
				),
			)
		);

		$repeater->add_control(
			'heading_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'hide_title',
			array(
				'label' => __( 'Hide Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'condition' => array(
					'hide_title' => '',
				),
			)
		);

		$repeater->add_responsive_control(
			'list_top_gap',
			array(
				'label' => __( 'List Top Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range' => array(
					'px' => array( 'max' => 300 ),
					'em' => array( 'max' => 10 ),
					'%' => array( 'max' => 100 ),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-sitemap__section{{CURRENT_ITEM}}' => '--cmsmasters-sitemap-list-gap-top: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'hide_title!' => '',
				),
			)
		);

		$this->add_control(
			'items',
			array(
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => array(
					array(
						'type_selector' => 'post_type',
						'title' => __( 'Pages', 'cmsmasters-elementor' ),
						'source_post_type' => 'page',
					),
					array(
						'type_selector' => 'taxonomy',
						'title' => __( 'Categories', 'cmsmasters-elementor' ),
						'source_taxonomy' => 'category',
					),
				),
				'render_type' => 'template',
				'title_field' => '{{{ title }}}',
			)
		);
	}

	/**
	 * Register general style for widget sitemap.
	 *
	 * Adds general style settings controls for widget sitemap.
	 *
	 * @since 1.3.0
	 */
	protected function register_general_controls() {
		$this->add_control(
			'items_heading',
			array(
				'label' => __( 'Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
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
				'prefix_class' => 'cmsmasters-sitemap__align%s-',
			)
		);

		$this->add_control(
			'item_direction',
			array(
				'label' => __( 'Direction', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'reverse' => __( 'Reverse', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'prefix_class' => 'cmsmasters-sitemap__direction-',
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
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'icon' => __( 'Icon', 'cmsmasters-elementor' ),
					'numeric' => __( 'Numeric', 'cmsmasters-elementor' ),
				),
				'render_type' => 'template',
				'default' => 'icon',
				'prefix_class' => 'cmsmasters-sitemap__marker-',
			)
		);

		$this->add_control(
			'global_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => '',
					'library' => '',
				),
				'recommended' => array(
					'fa-solid' => array(
						'circle',
						'square',
						'square-full',
						'check',
						'check-circle',
						'minus',
					),
					'fa-regular' => array(
						'circle',
					),
				),
				'skin' => 'inline',
				'condition' => array( 'global_marker' => 'icon' ),
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
					'{{WRAPPER}}' => '--cmsmasters-sitemap-item-counter-type: {{VALUE}};',
				),
				'condition' => array(
					'global_marker' => 'numeric',
					'hierarchical' => '',
				),
			)
		);

		$this->add_control(
			'numb_symbol',
			array(
				'label' => __( 'Number Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( '.', 'cmsmasters-elementor' ),
				'label_block' => false,
				'show_label' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-item-numb-symbol: "{{VALUE}}";',
				),
				'condition' => array(
					'global_marker' => 'numeric',
				),
			)
		);

		$this->add_control(
			'numb_symbol_child',
			array(
				'label' => __( 'Sublevel Number Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( '.', 'cmsmasters-elementor' ),
				'label_block' => false,
				'show_label' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-item-child-numb-symbol: "{{VALUE}}";',
				),
				'condition' => array(
					'global_marker' => 'numeric',
					'hierarchical' => 'yes',
				),
			)
		);

		$this->add_control(
			'marker_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'stacked' => __( 'Stacked', 'cmsmasters-elementor' ),
					'framed' => __( 'Framed', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'prefix_class' => 'cmsmasters-sitemap__view-',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'global_marker',
									'operator' => '===',
									'value' => 'icon',
								),
								array(
									'name' => 'global_icon[value]',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'name' => 'global_marker',
							'operator' => '===',
							'value' => 'numeric',
						),
					),
				),
			)
		);

		$this->add_control(
			'marker_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'circle' => __( 'Circle', 'cmsmasters-elementor' ),
					'square' => __( 'Square', 'cmsmasters-elementor' ),
				),
				'default' => 'circle',
				'prefix_class' => 'cmsmasters-sitemap__shape-',
				'condition' => array( 'marker_view!' => 'default' ),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'global_marker',
									'operator' => '===',
									'value' => 'icon',
								),
								array(
									'name' => 'global_icon[value]',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'name' => 'global_marker',
							'operator' => '===',
							'value' => 'numeric',
						),
					),
				),
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
				'default' => 'h2',
			)
		);

		$this->add_responsive_control(
			'content_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-sitemap__section .elementor-widget-cmsmasters-sitemap__title' => 'text-align: {{VALUE}}',
				),
			)
		);
	}

	/**
	 * Register additional settings tab for widget sitemap.
	 *
	 * Adds additional settings controls for widget sitemap.
	 *
	 * @since 1.3.0
	 */
	protected function register_additional_settings_section() {
		$this->start_controls_section(
			'query_section',
			array(
				'label' => __( 'Additional Options', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'heading_exclude',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Exclude', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'ex_current_post',
			array(
				'label' => __( 'Current Post', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'manual_selection',
			array(
				'label' => __( 'Manual Selection', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => true,
				'type' => CmsmastersControls::QUERY,
				'description' => __( 'Find and select a post to exclude.', 'cmsmasters-elementor' ),
				'options' => array(),
				'multiple' => true,
				'autocomplete' => array(
					'object' => Query_Manager::POST_OBJECT,
					'display' => 'detailed',
				),
				'export' => false,
			)
		);

		$this->add_control(
			'tems_exlude',
			array(
				'label' => __( 'Terms', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => true,
				'type' => CmsmastersControls::QUERY,
				'description' => __( 'Terms are items in a taxonomy. The available taxonomies are: Categories, Tags, Formats and custom taxonomies.', 'cmsmasters-elementor' ),
				'options' => array(),
				'multiple' => true,
				'autocomplete' => array(
					'object' => Query_Manager::CPT_TAX_OBJECT,
					'display' => 'detailed',
				),
				'export' => false,
			)
		);

		$this->add_control(
			'additional_divider',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'password_protected',
			array(
				'label' => __( ' Hide Protected Posts', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'hide_empty',
			array(
				'label' => __( 'Hide Empty Taxonomy', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'add_nofollow',
			array(
				'label' => __( 'Add Nofollow', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'open_new_window',
			array(
				'label' => __( 'Open In New Window', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'hierarchical',
			array(
				'label' => __( 'Hierarchical View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'cmsmasters-sitemap__hierarchical-',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'depth',
			array(
				'label' => __( 'Depth', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0',
				'options' => array(
					'0' => __( 'All', 'cmsmasters-elementor' ),
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
					'6' => 6,
				),
				'condition' => array(
					'hierarchical' => 'yes',
				),
			)
		);

		$this->add_control(
			'text_empty_list',
			array(
				'label' => __( 'Text For Empty List', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'This list is empty.', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register tab style for widget sitemap.
	 *
	 * Adds style section for widget sitemap.
	 *
	 * @since 1.3.0
	 */
	protected function register_style_tab() {
		$this->register_list_style_section();
		$this->register_item_style_section();
		$this->register_icon_style_section();
		$this->register_title_style_section();
	}

	/**
	 * Register widget list style section.
	 *
	 * Adds sitemap widget `list style` settings section controls.
	 *
	 * @since 1.3.0
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
				'size_units' => array( 'px', 'em', '%' ),
				'range' => array(
					'px' => array( 'max' => 50 ),
					'em' => array( 'max' => 5 ),
					'%' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-sitemap-items-gap: calc({{SIZE}}{{UNIT}}/2)',
				),
			)
		);

		$this->add_responsive_control(
			'child_gap',
			array(
				'label' => __( 'Sublevel Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range' => array(
					'px' => array( 'max' => 50 ),
					'em' => array( 'max' => 5 ),
					'%' => array( 'max' => 50 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-sitemap-child-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'hierarchical' => 'yes',
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
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-sitemap__item:not(:first-child) .elementor-widget-cmsmasters-sitemap__link-outer:after' => 'content: ""',
					'{{WRAPPER}} .elementor-widget-cmsmasters-sitemap__item .elementor-widget-cmsmasters-sitemap__item .elementor-widget-cmsmasters-sitemap__link-outer:after' => 'content: ""',
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
					'{{WRAPPER}}' => '--cmsmasters-icon-sitemap-items-divider-style: {{VALUE}}',
				),
				'condition' => array( 'divider' => 'yes' ),
			)
		);

		$this->add_control(
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
					'{{WRAPPER}}' => '--cmsmasters-icon-sitemap-items-divider-weight: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}}' => '--cmsmasters-icon-sitemap-items-divider-color: {{VALUE}}',
				),
				'condition' => array( 'divider' => 'yes' ),
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
					'{{WRAPPER}}' => '--cmsmasters-sitemap-columns-count: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'columns_gap',
			array(
				'label' => __( 'Columns Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range' => array(
					'px' => array( 'max' => 100 ),
					'em' => array( 'max' => 5 ),
					'%' => array( 'max' => 30 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-columns-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'columns!' => '1' ),
			)
		);

		$this->add_responsive_control(
			'rows_gap',
			array(
				'label' => __( 'Rows Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range' => array(
					'px' => array( 'max' => 100 ),
					'em' => array( 'max' => 5 ),
					'%' => array( 'max' => 30 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-rows-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget item style section.
	 *
	 * Adds sitemap widget `item style` settings section controls.
	 *
	 * @since 1.3.0
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
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-sitemap__wrapper a',
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
					'{{WRAPPER}}' => '--cmsmasters-sitemap-item-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-item-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-item-bd-color: {{VALUE}};',
				),
				'condition' => array( 'border_sitemap_item_border!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-sitemap__wrapper a',
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
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-item-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_bg_hover_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-item-bg-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_bd_hover_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-item-bd-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'text_shadow_hover',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-sitemap__wrapper a:hover',
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
					'{{WRAPPER}}' => '--cmsmasters-sitemap-item-text-indent: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'global_marker',
									'operator' => '===',
									'value' => 'icon',
								),
								array(
									'name' => 'global_icon[value]',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'name' => 'global_marker',
							'operator' => '===',
							'value' => 'numeric',
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_sitemap_item',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-sitemap__wrapper a',
				'separator' => 'before',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'sitemap_item_bdr',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-item-bdr: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sitema_item_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-item-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget icon style section.
	 *
	 * Adds sitemap widget `icon style` settings section controls.
	 *
	 * @since 1.3.0
	 */
	protected function register_icon_style_section() {
		$this->start_controls_section(
			'section_icon_style',
			array(
				'label' => __( 'Marker', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'global_marker',
									'operator' => '===',
									'value' => 'icon',
								),
								array(
									'name' => 'global_icon[value]',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'name' => 'global_marker',
							'operator' => '===',
							'value' => 'numeric',
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'number_typography',
				'exclude' => array( 'line_height' ),
				'selector' => '{{WRAPPER}}.cmsmasters-sitemap__marker-numeric .elementor-widget-cmsmasters-sitemap__icon-wrapper',
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
					'px' => array( 'min' => 5 ),
					'em' => array( 'min' => 0.5 ),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-sitemap__marker-icon .elementor-widget-cmsmasters-sitemap__icon-wrapper' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'global_marker' => 'icon' ),
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
					'{{WRAPPER}}' => '--cmsmasters-sitemap-icon-color: {{VALUE}};',
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
					'{{WRAPPER}}' => '--cmsmasters-sitemap-icon-secondary-color: {{VALUE}};',
				),
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'icon_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-sitemap__icon-wrapper',
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
					'{{WRAPPER}}' => '--cmsmasters-sitemap-icon-rotate: rotate({{SIZE}}{{UNIT}});',
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
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-icon-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_hover_secondary_color',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-icon-hover-secondary-color: {{VALUE}};',
				),
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'icon_hover_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-sitemap__link-outer-inner a:hover .elementor-widget-cmsmasters-sitemap__icon-wrapper',
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
					'{{WRAPPER}}' => '--cmsmasters-sitemap-icon-rotate-hover: rotate({{SIZE}}{{UNIT}});',
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
					'{{WRAPPER}}' => '--cmsmasters-sitemap-icon-wrapper: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}}' => '--cmsmasters-sitemap-icon-padding: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}}' => '--cmsmasters-sitemap-icon-border-width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}}' => '--cmsmasters-sitemap-icon-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'marker_view!' => 'default' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register widget title style section.
	 *
	 * Adds sitemap widget `title style` settings section controls.
	 *
	 * @since 1.3.0
	 */
	protected function register_title_style_section() {
		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-sitemap__title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-title-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_bg_color',
			array(
				'label' => __( 'Bacground Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-title-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-title-bd-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-sitemap__title',
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
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-title-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_sitemap_title',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-sitemap__title',
				'separator' => 'before',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'sitemap_title_bdr',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-title-bdr: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'sitema_title_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-sitemap-title-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.3.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['items'] ) ) {
			return;
		}

		$query_args = array();

		$this->add_render_attribute(
			array(
				'wrapper' => array(
					'class' => 'elementor-widget-cmsmasters-sitemap__wrapper',
				),
			)
		);

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';
		foreach ( $settings['items'] as $index => $sitemap_item ) {
			$repeater_item_setting_key = $this->get_repeater_setting_key( 'section', 'repeater-section', $index );

			echo $this->render_sitemap_item( $sitemap_item, $query_args, $repeater_item_setting_key );
		}
		echo '</div>';
	}

	/**
	 * Render sitemap item.
	 *
	 * @since 1.3.0
	 *
	 * @return string Sitemap item HTML.
	 */
	private function render_sitemap_item( $sitemap_item, $query_args, $repeater_item_setting_key ) {
		$settings = $this->get_settings_for_display();

		$hierarchical = 'yes' === $settings['hierarchical'] ? true : false;
		$max_depth = $settings['depth'];
		$query_args['order'] = $sitemap_item['order'];
		$is_taxonomy = 'taxonomy' === $sitemap_item['type_selector'];
		$item_type = $is_taxonomy ? $sitemap_item['source_taxonomy'] : $sitemap_item['source_post_type'];
		$title_tag = $settings['title_tag'];
		$title = $this->get_list_title( $sitemap_item['title'], $item_type, $is_taxonomy );

		$this->add_render_attribute(
			array(
				"{$repeater_item_setting_key}_{$item_type}" => array(
					'class' => array(
						'elementor-widget-cmsmasters-sitemap__section',
						'elementor-repeater-item-' . $sitemap_item['_id'],
					),
				),
				"list_{$item_type}" => array(
					'class' => array(
						'elementor-widget-cmsmasters-sitemap__list',
						"elementor-widget-cmsmasters-sitemap__{$item_type}-list",
					),
				),
				"{$title_tag}_{$item_type}" => array(
					'class' => array(
						'elementor-widget-cmsmasters-sitemap__title',
						"elementor-widget-cmsmasters-sitemap__{$item_type}-title",
					),
				),
				"item_{$item_type}" => array(
					'class' => array(
						'elementor-widget-cmsmasters-sitemap__item',
						"elementor-widget-cmsmasters-sitemap__item-{$item_type}",
					),
				),
			)
		);

		$items_html = '';

		if ( $is_taxonomy ) {
			$items_html .= $this->sitemap_html_taxonomies( $item_type, $hierarchical, $max_depth, $sitemap_item, $query_args );
		} else {
			$items_html .= $this->sitemap_html_post_types( $item_type, $hierarchical, $max_depth, $query_args, $sitemap_item );
		}

		if ( ! $sitemap_item['hide_title'] ) {
			$title = empty( $title ) ? '' : sprintf( '<%s %s>%s</%1$s>', Utils::validate_html_tag( $title_tag ), $this->get_render_attribute_string( "{$title_tag}_{$item_type}" ), wp_kses_post( $title ) );
		} else {
			$title = '';
		}

		$html = sprintf( '<div %s>%s', $this->get_render_attribute_string( "{$repeater_item_setting_key}_{$item_type}" ), $title );
		if ( empty( $items_html ) ) {
			$empty_text = '' !== $settings['text_empty_list'] ? esc_html( $settings['text_empty_list'] ) : __( 'This list is empty.', 'cmsmasters-elementor' );

			$html .= sprintf( '<span %s>%s</span>', $this->get_render_attribute_string( "list_{$item_type}" ), $empty_text );
		} else {
			$html .= sprintf( '<ul %s>%s</ul>', $this->get_render_attribute_string( "list_{$item_type}" ), $items_html );
		}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get list title.
	 *
	 * Retrieves list title.
	 *
	 * @since 1.3.0
	 *
	 * @return string Sitemap list title.
	 */
	private function get_list_title( $current_title, $item_type, $is_taxonomy ) {
		if ( '' !== $current_title ) {
			return $current_title;
		}

		if ( $is_taxonomy ) {
			$obj = get_taxonomy( $item_type );
			if ( false === $obj ) {
				return '';
			}
			return $obj->label;
		}

		$obj = get_post_type_object( $item_type );
		if ( null === $obj ) {
			return '';
		}

		if ( '' === $obj->labels->name ) {
			return $obj->labels->singular_name;
		}

		return $obj->labels->name;
	}

	/**
	 * Get sitemap list taxonomies.
	 *
	 * Retrieves sitemap list taxonomies.
	 *
	 * @since 1.3.0
	 *
	 * @return string Sitemap taxonomies list Html.
	 */
	private function sitemap_html_taxonomies( $taxonomy, $hierarchical, $max_depth, $sitemap_item, $query_args ) {
		$settings = $this->get_settings_for_display();

		$query_args['hide_empty'] = 'yes' === $settings['hide_empty'] ? true : false;
		$query_args['show_option_none'] = '';
		$query_args['taxonomy'] = $taxonomy;
		$query_args['title_li'] = '';
		$query_args['echo'] = false;
		$query_args['depth'] = $max_depth;
		$query_args['hierarchical'] = $hierarchical;
		$query_args['orderby'] = $sitemap_item['orderby_taxonomy'];
		$query_args['exclude'] = $settings['tems_exlude'];

		if ( 'default' === $sitemap_item['type_list'] ) {
			$query_args['offset'] = $sitemap_item['offset_items'];
			$query_args['number'] = $sitemap_item['count_items'];
		}

		if ( 'custom' === $sitemap_item['type_list'] ) {
			$query_args['exclude'] = '';

			$terms_ex_id = array();
			$terms = get_terms( $query_args );

			foreach ( $terms as $term ) {
				$term_id = $term->term_id;

				array_push( $terms_ex_id, $term_id );
			}

			$query_args['exclude'] = $terms_ex_id;
			$query_args['include'] = $sitemap_item['tems_include'];
		}

		$taxonomy_list = wp_list_categories( $query_args );
		$taxonomy_list = $this->add_sitemap_item_classes( "item_{$taxonomy}", $taxonomy_list );

		return $taxonomy_list;
	}

	/**
	 * Get sitemap list posts.
	 *
	 * Retrieves sitemap list posts.
	 *
	 * @since 1.3.0
	 *
	 * @return string Sitemap posts list Html.
	 */
	private function sitemap_html_post_types( $post_type, $hierarchical, $depth, $query_args, $sitemap_item ) {
		$html = '';

		$query_result = $this->query_by_post_type( $post_type, $query_args, $sitemap_item );

		if ( empty( $query_result ) ) {
			return '';
		}

		if ( $query_result->have_posts() ) {
			if ( ! $hierarchical ) {
				$depth = -1;
			}
			$walker = new \Walker_Page();
			$walker->tree_type = $post_type;
			$walker_str = $walker->walk( $query_result->posts, $depth );
			$html .= $this->add_sitemap_item_classes( "item_{$post_type}", $walker_str );
		}

		return $html;
	}

	/**
	 * Get posts.
	 *
	 * Retrieves posts.
	 *
	 * @since 1.3.0
	 *
	 * @return \WP_Query.
	 */
	private function query_by_post_type( $post_type, $query_args, $sitemap_item ) {
		$settings = $this->get_settings_for_display();

		if ( 'yes' === $settings['ex_current_post'] ) {
			$current_post = array( get_the_ID() );
			$manual_selection = '' === $settings['manual_selection'] ? array() : $settings['manual_selection'];

			$exlude_post = array_merge( $current_post, $manual_selection );
		} else {
			$exlude_post = $settings['manual_selection'];
		}

		if ( '' === $sitemap_item['count_items'] ) {
			$count = -1;
		} else {
			$count = $sitemap_item['count_items'];
		}

		$query_args['posts_per_page'] = $count;
		$query_args['update_post_meta_cache'] = false;
		$query_args['post_type'] = $post_type;
		$query_args['filter'] = 'ids';
		$query_args['post_status'] = 'publish';
		$query_args['post__not_in'] = $exlude_post;
		$query_args['has_password'] = 'yes' === $settings['password_protected'] ? false : null;
		$query_args['orderby'] = $sitemap_item['orderby_post_type'];
		$query_args['offset'] = $sitemap_item['offset_items'];

		if ( 'custom' === $sitemap_item['type_list'] ) {
			$query_args['post__not_in'] = '';

			$posts_ex_id = array();
			$posts = get_posts( $query_args );

			foreach ( $posts as $post ) {
				$post_id = $post->ID;

				array_push( $posts_ex_id, $post_id );
			}

			$query_args['post__not_in'] = $posts_ex_id;
			$query_args['post__in'] = $sitemap_item['post_include'];
		}

		$querys = new \WP_Query( $query_args );

		return $querys;
	}

	/**
	 * Add classes & html in sitemap widget.
	 *
	 * @since 1.3.0
	 * @since 1.16.4 Fixed render global icon.
	 *
	 * @return string Sitemap list item new classes & html.
	 */
	private function add_sitemap_item_classes( $element, $str ) {
		$settings = $this->get_settings_for_display();

		if ( $settings['global_icon'] && '' === $settings['global_icon']['value'] ) {
			$icon_html = '';
		} else {
			$icon_html = $this->render_sitemap_icon();
		}

		$start_replace_html = '<div class="elementor-widget-cmsmasters-sitemap__link-outer">
									<div class="elementor-widget-cmsmasters-sitemap__link-outer-inner">
										<a';

		$end_replace_html = "{$icon_html}</a></div></div>";

		$element_str = $this->get_render_attribute_string( $element );
		$element_str = substr_replace( $element_str, ' ', -1, 1 );

		$source = array(
			'class="',
			'<a',
			'</a>',
		);

		$replace = array(
			$element_str,
			$start_replace_html,
			$end_replace_html,
		);

		$nofollow = '';
		$open_new_window = '';

		if ( 'yes' === $settings['add_nofollow'] ) {
			$nofollow = 'rel="nofollow"';
		}

		if ( 'yes' === $settings['open_new_window'] ) {
			$open_new_window = 'target="_blank"';
		}

		if ( 'yes' === $settings['add_nofollow'] || 'yes' === $settings['open_new_window'] ) {
			$source[] = 'href=';
			$replace[] = "{$nofollow} {$open_new_window} href=";
		}

		return str_replace( $source, $replace, $str );
	}

	/**
	 * Get Icon.
	 *
	 * Retrieves Icon.
	 *
	 * @since 1.3.0
	 *
	 * @return string Icon Html.
	 */
	private function render_sitemap_icon() {
		$settings = $this->get_settings_for_display();
		$icon = $settings['global_icon'];

		ob_start();

		echo '<span class="elementor-widget-cmsmasters-sitemap__icon-wrapper">';
			Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
		echo '</span>';

		return ob_get_clean();
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
			'items' => array(
				array(
					'field' => 'title',
					'type' => esc_html__( 'Sitemap Title', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
			),
		);
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
				'field' => 'text_empty_list',
				'type' => esc_html__( 'Sitemap Text For Empty List', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
