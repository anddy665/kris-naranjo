<?php
namespace CmsmastersElementor\Classes;

use Elementor\Core\DynamicTags\Dynamic_CSS;
use Elementor\Core\Files\CSS\Post;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Loop Dynamic CSS.
 *
 * Class for post dynamic css.
 *
 * @since 1.11.4
 */
class Loop_Dynamic_CSS extends Dynamic_CSS {

	private $post_id_for_data;

	public function __construct( $post_id, $post_id_for_data ) {

		$this->post_id_for_data = $post_id_for_data;

		$post_css_file = Post::create( $post_id_for_data );

		parent::__construct( $post_id, $post_css_file );
	}

	public function get_post_id_for_data() {
		return $this->post_id_for_data;
	}

}
