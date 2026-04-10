<?php
namespace CmsmastersElementor\Modules\TemplateSections\Widgets;

interface Search_Interface {
	/**
	 * Get form id.
	 *
	 * Retrieve the form id.
	 *
	 * @since 1.0.0
	 *
	 * @return string The id form.
	 */
	public function get_form_search();
	public function get_form_search_template();
}
