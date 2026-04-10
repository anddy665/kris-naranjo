<?php
namespace CmsmastersElementor\Modules\TribeEvents\Widgets;

use CmsmastersElementor\Modules\TemplatePages\Widgets\Post_Content;
use CmsmastersElementor\Modules\TribeEvents\Traits\Tribe_Events_Singular_Widget;
use CmsmastersElementor\Utils;

use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Event Content widget.
 *
 * Addon widget that displays the content of current Event.
 *
 * @since 1.13.0
 */
class Event_Content extends Post_Content {

	use Tribe_Events_Singular_Widget;

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
		return __( 'Event Content', 'cmsmasters-elementor' );
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
		return 'cmsicon-event-content';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.13.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		$keywords = parent::get_keywords();

		$keywords = Utils::unset_items_by_value( 'post', $keywords );

		return $keywords;
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
