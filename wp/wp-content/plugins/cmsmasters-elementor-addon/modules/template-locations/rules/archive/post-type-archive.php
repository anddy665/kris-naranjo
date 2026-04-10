<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules\Archive;

use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Archive;
use CmsmastersElementor\Modules\TemplateLocations\Rules\Archive\Taxonomy;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Post_Type_Archive extends Base_Archive {

	private $post_type;
	private $post_taxonomies;

	public function get_name() {
		return sprintf( '%s_archive', $this->post_type->name );
	}

	public function get_title() {
		/* translators: Archive template location 'post type archive' rule title. %s: Post type name */
		return sprintf( __( '%s Archive', 'cmsmasters-elementor' ), $this->post_type->label );
	}

	public function __construct( $data ) {
		$post_type = $data['post_type'];
		$taxonomies_object = get_object_taxonomies( $post_type, 'objects' );

		$this->post_type = get_post_type_object( $post_type );
		$this->post_taxonomies = wp_filter_object_list( $taxonomies_object, array(
			'public' => true,
			'show_in_nav_menus' => true,
		) );

		parent::__construct();
	}

	public static function get_priority() {
		return 80; // 80
	}

	public function verify_expression() {
		$post_type_name = $this->post_type->name;

		$expression = is_post_type_archive( $post_type_name ) || ( 'post' === $post_type_name && is_home() );

		$expression = apply_filters( "cmsmasters_elementor/documents/locations/rules/archive/post_type/{$post_type_name}/expression", $expression );

		return $expression;
	}

	public function register_child_rules() {
		if ( ! is_array( $this->post_taxonomies ) ) {
			return;
		}

		foreach ( $this->post_taxonomies as $taxonomy_object ) {
			$taxonomy = new Taxonomy( array( 'object' => $taxonomy_object ) );

			$this->register_child_rule( $taxonomy );
		}
	}

}
