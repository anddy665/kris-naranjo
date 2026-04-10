<?php
namespace CmsmastersElementor\Modules\WebFonts\Services;

use CmsmastersElementor\Modules\WebFonts\Services\Base\Base_Service;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Transfonter extends Base_Service {

	protected $stylesheet_file = 'stylesheet.css';

	protected $data_file = 'demo.html';

	public static function get_type() {
		return __( 'Transfonter', 'cmsmasters-elementor' );
	}

	public static function get_link() {
		return 'https://transfonter.org/';
	}

}
