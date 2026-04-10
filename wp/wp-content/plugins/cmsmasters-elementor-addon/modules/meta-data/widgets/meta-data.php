<?php
namespace CmsmastersElementor\Modules\MetaData\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Classes\Separator;
use CmsmastersElementor\Controls_Manager as CmsmastersControlsManager;
use CmsmastersElementor\Controls\Groups\Group_Control_Format_Date;
use CmsmastersElementor\Controls\Groups\Group_Control_Format_Time;
use CmsmastersElementor\Modules\MetaData\Module as MetaDataModule;
use CmsmastersElementor\Modules\TemplatePages\Traits\Singular_Widget;
use CmsmastersElementor\Modules\TribeEvents\Module as TribeEventsModule;
use CmsmastersElementor\Modules\Woocommerce\Module as WoocommerceModule;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * CMSMasters Meta Data.
 *
 * Addon widget that displays date, time, author, taxonomy, likes & comments of current post.
 *
 * @since 1.0.0
 */
class Meta_Data extends Base_Widget {

	use Singular_Widget;

	/**
	 * List with css states (normal, hover).
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected static $css_states = array();

	/**
	 * Current meta-field.
	 *
	 * Holds the current meta-field settings.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $meta_field = array();

	/**
	 * Current meta-field.
	 *
	 * Holds the current meta-field settings.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $meta_fields = array();

	/**
	 * Separator.
	 *
	 * Separator for each meta-field.
	 *
	 * @since 1.0.0
	 *
	 * @var Separator
	 */
	protected $separator;

	/**
	 * Separator.
	 *
	 * Separator for each taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @var Separator
	 */
	protected $separator_taxonomy;

	/**
	 * @since 1.0.0
	 */
	public function get_name_prefix() {
		return self::WIDGET_NAME_PREFIX;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Meta Data', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_icon() {
		return 'cmsicon-meta-data';
	}

	/**
	 * @since 1.0.0
	 */
	public function get_unique_keywords() {
		return array(
			'meta',
			'author',
			'user',
			'avatar',
			'comments',
			'date',
			'time',
			'taxonomy',
			'categories',
			'tags',
			'likes',
			'views',
			'time to read',
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.0.0
	 * @since 1.0.2 Added Font Awesome Regular.
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		if ( ! Icons_Manager::is_migration_allowed() ) {
			return array();
		}

		return array(
			'elementor-icons-fa-solid',
			'elementor-icons-fa-brands',
			'elementor-icons-fa-regular',
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
	 * Meta Data Widget constructor.
	 *
	 * Initializing the meta data class.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		$this->separator = new Separator( $this, array(
			'name' => 'separator',
			'selector' => '{{WRAPPER}} .cmsmasters-meta-data__item',
		) );

		$this->separator_taxonomy = new Separator( $this, array(
			'name' => 'separator_taxonomy',
			'selector' => '{{WRAPPER}} .cmsmasters-postmeta[data-name="taxonomy"] .term-wrap',
		) );

		if ( ! self::$css_states ) {
			self::$css_states = array(
				'normal' => __( 'Normal', 'cmsmasters-elementor' ),
				'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			);
		}

		parent::__construct( $data, $args );
	}

	/**
	 * Get group for select.
	 *
	 * @return array
	 */
	public static function get_groups() {
		return array(
			'standard' => array(
				'label' => __( 'Standard', 'cmsmasters-elementor' ),
				'options' => static::get_standard_options(),
			),
			'taxonomy' => array(
				'label' => __( 'Taxonomy', 'cmsmasters-elementor' ),
				'options' => static::get_taxonomy_options(),
			),
			'count' => array(
				'label' => __( 'Count', 'cmsmasters-elementor' ),
				'options' => static::get_count_options(),
			),
		);
	}

	/**
	 * Get a list of taxonomy names.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fix for custom post types.
	 *
	 * @return array An array of taxonomy names.
	 */
	protected static function get_taxonomy_options() {
		$taxonomy_args = array(
			'show_in_nav_menus' => true,
			'public' => true,
		);
		$taxonomies_by_post_types = get_object_taxonomies( static::get_allowed_post_types(), 'object' );
		$taxonomies = wp_filter_object_list( $taxonomies_by_post_types, $taxonomy_args );
		$options = wp_list_pluck( $taxonomies, 'label', 'name' );

		return array_map( 'ucwords', $options );
	}

	/**
	 * Get options for group count.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added reading time meta.
	 *
	 * @return array
	 */
	protected static function get_count_options() {
		return array(
			'comments' => __( 'Comments', 'cmsmasters-elementor' ),
			'like' => __( 'Like', 'cmsmasters-elementor' ),
			'view' => __( 'View', 'cmsmasters-elementor' ),
			'reading_time' => __( 'Time to Read', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * Get options for group standard.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected static function get_standard_options() {
		return array(
			'author' => __( 'Author', 'cmsmasters-elementor' ),
			'date' => __( 'Date', 'cmsmasters-elementor' ),
			'post_type' => __( 'Post Type', 'cmsmasters-elementor' ),
			'time' => __( 'Time', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * Get allowed post types.
	 *
	 * Retrieves the list of post type names.
	 *
	 * @since 1.1.0
	 *
	 * @return string[] An array of post type names.
	 */
	protected static function get_allowed_post_types() {
		$post_types = array_keys(
			get_post_types(
				array(
					'show_in_nav_menus' => true,
					'public' => true,
				)
			)
		);

		if ( WoocommerceModule::is_active() ) {
			$post_types = array_diff( $post_types, array( 'product' ) );
		}

		if ( TribeEventsModule::is_active() ) {
			$post_types = array_diff( $post_types, array( 'tribe_events' ) );
		}

		return $post_types;
	}

	/**
	 * Register meta data widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Adds border & icons settings for taxonomy & author.
	 */
	public function register_controls() {
		$this->register_controls_content();
		$this->register_controls_style_general();
		$this->register_controls_style_author();
		$this->register_controls_style_post_type();
		$this->register_controls_style_taxonomy();
		$this->register_controls_style_count();
		$this->update_controls_separator();
		$this->update_base_controls_separator();
	}

	/**
	 * Register meta data widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_style_author() {
		$this->start_controls_section(
			'section_style_author',
			array(
				'label' => __( 'Author', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'author_style_tabs' );

		foreach ( self::$css_states as $state => $state_label ) {
			$avatar_selector = '{{WRAPPER}} .cmsmasters-postmeta[data-name="author"]';

			if ( 'normal' === $state ) {
				$avatar_selector .= '[rel="author"]';
			} else {
				$avatar_selector .= 'a[rel="author"]:hover';
			}

			$this->start_controls_tab(
				"author_style_tab_{$state}",
				array(
					'label' => $state_label,
				)
			);

			$this->add_control(
				"author_color_text_{$state}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$avatar_selector => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"author_color_icon_{$state}",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						( ( 'normal' === $state ? '{{WRAPPER}} .cmsmasters-postmeta[data-name="author"]' : '{{WRAPPER}} .cmsmasters-postmeta[data-name="author"]:hover' ) . ' .cmsmasters-postmeta__inner > .cmsmasters-wrap-icon' ) => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"author_color_border_{$state}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						( ( 'normal' === $state ? '{{WRAPPER}} .cmsmasters-postmeta[data-name="author"]' : '{{WRAPPER}} .cmsmasters-postmeta[data-name="author"]:hover' ) . ' img' ) => 'color: {{VALUE}};',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'author_avatar_display',
			array(
				'label' => __( 'Display', 'cmsmasters-elementor' ),
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'label_block' => false,
				'options' => array(
					'' => esc_html__( 'Horizontal', 'cmsmasters-elementor' ),
					'column' => esc_html__( 'Vertical', 'cmsmasters-elementor' ),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-postmeta[data-name="author"] .cmsmasters-postmeta__content' => 'flex-direction: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_author',
				'selector' => '{{WRAPPER}} .cmsmasters-postmeta[data-name="author"] .avatar-wrap img',
				'separator' => 'before',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'author_avatar_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ) . ' (px)',
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 20,
						'max' => 200,
					),
				),
				'render_type' => 'template',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-postmeta[data-name="author"]' => '--avatar-size: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'author_avatar_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 50,
					),
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-postmeta[data-name="author"] .avatar-wrap img' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'author_avatar_space',
			array(
				'label' => __( 'Avatar Space', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-postmeta[data-name="author"]' => '--avatar-space: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register meta data widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_style_post_type() {
		$selector_normal = '{{WRAPPER}} .cmsmasters-postmeta[data-name="post_type"]';

		$this->start_controls_section(
			'section_style_post_type',
			array(
				'label' => __( 'Post Type', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'post_type_style_tabs' );

		$css_states = self::$css_states;

		$css_states['hover'] = $css_states['hover'] . '(' . esc_html__( 'link', 'cmsmasters-elementor' ) . ')';

		foreach ( self::$css_states as $state => $state_label ) {
			$selector = $selector_normal;

			if ( 'hover' === $state ) {
				$selector .= ' a:hover';
			}

			$this->start_controls_tab(
				"post_type_style_tab_{$state}",
				array(
					'label' => $state_label,
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "post_type_box_shadow_{$state}",
					'selector' => $selector,
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "post_type_text_shadow_{$state}",
					'selector' => $selector,
				)
			);

			$this->add_control(
				"post_type_color_bg_{$state}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"post_type_color_text_{$state}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"post_type_color_icon_{$state}",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$selector} .cmsmasters-postmeta__inner > .cmsmasters-wrap-icon" => 'color: {{VALUE}};',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'post_type_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator' => 'before',
				'selectors' => array(
					$selector_normal => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'post_type_bdrs',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 50,
					),
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					$selector_normal => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register meta data widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_style_general() {
		$this->start_controls_section(
			'section_style_general',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography',
				'selector' => '{{WRAPPER}} .cmsmasters-meta-data__item, {{WRAPPER}} .cmsmasters-meta-data__item > *, {{WRAPPER}} .cmsmasters-meta-data__item a',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label' => __( 'Link Typography', 'cmsmasters-elementor' ),
				'name' => 'typography_link',
				'selector' => '{{WRAPPER}} .cmsmasters-meta-data__item .cmsmasters-postmeta a',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'label' => __( 'Before & After Text Typography', 'cmsmasters-elementor' ),
				'name' => 'typography_before_after',
				'selector' => '{{WRAPPER}} .content-side',
			)
		);

		$this->start_controls_tabs( 'standard_style_tabs' );

		foreach ( self::$css_states as $state => $state_label ) {
			$is_hover = 'hover' === $state;

			$this->start_controls_tab(
				"standard_style_tab_{$state}",
				array(
					'label' => $state_label,
				)
			);

			if ( ! $is_hover ) {
				$this->add_control(
					"color_text_{$state}",
					array(
						'label' => __( 'Text Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .cmsmasters-meta-data__item, {{WRAPPER}} .cmsmasters-meta-data__item > *, {{WRAPPER}} .cmsmasters-meta-data__item a' => 'color: {{VALUE}};',
						),
					)
				);
			}

			$this->add_control(
				"color_link_{$state}",
				array(
					'label' => __( 'Link Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						( $is_hover ? ( '{{WRAPPER}} .cmsmasters-meta-data__item a:hover, {{WRAPPER}} .cmsmasters-meta-data__item a.active' ) : '{{WRAPPER}} .cmsmasters-meta-data__item a' ) => 'color: {{VALUE}};',
					),
				)
			);

			if ( $is_hover ) {
				$this->add_control(
					"text_decoration_{$state}",
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
							'{{WRAPPER}} .cmsmasters-meta-data__item a:hover span, {{WRAPPER}} .cmsmasters-meta-data__item a.active span' => 'text-decoration: {{VALUE}};',
						),
					)
				);
			}

			if ( ! $is_hover ) {
				$this->add_control(
					"color_icon_{$state}",
					array(
						'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} .cmsmasters-meta-data__item .cmsmasters-postmeta__inner > .cmsmasters-wrap-icon' => 'color: {{VALUE}};',
						),
					)
				);
			}

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "text_shadow_{$state}",
					'selector' => ( $is_hover ? ( '{{WRAPPER}} .cmsmasters-meta-data__item a:hover, {{WRAPPER}} .cmsmasters-meta-data__item a.active' ) : '{{WRAPPER}} .cmsmasters-meta-data__item' ),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 50,
					),
					'em' => array(
						'min' => 0.5,
						'max' => 3,
						'step' => 0.1,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-meta-data__item .cmsmasters-postmeta__inner > .cmsmasters-wrap-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'icon_space',
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-postmeta' => '--cmsmasters-icon-space: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'text_space_before',
			array(
				'label' => __( 'Text Before Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .content-before' => 'margin-right: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'text_space_after',
			array(
				'label' => __( 'Text After Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .content-after' => 'margin-left: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Update meta data widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function update_controls_separator() {
		$space_between_args = $this->get_controls( $this->separator_taxonomy->get_prefix( 'space_between' ) );
		$space_between_args_y = $space_between_args;

		$space_between_args['label'] = esc_html__( 'Space Between X', 'cmsmasters-elementor' );

		unset( $space_between_args_y['tab'] );
		unset( $space_between_args_y['section'] );

		$space_between_args_y['label'] = esc_html__( 'Space Between Y', 'cmsmasters-elementor' );
		$space_between_args_y['selectors'] = array(
			'{{WRAPPER}} .cmsmasters-postmeta[data-name="taxonomy"] .cmsmasters-postmeta__content' => '--cmsmasters-taxonomy-spacing-y: {{SIZE}}{{UNIT}};',
		);

		if ( isset( $space_between_args['selectors'][ $this->separator_taxonomy->get_sep_selector() ] ) ) {
			unset( $space_between_args['selectors'][ $this->separator_taxonomy->get_sep_selector() ] );
		}

		$space_between_args['selectors']['{{WRAPPER}} .cmsmasters-postmeta[data-name="taxonomy"] .cmsmasters-postmeta__content'] = '--cmsmasters-taxonomy-spacing: {{SIZE}}{{UNIT}};';

		$this->update_responsive_control( $this->separator_taxonomy->get_prefix( 'space_between' ), $space_between_args );

		$this->start_injection(
			array(
				'of' => $this->separator_taxonomy->get_prefix( 'space_between' ),
			)
		);

		$this->add_responsive_control( $this->separator_taxonomy->get_prefix( 'space_between_y' ), $space_between_args_y );

		$this->end_injection();
	}

	/**
	 * Update meta data widget controls.
	 *
	 * @since 1.11.5 Added vertical gap control for meta data items.
	 */
	protected function update_base_controls_separator() {
		$this->update_control(
			$this->separator->get_prefix( 'space_between' ),
			array(
				'label' => esc_html__( 'Space Between X', 'cmsmasters-elementor' ),
			)
		);

		$this->start_injection(
			array(
				'of' => $this->separator->get_prefix( 'space_between' ),
			)
		);

		$this->add_responsive_control(
			$this->separator->get_prefix( 'space_between_y' ),
			array(
				'label' => esc_html__( 'Space Between Y', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'max' => 50,
					),
					'vw' => array(
						'max' => 50,
					),
				),
				'size_units' => array( 'px', 'vw', 'em' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-meta-data' => '--cmsmasters-meta-data-spacing-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_injection();
	}

	/**
	 *
	 * Register meta-data widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_style_count() {
		$css_states = self::$css_states;
		$selector_normal = '{{WRAPPER}} .cmsmasters-postmeta[data-name="count"]';
		$selector_hover = '{{WRAPPER}} a.cmsmasters-postmeta[data-name="count"]:hover';
		$selector_active = '{{WRAPPER}} .cmsmasters-postmeta[data-name="count"].active';

		$css_states['active'] = __( 'Active', 'cmsmasters-elementor' );

		$this->start_controls_section(
			'section_style_count',
			array(
				'label' => __( 'Count', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'count_direction_icon',
			array(
				'label' => __( 'Icon Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'row',
				'options' => array(
					'row' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'row-reverse' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
					'column' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
				),
				'prefix_class' => 'cmsmasters-count--dir-',
				'selectors' => array(
					"{$selector_normal} .cmsmasters-postmeta__inner" => 'flex-direction: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_count_shape',
			array(
				'label' => __( 'Icon Shape', 'cmsmasters-elementor' ),
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'label_block' => false,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'square' => __( 'Square', 'cmsmasters-elementor' ),
					'circle' => __( 'Circle', 'cmsmasters-elementor' ),
				),
				'selectors_dictionary' => array(
					'square' => '0',
					'circle' => '50%',
				),
				'selectors' => array(
					"{$selector_normal} .cmsmasters-postmeta__inner > .cmsmasters-wrap-icon" => 'border-radius: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_count_shape_size',
			array(
				'label' => __( 'Icon Background Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
					'em' => array(
						'min' => 0.1,
						'max' => 2,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					$selector_normal => '--icon-count-shape-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'icon_count_shape!' => '',
				),
			)
		);

		$this->add_control(
			'icon_count_shape_prefix_class',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 'postmeta--icon-shape',
				'prefix_class' => 'cmsmasters-',
				'condition' => array(
					'icon_count_shape!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'icon_count_border',
				'selector' => "{$selector_normal} .cmsmasters-postmeta__inner > .cmsmasters-wrap-icon",
				'exclude' => array( 'color' ),
			)
		);

		$this->start_controls_tabs( 'count_style_tabs' );

		foreach ( $css_states as $state => $state_label ) {
			switch ( $state ) {
				case 'normal':
					$selector = $selector_normal;

					break;
				case 'hover':
					$selector = $selector_hover;

					break;
				case 'active':
					$selector = $selector_active;

					break;
			}

			$this->start_controls_tab(
				"count_style_tab_{$state}",
				array(
					'label' => $state_label,
				)
			);

			$this->add_control(
				"count_color_icon_{$state}",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$selector} .cmsmasters-postmeta__inner > .cmsmasters-wrap-icon" => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"count_color_text_{$state}",
				array(
					'label' => __( 'Link Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"icon_count_color_shape_{$state}",
				array(
					'label' => __( 'Shape Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$selector} .cmsmasters-postmeta__inner > .cmsmasters-wrap-icon" => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						'icon_count_shape!' => '',
					),
				)
			);

			$this->add_control(
				"icon_count_bg_color_{$state}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"count_color_bd_icon_{$state}",
				array(
					'label' => __( 'Icon Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$selector} .cmsmasters-postmeta__inner > .cmsmasters-wrap-icon" => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'icon_count_border_border!' => '',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'count_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator' => 'before',
				'selectors' => array(
					$selector_normal => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'count_bdrs',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 50,
					),
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					$selector_normal => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register meta data widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_style_taxonomy() {
		$selector = '{{WRAPPER}} .cmsmasters-postmeta[data-name="taxonomy"] a.term';
		$conditions = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'taxonomy_item_prefix_type',
							'value' => 'text',
						),
						array(
							'name' => 'taxonomy_item_prefix_text',
							'operator' => '!=',
							'value' => '',
						),
					),
				),
				array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'taxonomy_item_prefix_type',
							'value' => 'icon',
						),
						array(
							'name' => 'taxonomy_item_prefix_icon[value]',
							'operator' => '!=',
							'value' => '',
						),
					),
				),
			),
		);

		$this->start_controls_section(
			'section_style_taxonomy',
			array(
				'label' => __( 'Taxonomy', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'taxonomy_style_tabs' );

		foreach ( self::$css_states as $state => $state_label ) {
			$is_hover = 'hover' === $state;
			$selector_state = ( $is_hover ? $selector . ':hover' : $selector );

			$this->start_controls_tab(
				"taxonomy_style_tab_{$state}",
				array(
					'label' => $state_label,
				)
			);

			$this->add_control(
				"taxonomy_color_{$state}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_state => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"taxonomy_color_icon_{$state}",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						( ( 'normal' === $state ? '{{WRAPPER}} .cmsmasters-postmeta[data-name="taxonomy"]' : '{{WRAPPER}} .cmsmasters-postmeta[data-name="taxonomy"]:hover' ) . ' .cmsmasters-postmeta__inner > .cmsmasters-wrap-icon' ) => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"taxonomy_bg_{$state}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_state => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"taxonomy_terms_bd_color_{$state}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_state => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'taxonomy_terms_bd_border!' => 'none',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "taxonomy_box_shadow_{$state}",
					'selector' => $selector_state,
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "taxonomy_text_shadow_{$state}",
					'selector' => $selector_state,
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'taxonomy_item_heading',
			array(
				'label' => __( 'Taxonomy Item', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'taxonomy_item_prefix_popover',
			array(
				'label' => esc_html__( 'Prefix', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'frontend_available' => true,
				'render_type' => 'template',
			)
		);

		$this->start_popover();

		$this->add_control(
			'taxonomy_item_prefix_heading',
			array(
				'label' => __( 'Prefix', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array(
					'taxonomy_item_prefix_popover!' => '',
				),
			)
		);

		$this->add_control(
			'taxonomy_item_prefix_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'label_block' => false,
				'default' => 'text',
				'options' => array(
					'text' => esc_html__( 'Text', 'cmsmasters-elementor' ),
					'icon' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				),
				'condition' => array(
					'taxonomy_item_prefix_popover!' => '',
				),
			)
		);

		$this->add_control(
			'taxonomy_item_prefix_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'condition' => array(
					'taxonomy_item_prefix_type' => 'text',
					'taxonomy_item_prefix_popover!' => '',
				),
			)
		);

		$this->add_control(
			'taxonomy_item_prefix_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'condition' => array(
					'taxonomy_item_prefix_type' => 'icon',
					'taxonomy_item_prefix_popover!' => '',
				),
			)
		);

		$this->add_control(
			'taxonomy_item_prefix_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'label_block' => false,
				'default' => 'before',
				'options' => array(
					'before' => esc_html__( 'Before', 'cmsmasters-elementor' ),
					'after' => esc_html__( 'After', 'cmsmasters-elementor' ),
				),
				'condition' => array(
					'taxonomy_item_prefix_popover!' => '',
				),
				'conditions' => $conditions,
			)
		);

		$this->add_control(
			'taxonomy_item_prefix_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					"{$selector} .taxonomy-additional-content--before" => 'margin-right: {{SIZE}}{{UNIT}};',
					"{$selector} .taxonomy-additional-content--after" => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'taxonomy_item_prefix_popover!' => '',
				),
				'conditions' => $conditions,
			)
		);

		$this->add_control(
			'taxonomy_item_prefix_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					"{$selector} .taxonomy-additional-content" => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'taxonomy_item_prefix_popover!' => '',
				),
				'conditions' => $conditions,
			)
		);

		foreach ( self::$css_states as $state => $state_label ) {
			if ( 'normal' === $state ) {
				$control_label = __( 'Color', 'cmsmasters-elementor' );
				$selector_tab = "{$selector} .taxonomy-additional-content";
			} else {
				$selector_tab = "{$selector}:hover .taxonomy-additional-content";
				$control_label = __( 'Color Hover', 'cmsmasters-elementor' );
			}

			$this->add_control(
				"taxonomy_add_content_{$state}",
				array(
					'label' => $control_label,
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_tab => 'color: {{VALUE}};',
					),
					'condition' => array(
						'taxonomy_item_prefix_popover!' => '',
					),
				)
			);
		}

		$this->end_popover();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'taxonomy_terms_bd',
				'selector' => $selector,
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => __( 'None', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
					),
					'width' => array(
						'condition' => array(
							'border!' => array( 'default', 'none' ),
						),
					),
				),
			)
		);

		$this->add_control(
			'taxonomy_terms_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 50,
					),
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					$selector => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'taxonomy_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors' => array(
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->separator_taxonomy->add_controls();

		$this->end_controls_section();
	}

	/**
	 * Register meta data widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Fixed global repeater bug.
	 * @since 1.4.0 Added `Date Modified` and `Time Modified` controls.
	 */
	protected function register_controls_content() {
		$groups = static::get_groups();

		$repeater = new Repeater();

		$repeater->add_control(
			'group',
			array(
				'label' => __( 'Group', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'standard',
				'options' => wp_list_pluck( $groups, 'label' ),
			)
		);

		$default_meta_fields = $this->get_default_meta_fields();
		$groups_defaults = array();

		foreach ( $default_meta_fields as $default_meta_field ) {
			if ( ! isset( $default_meta_field['group'] ) ) {
				continue;
			}

			$groups_defaults[ $default_meta_field['group'] ] = $default_meta_field;
		}

		foreach ( $groups as $group => $group_data ) {
			$group_default = array_keys( $group_data['options'] )[0];

			if ( isset( $groups_defaults[ $group ][ "group_type_{$group}" ] ) ) {
				$group_default = $groups_defaults[ $group ][ "group_type_{$group}" ];
			}

			$repeater->add_control(
				"group_type_{$group}",
				array(
					'label' => __( 'Type', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => $group_default,
					'options' => $group_data['options'],
					'condition' => array(
						'group' => $group,
					),
				)
			);
		}

		$repeater->add_control(
			'comments_type',
			array(
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'label' => __( 'Displaying as?', 'cmsmasters-elementor' ),
				'options' => array(
					'' => __( 'Number', 'cmsmasters-elementor' ),
					'text' => __( 'With Text', 'cmsmasters-elementor' ),
				),
				'separator' => 'before',
				'condition' => array(
					'group' => 'count',
					'group_type_count' => 'comments',
				),
			)
		);

		$repeater->add_control(
			'comments_text',
			array(
				'type' => Controls_Manager::TEXT,
				'label' => __( 'Comments', 'cmsmasters-elementor' ),
				// translators: comments count
				'placeholder' => __( '%s comment', 'cmsmasters-elementor' ),
				'condition' => array(
					'group' => 'count',
					'group_type_count' => 'comments',
					'comments_type' => 'text',
				),
			)
		);

		$repeater->add_control(
			'comments_text_plural',
			array(
				'type' => Controls_Manager::TEXT,
				'label' => __( 'Comments (plural)', 'cmsmasters-elementor' ),
				// translators: comments count
				'placeholder' => __( '%s comments', 'cmsmasters-elementor' ),
				// translators: comments count
				'description' => __( 'You can use %s instead of comments number.', 'cmsmasters-elementor' ),
				'condition' => array(
					'group' => 'count',
					'group_type_count' => 'comments',
					'comments_type' => 'text',
				),
			)
		);

		$repeater->add_control(
			'comments_text_no',
			array(
				'type' => Controls_Manager::TEXT,
				'label' => __( 'No Comments', 'cmsmasters-elementor' ),
				'placeholder' => __( 'no comments', 'cmsmasters-elementor' ),
				'condition' => array(
					'group' => 'count',
					'group_type_count' => 'comments',
					'comments_type' => 'text',
				),
			)
		);

		$repeater->add_control(
			'comments_text_disabled',
			array(
				'type' => Controls_Manager::TEXT,
				'label' => __( 'Comments Disabled', 'cmsmasters-elementor' ),
				'placeholder' => __( 'comments disabled', 'cmsmasters-elementor' ),
				'condition' => array(
					'group' => 'count',
					'group_type_count' => 'comments',
					'comments_type' => 'text',
				),
			)
		);

		$repeater->add_control(
			'icon_enable',
			array(
				'label' => __( 'Enable Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'separator' => 'before',
				'condition' => array(
					'group!' => 'count',
				),
			)
		);

		$repeater->add_control(
			'icon_enable_count',
			array(
				'label' => __( 'Enable Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
				'condition' => array(
					'group' => 'count',
				),
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label' => __( 'Choose Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'description' => __( 'If none then the default icon is displayed.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'group',
									'operator' => '!=',
									'value' => 'count',
								),
								array(
									'name' => 'icon_enable',
									'operator' => '!=',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'group',
									'operator' => '==',
									'value' => 'count',
								),
								array(
									'name' => 'icon_enable_count',
									'operator' => '!=',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		/* < Author */
		$repeater->add_control(
			'author_avatar',
			array(
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Author Avatar', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'condition' => array(
					'group' => 'standard',
					'group_type_standard' => 'author',
				),
			)
		);
		$repeater->add_control(
			'author_link',
			array(
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Author Link', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'condition' => array(
					'group' => 'standard',
					'group_type_standard' => 'author',
				),
			)
		);
		/* > Author */

		/* < Data */
		$repeater->add_group_control(
			Group_Control_Format_Date::get_type(),
			array(
				'name' => 'date',
				'condition' => array(
					'group' => 'standard',
					'group_type_standard' => 'date',
				),
			)
		);

		$repeater->add_control(
			'date_modified',
			array(
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'label' => __( 'Date Modified', 'cmsmasters-elementor' ),
				'options' => array(
					'created' => __( 'Created', 'cmsmasters-elementor' ),
					'updated' => __( 'Updated', 'cmsmasters-elementor' ),
				),
				'default' => 'create',
				'label_block' => false,
				'toggle' => false,
				'condition' => array(
					'group' => 'standard',
					'group_type_standard' => 'date',
					'date_date_format!' => 'human_readable',
				),
			)
		);

		$repeater->add_control(
			'date_link',
			array(
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Date Link', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'condition' => array(
					'group' => 'standard',
					'group_type_standard' => 'date',
				),
			)
		);
		/* > Data */

		/* < Time */
		$repeater->add_group_control(
			Group_Control_Format_Time::get_type(),
			array(
				'name' => 'time',
				'condition' => array(
					'group' => 'standard',
					'group_type_standard' => 'time',
				),
			)
		);

		$repeater->add_control(
			'time_modified',
			array(
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'label' => __( 'Time Modified', 'cmsmasters-elementor' ),
				'options' => array(
					'created' => __( 'Created', 'cmsmasters-elementor' ),
					'updated' => __( 'Updated', 'cmsmasters-elementor' ),
				),
				'default' => 'create',
				'label_block' => false,
				'toggle' => false,
				'condition' => array(
					'group' => 'standard',
					'group_type_standard' => 'time',
					'time_time_format!' => 'human_readable',
				),
			)
		);
		/* > Time */

		$repeater->add_control(
			'keep_link',
			array(
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Keep Link', 'cmsmasters-elementor' ),
				'label_off' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_on' => __( 'No', 'cmsmasters-elementor' ),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-postmeta[data-name="count"][data-type="like"].active' => 'pointer-events: none;',
				),
				'condition' => array(
					'group' => 'count',
					'group_type_count' => 'like',
				),
			)
		);

		$repeater->add_control(
			'additional_text',
			array(
				'label' => __( 'Additional Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'group',
							'operator' => '!=',
							'value' => 'count',
						),
						array(
							'name' => 'group_type_count',
							'operator' => '!=',
							'value' => 'comments',
						),
						array(
							'name' => 'comments_type',
							'operator' => '!=',
							'value' => 'text',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'text_before_plural',
			array(
				'label' => __( 'Text Before', 'cmsmasters-elementor' ) . ' (' . esc_html__( 'plural', 'cmsmasters-elementor' ) . ')',
				'type' => Controls_Manager::TEXT,
				'condition' => array(
					'group' => 'count',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'group',
							'operator' => '!=',
							'value' => 'count',
						),
						array(
							'name' => 'group_type_count',
							'operator' => '!=',
							'value' => 'comments',
						),
						array(
							'name' => 'comments_type',
							'operator' => '!=',
							'value' => 'text',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'text_after_plural',
			array(
				'label' => __( 'Text After', 'cmsmasters-elementor' ) . ' (' . esc_html__( 'plural', 'cmsmasters-elementor' ) . ')',
				'type' => Controls_Manager::TEXT,
				'condition' => array(
					'group' => 'count',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'group',
							'operator' => '!=',
							'value' => 'count',
						),
						array(
							'name' => 'group_type_count',
							'operator' => '!=',
							'value' => 'comments',
						),
						array(
							'name' => 'comments_type',
							'operator' => '!=',
							'value' => 'text',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'text_before',
			array(
				'label' => __( 'Text Before', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'group',
							'operator' => '!=',
							'value' => 'count',
						),
						array(
							'name' => 'group_type_count',
							'operator' => '!=',
							'value' => 'comments',
						),
						array(
							'name' => 'comments_type',
							'operator' => '!=',
							'value' => 'text',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'text_after',
			array(
				'label' => __( 'Text After', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'group',
							'operator' => '!=',
							'value' => 'count',
						),
						array(
							'name' => 'group_type_count',
							'operator' => '!=',
							'value' => 'comments',
						),
						array(
							'name' => 'comments_type',
							'operator' => '!=',
							'value' => 'text',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'additional_text_inside_link',
			array(
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Additional Text Inside Link?', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'default' => '',
				'condition' => array(
					'group' => 'count',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'text_before',
							'operator' => '!=',
							'value' => '',
						),
						array(
							'name' => 'text_after',
							'operator' => '!=',
							'value' => '',
						),
						array(
							'name' => 'text_before_plural',
							'operator' => '!=',
							'value' => '',
						),
						array(
							'name' => 'text_after_plural',
							'operator' => '!=',
							'value' => '',
						),
					),
				),
			)
		);

		$repeater->add_control(
			'additional_text_after_avatar',
			array(
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'label' => __( 'Additional Text Before Position', 'cmsmasters-elementor' ),
				'options' => array(
					'' => __( 'Before Avatar', 'cmsmasters-elementor' ),
					'after' => __( 'After Avatar', 'cmsmasters-elementor' ),
				),
				'condition' => array(
					'group' => 'standard',
					'group_type_standard' => 'author',
					'author_avatar!' => '',
					'text_before!' => '',
				),
			)
		);

		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'meta_fields',
			array(
				'label' => __( 'Fields', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => $this->get_title_field_repeater(),
				'default' => $this->get_default_meta_fields(),
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
				'prefix_class' => 'cmsmasters-metadata-alignment%s-',
				'separator' => 'before',
			)
		);

		$this->separator->add_controls();

		$this->end_controls_section();
	}

	/**
	 * Get string for title_field field.
	 *
	 * @since 1.0.0
	 *
	 * @return string.
	 */
	protected function get_title_field_repeater() {
		return '<span style="text-transform: capitalize;">' .
			'<# if ( obj[ \'icon_enable\' ] ) { #>' .
				'<i class="{{ icon.value }}"></i>' .
			'<# } #>' .
			'<# var groupType = \'group_type_\' + group; #>' .
			'{{{ cmsmastersElementor.helpers.getOptionLabelRepeater( \'meta_fields\', groupType, obj[ groupType ] ) }}} - ' .
			'{{{ cmsmastersElementor.helpers.getOptionLabelRepeater( \'meta_fields\', \'group\', group ) }}}' .
		'</span>';
	}

	/**
	 * Get meta-field default settings.
	 *
	 * Default settings for `Fields` control.
	 *
	 * @since 1.0.0
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_meta_fields() {
		return array(
			array(
				'group' => 'standard',
				'group_type_standard' => 'date',
			),
			array(
				'group' => 'standard',
				'group_type_standard' => 'author',
			),
			array(
				'group' => 'taxonomy',
				'group_type_taxonomy' => array_keys( static::get_taxonomy_options() )[0],
			),
		);
	}

	/**
	 * Get the current group type.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_group() {
		return $this->meta_field['group'];
	}

	/**
	 * Retrieve the current group type.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_group_type() {
		return $this->meta_field[ "group_type_{$this->get_group()}" ];
	}

	/**
	 * Render author element.
	 *
	 * @since 1.0.0
	 */
	protected function render_author() {
		$settings = $this->get_settings_for_display();
		$avatar_devices = array( 'author_avatar_size', 'author_avatar_size_tablet', 'author_avatar_size_mobile' );
		$avatar_size = 0;

		foreach ( $avatar_devices as $avatar_device ) {
			if (
				empty( $settings[ $avatar_device ]['size'] ) ||
				$settings[ $avatar_device ]['size'] <= $avatar_size
			) {
				continue;
			}

			$avatar_size = $settings[ $avatar_device ]['size'];
		}

		$args = array(
			'avatar' => $this->meta_field['author_avatar'],
			'link' => $this->meta_field['author_link'],
		);

		if ( $avatar_size ) {
			$args['avatar_size'] = $avatar_size;
		}

		MetaDataModule::render_author( $args, $this->get_postmeta_args() );
	}

	/**
	 * Render taxonomy element.
	 *
	 * @since 1.0.0
	 */
	protected function render_taxonomy() {
		$settings = $this->get_settings_for_display();

		$args = array(
			'taxonomy' => $this->get_group_type(),
			'separator' => $this->separator_taxonomy->get_render(),
		);

		switch ( $settings['taxonomy_item_prefix_type'] ) {
			case 'icon':
				$args[ $settings['taxonomy_item_prefix_position'] ] = CmsmastersUtils::get_render_icon( $settings['taxonomy_item_prefix_icon'], array( 'aria-hidden' => 'true' ) );

				break;

			case 'text':
				$args[ $settings['taxonomy_item_prefix_position'] ] = $settings['taxonomy_item_prefix_text'];

				break;
		}

		MetaDataModule::render_taxonomy( $args, $this->get_postmeta_args() );
	}

	/**
	 * Render date element.
	 *
	 * @since 1.0.0
	 */
	protected function render_date() {
		MetaDataModule::render_date( 'date', $this->meta_field, $this->get_postmeta_args() );
	}

	/**
	 * Render time element.
	 *
	 * @since 1.0.0
	 */
	protected function render_time() {
		MetaDataModule::render_time( 'time', $this->meta_field, $this->get_postmeta_args() );
	}

	/**
	 * Render post-type element.
	 *
	 * @since 1.0.0
	 */
	protected function render_post_type() {
		MetaDataModule::render_post_type( $this->get_postmeta_args() );
	}

	/**
	 * Get placeholder text from repeater.
	 *
	 * @since 1.1.0
	 * @since 1.2.0 Fixed global repeater bug.
	 *
	 * @param string $control_id Control ID.
	 *
	 * @return string
	 */
	protected function get_settings_fallback_repeater( $control_id ) {
		$value = '';

		if ( ! empty( $this->meta_field[ $control_id ] ) ) {
			$value = $this->meta_field[ $control_id ];
		} else {
			$placeholders = array(
				'comments_text' => __( '%s comment', 'cmsmasters-elementor' ),
				'comments_text_plural' => __( '%s comments', 'cmsmasters-elementor' ),
				'comments_text_no' => __( 'no comments', 'cmsmasters-elementor' ),
				'comments_text_disabled' => __( 'comments disabled', 'cmsmasters-elementor' ),
			);

			if ( isset( $placeholders[ $control_id ] ) ) {
				$value = $placeholders[ $control_id ];
			}
		}

		return $value;
	}

	/**
	 * Get args for render meta-data.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Add comments count with text.
	 *
	 * @return array
	 */
	protected function get_postmeta_args() {
		$postmeta_args = array(
			'icon_enable' => $this->meta_field['icon_enable'],
			'icon' => $this->meta_field['icon'],
		);

		if ( 'count' === $this->meta_field['group'] ) {
			$postmeta_args['icon_enable'] = $this->meta_field['icon_enable_count'];

			if ( 'comments' === $this->meta_field['group_type_count'] && 'text' === $this->meta_field['comments_type'] ) {
				if ( comments_open() ) {
					$comments_number = get_comments_number();

					if ( $comments_number ) {
						$postmeta_args['text'] = sprintf(
							_n(
								$this->get_settings_fallback_repeater( 'comments_text' ),
								$this->get_settings_fallback_repeater( 'comments_text_plural' ),
								$comments_number,
								'cmsmasters-elementor'
							),
							number_format_i18n( $comments_number )
						);
					} else {
						$postmeta_args['text'] = $this->get_settings_fallback_repeater( 'comments_text_no' );
					}
				} else {
					$postmeta_args['text'] = $this->get_settings_fallback_repeater( 'comments_text_disabled' );
				}
			}
		} else {
			$postmeta_args['icon_enable'] = $this->meta_field['icon_enable'];
		}

		return $postmeta_args;
	}

	/**
	 * Render additional text.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 * @since 1.3.0 Added reading time meta.
	 */
	protected function render_additional_text( $side ) {
		$field_name = "text_{$side}";
		$text_single = CmsmastersUtils::get_if_isset( $this->meta_field, $field_name );
		$text_plural = CmsmastersUtils::get_if_isset( $this->meta_field, "{$field_name}_plural" );

		if (
			'count' === $this->get_group() &&
			$text_plural &&
			1 < MetaDataModule::get_count( $this->get_group_type() )
		) {
			$additional_text = $text_plural;
		} else {
			$additional_text = $text_single;
		}

		if ( ! $additional_text ) {
			if ( 'reading_time' !== $this->get_group_type() || 'before' === $side ) {
				return;
			}

			$additional_text = __( ' min read', 'cmsmasters-elementor' );
		}

		echo '<span class="content-side content-' . esc_attr( $side ) . '">' . esc_html( $additional_text ) . '</span>';
	}

	/**
	 * Render additional text-before.
	 *
	 * @since 1.0.0
	 */
	public function render_additional_text_before() {
		$this->render_additional_text( 'before' );
	}

	/**
	 * Render additional text-after.
	 *
	 * @since 1.0.0
	 */
	public function render_additional_text_after() {
		$this->render_additional_text( 'after' );
	}

	/**
	 * Render elements of count group.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added reading time meta.
	 */
	protected function render_count() {
		$postmeta_args = $this->get_postmeta_args();

		switch ( $this->get_group_type() ) {
			case 'comments':
				MetaDataModule::render_comments( $postmeta_args );

				break;
			case 'like':
				MetaDataModule::render_like( $postmeta_args );

				break;
			case 'view':
				MetaDataModule::render_view( $postmeta_args );

				break;
			case 'reading_time':
				MetaDataModule::render_reading_time( $postmeta_args );

				break;
		}
	}

	/**
	 * Render elements of standard group.
	 *
	 * @since 1.0.0
	 */
	protected function render_standard() {
		switch ( $this->get_group_type() ) {
			case 'author':
				$this->render_author();

				break;
			case 'date':
				$this->render_date();

				break;
			case 'time':
				$this->render_time();

				break;
			case 'post_type':
				$this->render_post_type();

				break;
		}
	}

	/**
	 * Check if the current meta-field is ready for display.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function check() {
		$group = $this->get_group();

		if ( 'taxonomy' === $group ) {
			return MetaDataModule::has_terms( $this->get_group_type() );
		}

		return true;
	}

	/**
	 * Retrieve the list of meta-fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_meta_fields() {
		if ( ! $this->meta_fields ) {
			$this->meta_fields = array_filter( $this->get_settings_for_display( 'meta_fields' ), function ( $meta_field ) {
				$this->set_meta_field( $meta_field );

				return $this->check();
			}, ARRAY_FILTER_USE_BOTH );
		}

		return $this->meta_fields;
	}

	/**
	 * Set current meta-field.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param array $meta_field
	 */
	private function set_meta_field( $meta_field ) {
		$this->meta_field = $meta_field;
	}

	/**
	 * Calls the `$callback` for each meta-field.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param callable $callback
	 */
	protected function loop_meta_fields( callable $callback ) {
		$meta_fields = $this->get_meta_fields();

		foreach ( $meta_fields as $meta_field ) {
			$this->set_meta_field( $meta_field );

			if ( is_callable( $callback ) ) {
				call_user_func( $callback );
			}
		}
	}

	/**
	 * Render element.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Add text-before after author avatar.
	 */
	protected function render_meta_field( $meta_fields ) {
		$group = $this->get_group();

		if ( $this->meta_field['additional_text_inside_link'] ) {
			add_action( "cmsmasters_elementor/postmeta/{$group}/render/start", array( $this, 'render_additional_text_before' ) );
			add_action( "cmsmasters_elementor/postmeta/{$group}/render/end", array( $this, 'render_additional_text_after' ) );
		}

		if ( $this->meta_field['additional_text_after_avatar'] ) {
			add_action( 'cmsmasters_elementor/postmeta/author/avatar/render/end', array( $this, 'render_additional_text_before' ) );
		}

		$attrs = array(
			'class' => array(
				'cmsmasters-meta-data__item',
				"elementor-repeater-item-{$this->meta_field['_id']}",
			),
		);

		echo '<div ' . Utils::render_html_attributes( $attrs ) . '>' .
			'<div class="cmsmasters-meta-data__item__inner">';

		if ( ! $this->meta_field['additional_text_inside_link'] && ! $this->meta_field['additional_text_after_avatar'] ) {
			$this->render_additional_text_before();
		}

		switch ( $this->get_group() ) {
			case 'taxonomy':
				$this->render_taxonomy();

				break;
			case 'standard':
				$this->render_standard();

				break;
			case 'count':
				$this->render_count();

				break;
		}

		if ( ! $this->meta_field['additional_text_inside_link'] ) {
			$this->render_additional_text_after();
		}

		echo '</div>';

		if ( 1 < count( $meta_fields ) ) {
			$this->separator->render();
		}

		echo '</div>';

		if ( $this->meta_field['additional_text_inside_link'] ) {
			remove_action( "cmsmasters_elementor/postmeta/{$group}/render/start", array( $this, 'render_additional_text_before' ) );
			remove_action( "cmsmasters_elementor/postmeta/{$group}/render/end", array( $this, 'render_additional_text_after' ) );
		}

		if ( $this->meta_field['additional_text_after_avatar'] ) {
			remove_action( 'cmsmasters_elementor/postmeta/author/avatar/render/end', array( $this, 'render_additional_text_before' ) );
		}
	}

	/**
	 * Render element.
	 *
	 * Generates the final HTML on the frontend.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$meta_fields = $this->get_meta_fields();

		if ( ! $meta_fields ) {
			return;
		}

		?>
		<div class="cmsmasters-meta-data">
			<div class="cmsmasters-meta-data__inner">
				<?php
				$this->loop_meta_fields( function () use ( $meta_fields ) {
					if ( $this->render_meta_field( $meta_fields ) ) {
						$this->render_meta_field( $meta_fields );
					}
				} );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {}

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
				'field' => 'taxonomy_item_prefix_text',
				'type' => esc_html__( 'Item Prefix', 'cmsmasters-elementor' ),
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
			'meta_fields' => array(
				array(
					'field' => 'comments_text',
					'type' => esc_html__( 'Comments', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'comments_text_plural',
					'type' => esc_html__( 'Comments (plural)', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'comments_text_no',
					'type' => esc_html__( 'No Comments', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'comments_text_disabled',
					'type' => esc_html__( 'Comments Disabled', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'text_before_plural',
					'type' => esc_html__( 'Text Before', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'text_after_plural',
					'type' => esc_html__( 'Text After', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'text_before',
					'type' => esc_html__( 'Text Before', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'text_after',
					'type' => esc_html__( 'Text After', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
			),
		);
	}
}
