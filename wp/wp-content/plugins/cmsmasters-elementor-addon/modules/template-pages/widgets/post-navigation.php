<?php
namespace CmsmastersElementor\Modules\TemplatePages\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TemplatePages\Traits\Singular_Widget;
use CmsmastersElementor\Modules\Animation\Classes\Animation as AnimationModule;
use CmsmastersElementor\Utils;
use CmsmastersElementor\Modules\Settings\Kit_Globals;

use Elementor\Controls_Manager;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon Post Navigation widget.
 *
 * Addon widget that displays navigation.
 *
 * @since 1.0.0
 */
class Post_Navigation extends Base_Widget {

	use Singular_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Post Navigation', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve test widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-post-navigation';
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
			'navigation',
			'previous',
			'next',
			'links',
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
			'widget-cmsmasters-post-navigation',
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
	 * Register test widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added background gradient to wrapper & icon.
	 * Fix for hover statements. Changed conditions for 'Icon' style section controls.
	 * @since 1.2.0 Fixed `Border Radius` for icon.
	 * @since 1.2.3 Fix for line-clamp css property.
	 * @since 1.3.3 Add CSS filter post nav image.
	 * @since 1.5.0 Added pointer animaniton settings for title.
	 * @since 1.10.1 Fixed deprecated control attribute `scheme` to `global`.
	 * @since 1.11.5 Fixed issues for Elementor 3.18.0.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {
		$show_content_yes = array(
			array(
				'name' => 'show_label',
				'operator' => '=',
				'value' => 'yes',
			),
			array(
				'name' => 'show_title',
				'operator' => '=',
				'value' => 'yes',
			),
		);

		$show_content_no = array(
			array(
				'name' => 'show_label',
				'operator' => '!==',
				'value' => 'yes',
			),
			array(
				'name' => 'show_title',
				'operator' => '!==',
				'value' => 'yes',
			),
		);

		$image_condition = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'navigation_graphic_element',
					'operator' => '!==',
					'value' => 'icon',
				),
				array(
					'name' => 'image_size',
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => 'wrapper_background_style',
					'operator' => '=',
					'value' => 'color',
				),
			),
		);

		$not_icon_not_background = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'navigation_graphic_element',
					'operator' => '!==',
					'value' => 'icon',
				),
				array(
					'name' => 'wrapper_background_style',
					'operator' => '!==',
					'value' => 'color',
				),
			),
		);

		// Selectors
		$icon_wrap = '{{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__icon-wrapper';
		$icon_wrap_hover = '{{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__link:hover .elementor-widget-cmsmasters-post-navigation__icon-wrapper';
		$link = '{{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__link';
		$prev = '{{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__prev';
		$next = '{{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__next';
		$hover_prev = ':hover span.elementor-widget-cmsmasters-post-navigation__prev';
		$hover_next = ':hover span.elementor-widget-cmsmasters-post-navigation__next';
		$separator = '{{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__separator';
		$separator_mobile = '(mobile-){{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__separator-wrapper .elementor-widget-cmsmasters-post-navigation__separator';

		$this->start_controls_section(
			'section_post_navigation_content',
			array( 'label' => __( 'Post Navigation', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'heading_title',
			array(
				'label' => __( 'Post Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'show_title',
			array(
				'label' => __( 'Visibility', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'prefix_class' => 'cmsmasters-show-title-',
				'render_type' => 'template',
			)
		);

		$this->add_responsive_control(
			'line_clamp_count',
			array(
				'label' => __( 'Number of Lines', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 1,
				'min' => 1,
				'max' => 5,
				'selectors' => array(
					"{$prev}-title, {$next}-title" => 'display: -webkit-box; ' .
						'-webkit-line-clamp: {{SIZE}}; ' .
						'-webkit-box-orient: vertical; ' .
						'overflow: hidden; ' .
						'white-space: normal;',
				),
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'wrapper_alignment_vertical',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'top',
				'selectors_dictionary' => array(
					'top' => 'flex-start',
					'center' => 'center',
					'bottom' => 'flex-end',
				),
				'label_block' => false,
				'toggle' => false,
				'selectors' => array(
					"{$link}" => 'align-items: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'show_title',
							'operator' => '=',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'line_clamp_count',
									'operator' => '=',
									'value' => '',
								),
								array(
									'name' => 'line_clamp_count',
									'operator' => '>',
									'value' => '1',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'heading_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_label',
			array(
				'label' => __( 'Visibility', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'prefix_class' => 'cmsmasters-show-label-',
				'render_type' => 'template',
			)
		);

		$this->start_controls_tabs(
			'label_tabs',
			array(
				'condition' => array( 'show_label' => 'yes' ),
			)
		);

		$this->start_controls_tab(
			'prev_label_tab',
			array( 'label' => __( 'Previous', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'prev_label',
			array(
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'Previous', 'cmsmasters-elementor' ),
				'placeholder' => __( 'Previous', 'cmsmasters-elementor' ),
				'condition' => array( 'show_label' => 'yes' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'next_label_tab',
			array( 'label' => __( 'Next', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'next_label',
			array(
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'Next', 'cmsmasters-elementor' ),
				'placeholder' => __( 'Next', 'cmsmasters-elementor' ),
				'condition' => array( 'show_label' => 'yes' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'navigation_label_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'before' => array(
						'title' => __( 'Before Title', 'cmsmasters-elementor' ),
					),
					'after' => array(
						'title' => __( 'After Title', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'before',
				'toggle' => false,
				'render_type' => 'template',
				'conditions' => array(
					'relation' => 'and',
					'terms' => $show_content_yes,
				),
			)
		);

		$this->add_control(
			'divider_after_title',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'navigation_graphic_element',
			array(
				'label' => __( 'Graphic Element', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
					),
					'image' => array(
						'title' => __( 'Image', 'cmsmasters-elementor' ),
						'description' => __( 'Featured Image of previous\next post', 'cmsmasters-elementor' ),
					),
					'both' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => __( 'Featured Image & Icon', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'icon',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-graph-element-',
				'render_type' => 'template',
				'conditions' => array(
					'relation' => 'or',
					'terms' => $show_content_yes,
				),
			)
		);

		$this->add_control(
			'heading_graph_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array( 'navigation_graphic_element' => 'both' ),
			)
		);

		$this->start_controls_tabs(
			'icon_tabs',
			array(
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => $show_content_no,
						),
						array(
							'name' => 'navigation_graphic_element',
							'operator' => '!==',
							'value' => 'image',
						),
					),
				),
			)
		);

			$this->start_controls_tab(
				'icon_left_tab',
				array( 'label' => __( 'Left', 'cmsmasters-elementor' ) )
			);

			$this->add_control(
				'icon_left',
				array(
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'default' => array(
						'value' => 'fas fa-angle-left',
						'library' => 'fa-solid',
					),
					'recommended' => array(
						'fa-solid' => array(
							'angle-left',
							'angle-right',
							'angle-double-left',
							'angle-double-right',
							'chevron-left',
							'chevron-right',
							'chevron-circle-left',
							'chevron-circle-right',
							'caret-left',
							'caret-right',
							'arrow-left',
							'arrow-right',
							'long-arrow-left',
							'long-arrow-right',
							'arrow-circle-left',
							'arrow-circle-right',
							'arrow-circle-o-left',
							'arrow-circle-o-right',
							'hand-point-left',
							'hand-point-right',
						),
					),
				)
			);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_right_tab',
			array( 'label' => __( 'Right', 'cmsmasters-elementor' ) )
		);

			$this->add_control(
				'icon_right',
				array(
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'default' => array(
						'value' => 'fas fa-angle-right',
						'library' => 'fa-solid',
					),
					'recommended' => array(
						'fa-solid' => array(
							'angle-left',
							'angle-right',
							'angle-double-left',
							'angle-double-right',
							'chevron-left',
							'chevron-right',
							'chevron-circle-left',
							'chevron-circle-right',
							'caret-left',
							'caret-right',
							'arrow-left',
							'arrow-right',
							'long-arrow-left',
							'long-arrow-right',
							'arrow-circle-left',
							'arrow-circle-right',
							'arrow-circle-o-left',
							'arrow-circle-o-right',
							'hand-point-left',
							'hand-point-right',
						),
					),
				)
			);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'icon_next_to_label',
			array(
				'label' => __( 'Icon Next to Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-icon-next-to-label-',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'navigation_graphic_element',
							'operator' => '=',
							'value' => 'icon',
						),
						array(
							'name' => 'show_label',
							'operator' => '=',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'heading_graph_image',
			array(
				'label' => __( 'Post Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'navigation_graphic_element' => 'both' ),
			)
		);

		$this->add_control(
			'wrapper_background_style',
			array(
				'label' => __( 'Background Style', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'color' => array(
						'title' => __( 'Color', 'cmsmasters-elementor' ),
						'description' => __( 'Post Image will be separated object', 'cmsmasters-elementor' ),
					),
					'image' => array(
						'title' => __( 'Image', 'cmsmasters-elementor' ),
						'description' => __( 'Post Image will used as background', 'cmsmasters-elementor' ),
					),
					'image-hover' => array(
						'title' => __( 'Hover', 'cmsmasters-elementor' ),
						'description' => __( 'Post Image will used as background only on hover', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'color',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-wrapper-bg-style-',
				'render_type' => 'template',
				'condition' => array( 'navigation_graphic_element!' => 'icon' ),
			)
		);

		$this->add_control(
			'fallback_image',
			array(
				'label' => __( 'Fallback Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
				'condition' => array( 'navigation_graphic_element!' => 'icon' ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'image',
				'default' => 'thumbnail',
				'separator' => 'none',
				'exclude' => array(
					'custom',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'relation' => 'or',
							'terms' => $show_content_yes,
						),
						array(
							'name' => 'navigation_graphic_element',
							'operator' => '!==',
							'value' => 'icon',
						),
						array(
							'name' => 'wrapper_background_style',
							'operator' => '=',
							'value' => 'color',
						),
					),
				),
			)
		);

		if ( ! empty( $this->get_controls( 'image_size' )['options'] ) ) {
			$options = $this->get_controls( 'image_size' )['options'];

			$unused_option = array_pop( $options );

			$this->update_control(
				'image_size',
				array( 'options' => $options )
			);
		}

		$this->add_responsive_control(
			'image_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array( 'size' => 80 ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
						'step' => 5,
					),
				),
				'render_type' => 'template',
				'selectors' => array(
					"{$link} img" => 'width: {{SIZE}}px;',
					"{$link} .elementor-widget-cmsmasters-post-navigation__no-image" => 'width: {{SIZE}}px !important;',
					"{$link} .elementor-widget-cmsmasters-post-navigation__no-image span" => 'font-size: {{SIZE}}px !important;',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'relation' => 'or',
							'terms' => $show_content_yes,
						),
						$image_condition,
					),
				),
			)
		);

		$this->add_responsive_control(
			'image_height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array( 'size' => 80 ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
						'step' => 5,
					),
				),
				'render_type' => 'template',
				'selectors' => array(
					"{$link} img" => 'height: {{SIZE}}px;',
					'{{WRAPPER}}.cmsmasters-nav-view-horizontal .elementor-widget-cmsmasters-post-navigation__link-prev, {{WRAPPER}}.cmsmasters-nav-view-horizontal .elementor-widget-cmsmasters-post-navigation__link-next' => 'height: {{SIZE}}px;',
					"{$link} .elementor-widget-cmsmasters-post-navigation__no-image" => 'height: {{SIZE}}px !important;',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'relation' => 'or',
							'terms' => $show_content_yes,
						),
						$image_condition,
					),
				),
			)
		);

		$image_condition_fit = $image_condition;

		$image_condition_fit['terms'][] = array(
			'name' => 'image_height',
			'operator' => '!==',
			'value' => '',
		);

		$this->add_control(
			'image-object-fit',
			array(
				'label' => __( 'Object Fit', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Disabled', 'cmsmasters-elementor' ),
					'fill' => __( 'Fill', 'cmsmasters-elementor' ),
					'cover' => __( 'Cover', 'cmsmasters-elementor' ),
					'contain' => __( 'Contain', 'cmsmasters-elementor' ),
					'scale-down' => __( 'Scale Down', 'cmsmasters-elementor' ),
					'none' => __( 'None', 'cmsmasters-elementor' ),
				),
				'default' => '',
				'prefix_class' => 'cmsmasters-object-fit cmsmasters-object-fit-',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'relation' => 'or',
							'terms' => $show_content_yes,
						),
						$image_condition_fit,
					),
				),
			)
		);

		$this->add_control(
			'show_spacer',
			array(
				'label' => __( 'Spacer', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-separator-',
			)
		);

		$this->add_control(
			'use_loop_navigation',
			array(
				'label' => __( 'Use Loop', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'default' => '',
			)
		);

		$this->add_control(
			'in_same_term_divider',
			array( 'type' => Controls_Manager::DIVIDER )
		);

		// Filter out post type without taxonomies
		$post_type_options = array();
		$post_type_taxonomies = array();
		foreach ( Utils::get_public_post_types() as $post_type => $post_type_label ) {
			$taxonomies = Utils::get_taxonomies( array( 'object_type' => $post_type ), false );
			if ( empty( $taxonomies ) ) {
				continue;
			}

			$post_type_options[ $post_type ] = $post_type_label;
			$post_type_taxonomies[ $post_type ] = array();
			foreach ( $taxonomies as $taxonomy ) {
				$post_type_taxonomies[ $post_type ][ $taxonomy->name ] = $taxonomy->label;
			}
		}

		$this->add_control(
			'in_same_term',
			array(
				'label' => __( 'In same Term', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'options' => $post_type_options,
				'default' => '',
				'multiple' => true,
				'label_block' => true,
				'description' => __( 'Indicates whether next post must be within the same taxonomy term as the current post, this lets you set a taxonomy per each post type', 'cmsmasters-elementor' ),
			)
		);

		foreach ( $post_type_options as $post_type => $post_type_label ) {
			$this->add_control(
				$post_type . '_taxonomy',
				array(
					'label' => $post_type_label . ' ' . __( 'Taxonomy', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => $post_type_taxonomies[ $post_type ],
					'default' => '',
					'condition' => array( 'in_same_term' => $post_type ),
				)
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'wrapper_style',
			array(
				'label' => __( 'Wrapper', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => $show_content_yes,
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_width',
			array(
				'label' => __( 'Wrapper Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 150,
						'max' => 750,
						'step' => 10,
					),
					'%' => array(
						'min' => 20,
						'max' => 50,
						'step' => 5,
					),
				),
				'selectors' => array(
					"{$link}" => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'divider_before_background_style',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'wrapper_border_style',
			array(
				'label' => __( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
					'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
					'ridge' => __( 'Ridge', 'cmsmasters-elementor' ),
					'inset' => __( 'Inset', 'cmsmasters-elementor' ),
					'outset' => __( 'Outset', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					"{$link}" => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_border_width',
			array(
				'label' => _x( 'Width', 'Border Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'default' => array(
					'top' => '1',
					'right' => '1',
					'bottom' => '1',
					'left' => '1',
				),
				'selectors' => array(
					"{$prev}" => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					"{$next}" => 'border-width: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				),
				'condition' => array(
					'wrapper_border_style!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					"{$prev}" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					"{$next}" => 'border-radius: {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'relation' => 'or',
							'terms' => $show_content_yes,
						),
					),
				),
			)
		);

		$this->start_controls_tabs( 'wrapper_tabs' );

		$this->start_controls_tab(
			'wrapper_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'wrapper_background_group_background',
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
			'wrapper_background',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					"{$link}:before" => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'wrapper_background_group_background' => array(
						'color',
						'gradient',
					),
				),
			)
		);

		$this->add_control(
			'wrapper_background_group_color_stop',
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
					'wrapper_background_group_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'wrapper_background_group_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'wrapper_background_group_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'wrapper_background_group_color_b_stop',
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
					'wrapper_background_group_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'wrapper_background_group_gradient_type',
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
					'wrapper_background_group_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'wrapper_background_group_gradient_angle',
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
					"{$link}:before" => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{wrapper_background_group_color_stop.SIZE}}{{wrapper_background_group_color_stop.UNIT}}, {{wrapper_background_group_color_b.VALUE}} {{wrapper_background_group_color_b_stop.SIZE}}{{wrapper_background_group_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'wrapper_background_group_background' => array( 'gradient' ),
					'wrapper_background_group_gradient_type' => 'linear',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'wrapper_background_group_gradient_position',
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
					"{$link}:before" => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{wrapper_background_group_color_stop.SIZE}}{{wrapper_background_group_color_stop.UNIT}}, {{wrapper_background_group_color_b.VALUE}} {{wrapper_background_group_color_b_stop.SIZE}}{{wrapper_background_group_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'wrapper_background_group_background' => array( 'gradient' ),
					'wrapper_background_group_gradient_type' => 'radial',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'wrapper_image_overlay',
			array(
				'label' => __( 'Image Overlay Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$link} a:before" => 'background-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'navigation_graphic_element',
							'operator' => '!==',
							'value' => 'icon',
						),
						array(
							'name' => 'wrapper_background_style',
							'operator' => '=',
							'value' => 'image',
						),
					),
				),
			)
		);

		$this->add_control(
			'wrapper_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => array( 'default' => Kit_Globals::COLOR_PRIMARY ),
				'selectors' => array(
					"{$link}" => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'wrapper_border_style!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'wrap_box_shadow',
				'selector' => "{$link}",
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'wrapper_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'wrapper_background_group_hover_background',
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
			'wrapper_background_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					"{$link}:after" => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'wrapper_background_group_hover_background' => array(
						'color',
						'gradient',
					),
				),
			)
		);

		$this->add_control(
			'wrapper_background_group_hover_color_stop',
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
					'wrapper_background_group_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'wrapper_background_group_hover_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'wrapper_background_group_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'wrapper_background_group_hover_color_b_stop',
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
					'wrapper_background_group_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'wrapper_background_group_hover_gradient_type',
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
					'wrapper_background_group_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'wrapper_background_group_hover_gradient_angle',
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
					"{$link}:after" => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{wrapper_background_group_hover_color_stop.SIZE}}{{wrapper_background_group_hover_color_stop.UNIT}}, {{wrapper_background_group_hover_color_b.VALUE}} {{wrapper_background_group_hover_color_b_stop.SIZE}}{{wrapper_background_group_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'wrapper_background_group_hover_background' => array( 'gradient' ),
					'wrapper_background_group_hover_gradient_type' => 'linear',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'wrapper_background_group_hover_gradient_position',
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
					"{$link}:after" => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{wrapper_background_group_hover_color_stop.SIZE}}{{wrapper_background_group_hover_color_stop.UNIT}}, {{wrapper_background_group_hover_color_b.VALUE}} {{wrapper_background_group_hover_color_b_stop.SIZE}}{{wrapper_background_group_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'wrapper_background_group_hover_background' => array( 'gradient' ),
					'wrapper_background_group_hover_gradient_type' => 'radial',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'wrapper_image_overlay_hover',
			array(
				'label' => __( 'Image Overlay Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$link}:hover a:before" => 'background-color: {{VALUE}};',
				),
				'conditions' => $not_icon_not_background,
			)
		);

		$this->add_control(
			'wrapper_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => array( 'default' => Kit_Globals::COLOR_PRIMARY ),
				'selectors' => array(
					"{$link}:hover" => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'wrapper_border_style!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'wrap_box_shadow_hover',
				'selector' => "{$link}:hover",
			)
		);

		$this->add_control(
			'wrapper_transition',
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
					"{$link}, {$link}:before, {$link}:after, {$link} a, {$link} a:before, {$icon_wrap}, {$link} a img, {$link} .elementor-widget-cmsmasters-post-navigation__no-image, {$link}-prev, {$link}-next" => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'divider_after_wrapper_tabs',
			array(
				'type' => Controls_Manager::DIVIDER,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'navigation_graphic_element',
									'operator' => '=',
									'value' => 'icon',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'navigation_graphic_element',
									'operator' => '!==',
									'value' => 'icon',
								),
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'wrapper_background_style',
											'operator' => '!==',
											'value' => 'color',
										),
										array(
											'relation' => 'and',
											'terms' => array(
												array(
													'name' => 'wrapper_background_style',
													'operator' => '=',
													'value' => 'color',
												),
											),
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					"{$prev}" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					"{$next}" => 'padding: {{TOP}}{{UNIT}} {{LEFT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{RIGHT}}{{UNIT}};',
				),
				'default' => array(
					'top' => '15',
					'bottom' => '15',
					'left' => '15',
					'right' => '15',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'navigation_graphic_element',
									'operator' => '=',
									'value' => 'icon',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'navigation_graphic_element',
									'operator' => '!==',
									'value' => 'icon',
								),
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'wrapper_background_style',
											'operator' => '!==',
											'value' => 'color',
										),
										array(
											'relation' => 'and',
											'terms' => array(
												array(
													'name' => 'wrapper_background_style',
													'operator' => '=',
													'value' => 'color',
												),
											),
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_style',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => $show_content_yes,
				),
			)
		);

		$this->add_responsive_control(
			'content_gap_between',
			array(
				'label' => __( 'Vertical Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 50,
						'step' => 5,
					),
				),
				'selectors' => array(
					"{$link}-prev span + span, {$link}-next span + span" => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'label_divider',
			array(
				'type' => Controls_Manager::DIVIDER,
				'condition' => array( 'show_label' => 'yes' ),
			)
		);

		$this->add_control(
			'heading_label_style',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array( 'show_label' => 'yes' ),
			)
		);

		$this->start_controls_tabs(
			'tabs_label_style',
			array( 'condition' => array( 'show_label' => 'yes' ) )
		);

		$this->start_controls_tab(
			'label_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'label_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => array( 'default' => Kit_Globals::COLOR_TEXT ),
				'selectors' => array(
					"{$prev}-label" => 'color: {{VALUE}};',
					"{$next}-label" => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'label_typography',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_SECONDARY ),
				'selector' => "{$prev}-label, {$next}-label",
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'label_text_shadow',
				'selector' => "{$prev}-label, {$next}-label",
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'label_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'label_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$link}{$hover_prev}-label" => 'color: {{VALUE}};',
					"{$link}{$hover_next}-label" => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'label_typography_hover',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_SECONDARY ),
				'selector' => "{$link}{$hover_prev}-label, {$link}{$hover_next}-label",
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'label_text_shadow_hover',
				'selector' => "{$link}{$hover_prev}-label, {$link}{$hover_next}-label",
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'title_divider',
			array(
				'type' => Controls_Manager::DIVIDER,
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->add_control(
			'heading_title_style',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array( 'show_title' => 'yes' ),
			)
		);

		$this->start_controls_tabs(
			'tabs_title_style',
			array( 'condition' => array( 'show_title' => 'yes' ) )
		);

		$this->start_controls_tab(
			'tab_title_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => array( 'default' => Kit_Globals::COLOR_TEXT ),
				'selectors' => array(
					"{$prev}-title, {$next}-title" => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_typography',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_SECONDARY ),
				'selector' => "{$prev}-title, {$next}-title",
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'title_text_shadow',
				'selector' => "{$prev}-title, {$next}-title",
				'condition' => array( 'line_clamp_count' => '' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$link}{$hover_prev}-title, {$link}{$hover_next}-title" => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_typography_hover',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_SECONDARY ),
				'selector' => "{$link}{$hover_prev}-title, {$link}{$hover_next}-title",
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'title_text_shadow_hover',
				'selector' => "{$link}{$hover_prev}-title, {$link}{$hover_next}-title",
				'condition' => array( 'line_clamp_count' => '' ),
			)
		);

		$this->add_control(
			'content_transition',
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
					"{$prev}-label, {$next}-label, {$prev}-title, {$next}-title" => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'content_blend_mode',
			array(
				'label' => __( 'Blend Mode', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Normal', 'cmsmasters-elementor' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'difference' => 'Difference',
					'exclusion' => 'Exclusion',
					'hue' => 'Hue',
					'luminosity' => 'Luminosity',
				),
				'separator' => 'before',
				'selectors' => array(
					"{$prev}-label" => 'mix-blend-mode: {{VALUE}}',
					"{$next}-label" => 'mix-blend-mode: {{VALUE}}',
					"{$prev}-title" => 'mix-blend-mode: {{VALUE}}',
					"{$next}-title" => 'mix-blend-mode: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'icon_style',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => $show_content_no,
						),
						array(
							'name' => 'navigation_graphic_element',
							'operator' => '!==',
							'value' => 'image',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 6,
						'max' => 300,
					),
				),
				'selectors' => array(
					"{$icon_wrap}" => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_margin',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}}' => '--icon-margin: {{SIZE}}{{UNIT}};',
				),
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 5,
					),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => $show_content_yes,
				),
			)
		);

		$this->add_control(
			'icon_border_style',
			array(
				'label' => __( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
					'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
					'ridge' => __( 'Ridge', 'cmsmasters-elementor' ),
					'inset' => __( 'Inset', 'cmsmasters-elementor' ),
					'outset' => __( 'Outset', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					"{$icon_wrap}" => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_border_width',
			array(
				'label' => _x( 'Width', 'Border Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					"{$icon_wrap}" => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'icon_border_style',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			)
		);

		$this->start_controls_tabs( 'tabs_post_navigation_icon_style' );

		$this->start_controls_tab(
			'icon_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$icon_wrap}" => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_background_color_group_normal_background',
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
			'icon_background_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					"{$icon_wrap}:before" => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'icon_background_color_group_normal_background' => array(
						'color',
						'gradient',
					),
				),
			)
		);

		$this->add_control(
			'icon_background_color_group_normal_color_stop',
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
					'icon_background_color_group_normal_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'icon_background_color_group_normal_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'icon_background_color_group_normal_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'icon_background_color_group_normal_color_b_stop',
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
					'icon_background_color_group_normal_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'icon_background_color_group_normal_gradient_type',
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
					'icon_background_color_group_normal_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'icon_background_color_group_normal_gradient_angle',
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
					"{$icon_wrap}:before" => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{icon_background_color_group_normal_color_stop.SIZE}}{{icon_background_color_group_normal_color_stop.UNIT}}, {{icon_background_color_group_normal_color_b.VALUE}} {{icon_background_color_group_normal_color_b_stop.SIZE}}{{icon_background_color_group_normal_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'icon_background_color_group_normal_background' => array( 'gradient' ),
					'icon_background_color_group_normal_gradient_type' => 'linear',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'icon_background_color_group_normal_gradient_position',
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
					"{$icon_wrap}:before" => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{icon_background_color_group_normal_color_stop.SIZE}}{{icon_background_color_group_normal_color_stop.UNIT}}, {{icon_background_color_group_normal_color_b.VALUE}} {{icon_background_color_group_normal_color_b_stop.SIZE}}{{icon_background_color_group_normal_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'icon_background_color_group_normal_background' => array( 'gradient' ),
					'icon_background_color_group_normal_gradient_type' => 'radial',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'icon_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$icon_wrap}" => 'border-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'icon_border_style',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'icon_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$icon_wrap_hover}" => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_background_color_group_hover_background',
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
			'icon_background_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					"{$icon_wrap}:after" => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'icon_background_color_group_hover_background' => array(
						'color',
						'gradient',
					),
				),
			)
		);

		$this->add_control(
			'icon_background_color_group_hover_color_stop',
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
					'icon_background_color_group_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'icon_background_color_group_hover_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'icon_background_color_group_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'icon_background_color_group_hover_color_b_stop',
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
					'icon_background_color_group_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'icon_background_color_group_hover_gradient_type',
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
					'icon_background_color_group_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'icon_background_color_group_hover_gradient_angle',
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
					"{$icon_wrap}:after" => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{icon_background_color_group_hover_color_stop.SIZE}}{{icon_background_color_group_hover_color_stop.UNIT}}, {{icon_background_color_group_hover_color_b.VALUE}} {{icon_background_color_group_hover_color_b_stop.SIZE}}{{icon_background_color_group_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'icon_background_color_group_hover_background' => array( 'gradient' ),
					'icon_background_color_group_hover_gradient_type' => 'linear',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'icon_background_color_group_hover_gradient_position',
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
					"{$icon_wrap}:after" => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{icon_background_color_group_hover_color_stop.SIZE}}{{icon_background_color_group_hover_color_stop.UNIT}}, {{icon_background_color_group_hover_color_b.VALUE}} {{icon_background_color_group_hover_color_b_stop.SIZE}}{{icon_background_color_group_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'icon_background_color_group_hover_background' => array( 'gradient' ),
					'icon_background_color_group_hover_gradient_type' => 'radial',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'icon_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$icon_wrap_hover}" => 'border-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'icon_border_style',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			)
		);

		$this->add_control(
			'icon_border_transition',
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
					"{$icon_wrap}, {$icon_wrap}:before, {$icon_wrap}:after" => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'icon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__icon-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__icon-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					"{$icon_wrap}:before, {$icon_wrap}:after" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'image_style',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'navigation_graphic_element!' => 'icon',
					'wrapper_background_style' => 'color',
				),
			)
		);

		$this->add_control(
			'image_alignment_vertical',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'top',
				'selectors_dictionary' => array(
					'top' => 'flex-start',
					'center' => 'center',
					'bottom' => 'flex-end',
				),
				'label_block' => true,
				'toggle' => false,
				'selectors' => array(
					"{$link} img,
					{$link} .elementor-widget-cmsmasters-post-navigation__no-image" => 'align-self: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_margin',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}}' => '--image-margin: {{SIZE}}{{UNIT}};',
				),
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 5,
					),
				),
			)
		);

		$this->start_controls_tabs( 'post_navigation_image_style' );

		$this->start_controls_tab(
			'image_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'image_background_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$link} a img" => 'background-color: {{VALUE}};',
					"{$link} a .elementor-widget-cmsmasters-post-navigation__no-image" => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => "css_filters_normal",
				'selector' => "{$link} a img",
				'condition' => array(
					'navigation_graphic_element!' => 'icon',
				),
			)
		);

		$this->add_control(
			'image_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$link} a img" => 'border-color: {{VALUE}};',
					"{$link} a .elementor-widget-cmsmasters-post-navigation__no-image" => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'image_border_style!' => '' ),
			)
		);

		$this->add_control(
			'image_border_style',
			array(
				'label' => __( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
					'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
					'ridge' => __( 'Ridge', 'cmsmasters-elementor' ),
					'inset' => __( 'Inset', 'cmsmasters-elementor' ),
					'outset' => __( 'Outset', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					"{$link} a img" => 'border-style: {{VALUE}};',
					"{$link} a .elementor-widget-cmsmasters-post-navigation__no-image" => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_border_width',
			array(
				'label' => _x( 'Width', 'Border Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					"{$link} a img" => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					"{$link} a .elementor-widget-cmsmasters-post-navigation__no-image" => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'image_border_style!' => '' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'image_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'image_background_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$link} a:hover img" => 'background-color: {{VALUE}};',
					"{$link} a:hover .elementor-widget-cmsmasters-post-navigation__no-image" => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => "css_filters_hover",
				'selector' => "{$link} a:hover img",
				'condition' => array(
					'navigation_graphic_element!' => 'icon',
				),
			)
		);

		$this->add_control(
			'image_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$link} a:hover img" => 'border-bottom-color: {{VALUE}};',
					"{$link} a:hover .elementor-widget-cmsmasters-post-navigation__no-image" => 'border-bottom-color: {{VALUE}};',
				),
				'condition' => array( 'image_border_style!' => '' ),
			)
		);

		$this->add_control(
			'image_border_style_hover',
			array(
				'label' => __( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
					'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
					'ridge' => __( 'Ridge', 'cmsmasters-elementor' ),
					'inset' => __( 'Inset', 'cmsmasters-elementor' ),
					'outset' => __( 'Outset', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					"{$link} a:hover img" => 'border-style: {{VALUE}};',
					"{$link} a:hover .elementor-widget-cmsmasters-post-navigation__no-image" => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_border_width_hover',
			array(
				'label' => _x( 'Width', 'Border Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					"{$link} a:hover img" => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					"{$link} a:hover .elementor-widget-cmsmasters-post-navigation__no-image" => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'image_border_style_hover!' => '' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'image_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					"{$link} a img" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					"{$link} a .elementor-widget-cmsmasters-post-navigation__no-image" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'separator_section_style',
			array(
				'label' => __( 'Spacer', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'show_spacer!' => '' ),
			)
		);

		$this->add_responsive_control(
			'separator_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					"{$separator}" => 'margin: 0 calc({{SIZE}}{{UNIT}} / 2);',
					"{$separator_mobile}" => 'margin: calc({{SIZE}}{{UNIT}} / 2) auto;',
				),
			)
		);

		$this->add_control(
			'heading_separator',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'separator_type',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => _x( 'None', 'Separator Style', 'cmsmasters-elementor' ),
					'solid' => _x( 'Solid', 'Separator Style', 'cmsmasters-elementor' ),
					'double' => _x( 'Double', 'Separator Style', 'cmsmasters-elementor' ),
					'dotted' => _x( 'Dotted', 'Separator Style', 'cmsmasters-elementor' ),
					'dashed' => _x( 'Dashed', 'Separator Style', 'cmsmasters-elementor' ),
					'groove' => _x( 'Groove', 'Separator Style', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'selectors' => array(
					"{$separator}" => 'border-left-style: {{VALUE}};',
					"{$separator_mobile}" => 'border-top-style: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'separator_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#D4D4D4',
				'selectors' => array(
					"{$separator}" => 'border-left-color: {{VALUE}};',
					"{$separator_mobile}" => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__wrap' => 'color: {{VALUE}};',
				),
				'condition' => array( 'separator_type!' => 'none' ),
			)
		);

		$this->add_responsive_control(
			'separator_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'selectors' => array(
					"{$separator}" => 'border-left-width: {{SIZE}}{{UNIT}}',
					"{$separator_mobile}" => 'border-left-width: 0; border-top-width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'separator_type!' => 'none' ),
			)
		);

		$this->add_responsive_control(
			'separator_height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'step' => 5,
					),
					'%' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					"{$separator}" => 'height: {{SIZE}}{{UNIT}}',
					"{$separator_mobile}" => 'width: {{SIZE}}{{UNIT}}; height: auto;',
				),
				'condition' => array( 'separator_type!' => 'none' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'background_image_style',
			array(
				'label' => __( 'Background Image', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'relation' => 'or',
							'terms' => $show_content_yes,
						),
						array(
							'name' => 'navigation_graphic_element',
							'operator' => '!==',
							'value' => 'icon',
						),
						array(
							'name' => 'wrapper_background_style',
							'operator' => '!==',
							'value' => 'color',
						),
					),
				),
			)
		);

		$this->start_controls_tabs( 'navigation_tabs' );

		foreach ( array(
			'prev' => __( 'Previous', 'cmsmasters-elementor' ),
			'next' => __( 'Next', 'cmsmasters-elementor' ),
		) as $nav_key => $label ) {
			$nav_selector = '{{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__' . $nav_key;

			$this->start_controls_tab(
				"navigation_tab_{$nav_key}",
				array(
					'label' => $label,
				)
			);

			$this->add_responsive_control(
				"background_image_hover_position_{$nav_key}",
				array(
					'label' => _x( 'Position', 'Background Control', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => array(
						'top -40em center' => _x( 'Top', 'Background Control', 'cmsmasters-elementor' ),
						'top -40em right -40em' => _x( 'Top Right', 'Background Control', 'cmsmasters-elementor' ),
						'center right -40em' => _x( 'Right', 'Background Control', 'cmsmasters-elementor' ),
						'bottom -40em right -40em' => _x( 'Bottom Right', 'Background Control', 'cmsmasters-elementor' ),
						'bottom -40em center' => _x( 'Bottom', 'Background Control', 'cmsmasters-elementor' ),
						'bottom -40em left -40em' => _x( 'Bottom Left', 'Background Control', 'cmsmasters-elementor' ),
						'center left -40em' => _x( 'Left', 'Background Control', 'cmsmasters-elementor' ),
						'top -40em left -40em' => _x( 'Top Left', 'Background Control', 'cmsmasters-elementor' ),
					),
					'default' => 'top -40em center',
					'prefix_class' => 'cmsmasters-bg-image-position-',
					'selectors' => array(
						$nav_selector => 'background-size: cover; background-repeat: no-repeat; background-position: {{VALUE}};',
						"{$nav_selector}:hover" => 'background-position: center;',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'wrapper_background_style',
								'operator' => '=',
								'value' => 'image-hover',
							),
						),
					),
				)
			);

			$this->add_responsive_control(
				"background_image_position_{$nav_key}",
				array(
					'label' => _x( 'Position', 'Background Control', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => array(
						'' => _x( 'Default', 'Background Control', 'cmsmasters-elementor' ),
						'top left' => _x( 'Top Left', 'Background Control', 'cmsmasters-elementor' ),
						'top center' => _x( 'Top Center', 'Background Control', 'cmsmasters-elementor' ),
						'top right' => _x( 'Top Right', 'Background Control', 'cmsmasters-elementor' ),
						'center left' => _x( 'Center Left', 'Background Control', 'cmsmasters-elementor' ),
						'center center' => _x( 'Center Center', 'Background Control', 'cmsmasters-elementor' ),
						'center right' => _x( 'Center Right', 'Background Control', 'cmsmasters-elementor' ),
						'bottom left' => _x( 'Bottom Left', 'Background Control', 'cmsmasters-elementor' ),
						'bottom center' => _x( 'Bottom Center', 'Background Control', 'cmsmasters-elementor' ),
						'bottom right' => _x( 'Bottom Right', 'Background Control', 'cmsmasters-elementor' ),
						'initial' => _x( 'Custom', 'Background Control', 'cmsmasters-elementor' ),
					),
					'default' => 'center center',
					'selectors' => array(
						$nav_selector => 'background-position: {{VALUE}};',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'wrapper_background_style',
								'operator' => '=',
								'value' => 'image',
							),
						),
					),
				)
			);

			$this->add_responsive_control(
				"background_image_position_x_{$nav_key}",
				array(
					'label' => _x( 'X Position', 'Background Control', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', '%', 'vw' ),
					'default' => array(
						'unit' => 'px',
						'size' => 0,
					),
					'tablet_default' => array(
						'unit' => 'px',
						'size' => 0,
					),
					'mobile_default' => array(
						'unit' => 'px',
						'size' => 0,
					),
					'range' => array(
						'px' => array(
							'min' => -800,
							'max' => 800,
						),
						'em' => array(
							'min' => -100,
							'max' => 100,
						),
						'%' => array(
							'min' => -100,
							'max' => 100,
						),
						'vw' => array(
							'min' => -100,
							'max' => 100,
						),
					),
					'selectors' => array(
						$nav_selector => 'background-position: {{SIZE}}{{UNIT}} {{background_image_position_y.SIZE}}{{background_image_position_y.UNIT}}',
					),
					'required' => true,
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'wrapper_background_style',
								'operator' => '=',
								'value' => 'image',
							),
							array(
								'name' => "background_image_position_{$nav_key}",
								'operator' => '=',
								'value' => 'initial',
							),
						),
					),
				)
			);

			$this->add_responsive_control(
				"background_image_position_y_{$nav_key}",
				array(
					'label' => _x( 'Y Position', 'Background Control', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', '%', 'vh' ),
					'default' => array(
						'unit' => 'px',
						'size' => 0,
					),
					'tablet_default' => array(
						'unit' => 'px',
						'size' => 0,
					),
					'mobile_default' => array(
						'unit' => 'px',
						'size' => 0,
					),
					'range' => array(
						'px' => array(
							'min' => -800,
							'max' => 800,
						),
						'em' => array(
							'min' => -100,
							'max' => 100,
						),
						'%' => array(
							'min' => -100,
							'max' => 100,
						),
						'vh' => array(
							'min' => -100,
							'max' => 100,
						),
					),
					'selectors' => array(
						$nav_selector => 'background-position: {{background_image_position_x.SIZE}}{{background_image_position_x.UNIT}} {{SIZE}}{{UNIT}}',
					),
					'required' => true,
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'wrapper_background_style',
								'operator' => '=',
								'value' => 'image',
							),
							array(
								'name' => "background_image_position_{$nav_key}",
								'operator' => '=',
								'value' => 'initial',
							),
						),
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'background_image_attachment',
			array(
				'label' => _x( 'Attachment', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => _x( 'Default', 'Background Control', 'cmsmasters-elementor' ),
					'scroll' => _x( 'Scroll', 'Background Control', 'cmsmasters-elementor' ),
					'fixed' => _x( 'Fixed', 'Background Control', 'cmsmasters-elementor' ),
				),
				'default' => '',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__link' => 'background-attachment: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'wrapper_background_style',
							'operator' => '=',
							'value' => 'image',
						),
					),
				),
			)
		);

		$this->add_control(
			'background_image_attachment_alert',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-control-field-description',
				'raw' => __( 'Note: Attachment Fixed works only on desktop.', 'cmsmasters-elementor' ),
				'separator' => 'none',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'wrapper_background_style',
							'operator' => '=',
							'value' => 'image',
						),
						array(
							'name' => 'background_image_attachment',
							'operator' => '=',
							'value' => 'fixed',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'background_image_repeat',
			array(
				'label' => _x( 'Repeat', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					'' => _x( 'Default', 'Background Control', 'cmsmasters-elementor' ),
					'no-repeat' => _x( 'No-repeat', 'Background Control', 'cmsmasters-elementor' ),
					'repeat' => _x( 'Repeat', 'Background Control', 'cmsmasters-elementor' ),
					'repeat-x' => _x( 'Repeat-x', 'Background Control', 'cmsmasters-elementor' ),
					'repeat-y' => _x( 'Repeat-y', 'Background Control', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__link' => 'background-repeat: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'wrapper_background_style',
							'operator' => '=',
							'value' => 'image',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'background_image_size',
			array(
				'label' => _x( 'Size', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => _x( 'Default', 'Background Control', 'cmsmasters-elementor' ),
					'auto' => _x( 'Auto', 'Background Control', 'cmsmasters-elementor' ),
					'cover' => _x( 'Cover', 'Background Control', 'cmsmasters-elementor' ),
					'contain' => _x( 'Contain', 'Background Control', 'cmsmasters-elementor' ),
					'initial' => _x( 'Custom', 'Background Control', 'cmsmasters-elementor' ),
				),
				'default' => 'cover',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__link' => 'background-size: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'wrapper_background_style',
							'operator' => '=',
							'value' => 'image',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'background_image_bg_width',
			array(
				'label' => _x( 'Width', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'vw' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'vw' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array(
					'size' => 100,
					'unit' => '%',
				),
				'required' => true,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-post-navigation__link' => 'background-size: {{SIZE}}{{UNIT}} auto',

				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'wrapper_background_style',
							'operator' => '=',
							'value' => 'image',
						),
						array(
							'name' => 'background_image_size',
							'operator' => '=',
							'value' => 'initial',
						),
					),
				),
			)
		);

		$this->end_controls_section();

		$condition = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'show_title',
					'operator' => '!==',
					'value' => '',
				),
			),
		);

		AnimationModule::register_sections_controls( $this, false, $condition );
	}

	/**
	 * Render Post Navigation widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Added pointer animaniton for title.
	 */
	protected function render() {
		$settings = $this->get_active_settings();
		$direction = array( 'prev', 'next' );
		$nav_atts = array();

		$in_same_term = false;
		$taxonomy = 'category';
		$post_type = get_post_type( get_queried_object_id() );

		if (
			! empty( $settings['in_same_term'] ) &&
			is_array( $settings['in_same_term'] ) &&
			in_array( $post_type, $settings['in_same_term'], true )
		) {
			if ( isset( $settings[ $post_type . '_taxonomy' ] ) ) {
				$in_same_term = true;
				$taxonomy = $settings[ $post_type . '_taxonomy' ];
			}
		}

		foreach ( $direction as $side ) {
			$nav_atts[ $side ]['side'] = $side;
			$nav_atts[ $side ]['label'] = '';
			$nav_atts[ $side ]['attach'] = '';
			$nav_atts[ $side ]['title'] = '';
			$nav_atts[ $side ]['attach_icon'] = '';
			$nav_atts[ $side ]['post_id'] = '';

			if ( '' === $settings[ $side . '_label' ] ) {
				if ( 'prev' === $side ) {
					$label = 'Previous';
				} else {
					$label = 'Next';
				}
			} else {
				$label = $settings[ $side . '_label' ];
			}

			if (
				'yes' === $settings['show_label'] &&
				(
					(
						'yes' === $settings['icon_next_to_label'] &&
						'icon' === $settings['navigation_graphic_element']
					) ||
					'both' === $settings['navigation_graphic_element']
				)
			) {
				if ( 'prev' === $side ) {
					$icon_left = $this->get_icon_html( $settings, $side );
					$icon_right = '';
				} else {
					$icon_left = '';
					$icon_right = $this->get_icon_html( $settings, $side );
				}

				$nav_atts[ $side ]['label'] = "<span class=\"elementor-widget-cmsmasters-post-navigation__{$side}-label\">{$icon_left}{$label}{$icon_right}</span>";
			} else {
				$nav_atts[ $side ]['label'] = "<span class=\"elementor-widget-cmsmasters-post-navigation__{$side}-label\">{$label}</span>";
			}

			if (
				(
					'yes' !== $settings['show_label'] &&
					'yes' !== $settings['show_title']
				) ||
				'icon' === $settings['navigation_graphic_element']
			) {
				if (
					'yes' !== $settings['show_label'] ||
					'yes' !== $settings['icon_next_to_label']
				) {
					$nav_atts[ $side ]['attach'] = $this->get_icon_html( $settings, $side );
				} else {
					$nav_atts[ $side ]['attach'] = '';
				}
			} else {
				if (
					'both' === $settings['navigation_graphic_element'] &&
					'' === $settings['icon_next_to_label']
				) {
					$nav_atts[ $side ]['attach_icon'] = $this->get_icon_html( $settings, $side, ' cmsmasters-image-and-icon', $this->get_render_attribute_string( 'same-height-style' ) );
				}

				$prev_id = isset( get_previous_post()->ID ) ? get_previous_post()->ID : '';
				$next_id = isset( get_next_post()->ID ) ? get_next_post()->ID : '';

				if ( ! empty( $settings['use_loop_navigation'] ) ) {
					$target_post = $this->get_target_post( $settings, $side, $post_type, $taxonomy  );
					$post_id = $target_post->ID;
				} else {
					$post_id = ( 'prev' === $side ) ? $prev_id : $next_id;
				}

				$nav_atts[ $side ]['post_id'] = $post_id;

				if ( 'color' === $settings['wrapper_background_style'] ) {
					$nav_atts[ $side ]['attach'] = $this->get_attachment_image( $settings, $post_id );
				}
			}

			$line_clamp_count = ( isset( $settings['line_clamp_count'] ) ? $settings['line_clamp_count'] : '' );

			if ( 'yes' === $settings['show_title'] ) {
				if ( '' !== $line_clamp_count ) {
					$title_attr = 'title="%title"';
				} else {
					$title_attr = '';
				}

				$is_animation = 'none' !== $settings['pointer'];
				$animation_class = ( $is_animation ) ? AnimationModule::get_animation_class() : '';
				$animation_class_attr = ( $is_animation ) ? $animation_class : '';

				$nav_atts[ $side ]['title'] = "<span class=\"elementor-widget-cmsmasters-post-navigation__{$side}-title-weapper\">
					<span {$title_attr} class=\"elementor-widget-cmsmasters-post-navigation__{$side}-title {$animation_class_attr}\">%title</span>
				</span>";
			}
		}

		echo '<div class="elementor-widget-cmsmasters-post-navigation__wrap">';

		$this->get_post_navigation( $settings, $nav_atts['prev'], $in_same_term, $taxonomy, $animation_class_attr, $post_type );

		if ( 'yes' === $settings['show_spacer'] ) {
			echo '<div class="elementor-widget-cmsmasters-post-navigation__separator-wrapper">' .
				'<div class="elementor-widget-cmsmasters-post-navigation__separator"></div>' .
			'</div>';
		}

		$this->get_post_navigation( $settings, $nav_atts['next'], $in_same_term, $taxonomy, $animation_class_attr, $post_type );

		echo '</div>';
	}


	/**
	 * Get the next or previous post for loop navigation.
	 *
	 * Returns the adjacent post (previous or next) based on the widget settings:
	 * - can restrict navigation to the current term (category/taxonomy);
	 * - supports loop navigation — after the last post, returns the first one.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $settings  Widget settings.
	 * @param string $side      Navigation direction: 'prev' or 'next'.
	 * @param string $post_type Post type used to build the navigation.
	 * @param string $taxonomy  Taxonomy used for term-based filtering.
	 *
	 * @return WP_Post|null The adjacent post, or null if no posts are available.
	 */
	protected function get_target_post( $settings, $side, $post_type, $taxonomy ) {
		$current_post_id = get_the_ID();

		$target_post = null;

		$args = array(
			'posts_per_page' => -1,
			'order'          => 'ASC',
			'orderby'        => 'date',
			'fields'         => 'ids',
			'post_type'      => get_post_type( $current_post_id ),
			'post_status'    => 'publish',
		);

		if (
			! empty( $settings['in_same_term'] ) &&
			is_array( $settings['in_same_term'] ) &&
			in_array( $post_type, $settings['in_same_term'], true )
		) {
			if ( isset( $settings[ $post_type . '_taxonomy' ] ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => wp_get_post_terms(
							$current_post_id,
							$taxonomy,
							array( 'fields' => 'ids' )
						),
					),
				);
			}
		}

		$posts = get_posts( $args );

		if ( ! empty( $posts ) ) {
			$current_index = array_search( $current_post_id, $posts, true );

			$prev_index = ( $current_index - 1 + count( $posts ) ) % count( $posts );
			$next_index = ( $current_index + 1 ) % count( $posts );

			$prev_post = get_post( $posts[ $prev_index ] );
			$next_post = get_post( $posts[ $next_index ] );

			$target_post = ( 'prev' === $side ) ? $prev_post : $next_post;

		}

		return $target_post;
	}

	/**
	 * Return html of prev\next post.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings
	 * @param array $nav_atts
	 * @param bool $in_same_term
	 * @param string $taxonomy
	 */
	private function get_post_navigation( $settings, $nav_atts, $in_same_term, $taxonomy, $animation_class_attr, $post_type ) {
		$main_class = 'elementor-widget-cmsmasters-post-navigation';

		if ( 'prev' === $nav_atts['side'] ) {
			$main_link = '%1$s%4$s<span class="' . $main_class . '__link-' . $nav_atts['side'] . '">%2$s%3$s</span>';
		} else {
			$main_link = '<span class="' . $main_class . '__link-' . $nav_atts['side'] . '">%2$s%3$s</span>%4$s%1$s';
		}

		$use_loop_navigation = ! empty( $settings['use_loop_navigation'] );

		if ( $use_loop_navigation ) {
			$target_post = $this->get_target_post( $settings, $nav_atts['side'], $post_type, $taxonomy );

			if ( $target_post ) {

				if ( 'yes' === $settings['show_title'] ) {
					if ( 'prev' === $nav_atts['side'] ) {
						$nav_atts['title'] = "<span class=\"elementor-widget-cmsmasters-post-navigation__prev-title-weapper\">
							<span class=\"elementor-widget-cmsmasters-post-navigation__prev-title {$animation_class_attr}\">{$target_post->post_title}</span>
						</span>";
					} else {
						$nav_atts['title'] = "<span class=\"elementor-widget-cmsmasters-post-navigation__next-title-weapper\">
							<span class=\"elementor-widget-cmsmasters-post-navigation__next-title {$animation_class_attr}\">{$target_post->post_title}</span>
						</span>";
					}
				}

				if ( 'after' === $settings['navigation_label_position'] ) {
					$link_attr = array(
						get_permalink( $target_post->ID ),
						sprintf( $main_link,
							$nav_atts['attach'],
							$nav_atts['title'],
							$nav_atts['label'],
							$nav_atts['attach_icon']
						),
					);
				} else {
					$link_attr = array(
						get_permalink( $target_post->ID ),
						sprintf( $main_link,
							$nav_atts['attach'],
							$nav_atts['label'],
							$nav_atts['title'],
							$nav_atts['attach_icon']
						),
					);
				}

				$this->add_render_attribute( 'nav-inner_' . $nav_atts['side'], 'class', array(
					"{$main_class}__{$nav_atts['side']}",
					"{$main_class}__link",
				) );

				$img_url = Group_Control_Image_Size::get_attachment_image_src( get_post_thumbnail_id( $nav_atts['post_id'] ), 'image', $settings );

				if ( ! $img_url ) {
					$img_url = ( isset( $settings['fallback_image'] ) ? $settings['fallback_image']['url'] : '' );
				}

				if ( 'icon' !== $settings['navigation_graphic_element'] && 'color' !== $settings['wrapper_background_style'] ) {
					$this->add_render_attribute( 'nav-inner_' . $nav_atts['side'], 'style', array(
						'background-image' => 'background-image: url(' . $img_url . ');',
					) );
				}

				echo '<div ' . $this->get_render_attribute_string( 'nav-inner_' . esc_attr( $nav_atts['side'] ) ) . '>' .
					sprintf( '<a href="%s">%s</a>', esc_url( $link_attr[0] ), $link_attr[1] ) .
				'</div>';
			}
	} else {
			if ( 'after' === $settings['navigation_label_position'] ) {
				$link_attr = array(
					'%link',
					sprintf( $main_link,
						$nav_atts['attach'],
						$nav_atts['title'],
						$nav_atts['label'],
						$nav_atts['attach_icon']
					),
					$in_same_term,
					'',
					$taxonomy,
				);
			} else {
				$link_attr = array(
					'%link',
					sprintf( $main_link,
						$nav_atts['attach'],
						$nav_atts['label'],
						$nav_atts['title'],
						$nav_atts['attach_icon']
					),
					$in_same_term,
					'',
					$taxonomy,
				);
			}

			$this->add_render_attribute( 'nav-inner_' . $nav_atts['side'], 'class', array(
				"{$main_class}__{$nav_atts['side']}",
				"{$main_class}__link",
			) );

			$img_url = Group_Control_Image_Size::get_attachment_image_src( get_post_thumbnail_id( $nav_atts['post_id'] ), 'image', $settings );

			if ( ! $img_url ) {
				$img_url = ( isset( $settings['fallback_image'] ) ? $settings['fallback_image']['url'] : '' );
			}

			if ( 'icon' !== $settings['navigation_graphic_element'] && 'color' !== $settings['wrapper_background_style'] ) {
				$this->add_render_attribute( 'nav-inner_' . $nav_atts['side'], 'style', array(
					'background-image' => 'background-image: url(' . $img_url . ');',
				) );
			}

			echo '<div ' . $this->get_render_attribute_string( 'nav-inner_' . esc_html( $nav_atts['side'] ) ) . '>';

			if ( 'prev' === $nav_atts['side'] ) {
				call_user_func_array( 'previous_post_link', $link_attr );
			} else {
				call_user_func_array( 'next_post_link', $link_attr );
			}

			echo '</div>';
		}
	}

	/**
	 * Get icon html for prev\next post.
	 *
	 * Return custom or predefined icon html for prev\next post.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings
	 * @param string $direction
	 * @param string $add_class
	 * @param string $add_attr
	 *
	 * @return array $icon_html
	 */
	private function get_icon_html( $settings, $direction = '', $add_class = '', $add_attr = '' ) {
		$icon_html = '';
		$screen_only_label = ( 'prev' === $direction ) ? esc_html__( 'Prev', 'cmsmasters-elementor' ) : esc_html__( 'Next', 'cmsmasters-elementor' );
		$icon = $this->get_icon_class( $settings );
		$is_icon_side = ( 'prev' === $direction ) ? $settings['icon_left']['value'] : $settings['icon_right']['value'];

		if ( ! empty( $is_icon_side ) ) {
			$icon_html = "<span class=\"elementor-widget-cmsmasters-post-navigation__icon-wrapper elementor-widget-cmsmasters-post-navigation__icon-{$direction}{$add_class}\" {$add_attr}>" .
				$icon[ $direction ] .
				"<span class=\"cmsmasters-screen-only\">{$screen_only_label}</span>" .
			'</span>';
		}

		return $icon_html;
	}

	/**
	 * Get icon for prev\next post.
	 *
	 * Return custom or predefined icon for prev\next post.
	 *
	 * @since 1.0.0
	 * @since 1.11.6 Fixed render icons in widget.
	 *
	 * @param array $settings
	 *
	 * @return array $icon
	 */
	private function get_icon_class( $settings ) {
		$direction = array( 'prev', 'next' );
		$sides = array( 'left', 'right' );
		$icon = array();

		if ( is_rtl() ) {
			$sides = array_reverse( $sides );
		}

		$icon = array_combine( $direction, $sides );

		foreach ( $icon as $direction => $side ) {
			$icon[ $direction ] = Utils::get_render_icon( $settings[ 'icon_' . $side ], $attributes = array( 'aria-hidden' => 'true' ) );
		}

		return $icon;
	}

	/**
	 * Get attachment image for prev\next post.
	 *
	 * Return image width predefined dimensions or custom.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings
	 * @param string $id
	 */
	private function get_attachment_image( $settings, $id = '' ) {
		$size = $settings['image_size'];

		$image_class = "attachment-{$size} size-{$size}";

		if ( ! empty( $size ) && in_array( $size, get_intermediate_image_sizes(), true ) ) {
			$image_attr = array( 'class' => trim( $image_class ) );

			if ( 0 !== get_post_thumbnail_id( $id ) ) {
				$post_thumb_id = get_post_thumbnail_id( $id );
			} else {
				$post_thumb_id = $settings['fallback_image']['id'];
			}

			return wp_get_attachment_image( $post_thumb_id, $size, false, $image_attr );
		} else {
			$image_src = Group_Control_Image_Size::get_attachment_image_src( get_post_thumbnail_id( $id ), 'image', $settings );

			if ( ! $image_src ) {
				$image_src = wp_get_attachment_image_src( $id );
			}

			$attachment = get_post( $id );

			$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );

			if ( isset( $alt ) && ! $alt ) {
				$alt = $attachment->post_excerpt;

				if ( isset( $alt ) && ! $alt ) {
					$alt = $attachment->post_title;
				}
			}

			$alt_text = trim( wp_strip_all_tags( $alt ) );

			if ( ! empty( $image_src ) ) {
				$image_class_html = ! empty( $image_class ) ? ' class="' . $image_class . '"' : '';

				return sprintf( '<img src="%1$s" title="%2$s" alt="%3$s"%4$s />',
					esc_attr( $image_src ),
					get_the_title( $id ),
					$alt_text,
					$image_class_html
				);
			}
		}
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
				'field' => 'prev_label',
				'type' => esc_html__( 'Previous Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'next_label',
				'type' => esc_html__( 'Next Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
