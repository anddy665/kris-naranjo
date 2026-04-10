<?php
namespace CmsmastersElementor\Modules\Wpml;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Plugin;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * WPML module.
 *
 * @since 1.3.3
 */
class Module extends Base_Module {

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.3.3
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'wpml';
	}

	/**
	 * Module activation.
	 *
	 * Check if module is active.
	 *
	 * @since 1.3.3
	 *
	 * @return bool
	 */
	public static function is_active() {
		return did_action( 'wpml_loaded' );
	}

	/**
	 * Init filters.
	 *
	 * Initialize module filters.
	 *
	 * @since 1.3.3
	 */
	protected function init_filters() {
		add_filter( 'wpml_elementor_widgets_to_translate', array( $this, 'get_translatable_widgets' ) );

		add_filter( 'cmsmasters_translated_template_id', array( $this, 'get_translated_template_id' ) );

		add_filter( 'cmsmasters_translated_location_args', array( $this, 'translate_location_args' ) );

		// Remove locations for translated templates to prevent duplication
		add_filter( 'cmsmasters_elementor/documents/before_save_settings', array( $this, 'filter_translated_template_locations' ), 5 );
	}

	/**
	 * Init actions.
	 *
	 * Initialize module actions.
	 *
	 * @since 1.19.4
	 */
	protected function init_actions() {
		// Delete locations meta for translated templates after document save
		add_action( 'elementor/document/after_save', array( $this, 'delete_translated_template_locations' ), 10, 2 );

		// Hide locations section for translated templates
		add_action( 'cmsmasters_elementor/documents/header_footer/register_controls', array( $this, 'hide_locations_for_translated_templates' ), 20 );
		add_action( 'cmsmasters_elementor/documents/archive_singular/register_controls', array( $this, 'hide_locations_for_translated_templates' ), 20 );

		// Handle default language change - migrate locations
		add_action( 'update_option_icl_sitepress_settings', array( $this, 'on_wpml_settings_update' ), 10, 2 );

		// Switch to all languages for template locations queries to prevent storage corruption
		add_action( 'cmsmasters_elementor/locations/before_get_templates', array( $this, 'switch_to_all_languages' ) );
		add_action( 'cmsmasters_elementor/locations/after_get_templates', array( $this, 'restore_current_language' ) );


		// Fix taxonomy term after template language change in WP editor
		add_action( 'save_post_elementor_library', array( $this, 'fix_template_after_language_change' ), 100, 2 );
	}

	/**
	 * Get translatable widgets.
	 *
	 * @since 1.3.3
	 *
	 * @param array $widgets Translatable widgets.
	 *
	 * @return array Filtered translatable widgets.
	 */
	public function get_translatable_widgets( $widgets ) {
		foreach ( Plugin::elementor()->widgets_manager->get_widget_types() as $widget_key => $widget_obj ) {
			if ( false === strpos( $widget_key, 'cmsmasters' ) ) {
				continue;
			}

			$fields = $widget_obj::get_wpml_fields();
			$fields_in_item = $widget_obj::get_wpml_fields_in_item();

			if ( empty( $fields ) && empty( $fields_in_item ) ) {
				continue;
			}

			if ( ! empty( $fields ) ) {
				foreach ( $fields as $index => $field ) {
					$fields[ $index ]['type'] = $field['type'] . ' (' . $widget_obj->get_title() . ')';
				}
			}

			if ( ! empty( $fields_in_item ) ) {
				foreach ( $fields_in_item as $item_key => $item_fields ) {
					foreach ( $item_fields as $item_field_index => $item_field ) {
						$fields_in_item[ $item_key ][ $item_field_index ]['type'] = $item_field['type'] . ' (' . $widget_obj->get_title() . ')';
					}
				}
			}

			$widgets[ $widget_key ] = array(
				'conditions' => array(
					'widgetType' => $widget_key,
				),
				'fields' => $fields,
				'fields_in_item' => $fields_in_item,
			);
		}

		return $widgets;
	}

	/**
	 * Get translated template id.
	 *
	 * @since 1.3.3
	 *
	 * @param int $template_id Template id.
	 *
	 * @return int Translated template id.
	 */
	public function get_translated_template_id( $template_id ) {
		if ( empty( $template_id ) ) {
			return $template_id;
		}

		$post_type = get_post_type( $template_id );

		return apply_filters( 'wpml_object_id', $template_id, $post_type, true );
	}

	/**
	 * Translate location args (post IDs) to current language.
	 *
	 * Used for template display conditions with specific pages/posts.
	 *
	 * @since 1.19.4
	 *
	 * @param array $args Array of post IDs from location condition.
	 *
	 * @return array Translated post IDs.
	 */
	public function translate_location_args( $args ) {
		if ( empty( $args ) || ! is_array( $args ) ) {
			return $args;
		}

		$translated_args = array();

		foreach ( $args as $post_id ) {
			$post_type = get_post_type( $post_id );
			$translated_id = apply_filters( 'wpml_object_id', $post_id, $post_type, true );

			$translated_args[] = $translated_id ? (int) $translated_id : $post_id;
		}

		return $translated_args;
	}

	/**
	 * Hide locations section for translated templates.
	 *
	 * Replaces the locations control with an info notice
	 * for translated templates since they inherit locations
	 * from the original template.
	 *
	 * @since 1.19.4
	 *
	 * @param \CmsmastersElementor\Base\Base_Document $document Document instance.
	 */
	public function hide_locations_for_translated_templates( $document ) {
		$post_id = $document->get_main_id();

		if ( ! $this->is_translation( $post_id ) ) {
			return;
		}

		// Get the original template URL for the link
		$original_id = $this->get_original_template_id( $post_id );
		$original_edit_url = '';

		if ( $original_id ) {
			$original_edit_url = admin_url( 'post.php?post=' . $original_id . '&action=elementor' );
		}

		$description = __( 'Location settings are managed in the original (default language) template. Translated templates automatically inherit locations from the original.', 'cmsmasters-elementor' );

		// Replace the locations control with info notice
		$document->update_control(
			'locations',
			array(
				'label' => '',
				'type' => Controls_Manager::RAW_HTML,
				'raw' => $description,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		// Add button to edit original template as separate control
		if ( $original_edit_url ) {
			$document->start_injection( array(
				'of' => 'locations',
				'at' => 'after',
			) );

			$document->add_control(
				'locations_edit_original_button',
				array(
					'label' => '',
					'type' => Controls_Manager::RAW_HTML,
					'raw' => '<a href="' . esc_url( $original_edit_url ) . '" target="_blank" class="elementor-button elementor-button-default">' . __( 'Edit Original Template', 'cmsmasters-elementor' ) . '</a>',
				)
			);

			$document->end_injection();
		}
	}

	/**
	 * Filter locations for translated templates.
	 *
	 * Removes locations from settings when saving a translated template
	 * to prevent location duplication.
	 *
	 * @since 1.19.4
	 *
	 * @param array $settings Document settings being saved.
	 *
	 * @return array Filtered settings.
	 */
	public function filter_translated_template_locations( $settings ) {
		if ( ! isset( $settings['locations'] ) ) {
			return $settings;
		}

		$post_id = get_the_ID();

		if ( ! $post_id ) {
			return $settings;
		}

		if ( ! $this->is_translation( $post_id ) ) {
			return $settings;
		}

		// For translated templates, remove locations to prevent duplication
		unset( $settings['locations'] );

		return $settings;
	}

	/**
	 * Delete locations meta for translated templates.
	 *
	 * Called after document save to ensure translated templates
	 * don't have their own locations stored.
	 *
	 * @since 1.19.4
	 *
	 * @param \Elementor\Core\Base\Document $document The document instance.
	 * @param array $data The document data.
	 */
	public function delete_translated_template_locations( $document, $data ) {
		$post_id = $document->get_main_id();

		if ( ! $this->is_translation( $post_id ) ) {
			return;
		}

		// Delete locations meta for translated template
		$document->delete_main_meta( '_cmsmasters_locations' );

		// Remove only this specific template from global storage
		$this->remove_template_from_storage( $post_id );
	}

	/**
	 * Check if a post is a translation (not in default language).
	 *
	 * @since 1.19.4
	 *
	 * @param int $post_id Post ID to check.
	 *
	 * @return bool True if post is a translation, false if original.
	 */
	public function is_translation( $post_id ) {
		$default_language = apply_filters( 'wpml_default_language', null );

		if ( ! $default_language ) {
			return false;
		}

		$post_language = apply_filters( 'wpml_element_language_code', null, array(
			'element_id' => $post_id,
			'element_type' => get_post_type( $post_id ),
		) );

		if ( ! $post_language || $post_language === $default_language ) {
			return false;
		}

		// Check that an actual original template exists in the default language.
		// If the template was reassigned to a non-default language without a proper
		// translation relationship, it should be treated as standalone.
		$original_id = $this->get_original_template_id( $post_id );

		if ( ! $original_id || (int) $original_id === (int) $post_id ) {
			return false;
		}

		// Check if this post is the trid source (created first).
		// When a DE template is created first and EN translation added later,
		// WPML marks DE as source (source_language_code = null).
		// In that case, DE should manage its own locations.
		$element_type = 'post_' . get_post_type( $post_id );
		$trid = apply_filters( 'wpml_element_trid', null, $post_id, $element_type );

		if ( $trid ) {
			$translations = apply_filters( 'wpml_get_element_translations', null, $trid, $element_type );

			if ( is_array( $translations ) ) {
				foreach ( $translations as $translation ) {
					if ( (int) $translation->element_id === (int) $post_id && empty( $translation->source_language_code ) ) {
						// This post is the trid source — not a translation
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Get original (default language) template ID.
	 *
	 * @since 1.19.4
	 *
	 * @param int $post_id Translated post ID.
	 *
	 * @return int|false Original post ID or false.
	 */
	private function get_original_template_id( $post_id ) {
		$default_language = apply_filters( 'wpml_default_language', null );

		if ( ! $default_language ) {
			return false;
		}

		$post_type = get_post_type( $post_id );

		return apply_filters( 'wpml_object_id', $post_id, $post_type, true, $default_language );
	}

	/**
	 * Remove specific template from locations storage.
	 *
	 * @since 1.19.4
	 *
	 * @param int $post_id Template post ID to remove.
	 */
	private function remove_template_from_storage( $post_id ) {
		if ( ! class_exists( '\CmsmastersElementor\Modules\TemplateLocations\Module' ) ) {
			return;
		}

		$locations_module = \CmsmastersElementor\Modules\TemplateLocations\Module::instance();

		if ( $locations_module && method_exists( $locations_module, 'get_rules_manager' ) ) {
			$rules_manager = $locations_module->get_rules_manager();

			if ( $rules_manager && method_exists( $rules_manager, 'remove_post_from_storage' ) ) {
				$rules_manager->remove_post_from_storage( $post_id );
			}
		}
	}

	/**
	 * Handle WPML settings update.
	 *
	 * Detects when default language changes and migrates template locations.
	 *
	 * @since 1.24.1
	 *
	 * @param array $old_value Old settings value.
	 * @param array $new_value New settings value.
	 */
	public function on_wpml_settings_update( $old_value, $new_value ) {
		$old_default = isset( $old_value['default_language'] ) ? $old_value['default_language'] : '';
		$new_default = isset( $new_value['default_language'] ) ? $new_value['default_language'] : '';

		if ( empty( $old_default ) || empty( $new_default ) || $old_default === $new_default ) {
			return;
		}

		$this->migrate_locations_on_default_language_change( $old_default, $new_default );
	}

	/**
	 * Migrate template locations when default language changes.
	 *
	 * Moves locations meta and directly updates the global storage option.
	 * Does not depend on TemplateLocations module being initialized.
	 *
	 * @since 1.24.1
	 *
	 * @param string $old_lang Old default language code.
	 * @param string $new_lang New default language code.
	 */
	private function migrate_locations_on_default_language_change( $old_lang, $new_lang ) {
		$meta_key = '_cmsmasters_locations';
		$option_name = 'cmsmasters_elementor_documents_locations';

		// Switch to all languages to find templates in any language
		$this->switch_to_all_languages();

		// Query all templates that have locations meta
		$templates_query = new \WP_Query( array(
			'post_type' => 'elementor_library',
			'meta_key' => $meta_key,
			'fields' => 'ids',
			'posts_per_page' => -1,
			'post_status' => 'any',
		) );

		$templates_with_locations = $templates_query->posts;

		$this->restore_current_language();

		if ( empty( $templates_with_locations ) ) {
			return;
		}

		// Collect ID mapping: old_id => new_id
		$id_map = array();

		foreach ( $templates_with_locations as $template_id ) {
			$template_lang = apply_filters( 'wpml_element_language_code', null, array(
				'element_id' => $template_id,
				'element_type' => 'elementor_library',
			) );

			if ( $template_lang !== $old_lang ) {
				continue;
			}

			$new_template_id = apply_filters( 'wpml_object_id', $template_id, 'elementor_library', false, $new_lang );

			if ( ! $new_template_id || $new_template_id === $template_id ) {
				continue;
			}

			$locations = get_post_meta( $template_id, $meta_key, true );

			if ( empty( $locations ) ) {
				continue;
			}

			// Move locations meta
			update_post_meta( $new_template_id, $meta_key, $locations );
			delete_post_meta( $template_id, $meta_key );

			$id_map[ $template_id ] = $new_template_id;
		}

		if ( empty( $id_map ) ) {
			return;
		}

		// Directly update the global storage option — swap template IDs
		$storage = get_option( $option_name, array() );

		if ( empty( $storage ) || ! is_array( $storage ) ) {
			return;
		}

		$updated_storage = array();

		foreach ( $storage as $location_type => $templates ) {
			if ( ! is_array( $templates ) ) {
				$updated_storage[ $location_type ] = $templates;
				continue;
			}

			$updated_templates = array();

			foreach ( $templates as $doc_id => $rules ) {
				if ( isset( $id_map[ $doc_id ] ) ) {
					$updated_templates[ $id_map[ $doc_id ] ] = $rules;
				} else {
					$updated_templates[ $doc_id ] = $rules;
				}
			}

			$updated_storage[ $location_type ] = $updated_templates;
		}

		update_option( $option_name, $updated_storage );
	}

	/**
	 * Switch WPML to all languages mode.
	 *
	 * Used before querying templates with locations to ensure
	 * all language versions are included in the results.
	 *
	 * @since 1.24.1
	 */
	public function switch_to_all_languages() {
		do_action( 'wpml_switch_language', 'all' );
	}

	/**
	 * Restore WPML to the current language.
	 *
	 * Called after querying templates to restore the original
	 * language context.
	 *
	 * @since 1.24.1
	 */
	public function restore_current_language() {
		do_action( 'wpml_switch_language', null );
	}

	/**
	 * Fix template taxonomy and storage after language change in WP editor.
	 *
	 * When a template's language is changed via the WordPress editor
	 * (not Elementor), the taxonomy term relationship may break and
	 * the template disappears from type-filtered views.
	 *
	 * This re-assigns the taxonomy term and regenerates locations storage.
	 *
	 * @since 1.24.1
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 */
	public function fix_template_after_language_change( $post_id, $post ) {
		// Skip autosaves and revisions
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Skip if this is an Elementor save — Elementor handles it already
		if ( did_action( 'elementor/document/before_save' ) ) {
			return;
		}

		// Re-assign the template type taxonomy term to ensure it's
		// properly linked in the new language context.
		$template_type = get_post_meta( $post_id, '_elementor_template_type', true );

		if ( ! empty( $template_type ) ) {
			wp_set_object_terms( $post_id, $template_type, 'elementor_library_type' );
		}

		// If this template has locations, regenerate the global storage
		$locations = get_post_meta( $post_id, '_cmsmasters_locations', true );

		if ( ! empty( $locations ) ) {
			$this->regenerate_locations_storage();
		}
	}

	/**
	 * Regenerate global locations storage.
	 *
	 * @since 1.24.1
	 */
	private function regenerate_locations_storage() {
		if ( ! class_exists( '\CmsmastersElementor\Modules\TemplateLocations\Module' ) ) {
			return;
		}

		$locations_module = \CmsmastersElementor\Modules\TemplateLocations\Module::instance();

		if ( $locations_module && method_exists( $locations_module, 'get_rules_manager' ) ) {
			$rules_manager = $locations_module->get_rules_manager();

			if ( $rules_manager && method_exists( $rules_manager, 'regenerate_locations' ) ) {
				$rules_manager->regenerate_locations();
			}
		}
	}

}
