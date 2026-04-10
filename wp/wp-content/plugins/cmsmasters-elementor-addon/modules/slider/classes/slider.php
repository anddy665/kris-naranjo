<?php
namespace CmsmastersElementor\Modules\Slider\Classes;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControlsManager;
use CmsmastersElementor\Controls\Groups\Group_Control_Flex_Align;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Skin_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters Elementor slider class.
 *
 * @since 1.0.0
 */
class Slider {

	/**
	 * Elementor object that adds controls.
	 *
	 * @since 1.0.0
	 *
	 * @var Controls_Stack|Skin_Base
	 */
	protected $element;

	/**
	 * Addon widget object.
	 *
	 * @since 1.0.0
	 *
	 * @var Base_Widget
	 */
	protected $widget;

	/**
	 *  Whether the element is skin.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	protected $is_skin = false;

	/**
	 * CSS selector
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $slider_selector = '';

	/**
	 * The conditions to check.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $conditions = array();

	/**
	 * Slider constructor.
	 *
	 * Initializing the slider in widget.
	 *
	 * @param Controls_Stack|Skin_Base $element
	 *
	 * @since 1.0.0
	 */
	public function __construct( $element ) {
		$this->element = $element;

		$this->is_skin = $this->element instanceof Skin_Base;
		$this->slider_selector = '#cmsmasters-slider-{{ID}}';

		$this->conditions = array(
			'navigation' => array(
				'relation' => 'or',
				'terms' => array(
					array(
						'name' => $this->get_control_prefix( 'slider_scrollbar' ),
						'value' => '',
					),
					array(
						'name' => $this->get_control_prefix( 'slider_infinite' ),
						'operator' => '!==',
						'value' => '',
					),
				),
			),
			'arrow_text' => array(
				'relation' => 'or',
				'terms' => array(
					array(
						'name' => $this->get_control_prefix( 'slider_arrow_text_prev' ),
						'operator' => '!=',
						'value' => '',
					),
					array(
						'name' => $this->get_control_prefix( 'slider_arrow_text_next' ),
						'operator' => '!=',
						'value' => '',
					),
				),
			),
		);
	}


	/**
	 * Register control.
	 *
	 * @since 1.0.0
	 * @since 1.0.2 `slider_per_view` control: removed 3 columns by default on mobile
	 */
	public function register_controls_content_per_view() {
		$per_view_options = array(
			'' => esc_html__( 'Auto', 'cmsmasters-elementor' ),
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
			'7' => '7',
			'8' => '8',
			'9' => '9',
			'10' => '10',
		);

		$this->element->add_control(
			'slider_type',
			array(
				'label' => __( 'Slider Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'default' => 'carousel',
				'options' => array(
					'carousel' => __( 'Carousel', 'cmsmasters-elementor' ),
					'coverflow' => __( 'Coverflow', 'cmsmasters-elementor' ),
				),
				'label_block' => false,
				'render_type' => 'none',
				'frontend_available' => true,
			)
		);

		$this->element->add_control(
			'slider_effect',
			array(
				'label' => __( 'Slider Effect', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => array(
					'slide' => __( 'Slide', 'cmsmasters-elementor' ),
					'cube' => __( 'Cube', 'cmsmasters-elementor' ),
					'fade' => __( 'Fade', 'cmsmasters-elementor' ),
					'flip' => __( 'Flip', 'cmsmasters-elementor' ),
				),
				'prefix_class' => 'cmsmasters-slider--effect-',
				'render_type' => 'none',
				'frontend_available' => true,
				'condition' => array(
					$this->get_control_prefix( 'slider_type!' ) => 'coverflow',
				),
			)
		);

		$this->element->add_control(
			'slider_direction',
			array(
				'label' => __( 'Slider Direction', 'cmsmasters-elementor' ),
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => __( 'Horizontal', 'cmsmasters-elementor' ),
					'vertical' => __( 'Vertical', 'cmsmasters-elementor' ),
				),
				'label_block' => false,
				'render_type' => 'ui',
				'frontend_available' => true,
			)
		);

		$this->element->add_control(
			'slider_height_type',
			array(
				'label' => __( 'Slider Height', 'cmsmasters-elementor' ),
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'default' => 'auto',
				'options' => array(
					'auto' => __( 'Auto', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom', 'cmsmasters-elementor' ),
				),
				'label_block' => false,
				'render_type' => 'ui',
				'frontend_available' => true,
				'condition' => array(
					$this->get_control_prefix( 'slider_direction' ) => 'horizontal',
					$this->get_control_prefix( 'slider_effect!' ) => 'cube',
				),
			)
		);

		$this->element->add_responsive_control(
			'slider_height',
			array(
				'label' => __( 'Slider Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 500,
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'vh',
					'vw',
					'%',
				),
				'range' => array(
					'px' => array(
						'max' => 1080,
						'min' => 50,
					),
					'vh' => array(
						'max' => 100,
						'min' => 1,
					),
				),
				'selectors' => array(
					$this->slider_selector => '--cmsmasters-slider-height: {{SIZE}}{{UNIT}};',
				),
				'render_type' => 'ui',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => $this->get_control_prefix( 'slider_direction' ),
									'operator' => '===',
									'value' => 'horizontal',
								),
								array(
									'name' => $this->get_control_prefix( 'slider_height_type' ),
									'operator' => '===',
									'value' => 'custom',
								),
							),
						),
						array(
							'name' => $this->get_control_prefix( 'slider_direction' ),
							'operator' => '===',
							'value' => 'vertical',
						),
						array(
							'name' => $this->get_control_prefix( 'slider_effect' ),
							'operator' => '===',
							'value' => 'cube',
						),
					),
				),
			)
		);

		$this->element->add_responsive_control(
			'slider_per_view',
			array(
				'label' => __( 'Slides Per View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'render_type' => 'none',
				'options' => $per_view_options,
				'frontend_available' => true,
				'selectors' => array(
					$this->slider_selector => '--cmsmasters-columns: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_prefix( 'slider_direction' ) => 'horizontal',
					$this->get_control_prefix( 'slider_effect' ) => 'slide',
				),
			)
		);

		$this->element->add_control(
			'slider_to_scroll',
			array(
				'label' => __( 'Slides to Scroll', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'description' => __( 'Set how many slides are scrolled per swipe.', 'cmsmasters-elementor' ),
				'default' => '',
				'options' => $per_view_options,
				'render_type' => 'none',
				'frontend_available' => true,
				'condition' => array(
					$this->get_control_prefix( 'slider_effect' ) => 'slide',
					$this->get_control_prefix( 'slider_direction' ) => 'horizontal',
					$this->get_control_prefix( 'slider_per_view!' ) => '1',
				),
			)
		);
	}

	/**
	 * Get the name of the control with the skin prefix.
	 *
	 * @param string $control_base_id
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_skin_control_id( $control_base_id ) {
		$skin_id = str_replace( '-', '_', $this->element->get_id() );

		return "{$skin_id}_{$control_base_id}";
	}

	/**
	 * Get the name of the control with the skin prefix.
	 *
	 * @param string $control_base_id
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @return string
	 */
	public function get_control_prefix( $id ) {
		if ( $this->is_skin ) {
			return $this->get_skin_control_id( $id );
		}

		return $id;
	}

	/**
	 * Register slider widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Added icon switcher & remove default icon.
	 */
	public function register_section_content() {
		$condition_arrows = array(
			$this->get_control_prefix( 'slider_arrows!' ) => '',
		);

		$this->element->start_controls_section(
			'section_slider_options',
			array(
				'label' => __( 'Slider Options', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->element->add_control(
			'slider_autoplay',
			array(
				'label' => __( 'Autoplay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'render_type' => 'none',
				'frontend_available' => true,
			)
		);

		$this->element->add_control(
			'slider_autoplay_speed',
			array(
				'label' => __( 'Autoplay Speed', 'cmsmasters-elementor' ) . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'min' => 500,
				'step' => 100,
				'frontend_available' => true,
				'render_type' => 'none',
				'condition' => array(
					$this->get_control_prefix( 'slider_autoplay!' ) => '',
				),
			)
		);

		$this->element->add_control(
			'slider_speed',
			array(
				'label' => __( 'Animation Speed', 'cmsmasters-elementor' ) . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
				'step' => 100,
				'render_type' => 'none',
				'frontend_available' => true,
			)
		);

		$this->element->add_control(
			'slider_slide_index',
			array(
				'label' => __( 'Active Slide', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'placeholder' => '1',
				'min' => 1,
				'step' => 1,
				'render_type' => 'none',
				'frontend_available' => true,
			)
		);

		$this->element->add_control(
			'slider_pause_on_hover',
			array(
				'label' => __( 'Pause on Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'render_type' => 'none',
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => array(
					$this->get_control_prefix( 'slider_autoplay!' ) => '',
				),
			)
		);

		$this->element->add_control(
			'slider_autoplay_reverse',
			array(
				'label' => __( 'Autoplay Reverse', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'render_type' => 'none',
				'frontend_available' => true,
				'condition' => array(
					$this->get_control_prefix( 'slider_autoplay!' ) => '',
				),
			)
		);

		$this->element->add_control(
			'slider_infinite',
			array(
				'label' => __( 'Infinite Loop', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'render_type' => 'none',
				'default' => 'yes',
				'frontend_available' => true,
			)
		);

		$this->element->add_control(
			'slider_mousewheel',
			array(
				'label' => __( 'Mousewheel Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'render_type' => 'none',
				'frontend_available' => true,
			)
		);

		$this->element->add_control(
			'slider_centered_slides',
			array(
				'label' => __( 'Centered Slides', 'cmsmasters-elementor' ),
				'description' => __( 'Turn on for a slider with an even number of slides only', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'render_type' => 'none',
				'frontend_available' => true,
				'condition' => array(
					$this->get_control_prefix( 'slider_type' ) => 'carousel',
					$this->get_control_prefix( 'slider_effect' ) => 'slide',
				),
			)
		);

		$this->element->add_responsive_control(
			'slider_centered_slides_width',
			array(
				'label' => __( 'Centered Slides Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 50,
					'unit' => '%',
				),
				'size_units' => array( '%' ),
				'range' => array(
					'%' => array(
						'max' => 90,
						'min' => 35,
					),
				),
				'selectors' => array(
					"{$this->slider_selector} .swiper-slide" => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_prefix( 'slider_centered_slides!' ) => '',
					$this->get_control_prefix( 'slider_effect' ) => 'slide',
					$this->get_control_prefix( 'slider_per_view' ) => array( 'auto' ),
					$this->get_control_prefix( 'slider_type' ) => 'carousel',
				),
			)
		);

		$this->element->add_control(
			'slider_free_mode',
			array(
				'label' => __( 'Free Positioning', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Enable free slides positioning.', 'cmsmasters-elementor' ),
				'render_type' => 'none',
				'frontend_available' => true,
			)
		);

		$this->element->add_control(
			'slider_scrollbar',
			array(
				'label' => __( 'Scrollbar', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'render_type' => 'ui',
				'separator' => 'before',
				'condition' => array(
					$this->get_control_prefix( 'slider_infinite' ) => '',
				),
			)
		);

		$this->element->add_control(
			'slider_navigation_heading',
			array(
				'label' => __( 'Navigation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => $this->conditions['navigation'],
			)
		);

		$this->element->add_control(
			'slider_navigation',
			array(
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'default' => '',
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'bullets' => __( 'Bullets', 'cmsmasters-elementor' ),
					'progressbar' => __( 'Progress', 'cmsmasters-elementor' ),
					'fraction' => __( 'Fraction', 'cmsmasters-elementor' ),
				),
				'frontend_available' => true,
				'render_type' => 'ui',
				'conditions' => $this->conditions['navigation'],
			)
		);

		$this->element->add_control(
			'slider_bullets_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'options' => array(
					'' => __( 'Normal', 'cmsmasters-elementor' ),
					'dynamic' => __( 'Dynamic', 'cmsmasters-elementor' ),
					'numbered' => __( 'Numbered', 'cmsmasters-elementor' ),
				),
				'label_block' => false,
				'render_type' => 'ui',
				'frontend_available' => true,
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						$this->conditions['navigation'],
						array(
							'name' => $this->get_control_prefix( 'slider_navigation' ),
							'value' => 'bullets',
						),
					),
				),
			)
		);

		$this->element->add_control(
			'slider_arrows_heading',
			array(
				'label' => __( 'Arrows', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->element->add_control(
			'slider_arrows',
			array(
				'label' => __( 'Show Arrows', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'render_type' => 'template',
			)
		);

		$this->element->add_control(
			'slider_arrows_visibility',
			array(
				'label' => __( 'Visibility', 'cmsmasters-elementor' ),
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'default' => 'always',
				'label_block' => false,
				'options' => array(
					'always' => __( 'Always', 'cmsmasters-elementor' ),
					'hover' => __( 'On Hover', 'cmsmasters-elementor' ),
				),
				'render_type' => 'ui',
				'frontend_available' => true,
				'condition' => $condition_arrows,
			)
		);

		$this->element->add_control(
			'slider_arrows_switcher',
			array(
				'label' => __( 'Enable Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => $condition_arrows,
			)
		);

		$this->element->start_controls_tabs(
			'slider_arrows_tabs_prev_next',
			array(
				'condition' => $condition_arrows,
			)
		);

		foreach ( array(
			'prev' => __( 'Previous', 'cmsmasters-elementor' ),
			'next' => __( 'Next', 'cmsmasters-elementor' ),
		) as $key => $label ) {
			$is_prev = 'prev' === $key;
			$selector = "{$this->slider_selector}";

			if ( $is_prev ) {
				$selector .= ' .swiper-button-prev .swiper-button-inner';
			} else {
				$selector .= ' .swiper-button-next .swiper-button-inner';
			}

			$this->element->start_controls_tab(
				"slider_arrow_tab_{$key}",
				array(
					'label' => $label,
					'condition' => $condition_arrows,
				)
			);

			$this->element->add_control(
				"slider_arrow_text_{$key}",
				array(
					'label' => __( 'Text', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'condition' => $condition_arrows,
				)
			);

			$this->element->add_control(
				"slider_arrow_icon_{$key}",
				array(
					'label' => __( 'Custom Icon', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::ICONS,
					'render_type' => 'template',
					'condition' => $condition_arrows + array(
						'slider_arrows_switcher!' => '',
					),
				)
			);

			$this->element->add_control(
				"slider_arrow_direction_{$key}",
				array(
					'label' => __( 'Icon Position', 'cmsmasters-elementor' ),
					'type' => CmsmastersControlsManager::CHOOSE_TEXT,
					'label_block' => false,
					'default' => $is_prev ? 'before' : 'after',
					'options' => array(
						'before' => __( 'Before', 'cmsmasters-elementor' ),
						'after' => __( 'After', 'cmsmasters-elementor' ),
					),
					'selectors_dictionary' => array(
						'before' => ' ',
						'after' => '-reverse',
					),
					'selectors' => array(
						$selector => 'flex-direction: row{{VALUE}}',
						".cmsmasters-slider--text-dir-arrows-vertical{$selector}" => 'flex-direction: column{{VALUE}}',
					),
					'condition' => array_merge( $condition_arrows, array(
						'slider_arrows_switcher!' => '',
						$this->get_control_prefix( "slider_arrow_text_{$key}!" ) => '',
					) ),
				)
			);

			$this->element->end_controls_tab();
		}

		$this->element->end_controls_tabs();

		$this->element->add_control(
			'slider_arrows_text_dir',
			array(
				'label' => __( 'Text Direction', 'cmsmasters-elementor' ),
				'type' => CmsmastersControlsManager::CHOOSE_TEXT,
				'label_block' => false,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => __( 'Horizontal', 'cmsmasters-elementor' ),
					'vertical' => __( 'Vertical', 'cmsmasters-elementor' ),
				),
				'render_type' => 'ui',
				'frontend_available' => true,
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => $this->get_control_prefix( 'slider_arrows' ),
							'operator' => '!=',
							'value' => '',
						),
						array(
							'name' => $this->get_control_prefix( 'slider_arrows_switcher' ),
							'operator' => '!=',
							'value' => '',
						),
						$this->conditions['arrow_text'],
					),
				),
			)
		);

		$this->element->end_controls_section();
	}

	/**
	 * Register slider widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	public function register_sections_style() {
		$this->register_section_style_style_layout();
		$this->register_section_style_arrows();
		$this->register_section_style_bullets();
		$this->register_section_style_fraction();
		$this->register_section_style_progressbar();
		$this->register_section_style_scrollbar();
	}

	/**
	 * Register slider widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	public function register_section_style_bullets() {
		$condition = array(
			$this->get_control_prefix( 'slider_navigation' ) => 'bullets',
		);
		$condition_text = array_merge( $condition, array(
			$this->get_control_prefix( 'slider_bullets_type' ) => 'numbered',
		) );

		$this->element->start_controls_section(
			'section_slider_style_bullets',
			array(
				'label' => __( 'Slider Navigation', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
				'conditions' => $this->conditions['navigation'],
			)
		);

		$this->element->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'slider_bullets',
				'selector' => "{$this->slider_selector} .swiper-pagination-bullet",
				'condition' => $condition_text,
				'exclude' => array( 'line_height' ),
			)
		);

		$this->element->start_controls_tabs( 'slider_bullets_style_tabs' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'active' => __( 'Active', 'cmsmasters-elementor' ),
		) as $key => $label ) {
			$selector = "{$this->slider_selector} .swiper-pagination-bullet";

			switch ( $key ) {
				case 'hover':
					$selector .= ':hover';

					break;
				case 'active':
					$selector .= '.swiper-pagination-bullet-active';

					break;
			}

			$this->element->start_controls_tab(
				"slider_bullets_style_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->element->add_control(
				"slider_bullets_bg_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'background-color: {{VALUE}};',
					),
					'condition' => $condition,
				)
			);

			$this->element->add_control(
				"slider_bullets_text_color_{$key}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
					),
					'condition' => $condition_text,
				)
			);

			$this->element->add_control(
				"slider_bullets_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'border-color: {{VALUE}};',
					),
					'condition' => $condition + array(
						'slider_bullets_border!' => array( '', 'none' ),
					),
				)
			);

			$this->element->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "slider_bullets_{$key}",
					'selector' => $selector,
					'condition' => $condition,
				)
			);

			$this->element->end_controls_tab();
		}

		$this->element->end_controls_tabs();

		$this->element->add_control(
			'slider_bullets_border',
			array(
				'label' => __( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'none' => __( 'Disable', 'cmsmasters-elementor' ),
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
					'groove' => __( 'Groove', 'cmsmasters-elementor' ),
				),
				'condition' => array(
					$this->get_control_prefix( 'slider_navigation' ) => 'bullets',
				),
				'separator' => 'before',
				'selectors' => array(
					"{$this->slider_selector} .swiper-pagination-bullet" => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->element->add_responsive_control(
			'slider_bullets_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'default' => array(
					'size' => 1,
				),
				'selectors' => array(
					"{$this->slider_selector} .swiper-pagination-bullet" => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => $condition + array(
					$this->get_control_prefix( 'slider_bullets_border!' ) => array( '', 'none' ),
				),
			)
		);

		$this->element->add_responsive_control(
			'slider_bullets_bdrs',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 50,
					'unit' => '%',
				),
				'size_units' => array( '%' ),
				'range' => array(
					'%' => array(
						'max' => 50,
						'min' => 0,
					),
				),
				'selectors' => array(
					"{$this->slider_selector} .swiper-pagination-bullet" => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);

		$this->element->add_responsive_control(
			'slider_bullets_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 50,
						'min' => 1,
					),
				),
				'selectors' => array(
					$this->slider_selector => '--cmsmasters-bullets-size: {{SIZE}}{{UNIT}}',
				),
				'size_units' => array( 'px' ),
				'condition' => $condition,
			)
		);

		$this->element->add_responsive_control(
			'slider_bullets_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 20,
						'min' => 0,
					),
				),
				'selectors' => array(
					$this->slider_selector => '--cmsmasters-bullets-spacing: {{SIZE}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);

		$this->element->add_control(
			'slider_bullets_container_heading',
			array(
				'label' => __( 'Container', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => $condition,
			)
		);

		$this->element->add_control(
			'slider_bullets_bg_color_outer',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$this->slider_selector} .swiper-pagination-outer" => 'background-color: {{VALUE}};',
				),
				'condition' => $condition,
			)
		);

		$this->element->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'slider_bullets_border',
				'selector' => "{$this->slider_selector} .swiper-pagination-outer",
				'condition' => $condition,
			)
		);

		$this->element->add_responsive_control(
			'slider_bullets_container_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					"{$this->slider_selector} .swiper-pagination-outer" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->element->add_responsive_control(
			'slider_bullets_container_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					"{$this->slider_selector} .swiper-pagination-wrap" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->element->add_responsive_control(
			'slider_bullets_container_bdrs',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'selectors' => array(
					"{$this->slider_selector} .swiper-pagination-outer" => 'border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition' => $condition,
			)
		);

		$this->element->add_group_control(
			Group_Control_Flex_Align::get_type(),
			array(
				'name' => 'slider_bullets_container',
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'selector' => "{$this->slider_selector} .swiper-pagination-wrap",
				'fields_options' => array(
					'position' => array(
						'type' => Controls_Manager::HIDDEN,
					),
					'jc_horizontal' => array(
						'default' => 'center',
					),
					'ai_vertical' => array(
						'default' => 'flex-end',
					),
				),
				'exclude_property' => array(
					'horizontal' => array(
						'space-between',
					),
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => $this->get_control_prefix( 'slider_scrollbar' ),
									'operator' => '==',
									'value' => '',
								),
								array(
									'name' => $this->get_control_prefix( 'slider_infinite' ),
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'name' => $this->get_control_prefix( 'slider_navigation' ),
							'operator' => '==',
							'value' => 'bullets',
						),
					),
				),
			)
		);

		$this->element->end_controls_section();
	}

	/**
	 * Register slider widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	public function register_section_style_scrollbar() {
		$selector = "{$this->slider_selector} .swiper-scrollbar";
		$conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => $this->get_control_prefix( 'slider_scrollbar' ),
					'operator' => '!==',
					'value' => '',
				),
				array(
					'name' => $this->get_control_prefix( 'slider_infinite' ),
					'operator' => '==',
					'value' => '',
				),
			),
		);

		$this->element->start_controls_section(
			'section_slider_style_scrollbar',
			array(
				'label' => __( 'Slider Scrollbar', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => $conditions,
			)
		);

		$this->element->add_control(
			'slider_scrollbar_bg_color_outer',
			array(
				'label' => __( 'Background Color Outer', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->element->add_control(
			'slider_scrollbar_bg_color_inner',
			array(
				'label' => __( 'Background Color Inner', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$selector} .swiper-scrollbar-drag" => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->element->add_control(
			'slider_scrollbar_visible',
			array(
				'label' => __( 'Always Visible', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'render_type' => 'ui',
				'separator' => 'before',
				'frontend_available' => true,
			)
		);

		$this->element->add_control(
			'slider_scrollbar_circle',
			array(
				'label' => __( 'Circle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'circle',
				'return_value' => 'circle',
				'render_type' => 'ui',
				'frontend_available' => true,
			)
		);

		$this->element->add_control(
			'slider_scrollbar_thickness',
			array(
				'label' => __( 'Thickness', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
				),
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 10,
						'min' => 2,
					),
				),
				'selectors' => array(
					".cmsmasters-slider--dir-horizontal{$selector}" => 'height: {{SIZE}}{{UNIT}}',
					".cmsmasters-slider--dir-vertical{$selector}" => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->element->add_control(
			'slider_scrollbar_side',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'flex-start',
				'options' => array(
					'flex-start' => __( 'Start', 'cmsmasters-elementor' ),
					'flex-end' => __( 'End', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					"{$this->slider_selector} .swiper-scrollbar-wrap" => 'align-items: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_prefix( 'slider_infinite' ) => '',
					$this->get_control_prefix( 'slider_scrollbar!' ) => '',
				),
			)
		);

		$this->element->end_controls_section();
	}

	/**
	 * Register slider widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	public function register_section_style_arrows() {
		$selector = "{$this->slider_selector} .swiper-button";
		$condition = array(
			$this->get_control_prefix( 'slider_arrows!' ) => '',
		);
		$conditions_icon = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => $this->get_control_prefix( 'slider_arrows_switcher' ),
					'operator' => '!=',
					'value' => '',
				),
			),
		);
		$conditions_icon_spacing = array(
			'relation' => 'and',
			'terms' => array(
				$conditions_icon,
				$this->conditions['arrow_text'],
			),
		);

		$this->element->start_controls_section(
			'section_slider_style_arrows',
			array(
				'label' => __( 'Slider Arrows', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
			)
		);

		$this->element->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'slider_arrows_filter',
				'selector' => "{$selector} .text",
				'condition' => $condition,
				'conditions' => $this->conditions['arrow_text'],
				'exclude' => array( 'line_height' ),
			)
		);

		$this->element->start_controls_tabs( 'slider_arrows_style_tabs' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'disabled' => __( 'Disabled', 'cmsmasters-elementor' ),
		) as $key => $label ) {
			$selector_tab = $selector;

			if ( 'normal' !== $key ) {
				$selector_tab .= ':hover';
			}

			$tab_args = array(
				'label' => $label,
			);

			if ( 'disabled' === $key ) {
				$tab_args['condition'] = array(
					'slider_infinite' => '',
				);
			}

			$this->element->start_controls_tab( "slider_arrows_style_tab_{$key}", $tab_args );

			if ( 'disabled' === $key ) {
				$this->element->add_control(
					"slider_arrows_opacity_{$key}",
					array(
						'label' => __( 'Opacity', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'range' => array(
							'px' => array(
								'max' => 0.9,
								'min' => 0.0,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							$this->slider_selector => '--cmsmasters-slider-disabled-opacity: {{SIZE}};',
						),
					)
				);

				continue;
			}

			$this->element->add_control(
				"slider_arrows_icon_color_{$key}",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$selector_tab} .cmsmasters-wrap-icon" => 'color: {{VALUE}};',
					),
					'condition' => $condition,
					'conditions' => $conditions_icon,
				)
			);

			$this->element->add_control(
				"slider_arrows_bg_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_tab => 'background-color: {{VALUE}};',
					),
					'condition' => $condition,
				)
			);

			$this->element->add_control(
				"slider_arrows_text_color_{$key}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_tab => 'color: {{VALUE}};',
					),
					'condition' => $condition,
					'conditions' => $this->conditions['arrow_text'],
				)
			);

			$this->element->add_control(
				"slider_arrows_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_tab => 'border-color: {{VALUE}};',
					),
					'condition' => array_merge( $condition, array(
						$this->get_control_prefix( 'slider_arrows_border!' ) => array( '', 'none' ),
					) ),
				)
			);

			$this->element->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "slider_arrows_{$key}",
					'selector' => $selector_tab,
					'condition' => $condition,
				)
			);

			$this->element->end_controls_tab();
		}

		$this->element->end_controls_tabs();

		$this->element->add_responsive_control(
			'slider_arrows_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 50,
						'min' => 1,
					),
					'em' => array(
						'max' => 5,
						'min' => 0.1,
						'step' => 0.1,
					),
				),
				'size_units' => array( 'px', 'em' ),
				'separator' => 'before',
				'selectors' => array(
					"{$selector} .cmsmasters-wrap-icon" => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $conditions_icon,
				'condition' => $condition,
			)
		);

		$this->element->add_responsive_control(
			'slider_arrows_spacing',
			array(
				'label' => __( 'Arrows Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 50,
						'min' => 0,
					),
				),
				'selectors' => array(
					$this->slider_selector => '--cmsmasters-arrows-spacing: {{SIZE}}{{UNIT}};',
				),
				'condition' => array_merge( $condition, array(
					$this->get_control_prefix( 'slider_arrows_align_jc_horizontal!' ) => 'space-between',
				) ),
			)
		);

		$this->element->add_responsive_control(
			'slider_arrows_width',
			array(
				'label' => __( 'Arrows Box Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
				),
				'range' => array(
					'%' => array(
						'max' => 100,
						'min' => 5,
					),
					'px' => array(
						'max' => 250,
						'min' => 10,
					),
				),
				'size_units' => array( '%', 'px' ),
				'selectors' => array(
					$this->slider_selector => '--cmsmasters-arrows-width: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->element->add_responsive_control(
			'slider_arrows_height',
			array(
				'label' => __( 'Arrows Box Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
				),
				'range' => array(
					'%' => array(
						'max' => 100,
						'min' => 5,
					),
					'px' => array(
						'max' => 250,
						'min' => 10,
					),
				),
				'size_units' => array( '%', 'px' ),
				'selectors' => array(
					$this->slider_selector => '--cmsmasters-arrows-height: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->element->add_responsive_control(
			'slider_arrows_bdrs',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'max' => 100,
						'min' => 0,
					),
					'%' => array(
						'max' => 50,
						'min' => 0,
					),
				),
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					$selector => 'border-radius: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->element->add_responsive_control(
			'slider_arrows_icon_spacing',
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 5,
				),
				'range' => array(
					'px' => array(
						'max' => 50,
						'min' => 0,
					),
				),
				'selectors' => array(
					"{$this->slider_selector} .swiper-button-inner" => 'margin: -{{SIZE}}{{UNIT}};',
					"{$this->slider_selector} .swiper-button-inner > *" => 'margin: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $conditions_icon_spacing,
				'condition' => $condition,
			)
		);

		$this->element->add_control(
			'slider_arrows_border',
			array(
				'label' => __( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'none' => __( 'Disable', 'cmsmasters-elementor' ),
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
					'groove' => __( 'Groove', 'cmsmasters-elementor' ),
				),
				'condition' => $condition,
				'separator' => 'before',
				'selectors' => array(
					$selector => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->element->add_responsive_control(
			'slider_arrows_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					$selector => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array_merge( $condition, array(
					$this->get_control_prefix( 'slider_arrows_border!' ) => array( '', 'none' ),
				) ),
			)
		);

		$this->element->add_responsive_control(
			'slider_arrows_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->element->add_control(
			'slider_arrows_hide_tablet_mobile',
			array(
				'label' => __( 'Hide Text on Tablet/Mobile', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'selectors' => array(
					"(tablet){$selector} .text" => 'display: none;',
				),
				'condition' => $condition,
				'conditions' => $conditions_icon_spacing,
			)
		);

		$this->element->add_control(
			'slider_arrows_container_heading',
			array(
				'label' => __( 'Container', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $condition,
			)
		);

		$this->element->add_group_control(
			Group_Control_Flex_Align::get_type(),
			array(
				'name' => 'slider_arrows_align',
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'selector' => "{$this->slider_selector} .swiper-buttons-wrap-inner",
				'fields_options' => array(
					'position' => array(
						'frontend_available' => true,
					),
					'jc_horizontal' => array(
						'default' => 'space-between',
					),
					'ai_horizontal' => array(
						'default' => 'flex-end',
					),
					'jc_vertical' => array(
						'default' => 'flex-end',
					),
					'ai_vertical' => array(
						'default' => 'center',
					),
				),
				'condition' => $condition,
			)
		);

		$this->element->add_responsive_control(
			'slider_arrows_container_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					"{$this->slider_selector} .swiper-buttons-wrap" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->element->end_controls_section();
	}

	/**
	 * Register slider widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	public function register_section_style_style_layout() {
		$this->element->start_controls_section(
			'section_slider_layout_style',
			array(
				'label' => __( 'Slider Layout', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->element->add_responsive_control(
			'slider_space_between',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 50,
					),
				),
				'render_type' => 'ui',
				'frontend_available' => true,
				'selectors' => array(
					$this->slider_selector => '--cmsmasters-space-between: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_control_prefix( 'slider_effect' ) => 'slide',
				),
			)
		);

		$this->element->add_responsive_control(
			'slider_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => '%',
				),
				'size_units' => array( 'px', '%', 'vw', 'vh' ),
				'range' => array(
					'px' => array(
						'max' => 1920,
						'min' => 320,
					),
					'%' => array(
						'max' => 100,
						'min' => 10,
					),
				),
				'render_type' => 'ui',
				'frontend_available' => true,
				'selectors' => array(
					$this->slider_selector => '--cmsmasters-slider-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->element->add_responsive_control(
			'slider_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					"{$this->slider_selector} .swiper" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->element->add_control(
			'slider_bd_type',
			array(
				'label' => __( 'Separators', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
					'groove' => __( 'Groove', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					"{$this->slider_selector} .swiper-slide::after" => 'border-right-style: {{VALUE}}',
				),
				'separator' => 'before',
				'condition' => array(
					$this->get_control_prefix( 'slider_per_view!' ) => '1',
					$this->get_control_prefix( 'slider_direction' ) => 'horizontal',
					$this->get_control_prefix( 'slider_type' ) => 'carousel',
				),
			)
		);

		$this->element->add_responsive_control(
			'slider_bd_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 1,
				),
				'range' => array(
					'px' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					$this->slider_selector => '--cmsmasters-slider-bd-width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					$this->get_control_prefix( 'slider_bd_type!' ) => '',
					$this->get_control_prefix( 'slider_per_view!' ) => '1',
					$this->get_control_prefix( 'slider_direction' ) => 'horizontal',
					$this->get_control_prefix( 'slider_type' ) => 'carousel',
				),
			)
		);

		$this->element->add_control(
			'slider_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$this->slider_selector} .swiper-slide::after" => 'border-right-color: {{VALUE}}',
				),
				'condition' => array(
					$this->get_control_prefix( 'slider_bd_type!' ) => '',
					$this->get_control_prefix( 'slider_per_view!' ) => '1',
					$this->get_control_prefix( 'slider_direction' ) => 'horizontal',
					$this->get_control_prefix( 'slider_type' ) => 'carousel',
				),
			)
		);

		$this->element->end_controls_section();
	}

	/**
	 * Register slider widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	public function register_section_style_fraction() {
		$selector = "{$this->slider_selector} .swiper-pagination-fraction";
		$condition = array(
			$this->get_control_prefix( 'slider_navigation' ) => 'fraction',
		);

		$this->element->start_controls_section(
			'section_slider_fraction',
			array(
				'label' => __( 'Slider Navigation', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
				'conditions' => $this->conditions['navigation'],
			)
		);

		$this->element->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'slider_fraction',
				'selector' => $selector,
				'condition' => $condition,
			)
		);

		$this->element->add_control(
			'slider_fraction_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => 'color: {{VALUE}}',
				),
			)
		);

		$this->element->add_control(
			'slider_fraction_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->element->add_control(
			'slider_fraction_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					$this->get_control_prefix( 'slider_fraction_border_border!' ) => '',
				),
			)
		);

		$this->element->add_control(
			'slider_fraction_space',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
				),
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 30,
						'min' => 0,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					$this->slider_selector => '--cmsmasters-fraction-spacing: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->element->add_control(
			'slider_fraction_container_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Container', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->element->add_responsive_control(
			'slider_fraction_container_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					"{$this->slider_selector} .swiper-pagination-fraction" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->element->add_responsive_control(
			'slider_fraction_container_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					"{$this->slider_selector} .swiper-pagination-wrap" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->element->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'slider_fraction_border',
				'selector' => $selector,
				'condition' => $condition,
				'exclude' => array( 'color' ),
			)
		);

		$this->element->add_control(
			'slider_fraction_bdrs',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
				),
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 30,
						'min' => 0,
					),
				),
				'selectors' => array(
					$selector => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->element->add_group_control(
			Group_Control_Flex_Align::get_type(),
			array(
				'name' => 'slider_fraction_container',
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'selector' => "{$this->slider_selector} .swiper-pagination-wrap",
				'fields_options' => array(
					'position' => array(
						'type' => Controls_Manager::HIDDEN,
					),

					'jc_horizontal' => array(
						'default' => 'center',
					),

					'jc_vertical' => array(
						'default' => 'flex-end',
					),
				),
				'exclude_property' => array(
					'horizontal' => array(
						'space-between',
					),
				),
				'condition' => $condition,
			)
		);

		$this->element->end_controls_section();
	}

	/**
	 * Register slider widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	public function register_section_style_progressbar() {
		$selector = "{$this->slider_selector} .swiper-pagination-progressbar";
		$condition = array(
			$this->get_control_prefix( 'slider_navigation' ) => 'progressbar',
		);

		$this->element->start_controls_section(
			'section_slider_progressbar',
			array(
				'label' => __( 'Slider Navigation', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
				'conditions' => $this->conditions['navigation'],
			)
		);

		$this->element->start_controls_tabs(
			'slider_progressbar_bg_tabs',
			array(
				'condition' => $condition,
			)
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'fill' => __( 'Fill', 'cmsmasters-elementor' ),
		) as $key => $label ) {
			$selector_bg = $selector;

			if ( 'fill' === $key ) {
				$selector_bg .= '-fill';
			}

			$this->element->start_controls_tab(
				"slider_progressbar_bg_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->element->add_control(
				"slider_progressbar_bg_{$key}",
				array(
					'label' => __( 'Background', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_bg => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->element->end_controls_tab();
		}

		$this->element->end_controls_tabs();

		$this->element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'slider_progressbar_box_shadow',
				'selector' => $selector,
			)
		);

		$this->element->add_control(
			'slider_progressbar_thickness',
			array(
				'label' => __( 'Thickness', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
				),
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 10,
						'min' => 2,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					".cmsmasters-slider--dir-horizontal{$selector}" => 'height: {{SIZE}}{{UNIT}}',
					".cmsmasters-slider--dir-vertical{$selector}" => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->element->add_control(
			'slider_progressbar_circle',
			array(
				'label' => __( 'Circle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'circle',
				'return_value' => 'circle',
				'render_type' => 'ui',
				'frontend_available' => true,
			)
		);

		$this->element->add_control(
			'slider_progressbar_container_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Container', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->element->add_responsive_control(
			'slider_progressbar_container_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					"{$this->slider_selector} .swiper-pagination-wrap" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->element->add_control(
			'slider_progressbar_side',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'flex-start' => __( 'Start', 'cmsmasters-elementor' ),
					'flex-end' => __( 'End', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					"{$this->slider_selector} .swiper-pagination-wrap" => 'align-items: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->element->end_controls_section();
	}

	/**
	 * Start slide rendering.
	 *
	 * Use when rendering each slide.
	 *
	 * @since 1.0.0
	 */
	public function render_slide_open() {
		echo '<div class="swiper-slide">';
	}

	/**
	 * Finish slide rendering.
	 *
	 * Use when rendering each slide.
	 *
	 * @since 1.0.0
	 */
	public function render_slide_close() {
		echo '</div>';
	}

	/**
	 * Render slider.
	 *
	 * Generates the final HTML on the frontend.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 * @since 1.0.2 Fixed checking `$callback` variable.
	 */
	public function render( $callback ) {
		if ( ! is_callable( $callback ) ) {
			return;
		}

		$this->render_root( function() use ( $callback ) {
			$this->render_slides( $callback );
			$this->render_interface();
		} );
	}

	/**
	 * Wrapper for slider.
	 *
	 * @param callable $callback
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 * @since 1.0.2 Fixed checking `$callback` variable.
	 */
	public function render_root( $callback ) {
		if ( ! is_callable( $callback ) ) {
			return;
		}

		if ( $this->is_skin ) {
			if ( ! $this->widget instanceof Base_Widget ) {
				throw new \Exception( 'Widget most be instance, run `set_widget` method before render slider.' );
			}

			$id_prefix = $this->widget->get_id();
		} else {
			$id_prefix = $this->element->get_id();
		}

		echo '<div id="cmsmasters-slider-' . esc_attr( $id_prefix ) . '" class="cmsmasters-slider">';

		call_user_func( $callback );

		echo '</div>';
	}

	/**
	 * Render each slides.
	 *
	 * @param callable $callback
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 * @since 1.0.2 Fixed checking `$callback` variable.
	 * @since 1.7.4 Fix for v8.45 swiper slider.
	 */
	public function render_slides( $callback ) {
		if ( ! is_callable( $callback ) ) {
			return;
		}

		echo '<div class="swiper cmsmasters-swiper-container">' .
		'<div class="swiper-wrapper">';

		call_user_func( $callback );

		echo '</div>' .
		'</div>';
	}

	/**
	 * Render pagination, arrows and scrollbar.
	 *
	 * @since 1.0.0
	 */
	public function render_interface() {
		$this->render_pagination();
		$this->render_arrows();
		$this->render_scrollbar();
	}

	/**
	 * Render pagination.
	 *
	 * @since 1.0.0
	 */
	public function render_pagination() {
		$show_pagination = in_array(
			$this->get_settings( 'slider_navigation' ),
			array(
				'bullets',
				'progressbar',
				'fraction',
			),
			true
		);

		if ( ! $show_pagination && ! is_admin() ) {
			return;
		}

		echo '<div class="swiper-pagination-wrap">' .
			'<div class="swiper-pagination-outer">' .
				'<div class="swiper-pagination"></div>' .
			'</div>' .
		'</div>';
	}

	/**
	 * Get settings.
	 *
	 * @param string $setting Optional. The key of the requested setting. Default is null.
	 *
	 * @since 1.0.0
	 */
	private function get_settings( $setting_key = null ) {
		if ( $this->is_skin ) {
			return $this->element->get_instance_value( $setting_key );
		}

		return $this->element->get_settings_for_display( $setting_key );
	}

	/**
	 * Render arrows.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Fix for icon switcher.
	 * @since 1.17.3 Fixed Slider arrows in RTL mode.
	 */
	public function render_arrows() {
		$show_arrows = $this->get_settings( 'slider_arrows' );

		if ( ! $show_arrows && ! is_admin() ) {
			return;
		}

		echo '<div class="swiper-buttons-wrap">' .
		'<div class="swiper-buttons-wrap-inner">';

		foreach ( array( 'prev', 'next' ) as $arrow_type ) {
			$icon = $this->get_settings( "slider_arrow_icon_{$arrow_type}" );

			if ( empty( $icon['value'] ) && $this->get_settings( 'slider_arrows_switcher' ) ) {
				$arrow_left = 'fas fa-chevron-' . ( is_rtl() ? 'right' : 'left' );
				$arrow_right = 'fas fa-chevron-' . ( is_rtl() ? 'left' : 'right' );

				$icon = array(
					'value' => 'prev' === $arrow_type ? $arrow_left : $arrow_right,
					'library' => 'fa-solid',
				);
			}

			$text = $this->get_settings( "slider_arrow_text_{$arrow_type}" );

			echo '<div class="swiper-button swiper-button-' . esc_attr( $arrow_type ) . '">' .
			'<div class="swiper-button-inner">';

			$arrow_icon_att = array( 'aria-hidden' => 'true' );

			if ( ! $text ) {
				$arrow_icon_att = array_merge(
					$arrow_icon_att,
					array( 'aria-label' => esc_attr( ucwords( $arrow_type ) ) . ' Arrow' ),
				);
			}

			Utils::render_icon( $icon, $arrow_icon_att );

			if ( $text ) {
				echo '<span class="text">' . esc_html( $text ) . '</span>';
			}

			echo '</div>' .
			'</div>';
		}

		echo '</div>' .
		'</div>';
	}

	/**
	 * Render scrollbar.
	 *
	 * @since 1.0.0
	 */
	public function render_scrollbar() {
		$show_scrollbar = $this->get_settings( 'slider_scrollbar' );

		if ( ! $show_scrollbar && ! is_admin() ) {
			return;
		}

		echo '<div class="swiper-scrollbar-wrap">' .
			'<div class="swiper-scrollbar"></div>' .
		'</div>';
	}

	/**
	 * Set current widget.
	 *
	 * @param Base_Widget $widget Addon base widget class.
	 */
	public function set_widget( Base_Widget $widget ) {
		$this->widget = $widget;
	}

}
