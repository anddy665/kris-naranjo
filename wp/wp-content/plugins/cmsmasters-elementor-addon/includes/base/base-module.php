<?php
namespace CmsmastersElementor\Base;

use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Core\Base\Module as ElementorBaseModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon base module class.
 *
 * An abstract class to register and manage new Addon modules.
 *
 * This class extends the `Elementor\Core\Base\Module` class to inherit
 * his properties and methods, and must be extended in order to
 * register new Elementor modules.
 *
 * @since 1.0.0
 */
abstract class Base_Module extends ElementorBaseModule {

	/**
	 * Module features.
	 *
	 * Holds the module features.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $features = array();

	/**
	 * Get widgets.
	 *
	 * Retrieve the modules widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_widgets() {
		return array();
	}

	/**
	 * Base modules class constructor.
	 *
	 * Initializing the Addon base modules class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// $this->set_reflection();

		$this->init_base_actions();

		$this->init_actions();
		$this->init_filters();
	}

	/**
	 * Base modules class constructor.
	 *
	 * Initializing the Addon base modules class.
	 *
	 * @since 1.0.0
	 * @since 1.6.5 Fixed elementor deprecation.
	 * @since 1.11.1 Add lazyload widget functionality.
	 */
	private function init_base_actions() {
		add_action( 'elementor/widgets/register', array( $this, 'init_widgets' ) );

		add_action( 'cmsmasters_elementor/ajax_widget/register', array( $this, 'lazyload_widget_register_ajax' ) );
	}

	/**
	 * Init widgets.
	 *
	 * Add modules widgets to Elementor widgets manager.
	 *
	 * Fired by `elementor/widgets/register` Elementor action hook.
	 *
	 * @since 1.0.0
	 * @since 1.6.5 Fixed elementor deprecation.
	 */
	public function init_widgets() {
		$widget_manager = Plugin::elementor()->widgets_manager;

		foreach ( $this->get_widgets() as $widget ) {
			$class_name = $this->get_reflection()->getNamespaceName() . '\\Widgets\\' . $widget;

			$widget_manager->register( new $class_name() );
		}
	}

	/**
	 * Lazyload widget register ajax.
	 *
	 * @since 1.11.1
	 * @since 1.17.4 Added check nonce trigger.
	 *
	 * @param object $ajax_widget Ajax widget.
	 */
	public function lazyload_widget_register_ajax( $ajax_widget ) {
		$ajax_widget->add_handler( 'lazyload_widget_ajax_render_content', array( $this, 'lazyload_widget_ajax_render_content' ), false );
	}

	/**
	 * Lazyload widget ajax render content.
	 *
	 * @since 1.11.1
	 * @since 1.18.0 Fixed custom image sizes.
	 *
	 * @param array $ajax_vars Ajax vars.
	 * @param object $widget Widget.
	 *
	 * @return string Rendered widget content.
	 */
	public function lazyload_widget_ajax_render_content( $ajax_vars, $widget ) {
		if ( ! method_exists( $widget, 'render' ) ) {
			return;
		}

		$post_id = Utils::get_document_id();

		global $post;

		$post = get_post( $post_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		setup_postdata( $post );

		// for applying custom image sizes in lazyload widget.
		add_filter( 'image_resize_dimensions', array( $this, 'image_resize_dimensions' ), 10, 5 );

		ob_start();

		$widget->render();

		wp_reset_postdata();

		return wp_unslash( ob_get_clean() );
	}


	/**
	 * Filtered image resize dimensions.
	 *
	 * @since 1.18.0
	 */
	public function image_resize_dimensions( $payload, $orig_w, $orig_h, $dest_w, $dest_h, $crop = false ) {
		if ( function_exists( 'bfi_image_resize_dimensions' ) ) {
			$payload = bfi_image_resize_dimensions( $payload, $orig_w, $orig_h, $dest_w, $dest_h, $crop );
		}

		return $payload;
	}

	/**
	 * Init actions.
	 *
	 * Initialize module actions.
	 *
	 * @since 1.0.0
	 */
	protected function init_actions() { }

	/**
	 * Init filters.
	 *
	 * Initialize module filters.
	 *
	 * @since 1.0.0
	 */
	protected function init_filters() { }

	/**
	 * Add module feature.
	 *
	 * Add new feature to the current module.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Feature ID.
	 * @param mixed $class The feature class.
	 */
	public function add_feature( $id, $class_name ) {
		$this->features[ $id ] = $class_name;
	}

	/**
	 * Get module features.
	 *
	 * Retrieve the array of module features.
	 *
	 * @since 1.0.0
	 *
	 * @return array The module features.
	 */
	public function get_features() {
		return $this->features;
	}

	/**
	 * Get module feature.
	 *
	 * Retrieve the module feature.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Feature ID.
	 *
	 * @return mixed The feature class, or `false` if the feature doesn't exist.
	 */
	public function get_feature( $id, $parameters = null ) {
		if ( ! isset( $this->features[ $id ] ) ) {
			return false;
		}

		$handler = $this->features[ $id ];

		// /** @var Base_Field $field_handler */
		$feature_handler = new $handler( $parameters );

		if ( ! $feature_handler ) {
			return false;
		}

		return $feature_handler;
	}

}
