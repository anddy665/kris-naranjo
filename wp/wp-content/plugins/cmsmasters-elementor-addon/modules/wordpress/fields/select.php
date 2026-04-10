<?php
namespace CmsmastersElementor\Modules\Wordpress\Fields;

use CmsmastersElementor\Modules\Wordpress\Fields\Base\Base_Field;
use CmsmastersElementor\Modules\Wordpress\Managers\Fields_Manager;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Select extends Base_Field {

	public function get_name() {
		return Fields_Manager::SELECT;
	}

	public function get_field() {
		if ( ! isset( $this->parameters['options'] ) ) {
			$this->parameters['options'] = array();
		}

		return Utils::generate_html_tag( $this->get_name(), $this->get_attributes_string(), $this->get_options() );
	}

	public function get_options() {
		$output = '';

		foreach ( $this->parameters['options'] as $key => $label ) {
			$attributes = array( 'value' => esc_attr( $key ) );

			if ( (string) $key === (string) $this->value ) {
				$attributes['selected'] = 'selected';
			}

			$output .= Utils::generate_html_tag( 'option', $this->get_attributes_string( array() ), esc_html( $label ) );
		}

		return $output;
	}

}
