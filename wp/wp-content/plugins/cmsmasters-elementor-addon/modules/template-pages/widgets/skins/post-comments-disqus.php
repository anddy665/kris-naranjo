<?php
namespace CmsmastersElementor\Modules\TemplatePages\Widgets\Skins;

class Post_Comments_Disqus extends Post_Comments_Base {
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
		return 'disqus';
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
		return __( 'Disqus', 'cmsmasters-elementor' );
	}

	/**
	 * Render skin output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		comments_template();
	}
}
