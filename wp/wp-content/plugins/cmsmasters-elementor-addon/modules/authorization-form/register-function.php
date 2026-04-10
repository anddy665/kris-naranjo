<?php
namespace CmsmastersElementor\Modules\AuthorizationForm;

use CmsmastersElementor\Traits\Singleton;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Register_Function {
	use Singleton;

	protected $username;
	protected $email;
	protected $first_name;
	protected $last_name;
	protected $website;
	protected $role;
	protected $type_redirect;
	protected $user_short;
	protected $user_exists;
	protected $user_novalid;
	protected $email_novalid;
	protected $email_exists;

	public function __construct() {
		$filter = $this->get_filters();

		$this->username = sanitize_user( isset( $_POST['username'] ) ? $_POST['username'] : '' );
		$this->email = sanitize_email( isset( $_POST['email'] ) ? $_POST['email'] : '' );
		$this->first_name = esc_attr( isset( $_POST[ $filter['fname_name'] ] ) ? $_POST[ $filter['fname_name'] ] : '' );
		$this->last_name = esc_attr( isset( $_POST[ $filter['lname_name'] ] ) ? $_POST[ $filter['lname_name'] ] : '' );
		$this->website = esc_url( isset( $_POST[ $filter['website_name'] ] ) ? $_POST[ $filter['website_name'] ] : '' );
		$this->role = esc_attr( isset( $_POST['role'] ) ? $_POST['role'] : 'subscriber' );
		$this->type_redirect = esc_attr( isset( $_POST['type-redirect'] ) ? $_POST['type-redirect'] : 'email' );
		$this->user_short = esc_attr( isset( $_POST['user-short'] ) ? $_POST['user-short'] : '' );
		$this->user_exists = esc_attr( isset( $_POST['user-exists'] ) ? $_POST['user-exists'] : '' );
		$this->user_novalid = esc_attr( isset( $_POST['user-novalid'] ) ? $_POST['user-novalid'] : '' );
		$this->email_novalid = esc_attr( isset( $_POST['email-novalid'] ) ? $_POST['email-novalid'] : '' );
		$this->email_exists = esc_attr( isset( $_POST['email-exists'] ) ? $_POST['email-exists'] : '' );
	}

	/**
		 * Filters fields attributes.
		 *
		 * Filters register widget fields attributes.
		 *
		 * @since 1.0.0
		 *
		 */
	public function get_filters() {
		$filters = array(
			'fname_id' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register-function/fname_id', 'first-name' ),
			'fname_type' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register-function/fname-type', 'text' ),
			'fname_name' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register-function/fname-name', 'first-name' ),
			'lname_id' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register-function/lname-id', 'last-name' ),
			'lname_type' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register-function/lname-type', 'text' ),
			'lname_name' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register-function/lname-id', 'last-name' ),
			'website_id' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register-function/website-id', 'website' ),
			'website_type' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register-function/website-type', 'text' ),
			'website_name' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register-function/website-id', 'website' ),
		);

		return $filters;
	}

	/**
	 * Get custom registration function.
	 *
	 * performs user registration.
	 *
	 * @since 1.0.0
	 *
	 */
	public function custom_registration_function() {
		if ( $this->check_nonce() ) {

			$this->complete_registration();
		}
	}

	/**
	 * Get check nonce form.
	 *
	 * Retrieve check nonce form.
	 *
	 * @since 1.0.0
	 *
	 */
	public function check_nonce() {
		$nonce = isset( $_POST['register-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['register-nonce'] ) ) : '';

		return ! empty( $nonce ) && wp_verify_nonce( $nonce, 'register' );
	}

	/**
	 * Get complete registration.
	 *
	 * Retrieve complete registration user.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function complete_registration() {
		if ( $this->is_errors() ) {
			return;
		}

		$password = esc_attr( wp_generate_password( 12, false, false ) );

		$new_user = array(
			'user_login' => $this->username,
			'user_email' => $this->email,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'user_url' => $this->website,
			'user_pass' => $password,
			'role' => $this->role,
			'user_registered' => date( 'Y-m-d H:i:s' ),
		);

		wp_insert_user( $new_user );

		if ( 'email' === $this->type_redirect ) {
			$this->email_massage();
		} else {
			$this->redirect();
		}
	}

	/**
	 * Get errors.
	 *
	 * receives validation error message.
	 *
	 * @since 1.0.0
	 *
	 */
	public function is_errors() {
		return (bool) $this->get_errors()->get_error_messages();
	}

	/**
	 * Get errors messages.
	 *
	 * receives field validation.
	 *
	 * @since 1.0.0
	 *
	 */
	public function get_errors() {
		static $reg_errors = null;

		if ( ! $reg_errors ) {
			$reg_errors = new \WP_Error();

			if ( 4 > strlen( $this->username ) ) {
				$reg_errors->add( 'username_length', $this->user_short );
			}

			if ( username_exists( $this->username ) ) {
				$reg_errors->add( 'user_name', $this->user_exists );
			}

			if ( ! validate_username( $this->username ) ) {
				$reg_errors->add( 'username_invalid', $this->user_novalid );
			}

			if ( ! is_email( $this->email ) ) {
				$reg_errors->add( 'email_invalid', $this->email_novalid );
			}

			if ( email_exists( $this->email ) ) {
				$reg_errors->add( 'email', $this->email_exists );
			}
		}

		return $reg_errors;
	}

	/**
	 * Email Massage.
	 *
	 * receives a message upon successful registration.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function email_massage() {
		$subject = get_bloginfo( 'name' );

		$mail_message = __( 'Username: ', 'cmsmasters-elementor' ) . $this->username . "\n\n";
		$mail_message .= __( 'To set your password, visit the following address:', 'cmsmasters-elementor' ) . "\n";
		$mail_message .= $this->password_link() . "\n\n";
		$mail_message .= get_bloginfo( 'url' );

		wp_mail( $this->email, $subject, $mail_message );
	}

	/**
	 * Password link.
	 *
	 * receives a password reset link.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function password_link() {
		$user_data = get_user_by( 'login', $this->username );

		if ( ! $user_data && is_email( $this->email ) ) {
			$user_data = get_user_by( 'email', $this->email );
		}

		if ( ! $user_data ) {
			return '';
		}

		$key = get_password_reset_key( $user_data );

		$link = network_site_url( 'wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode( $user_data->user_login ), 'login' );

		return $link;
	}

	/**
	 * Redirect.
	 *
	 * goes to the password replacement page after registration.
	 *
	 * @since 1.0.0
	 *
	 */
	public function redirect() {
		wp_safe_redirect( $this->password_link() );
		exit;
	}
}


