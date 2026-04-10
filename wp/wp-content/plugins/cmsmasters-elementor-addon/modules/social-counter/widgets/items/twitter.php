<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Items;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Twitter social counter class.
 *
 * @since 1.0.0
 */
class Twitter extends Base {

	/**
	 * @since 1.0.0
	 */
	public static function get_name() {
		return 'twitter';
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_label() {
		return __( 'X Twitter', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_default_icon() {
		return array(
			'value' => 'fab fa-x-twitter',
			'library' => 'fa-brands',
		);
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_types() {
		return array(
			'followers' => __( 'Followers', 'cmsmasters-elementor' ),
			'tweets' => __( 'Tweets', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * Get user data numbers.
	 *
	 * @since 1.0.0
	 * @since 1.6.1 Fixed twitter connection.
	 * @since 1.11.2 Fixed twitter connection.
	 *
	 * @return string User data numbers.
	 */
	protected function get_numbers_remote() {
		$user_data = get_transient( 'cmsmasters_twitter_user_data' );

		if ( empty( $user_data ) ) {
			$user_data = $this->get_api_data();

			if ( empty( $user_data ) ) {
				return false;
			}
	
			set_transient( 'cmsmasters_twitter_user_data', $user_data, DAY_IN_SECONDS );
		}

		switch ( $this->get_type() ) {
			case 'followers':
				return $user_data['followers_count'];
			case 'tweets':
				return $user_data['tweet_count'];
		}
	}

	/**
	 * Get API data.
	 *
	 * @since 1.11.2
	 *
	 * @return array API data.
	 */
	protected function get_api_data() {
		$base_url = 'https://api.twitter.com/2/users/me';

		$fields = array(
			'user.fields' => 'id,public_metrics,username',
		);

		$consumer_key = get_option( 'elementor_twitter_consumer_key' );
		$consumer_secret = get_option( 'elementor_twitter_consumer_secret' );
		$access_token = get_option( 'elementor_twitter_access_token' );
		$access_token_secret = get_option( 'elementor_twitter_access_token_secret' );

		if (
			empty( $consumer_key ) ||
			empty( $consumer_secret ) ||
			empty( $access_token ) ||
			empty( $access_token_secret )
		) {
			return false;
		}

		$oauth_params = array(
			'oauth_consumer_key' => $consumer_key,
			'oauth_nonce' => md5( mt_rand() ),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp' => time(),
			'oauth_token' => $access_token,
			'oauth_version' => '1.0',
		);

        foreach ( $fields as $field_key => $field_value ) {
            $oauth_params[ $field_key ] = $field_value;
        }

		$base_string = $this->build_base_string( $base_url, $oauth_params );

		$composite_key = rawurlencode( $consumer_secret ) . '&' . rawurlencode( $access_token_secret );
        $oauth_signature = base64_encode( hash_hmac( 'sha1', $base_string, $composite_key, true ) );
        $oauth_params['oauth_signature'] = $oauth_signature;

		$oauth_header = $this->build_oauth_header( $oauth_params );

		$request_url = $base_url . $this->parse_fields_to_string( $fields );

        $response = wp_remote_get( $request_url, array(
            'headers' => $oauth_header,
            'timeout' => 60,
        ) );

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$user_remote_data = json_decode( wp_remote_retrieve_body( $response ) );

		return array(
			'followers_count' => $user_remote_data->data->public_metrics->followers_count,
			'tweet_count' => $user_remote_data->data->public_metrics->tweet_count,
		);
	}

	/**
	 * Build base string.
	 *
	 * @since 1.11.2
	 *
	 * @param string $base_url Base request url.
	 * @param array $oauth_params oAuth params.
	 *
	 * @return string base string for oAuth.
	 */
	protected function build_base_string( $base_url, $oauth_params ) {
		$base_string = array();

		ksort( $oauth_params );

		foreach ( $oauth_params as $key => $value ) {
			$base_string[] = rawurlencode( $key ) . '=' . rawurlencode( $value );
		}

		return 'GET&' . rawurlencode( $base_url ) . '&' . rawurlencode( implode( '&', $base_string ) );
	}

	/**
	 * Build oAuth header.
	 *
	 * @since 1.11.2
	 *
	 * @param array $oauth_params oAuth Params.
	 *
	 * @return string oAuth header.
	 */
	protected function build_oauth_header( $oauth_params ) {
		$header = 'Authorization: OAuth ';
		$values = array();

		foreach ( $oauth_params as $key => $value ) {
			if ( in_array( $key, array( 'oauth_consumer_key', 'oauth_nonce', 'oauth_signature', 'oauth_signature_method', 'oauth_timestamp', 'oauth_token', 'oauth_version' ) ) ) {
				$values[] = "$key=\"" . rawurlencode( $value ) . "\"";
			}
		}

		$header .= implode( ', ', $values );

		return $header;
	}

	/**
	 * Parse fields to string.
	 *
	 * @since 1.11.2
	 *
	 * @param array $fields Request fields.
	 *
	 * @return string parsed fields.
	 */
	protected function parse_fields_to_string( $fields ) {
		$url_string = '?';
		$length = count( $fields );
		$j = 1;

		foreach ( $fields as $key => $value ) {
			$url_string .= rawurlencode( $key ) . '=' . rawurlencode( $value );

			if ( $j != $length ) {
				$url_string .= '&';
			}

			$j++;
		}

		return $url_string;
	}

}
