<?php
namespace CmsmastersElementor\Modules\AuthorizationForm\Widgets;

use CmsmastersElementor\Modules\AuthorizationForm\Widgets\Base;

use Elementor\Group_Control_Typography;
use Elementor\Controls_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Login extends Base {

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
		return 'cmsmasters-login-form';
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
		return __( 'Login Form', 'cmsmasters-elementor' );
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
		return 'cmsicon-login-form';
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
			'login',
			'user',
			'form',
		);
	}

	/**
	 * Name widget form.
	 *
	 * Retrieve the widget name form.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget name form.
	 */
	protected function form_name() {
		return 'login';
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
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		$is_register = get_option( 'users_can_register' );

		$this->start_controls_section(
			'section_login_content',
			array(
				'label' => __( 'Login Form Settings', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'label',
			array(
				'label' => __( 'Labels', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'placeholder',
			array(
				'label' => __( 'Placeholders', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'show_remember_me',
			array(
				'label' => __( 'Remember Me', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'remember_position',
			array(
				'label' => __( 'Remember Me Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'right',
				'label_block' => false,
				'toggle' => false,
				'prefix_class' => 'cmsmasters-login-form__remember-position-',
				'condition' => array( 'show_remember_me' => 'yes' ),
			)
		);

		$this->add_control(
			'show_lost_password',
			array(
				'label' => __( 'Lost your password?', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
			)
		);

		if ( $is_register ) {
			$this->add_control(
				'show_register',
				array(
					'label' => __( 'Register', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
					'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				)
			);
		}

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
					'{{WRAPPER}} .elementor-widget-cmsmasters-login-form__field-group' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-login-form__wrapper' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional_option',
			array(
				'label' => __( 'Additional Options', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'redirect_after_login',
			array(
				'label' => __( 'Redirect After Login', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'redirect_url',
			array(
				'type' => Controls_Manager::URL,
				'show_label' => false,
				'show_external' => false,
				'separator' => false,
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'description' => __( 'Note: Because of security reasons, you can ONLY use your current domain here.', 'cmsmasters-elementor' ),
				'condition' => array(
					'redirect_after_login' => 'yes',
				),
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
			'user_label',
			array(
				'label' => __( 'Username Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( ' Username or Email Address', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'label' => 'yes',
				),
			)
		);

		$this->add_control(
			'user_placeholder',
			array(
				'label' => __( 'Username Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( ' Username or Email Address', 'cmsmasters-elementor' ),
				'condition' => array(
					'placeholder' => 'yes',
				),
			)
		);

		$this->add_control(
			'password_label',
			array(
				'label' => __( 'Password Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Password', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'label' => 'yes',
				),
			)
		);

		$this->add_control(
			'password_placeholder',
			array(
				'label' => __( 'Password Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Password', 'cmsmasters-elementor' ),
				'condition' => array(
					'placeholder' => 'yes',
				),
			)
		);

		$this->add_control(
			'remember_me_text',
			array(
				'label' => __( 'Remember Me', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Remember Me', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'show_remember_me' => 'yes',
				),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label' => __( 'Button Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Log In', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'lost_password_text',
			array(
				'label' => __( 'Lost you Password', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Lost you Password', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'show_lost_password' => 'yes',
				),
			)
		);

		if ( $is_register ) {
			$this->add_control(
				'register_text',
				array(
					'label' => __( 'Register', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => __( 'Register', 'cmsmasters-elementor' ),
					'separator' => 'before',
					'condition' => array(
						'show_register' => 'yes',
					),
				)
			);
		}

		$this->end_controls_section();

		parent::register_controls();

		$this->start_controls_section(
			'section_style_links',
			array(
				'label' => __( 'Links', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'links_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-login-form__field-group > a',
			)
		);

		$this->add_control(
			'links_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-login-form__field-group > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'links_hover_color',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-login-form__field-group > a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		if ( $is_register ) {
			$this->add_control(
				'separator_color',
				array(
					'label' => __( 'Separator Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-login-form__login-separator' => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'show_register' => 'yes',
						'show_logged_in_message' => 'yes',
					),
				)
			);

			$this->add_control(
				'link_gap',
				array(
					'label' => __( 'Links Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'default' => array(
						'size' => '5',
					),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'separator' => 'before',
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-login-form__login-separator' => 'margin: 0 {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						'show_register' => 'yes',
						'show_logged_in_message' => 'yes',
					),
				)
			);

			$this->add_control(
				'separator_type',
				array(
					'label' => __( 'Separator Type', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => array(
						'solid' => __( 'Solid', 'cmsmasters-elementor' ),
						'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
						'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
						'double' => __( 'Double', 'cmsmasters-elementor' ),
						'groove' => __( 'Groove', 'cmsmasters-elementor' ),
						'ridge' => __( 'Ridge', 'cmsmasters-elementor' ),
					),
					'default' => 'solid',
					'separator' => 'before',
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-login-form__login-separator' => 'border-right-style: {{VALUE}};',
					),
					'condition' => array(
						'show_register' => 'yes',
						'show_logged_in_message' => 'yes',
					),
				)
			);

			$this->add_control(
				'separator_width',
				array(
					'label' => __( 'Separator Width', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'default' => array(
						'size' => '2',
					),
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 15,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-login-form__login-separator' => 'border-width: {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						'show_register' => 'yes',
						'show_logged_in_message' => 'yes',
					),
				)
			);
		}

		$this->end_controls_section();

		$this->update_controls();
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
		$section_style_labels = $this->get_controls( 'section_style_labels' );
		$align = $this->get_controls( 'align' );

		$section_style_labels['conditions']['terms'][] = array(
			'name' => 'show_remember_me',
			'operator' => '===',
			'value' => 'yes',
		);

		$align['conditions'] = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'name' => 'remember_position',
					'operator' => '===',
					'value' => 'top',
				),
				array(
					'name' => 'show_remember_me',
					'operator' => '===',
					'value' => '',
				),
			),
		);

		$this->update_control( 'section_style_labels', $section_style_labels );
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
		$settings = $this->get_settings_for_display();
		$current_url = remove_query_arg( 'fake_arg' );
		$logout_redirect = $current_url;
		$is_editor = Plugin::$instance->editor->is_edit_mode();

		if ( 'yes' === $settings['redirect_after_login'] && ! empty( $settings['redirect_url']['url'] ) ) {
			$redirect_url = $settings['redirect_url']['url'];
		} else {
			$redirect_url = $current_url;
		}

		if ( 'yes' === $settings['redirect_after_logout'] && ! empty( $settings['redirect_logout_url']['url'] ) ) {
			$logout_redirect = $settings['redirect_logout_url']['url'];
		}

		if ( is_user_logged_in() && ! $is_editor ) {
			parent::render_logged_message( $logout_redirect );

			return;
		}

		$this->form_fields_render_attributes( $redirect_url );

		echo '<form ' . $this->get_render_attribute_string( 'form' ) . '">
			<input type="hidden" name="redirect_to" value="' . esc_attr( $redirect_url ) . '">
			<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>
				<div ' . $this->get_render_attribute_string( 'field-group' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( 'yes' === $settings['label'] ) {
			$user_label = ( empty( $settings['user_label'] ) ? __( 'Username or Email Address', 'cmsmasters-elementor' ) : esc_html( $settings['user_label'] ) );

			echo '<label ' . $this->get_render_attribute_string( 'user_label' ) . '>' . $user_label . '*</label>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

					echo '<input size="1" ' . $this->get_render_attribute_string( 'user_input' ) . ' required>
				</div>
				<div ' . $this->get_render_attribute_string( 'field-group' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( 'yes' === $settings['label'] ) {
			$password_label = ( empty( $settings['password_label'] ) ? __( 'Password', 'cmsmasters-elementor' ) : esc_html( $settings['password_label'] ) );

			echo '<label ' . $this->get_render_attribute_string( 'password_label' ) . '>' . $password_label . '*</label>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

					echo '<input size="1" ' . $this->get_render_attribute_string( 'password_input' ) . ' required>
				</div>
				<div class="elementor-widget-cmsmasters-login-form__align-wrapper">';

		if ( 'yes' === $settings['show_remember_me'] ) {
			$remember_me = __( 'Remember Me', 'cmsmasters-elementor' );

			if ( ! empty( $settings['remember_me_text'] ) ) {
				$remember_me = esc_html( $settings['remember_me_text'] );
			}

			echo '<div ' . $this->get_render_attribute_string( 'remember-me-wrapper' ) . '>
				<label for="elementor-widget-cmsmasters-login-form__login-remember-me">
					<input ' . $this->get_render_attribute_string( 'remember-me-input' ) . '>' .
					$remember_me .
				'</label>
			</div>';
		}

					$button_text = ( empty( $settings['button_text'] ) ? __( 'Log In', 'cmsmasters-elementor' ) : esc_html( $settings['button_text'] ) );

					echo '<div ' . $this->get_render_attribute_string( 'submit-group' ) . '>
						<button type="submit" ' . $this->get_render_attribute_string( 'button' ) . '>
							<span class="elementor-widget-cmsmasters-login-form__button-text">' . $button_text . '</span>
						</button>
					</div>
				</div>';

		$show_lost_password = 'yes' === $settings['show_lost_password'];
		$show_register = get_option( 'users_can_register' ) && 'yes' === $settings['show_register'];

		if ( $show_lost_password || $show_register ) {
			echo '<div ' . $this->get_render_attribute_string( 'links-wrapper' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( $show_lost_password ) {
				if ( empty( $settings['lost_password_text'] ) ) {
					$lost_password = __( 'Lost your password?', 'cmsmasters-elementor' );
				} else {
					$lost_password = esc_html( $settings['lost_password_text'] );
				}

				echo '<a ' . $this->get_render_attribute_string( 'lost-pass' ) . '>' .
				$lost_password .
				'</a>';
			}

			if ( $show_register ) {
				if ( $show_lost_password ) {
					echo '<span class="elementor-widget-cmsmasters-login-form__login-separator"></span>';
				}

				$register_text = __( 'Register', 'cmsmasters-elementor' );

				if ( ! empty( $settings['register_text'] ) ) {
					$register_text = esc_html( $settings['register_text'] );
				}

				echo '<a ' . $this->get_render_attribute_string( 'register' ) . '>' .
				$register_text .
				'</a>';
			}

			echo '</div>';
		}

			echo '</div>
		</form>	';

		if ( $is_editor ) {
			parent::render_logged_message( $logout_redirect );
		}
	}

	/**
	 * Get Widgets Attributes
	 *
	 * Retrieve Widgets Attributes.
	 *
	 * @since 1.0.0
	 *
	 */
	private function form_fields_render_attributes( $redirect_url ) {
		$settings = $this->get_settings_for_display();

		if ( $settings['button_hover_animation'] ) {
			$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['button_hover_animation'] );
		}

		$user_placeholder = '';

		if ( $settings['placeholder'] ) {
			$user_placeholder = __( 'Username or Email Address', 'cmsmasters-elementor' );

			if ( ! empty( $settings['user_placeholder'] ) ) {
				$user_placeholder = esc_attr( $settings['user_placeholder'] );
			}
		}

		$password_placeholder = '';

		if ( $settings['placeholder'] ) {
			$password_placeholder = __( 'Password', 'cmsmasters-elementor' );

			if ( ! empty( $settings['password_placeholder'] ) ) {
				$password_placeholder = esc_attr( $settings['password_placeholder'] );
			}
		}

		$this->add_render_attribute(
			array(
				'form' => array(
					'class' => 'elementor-widget-cmsmasters-login-form__login',
					'method' => 'post',
					'action' => esc_url( site_url( 'wp-login.php', 'login_post' ) ),
				),

				'wrapper' => array(
					'class' => array(
						'elementor-widget-cmsmasters-login-form__wrapper',
					),
				),

				'field-group' => array(
					'class' => array(
						'elementor-widget-cmsmasters-login-form__field-type-text',
						'elementor-widget-cmsmasters-login-form__field-group',
					),
				),

				'submit-group' => array(
					'class' => array(
						'elementor-widget-cmsmasters-login-form__field-group',
						'elementor-widget-cmsmasters-login-form__field-type-submit',
					),
				),

				'remember-me-wrapper' => array(
					'class' => array(
						'elementor-widget-cmsmasters-login-form__field-type-checkbox',
						'elementor-widget-cmsmasters-login-form__field-group',
						'elementor-widget-cmsmasters-login-form__remember-me',
					),
				),

				'links-wrapper' => array(
					'class' => array(
						'elementor-widget-cmsmasters-login-form__links',
						'elementor-widget-cmsmasters-login-form__field-group',
					),
				),

				'lost-pass' => array(
					'class' => array(
						'elementor-widget-cmsmasters-login-form__lost-password',
					),
					'href' => esc_url( wp_lostpassword_url( $redirect_url ) ),
				),

				'register' => array(
					'class' => array(
						'elementor-widget-cmsmasters-login-form__register',
					),
					'href' => esc_url( wp_registration_url() ),
				),

				'remember-me-input' => array(
					'type' => 'checkbox',
					'name' => 'rememberme',
					'id' => 'elementor-widget-cmsmasters-login-form__login-remember-me',
					'value' => 'forever',
				),

				'button' => array(
					'class' => array(
						'elementor-widget-cmsmasters-login-form__button',
					),
					'name' => 'wp-submit',
				),

				'user_label' => array(
					'for' => 'user',
				),

				'user_input' => array(
					'class' => array(
						'elementor-widget-cmsmasters-login-form__field',
						'elementor-widget-cmsmasters-login-form__field-textual',
					),
					'type' => 'text',
					'name' => 'log',
					'id' => 'user',
					'placeholder' => $user_placeholder,
				),

				'password_input' => array(
					'class' => array(
						'elementor-widget-cmsmasters-login-form__field',
						'elementor-widget-cmsmasters-login-form__field-textual',
					),
					'type' => 'password',
					'name' => 'pwd',
					'id' => 'password',
					'placeholder' => $password_placeholder,
				),

				'label_user' => array(
					'for' => 'user',
					'class' => 'elementor-widget-cmsmasters-login-form__field-label',
				),

				'label_password' => array(
					'for' => 'password',
					'class' => 'elementor-widget-cmsmasters-login-form__field-label',
				),
			)
		);
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
			'redirect_url' => array(
				'field' => 'url',
				'type' => esc_html__( 'Redirect Url', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			'redirect_logout_url' => array(
				'field' => 'url',
				'type' => esc_html__( 'Redirect Logout Url', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'user_label',
				'type' => esc_html__( 'User Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'user_placeholder',
				'type' => esc_html__( 'User Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'password_label',
				'type' => esc_html__( 'Password Label', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'password_placeholder',
				'type' => esc_html__( 'Password Placeholder', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'remember_me_text',
				'type' => esc_html__( 'Remember Me Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'button_text',
				'type' => esc_html__( 'Button Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'lost_password_text',
				'type' => esc_html__( 'Lost Password Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'register_text',
				'type' => esc_html__( 'Register Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'text_logged_in_message',
				'type' => esc_html__( 'Logged In Message Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'link_logged_in_message',
				'type' => esc_html__( 'Logged In Message Link', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
