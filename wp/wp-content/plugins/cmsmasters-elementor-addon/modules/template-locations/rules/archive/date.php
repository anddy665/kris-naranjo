<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules\Archive;

use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Archive;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Date extends Base_Archive {

	public function get_name() {
		return 'date';
	}

	public function get_title() {
		return __( 'Date Archive', 'cmsmasters-elementor' );
	}

	public static function get_priority() {
		return 80; // 79
	}

	public function verify_expression() {
		return is_date();
	}

}
