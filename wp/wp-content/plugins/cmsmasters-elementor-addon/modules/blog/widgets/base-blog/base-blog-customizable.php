<?php
namespace CmsmastersElementor\Modules\Blog\Widgets\Base_Blog;

use CmsmastersElementor\Controls_Manager as CmsmastersManagerControls;
use CmsmastersElementor\Controls\Groups\Group_Control_Button_Background;
use CmsmastersElementor\Modules\Blog\Widgets\Base_Blog\Base_Blog_Elements;
use CmsmastersElementor\Modules\MetaData\Classes\Meta_Data;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin as ElementorPlugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Addon blog widget class.
 *
 * An abstract class to register new Blog widgets.
 *
 * @since 1.0.0
 */
abstract class Base_Blog_Customizable extends Base_Blog_Elements {

	const META_DATA_TOP = 'meta_data_top';
	const META_DATA_BOTTOM = 'meta_data_bottom';


	/**
	 * Meta Data instance.
	 *
	 * Holds the meta-data instance.
	 *
	 * @var Meta_Data
	 */
	private $meta_data_top;


	/**
	 * Meta Data instance.
	 *
	 * Holds the meta-data instance.
	 *
	 * @var Meta_Data
	 */
	private $meta_data_bottom;

	/**
	 * Displays a blog at the template ID.
	 *
	 * @var int
	 */
	protected $template_id = false;

	/**
	 * @since 1.0.0
	 * @since 1.12.1 Add checking template.
	 */
	protected function init( $data ) {
		parent::init( $data );

		$template_id = (int) $this->get_settings_for_display( 'blog_template_id' );

		if ( Utils::check_template( $template_id ) ) {
			$this->template_id = $template_id;
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function register_controls() {
		parent::register_controls();

		$this->injection_section_layout();
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_style_section_controls() {
		$this->register_section_style_post();
		$this->register_section_style_post_main_content();
		$this->register_section_style_meta_data_top();
		$this->register_section_style_meta_data_bottom();
		$this->register_section_style_read_mode();

		parent::register_style_section_controls();
	}

	/**
	 * Get HTML wrapper class.
	 *
	 * Retrieve the widget container class. Can be used to override the
	 * container class for specific widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_html_wrapper_class() {
		return parent::get_html_wrapper_class() . ' elementor-widget-cmsmasters-blog-similar';
	}

	/**
	 * @since 1.0.0
	 */
	protected function init_controls() {
		parent::init_controls();

		$this->update_controls_hide_when_default();
		$this->update_controls_border_columns();
	}

	/**
	 * @since 1.0.0
	 */
	public function get_blog_classes() {
		return array_merge( parent::get_blog_classes(), array( static::get_css_class() ) );
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
		$this->meta_data_top = new Meta_Data(
			$this,
			array(
				'name' => self::META_DATA_TOP,
			)
		);

		$this->meta_data_bottom = new Meta_Data(
			$this,
			array(
				'name' => self::META_DATA_BOTTOM,
			)
		);

		parent::__construct( $data, $args );
	}

	/**
	 * Get template IDs.
	 *
	 * @since 1.11.1
	 *
	 * @return array Template IDs.
	 */
	public function get_template_ids() {
		if ( $this->is_setting_as_default( 'blog_layout' ) ) {
			return array();
		}

		if ( empty( $this->template_id ) ) {
			return array();
		}

		return array( $this->template_id );
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
		if ( ! $this->is_setting_as_default( 'blog_layout' ) ) {
			$template_ids = $this->get_template_ids();

			if ( empty( $template_ids ) ) {
				if ( is_admin() ) {
					/* translators: Blog widgets undefined template warning. %s: Blog widget title */
					Utils::render_alert( sprintf( esc_html__( 'Please choose your custom "%s" widget template!', 'cmsmasters-elementor' ), $this->get_title() ) );
				}

				return;
			}

			if ( 'enable' !== $this->lazyload_widget_get_status() ) {
				Plugin::instance()->frontend->print_template_css( $template_ids, $this->get_id() );
			}
		}

		parent::render();
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	public function injection_section_layout() {
		$this->start_injection( array(
			'of' => 'section_layout',
			'at' => 'start',
			'type' => 'section',
		) );

		$this->add_control(
			'blog_layout',
			array(
				'label' => esc_html__( 'Blog Layout', 'cmsmasters-elementor' ),
				'type' => CmsmastersManagerControls::CHOOSE_TEXT,
				'default' => 'default',
				'options' => array(
					'default' => esc_html__( 'Default', 'cmsmasters-elementor' ),
					'custom' => esc_html__( 'Custom', 'cmsmasters-elementor' ),
				),
				'prefix_class' => 'cmsmasters-blog--layout-',
				'frontend_available' => true,
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'blog_template_id',
			array(
				'label' => __( 'Choose Entry Template', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => CmsmastersManagerControls::QUERY,
				'autocomplete' => array(
					'object' => Query_Manager::TEMPLATE_OBJECT,
					'query' => array(
						'meta_query' => array(
							array(
								'key' => Document::TYPE_META_KEY,
								'value' => 'cmsmasters_entry',
							),
						),
					),
				),
				'condition' => array(
					'blog_layout' => array( 'custom' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'alignment',
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
				'prefix_class' => 'cmsmasters-align%s--',
			)
		);

		$this->register_controls_content_thumbnail();
		$this->register_controls_content_meta_data_top();
		$this->register_controls_content_heading();
		$this->register_controls_content_excerpt();
		$this->register_controls_content_read_more();
		$this->register_controls_content_meta_data_bottom();

		$this->end_injection();
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Image size changed from "full" to "medium_large" by default.
	 */
	protected function register_controls_content_thumbnail() {
		$this->add_control(
			'thumbnail_heading',
			array(
				'label' => __( 'Featured Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_control(
			'thumbnail_show',
			array(
				'label' => __( 'Featured Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_control(
			'image_ratio_switcher',
			array(
				'label' => __( 'Custom Image Ratio', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'return_value' => 'custom-image-ratio',
				'prefix_class' => 'cmsmasters--',
				'render_type' => 'ui',
				'default' => '',
				'condition' => array(
					'blog_layout' => 'default',
					'thumbnail_show!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'image_ratio',
			array(
				'label' => __( 'Image Ratio', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0.1,
						'max' => 2,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					$this->get_blog_selector() => '--cmsmasters-image-ratio: {{SIZE}};',
				),
				'condition' => array(
					'blog_layout' => 'default',
					'thumbnail_show!' => '',
					'image_ratio_switcher!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'thumbnail',
				'default' => 'medium_large',
				'condition' => array(
					'blog_layout' => 'default',
					'thumbnail_show!' => '',
				),
			)
		);
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_content_meta_data_top() {
		$this->meta_data_top->register_controls_content();

		$this->update_control(
			self::META_DATA_TOP . '_show',
			array(
				'label' => __( 'Meta Data Top Area', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->update_control(
			self::META_DATA_TOP . '_options',
			array(
				'default' => array( 'category' ),
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Add truncate text.
	 * @since 1.2.3 Fix for line-clamp css property.
	 */
	protected function register_controls_content_heading() {
		$this->add_control(
			'post_title_heading',
			array(
				'label' => __( 'Post Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_control(
			'heading_show',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_control(
			'heading_tag',
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
				'condition' => array(
					'blog_layout' => 'default',
					'heading_show!' => '',
				),
			)
		);

		$this->add_control(
			'post_heading_rows',
			array(
				'label' => __( 'Truncate Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'prefix_class' => 'cmsmasters-heading-line-clamp-',
				'condition' => array(
					'blog_layout' => 'default',
					'heading_show!' => '',
				),
			)
		);

		$this->add_control(
			'post_heading_rows_count',
			array(
				'label' => __( 'Number of Lines', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 2,
				'min' => 1,
				'max' => 5,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post-title a' => '-webkit-line-clamp: {{SIZE}};',
				),
				'condition' => array(
					'blog_layout' => 'default',
					'heading_show!' => '',
					'post_heading_rows!' => '',
				),
			)
		);
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Add truncate text.
	 * @since 1.2.3 Fix for line-clamp css property.
	 */
	protected function register_controls_content_excerpt() {
		$this->add_control(
			'excerpt_heading',
			array(
				'label' => __( 'Post Excerpt', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_control(
			'excerpt_show',
			array(
				'label' => __( 'Excerpt', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label' => __( 'Length', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'placeholder' => 25,
				'condition' => array(
					'blog_layout' => 'default',
					'excerpt_show!' => '',
				),
			)
		);

		$this->add_control(
			'post_excerpt_rows',
			array(
				'label' => __( 'Truncate Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
				'prefix_class' => 'cmsmasters-excerpt-line-clamp-',
				'condition' => array(
					'blog_layout' => 'default',
					'excerpt_show!' => '',
				),
			)
		);

		$this->add_control(
			'post_excerpt_rows_count',
			array(
				'label' => __( 'Number of Lines', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 4,
				'min' => 1,
				'max' => 6,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post-excerpt' => '-webkit-line-clamp: {{SIZE}};',
				),
				'condition' => array(
					'blog_layout' => 'default',
					'excerpt_show!' => '',
					'post_excerpt_rows!' => '',
				),
			)
		);
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	public function register_controls_content_read_more() {
		$this->add_control(
			'read_more_heading',
			array(
				'label' => __( 'Read More', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_control(
			'read_more_show',
			array(
				'label' => __( 'Read More', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_control(
			'read_more_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Read More', 'cmsmasters-elementor' ),
				'condition' => array(
					'blog_layout' => 'default',
					'read_more_show!' => '',
				),
			)
		);

		$this->add_control(
			'read_more_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => true,
				'condition' => array(
					'blog_layout' => 'default',
					'read_more_show!' => '',
				),
			)
		);

		$this->add_control(
			'read_more_icon_align',
			array(
				'label' => __( 'Icon Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersManagerControls::CHOOSE_TEXT,
				'label_block' => false,
				'default' => 'after',
				'options' => array(
					'before' => __( 'Before', 'cmsmasters-elementor' ),
					'after' => __( 'After', 'cmsmasters-elementor' ),
				),
				'prefix_class' => 'cmsmasters-read-more-align-',
				'selectors_dictionary' => array(
					'before' => 'row',
					'after' => 'row-reverse',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post__read_more' => 'flex-direction: {{VALUE}}',
				),
				'condition' => array(
					'blog_layout' => 'default',
					'read_more_icon[value]!' => '',
					'read_more_show!' => '',
				),
			)
		);
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_content_meta_data_bottom() {
		$this->meta_data_bottom->register_controls_content();

		$this->update_control(
			self::META_DATA_BOTTOM . '_show',
			array(
				'label' => __( 'Meta Data Bottom Area', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->update_control(
			self::META_DATA_BOTTOM . '_options',
			array(
				'default' => array( 'author', 'date', 'like' ),
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_section_style_post() {
		$this->start_controls_section(
			'section_style_post',
			array(
				'label' => __( 'Post', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_control(
			'post_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'post_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'post_border',
				'selector' => '{{WRAPPER}} .cmsmasters-blog__post',
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_control(
			'post_bdrs',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'post_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-blog__post',
				'condition' => array( 'blog_layout' => 'default' ),
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
	 * @since 1.0.3 Move truncate text controls to content tab.
	 */
	protected function register_section_style_post_main_content() {
		$this->start_controls_section(
			'section_style_post_main_content',
			array(
				'label' => __( 'Post: Main Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'blog_layout',
							'operator' => '===',
							'value' => 'default',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'thumbnail_show',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'heading_show',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'excerpt_show',
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
			'post_style_image_heading',
			array(
				'label' => __( 'Featured Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array(
					'blog_layout' => 'default',
					'thumbnail_show!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'post_image_margin',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'range' => array(
					'px' => array(
						'min' => -100,
						'max' => 0,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post-thumbnail' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'blog_layout' => 'default',
					'thumbnail_show!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'post_image_border',
				'selector' => '{{WRAPPER}} .cmsmasters-blog__post-thumbnail__inner',
				'condition' => array(
					'blog_layout' => 'default',
					'thumbnail_show!' => '',
				),
			)
		);

		$this->add_control(
			'post_image_bdrs',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post-thumbnail__inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'blog_layout' => 'default',
					'thumbnail_show!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'post_image_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-blog__post-thumbnail__inner',
				'condition' => array(
					'blog_layout' => 'default',
					'thumbnail_show!' => '',
				),
			)
		);

		$this->add_control(
			'post_style_title_heading',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'blog_layout' => 'default',
					'heading_show!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'heading',
				'selector' => '{{WRAPPER}} .cmsmasters-blog__post-title, {{WRAPPER}} .cmsmasters-blog__post-title a',
				'condition' => array(
					'blog_layout' => 'default',
					'heading_show!' => '',
				),
			)
		);

		$this->start_controls_tabs(
			'heading_style_tabs',
			array(
				'condition' => array(
					'blog_layout' => 'default',
					'heading_show!' => '',
				),
			)
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $key => $label ) {
			$selector = '';

			if ( 'normal' === $key ) {
				$selector .= '{{WRAPPER}} .cmsmasters-blog__post-title, {{WRAPPER}} .cmsmasters-blog__post-title a';
			} else {
				$selector .= '{{WRAPPER}} .cmsmasters-blog__post-title a:hover';
			}

			$this->start_controls_tab(
				'heading_style_tab_' . $key,
				array(
					'label' => $label,
					'condition' => array(
						'blog_layout' => 'default',
						'heading_show!' => '',
					),
				)
			);

			$this->add_control(
				'heading_color_' . $key,
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
					),
					'condition' => array(
						'blog_layout' => 'default',
						'heading_show!' => '',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'post_heading_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'vw' ),
				'range' => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'blog_layout' => 'default',
					'heading_show!' => '',
				),
			)
		);

		$this->add_control(
			'post_style_excerpt_heading',
			array(
				'label' => __( 'Excerpt', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'blog_layout' => 'default',
					'excerpt_show!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'excerpt',
				'selector' => '{{WRAPPER}} .cmsmasters-blog__post-excerpt',
				'condition' => array(
					'blog_layout' => 'default',
					'excerpt_show!' => '',
				),
			)
		);

		$this->add_control(
			'excerpt_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post-excerpt' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'blog_layout' => 'default',
					'excerpt_show!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'post_excerpt_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'vw' ),
				'range' => array(
					'px' => array(
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post-excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'blog_layout' => 'default',
					'excerpt_show!' => '',
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
	protected function register_section_style_meta_data_top() {
		$this->start_controls_section(
			'section_meta_data_top_style',
			array(
				'label' => __( 'Post: Meta Data Top', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'blog_layout' => 'default',
					self::META_DATA_TOP . '_show!' => '',
					self::META_DATA_TOP . '_options!' => '',
				),
			)
		);

		$this->meta_data_top->register_controls_style();

		$this->start_injection(
			array(
				'of' => $this->meta_data_top->separator->get_prefix( 'heading' ),
				'at' => 'before',
			)
		);

		$this->add_responsive_control(
			$this->meta_data_top->get_prefix( 'post_spacing' ),
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'vw' ),
				'range' => array(
					'px' => array(
						'max' => 100,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-meta-data[data-name="' . self::META_DATA_TOP . '"]' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->end_injection();

		$this->end_controls_section();
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_section_style_meta_data_bottom() {
		$this->start_controls_section(
			'section_meta_data_bottom_style',
			array(
				'label' => __( 'Post: Meta Data Bottom', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'blog_layout' => 'default',
					self::META_DATA_BOTTOM . '_show!' => '',
					self::META_DATA_BOTTOM . '_options!' => '',
				),
			)
		);

		$this->meta_data_bottom->register_controls_style();

		$this->end_controls_section();
	}

	/**
	 * Register blog controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 * @since 1.16.4 Added `Color` control for normal and hover state button icon.
	 */
	protected function register_section_style_read_mode() {
		$states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		$this->start_controls_section(
			'section_read_mode_style',
			array(
				'label' => __( 'Post: Read More', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'blog_layout' => 'default',
					'read_more_show!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'read_more_typography',
				'selector' => '{{WRAPPER}} ul.page-numbers',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-font-style: {{VALUE}};',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
				),
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->start_controls_tabs(
			'read_more_style_tabs',
			array( 'condition' => array( 'blog_layout' => 'default' ) )
		);

		foreach ( $states as $state => $state_label ) {
			$selector = '{{WRAPPER}} .cmsmasters-blog__post__read_more';

			if ( 'hover' === $state ) {
				$selector .= ':hover';
				$selector_bg = "{$selector}::after";
			} else {
				$selector_bg = "{$selector}::before";
			}

			$this->start_controls_tab(
				"read_more_tab_{$state}",
				array(
					'label' => $state_label,
					'condition' => array( 'blog_layout' => 'default' ),
				)
			);

			$this->add_group_control(
				Group_Control_Button_Background::get_type(),
				array(
					'name' => "read_more_bg_{$state}",
					'selector' => $selector_bg,
					'exclude' => array( 'color' ),
					'condition' => array( 'blog_layout' => 'default' ),
				)
			);

			$this->start_injection( array( 'of' => "read_more_bg_{$state}_background" ) );

			$this->add_control(
				"read_more_bg_{$state}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_bg => '--button-bg-color: {{VALUE}}; background-color: var( --button-bg-color );',
					),
					'condition' => array( 'blog_layout' => 'default' ),
				)
			);

			$this->end_injection();

			$this->add_control(
				"read_more_color_{$state}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
					),
					'condition' => array( 'blog_layout' => 'default' ),
				)
			);

			$this->add_control(
				"read_more_bd_color_{$state}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'blog_layout' => 'default',
						'read_more_border_border!' => '',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "read_more_box_shadow_{$state}",
					'selector' => $selector,
					'condition' => array( 'blog_layout' => 'default' ),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "read_more_text_shadow_{$state}",
					'selector' => $selector,
					'separator' => 'after',
					'condition' => array( 'blog_layout' => 'default' ),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'read_more_border',
				'selector' => '{{WRAPPER}} .cmsmasters-blog__post__read_more',
				'exclude' => array( 'color' ),
				'separator' => 'before',
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_control(
			'read_more_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post__read_more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'read_more_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post__read_more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
				'condition' => array( 'blog_layout' => 'default' ),
			)
		);

		$this->add_control(
			'button_icon_heading',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'blog_layout' => 'default',
					'read_more_icon[value]!' => '',
					'read_more_show!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'read_more_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 50,
					),
					'em' => array(
						'max' => 10,
					),
				),
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post__read_more .cmsmasters-wrap-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'blog_layout' => 'default',
					'read_more_icon[value]!' => '',
					'read_more_show!' => '',
				),
			)
		);

		$this->start_controls_tabs(
			'tabs_button_icon_style',
			array(
				'condition' => array(
					'blog_layout' => 'default',
					'read_more_icon[value]!' => '',
					'read_more_show!' => '',
				),
			)
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$state = ( 'hover' === $main_key ? ':hover' : '' );

			$this->start_controls_tab(
				"tab_read_more_icon_{$main_key}_tab",
				array( 'label' => $label )
			);

			$this->add_control(
				"tab_read_more_icon_{$main_key}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--read-more-icon-{$main_key}-color: {{VALUE}};",
					),
					'condition' => array(
						'blog_layout' => 'default',
						'read_more_icon[value]!' => '',
						'read_more_show!' => '',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'read_more_icon_spacing',
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 5,
				),
				'range' => array(
					'px' => array(
						'max' => 50,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__post__read_more .cmsmasters-wrap-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-read-more-align-after .cmsmasters-blog__post__read_more .cmsmasters-wrap-icon' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: 0;',
				),
				'condition' => array(
					'blog_layout' => 'default',
					'read_more_icon[value]!' => '',
					'read_more_show!' => '',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Change blog controls.
	 *
	 * Update input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 * @since 1.11.5 Fixed issues for Elementor 3.18.0.
	 */
	private function update_controls_hide_when_default() {
		$condition_default = array( 'blog_layout' => array( 'default' ) );
		$controls_ids = $this->get_controls_to_hide_for_template();

		foreach ( $controls_ids as $control_id ) {
			$control = $this->get_controls( $control_id );
			$args = array(
				'condition' => $condition_default,
			);
			$options = array(
				'recursive' => true,
			);

			if ( ! empty( $control['type'] ) && Controls_Manager::SECTION === $control['type'] ) {
				ElementorPlugin::instance()->controls_manager->update_control_in_stack( $this, $control_id, $args, $options );
			} elseif ( Utils::get_if_isset( $control, 'responsive' ) ) {
				$this->update_control( $control_id, $args, $options );
			} else {
				$this->update_control( $control_id, $args, $options );
			}
		}
	}

	/**
	 * List of controls that need to be hidden when selecting a template.
	 *
	 * @since 1.0.0
	 * @since 1.14.5 Fixed separator.
	 *
	 * @return string[] Controls names.
	 */
	protected function get_controls_to_hide_for_template() {
		$controls_manager = Plugin::elementor()->controls_manager;
		$thumbnail_controls = $controls_manager->get_control_groups( Group_Control_Image_Size::get_type() )->get_fields();
		$sections_ids = array(
			'section_meta_data_bottom_style',
			'section_meta_data_top_style',
			'section_read_mode_style',
			'section_style_post_main_content',
			'section_style_post',
		);
		$controls_ids = array(
			'alignment',

			'excerpt_heading',
			'excerpt_length',
			'excerpt_show',

			'heading_show',
			'heading_tag',

			'image_ratio',

			'meta_data_top_options',
			'meta_data_top_show',
			'meta_data_top_author_heading',
			'meta_data_top_author_avatar',
			'meta_data_top_author_prefix',
			'meta_data_top_date_heading',
			'meta_data_top_date_date_format',

			'meta_data_bottom_options',
			'meta_data_bottom_show',
			'meta_data_bottom_author_heading',
			'meta_data_bottom_author_avatar',
			'meta_data_bottom_author_prefix',
			'meta_data_bottom_date_heading',
			'meta_data_bottom_date_date_format',

			'post_title_heading',

			'read_more_heading',
			'read_more_icon_align',
			'read_more_icon_spacing',
			'read_more_icon',
			'read_more_show',
			'read_more_text',

			'thumbnail_heading',
			'thumbnail_show',
			'image_ratio_switcher',
		);

		/* All Controls in Group_Control_Image_Size */
		foreach ( $thumbnail_controls as $control_id => $control_value ) {
			array_push( $controls_ids, "thumbnail_{$control_id}" );
		}

		foreach ( $sections_ids as $section_name ) {
			array_push( $controls_ids, $section_name );
		}

		return $controls_ids;
	}

	/**
	 * Change blog controls.
	 *
	 * Update input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function update_controls_border_columns() {
		$this->update_control(
			'border_columns_type',
			array(
				'default' => 'solid',
			)
		);

		$this->update_control(
			'border_vertical_width',
			array(
				'default' => array(
					'size' => 1,
					'unit' => 'px',
				),
			)
		);
	}

	/**
	 * @since 1.0.0
	 */
	protected function render_post_inner() {
		if ( $this->is_setting_as_default( 'blog_layout' ) ) {
			$this->render_post_thumbnail();
			$this->meta_data_top->render();
			$this->render_post_title();
			$this->render_post_excerpt();
			$this->render_post_footer();
		} else {
			echo Plugin::instance()->frontend->get_widget_template( $this->template_id, false, true );
		}
	}

	/**
	 * Display the post thumbnail.
	 *
	 * @since 1.0.0
	 * @since 1.17.3 Fixed duplicate permalink in post thumbnail.
	 */
	protected function render_post_thumbnail() {
		$settings = $this->get_settings_for_display();

		if ( ! $settings['thumbnail_show'] || ! has_post_thumbnail() ) {
			return;
		}

		$settings['thumbnail'] = array(
			'id' => get_post_thumbnail_id(),
		);

		$post_title = get_the_title();

		if ( ! $post_title ) {
			$post_title = '(' . esc_html__( 'Post Thumbnail', 'cmsmasters-elementor' ) . ')';
		}

		$this->remove_render_attribute( 'cmsmasters-blog-post-thumbnail-inner' );

		$this->add_render_attribute(
			'cmsmasters-blog-post-thumbnail-inner',
			array(
				'href' => esc_attr( get_permalink() ),
				'class' => 'cmsmasters-blog__post-thumbnail__inner',
				'aria-label' => esc_attr( wp_kses_post( $post_title ) ),
			)
		);

		echo '<div class="cmsmasters-blog__post-thumbnail">' .
			'<a ' . $this->get_render_attribute_string( 'cmsmasters-blog-post-thumbnail-inner' ) . '>' .
				Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail' ) .
			'</a>' .
		'</div>';
	}

	/**
	 * Display the post title.
	 *
	 * @since 1.0.0
	 */
	protected function render_post_title() {
		$settings = $this->get_settings_for_display();

		if ( ! $settings['heading_show'] ) {
			return;
		}

		$title = get_the_title();

		if ( ! $title ) {
			$title = '(' . esc_html__( 'No Title', 'cmsmasters-elementor' ) . ')';
		}

		echo '<' . tag_escape( $settings['heading_tag'] ) . ' class="cmsmasters-blog__post-title">' .
			'<a href="' . esc_url( get_permalink() ) . '">' .
				wp_kses_post( $title ) .
			'</a>' .
		'</' . tag_escape( $settings['heading_tag'] ) . '>';
	}

	/**
	 * Display the post excerpt.
	 *
	 * @since 1.0.0
	 */
	protected function render_post_excerpt() {
		if ( ! $this->get_settings_for_display( 'excerpt_show' ) ) {
			return;
		}

		if ( ! get_the_excerpt() ) {
			return;
		}

		$has_excerpt = has_excerpt();

		if ( $has_excerpt ) {
			add_filter( 'wp_trim_excerpt', array( $this, 'filter_wp_trim_excerpt' ) );
		} else {
			add_filter( 'excerpt_more', array( $this, 'filter_excerpt_more' ), 20 );
			add_filter( 'excerpt_length', array( $this, 'filter_excerpt_length' ), 20 );
		}

		echo '<div class="cmsmasters-blog__post-excerpt">';

			$excerpt = wp_trim_words( get_the_excerpt(), $this->filter_excerpt_length(), $this->filter_excerpt_more() );

			echo wp_kses_post( $excerpt );

		echo '</div>';

		if ( $has_excerpt ) {
			remove_filter( 'wp_trim_excerpt', array( $this, 'filter_wp_trim_excerpt' ) );
		} else {
			remove_filter( 'excerpt_length', array( $this, 'filter_excerpt_length' ), 20 );
			remove_filter( 'excerpt_more', array( $this, 'filter_excerpt_more' ), 20 );
		}
	}

	/**
	 * Get text after a trimmed excerpt.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function filter_excerpt_more() {
		return '...';
	}

	/**
	 * Crop excerpt.
	 *
	 * @param string $excerpt
	 *
	 * @return string
	 */
	public function filter_wp_trim_excerpt( $excerpt ) {
		return wp_trim_words( $excerpt, $this->filter_excerpt_length(), $this->filter_excerpt_more() );
	}

	/**
	 * Get maximum number of words in a post excerpt.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function filter_excerpt_length() {
		$excerpt_length = $this->get_settings_fallback( 'excerpt_length' );

		return ( ! empty( $excerpt_length ) ? $excerpt_length : 25 );
	}

	/**
	 * Display the post footer.
	 *
	 * @since 1.0.0
	 */
	protected function render_post_footer() {
		$settings = $this->get_settings_for_display();

		if ( ! $settings['read_more_show'] && ! $this->meta_data_bottom->is_visible() ) {
			return;
		}

		echo '<div class="cmsmasters-blog__post_footer">';

		$this->meta_data_bottom->render();

		$this->render_read_more();

		echo '</div>';
	}

	/**
	 * Display the read more.
	 *
	 * @since 1.0.0
	 */
	public function render_read_more() {
		$settings = $this->get_settings_for_display();

		if ( ! $settings['read_more_show'] ) {
			return;
		}

		$read_more_text = $settings['read_more_text'];

		if ( ! $read_more_text ) {
			$read_more_text = esc_html__( 'Read More', 'cmsmasters-elementor' );
		}

		echo '<a class="cmsmasters-blog__post__read_more cmsmasters-theme-button" href="' . esc_url( get_permalink() ) . '">';

		Utils::render_icon( $settings['read_more_icon'], array( 'aria-hidden' => 'true' ) );

		echo '<span>' .
				esc_html( $read_more_text ) .
			'</span>' .
		'</a>';
	}

	/**
	 * Get class for default styling.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_css_class() {
		return 'cmsmasters-blog--type-default';
	}

	/**
	 * Get selector for default styling.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_blog_selector() {
		return '{{WRAPPER}} .' . static::get_css_class();
	}
}
