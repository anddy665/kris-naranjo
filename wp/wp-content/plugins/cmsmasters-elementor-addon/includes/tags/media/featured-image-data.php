<?php
namespace CmsmastersElementor\Tags\Media;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Tags\Media\Traits\Media_Group;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters featured image data.
 *
 * Retrieves the data of the featured image.
 *
 * @since 1.0.0
 */
class Featured_Image_Data extends Tag {

	use Base_Tag, Media_Group;

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
		return 'featured-image-data';
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
		return __( 'Featured Image Data', 'cmsmasters-elementor' );
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
			'attachment_data',
			array(
				'label' => __( 'Data', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'title' => __( 'Title', 'cmsmasters-elementor' ),
					'alt' => __( 'Alt', 'cmsmasters-elementor' ),
					'caption' => __( 'Caption', 'cmsmasters-elementor' ),
					'description' => __( 'Description', 'cmsmasters-elementor' ),
					'src' => __( 'File URL', 'cmsmasters-elementor' ),
					'href' => __( 'Attachment URL', 'cmsmasters-elementor' ),
				),
				'default' => 'title',
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
		$settings = $this->get_settings();
		$attachment = $this->get_attachment();

		if ( ! $attachment ) {
			return '';
		}

		$value = '';

		switch ( $settings['attachment_data'] ) {
			case 'title':
				$value = $attachment->post_title;

				break;
			case 'alt':
				$value = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );

				break;
			case 'caption':
				$value = $attachment->post_excerpt;

				break;
			case 'description':
				$value = $attachment->post_content;

				break;
			case 'src':
				$value = $attachment->guid;

				break;
			case 'href':
				$value = get_permalink( $attachment->ID );

				break;
		}

		echo wp_kses_post( $value );
	}

	/**
	* Get attachment.
	*
	* Retrieves post data.
	*
	* @since 1.0.0
	*
	* @return array Post data.
	*/
	private function get_attachment() {
		$id = get_post_thumbnail_id();

		if ( ! $id ) {
			return false;
		}

		return get_post( $id );
	}

}
