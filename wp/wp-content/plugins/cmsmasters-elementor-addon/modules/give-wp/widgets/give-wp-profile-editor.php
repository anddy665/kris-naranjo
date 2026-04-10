<?php
namespace CmsmastersElementor\Modules\GiveWp\Widgets;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Give_WP_Profile_Editor extends Give_WP_Base {

	/**
	 * @since 1.24.0
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.6.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return 'cmsmasters-give-wp-profile-editor';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.6.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'GiveWP Profile Editor', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.6.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-profile-editor';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.6.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'profile',
			'editor',
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

	/**
	 * Get shortcode widget.
	 *
	 * Retrieve the widget shortcode.
	 *
	 * @since 1.6.0
	 *
	 * @return string The widget shortcode.
	 */
	public function get_shortcode() {
		return "[give_profile_editor]";
	}

	public function add_filter_for_editor_content() {
		return false;
	}
}
