<?php
namespace CmsmastersElementor\Modules\TemplatePages\Widgets\Skins;

use CmsmastersElementor\Modules\Social\Classes\Facebook_SDK_Manager;
use CmsmastersElementor\Modules\Social\Module as SocialModule;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Post_Comments_Facebook extends Post_Comments_Base {

	private $index = 0;

	/**
	 * Get skin id.
	 *
	 * Retrieve skin id.
	 *
	 * @since 1.0.0
	 *
	 * @return string Skin id.
	 */
	public function get_id() {
		return 'facebook';
	}

	/**
	 * Get skin title.
	 *
	 * Retrieve skin title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Skin title.
	 */
	public function get_title() {
		return esc_html__( 'Facebook', 'cmsmasters-elementor' );
	}

	/**
	 * Register skin controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.5.1 Fixed notice "_skin".
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->parent->start_injection( array(
			'of' => '_skin',
		) );

		$this->update_control(
			'app_id',
			array(
				'condition' => array( '_skin' => 'facebook' ),
			)
		);

		Facebook_SDK_Manager::add_app_id_control( $this );

		$this->add_control(
			'comments_number',
			array(
				'label' => __( 'Comment Count', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 5,
				'max' => 100,
				'default' => '10',
				'description' => __( 'Minimum number of comments: 5', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label' => __( 'Order By', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'social' => __( 'Social', 'cmsmasters-elementor' ),
					'reverse_time' => __( 'Reverse Time', 'cmsmasters-elementor' ),
					'time' => __( 'Time', 'cmsmasters-elementor' ),
				),
				'default' => 'social',
			)
		);

		$this->add_control(
			'url_type',
			array(
				'label' => __( 'Target URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					SocialModule::URL_TYPE_CURRENT_PAGE => __( 'Current Page', 'cmsmasters-elementor' ),
					SocialModule::URL_TYPE_CUSTOM => __( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => SocialModule::URL_TYPE_CURRENT_PAGE,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'url_format',
			array(
				'label' => __( 'URL Format', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					SocialModule::URL_FORMAT_PLAIN => __( 'Plain Permalink', 'cmsmasters-elementor' ),
					SocialModule::URL_FORMAT_PRETTY => __( 'Pretty Permalink', 'cmsmasters-elementor' ),
				),
				'default' => SocialModule::URL_FORMAT_PLAIN,
				'condition' => array(
					'url_type' => SocialModule::URL_TYPE_CURRENT_PAGE,
				),
			)
		);

		$this->add_control(
			'url',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'label_block' => true,
				'condition' => array(
					'url_type' => SocialModule::URL_TYPE_CUSTOM,
				),
			)
		);

		$this->parent->end_injection();
	}

	/**
	 * Render skin output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		$settings = $this->parent->get_settings();

		if ( SocialModule::URL_TYPE_CURRENT_PAGE === $settings['facebook_url_type'] ) {
			$permalink = Facebook_SDK_Manager::get_permalink( $settings );
		} else {
			if ( ! filter_var( $settings['facebook_url'], FILTER_VALIDATE_URL ) ) {
				echo $this->get_title() . ': ' . esc_html__( 'Please enter a valid URL', 'cmsmasters-elementor' ); // XSS ok.

				return;
			}

			$permalink = esc_url( $settings['facebook_url'] );
		}

		$attributes = array(
			'class' => 'cmsmasters-widget-comments__facebook fb-comments',
			'data-href' => $permalink,
			'data-numposts' => esc_attr( $settings['facebook_comments_number'] ),
			'data-order-by' => esc_attr( $settings['facebook_order_by'] ),
			// The style prevents the `widget.handleEmptyWidget` to set it as an empty widget
			'style' => 'min-height: 1px',
		);

		$this->parent->add_render_attribute( 'embed_div', $attributes );

		echo '<div ' . $this->parent->get_render_attribute_string( 'embed_div' ) . '></div>'; // XSS ok.
	}
}
