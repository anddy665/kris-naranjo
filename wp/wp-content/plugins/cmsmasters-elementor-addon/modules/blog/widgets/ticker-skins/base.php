<?php
namespace CmsmastersElementor\Modules\Blog\Widgets\Ticker_Skins;

use CmsmastersElementor\Modules\Blog\Widgets\Ticker as TickerWidget;

use Elementor\Skin_Base as ElementorSkinBase;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Base extends ElementorSkinBase {

	/**
	 * Parent widget.
	 *
	 * Holds the parent widget of the skin. Default value is null, no parent widget.
	 *
	 * @var TickerWidget|null
	 */
	protected $parent = null;

	protected function _register_controls_actions() {
		add_action( 'cmsmasters_elementor/element/cmsmasters-ticker/after_init_controls', array( $this, 'register_controls' ) );
	}

	/**
	 * Register controls.
	 *
	 * @since 1.5.0 Fixed notice "_skin".
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;
	}

	public function render() {
		$this->parent->init_query();

		if ( ! $this->parent->get_query()->found_posts ) {
			return;
		}

		echo '<div class="cmsmasters-ticker">';

			$this->parent->render_header();

			$this->render_posts();

		echo '</div>';

		wp_reset_postdata();
	}

	protected function render_posts() {
		echo '<div class="cmsmasters-ticker-posts">';
			$this->render_posts_loop();
		echo '</div>';
	}

	protected function render_posts_loop() {
		while ( $this->parent->get_query()->have_posts() ) {
			$this->parent->prepare_the_post();

			$this->render_post();
		}
	}

	protected function render_post() {
		$this->parent->render_post_open();

		$this->render_post_inner();

		$this->parent->render_post_close();
	}

	protected function render_post_inner() {
		$post_id = get_the_ID();
		$post_title = get_the_title( $post_id );

		if ( ! empty( $post_title ) ) {
			echo '<h4 class="cmsmasters-ticker-post-title entry-title">' .
				'<a title="' . esc_attr( $post_title ) . '" href="' . esc_url( get_permalink() ) . '">' .
					esc_html( $post_title ) .
				'</a>' .
			'</h4>';
		}

		if ( $this->parent->meta_data->is_visible() ) {
			$this->parent->meta_data->render();
		}
	}
}
