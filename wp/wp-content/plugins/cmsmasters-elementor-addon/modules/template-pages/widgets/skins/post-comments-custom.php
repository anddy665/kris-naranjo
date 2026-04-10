<?php
namespace CmsmastersElementor\Modules\TemplatePages\Widgets\Skins;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Controls\Groups\Group_Control_Format_Date;
use CmsmastersElementor\Controls\Groups\Group_Control_Format_Time;
use CmsmastersElementor\Modules\TemplatePreview\Module as TemplatePreviewModule;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Utils as ElementorUtils;
use Elementor\Widget_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Post_Comments_Custom extends Post_Comments_Base {

	/**
	 * Get skin id.
	 *
	 * Retrieve skin id.
	 *
	 * @since 1.0.0
	 *
	 * @return string Skin id.
	 */
	public function get_id() {
		return 'custom';
	}

	/**
	 * Get skin title.
	 *
	 * Retrieve skin title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Skin title.
	 */
	public function get_title() {
		return __( 'Custom', 'cmsmasters-elementor' );
	}

	/**
	 * Register skin controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added background gradient to reply & submit.
	 * Added controls for submit to match global button control settings.
	 * Added control 'HTML Tag' for titles. Added gradient controls for comments.
	 * Added units to SLIDER & DIMENSIONS controls.
	 * @since 1.2.4 Fixed error with responsive controls in elementor 3.4.0
	 * @since 1.3.8 Added control settings reply & edit buttons for comments list.
	 * @since 1.4.0 Fixed selectors.
	 * @since 1.5.1 Fixed notice "_skin".
	 * @since 1.11.5 Fixed issues for Elementor 3.18.0.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->parent->start_injection( array( 'of' => '_skin' ) );

		$this->add_control(
			'source_custom',
			array(
				'label' => __( 'Custom Source', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => CmsmastersControls::QUERY,
				'autocomplete' => array( 'object' => Query_Manager::POST_OBJECT ),
			)
		);

		$this->parent->end_injection();

		$this->content_commentlist();

		$this->content_form();

		$this->content_advanced_options();

		$this->content_navigation();

		$this->style_comment_title();

		$this->style_comment();

		$this->style_comment_child();

		$this->style_avatar();

		$this->style_author();

		$this->style_reply();

		$this->style_date();

		$this->style_content();

		$this->style_navigation();

		$this->style_form_title();

		$this->style_form();

		$this->style_form_elements();
	}

	protected function content_commentlist() {
		$this->start_controls_section(
			'section_content_commentlist',
			array(
				'label' => __( 'Comment List', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'avatar_position',
			array(
				'label' => __( 'Avatar Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'aside' => array(
						'title' => __( 'Aside', 'cmsmasters-elementor' ),
						'description' => __( 'Stays aside from all content', 'cmsmasters-elementor' ),
					),
					'with-author' => array(
						'title' => __( 'With author', 'cmsmasters-elementor' ),
						'description' => __( 'Stays in line with author & date', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'aside',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-avatar-position-',
			)
		);

		$this->add_responsive_control(
			'avatar_size',
			array(
				'label' => __( 'Avatar Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array(
					'unit' => 'px',
					'size' => 100,
				),
				'range' => array(
					'px' => array(
						'min' => 50,
						'max' => 200,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment__avatar' => 'min-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .cmsmasters-single-post-comment__avatar img' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .cmsmasters-single-post-comment' => '--avatar-size: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'author_heading',
			array(
				'label' => __( 'Author', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'author_alignment',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
				),
				'label_block' => false,
				'default' => 'center',
				'toggle' => false,
				'selectors_dictionary' => array(
					'top' => 'flex-start;',
					'center' => 'center',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment__info' => 'align-items: {{VALUE}}; display: flex;',
				),
				'condition' => array( 'custom_avatar_position' => 'with-author' ),
			)
		);

		$this->add_control(
			'author_text_after',
			array(
				'label' => __( 'Author Text After', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'says:', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'author_highlight',
			array(
				'label' => __( 'Highlight Post Author', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'post_author',
			array(
				'label' => __( 'Post Author', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '(Post Author)', 'cmsmasters-elementor' ),
				'placeholder' => __( '(Post Author)', 'cmsmasters-elementor' ),
				'condition' => array( 'custom_author_highlight!' => '' ),
			)
		);

		$this->add_control(
			'date_heading',
			array(
				'label' => __( 'Date and Time', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'date_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inline' => array(
						'title' => __( 'Inline', 'cmsmasters-elementor' ),
					),
					'block' => array(
						'title' => __( 'Block', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'block',
				'toggle' => false,
			)
		);

		$this->add_control(
			'date_inline_position',
			array(
				'label' => __( 'Position in Line', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'label_block' => false,
				'default' => 'end',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-date-inline-',
				'condition' => array( 'custom_date_position' => 'inline' ),
			)
		);

		$this->add_group_control(
			Group_Control_Format_Date::get_type(),
			array(
				'name' => 'date',
				'exclude' => array( 'time' ),
			)
		);

		if ( ! empty( $this->parent->get_controls( 'custom_date_date_format' )['options'] ) ) {
			$options = $this->parent->get_controls( 'custom_date_date_format' )['options'];

			$options['disable'] = __( 'Disable', 'cmsmasters-elementor' );

			$this->update_control(
				'date_date_format',
				array( 'options' => $options )
			);
		}

		$this->add_control(
			'date_time_switcher',
			array(
				'label' => __( 'Enable Time', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'custom_date_date_format!' => array( 'disable', 'human_readable' ),
				),
			)
		);

		$this->add_control(
			'date_time_separator_text',
			array(
				'label' => __( 'Time Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'at',
				'condition' => array(
					'custom_date_date_format!' => array( 'disable', 'human_readable' ),
					'custom_date_time_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Format_Time::get_type(),
			array(
				'name' => 'time',
				'exclude' => array( 'date' ),
			)
		);

		if ( ! empty( $this->parent->get_controls( 'custom_time_time_format' )['options'] ) ) {
			$options = $this->parent->get_controls( 'custom_time_time_format' )['options'];

			$unused_option = array_pop( $options );

			$this->update_control(
				'time_time_format',
				array(
					'options' => $options,
					'condition' => array(
						'custom_date_date_format!' => array( 'disable', 'human_readable' ),
						'custom_date_time_switcher' => 'yes',
					),
				)
			);
		}

		$this->add_control(
			'date_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'condition' => array( 'custom_date_date_format!' => 'disable' ),
			)
		);

		$this->add_control(
			'button_heading',
			array(
				'label' => __( 'Buttons', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => array(
						'title' => __( 'Default', 'cmsmasters-elementor' ),
					),
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
					),
				),
				'prefix_class' => 'cmsmasters-button-position__',
				'default' => 'default',
				'toggle' => false,
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'reply_position',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inline' => array(
						'title' => __( 'Inline', 'cmsmasters-elementor' ),
					),
					'block' => array(
						'title' => __( 'Block', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'inline',
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-reply-position-',
			)
		);

		$this->add_control(
			'reply_heading',
			array(
				'label' => __( 'Reply', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'reply_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => __( 'Show Text', 'cmsmasters-elementor' ),
					),
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => __( 'Show Icon', 'cmsmasters-elementor' ),
					),
					'text-icon' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => __( 'Show Text & Icon', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'text',
				'toggle' => false,
			)
		);

		$this->add_control(
			'reply_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Reply', 'cmsmasters-elementor' ),
				'condition' => array( 'custom_reply_type!' => 'icon' ),
			)
		);

		$this->add_control(
			'reply_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-reply',
					'library' => 'fa-solid',
				),
				'condition' => array( 'custom_reply_type!' => 'text' ),
			)
		);

		$this->add_control(
			'edit_heading',
			array(
				'label' => __( 'Edit', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'edit_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Edit', 'cmsmasters-elementor' ),
			)
		);

		$this->end_controls_section();
	}

	protected function content_form() {
		$this->start_controls_section(
			'section_form_content',
			array(
				'label' => __( 'Form', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'form_view_as',
			array(
				'label' => __( 'View as', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'admin' => array(
						'title' => __( 'Logged in user', 'cmsmasters-elementor' ),
					),
					'user' => array(
						'title' => __( 'Logged out user', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => true,
				'default' => 'user',
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-form-view-',
			)
		);

		$this->add_control(
			'comment_heading',
			array(
				'label' => __( 'Comment Field', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'comment_direction',
			array(
				'label' => __( 'Direction', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'row' => array(
						'title' => __( 'Row', 'cmsmasters-elementor' ),
						'description' => __( 'Textarea & inputs in one column', 'cmsmasters-elementor' ),
					),
					'aside' => array(
						'title' => __( 'Aside', 'cmsmasters-elementor' ),
						'description' => __( 'Separates textarea & inputs to columns', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'aside',
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-comment-direction-',
			)
		);

		$this->add_control(
			'comment_area_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'end',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-comment-position-',
			)
		);

		$this->add_control(
			'input_heading',
			array(
				'label' => __( 'Input Fields', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'input_direction',
			array(
				'label' => __( 'Direction', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inline' => array(
						'title' => __( 'Inline', 'cmsmasters-elementor' ),
						'description' => __( 'Inputs in row', 'cmsmasters-elementor' ),
					),
					'rows' => array(
						'title' => __( 'Rows', 'cmsmasters-elementor' ),
						'description' => __( 'All inputs from new row', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'rows',
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-input-direction-',
				'condition' => array( 'custom_comment_direction' => 'row' ),
			)
		);

		$this->add_control(
			'website_input',
			array(
				'label' => __( 'Website Field', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-website-input-',
			)
		);

		$this->add_control(
			'website_input_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inline' => array(
						'title' => __( 'Inline', 'cmsmasters-elementor' ),
						'description' => __( 'Website input is in row with Name & Email inputs', 'cmsmasters-elementor' ),
					),
					'above' => array(
						'title' => __( 'Above', 'cmsmasters-elementor' ),
						'description' => __( 'Website input is in separate row above Name & Email inputs', 'cmsmasters-elementor' ),
					),
					'below' => array(
						'title' => __( 'Below', 'cmsmasters-elementor' ),
						'description' => __( 'Website input is in separate row below Name & Email inputs', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'inline',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-website-position-',
				'condition' => array(
					'custom_input_direction' => 'inline',
					'custom_website_input' => 'yes',
				),
			)
		);

		$this->add_control(
			'submit_button_content_heading',
			array(
				'label' => __( 'Submit Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'submit_button_align',
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
					'justify' => array(
						'title' => __( 'Justify', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'label_block' => false,
				'default' => 'left',
				'toggle' => false,
				'selectors_dictionary' => array(
					'left' => 'text-align:left;',
					'center' => 'text-align:center;',
					'right' => 'text-align:right;',
					'justify' => 'text-align:center; display:flex; flex-direction:column;',
				),
				'prefix_class' => 'cmsmasters-button-align-',
				'selectors' => array(
					'{{WRAPPER}} .form-submit' => '{{VALUE}}',
				),
			)
		);

		$this->add_control(
			'submit_button_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => __( 'Show Submit Text', 'cmsmasters-elementor' ),
					),
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => __( 'Show Submit Icon', 'cmsmasters-elementor' ),
					),
					'both' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => __( 'Show Submit Text & Icon', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'text',
				'toggle' => false,
			)
		);

		$this->add_control(
			'submit_button_icon_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'label_block' => false,
				'default' => 'end',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-submit-icon-position-',
				'render_type' => 'template',
				'condition' => array( 'custom_submit_button_type' => 'both' ),
			)
		);

		$this->end_controls_section();
	}

	protected function content_advanced_options() {
		$this->start_controls_section(
			'section_content_advanced_options',
			array(
				'label' => __( 'Advanced Options', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'label_heading',
			array(
				'label' => __( 'Field Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'use_label_instead_placeholder',
			array(
				'label' => __( 'Use Label Instead Of Placeholder', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-label-instead-placeholder-',
			)
		);

		$this->add_control(
			'label_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range' => array(
					'px' => array(
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments' => '--label-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'custom_use_label_instead_placeholder!' => 'yes' ),
			)
		);

		$this->add_control(
			'enable_input_icon',
			array(
				'label' => __( 'Field Icons', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-label-icon-',
			)
		);

		$input_icon = array( 'custom_enable_input_icon' => 'yes' );

		$this->start_controls_tabs(
			'field_icons_tabs',
			array(
				'separator' => 'before',
			)
		);

		$this->start_controls_tab(
			'field_icon_comment',
			array(
				'label' => __( 'Comment', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'comment_text',
			array(
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Comment', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'comment_icon',
			array(
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-comment',
					'library' => 'fa-solid',
				),
				'condition' => $input_icon,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'field_icon_name',
			array(
				'label' => __( 'Name', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'name_text',
			array(
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Name', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'name_icon',
			array(
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-user',
					'library' => 'fa-solid',
				),
				'condition' => $input_icon,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'field_icon_email',
			array(
				'label' => __( 'Email', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'email_text',
			array(
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Email', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'email_icon',
			array(
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-envelope',
					'library' => 'fa-solid',
				),
				'condition' => $input_icon,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'field_icon_website',
			array(
				'label' => __( 'Website', 'cmsmasters-elementor' ),
				'condition' => array( 'custom_website_input' => 'yes' ),
			)
		);

		$this->add_control(
			'website_text',
			array(
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Website', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'website_icon',
			array(
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-globe',
					'library' => 'fa-solid',
				),
				'condition' => $input_icon,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'submit_button_additional_heading',
			array(
				'label' => __( 'Submit Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'submit_button_text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => 'Post Comment',
			)
		);

		$this->add_control(
			'submit_button_icon',
			array(
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'recommended' => array(
					'fa-regular' => array(
						'envelope',
						'envelope-open',
					),
				),
				'default' => array(
					'value' => 'far fa-envelope',
					'library' => 'fa-regular',
				),
				'condition' => array( 'custom_submit_button_type!' => 'text' ),
			)
		);

		$this->add_control(
			'comment_heading_title',
			array(
				'label' => __( 'Comment Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'comment_title_tag',
			array(
				'label' => __( 'HTML Tag', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
				'default' => 'h3',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'comment_title_text',
			array(
				'label' => __( 'Text Only', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => 'Comments',
			)
		);

		$this->add_control(
			'comment_title_single_text',
			array(
				'label' => __( 'Single Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => 'One Comment',
			)
		);

		$this->add_control(
			'comment_title_multiple_text',
			array(
				'label' => __( 'Multiple Text', 'cmsmasters-elementor' ),
				/* translators: Addon %s: comments number */
				'description' => __( 'You can use %s instead of comments number.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( '%s Comments', 'cmsmasters-elementor' ), // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
			)
		);

		$this->add_control(
			'reply_heading_title',
			array(
				'label' => __( 'Form Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'leave_reply_tag',
			array(
				'label' => __( 'HTML Tag', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
				'default' => 'h3',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'leave_reply_text',
			array(
				'label' => __( 'Leave A Reply Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => 'Leave A Reply',
			)
		);

		$this->add_control(
			'leave_reply_to_text',
			array(
				'label' => __( 'Leave A Reply to Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Leave A Reply to %s', 'cmsmasters-elementor' ), // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
			)
		);

		$this->end_controls_section();
	}

	protected function content_navigation() {
		$this->start_controls_section(
			'section_content_navigation',
			array(
				'label' => __( 'Navigation', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'navigation_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => __( 'Show Text', 'cmsmasters-elementor' ),
					),
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => __( 'Show Icon', 'cmsmasters-elementor' ),
					),
					'text-icon' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => __( 'Show Text & Icon', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'text',
				'toggle' => false,
			)
		);

		$this->start_controls_tabs( 'navigation_content_tabs' );

		$this->start_controls_tab(
			'navigation_content_tab_previous',
			array(
				'label' => __( 'Previous', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'navigation_previous_icon',
			array(
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-angle-left',
					'library' => 'fa-solid',
				),
				'skin' => 'inline',
				'exclude_inline_options' => array( 'svg' ),
				'condition' => array(
					'custom_navigation_type!' => 'text',
				),
			)
		);

		$this->add_control(
			'navigation_text_previous',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => 'Older Comments',
				'condition' => array( 'custom_navigation_type!' => 'icon' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'navigation_content_tab_next',
			array(
				'label' => __( 'Next', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'navigation_next_icon',
			array(
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-angle-right',
					'library' => 'fa-solid',
				),
				'skin' => 'inline',
				'exclude_inline_options' => array( 'svg' ),
				'condition' => array( 'custom_navigation_type!' => 'text' ),
			)
		);

		$this->add_control(
			'navigation_text_next',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => 'Newer Comments',
				'condition' => array( 'custom_navigation_type!' => 'icon' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function style_comment_title() {
		$this->start_controls_section(
			'section_style_comments_title',
			array(
				'label' => __( 'Comments Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'comment_title_alignment',
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
				'label_block' => false,
				'prefix_class' => 'cmsmasters-comment-title-align-',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'comment_title_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-single-post-comments__title',
			)
		);

		$this->add_control(
			'comment_title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'comment_title_text_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-single-post-comments__title',
			)
		);

		$this->add_responsive_control(
			'comment_title_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'comment_title_width_auto',
			array(
				'label' => __( 'Width Auto', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'cmsmasters-comment-title-width-auto-',
				'condition' => array( 'custom_comment_title_show_lines' => '' ),
			)
		);

		$this->add_responsive_control(
			'comment_title_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 30,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__title' => 'width: {{SIZE}}%;',
				),
				'condition' => array( 'custom_comment_title_show_lines' => 'yes' ),
			)
		);

		$this->add_control(
			'comment_title_show_lines',
			array(
				'label' => __( 'Decoration Lines', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-comment-title-show-lines-',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'comment_title_separator',
				'selector' => '{{WRAPPER}} .cmsmasters-single-post-comments .cmsmasters-single-post-comments__title:before, {{WRAPPER}} .cmsmasters-single-post-comments .cmsmasters-single-post-comments__title:after',
				'fields_options' => array(
					'border' => array(
						'label' => __( 'Lines Type', 'cmsmasters-elementor' ),
						'default' => 'solid',
					),
					'width' => array(
						'label' => __( 'Lines Width', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => array( 'px' ),
						'default' => array( 'size' => 1 ),
						'range' => array(
							'px' => array(
								'max' => 10,
							),
						),
						'selectors' => array(
							'{{SELECTOR}}' => 'border-top-width: {{SIZE}}{{UNIT}};',
						),
					),
					'color' => array(
						'label' => __( 'Lines Color', 'cmsmasters-elementor' ),
					),
				),
				'condition' => array( 'custom_comment_title_show_lines' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'comment_title_separator_gap',
			array(
				'label' => __( 'Lines Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments' => '--comment-title-border-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'custom_comment_title_show_lines' => 'yes',
					'custom_comment_title_separator_border!' => '',
				),
			)
		);

		$this->add_control(
			'comment_title_background',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__title span' => 'background-color: {{VALUE}}',
				),
				'condition' => array( 'custom_comment_title_show_lines' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'comment_title_border',
				'selector' => '{{WRAPPER}}.cmsmasters-comment-title-width-auto-yes .cmsmasters-single-post-comments__title, {{WRAPPER}}:not(.cmsmasters-comment-title-width-auto-yes) .cmsmasters-single-post-comments__title span',
				'fields_options' => array(
					'border' => array(
						'separator' => 'before',
					),
				),
				'condition' => array( 'custom_comment_title_show_lines' => '' ),
			)
		);

		$title_style_condition = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'custom_comment_title_show_lines',
					'operator' => '=',
					'value' => '',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'custom_comment_title_border_border',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'custom_comment_title_background',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			),
		);

		$this->add_responsive_control(
			'comment_title_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__title span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $title_style_condition,
			)
		);

		$this->add_responsive_control(
			'comment_title_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__title span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $title_style_condition,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'comment_title_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-single-post-comments__title',
				'conditions' => $title_style_condition,
			)
		);

		$this->add_control(
			'comment_title_stroke_heading',
			array(
				'label' => __( 'Stroke', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'comment_title_stroke_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '',
				),
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
					'em' => array(
						'min' => 0,
						'max' => 0.2,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__title' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'comment_title_stroke_color_normal',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__title' => '-webkit-text-stroke-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'custom_comment_title_stroke_width[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->add_control(
			'comment_title_stroke_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__title:hover' => '-webkit-text-stroke-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'custom_comment_title_stroke_width[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->add_control(
			'comment_title_stroke_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 0.3 ),
				'range' => array(
					'px' => array(
						'max' => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__title' => 'transition: all {{SIZE}}s',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'custom_comment_title_stroke_width[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->add_control(
			'comment_title_icon_heading',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'comment_title_icon',
			array(
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
			)
		);

		$this->add_control(
			'comment_title_icon_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array( 'size' => 10 ),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 30,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__title i,' .
					'{{WRAPPER}} .cmsmasters-single-post-comments__title svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'custom_comment_title_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'comment_title_icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__title i,' .
					'{{WRAPPER}} .cmsmasters-single-post-comments__title svg' => 'color: {{VALUE}}',
				),
				'condition' => array( 'custom_comment_title_icon[value]!' => '' ),
			)
		);

		$this->end_controls_section();
	}

	protected function style_comment() {
		$this->start_controls_section(
			'section_style_comment',
			array(
				'label' => __( 'Comment', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'comment_list_width',
			array(
				'label' => __( 'Comment List Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vh', 'vw' ),
				'range' => array(
					'%' => array(
						'min' => 30,
					),
					'px' => array(
						'min' => 300,
						'max' => 1300,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__list' => 'width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto; max-width: 100%;',
				),
			)
		);

		$this->add_responsive_control(
			'comment_gap_between',
			array(
				'label' => __( 'Gap Between Comments', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment' => '--wrapper-between-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'comment_border_border',
			array(
				'label' => __( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'' => __( 'None', 'cmsmasters-elementor' ),
					'solid' => _x( 'Solid', 'Border Type', 'cmsmasters-elementor' ),
					'double' => _x( 'Double', 'Border Type', 'cmsmasters-elementor' ),
					'dotted' => _x( 'Dotted', 'Border Type', 'cmsmasters-elementor' ),
					'dashed' => _x( 'Dashed', 'Border Type', 'cmsmasters-elementor' ),
					'groove' => _x( 'Groove', 'Border Type', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'selectors_dictionary' => array(
					'' => 'none; --wrapper-border-top: 0px; --wrapper-border-left: 0px;',
					'default' => 'default; --wrapper-border-top: 0px; --wrapper-border-left: 0px;',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment__body' => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'comment_border_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment__body' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-single-post-comment' => '--wrapper-border-top: {{TOP}}{{UNIT}}; --wrapper-border-left: {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'custom_comment_border_border!' => array(
						'',
						'default',
					),
				),
			)
		);

		$comment_states = array(
			'' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		$this->start_controls_tabs(
			'comment_tabs',
			array( 'separator' => 'before' )
		);

		foreach ( $comment_states  as $comments_key => $label ) {
			$comment_selector = '{{WRAPPER}} .cmsmasters-single-post-comment__body';

			if ( 'hover' === $comments_key ) {
				$comment_key = '_' . $comments_key;
				$gradient_selector = '{{WRAPPER}} .cmsmasters-single-post-comment__bg:after';
				$comment_selector .= ':hover';
			} else {
				$comment_key = $comments_key;
				$gradient_selector = '{{WRAPPER}} .cmsmasters-single-post-comment__bg:before';
			}

			$this->start_controls_tab(
				"comment_tab{$comment_key}",
				array(
					'label' => $label,
				)
			);

			$this->add_group_control(
				CmsmastersControls::BUTTON_BACKGROUND_GROUP,
				array(
					'name' => "comment_background{$comment_key}",
					'selector' => $gradient_selector,
				)
			);

			$this->add_control(
				"comment_border_color{$comment_key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$comment_selector => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'custom_comment_border_border!' => '',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "comment_box_shadow{$comment_key}",
					'selector' => $comment_selector,
				)
			);

			$this->add_responsive_control(
				"comment_border_radius{$comment_key}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors' => array(
						$comment_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			if ( 'hover' === $comments_key ) {
				$this->add_control(
					'comment_transition',
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'default' => array( 'size' => 0.3 ),
						'range' => array(
							'px' => array(
								'max' => 3,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							'{{WRAPPER}} .cmsmasters-single-post-comment__body, {{WRAPPER}} .cmsmasters-single-post-comment__bg:before, {{WRAPPER}} .cmsmasters-single-post-comment__bg:after' => 'transition: all {{SIZE}}s',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'comment_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment' => '--wrapper-padding-top: {{TOP}}{{UNIT}}; --wrapper-padding-right: {{RIGHT}}{{UNIT}}; --wrapper-padding-bottom: {{BOTTOM}}{{UNIT}}; --wrapper-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function style_comment_child() {
		$this->start_controls_section(
			'section_comment_child',
			array(
				'label' => __( 'Child Comment', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'comment_level_gap',
			array(
				'label' => __( 'Level Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .depth-2 > .cmsmasters-single-post-comment__body' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .depth-3 > .cmsmasters-single-post-comment__body' => 'margin-left: calc( {{SIZE}}{{UNIT}} * 2 );',
					'{{WRAPPER}} .depth-4 > .cmsmasters-single-post-comment__body' => 'margin-left: calc( {{SIZE}}{{UNIT}} * 3 );',
					'{{WRAPPER}} .depth-5 > .cmsmasters-single-post-comment__body' => 'margin-left: calc( {{SIZE}}{{UNIT}} * 4 );',
					'{{WRAPPER}} .depth-6 > .cmsmasters-single-post-comment__body' => 'margin-left: calc( {{SIZE}}{{UNIT}} * 5 );',
					'{{WRAPPER}} .depth-7 > .cmsmasters-single-post-comment__body' => 'margin-left: calc( {{SIZE}}{{UNIT}} * 6 );',
					'{{WRAPPER}} .cmsmasters-single-post-comment' => '--wrapper-level-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$wrap_child = '{{WRAPPER}} .children .cmsmasters-single-post-comment';

		$this->add_responsive_control(
			'comment_child_gap_between',
			array(
				'label' => __( 'Gap Between Comments', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					$wrap_child => '--wrapper-between-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'child_avatar_size',
			array(
				'label' => __( 'Avatar Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array(
					'unit' => 'px',
					'size' => 100,
				),
				'tablet_default' => array(
					'unit' => 'px',
					'size' => 100,
				),
				'mobile_default' => array(
					'unit' => 'px',
					'size' => 70,
				),
				'range' => array(
					'px' => array(
						'min' => 50,
						'max' => 200,
						'step' => 5,
					),
				),
				'selectors' => array(
					"{$wrap_child}__avatar" => 'min-width: {{SIZE}}{{UNIT}}',
					"{$wrap_child}__avatar img" => 'width: {{SIZE}}{{UNIT}}',
					$wrap_child => '--avatar-size: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'comment_child_background',
				'exclude' => array( 'image' ),
				'selector' => "{$wrap_child}__body",
			)
		);

		$this->add_control(
			'comment_child_border',
			array(
				'label' => _x( 'Border Type', 'Border Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'default' => _x( 'Default', 'Border Control', 'cmsmasters-elementor' ),
					'none' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
					'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'selectors_dictionary' => array(
					'none' => 'none; --wrapper-border-top: 0px; --wrapper-border-left: 0px;',
				),
				'separator' => 'before',
				'selectors' => array(
					"{$wrap_child}__body" => 'border-style: {{VALUE}};',
				),
				'condition' => array(
					'custom_comment_border_border!' => array(
						'',
						'default',
					),
				),
			)
		);

		$this->add_control(
			'comment_child_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					"{$wrap_child}__body" => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					$wrap_child => '--wrapper-border-top: {{TOP}}{{UNIT}}; --wrapper-border-left: {{LEFT}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'custom_comment_child_border',
							'operator' => '!==',
							'value' => 'none',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'custom_comment_border_border',
									'operator' => '!==',
									'value' => 'none',
								),
								array(
									'name' => 'custom_comment_border_border',
									'operator' => '!==',
									'value' => 'default',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'comment_child_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					"{$wrap_child}__body" => 'border-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'custom_comment_child_border',
							'operator' => '!==',
							'value' => 'none',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'custom_comment_border_border',
									'operator' => '!==',
									'value' => 'none',
								),
								array(
									'name' => 'custom_comment_border_border',
									'operator' => '!==',
									'value' => 'default',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'comment_child_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					"{$wrap_child}__body" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'comment_child_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator' => 'before',
				'selectors' => array(
					$wrap_child => '--wrapper-padding-top: {{TOP}}{{UNIT}}; --wrapper-padding-right: {{RIGHT}}{{UNIT}}; --wrapper-padding-bottom: {{BOTTOM}}{{UNIT}}; --wrapper-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'comment_child_box_shadow',
				'selector' => "{$wrap_child}__body",
			)
		);

		$this->add_control(
			'comment_child_line_heading',
			array(
				'label' => __( 'Thread Line', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'comment_child_line_style',
			array(
				'label' => __( 'Line Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
					'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
					'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
					'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
					'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
					'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
					'ridge' => _x( 'Ridge', 'Border Control', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment' => '--wrapper-thread-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'comment_child_line_size',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment' => '--wrapper-thread-width: {{SIZE}}px;',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'custom_comment_child_line_style',
							'operator' => '!==',
							'value' => 'none',
						),
					),
				),
			)
		);

		$this->add_control(
			'comment_child_line_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment:before,
					{{WRAPPER}} .cmsmasters-single-post-comment:after,
					{{WRAPPER}} .cmsmasters-single-post-comment__body:before,
					{{WRAPPER}} .cmsmasters-single-post-comment__body:after' => 'border-color: {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'custom_comment_child_line_style',
							'operator' => '!==',
							'value' => 'none',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function style_navigation() {
		$this->start_controls_section(
			'section_style_navigation',
			array(
				'label' => __( 'Navigation', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'navigation_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array( 'size' => 20 ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__title + .comment-navigation' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-single-post-comments__list + .comment-navigation' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'navigation_icon_heading',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'custom_navigation_type!' => 'text' ),
			)
		);

		$this->add_control(
			'navigation_icon_gap',
			array(
				'label' => __( 'Icon Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array( 'size' => 5 ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments .nav-previous i,' .
					'{{WRAPPER}} .cmsmasters-single-post-comments .nav-previous svg' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-single-post-comments .nav-next i,' .
					'{{WRAPPER}} .cmsmasters-single-post-comments .nav-next svg' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'custom_navigation_type!' => 'text' ),
			)
		);

		$this->add_control(
			'navigation_text_heading',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'custom_navigation_type!' => 'icon' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'navigation_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-single-post-comments .nav-links a',
				'condition' => array( 'custom_navigation_type!' => 'icon' ),
			)
		);

		$this->add_control(
			'navigation_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'link' => array(
						'title' => __( 'Link', 'cmsmasters-elementor' ),
					),
					'button' => array(
						'title' => __( 'Button', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'link',
				'toggle' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'navigation_border',
				'exclude' => array( 'color' ),
				'selector' => '{{WRAPPER}} .cmsmasters-single-post-comments .nav-links a',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
				),
				'condition' => array( 'custom_navigation_view' => 'button' ),
			)
		);

		$this->start_controls_tabs(
			'navigation_tabs',
			array( 'separator' => 'before' )
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $tabs_key => $label ) {
			$tab_selector = '{{WRAPPER}} .cmsmasters-single-post-comments .nav-links a';

			if ( 'hover' === $tabs_key ) {
				$tab_selector .= ':hover';
			}

			$this->start_controls_tab(
				"navigation_tab_{$tabs_key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"navigation_color_{$tabs_key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$tab_selector => 'color: {{VALUE}}',
					),
				)
			);

			$navigation_condition = array(
				'custom_navigation_view' => 'button',
			);

			$this->add_control(
				"navigation_bg_{$tabs_key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$tab_selector => 'background-color: {{VALUE}}',
					),
					'condition' => $navigation_condition,
				)
			);

			$this->add_control(
				"navigation_border_{$tabs_key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$tab_selector => 'border-color: {{VALUE}}',
					),
					'condition' => $navigation_condition,
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "navigation_box_shadow_{$tabs_key}",
					'selector' => $tab_selector,
					'condition' => $navigation_condition,
				)
			);

			if ( 'hover' === $tabs_key ) {
				$this->add_control(
					'navigation_transition',
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'default' => array( 'size' => 0.3 ),
						'range' => array(
							'px' => array(
								'max' => 3,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							'{{WRAPPER}} .cmsmasters-single-post-comments .nav-links a' => 'transition: all {{SIZE}}s',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'navigation_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments .nav-links a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $navigation_condition,
			)
		);

		$this->add_responsive_control(
			'navigation_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments .nav-links a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => $navigation_condition,
			)
		);

		$this->end_controls_section();
	}

	protected function style_avatar() {
		$this->start_controls_section(
			'section_style_avatar',
			array(
				'label' => __( 'Avatar', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'avatar_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment' => '--avatar-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'avatar_border',
				'selector' => '{{WRAPPER}} .cmsmasters-single-post-comment__avatar img',
				'fields_options' => array(
					'border' => array(
						'separator' => 'before',
					),
				),
			)
		);

		$this->add_responsive_control(
			'avatar_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment__avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'avatar_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-single-post-comment__avatar img',
			)
		);

		$this->end_controls_section();
	}

	protected function style_author() {
		$wrapper = '{{WRAPPER}} .cmsmasters-single-post-comment';

		$this->start_controls_section(
			'section_style_author',
			array(
				'label' => __( 'Author', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'author_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment' => '--author-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'author_typography',
				'selector' => "{$wrapper}__author, {$wrapper}__author a",
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'author_text_shadow',
				'selector' => "{$wrapper}__author, {$wrapper}__author a",
			)
		);

		$this->start_controls_tabs(
			'author_tabs',
			array(
				'separator' => 'before',
			)
		);

		/* Start Tab Title Tab */
		$this->start_controls_tab(
			'author_tab_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'author_color_normal',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$wrapper}__author a, {$wrapper}__author" => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'author_tab_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'author_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$wrapper}__author a:hover, {$wrapper}__author:hover" => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'author_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 0.3 ),
				'range' => array(
					'px' => array(
						'max' => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					"{$wrapper}__author a, {$wrapper}__author" => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$text_after = array( 'custom_author_text_after!' => '' );

		$this->add_control(
			'author_text_after_heading',
			array(
				'label' => __( 'Text After', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $text_after,
			)
		);

		$this->add_control(
			'author_text_after_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					"{$wrapper}__author .cmsmasters-text-after" => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => $text_after,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'author_text_after_typography',
				'selector' => "{$wrapper}__author .cmsmasters-text-after",
				'condition' => $text_after,
			)
		);

		$this->add_control(
			'author_text_after_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					"{$wrapper}__author .cmsmasters-text-after" => 'color: {{VALUE}}',
				),
				'condition' => $text_after,
			)
		);

		$author_highlight = array( 'custom_author_highlight' => 'yes' );

		$this->add_control(
			'author_highlight_heading',
			array(
				'label' => __( 'Highlight', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $author_highlight,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'author_highlight_border',
				'exclude' => array( 'color' ),
				'selector' => "{$wrapper}[class*=\"comment-author-\"] .cmsmasters-single-post-comment__author",
				'condition' => $author_highlight,
			)
		);

		$author_condition = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'custom_author_highlight',
					'operator' => '=',
					'value' => 'yes',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'custom_author_highlight_background_color_normal',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'custom_author_highlight_border_border',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			),
		);

		$this->add_responsive_control(
			'author_highlight_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'separator' => 'before',
				'selectors' => array(
					"{$wrapper}[class*=\"comment-author-\"] .cmsmasters-single-post-comment__author, {$wrapper}[class*=\"comment-author-\"] .cmsmasters-single-post-comment__author a" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $author_condition,
			)
		);

		$link_states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		$this->start_controls_tabs(
			'highlight_tabs',
			array(
				'separator' => 'before',
				'condition' => $author_highlight,
			)
		);

		foreach ( $link_states as $tabs_key => $label ) {
			$selector = "{$wrapper}[class*=\"comment-author-\"] .cmsmasters-single-post-comment__author";
			$highlight_tab_selector = implode( ', ', array( $selector, "{$selector} a" ) );

			if ( 'hover' === $tabs_key ) {
				$highlight_tab_selector = implode( ', ', array( "{$selector}:hover", "{$selector} a:hover" ) );
			}

			$this->start_controls_tab(
				"highlight_tab_{$tabs_key}",
				array(
					'label' => $label,
					'condition' => $author_highlight,
				)
			);

			$this->add_control(
				"author_highlight_color_{$tabs_key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#1869CA',
					'selectors' => array(
						$highlight_tab_selector => 'color: {{VALUE}}',
					),
					'condition' => $author_highlight,
				)
			);

			$this->add_control(
				"author_highlight_background_color_{$tabs_key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$highlight_tab_selector => 'background-color: {{VALUE}}',
					),
					'condition' => $author_highlight,
				)
			);

			$this->add_control(
				"author_highlight_border_color_{$tabs_key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$highlight_tab_selector => 'border-color: {{VALUE}}',
					),
					'condition' => array(
						'custom_author_highlight' => 'yes',
						'custom_author_highlight_border_border!' => '',
					),
				)
			);

			if ( 'hover' === $tabs_key ) {
				$this->add_control(
					'author_highlight_transition',
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'default' => array( 'size' => 0.3 ),
						'range' => array(
							'px' => array(
								'max' => 3,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							"{$wrapper}[class*=\"comment-author-\"] .cmsmasters-single-post-comment__author, {$wrapper}[class*=\"comment-author-\"] .cmsmasters-single-post-comment__author a" => 'transition: all {{SIZE}}s',
						),
						'condition' => $author_highlight,
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'author_highlight_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					"{$wrapper}[class*=\"comment-author-\"] .cmsmasters-single-post-comment__author, {$wrapper}[class*=\"comment-author-\"] .cmsmasters-single-post-comment__author a" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $author_condition,
			)
		);

		$this->add_control(
			'post_author_heading',
			array(
				'label' => __( 'Post Author', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $author_highlight,
			)
		);

		$this->add_control(
			'post_author_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					"{$wrapper}__author .cmsmasters-post-author" => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => $author_highlight,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'post_author_typography',
				'selector' => "{$wrapper}__author .cmsmasters-post-author",
				'condition' => $author_highlight,
			)
		);

		$this->add_control(
			'post_author_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#1869CA',
				'selectors' => array(
					"{$wrapper}__author .cmsmasters-post-author" => 'color: {{VALUE}}',
				),
				'condition' => $author_highlight,
			)
		);

		$this->end_controls_section();
	}

	protected function style_reply() {
		$wrapper = '{{WRAPPER}} .cmsmasters-single-post-comment';

		$reply_condition = array(
			'custom_reply_view' => 'button',
		);

		$this->start_controls_section(
			'section_style_reply',
			array(
				'label' => __( 'Reply & Edit', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'comments_button_gap',
			array(
				'label' => __( 'Buttons Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment' => '--comments-button-margin: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'custom_button_position!' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'comments_button_gap_top',
			array(
				'label' => __( 'Buttons Top Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment' => '--comments-button-margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'custom_button_position' => 'bottom' ),
				'separator' => 'after',
			)
		);

		$this->add_control(
			'title_reply',
			array(
				'label' => __( 'Reply', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'reply_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'link' => array(
						'title' => __( 'Link', 'cmsmasters-elementor' ),
					),
					'button' => array(
						'title' => __( 'Button', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'link',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-reply-button-view-',
				'render_type' => 'template',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'reply_typography',
				'selector' => "{$wrapper}__reply, {$wrapper}__reply .comment-reply-link",
				'condition' => array( 'custom_reply_type!' => 'icon' ),
			)
		);

		$link_states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		$this->start_controls_tabs(
			'reply_tabs',
			array( 'separator' => 'before' )
		);

		foreach ( $link_states  as $tabs_key => $label ) {
			$tab_selector = "{$wrapper}__reply .comment-reply-link";

			if ( 'hover' === $tabs_key ) {
				$gradient_selector = $tab_selector . ':after';
				$tab_selector .= ':hover';
			} else {
				$gradient_selector = $tab_selector . ':before';
			}

			$this->start_controls_tab(
				"reply_tab_{$tabs_key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"reply_color_{$tabs_key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$tab_selector => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				"reply_bg_group_{$tabs_key}_background",
				array(
					'label' => __( 'Background Type', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'color' => array(
							'title' => __( 'Color', 'cmsmasters-elementor' ),
							'icon' => 'eicon-paint-brush',
						),
						'gradient' => array(
							'title' => __( 'Gradient', 'cmsmasters-elementor' ),
							'icon' => 'eicon-barcode',
						),
					),
					'default' => 'color',
					'toggle' => false,
					'render_type' => 'ui',
					'condition' => $reply_condition,
				)
			);

			$this->add_control(
				"reply_bg_{$tabs_key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$gradient_selector => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						'custom_reply_view' => 'button',
						"custom_reply_bg_group_{$tabs_key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"reply_bg_group_{$tabs_key}_color_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 0,
					),
					'render_type' => 'ui',
					'condition' => array(
						'custom_reply_view' => 'button',
						"custom_reply_bg_group_{$tabs_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"reply_bg_group_{$tabs_key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						'custom_reply_view' => 'button',
						"custom_reply_bg_group_{$tabs_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"reply_bg_group_{$tabs_key}_color_b_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'render_type' => 'ui',
					'condition' => array(
						'custom_reply_view' => 'button',
						"custom_reply_bg_group_{$tabs_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"reply_bg_group_{$tabs_key}_gradient_type",
				array(
					'label' => __( 'Type', 'cmsmasters-elementor' ),
					'label_block' => false,
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'linear' => __( 'Linear', 'cmsmasters-elementor' ),
						'radial' => __( 'Radial', 'cmsmasters-elementor' ),
					),
					'default' => 'linear',
					'render_type' => 'ui',
					'condition' => array(
						'custom_reply_view' => 'button',
						"custom_reply_bg_group_{$tabs_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"reply_bg_group_{$tabs_key}_gradient_angle",
				array(
					'label' => __( 'Angle', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'deg' ),
					'default' => array(
						'unit' => 'deg',
						'size' => 180,
					),
					'range' => array(
						'deg' => array( 'step' => 10 ),
					),
					'selectors' => array(
						$gradient_selector => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{custom_reply_bg_group_{$tabs_key}_color_stop.SIZE}}{{custom_reply_bg_group_{$tabs_key}_color_stop.UNIT}}, {{custom_reply_bg_group_{$tabs_key}_color_b.VALUE}} {{custom_reply_bg_group_{$tabs_key}_color_b_stop.SIZE}}{{custom_reply_bg_group_{$tabs_key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						'custom_reply_view' => 'button',
						"custom_reply_bg_group_{$tabs_key}_background" => array( 'gradient' ),
						"custom_reply_bg_group_{$tabs_key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"reply_bg_group_{$tabs_key}_gradient_position",
				array(
					'label' => __( 'Position', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => array(
						'center center' => __( 'Center Center', 'cmsmasters-elementor' ),
						'center left' => __( 'Center Left', 'cmsmasters-elementor' ),
						'center right' => __( 'Center Right', 'cmsmasters-elementor' ),
						'top center' => __( 'Top Center', 'cmsmasters-elementor' ),
						'top left' => __( 'Top Left', 'cmsmasters-elementor' ),
						'top right' => __( 'Top Right', 'cmsmasters-elementor' ),
						'bottom center' => __( 'Bottom Center', 'cmsmasters-elementor' ),
						'bottom left' => __( 'Bottom Left', 'cmsmasters-elementor' ),
						'bottom right' => __( 'Bottom Right', 'cmsmasters-elementor' ),
					),
					'default' => 'center center',
					'selectors' => array(
						$gradient_selector => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{custom_reply_bg_group_{$tabs_key}_color_stop.SIZE}}{{custom_reply_bg_group_{$tabs_key}_color_stop.UNIT}}, {{custom_reply_bg_group_{$tabs_key}_color_b.VALUE}} {{custom_reply_bg_group_{$tabs_key}_color_b_stop.SIZE}}{{custom_reply_bg_group_{$tabs_key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						'custom_reply_view' => 'button',
						"custom_reply_bg_group_{$tabs_key}_background" => array( 'gradient' ),
						"custom_reply_bg_group_{$tabs_key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"reply_border_{$tabs_key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$tab_selector => 'border-color: {{VALUE}}',
					),
					'condition' => $reply_condition,
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "reply_text_shadow_{$tabs_key}",
					'selector' => $tab_selector,
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "reply_box_shadow_{$tabs_key}",
					'selector' => $tab_selector,
					'condition' => $reply_condition,
				)
			);

			$border_radius_id = ( 'hover' === $tabs_key ) ? "reply_border_radius_{$tabs_key}" : 'reply_border_radius';

			$this->add_responsive_control(
				$border_radius_id,
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors' => array(
						$tab_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition' => $reply_condition,
				)
			);

			if ( 'hover' === $tabs_key ) {
				$this->add_control(
					'reply_transition',
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'default' => array( 'size' => 0.3 ),
						'range' => array(
							'px' => array(
								'max' => 3,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							"{$wrapper}__reply .comment-reply-link, {$wrapper}__reply .comment-reply-link:before, {$wrapper}__reply .comment-reply-link:after, {$wrapper}__reply i, {$wrapper}__reply svg" => 'transition: all {{SIZE}}s',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'reply_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment' => '--reply-margin: {{SIZE}}{{UNIT}};',
				),
				'separator' => 'before',
				'condition' => array( 'custom_button_position' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'reply_border',
				'exclude' => array( 'color' ),
				'selector' => "{$wrapper}__reply .comment-reply-link",
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
				),
				'separator' => 'before',
				'condition' => $reply_condition,
			)
		);

		$this->add_responsive_control(
			'reply_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					"{$wrapper}__reply .comment-reply-link" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'separator' => 'before',
				'condition' => $reply_condition,
			)
		);

		$this->add_control(
			'reply_icon_heading',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'custom_reply_type!' => 'text' ),
			)
		);

		$this->add_control(
			'reply_icon_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array( 'size' => 5 ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment' => '--reply-icon-margin: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'custom_reply_type' => 'text-icon' ),
			)
		);

		$this->add_responsive_control(
			'reply_text_icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					"{$wrapper}__reply .comment-reply-link i, {$wrapper}__reply .comment-reply-link svg" => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'custom_reply_type!' => 'text' ),
			)
		);

		$this->add_control(
			'reply_icon_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'label_block' => false,
				'default' => 'left',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-reply-icon-',
				'condition' => array( 'custom_reply_type' => 'text-icon' ),
			)
		);

		$reply_icon_states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		$this->start_controls_tabs(
			'reply_icon_tabs',
			array(
				'separator' => 'before',
				'condition' => array( 'custom_reply_type!' => 'text' ),
			)
		);

		foreach ( $reply_icon_states  as $reply_icon_key => $label ) {
			if ( 'hover' === $reply_icon_key ) {
				$reply_icon_selector = "{$wrapper}__reply .comment-reply-link:hover i, {$wrapper}__reply .comment-reply-link:hover svg";
			} else {
				$reply_icon_selector = "{$wrapper}__reply .comment-reply-link i, {$wrapper}__reply .comment-reply-link svg";
			}

			$this->start_controls_tab(
				"reply_icon_tab_{$reply_icon_key}",
				array(
					'label' => $label,
					'condition' => array( 'custom_reply_type!' => 'text' ),
				)
			);

			$this->add_control(
				"reply_icon_color_{$reply_icon_key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$reply_icon_selector => 'color: {{VALUE}}',
					),
					'condition' => array( 'custom_reply_type!' => 'text' ),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "reply_icon_text_shadow_{$reply_icon_key}",
					'selector' => $reply_icon_selector,
					'condition' => array( 'custom_reply_type!' => 'text' ),
				)
			);

			if ( 'hover' === $reply_icon_key ) {
				$this->add_control(
					'reply_icon_transition',
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'default' => array( 'size' => 0.3 ),
						'range' => array(
							'px' => array(
								'max' => 3,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							"{$wrapper}__reply .comment-reply-link i, {$wrapper}__reply .comment-reply-link svg" => 'transition: all {{SIZE}}s',
						),
						'condition' => array( 'custom_reply_type!' => 'text' ),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'title_edit',
			array(
				'label' => __( 'Edit', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$edit_condition = array(
			'custom_edit_view' => 'button',
		);

		$this->add_control(
			'edit_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'link' => array(
						'title' => __( 'Link', 'cmsmasters-elementor' ),
					),
					'button' => array(
						'title' => __( 'Button', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'link',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-edit-button-view-',
				'render_type' => 'template',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'edit_typography',
				'selector' => '{{WRAPPER}} .comment-edit-link',
			)
		);

		$link_edit_states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		$this->start_controls_tabs(
			'edit_tabs',
			array( 'separator' => 'before' )
		);

		foreach ( $link_edit_states  as $tabs_key => $label ) {
			$tab_selector = "{$wrapper} .comment-edit-link";

			if ( 'hover' === $tabs_key ) {
				$gradient_selector = $tab_selector . ':after';
				$tab_selector .= ':hover';
			} else {
				$gradient_selector = $tab_selector . ':before';
			}

			$this->start_controls_tab(
				"edit_tab_{$tabs_key}",
				array(
					'label' => $label,
				)
			);

			$color_id = ( 'hover' === $tabs_key ) ? "edit_color_{$tabs_key}" : 'edit_color';

			$this->add_control(
				$color_id,
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$tab_selector => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				"edit_bg_group_{$tabs_key}_background",
				array(
					'label' => __( 'Background Type', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'color' => array(
							'title' => __( 'Color', 'cmsmasters-elementor' ),
							'icon' => 'eicon-paint-brush',
						),
						'gradient' => array(
							'title' => __( 'Gradient', 'cmsmasters-elementor' ),
							'icon' => 'eicon-barcode',
						),
					),
					'default' => 'color',
					'toggle' => false,
					'render_type' => 'ui',
					'condition' => $edit_condition,
				)
			);

			$this->add_control(
				"edit_bg_{$tabs_key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$gradient_selector => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						'custom_edit_view' => 'button',
						"custom_edit_bg_group_{$tabs_key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"edit_bg_group_{$tabs_key}_color_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 0,
					),
					'render_type' => 'ui',
					'condition' => array(
						'custom_edit_view' => 'button',
						"custom_edit_bg_group_{$tabs_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"edit_bg_group_{$tabs_key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						'custom_edit_view' => 'button',
						"custom_edit_bg_group_{$tabs_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"edit_bg_group_{$tabs_key}_color_b_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'render_type' => 'ui',
					'condition' => array(
						'custom_edit_view' => 'button',
						"custom_edit_bg_group_{$tabs_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"edit_bg_group_{$tabs_key}_gradient_type",
				array(
					'label' => __( 'Type', 'cmsmasters-elementor' ),
					'label_block' => false,
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'linear' => __( 'Linear', 'cmsmasters-elementor' ),
						'radial' => __( 'Radial', 'cmsmasters-elementor' ),
					),
					'default' => 'linear',
					'render_type' => 'ui',
					'condition' => array(
						'custom_edit_view' => 'button',
						"custom_edit_bg_group_{$tabs_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"edit_bg_group_{$tabs_key}_gradient_angle",
				array(
					'label' => __( 'Angle', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'deg' ),
					'default' => array(
						'unit' => 'deg',
						'size' => 180,
					),
					'range' => array(
						'deg' => array( 'step' => 10 ),
					),
					'selectors' => array(
						$gradient_selector => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{custom_edit_bg_group_{$tabs_key}_color_stop.SIZE}}{{custom_edit_bg_group_{$tabs_key}_color_stop.UNIT}}, {{custom_edit_bg_group_{$tabs_key}_color_b.VALUE}} {{custom_edit_bg_group_{$tabs_key}_color_b_stop.SIZE}}{{custom_edit_bg_group_{$tabs_key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						'custom_edit_view' => 'button',
						"custom_edit_bg_group_{$tabs_key}_background" => array( 'gradient' ),
						"custom_edit_bg_group_{$tabs_key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"edit_bg_group_{$tabs_key}_gradient_position",
				array(
					'label' => __( 'Position', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => array(
						'center center' => __( 'Center Center', 'cmsmasters-elementor' ),
						'center left' => __( 'Center Left', 'cmsmasters-elementor' ),
						'center right' => __( 'Center Right', 'cmsmasters-elementor' ),
						'top center' => __( 'Top Center', 'cmsmasters-elementor' ),
						'top left' => __( 'Top Left', 'cmsmasters-elementor' ),
						'top right' => __( 'Top Right', 'cmsmasters-elementor' ),
						'bottom center' => __( 'Bottom Center', 'cmsmasters-elementor' ),
						'bottom left' => __( 'Bottom Left', 'cmsmasters-elementor' ),
						'bottom right' => __( 'Bottom Right', 'cmsmasters-elementor' ),
					),
					'default' => 'center center',
					'selectors' => array(
						$gradient_selector => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{custom_edit_bg_group_{$tabs_key}_color_stop.SIZE}}{{custom_edit_bg_group_{$tabs_key}_color_stop.UNIT}}, {{custom_edit_bg_group_{$tabs_key}_color_b.VALUE}} {{custom_edit_bg_group_{$tabs_key}_color_b_stop.SIZE}}{{custom_edit_bg_group_{$tabs_key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						'custom_edit_view' => 'button',
						"custom_edit_bg_group_{$tabs_key}_background" => array( 'gradient' ),
						"custom_edit_bg_group_{$tabs_key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"edit_border_{$tabs_key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$tab_selector => 'border-color: {{VALUE}}',
					),
					'condition' => $edit_condition,
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "edit_text_shadow_{$tabs_key}",
					'selector' => $tab_selector,
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "edit_box_shadow_{$tabs_key}",
					'selector' => $tab_selector,
					'condition' => $edit_condition,
				)
			);

			$border_radius_id = ( 'hover' === $tabs_key ) ? "edit_border_radius_{$tabs_key}" : 'edit_border_radius';

			$this->add_responsive_control(
				$border_radius_id,
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors' => array(
						$tab_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition' => $edit_condition,
				)
			);

			if ( 'hover' === $tabs_key ) {
				$this->add_control(
					'edit_transition',
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'default' => array( 'size' => 0.3 ),
						'range' => array(
							'px' => array(
								'max' => 3,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							"{$wrapper}__button-wrapper .comment-edit-link, {$wrapper}__button-wrapper .comment-edit-link:before, {$wrapper}__button-wrapper .comment-edit-link:after, {{WRAPPER}} .comment-edit-link, {$wrapper}__button-wrapper .comment-edit-link i, {$wrapper}__button-wrapper .comment-edit-link svg" => 'transition: all {{SIZE}}s',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'edit_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default' => array( 'size' => 10 ),
				'range' => array(
					'px' => array(
						'max' => 30,
					),
					'em' => array(
						'max' => 1,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .comment-edit-link' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'separator' => 'before',
				'condition' => array( 'custom_button_position' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'edit_border',
				'exclude' => array( 'color' ),
				'selector' => "{$wrapper} .comment-edit-link",
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
				),
				'separator' => 'before',
				'condition' => $edit_condition,
			)
		);

		$this->add_responsive_control(
			'edit_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					"{$wrapper}__button-wrapper .comment-edit-link, {{WRAPPER}} .comment-edit-link" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'separator' => 'before',
				'condition' => $edit_condition,
			)
		);

		$this->end_controls_section();
	}

	protected function style_date() {
		$this->start_controls_section(
			'section_style_date',
			array(
				'label' => __( 'Date', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'date_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--date-margin: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'custom_date_position' => 'inline' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'date_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-single-post-comment__date-wrap, {{WRAPPER}} .cmsmasters-single-post-comment__date, {{WRAPPER}} .cmsmasters-single-post-comment__date-wrap a',
			)
		);

		$this->add_control(
			'date_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment__date, {{WRAPPER}} .cmsmasters-single-post-comment__date-wrap a' => 'color: {{VALUE}}',
				),
			)
		);

		$date_icon = array( 'custom_date_icon[value]!' => '' );

		$this->add_control(
			'date_icon_heading',
			array(
				'label' => __( 'Date Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $date_icon,
			)
		);

		$this->add_control(
			'date_icon_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'default' => array( 'size' => 5 ),
				'range' => array(
					'px' => array(
						'max' => 30,
					),
					'em' => array(
						'max' => 1,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment__date-wrap i,' .
					'{{WRAPPER}} .cmsmasters-single-post-comment__date-wrap svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => $date_icon,
			)
		);

		$this->add_control(
			'date_icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 30,
					),
					'em' => array(
						'min' => 0.5,
						'max' => 2,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment__date-wrap i,' .
					'{{WRAPPER}} .cmsmasters-single-post-comment__date-wrap svg' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => $date_icon,
			)
		);

		$this->add_control(
			'date_icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment__date-wrap i,' .
					'{{WRAPPER}} .cmsmasters-single-post-comment__date-wrap svg' => 'color: {{VALUE}}',
				),
				'condition' => $date_icon,
			)
		);

		$this->end_controls_section();
	}

	protected function style_content() {
		$this->start_controls_section(
			'section_style_content',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment' => '--content-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-single-post-comment__content',
			)
		);

		$this->add_control(
			'content_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment__content' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'content_background',
				'exclude' => array( 'image' ),
				'selector' => '{{WRAPPER}} .cmsmasters-single-post-comment__content',
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'custom_content_background_color!' => '' ),
			)
		);

		$this->add_responsive_control(
			'content_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comment__content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'custom_content_background_color!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'content_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-single-post-comment__content',
				'condition' => array( 'custom_content_background_color!' => '' ),
			)
		);

		$this->end_controls_section();
	}

	protected function style_form_title() {
		$this->start_controls_section(
			'section_style_form_title',
			array(
				'label' => __( 'Reply Form Heading', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'form_title_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .comment-reply-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'form_title_alignment',
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
				'label_block' => false,
				'separator' => 'before',
				'selectors_dictionary' => array(
					'left' => 'justify-content: flex-start;',
					'center' => 'justify-content: center;',
					'right' => 'justify-content: flex-end;',
				),
				'selectors' => array(
					'{{WRAPPER}} .comment-reply-title' => '{{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'form_title_typography',
				'selector' => '{{WRAPPER}} .comment-reply-title',
			)
		);

		$this->add_control(
			'form_title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .comment-reply-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'form_title_text_shadow',
				'selector' => '{{WRAPPER}} .comment-reply-title',
			)
		);

		$this->add_control(
			'form_title_stroke_heading',
			array(
				'label' => __( 'Stroke', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'form_title_stroke_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '',
				),
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
					'em' => array(
						'min' => 0,
						'max' => 0.2,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .comment-reply-title' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'form_title_stroke_color_normal',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .comment-reply-title' => '-webkit-text-stroke-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'custom_form_title_stroke_width[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->add_control(
			'form_title_stroke_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .comment-reply-title:hover' => '-webkit-text-stroke-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'custom_form_title_stroke_width[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->add_control(
			'form_title_stroke_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 0.3 ),
				'range' => array(
					'px' => array(
						'max' => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .comment-reply-title' => 'transition: all {{SIZE}}s',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'custom_form_title_stroke_width[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->add_control(
			'form_title_icon_heading',
			array(
				'label' => __( 'Title Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'form_title_icon',
			array(
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
			)
		);

		$this->add_control(
			'form_title_icon_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array( 'size' => 10 ),
				'range' => array(
					'px' => array(
						'max' => 30,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .comment-reply-title i,' .
					'{{WRAPPER}} .comment-reply-title svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'custom_form_title_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'form_title_icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}} .comment-reply-title i,' .
					'{{WRAPPER}} .comment-reply-title svg' => 'color: {{VALUE}}',
				),
				'condition' => array( 'custom_form_title_icon[value]!' => '' ),
			)
		);

		$this->start_controls_tabs( 'form_title_additional_tabs' );

		foreach ( array(
			'reply_to' => __( 'Reply to', 'cmsmasters-elementor' ),
			'cancel_reply' => __( 'Cancel Reply', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			if ( 'reply_to' === $main_key ) {
				$form_title_selector = '{{WRAPPER}} .comment-reply-title > a';
			} else {
				$form_title_selector = '{{WRAPPER}} .comment-reply-title > small a';
			}

			$this->start_controls_tab(
				"form_title_{$main_key}_tab",
				array(
					'label' => $label,
				)
			);

			$this->add_responsive_control(
				"form_title_{$main_key}_gap",
				array(
					'label' => __( 'Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default' => array( 'size' => 10 ),
					'selectors' => array(
						$form_title_selector => 'margin-left: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => "form_title_{$main_key}_typography",
					'selector' => $form_title_selector,
				)
			);

			$this->add_control(
				"form_title_{$main_key}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$form_title_selector => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				"form_title_{$main_key}_color_hover",
				array(
					'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$form_title_selector}:hover" => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				"form_title_{$main_key}_transition",
				array(
					'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'default' => array( 'size' => 0.3 ),
					'range' => array(
						'px' => array(
							'max' => 3,
							'step' => 0.1,
						),
					),
					'selectors' => array(
						$form_title_selector => 'transition: all {{SIZE}}s',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function style_form() {
		$this->start_controls_section(
			'section_style_form',
			array(
				'label' => __( 'Form', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'form_wrapper_width',
			array(
				'label' => __( 'Form Wrapper Width', 'cmsmasters-elementor' ),
				'description' => __( 'Does not apply to reply form.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vh', 'vw' ),
				'range' => array(
					'%' => array(
						'min' => 30,
					),
					'px' => array(
						'min' => 300,
						'max' => 1300,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments > .cmsmasters-respond-wrapper' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto; width: 100%;',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::BUTTON_BACKGROUND_GROUP,
			array(
				'name' => 'form_wrapper_background',
				'selector' => '{{WRAPPER}} .cmsmasters-single-post-comments > .cmsmasters-respond-wrapper',
			)
		);

		$this->add_responsive_control(
			'form_width',
			array(
				'label' => __( 'Form Width', 'cmsmasters-elementor' ),
				'description' => __( 'Does not apply to reply form. Can not be wider than Form Wrapper Width.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vh', 'vw' ),
				'range' => array(
					'%' => array(
						'min' => 30,
					),
					'px' => array(
						'min' => 300,
						'max' => 1300,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments > .cmsmasters-respond-wrapper #respond.comment-respond' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto; width: 100%;',
				),
			)
		);

		$this->add_responsive_control(
			'form_align',
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
				'label_block' => false,
				'default' => 'center',
				'toggle' => false,
				'selectors_dictionary' => array(
					'left' => 'margin-left: 0;',
					'center' => '',
					'right' => 'margin-right: 0;',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments .cmsmasters-respond-wrapper > #respond.comment-respond' => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'comment_respond_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'max' => 120,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments' => '--comment-respond-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'comment_respond_separator',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'cmsmasters-comment-separator-',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'comment_respond_border',
				'selector' => '{{WRAPPER}} .comment-respond:before',
				'fields_options' => array(
					'border' => array(
						'label' => __( 'Separator Type', 'cmsmasters-elementor' ),
						'default' => 'solid',
					),
					'width' => array(
						'type' => Controls_Manager::SLIDER,
						'size_units' => array( 'px' ),
						'default' => array( 'size' => 1 ),
						'range' => array(
							'px' => array(
								'max' => 10,
							),
						),
						'selectors' => array(
							'{{SELECTOR}}' => 'border-top-width: {{SIZE}}{{UNIT}};',
						),
					),
				),
				'condition' => array( 'custom_comment_respond_separator' => 'yes' ),
			)
		);

		$form_selector = '{{WRAPPER}} .comment-respond';

		$this->add_control(
			'form_background',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$form_selector => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'form_border',
				'selector' => $form_selector,
				'fields_options' => array(
					'border' => array(
						'separator' => 'before',
					),
				),
			)
		);

		$this->add_responsive_control(
			'form_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					$form_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'form_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'separator' => 'before',
				'selectors' => array(
					$form_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'form_box_shadow',
				'selector' => $form_selector,
			)
		);
		$this->add_responsive_control(
			'reply_form_padding',
			array(
				'label' => __( 'Reply Form Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments__list .comment-respond' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function style_form_elements() {
		$this->start_controls_section(
			'section_style_form_elements',
			array(
				'label' => __( 'Form Elements', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$selector_author = '{{WRAPPER}} .comment-form-author input';
		$selector_email = '{{WRAPPER}} .comment-form-email input';
		$selector_url = '{{WRAPPER}} .comment-form-url input';
		$selector_comment = '{{WRAPPER}} .comment-form-comment textarea';

		$this->add_control(
			'comment_style_heading',
			array(
				'label' => __( 'Comment Field', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'form_elements_comment_rows',
			array(
				'label' => __( 'Rows Number', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'default' => array( 'size' => 4 ),
			)
		);

		$this->add_responsive_control(
			'comment_area_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 30,
						'max' => 70,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments' => '--comment-width: {{SIZE}}%;',
				),
				'condition' => array( 'custom_comment_direction' => 'aside' ),
			)
		);

		$this->add_control(
			'input_style_heading',
			array(
				'label' => __( 'Input Fields', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'input_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'%' => array(
						'min' => 30,
					),
					'px' => array(
						'min' => 100,
						'max' => 500,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments' => '--input-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'custom_comment_direction' => 'row',
					'custom_input_direction' => 'rows',
				),
			)
		);

		$this->add_control(
			'input_gap',
			array(
				'label' => __( 'Gap from Comment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array( 'max' => 60 ),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments' => '--input-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'input_gap_between',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array( 'max' => 60 ),
				),
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments' => '--input-gap-between: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'form_elements_typography',
				'selector' => "{$selector_author}, {$selector_email}, {$selector_url}, {$selector_comment}",
				'fields_options' => array(
					'typography' => array(
						'label' => __( 'Form Typography', 'cmsmasters-elementor' ),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'form_elements_border',
				'exclude' => array( 'color' ),
				'selector' => "{$selector_author}, {$selector_email}, {$selector_url}, {$selector_comment}",
			)
		);

		$this->add_responsive_control(
			'form_elements_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-input-wrap input, {{WRAPPER}} .comment-form-comment textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$input_states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'focus' => __( 'Focus', 'cmsmasters-elementor' ),
		);

		/* Start Tab Title Tabs */
		$this->start_controls_tabs(
			'form_elements_tabs',
			array( 'separator' => 'before' )
		);

		foreach ( $input_states as $main_key => $label ) {
			$selectors = array(
				$selector_author,
				$selector_email,
				$selector_url,
				$selector_comment,
			);

			$selector = implode( ', ', $selectors );

			$icon_selector = implode( ' + i, ', $selectors ) . ' + i';
			$svg_icon_selector = implode( ' + svg, ', $selectors ) . ' + svg';

			$webkit_placeholder = '{{WRAPPER}} input::-webkit-input-placeholder, {{WRAPPER}} textarea::-webkit-input-placeholder';
			$moz_placeholder = '{{WRAPPER}} input::-moz-placeholder, {{WRAPPER}} textarea::-moz-placeholder';

			if ( 'focus' === $main_key ) {
				$selector = implode( ':focus, ', $selectors ) . ':focus';

				$icon_selector = implode( ':focus + i, ', $selectors ) . ':focus + i';
				$svg_icon_selector = implode( ':focus + svg, ', $selectors ) . ':focus + svg';

				$webkit_placeholder = '';
				$moz_placeholder = '';
			}

			/* Start Tab Title Tab */
			$this->start_controls_tab(
				"form_elements_tab_{$main_key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"form_elements_color_{$main_key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$icon_selector => 'color: {{VALUE}}',
						$svg_icon_selector => 'color: {{VALUE}}',
						$selector => 'color: {{VALUE}}',
						$webkit_placeholder => 'color: {{VALUE}}',
						$moz_placeholder => 'color: {{VALUE}}',
					),
				)
			);

			if ( 'normal' === $main_key ) {
				$this->add_control(
					"form_elements_placeholder_color_{$main_key}",
					array(
						'label' => __( 'Placeholder Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$webkit_placeholder => 'color: {{VALUE}} !important;',
							$moz_placeholder => 'color: {{VALUE}} !important;',
						),
					)
				);
			}

			$this->add_control(
				"form_elements_background_{$main_key}",
				array(
					'label' => __( 'Background', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				"form_elements_border_{$main_key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'border-color: {{VALUE}}',
					),
					'condition' => array( 'custom_form_elements_border_border!' => '' ),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "form_elements_box_shadow_{$main_key}",
					'selector' => $selector,
				)
			);

			if ( 'focus' === $main_key ) {
				$this->add_control(
					"{$main_key}_transition",
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'default' => array( 'size' => 0.3 ),
						'range' => array(
							'px' => array(
								'max' => 3,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							"{$selector_author},
							{$selector_email},
							{$selector_url},
							{$selector_comment},
							{{WRAPPER}} .comment-form .comment-form-comment i,
							{{WRAPPER}} .comment-form .comment-form-comment svg,
							{{WRAPPER}} .comment-form .cmsmasters-input-wrap i,
							{{WRAPPER}} .comment-form .cmsmasters-input-wrap svg" => 'transition: all {{SIZE}}s',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'form_elements_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-input-wrap input, {{WRAPPER}} .comment-form-comment textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .cmsmasters-single-post-comments' => '--input-icon-padding: {{RIGHT}}{{UNIT}}; --textarea-icon-padding: {{TOP}}{{UNIT}};',
				),
			)
		);

		$label_instead_placeholder = array( 'custom_use_label_instead_placeholder!' => 'yes' );

		$this->add_control(
			'input_label_heading',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $label_instead_placeholder,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'form_label_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-input-wrap label, {{WRAPPER}} .comment-form-comment label',
				'condition' => $label_instead_placeholder,
			)
		);

		$this->add_control(
			'input_label_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-input-wrap label, {{WRAPPER}} .comment-form-comment label' => 'color: {{VALUE}};',
				),
				'condition' => $label_instead_placeholder,
			)
		);

		$this->add_control(
			'style_input_icon_heading',
			array(
				'label' => __( 'Input Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'input_icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-input-wrap i, {{WRAPPER}} .comment-form-comment i,' .
					'{{WRAPPER}} .cmsmasters-input-wrap svg, {{WRAPPER}} .comment-form-comment svg' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .cmsmasters-single-post-comments' => '--input-icon-size: {{SIZE}}px;',
				),
				'condition' => array( 'custom_website_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'input_icon_icon',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-input-wrap i, {{WRAPPER}} .comment-form-comment i,' .
					'{{WRAPPER}} .cmsmasters-input-wrap svg, {{WRAPPER}} .comment-form-comment svg' => 'color: {{VALUE}}',
				),
				'condition' => array( 'custom_website_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'submit_button_heading',
			array(
				'label' => __( 'Submit Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'button_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-single-post-comments' => '--button-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$submit = '{{WRAPPER}} .form-submit .submit';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'submit_button_typography',
				'selector' => $submit,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'submit_button_border',
				'exclude' => array( 'color' ),
				'selector' => $submit,
			)
		);

		$link_states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		/* Start Tab Title Tabs */
		$this->start_controls_tabs(
			'submit_button_tabs',
			array( 'separator' => 'before' )
		);

		foreach ( $link_states as $main_key => $label ) {
			$selector = $submit;

			if ( 'hover' === $main_key ) {
				$gradient_selector = $selector . ':after';
				$selector .= ':hover';
			} else {
				$gradient_selector = $selector . ':before';
			}

			/* Start Tab Title Tab */
			$this->start_controls_tab(
				"submit_button_tab_{$main_key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"submit_button_color_{$main_key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				"submit_button_background_group_{$main_key}_background",
				array(
					'label' => __( 'Background Type', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'color' => array(
							'title' => __( 'Color', 'cmsmasters-elementor' ),
							'icon' => 'eicon-paint-brush',
						),
						'gradient' => array(
							'title' => __( 'Gradient', 'cmsmasters-elementor' ),
							'icon' => 'eicon-barcode',
						),
					),
					'default' => 'color',
					'toggle' => false,
					'render_type' => 'ui',
				)
			);

			$this->add_control(
				"submit_button_background_{$main_key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$gradient_selector => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"custom_submit_button_background_group_{$main_key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"submit_button_background_group_{$main_key}_color_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 0,
					),
					'render_type' => 'ui',
					'condition' => array(
						"custom_submit_button_background_group_{$main_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"submit_button_background_group_{$main_key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"custom_submit_button_background_group_{$main_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"submit_button_background_group_{$main_key}_color_b_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'render_type' => 'ui',
					'condition' => array(
						"custom_submit_button_background_group_{$main_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"submit_button_background_group_{$main_key}_gradient_type",
				array(
					'label' => __( 'Type', 'cmsmasters-elementor' ),
					'label_block' => false,
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'linear' => __( 'Linear', 'cmsmasters-elementor' ),
						'radial' => __( 'Radial', 'cmsmasters-elementor' ),
					),
					'default' => 'linear',
					'render_type' => 'ui',
					'condition' => array(
						"custom_submit_button_background_group_{$main_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"submit_button_background_group_{$main_key}_gradient_angle",
				array(
					'label' => __( 'Angle', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'deg' ),
					'default' => array(
						'unit' => 'deg',
						'size' => 180,
					),
					'range' => array(
						'deg' => array( 'step' => 10 ),
					),
					'selectors' => array(
						$gradient_selector => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{custom_submit_button_background_group_{$main_key}_color_stop.SIZE}}{{custom_submit_button_background_group_{$main_key}_color_stop.UNIT}}, {{custom_submit_button_background_group_{$main_key}_color_b.VALUE}} {{custom_submit_button_background_group_{$main_key}_color_b_stop.SIZE}}{{custom_submit_button_background_group_{$main_key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"custom_submit_button_background_group_{$main_key}_background" => array( 'gradient' ),
						"custom_submit_button_background_group_{$main_key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"submit_button_background_group_{$main_key}_gradient_position",
				array(
					'label' => __( 'Position', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => array(
						'center center' => __( 'Center Center', 'cmsmasters-elementor' ),
						'center left' => __( 'Center Left', 'cmsmasters-elementor' ),
						'center right' => __( 'Center Right', 'cmsmasters-elementor' ),
						'top center' => __( 'Top Center', 'cmsmasters-elementor' ),
						'top left' => __( 'Top Left', 'cmsmasters-elementor' ),
						'top right' => __( 'Top Right', 'cmsmasters-elementor' ),
						'bottom center' => __( 'Bottom Center', 'cmsmasters-elementor' ),
						'bottom left' => __( 'Bottom Left', 'cmsmasters-elementor' ),
						'bottom right' => __( 'Bottom Right', 'cmsmasters-elementor' ),
					),
					'default' => 'center center',
					'selectors' => array(
						$gradient_selector => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{custom_submit_button_background_group_{$main_key}_color_stop.SIZE}}{{custom_submit_button_background_group_{$main_key}_color_stop.UNIT}}, {{custom_submit_button_background_group_{$main_key}_color_b.VALUE}} {{custom_submit_button_background_group_{$main_key}_color_b_stop.SIZE}}{{custom_submit_button_background_group_{$main_key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"custom_submit_button_background_group_{$main_key}_background" => array( 'gradient' ),
						"custom_submit_button_background_group_{$main_key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"submit_button_border_{$main_key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'border-color: {{VALUE}}',
					),
					'condition' => array( 'custom_submit_button_border_border!' => '' ),
				)
			);

			$text_shadow_id = ( 'hover' === $main_key ) ? "submit_button_text_shadow_{$main_key}" : 'submit_button_text_shadow';

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => $text_shadow_id,
					'selector' => $selector,
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "submit_button_box_shadow_{$main_key}",
					'selector' => $selector,
				)
			);

			$border_radius_id = ( 'hover' === $main_key ) ? "submit_button_border_radius_{$main_key}" : 'submit_button_border_radius';

			$this->add_responsive_control(
				$border_radius_id,
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors' => array(
						$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					),
				)
			);

			if ( 'hover' === $main_key ) {
				$this->add_control(
					"{$main_key}_transition",
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'default' => array( 'size' => 0.3 ),
						'range' => array(
							'px' => array(
								'max' => 3,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							"{$submit}, {$submit}:before, {$submit}:after" => 'transition: all {{SIZE}}s',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'submit_button_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator' => 'before',
				'selectors' => array(
					$submit => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .cmsmasters-single-post-comments' => '--submit-padding-right: {{RIGHT}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'submit_button_icon_heading',
			array(
				'label' => __( 'Submit Button Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'custom_submit_button_type!' => 'text' ),
			)
		);

		$this->add_responsive_control(
			'submit_button_icon_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .comment-respond' => '--submit-icon-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'custom_submit_button_type!' => 'text' ),
			)
		);

		$this->add_control(
			'submit_button_icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .comment-respond .form-submit .submit i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .comment-respond .form-submit .submit svg' => 'fill: {{VALUE}}',
				),
				'condition' => array( 'custom_submit_button_type!' => 'text' ),
			)
		);

		$this->add_control(
			'submit_button_icon_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .comment-respond .form-submit .submit:hover i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .comment-respond .form-submit .submit:hover svg' => 'fill: {{VALUE}}',
				),
				'condition' => array( 'custom_submit_button_type!' => 'text' ),
			)
		);

		$this->add_control(
			'additional_fields_heading',
			array(
				'label' => __( 'Additional Form Elements', 'cmsmasters-elementor' ),
				'description' => __( 'Logged as info, Comment notes, Cookies', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'additional_fields_align',
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
				'label_block' => false,
				'default' => 'left',
				'toggle' => false,
				'selectors_dictionary' => array(
					'left' => 'text-align:left;',
					'center' => 'text-align:center;',
					'right' => 'text-align:right;',
				),
				'selectors' => array(
					'{{WRAPPER}} .logged-in-as, {{WRAPPER}} .comment-notes, {{WRAPPER}} .comment-form-cookies-consent' => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'additional_comment_notes',
			array(
				'label' => __( 'Comment Notes Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .comment-form .comment-notes,
					{{WRAPPER}} .comment-form .logged-in-as' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'additional_cookies_consent',
			array(
				'label' => __( 'Cookies Consent Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .comment-form .comment-form-cookies-consent' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-comment-position-end.cmsmasters-form-button-inline-yes .comment-form-cookies-consent' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$selector_info = '{{WRAPPER}} .logged-in-as, {{WRAPPER}} .logged-in-as a, {{WRAPPER}} .comment-notes, {{WRAPPER}} .comment-form-cookies-consent label';

		$this->add_control(
			'additional_fields_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector_info => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'additional_fields_typography',
				'selector' => $selector_info,
			)
		);

		$this->add_control(
			'logged_as_hover_color',
			array(
				'label' => __( '\'Logged in as\' Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .logged-in-as a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render skin output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		$settings = $this->parent->get_settings();

		$post_id = (int) $settings['custom_source_custom'];

		if ( 0 !== $post_id ) {
			/** @var TemplatePreviewModule $preview_module */
			$preview_module = TemplatePreviewModule::instance();

			$preview_module->switch_to_preview_post( $post_id );
		}

		if (
			! comments_open() &&
			( Utils::is_preview_mode() || Utils::is_edit_mode() )
		) {
			?>
			<div class="elementor-alert elementor-alert-danger" role="alert">
				<span class="elementor-alert-title">
					<?php esc_html_e( 'Comments Are Closed!', 'cmsmasters-elementor' ); ?>
				</span>
				<span class="elementor-alert-description">
					<?php esc_html_e( 'Switch on comments from either the discussion box on the WordPress post edit screen or from the WordPress discussion settings.', 'cmsmasters-elementor' ); ?>
				</span>
			</div>
			<?php
		} elseif ( comments_open() ) {
			add_filter( 'dsq_can_load', '__return_false' );

			if ( Utils::is_edit_mode() && 'user' === $settings['custom_form_view_as'] ) {
				add_filter( 'comment_form_field_comment', array( $this, 'editor_log_out_preview' ) );
			}

			add_filter( 'comments_template', array( $this, 'comments_template_path' ) );
			add_filter( 'cmsmasters_elementor/widgets/cmsmasters-post-comments/template_variables', array( $this, 'template_variables' ) );

			if ( 'human_readable' !== $this->render_date( 'date' ) ) {
				add_filter( 'get_comment_date', array( $this, 'custom_comment_date' ), 10, 3 );
				add_filter( 'get_comment_time', array( $this, 'custom_comment_time' ), 10, 5 );
			}

			comments_template();

			remove_filter( 'cmsmasters_elementor/widgets/cmsmasters-post-comments/template_variables', array( $this, 'template_variables' ) );
			remove_filter( 'comments_template', array( $this, 'comments_template_path' ) );

			if ( 'human_readable' !== $this->render_date( 'date' ) ) {
				remove_filter( 'get_comment_date', array( $this, 'custom_comment_date' ), 10, 3 );
				remove_filter( 'get_comment_time', array( $this, 'custom_comment_time' ), 10, 5 );
			}

			add_filter( 'dsq_can_load', '__return_true' );
		}

		if ( 0 !== $post_id ) {
			$preview_module->restore_current_post();
		}
	}

	/**
	 * Log out user preview.
	 *
	 * Simulates view of form for log out user.
	 *
	 * @since 1.0.0
	 *
	 * @return string Form parameters.
	 */
	public function editor_log_out_preview() {
		$data = $this->template_variables();

		$email_end = '';
		$website_out = '';
		$is_placeholder = $data['settings']['custom_use_label_instead_placeholder'];
		$website_text = Utils::get_if_not_empty( $data['settings'], 'custom_website_text', esc_attr__( 'Website', 'cmsmasters-elementor' ) );

		if ( 'yes' === $data['settings']['custom_website_input'] ) {
			$website_out = '<p class="comment-form-url">' .
			( ( ! $is_placeholder ) ? '<label for="url">' . $data['icon']['website'] . $website_text . '</label> ' : '' ) .
				'<input type="url" id="url" name="url" size="35"
					placeholder="' . ( $is_placeholder ? $website_text : '' ) .
				'" />' .
				( $is_placeholder ? $data['icon']['website'] : '' ) .
			'</p></div>';
		} else {
			$email_end = '</div>';
		}

		$comment_text = ( ( ! $data['comment_text'] ) ? esc_attr__( 'Comment', 'cmsmasters-elementor' ) . ' *' : $data['comment_text'] . ' *' );
		$name_text = ( ( ! $data['name_text'] ) ? esc_attr__( 'Name', 'cmsmasters-elementor' ) : $data['name_text'] );
		$email_text = ( ( ! $data['email_text'] ) ? esc_attr__( 'Email', 'cmsmasters-elementor' ) : $data['email_text'] );
		$cookies = ( '1' === get_option( 'show_comments_cookies_opt_in' ) )
			? '<p class="comment-form-cookies-consent">' .
				'<input type="checkbox" id="comment-form-cookies-consent" name="comment-form-cookies-consent" value="yes" checked="checked" />' .
				'<label for="comment-form-cookies-consent">' .
					esc_html__( 'Save my name, email, and website in this browser for the next time I comment.', 'cmsmasters-elementor' ) .
				'</label>' .
			'</p>'
			: '';
		$comment_uniqid = uniqid( 'comment-' );
		$author_uniqid = uniqid( 'author-' );
		$email_uniqid = uniqid( 'email-' );

		/* translators: Post Comments widget comment notes. %1$s: Email notes %2$s: Required fields note */
		$form_fields = sprintf(
			'<p class="comment-notes">%1$s%2$s</p>',
			/* translators: Post Comments widget email notes. %s: Email note */
			sprintf(
				'<span id="email-notes">%s</span>',
				__( 'Your email address will not be published.', 'cmsmasters-elementor' )
			),
			' ' . __( 'Required fields are marked *', 'cmsmasters-elementor' )
		) .
		'<p class="comment-form-comment">' .
			'<label for="' . $comment_uniqid . '"' . ( $is_placeholder ? ' class="screen-reader-text"' : '' ) . '>' .
				$data['icon']['comment'] . $comment_text .
			'</label>' .
			'<textarea id="' . $comment_uniqid . '" name="comment" cols="67"
				rows="' . $data['settings']['custom_form_elements_comment_rows']['size'] . '"
				placeholder="' . $comment_text . '"></textarea>' .
			( ( $is_placeholder ) ? $data['icon']['comment'] : '' ) .
		'</p>' .
		'<div class="cmsmasters-input-wrap"><p class="comment-form-author">' .
			'<label for="' . $author_uniqid . '"' . ( $is_placeholder ? ' class="screen-reader-text"' : '' ) . '>' .
				$data['icon']['name'] . $name_text . ' *' .
			'</label>' .
			'<input id="' . $author_uniqid . '" type="text" name="author" size="35" placeholder="' .
				( ( $is_placeholder ) ? $name_text . ' *' : '' ) .
			'" />' .
			( ( $is_placeholder ) ? $data['icon']['name'] : '' ) .
		'</p>' .
		'<p class="comment-form-email">' .
			'<label for="' . $email_uniqid . '"' . ( $is_placeholder ? ' class="screen-reader-text"' : '' ) . '>' .
				$data['icon']['email'] . $email_text . ' *' .
			'</label>' .
			'<input id="' . $email_uniqid . '" type="text" name="email" size="35" placeholder="' .
				( ( $is_placeholder ) ? $email_text . ' *' : '' ) .
			'" />' .
			( ( $is_placeholder ) ? $data['icon']['email'] : '' ) .
		'</p>' .
		$email_end .
		$website_out .
		$cookies;

		return $form_fields;
	}

	/**
	 * Path of comments template.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @return string Path of comments template.
	 */
	public function comments_template_path() {
		return realpath( __DIR__ . '/../..' ) . '/templates/comments.php';
	}

	/**
	 * Template variables.
	 *
	 * Retrieves skin parameters to comments template.
	 *
	 * @since 1.0.0
	 *
	 * @return array Retrieves skin parameters to comments template.
	 */
	public function template_variables() {
		$data = array();
		$settings = $this->parent->get_settings();

		$data['settings'] = $settings;

		$data['icon']['comment_title'] = $this->render_icon( 'comment_title' );
		$data['icon']['form_title'] = $this->render_icon( 'form_title' );
		$data['icon']['comment'] = ( 'yes' === $settings['custom_enable_input_icon'] ) ? $this->render_icon( 'comment' ) : '';
		$data['icon']['name'] = ( 'yes' === $settings['custom_enable_input_icon'] ) ? $this->render_icon( 'name' ) : '';
		$data['icon']['email'] = ( 'yes' === $settings['custom_enable_input_icon'] ) ? $this->render_icon( 'email' ) : '';
		$data['icon']['website'] = ( 'yes' === $settings['custom_enable_input_icon'] && 'yes' === $settings['custom_website_input'] ) ? $this->render_icon( 'website' ) : '';

		if ( 'text' !== $settings['custom_submit_button_type'] && ! empty( $this->render_icon( 'submit_button' ) ) ) {
			$data['icon']['submit'] = $this->render_icon( 'submit_button' );
		} elseif ( 'text' !== $settings['custom_submit_button_type'] && empty( $this->render_icon( 'submit_button' ) ) ) {
			$data['icon']['submit'] = Utils::get_render_icon(
				array(
					'value' => 'far fa-envelope',
					'library' => 'fa-regular',
				),
				array( 'aria-hidden' => 'true' ),
				false
			);
		} else {
			$data['icon']['submit'] = '';
		}

		$submit_meta_text = ( '' !== $settings['custom_submit_button_text'] )
			? $settings['custom_submit_button_text']
			: __( 'Post Comment', 'cmsmasters-elementor' );

		$data['submit_text'] = $submit_meta_text;

		$data['comment_text'] = $this->is_text( $settings, 'comment' );
		$data['name_text'] = $this->is_text( $settings, 'name' );
		$data['email_text'] = $this->is_text( $settings, 'email' );

		$reply_text = ( '' !== $settings['custom_reply_text'] )
			? $settings['custom_reply_text']
			: esc_attr__( 'Reply', 'cmsmasters-elementor' );

		$data['comments_meta']['title_text_only'] = $this->is_text( $settings, 'comment_title' );
		$data['comments_meta']['title_single_text'] = $this->is_text( $settings, 'comment_title_single' );
		$data['comments_meta']['title_multiple_text'] = $this->is_text( $settings, 'comment_title_multiple' );

		$data['comment_meta']['post_author'] = ( '' !== $settings['custom_author_highlight'] ) ? '<span class="cmsmasters-post-author">' . $settings['custom_post_author'] . '</span>' : '';
		$data['comment_meta']['author_text_after'] = '<span class="cmsmasters-text-after">' . $settings['custom_author_text_after'] . '</span>';
		$data['comment_meta']['date_format'] = $this->render_date( 'date' );
		$data['comment_meta']['time_enable'] = $settings['custom_date_time_switcher'];
		$data['comment_meta']['date_separator'] = $settings['custom_date_time_separator_text'];
		$data['comment_meta']['date_icon'] = $this->render_icon( 'date' );
		$data['comment_meta']['reply_icon'] = ( 'text' !== $settings['custom_reply_type'] ? $this->render_icon( 'reply' ) : '' );
		$data['comment_meta']['reply_text'] = ( 'icon' !== $settings['custom_reply_type'] ? $reply_text : '' );

		if ( 'human_readable' === $this->render_date( 'date' ) ) {
			$data['comment_meta']['human_readable'] = true;
		} else {
			$data['comment_meta']['human_readable'] = false;
		}

		$data['navigation']['prev_icon'] = ( 'text' !== $settings['custom_navigation_type'] ? $this->render_icon( 'navigation_previous' ) : '' );
		$prev_text = ( '' !== $settings['custom_navigation_text_previous'] ) ? $settings['custom_navigation_text_previous'] : esc_attr__( 'Older Comments', 'cmsmasters-elementor' );
		$next_text = ( '' !== $settings['custom_navigation_text_next'] ) ? $settings['custom_navigation_text_next'] : esc_attr__( 'Newer Comments', 'cmsmasters-elementor' );
		$data['navigation']['prev_text'] = ( 'icon' !== $settings['custom_navigation_type'] ? $prev_text : '' );
		$data['navigation']['next_icon'] = ( 'text' !== $settings['custom_navigation_type'] ? $this->render_icon( 'navigation_next' ) : '' );
		$data['navigation']['next_text'] = ( 'icon' !== $settings['custom_navigation_type'] ? $next_text : '' );

		return $data;
	}

	/**
	 * Get icon.
	 *
	 * Retrieves icon or svg icon.
	 *
	 * @since 1.0.0
	 * @since 1.11.6 Fixed render icons in widget.
	 *
	 * @return string Retrieves icon or svg icon.
	 */
	public function render_icon( $icon_type ) {
		$icon = $this->parent->get_settings( "custom_{$icon_type}_icon" );

		return Utils::get_render_icon( $icon, $attributes = array( 'aria-hidden' => 'true' ), $with_wrap = false );
	}

	/**
	 * Comment date.
	 *
	 * Changes comment date to certain format.
	 *
	 * @since 1.0.0
	 *
	 * @return string Changes comment date to certain format.
	 */
	public function custom_comment_date( $date, $d, $comment ) {
		return mysql2date( $this->render_date( 'date' ), $comment->comment_date );
	}

	/**
	 * Comment time.
	 *
	 * Changes comment time to certain format.
	 *
	 * @since 1.0.0
	 *
	 * @return string Changes comment time to certain format.
	 */
	public function custom_comment_time( $date, $format, $gmt, $translate, $comment ) {
		return mysql2date( $this->render_date( 'time' ), $comment->comment_date );
	}

	/**
	 * Get date or time.
	 *
	 * Retrieves comment date or time according to certain format.
	 *
	 * @since 1.0.0
	 *
	 * @return string Retrieves comment date or time according to certain format.
	 */
	private function render_date( $date_format ) {
		$format = $this->get_meta_data_format( $date_format );

		return $this->get_render_date( array( 'format' => $format ) );
	}

	/**
	 * Get date or time format.
	 *
	 * Retrieves comment date or time format.
	 *
	 * @since 1.0.0
	 *
	 * @return string Retrieves comment date or time format.
	 */
	private function get_meta_data_format( $type ) {
		$format = $this->parent->get_settings( "custom_{$type}_{$type}_format" );

		if ( 'custom' === $format || ! $format ) {
			$format = $this->parent->get_settings( "custom_{$type}_{$type}_format_custom" );
		}

		return $format;
	}

	/**
	 * Get date or time.
	 *
	 * Retrieves comment date or time.
	 *
	 * @since 1.0.0
	 *
	 * @return string Retrieves comment date or time.
	 */
	public function get_render_date( array $args = array() ) {
		$args_default = array(
			'format' => '',
			'before' => '',
			'after' => '',
		);

		$args = array_merge( $args_default, $args );

		if ( ! $args['format'] ) {
			$args['format'] = get_option( 'date_format' );
		}

		return $args['format'];
	}

	/**
	 * Is text.
	 *
	 * Whether it is text or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether it is text or not.
	 */
	public function is_text( $settings, $name ) {
		return $settings[ 'custom_' . $name . '_text' ];
	}
}
