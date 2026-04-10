<?php
namespace CmsmastersElementor\Modules\TribeEvents\Widgets;

use CmsmastersElementor\Modules\TemplatePages\Widgets\Post_Featured_Image;
use CmsmastersElementor\Modules\TribeEvents\Traits\Tribe_Events_Singular_Widget;

use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon Event Image widget.
 *
 * Addon widget that displays image of current event.
 *
 * @since 1.13.0
 */
class Event_Image extends Post_Featured_Image {

	use Tribe_Events_Singular_Widget;

	/**
	 * Get group name.
	 *
	 * @since 1.13.0
	 *
	 * @return string Group name.
	 */
	public function get_group_name() {
		return 'cmsmasters-post-featured-image';
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.16.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'widget-cmsmasters-post-featured-image',
			'widget-cmsmasters-tribe-events',
		);
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.13.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Event Image', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.13.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-event-image';
	}

	/**
	 * Get tag names.
	 *
	 * Retrieve widget dynamic controls tag names.
	 *
	 * @since 1.13.0
	 *
	 * @return array Widget dynamic controls tag names.
	 */
	protected function get_tag_names() {
		return array(
			'image_id' => 'cmsmasters-tribe-events-event-image-id',
			'image_url' => 'cmsmasters-tribe-events-event-image-url',
			'post_url' => 'cmsmasters-tribe-events-event-url',
		);
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}
}
