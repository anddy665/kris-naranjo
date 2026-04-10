<?php
namespace CmsmastersElementor\Modules\SocialCounter;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Utils;
use CmsmastersElementor\Modules\SocialCounter\Widgets\Social_Counter;
use CmsmastersElementor\Modules\AjaxWidget\Module as AjaxWidgetModule;
use CmsmastersElementor\Modules\AjaxWidget\Classes\Ajax_Action_Handler;
use CmsmastersElementor\Modules\Settings\Settings_Page;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CMSMasters Elementor social counter module.
 *
 * @since 1.0.0
 */
final class Module extends Base_Module {

	const URL_SERVER_API = 'https://cmsmasters-access-token.herokuapp.com';
	const PARAMETER_NAME = 'cmsmasters';

	/**
	 * @since 1.0.0
	 */
	public function get_name() {
		return 'cmsmasters_social_counter';
	}

	/**
	 * Add filters initialization.
	 *
	 * Register filters for the Social Counter module.
	 *
	 * @since 1.0.0
	 */
	protected function init_filters() {
		add_filter( 'cmsmasters_elementor/frontend/settings', array( $this, 'filter_frontend_settings' ) );
		add_filter( 'cmsmasters_elementor/admin/settings', array( $this, 'filter_admin_settings' ) );
	}

	/**
	 * Add actions initialization.
	 *
	 * Register actions for the Social Counter module.
	 *
	 * @since 1.0.0
	 */
	protected function init_actions() {
		if ( is_admin() ) {
			if ( ! Utils::is_ajax() ) {
				$this->set_new_social_data();
			}

			add_action( 'elementor/admin/after_create_settings/' . Settings_Page::PAGE_ID, array( $this, 'register_admin_fields' ), 100 );
		}

		add_action( 'cmsmasters_elementor/ajax_widget/register', array( $this, 'register_ajax_widget' ) );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_widgets() {
		return array(
			'Social_Counter',
		);
	}

	/**
	 * Filter frontend settings.
	 *
	 * Filters the Addon settings for elementor frontend.
	 *
	 * Fired by `cmsmasters_elementor/frontend/settings` Addon filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Frontend settings.
	 *
	 * @return array Filtered frontend settings.
	 */
	public function filter_frontend_settings( $settings ) {
		return array_replace_recursive( array(
			'nonces' => array(
				'social_counter' => wp_create_nonce( $this->get_name() ),
			),
		), $settings );
	}

	/**
	 * Filter admin settings.
	 *
	 * Filters the Addon settings for elementor admin.
	 *
	 * Fired by `cmsmasters_elementor/admin/settings` Addon filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Frontend settings.
	 *
	 * @return array Filtered frontend settings.
	 */
	public function filter_admin_settings( $settings ) {
		return array_replace_recursive( array(
			'social_counter' => array(
				'parameter_name' => self::PARAMETER_NAME,
			),
		), $settings );
	}

	/**
	 * Add handler for ajax widget.
	 *
	 * Register handler for the Blog widgets.
	 *
	 * Fired by `cmsmasters_elementor/ajax_widget/register` Addon action hook.
	 *
	 * @since 1.0.0
	 */
	public function register_ajax_widget( AjaxWidgetModule $ajax_widget ) {
		$ajax_widget->add_handler( 'cmsmasters-social-counter', array( $this, 'get_ajax_social_counter' ), false );
	}

	/**
	 * Register CMSMasters fields in dashboard.
	 *
	 * Fired by `elementor/admin/after_create_settings/cmsmasters` Cmsmasters action hook.
	 *
	 * @since 1.0.0
	 * @since 1.6.1 Fixed twitter connection.
	 * @since 1.7.5 Fixed social counter.
	 * @since 1.11.2 Fixed twitter connection.
	 *
	 * @param Settings_Page $settings Cmsmasters "Settings" page in WordPress dashboard.
	 */
	public function register_admin_fields( Settings_Page $settings ) {
		$settings->add_section( 'cmsmasters', 'behance', array(
			'callback' => function() {
				echo '<br><hr><br>' .
				'<h2>' . esc_html__( 'Behance', 'cmsmasters-elementor' ) . '</h2>';
			},
			'fields' => array(
				'behance_url' => array(
					'label' => __( 'Profile URL', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
					),
				),
				'behance_api_key' => array(
					'label' => __( 'API Key', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
						'desc' => sprintf(
							__( 'You need an', 'cmsmasters-elementor' ) . ' <a href="%s" target="_blank">%s</a>',
							esc_url( 'https://www.behance.net/dev' ),
							__( 'API Key', 'cmsmasters-elementor' )
						),
					),
				),
			),
		) );

		$settings->add_section( 'cmsmasters', 'dribbble', array(
			'callback' => function() {
				echo '<br><hr><br><h2>' . esc_html__( 'Dribbble', 'cmsmasters-elementor' ) . '</h2>';
			},
			'fields' => array(
				'dribbble_url' => array(
					'label' => __( 'Profile URL', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
					),
				),
			),
		) );

		$settings->add_section( 'cmsmasters', 'facebook', array(
			'callback' => function() {
				echo '<br><hr><br><h2>' . esc_html__( 'Facebook', 'cmsmasters-elementor' ) . '</h2>';
			},
			'fields' => array(
				'facebook_page_id' => array(
					'label' => __( 'Page ID/Name', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
					),
				),
				'facebook_access_token' => array(
					'label' => __( 'Access Token', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
						'desc' => sprintf(
							__( 'How to get', 'cmsmasters-elementor' ) . ' <a href="%s" target="_blank">%s</a>',
							esc_url( 'https://docs.cmsmasters.net/how-to-get-facebook-page-id-and-access-token/' ),
							__( 'Facebook Access Token', 'cmsmasters-elementor' )
						),
					),
				),
			),
		) );

		$settings->add_section( 'cmsmasters', 'google', array(
			'callback' => function() {
				echo '<br><hr><br><h2>' . esc_html__( 'Google', 'cmsmasters-elementor' ) . '</h2>';
			},
			'fields' => array(
				'youtube_url' => array(
					'label' => __( 'YouTube', 'cmsmasters-elementor' ) . ' ' . __( 'Channel or User URL', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
					),
				),
				'google_api_key' => array(
					'label' => __( 'API key', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
						'desc' => sprintf(
							__( 'You need an', 'cmsmasters-elementor' ) . ' <a href="%s" target="_blank">%s</a>',
							esc_url( 'https://console.developers.google.com/apis' ),
							__( 'API Key', 'cmsmasters-elementor' )
						),
					),
				),
			),
		) );

		$settings->add_section( 'cmsmasters', 'pinterest', array(
			'callback' => function() {
				echo '<br><hr><br><h2>' . esc_html__( 'Pinterest', 'cmsmasters-elementor' ) . '</h2>';
			},
			'fields' => array(
				'pinterest_url' => array(
					'label' => __( 'Profile URL', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
					),
				),
			),
		) );

		$settings->add_section( 'cmsmasters', 'reddit', array(
			'callback' => function() {
				echo '<br><hr><br><h2>' . esc_html__( 'Reddit', 'cmsmasters-elementor' ) . '</h2>';
			},
			'fields' => array(
				'reddit_url' => array(
					'label' => __( 'Profile URL', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
					),
				),
			),
		) );

		$settings->add_section( 'cmsmasters', 'soundcloud', array(
			'callback' => function() {
				echo '<br><hr><br><h2>' . esc_html__( 'Soundcloud', 'cmsmasters-elementor' ) . '</h2>';
			},
			'fields' => array(
				'soundcloud_url' => array(
					'label' => __( 'Profile URL', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
					),
				),
			),
		) );

		$settings->add_section( 'cmsmasters', 'twitch', array(
			'callback' => function() {
				echo '<br><hr><br><h2>' . esc_html__( 'Twitch', 'cmsmasters-elementor' ) . '</h2>';
			},
			'fields' => array(
				'twitch_url' => array(
					'label' => __( 'Channel URL', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
					),
				),
				'twitch_client_id' => array(
					'label' => __( 'Client ID', 'cmsmasters-elementor' ),
					'class' => 'cmsmasters_disabled_input',
					'field_args' => array(
						'class' => 'cmsmasters_disabled_input',
						'type' => 'text',
					),
				),
				'twitch_access_token' => array(
					'label' => __( 'Access Token', 'cmsmasters-elementor' ),
					'class' => 'cmsmasters_disabled_input',
					'field_args' => array(
						'type' => 'text',
						'class' => 'cmsmasters_disabled_input',
					),
				),
				'twitch_connector' => array(
					'field_args' => array(
						'type' => 'raw_html',
						'html' => sprintf(
							'<a href="%s" class="button">%s</a>',
							esc_url(
								add_query_arg(
									array(
										'redirect_url' => Settings_Page::get_url(),
									),
									"{$this->get_server_api_url()}/twitch/authorize"
								)
							),
							esc_html__( 'Connect an Twitch Account', 'cmsmasters-elementor' )
						),
					),
				),
			),
		) );

		$settings->add_section( 'cmsmasters', 'twitter', array(
			'callback' => function() {
				echo '<br><hr><br><h2>' . esc_html__( 'X Twitter', 'cmsmasters-elementor' ) . '</h2>';
			},
			'fields' => array(
				'twitter_screen_name' => array(
					'label' => __( 'Screen Name', 'cmsmasters-elementor' ),
					'class' => 'cmsmasters_disabled_input',
					'field_args' => array(
						'class' => 'cmsmasters_disabled_input',
						'type' => 'text',
					),
				),
				'twitter_user_id' => array(
					'label' => __( 'User ID', 'cmsmasters-elementor' ),
					'class' => 'cmsmasters_disabled_input',
					'field_args' => array(
						'class' => 'cmsmasters_disabled_input',
						'type' => 'text',
					),
				),
				'twitter_access_token' => array(
					'label' => __( 'Access Token', 'cmsmasters-elementor' ),
					'class' => 'cmsmasters_disabled_input',
					'field_args' => array(
						'class' => 'cmsmasters_disabled_input',
						'type' => 'text',
					),
				),
				'twitter_access_token_secret' => array(
					'label' => __( 'Access Token Secret', 'cmsmasters-elementor' ),
					'class' => 'cmsmasters_disabled_input',
					'field_args' => array(
						'class' => 'cmsmasters_disabled_input',
						'type' => 'text',
					),
				),
				'twitter_connector' => array(
					'field_args' => array(
						'type' => 'raw_html',
						'html' => sprintf(
							'<a href="%s" class="button">%s</a>',
							esc_url(
								add_query_arg(
									array(
										'redirect_url' => Settings_Page::get_url(),
									),
									"https://api.cmsmasters.net/wp-json/cmsmasters-api/v1/twitter-request-token"
								)
							),
							esc_html__( 'Connect an X Twitter Account', 'cmsmasters-elementor' )
						),
					),
				),
			),
		) );

		$settings->add_section( 'cmsmasters', 'vimeo', array(
			'callback' => function() {
				echo '<br><hr><br><h2>' . esc_html__( 'Vimeo', 'cmsmasters-elementor' ) . '</h2>';
			},
			'fields' => array(
				'vimeo_url' => array(
					'label' => __( 'Profile URL', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
					),
				),
			),
		) );
	}

	/**
	 * Get social count on ajax.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @return int
	 */
	public function get_ajax_social_counter( $ajax_vars, Social_Counter $widget_obj, Ajax_Action_Handler $ajax_widget ) {
		$items = Utils::get_if_isset( $ajax_vars, 'items' );

		if ( ! $items ) {
			$ajax_widget->send_required_fields_json_error();
		}

		return $widget_obj->get_data_for_ajax( $items );
	}

	/**
	 * Get url to api server.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_server_api_url() {
		if ( self::is_local() ) {
			return 'http://localhost:5000';
		}

		return self::URL_SERVER_API;
	}

	/**
	 * Check if local site.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_local() {
		return Utils::get_client_ip() === Utils::IP_LOCAL;
	}

	/**
	 * Check and set new social data.
	 *
	 * @since 1.0.0
	 * @since 1.6.1 Fixed twitter connection.
	 */
	public function set_new_social_data() {
		if ( empty( $_GET[ self::PARAMETER_NAME ] ) ) {
			return;
		}

		$result = $_GET[ self::PARAMETER_NAME ];

		if ( empty( $result ) ) {
			return;
		}

		switch ( $result['name'] ) {
			case 'twitter':
				if ( isset( $result['data']['screen_name'] ) ) {
					update_option( 'elementor_twitter_screen_name', $result['data']['screen_name'] );
				}

				if ( isset( $result['data']['user_id'] ) ) {
					update_option( 'elementor_twitter_user_id', $result['data']['user_id'] );
				}

				if ( isset( $result['data']['consumer_key'] ) ) {
					update_option( 'elementor_twitter_consumer_key', $result['data']['consumer_key'] );
				}

				if ( isset( $result['data']['consumer_secret'] ) ) {
					update_option( 'elementor_twitter_consumer_secret', $result['data']['consumer_secret'] );
				}

				if ( isset( $result['data']['access_token'] ) ) {
					update_option( 'elementor_twitter_access_token', $result['data']['access_token'] );
				}

				if ( isset( $result['data']['access_token_secret'] ) ) {
					update_option( 'elementor_twitter_access_token_secret', $result['data']['access_token_secret'] );
				}

				break;
			case 'twitch':
				if ( isset( $result['data']['access_token'] ) ) {
					update_option( 'elementor_twitch_access_token', $result['data']['access_token'] );
				}

				if ( isset( $result['data']['client_id'] ) ) {
					update_option( 'elementor_twitch_client_id', $result['data']['client_id'] );
				}

				if ( isset( $result['data']['display_name'] ) && ! get_option( 'elementor_twitch_url' ) ) {
					update_option( 'elementor_twitch_url', "https://www.twitch.tv/{$result['data']['display_name']}" );
				}
				break;
		}

		wp_safe_redirect( Settings_Page::get_url() );
	}

}
