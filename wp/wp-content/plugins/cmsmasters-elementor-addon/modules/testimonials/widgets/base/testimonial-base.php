<?php
namespace CmsmastersElementor\Modules\Testimonials\Widgets\Base;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Testimonials widgets base class.
 *
 * @since 1.1.0
 */
class Testimonial_Base extends Base_Widget {

	/**
	 * Widget type.
	 *
	 * @since 1.1.0
	 *
	 * @var string Widget type.
	 */
	protected $type = 'single';

	/**
	 * Item selector.
	 *
	 * @since 1.1.0
	 *
	 * @var string item selector.
	 */
	protected $item_selector = 'cmsmasters-testimonial';

	/**
	 * Widget settings for display.
	 *
	 * @since 1.1.0
	 *
	 * @var string Widget settings for display.
	 */
	protected $settings;

	/**
	 * Item settings.
	 *
	 * @since 1.1.0
	 *
	 * @var array Widget type.
	 */
	protected $item_settings;

	/**
	 * Horizontal text parts.
	 *
	 * @since 1.1.0
	 */
	protected $h_start;
	protected $h_end;

	/**
	 * Conditions.
	 *
	 * @since 1.1.0
	 */
	protected $title_condition = array();
	protected $title_conditions = array();
	protected $author_subtitle_condition = array();
	protected $author_subtitle_conditions = array();
	protected $avatar_condition = array();
	protected $avatar_conditions = array();
	protected $rating_condition = array();
	protected $rating_conditions = array();

	/**
	 * Get group name.
	 *
	 * @since 1.6.5
	 *
	 * @return string Group name.
	 */
	public function get_group_name() {
		return 'cmsmasters-testimonials';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.1.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'testimonial',
			'quote',
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
			'widget-cmsmasters-testimonials',
		);
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.1.0
	 */
	protected function register_controls() {
		$this->set_controls_properties();

		$this->content_testimonial();

		$this->content_layout();

		$this->content_rating();

		if ( 'slider' === $this->type ) {
			$this->slider->register_section_content();
		}

		$this->style_content();

		$this->style_author();

		$this->style_avatar();

		$this->style_rating();

		$this->style_icon();

		if ( 'slider' === $this->type ) {
			$this->slider->register_sections_style();

			$this->update_control_slider();
		}
	}

	/**
	 * Set controls properties.
	 *
	 * @since 1.1.0
	 */
	protected function set_controls_properties() {
		$this->h_start = is_rtl() ? 'right' : 'left';
		$this->h_end = ! is_rtl() ? 'right' : 'left';

		$default_condition = array(
			'widget_type' => $this->type,
		);

		$default_conditions = array(
			'name' => 'widget_type',
			'operator' => '===',
			'value' => $this->type,
		);

		$this->title_condition = $default_condition;
		$this->title_conditions = $default_conditions;
		$this->author_subtitle_condition = $default_condition;
		$this->author_subtitle_conditions = $default_conditions;
		$this->avatar_condition = $default_condition;
		$this->avatar_conditions = $default_conditions;
		$this->rating_condition = $default_condition;
		$this->rating_conditions = $default_conditions;

		if ( 'single' === $this->type ) {
			$this->title_condition = array(
				'title!' => '',
			);

			$this->author_subtitle_condition = array(
				'author_subtitle!' => '',
			);

			$this->author_subtitle_conditions = array(
				'name' => 'author_subtitle',
				'operator' => '!==',
				'value' => '',
			);

			$this->avatar_condition = array(
				'avatar[url]!' => '',
			);

			$this->avatar_conditions = array(
				'name' => 'avatar[url]',
				'operator' => '!==',
				'value' => '',
			);

			$this->rating_condition = array(
				'rating!' => '',
			);

			$this->rating_conditions = array(
				'name' => 'rating',
				'operator' => '!==',
				'value' => '',
			);
		}
	}

	/**
	 * Content testimonial controls.
	 *
	 * @since 1.1.0
	 */
	protected function content_testimonial() {
		$this->start_controls_section(
			'section_content_testimonial',
			array(
				'label' => __( 'Testimonial', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		if ( 'slider' === $this->type ) {
			$handler = new Repeater();
		} else {
			$handler = $this;
		}

		$this->add_control(
			'widget_type',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => $this->type,
			)
		);

		$handler->add_control(
			'title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$handler->add_control(
			'text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => '8',
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'Click edit button to change handler text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'cmsmasters-elementor' ),
			)
		);

		$handler->add_control(
			'author_info_heading_control',
			array(
				'label' => __( 'Author Info', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$handler->add_control(
			'author_name',
			array(
				'label' => __( 'Name', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'John Doe', 'cmsmasters-elementor' ),
			)
		);

		$handler->add_control(
			'author_subtitle',
			array(
				'label' => __( 'Subtitle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'Developer', 'cmsmasters-elementor' ),
			)
		);

		$handler->add_control(
			'author_link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'separator' => 'after',
			)
		);

		$handler->add_control(
			'avatar',
			array(
				'label' => __( 'Avatar', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$handler->add_control(
			'rating',
			array(
				'label' => __( 'Rating', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 10,
				'step' => 0.1,
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		if ( 'slider' === $this->type ) {
			$this->add_control(
				'items',
				array(
					'label' => __( 'Items', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::REPEATER,
					'fields' => $handler->get_controls(),
					'title_field' => '{{{ author_name }}}',
					'default' => array(
						array(
							'text' => __( 'This is item #01. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'cmsmasters-elementor' ),
							'author_name' => __( 'John Doe', 'cmsmasters-elementor' ),
							'author_subtitle' => __( 'Developer', 'cmsmasters-elementor' ),
						),
						array(
							'text' => __( 'This is item #02. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'cmsmasters-elementor' ),
							'author_name' => __( 'Jane Doe', 'cmsmasters-elementor' ),
							'author_subtitle' => __( 'Designer', 'cmsmasters-elementor' ),
						),
					),
				)
			);

			$this->slider->register_controls_content_per_view();
		}

		$this->end_controls_section();
	}

	/**
	 * Content layout controls.
	 *
	 * @since 1.1.0
	 * @since 1.2.1 Change controls for responsive.
	 */
	protected function content_layout() {
		$this->start_controls_section(
			'section_content_layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_responsive_control(
			'layout_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_start}",
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'flex-end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_end}",
					),
				),
				'default' => 'flex-start',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'layout_alignment', '{{VALUE}}' ),
				),
			)
		);

		$this->add_control(
			'layout_start_text_alignment',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 'start',
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'layout_text_alignment', 'start' ),
				),
				'condition' => array(
					'layout_alignment' => 'flex-start',
				),
			)
		);

		$this->add_control(
			'layout_center_text_alignment',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'layout_text_alignment', 'center' ),
				),
				'condition' => array(
					'layout_alignment' => 'center',
				),
			)
		);

		$this->add_control(
			'layout_end_text_alignment',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 'end',
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'layout_text_alignment', 'end' ),
				),
				'condition' => array(
					'layout_alignment' => 'flex-end',
				),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'top' => array(
						'title' => __( 'Vertical Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'bottom' => array(
						'title' => __( 'Vertical Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
					'left' => array(
						'title' => __( 'Horizontal Start', 'cmsmasters-elementor' ),
						'icon' => "eicon-h-align-{$this->h_start}",
					),
					'right' => array(
						'title' => __( 'Horizontal End', 'cmsmasters-elementor' ),
						'icon' => "eicon-h-align-{$this->h_end}",
					),
				),
				'default' => 'bottom',
				'prefix_class' => "{$this->item_selector}-layout-",
				'render_type' => 'template',
				'toggle' => false,
			)
		);

		$this->add_control(
			'avatar_placement',
			array(
				'label' => __( 'Avatar Placement', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'with_author' => array(
						'title' => __( 'With author', 'cmsmasters-elementor' ),
						'description' => __( 'Place avatar with author info block.', 'cmsmasters-elementor' ),
					),
					'separate' => array(
						'title' => __( 'Separate', 'cmsmasters-elementor' ),
						'description' => __( 'Place avatar separate to author info block.', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'with_author',
				'toggle' => false,
				'condition' => array_merge(
					array(
						'layout' => array( 'top', 'bottom' ),
					),
					$this->avatar_condition
				),
			)
		);

		$this->add_control(
			'avatar_separate_position',
			array(
				'label' => __( 'Avatar Position', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'opposite' => array(
						'title' => __( 'Opposite', 'cmsmasters-elementor' ),
						'description' => __( 'Opposite to author info.', 'cmsmasters-elementor' ),
					),
					'left' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'description' => __( 'Start of content.', 'cmsmasters-elementor' ),
					),
					'right' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'description' => __( 'End of content.', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'opposite',
				'toggle' => false,
				'condition' => array_merge(
					array(
						'layout' => array( 'top', 'bottom' ),
						'avatar_placement' => 'separate',
					),
					$this->avatar_condition
				),
			)
		);

		$this->add_control(
			'avatar_author_position',
			array(
				'label' => __( 'Avatar Position', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
					'left' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => "eicon-h-align-{$this->h_start}",
					),
					'right' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => "eicon-h-align-{$this->h_end}",
					),
				),
				'default' => 'top',
				'toggle' => false,
				'prefix_class' => "{$this->item_selector}-author-avatar-",
				'render_type' => 'template',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						$this->avatar_conditions,
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '===',
									'value' => 'left',
								),
								array(
									'name' => 'layout',
									'operator' => '===',
									'value' => 'right',
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'relation' => 'or',
											'terms' => array(
												array(
													'name' => 'layout',
													'operator' => '===',
													'value' => 'top',
												),
												array(
													'name' => 'layout',
													'operator' => '===',
													'value' => 'bottom',
												),
											),
										),
										array(
											'name' => 'avatar_placement',
											'operator' => '===',
											'value' => 'with_author',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->content_layout_side_area();

		$this->end_controls_section();
	}

	protected function content_layout_side_area() {
		$conditions = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'layout',
					'operator' => '===',
					'value' => 'left',
				),
				array(
					'name' => 'layout',
					'operator' => '===',
					'value' => 'right',
				),
				array(
					'relation' => 'and',
					'terms' => array(
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'layout',
									'operator' => '===',
									'value' => 'top',
								),
								array(
									'name' => 'layout',
									'operator' => '===',
									'value' => 'bottom',
								),
							),
						),
						array(
							'name' => 'avatar_placement',
							'operator' => '===',
							'value' => 'separate',
						),
						array(
							'name' => 'avatar_separate_position',
							'operator' => '!==',
							'value' => 'opposite',
						),
					),
				),
			),
		);

		$this->add_control(
			'side_area_heading_control',
			array(
				'label' => __( 'Side Area', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $conditions,
			)
		);

		$this->add_responsive_control(
			'side_area_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'%',
				),
				'range' => array(
					'%' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'side_area_width', '{{SIZE}}%' ),
				),
				'conditions' => $conditions,
			)
		);

		$this->add_responsive_control(
			'side_area_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'default' => array(
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'side_area_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'conditions' => $conditions,
			)
		);

		$this->add_responsive_control(
			'side_area_vertical_alignment',
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
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'side_area_vertical_alignment', '{{VALUE}}' ),
				),
				'conditions' => $conditions,
			)
		);
	}

	/**
	 * Content rating controls.
	 *
	 * @since 1.1.0
	 */
	protected function content_rating() {
		$this->start_controls_section(
			'section_content_rating',
			array(
				'label' => __( 'Rating', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => $this->rating_condition,
			)
		);

		$this->add_control(
			'rating_scale',
			array(
				'label' => __( 'Scale', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'5' => array(
						'title' => '5',
					),
					'10' => array(
						'title' => '10',
					),
				),
				'default' => '5',
			)
		);

		$this->add_control(
			'rating_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
					),
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'icon',
				'toggle' => false,
			)
		);

		$this->start_controls_tabs(
			'rating_icons_tabs',
			array(
				'condition' => array(
					'rating_type' => 'icon',
				),
			)
		);

		$this->start_controls_tab(
			'rating_icons_tab_empty',
			array( 'label' => __( 'Empty', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'rating_icon_empty',
			array(
				'type' => Controls_Manager::ICONS,
				'description' => __( 'For correct displaying variations of the same icon should be used (eg.: star icons both for empty and filled options).<br>You can also choose either Empty or Filled icon to be used for both states.', 'cmsmasters-elementor' ),
				'default' => array(
					'value' => 'far fa-star',
					'library' => 'fa-regular',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'rating_icons_tab_filled',
			array( 'label' => __( 'Filled', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'rating_icon_filled',
			array(
				'type' => Controls_Manager::ICONS,
				'description' => __( 'For correct displaying variations of the same icon should be used (eg.: star icons both for empty and filled options).<br>You can also choose either Empty or Filled icon to be used for both states.', 'cmsmasters-elementor' ),
				'default' => array(
					'value' => 'fas fa-star',
					'library' => 'fa-regular',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'rating_text_delimiter',
			array(
				'label' => __( 'Delimiter', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => '/',
				'condition' => array(
					'rating_type' => 'text',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style content controls.
	 *
	 * @since 1.1.0
	 */
	protected function style_content() {
		$this->start_controls_section(
			'section_style_content',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_start}",
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_end}",
					),
				),
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'content_alignment', '{{VALUE}}' ),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BACKGROUND_GROUP,
			array(
				'name' => 'content_bg',
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'content_bd',
			)
		);

		$this->add_control(
			'content_bd_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'content_bd_radius', '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array(
				'name' => 'content',
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'content_padding_top', '{{TOP}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'content_padding_right', '{{RIGHT}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'content_padding_bottom', '{{BOTTOM}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'content_padding_left', '{{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->style_content_triangle();

		$this->style_text();

		$this->style_title();

		$this->end_controls_section();
	}

	/**
	 * Style text controls.
	 *
	 * @since 1.1.0
	 */
	protected function style_text() {
		$this->add_control(
			'style_text_heading_control',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'text',
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'text_color', '{{VALUE}}' ),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'text',
			)
		);
	}

	/**
	 * Style title controls.
	 *
	 * @since 1.1.0
	 */
	protected function style_title() {
		$this->add_control(
			'style_title_heading_control',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $this->title_condition,
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label' => __( 'Title HTML Tag', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				),
				'default' => 'h4',
				'condition' => $this->title_condition,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title',
				'selector' => '{{WRAPPER}} .cmsmasters-testimonial__title',
				'condition' => $this->title_condition,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'title_color', '{{VALUE}}' ),
				),
				'condition' => $this->title_condition,
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'title',
				'condition' => $this->title_condition,
			)
		);

		$this->add_responsive_control(
			'title_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'title_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => $this->title_condition,
			)
		);
	}

	/**
	 * Style content triangle controls.
	 *
	 * @since 1.1.0
	 */
	protected function style_content_triangle() {
		$this->add_control(
			'content_triangle_toggle',
			array(
				'label' => __( 'Triangle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
			)
		);

		$this->start_popover();

		$this->add_control(
			'content_triangle_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'isosceles' => array(
						'title' => __( 'Isosceles', 'cmsmasters-elementor' ),
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'isosceles',
				'toggle' => false,
				'render_type' => 'ui',
				'condition' => array(
					'content_triangle_toggle' => 'yes',
				),
			)
		);

		$this->add_control(
			'content_triangle_isosceles_direction',
			array(
				'label' => __( 'Direction', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-caret-up',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-caret-down',
					),
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => "eicon-caret-left",
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => "eicon-caret-right",
					),
				),
				'default' => 'bottom',
				'prefix_class' => "{$this->item_selector}-triangle-isosceles-",
				'toggle' => false,
				'condition' => array(
					'content_triangle_toggle' => 'yes',
					'content_triangle_shape' => 'isosceles',
				),
			)
		);

		$this->add_control(
			'content_triangle_right_direction',
			array(
				'label' => __( 'Direction', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'top-left' => __( 'Top Left', 'cmsmasters-elementor' ),
					'top-right' => __( 'Top Right', 'cmsmasters-elementor' ),
					'bottom-left' => __( 'Bottom Left', 'cmsmasters-elementor' ),
					'bottom-right' => __( 'Bottom Right', 'cmsmasters-elementor' ),
					'left-top' => __( 'Left Top', 'cmsmasters-elementor' ),
					'left-bottom' => __( 'Left Bottom', 'cmsmasters-elementor' ),
					'right-top' => __( 'Right Top', 'cmsmasters-elementor' ),
					'right-bottom' => __( 'Right Bottom', 'cmsmasters-elementor' ),
				),
				'default' => 'bottom-left',
				'prefix_class' => "{$this->item_selector}-triangle-right-",
				'toggle' => false,
				'condition' => array(
					'content_triangle_toggle' => 'yes',
					'content_triangle_shape' => 'right',
				),
			)
		);

		$this->add_responsive_control(
			'content_triangle_offset',
			array(
				'label' => __( 'Offset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'default' => array(
					'unit' => 'px',
				),
				'frontend_available' => true,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'content_triangle_offset', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'content_triangle_toggle' => 'yes',
				),
			)
		);

		$this->add_control(
			'content_triangle_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'content_triangle_color', '{{VALUE}}' ),
				),
				'condition' => array(
					'content_triangle_toggle' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'content_triangle_base_size',
			array(
				'label' => __( 'Base Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'content_triangle_base_size', '{{SIZE}}px' ),
				),
				'condition' => array(
					'content_triangle_toggle' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'content_triangle_length_size',
			array(
				'label' => __( 'Length Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'content_triangle_length_size', '{{SIZE}}px' ),
				),
				'condition' => array(
					'content_triangle_toggle' => 'yes',
				),
			)
		);

		$this->add_control(
			'content_triangle_back',
			array(
				'label' => __( 'Back Triangle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'render_type' => 'ui',
			)
		);

		$this->add_control(
			'content_triangle_back_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'content_triangle_back_color', '{{VALUE}}' ),
				),
				'condition' => array(
					'content_triangle_toggle' => 'yes',
					'content_triangle_back' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'content_triangle_back_scale_size',
			array(
				'label' => __( 'Size to Scale', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'content_triangle_back_scale_size', '{{SIZE}}px' ),
				),
				'condition' => array(
					'content_triangle_toggle' => 'yes',
					'content_triangle_back' => 'yes',
				),
			)
		);

		$this->end_popover();
	}

	/**
	 * Style author controls.
	 *
	 * @since 1.1.0
	 */
	protected function style_author() {
		$this->start_controls_section(
			'section_style_author',
			array(
				'label' => __( 'Author Info', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'author_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inline' => array(
						'title' => __( 'Inline', 'cmsmasters-elementor' ),
					),
					'full' => array(
						'title' => __( 'Full', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'inline',
				'toggle' => false,
				'prefix_class' => "{$this->item_selector}-author-width-",
			)
		);

		$this->add_responsive_control(
			'author_horizontal_alignment',
			array(
				'label' => __( 'Horizontal Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => "eicon-h-align-{$this->h_start}",
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => "eicon-h-align-center",
					),
					'flex-end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => "eicon-h-align-{$this->h_end}",
					),
				),
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'author_horizontal_alignment', '{{VALUE}}' ),
				),
			)
		);

		$this->add_control(
			'author_text_view',
			array(
				'label' => __( 'Text View', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'vertical' => array(
						'title' => __( 'Vertical', 'cmsmasters-elementor' ),
						'icon' => 'eicon-ellipsis-v',
					),
					'horizontal' => array(
						'title' => __( 'Horizontal', 'cmsmasters-elementor' ),
						'icon' => 'eicon-ellipsis-h',
					),
				),
				'default' => 'vertical',
				'toggle' => false,
				'prefix_class' => "{$this->item_selector}-author-text-view-",
				'render_type' => 'template',
				'condition' => $this->author_subtitle_condition,
			)
		);

		$this->add_responsive_control(
			'author_text_alignment',
			array(
				'label' => __( 'Text Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_start}",
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_end}",
					),
				),
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'author_text_alignment', '{{VALUE}}' ),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								$this->author_subtitle_conditions,
								array(
									'name' => 'author_text_view',
									'operator' => '===',
									'value' => 'vertical',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								$this->rating_conditions,
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'rating_position',
											'operator' => '===',
											'value' => 'top_author',
										),
										array(
											'name' => 'rating_position',
											'operator' => '===',
											'value' => 'bottom_author',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'author_delimiter',
			array(
				'label' => __( 'Text Delimiter', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => '',
				'condition' => array_merge(
					array(
						'author_text_view' => 'horizontal',
					),
					$this->author_subtitle_condition
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BACKGROUND_GROUP,
			array(
				'name' => 'author_bg',
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'author_bd',
			)
		);

		$this->add_control(
			'author_bd_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'author_bd_radius', '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array(
				'name' => 'author',
			)
		);

		$this->add_responsive_control(
			'author_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'author_padding_top', '{{TOP}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'author_padding_right', '{{RIGHT}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'author_padding_bottom', '{{BOTTOM}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'author_padding_left', '{{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'author_gap',
			array(
				'label' => __( 'Gap to Content', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'author_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'layout' => array( 'top', 'bottom' ),
				),
			)
		);

		$this->add_control(
			'author_name_heading_control',
			array(
				'label' => __( 'Name', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'author_name',
			)
		);

		$this->start_controls_tabs( 'author_name_states_tabs' );

		$author_name_states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $author_name_states as $state_key => $state_label ) {
			$this->start_controls_tab(
				"author_name_states_{$state_key}_tab",
				array( 'label' => $state_label )
			);

			$this->add_control(
				"author_name_{$state_key}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "author_name_{$state_key}_color", '{{VALUE}}' ),
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
				array(
					'name' => "author_name_{$state_key}",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'author_subtitle_heading_control',
			array(
				'label' => __( 'Subtitle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $this->author_subtitle_condition,
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'author_subtitle',
				'condition' => $this->author_subtitle_condition,
			)
		);

		$this->start_controls_tabs(
			'author_subtitle_states_tabs',
			array(
				'condition' => $this->author_subtitle_condition,
			)
		);

		$author_subtitle_states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $author_subtitle_states as $state_key => $state_label ) {
			$this->start_controls_tab(
				"author_subtitle_states_{$state_key}_tab",
				array( 'label' => $state_label )
			);

			$this->add_control(
				"author_subtitle_{$state_key}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "author_subtitle_{$state_key}_color", '{{VALUE}}' ),
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
				array(
					'name' => "author_subtitle_{$state_key}",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'author_subtitle_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'author_subtitle_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => $this->author_subtitle_condition,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style avatar controls.
	 *
	 * @since 1.1.0
	 */
	protected function style_avatar() {
		$this->start_controls_section(
			'section_style_avatar',
			array(
				'label' => __( 'Avatar', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $this->avatar_condition,
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'avatar',
				'default' => 'thumbnail',
			)
		);

		$this->add_responsive_control(
			'avatar_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'%' => array(
						'min' => 1,
						'max' => 100,
					),
					'px' => array(
						'min' => 1,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'avatar_width', '{{SIZE}}{{UNIT}}' ),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '===',
							'value' => 'left',
						),
						array(
							'name' => 'layout',
							'operator' => '===',
							'value' => 'right',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'layout',
											'operator' => '===',
											'value' => 'top',
										),
										array(
											'name' => 'layout',
											'operator' => '===',
											'value' => 'bottom',
										),
									),
								),
								array(
									'name' => 'avatar_placement',
									'operator' => '===',
									'value' => 'with_author',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'layout',
											'operator' => '===',
											'value' => 'top',
										),
										array(
											'name' => 'layout',
											'operator' => '===',
											'value' => 'bottom',
										),
									),
								),
								array(
									'name' => 'avatar_placement',
									'operator' => '===',
									'value' => 'separate',
								),
								array(
									'name' => 'avatar_separate_position',
									'operator' => '===',
									'value' => 'opposite',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'avatar_horizontal_alignment',
			array(
				'label' => __( 'Horizontal Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => "eicon-h-align-{$this->h_start}",
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => "eicon-h-align-center",
					),
					'flex-end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => "eicon-h-align-{$this->h_end}",
					),
				),
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'avatar_horizontal_alignment', '{{VALUE}}' ),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'layout',
											'operator' => '===',
											'value' => 'top',
										),
										array(
											'name' => 'layout',
											'operator' => '===',
											'value' => 'bottom',
										),
									),
								),
								array(
									'name' => 'avatar_placement',
									'operator' => '===',
									'value' => 'separate',
								),
								array(
									'name' => 'avatar_separate_position',
									'operator' => '===',
									'value' => 'opposite',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'avatar_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'avatar_bg_color', '{{VALUE}}' ),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'avatar_bd',
			)
		);

		$this->add_control(
			'avatar_bd_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'avatar_bd_radius', '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array(
				'name' => 'avatar',
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_CSS_FILTER_GROUP,
			array(
				'name' => 'avatar',
			)
		);

		$this->add_responsive_control(
			'avatar_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'avatar_padding_top', '{{TOP}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'avatar_padding_right', '{{RIGHT}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'avatar_padding_bottom', '{{BOTTOM}}{{UNIT}}' ) .
					CmsmastersUtils::prepare_css_var( 'avatar_padding_left', '{{LEFT}}{{UNIT}}' ),
				),
			)
		);

		$this->add_responsive_control(
			'avatar_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'avatar_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'layout',
							'operator' => '===',
							'value' => 'left',
						),
						array(
							'name' => 'layout',
							'operator' => '===',
							'value' => 'right',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'layout',
											'operator' => '===',
											'value' => 'top',
										),
										array(
											'name' => 'layout',
											'operator' => '===',
											'value' => 'bottom',
										),
									),
								),
								array(
									'name' => 'avatar_placement',
									'operator' => '===',
									'value' => 'with_author',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'layout',
											'operator' => '===',
											'value' => 'top',
										),
										array(
											'name' => 'layout',
											'operator' => '===',
											'value' => 'bottom',
										),
									),
								),
								array(
									'name' => 'avatar_placement',
									'operator' => '===',
									'value' => 'separate',
								),
								array(
									'name' => 'avatar_separate_position',
									'operator' => '===',
									'value' => 'opposite',
								),
							),
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style rating controls.
	 *
	 * @since 1.1.0
	 */
	protected function style_rating() {
		$this->start_controls_section(
			'section_style_rating',
			array(
				'label' => __( 'Rating', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $this->rating_condition,
			)
		);

		$this->style_rating_icon( array(
			'rating_type' => 'icon',
		) );

		$this->style_rating_text( array(
			'rating_type' => 'text',
		) );

		$this->add_control(
			'rating_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'in_content' => __( 'In Content', 'cmsmasters-elementor' ),
					'top_author' => __( 'Top of Author Info', 'cmsmasters-elementor' ),
					'bottom_author' => __( 'Bottom of Author Info', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'in_content',
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => "{$this->item_selector}-rating-position-",
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'rating_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'rating_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'rating_position!' => 'custom',
				),
			)
		);

		$this->add_control(
			'rating_offset_orientation_h',
			array(
				'label' => __( 'Horizontal Orientation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => is_rtl() ? __( 'Right', 'cmsmasters-elementor' ) : __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'end' => array(
						'title' => ! is_rtl() ? __( 'Right', 'cmsmasters-elementor' ) : __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'end',
				'toggle' => false,
				'render_type' => 'ui',
				'frontend_available' => true,
				'prefix_class' => "{$this->item_selector}-rating-offset-orientation-h-",
				'condition' => array(
					'rating_position' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'rating_offset_x',
			array(
				'label' => __( 'Offset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					),
					'%' => array(
						'min' => -200,
						'max' => 200,
					),
				),
				'default' => array(
					'unit' => 'px',
				),
				'frontend_available' => true,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'rating_offset_x', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'rating_position' => 'custom',
				),
			)
		);

		$this->add_control(
			'rating_offset_orientation_v',
			array(
				'label' => __( 'Vertical Orientation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'end' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'toggle' => false,
				'default' => 'end',
				'render_type' => 'ui',
				'frontend_available' => true,
				'prefix_class' => "{$this->item_selector}-rating-offset-orientation-v-",
				'condition' => array(
					'rating_position' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'rating_offset_y',
			array(
				'label' => __( 'Offset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					),
					'%' => array(
						'min' => -200,
						'max' => 200,
					),
				),
				'default' => array(
					'unit' => 'px',
				),
				'frontend_available' => true,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'rating_offset_y', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'rating_position' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'rating_z_index',
			array(
				'label' => __( 'Z-Index', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'rating_z_index', '{{VALUE}}' ),
				),
				'condition' => array(
					'rating_position' => 'custom',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style rating icon controls.
	 *
	 * @since 1.1.0
	 *
	 * @param array $condition Controls condition.
	 */
	protected function style_rating_icon( $condition ) {
		$this->add_responsive_control(
			'rating_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'rating_icon_size', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'rating_icon_gap',
			array(
				'label' => __( 'Icon Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'rating_icon_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => $condition,
			)
		);

		$this->start_controls_tabs(
			'rating_icon_types_tabs',
			array(
				'condition' => $condition,
			)
		);

		$this->start_controls_tab(
			'rating_icon_empty_tab',
			array(
				'label' => __( 'Empty', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'rating_icon_color_empty',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'rating_icon_color_empty', '{{VALUE}}' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'rating_icon_filled_tab',
			array(
				'label' => __( 'Filled', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'rating_icon_color_filled',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'rating_icon_color_filled', '{{VALUE}}' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'rating_icon',
				'label' => _x( 'Shadow', 'Text Shadow Control', 'cmsmasters-elementor' ),
				'condition' => $condition,
			)
		);
	}

	/**
	 * Style rating text controls.
	 *
	 * @since 1.1.0
	 *
	 * @param array $condition Controls condition.
	 */
	protected function style_rating_text( $condition ) {
		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'rating_text',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'rating_text_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'rating_text_color', '{{VALUE}}' ),
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'rating_text_current_color',
			array(
				'label' => __( 'Current Rating Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'rating_text_current_color', '{{VALUE}}' ),
				),
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'rating_text',
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'rating_text_gap',
			array(
				'label' => __( 'Gap Between Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'rating_text_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => $condition,
			)
		);
	}

	/**
	 * Style icon controls.
	 *
	 * @since 1.1.0
	 */
	protected function style_icon() {
		$this->start_controls_section(
			'section_style_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'icon_size', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'icon[value]!' => '',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'icon_color', '{{VALUE}}' ),
				),
				'condition' => array(
					'icon[value]!' => '',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'icon',
				'condition' => array(
					'icon[value]!' => '',
				),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'start' => __( 'Start of Content', 'cmsmasters-elementor' ),
					'end' => __( 'End of Content', 'cmsmasters-elementor' ),
					'top' => __( 'Top of Content', 'cmsmasters-elementor' ),
					'bottom' => __( 'Bottom of Content', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'start',
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => "{$this->item_selector}-icon-position-",
				'separator' => 'before',
				'condition' => array(
					'icon[value]!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'icon_horizontal_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_start}",
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_end}",
					),
				),
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'icon_horizontal_alignment', '{{VALUE}}' ),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'icon[value]',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'icon_position',
									'operator' => '===',
									'value' => 'top',
								),
								array(
									'name' => 'icon_position',
									'operator' => '===',
									'value' => 'bottom',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'icon_vertical_alignment',
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
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'icon_vertical_alignment', '{{VALUE}}' ),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'icon[value]',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'icon_position',
									'operator' => '===',
									'value' => 'start',
								),
								array(
									'name' => 'icon_position',
									'operator' => '===',
									'value' => 'end',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'icon_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'icon_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'icon[value]!' => '',
					'icon_position!' => 'custom',
				),
			)
		);

		$this->add_control(
			'icon_offset_orientation_h',
			array(
				'label' => __( 'Horizontal Orientation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'start',
				'options' => array(
					'start' => array(
						'title' => is_rtl() ? __( 'Right', 'cmsmasters-elementor' ) : __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'end' => array(
						'title' => ! is_rtl() ? __( 'Right', 'cmsmasters-elementor' ) : __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'render_type' => 'ui',
				'frontend_available' => true,
				'prefix_class' => "{$this->item_selector}-icon-offset-orientation-h-",
				'condition' => array(
					'icon[value]!' => '',
					'icon_position' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'icon_offset_x',
			array(
				'label' => __( 'Offset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					),
					'%' => array(
						'min' => -200,
						'max' => 200,
					),
				),
				'default' => array(
					'unit' => 'px',
				),
				'frontend_available' => true,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'icon_offset_x', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'icon[value]!' => '',
					'icon_position' => 'custom',
				),
			)
		);

		$this->add_control(
			'icon_offset_orientation_v',
			array(
				'label' => __( 'Vertical Orientation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'start',
				'options' => array(
					'start' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'end' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'render_type' => 'ui',
				'frontend_available' => true,
				'prefix_class' => "{$this->item_selector}-icon-offset-orientation-v-",
				'condition' => array(
					'icon[value]!' => '',
					'icon_position' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'icon_offset_y',
			array(
				'label' => __( 'Offset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					),
					'%' => array(
						'min' => -200,
						'max' => 200,
					),
				),
				'default' => array(
					'unit' => 'px',
				),
				'frontend_available' => true,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'icon_offset_y', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'icon[value]!' => '',
					'icon_position' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'icon_z_index',
			array(
				'label' => __( 'Z-Index', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'selectors' => array(
					'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( 'icon_z_index', '{{VALUE}}' ),
				),
				'condition' => array(
					'icon[value]!' => '',
					'icon_position' => 'custom',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Update widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	public function update_control_slider() {
		$this->update_control( 'slider_type', array(
			'type' => Controls_Manager::HIDDEN,
			'default' => 'carousel',
		) );

		$this->update_control( 'slider_effect', array(
			'options' => array(
				'slide' => __( 'Slide', 'cmsmasters-elementor' ),
				'fade' => __( 'Fade', 'cmsmasters-elementor' ),
				'flip' => __( 'Flip', 'cmsmasters-elementor' ),
			),
		) );

		$this->update_control( 'slider_height_type', array(
			'type' => Controls_Manager::HIDDEN,
			'default' => 'auto',
		) );

		$this->update_responsive_control( 'slider_per_view', array(
			'default' => '1',
		) );

		$this->update_control( 'slider_to_scroll', array(
			'default' => '1',
		) );

		$this->update_control( 'slider_autoplay', array(
			'default' => '',
		) );

		$this->update_control( 'slider_infinite', array(
			'default' => '',
		) );
	}

	/**
	 * Render item.
	 *
	 * @since 1.1.0
	 * @since 1.2.4 Add microformats.
	 */
	protected function render_item() {
		$author_link = $this->item_settings['author_link'];

		if ( ! empty( $author_link['url'] ) ) {
			$this->add_link_attributes( 'author_link' . $this->item_settings['index'], $author_link, true );
		}

		$layout = $this->settings['layout'];
		$avatar_position = 'with_author';

		if ( 'top' === $layout || 'bottom' === $layout ) {
			if ( 'separate' === $this->settings['avatar_placement'] ) {
				$avatar_position = $this->settings['avatar_separate_position'];

				if ( 'opposite' === $avatar_position ) {
					if ( 'top' === $layout ) {
						$avatar_position = 'bottom';
					} elseif ( 'bottom' === $layout ) {
						$avatar_position = 'top';
					}
				}
			}
		}

		$left_area_out = 'left' === $layout ? $this->render_item_author_info() : '';
		$left_area_out .= 'left' === $avatar_position ? $this->render_item_avatar() : '';
		$left_area_out = '' !== $left_area_out ? '<div class="' . $this->item_selector . '__side-area">' . $left_area_out . '</div>' : '';

		$right_area_out = 'right' === $layout ? $this->render_item_author_info() : '';
		$right_area_out .= 'right' === $avatar_position ? $this->render_item_avatar() : '';
		$right_area_out = '' !== $right_area_out ? '<div class="' . $this->item_selector . '__side-area">' . $right_area_out . '</div>' : '';

		$main_area_out = '<div class="' . $this->item_selector . '__main-area">' .
			( 'top' === $layout ? $this->render_item_author_info() : '' ) .
			( 'top' === $avatar_position ? $this->render_item_avatar() : '' ) .
			$this->render_item_content() .
			( 'bottom' === $layout ? $this->render_item_author_info() : '' ) .
			( 'bottom' === $avatar_position ? $this->render_item_avatar() : '' ) .
		'</div>';

		echo '<div class="' . $this->item_selector . '" itemscope itemtype="https://schema.org/Review">' .
			( 'custom' === $this->settings['icon_position'] ? $this->render_item_icon() : '' ) .
			( 'custom' === $this->settings['rating_position'] ? $this->render_item_rating() : '' ) .
			'<div class="elementor-screen-only" itemprop="itemReviewed" itemscope itemtype="https://schema.org/Organization">
				<meta itemprop="name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">
				<meta itemprop="url" content="' . esc_url( get_home_url() ) . '" >
			</div>' .
			'<div class="' . $this->item_selector . '__inner">';

				Utils::print_unescaped_internal_string( $left_area_out ); // XSS ok.

				Utils::print_unescaped_internal_string( $main_area_out ); // XSS ok.

				Utils::print_unescaped_internal_string( $right_area_out ); // XSS ok.

			echo '</div>' .
		'</div>';
	}

	/**
	 * Render item content.
	 *
	 * Retrieve the widget item content.
	 *
	 * @since 1.1.0
	 * @since 1.2.4 Add microformats.
	 *
	 * @return string Item content HTML.
	 */
	protected function render_item_content() {
		$triangle_out = '';
		$title_out = '';
		$text_out = esc_html__( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'cmsmasters-elementor' );

		if ( ! empty( $this->settings['content_triangle_toggle'] ) ) {
			$triangle_out = '<span class="' . $this->item_selector . '__triangle"></span>';
		}

		if ( ! empty( $this->item_settings['title'] ) ) {
			$tag = ( isset( $this->settings['title_tag'] ) ? $this->settings['title_tag'] : 'h6' );

			$title_out = '<' . Utils::validate_html_tag( $tag ) . ' class="' . $this->item_selector . '__title" itemprop="name">' .
				esc_html( $this->item_settings['title'] ) .
			'</' . Utils::validate_html_tag( $tag ) . '>';
		}

		if ( ! empty( $this->item_settings['text'] ) ) {
			$text_out = wp_kses_post( $this->item_settings['text'] );
		}

		return '<div class="' . $this->item_selector . '__content">' .
			$triangle_out .
			'<div class="' . $this->item_selector . '__content-outer">' .
				( ( 'start' === $this->settings['icon_position'] || 'top' === $this->settings['icon_position'] ) ? $this->render_item_icon() : '' ) .
				'<div class="' . $this->item_selector . '__content-inner">' .
					( 'in_content' === $this->settings['rating_position'] ? $this->render_item_rating() : '' ) .
					$title_out .
					'<div class="' . $this->item_selector . '__text" itemprop="reviewBody">' .
						'<p>' . $text_out . '</p>' .
					'</div>' .
				'</div>' .
				( ( 'end' === $this->settings['icon_position'] || 'bottom' === $this->settings['icon_position'] ) ? $this->render_item_icon() : '' ) .
			'</div>' .
		'</div>';
	}

	/**
	 * Render item avatar.
	 *
	 * Retrieve the widget item avatar.
	 *
	 * @since 1.1.0
	 *
	 * @return string Item avatar HTML.
	 */
	protected function render_item_avatar() {
		if ( empty( $this->item_settings['avatar']['url'] ) ) {
			return '';
		}

		$avatar = Group_Control_Image_Size::get_attachment_image_html( $this->item_settings, 'avatar' );

		if ( empty( $avatar ) ) {
			return '';
		}

		return '<div class="' . $this->item_selector . '__avatar">' .
			$this->wrap_author_link( $avatar ) .
		'</div>';
	}

	/**
	 * Render item author info.
	 *
	 * Retrieve the widget item author info.
	 *
	 * @since 1.1.0
	 *
	 * @return string Item author info HTML.
	 */
	protected function render_item_author_info() {
		$layout = $this->settings['layout'];
		$avatar_position = '';

		if (
			( 'left' === $layout || 'right' === $layout ) ||
			(
				( 'top' === $layout || 'bottom' === $layout ) &&
				'with_author' === $this->settings['avatar_placement']
			)
		) {
			$avatar_position = $this->settings['avatar_author_position'];
		}

		return '<div class="' . $this->item_selector . '__author-info">' .
			( ( 'top' === $avatar_position || 'left' === $avatar_position ) ? $this->render_item_avatar() : '' ) .
			'<div class="' . $this->item_selector . '__author-info-outer">' .
				'<div class="' . $this->item_selector . '__author-info-inner">' .
					( 'top_author' === $this->settings['rating_position'] ? $this->render_item_rating() : '' ) .
					'<div class="' . $this->item_selector . '__author-info-wrap">' .
						$this->render_item_author_name() .
						$this->render_item_author_subtitle() .
					'</div>' .
					( 'bottom_author' === $this->settings['rating_position'] ? $this->render_item_rating() : '' ) .
				'</div>' .
			'</div>' .
			( ( 'bottom' === $avatar_position || 'right' === $avatar_position ) ? $this->render_item_avatar() : '' ) .
		'</div>';
	}

	/**
	 * Render item author name.
	 *
	 * Retrieve the widget item author name.
	 *
	 * @since 1.1.0
	 *
	 * @return string Item author name HTML.
	 */
	protected function render_item_author_name() {
		$author_name = $this->item_settings['author_name'];

		if ( empty( $author_name ) ) {
			$author_name = esc_html__( 'Unknown', 'cmsmasters-elementor' );
		}

		return '<span class="' . $this->item_selector . '__author-name">' .
			$this->wrap_author_link( $author_name, 'name' ) .
		'</span>';
	}

	/**
	 * Render item author subtitle.
	 *
	 * Retrieve the widget item author subtitle.
	 *
	 * @since 1.1.0
	 *
	 * @return string Item author subtitle HTML.
	 */
	protected function render_item_author_subtitle() {
		if ( empty( $this->item_settings['author_subtitle'] ) ) {
			return '';
		}

		$out = '';

		if ( 'horizontal' === $this->settings['author_text_view'] ) {
			$delimiter = $this->settings['author_delimiter'];

			$out .= '<span class="' . $this->item_selector . '__author-delimiter">' .
				( ! empty( $delimiter ) ? esc_html( $delimiter ) : '' ) .
			'</span>';
		}

		$out .= '<span class="' . $this->item_selector . '__author-subtitle">' .
			$this->wrap_author_link( $this->item_settings['author_subtitle'] ) .
		'</span>';

		return $out;
	}

	/**
	 * Render item rating.
	 *
	 * Retrieve the widget item rating.
	 *
	 * @since 1.1.0
	 *
	 * @return string Item rating HTML.
	 */
	protected function render_item_rating() {
		$rating = $this->item_settings['rating'];

		if ( empty( $rating ) ) {
			return '';
		}

		$rating_scale = (int) $this->settings['rating_scale'];
		$rating = (float) ( $rating > $rating_scale ? $rating_scale : $rating );
		$textual_rating = $rating . '/' . $rating_scale;
		$filled_percent = $rating / $rating_scale * 100;

		$rating_out = '';

		if ( 'icon' === $this->settings['rating_type'] ) {
			$rating_out = '<div class="' . $this->item_selector . '__rating-icons">' .
				'<div class="' . $this->item_selector . '__rating-icons-empty">' .
					$this->render_item_rating_icons( 'empty' ) .
				'</div>' .
				'<div class="' . $this->item_selector . '__rating-icons-filled" style="width: ' . esc_attr( $filled_percent ) . '%">' .
					$this->render_item_rating_icons( 'filled' ) .
				'</div>' .
			'</div>';
		} else {
			$rating_out = '<div class="' . $this->item_selector . '__rating-text">' .
				'<span class="' . $this->item_selector . '__rating-text-current">' . esc_html( $rating ) . '</span>' .
				'<span class="' . $this->item_selector . '__rating-text-delimiter">' . esc_html( $this->settings['rating_text_delimiter'] ) . '</span>' .
				'<span class="' . $this->item_selector . '__rating-text-scale">' . esc_html( $rating_scale ) . '</span>' .
			'</div>';
		}

		$this->add_render_attribute( 'rating_inner', array(
			'class' => $this->item_selector . '__rating-inner',
			'title' => esc_attr( $textual_rating ),
			'itemtype' => 'http://schema.org/Rating',
			'itemscope' => '',
			'itemprop' => 'reviewRating',
		) );

		$out = '<div class="' . $this->item_selector . '__rating">' .
			'<div ' . $this->get_render_attribute_string( 'rating_inner' ) . '>' .
				$rating_out .
				'<span itemprop="ratingValue" class="elementor-screen-only">' . esc_html( $textual_rating ) . '</span>' .
			'</div>' .
		'</div>';

		return $out;
	}

	/**
	 * Render item rating icons.
	 *
	 * Retrieve the widget item rating icons.
	 *
	 * @since 1.1.0
	 *
	 * @return string Item rating icons HTML.
	 */
	protected function render_item_rating_icons( $type = 'empty' ) {
		$icon = $this->settings[ "rating_icon_{$type}" ];

		if ( 'filled' === $type && empty( $icon['value'] ) ) {
			$icon = $this->settings["rating_icon_empty"];
		}

		if ( empty( $icon['value'] ) ) {
			$icon = array(
				'value' => 'far fa-star',
				'library' => 'fa-regular',
			);
		}

		$rating_scale = (int) $this->settings['rating_scale'];
		$out = '';

		foreach ( range( 1, $rating_scale ) as $rating_number ) {
			$out .= CmsmastersUtils::get_render_icon( $icon, array( 'aria-hidden' => 'true' ) );
		}

		return $out;
	}

	/**
	 * Render item icon.
	 *
	 * Retrieve the widget item icon.
	 *
	 * @since 1.1.0
	 *
	 * @return string Item icon HTML.
	 */
	protected function render_item_icon() {
		$icon = $this->settings['icon'];

		if ( empty( $icon['value'] ) ) {
			return '';
		}

		return '<div class="' . $this->item_selector . '__icon">' .
			CmsmastersUtils::get_render_icon( $icon, array( 'aria-hidden' => 'true' ) ) .
		'</div>';
	}

	/**
	 * Wrap element to author link.
	 *
	 * @since 1.1.0
	 * @since 1.2.4 Add microformats.
	 *
	 * @param string $content Content in link.
	 * @param string $role Content role.
	 *
	 * @return string Element in link.
	 */
	protected function wrap_author_link( $content = '', $role = '' ) {
		if ( 'name' === $role ) {
			$content = '<span itemprop="author">' . esc_html( $content ) . '</span>';
		}

		if ( empty( $this->item_settings['author_link']['url'] ) ) {
			return $content;
		}

		return '<a ' . $this->get_render_attribute_string( 'author_link' . esc_attr( $this->item_settings['index'] ) ) . '>' . $content . '</a>';
	}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.1.0
	 */
	protected function content_template() {}
}
