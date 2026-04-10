<?php
namespace CmsmastersElementor\Modules\Social\Classes;

use CmsmastersElementor\Modules\Settings\Settings_Page;
use CmsmastersElementor\Modules\Social\Module as SocialModule;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Integration with Facebook SDK.
 *
 * @since 1.0.0
 */
class Facebook_SDK_Manager {

	const OPTION_NAME_APP_ID = 'elementor_cmsmasters_facebook_app_id';

	/**
	 * Get application id.
	 *
	 * @since 1.0.0
	 *
	 * @return string Facebook application id.
	 */
	public static function get_app_id() {
		return get_option( self::OPTION_NAME_APP_ID, '' );
	}

	/**
	 * Get locale id.
	 *
	 * @since 1.0.0
	 *
	 * @return string The locale ID.
	 */
	public static function get_lang() {
		return get_locale();
	}

	/**
	 * Enqueue meta application id.
	 *
	 * @since 1.0.0
	 */
	public static function enqueue_meta_app_id() {
		$app_id = self::get_app_id();
		if ( $app_id ) {
			printf( '<meta property="fb:app_id" content="%s" />', esc_attr( $app_id ) );
		}
	}

	/**
	 * Undocumented function
	 *
	 * @since 1.0.0
	 *
	 * @param Widget_Base $widget
	 * @param array $condition
	 */
	public static function add_app_id_control( $widget, $condition = array() ) {
		if ( ! self::get_app_id() ) {
			/* translators: Addon 'Facebook ID' notice. %s: Integrations setting page link */
			$html = sprintf(
				__( 'You can set your Facebook App ID in the %s.', 'cmsmasters-elementor' ),
				sprintf(
					'<a href="%1$s" target="_blank">%2$s</a>',
					Settings_Page::get_url(),
					__( 'Integrations Settings', 'cmsmasters-elementor' )
				)
			);

			$content_classes = 'elementor-panel-alert elementor-panel-alert-warning';
		} else {
			/* translators: Addon 'Facebook ID' info. %1$s: App ID, %2$s: Setting Page link */
			$html = sprintf(
				__( 'You are connected to Facebook App %1$s, to change app <a href="%2$s" target="_blank">click here</a>.', 'cmsmasters-elementor' ),
				self::get_app_id(),
				Settings_Page::get_url()
			);

			$content_classes = 'elementor-panel-alert elementor-panel-alert-info';
		}

		$widget_args = array(
			'type' => Controls_Manager::RAW_HTML,
			'raw' => $html,
			'content_classes' => $content_classes,
		);

		if ( ! empty( $condition ) ) {
			$widget_args['condition'] = $condition;
		}

		$widget->add_control(
			'app_id',
			$widget_args
		);
	}

	/**
	 * Set settings for control
	 *
	 * @param array $settings
	 *
	 * @return array $settings
	 */
	public function localize_settings( $settings ) {
		$settings['facebook_sdk'] = array(
			'lang' => self::get_lang(),
			'app_id' => self::get_app_id(),
		);

		return $settings;
	}

	/**
	 * Main class constructor.
	 *
	 * Initializing the facebook sdk manager.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_head', array( __CLASS__, 'enqueue_meta_app_id' ) );
		add_filter( 'cmsmasters_elementor/frontend/settings', array( $this, 'localize_settings' ) );

		if ( ! empty( $_POST['option_page'] ) && 'elementor' === $_POST['option_page'] ) {
			$this->validate_sdk();
		}

		if ( is_admin() ) {
			add_action( 'elementor/admin/after_create_settings/' . Settings_Page::PAGE_ID, array( $this, 'register_admin_fields' ), 100 );
		}
	}

	/**
	 * Undocumented function
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings
	 */
	public static function get_permalink( $settings = array() ) {
		$post_id = get_the_ID();

		if ( isset( $settings['url_format'] ) && SocialModule::URL_FORMAT_PRETTY === $settings['url_format'] ) {
			return get_permalink( $post_id );
		}

		// Use plain url to avoid losing comments after change the permalink.
		return add_query_arg( 'p', $post_id, home_url() );
	}

	/**
	 * Undocumented function
	 *
	 * @since 1.0.0
	 *
	 * @param \Elementor\Settings $settings
	 */
	public function register_admin_fields( Settings_Page $settings ) {
		$settings->add_section( 'cmsmasters', 'facebook_sdk', array(
			'callback' => function() {
				echo '<h2>' . esc_html__( 'Facebook SDK', 'cmsmasters-elementor' ) . '</h2>';

				/* translators: %s: Facebook App Setting link. */
				echo '<p>' . sprintf( __( 'Facebook SDK lets you connect to your <a href="%s" target="_blank">dedicated application</a> so you can track the Facebook Widgets analytics on your site.', 'cmsmasters-elementor' ), 'https://developers.facebook.com/docs/apps/register/' ) .
					'<br>' .
					'<br>' .
					esc_html__( 'If you are using the Cmsmasters Comments Widget, you can add moderating options through your application. Note that this option will not work on local sites and on domains that don\'t have public access.', 'cmsmasters-elementor' ) . '</p>';
			},
			'fields' => array(
				'cmsmasters_facebook_app_id' => array(
					'label' => __( 'App ID', 'cmsmasters-elementor' ),
					'field_args' => array(
						'type' => 'text',
						/* translators: %s: Facebook App Setting link. */
						'desc' => sprintf( __( 'Remember to add the domain to your <a href="%s" target="_blank">App Domains</a>', 'cmsmasters-elementor' ), $this->get_app_settings_url() ),
					),
				),
			),
		) );
	}

	/**
	 * Get url from settings.
	 *
	 * @since 1.0.0
	 *
	 * @return string Settings url.
	 */
	private function get_app_settings_url() {
		$app_id = self::get_app_id();

		if ( $app_id ) {
			return sprintf( 'https://developers.facebook.com/apps/%d/settings/', $app_id );
		} else {
			return 'https://developers.facebook.com/apps/';
		}
	}

	/**
	 * Validate sdk.
	 *
	 * Validation sdk check.
	 *
	 * @since 1.0.0
	 */
	private function validate_sdk() {
		$errors = array();

		if ( ! empty( $_POST['elementor_cmsmasters_facebook_app_id'] ) ) {
			$response = wp_remote_get( 'https://graph.facebook.com/' . $_POST['elementor_cmsmasters_facebook_app_id'] );

			if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				$errors[] = esc_html__( 'Facebook App ID is not valid', 'cmsmasters-elementor' );
			}
		}

		$message = implode( '<br>', $errors );

		if ( ! empty( $errors ) ) {
			wp_die( $message, esc_html__( 'Facebook SDK', 'cmsmasters-elementor' ), array( 'back_link' => true ) );
		}
	}
}
