<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules;

use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Archive;
use CmsmastersElementor\Modules\TemplateLocations\Rules\Archive\Post_Type_Archive;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Archive extends Base_Archive {

	protected $child_rules = array();

	public function get_name() {
		return 'archive';
	}

	public function get_title() {
		return __( 'Archives', 'cmsmasters-elementor' );
	}

	public function get_multiple_title() {
		return __( 'All Archives', 'cmsmasters-elementor' );
	}

	public static function get_priority() {
		return 85; // 85
	}

	public function verify_expression() {
		return is_archive() || is_home() || is_search();
	}

	public function register_child_rules() {
		$post_types = Utils::get_public_post_types();

		foreach ( array_keys( $post_types ) as $post_type ) {
			if ( ! get_post_type_archive_link( $post_type ) ) {
				continue;
			}

			$post_type_archive = new Post_Type_Archive( array( 'post_type' => $post_type ) );

			$this->register_child_rule( $post_type_archive );
		}

		array_push( $this->child_rules, 'date', 'author', 'search' );
	}

}
