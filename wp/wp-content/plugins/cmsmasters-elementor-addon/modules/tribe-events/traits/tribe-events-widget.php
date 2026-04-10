<?php
namespace CmsmastersElementor\Modules\TribeEvents\Traits;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Widget;

use Elementor\Icons_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Tribe Events widget trait.
 *
 * Provides basic widget methods for Tribe Events template document.
 *
 * @since 1.13.0
 */
trait Tribe_Events_Widget {

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.13.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return sprintf( '%1$stribe-events-%2$s', Base_Widget::WIDGET_NAME_PREFIX, self::get_widget_class_name() );
	}

	/**
	 * Get widget name prefix.
	 *
	 * Retrieve the widget name prefix.
	 *
	 * @since 1.13.0
	 *
	 * @return string The widget name prefix.
	 */
	public function get_name_prefix() {
		return Base_Widget::WIDGET_NAME_PREFIX . 'tribe-events-';
	}

	/**
	 * Get group name.
	 *
	 * @since 1.13.0
	 *
	 * @return string Group name.
	 */
	public function get_group_name() {
		return 'cmsmasters-tribe-events';
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
		$style_depends = array(
			'widget-cmsmasters-tribe-events',
		);

		if ( Icons_Manager::is_migration_allowed() ) {
			$style_depends = array_merge( $style_depends, array(
				'elementor-icons-fa-solid',
				'elementor-icons-fa-brands',
				'elementor-icons-fa-regular',
			) );
		}

		return $style_depends;
	}

	/**
	 * Get global widget keywords.
	 *
	 * Retrieve the list of global keywords the widget belongs to.
	 *
	 * @since 1.13.0
	 *
	 * @return array Widget global keywords.
	 */
	public function get_global_keywords() {
		$parent_keywords = parent::get_global_keywords();

		$global_keywords = array(
			'tribe',
			'events',
		);

		return array_merge( $parent_keywords, $global_keywords );
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the widget categories.
	 *
	 * @since 1.13.0
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( Base_Document::TRIBE_EVENTS_WIDGETS_CATEGORY );
	}

}
