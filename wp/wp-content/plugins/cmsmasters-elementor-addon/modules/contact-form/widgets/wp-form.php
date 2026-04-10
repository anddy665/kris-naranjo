<?php
namespace CmsmastersElementor\Modules\ContactForm\Widgets;

use CmsmastersElementor\Modules\ContactForm\Widgets\Base\Base_Form;

use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class WP_Form extends Base_Form {

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return 'cmsmasters-wp-form';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'WPForms', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-wpforms';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'contact',
			'form',
			'email',
			'wp',
			'wpforms',
			'wp forms',
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
	 * Specifying caching of the widget by default.
	 *
	 * @since 1.14.0
	 */
	protected function is_dynamic_content(): bool {
		return false;
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
			parent::register_controls();
	}

		/**
	 * Get Contact forms.
	 *
	 * Retrieve WPform plugin forms list.
	 *
	 * @since 1.0.0
	 * @since 1.2.4 Fixed display of the number of records.
	 *
	 * @return array Forms.
	 */
	public function get_select_contact_form() {
		$options = array();

		$wp_form_list = get_posts(
			array(
				'post_type' => 'wpforms',
				'numberposts' => -1,
			)
		);

		if ( ! empty( $wp_form_list ) && ! is_wp_error( $wp_form_list ) ) {
			foreach ( $wp_form_list as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
		}

		return $options;
	}

	/**
	 * Get form Name.
	 *
	 * Retrieve WPform plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Form name.
	 */
	public function get_form_name() {
		return $this->get_title() . ': ';
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_input( $state = '' ) {
		return "#cmsmasters_body {{WRAPPER}} div.wpforms-container-full .wpforms-form .wpforms-field input:not([type=button]):not([type=checkbox]):not([type=file]):not([type=hidden]):not([type=image]):not([type=radio]):not([type=reset]):not([type=submit]):not([type=color]):not([type=range]){$state}";
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_select( $state = '' ) {
		return "#cmsmasters_body {{WRAPPER}} div.wpforms-container-full .wpforms-form .wpforms-field select{$state}";
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_textarea( $state = '' ) {
		return "#cmsmasters_body {{WRAPPER}} div.wpforms-container-full .wpforms-form .wpforms-field textarea{$state}";
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_submit() {
		return '#cmsmasters_body {{WRAPPER}} div.wpforms-container-full .wpforms-form .wpforms-field button[type=submit], #cmsmasters_body {{WRAPPER}} div.wpforms-container-full .wpforms-form .wpforms-field input[type=submit], #cmsmasters_body {{WRAPPER}} div.wpforms-container .wpforms-form div.wpforms-submit-container button[type=submit], #cmsmasters_body {{WRAPPER}} div.wpforms-container .wpforms-form div.wpforms-submit-container input[type=submit]';
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_submit_hover() {
		return '#cmsmasters_body {{WRAPPER}} div.wpforms-container-full .wpforms-form .wpforms-field button[type=submit]:hover, #cmsmasters_body {{WRAPPER}} div.wpforms-container-full .wpforms-form .wpforms-field input[type=submit]:hover, #cmsmasters_body {{WRAPPER}} div.wpforms-container .wpforms-form div.wpforms-submit-container button[type=submit]:hover, #cmsmasters_body {{WRAPPER}} div.wpforms-container .wpforms-form div.wpforms-submit-container input[type=submit]:hover';
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_radio_checkbox_desc() {
		return '#cmsmasters_body {{WRAPPER}} .wpforms-container ul li label.wpforms-field-label-inline';
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_label_form() {
		return '#cmsmasters_body {{WRAPPER}} div.wpforms-container-full .wpforms-form .wpforms-field > label';
	}

	/**
	 * Get admin url.
	 *
	 * Retrieve WPform plugin admin url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Admin url.
	 */
	public function get_url() {
		return esc_url( admin_url( 'admin.php?page=wpforms-builder' ) );
	}

	/**
	 * Get shortcode widget.
	 *
	 * Retrieve the widget shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget shortcode.
	 */
	public function get_shortcode() {
		$form_id = $this->get_form_id();
		return "[wpforms id=\"{$form_id}\"]";
	}

	/**
	 * Get form id.
	 *
	 * Retrieve the form id.
	 *
	 * @since 1.1.0
	 *
	 * @return string The id form.
	 */
	public function get_form_id() {
		$settings = $this->get_settings_for_display();
		$form_id = $settings['form_list'];

		return $form_id;
	}
}
