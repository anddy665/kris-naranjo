<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules\Singular;

use CmsmastersElementor\Controls_Manager;
use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Singular;
use CmsmastersElementor\Modules\TemplateLocations\Rules\Singular;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Post extends Base_Singular {

	private $post_type;
	private $post_taxonomies;

	public function get_name() {
		return $this->post_type->name;
	}

	public function get_title() {
		return $this->post_type->labels->singular_name;
	}

	public function get_multiple_title() {
		return $this->post_type->label;
	}

	public function __construct( $data ) {
		$taxonomies = get_object_taxonomies( $data['post_type'], 'objects' );

		$this->post_type = get_post_type_object( $data['post_type'] );
		$this->post_taxonomies = wp_filter_object_list( $taxonomies, array(
			'public' => true,
			'show_in_nav_menus' => true,
		) );

		parent::__construct();
	}

	protected function register_controls() {
		$this->add_control(
			'post_id',
			array(
				'section' => 'settings',
				'type' => Controls_Manager::QUERY,
				'select2options' => array( 'dropdownCssClass' => 'elementor-locations-select2-dropdown' ),
				'autocomplete' => array(
					'object' => Query_Manager::POST_OBJECT,
					'query' => array( 'post_type' => $this->get_name() ),
				),
			)
		);
	}

	public static function get_priority() {
		return 55; // 55 / 40
	}

	public static function get_args_priority() {
		return 40; // 40
	}

	public function verify_expression( $args = array() ) {
		if ( ! empty( $args ) ) {
			return is_singular() && in_array( get_queried_object_id(), $args, true );
		}

		return is_singular( $this->post_type->name );
	}

	public function register_child_rules() {
		if ( is_array( $this->post_taxonomies ) ) {
			foreach ( $this->post_taxonomies as $object ) {
				$in_taxonomy = new Singular\In_Taxonomy( array( 'object' => $object ) );

				$this->register_child_rule( $in_taxonomy );
			}
		}

		$by_author = new Singular\Post_Type_By_Author( $this->post_type );

		$this->register_child_rule( $by_author );
	}

}
