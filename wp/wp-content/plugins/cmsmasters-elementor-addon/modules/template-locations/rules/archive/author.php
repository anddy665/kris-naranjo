<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules\Archive;

use CmsmastersElementor\Controls_Manager;
use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Archive;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Author extends Base_Archive {

	public function get_name() {
		return 'author';
	}

	public function get_title() {
		return __( 'Author Archive', 'cmsmasters-elementor' );
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
		return 80; // 79 / 75
	}

	public function verify_expression( $args = array() ) {
		return is_author( $args );
	}

}
