<?php
namespace CmsmastersElementor\Modules\Wordpress\Fields;

use CmsmastersElementor\Modules\Wordpress\Fields\Base\Base_Field;
use CmsmastersElementor\Modules\Wordpress\Managers\Fields_Manager;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class RawHtml extends Base_Field {

	public function get_name() {
		return Fields_Manager::RAW_HTML;
	}

	public function get_output_type() {
		return 'raw';
	}

	public function get_field() {
		return Utils::get_if_isset( $this->parameters, 'raw_html' );
	}

}
