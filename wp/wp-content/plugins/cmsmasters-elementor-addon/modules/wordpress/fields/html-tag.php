<?php
namespace CmsmastersElementor\Modules\Wordpress\Fields;

use CmsmastersElementor\Modules\Wordpress\Fields\Base\Base_Field;
use CmsmastersElementor\Modules\Wordpress\Managers\Fields_Manager;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class HtmlTag extends Base_Field {

	public function get_name() {
		return Fields_Manager::HTML_TAG;
	}

	public function get_output_type() {
		return 'raw';
	}

	public function get_field() {
		$tag = Utils::get_if_isset( $this->parameters, 'tag', 'div' );

		if ( isset( $this->parameters['close'] ) && true === $this->parameters['close'] ) {
			return "</{$tag}>";
		}

		$content = Utils::get_if_isset( $this->parameters, 'content', false );

		return Utils::generate_html_tag( $tag, $this->get_attributes_string( array() ), $content );
	}

}
