<?php
namespace CmsmastersElementor\Modules\Testimonials\Widgets;

use CmsmastersElementor\Modules\Testimonials\Widgets\Base\Testimonial_Base;

use Elementor\Controls_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Testimonial widget.
 *
 * @since 1.1.0
 */
class Testimonial extends Testimonial_Base {

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
		return __( 'Testimonial', 'cmsmasters-elementor' );
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
		return 'eicon-testimonial';
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
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
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
	 * Get the selector for the animation type 'Item by item'.
	 *
	 * @since 1.18.0
	 */
	public function get_separate_animation_selector() {
		return implode( ', ', array(
			'.cmsmasters-testimonial__rating',
			'.cmsmasters-testimonial__title',
			'.cmsmasters-testimonial__text',
			'.cmsmasters-testimonial__avatar',
			'.cmsmasters-testimonial__author-info-outer',
		) );
	}

	/**
	 * Get the class name ot the block for the text animation types 'Line by Line', 'Word by Word', 'Char by Char', etc.
	 * and names of animation types suitable for this widget.
	 *
	 * @since 1.18.0
	 */
	public function get_text_animation_class() {
		$selectors = implode( ', ', array(
			'cmsmasters-testimonial__rating',
			'cmsmasters-testimonial__title',
			'cmsmasters-testimonial__text',
			'cmsmasters-testimonial__avatar',
			'cmsmasters-testimonial__author-info-outer',
		) );

		return array( $selectors => 'sequental, random' );
	}

	/**
	 * @since 1.18.0
	 */
	protected function register_controls() {
		parent::register_controls();

		$this->start_injection(
			array(
				'of' => 'title',
				'at' => 'before',
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

		$this->add_render_attribute( '_wrapper', array(
			'data-separate-animation-selector' => $this->get_separate_animation_selector(),
			'data-text-animation-class' => $this->get_text_animation_class(),
		) );

		$this->item_settings = array(
			'index' => '',
			'title' => $this->settings['title'],
			'text' => $this->settings['text'],
			'author_name' => $this->settings['author_name'],
			'author_subtitle' => $this->settings['author_subtitle'],
			'author_link' => $this->settings['author_link'],
			'avatar' => $this->settings['avatar'],
			'avatar_size' => $this->settings['avatar_size'],
			'avatar_custom_dimension' => $this->settings['avatar_custom_dimension'],
			'rating' => $this->settings['rating'],
		);

		$this->render_item();
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
}
