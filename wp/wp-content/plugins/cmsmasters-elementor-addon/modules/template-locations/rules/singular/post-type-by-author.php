<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules\Singular;

use CmsmastersElementor\Controls_Manager;
use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Singular;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Post_Type_By_Author extends Base_Singular {

	private $post_type;

	public function get_name() {
		return $this->post_type->name . '_by_author';
	}

	public function get_title() {
		/* translators: Singular template location 'post type by author' rule title. %s: Post type name */
		return sprintf( __( '%s by Author', 'cmsmasters-elementor' ), $this->post_type->label );
	}

	public function __construct( $post_type ) {
		parent::__construct();

		$this->post_type = $post_type;
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
		return 55; // - / 50
	}

	public function verify_expression( $args = array() ) {
		if ( empty( $args ) ) {
			return false;
		}

		return is_singular( $this->post_type->name ) && in_array( (int) get_post_field( 'post_author' ), $args, true );
	}

}
