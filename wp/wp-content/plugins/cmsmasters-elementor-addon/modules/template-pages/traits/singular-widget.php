<?php
namespace CmsmastersElementor\Modules\TemplatePages\Traits;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Widget;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Singular widget trait.
 *
 * Provides basic widget methods for singular template document.
 *
 * @since 1.0.0
 */
trait Singular_Widget {

	/**
	 * Get widget name prefix.
	 *
	 * Retrieve the widget name prefix.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget name prefix.
	 */
	public function get_name_prefix() {
		return Base_Widget::WIDGET_NAME_PREFIX . 'post-';
	}

	/**
	 * Get global widget keywords.
	 *
	 * Retrieve the list of global keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget global keywords.
	 */
	public function get_global_keywords() {
		$parent_keywords = parent::get_global_keywords();

		$global_keywords = array( 'post' );

		return array_merge( $parent_keywords, $global_keywords );
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the widget categories.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( Base_Document::SINGULAR_WIDGETS_CATEGORY );
	}

}
