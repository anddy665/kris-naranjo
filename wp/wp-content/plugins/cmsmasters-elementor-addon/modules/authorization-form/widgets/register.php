<?php
namespace CmsmastersElementor\Modules\AuthorizationForm\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\AuthorizationForm\Register_Function;
use CmsmastersElementor\Modules\AuthorizationForm\Widgets\Base;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Register extends Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return 'cmsmasters-register-form';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Registration Form', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-registration-form';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'register',
			'user',
			'form',
		);
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Name widget form keywords.
	 *
	 * Retrieve the widget name form.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget name form.
	 */
	protected function form_name() {
		return 'register';
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		if ( ! get_option( 'users_can_register' ) ) {
			$this->start_controls_section(
				'section_warning',
				array(
					'label' => __( 'Register Form', 'cmsmasters-elementor' ),
				)
			);

			$this->add_control(
				'warning',
				array(
					'raw' => __( 'Please go to the ', 'cmsmasters-elementor' ) . '<a href="' . esc_url( network_admin_url( 'settings.php' ) ) . '" target="_blank">' . __( 'settings page', 'cmsmasters-elementor' ) . '</a>' . __( ' and allow registration for your sites', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'render_type' => 'ui',
				)
			);

			$this->end_controls_section();

			return;
		}

		$label = $this->controls_labels();

		$this->start_controls_section(
			'section_register_content',
			array(
				'label' => __( 'Register Form Settings', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'first_name',
			array(
				'label' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register/fname_label', $label['fname'] ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'optional' => __( 'Optional', 'cmsmasters-elementor' ),
					'req' => __( 'Required', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'toggle' => false,
			)
		);

		$this->add_control(
			'last_name',
			array(
				'label' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register/lname_label', $label['lname'] ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'optional' => __( 'Optional', 'cmsmasters-elementor' ),
					'req' => __( 'Required', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'toggle' => false,
			)
		);

		$this->add_control(
			'website',
			array(
				'label' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register/website_label', $label['website'] ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'optional' => __( 'Optional', 'cmsmasters-elementor' ),
					'req' => __( 'Required', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'toggle' => false,
			)
		);

		$this->add_control(
			'login',
			array(
				'label' => __( 'Log In', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'login_url',
			array(
				'type' => Controls_Manager::URL,
				'show_label' => false,
				'show_external' => false,
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'description' => __( 'Note: if you do not keep a link, then the redirect will be to the default page log in.', 'cmsmasters-elementor' ),
				'condition' => array(
					'login' => 'yes',
				),
			)
		);

		$this->add_control(
			'login_position',
			array(
				'label' => __( 'Log In Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'right',
				'label_block' => false,
				'toggle' => false,
				'prefix_class' => 'cmsmasters-register-form__login-position-',
				'condition' => array( 'login' => 'yes' ),
			)
		);

		$this->add_control(
			'label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'placeholder',
			array(
				'label' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'show_logged_in_message',
			array(
				'label' => __( 'Logged in Message', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'row_gap',
			array(
				'label' => __( 'Rows Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => '10',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__field-group' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__wrapper' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional',
			array(
				'label' => __( 'Additional Options', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'role',
			array(
				'label' => __( 'User Role', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'subscriber' => __( 'Subscriber', 'cmsmasters-elementor' ),
					'author' => __( 'Author', 'cmsmasters-elementor' ),
					'contributor' => __( 'Contributor', 'cmsmasters-elementor' ),
					'editor' => __( 'Editor', 'cmsmasters-elementor' ),
				),
				'default' => 'subscriber',
			)
		);

		$this->add_control(
			'redirect_register',
			array(
				'label' => __( 'Sending Password After Registration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'email' => __( 'Email', 'cmsmasters-elementor' ),
					'page' => __( 'Password Reset Page ', 'cmsmasters-elementor' ),
				),
				'default' => 'email',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'redirect_after_logout',
			array(
				'label' => __( 'Redirect After Logout', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'show_logged_in_message' => 'yes',
				),
			)
		);

		$this->add_control(
			'redirect_logout_url',
			array(
				'type' => Controls_Manager::URL,
				'show_label' => false,
				'show_external' => false,
				'separator' => false,
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'description' => __( 'Note: Because of security reasons, you can ONLY use your current domain here.', 'cmsmasters-elementor' ),
				'condition' => array(
					'redirect_after_logout' => 'yes',
					'show_logged_in_message' => 'yes',
				),
			)
		);

		$this->add_control(
			'user_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Username', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'label',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'placeholder',
							'operator' => '===',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'user_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Username', 'cmsmasters-elementor' ),
				'condition' => array(
					'label' => 'yes',
				),
			)
		);

		$this->add_control(
			'user_placeholder',
			array(
				'label' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Username', 'cmsmasters-elementor' ),
				'condition' => array(
					'placeholder' => 'yes',
				),
			)
		);

		$this->add_control(
			'email_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Email', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'label',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'placeholder',
							'operator' => '===',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'email_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Label', 'cmsmasters-elementor' ),
				'condition' => array(
					'label' => 'yes',
				),
			)
		);

		$this->add_control(
			'email_placeholder',
			array(
				'label' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'condition' => array(
					'placeholder' => 'yes',
				),
			)
		);

		$this->add_control(
			'first_name_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register/fname_label', $label['fname'] ),
				'separator' => 'before',
				'condition' => array(
					'first_name!' => 'none',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'label',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'placeholder',
							'operator' => '===',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'first_name_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Label', 'cmsmasters-elementor' ),
				'condition' => array(
					'label' => 'yes',
					'first_name!' => 'none',
				),
			)
		);

		$this->add_control(
			'first_name_placeholder',
			array(
				'label' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'condition' => array(
					'first_name!' => 'none',
					'placeholder' => 'yes',
				),
			)
		);

		$this->add_control(
			'last_name_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register/lname_label', $label['lname'] ),
				'separator' => 'before',
				'condition' => array(
					'last_name!' => 'none',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'label',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'placeholder',
							'operator' => '===',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'last_name_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Label', 'cmsmasters-elementor' ),
				'condition' => array(
					'label' => 'yes',
					'last_name!' => 'none',
				),
			)
		);

		$this->add_control(
			'last_name_placeholder',
			array(
				'label' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'condition' => array(
					'last_name!' => 'none',
					'placeholder' => 'yes',
				),
			)
		);

		$this->add_control(
			'website_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => apply_filters( 'cmsmasters_elementor/widgets/authorization_form/register/website_label', $label['website'] ),
				'separator' => 'before',
				'condition' => array(
					'website!' => 'none',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'label',
							'operator' => '===',
							'value' => 'yes',
						),
						array(
							'name' => 'placeholder',
							'operator' => '===',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'website_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Label', 'cmsmasters-elementor' ),
				'condition' => array(
					'label' => 'yes',
					'website!' => 'none',
				),

			)
		);

		$this->add_control(
			'website_placeholder',
			array(
				'label' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Placeholder', 'cmsmasters-elementor' ),
				'condition' => array(
					'website!' => 'none',
					'placeholder' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label' => __( 'Button Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Register', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'login_text',
			array(
				'label' => __( 'Login Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Login', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'login' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_message',
			array(
				'label' => __( 'Message', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'valid_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Valid', 'cmsmasters-elementor' ),
				'condition' => array(
					'redirect_register' => 'email',
				),
			)
		);

		$this->add_control(
			'user_valid',
			array(
				'label' => __( 'Register successful', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Registration successful. The password has been sent to your email, please check the Inbox and Spam folder.', 'cmsmasters-elementor' ),
				'label_block' => true,
				'separator' => 'after',
				'condition' => array(
					'redirect_register' => 'email',
				),
			)
		);

		$this->add_control(
			'novalid_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Invalid', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'user_short',
			array(
				'label' => __( 'Username too short', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'The username is too short. At least 4 characters are required.', 'cmsmasters-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'user_exists',
			array(
				'label' => __( 'Username Exists', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Sorry, that username already exists. Please try another one.', 'cmsmasters-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'user_novalid',
			array(
				'label' => __( 'Username Not Valid', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Sorry, the username you entered is not valid. Please use only letters (a-z), numbers and periods.', 'cmsmasters-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'email_novalid',
			array(
				'label' => __( 'Email Not Valid', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Invalid email address. Please enter a valid address.', 'cmsmasters-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'email_exists',
			array(
				'label' => __( 'Email In Use', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'That email is already in use. Please try another one.', 'cmsmasters-elementor' ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		parent::register_controls();

		$this->update_controls();

		$this->start_controls_section(
			'section_style_login',
			array(
				'label' => __( 'Log In', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'login' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'links_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-register-form__login-link',
			)
		);

		$this->add_control(
			'login_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__login-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'login_hover_color',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__login-link:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_valid',
			array(
				'label' => __( 'Message', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_valid',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Valid', 'cmsmasters-elementor' ),
				'condition' => array(
					'redirect_register' => 'email',
				),
			)
		);

		$this->add_control(
			'valid_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__valid' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'redirect_register' => 'email',
				),
			)
		);

		$this->add_control(
			'valid_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__valid' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'redirect_register' => 'email',
				),
			)
		);

		$this->add_control(
			'valid_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__valid' => 'border-color: {{VALUE}};',
				),
				'separator' => 'after',
				'condition' => array(
					'redirect_register' => 'email',
				),
			)
		);

		$this->add_control(
			'heading_novalid',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Invalid', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'novalid_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__wrapper-error > p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'novalid_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__wrapper-error > p' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'novalid_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__wrapper-error > p' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'valid_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-register-form__valid, {{WRAPPER}} .elementor-widget-cmsmasters-register-form__wrapper-error > p',
			)
		);

		$this->add_control(
			'align_valid',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'toggle' => false,
				'default' => 'left',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__valid' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__wrapper-error > p' => 'text-align: {{VALUE}};',
				),

			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(), array(
				'name' => 'valid_border',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-register-form__valid, {{WRAPPER}} .elementor-widget-cmsmasters-register-form__wrapper-error > p',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'valid_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__valid' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__wrapper-error > p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'valid_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__valid' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-register-form__wrapper-error > p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get widget control fields labels.
	 *
	 * Retrieve the control fields labels.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget fields labels.
	 */
	protected function controls_labels() {
		$controls_labels = array(
			'fname' => __( 'First Name', 'cmsmasters-elementor' ),
			'lname' => __( 'Last Name', 'cmsmasters-elementor' ),
			'website' => __( 'Website', 'cmsmasters-elementor' ),
			'fname_req' => __( 'First Name Required', 'cmsmasters-elementor' ),
			'lname_req' => __( 'Last Name Required', 'cmsmasters-elementor' ),
			'website_req' => __( 'Website Required', 'cmsmasters-elementor' ),
		);

		return $controls_labels;
	}

	/**
	 * Update Controls
	 *
	 * Retrieve Update Controls.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function update_controls() {
		$align = $this->get_controls( 'align' );

		$align['conditions'] = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'login_position',
					'operator' => '===',
					'value' => 'bottom',
				),
				array(
					'name' => 'login',
					'operator' => '===',
					'value' => '',
				),
			),
		);

		$this->update_control( 'align', $align );
	}

	/**
	 * Render widget.
	 *
	 * Outputs the widget HTML code on the frontend.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		if ( get_option( 'users_can_register' ) ) {
			$this->render_registration_form();
		}
	}

	/**
	 * Get register form widget.
	 *
	 * Retrieve register form.
	 *
	 * @since 1.0.0
	 */
	protected function render_registration_form() {
		$settings = $this->get_settings_for_display();
		$logout_redirect = remove_query_arg( 'fake_arg' );

		$this->form_fields_render_attributes();

		if ( is_user_logged_in() && ! Plugin::$instance->editor->is_edit_mode() ) {

			if ( 'yes' === $settings['redirect_after_logout'] && ! empty( $settings['redirect_logout_url']['url'] ) ) {
				$logout_redirect = $settings['redirect_logout_url']['url'];
			}

			parent::render_logged_message( $logout_redirect );

			return;
		}

		echo '<form class="elementor-widget-cmsmasters-register-form__register" method="post" action="' . esc_url( wp_get_referer() ) . '">
			<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>
				<div ' . $this->get_render_attribute_string( 'field-group' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( 'none' !== $settings['first_name'] ) {

			if ( 'req' === $settings['first_name'] ) {
				$required_fname = 'required';
				$star_fname = '*';
			} else {
				$required_fname = '';
				$star_fname = '';
			}

			echo '<div ' . $this->get_render_attribute_string( 'field-group' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( 'yes' === $settings['label'] ) {
				$fname_label = ( empty( $settings['first_name_label'] ) ? __( 'First Name', 'cmsmasters-elementor' ) : esc_html( $settings['first_name_label'] ) );

				echo '<label ' . $this->get_render_attribute_string( 'label_fname' ) . '>' . $fname_label . $star_fname . '</label>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

				echo '<input size="1" ' . $this->get_render_attribute_string( 'fname_input' ) . ' ' . $required_fname . '>
			</div>';
		}

		if ( 'none' !== $settings['last_name'] ) {

			if ( 'req' === $settings['last_name'] ) {
				$required_lname = 'required';
				$star_lname = '*';
			} else {
				$required_lname = '';
				$star_lname = '';
			}

			echo '<div ' . $this->get_render_attribute_string( 'field-group' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( 'yes' === $settings['label'] ) {
				$lname_label = ( empty( $settings['last_name_label'] ) ? __( 'Last Name', 'cmsmasters-elementor' ) : esc_html( $settings['last_name_label'] ) );

				echo '<label ' . $this->get_render_attribute_string( 'label_lname' ) . '>' . $lname_label . $star_lname . '</label>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

				echo '<input size="1" ' . $this->get_render_attribute_string( 'lname_input' ) . ' ' . $required_lname . '>
			</div>';
		}

		if ( 'yes' === $settings['label'] ) {
			$user_label = ( empty( $settings['user_label'] ) ? __( 'Username', 'cmsmasters-elementor' ) : esc_html( $settings['user_label'] ) );

			echo '<label ' . $this->get_render_attribute_string( 'label_user' ) . '>' . $user_label . '*</label>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

					echo '<input size="1" ' . $this->get_render_attribute_string( 'user_input' ) . '>
				</div>
				<div ' . $this->get_render_attribute_string( 'field-group' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( 'yes' === $settings['label'] ) {
			$email_label = ( empty( $settings['email_label'] ) ? __( 'Email', 'cmsmasters-elementor' ) : esc_html( $settings['email_label'] ) );

			echo '<label ' . $this->get_render_attribute_string( 'label_email' ) . '>' . $email_label . '*</label>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

					echo '<input size="1" ' . $this->get_render_attribute_string( 'email_input' ) . '>
				</div>';

		if ( 'none' !== $settings['website'] ) {

			if ( 'req' === $settings['website'] ) {
				$required_website = 'required';
				$star_website = '*';
			} else {
				$required_website = '';
				$star_website = '';
			}

				echo '<div ' . $this->get_render_attribute_string( 'field-group' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( $settings['label'] ) {
				$website_label = ( empty( $settings['website_label'] ) ? __( 'Website', 'cmsmasters-elementor' ) : esc_html( $settings['website_label'] ) );

				echo '<label ' . $this->get_render_attribute_string( 'label_website' ) . '>' . $website_label . $star_website . '</label>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

				echo '<input size="1" ' . $this->get_render_attribute_string( 'website_input' ) . ' ' . $required_website . '>
			</div>';
		}

				$button_text = ( empty( $settings['button_text'] ) ? __( 'Register', 'cmsmasters-elementor' ) : esc_html( $settings['button_text'] ) );

				echo '<div class="elementor-widget-cmsmasters-register-form__align-wrapper">
					<div ' . $this->get_render_attribute_string( 'submit-group' ) . '>
						<button type="submit" ' . $this->get_render_attribute_string( 'button' ) . '>
							<span class="elementor-widget-cmsmasters-register-form__button-text">' . $button_text . '</span>
						</button>
					</div>';

		if ( 'yes' === $settings['login'] ) {
			$login_text = ( empty( $settings['login_text'] ) ? __( 'Login', 'cmsmasters-elementor' ) : esc_html( $settings['login_text'] ) );

			echo '<a ' . $this->get_render_attribute_string( 'login-link' ) . '">' . $login_text . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

				echo '</div>
			</div>';

			$user_short = ( '' === $settings['user_short'] ? __( 'The username is too short. At least 4 characters are required.', 'cmsmasters-elementor' ) : esc_attr( $settings['user_short'] ) );
			$user_exists = ( '' === $settings['user_exists'] ? __( 'Sorry, that username already exists. Please try another one.', 'cmsmasters-elementor' ) : esc_attr( $settings['user_exists'] ) );
			$user_novalid = ( '' === $settings['user_novalid'] ? __( 'Sorry, the username you entered is not valid. Please use only letters (a-z), numbers and periods.', 'cmsmasters-elementor' ) : esc_attr( $settings['user_novalid'] ) );
			$email_novalid = ( '' === $settings['email_novalid'] ? __( 'Invalid email address. Please enter a valid address. Example: someone@example.com', 'cmsmasters-elementor' ) : esc_attr( $settings['email_novalid'] ) );
			$email_exists = ( '' === $settings['email_exists'] ? __( 'That email is already in use. Please try another one.', 'cmsmasters-elementor' ) : esc_attr( $settings['email_exists'] ) );

			wp_nonce_field( 'register', 'register-nonce' );
			echo '<input type="hidden" name="role" value="' . esc_attr( $settings['role'] ) . '">
			<input type="hidden" name="type-redirect" value="' . esc_attr( $settings['redirect_register'] ) . '">
			<input type="hidden" name="user-short" value="' . $user_short . '">
			<input type="hidden" name="user-exists" value="' . $user_exists . '">
			<input type="hidden" name="user-novalid" value="' . $user_novalid . '">
			<input type="hidden" name="email-novalid" value="' . $email_novalid . '">
			<input type="hidden" name="email-exists" value="' . $email_exists . '">
		</form>';

		$this->render_valid_messages();

		if ( Plugin::$instance->editor->is_edit_mode() ) {
			$this->render_correct_valid();
			$this->render_no_valid_on_backend();

			parent::render_logged_message( $logout_redirect );
		}
	}

	/**
	 * Get widgets attributes
	 *
	 * Retrieve widgets attributes.
	 *
	 * @since 1.0.0
	 *
	 */
	private function form_fields_render_attributes() {
		$settings = $this->get_settings_for_display();
		$filter = Register_Function::instance()->get_filters();

		if ( $settings['button_hover_animation'] ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['button_hover_animation'] );
		}

		if ( 'yes' === $settings['placeholder'] && ! empty( $settings['user_placeholder'] ) ) {
			$user_placeholder = esc_attr( $settings['user_placeholder'] );
		} elseif ( 'yes' === $settings['placeholder'] && empty( $settings['user_placeholder'] ) ) {
			$user_placeholder = __( 'Username', 'cmsmasters-elementor' );
		} elseif ( ! $settings['placeholder'] ) {
			$user_placeholder = '';
		}

		if ( 'yes' === $settings['placeholder'] && ! empty( $settings['email_placeholder'] ) ) {
			$email_placeholder = esc_attr( $settings['email_placeholder'] );
		} elseif ( 'yes' === $settings['placeholder'] && empty( $settings['email_placeholder'] ) ) {
			$email_placeholder = __( 'Password', 'cmsmasters-elementor' );
		} elseif ( ! $settings['placeholder'] ) {
			$email_placeholder = '';
		}

		if ( 'yes' === $settings['placeholder'] && ! empty( $settings['first_name_placeholder'] ) ) {
			$first_name_placeholder = esc_attr( $settings['first_name_placeholder'] );
		} elseif ( 'yes' === $settings['placeholder'] && empty( $settings['first_name_placeholder'] ) ) {
			$first_name_placeholder = __( 'First Name', 'cmsmasters-elementor' );
		} elseif ( ! $settings['placeholder'] ) {
			$first_name_placeholder = '';
		}

		if ( 'yes' === $settings['placeholder'] && ! empty( $settings['last_name_placeholder'] ) ) {
			$last_name_placeholder = esc_attr( $settings['last_name_placeholder'] );
		} elseif ( 'yes' === $settings['placeholder'] && empty( $settings['last_name_placeholder'] ) ) {
			$last_name_placeholder = __( 'Last Name', 'cmsmasters-elementor' );
		} elseif ( ! $settings['placeholder'] ) {
			$last_name_placeholder = '';
		}

		if ( 'yes' === $settings['placeholder'] && ! empty( $settings['website_placeholder'] ) ) {
			$website_placeholder = esc_attr( $settings['website_placeholder'] );
		} elseif ( 'yes' === $settings['placeholder'] && empty( $settings['website_placeholder'] ) ) {
			$website_placeholder = __( 'Website', 'cmsmasters-elementor' );
		} elseif ( ! $settings['placeholder'] ) {
			$website_placeholder = '';
		}

		$this->add_render_attribute(
			array(
				'wrapper' => array(
					'class' => array(
						'elementor-widget-cmsmasters-register-form__wrapper',
					),
				),

				'field-group' => array(
					'class' => array(
						'elementor-widget-cmsmasters-register-form__field-type-text',
						'elementor-widget-cmsmasters-register-form__field-group',
					),
				),

				'submit-group' => array(
					'class' => array(
						'elementor-widget-cmsmasters-register-form__field-group',
						'elementor-widget-cmsmasters-register-form__field-type-submit',
					),
				),

				'login-link' => array(
					'href' => ( '' === $settings['login_url'] ? esc_url( wp_login_url() ) : $settings['login_url'] ),
					'class' => array(
						'elementor-widget-cmsmasters-register-form__field-group',
						'elementor-widget-cmsmasters-register-form__login-link',
					),
				),

				'button' => array(
					'class' => array(
						'elementor-widget-cmsmasters-register-form__button',
					),
					'name' => 'submit',
				),

				'user_input' => array(
					'class' => array(
						'elementor-widget-cmsmasters-register-form__field',
						'elementor-widget-cmsmasters-register-form__field-textual',
					),
					'id' => 'username',
					'type' => 'text',
					'name' => 'username',
					'placeholder' => $user_placeholder,
					'value' => ( isset( $_POST['username'] ) ? $_POST['username'] : '' ),
					'required' => 'required',
				),

				'email_input' => array(
					'class' => array(
						'elementor-widget-cmsmasters-register-form__field',
						'elementor-widget-cmsmasters-register-form__field-textual',
					),
					'id' => 'email',
					'type' => 'text',
					'name' => 'email',
					'placeholder' => $email_placeholder,
					'value' => ( isset( $_POST['email'] ) ? $_POST['email'] : '' ),
					'required' => 'required',
				),

				'fname_input' => array(
					'class' => array(
						'elementor-widget-cmsmasters-register-form__field',
						'elementor-widget-cmsmasters-register-form__field-textual',
					),
					'id' => $filter['fname_id'],
					'type' => $filter['fname_type'],
					'name' => $filter['fname_name'],
					'placeholder' => $first_name_placeholder,
					'value' => ( isset( $_POST['first-name'] ) ? $_POST['first-name'] : '' ),
				),

				'lname_input' => array(
					'class' => array(
						'elementor-widget-cmsmasters-register-form__field',
						'elementor-widget-cmsmasters-register-form__field-textual',
					),
					'id' => $filter['lname_id'],
					'type' => $filter['lname_type'],
					'name' => $filter['lname_name'],
					'placeholder' => $last_name_placeholder,
					'value' => ( isset( $_POST['last-name'] ) ? $_POST['last-name'] : '' ),
				),

				'website_input' => array(
					'class' => array(
						'elementor-widget-cmsmasters-register-form__field',
						'elementor-widget-cmsmasters-register-form__field-textual',
					),
					'id' => $filter['website_id'],
					'type' => $filter['website_type'],
					'name' => $filter['website_name'],
					'placeholder' => $website_placeholder,
					'value' => ( isset( $_POST['website'] ) ? $_POST['website'] : '' ),
				),

				'label_user' => array(
					'for' => 'username',
					'class' => 'elementor-widget-cmsmasters-register-form__field-label',
				),

				'label_email' => array(
					'for' => 'email',
					'class' => 'elementor-widget-cmsmasters-register-form__field-label',
				),

				'label_fname' => array(
					'for' => 'first-name',
					'class' => 'elementor-widget-cmsmasters-register-form__field-label',
				),

				'label_lname' => array(
					'for' => 'last-name',
					'class' => 'elementor-widget-cmsmasters-register-form__field-label',
				),

				'label_website' => array(
					'for' => 'website',
					'class' => 'elementor-widget-cmsmasters-register-form__field-label',
				),
			)
		);
	}

	/**
	 * Get widgets all valid messages
	 *
	 * Retrieve all valid messages.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function render_valid_messages() {
		$settings = $this->get_settings_for_display();
		$register = Register_Function::instance();

		if ( ! $register->check_nonce() ) {
			return;
		}

		if ( $register->is_errors() ) {
			echo '<div class="elementor-widget-cmsmasters-register-form__wrapper-error">';

			foreach ( $register->get_errors()->get_error_messages() as $error ) {
				echo wpautop( $error );
			}

			echo '</div>';
		} elseif ( ! $register->is_errors() && 'email' === $settings['redirect_register'] ) {
			$this->render_correct_valid();
		}
	}

	/**
	 * Get widgets correct valid messages
	 *
	 * Retrieve correct valid messages.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function render_correct_valid() {
		$settings = $this->get_settings_for_display();
		$backend_class = '';

		if ( 'email' === $settings['redirect_register'] ) {

			if ( Plugin::$instance->editor->is_edit_mode() ) {
				$backend_class = ' elementor-widget-cmsmasters-register-form__hide-valid';
			}

			$user_valid = ( '' === $settings['user_valid'] ? __( 'Registration successful. The password has been sent to your email, please check the Inbox and Spam folder.', 'cmsmasters-elementor' ) : esc_html( $settings['user_valid'] ) );

			echo '<div class="elementor-widget-cmsmasters-register-form__valid' . esc_attr( $backend_class ) . '">' . $user_valid . '</div>';
		}
	}

	/**
	 * Get widgets no valid fields messages only backend.
	 *
	 * Retrieve no valid fields messages only backend.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function render_no_valid_on_backend() {
		$settings = $this->get_settings_for_display();

		$user_short = ( '' === $settings['user_short'] ? __( 'The username is too short. At least 4 characters are required.', 'cmsmasters-elementor' ) : esc_html( $settings['user_short'] ) );
		$user_exists = ( '' === $settings['user_exists'] ? __( 'Sorry, that username already exists. Please try another one.', 'cmsmasters-elementor' ) : esc_html( $settings['user_exists'] ) );
		$user_novalid = ( '' === $settings['user_novalid'] ? __( 'Please use only letters (a-z), numbers and periods.', 'cmsmasters-elementor' ) : esc_html( $settings['user_novalid'] ) );
		$email_novalid = ( '' === $settings['email_novalid'] ? __( 'Invalid email address. Please enter a valid address. Example: someone@example.com', 'cmsmasters-elementor' ) : esc_html( $settings['email_novalid'] ) );
		$email_exists = ( '' === $settings['email_exists'] ? __( 'That email is already in use. Please try another one.', 'cmsmasters-elementor' ) : esc_html( $settings['email_exists'] ) );

		echo '<div class="elementor-widget-cmsmasters-register-form__wrapper-error elementor-widget-cmsmasters-register-form__hide-novalid">
			<p>' . $user_short . '</p>
			<p>' . $user_exists . '</p>
			<p>' . $user_novalid . '</p>
			<p>' . $email_novalid . '</p>
			<p>' . $email_exists . '</p>
		</div>';
	}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			'login_url' => array(
				'field' => 'url',
				'type' => esc_html__( 'Login URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			'redirect_logout_url' => array(
				'field' => 'url',
				'type' => esc_html__( 'Redirect URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'user_label',
				'type' => esc_html__( 'Username Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'user_placeholder',
				'type' => esc_html__( 'Username Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'email_label',
				'type' => esc_html__( 'Email Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'email_placeholder',
				'type' => esc_html__( 'Email Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'first_name_label',
				'type' => esc_html__( 'First Name Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'first_name_placeholder',
				'type' => esc_html__( 'First Name Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'last_name_label',
				'type' => esc_html__( 'Last Name Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'last_name_placeholder',
				'type' => esc_html__( 'Last Name Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'website_label',
				'type' => esc_html__( 'Website Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'website_placeholder',
				'type' => esc_html__( 'Website Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'button_text',
				'type' => esc_html__( 'Button', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'login_text',
				'type' => esc_html__( 'Login Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'user_valid',
				'type' => esc_html__( 'Register Successful', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'user_short',
				'type' => esc_html__( 'Username too short', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'user_exists',
				'type' => esc_html__( 'Username Exists', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'user_novalid',
				'type' => esc_html__( 'Username Not Valid', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'email_novalid',
				'type' => esc_html__( 'Email Not Valid', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'email_exists',
				'type' => esc_html__( 'Email In Use', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'text_logged_in_message',
				'type' => esc_html__( 'Text Logged In Message', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'link_logged_in_message',
				'type' => esc_html__( 'Link Logged In Message', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
