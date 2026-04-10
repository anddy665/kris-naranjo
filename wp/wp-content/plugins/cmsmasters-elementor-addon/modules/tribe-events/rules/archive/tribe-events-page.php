<?php
namespace CmsmastersElementor\Modules\TribeEvents\Rules\Archive;

use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Archive;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Tribe_Events_Page extends Base_Archive {

	public function get_name() {
		return 'tribe_events_page';
	}

	public function get_title() {
		return __( 'Tribe Events Page', 'cmsmasters-elementor' );
	}

	public static function get_priority() {
		return 55; // 55 / 40
	}

	public static function get_args_priority() {
		return 40; // 40
	}

	public function verify_expression() {
		return is_shop();
	}

}
