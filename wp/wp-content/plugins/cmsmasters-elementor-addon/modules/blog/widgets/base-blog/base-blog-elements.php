<?php
namespace CmsmastersElementor\Modules\Blog\Widgets\Base_Blog;

use CmsmastersElementor\Classes\Separator;
use CmsmastersElementor\Controls_Manager as AddonControls;
use CmsmastersElementor\Modules\AjaxWidget\Module as AjaxWidgetModule;
use CmsmastersElementor\Modules\Blog\Classes\Pagination;
use CmsmastersElementor\Modules\Blog\Module as BlogModule;
use CmsmastersElementor\Modules\Blog\Widgets\Base_Blog\Base_Blog;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;
use CmsmastersElementor\Acf_Utils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Utils as ElementorUtils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon blog widget class.
 *
 * An abstract class to register new Blog widgets.
 *
 * @since 1.0.0
 */
abstract class Base_Blog_Elements extends Base_Blog {

	const FILTER_URL_SEPARATOR = '|';

	/**
	 * Pagination instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Pagination
	 */
	protected $pagination;

	/**
	 * Pagination instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Separator
	 */
	protected $separator_filter;

	/**
	 * Whether blog header needed.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $has_header = true;

	/**
	 * Whether blog pagination needed.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $has_pagination = true;

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
		return parent::get_unique_keywords() + array(
			'template',
			'custom',
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function get_script_depends() {
		return array_merge( array(
			'perfect-scrollbar-js',
			'imagesloaded',
		), parent::get_script_depends() );
	}

	/**
	 * Blog Widget constructor.
	 *
	 * Initializing the widget blog class.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		if ( $this->has_pagination ) {
			$this->pagination = new Pagination( $this, static::QUERY_CONTROL_PREFIX );
		}

		$this->separator_filter = new Separator(
			$this,
			array(
				'name' => 'separator_filter',
				'selector' => '{{WRAPPER}} .cmsmasters-blog-filter-nav-primary > li',
			)
		);

		parent::__construct( $data, $args );
	}

	/**
	 * @since 1.0.0
	 */
	public function register_controls() {
		$this->register_template_section_controls();

		parent::register_controls();

		$this->register_header_section_controls();

		$this->register_style_section_controls();

		if ( $this->has_pagination ) {
			$this->pagination->register_controls_content();
		}
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_template_section_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_style_section_controls() {
		$this->register_controls_style_header();
		$this->register_controls_style_header_filter();

		if ( $this->has_pagination ) {
			$this->pagination->register_controls_style();
		}
	}

	/**
	 * Register header section controls.
	 *
	 * Adds header section controls to widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added headline title visibility control and changed headline controls titles.
	 * @since 1.16.4 Fixed render default text for header title.
	 */
	protected function register_header_section_controls() {
		if ( ! $this->has_header ) {
			return;
		}

		$this->start_controls_section(
			'section_header',
			array(
				'label' => __( 'Headline', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'header_show',
			array(
				'label' => __( 'Headline', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
			)
		);

		$header_title_toggle_default = isset( $this->original_settings['header_title'] ) ?
			( (bool) $this->original_settings['header_title'] ? 'yes' : '' ) :
			'yes';

		$this->add_control(
			'header_title_show',
			array(
				'label' => __( 'Title Visibility', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => $header_title_toggle_default,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'condition' => array(
					'header_show!' => '',
				),
			)
		);

		$this->add_control(
			'header_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'My Posts', 'cmsmasters-elementor' ),
				'condition' => array(
					'header_show!' => '',
					'header_title_show!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'header_title_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-' . ( is_rtl() ? 'right' : 'left' ),
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-' . ( ! is_rtl() ? 'right' : 'left' ),
					),
				),
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( 'header_title_alignment', '{{VALUE}}' ),
				),
				'condition' => array(
					'header_show!' => '',
					'header_title_show!' => '',
					'filter_view' => 'multiple-rows',
				),
			)
		);

		$this->add_responsive_control(
			'header_title_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( 'header_title_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'header_show!' => '',
					'header_title_show!' => '',
					'filter_view' => 'multiple-rows',
				),
			)
		);

		$this->add_control(
			'filter_heading',
			array(
				'label' => __( 'Filter', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'header_show!' => '',
				),
			)
		);

		$this->add_control(
			'header_filter_show',
			array(
				'label' => __( 'Visibility', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'condition' => array(
					'header_show!' => '',
				),
			)
		);

		$this->add_control(
			'header_filter_via_ajax',
			array(
				'label' => __( 'Load via AJAX', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
				),
			)
		);

		$this->add_control(
			'header_filter_save_state',
			array(
				'label' => __( 'Save Filter', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Set Yes to save filter in URL.', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'frontend_available' => true,
				'render_type' => 'none',
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
					'header_filter_via_ajax!' => '',
				),
			)
		);

		$this->add_control(
			'filter_default_text',
			array(
				'label' => __( 'All Items Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'All Posts', 'cmsmasters-elementor' ),
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
				),
			)
		);

		$this->add_control(
			'filter_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => AddonControls::CHOOSE_TEXT,
				'options' => array(
					'query' => __( 'Widget Query', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'custom',
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
				),
			)
		);

		$control_terms_args = $this->get_controls( self::QUERY_CONTROL_PREFIX . '_include_term_ids' );

		unset( $control_terms_args['section'] );
		unset( $control_terms_args['tab'] );
		unset( $control_terms_args['name'] );

		$control_terms_args['label'] = esc_html__( 'Filter Items', 'cmsmasters-elementor' );
		$control_terms_args['condition'] = array(
			'header_show!' => '',
			'header_filter_show!' => '',
			'filter_type' => 'custom',
		);

		$this->add_control( 'filter_' . self::QUERY_CONTROL_PREFIX . '_include_term_ids', $control_terms_args );

		$this->add_control(
			'filter_item_elements',
			array(
				'label' => __( 'Filter Item Elements', 'cmsmasters-elementor' ),
				'label_block' => true,
				'description' => esc_html__( 'Select the elements that will be displayed in each filter item.', 'cmsmasters-elementor' ),
				'type' => AddonControls::SELECTIZE,
				'multiple' => true,
				'options' => array(
					'name' => __( 'Name', 'cmsmasters-elementor' ),
					'image' => __( 'Image', 'cmsmasters-elementor' ),
					'description' => __( 'Description', 'cmsmasters-elementor' ),
				),
				'default' => array( 'name' ),
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
				),
			)
		);

		if ( class_exists( '\acf' ) ) {
			$this->add_control(
				'filter_item_image',
				array(
					'label' => __( 'Filter Item Image', 'cmsmasters-elementor' ),
					'description' => __( 'Select the ACF image field for the taxonomy you are displaying in the filter.', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'groups' => Acf_Utils::get_control_options( array( 'image' ) ),
					'condition' => array(
						'header_show!' => '',
						'header_filter_show!' => '',
						'filter_item_elements' => 'image',
					),
				)
			);
		}

		$this->add_control(
			'filter_all_items_image',
			array(
				'label' => __( 'All Items Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
					'filter_item_elements' => 'image',
				),
			)
		);

		$this->add_control(
			'filter_all_items_description',
			array(
				'label' => __( 'All Items Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
					'filter_item_elements' => 'description',
				),
			)
		);

		$this->add_control(
			'filter_view',
			array(
				'label' => __( 'Filter View', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => AddonControls::CHOOSE_TEXT,
				'options' => array(
					'single-row' => __( 'Single Row', 'cmsmasters-elementor' ),
					'multiple-rows' => __( 'Multiple Rows', 'cmsmasters-elementor' ),
				),
				'default' => 'single-row',
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
				),
			)
		);

		$this->add_control(
			'filter_multiple_rows_layout',
			array(
				'label' => __( 'Filter Layout', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => AddonControls::CHOOSE_TEXT,
				'options' => array(
					'flex' => __( 'Flex', 'cmsmasters-elementor' ),
					'grid' => __( 'Grid', 'cmsmasters-elementor' ),
				),
				'default' => 'flex',
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
					'filter_view' => 'multiple-rows',
				),
			)
		);

		$this->add_responsive_control(
			'filter_multiple_rows_grid_columns',
			array(
				'label' => __( 'Filter Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( 'filter_multiple_rows_grid_columns', '{{SIZE}}' ),
				),
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
					'filter_view' => 'multiple-rows',
					'filter_multiple_rows_layout' => 'grid',
				),
			)
		);

		$this->add_responsive_control(
			'filter_multiple_rows_alignment',
			array(
				'label' => __( 'Filter Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-' . ( is_rtl() ? 'right' : 'left' ),
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-' . ( ! is_rtl() ? 'right' : 'left' ),
					),
				),
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( 'filter_multiple_rows_alignment', '{{VALUE}}' ),
				),
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
					'filter_view' => 'multiple-rows',
					'filter_multiple_rows_layout' => 'flex',
				),
			)
		);

		$this->add_responsive_control(
			'filter_item_layout',
			array(
				'label' => __( 'Filter Item Layout', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'row' => array(
						'title' => esc_html__( 'Horizontal', 'cmsmasters-elementor' ),
						'icon' => 'eicon-ellipsis-h',
					),
					'column' => array(
						'title' => esc_html__( 'Vertical', 'cmsmasters-elementor' ),
						'icon' => 'eicon-ellipsis-v',
					),
				),
				'default' => 'row',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( 'filter_item_layout', '{{VALUE}}' ),
				),
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'filter_item_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-' . ( is_rtl() ? 'right' : 'left' ),
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-' . ( ! is_rtl() ? 'right' : 'left' ),
					),
				),
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( 'filter_item_alignment', '{{VALUE}}' ),
				),
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
					'filter_item_layout' => 'column',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_style_header() {
		if ( ! $this->has_header ) {
			return;
		}

		$selector = '{{WRAPPER}} .cmsmasters-blog-header';

		$this->start_controls_section(
			'section_header_style',
			array(
				'label' => __( 'Headline', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'header_show!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'header_text_align',
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
					'{{WRAPPER}} .cmsmasters-blog-header-title' => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'header_show!' => '',
					'header_filter_show' => '',
				),
			)
		);

		$this->add_control(
			'header_height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 30,
						'max' => 200,
						'step' => 1,
					),
					'vh' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'size_units' => array( 'px', 'vh', 'vw' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog' => '--cmsmasters-header-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'header_show!' => '',
					'filter_view!' => 'multiple-rows',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'header',
				'selector' => $selector,
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => __( 'Disable', 'cmsmasters-elementor' ),
							'solid' => __( 'Solid', 'cmsmasters-elementor' ),
							'double' => __( 'Double', 'cmsmasters-elementor' ),
							'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
							'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
							'groove' => __( 'Groove', 'cmsmasters-elementor' ),
						),
					),
					'width' => array(
						'condition' => array(
							'border!' => array( '', 'none' ),
						),
					),
					'color' => array(
						'condition' => array(
							'border!' => array( '', 'none' ),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'header_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => array(
					'header_show!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'header_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					$selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'separator' => 'after',
				'condition' => array(
					'header_show!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_header_title',
				'selector' => '{{WRAPPER}} .cmsmasters-blog-header-title',
				'exclude' => array( 'line_height' ),
			)
		);

		$this->add_control(
			'header_color_text',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog-header-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog-header' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_style_header_filter() {
		if ( ! $this->has_header ) {
			return;
		}

		$this->start_controls_section(
			'section_header_filter_style',
			array(
				'label' => __( 'Headline Filter', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'header_filter_columns_gap',
			array(
				'label' => __( 'Columns Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( 'header_filter_columns_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'filter_view' => 'multiple-rows',
				),
			)
		);

		$this->add_responsive_control(
			'header_filter_rows_gap',
			array(
				'label' => __( 'Rows Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( 'header_filter_rows_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'filter_view' => 'multiple-rows',
				),
			)
		);

		$this->add_responsive_control(
			'header_filter_item_elements_gap',
			array(
				'label' => __( 'Item Elements Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( 'header_filter_item_elements_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'header_filter_image_width',
			array(
				'label' => __( 'Image Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( 'header_filter_image_width', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
					'filter_item_elements' => 'image',
				),
			)
		);

		$this->add_responsive_control(
			'header_filter_image_margin',
			array(
				'label' => __( 'Image Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( 'header_filter_image_margin_top', '{{TOP}}{{UNIT}}' ) .
					Utils::prepare_css_var( 'header_filter_image_margin_right', '{{RIGHT}}{{UNIT}}' ) .
					Utils::prepare_css_var( 'header_filter_image_margin_bottom', '{{BOTTOM}}{{UNIT}}' ) .
					Utils::prepare_css_var( 'header_filter_image_margin_left', '{{LEFT}}{{UNIT}}' ),
				),
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
					'filter_item_elements' => 'image',
				),
			)
		);

		$this->add_responsive_control(
			'header_filter_description_margin',
			array(
				'label' => __( 'Description Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( 'header_filter_description_margin_top', '{{TOP}}{{UNIT}}' ) .
					Utils::prepare_css_var( 'header_filter_description_margin_right', '{{RIGHT}}{{UNIT}}' ) .
					Utils::prepare_css_var( 'header_filter_description_margin_bottom', '{{BOTTOM}}{{UNIT}}' ) .
					Utils::prepare_css_var( 'header_filter_description_margin_left', '{{LEFT}}{{UNIT}}' ),
				),
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
					'filter_item_elements' => 'description',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_header_filter',
				'selector' => '{{WRAPPER}} .cmsmasters-blog-filter-nav-primary a,' .
					'{{WRAPPER}} .cmsmasters-blog-filter-nav-secondary-trigger',
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
				),
				'exclude' => array(
					'line_height',
					'text_decoration',
				),
			)
		);

		$this->add_group_control(
			AddonControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'header_filter_description',
				'label' => __( 'Description Typography', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .cmsmasters-blog',
				'condition' => array(
					'header_show!' => '',
					'header_filter_show!' => '',
					'filter_item_elements' => 'description',
				),
				'exclude' => array(
					'line_height',
					'text_decoration',
				),
			)
		);

		$this->start_controls_tabs(
			'tabs_header_filter_style',
			array(
				'condition' => array(
					'header_filter_show!' => '',
				),
			)
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'active' => __( 'Active', 'cmsmasters-elementor' ),
		) as $type => $label ) {
			$selector_loop_link_normal = '{{WRAPPER}} .cmsmasters-blog-filter-nav-primary a,' .
				'{{WRAPPER}} .cmsmasters-blog-filter-nav-secondary-trigger';
			$selector_loop_link = $selector_loop_link_normal;

			switch ( $type ) {
				case 'hover':
					$selector_loop_link = '{{WRAPPER}} .cmsmasters-blog-filter-nav-primary a:hover,' .
						'{{WRAPPER}} .cmsmasters-blog-filter-nav-secondary-trigger:hover';

					break;
				case 'active':
					$selector_loop_link = '{{WRAPPER}} .cmsmasters-blog-filter-nav-primary .term-link-active';

					break;
			}

			$this->start_controls_tab(
				"header_filter_tab_{$type}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"header_filter_color_{$type}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_loop_link => 'color: {{VALUE}};',
					),
					'condition' => array(
						'header_filter_show!' => '',
					),
				)
			);

			$this->add_control(
				"header_filter_description_color_{$type}",
				array(
					'label' => __( 'Description Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( "header_filter_description_color_{$type}", '{{VALUE}}' ),
					),
					'condition' => array(
						'header_filter_show!' => '',
						'filter_item_elements' => 'description',
					),
				)
			);

			$this->add_control(
				"header_filter_bg_{$type}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_loop_link => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						'header_filter_show!' => '',
					),
				)
			);

			$this->add_control(
				"header_filter_bd_color_{$type}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( "header_filter_bd_color_{$type}", '{{VALUE}}' ),
					),
					'condition' => array(
						'header_filter_show!' => '',
						'header_filter_bd_border!' => 'none',
					),
				)
			);

			$this->add_control(
				"header_filter_bd_radius_{$type}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}} .cmsmasters-blog' => Utils::prepare_css_var( "header_filter_bd_radius_{$type}", '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}' ),
					),
				)
			);

			$this->add_control(
				"header_filter_text_decoration_{$type}",
				array(
					'label' => __( 'Text Decoration', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => '',
					'options' => array(
						'' => __( 'Default', 'cmsmasters-elementor' ),
						'none' => __( 'None', 'cmsmasters-elementor' ),
						'underline' => __( 'Underline', 'cmsmasters-elementor' ),
						'overline' => __( 'Overline', 'cmsmasters-elementor' ),
						'line-through' => __( 'Line Through', 'cmsmasters-elementor' ),
					),
					'selectors' => array(
						$selector_loop_link => 'text-decoration: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				AddonControls::VARS_BOX_SHADOW_GROUP,
				array(
					'name' => "header_filter_{$type}",
					'selector' => '{{WRAPPER}} .cmsmasters-blog',
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'label' => __( 'Text Shadow', 'cmsmasters-elementor' ),
					'name' => "header_filter_text_shadow_{$type}",
					'selector' => $selector_loop_link,
				)
			);

			if ( 'hover' === $type ) {
				$this->add_control(
					"header_filter_anim_dur_{$type}",
					array(
						'label' => __( 'Animation Duration', 'cmsmasters-elementor' ) . ' (ms)',
						'type' => Controls_Manager::SLIDER,
						'range' => array(
							'px' => array(
								'min' => 0,
								'max' => 3000,
							),
						),
						'selectors' => array(
							$selector_loop_link_normal => 'transition-duration: {{SIZE}}ms',
						),
					)
				);
			}

			if ( 'hover' === $type && 'active' === $type ) {
				$this->add_control(
					"header_opacity_{$type}",
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
							$selector_loop_link_normal => 'opacity: {{SIZE}};',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			AddonControls::VARS_BORDER_GROUP,
			array(
				'name' => 'header_filter_bd',
				'selector' => '{{WRAPPER}} .cmsmasters-blog',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array( 'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ) ),
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'header_filter_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog-filter-nav-primary a,' .
					'{{WRAPPER}} .cmsmasters-blog-filter-nav-secondary-trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->separator_filter->add_controls( array(
			'filter_view!' => 'multiple-rows',
		) );

		$this->end_controls_section();
	}

	/**
	 * Render widget on ajax.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public function render_ajax( $ajax_vars ) {
		$query_vars = isset( $ajax_vars['query_vars'] ) ? BlogModule::get_allowed_query_vars( $ajax_vars['query_vars'] ) : array();

		if ( ! empty( $query_vars ) ) {
			$this->set_query_vars( $query_vars );
		}

		$this->init_query();

		if ( ! $this->get_query()->found_posts ) {
			wp_die( 0, '', 404 );
		}

		$this->render_posts();
		$this->render_pagination();
	}

	/**
	 * Render pagination.
	 *
	 * @since 1.0.0
	 */
	protected function render_pagination() {
		if ( ! $this->has_pagination ) {
			return;
		}

		$this->pagination->set_wp_query( $this->get_query() );
		$this->pagination->render();
	}

	/**
	 * @since 1.0.0
	 */
	protected function render_blog() {
		$this->render_header();

		echo '<div class="cmsmasters-blog__posts-variable">';

		$this->render_posts();
		$this->render_pagination();

		echo '</div>';
	}

	/**
	 * Render header.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Render header-title with fallback.
	 * @since 1.1.0 Added headline title visibility control.
	 * @since 1.12.1 Fixed displaying headline title when enabling title and hiding filter.
	 */
	protected function render_header() {
		if ( ! $this->has_header ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		$header_show = ( isset( $settings['header_show'] ) ? $settings['header_show'] : '' );
		$header_title_show = ( isset( $settings['header_title_show'] ) ? $settings['header_title_show'] : '' );
		$header_filter_show = ( isset( $settings['header_filter_show'] ) ? $settings['header_filter_show'] : '' );

		if ( ! $header_show || ( $header_show && ! $header_title_show && ! $header_filter_show ) ) {
			return;
		}

		$filter_view = ( isset( $settings['filter_view'] ) ? $settings['filter_view'] : '' );
		$filter_multiple_rows_layout = ( isset( $settings['filter_multiple_rows_layout'] ) ? $settings['filter_multiple_rows_layout'] : '' );
		$multiple_rows = ( 'multiple-rows' === $filter_view ? ' cmsmasters-blog-filter-nav-multiple-rows-' . $filter_multiple_rows_layout : '' );

		echo '<div class="cmsmasters-blog-header' . esc_attr( $multiple_rows ) . '">
			<div class="cmsmasters-blog-header-inner">';

				if ( $settings['header_title_show'] ) {
					printf(
						'<%1$s class="cmsmasters-blog-header-title">%2$s</%1$s>',
						'h3',
						esc_html( $this->get_settings_fallback( 'header_title' ) )
					);
				}

				$this->render_filter();

			echo '</div>
		</div>';
	}

	/**
	 * Render header filter.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Render 'Filter Default Text' with fallback.
	 * @since 1.19.4 Fixed active link term without `Load via AJAX`. Fixed displaying all categories
	 * when using filter type query.
	 */
	protected function render_filter() {
		$settings = $this->get_settings_for_display();

		if ( ! $settings['header_filter_show'] ) {
			return;
		}

		$filter_type = $this->get_settings_for_display( 'filter_type' );
		$control_id = self::QUERY_CONTROL_PREFIX . '_include_term_ids';

		if ( 'custom' === $filter_type ) {
			$control_id = "filter_{$control_id}";
		}

		$term_ids = $this->get_settings_for_display( $control_id );
		$default_category = get_option( 'default_category' );
		$filter_data = $this->get_filter_data();

		if ( 'query' === $filter_type && empty( $term_ids ) ) {
			$post_type = $this->get_settings_for_display( self::QUERY_CONTROL_PREFIX . '_post_type' );

			$all_taxonomies = get_object_taxonomies( $post_type, 'objects' );

			$category_taxonomies = array();

			foreach ( $all_taxonomies as $taxonomy => $tax_obj ) {
				if ( $tax_obj->hierarchical ) {
					$category_taxonomies[] = $taxonomy;
				}
			}

			if ( ! empty( $category_taxonomies ) ) {
				$all_terms = get_terms( array(
					'taxonomy' => $category_taxonomies,
					'hide_empty' => true,
					'fields' => 'ids',
				));

				if ( ! is_wp_error( $all_terms ) ) {
					$term_ids = $all_terms;
				}
			}
		}

		if ( 'custom' === $filter_type && empty( $term_ids ) && $default_category ) {
			$term_ids = array( $default_category );
		}

		if ( ! $term_ids ) {
			return;
		}

		$filter_item_elements = $settings['filter_item_elements'];

		echo '<div class="cmsmasters-blog-filter">
			<ul class="cmsmasters-blog-filter-nav cmsmasters-blog-filter-nav-primary">
				<li>';

					$term_link_attrs = array(
						'class' => array( 'term-link', 'term-link-reset' ),
						'title' => esc_html__( 'All Posts', 'cmsmasters-elementor' ),
						'href' => add_query_arg(
							array(
								$this->get_filter_var_name() => null,
								$this->pagination->get_paged_name() => null,
							),
							( Utils::is_ajax() ? wp_get_referer() : false )
						),
					);

					if ( empty( $filter_data ) ) {
						$term_link_attrs['class'][] = 'term-link-active';
					}

					echo '<a ' . ElementorUtils::render_html_attributes( $term_link_attrs ) . ' tabindex="0">';

						$filter_all_items_out = '';

						if ( ! empty( $filter_item_elements ) ) {
							foreach ( $filter_item_elements as $filter_item_element ) {
								if ( 'name' === $filter_item_element ) {
									$filter_all_items_out .= '<span class="cmsmasters-blog-filter__item-name">' . esc_html( $this->get_settings_for_display( 'filter_default_text' ) ) . '</span>';
								}

								if ( 'description' === $filter_item_element && ! empty( $settings['filter_all_items_description'] ) ) {
									$filter_all_items_out .= '<span class="cmsmasters-blog-filter__item-description">' . esc_html( $settings['filter_all_items_description'] ) . '</span>';
								}

								if ( 'image' === $filter_item_element ) {
									$filter_all_items_out .= '<span class="cmsmasters-blog-filter__item-image">' .
										Group_Control_Image_Size::get_attachment_image_html( array(
											'filter_all_items_image' => $settings['filter_all_items_image'],
											'filter_all_items_image_size' => 'full',
										), 'filter_all_items_image' ) .
									'</span>';
								}
							}
						}

						if ( empty( $filter_all_items_out ) ) {
							$filter_all_items_out = '<span class="cmsmasters-blog-filter__item-name">' . esc_html( $this->get_settings_for_display( 'filter_default_text' ) ) . '</span>';
						}

						ElementorUtils::print_unescaped_internal_string( $filter_all_items_out ); // XSS ok.

					echo '</a>';

					if ( 'multiple-rows' !== $settings['filter_view'] ) {
						$this->separator_filter->render();
					}

				echo '</li>';

				foreach ( $term_ids as $term_id ) {
					$term = get_term( $term_id );

					if ( ! $term || empty( $term ) ) {
						continue;
					}

					$args = array(
						'post_type' => $this->get_settings_for_display( self::QUERY_CONTROL_PREFIX . '_post_type' ),
						'tax_query' => array(
							array(
								'taxonomy' => $term->taxonomy,
								'field' => 'term_id',
								'terms' => $term->term_id,
							),
						),
						'post_status' => 'publish',
						'posts_per_page' => 1,
					);

					$posts_query = new \WP_Query( $args );

					if ( $posts_query->have_posts() ) {
						$page_url = false;

						if ( is_admin() ) {
							$post_id = Utils::get_document_id();
							$page_url = Plugin::elementor()->documents->get( $post_id )->get_wp_preview_url();
						} elseif ( Utils::is_ajax() ) {
							$page_url = wp_get_referer();
						}

						$href = add_query_arg(
							array(
								$this->get_filter_var_name() => $term->taxonomy . static::FILTER_URL_SEPARATOR . $term->term_id,
								$this->pagination->get_paged_name() => null,
							),
							$page_url
						);

						$term_link_attrs = array(
							'class' => array( 'term-link' ),
							'title' => get_taxonomy( $term->taxonomy )->labels->singular_name,
							'data-taxonomy' => $term->taxonomy,
							'data-term-id' => $term->term_id,
							'href' => esc_url( $href ),
						);

						if (
							$filter_data &&
							$term->taxonomy === $filter_data['taxonomy'] &&
							$term->term_id === (int) $filter_data['term_id']
						) {
							$term_link_attrs['class'][] = 'term-link-active';
						}

						echo '<li>' .
							'<a ' . ElementorUtils::render_html_attributes( $term_link_attrs ) . ' tabindex="0">';

								$filter_item_out = '';

								if ( ! empty( $filter_item_elements ) ) {
									foreach ( $filter_item_elements as $filter_item_element ) {
										if ( 'name' === $filter_item_element ) {
											$filter_item_out .= '<span class="cmsmasters-blog-filter__item-name">' . esc_html( $term->name ) . '</span>';
										}

										if ( 'description' === $filter_item_element && ! empty( $term->description ) ) {
											$filter_item_out .= '<span class="cmsmasters-blog-filter__item-description">' . esc_html( $term->description ) . '</span>';
										}

										if ( class_exists( '\acf' ) && 'image' === $filter_item_element ) {
											$filter_item_out .= $this->get_filter_item_image( $term, $settings['filter_item_image'] );
										}
									}
								}

								if ( empty( $filter_item_out ) ) {
									$filter_item_out = '<span class="cmsmasters-blog-filter__item-name">' . esc_html( $term->name ) . '</span>';
								}

								ElementorUtils::print_unescaped_internal_string( $filter_item_out ); // XSS ok.

							echo '</a>';

							if ( 'multiple-rows' !== $settings['filter_view'] && next( $term_ids ) ) {
								$this->separator_filter->render();
							}

						echo '</li>';
					}

					wp_reset_query();
				}

			echo '</ul>';

			if ( 'multiple-rows' !== $settings['filter_view'] ) {
				echo '<ul class="cmsmasters-blog-filter-nav cmsmasters-blog-filter-nav-secondary"></ul>
				<a href="#" class="cmsmasters-blog-filter-nav-secondary-trigger" aria-label="Filter navigation trigger">';
					Utils::render_icon(
						array(
							'value' => 'fas fa-ellipsis-v',
							'library' => 'fa-solid',
						),
						array(
							'aria-hidden' => 'true',
							'aria-label' => 'Filter Trigger',
						)
					);
				echo '</a>';
			}

		echo '</div>';
	}

	/**
	 * Get filter item image.
	 *
	 * @since 1.11.1
	 *
	 * @param object $term Taxonomy term.
	 * @param string $key ACF key.
	 *
	 * @return string Filter item image.
	 */
	protected function get_filter_item_image( $term, $key = '' ) {
		if ( ! function_exists( 'get_field_object' ) || empty( $key ) || ! is_object( $term ) ) {
			return '';
		}

		$keys = array_reverse( explode( ':', $key ) );

		list( $meta_key, $field_key ) = array_pad( $keys, 2, false );

		$field = get_field_object( $meta_key, $term );

		if ( empty( $field ) ) {
			return '';
		}

		$settings = array(
			'image' => array(
				'id' => null,
				'url' => '',
			),
			'image_size' => 'full',
		);

		if ( 'array' === $field['return_format'] ) {
			$settings['image']['id'] = $field['value']['id'];
			$settings['image']['url'] = $field['value']['url'];
		} elseif ( 'url' === $field['return_format'] ) {
			$settings['image']['url'] = $field['value'];
		} elseif ( 'id' === $field['return_format'] ) {
			$settings['image']['id'] = $field['value'];
		}

		if ( empty( $settings['image']['id'] ) && empty( $settings['image']['url'] ) ) {
			return '';
		}

		return '<span class="cmsmasters-blog-filter__item-image">' . Group_Control_Image_Size::get_attachment_image_html( $settings, 'image' ) . '</span>';
	}

	/**
	 * Get query vars.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_query_vars() {
		$query_vars = parent::get_query_vars();
		$filter_data = $this->get_filter_data();

		if ( $this->has_pagination && ! AjaxWidgetModule::is_active_ajax() ) {
			$query_vars['paged'] = $this->pagination->get_paged();
		}

		if ( $filter_data ) {
			$query_vars['tax_query'] = array(
				array(
					'field' => 'term_id',
					'taxonomy' => $filter_data['taxonomy'],
					'terms' => array( $filter_data['term_id'] ),
				),
			);
		}

		return $query_vars;
	}

	/**
	 * Get query var for filter.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_filter_var_name() {
		return "cmsmasters-filter-{$this->get_ID()}";
	}

	/**
	 * Get current filter by query.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_filter_data() {
		list( $taxonomy, $term_id ) = array_pad(
			explode( static::FILTER_URL_SEPARATOR, Utils::get_if_isset( $_GET, $this->get_filter_var_name(), '' ) ),
			2,
			false
		);

		if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
			return array();
		}

		$term = get_term( (int) $term_id, $taxonomy );

		if ( ! $term || is_wp_error( $term ) ) {
			return array();
		}

		return array(
			'term_id' => $term_id,
			'taxonomy' => $taxonomy,
		);
	}
}
