<?php
namespace CmsmastersElementor\Modules\Woocommerce\Documents;

use CmsmastersElementor\Modules\Blog\Documents\Entry;
use CmsmastersElementor\Modules\Woocommerce\Module as WooModule;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Document;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Utils as ElementorUtils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSmasters product entry library document.
 *
 * CMSmasters product entry library document handler class is responsible for
 * handling a document of a product entry type.
 *
 * @since 1.0.0
 */
class Product_Entry extends Entry {

	use Woo_Document;

	/**
	 * @since 1.0.0
	 * @since 1.19.3 Added `Add To Cart Button` widget display in the Product Entry template.
	 */
	public static $widgets_visibility = array(
		'Product_Images' => false,
		'Product_Stock' => false,
		'Product_Add_To_Cart_Button' => true,
		'Product_Data_Tabs' => false,
		'Product_Additional_Information' => false,
		'Product_Reviews' => false,
		'Product_Related' => false,
		'Breadcrumbs' => false,
	);

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
		return 'cmsmasters_product_entry';
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
		return __( 'Product Entry', 'cmsmasters-elementor' );
	}

	/**
	 * Get editor panel categories.
	 *
	 * Retrieve the list of categories the element belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Editor panel categories.
	 */
	protected static function get_editor_panel_categories() {
		$categories = array(
			self::WOO_SINGULAR_WIDGETS_CATEGORY => array( 'title' => self::get_title() ),
		);

		if ( Utils::is_pro() ) {
			$categories['woocommerce-elements-single'] = array(
				'title' => __( 'Product', 'cmsmasters-elementor' ),
				'active' => true,
			);
		}

		$categories += parent::get_editor_panel_categories();

		unset( $categories[ self::SINGULAR_WIDGETS_CATEGORY ] );

		if ( Utils::is_pro() ) {
			unset( $categories['theme-elements-single'] );
		}

		return $categories;
	}

	/**
	 * @since 1.0.0
	 */
	protected function register_controls() {
		parent::register_controls();

		$this->update_control(
			'preview_type',
			array( 'type' => Controls_Manager::HIDDEN )
		);
	}

	/**
	 * @since 1.0.0
	 */
	protected function set_default_preview() {
		if ( ! empty( $this->preview_type_default ) ) {
			return;
		}

		$latest_post = get_posts( array(
			'post_type' => WooModule::$post_type,
			'fields' => 'ids',
			'numberposts' => 1,
		) );

		if ( empty( $latest_post ) ) {
			return;
		}

		$this->preview_type_default = sprintf( 'singular/%s', WooModule::$post_type );
		$this->preview_id_default = $latest_post[0];
	}

	/**
	 * Print element with wrapper.
	 *
	 * Used to generate the element final HTML inside user-selected
	 * wrapper tag on the frontend and the editor.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function print_elements_with_wrapper( $elements_data = null ) {
		$template_id = wc_get_loop_prop( WooModule::CONTROL_TEMPLATE_NAME );

		if ( $template_id ) {
			WooModule::remove_template_id_content_product();
		}

		$html_elements = parent::print_elements_with_wrapper( $elements_data );

		if ( $template_id ) {
			WooModule::set_template_id_content_product( $template_id );
		}

		return $html_elements;
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_preview_elements_data() {
		$widget = Plugin::elementor()->widgets_manager->get_widget_types( 'cmsmasters-woo-products' );

		if ( ! $widget ) {
			return;
		}

		return array(
			array(
				'id' => ElementorUtils::generate_random_string(),
				'elType' => 'section',
				'elements' => array(
					array(
						'id' => ElementorUtils::generate_random_string(),
						'elType' => 'column',
						'settings' => array(
							'_column_size' => 100,
						),
						'elements' => array(
							array(
								'id' => ElementorUtils::generate_random_string(),
								'elType' => $widget::get_type(),
								'widgetType' => $widget->get_name(),
								'settings' => array(
									WooModule::CONTROL_TEMPLATE_NAME => $this->get_main_id(),
									'template_layout' => 'custom',
									'rows' => 1,
								),
							),
						),
					),
				),
			),
		);
	}

}
