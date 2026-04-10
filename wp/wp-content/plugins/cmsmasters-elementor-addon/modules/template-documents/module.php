<?php
namespace CmsmastersElementor\Modules\TemplateDocuments;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\TemplatePages\Documents;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Core\Admin\EditorOneMenu\Interfaces\Menu_Item_Interface;
use Elementor\Core\Admin\EditorOneMenu\Interfaces\Menu_Item_With_Custom_Url_Interface;
use Elementor\Core\Documents_Manager;
use Elementor\Core\Editor\Editor;
use Elementor\Modules\EditorOne\Classes\Menu_Config;
use Elementor\Modules\EditorOne\Classes\Menu_Data_Provider;
use Elementor\TemplateLibrary\Source_Local;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * CMSMasters template documents module.
 *
 * CMSMasters template documents module handler class is responsible for
 * registering and managing Elementor templates library document types.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	/**
	 * @since 1.0.0
	 *
	 * @var array Document types.
	 */
	private $document_types = array();

	/**
	 * @since 1.0.0
	 *
	 * @var array Default Elementor documents.
	 */
	protected $elementor_documents = array();

	/**
	 * @since 1.0.0
	 *
	 * @var array Elementor finder custom library templates keywords.
	 */
	private $document_keywords = array();

	/**
	 * @since 1.0.0
	 *
	 * @var array Elementor finder create keywords.
	 */
	private $create_keywords = array();

	/**
	 * Get module name.
	 *
	 * Retrieve the CMSMasters template name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'template-documents';
	}

	/**
	 * CMSMasters template documents module constructor.
	 *
	 * Initializing CMSMasters template documents module.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->set_document_types();
		$this->set_finder_keywords();

		parent::__construct();
	}

	private function set_document_types() {
		/**
		 * Set document types.
		 *
		 * Filters the Elementor templates library document types.
		 *
		 * @since 1.0.0
		 *
		 * @param array $document_types Addon document types.
		 */
		$this->document_types = apply_filters( 'cmsmasters_elementor/documents/set_document_types', $this->document_types );

		$this->set_elementor_documents();
	}

	private function set_elementor_documents() {
		/**
		 * Set Elementor documents.
		 *
		 * Filters the Elementor default document types reassigned in Addon.
		 *
		 * @since 1.0.0
		 *
		 * @param array $elementor_documents Elementor document types.
		 */
		$this->elementor_documents = apply_filters( 'cmsmasters_elementor/documents/set_elementor_documents', $this->elementor_documents );
	}

	private function set_finder_keywords() {
		foreach ( array_keys( $this->document_types ) as $document_type ) {
			if ( in_array( $document_type, $this->elementor_documents, true ) ) {
				continue;
			}

			$this->document_keywords[] = str_replace( 'cmsmasters_', '', $document_type );
		}

		$this->create_keywords = array(
			'template',
			'library',
			'create',
			'new',
			'add',
		);
	}

	/**
	 * Add actions initialization.
	 *
	 * Register action hooks for the module.
	 *
	 * @since 1.0.0
	 * @since 1.20.1 Added `Templates` menu item in Elementor admin menu.
	 * @since 1.20.1 Added redirect to`Templates` menu item in Elementor admin menu.
	 */
	protected function init_actions() {
		// Admin
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu_reorder' ), 800 );
		add_action( 'all_admin_notices', array( $this, 'replace_admin_heading' ) );

		// Added the "Templates" menu item to the Elementor admin menu after updating Elementor to version 3.34.2.
		add_action( 'admin_init', array( $this, 'redirect_to_cmsmasters_theme_templates' ) );
		add_action( 'elementor/editor-one/menu/register', array( $this, 'register_cmsmasters_theme_templates_menu' ), 207 );

		if ( isset( $_GET['tabs_group'] ) && 'cmsmasters' === $_GET['tabs_group'] ) {
			add_filter( 'admin_body_class', function( $classes ) {
				$classes .= ' cmsmasters-theme-templates';

				return $classes;
			} );
		}

		add_action( 'elementor/template-library/create_new_dialog_fields', array( $this, 'create_singular_field' ) );
		add_action( 'elementor/template-library/create_new_dialog_fields', array( $this, 'create_archive_field' ) );

		// Common
		add_action( 'elementor/documents/register', array( $this, 'register_document_types' ) );
	}

	/**
	 * Add filters initialization.
	 *
	 * Register filter hooks for the module.
	 *
	 * @since 1.0.0
	 */
	protected function init_filters() {
		// Admin
		add_filter( 'admin_title', array( $this, 'admin_title' ), 10, 2 );
		add_filter( 'the_content', array( $this, 'builder_wrapper' ), 9999999 );

		// Common
		add_filter( 'elementor/finder/categories', array( $this, 'add_finder_static_items' ) );
		add_filter( 'elementor/finder/categories', array( $this, 'add_finder_dynamic_items' ) );
	}

	/**
	 * Redirect to cmsmasters templates.
	 *
	 * Redirect to CMSMasters Templates in Elementor `Templates` admin menu.
	 *
	 * Fired by `admin_init` action.
	 *
	 * @since 1.20.1
	 */
	public function redirect_to_cmsmasters_theme_templates() {
		if (
			is_admin() &&
			isset( $_GET['page'] ) &&
			'cmsmasters-theme-templates' === $_GET['page']
		) {
			wp_safe_redirect(
				admin_url( 'edit.php?post_type=elementor_library&tabs_group=cmsmasters' )
			);

			exit;
		}
	}

	/**
	 * Register CMSMasters Templates menu item in Elementor admin menu.
	 *
	 * Adds a custom `Templates` submenu item to the Elementor admin menu
	 * that links to CMSMasters templates library
	 * (`elementor_library` with `tabs_group=cmsmasters`).
	 *
	 * Fired by `elementor/editor-one/menu/register` action.
	 *
	 * @since 1.20.1
	 *
	 * @param Menu_Data_Provider $menu_data_provider Elementor menu data provider instance.
	 */
	public function register_cmsmasters_theme_templates_menu( $menu_data_provider ) {
		if ( ! $menu_data_provider instanceof Menu_Data_Provider ) {
			return;
		}

		$menu_data_provider->register_menu(
			new class implements
				Menu_Item_Interface,
				Menu_Item_With_Custom_Url_Interface {

				public function get_capability(): string {
					return 'edit_posts';
				}

				public function get_parent_slug(): string {
					return Menu_Config::ELEMENTOR_MENU_SLUG;
				}

				public function is_visible(): bool {
					return true;
				}

				public function get_label(): string {
					return __( 'Templates', 'cmsmasters-elementor' );
				}

				public function get_position(): int {
					return 1;
				}

				public function get_slug(): string {
					return 'cmsmasters-theme-templates';
				}

				public function get_menu_url(): string {
					return admin_url(
						'edit.php?post_type=elementor_library&tabs_group=cmsmasters'
					);
				}

				public function get_group_id(): string {
					return Menu_Config::TEMPLATES_GROUP_ID;
				}
			}
		);
	}

	/**
	 * Add admin menu item.
	 *
	 * Creates CMSMasters submenu for Elementor `Templates` admin menu.
	 *
	 * Fired by `admin_menu` action.
	 *
	 * @since 1.0.0
	 * @since 1.20.1 Removed `Theme Templates` submenu item.
	 * @since 1.20.1 Added new `Templates` menu item.
	 */
	public function admin_menu() {
		add_menu_page(
			__( 'Templates', 'cmsmasters-elementor' ),
			__( 'Templates', 'cmsmasters-elementor' ),
			Editor::EDITING_CAPABILITY,
			'cmsmasters-theme-templates',
			'__return_null',
			'data:image/svg+xml;base64,' . Utils::MENU_ICON,
			'58.3'
		);
	}

	private function get_cmsmasters_submenu_title() {
		return '<i class="cmsms-logo"></i>' . __( 'Theme Templates', 'cmsmasters-elementor' );
	}

	/**
	 * Add admin menu item.
	 *
	 * Creates CMSMasters submenu for Elementor `Templates` admin menu.
	 *
	 * Fired by `admin_menu` action.
	 *
	 * @since 1.0.0
	 */
	public function admin_menu_reorder() {
		global $submenu,
			$pagenow,
			$typenow;

		if (
			! isset( $submenu[ Source_Local::ADMIN_MENU_SLUG ] ) ||
			'edit.php' !== $pagenow ||
			Source_Local::CPT !== $typenow ||
			empty( $_REQUEST['tabs_group'] )
		) {
			return;
		}

		$library_submenu = &$submenu[ Source_Local::ADMIN_MENU_SLUG ];
		$current_tab_group = $_REQUEST['tabs_group'];

		$titles = array(
			'cmsmasters' => $this->get_cmsmasters_submenu_title(),
		);

		if ( empty( $titles[ $current_tab_group ] ) ) {
			return;
		}

		$library_title = $titles[ $current_tab_group ];

		foreach ( $library_submenu as &$item ) {
			if ( $library_title === $item[0] ) {
				$item[4] = isset( $item[4] ) ? "{$item[4]} current" : 'current';
			}
		}
	}

	private function get_admin_templates_url( $relative = false ) {
		$admin_url = Source_Local::ADMIN_MENU_SLUG;

		if ( ! $relative ) {
			$admin_url = admin_url( $admin_url );
		}

		return add_query_arg( array( 'tabs_group' => 'cmsmasters' ), $admin_url );
	}

	public function replace_admin_heading() {
		global $post_type_object;

		if ( ! $post_type_object || 'elementor_library' !== $post_type_object->name ) {
			return false;
		}

		if ( ! isset( $_GET['tabs_group'] ) || 'cmsmasters' !== $_GET['tabs_group'] ) {
			return false;
		}

		$post_type_object->labels->name = __( 'Templates', 'cmsmasters-elementor' );
	}

	/**
	 * Create singular field.
	 *
	 * Adds new post type field to template library new template dialog.
	 *
	 * Fired by `elementor/template-library/create_new_dialog_fields` action.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed Template part.
	 */
	public function create_singular_field() {
		$post_types_list = Utils::filter_public_post_types( array(), true );
		$post_types_list['attachment'] = get_post_type_object( 'attachment' )->label;

		$options = array();

		foreach ( $post_types_list as $post_type => $post_type_label ) {
			$options[ 'singular/' . $post_type ] = $post_type_label;
		}

		$options['page/error_404'] = __( '404 Error page', 'cmsmasters-elementor' );

		$this->print_new_dialog_field_template(
			$options,
			Documents\Singular::SINGULAR_TEMPLATE_TYPE_META,
			__( 'singular', 'cmsmasters-elementor' )
		);
	}

	/**
	 * Create archive field.
	 *
	 * Adds new archive type field to template library new template dialog.
	 *
	 * Fired by `elementor/template-library/create_new_dialog_fields` action.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed Template part.
	 */
	public function create_archive_field() {
		$options = array();

		foreach ( Utils::filter_public_post_types() as $post_type => $post_type_label ) {
			$post_type_object = get_post_type_object( $post_type );

			$options[ $post_type ] = array( 'label' => $post_type_label );

			if ( 'post' === $post_type ) {
				$options[ $post_type ]['post_type_archive/post'] = __( 'Recent posts', 'cmsmasters-elementor' );
			}

			if ( $post_type_object->has_archive ) {
				/* translators: Add new template dialog archive field options. %s: Post type name */
				$options[ $post_type ][ "post_type_archive/{$post_type}" ] = sprintf( __( '%s archive', 'cmsmasters-elementor' ), $post_type_object->label );
			}

			$post_type_taxonomies_object = get_object_taxonomies( $post_type, 'objects' );

			if ( empty( $post_type_taxonomies_object ) ) {
				$options = $this->validate_optgroup( $options, $post_type );

				continue;
			}

			$filtered_object_taxonomies = wp_filter_object_list( $post_type_taxonomies_object, array(
				'public' => true,
				'show_in_nav_menus' => true,
			) );

			foreach ( $filtered_object_taxonomies as $slug => $object ) {
				/* translators: Add new template dialog archive field options. %s: Taxonomy name */
				$options[ $post_type ][ "taxonomy/{$slug}" ] = sprintf( __( '%s archive', 'cmsmasters-elementor' ), $object->label );
			}

			$options = $this->validate_optgroup( $options, $post_type );
		}

		$options += array(
			'archive/date' => __( 'Date archive', 'cmsmasters-elementor' ),
			'archive/author' => __( 'Author archive', 'cmsmasters-elementor' ),
			'page/search' => __( 'Search results', 'cmsmasters-elementor' ),
		);

		$this->print_new_dialog_field_template(
			$options,
			Documents\Archive::ARCHIVE_TEMPLATE_TYPE_META,
			__( 'archive', 'cmsmasters-elementor' )
		);
	}

	public function validate_optgroup( $options, $post_type ) {
		if ( 1 >= count( $options[ $post_type ] ) ) {
			unset( $options[ $post_type ] );
		}

		return $options;
	}

	public function print_new_dialog_field_template( $options, $meta_name, $template_type ) {
		$template_type_class = str_replace( ' ', '-', $template_type );
		$field_id = "elementor-new-template__form__{$template_type_class}-type";
		$field_class = 'elementor-form-field';
		?>
		<div id="<?php echo esc_attr( $field_id ); ?>__wrapper" class="<?php echo esc_attr( $field_class ); ?>">
			<label for="<?php echo esc_attr( $field_id ); ?>" class="<?php echo esc_attr( $field_class ); ?>__label"><?php
				/* translators: Add new template dialog custom field label. %s: Template type */
				printf( esc_html__( 'Select the type of your %s (optional)', 'cmsmasters-elementor' ), $template_type );
			?></label>
			<div class="<?php echo esc_attr( $field_class ); ?>__select__wrapper">
				<select id="<?php echo esc_attr( $field_id ); ?>" class="<?php echo esc_attr( $field_class ); ?>__select" name="<?php echo esc_attr( $meta_name ); ?>">
					<option value=""><?php esc_html_e( 'Select...', 'cmsmasters-elementor' ); ?></option>
					<?php
					foreach ( $options as $value => $text ) {
						$this->print_dialog_template_option( array(
							$value,
							$text,
						) );
					}
					?>
				</select>
			</div>
		</div>
		<?php
	}

	public function print_dialog_template_option( $option ) {
		list( $key, $value ) = $option;

		if ( is_array( $value ) ) {
			printf( '<optgroup label="%s">', esc_attr( $value['label'] ) );

			unset( $value['label'] );

			foreach ( $value as $option_key => $option_value ) {
				$this->print_dialog_template_option( array(
					$option_key,
					$option_value,
				) );
			}

			print( '</optgroup>' );

			return;
		}

		printf( '<option value="%1$s">%2$s</option>', esc_attr( $key ), esc_html( $value ) );
	}

	/**
	 * Register Elementor library documents.
	 *
	 * Register custom Elementor templates library document types.
	 *
	 * Fired by `elementor/documents/register` action.
	 *
	 * @since 1.0.0
	 * @since 1.3.6 Fixed for elementor pro document types.
	 *
	 * @param Documents_Manager $documents_manager Elementor documents manager.
	 */
	public function register_document_types( $documents_manager ) {
		$document_types = array_merge(
			$this->document_types,
			$this->get_elementor_pro_document_types()
		);

		foreach ( $document_types as $document_type => $class_full_name ) {
			$documents_manager->register_document_type( $document_type, $class_full_name );
		}
	}

	/**
	 * Get elementor pro document types.
	 *
	 * @since 1.3.6
	 *
	 * @return array Elementor Pro document types.
	 */
	private function get_elementor_pro_document_types() {
		if ( ! Utils::is_pro() ) {
			return array();
		}

		$document_types = array(
			'widget' => 'ElementorPro\Modules\GlobalWidget\Documents\Widget',
			'popup' => 'ElementorPro\Modules\Popup\Document',
			'section' => 'ElementorPro\Modules\ThemeBuilder\Documents\Section',
			'header' => 'ElementorPro\Modules\ThemeBuilder\Documents\Header',
			'footer' => 'ElementorPro\Modules\ThemeBuilder\Documents\Footer',
			'single' => 'ElementorPro\Modules\ThemeBuilder\Documents\Single',
			'single-post' => 'ElementorPro\Modules\ThemeBuilder\Documents\Single_Post',
			'single-page' => 'ElementorPro\Modules\ThemeBuilder\Documents\Single_Page',
			'archive' => 'ElementorPro\Modules\ThemeBuilder\Documents\Archive',
			'search-results' => 'ElementorPro\Modules\ThemeBuilder\Documents\Search_Results',
			'error-404' => 'ElementorPro\Modules\ThemeBuilder\Documents\Error_404',
			'loop-item' => 'ElementorPro\Modules\LoopBuilder\Documents\Loop',
		);

		if ( class_exists( 'woocommerce' ) ) {
			$document_types['product-post'] = 'ElementorPro\Modules\Woocommerce\Documents\Product_Post';
			$document_types['product'] = 'ElementorPro\Modules\Woocommerce\Documents\Product';
			$document_types['product-archive'] = 'ElementorPro\Modules\Woocommerce\Documents\Product_Archive';
		}

		$document_types['code_snippet'] = 'ElementorPro\Modules\CustomCode\Document';

		return $document_types;
	}

	public function admin_title( $admin_title, $title ) {
		$library_title = $this->get_library_title();

		if ( $library_title ) {
			$admin_title = str_replace( $title, $library_title, $admin_title );
		}

		return $admin_title;
	}

	private function get_library_title() {
		$title = '';

		if ( $this->is_current_screen() ) {
			$current_tab_group = $this->get_current_tab_group();

			if ( $current_tab_group ) {
				$titles = array(
					'cmsmasters' => __( 'Theme Templates', 'cmsmasters-elementor' ),
					'cmsmasters_woo' => __( 'WooCommerce Templates', 'cmsmasters-elementor' ),
					'cmsmasters_tribe_events' => __( 'Tribe Events Templates', 'cmsmasters-elementor' ),
					'cmsmasters_popup' => __( 'Popup Templates', 'cmsmasters-elementor' ),
				);

				$title = Utils::get_if_not_empty( $titles, $current_tab_group, $title );
			}
		}

		return $title;
	}

	private function is_current_screen() {
		global $pagenow, $typenow;

		return 'edit.php' === $pagenow && Source_Local::CPT === $typenow;
	}

	private function get_current_tab_group( $default = '' ) {
		$current_tabs_group = $default;

		$request = Utils::get_if_not_empty( $_REQUEST, Source_Local::TAXONOMY_TYPE_SLUG );

		if ( $request ) {
			$document_type = Plugin::elementor()->documents->get_document_type( $request, '' );

			if ( $document_type ) {
				$current_tabs_group = $document_type::get_property( 'admin_tab_group' );
			}

			return $current_tabs_group;
		}

		return Utils::get_if_not_empty( $_REQUEST, 'tabs_group', $current_tabs_group );
	}

	/**
	 * Builder wrapper.
	 *
	 * Used to replace an empty HTML wrapper for the builder with content area
	 * placeholder for header/footer and etc. template types.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content The content of the builder.
	 *
	 * @return string Content area HTML placeholder or default builder wrapper.
	 */
	public function builder_wrapper( $content ) {
		$post_id = get_the_ID();

		if ( ! $post_id ) {
			return $content;
		}

		$document = $this->get_document( $post_id );

		if ( ! $document || $document->get_content_editable() ) {
			return $content;
		}

		return sprintf(
			'<div class="cmsmasters-template-content-area-placeholder">%s</div>',
			__( 'Content Area', 'cmsmasters-elementor' )
		);
	}

	/**
	 * Get document instance.
	 *
	 * Retrieve document instance of current post.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return false|Base_Document Instance of current document or false.
	 */
	public function get_document( $post_id ) {
		$document = Plugin::elementor()->documents->get( $post_id );

		if ( ! $document || ! $document instanceof Base_Document ) {
			return false;
		}

		return $document;
	}

	/**
	 * Get Addon document types properties.
	 *
	 * Retrieve properties of all the Addon document types.
	 *
	 * @since 1.0.0
	 *
	 * @return array Document types properties.
	 */
	public function get_document_types_properties() {
		$properties = array();

		$document_types = $this->get_document_types();

		foreach ( $document_types as $type => $document ) {
			$properties[ $type ] = $document::get_properties();
		}

		return $properties;
	}

	/**
	 * Get document types
	 *
	 * Retrieves Addon document types list.
	 *
	 * @since 1.0.0
	 *
	 * @return Base_Document[] Document types.
	 */
	public function get_document_types() {
		return $this->document_types;
	}

	public function get_location_available_document_types( $available_category = 'child' ) {
		$available_types = array();

		$document_types = $this->get_document_types();

		foreach ( $document_types as $type => $document ) {
			$category = $document::get_property( 'locations_category' );

			$condition = false;

			switch ( $available_category ) {
				case 'all':
					$condition = true;

					break;
				case 'parent':
					$condition = $available_category === $category;

					break;
				case 'child':
					$condition = in_array( $category, array( 'parent', $available_category ), true );

					break;
			}

			if ( $condition ) {
				$available_types[] = $type;
			}
		}

		return $available_types;
	}

	/**
	 * Filter finder categories.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 * @since 1.5.1 Fixed filter for finder categories in Elementor 3.7.0.
	 *
	 * @param array $categories Finder categories.
	 *
	 * @return array Filtered list of finder categories.
	 */
	public function add_finder_static_items( $categories ) {
		if ( empty( $this->document_keywords ) ) {
			return $categories;
		}

		$categories['general']['items']['saved-templates']['keywords'] = array_merge(
			$categories['general']['items']['saved-templates']['keywords'],
			$this->document_keywords,
			array(
				'search',
				'404',
			)
		);

		if ( version_compare( ELEMENTOR_VERSION, '3.7.0', '<' ) ) {
			$categories['create']['items']['elementor_library']['keywords'] = array_merge(
				$categories['create']['items']['elementor_library']['keywords'],
				$this->document_keywords,
				array(
					'add',
				)
			);
		}

		$categories['general']['items']['cmsmasters-templates'] = array(
			'title' => __( 'Theme Templates', 'cmsmasters-elementor' ),
			'icon' => 'library-save',
			'url' => $this->get_admin_templates_url(),
			'keywords' => array_merge( $this->document_keywords, array(
				'template',
				'library',
				'search',
				'404',
			) ),
		);

		return $categories;
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public function add_finder_dynamic_items( $categories ) {
		if ( empty( $this->document_keywords ) ) {
			return $categories;
		}

		$document_titles = $this->get_document_types_titles();

		foreach ( $this->document_keywords as $keyword ) {
			$admin_url = add_query_arg( array( 'elementor_library_type' => "cmsmasters_{$keyword}" ), $this->get_admin_templates_url() );

			$categories['general']['items'][ "cmsmasters-document-{$keyword}" ] = array(
				/* translators: Elementor finder categories - saved custom template documents finder item. %s: Template document name */
				'title' => sprintf( __( '%s Templates', 'cmsmasters-elementor' ), $document_titles[ "cmsmasters_{$keyword}" ] ),
				'icon' => 'library-save',
				'url' => $admin_url,
				'keywords' => array( $keyword ),
			);

			$create_keywords = array_merge( array( $keyword ), $this->create_keywords );

			if ( 'archive' === $keyword ) {
				$create_keywords = array_merge( $create_keywords, array( 'search' ) );
			}

			if ( 'singular' === $keyword ) {
				$create_keywords = array_merge( $create_keywords, array( '404' ) );
			}

			$categories['create']['items'][ "cmsmasters-template-{$keyword}" ] = array(
				/* translators: Elementor finder categories - add new custom template document finder item'. %s: Template document name */
				'title' => sprintf( __( 'Add New %s', 'cmsmasters-elementor' ), $document_titles[ "cmsmasters_{$keyword}" ] ),
				'icon' => 'plus-circle',
				'url' => "{$admin_url}#add_new",
				'keywords' => $create_keywords,
			);
		}

		return $categories;
	}

	private function get_document_types_titles() {
		$titles = array();

		$document_types = $this->get_document_types();

		foreach ( $document_types as $type => $document ) {
			$titles[ $type ] = $document::get_title();
		}

		return $titles;
	}

}
