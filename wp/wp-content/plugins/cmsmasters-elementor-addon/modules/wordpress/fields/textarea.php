<?php
namespace CmsmastersElementor\Modules\Wordpress\Fields;

use CmsmastersElementor\Modules\Wordpress\Fields\Base\Base_Field;
use CmsmastersElementor\Modules\Wordpress\Managers\Fields_Manager;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Textarea extends Base_Field {

	public function get_name() {
		return Fields_Manager::TEXTAREA;
	}

	public function get_field() {
		return Utils::generate_html_tag( $this->get_name(), $this->get_attributes_string(), esc_textarea( $this->value ) );
	}

}
