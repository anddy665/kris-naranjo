<?php
namespace CmsmastersElementor\Modules\Wordpress\Fields;

use CmsmastersElementor\Modules\Wordpress\Fields\Base\Base_Field;
use CmsmastersElementor\Modules\Wordpress\Managers\Fields_Manager;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Repeater extends Base_Field {

	public function get_name() {
		return Fields_Manager::REPEATER;
	}

	public function get_field( $parameters ) {
		return Utils::get_if_isset( $parameters, 'raw_html' );
	}

}
