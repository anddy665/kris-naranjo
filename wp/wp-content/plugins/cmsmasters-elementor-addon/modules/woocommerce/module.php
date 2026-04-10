<?php
namespace CmsmastersElementor\Modules\Woocommerce;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\AjaxWidget\Module as AjaxWidgetModule;
use CmsmastersElementor\Modules\TemplateDocuments\Module as DocumentsModule;
use CmsmastersElementor\Modules\TemplateLocations\Module as LocationsModule;
use CmsmastersElementor\Modules\TemplateLocations\Rules_Manager;
use CmsmastersElementor\Modules\Woocommerce\Documents;
use CmsmastersElementor\Modules\Woocommerce\Rules;
use CmsmastersElementor\Modules\Woocommerce\Widgets;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Modules\PageTemplates\Module as PageTemplatesModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon WooCommerce module.
 *
 * The woocommerce class is responsible for woocommerce module controls integration.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	const CONTROL_TEMPLATE_NAME = 'cmsmasters_template_id';
	const TEMPLATE_MINI_CART = 'cart/mini-cart.php';

	public static $post_type = 'product';

	protected $use_mini_cart_template;

	/**
	 * Get module name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'woocommerce';
	}

	/**
	 * Module activation.
	 *
	 * Check if module is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_active() {
		return class_exists( 'woocommerce' );
	}

	/**
	 * Retrieve widget classes name.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_widgets() {
		$widgets = array(
			'Archive_Description',
			'Archive_Products',

			'Products',
			'Products_Slider',
			'Cart',
			'Woo_Breadcrumbs',
			'Cart_Page',
			'My_Account',
			'Notices',
			'Checkout',
			'Purchase_Summary',
			'Woo_Pages',

			'Product_Title',
			'Product_Image',
			'Product_Images',
			'Product_Price',
			'Product_Rating',
			'Product_Add_To_Cart',
			'Product_Add_To_Cart_Button',
			'Product_Meta',
			'Product_Stock',
			'Product_Data_Tabs',
			'Product_Short_Description',
			'Product_Additional_Information',
			'Product_Reviews',
			'Product_Content',
			'Product_Related',
			'Product_Badge_Sale',
			'Product_Badge_Stock',
			'Product_Categories',
			'Product_Categories_Slider',
		);

		if ( class_exists( 'WPCleverWoosw' ) ) {
			$widgets[] = 'Wpclever_Smart_Wishlist_Counter';
			$widgets[] = 'Wpclever_Smart_Wishlist_Button';
			$widgets[] = 'Wpclever_Smart_Wishlist';
		}

		if ( class_exists( 'WPCleverWoosc' ) ) {
			$widgets[] = 'Wpclever_Smart_Compare_Counter';
			$widgets[] = 'Wpclever_Smart_Compare_Button';
			$widgets[] = 'Wpclever_Smart_Compare_list';
		}

		if ( class_exists( 'WPCleverWoosq' ) ) {
			$widgets[] = 'Wpclever_Smart_Quick_View_Button';
		}

		if ( class_exists( 'FrameWpf' ) ) {
			$widgets[] = 'Wbw_Product_Filter';
		}

		return $widgets;
	}

	/**
	 * Add actions initialization.
	 *
	 * Register actions for the WooCommerce module.
	 *
	 * @since 1.0.0
	 */
	protected function init_actions() {
		$is_action_elementor = ! empty( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'];

		if ( is_admin() && $is_action_elementor ) {
			// On Editor - Register WooCommerce frontend hooks before the Editor init.
			// Priority = 5, in order to allow plugins remove/add their wc hooks on init.
			add_action( 'init', array( $this, 'register_wc_hooks' ), 5 );
		}

		add_action( 'cmsmasters_elementor/documents/locations/register_rules', array( $this, 'register_location_rules' ) );
		add_action( 'elementor/template-library/create_new_dialog_fields', array( $this, 'create_products_archive_field' ) );

		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'maybe_init_cart' ) );

		if ( class_exists( 'FrameWpf' ) ) {
			add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_wbw_product_filter_scripts' ) );
		}

		add_action( 'cmsmasters_elementor/ajax_widget/register', array( $this, 'register_ajax_widget_handlers' ) );

		if ( class_exists( 'WPCleverWpcvs' ) ) {
			add_action( 'admin_init', array( $this, 'update_wpcvs_options' ) );
		}

	}

	/**
	 * Add filters initialization.
	 *
	 * Register filters for the WooCommerce module.
	 *
	 * @since 1.0.0
	 */
	protected function init_filters() {
		add_filter( 'woocommerce_locate_template', array( $this, 'woocommerce_locate_template' ), 11, 2 );

		add_filter( 'cmsmasters_elementor/documents/set_document_types', array( $this, 'set_document_types' ) );
		add_filter( 'cmsmasters_elementor/documents/set_elementor_documents', array( $this, 'set_elementor_documents' ) );
		add_filter( 'cmsmasters_elementor/locations/template_include/location', array( $this, 'set_custom_document_location' ) );
		add_filter( 'cmsmasters_elementor/locations/template_include/page_template', array( $this, 'set_custom_document_template' ), 10, 2 );
		add_filter( 'cmsmasters_elementor/documents/locations/rules/archive/post_type/' . self::$post_type . '/expression', array( $this, 'product_archive_location_rule_expression' ) );
		add_filter( 'cmsmasters_elementor/frontend/settings', array( $this, 'frontend_settings' ) );

		if ( class_exists( 'FrameWpf' ) ) {
			add_filter( 'wpf_getDefaultSettings', function( $defaults ) { 
				$defaults['force_theme_templates'] = 1;
				return $defaults;
			 } );
		}

		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'menu_cart_fragments' ) );
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'update_notices_ajax_add_to_cart_add_fragments' ) );
	}

	/**
	 * Enqueue scripts.
	 *
	 * Enqueue all the wbw product filter preview scripts.
	 *
	 * @since 1.11.0
	 * @access public
	 */
	public function enqueue_wbw_product_filter_scripts() {
		$frame_wpf = new \FrameWpf();

		$isPro = $frame_wpf->_()->isPro();
		$modPath = $frame_wpf->_()->getModule( 'woofilters' )->getModPath();
		$tempPath = $frame_wpf->_()->getModule( 'templates' )->getModPath();

		wp_enqueue_script( 'commonWpf', WPF_JS_PATH . 'common.js', array( 'jquery' ), WPF_VERSION );
		wp_enqueue_script( 'coreWpf', WPF_JS_PATH . 'core.js', array( 'jquery' ), WPF_VERSION ) ;

		wp_enqueue_script( 'tooltipster', $tempPath . 'lib/tooltipster/jquery.tooltipster.min.js', false, WPF_VERSION );
		wp_enqueue_style( 'tooltipster', $tempPath . 'lib/tooltipster/tooltipster.css', false, WPF_VERSION );

		//addCommonAssets
		$options = $frame_wpf->_()->getModule( 'options' )->getModel( 'options' )->getAll();
		wp_enqueue_style( 'frontend.filters', $modPath . 'css/frontend.woofilters.css', false, WPF_VERSION );
		wp_enqueue_script( 'frontend.filters', $modPath . 'js/frontend.woofilters.js', false, WPF_VERSION );
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			$code = 'var isElementorPreview=1;';
			wp_add_inline_script( 'frontend.filters', $code, 'before' );
		}

		if ( isset( $options['content_accessibility'] ) && '1' === $options['content_accessibility']['value'] ) {
			wp_enqueue_style( 'frontend.filters.accessibility', $modPath . 'css/frontend.woofilters.accessibility.css', false, WPF_VERSION );
		}

		wp_enqueue_style( 'frontend.multiselect', $modPath . 'css/frontend.multiselect.css', false, WPF_VERSION );
		wp_enqueue_script( 'frontend.multiselect', $modPath . 'js/frontend.multiselect.js', false, WPF_VERSION );
		$selectedTitle = esc_attr__(( isset($options['selected_title']['value']) && ''!==$options['selected_title']['value'] ) ? $options['selected_title']['value'] : 'selected', 'cmsmasters-elementor');
		wp_add_inline_script( 'frontend.multiselect', "var wpfMultySelectedTraslate = '{$selectedTitle}';", 'before' );
		
		//loadJqueryUi
		wp_enqueue_style( 'jquery-ui', WPF_CSS_PATH . 'jquery-ui.min.css', false, WPF_VERSION );
		wp_enqueue_style( 'jquery-ui.structure', WPF_CSS_PATH . 'jquery-ui.structure.min.css', false, WPF_VERSION );
		wp_enqueue_style( 'jquery-ui.theme', WPF_CSS_PATH . 'jquery-ui.theme.min.css', false, WPF_VERSION );
		wp_enqueue_style( 'jquery-slider', WPF_CSS_PATH . 'jquery-slider.css', false, WPF_VERSION );
		wp_enqueue_script( 'jquery-ui-slider', '', false, WPF_VERSION );
			
		//addPluginCustomStyles
		$a = new \ReqWpf();
		$params = $a->get( 'get' );
		if ( ! is_admin() || ( isset( $params['page'] ) && 'wpf-filters' === $params['page'] ) ) {
			wp_enqueue_style( 'custom.filters', $modPath . 'css/custom.woofilters.css', false, WPF_VERSION );
		}

		//addScriptsContent
		if ( $isPro ) {
			$modPathPRO = $frame_wpf->_()->getModule( 'woofilterpro' )->getModPath();

			wp_enqueue_script( 'frontend.filters.pro', $modPathPRO . 'js/frontend.woofilters.pro.js', array( 'frontend.filters' ), WPF_VERSION, true );

			wp_localize_script( 'frontend.filters.pro', 'wpfTraslate', array(
				'ShowMore'  => __( 'Show More', 'cmsmasters-elementor' ),
				'ShowFewer' => __( 'Show Fewer', 'cmsmasters-elementor' ),
			));

			wp_enqueue_style( 'frontend.filters.pro', $modPathPRO . 'css/frontend.woofilters.pro.css', false, WPF_VERSION );
			wp_enqueue_style( 'custom.filters.pro', $modPathPRO . 'css/custom.woofilters.pro.css', false, WPF_VERSION );
			wp_enqueue_script( 'jquery-ui-autocomplete', '', false, WPF_VERSION );
			wp_enqueue_style( 'jquery-ui-autocomplete', $modPathPRO . 'css/jquery-ui-autocomplete.css', false, WPF_VERSION );
			wp_enqueue_script( 'ion.slider', $modPathPRO . 'js/ion.rangeSlider.min.js', false, WPF_VERSION );
			wp_enqueue_style( 'ion.slider', $modPathPRO . 'css/ion.rangeSlider.css', false, WPF_VERSION );
		
		}
	}

	/**
	 * Initialization woo on backend.
	 *
	 * @since 1.0.0
	 */
	public function register_wc_hooks() {
		wc()->frontend_includes();
	}

	/**
	 * Undocumented function
	 *
	 * Description.
	 *
	 * @since 1.0.0
	 *
	 * @param Rules_Manager $rules_manager
	 */
	public function register_location_rules( $rules_manager ) {
		$rules_manager_general = $rules_manager->get_rule_instance( 'general' );
		$woo_general_rule = new Rules\Woocommerce();

		$rules_manager_general->register_child_rule( $woo_general_rule );

		$rules_manager_archive = $rules_manager->get_rule_instance( 'archive' );
		$woo_search_rule = new Rules\Archive\Product_Search();
		$woo_shop_rule = new Rules\Archive\Shop_Page();

		$rules_manager_archive->register_child_rule( $woo_search_rule );
		$rules_manager_archive->register_child_rule( $woo_shop_rule );
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
	public function create_products_archive_field() {
		/** @var DocumentsModule $documents_module */
		$documents_module = DocumentsModule::instance();

		$options = array();

		$post_type_object = get_post_type_object( self::$post_type );

		$options[ self::$post_type ] = array( 'label' => $post_type_object->label );

		if ( $post_type_object->has_archive ) {
			/* translators: Add new template dialog archive field options. %s: Post type name */
			$options[ self::$post_type ][ 'post_type_archive/' . self::$post_type ] = sprintf( __( '%s archive', 'cmsmasters-elementor' ), $post_type_object->label );
		}

		$post_type_taxonomies_object = get_object_taxonomies( self::$post_type, 'objects' );

		$filtered_object_taxonomies = wp_filter_object_list( $post_type_taxonomies_object, array(
			'public' => true,
			'show_in_nav_menus' => true,
		) );

		foreach ( $filtered_object_taxonomies as $slug => $object ) {
			/* translators: Add new template dialog archive field options. %s: Taxonomy name */
			$options[ self::$post_type ][ "taxonomy/{$slug}" ] = sprintf( __( '%s archive', 'cmsmasters-elementor' ), $object->label );
		}

		$options = $documents_module->validate_optgroup( $options, self::$post_type );

		$options += array( 'page/search' => __( 'Products search results', 'cmsmasters-elementor' ) );
		$options += array( 'page/shop' => __( 'Shop Page', 'cmsmasters-elementor' ) );

		$documents_module->print_new_dialog_field_template(
			$options,
			Documents\Product_Archive::PRODUCTS_ARCHIVE_TEMPLATE_TYPE_META,
			__( 'products archive', 'cmsmasters-elementor' )
		);
	}

	/**
	 * Initialization woo cart.
	 *
	 * @since 1.0.0
	 */
	public function maybe_init_cart() {
		if ( ! is_a( WC()->cart, 'WC_Cart' ) ) {
			$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );

			WC()->session = new $session_class();
			WC()->session->init();

			WC()->cart = new \WC_Cart();
			WC()->customer = new \WC_Customer( get_current_user_id(), true );
		}
	}

	/**
	 * Add handlers for ajax widget.
	 *
	 * @since 1.0.0
	 */
	public function register_ajax_widget_handlers( AjaxWidgetModule $ajax_widget ) {
		$ajax_widget->add_handler( 'cmsmasters-woo-archive-products', array( $this, 'render_ajax_widget' ), false );
		$ajax_widget->add_handler( 'cmsmasters-woo-products', array( $this, 'render_ajax_widget' ), false );
		
		add_action( "wp_ajax_ajax_widget_cmsmasters-woo-product-add-to-cart", array( $this, 'ajax_add_to_cart' ), false );
		add_action( "wp_ajax_nopriv_ajax_widget_cmsmasters-woo-product-add-to-cart", array( $this, 'ajax_add_to_cart' ), false );
	}

	/**
	 * Add ajax add to cart.
	 *
	 * @since 1.11.8
	 */
	public function ajax_add_to_cart() {
		ob_start();

		if ( ! check_ajax_referer( 'cmsmasters_ajax_widget', false, false ) ) {
			wp_send_json_error( array( 'message' => 'Nonce code has not been installed or does not match.' ), 400 );
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['product_id'] ) ) {
			return;
		}

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$product           = wc_get_product( $product_id );
		$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		$product_status    = get_post_status( $product_id );
		$variation_id      = 0;
		$variation         = array();

		if ( $product && 'variation' === $product->get_type() ) {
			$variation_id = $product_id;
			$product_id   = $product->get_parent_id();
			$variation    = $product->get_variation_attributes();
		}

		// CMSMasters. Started add parameters formation.
		if ( 'simple' === $product->get_type() ) {
			if ( function_exists( 'wc_add_to_cart_message' ) ) {
				wc_add_to_cart_message( array( $product_id => $quantity ), true );
			}

			if ( false === WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) ) {
				// If there was an error adding to the cart, redirect to the product page to show any errors.
				$data = array(
					'error'       => true,
					'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
				);
	
				wp_send_json( $data );
			}
		}

		if ( 'variable' === $product->get_type() ) {
			$variation_id = $_POST['variation_id'];

			if ( function_exists( 'wc_clear_notices' ) ) {
				wc_clear_notices();
			}

			if ( function_exists( 'wc_add_to_cart_message' ) ) {
				wc_add_to_cart_message( array( $variation_id => $quantity ), true );
			}

			foreach ( $_POST as $item_name => $item_value ) {
				if ( false !== strpos(  $item_name, 'attribute_' ) ) {
					$variation[ $item_name ] = $item_value;
				}
			}
		}

		if ( 'grouped' === $product->get_type() ) {
			if ( function_exists( 'wc_clear_notices' ) ) {
				wc_clear_notices();
			}

			if ( function_exists( 'wc_add_to_cart_message' ) ) {
				$added_to_cart     = array();
				$was_added_to_cart = false;
				$items             = isset( $_POST['quantity'] ) && is_array( $_POST['quantity'] ) ? wp_unslash( $_POST['quantity'] ) : array();

				if ( ! empty( $items ) ) {
					foreach ( $items as $item => $quantity ) {
						$was_added_to_cart      = true;
						$added_to_cart[ $item ] = $quantity;
					}

					if ( ! $was_added_to_cart ) {
						wc_add_notice( __( 'Please choose the quantity of items you wish to add to your cart&hellip;', 'cmsmasters-elementor' ), 'error' );
					} elseif ( $was_added_to_cart ) {
						wc_add_to_cart_message( $added_to_cart, true );
					}
				} elseif ( $product_id ) {
					/* Link on product archives */
					wc_add_notice( __( 'Please choose a product to add to your cart&hellip;', 'cmsmasters-elementor' ), 'error' );
				}
			}
		}
		// CMSMasters. Ended add parameters formation.

		// CMSMasters. Removed the function check and run `false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation )`.
		if ( $passed_validation && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			// CMSMasters. Changed `self` on the \WC_AJAX parent class.
			if ( class_exists( 'WC_AJAX' ) ) {
				\WC_AJAX::get_refreshed_fragments( $product_id, $quantity );
			}

		} else {

			// If there was an error adding to the cart, redirect to the product page to show any errors.
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			);

			wp_send_json( $data );
		}
		// phpcs:enable
	}

	/**
	 * Added new field in add to cart add fragment.
	 *
	 * @since 1.11.8
	 */
	public function update_notices_ajax_add_to_cart_add_fragments( $fragments ) {
		$all_notices = WC()->session->get( 'wc_notices', array() );
		$notice_types = apply_filters( 'woocommerce_notice_types', array( 'error', 'success', 'notice' ) );

		ob_start();

		foreach ( $notice_types as $notice_type ) {
			if ( function_exists( 'wc_notice_count' ) && function_exists( 'wc_get_template' ) ) {
				if ( wc_notice_count( $notice_type ) > 0 ) {
					wc_get_template( "notices/{$notice_type}.php", array(
						'notices' => array_filter( $all_notices[ $notice_type ] ),
					) );
				}
			}
		}

		$fragments['notices_html'] = ob_get_clean();

		wc_clear_notices();

		return $fragments;
	}

	/**
	 * Render widgets on Ajax request.
	 *
	 * Sends HTML to frontend
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @return string
	 */
	public function render_ajax_widget( $ajax_vars, Widgets\Products $widget ) {
		ob_start();

		$widget->render_ajax( $ajax_vars );

		return ob_get_clean();
	}

	/**
	 * Return the path of the template to include if it exists.
	 *
	 * @param string $template
	 * @param string $template_name
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function woocommerce_locate_template( $template, $template_name ) {
		$plugin_path = CMSMASTERS_ELEMENTOR_MODULES_PATH . 'woocommerce/wc-templates/';

		if ( file_exists( $plugin_path . $template_name ) ) {
			$template = $plugin_path . $template_name;
		}

		return $template;
	}

	/**
	 * Set woocommerce module document.
	 *
	 * Fired by `cmsmasters_elementor/documents/set_document_types` Addon filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function set_document_types( $document_types ) {
		$module_document_types = array(
			'cmsmasters_product_singular' => Documents\Product_Singular::get_class_full_name(),
			'cmsmasters_product_archive' => Documents\Product_Archive::get_class_full_name(),
			'cmsmasters_product_entry' => Documents\Product_Entry::get_class_full_name(),
			'woo-post' => Documents\Woo\Product_Post::get_class_full_name(),
		);

		$document_types = array_merge( $document_types, $module_document_types );

		return $document_types;
	}

	/**
	 * Set Elementor documents.
	 *
	 * @param string[] $elementor_documents
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function set_elementor_documents( $elementor_documents ) {
		$elementor_documents[] = 'woo-post';

		return $elementor_documents;
	}

	public function set_custom_document_location( $location ) {
		/** @var LocationsModule $locations_module */
		$locations_module = LocationsModule::instance();

		$is_shop = $locations_module->get_locations_manager()->verify_location_expression( 'shop_page' );

		if ( empty( $location ) && $is_shop ) {
			$location = 'cmsmasters_archive';
		}

		return $location;
	}

	public function set_custom_document_template( $page_template, $location ) {
		if ( empty( $page_template ) && 'cmsmasters_singular' === $location && is_product() ) {
			/** @var PageTemplatesModule $page_templates_module */
			$page_templates_module = Plugin::elementor()->modules_manager->get_modules( 'page-templates' );

			$page_template = $page_templates_module::TEMPLATE_HEADER_FOOTER;
		}

		return $page_template;
	}

	public function product_archive_location_rule_expression() {
		return is_shop() || is_product_taxonomy() || Utils::is_search_product();
	}

	/**
	 * Filter frontend settings.
	 *
	 * Filters the Addon settings for elementor frontend.
	 *
	 * Fired by `cmsmasters_elementor/frontend/settings` Addon action hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Frontend settings.
	 *
	 * @return array Filtered frontend settings.
	 */
	public function frontend_settings( $settings ) {
		return array_replace_recursive(
			array(
				'woocommerce' => array( 'default_orderby' => static::get_default_catalog_orderby() ),
				'i18n' => array(
					'cmsmasters_template_id' => __( 'Template', 'cmsmasters-elementor' ),
				),
			),
			$settings
		);
	}

	/**
	 * Refresh the Menu Cart button and items counter.
	 * The mini-cart itself will be rendered by WC functions.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param $fragments
	 *
	 * @return array
	 */
	public function menu_cart_fragments( $fragments ) {
		$has_cart = is_a( WC()->cart, 'WC_Cart' );

		if ( $has_cart ) {
			$fragments['.elementor-widget-cmsmasters-woo-cart__button-counter'] = Utils::get_ob_html( function() {
				Widgets\Cart::get_counter_inner();
			} );

			$fragments['.elementor-widget-cmsmasters-woo-cart__subtotal'] = Utils::get_ob_html( function() {
				Widgets\Cart::get_subtotal_inner();
			} );

			$fragments['.cmsmasters-menu-cart'] = Utils::get_ob_html( function() {
				woocommerce_mini_cart();
			} );
		}

		return $fragments;
	}

	/**
	 * Retrieve default order.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public static function get_default_catalog_orderby() {
		return apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
	}

	/**
	 * Set template instead woo template.
	 *
	 * @since 1.0.0
	 */
	public static function set_template_id_content_product( $template_id ) {
		if ( ! $template_id ) {
			return;
		}

		wc_set_loop_prop( static::CONTROL_TEMPLATE_NAME, $template_id );

		add_filter( 'wc_get_template_part', array( get_called_class(), 'template_part_content_product' ), 10, 3 );
	}

	/**
	 *
	 * Filters for woocommerce template.
	 *
	 * Redirects the path to the content template.
	 *
	 * Fired by `wc_get_template_part` filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function template_part_content_product( $template, $slug, $name ) {
		if ( 'content' === $slug && 'product' === $name ) {
			return CMSMASTERS_ELEMENTOR_MODULES_PATH . 'woocommerce/wc-templates/content-product.php';
		}

		return $template;
	}

	/**
	 * Remove filter for woo template.
	 *
	 * @since 1.0.0
	 */
	public static function remove_template_id_content_product() {
		if ( wc_get_loop_prop( static::CONTROL_TEMPLATE_NAME ) ) {
			wc_set_loop_prop( static::CONTROL_TEMPLATE_NAME, null );
		}

		remove_filter( 'wc_get_template_part', array( get_called_class(), 'template_part_content_product' ), 10 );
	}

	/**
	 * Get product review HTML.
	 *
	 * Uses as replacement to WordPress comment method.
	 *
	 * @since 1.0.0
	 */
	public function product_review( $comment, $args, $depth ) {
		$parent_class = 'cmsmasters-product-review';

		$data = apply_filters( 'cmsmasters_elementor/widgets/cmsmasters-product-review/template_variables', array() );

		$comment_meta = $data['comment_meta'];
		$rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );

		echo '<li id="li-comment-' . get_comment_ID() . '" class="' . join( ' ', get_comment_class( $parent_class ) ) . '">' .
			'<div id="comment-' . get_comment_ID() . '" class="' . esc_attr( $parent_class ) . '__body comment-body">' .
				'<div class="' . esc_attr( $parent_class ) . '__outer">' .
					'<div class="' . esc_attr( $parent_class ) . '__info">' .
						'<div class="' . esc_attr( $parent_class ) . '__info-inner">' .
							'<figure class="' . esc_attr( $parent_class ) . '__avatar">' .
								get_avatar( $comment->comment_author_email, 170, get_option( 'avatar_default' ) ) .
							'</figure>' .
							'<h4 class="' . esc_attr( $parent_class ) . '__author fn">' .
								get_comment_author_link() . $comment_meta['author_text_after'] .
							'</h4>';

		if ( 'inline' === $data['settings']['custom_date_position'] ) {
			$this->get_date_html( $comment_meta, $parent_class );
		}

		if ( 'inline' === $data['settings']['custom_rating_position'] ) {
			$this->get_star_rating( $rating, $data, $parent_class );
		}

		echo '</div>';

		if ( 'block' === $data['settings']['custom_date_position'] ) {
			$this->get_date_html( $comment_meta, $parent_class );
		}

		echo '</div>';

		if ( 'block' === $data['settings']['custom_rating_position'] ) {
			$this->get_star_rating( $rating, $data, $parent_class );
		}

		echo '<div class="' . esc_attr( $parent_class ) . '__content comment-content">';

		comment_text();

		if ( '0' === $comment->comment_approved ) {
			echo '<p>' .
				'<em>' . esc_html__( 'Your comment is awaiting moderation.', 'cmsmasters-elementor' ) . '</em>' .
			'</p>';
		}

		echo '</div>';

		echo '</div>' .
		'</div>';
	}

	/**
	 * Get review date html.
	 *
	 * @since 1.0.0
	 *
	 * @param array $comment_meta Array of comment data from widget file.
	 * @param string $parent_class Parent`s widget class.
	 */
	public function get_date_html( $comment_meta, $parent_class ) {
		if ( 'disable' === $comment_meta['date_format'] ) {
			return;
		}

		echo '<div class="' . esc_attr( $parent_class ) . '__date-wrap">' .
			$comment_meta['date_icon'];

		if ( $comment_meta['human_readable'] ) {
			$this->get_comment_human_time( get_comment_ID() );
		} else {
			echo '<abbr class="' . esc_attr( $parent_class ) . '__date published" title="' . get_comment_date() . '">' .
				sprintf(
					'%1$s %3$s %2$s',
					get_comment_date(),
					( 'yes' === $comment_meta['time_enable'] ) ? get_comment_time() : '',
					( 'yes' === $comment_meta['time_enable'] ) ? $comment_meta['date_separator'] : ''
				) .
			'</abbr>';
		}

		echo '</div>';
	}

	/**
	 * Get age of review in "%s ago" mask.
	 *
	 * @since 1.0.0
	 *
	 * @param string $comment_id ID of current comment.
	 */
	public function get_comment_human_time( $comment_id ) {
		printf(
			/* translators: Addon WooCommerce comments template get comment time ago text. %s: time ago */
			__( '%s ago', 'cmsmasters-elementor' ),
			human_time_diff(
				get_comment_date( 'U', $comment_id ),
				current_time( 'mysql' )
			)
		);
	}

	/**
	 * Get star rating.
	 *
	 * Retrieves star rating by using 'woocommerce_product_get_rating_html' filter.
	 *
	 * @since 1.0.0
	 */
	public function get_star_rating( $rating, $data, $parent_class ) {
		if ( $rating && wc_review_ratings_enabled() ) {
			echo '<div class="' . esc_attr( $parent_class ) . '__rating">';

			if ( 0 < $rating ) {
				/* translators: Addon WooCommerce comments template rating aria-label text. %s: rating */
				$label = sprintf( __( 'Rated %s out of 5', 'cmsmasters-elementor' ), $rating );
				$html = '<div class="cmsmasters_star_rating" role="img" aria-label="' . esc_attr( $label ) . '">' .
					$this->get_star_rating_html( $rating, $data ) .
				'</div>';
			}

			echo apply_filters( 'woocommerce_product_get_rating_html', $html, $rating, 0 );

			echo '</div>';
		}
	}

	/**
	 * Get star rating html.
	 *
	 * Retrieves star rating html.
	 *
	 * @since 1.0.0
	 */
	public function get_star_rating_html( $rating, $data, $count = 0 ) {
		$rating_width = $rating / 5 * 100;

		$html = '<div class="cmsmasters_star_trans_wrap">' .
			$this->get_star_rating_item_html( $data, 'rating_empty' ) .
		'</div>' .
		"<div class=\"cmsmasters_star_color_wrap\" style=\"width: {$rating_width}%\">" .
			'<div class="cmsmasters_star_color_inner">' .
				$this->get_star_rating_item_html( $data, 'rating_filled' ) .
			'</div>' .
		'</div>';

		return apply_filters( 'woocommerce_get_star_rating_html', $html, $rating, $count );
	}

	/**
	 * Get star rating item html.
	 *
	 * Retrieves stars with icon, that selected in control.
	 *
	 * @since 1.0.0
	 */
	public function get_star_rating_item_html( $data, $icon ) {
		$html = '';

		foreach ( range( 0, 4 ) as $i ) {
			$html .= $data['comment_meta'][ $icon ];
		}

		return $html;
	}

	public static function add_product_post_class( $classes ) {
		$classes[] = 'product';

		return $classes;
	}

	public static function add_products_post_class_filter() {
		add_filter( 'post_class', array( get_called_class(), 'add_product_post_class' ) );
	}

	public static function remove_products_post_class_filter() {
		remove_filter( 'post_class', array( get_called_class(), 'add_product_post_class' ) );
	}

	/**
	 * Updating default options for WPC Variation Swatches plugin.
	 *
	 * @since 1.11.0
	 *
	 * @return string Hex color.
	 */
	public function update_wpcvs_options() {
		if ( 'update' === get_option( 'cmsmasters_wpcvs_variations_update_option' ) ) {
			return;
		}

		$wpcvs_settings = get_option( 'wpcvs_settings', array() );

		$wpcvs_settings['button_default'] = 'yes';
		$wpcvs_settings['second_click'] = 'yes';
		$wpcvs_settings['tooltip_library'] = 'none';
		$wpcvs_settings['tooltip_position'] = 'top';
		$wpcvs_settings['style'] = 'square';
		$wpcvs_settings['archive_enable'] = 'no';
		$wpcvs_settings['archive_position'] = 'before';
		$wpcvs_settings['archive_limit'] = '10';
		$wpcvs_settings['archive_product'] = '';
		$wpcvs_settings['archive_image'] = '';
		$wpcvs_settings['archive_atc'] = '';
		$wpcvs_settings['archive_atc_text'] = '';

		update_option( 'wpcvs_settings', $wpcvs_settings );

		update_option( 'cmsmasters_wpcvs_variations_update_option', 'update' );
	}

}
