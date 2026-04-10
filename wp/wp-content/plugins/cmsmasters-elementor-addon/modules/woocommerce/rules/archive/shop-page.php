<?php
namespace CmsmastersElementor\Modules\Woocommerce\Rules\Archive;

use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Archive;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Shop_Page extends Base_Archive {

	public function get_name() {
		return 'shop_page';
	}

	public function get_title() {
		return __( 'Shop Page', 'cmsmasters-elementor' );
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
