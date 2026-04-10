<?php
namespace CmsmastersElementor\Tags\Post;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Post\Traits\Post_Group;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters terms.
 *
 * Retrieve a post terms as a list.
 *
 * @since 1.0.0
 */
class Terms extends Tag {

	use Base_Tag, Post_Group;

	/**
	* Get tag name.
	*
	* Returns the name of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag name.
	*/
	public static function tag_name() {
		return 'terms';
	}

	/**
	* Get tag title.
	*
	* Returns the title of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag title.
	*/
	public static function tag_title() {
		return __( 'Terms', 'cmsmasters-elementor' );
	}

	/**
	* Register controls.
	*
	* Registers the controls of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return void Tag controls.
	*/
	protected function register_controls() {
		$this->add_control(
			'taxonomy',
			array(
				'label' => __( 'Taxonomy', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_post_taxonomies(),
				'default' => 'post_tag',
			)
		);

		$this->add_control(
			'separator',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => ', ',
			)
		);
	}

	/**
	* Get meta keys.
	*
	* Retrieves current post public meta keys.
	*
	* @since 1.0.0
	*
	* @return array Post meta keys.
	*/
	private function get_post_taxonomies() {
		$args = array(
			'object_type' => array( get_post_type() ),
			'show_in_nav_menus' => true,
		);

		$taxonomies = get_taxonomies( $args, 'objects' );
		$options = array();

		foreach ( $taxonomies as $taxonomy => $object ) {
			$options[ $taxonomy ] = $object->label;
		}

		return $options;
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return void Tag render result.
	*/
	public function render() {
		$taxonomy = $this->get_settings( 'taxonomy' );
		$separator = $this->get_settings( 'separator' );
		$terms = get_the_term_list( get_the_ID(), $taxonomy, '', $separator );

		if ( ! $terms ) {
			echo '';
		}

		echo wp_kses_post( $terms );
	}

}
