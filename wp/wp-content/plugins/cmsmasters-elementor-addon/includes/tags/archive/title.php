<?php
namespace CmsmastersElementor\Tags\Archive;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Archive\Traits\Archive_Group;
use CmsmastersElementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * CMSMasters title.
 *
 * Retrieve the page title.
 *
 * @since 1.0.0
 */
class Title extends Tag {

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
		return 'title';
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
		return __( 'Title', 'cmsmasters-elementor' );
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
			'context',
			array(
				'label' => __( 'Include Context', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
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
		$title = Utils::get_page_title( 'yes' === $this->get_settings( 'context' ) );

		echo wp_kses_post( $title );
	}

}
