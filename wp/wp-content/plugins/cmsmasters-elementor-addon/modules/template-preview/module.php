<?php
namespace CmsmastersElementor\Modules\TemplatePreview;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TemplateDocuments\Module as DocumentsModule;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Template Preview module.
 *
 * The template preview handler class is responsible for replacing
 * Elementor documents content in editor preview.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	/**
	 * Get module name.
	 *
	 * Retrieve the CMSMasters template preview module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'template-preview';
	}

	/**
	 * Check if module is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_active() {
		return class_exists( DocumentsModule::class );
	}

	/**
	 * Add actions initialization.
	 *
	 * Register actions for the Template Preview module.
	 *
	 * @since 1.0.0
	 * @since 1.18.0 Added container template.
	 */
	protected function init_actions() {
		// Start editor actions
		add_action( 'cmsmasters_elementor/documents/section/register_controls', array( $this, 'register_controls' ) );
		add_action( 'cmsmasters_elementor/documents/container/register_controls', array( $this, 'register_controls' ) );
		add_action( 'cmsmasters_elementor/documents/archive_singular/register_controls', array( $this, 'register_controls' ) );

		// Addon documents preview
		add_action( 'cmsmasters_elementor/documents/before_render_element', array( $this, 'switch_to_preview_query' ) );
		add_action( 'cmsmasters_elementor/documents/after_render_element', array( $this, 'restore_current_query' ) );

		add_action( 'cmsmasters_elementor/documents/before_get_elements_raw_data', array( $this, 'switch_to_preview_query' ) );
		add_action( 'cmsmasters_elementor/documents/after_get_elements_raw_data', array( $this, 'restore_current_query' ) );

		add_action( 'cmsmasters_elementor/documents/before_get_content', array( $this, 'switch_to_preview_query' ) );
		add_action( 'cmsmasters_elementor/documents/after_get_content', array( $this, 'restore_current_query' ) );

		// Addon location rules manager preview
		add_action( 'cmsmasters_elementor/location_rules_manager/before_get_location_templates', array( $this, 'switch_to_preview_query' ) );
		add_action( 'cmsmasters_elementor/location_rules_manager/after_get_location_templates', array( $this, 'restore_current_query' ) );

		// Addon templates library preview
		add_action( 'cmsmasters_elementor/templates-library/before_get_source_data', array( $this, 'switch_to_preview_query' ) );
		add_action( 'cmsmasters_elementor/templates-library/after_get_source_data', array( $this, 'restore_current_query' ) );

		// Elementor templates library preview
		add_action( 'elementor/template-library/before_get_source_data', array( $this, 'switch_to_preview_query' ) );
		add_action( 'elementor/template-library/after_get_source_data', array( $this, 'restore_current_query' ) );

		// Elementor dynamic tags preview
		add_action( 'elementor/dynamic_tags/before_render', array( $this, 'switch_to_preview_query' ) );
		add_action( 'elementor/dynamic_tags/after_render', array( $this, 'restore_current_query' ) );
		// Finish editor actions
	}

	/**
	 * Add filters initialization.
	 *
	 * Register filters for the Template Preview module.
	 *
	 * @since 1.0.0
	 */
	protected function init_filters() {
		add_filter( 'elementor/document/urls/wp_preview', array( $this, 'get_wp_preview_url' ), 10, 2 );

		// add_filter( 'cmsmasters_elementor/dynamic_tags/post_terms/taxonomy_args', array( $this, 'filter_post_terms_taxonomy_args' ) );

		// add_filter( 'cmsmasters_elementor/query_control/get_query_vars/current_query', array( $this, 'filter_query_control_vars' ) );
		add_filter( 'cmsmasters_elementor/query/get_query_args/current_query', array( $this, 'filter_current_query_args' ) ); // TODO: maybe add `cmsmasters_elementor/posts_archive/query_posts/query_vars` filter

		add_filter( 'cmsmasters_elementor/widgets/post_content/render_preview_post', array( $this, 'set_preview_query_post' ) );
	}

	/**
	 * Register Addon document controls.
	 *
	 * Used to add new controls to the global document settings.
	 *
	 * Fired by `cmsmasters_elementor/documents/register_controls` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @param Base_Document $document Addon base document instance.
	 */
	public function register_controls( $document ) {
		$document->start_controls_section(
			'preview_settings',
			array(
				'tab' => Controls_Manager::TAB_SETTINGS,
				'label' => __( 'Preview', 'cmsmasters-elementor' ),
			)
		);

		$document->add_control(
			'preview_settings_heading',
			array(
				'label' => __( 'Preview Query:', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$document->add_control(
			'preview_type',
			array(
				'label' => __( 'Preview Type', 'cmsmasters-elementor' ),
				'show_label' => false,
				'label_block' => true,
				'type' => Controls_Manager::SELECT,
				'groups' => $document::get_preview_type_options(),
				'default' => $document->get_preview_type_default(),
			)
		);

		$document->add_control(
			'preview_id',
			array(
				'label_block' => true,
				'show_label' => false,
				'type' => CmsmastersControls::QUERY,
				'default' => $document->get_preview_id_default(),
				'autocomplete' => array( 'object' => Query_Manager::JS_OBJECT ),
				'condition' => array(
					'preview_type!' => array(
						'',
						'page/search',
						'page/error_404',
					),
				),
				'export' => false,
			)
		);

		$document->add_control(
			'preview_search_keyword',
			array(
				'label' => __( 'Search Keyword', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'condition' => array( 'preview_type' => 'page/search' ),
				'export' => false,
			)
		);

		$document->add_control(
			'preview_settings_info_box',
			array(
				'label_block' => true,
				'show_label' => false,
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __( 'Here you can select preview query for editor dynamic content, like post Featured Image or Title.', 'cmsmasters-elementor' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$document->add_control(
			'apply_preview',
			array(
				'label_block' => true,
				'show_label' => false,
				'type' => Controls_Manager::BUTTON,
				'text' => __( 'Apply & Reload', 'cmsmasters-elementor' ),
				'event' => 'cmsmasters:preview_manager:apply_preview',
			)
		);

		$document->end_controls_section();
	}

	/**
	 * Switch to preview query.
	 *
	 * Change the WordPress query to a new query with the requested
	 * query arguments.
	 *
	 * Fired by multiple action hooks.
	 *
	 * @param int $post_id Optional. Post ID. Default is `null`, the current post ID.
	 *
	 * @since 1.0.0
	 */
	public function switch_to_preview_query( $post_id = null ) {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$document = Plugin::elementor()->documents->get_doc_or_auto_save( $post_id );

		if ( ! $document || ! $document instanceof Base_Document ) {
			return;
		}

		$query_args = $this->get_preview_as_query_args( $document );

		Plugin::elementor()->db->switch_to_query( $query_args, true );

		$this->after_preview_switch_to_query( $document );
	}

	/**
	 * Switch to preview query.
	 *
	 * Change the WordPress query to a new query with the requested
	 * query arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param Base_Document $document Addon base document instance.
	 */
	public function get_preview_as_query_args( Base_Document $document ) {
		$preview_type_setting = $document->get_settings( 'preview_type' );
		$preview_type_setting = is_string( $preview_type_setting ) ? $preview_type_setting : '';

		list( $preview_type, $preview_subtype ) = array_pad( explode( '/', $preview_type_setting ), 2, '' );

		$preview_id = (int) $document->get_settings( 'preview_id' );

		switch ( $preview_type ) {
			case 'archive':
				switch ( $preview_subtype ) {
					case 'author':
						if ( empty( $preview_id ) ) {
							$preview_id = get_current_user_id();
						}

						$query_args = array( 'author' => $preview_id );

						break;
					case 'date':
						$query_args = array( 'year' => gmdate( 'Y' ) );

						break;
				}

				break;
			case 'page':
				switch ( $preview_subtype ) {
					case 'search':
						$query_args = array( 's' => $document->get_settings( 'preview_search_keyword' ) );

						break;
					case 'error_404':
						$query_args = array( 'p' => -1 );

						break;
				}

				break;
			case 'taxonomy':
				$term = get_term( $preview_id );

				if ( $term && ! is_wp_error( $term ) ) {
					$query_args = array(
						'tax_query' => array(
							array(
								'taxonomy' => $term->taxonomy,
								'terms' => array( $preview_id ),
								'field' => 'id',
							),
						),
					);
				}

				break;
			case 'post_type_archive':
				if ( post_type_exists( $preview_subtype ) ) {
					$query_args = array( 'post_type' => $preview_subtype );
				}

				break;
			case 'singular':
				$post = get_post( $preview_id );

				if ( $post && $preview_subtype === $post->post_type ) {
					$query_args = array(
						'p' => $post->ID,
						'post_type' => $post->post_type,
					);
				}

				break;
		}

		if ( empty( $query_args ) ) {
			$query_args = array(
				'post_type' => $document->get_main_post()->post_type,
				'p' => $document->get_main_id(),
			);
		}

		if ( Utils::is_publish( $document->get_main_post() ) ) {
			$query_args['post_status'] = 'publish';
		}

		/**
		 * Preview query args.
		 *
		 * Filters the WordPress preview query arguments.
		 *
		 * @since 1.0.0
		 *
		 * @param array $query_args Preview query arguments.
		 * @param Base_Document $document An instance of the Addon base document.
		 */
		$query_args = apply_filters( 'cmsmasters_elementor/preview/query_args', $query_args, $document );

		return $query_args;
	}

	public function after_preview_switch_to_query( $document ) {
		global $wp_query;

		if ( 'post_type_archive/post' === $document->get_settings( 'preview_type' ) ) {
			$wp_query->is_archive = true;
		}
	}

	/**
	 * Restore current query.
	 *
	 * Rollback to the previous query, rolling back from `DB::switch_to_query()`.
	 *
	 * Fired by multiple action hooks.
	 *
	 * @since 1.0.0
	 */
	public function restore_current_query() {
		Plugin::elementor()->db->restore_current_query();
	}

	/**
	 * Set preview query post.
	 *
	 * Change current post to another preview query with the requested
	 * query arguments.
	 *
	 * Fired by `cmsmasters_elementor/widgets/post_content/render_preview_post` filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post Current post object.
	 *
	 * @return object|false Preview post object if selected, or false for
	 * selected post broken.
	 */
	public function set_preview_query_post( $post = false ) {
		if ( ! $post ) {
			$post = get_post();
		}

		if ( ! isset( $post->ID ) ) {
			return false;
		}

		/** @var DocumentsModule $documents_module */
		$documents_module = DocumentsModule::instance();
		$document = $documents_module->get_document( $post->ID );

		if ( ! $document ) {
			return $post;
		}

		$preview_type = $document->get_settings( 'preview_type' );
		$preview_id = $document->get_settings( 'preview_id' );

		if ( 0 !== strpos( $preview_type, 'singular' ) || empty( $preview_id ) ) {
			return false;
		}

		$post = get_post( $preview_id );

		if ( ! $post ) {
			return false;
		}

		return $post;
	}

	/**
	 * Switch to preview post.
	 *
	 * Change the WordPress query to a new query with the requested
	 * query arguments.
	 *
	 * @since 1.0.0
	 */
	public function switch_to_preview_post( $post_id = null ) {
		if ( ! $post_id ) {
			$post = $this->set_preview_query_post();

			if ( ! $post ) {
				return;
			}

			$post_id = $post->ID;
		}

		Plugin::elementor()->db->switch_to_post( $post_id );
	}

	/**
	 * Restore current post.
	 *
	 * Rollback to the previous query, rolling back from `DB::switch_to_post()`.
	 *
	 * @since 1.0.0
	 */
	public function restore_current_post() {
		Plugin::elementor()->db->restore_current_post();
	}

	/**
	 * Switch to preview url.
	 *
	 * Change default WordPress preview url to the new with the
	 * requested query arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param Base_Document $document Addon base document instance.
	 */
	public function get_wp_preview_url( $preview_url, $document ) {
		// Ajax request from editor.
		if ( ! empty( $_POST['initial_document_id'] ) ) {
			return $preview_url;
		}

		$preview_type_settings = $document->get_settings( 'preview_type' );

		if ( empty( $preview_type_settings ) ) {
			$preview_type_settings = '';
		}

		list( $preview_type, $preview_subtype ) = array_pad( explode( '/', $preview_type_settings ), 2, '' );

		$preview_id = (int) $document->get_settings( 'preview_id' );
		$home_url = trailingslashit( home_url() );

		switch ( $preview_type ) {
			case 'archive':
				switch ( $preview_subtype ) {
					case 'author':
						if ( empty( $preview_id ) ) {
							$preview_id = get_current_user_id();
						}

						$preview_url = get_author_posts_url( $preview_id );

						break;
					case 'date':
						$preview_url = add_query_arg( 'year', gmdate( 'Y' ), $home_url );

						break;
				}

				break;
			case 'page':
				switch ( $preview_subtype ) {
					case 'search':
						$preview_url = add_query_arg( 's', $document->get_settings( 'preview_search_keyword' ), $home_url );

						break;
					case 'error_404':
						$preview_url = add_query_arg( 'p', '0', $home_url );

						break;
				}

				break;
			case 'taxonomy':
				$term = get_term( $preview_id );

				if ( $term && ! is_wp_error( $term ) ) {
					$preview_url = get_term_link( $preview_id );
				}

				break;
			case 'post_type_archive':
				if ( 'post' !== $preview_subtype && post_type_exists( $preview_subtype ) ) {
					$preview_url = get_post_type_archive_link( $preview_subtype );
				}

				break;
			case 'singular':
				$post = get_post( $preview_id );

				if ( $post ) {
					$preview_url = get_permalink( $post );
				}

				break;
		}

		if ( empty( $preview_url ) ) {
			$preview_url = $document->get_permalink();
		}

		// require_once CMSMASTERS_ELEMENTOR_PATH . 'ChromePhp.php';
		// \ChromePhp::log( '$preview_url', $preview_url );

		$template_id = $document->get_main_id();
		$query_args = array(
			'preview' => true,
			'preview_nonce' => wp_create_nonce( 'post_preview_' . $template_id ),
			'cmsmasters_template_id' => $template_id,
		);

		// $preview_url = get_preview_post_link( $template_id, $query_args, $preview_url );
		$preview_url = set_url_scheme( add_query_arg( $query_args, $preview_url ) );

		/**
		 * Preview URL.
		 *
		 * Filters the WordPress preview URL.
		 *
		 * @since 1.0.0
		 *
		 * @param string $preview_url WordPress preview URL.
		 * @param Base_Document $this An instance of the Addon base document.
		 */
		$preview_url = apply_filters( 'cmsmasters_elementor/preview/preview_url', $preview_url, $document );

		return $preview_url;
	}

	/**
	 * Filter dynamic tags taxonomy args.
	 *
	 * Filters the taxonomy arguments used to retrieve the registered taxonomies
	 * displayed in the taxonomy dynamic tag.
	 *
	 * Fired by `cmsmasters_elementor/dynamic_tags/post_terms/taxonomy_args` filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $taxonomy_args An array of `key => value` arguments to match
	 * against the taxonomy objects inside the `get_taxonomies()` function.
	 *
	 * @return array Filtered taxonomy arguments.
	 */
	public function filter_post_terms_taxonomy_args( $taxonomy_args ) {
		/** @var DocumentsModule $documents_module */
		$documents_module = DocumentsModule::instance();

		if ( $documents_module->get_document( get_the_ID() ) ) {
			// Show all taxonomies
			unset( $taxonomy_args['object_type'] );
		}

		return $taxonomy_args;
	}

	/**
	 * Filter query control arguments.
	 *
	 * Filters the arguments for the query control.
	 *
	 * Fired by `cmsmasters_elementor/query/get_query_args/current_query` Addon filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_args Current WP query arguments.
	 *
	 * @return array Filtered query arguments.
	 */
	public function filter_current_query_args( $query_args ) {
		$document = Plugin::elementor()->documents->get_doc_or_auto_save( get_the_ID() );

		if ( $document && $document instanceof Base_Document ) {
			$query_args = $this->get_preview_as_query_args( $document );
		}

		return $query_args;
	}

}
