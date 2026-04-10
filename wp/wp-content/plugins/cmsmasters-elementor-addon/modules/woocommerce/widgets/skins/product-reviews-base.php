<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets\Skins;

use Elementor\Skin_Base as ElementorSkinBase;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


abstract class Product_Reviews_Base extends ElementorSkinBase {

	protected function _register_controls_actions() {
		add_action( 'elementor/element/' . $this->parent->get_name() . '/section_content/after_section_end', array( $this, 'register_controls' ) );
	}

	/**
	 * Render skin output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function render() {}
}
