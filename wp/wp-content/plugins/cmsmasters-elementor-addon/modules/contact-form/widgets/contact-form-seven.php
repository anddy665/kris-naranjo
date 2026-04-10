<?php
namespace CmsmastersElementor\Modules\ContactForm\Widgets;

use CmsmastersElementor\Modules\ContactForm\Widgets\Base\Base_Form;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Contact_Form_Seven extends Base_Form {

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
		return 'cmsmasters-contact-form-seven';
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
		return __( 'Contact Form 7', 'cmsmasters-elementor' );
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
		return 'cmsicon-contact-form-7';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'contact',
			'form',
			'email',
			'7',
			'seven',
		);
	}

	/**
	 * Specifying caching of the widget by default.
	 *
	 * @since 1.14.0
	 */
	protected function is_dynamic_content(): bool {
		return false;
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
		parent::register_controls();

		$this->start_controls_section(
			'section_error_style',
			array(
				'label' => __( 'Errors/Valid', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_error_m',
				'label' => __( 'Message Typography', 'cmsmasters-elementor' ),
				'selector' => '#cmsmasters_body {{WRAPPER}} .wpcf7-validation-errors, #cmsmasters_body {{WRAPPER}} .wpcf7-mail-sent-ok',
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'message_tabs' );

		$type = array(
			'error' => __( 'Error', 'cmsmasters-elementor' ),
			'complete' => __( 'Complete', 'cmsmasters-elementor' ),
		);

		foreach ( $type as $key => $label ) {

			$this->start_controls_tab(
				"message_{$key}",
				array(
					'label' => $label,
				)
			);

			if ( 'error' === $key ) {
				$selector = '#cmsmasters_body {{WRAPPER}} .wpcf7-validation-errors';
			} else {
				$selector = '#cmsmasters_body {{WRAPPER}} .wpcf7-mail-sent-ok';
			}

			$this->add_control(
				"background_color_{$key}",
				array(
					'label' => __( 'Message Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"color_{$key}",
				array(
					'label' => __( 'Message Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"border_color_{$key}",
				array(
					'label' => __( 'Message Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'border-color: {{VALUE}};',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'error_alignment_m',
			array(
				'label' => __( 'Message Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
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
				'default' => '',
				'separator' => 'before',
				'prefix_class' => 'elementor-widget-cmsmasters-contact-form__error-m-align-',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_error_m',
				'label' => __( 'Message Border', 'cmsmasters-elementor' ),
				'selector' => '#cmsmasters_body {{WRAPPER}} .wpcf7-validation-errors, #cmsmasters_body {{WRAPPER}} .wpcf7-mail-sent-ok',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'error_m_radius',
			array(
				'label' => __( 'Message Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .wpcf7-validation-errors' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'#cmsmasters_body {{WRAPPER}} .wpcf7-mail-sent-ok' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'error_m_margin',
			array(
				'label' => __( 'Message Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .wpcf7-validation-errors' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'#cmsmasters_body {{WRAPPER}} .wpcf7-mail-sent-ok' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'error_m_padding',
			array(
				'label' => __( 'Message Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'#cmsmasters_body {{WRAPPER}} .wpcf7-validation-errors' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'#cmsmasters_body {{WRAPPER}} .wpcf7-mail-sent-ok' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get Contact forms.
	 *
	 * Retrieve Contact form 7 plugin forms list.
	 *
	 * @since 1.0.0
	 * @since 1.2.4 Fixed display of the number of records.
	 *
	 * @return array Plugin forms.
	 */
	public function get_select_contact_form() {
		$options = array();

		$wpcf7_form_list = get_posts(
			array(
				'post_type' => 'wpcf7_contact_form',
				'numberposts' => -1,
			)
		);

		if ( ! empty( $wpcf7_form_list ) && ! is_wp_error( $wpcf7_form_list ) ) {
			foreach ( $wpcf7_form_list as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
		}

		return $options;
	}

	/**
	 * Get Plugin form Name.
	 *
	 * Retrieve Contact form 7 plugin name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin name.
	 */
	public function get_form_name() {
		return $this->get_title() . ': ';
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_input( $state = '' ) {
		return "#cmsmasters_body {{WRAPPER}} input:not([type=button]):not([type=checkbox]):not([type=file]):not([type=hidden]):not([type=image]):not([type=radio]):not([type=reset]):not([type=submit]):not([type=color]):not([type=range]){$state}";
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_select( $state = '' ) {
		return "#cmsmasters_body {{WRAPPER}} select{$state}";
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_textarea( $state = '' ) {
		return "#cmsmasters_body {{WRAPPER}} textarea{$state}";
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_submit() {
		return '#cmsmasters_body {{WRAPPER}} input[type=submit]';
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_selector_submit_hover() {
		return '#cmsmasters_body {{WRAPPER}} input[type=submit]:hover';
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_radio_checkbox_desc() {
		return '#cmsmasters_body {{WRAPPER}} .wpcf7-radio span.wpcf7-list-item-label, #cmsmasters_body {{WRAPPER}} .wpcf7-checkbox span.wpcf7-list-item-label, #cmsmasters_body {{WRAPPER}} .wpcf7-acceptance span.wpcf7-list-item-label';
	}

	/**
	 * Get selector widget.
	 *
	 * Retrieve the widget selector.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget selector.
	 */
	public function get_label_form() {
		return '#cmsmasters_body {{WRAPPER}} label, #cmsmasters_body {{WRAPPER}} .wpcf7-quiz-label';
	}

	/**
	 * Get Plugin admin url.
	 *
	 * Retrieve Contact form 7 plugin admin url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin admin url.
	 */
	public function get_url() {
		return esc_url( admin_url( 'admin.php?page=wpcf7' ) );
	}

	/**
	 * Get shortcode widget.
	 *
	 * Retrieve the widget shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget shortcode.
	 */
	public function get_shortcode() {
		$form_id = $this->get_form_id();
		return "[contact-form-7 id=\"{$form_id }\"]";
	}

	/**
	 * Get form id.
	 *
	 * Retrieve the form id.
	 *
	 * @since 1.1.0
	 *
	 * @return string The id form.
	 */
	public function get_form_id() {
		$settings = $this->get_settings_for_display();
		$form_id = $settings['form_list'];

		return $form_id;
	}
}
