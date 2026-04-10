<?php
namespace CmsmastersElementor\Modules\Woocommerce\Rules\Archive;

use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Archive;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Product_Search extends Base_Archive {

	public function get_name() {
		return 'product_search';
	}

	public function get_title() {
		return __( 'Products Search Results', 'cmsmasters-elementor' );
	}

	public static function get_priority() {
		return 55; // 55 / 40
	}

	public static function get_args_priority() {
		return 40; // 40
	}

	public function verify_expression() {
		return Utils::is_search_product();
	}

}
