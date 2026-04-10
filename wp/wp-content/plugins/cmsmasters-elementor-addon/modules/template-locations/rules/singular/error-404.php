<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules\Singular;

use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Singular;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Error_404 extends Base_Singular {

	public function get_name() {
		return 'error_404';
	}

	public function get_title() {
		return __( '404 Error Page', 'cmsmasters-elementor' );
	}

	public static function get_priority() {
		return 20; // 19
	}

	public function verify_expression() {
		return is_404();
	}

}
