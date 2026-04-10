<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules\Singular;

use CmsmastersElementor\Controls_Manager;
use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Singular;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class By_Author extends Base_Singular {

	public function get_name() {
		return 'by_author';
	}

	public function get_title() {
		return __( 'By Author', 'cmsmasters-elementor' );
	}

	protected function register_controls() {
		$this->add_control(
			'author_id',
			array(
				'section' => 'settings',
				'type' => Controls_Manager::QUERY,
				'select2options' => array( 'dropdownCssClass' => 'elementor-locations-select2-dropdown' ),
				'autocomplete' => array( 'object' => Query_Manager::AUTHOR_OBJECT ),
			)
		);
	}

	public static function get_priority() {
		return 65; // - / 60
	}

	public function verify_expression( $args = array() ) {
		if ( empty( $args ) ) {
			return false;
		}

		return is_singular() && in_array( (int) get_post_field( 'post_author' ), $args, true );
	}

}
