<?php
namespace CmsmastersElementor\Modules\Blog\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\AjaxWidget\Module as AjaxWidgetModule;
use CmsmastersElementor\Modules\Blog\Widgets\Base_Blog\Base_Blog_Elements;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Plugin as CmsmastersPlugin;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon blog-featured widget.
 *
 * Addon widget that displays blog-featured.
 *
 * @since 1.0.0
 */
class Blog_Featured extends Base_Blog_Elements {

	const TYPE_FEATURED = 'featured';
	const TYPE_REGULAR = 'regular';

	/**
	 * List of template types.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $templates_types = array();

	/**
	 * Displays a blog at the template ID.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $template_id;

	/**
	 * Current blog ajax method.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $ajax_method = '';

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Featured Posts', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_icon() {
		return 'cmsicon-featured-posts';
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
		return array_unique(
			array_merge(
				parent::get_unique_keywords(),
				array(
					'featured',
					'grig',
					'masonry',
				)
			)
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
	 * @since 1.0.0
	 */
	public function register_controls() {
		parent::register_controls();

		$this->register_section_layout_content();
		$this->register_control_pagination();
	}

	/**
	 * Featured Blog Widget constructor.
	 *
	 * Initializing the widget blog class.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		static::$templates_types = array(
			self::TYPE_FEATURED => __( 'Featured', 'cmsmasters-elementor' ),
			self::TYPE_REGULAR => __( 'Regular', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public function render_ajax( $ajax_vars ) {
		$this->ajax_method = Utils::get_if_isset( $ajax_vars, 'ajaxMethod' );

		parent::render_ajax( $ajax_vars );
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
		$template_id_featured = $this->get_settings_for_display( 'post_featured_template_id' );
		$template_id_regular = $this->get_settings_for_display( 'post_regular_template_id' );

		$is_featured_published = Utils::check_template( $template_id_featured );
		$is_regular_published = Utils::check_template( $template_id_regular );

		if ( ! $is_featured_published || ! $is_regular_published ) {
			return array();
		}

		return array(
			$template_id_featured,
			$template_id_regular,
		);
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.11.1 Optimized loading CSS in templates.
	 * @since 1.12.1 Add checking template.
	 */
	public function render() {
		$template_ids = $this->get_template_ids();

		if ( empty( $template_ids ) ) {
			if ( is_admin() ) {
				$template_id_featured = $this->get_settings_for_display( 'post_featured_template_id' );
				$template_id_regular = $this->get_settings_for_display( 'post_regular_template_id' );

				$is_featured_published = Utils::check_template( $template_id_featured );
				$is_regular_published = Utils::check_template( $template_id_regular );

				if ( ! $is_featured_published ) {
					Utils::render_alert( esc_html__( 'Please choose your blog widget featured template!', 'cmsmasters-elementor' ) );
				}

				if ( ! $is_regular_published ) {
					Utils::render_alert( esc_html__( 'Please choose your blog widget regular template!', 'cmsmasters-elementor' ) );
				}
			}

			return;
		}

		if ( 'enable' !== $this->lazyload_widget_get_status() ) {
			/** @var Addon $addon */
			$addon = CmsmastersPlugin::instance();

			$addon->frontend->print_template_css( $template_ids, $this->get_id() );
		}

		parent::render();
	}

	/**
	 * @since 1.0.0
	 */
	protected function register_style_section_controls() {
		$this->register_section_style_layout();

		parent::register_style_section_controls();
	}

	/**
	 * @since 1.0.0
	 * @since 1.12.1 Add checking template.
	 */
	protected function render_post_inner() {
		if ( ! Utils::check_template( $this->template_id ) ) {
			if ( is_admin() ) {
				Utils::render_alert( esc_html__( 'Please choose your entry template!', 'cmsmasters-elementor' ) );
			}

			return;
		}

		echo CmsmastersPlugin::instance()->frontend->get_widget_template( $this->template_id, false, true );
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_posts_per_page() {
		$posts_count = 0;

		foreach ( array_keys( $this->get_templates_types() ) as $type ) {
			$count = $this->get_settings( "post_{$type}_posts" );

			if ( $count ) {
				$posts_count += $count;
			}
		}

		return $posts_count;
	}

	/**
	 * @since 1.0.0
	 */
	protected function render_posts_inner() {
		if ( ! $this->has_else_posts() ) {
			return;
		}

		echo '<div class="cmsmasters-blog-featured-temp-wrap">';

		foreach ( $this->get_templates_types() as $template_name => $template_label ) {
			$this->template_id = $this->get_settings( "post_{$template_name}_template_id" );
			$posts_count = $this->get_settings( "post_{$template_name}_posts" );

			echo '<div class="cmsmasters-blog-featured-temp cmsmasters-blog-featured-temp-' . esc_attr( $template_name ) . '">';

			for ( $post_index = 0; $post_index < $posts_count; $post_index++ ) {
				if ( ! $this->has_else_posts() ) {
					break;
				}

				$this->prepare_the_post();
				$this->render_post();
			}

			echo '</div>';

			if ( ! $this->has_else_posts() ) {
				break;
			}
		}

		echo '</div>';
	}

	/**
	 * Get list of template types.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_templates_types() {
		$templates_types = array();

		/* Only For load-more */
		if ( AjaxWidgetModule::is_active_ajax() && 'load-more' === $this->ajax_method ) {
			$load_more_ajax = ! ! $this->get_settings( 'pagination_load_more_insert_in' );

			if ( $load_more_ajax ) {
				$templates_types[ self::TYPE_REGULAR ] = static::$templates_types[ self::TYPE_REGULAR ];
			} else {
				$templates_types = static::$templates_types;
			}
		}

		if ( empty( $templates_types ) ) {
			$templates_types = static::$templates_types;
		}

		return $templates_types;
	}

	/**
	 * Check if more posts.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function has_else_posts() {
		return $this->get_query()->current_post + 1 < $this->get_query()->post_count;
	}

	/**
	 * Register blog featured controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 * @since 1.3.3 Fixed on breakpoints.
	 */
	public function register_section_layout_content() {
		$this->start_injection( array(
			'of' => 'section_layout',
		) );

		$this->add_responsive_control(
			'template_layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'vertical',
				'mobile_default' => 'horizontal',
				'tablet_default' => 'vertical',
				'options' => array(
					'horizontal' => __( 'Vertical', 'cmsmasters-elementor' ),
					'vertical' => __( 'Horizontal', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog-featured-temp-wrap' => 'grid-template-columns: {{VALUE}}',
				),
				'selectors_dictionary' => array(
					'horizontal' => '100%',
					'vertical' => 'inherit',
				),
				'prefix_class' => 'cmsmasters-blog-featured--layout%s-',
				'frontend_available' => true,
				'render_type' => 'ui',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'template_proportions',
			array(
				'label' => __( 'Proportions', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'tablet_default' => array(
					'unit' => '%',
					'size' => 70,
				),
				'mobile_default' => array(
					'unit' => '%',
					'size' => 70,
				),
				'default' => array(
					'unit' => '%',
					'size' => 70,
				),
				'range' => array(
					'%' => array(
						'min' => 10,
						'max' => 90,
					),
					'vw' => array(
						'min' => 10,
						'max' => 90,
					),
					'vh' => array(
						'min' => 10,
						'max' => 90,
					),
					'px' => array(
						'min' => 50,
						'max' => 1440,
					),
				),
				'size_units' => array( '%', 'px', 'vw', 'vh' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog-featured-temp-wrap' => 'grid-template-columns: minmax(0, {{SIZE}}{{UNIT}}) minmax(0, calc(100% - {{SIZE}}{{UNIT}}));',
				),
				'condition' => array(
					'template_layout' => array( 'vertical' ),
				),
			)
		);

		$this->start_controls_tabs( 'content_tabs' );

		foreach ( static::$templates_types as $key => $label ) {
			$this->start_controls_tab(
				"content_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"post_{$key}_template_id",
				array(
					'label' => __( 'Choose Entry Template', 'cmsmasters-elementor' ),
					'label_block' => true,
					'type' => CmsmastersControls::QUERY,
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
					'frontend_available' => true,
					'render_type' => 'template',
				)
			);

			$this->add_control(
				"post_{$key}_posts",
				array(
					'label' => __( 'Posts', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'render_type' => 'template',
				)
			);

			$columns = array(
				'' => __( 'Auto', 'cmsmasters-elementor' ),
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
			);

			if ( self::TYPE_REGULAR === $key ) {
				$columns['5'] = '5';
				$columns['6'] = '6';
			}

			$this->add_responsive_control(
				"post_{$key}_column",
				array(
					'label' => __( 'Column', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => $columns,
					'selectors' => array(
						"{{WRAPPER}} .cmsmasters-blog-featured-temp-{$key}" => '--columns: {{VALUE}};',
					),
					'render_type' => 'ui',
					'frontend_available' => true,
					'condition' => array(
						"post_{$key}_posts!" => '1',
					),
				)
			);

			if ( self::TYPE_REGULAR === $key ) {
				$this->add_control(
					'masonry_regular',
					array(
						'label' => __( 'Masonry', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SWITCHER,
						'render_type' => 'ui',
						'frontend_available' => true,
						'prefix_class' => 'cmsmasters-blog-featured--masonry-',
						'condition' => array(
							'pagination_show!' => '',
							"post_{$key}_column!" => array( '', '1' ),
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->update_control(
			'post_featured_posts',
			array(
				'default' => '1',
				'options' => array_combine( range( 1, 5 ), range( 1, 5 ) ),
			)
		);

		$this->update_control(
			'post_regular_posts',
			array(
				'default' => '2',
				'options' => array_combine( range( 2, 10 ), range( 2, 10 ) ),
			)
		);

		$this->end_injection();
	}

	/**
	 * Register blog featured controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_control_pagination() {
		$this->start_injection(
			array(
				'of' => 'section_pagination',
				'type' => 'section',
			)
		);

		$this->add_control(
			'pagination_load_more_insert_in',
			array(
				'label' => __( 'Show regular posts only', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'render_type' => 'template',
				'separator' => 'before',
				'frontend_available' => true,
				'condition' => array(
					'pagination_show!' => '',
					'pagination_type' => array( 'load_more' ),
				),
			)
		);

		$this->end_injection();
	}

	/**
	 * Register blog featured controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	public function register_section_style_layout() {
		$this->start_controls_section(
			'start_controls_layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'template_space_between',
			array(
				'label' => __( 'Space Between Templates', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 150,
					),
				),
				'size_units' => array( 'px', '%', 'vw' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog-featured-temp-wrap' => 'grid-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'template_bd_width',
			array(
				'label' => __( 'Separator Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 5,
					),
				),
				'frontend_available' => true,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-blog-featured--layout-vertical .cmsmasters-border-columns' => 'border-right-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-blog-featured--layout-horizontal .cmsmasters-blog-featured-temp-featured::after' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-blog-featured-temp::after' => 'bottom: calc(-{{template_space_between.SIZE}}{{template_space_between.UNIT}} / 2 - ({{SIZE}}{{UNIT}} / 2));',
				),
				'device_args' => Utils::get_devices_args( array(
					'selectors' => array(
						'{{WRAPPER}}.cmsmasters-blog-featured--layout-{{cmsmasters_device}}-vertical .cmsmasters-border-columns' => 'border-right-width: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}}.cmsmasters-blog-featured--layout-{{cmsmasters_device}}-horizontal .cmsmasters-blog-featured-temp-featured::after' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .cmsmasters-blog-featured-temp::after' => 'bottom: calc(-{{template_space_between_{{cmsmasters_device}}.SIZE}}{{template_space_between_{{cmsmasters_device}}.UNIT}} / 2 - ({{SIZE}}{{UNIT}} / 2));',
					),
				) ),
			)
		);

		$this->add_control(
			'template_bd',
			array(
				'label' => __( 'Separator Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => array(
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
				),
				'frontend_available' => true,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-border-columns,' .
					'{{WRAPPER}} .cmsmasters-blog-featured-temp::after,' .
					'{{WRAPPER}} .cmsmasters-blog-featured-temp .cmsmasters-blog__post::after' => 'border-style: {{VALUE}};',
				),
				'condition' => array(
					'template_bd_width[size]!' => array(
						'',
						0,
					),
				),
			)
		);

		$this->add_control(
			'layout_separator_color',
			array(
				'label' => __( 'Separator Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-border-columns,' .
					'{{WRAPPER}} .cmsmasters-blog-featured-temp::after,' .
					'{{WRAPPER}} .cmsmasters-blog-featured-temp .cmsmasters-blog__post::after' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'template_bd_width[size]!' => array(
						'',
						0,
					),
				),
			)
		);

		$this->start_controls_tabs( 'content_style_tabs' );

		foreach ( static::$templates_types as $key => $label ) {
			$this->start_controls_tab(
				"content_style_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->add_responsive_control(
				"post_{$key}_space_x",
				array(
					'label' => __( 'Space Between Columns', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 150,
						),
					),
					'size_units' => array( 'px', 'vw' ),
					'selectors' => array(
						"{{WRAPPER}} .cmsmasters-blog-featured-temp-{$key}" => '--column-gap: {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						"post_{$key}_column!" => '1',
					),
				)
			);

			$this->add_responsive_control(
				"post_{$key}_space_y",
				array(
					'label' => __( 'Space Between Rows', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 150,
						),
					),
					'size_units' => array( 'px', 'vw' ),
					'frontend_available' => true,
					'selectors' => array(
						"{{WRAPPER}} .cmsmasters-blog-featured-temp-{$key}" => '--row-gap: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_responsive_control(
				"post_{$key}_bd_width_x",
				array(
					'label' => __( 'Separator width (Rows)', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 10,
						),
					),
					'size_units' => array( 'px' ),
					'frontend_available' => true,
					'selectors' => array(
						"{{WRAPPER}} .cmsmasters-blog-featured-temp-{$key} .cmsmasters-blog__post::after" => "border-bottom-width: {{SIZE}}{{UNIT}}; bottom: calc(-{{post_{$key}_space_y.SIZE}}{{post_{$key}_space_y.UNIT}} / 2 - ({{SIZE}}{{UNIT}} / 2));",
					),
					'device_args' => Utils::get_devices_args( array(
						'selectors' => array(
							"{{WRAPPER}} .cmsmasters-blog-featured-temp-{$key} .cmsmasters-blog__post::after" => "border-bottom-width: {{SIZE}}{{UNIT}}; bottom: calc(-{{post_{$key}_space_y_{{cmsmasters_device}}.SIZE}}{{post_{$key}_space_y_{{cmsmasters_device}}.UNIT}} / 2 - ({{SIZE}}{{UNIT}} / 2));",
						),
					) ),
				)
			);

			$this->add_responsive_control(
				"post_{$key}_bd_width_y",
				array(
					'label' => __( 'Separator width (Columns)', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 10,
						),
					),
					'size_units' => array( 'px' ),
					'frontend_available' => true,
					'selectors' => array(
						"{{WRAPPER}} .cmsmasters-blog-featured-temp-{$key} .cmsmasters-border-columns" => 'border-right-width: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render post markers.
	 *
	 * @since 1.4.0
	 */
	public function marker_post() {
		return '';
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
				'field' => 'blog_filter_id',
				'type' => esc_html__( 'Filter ID', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'header_title',
				'type' => esc_html__( 'Header Title', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'filter_default_text',
				'type' => esc_html__( 'Filter Default Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'nothing_found_message',
				'type' => esc_html__( 'Nothing Found Message', 'cmsmasters-elementor' ),
				'editor_type' => 'AREA',
			),
			array(
				'field' => 'pagination_load_more_text_normal',
				'type' => esc_html__( 'Load More Text (Normal state)', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_load_more_text_loading',
				'type' => esc_html__( 'Load More Text (Loading state)', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_infinite_scroll_text',
				'type' => esc_html__( 'Infinite Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_text_prev',
				'type' => esc_html__( 'Pagination Previous Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'pagination_text_next',
				'type' => esc_html__( 'Pagination Next Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'separator_filter_content',
				'type' => esc_html__( 'Separator Content', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
