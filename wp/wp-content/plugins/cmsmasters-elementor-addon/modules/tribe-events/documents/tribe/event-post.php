<?php
namespace CmsmastersElementor\Modules\TribeEvents\Documents\Tribe;

use CmsmastersElementor\Base\Base_Document;

use Elementor\Controls_Manager;
use Elementor\Core\DocumentTypes\Post as WpPost;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Event_Post extends WpPost {

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @since 1.13.0
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['cpt'] = array( 'tribe_events' );

		return $properties;
	}

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @since 1.13.0
	 *
	 * @return string Document name.
	 */
	public function get_name() {
		return 'tribe-events-post';
	}

	/**
	 * Get document title.
	 *
	 * Retrieve the document title.
	 *
	 * @since 1.13.0
	 *
	 * @return string Document title.
	 */
	public static function get_title() {
		return __( 'Event Post', 'cmsmasters-elementor' );
	}

	/**
	 * Get editor panel categories.
	 *
	 * Retrieve the list of categories the element belongs to.
	 *
	 * @since 1.13.0
	 *
	 * @return array Editor panel categories.
	 */
	protected static function get_editor_panel_categories() {
		$editor_panel_categories = parent::get_editor_panel_categories();

		$editor_panel_categories = Utils::array_inject(
			$editor_panel_categories,
			'basic',
			array(
				Base_Document::WIDGETS_CATEGORY => array(
					'title' => __( 'CMSMasters', 'cmsmasters-elementor' ),
					'active' => true,
				),
			)
		);

		$editor_panel_categories = Utils::array_inject(
			$editor_panel_categories,
			'general',
			array(
				Base_Document::TRIBE_EVENTS_WIDGETS_CATEGORY => array(
					'title' => __( 'Tribe Events', 'cmsmasters-elementor' ),
					'active' => false,
				),
			)
		);

		$editor_panel_categories = Utils::array_inject(
			$editor_panel_categories,
			'general',
			array(
				Base_Document::SITE_WIDGETS_CATEGORY => array(
					'title' => __( 'Site', 'cmsmasters-elementor' ),
					'active' => false,
				),
			)
		);

		$editor_panel_categories = Utils::array_inject(
			$editor_panel_categories,
			Base_Document::SITE_WIDGETS_CATEGORY,
			array(
				Base_Document::TRIBE_EVENTS_SINGULAR_WIDGETS_CATEGORY => array(
					'title' => __( 'Event', 'cmsmasters-elementor' ),
					'active' => false,
				),
			)
		);

		return $editor_panel_categories;
	}

	/**
	 * Undocumented function
	 *
	 * Description.
	 *
	 * @since 1.13.0
	 */
	protected function register_controls() {
		parent::register_controls();

		if ( post_type_supports( $this->post->post_type, 'excerpt' ) ) {
			$this->update_control(
				'post_excerpt',
				array(
					'label' => __( 'Event Short Description', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::WYSIWYG,
				)
			);
		}

		if (
			current_theme_supports( 'post-thumbnails' ) &&
			post_type_supports( $this->post->post_type, 'thumbnail' )
		) {
			$this->update_control(
				'post_featured_image',
				array( 'label' => __( 'Event Image', 'cmsmasters-elementor' ) )
			);
		}
	}

}
