<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules\Archive;

use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Archive;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Search extends Base_Archive {

	public function get_name() {
		return 'search';
	}

	public function get_title() {
		return __( 'Search Results', 'cmsmasters-elementor' );
	}

	public static function get_priority() {
		return 80; // 79
	}

	public function verify_expression() {
		return is_search();
	}

}
