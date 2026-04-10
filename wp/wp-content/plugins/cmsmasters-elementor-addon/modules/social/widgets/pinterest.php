<?php
namespace CmsmastersElementor\Modules\Social\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Modules\Social\Traits\Social_Widget;

use Elementor\Controls_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Elementor pinterest widget.
 *
 * Elementor widget lets you easily embed and promote any public
 * pinterest on your website.
 *
 * @since 1.0.0
 */
class Pinterest extends Base_Widget {

	use Social_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve pinterest widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Pinterest', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve pinterest widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-pinterest';
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
		return array( 'pinterest' );
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
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register pinterest widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize
	 * the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'embed_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'pin' => __( 'Pin', 'cmsmasters-elementor' ),
					'board' => __( 'Board', 'cmsmasters-elementor' ),
					'profile' => __( 'Profile', 'cmsmasters-elementor' ),
				),
				'default' => 'pin',
			)
		);

		$this->add_control(
			'url_pin',
			array(
				'label' => __( 'Enter URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://www.pinterest.com/pin/99360735500167749/',
				'placeholder' => __( 'https://www.pinterest.com/pin', 'cmsmasters-elementor' ),
				'description' => __( 'You can only use a link that is located in the browser address bar (not a Pinterest short link).', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => 'pin',
				),
			)
		);

		$this->add_control(
			'url_board',
			array(
				'label' => __( 'Enter URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://www.pinterest.com/pinterest/official-news/',
				'placeholder' => __( 'https://www.pinterest.com/board', 'cmsmasters-elementor' ),
				'description' => __( 'You can only use a link that is located in the browser address bar (not a Pinterest short link).', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => 'board',
				),
			)
		);

		$this->add_control(
			'url_profile',
			array(
				'label' => __( 'Enter URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'https://www.pinterest.com/pinterest/',
				'placeholder' => __( 'https://www.pinterest.com/profile', 'cmsmasters-elementor' ),
				'description' => __( 'You can only use a link that is located in the browser address bar (not a Pinterest short link).', 'cmsmasters-elementor' ),
				'condition' => array(
					'embed_type' => 'profile',
				),
			)
		);

		$this->add_control(
			'size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'small' => __( 'Small', 'cmsmasters-elementor' ),
					'medium' => __( 'Medium', 'cmsmasters-elementor' ),
					'large' => __( 'Large', 'cmsmasters-elementor' ),
				),
				'default' => array(
					'small',
				),
				'multiple' => false,
				'condition' => array(
					'embed_type' => 'pin',
				),
			)
		);

		$this->add_control(
			'image_width',
			array(
				'label' => __( 'Image Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 80,
				),
				'range' => array(
					'px' => array(
						'min' => 60,
						'max' => 190,
						'step' => 5,
					),
				),
				'separator' => 'before',
				'condition' => array(
					'embed_type!' => 'pin',
				),
			)
		);

		$this->add_control(
			'board_width',
			array(
				'label' => __( 'Board Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 400,
				),
				'range' => array(
					'px' => array(
						'min'  => 60,
						'max'  => 730,
						'step' => 10,
					),
				),
				'condition' => array(
					'embed_type!' => 'pin',
				),
			)
		);

		$this->add_control(
			'board_height',
			array(
				'label' => __( 'Board Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 240,
				),
				'range' => array(
					'px' => array(
						'min' => 60,
						'max' => 1300,
						'step' => 10,
					),
				),
				'condition' => array(
					'embed_type!' => 'pin',
				),
			)
		);

		$this->add_control(
			'pin_alignment',
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
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-pinterest__wrapper' => 'text-align: {{VALUE}}',
				),
			)
		);
	}

	/**
	 * Render tabs widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		$settings = $this->get_settings();

		$widget_name = 'elementor-widget-' . $this->get_name();

		$this->add_render_attribute( 'wrapper', array(
			'class' => "{$widget_name}__wrapper",
			'style' => 'min-height: 1px',
		) );

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>';

		switch ( $settings['embed_type'] ) {
			case 'pin':
				$this->get_pin_html();

				break;
			case 'board':
			case 'profile':
				$this->get_board_profile_html();

				break;
		}

		echo '</div>';
	}

	/**
	 * Render pin pinterest.
	 *
	 * Used to generate the pin HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string Pin HTML.
	 */
	public function get_pin_html() {
		$settings = $this->get_settings();

		$attributes = array(
			'id' => $this->get_id(),
			'href' => esc_url( $settings['url_pin'] ),
			'data-pin-do' => 'embedPin',
			'data-pin-width' => esc_attr( $settings['size'][0] ),
			'data-pin-terse' => 'true',
		);

		$this->add_render_attribute( 'pin', $attributes );

		echo '<a ' . $this->get_render_attribute_string( 'pin' ) . '></a>';
	}

	/**
	 * Render board & profile pinterest.
	 *
	 * Used to generate the board & profile HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string Board & profile HTML.
	 */
	public function get_board_profile_html() {
		$settings = $this->get_settings();

		switch ( $settings['embed_type'] ) {
			case 'board':
				$url = $settings['url_board'];
				$class = 'embedBoard';

				break;
			case 'profile':
				$url = $settings['url_profile'];
				$class = 'embedUser';

				break;
		}

		$attributes = array(
			'id' => $this->get_id(),
			'href' => esc_url( $url ),
			'data-pin-do' => $class,
			'data-pin-scale-width' => esc_attr( $settings['image_width']['size'] ),
			'data-pin-scale-height' => esc_attr( $settings['board_height']['size'] ),
			'data-pin-board-width' => esc_attr( $settings['board_width']['size'] ),
		);

		$this->add_render_attribute( 'board-profile', $attributes );

		echo '<a ' . $this->get_render_attribute_string( 'board-profile' ) . '></a>';
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
				'field' => 'url_pin',
				'type' => esc_html__( 'Pinterest URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'url_board',
				'type' => esc_html__( 'Pinterest URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'url_profile',
				'type' => esc_html__( 'Pinterest URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
