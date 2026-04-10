<?php
namespace CmsmastersElementor\Modules\Testimonials\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Testimonials\Widgets\Base\Testimonial_Base;
use CmsmastersElementor\Modules\Slider\Classes\Slider;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Testimonials Slider widget.
 *
 * @since 1.1.0
 */
class Testimonials_Slider extends Testimonial_Base {

	/**
	 * Widget type.
	 *
	 * @since 1.1.0
	 *
	 * @var string Widget type.
	 */
	protected $type = 'slider';

	/**
	 * Slider module.
	 *
	 * @since 1.1.0
	 *
	 * @var object Slider module class.
	 */
	protected $slider;

	/**
	 * Initializing the Addon `testimonials slider` widget class.
	 *
	 * @since 1.1.0
	 *
	 * @throws \Exception If arguments are missing when initializing a
	 * full widget instance.
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		$this->slider = new Slider( $this );
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.1.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Testimonials Slider', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.1.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-testimonial-carousel';
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
			'carousel',
			'slider',
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.15.3 Added dependency of connecting swiper styles for widgets with swiper slider after elementor 3.26 version.
	 * @since 1.16.0 Fixed style dependencies.
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array_merge( array(
			'e-swiper',
		), parent::get_style_depends() );
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
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the widget requires.
	 *
	 * @since 1.0.0
	 * @since 1.16.0 Added dependency of connecting swiper script after elementor 3.27 version.
	 *
	 * @return array Widget script dependencies.
	 */
	public function get_script_depends() {
		return array_merge( array(
			'swiper',
			'perfect-scrollbar-js',
			'imagesloaded',
		), parent::get_script_depends() );
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
	 * @since 1.12.6
	 */
	protected function register_controls() {
		parent::register_controls();

		parent::content_rating();
	}

	/**
	 * Testimonials Item controls.
	 *
	 * @since 1.12.6
	 */
	protected function content_rating() {
		$this->start_injection(
			array(
				'at' => 'before',
				'of' => 'section_style_content',
			)
		);

		$this->start_controls_section(
			'testimonials_item_section',
			array(
				'label' => __( 'Testimonials Item', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'testimonials_item_tabs' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$this->start_controls_tab(
				"testimonials_item_{$main_key}_tab",
				array( 'label' => $label )
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BACKGROUND_GROUP,
				array( 'name' => "testimonials_item_{$main_key}_bg" )
			);

			$this->add_responsive_control(
				"testimonials_item_{$main_key}_padding",
				array(
					'label' => __( 'Padding', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "testimonials_item_{$main_key}_padding_top", '{{TOP}}{{UNIT}}' ) .
						CmsmastersUtils::prepare_css_var( "testimonials_item_{$main_key}_padding_right", '{{RIGHT}}{{UNIT}}' ) .
						CmsmastersUtils::prepare_css_var( "testimonials_item_{$main_key}_padding_bottom", '{{BOTTOM}}{{UNIT}}' ) .
						CmsmastersUtils::prepare_css_var( "testimonials_item_{$main_key}_padding_left", '{{LEFT}}{{UNIT}}' ),
					),
				)
			);

			$this->add_control(
				"testimonials_item_{$main_key}_bd_radius",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => CmsmastersUtils::prepare_css_var( "testimonials_item_{$main_key}_bd_radius", '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}' ),
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BORDER_GROUP,
				array( 'name' => "testimonials_item_{$main_key}_bd" )
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->end_injection();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.1.0
	 */
	protected function render() {
		$this->settings = $this->get_settings_for_display();

		$this->slider->render( function () {
			$this->render_items();
		} );
	}

	/**
	 * Render items.
	 *
	 * Retrieve the widget items.
	 *
	 * @since 1.1.0
	 */
	protected function render_items() {
		foreach ( $this->settings['items'] as $index => $item ) {
			$this->slider->render_slide_open();

			$this->item_settings = array(
				'index' => $index,
				'title' => $item['title'],
				'text' => $item['text'],
				'author_name' => $item['author_name'],
				'author_subtitle' => $item['author_subtitle'],
				'author_link' => $item['author_link'],
				'avatar' => $item['avatar'],
				'avatar_size' => $this->settings['avatar_size'],
				'avatar_custom_dimension' => $this->settings['avatar_custom_dimension'],
				'rating' => $item['rating'],
			);

			$this->render_item();

			$this->slider->render_slide_close();
		}
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
				'field' => 'rating_text_delimiter',
				'type' => esc_html__( 'Rating Text Delimiter', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'author_delimiter',
				'type' => esc_html__( 'Author Text Delimiter', 'cmsmasters-elementor' ),
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
			'items' => array(
				array(
					'field' => 'title',
					'type' => esc_html__( 'Title', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'text',
					'type' => esc_html__( 'Text', 'cmsmasters-elementor' ),
					'editor_type' => 'AREA',
				),
				array(
					'field' => 'author_name',
					'type' => esc_html__( 'Author Name', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'author_subtitle',
					'type' => esc_html__( 'Author Subtitle', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				'author_link' => array(
					'field' => 'url',
					'type' => esc_html__( 'Author Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
			),
		);
	}
}
