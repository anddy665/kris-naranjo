<?php
namespace CmsmastersElementor\Modules\TemplatePages\Documents;

use CmsmastersElementor\Modules\TemplateLocations\Module as LocationsModule;
use CmsmastersElementor\Modules\TemplatePages\Documents\Base\Archive_Singular_Document;
use CmsmastersElementor\Modules\TemplatePreview\Traits\Preview_Type;
use CmsmastersElementor\Utils;

use Elementor\TemplateLibrary\Source_Local;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Archive extends Archive_Singular_Document {

	use Preview_Type;

	/**
	 * Document post type meta key.
	 */
	const ARCHIVE_TEMPLATE_TYPE_META = '_cmsmasters_archive_post_type';

	protected $preview_type_default = '';

	protected $preview_id_default = '';

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @since 1.0.0
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['location_type'] = 'archive';

		$properties = apply_filters( 'cmsmasters_elementor/documents/archive/get_properties', $properties );

		return $properties;
	}

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Document name.
	 */
	public function get_name() {
		return 'cmsmasters_archive';
	}

	/**
	 * Get document title.
	 *
	 * Retrieve the document title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Document title.
	 */
	public static function get_title() {
		return __( 'Archive', 'cmsmasters-elementor' );
	}

	/**
	 * Get editor widgets panel categories.
	 *
	 * Retrieve an array of reordered editor widgets panel categories.
	 *
	 * Move archive category widgets to the beginning of editor panel.
	 *
	 * @since 1.0.0
	 *
	 * @return array Reordered array of widget categories.
	 */
	protected static function get_editor_panel_categories() {
		$categories = array(
			self::ARCHIVE_WIDGETS_CATEGORY => array( 'title' => self::get_title() ),
		);

		if ( Utils::is_pro() ) {
			$categories['theme-elements-archive'] = array(
				'title' => __( 'Archive', 'cmsmasters-elementor' ),
				'active' => true,
			);
		}

		$categories += parent::get_editor_panel_categories();

		if ( ! Utils::is_pro() ) {
			$categories['pro-elements']['active'] = false;
		}

		return $categories;
	}

	/**
	 * Save template type.
	 *
	 * Set document name as template type meta key.
	 *
	 * Set archive template post type meta key if selected.
	 *
	 * @since 1.0.0
	 */
	public function save_template_type() {
		parent::save_template_type();

		if ( ! empty( $_REQUEST[ self::ARCHIVE_TEMPLATE_TYPE_META ] ) ) {
			$template_type = $_REQUEST[ self::ARCHIVE_TEMPLATE_TYPE_META ];

			$this->update_meta( self::ARCHIVE_TEMPLATE_TYPE_META, $template_type );
		}
	}

	/**
	 * Archive document constructor.
	 *
	 * Initializing the Addon archive document.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Class initial data.
	 */
	public function __construct( array $data = array() ) {
		if ( $data ) {
			add_filter( 'body_class', array( $this, 'filter_body_classes' ) );
		}

		parent::__construct( $data );
	}

	/**
	 * Add body classes.
	 *
	 * Filters body classes for the `style` controls selector.
	 *
	 * @since 1.0.0
	 *
	 * @param array $body_classes Body classes array.
	 *
	 * @return array Filtered body classes array.
	 */
	public function filter_body_classes( $body_classes ) {
		$template_type = Source_Local::get_template_type( get_the_ID() );

		if ( is_home() || is_archive() || is_search() || 'archive' === $template_type ) {
			$body_classes[] = 'elementor-page-' . $this->get_main_id();
		}

		return $body_classes;
	}

	public static function get_preview_type_options() {
		return array_merge( parent::get_preview_type_options(), static::get_archive_preview_type_options_choices( false, false ) );
	}

	public function get_preview_type_default() {
		if ( empty( $this->preview_type_default ) ) {
			$this->set_default_preview();
		}

		return $this->preview_type_default;
	}

	public function get_preview_id_default() {
		if ( empty( $this->preview_type_default ) ) {
			$this->set_default_preview();
		}

		return $this->preview_id_default;
	}

	protected function set_default_preview() {
		if ( ! empty( $this->preview_type_default ) ) {
			return;
		}

		$template_type = $this->get_main_meta( self::ARCHIVE_TEMPLATE_TYPE_META );

		list( $rule_type, $rule_subtype ) = array_pad( explode( '/', $template_type ), 2, '' );

		$posts_args = array(
			'fields' => 'ids',
			'numberposts' => 1,
		);

		switch ( $rule_type ) {
			case 'post_type_archive':
				$posts_args['post_type'] = $rule_subtype;
				$recent_posts = get_posts( $posts_args );

				if ( ! empty( $recent_posts ) ) {
					$this->preview_type_default = $template_type;
				}

				break;
			case 'archive':
				switch ( $rule_subtype ) {
					case 'date':
						$posts_args['year'] = gmdate( 'Y' );
						$recent_posts = get_posts( $posts_args );

						if ( ! empty( $recent_posts ) ) {
							$this->preview_type_default = $template_type;
						} else {
							$posts_args['year'] = (int) $posts_args['year'] - 1;
							$recent_posts = get_posts( $posts_args );

							if ( ! empty( $recent_posts ) ) {
								$this->preview_type_default = $template_type;
							}
						}

						break;
					case 'author':
						$user_id = get_current_user_id();

						if ( count_user_posts( $user_id ) ) {
							$this->preview_type_default = $template_type;
							$this->preview_id_default = $user_id;
						} else {
							$author_ids = get_users( array(
								'has_published_posts' => true,
								'fields' => 'ID',
								'orderby' => 'post_count',
								'order' => 'DESC',
								'number' => 1,
							) );

							if ( ! empty( $author_ids ) ) {
								$this->preview_type_default = $template_type;
								$this->preview_id_default = $author_ids[0];
							}
						}

						break;
				}

				break;
			case 'taxonomy':
				$taxonomy_ids = get_terms( array(
					'taxonomy' => $rule_subtype,
					'fields' => 'ids',
					'orderby' => 'count',
					'order' => 'DESC',
					'number' => 1,
				) );

				if ( ! empty( $taxonomy_ids ) ) {
					$this->preview_type_default = $template_type;
					$this->preview_id_default = $taxonomy_ids[0];
				}

				break;
			case 'page':
				if ( 'search' === $rule_subtype ) {
					$posts_args['s'] = '';
					$recent_posts = get_posts( $posts_args );

					if ( ! empty( $recent_posts ) ) {
						$this->preview_type_default = $template_type;
					}
				}

				break;
		}
	}

	public function get_locations_default() {
		$default_locations = parent::get_locations_default();

		if ( ! empty( $default_locations ) ) {
			return $default_locations;
		}

		/** @var LocationsModule $locations_module */
		$locations_module = LocationsModule::instance();
		$rules_manager = $locations_module->get_rules_manager();

		$template_type = $this->get_main_meta( self::ARCHIVE_TEMPLATE_TYPE_META );

		list( $rule_type, $rule_subtype ) = array_pad( explode( '/', $template_type ), 2, '' );

		if ( 'post_type_archive' === $rule_type ) {
			$rule_subtype = "{$rule_subtype}_archive";
		}

		if ( $rules_manager->get_rule_instance( $rule_subtype ) ) {
			$default_locations[] = array(
				'stmt' => 'include',
				'main' => 'archive',
				'addl' => $rule_subtype,
			);
		}

		return $default_locations;
	}

}
