<?php
namespace CmsmastersElementor\Modules\Mailchimp;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\AjaxWidget\Module as AjaxWidgetModule;
use CmsmastersElementor\Modules\AjaxWidget\Classes\Ajax_Action_Handler;
use CmsmastersElementor\Modules\Mailchimp\Widgets\Mailchimp;
use CmsmastersElementor\Modules\Settings\Settings_Page;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters Elementor mailchimp module.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	const OPTION_NAME_API_KEY = 'mailchimp_api_key';

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-mailchimp';
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		return array( 'Mailchimp' );
	}

	/**
	 * Add actions initialization.
	 *
	 * Register actions for the Mailchimp module.
	 *
	 * @since 1.0.0
	 */
	public function init_actions() {
		add_action( 'cmsmasters_elementor/ajax_widget/register', array( $this, 'register_ajax_widget' ) );

		add_action( 'update_option_elementor_' . self::OPTION_NAME_API_KEY, array( $this, 'clear_cache' ), 10, 2 );

		if ( is_admin() ) {
			add_action( 'elementor/admin/after_create_settings/' . Settings_Page::PAGE_ID, array( $this, 'register_admin_fields' ), 100 );
		}
	}

	public function clear_cache() {
		global $wpdb;

		$prefix = join( '_', array( 'cmsmasters-mailchimp', 'cache' ) );

		$wpdb->query( $wpdb->prepare( "DELETE FROM `{ $wpdb->options }` WHERE `option_name` LIKE %s", "_transient_timeout_{ $prefix }%" ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM `{ $wpdb->options }` WHERE `option_name` LIKE %s", "_transient_{ $prefix }%" ) );
	}

	/**
	 * Register admin fields.
	 *
	 * Register api fields for mailchimp widget.
	 *
	 * @since 1.0.0
	 *
	 */
	public function register_admin_fields( Settings_Page $settings ) {
		$settings->add_section( 'cmsmasters', 'mailchimp', array(
			'callback' => function () {
				echo '<br><hr><br><h2>' . esc_html__( 'Mailchimp', 'cmsmasters-elementor' ) . '</h2>';
			},
			'fields' => array(
				self::OPTION_NAME_API_KEY => array(
					'label' => __( 'API Key', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
						'desc' => sprintf( __( 'Mailchimp', 'cmsmasters-elementor' ) . '. %s.',
							sprintf( '<a href="%1$s" target="_blank">%2$s</a>',
								'https://mailchimp.com/help/about-api-keys/',
								__( 'Get API Key', 'cmsmasters-elementor' )
							)
						),
					),
				),
			),
		) );
	}

	/**
	 * Register ajax widget.
	 *
	 * Retrieve and processes data mailchimp from.
	 *
	 * @since 1.0.0
	 *
	 */
	public function register_ajax_widget( AjaxWidgetModule $ajax_widget ) {
		$ajax_widget->add_handler( 'cmsmasters-mailchimp', array( $this, 'render_ajax_mailchimp' ), false );
	}

	/**
	 * Render ajax form.
	 *
	 * Retrieve and processes data mailchimp from.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public function render_ajax_mailchimp( $ajax_vars, Mailchimp $widget_obj, Ajax_Action_Handler $ajax_widget ) {
		$lists_id = $widget_obj->get_settings_for_display( 'mailchimp_list' );

		if ( empty( $lists_id ) || empty( $ajax_vars['fields'] ) ) {
			$ajax_widget->send_required_fields_json_error();
		}

		if ( ! is_array( $lists_id ) ) {
			$lists_id = explode( ' ', $lists_id );
		}

		foreach ( $lists_id as $index => $list_id ) {
			$settings = $widget_obj->get_settings_for_display();
			$fields = wp_parse_args( $ajax_vars['fields'] );
			$message_text = $this->message( $widget_obj );

			$email = isset( $fields['email'] ) ? sanitize_email( strtolower( $fields['email'] ) ) : '';
			$url = $this->url() . $list_id . '/members/' . md5( $email );

			$response = wp_remote_get(
				esc_url_raw( $url ),
				$this->response( $ajax_vars, $widget_obj, 'GET', false )
			);

			$body = json_decode( wp_remote_retrieve_body( $response ) );

			if ( ( 'subscribe' === $fields['action-form'] && isset( $fields['action-form'] ) ) || ! isset( $fields['action-form'] ) ) {
				if ( 'subscribed' !== $body->status && ! $settings['double_optin'] || ( ( 404 !== $body->status && $settings['double_optin'] ) && 'subscribed' !== $body->status ) ) {
					$response = wp_remote_post(
						esc_url_raw( $url ),
						$this->response( $ajax_vars, $widget_obj, 'PUT', true )
					);

					$message = $message_text['successfully'];
				} elseif ( 404 === $body->status && $settings['double_optin'] ) {
					$response = wp_remote_post(
						esc_url_raw( $url ),
						$this->response( $ajax_vars, $widget_obj, 'PUT', true )
					);

					$message = $message_text['successfully_opt_in'];
				} elseif ( 'subscribed' === $body->status && ! $settings['update_existing'] ) {
					$message_notice = $message_text['already_subscribed'];
				} elseif ( 'subscribed' === $body->status && $settings['update_existing'] ) {
					$response = wp_remote_post(
						esc_url_raw( $url ),
						$this->response( $ajax_vars, $widget_obj, 'PUT', true )
					);

					$message = $message_text['update'];
				}
			} elseif ( 'unsubscribe' === $fields['action-form'] && isset( $fields['action-form'] ) ) {
				if ( 404 === $response['response']['code'] && 'unsubscribe' === $fields['action-form'] ) {
					$response = wp_remote_post(
						esc_url_raw( $url ),
						$this->response( $ajax_vars, $widget_obj, 'PUT', true )
					);

					$message_notice = $message_text['not_subscribed'];
				} elseif ( 200 === $response['response']['code'] && 'unsubscribe' === $fields['action-form'] ) {
					$response = wp_remote_post(
						esc_url_raw( $url ),
						$this->response( $ajax_vars, $widget_obj, 'PUT', true )
					);

					$message = $message_text['unsubscribed'];
				}
			}

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( array( 'message' => 'Error.' ), 500 );
			}

			$body = json_decode( wp_remote_retrieve_body( $response ) );

			if ( empty( $body ) ) {
				wp_send_json_error( array( 'message' => 'Error.' ), 500 );
			}

			if ( 200 !== $response['response']['code'] ) {
				wp_send_json_error(
					array(
						'message_error' => $message_text['general'],
					),
					$body->status
				);
			} else {
				wp_send_json_success( array(
					'message' => $message,
					'message_notice' => $message_notice,
				) );
			}
		}
	}

	/**
	 * Get response.
	 *
	 * Receive or send data.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function response( $ajax_vars, $widget_obj, $method = 'PUT', $body_resp = true ) {
		$arg = array(
			'method' => $method,
			'headers' => array(
				'Content-Type' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode( 'user:' . $this->api_key() ), // TODO: base64_encode not allowed function, check if can be replaced with similar
			),
		);

		$body = $this->body( $ajax_vars, $widget_obj );

		if ( $body_resp ) {
			$arg['body'] = wp_json_encode( $body );
		}

		return $arg;
	}

	/**
	 * Get body.
	 *
	 * Defines body settings and data.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function body( $ajax_vars, $widget_obj ) {
		$settings = $widget_obj->get_settings_for_display();
		$fields = wp_parse_args( $ajax_vars['fields'] );

		$double_optin = false;
		$update_existing = true;
		$status_if_new = 'subscribed';
		$status = 'subscribed';

		if ( $settings['double_optin'] ) {
			$double_optin = true;
			$status_if_new = 'pending';
		}

		if ( ! $settings['update_existing'] ) {
			$update_existing = false;
		}

		if ( 'unsubscribe' === $fields['action-form'] ) {
			$status = 'unsubscribed';
		}

		$email_address = isset( $fields['email'] ) ? sanitize_email( $fields['email'] ) : '';

		$body = array(
			'email_address' => $email_address,
			'status_if_new' => $status_if_new,
			'status' => $status,
			'merge_fields' => (object) $this->merge_fields( $ajax_vars ),
			'double_optin' => $double_optin,
			'update_existing' => $update_existing,
		);

		if ( '' !== $settings['tag'] ) {
			$body['tags'] = $this->tags( $widget_obj );
		}

		return $body;
	}

	/**
	 * Get form fields.
	 *
	 * Gets form field data.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function merge_fields( $ajax_vars ) {
		$merge_fields = array();
		$fields = wp_parse_args( $ajax_vars['fields'] );

		if ( ! empty( $fields['mp-first-name'] ) ) {
			$merge_fields['FNAME'] = sanitize_text_field( $fields['mp-first-name'] );
		}

		if ( ! empty( $fields['mp-last-name'] ) ) {
			$merge_fields['LNAME'] = sanitize_text_field( $fields['mp-last-name'] );
		}

		if ( ! empty( $fields['phone'] ) ) {
			$merge_fields['PHONE'] = sanitize_text_field( $fields['phone'] );
		}

		if ( ! empty( $fields['bday'] ) ) {
			$merge_fields['BIRTHDAY'] = $this->bdays_date( $ajax_vars );
		}

		if ( ! empty( $fields['additionally'] ) || ! empty( $fields['city'] ) || ! empty( $fields['state'] ) || ! empty( $fields['zip'] ) || ! empty( $fields['country'] ) ) {
			$merge_fields['ADDRESS'] = array(
				'addr1' => ! empty( $fields['additionally'] ) ? sanitize_text_field( $fields['additionally'] ) : '',
				'city' => ! empty( $fields['city'] ) ? sanitize_text_field( $fields['city'] ) : '',
				'state' => ! empty( $fields['state'] ) ? sanitize_text_field( $fields['state'] ) : '',
				'zip' => ! empty( $fields['zip'] ) ? sanitize_text_field( $fields['zip'] ) : '',
				'country' => ! empty( $fields['country'] ) ? sanitize_text_field( $fields['country'] ) : '',
			);
		}

		return $merge_fields;
	}

	/**
	 * Get tag.
	 *
	 * Defines tags for the subscriber.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function tags( $widget_obj ) {
		$settings = $widget_obj->get_settings_for_display();

		if ( '' !== $settings['tag'] ) {
			$tags = explode( ',', trim( $settings['tag'] ) );
			$tags = array_map( 'trim', $tags );

			foreach ( $tags as $i => $tag ) {
				if ( '' === $tag ) {
					unset( $tags[ $i ] );
				}
			}

			$tags = array_values( $tags );

			return $tags;
		}
	}

	/**
	 * Get birth day.
	 *
	 * Gets a subscriber's birthday
	 *
	 * @since 1.0.0
	 *
	 */
	protected function bdays_date( $ajax_vars ) {
		$fields = wp_parse_args( $ajax_vars['fields'] );

		$date = ! empty( $fields['bday'] ) ? $fields['bday'] : '';
		$date = str_replace( array( ' ', '.', '-', ',' ), '/', $date );

		$date = explode( '/', $date );

		if ( $date[0] > 12 && $date[0] <= 31 && $date[1] <= 12 ) {
			$date = array_reverse( $date );
		}

		$date  = join( '/', $date );

		return $date;
	}

	protected function message( $widget_obj ) {
		$settings = $widget_obj->get_settings_for_display();

		$general = empty( $settings['general_error_text'] ) ? __( 'Oops. Something went wrong. Please try again later.', 'cmsmasters-elementor' ) : esc_html( $settings['general_error_text'] );
		$already_subscribed = empty( $settings['already_subscribed'] ) ? __( 'Given email address is already subscribed, thank you!', 'cmsmasters-elementor' ) : esc_html( $settings['already_subscribed'] );
		$unsubscribed = empty( $settings['unsubscribed'] ) ? __( 'You were successfully unsubscribed.', 'cmsmasters-elementor' ) : esc_html( $settings['unsubscribed'] );
		$not_subscribed = empty( $settings['not_subscribed'] ) ? __( 'Given email address is not subscribed.', 'cmsmasters-elementor' ) : esc_html( $settings['not_subscribed'] );
		$success_text = empty( $settings['success_text'] ) ? __( 'Thank you for subscribing to the newsletter.', 'cmsmasters-elementor' ) : esc_html( $settings['success_text'] );
		$success_optin_text = empty( $settings['success_optin_text'] ) ? __( 'Please go to your email address and confirm.', 'cmsmasters-elementor' ) : esc_html( $settings['success_optin_text'] );
		$update = empty( $settings['update'] ) ? __( 'Thank you, your records have been updated!', 'cmsmasters-elementor' ) : esc_html( $settings['update'] );

		$message = array(
			'general' => $general,
			'already_subscribed' => $already_subscribed,
			'unsubscribed' => $unsubscribed,
			'not_subscribed' => $not_subscribed,
			'successfully' => $success_text,
			'successfully_opt_in' => $success_optin_text,
			'update' => $update,
		);

		return $message;
	}

	/**
	 * Get mailchimp lists.
	 *
	 * @since 1.0.0
	 *
	 */
	public function mailchimp_lists() {
		$lists = array();
		$url = esc_url_raw( $this->url() . '?fields=lists.id,lists.name&count=1000' );

		$response = wp_remote_get( $url, array(
			'headers' => array(
				'Content-Type' => 'application/json',
				'Authorization' => 'Basic ' . base64_encode( 'user:' . $this->api_key() ),
			),
		) );

		if ( ! is_wp_error( $response ) ) {
			$response = json_decode( wp_remote_retrieve_body( $response ) );

			if ( ! empty( $response ) && ! empty( $response->lists ) ) {
				$count_lis = count( $response->lists );

				for ( $i = 0; $i < $count_lis; $i++ ) {
					$lists[ $response->lists[ $i ]->id ] = $response->lists[ $i ]->name;
				}
			}
		}

		return $lists;
	}

	protected function api_key() {
		$mailchimp_api_key = get_option( 'elementor_mailchimp_api_key' );

		return $mailchimp_api_key;
	}

	protected function url() {
		return 'https://' . substr( $this->api_key(), strpos( $this->api_key(), '-' ) + 1 ) . '.api.mailchimp.com/3.0/lists/';
	}
}
