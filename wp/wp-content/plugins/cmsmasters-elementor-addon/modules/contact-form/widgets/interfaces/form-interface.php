<?php
namespace CmsmastersElementor\Modules\ContactForm\Widgets\Interfaces;

interface Form_Interface {

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget name.
	 */
	public function get_name();

	/**
	 * Get Contact forms.
	 *
	 * Retrieve plugin forms list.
	 *
	 * @since 1.0.0
	 *
	 * @return array Plugin forms.
	 */
	public function get_select_contact_form();

	/**
	 * Get Plugin form Name.
	 *
	 * Retrieve form plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin name.
	 */
	public function get_form_name();

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_input( $state = '' );

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_textarea();

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_select();

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_submit();

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_submit_hover();

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_radio_checkbox_desc();

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_label_form();

	/**
	 * Get Plugin admin url.
	 *
	 * Retrieve plugin admin url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin admin url.
	 */
	public function get_url();

	/**
	 * Get shortcode widget.
	 *
	 * Retrieve the widget shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget shortcode.
	 */
	public function get_shortcode();

	/**
	 * Get form id.
	 *
	 * Retrieve the form id.
	 *
	 * @since 1.1.0
	 *
	 * @return string The id form.
	 */
	public function get_form_id();

}
