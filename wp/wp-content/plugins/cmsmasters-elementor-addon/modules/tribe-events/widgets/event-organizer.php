<?php
namespace CmsmastersElementor\Modules\TribeEvents\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TribeEvents\Traits\Tribe_Events_Singular_Widget;
use CmsmastersElementor\Plugin as CmsmastersPlugin;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Event Organizer widget.
 *
 * Addon widget that displays organizer of current Event.
 *
 * @since 1.13.0
 */
class Event_Organizer extends Base_Widget {

	use Tribe_Events_Singular_Widget;

	/**
	 * Get widget organizer.
	 *
	 * Retrieve widget organizer.
	 *
	 * @since 1.13.0
	 *
	 * @return string Widget organizer.
	 */
	public function get_title() {
		return __( 'Event Organizer', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.13.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-event-organizer';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.13.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'event',
			'organizer',
		);
	}

	/**
	 * Get tag names.
	 *
	 * Retrieve widget dynamic controls tag names.
	 *
	 * @since 1.13.0
	 *
	 * @return array Widget dynamic controls tag names.
	 */
	protected function get_tag_names() {
		return array(
			'organizer-email' => 'cmsmasters-tribe-events-organizer-email',
			'organizer-phone' => 'cmsmasters-tribe-events-organizer-phone',
			'organizer-title' => 'cmsmasters-tribe-events-organizer-title',
			'organizer-url' => 'cmsmasters-tribe-events-organizer-url',
			'organizer-website' => 'cmsmasters-tribe-events-organizer-website',
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

	protected function get_organizer_options() {
		return array(
			'organizer-title' => __( 'Title', 'cmsmasters-elementor' ),
			'organizer-phone' => __( 'Phone', 'cmsmasters-elementor' ),
			'organizer-website' => __( 'Website', 'cmsmasters-elementor' ),
			'organizer-email' => __( 'Email', 'cmsmasters-elementor' ),
		);
	}

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-tribe-events-organizer';
	}

	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	protected function register_controls() {
		$this->register_organizer_controls_content();

		$this->register_general_controls_style();

		$this->register_customize_controls_style();

		$this->register_customize_data_controls_style();
	}

	protected function register_organizer_controls_content() {
		$dynamic_tags = CmsmastersPlugin::elementor()->dynamic_tags;
		$tag_names = $this->get_tag_names();

		$this->start_controls_section(
			'organizer_section_content',
			array(
				'label' => __( 'Organizer', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$tribe_events_pro = class_exists( 'Tribe__Events__Pro__Main' );
		$version = ( $tribe_events_pro ? '_pro' : '' );
		$options = array(
			'no' => array(
				'title' => __( 'None', 'cmsmasters-elementor' ),
			),
			'custom' => array(
				'title' => __( 'Custom', 'cmsmasters-elementor' ),
				'description' => __( 'Custom URL', 'cmsmasters-elementor' ),
			),
		);

		if ( $tribe_events_pro ) {
			$index = array_search( 'no', array_keys( $options ), true );

			$options = array_merge(
				array_slice( $options, 0, $index + 1, true ),
				array(
					'yes' => array(
						'title' => __( 'Organizer', 'cmsmasters-elementor' ),
						'description' => __( 'Open Organizer', 'cmsmasters-elementor' ),
					),
				),
				array_slice( $options, $index + 1, count( $options ) - $index, true )
			);
		}

		$this->add_control(
			"organizer_title_link_switcher{$version}",
			array(
				'label' => __( 'Title Link To', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => $options,
				'default' => 'no',
				'condition' => array( 'organizer_data_sequence' => 'organizer-title' ),
			)
		);

		$this->add_control(
			"organizer_title_custom_link{$version}",
			array(
				'label' => __( 'Custom Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'show_label' => false,
				'condition' => array(
					'organizer_data_sequence' => 'organizer-title',
					"organizer_title_link_switcher{$version}" => 'custom',
				),
			)
		);

		$this->add_control(
			'organizer_email',
			array(
				'label' => __( 'Organizer Email', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['organizer-email'] ),
				),
			)
		);

		$this->add_control(
			'organizer_phone',
			array(
				'label' => __( 'Organizer Phone', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['organizer-phone'] ),
				),
			)
		);

		$this->add_control(
			'organizer_title',
			array(
				'label' => __( 'Organizer Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['organizer-title'] ),
				),
			)
		);

		$this->add_control(
			'organizer_url',
			array(
				'label' => __( 'Organizer Url', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['organizer-url'] ),
				),
			)
		);

		$this->add_control(
			'organizer_website',
			array(
				'label' => __( 'Organizer Website', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['organizer-website'] ),
				),
			)
		);

		$this->add_control(
			'organizer_data_sequence',
			array(
				'label' => __( 'Organizer Data Sequence', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::SELECTIZE,
				'label_block' => true,
				'options' => $this->get_organizer_options(),
				'default' => array(
					'organizer-title',
					'organizer-phone',
					'organizer-website',
				),
				'multiple' => true,
			)
		);

		$this->add_control(
			'organizer_phone_view',
			array(
				'label' => __( 'Phone', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
					),
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'text',
				'render_type' => 'template',
				'separator' => 'before',
				'condition' => array( 'organizer_data_sequence' => 'organizer-phone' ),
			)
		);

		$this->add_control(
			'organizer_phone_icon',
			array(
				'label' => __( 'Phone Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-phone',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'organizer_data_sequence' => 'organizer-phone',
					'organizer_phone_view' => 'icon',
				),
			)
		);

		$this->add_control(
			'organizer_website_view',
			array(
				'label' => __( 'Website', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
					),
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'text',
				'render_type' => 'template',
				'separator' => 'before',
				'condition' => array( 'organizer_data_sequence' => 'organizer-website' ),
			)
		);

		$this->add_control(
			'organizer_website_icon',
			array(
				'label' => __( 'Website Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-link',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'organizer_data_sequence' => 'organizer-website',
					'organizer_website_view' => 'icon',
				),
			)
		);

		$this->add_control(
			'organizer_email_view',
			array(
				'label' => __( 'Email', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
					),
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'text',
				'render_type' => 'template',
				'separator' => 'before',
				'condition' => array( 'organizer_data_sequence' => 'organizer-email' ),
			)
		);

		$this->add_control(
			'organizer_email_icon',
			array(
				'label' => __( 'Email Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-envelope',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'organizer_data_sequence' => 'organizer-email',
					'organizer_email_view' => 'icon',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_general_controls_style() {
		$this->start_controls_section(
			'general_section_styles',
			array(
				'label' => esc_html__( 'General', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array( 'organizer_data_sequence!' => '' ),
			)
		);

		$this->add_responsive_control(
			'general_align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'flex-end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
					'space-between' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--organizer-text-align: {{VALUE}};',
				),
				'condition' => array( 'organizer_data_sequence!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'general_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--organizer-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--organizer-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--organizer-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--organizer-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--organizer-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--organizer-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--organizer-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--organizer-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--organizer-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'organizer_data_sequence!' => '' ),
			)
		);

		$this->add_control(
			'general_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--organizer-color: {{VALUE}};',
				),
				'condition' => array( 'organizer_data_sequence!' => '' ),
			)
		);

		$this->add_control(
			'general_link_color',
			array(
				'label' => __( 'Link Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--organizer-link-color: {{VALUE}};',
				),
				'condition' => array( 'organizer_data_sequence!' => '' ),
			)
		);

		$this->add_control(
			'general_link_hover_color',
			array(
				'label' => __( 'Link Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--organizer-link-hover-color: {{VALUE}};',
				),
				'condition' => array( 'organizer_data_sequence!' => '' ),
			)
		);

		$this->add_responsive_control(
			'general_column_gap',
			array(
				'label' => __( 'Horizontal Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--organizer-column-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'organizer_data_sequence!' => '' ),
			)
		);

		$this->add_responsive_control(
			'general_row_gap',
			array(
				'label' => __( 'Vertical Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--organizer-row-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'organizer_data_sequence!' => '' ),
			)
		);

		$this->add_responsive_control(
			'general_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--organizer-icon-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'organizer_data_sequence!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'general_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--organizer-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'organizer_data_sequence!' => '' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_customize_controls_style() {
		$this->start_controls_section(
			'section_customize_style',
			array(
				'label' => esc_html__( 'Customize', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'organizer_data_sequence!' => '' ),
			)
		);

		$this->add_control(
			'section_cart_show_customize_elements',
			array(
				'label' => esc_html__( 'Select element of the organizer to customize:', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $this->get_organizer_options(),
				'render_type' => 'ui',
				'label_block' => true,
				'condition' => array( 'organizer_data_sequence!' => '' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_customize_data_controls_style() {
		foreach ( $this->get_organizer_options() as $data => $value ) {
			$condition = array(
				'organizer_data_sequence' => $data,
				'section_cart_show_customize_elements' => $data,
			);

			$condition_text = ( 'organizer-title' === $data ? $condition : array_merge(
				$condition,
				array( str_replace( '-', '_', $data ) . '_view' => 'text' ),
			) );

			$condition_icon = ( 'organizer-title' === $data ? $condition : array_merge(
				$condition,
				array( str_replace( '-', '_', $data ) . '_view' => 'icon' ),
			) );

			$this->start_controls_section(
				"customize_{$data}_section_style",
				array(
					'label' => sprintf( esc_html__( 'Customize: %s', 'cmsmasters-elementor' ), $value ),
					'tab' => Controls_Manager::TAB_STYLE,
					'condition' => $condition,
				)
			);

			$this->add_control(
				"customize_{$data}section_heading",
				array(
					'type' => Controls_Manager::HEADING,
					'label' => esc_html__( 'Section', 'cmsmasters-elementor' ),
					'condition' => $condition,
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => "customize_{$data}_typography",
					'fields_options' => array(
						'font_family' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--organizer-font-family: {{VALUE}};',
							),
						),
						'font_size' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--organizer-font-size: {{SIZE}}{{UNIT}};',
							),
						),
						'font_weight' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--organizer-font-weight: {{VALUE}};',
							),
						),
						'text_transform' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--organizer-text-transform: {{VALUE}};',
							),
						),
						'font_style' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--organizer-font-style: {{VALUE}};',
							),
						),
						'text_decoration' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--organizer-text-decoration: {{VALUE}}',
							),
						),
						'line_height' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--organizer-line-height: {{SIZE}}{{UNIT}};',
							),
						),
						'letter_spacing' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--organizer-letter-spacing: {{SIZE}}{{UNIT}};',
							),
						),
						'word_spacing' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--organizer-word-spacing: {{SIZE}}{{UNIT}}',
							),
						),
					),
					'selector' => '{{WRAPPER}} ' . $this->get_widget_selector() . "__{$data}",
					'condition' => $condition_text,
				)
			);

			if ( 'organizer-title' === $data ) {
				$this->add_control(
					"customize_{$data}_color",
					array(
						'label' => __( 'Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} ' . $this->get_widget_selector() . "__{$data}" => '--organizer-color: {{VALUE}};',
						),
						'condition' => $condition,
					)
				);
			}

			$this->add_control(
				"customize_{$data}_link_color",
				array(
					'label' => __( 'Link Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $this->get_widget_selector() . "__{$data}" => '--organizer-link-color: {{VALUE}};',
					),
					'condition' => $condition,
				)
			);

			$this->add_control(
				"customize_{$data}_link_hover_color",
				array(
					'label' => __( 'Link Hover Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $this->get_widget_selector() . "__{$data}" => '--organizer-link-hover-color: {{VALUE}};',
					),
					'condition' => $condition,
				)
			);

			if ( 'organizer-title' !== $data ) {
				$this->add_responsive_control(
					"customize_{$data}_icon_size",
					array(
						'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => array(
							'px',
							'em',
						),
						'range' => array(
							'px' => array(
								'min' => 0,
								'max' => 40,
							),
						),
						'selectors' => array(
							'{{WRAPPER}} ' . $this->get_widget_selector() . "__{$data}" => '--organizer-icon-size: {{SIZE}}{{UNIT}};',
						),
						'condition' => $condition_icon,
					)
				);
			}

			$this->add_responsive_control(
				"customize_{$data}_gap",
				array(
					'label' => __( 'Additional Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px' ),
					'allowed_dimensions' => 'horizontal',
					'placeholder' => array(
						'top' => 'auto',
						'right' => '',
						'bottom' => 'auto',
						'left' => '',
					),
					'selectors' => array(
						'{{WRAPPER}} ' . $this->get_widget_selector() . "__{$data}" => '--organizer-item-left-gap: {{LEFT}}{{UNIT}}; --organizer-item-right-gap: {{RIGHT}}{{UNIT}};',
					),
					'condition' => $condition,
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "customize_{$data}_text_shadow",
					'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
					'fields_options' => array(
						'text_shadow' => array(
							'selectors' => array(
								'{{WRAPPER}} ' . $this->get_widget_selector() . "__{$data}" => '--organizer-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
							),
						),
					),
					'selector' => '{{WRAPPER}}',
					'condition' => $condition,
				)
			);

			$this->end_controls_section();
		}
	}

	/**
	 * Render organizer data on the frontend.
	 *
	 * @since 1.13.0
	 * @since 1.14.4 Fixed organizer email prefix mailto.
	 */
	protected function get_organizer_data( $data ) {
		$settings = $this->get_settings_for_display();

		$value = ( isset( $settings[ str_replace( '-', '_', $data ) ] ) ? $settings[ str_replace( '-', '_', $data ) ] : '' );

		if ( ! empty( $value ) ) {
			echo '<div class="' . $this->get_widget_class() . '__' . esc_attr( $data ) . '">';

			if ( 'organizer-title' === $data ) {
				$tribe_events_pro = class_exists( 'Tribe__Events__Pro__Main' );
				$version = ( $tribe_events_pro ? '_pro' : '' );

				$organizer_title_link_switcher = ( isset( $settings[ "organizer_title_link_switcher{$version}" ] ) ? $settings[ "organizer_title_link_switcher{$version}" ] : 'no' );
				$organizer_url = ( isset( $settings['organizer_url'] ) ? $settings['organizer_url'] : '' );
				$organizer_title_custom_link = ( isset( $settings[ "organizer_title_custom_link{$version}" ] ) ? $settings[ "organizer_title_custom_link{$version}" ]['url'] : '' );

				if ( $tribe_events_pro && 'yes' === $organizer_title_link_switcher && '' !== $organizer_url ) {
					echo '<a href="' . esc_url( $organizer_url ) . '">';
				} elseif ( 'custom' === $organizer_title_link_switcher && '' !== $organizer_title_custom_link ) {
					echo '<a href="' . esc_url( $organizer_title_custom_link ) . '">';
				}

				echo esc_html( $value );

				if (
					( $tribe_events_pro && 'yes' === $organizer_title_link_switcher && '' !== $organizer_url ) ||
					( 'custom' === $organizer_title_link_switcher && '' !== $organizer_title_custom_link )
				) {
					echo '</a>';
				}
			} elseif ( 'organizer-phone' === $data || 'organizer-website' === $data || 'organizer-email' === $data ) {
				$settings_id = str_replace( '-', '_', $data );

				$prefix = '';

				if ( 'organizer-phone' === $data ) {
					$prefix = 'tel:';
				} elseif ( 'organizer-email' === $data ) {
					$prefix = 'mailto:';
				}

				$organizer_data = ( isset( $settings[ $settings_id ] ) ? $prefix . $settings[ $settings_id ] : '' );
				$organizer_data_view = ( isset( $settings[ "{$settings_id}_view" ] ) ? $settings[ "{$settings_id}_view" ] : '' );
				$organizer_label = '';

				if ( 'icon' === $organizer_data_view ) {
					$organizer_label = ' aria-label="' . esc_attr( ucwords( str_replace( '-', ' ', $data ) ) ) . '"';
				}

				echo '<a href="' . esc_url( $organizer_data ) . '"' . $organizer_label . '>';

				if ( 'text' === $organizer_data_view ) {
					echo esc_html( $value );
				}

				if ( 'icon' === $organizer_data_view ) {
					Icons_Manager::render_icon( $settings[ "{$settings_id}_icon" ], array( 'aria-hidden' => 'true' ) );
				}

				echo '</a>';
			} else {
				echo esc_html( $value );
			}

			echo '</div>';
		}
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.13.0
	 */
	protected function render() {
		$event_data = tribe_get_event();

		$organizer = $event_data->organizers[0];

		if ( ! get_post() || ! $organizer ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['organizer_data_sequence'] ) ) {
			echo '<div class="' . $this->get_widget_class() . '__wrap">';

			foreach ( $settings['organizer_data_sequence'] as $data ) {
				switch ( $data ) {
					case 'organizer-title':
						$this->get_organizer_data( $data );

						break;
					case 'organizer-phone':
						$this->get_organizer_data( $data );

						break;
					case 'organizer-website':
						$this->get_organizer_data( $data );

						break;
					case 'organizer-email':
						$this->get_organizer_data( $data );

						break;
				}
			}

			echo '</div>';
		}
	}
}
