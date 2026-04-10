<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets\Skins;

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
	exit; // Exit if accessed directly
}


class Product_Reviews_Custom extends Product_Reviews_Base {

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
	 * Get script depends.
	 *
	 * @since 1.0.0
	 *
	 * @return array Get script depends.
	 */
	public function get_script_depends() {
		return array( 'wc-single-product' );
	}

	/**
	 * Register skin controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Moved form_width & form_align to Form Style Section. Fixed it`s selectors.
	 * @since 1.1.0 Added background gradient to submit.
	 * Added controls for submit to match global button control settings.
	 * Added control 'HTML Tag' for titles. Fixed working of icon positions in button.
	 * @since 1.6.2 Fixed notice _skin.
	 * @since 1.11.5 Fixed issues for Elementor 3.18.0.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->parent->start_injection( array(
			'of' => '_skin',
		) );

		$this->add_control(
			'source_custom',
			array(
				'label' => __( 'Select Product (Optional)', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => CmsmastersControls::QUERY,
				'autocomplete' => array(
					'object' => Query_Manager::POST_OBJECT,
					'query' => array( 'post_type' => 'product' ),
				),
			)
		);

		$this->add_control(
			'review_heading',
			array(
				'label' => __( 'Review', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'review_direction',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'row' => array(
						'title' => __( 'Row', 'cmsmasters-elementor' ),
						'description' => __( 'Reviews & form in one column', 'cmsmasters-elementor' ),
					),
					'aside' => array(
						'title' => __( 'Aside', 'cmsmasters-elementor' ),
						'description' => __( 'Separates reviews & form to columns', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'aside',
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-review-direction-',
			)
		);

		$this->add_control(
			'review_position',
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
				'prefix_class' => 'cmsmasters-review-position-',
			)
		);

		$this->add_control(
			'review_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 40,
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-reviews' => '--review-width: {{SIZE}}%;',
				),
				'condition' => array( 'custom_review_direction' => 'aside' ),
			)
		);

		$this->add_responsive_control(
			'review_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'description' => __( 'Adds gap between reviews list & form.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 120,
						'step' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-reviews' => '--review-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'respond_separator',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'comment_respond_separator',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'cmsmasters-review-separator-',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'comment_respond_border',
				'selector' => '{{WRAPPER}} .comment-respond:before',
				'fields_options' => array(
					'border' => array(
						'label' => __( 'Type', 'cmsmasters-elementor' ),
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
							'{{SELECTOR}}' => 'border-width: {{SIZE}}{{UNIT}};',
						),
					),
				),
				'condition' => array( 'custom_comment_respond_separator' => 'yes' ),
			)
		);

		$this->parent->end_injection();

		$this->content_review_list();

		$this->content_form();

		$this->content_advanced_options();

		$this->content_navigation();

		$this->style_review_title();

		$this->style_review();

		$this->style_avatar();

		$this->style_author();

		$this->style_rating();

		$this->style_date();

		$this->style_content();

		$this->style_form_title();

		$this->style_navigation();

		$this->style_form();

		$this->style_form_elements();
	}

	protected function content_review_list() {
		$this->start_controls_section(
			'section_content_review_list',
			array(
				'label' => __( 'Reviews List', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'review_list_title_heading',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'review_list_title_style',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'' => array(
						'title' => __( 'Disable', 'cmsmasters-elementor' ),
					),
					'default' => array(
						'title' => __( 'Default', 'cmsmasters-elementor' ),
					),
					'only-count' => array(
						'title' => __( 'Only Count', 'cmsmasters-elementor' ),
						'description' => __( 'Displays amount of reviews', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'default',
				'toggle' => false,
			)
		);

		$this->add_control(
			'review_list_title_tag',
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
				'condition' => array(
					'custom_review_list_title_style!' => '',
				),
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
						'title' => __( 'Inline', 'cmsmasters-elementor' ),
						'description' => __( 'Stays in line with author & date', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'aside',
				'toggle' => false,
				'separator' => 'before',
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
					'{{WRAPPER}} .cmsmasters-product-review__avatar' => 'min-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .cmsmasters-product-review__avatar img' => 'width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .cmsmasters-product-review' => '--avatar-size: {{SIZE}}{{UNIT}}',
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
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}} .cmsmasters-product-review__info' => 'align-items: {{VALUE}}; display: flex;',
				),
				'condition' => array( 'custom_avatar_position' => 'with-author' ),
			)
		);

		$this->add_control(
			'author_text_after',
			array(
				'label' => __( 'Author Suffix', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'says:', 'cmsmasters-elementor' ),
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
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'block' => array(
						'title' => __( 'Block', 'cmsmasters-elementor' ),
					),
					'inline' => array(
						'title' => __( 'Inline', 'cmsmasters-elementor' ),
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
			'rating_heading',
			array(
				'label' => __( 'Rating', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'rating_position',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'block' => array(
						'title' => __( 'Block', 'cmsmasters-elementor' ),
					),
					'inline' => array(
						'title' => __( 'Inline', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'inline',
				'toggle' => false,
			)
		);

		$this->add_control(
			'rating_inline_position',
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
				'prefix_class' => 'cmsmasters-rating-inline-',
				'condition' => array( 'custom_rating_position' => 'inline' ),
			)
		);

		$this->start_controls_tabs( 'rating_icon_tabs' );

			$this->start_controls_tab(
				'rating_empty',
				array( 'label' => __( 'Empty', 'cmsmasters-elementor' ) )
			);

			$this->add_control(
				'empty_rating_icon',
				array(
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'default' => array(
						'value' => 'far fa-star',
						'library' => 'fa-regular',
					),
				)
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'rating_filled',
				array( 'label' => __( 'Filled', 'cmsmasters-elementor' ) )
			);

			$this->add_control(
				'filled_rating_icon',
				array(
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'icon',
					'default' => array(
						'value' => 'fas fa-star',
						'library' => 'fa-regular',
					),
				)
			);

			$this->end_controls_tab();

		$this->end_controls_tabs();

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
						'title' => __( 'Logged In User', 'cmsmasters-elementor' ),
					),
					'user' => array(
						'title' => __( 'Logged Out User', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => true,
				'default' => 'user',
				'toggle' => false,
				'separator' => 'before',
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-form-view-',
			)
		);

		$this->add_control(
			'comment_heading',
			array(
				'label' => __( 'Review Field', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'comment_direction',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
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
				'default' => 'row',
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
				'default' => 'start',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-comment-position-',
			)
		);

		$this->add_control(
			'input_heading',
			array(
				'label' => __( 'Input', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'custom_comment_direction' => 'row' ),
			)
		);

		$this->add_control(
			'input_direction',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inline' => array(
						'title' => __( 'Inline', 'cmsmasters-elementor' ),
						'description' => __( 'Inputs in line', 'cmsmasters-elementor' ),
					),
					'rows' => array(
						'title' => __( 'Rows', 'cmsmasters-elementor' ),
						'description' => __( 'Every input on a new line', 'cmsmasters-elementor' ),
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

		$this->add_responsive_control(
			'input_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 30,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-reviews' => '--input-width: {{SIZE}}%;',
				),
				'condition' => array(
					'custom_comment_direction' => 'row',
					'custom_input_direction' => 'rows',
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
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'label_block' => false,
				'default' => 'left',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-button-align-',
				'selectors' => array(
					'{{WRAPPER}} .form-submit' => 'text-align: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-button-align-justify .submit' => 'width: 100%; text-align: center;',
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

		$this->add_responsive_control(
			'review_list_width',
			array(
				'label' => __( 'Review List Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
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
					'{{WRAPPER}} .cmsmasters-product-reviews__wrapper' => 'width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
				'condition' => array( 'custom_review_direction' => 'row' ),
			)
		);

		$this->add_control(
			'add_review_heading',
			array(
				'label' => __( 'Add Review', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'add_review_hide',
			array(
				'label' => __( 'Hide', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'add_review_tag',
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
				'condition' => array(
					'custom_add_review_hide' => '',
				),
			)
		);

		$this->add_control(
			'add_review_text',
			array(
				'label' => __( 'Add Review Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Add Review', 'cmsmasters-elementor' ),
				'condition' => array(
					'custom_add_review_hide' => '',
				),
			)
		);

		$this->add_control(
			'label_heading',
			array(
				'label' => __( 'Field Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
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
					'{{WRAPPER}} .cmsmasters-product-reviews' => '--label-gap: {{SIZE}}{{UNIT}};',
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
			'field_icon_review',
			array(
				'label' => __( 'Review', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'review_text',
			array(
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Review', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'review_icon',
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
				'placeholder' => 'Add Review',
			)
		);

		$this->add_control(
			'submit_button_icon',
			array(
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'condition' => array( 'custom_submit_button_type!' => 'text' ),
			)
		);

		$this->add_control(
			'form_rating_inline',
			array(
				'label' => __( 'Rating Label Inline', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-form-rating-inline-',
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
					'standard' => array(
						'title' => __( 'Standard', 'cmsmasters-elementor' ),
					),
					'pagination' => array(
						'title' => __( 'Pagination', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'standard',
				'toggle' => false,
			)
		);

		$this->add_control(
			'navigation_standard_type',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
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
				'default' => 'text-icon',
				'toggle' => false,
				'condition' => array(
					'custom_navigation_type' => 'standard',
				),
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
					'conditions' => array(
						'relation' => 'or',
						'terms' => array(
							array(
								'name' => 'custom_navigation_type',
								'operator' => '=',
								'value' => 'pagination',
							),
							array(
								'relation' => 'and',
								'terms' => array(
									array(
										'name' => 'custom_navigation_type',
										'operator' => '=',
										'value' => 'standard',
									),
									array(
										'name' => 'custom_navigation_standard_type',
										'operator' => '!==',
										'value' => 'text',
									),
								),
							),
						),
					),
				)
			);

			$this->add_control(
				'navigation_text_previous',
				array(
					'label' => __( 'Text', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => 'Older Reviews',
					'condition' => array(
						'custom_navigation_type' => 'standard',
						'custom_navigation_standard_type!' => 'icon',
					),
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
					'conditions' => array(
						'relation' => 'or',
						'terms' => array(
							array(
								'name' => 'custom_navigation_type',
								'operator' => '=',
								'value' => 'pagination',
							),
							array(
								'relation' => 'and',
								'terms' => array(
									array(
										'name' => 'custom_navigation_type',
										'operator' => '=',
										'value' => 'standard',
									),
									array(
										'name' => 'custom_navigation_standard_type',
										'operator' => '!==',
										'value' => 'text',
									),
								),
							),
						),
					),
				)
			);

			$this->add_control(
				'navigation_text_next',
				array(
					'label' => __( 'Text', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => 'Newer Reviews',
					'condition' => array(
						'custom_navigation_type' => 'standard',
						'custom_navigation_standard_type!' => 'icon',
					),
				)
			);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function style_review_title() {
		$this->start_controls_section(
			'section_style_review_title',
			array(
				'label' => __( 'Reviews List Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'review_title_alignment',
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
				'name' => 'review_title_typography',
				'selector' => '{{WRAPPER}} #reviews .cmsmasters-product-reviews__title',
			)
		);

		$this->add_control(
			'review_title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #reviews .cmsmasters-product-reviews__title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'review_title_text_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-product-reviews__title',
			)
		);

		$this->add_responsive_control(
			'review_title_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} #reviews .cmsmasters-product-reviews__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'review_title_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 30,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} #reviews .cmsmasters-product-reviews__title' => 'width: {{SIZE}}%; margin-left: auto; margin-right: auto;',
				),
				'condition' => array( 'custom_review_title_show_lines' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'review_title_width_auto',
			array(
				'label' => __( 'Width Auto', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'cmsmasters-comment-title-width-auto-',
				'condition' => array( 'custom_review_title_show_lines' => '' ),
			)
		);

		$this->add_control(
			'review_title_show_lines',
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
				'name' => 'review_title_separator',
				'selector' => '{{WRAPPER}} #reviews .cmsmasters-product-reviews__title:before, {{WRAPPER}} #reviews .cmsmasters-product-reviews__title:after',
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
				'condition' => array( 'custom_review_title_show_lines' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'review_title_separator_gap',
			array(
				'label' => __( 'Border Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-reviews' => '--comment-title-border-gap: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'custom_review_title_show_lines' => 'yes',
					'custom_review_title_separator_border!' => '',
				),
			)
		);

		$this->add_control(
			'review_title_background',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #reviews .cmsmasters-product-reviews__title' => 'background-color: {{VALUE}}',
				),
				'condition' => array( 'custom_review_title_show_lines' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'review_title_border',
				'selector' => '{{WRAPPER}} #reviews .cmsmasters-product-reviews__title',
				'fields_options' => array(
					'border' => array(
						'separator' => 'before',
					),
				),
				'condition' => array( 'custom_review_title_show_lines' => '' ),
			)
		);

		$title_style_condition = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'custom_review_title_show_lines',
					'operator' => '=',
					'value' => '',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'custom_review_title_border_border',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'custom_review_title_background',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			),
		);

		$this->add_responsive_control(
			'review_title_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} #reviews .cmsmasters-product-reviews__title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $title_style_condition,
			)
		);

		$this->add_responsive_control(
			'review_title_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} #reviews .cmsmasters-product-reviews__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $title_style_condition,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'review_title_box_shadow',
				'selector' => '{{WRAPPER}} #reviews .cmsmasters-product-reviews__title',
				'conditions' => $title_style_condition,
			)
		);

		$this->end_controls_section();
	}

	protected function style_review() {
		$this->start_controls_section(
			'section_style_review',
			array(
				'label' => __( 'Review', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'review_gap_between',
			array(
				'label' => __( 'Gap Between Reviews', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-review' => '--wrapper-between-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'review_background',
				'exclude' => array( 'image' ),
				'selector' => '{{WRAPPER}} .cmsmasters-product-review__body',
			)
		);

		$this->add_control(
			'review_border_border',
			array(
				'label' => __( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'solid' => _x( 'Solid', 'Border Type', 'cmsmasters-elementor' ),
					'double' => _x( 'Double', 'Border Type', 'cmsmasters-elementor' ),
					'dotted' => _x( 'Dotted', 'Border Type', 'cmsmasters-elementor' ),
					'dashed' => _x( 'Dashed', 'Border Type', 'cmsmasters-elementor' ),
					'groove' => _x( 'Groove', 'Border Type', 'cmsmasters-elementor' ),
				),
				'default' => 'solid',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-review__body' => 'border-style: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'review_border_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-review__body' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-product-review' => '--wrapper-border-left: {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'custom_review_border_border!' => '' ),
			)
		);

		$this->add_control(
			'review_border_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-review__body' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'custom_review_border_border!' => '' ),
			)
		);

		$this->add_responsive_control(
			'review_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-review__body' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'custom_review_background_color',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'custom_review_border_border',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'review_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-review' => '--wrapper-padding-top: {{TOP}}{{UNIT}}; --wrapper-padding-right: {{RIGHT}}{{UNIT}}; --wrapper-padding-bottom: {{BOTTOM}}{{UNIT}}; --wrapper-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'review_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-product-review__body',
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
					'{{WRAPPER}} .cmsmasters-product-review' => '--avatar-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'avatar_border',
				'selector' => '{{WRAPPER}} .cmsmasters-product-review__avatar img',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
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
					'{{WRAPPER}} .cmsmasters-product-review__avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'avatar_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-product-review__avatar img',
			)
		);

		$this->end_controls_section();
	}

	protected function style_author() {
		$wrapper = '{{WRAPPER}} .cmsmasters-product-review';

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
					'{{WRAPPER}} .cmsmasters-product-review' => '--author-margin: {{SIZE}}{{UNIT}};',
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
							"{$wrapper}__author," .
							"{$wrapper}__author a" => 'color: {{VALUE}}',
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
							"{$wrapper}__author a:hover" => 'color: {{VALUE}}',
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
							"{$wrapper}__author a" => 'transition: all {{SIZE}}s',
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

		$this->end_controls_section();
	}

	protected function style_rating() {
		$wrapper = '{{WRAPPER}} .cmsmasters-product-review';

		$this->start_controls_section(
			'section_style_rating',
			array(
				'label' => __( 'Rating', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'rating_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-review' => '--rating-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'rating_icon_gap',
			array(
				'label' => __( 'Icon Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-review' => '--rating-icon-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'rating_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-reviews' => '--rating-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'rating_tabs',
			array( 'separator' => 'before' )
		);

			$this->start_controls_tab(
				'rating_tab_empty',
				array(
					'label' => __( 'Empty', 'cmsmasters-elementor' ),
				)
			);

				$this->add_control(
					'rating_color_empty',
					array(
						'label' => __( 'Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}}' => '--star-color: {{VALUE}}',
						),
					)
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'rating_tab_filled',
				array(
					'label' => __( 'Filled', 'cmsmasters-elementor' ),
				)
			);

				$this->add_control(
					'rating_color_filled',
					array(
						'label' => __( 'Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}}' => '--star-active-color: {{VALUE}}',
						),
					)
				);

				$this->add_control(
					'rating_transition',
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
							'{{WRAPPER}} .stars, {{WRAPPER}} .stars a' => 'transition: color {{SIZE}}s',
						),
					)
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'rating_text_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters_star_trans_wrap',
				'separator' => 'before',
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
					'{{WRAPPER}} .cmsmasters-product-review' => '--date-margin: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'custom_date_position' => 'inline',
					'custom_date_inline_position' => 'start',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'date_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-product-review__date-wrap, {{WRAPPER}} .cmsmasters-product-review__date, {{WRAPPER}} .cmsmasters-product-review__date-wrap a',
			)
		);

		$this->add_control(
			'date_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-review__date, {{WRAPPER}} .cmsmasters-product-review__date-wrap a' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .cmsmasters-product-review__date-wrap i,' .
					'{{WRAPPER}} .cmsmasters-product-review__date-wrap svg' => 'margin-right: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .cmsmasters-product-review__date-wrap i,' .
					'{{WRAPPER}} .cmsmasters-product-review__date-wrap svg' => 'font-size: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .cmsmasters-product-review__date-wrap i,' .
					'{{WRAPPER}} .cmsmasters-product-review__date-wrap svg' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .cmsmasters-product-review' => '--content-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-product-review__content',
			)
		);

		$this->add_control(
			'content_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-review__content' => 'color: {{VALUE}}',
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'navigation_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-product-reviews .nav-links a, {{WRAPPER}} .cmsmasters-pagination .page-numbers:not(ul)',
				'condition' => array( 'custom_navigation_standard_type!' => 'icon' ),
			)
		);

		$this->add_control(
			'navigation_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array( 'size' => 20 ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-reviews__title + .comment-navigation' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-product-reviews__list + .comment-navigation,
					{{WRAPPER}} .cmsmasters-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
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
					'{{WRAPPER}} .cmsmasters-product-reviews .nav-previous i,' .
					'{{WRAPPER}} .cmsmasters-product-reviews .nav-previous svg' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-product-reviews .nav-next i,' .
					'{{WRAPPER}} .cmsmasters-product-reviews .nav-next svg' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'custom_navigation_standard_type!' => 'text' ),
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
				'selector' => '{{WRAPPER}} .cmsmasters-product-reviews .nav-links a, {{WRAPPER}} .cmsmasters-pagination .page-numbers:not(ul)',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'custom_navigation_type',
							'operator' => '=',
							'value' => 'pagination',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'custom_navigation_type',
									'operator' => '=',
									'value' => 'standard',
								),
								array(
									'name' => 'custom_navigation_view',
									'operator' => '=',
									'value' => 'button',
								),
							),
						),
					),
				),
			)
		);

		$navigation_condition = array(
			'relation' => 'or',
			'terms' => array(
				array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'custom_navigation_type',
							'operator' => '=',
							'value' => 'pagination',
						),
						array(
							'name' => 'custom_navigation_border_border',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
				array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'custom_navigation_type',
							'operator' => '=',
							'value' => 'standard',
						),
						array(
							'name' => 'custom_navigation_border_border',
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => 'custom_navigation_view',
							'operator' => '=',
							'value' => 'button',
						),
					),
				),
			),
		);

		$this->start_controls_tabs(
			'navigation_tabs',
			array( 'separator' => 'before' )
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			'current' => __( 'Current', 'cmsmasters-elementor' ),
		) as $tabs_key => $label ) {
			if ( 'normal' === $tabs_key ) {
				$tab_selector = '{{WRAPPER}} .cmsmasters-product-reviews .nav-links a, {{WRAPPER}} .cmsmasters-pagination .page-numbers:not(ul)';
			} elseif ( 'hover' === $tabs_key ) {
				$tab_selector = '{{WRAPPER}} .cmsmasters-product-reviews .nav-links a:hover, {{WRAPPER}} .cmsmasters-pagination .page-numbers:not(ul):hover';
			} else {
				$tab_selector = '{{WRAPPER}} .cmsmasters-pagination .page-numbers:not(ul).current';
			}

			$this->navigation_tab( $tabs_key, $label, $tab_selector, $navigation_condition );
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'navigation_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-reviews .nav-links a, {{WRAPPER}} .cmsmasters-pagination .page-numbers:not(ul)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'conditions' => $navigation_condition,
			)
		);

		$this->add_responsive_control(
			'navigation_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-reviews .nav-links a, {{WRAPPER}} .cmsmasters-pagination .page-numbers:not(ul)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => $navigation_condition,
			)
		);

		$this->end_controls_section();
	}

	protected function navigation_tab( $tabs_key, $label, $tab_selector, $navigation_condition ) {
		if ( 'current' !== $tabs_key ) {
			$this->start_controls_tab(
				"navigation_tab_{$tabs_key}",
				array(
					'label' => $label,
				)
			);
		} else {
			$this->start_controls_tab(
				"navigation_tab_{$tabs_key}",
				array(
					'label' => $label,
					'condition' => array( 'custom_navigation_type' => 'pagination' ),
				)
			);
		}

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

			$this->add_control(
				"navigation_bg_{$tabs_key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$tab_selector => 'background-color: {{VALUE}}',
					),
					'conditions' => $navigation_condition,
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
					'conditions' => $navigation_condition,
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "navigation_box_shadow_{$tabs_key}",
					'selector' => $tab_selector,
					'conditions' => $navigation_condition,
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
						'{{WRAPPER}} .cmsmasters-product-reviews .nav-links a,
						{{WRAPPER}} .cmsmasters-product-reviews .cmsmasters-pagination a' => 'transition: all {{SIZE}}s',
					),
				)
			);
		}

		$this->end_controls_tab();
	}

	protected function style_form_title() {
		$this->start_controls_section(
			'section_style_form_title',
			array(
				'label' => __( 'Review Form Heading', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'custom_add_review_hide' => '' ),
			)
		);

		$this->add_responsive_control(
			'form_title_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} #reviews .comment-reply-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
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
					'left' => 'flex-direction: row;',
					'center' => 'flex-direction: column; align-items: center;',
					'right' => 'flex-direction: row-reverse;',
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
					'{{WRAPPER}} .cmsmasters-product-reviews > .cmsmasters-reviews-wrapper' => 'width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto; max-width: 100%;',
				),
				'condition' => array( 'custom_review_direction' => 'row' ),
			)
		);

		$this->add_group_control(
			CmsmastersControls::BUTTON_BACKGROUND_GROUP,
			array(
				'name' => 'form_wrapper_background',
				'selector' => '{{WRAPPER}} .cmsmasters-product-reviews > .cmsmasters-reviews-wrapper',
				'condition' => array( 'custom_review_direction' => 'row' ),
			)
		);

		$this->add_responsive_control(
			'form_width',
			array(
				'label' => __( 'Form Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
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
					'{{WRAPPER}} .cmsmasters-product-reviews > #respond.comment-respond' => 'width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
				'condition' => array( 'custom_review_direction' => 'row' ),
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
					'{{WRAPPER}} .cmsmasters-product-reviews > #respond.comment-respond' => '{{VALUE}}',
				),
				'condition' => array( 'custom_review_direction' => 'row' ),
			)
		);

		$form_selector = '{{WRAPPER}} #reviews #respond.comment-respond';

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
					'{{WRAPPER}} .cmsmasters-product-reviews' => '--comment-width: {{SIZE}}%;',
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

		$this->add_control(
			'input_gap',
			array(
				'label' => __( 'Gap from Review', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 60 ),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-reviews' => '--input-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'input_gap_between',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range' => array(
					'px' => array(
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-reviews' => '--input-gap-between: {{SIZE}}{{UNIT}};',
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

		/* Start Tab Title Tabs */
		$this->start_controls_tabs(
			'form_elements_tabs',
			array( 'separator' => 'before' )
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'focus' => __( 'Focus', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			if ( 'normal' === $main_key ) {
				$selector = "{$selector_author}, {$selector_email}, {$selector_url}, {$selector_comment}";

				$webkit_placeholder = '{{WRAPPER}} input::-webkit-input-placeholder, {{WRAPPER}} textarea::-webkit-input-placeholder';
				$moz_placeholder = '{{WRAPPER}} input::-moz-placeholder, {{WRAPPER}} textarea::-moz-placeholder';
			} else {
				$selector = "{$selector_author}:focus, {$selector_email}:focus, {$selector_url}:focus, {$selector_comment}:focus";

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
							$selector => 'color: {{VALUE}}',
							$webkit_placeholder => 'color: {{VALUE}}',
							$moz_placeholder => 'color: {{VALUE}}',
						),
					)
				);

				$this->add_control(
					"form_elements_background_{$main_key}",
					array(
						'label' => __( 'Background Color', 'cmsmasters-elementor' ),
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
							"{$selector_author}, {$selector_email}, {$selector_url}, {$selector_comment}" => 'transition: all {{SIZE}}s',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'form_elements_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-input-wrap input,
					{{WRAPPER}} .comment-form-comment textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'form_elements_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-input-wrap input,
					{{WRAPPER}} .comment-form-comment textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .cmsmasters-product-reviews' => '--input-icon-padding: {{RIGHT}}{{UNIT}}; --textarea-icon-padding: {{TOP}}{{UNIT}};',
				),
			)
		);

		$label_as_placeholder = array( 'custom_use_label_instead_placeholder!' => 'yes' );

		$this->add_control(
			'input_label_heading',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $label_as_placeholder,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'form_label_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-input-wrap label, {{WRAPPER}} .comment-form-comment label',
				'condition' => $label_as_placeholder,
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
				'condition' => $label_as_placeholder,
			)
		);

		$input_icon = array( 'custom_enable_input_icon' => 'yes' );

		$this->add_control(
			'input_icon_style_heading',
			array(
				'label' => __( 'Input Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $input_icon,
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
					'{{WRAPPER}} .comment-form-comment i,' .
					'{{WRAPPER}} .cmsmasters-input-wrap i,' .
					'{{WRAPPER}} .comment-form-comment svg,' .
					'{{WRAPPER}} .cmsmasters-input-wrap svg' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .cmsmasters-product-reviews' => '--input-icon-size: {{SIZE}}px;',
				),
				'condition' => $input_icon,
			)
		);

		$this->add_control(
			'input_icon_icon',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .comment-form-comment i,' .
					'{{WRAPPER}} .cmsmasters-input-wrap i,' .
					'{{WRAPPER}} .comment-form-comment svg,' .
					'{{WRAPPER}} .cmsmasters-input-wrap svg' => 'color: {{VALUE}}',
				),
				'condition' => $input_icon,
			)
		);

		$this->add_control(
			'submit_button_heading',
			array(
				'label' => __( 'Form Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-reviews' => '--button-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$submit = '{{WRAPPER}} #respond .form-submit .submit#submit';

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

		/* Start Tab Title Tabs */
		$this->start_controls_tabs(
			'submit_button_tabs',
			array( 'separator' => 'before' )
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
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
							"{$selector_author}, {$selector_email}, {$selector_comment}" => 'transition: all {{SIZE}}s',
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
				'selectors' => array(
					$submit => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .cmsmasters-product-reviews' => '--submit-padding-right: {{RIGHT}}{{UNIT}}',
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
					'{{WRAPPER}} #respond.comment-respond .form-submit .submit i' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} #respond.comment-respond .form-submit .submit:hover i' => 'color: {{VALUE}}',
				),
				'condition' => array( 'custom_submit_button_type!' => 'text' ),
			)
		);

		$this->add_control(
			'additional_fields_heading',
			array(
				'label' => __( 'Additional Form Fields', 'cmsmasters-elementor' ),
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

		$this->start_controls_tabs( 'additional_fields_styles' );

			$this->start_controls_tab(
				'additional_fields_gap',
				array( 'label' => __( 'Gap', 'cmsmasters-elementor' ) )
			);

		$this->add_control(
			'rating_label_margin',
			array(
				'label' => __( 'Rating Label Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--form-rating-label-margin: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'your_rating_gap',
			array(
				'label' => __( 'Rating', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 60 ),
				),
				'selectors' => array(
					'{{WRAPPER}} .comment-form-rating > p' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'additional_comment_notes',
			array(
				'label' => __( 'Review Notes', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
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

		$this->add_control(
			'additional_cookies_consent',
			array(
				'label' => __( 'Cookies Consent', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
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

		$this->end_controls_tab();

		$this->start_controls_tab(
			'additional_fields_style',
			array( 'label' => __( 'Style', 'cmsmasters-elementor' ) )
		);

		$selector_info = '{{WRAPPER}} .logged-in-as, {{WRAPPER}} .logged-in-as a, {{WRAPPER}} .comment-notes, {{WRAPPER}} .comment-form-cookies-consent label, {{WRAPPER}} .comment-form-rating label';

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
				'label' => __( '‘Logged in as’ Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .logged-in-as a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'rating_style_form_heading',
			array(
				'label' => __( 'Rating', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'form_rating_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-reviews' => '--form-rating-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'form_rating_icon_gap',
			array(
				'label' => __( 'Icon Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-product-review' => '--form-rating-icon-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'form_rating_color_empty',
			array(
				'label' => __( 'Color Empty', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--form-star-color: {{VALUE}}',
				),
			)
		);
		$this->add_control(
			'form_rating_color_filled',
			array(
				'label' => __( 'Color Filled', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--form-star-active-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'form_rating_text_shadow',
				'selector' => '{{WRAPPER}} .comment-form-rating p.stars a',
			)
		);

		$this->add_control(
			'rating_label_style_form_heading',
			array(
				'label' => __( 'Rating Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'rating_label_style_form_typography',
				'selector' => '{{WRAPPER}} #respond .comment-form-rating label',
			)
		);

		$this->add_control(
			'rating_label_style_form_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #respond .comment-form-rating label' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'rating_label_style_form_text_shadow',
				'selector' => '{{WRAPPER}} #respond .comment-form-rating label',
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
		) :
			?>
			<div class="elementor-alert elementor-alert-danger" role="alert">
				<span class="elementor-alert-title">
					<?php esc_html_e( 'Reviews Are Closed!', 'cmsmasters-elementor' ); ?>
				</span>
				<span class="elementor-alert-description">
					<?php esc_html_e( 'Switch on comments from either the discussion box on the WordPress post edit screen or from the WordPress discussion settings.', 'cmsmasters-elementor' ); ?>
				</span>
			</div>
			<?php
		elseif ( comments_open() ) :
			add_filter( 'dsq_can_load', '__return_false' );

			if ( Utils::is_edit_mode() && 'user' === $settings['custom_form_view_as'] ) {
				add_filter( 'comment_form_field_comment', array( $this, 'editor_log_out_preview' ) );
			}

			add_filter( 'comments_template', array( $this, 'comments_template_path' ) );
			add_filter( 'cmsmasters_elementor/widgets/cmsmasters-product-review/template_variables', array( $this, 'template_variables' ) );

			if ( 'human_readable' !== $this->render_date( 'date' ) ) {
				add_filter( 'get_comment_date', array( $this, 'custom_comment_date' ), 10, 3 );
				add_filter( 'get_comment_time', array( $this, 'custom_comment_time' ), 10, 5 );
			}

			comments_template();

			remove_filter( 'cmsmasters_elementor/widgets/cmsmasters-product-review/template_variables', array( $this, 'template_variables' ) );
			remove_filter( 'comments_template', array( $this, 'comments_template_path' ) );

			if ( 'human_readable' !== $this->render_date( 'date' ) ) {
				remove_filter( 'get_comment_date', array( $this, 'custom_comment_date' ), 10, 3 );
				remove_filter( 'get_comment_time', array( $this, 'custom_comment_time' ), 10, 5 );
			}

			add_filter( 'dsq_can_load', '__return_true' );

			?>
			<script>
				jQuery( '#reviews, .cmsmasters-product-reviews, #rating' ).trigger( 'init' );
			</script>
			<?php

		endif;

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

		$is_placeholder = $data['settings']['custom_use_label_instead_placeholder'];
		$comment_text = ( ( ! $data['review_text'] ) ? esc_attr__( 'Review', 'cmsmasters-elementor' ) : ( isset( $data['comment_text'] ) ? $data['comment_text'] : '' ) );
		$name_text = ( ( ! $data['name_text'] ) ? esc_attr__( 'Name', 'cmsmasters-elementor' ) : $data['name_text'] );
		$email_text = ( ( ! $data['email_text'] ) ? esc_attr__( 'Email', 'cmsmasters-elementor' ) : $data['email_text'] );
		$rating_uniqid = uniqid( 'rating-' );
		$comment_uniqid = uniqid( 'comment-' );
		$author_uniqid = uniqid( 'author-' );
		$email_uniqid = uniqid( 'email-' );

		/* translators: Product Reviews WooCommerce widget comment notes. %1$s: Email notes %2$s: Required fields note */
		$form_fields = sprintf(
			'<p class="comment-notes">%1$s%2$s</p>',
			/* translators: Product Reviews WooCommerce widget email notes. %s: Email note */
			sprintf(
				'<span id="email-notes">%s</span>',
				__( 'Your email address will not be published.', 'cmsmasters-elementor' )
			),
			' ' . __( 'Required fields are marked *', 'cmsmasters-elementor' )
		) .
		'<div class="comment-form-rating">
			<label for="' . $comment_uniqid . '">' .
				esc_html__( 'Your rating', 'cmsmasters-elementor' ) .
			'</label>
			<select id="' . $comment_uniqid . '" name="rating" required>
				<option value="">' . esc_html__( 'Rate&hellip;', 'cmsmasters-elementor' ) . '</option>
				<option value="5">' . esc_html__( 'Perfect', 'cmsmasters-elementor' ) . '</option>
				<option value="4">' . esc_html__( 'Good', 'cmsmasters-elementor' ) . '</option>
				<option value="3">' . esc_html__( 'Average', 'cmsmasters-elementor' ) . '</option>
				<option value="2">' . esc_html__( 'Not that bad', 'cmsmasters-elementor' ) . '</option>
				<option value="1">' . esc_html__( 'Very poor', 'cmsmasters-elementor' ) . '</option>
			</select>
		</div>' .
		'<p class="comment-form-comment">' .
			'<label for="' . $comment_uniqid . '"' . ( $is_placeholder ? ' class="screen-reader-text"' : '' ) . '>' .
				$data['icon']['review'] . $comment_text .
			'</label>' .
			'<textarea id="' . $comment_uniqid . '" name="comment" cols="67"
				rows="' . $data['settings']['custom_form_elements_comment_rows']['size'] . '"
				placeholder="' . $comment_text .
			'"></textarea>' .
			( ( $is_placeholder ) ? $data['icon']['review'] : '' ) .
		'</p>' .
		'<div class="cmsmasters-input-wrap"><p class="comment-form-author">' .
			'<label for="' . $author_uniqid . '"' . ( $is_placeholder ? ' class="screen-reader-text"' : '' ) . '>' .
				$data['icon']['name'] . $name_text . ' *' .
			'</label>' .
			'<input id="' . $author_uniqid . '" type="text" name="author" size="35"
				placeholder="' . ( ( $is_placeholder ) ? $name_text . ' *' : '' ) .
			'" />' .
			( ( $is_placeholder ) ? $data['icon']['name'] : '' ) .
		'</p>' .
		'<p class="comment-form-email">' .
			'<label for="' . $email_uniqid . '"' . ( $is_placeholder ? ' class="screen-reader-text"' : '' ) . '>' .
				$data['icon']['email'] . $email_text . ' *' .
			'</label>' .
			'<input id="' . $email_uniqid . '" type="text" name="email" size="35"
				placeholder="' . ( ( $is_placeholder ) ? $email_text . ' *' : '' ) .
			'" />' .
			( ( $is_placeholder ) ? $data['icon']['email'] : '' ) .
		'</p></div>' .
		'<p class="comment-form-cookies-consent">' .
			'<input type="checkbox" id="comment-form-cookies-consent" name="comment-form-cookies-consent" value="yes" checked="checked" />' .
			'<label for="comment-form-cookies-consent">' .
				esc_html__( 'Save my name and email in this browser for the next time I review.', 'cmsmasters-elementor' ) .
			'</label>' .
		'</p>';

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

		$data['icon']['form_title_icon'] = $this->render_icon( 'form_title_icon' );
		$data['icon']['review'] = ( 'yes' === $settings['custom_enable_input_icon'] ) ? $this->render_icon( 'review' ) : '';
		$data['icon']['name'] = ( 'yes' === $settings['custom_enable_input_icon'] ) ? $this->render_icon( 'name' ) : '';
		$data['icon']['email'] = ( 'yes' === $settings['custom_enable_input_icon'] ) ? $this->render_icon( 'email' ) : '';
		$data['icon']['submit'] = ( 'text' !== $settings['custom_submit_button_type'] ) ? $this->render_icon( 'submit_button' ) : '';

		$data['submit_text'] = ( '' !== $settings['custom_submit_button_text'] ) ?
			$settings['custom_submit_button_text'] :
			__( 'Add Review', 'cmsmasters-elementor' );

		$data['review_text'] = $this->is_text( $settings, 'review' );
		$data['name_text'] = $this->is_text( $settings, 'name' );
		$data['email_text'] = $this->is_text( $settings, 'email' );

		$data['comment_meta']['author_text_after'] = '<span class="cmsmasters-text-after">' . $settings['custom_author_text_after'] . '</span>';
		$data['comment_meta']['date_format'] = $this->render_date( 'date' );
		$data['comment_meta']['time_enable'] = $settings['custom_date_time_switcher'];
		$data['comment_meta']['date_separator'] = $settings['custom_date_time_separator_text'];
		$data['comment_meta']['date_icon'] = $this->render_icon( 'date' );
		$data['comment_meta']['rating_filled'] = $this->render_icon( 'filled_rating' );
		$data['comment_meta']['rating_empty'] = $this->render_icon( 'empty_rating' );

		if ( 'human_readable' === $this->render_date( 'date' ) ) {
			$data['comment_meta']['human_readable'] = true;
		} else {
			$data['comment_meta']['human_readable'] = false;
		}

		$data['navigation']['prev_icon'] = ( 'text' !== $settings['custom_navigation_standard_type'] ? $this->render_icon( 'navigation_previous' ) : '' );
		$prev_text = ( '' !== $settings['custom_navigation_text_previous'] ) ? $settings['custom_navigation_text_previous'] : esc_attr__( 'Older Reviews', 'cmsmasters-elementor' );
		$next_text = ( '' !== $settings['custom_navigation_text_next'] ) ? $settings['custom_navigation_text_next'] : esc_attr__( 'Newer Reviews', 'cmsmasters-elementor' );
		$data['navigation']['prev_text'] = ( 'icon' !== $settings['custom_navigation_standard_type'] ? $prev_text : '' );
		$data['navigation']['next_icon'] = ( 'text' !== $settings['custom_navigation_standard_type'] ? $this->render_icon( 'navigation_next' ) : '' );
		$data['navigation']['next_text'] = ( 'icon' !== $settings['custom_navigation_standard_type'] ? $next_text : '' );

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

		return $this->get_render_date(
			array( 'format' => $format )
		);
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
