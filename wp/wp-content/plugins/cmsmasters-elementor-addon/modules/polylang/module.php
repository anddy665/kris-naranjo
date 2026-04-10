<?php
namespace CmsmastersElementor\Modules\Polylang;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Plugin;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Polylang module.
 *
 * @since 1.19.4
 */
class Module extends Base_Module {

	/**
	 * Whether language filtering is currently suppressed.
	 *
	 * @since 1.24.1
	 *
	 * @var bool
	 */
	private $suppress_language_filter = false;

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.19.4
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'polylang';
	}

	/**
	 * Module activation.
	 *
	 * Check if module is active.
	 *
	 * @since 1.19.4
	 *
	 * @return bool
	 */
	public static function is_active() {
		return defined( 'POLYLANG_VERSION' ) || function_exists( 'pll_current_language' );
	}

	/**
	 * Init filters.
	 *
	 * Initialize module filters.
	 *
	 * @since 1.19.4
	 */
	protected function init_filters() {
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
		add_action( 'update_option_polylang', array( $this, 'on_polylang_options_update' ), 10, 2 );

		// Switch to all languages for template locations queries to prevent storage corruption
		add_action( 'cmsmasters_elementor/locations/before_get_templates', array( $this, 'switch_to_all_languages' ) );
		add_action( 'cmsmasters_elementor/locations/after_get_templates', array( $this, 'restore_current_language' ) );


		// Fix taxonomy term after template language change in WP editor
		add_action( 'save_post_elementor_library', array( $this, 'fix_template_after_language_change' ), 100, 2 );
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
	 * Get original (default language) template ID.
	 *
	 * @since 1.19.4
	 *
	 * @param int $post_id Translated post ID.
	 *
	 * @return int|false Original post ID or false.
	 */
	private function get_original_template_id( $post_id ) {
		if ( ! function_exists( 'pll_get_post' ) || ! function_exists( 'pll_default_language' ) ) {
			return false;
		}

		$default_language = pll_default_language();

		return pll_get_post( $post_id, $default_language );
	}

	/**
	 * Get translated template id.
	 *
	 * @since 1.19.4
	 *
	 * @param int $template_id Template id.
	 *
	 * @return int Translated template id.
	 */
	public function get_translated_template_id( $template_id ) {
		if ( empty( $template_id ) ) {
			return $template_id;
		}

		if ( ! function_exists( 'pll_get_post' ) ) {
			return $template_id;
		}

		$translated_id = pll_get_post( $template_id );

		return $translated_id ? $translated_id : $template_id;
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

		if ( ! function_exists( 'pll_get_post' ) ) {
			return $args;
		}

		$translated_args = array();

		foreach ( $args as $post_id ) {
			$translated_id = pll_get_post( $post_id );

			$translated_args[] = $translated_id ? (int) $translated_id : $post_id;
		}

		return $translated_args;
	}

	/**
	 * Filter locations for translated templates.
	 *
	 * Removes locations from settings when saving a translated template
	 * to prevent location duplication. Only the original (default language)
	 * template should have locations stored.
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
	 * Check if a post is a translation (not in default language).
	 *
	 * @since 1.19.4
	 *
	 * @param int $post_id Post ID to check.
	 *
	 * @return bool True if post is a translation, false if original.
	 */
	public function is_translation( $post_id ) {
		if ( ! function_exists( 'pll_get_post_language' ) || ! function_exists( 'pll_default_language' ) ) {
			return false;
		}

		$post_language = pll_get_post_language( $post_id );
		$default_language = pll_default_language();

		if ( $post_language === $default_language ) {
			return false;
		}

		// Check that an actual original template exists in the default language.
		// If the template was reassigned to a non-default language without a proper
		// translation relationship, it should be treated as standalone.
		$original_id = $this->get_original_template_id( $post_id );

		if ( ! $original_id || (int) $original_id === (int) $post_id ) {
			return false;
		}

		return true;
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

		// Remove only this specific template from global storage (without full regeneration)
		$this->remove_template_from_storage( $post_id );
	}

	/**
	 * Remove specific template from locations storage.
	 *
	 * Surgically removes only the translated template from global option
	 * without affecting other templates.
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
	 * Handle Polylang options update.
	 *
	 * Detects when default language changes and migrates template locations.
	 *
	 * @since 1.24.1
	 *
	 * @param array $old_value Old options value.
	 * @param array $new_value New options value.
	 */
	public function on_polylang_options_update( $old_value, $new_value ) {
		// Check if default_lang changed
		$old_default = isset( $old_value['default_lang'] ) ? $old_value['default_lang'] : '';
		$new_default = isset( $new_value['default_lang'] ) ? $new_value['default_lang'] : '';

		if ( empty( $old_default ) || empty( $new_default ) || $old_default === $new_default ) {
			return;
		}

		// Migrate locations from old default language templates to new default language templates
		$this->migrate_locations_on_default_language_change( $old_default, $new_default );
	}

	/**
	 * Migrate template locations when default language changes.
	 *
	 * Copies locations from templates in old default language to their
	 * translations in the new default language.
	 *
	 * @since 1.24.1
	 *
	 * @param string $old_lang Old default language code.
	 * @param string $new_lang New default language code.
	 */
	private function migrate_locations_on_default_language_change( $old_lang, $new_lang ) {
		if ( ! function_exists( 'pll_get_post' ) || ! function_exists( 'pll_get_post_language' ) ) {
			return;
		}

		if ( ! class_exists( '\CmsmastersElementor\Modules\TemplateLocations\Rules_Manager' ) ) {
			return;
		}

		// Get all templates with locations
		$templates_with_locations = \CmsmastersElementor\Modules\TemplateLocations\Rules_Manager::get_templates_with_locations();

		if ( empty( $templates_with_locations ) ) {
			return;
		}

		$migrated = false;

		foreach ( $templates_with_locations as $template_id ) {
			$template_lang = pll_get_post_language( $template_id );

			if ( $template_lang !== $old_lang ) {
				continue;
			}

			$new_template_id = pll_get_post( $template_id, $new_lang );

			if ( ! $new_template_id || $new_template_id === $template_id ) {
				continue;
			}

			$locations = get_post_meta( $template_id, '_cmsmasters_locations', true );

			if ( empty( $locations ) ) {
				continue;
			}

			update_post_meta( $new_template_id, '_cmsmasters_locations', $locations );
			delete_post_meta( $template_id, '_cmsmasters_locations' );

			$migrated = true;
		}

		if ( $migrated ) {
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

	/**
	 * Switch Polylang to all languages mode.
	 *
	 * Adds a pre_get_posts filter that sets lang to empty string,
	 * which tells Polylang to return posts in all languages.
	 *
	 * @since 1.24.1
	 */
	public function switch_to_all_languages() {
		$this->suppress_language_filter = true;

		add_action( 'pre_get_posts', array( $this, 'set_query_all_languages' ), 1 );
	}

	/**
	 * Restore Polylang to the previous language.
	 *
	 * Removes the pre_get_posts filter that suppresses language filtering.
	 *
	 * @since 1.24.1
	 */
	public function restore_current_language() {
		$this->suppress_language_filter = false;

		remove_action( 'pre_get_posts', array( $this, 'set_query_all_languages' ), 1 );
	}

	/**
	 * Set WP_Query to return all languages.
	 *
	 * Callback for pre_get_posts that tells Polylang
	 * to skip language filtering for this query.
	 *
	 * @since 1.24.1
	 *
	 * @param \WP_Query $query The WP_Query instance.
	 */
	public function set_query_all_languages( $query ) {
		if ( $this->suppress_language_filter ) {
			$query->set( 'lang', '' );
		}
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

}




