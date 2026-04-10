<?php
namespace CmsmastersElementor\Tags\Archive;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Archive\Traits\Archive_Group;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters meta.
 *
 * Retrieves metadata for a term or meta field for a given user.
 *
 * @since 1.0.0
 */
class Meta extends Tag {

	use Base_Tag, Archive_Group;

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
		return 'meta';
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
		return __( 'Meta', 'cmsmasters-elementor' );
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
				'label' => __( 'Meta Key', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
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
		$key = $this->get_settings( 'key' );

		if ( empty( $key ) ) {
			return;
		}

		$object_id = get_queried_object_id();
		$value = '';

		if ( is_category() || is_tax() ) {
			$value = get_term_meta( $object_id, $key, true );
		} elseif ( is_author() ) {
			$value = get_user_meta( $object_id, $key, true );
		}

		echo wp_kses( $value, 'post' );
	}

}
