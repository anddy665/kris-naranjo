<?php
namespace CmsmastersElementor\Modules\AjaxWidget\Classes;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Modules\AjaxWidget\Module as AjaxWidgetModule;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;
use Elementor\Core\Base\Document;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Ajax Action Handler.
 *
 * @since 1.0.0
 */
class Ajax_Action_Handler {

	/**
	 * Widget Name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $widget_name;

	/**
	 * Action name with prefix Widget Name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $action_name;

	/**
	 * Callback function.
	 *
	 * Callback function of ajax-request.
	 *
	 * @since 1.0.0
	 *
	 * @var callable.
	 */
	private $callback;

	/**
	 * Check nonce trigger.
	 *
	 * @since 1.17.4
	 *
	 * @var bool
	 */
	private $check_nonce = true;

	/**
	 * Ajax Action Handler class constructor.
	 *
	 * Initializing the class.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 * @since 1.17.4 Added check nonce trigger.
	 */
	public function __construct( $widget_name, $callback, $check_nonce = true ) {
		$this->widget_name = $widget_name;
		$this->action_name = "ajax_widget_{$this->widget_name}";
		$this->callback = $callback;
		$this->check_nonce = $check_nonce;

		$this->add_actions();
	}

	/**
	 * Add actions initialization.
	 *
	 * Register actions.
	 *
	 * @since 1.0.0
	 */
	private function add_actions() {
		add_action( "wp_ajax_{$this->action_name}", array( $this, 'get_widget_instance_ajax' ) );
		add_action( "wp_ajax_nopriv_{$this->action_name}", array( $this, 'get_widget_instance_ajax' ) );

		add_action( "cmsmasters_elementor/widget/{$this->widget_name}/after_add_attributes", array( $this, 'add_attribute_document_id' ) );
	}

	/**
	 * Prepares and initializes the widget.
	 *
	 * @since 1.0.0
	 */
	public function get_widget_instance_ajax() {
		/** @var AjaxWidgetModule $ajax_widget_module */
		$ajax_widget_module = AjaxWidgetModule::instance();
		$ajax_nonce_name = $ajax_widget_module->get_nonce_name();

		if ( $this->check_nonce && ! check_ajax_referer( $ajax_nonce_name, false, false ) ) {
			wp_send_json_error( array( 'message' => 'Nonce code has not been installed or does not match.' ), 400 );
		}

		if ( ! Utils::is_ajax() ) {
			wp_send_json_error( array( 'message' => 'Only for ajax request.' ), 400 );
		}

		$widget_id = Utils::get_if_isset( $_REQUEST, 'widget_id' );
		$document_id = Utils::get_if_isset( $_REQUEST, 'document_id' );

		if ( ! $widget_id || ! $document_id ) {
			self::send_required_fields_json_error();
		}

		$elementor = Plugin::elementor();
		$documents_manager = $elementor->documents;
		$document = $documents_manager->get( $document_id );

		if ( ! $document || ! $document instanceof Document ) {
			wp_send_json_error( array( 'message' => 'Document not found.' ), 404 );
		}

		/**
		 * Before instance widget.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cmsmasters_elementor/ajax_widget/before' );

		$documents_manager->switch_to_document( $document );

		$element_data = Utils::get_if_isset( $_REQUEST, 'element_data', array() );

		if ( empty( $element_data ) ) {
			$element_data = Utils::find_widget_elements_by_id( $document->get_elements_data(), $widget_id );
		}

		if ( empty( $element_data ) ) {
			wp_send_json_error( array( 'message' => 'Widget Data not found.' ), 404 );
		}

		$widget_obj = $elementor->elements_manager->create_element_instance( $element_data );

		if ( ! $widget_obj || ! $widget_obj instanceof Base_Widget ) {
			wp_send_json_error( array( 'message' => 'Widget not found.' ), 404 );
		}

		$ajax_vars = Utils::get_if_isset( $_REQUEST, 'ajax_vars', array() );

		$callback_result = call_user_func_array( $this->callback, array(
			$ajax_vars,
			$widget_obj,
			$this,
		) );

		$documents_manager->restore_document();

		/**
		 * After instance widget.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cmsmasters_elementor/ajax_widget/after' );

		wp_send_json_success( $callback_result );
	}

	/**
	 * Send error about required fields.
	 *
	 * @since 1.0.0
	 */
	public static function send_required_fields_json_error() {
		wp_send_json_error( array( 'message' => 'Required fields have not been added' ), 400 );
	}

	/**
	 * Add document id attribute to widget container.
	 *
	 * Fired by `cmsmasters_elementor/widget/{widget_name}/after_add_attributes` action.
	 *
	 * @since 1.0.0
	 */
	public function add_attribute_document_id( Base_Widget $element ) {
		$document_id = Utils::get_document_id();

		if ( ! $document_id ) {
			return;
		}

		$element->add_render_attribute( '_wrapper', 'data-document-id', $document_id );
	}

}
