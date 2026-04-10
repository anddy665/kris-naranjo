<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules\Singular;

use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Singular;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Front_Page extends Base_Singular {

	public function get_name() {
		return 'front_page';
	}

	public function get_title() {
		return __( 'Front Page', 'cmsmasters-elementor' );
	}

	public static function get_priority() {
		return 30; // 29
	}

	public function verify_expression() {
		return is_front_page();
	}

}
