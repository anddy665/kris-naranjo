<?php
namespace CmsmastersElementor\Tags\Post;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Post\Traits\Post_Group;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters custom field.
 *
 * Retrieve meta field values for a post.
 *
 * @since 1.0.0
 */
class Custom_Field extends Tag {

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
		return 'custom-field';
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
		return __( 'Custom Field', 'cmsmasters-elementor' );
	}

	/**
	* Get categories.
	*
	* Returns an array of dynamic tag categories.
	*
	* @since 1.0.0
	*
	* @return array Tag categories.
	*/
	public function get_categories() {
		return array(
			TagsModule::TEXT_CATEGORY,
			TagsModule::URL_CATEGORY,
			TagsModule::POST_META_CATEGORY,
			TagsModule::COLOR_CATEGORY,
		);
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
			'key',
			array(
				'label' => __( 'Key', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_meta_keys(),
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
	private function get_meta_keys() {
		$custom_fields = get_post_custom_keys();
		$options = array( '' => __( 'Select', 'cmsmasters-elementor' ) );

		if ( empty( $custom_fields ) ) {
			return $options;
		}

		foreach ( $custom_fields as $custom_field ) {
			if ( '_' === substr( $custom_field, 0, 1 ) ) {
				continue;
			}

			$options[ $custom_field ] = ucwords( str_replace( '_', ' ', $custom_field ) );
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
		$field = $this->get_settings( 'key' );

		if ( empty( $field ) ) {
			return;
		}

		$post_meta = get_post_meta( get_the_ID(), $field, true );

		if ( ! is_string( $post_meta ) ) {
			return;
		}

		echo wp_kses_post( $post_meta );
	}

}
