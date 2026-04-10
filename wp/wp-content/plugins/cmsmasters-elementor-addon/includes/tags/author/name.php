<?php
namespace CmsmastersElementor\Tags\Author;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Author\Traits\Author_Group;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters name.
 *
 * Retrieves the name of the author of the current post.
 *
 * @since 1.0.0
 */
class Name extends Tag {

	use Base_Tag, Author_Group;

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
		return 'name';
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
		return __( 'Name', 'cmsmasters-elementor' );
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
			'field',
			array(
				'label' => __( 'Field', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'display_name',
				'options' => array(
					'display_name' => __( 'Name', 'cmsmasters-elementor' ),
					'nickname' => __( 'Nickname', 'cmsmasters-elementor' ),
				),
			)
		);
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
		$field = $this->get_settings( 'field' );

		if ( empty( $field ) ) {
			return;
		}

		$name = get_the_author_meta( $field );

		echo wp_kses_post( $name );
	}

}
