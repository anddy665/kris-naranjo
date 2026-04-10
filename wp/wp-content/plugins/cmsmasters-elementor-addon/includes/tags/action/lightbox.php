<?php
namespace CmsmastersElementor\Tags\Action;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Tags\Action\Traits\Action_Group;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Embed;
use Elementor\Modules\DynamicTags\Module as TagsModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Lightbox extends Tag {

	use Base_Tag, Action_Group;

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
		return 'lightbox';
	}

	/**
	* Get title.
	*
	* Returns the title of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag title.
	*/
	public function get_title() {
		return __( 'Lightbox', 'cmsmasters-elementor' );
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
		return array( TagsModule::URL_CATEGORY );
	}

	/**
	* Register advanced section controls.
	*
	* Registers the advanced section controls of the dynamic tag.
	*
	* Keep Empty to avoid default dynamic tag advanced section.
	*
	* @since 1.0.0
	*/
	protected function register_advanced_section() {}

	/**
	* Register controls.
	*
	* Registers the controls of the dynamic tag.
	*
	* @since 1.0.0
	*/
	public function register_controls() {
		$this->add_control(
			'type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'video' => array(
						'title' => __( 'Video', 'cmsmasters-elementor' ),
						'icon' => 'eicon-video-camera',
					),
					'image' => array(
						'title' => __( 'Image', 'cmsmasters-elementor' ),
						'icon' => 'eicon-image-bold',
					),
				),
			)
		);

		$this->add_control(
			'image',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => array( 'type' => 'image' ),
			)
		);

		$this->add_control(
			'video_url',
			array(
				'label' => __( 'Video URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'condition' => array( 'type' => 'video' ),
			)
		);
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.0.0
	*/
	public function render() {
		$settings = $this->get_settings();

		if ( ! $settings['type'] ) {
			return;
		}

		if ( 'image' === $settings['type'] && $settings['image'] ) {
			$output = $this->get_image_action_settings( $settings['image'] );
		} elseif ( 'video' === $settings['type'] && $settings['video_url'] ) {
			$output = $this->get_video_action_settings( $settings['video_url'] );
		} else {
			$output = array();
		}

		if ( ! $output ) {
			return;
		}

		echo Plugin::elementor()->frontend->create_action_hash( 'lightbox', $output );
	}

	private function get_image_action_settings( $image ) {
		$image_settings = array(
			'url' => $image['url'],
			'type' => 'image',
		);

		$image_id = $image['id'];

		if ( $image_id ) {
			$lightbox_image_attributes = Plugin::elementor()->images_manager->get_lightbox_image_attributes( $image_id );

			$image_settings = array_merge( $image_settings, $lightbox_image_attributes );
		}

		return $image_settings;
	}

	private function get_video_action_settings( $video_url ) {
		$video_properties = Embed::get_video_properties( $video_url );

		$video_type = 'hosted';

		if ( $video_properties ) {
			$video_type = $video_properties['provider'];
			$video_url = Embed::get_embed_url( $video_url );
		}

		if ( null === $video_url ) {
			return '';
		}

		return array(
			'type' => 'video',
			'videoType' => $video_type,
			'url' => $video_url,
		);
	}

}
