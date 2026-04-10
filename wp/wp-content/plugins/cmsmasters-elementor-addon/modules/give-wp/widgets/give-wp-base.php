<?php

namespace CmsmastersElementor\Modules\GiveWp\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Plugin;
use Give\DonationForms\Properties\FormSettings;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


abstract class Give_WP_Base extends Base_Widget implements Give_Wp_Interface {

	/**
	 * Get group name.
	 *
	 * @since 1.6.5
	 *
	 * @return string Group name.
	 */
	public function get_group_name() {
		return 'cmsmasters-give-wp';
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
			'widget-cmsmasters-give-wp',
		);
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
			'give',
			'givewp',
			'donation',
			'donor',
		);
	}

	 /**
	 * Error Section.
	 *
	 * If form list empty.
	 *
	 * @since 1.6.0
	 */
	public function error_section() {
		$this->start_controls_section(
			'section_error',
			array(
				'label' => __( 'Warning', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'warning_section',
			array(
				'raw' => '<strong>GiveWP</strong>' . __( ' You do not have any forms created. ', 'cmsmasters-elementor' ) . '<a href="' . $this->get_url( 'give_forms' ) . '" target="_blank">' . __( 'Go to the form creation page', 'cmsmasters-elementor' ) . '</a>',
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type' => 'ui',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get give wp forms.
	 *
	 * Retrieve GiveWP plugin forms list.
	 *
	 * @since 1.6.0
	 *
	 * @return array Plugin forms.
	 */
	public function get_select_form() {
		$options = array();

		$give_forms = get_posts(
			array(
				'post_type' => 'give_forms',
				'numberposts' => -1,
			)
		);

		if ( ! empty( $give_forms ) && ! is_wp_error( $give_forms ) ) {
			foreach ( $give_forms as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
		}

		return $options;
	}

	/**
	 * Get GiveWP donors.
	 *
	 * Retrieve GiveWP plugin donors list.
	 *
	 * @since 1.6.0
	 *
	 * @return array Plugin donors.
	 */
	public function get_select_donor() {
		$donor_query = new \Give_Donors_Query();
		$donors_query = $donor_query->get_donors();

		$options = array();

		if ( ! empty( $donors_query ) && isset( $donors_query ) && null == ! $donors_query ) {
			foreach ( $donors_query as $donar ) {
				$options[ $donar->id ] = $donar->name;
			}
		}

		return $options;
	}

	/**
	 * Get GiveWP taxonomies.
	 *
	 * Retrieve GiveWP plugin taxonomies list.
	 *
	 * @since 1.6.0
	 *
	 * @return array Plugin taxonomies.
	 */
	public function get_select_taxonomy( $taxonomy_name ) {

		$taxonomies = get_categories( array(
			'taxonomy' => $taxonomy_name,
			'type' => 'give_forms',
		) );

		$options = array();

		if ( ! empty( $taxonomies ) && isset( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$options[ $taxonomy->term_id ] = $taxonomy->name;
			}
		}

		return $options;
	}

	/**
	 * Get string ids.
	 *
	 * Retrieve the string ids.
	 *
	 * @since 1.6.0
	 *
	 * @return string The ids.
	 */
	public function get_string_ids( $control_name ) {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings[ $control_name ] ) ) {
			return false;
		}

		$ids_array = $settings[ $control_name ];

		$ids = implode( ',', $ids_array );

		return $ids;
	}

	/**
	 * Get theme colors.
	 *
	 * @since 1.6.0
	 *
	 * @return string Get theme Colors.
	 */
	public function get_theme_color( $color ) {
		$system_colors_rows = $this->get_kit_options();

		foreach ( $system_colors_rows['system_colors'] as $system_color ) {
			if ( $color === $system_color['_id'] ) {
				$color = $system_color['color'];
			}
		}

		return $color;
	}

	/**
	 * Change rgba theme color to hex.
	 *
	 * @since 1.6.0
	 *
	 * @return string Hex color.
	 */
	public function rgba_to_hex( $color ) {
		$old_color = $this->get_theme_color( $color );

		if ( strpos( $old_color, '#' ) === 0 ) {
			if ( 7 < strlen( $old_color ) ) {
				$old_color = mb_substr( $old_color, 0, 7 );
			}

			return $old_color;
		}

		preg_match( '/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i', $old_color, $new_color );

		return sprintf( '#%02x%02x%02x', $new_color[1], $new_color[2], $new_color[3] );
	}

	/**
	 * Get kit options.
	 *
	 * @since 1.6.0
	 *
	 * @return array Kit options.
	 */
	public function get_kit_options() {
		$active_kit = $this->get_active_kit();

		return get_post_meta( $active_kit, '_elementor_page_settings', true );
	}

	/**
	 * Get Elementor active kit ID.
	 *
	 * @since 1.6.0
	 *
	 * @return string Elementor active kit ID.
	 */
	public function get_active_kit() {
		$active_kit = get_option( 'elementor_active_kit', '' );

		if ( ! empty( $active_kit ) && did_action( 'wpml_loaded' ) ) {
			$post_type = get_post_type( $active_kit );

			$active_kit = apply_filters( 'wpml_object_id', $active_kit, $post_type, true );
		}

		return $active_kit;
	}

	/**
	 * Get Plugin admin url.
	 *
	 * Retrieve GiveWP plugin admin url.
	 *
	 * @since 1.6.0
	 *
	 * @return string Plugin admin url.
	 */
	public function get_url( $page ) {
		return esc_url( admin_url( "edit.php?post_type={$page}" ) );
	}

	/**
	 * Render widget.
	 *
	 * Outputs the widget HTML code on the frontend.
	 *
	 * @since 1.6.0
	 */
	public function render() {
		$is_editor = Plugin::elementor()->editor->is_edit_mode();
		$name = $this->get_name();
		$give_wp_settings = get_option( 'give_settings' );

		if ( empty( $give_wp_settings ) || ! is_array( $give_wp_settings ) ) {
			return;
		}

		$this->add_filter_for_editor_content();

		if ( $is_editor && 'cmsmasters-give-wp-donor-wall' === $name ) {
			if ( wp_doing_ajax() ) {
				$turn_off_donor_ajax = 0;

				add_filter( 'wp_doing_ajax', array( $this, $turn_off_donor_ajax ) );
			}
		}

		$base_widget_class = 'cmsmasters-give-wp-widget';

		if ( 'cmsmasters-give-wp-form-grid' === $name ) {
			$show_featured_image = $this->get_settings_for_display( 'show_featured_image' );

			if ( 'yes' !== $show_featured_image || ( 'enabled' !== $give_wp_settings['form_featured_img'] && isset( $give_wp_settings['form_featured_img'] ) ) ) {
				$base_widget_class = 'cmsmasters-give-wp-widget cmsmasters-give-wp-form-grid-no-image';
			}
		}

		if ( 'cmsmasters-give-wp-multi-form-goal' === $name ) {
			$is_heding = $this->get_settings_for_display( 'heading' );
			$is_text = $this->get_settings_for_display( 'summary' );

			if ( '' === $is_heding && '' === $is_text ) {
				$base_widget_class = 'cmsmasters-give-wp-widget cmsmasters-give-wp-multi-goal-no-content';
			}
		}

		try {
			$shortcode = $this->get_shortcode();
			if ( empty( $shortcode ) ) {
				throw new \Exception( 'Shortcode was not generated' );
			}

			if ( strpos( $shortcode, '[give_form_grid' ) !== false ) {
				$decoded_shortcode_data = json_decode( $shortcode, true );

				if ( json_last_error() !== JSON_ERROR_NONE ) {
					throw new \Exception( 'The shortcode JSON data is invalid: ' . json_last_error_msg() );
				}

				$form_settings = FormSettings::fromJson( json_encode( $decoded_shortcode_data ) );
				if ( ! $form_settings ) {
					throw new \Exception( '<p>Failed to process form settings through FormSettings.</p>' );
				}
			}

			$shortcode_output = do_shortcode( shortcode_unautop( $shortcode ) );
		} catch ( \Exception $e ) {
			$shortcode_output = do_shortcode( shortcode_unautop( $shortcode ) );
		}

		echo "<div class='" . esc_attr( $base_widget_class ) . "'>{$shortcode_output}</div>";

		if ( $is_editor && 'cmsmasters-give-wp-donor-wall' === $name ) {
			if ( wp_doing_ajax() ) {
				add_filter( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX );
			}
		}
	}


	/**
	 * Render shortcode widget as plain content.
	 *
	 * Override the default behavior by printing the shortcode instead of rendering it.
	 *
	 * @since 1.6.0
	 * @access public
	 */
	public function render_plain_content() {}

	/**
	 * Render shortcode widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.6.0
	 * @access protected
	 */
	protected function content_template() {}
}