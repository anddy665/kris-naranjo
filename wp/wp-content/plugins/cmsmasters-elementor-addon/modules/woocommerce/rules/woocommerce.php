<?php
namespace CmsmastersElementor\Modules\Woocommerce\Rules;

use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Rule;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Woocommerce extends Base_Rule {

	public function get_name() {
		return 'woocommerce';
	}

	public static function get_group() {
		return 'general';
	}

	public function get_title() {
		return __( 'Entire Shop', 'cmsmasters-elementor' );
	}

	public static function get_priority() {
		return 95; // 95
	}

	public function verify_expression() {
		return is_woocommerce() || Utils::is_search_product();
	}

}
