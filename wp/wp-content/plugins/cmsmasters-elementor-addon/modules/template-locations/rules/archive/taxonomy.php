<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules\Archive;

use CmsmastersElementor\Controls_Manager;
use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Archive;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Taxonomy extends Base_Archive {

	private $taxonomy;

	public function get_name() {
		return $this->taxonomy->name;
	}

	public function get_title() {
		return $this->taxonomy->label;
	}

	public function __construct( $data ) {
		parent::__construct();

		$this->taxonomy = $data['object'];
	}

	protected function register_controls() {
		$this->add_control(
			'taxonomy',
			array(
				'section' => 'settings',
				'type' => Controls_Manager::QUERY,
				'select2options' => array( 'dropdownCssClass' => 'elementor-locations-select2-dropdown' ),
				'autocomplete' => array(
					'object' => Query_Manager::TAX_OBJECT,
					'query' => array( 'taxonomy' => $this->taxonomy->name ),
					'by_field' => 'term_id',
				),
			)
		);
	}

	public static function get_priority() {
		return 80; // 79 / 75
	}

	public function verify_expression( $args = array() ) {
		$taxonomy = $this->get_name();

		if ( 'category' === $taxonomy ) {
			return is_category( $args );
		}

		if ( 'post_tag' === $taxonomy ) {
			return is_tag( $args );
		}

		return is_tax( $taxonomy, $args );
	}

}
