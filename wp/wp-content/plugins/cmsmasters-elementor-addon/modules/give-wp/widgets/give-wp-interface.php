<?php
namespace CmsmastersElementor\Modules\GiveWp\Widgets;

interface Give_Wp_Interface {

	/**
	 * Get shortcode widget.
	 *
	 * Retrieve the widget shortcode.
	 *
	 * @since 1.6.0
	 *
	 * @return string The widget shortcode.
	 */
	public function get_shortcode();

	/**
	 * add filter content for form
	 * 
	 * @since 1.6.0
	 *
	 * @return string filter.
	 */
	public function add_filter_for_editor_content();
}
