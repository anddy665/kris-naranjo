<?php
namespace CmsmastersElementor\Modules\TemplateLocations\Rules;

use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Singular;
use CmsmastersElementor\Modules\TemplateLocations\Rules\Singular\Post;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Singular extends Base_Singular {

	protected $child_rules = array(
		'front_page',
	);

	public function get_name() {
		return 'singular';
	}

	public function get_title() {
		return __( 'Singular', 'cmsmasters-elementor' );
	}

	public function get_multiple_title() {
		return __( 'All Singular', 'cmsmasters-elementor' );
	}

	public static function get_priority() {
		return 65; // 65
	}

	public function verify_expression() {
		return ( is_singular() && ! is_embed() ) || is_404();
	}

	public function register_child_rules() {
		$post_types = Utils::get_public_post_types();

		$post_types['attachment'] = get_post_type_object( 'attachment' )->label;

		foreach ( array_keys( $post_types ) as $post_type ) {
			$post = new Post( array( 'post_type' => $post_type ) );

			$this->register_child_rule( $post );
		}

		array_push( $this->child_rules, 'by_author', 'error_404' );
	}

}
