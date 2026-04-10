<?php
namespace CmsmastersElementor\Modules\TextEditor\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * CMSMasters Text Editor widget.
 *
 * Widget that displays a WYSIWYG text editor, just like the WordPress editor.
 *
 * @since 1.23.0
 */
class Text_Editor extends Base_Widget {

	/**
	 * Get widget title.
	 *
	 * @since 1.23.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Text Editor', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.23.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-text-editor';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * @since 1.23.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'text',
			'editor',
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * @since 1.23.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array( 'widget-cmsmasters-text-editor' );
	}

	/**
	 * @since 1.23.0
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register text editor widget controls.
	 *
	 * @since 1.23.0
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_editor',
			array( 'label' => __( 'Text Editor', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'editor',
			array(
				'label' => '',
				'type' => Controls_Manager::WYSIWYG,
				'default' => '<p>' . __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'cmsmasters-elementor' ) . '</p>',
			)
		);

		$this->add_control(
			'drop_cap',
			array(
				'label' => __( 'Drop Cap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'prefix_class' => 'cmsmasters-drop-cap-',
				'frontend_available' => true,
				'assets' => array(
					'styles' => array(
						array(
							'name' => 'widget-text-editor',
							'conditions' => array(
								'terms' => array(
									array(
										'name' => 'drop_cap',
										'operator' => '===',
										'value' => 'yes',
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'text_columns',
			array(
				'label' => __( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmaster-text-editor-columns: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label' => __( 'Columns Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'em',
					'rem',
					'vw',
					'custom',
				),
				'range' => array(
					'px' => array(
						'max' => 100,
					),
					'%' => array(
						'max' => 10,
						'step' => 0.1,
					),
					'vw' => array(
						'max' => 10,
						'step' => 0.1,
					),
					'em' => array(
						'max' => 10,
					),
					'rem' => array(
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmaster-text-editor-column-gap: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'text_columns',
							'operator' => '>',
							'value' => 1,
						),
						array(
							'name' => 'text_columns',
							'operator' => '===',
							'value' => '',
						),
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Text Editor', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'classes' => 'elementor-control-start-end',
				'selectors_dictionary' => array(
					'left' => is_rtl() ? 'end' : 'start',
					'right' => is_rtl() ? 'start' : 'end',
				),
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmaster-text-editor-text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array( 'name' => 'text_editor_typography' )
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array( 'name' => 'text_editor' )
		);

		$this->add_responsive_control(
			'text_spacing',
			array(
				'label' => __( 'Text Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'rem',
					'vh',
					'custom',
				),
				'range' => array(
					'px' => array(
						'max' => 100,
					),
					'em' => array(
						'min' => 0.1,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-text-editor-text-spacing: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'heading_spacing',
			array(
				'label' => __( 'Heading Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'rem',
					'vh',
					'custom',
				),
				'range' => array(
					'px' => array(
						'max' => 100,
					),
					'em' => array(
						'min' => 0.1,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-text-editor-heading-spacing: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'separator',
			array( 'type' => Controls_Manager::DIVIDER )
		);

		$this->start_controls_tabs( 'link_colors' );

		$this->start_controls_tab(
			'colors_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'text_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmaster-text-editor-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label' => __( 'Link Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmaster-text-editor-link-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'colors_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'link_hover_color',
			array(
				'label' => __( 'Link Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmaster-text-editor-link-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_hover_color_transition_duration',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					's',
					'ms',
					'custom',
				),
				'default' => array( 'unit' => 's' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmaster-text-editor-link-transition-duration: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_drop_cap',
			array(
				'label' => __( 'Drop Cap', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'drop_cap' => 'yes' ),
			)
		);

		$this->add_control(
			'drop_cap_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'stacked' => __( 'Stacked', 'cmsmasters-elementor' ),
					'framed' => __( 'Framed', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'prefix_class' => 'cmsmasters-drop-cap-view-',
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'text_editor_drop_cap_typography',
				'exclude' => array(
					'letter_spacing',
					'word_spacing',
				),
			)
		);

		$this->add_control(
			'drop_cap_primary_color',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmaster-text-editor-drop-cap-primary-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'drop_cap_secondary_color',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmaster-text-editor-drop-cap-secondary-color: {{VALUE}};',
				),
				'condition' => array( 'drop_cap_view!' => 'default' ),
			)
		);

		$this->add_control(
			'drop_cap_space',
			array(
				'label' => __( 'Space', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'rem',
					'custom',
				),
				'range' => array(
					'px' => array(
						'max' => 50,
					),
					'em' => array(
						'max' => 5,
					),
					'rem' => array(
						'max' => 5,
					),
				),
				'default' => array( 'size' => 10 ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmaster-text-editor-drop-cap-space: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'drop_cap_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'rem',
					'custom',
				),
				'range' => array(
					'px' => array(
						'max' => 30,
					),
					'em' => array(
						'max' => 3,
					),
					'rem' => array(
						'max' => 3,
					),
				),
				'default' => array( 'size' => 5 ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmaster-text-editor-drop-cap-padding: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'drop_cap_view!' => 'default' ),
			)
		);

		$this->add_control(
			'drop_cap_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'em',
					'rem',
					'custom',
				),
				'range' => array(
					'%' => array(
						'max' => 50,
					),
				),
				'default' => array( 'unit' => '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmaster-text-editor-drop-cap-border-radius: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'drop_cap_view!' => 'default' ),
			)
		);

		$this->add_control(
			'drop_cap_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
					'rem',
					'vw',
					'custom',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmaster-text-editor-drop-cap-border-top-width: {{TOP}}{{UNIT}}; --cmsmaster-text-editor-drop-cap-border-right-width: {{RIGHT}}{{UNIT}}; --cmsmaster-text-editor-drop-cap-border-bottom-width: {{BOTTOM}}{{UNIT}}; --cmsmaster-text-editor-drop-cap-border-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'drop_cap_view' => 'framed' ),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array( 'name' => 'text_editor_drop_cap' )
		);

		$this->end_controls_section();
	}

	/**
	 * Render text editor widget output on the frontend.
	 *
	 * @since 1.23.0
	 */
	protected function render() {
		$should_render_inline_editing = Plugin::$instance->editor->is_edit_mode();

		$editor_content = $this->get_settings_for_display( 'editor' );
		$editor_content = $this->parse_text_editor( $editor_content );

		if ( empty( $editor_content ) ) {
			return;
		}

		if ( $should_render_inline_editing ) {
			$this->add_render_attribute( 'editor', 'class', array( 'elementor-text-editor', 'elementor-clearfix' ) );
		}

		$this->add_inline_editing_attributes( 'editor', 'advanced' );
		?>
		<?php if ( $should_render_inline_editing ) { ?>
			<div <?php $this->print_render_attribute_string( 'editor' ); ?>>
		<?php } ?>
		<?php // PHPCS - DO NOT REMOVE THIS ECHO - THE MAIN TEXT OF A WIDGET SHOULD NOT BE ESCAPED!
			echo $editor_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
		<?php if ( $should_render_inline_editing ) { ?>
			</div>
		<?php } ?>
		<?php
	}

	/**
	 * Render text editor widget as plain content.
	 *
	 * @since 1.23.0
	 */
	public function render_plain_content() {
		$this->print_unescaped_setting( 'editor' );
	}

	/**
	 * Render text editor widget output in the editor.
	 *
	 * @since 1.23.0
	 */
	protected function content_template() {
		?>
		<#
		if ( '' === settings.editor ) {
			return;
		}

		const shouldRenderInlineEditing = elementorFrontend.isEditMode();

		if ( shouldRenderInlineEditing ) {
			view.addRenderAttribute( 'editor', 'class', [ 'elementor-text-editor', 'elementor-clearfix' ] );
		}

		view.addInlineEditingAttributes( 'editor', 'advanced' );

		if ( shouldRenderInlineEditing ) { #>
			<div {{{ view.getRenderAttributeString( 'editor' ) }}}>
		<# } #>

			{{{ settings.editor }}}

		<# if ( shouldRenderInlineEditing ) { #>
			</div>
		<# } #>
		<?php
	}
}
