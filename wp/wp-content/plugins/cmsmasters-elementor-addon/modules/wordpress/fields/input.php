<?php
namespace CmsmastersElementor\Modules\Wordpress\Fields;

use CmsmastersElementor\Modules\Wordpress\Fields\Base\Base_Field;
use CmsmastersElementor\Modules\Wordpress\Managers\Fields_Manager;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Input extends Base_Field {

	public function get_name() {
		return Fields_Manager::INPUT;
	}

	public function get_field() {
		if ( ! isset( $this->parameters['type'] ) ) {
			$this->parameters['type'] = 'text';
		}

		$attributes = array( 'id', 'type' );

		if ( $this->value ) {
			$attributes[] = 'value';

			$this->parameters['value'] = $this->value;
		}

		return Utils::generate_html_tag( $this->get_name(), $this->get_attributes_string( $attributes ) );
	}

}
