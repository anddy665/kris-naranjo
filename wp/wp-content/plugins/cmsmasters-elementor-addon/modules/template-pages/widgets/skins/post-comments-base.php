<?php
namespace CmsmastersElementor\Modules\TemplatePages\Widgets\Skins;

use Elementor\Skin_Base as ElementorSkinBase;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Post_Comments_Base extends ElementorSkinBase {
	protected function _register_controls_actions() {
		add_action( 'elementor/element/' . $this->parent->get_name() . '/section_content/after_section_end', array( $this, 'register_controls' ) );
	}

	/**
	 * Register skin base controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.5.1 Fixed notice "_skin".
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;
	}

	/**
	 * Render skin output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function render() {
	}
}
