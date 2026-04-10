<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules\Singular;

use CmsmastersElementor\Controls_Manager;
use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Singular;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class In_Taxonomy extends Base_Singular {

	private $taxonomy;

	public function get_name() {
		return 'in_' . $this->taxonomy->name;
	}

	public function get_title() {
		/* translators: Singular template location 'in taxonomy' rule title. %s: Taxonomy name */
		return sprintf( __( 'In %s', 'cmsmasters-elementor' ), $this->taxonomy->labels->singular_name );
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
		return 55; // 54 / 45
	}

	public static function get_args_priority() {
		return 45; // 45
	}

	public function verify_expression( $args = array() ) {
		return is_singular() && has_term( $args, $this->taxonomy->name );
	}

}
