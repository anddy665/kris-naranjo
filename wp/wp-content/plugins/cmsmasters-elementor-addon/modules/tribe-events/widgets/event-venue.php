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


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Event Venue widget.
 *
 * Addon widget that displays venue of current Event.
 *
 * @since 1.13.0
 */
class Event_Venue extends Base_Widget {

	use Tribe_Events_Singular_Widget;

	/**
	 * Get widget venue.
	 *
	 * Retrieve widget venue.
	 *
	 * @since 1.13.0
	 *
	 * @return string Widget venue.
	 */
	public function get_title() {
		return __( 'Event Venue', 'cmsmasters-elementor' );
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
		return 'cmsicon-event-venue';
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
			'venue',
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
			'venue-address' => 'cmsmasters-tribe-events-venue-address',
			'venue-city' => 'cmsmasters-tribe-events-venue-city',
			'venue-country' => 'cmsmasters-tribe-events-venue-country',
			'venue-map-url' => 'cmsmasters-tribe-events-venue-map-url',
			'venue-phone' => 'cmsmasters-tribe-events-venue-phone',
			'venue-state-province' => 'cmsmasters-tribe-events-venue-state-province',
			'venue-title' => 'cmsmasters-tribe-events-venue-title',
			'venue-url' => 'cmsmasters-tribe-events-venue-url',
			'venue-website' => 'cmsmasters-tribe-events-venue-website',
			'venue-zip' => 'cmsmasters-tribe-events-venue-zip',
		);
	}

	protected function get_venue_options() {
		return array(
			'venue-title' => __( 'Title', 'cmsmasters-elementor' ),
			'venue-address' => __( 'Address', 'cmsmasters-elementor' ),
			'venue-city' => __( 'City', 'cmsmasters-elementor' ),
			'venue-state-province' => __( 'State Province', 'cmsmasters-elementor' ),
			'venue-zip' => __( 'Zip', 'cmsmasters-elementor' ),
			'venue-country' => __( 'Country', 'cmsmasters-elementor' ),
			'venue-map-url' => __( 'Google Map Icon', 'cmsmasters-elementor' ),
			'venue-phone' => __( 'Phone', 'cmsmasters-elementor' ),
			'venue-website' => __( 'Website', 'cmsmasters-elementor' ),
		);
	}

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-tribe-events-venue';
	}

	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	protected function register_controls() {
		$this->register_venue_controls_content();

		$this->register_general_controls_style();

		$this->register_customize_controls_style();

		$this->register_customize_data_controls_style();
	}

	protected function register_venue_controls_content() {
		$dynamic_tags = CmsmastersPlugin::elementor()->dynamic_tags;
		$tag_names = $this->get_tag_names();

		$this->start_controls_section(
			'venue_section_content',
			array(
				'label' => __( 'Venue', 'cmsmasters-elementor' ),
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
						'title' => __( 'Venue', 'cmsmasters-elementor' ),
						'description' => __( 'Open Venue', 'cmsmasters-elementor' ),
					),
				),
				array_slice( $options, $index + 1, count( $options ) - $index, true )
			);
		}

		$this->add_control(
			"venue_title_link_switcher{$version}",
			array(
				'label' => __( 'Title Link To', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => $options,
				'default' => 'no',
				'condition' => array( 'venue_data_sequence' => 'venue-title' ),
			)
		);

		$this->add_control(
			"venue_title_custom_link{$version}",
			array(
				'label' => __( 'Custom Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'show_label' => false,
				'condition' => array(
					'venue_data_sequence' => 'venue-title',
					"venue_title_link_switcher{$version}" => 'custom',
				),
			)
		);

		$this->add_control(
			'venue_address',
			array(
				'label' => __( 'Venue Address', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['venue-address'] ),
				),
			)
		);

		$this->add_control(
			'venue_city',
			array(
				'label' => __( 'Venue City', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['venue-city'] ),
				),
			)
		);

		$this->add_control(
			'venue_country',
			array(
				'label' => __( 'Venue Country', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['venue-country'] ),
				),
			)
		);

		$this->add_control(
			'venue_map_url',
			array(
				'label' => __( 'Venue Map Url', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['venue-map-url'] ),
				),
			)
		);

		$this->add_control(
			'venue_phone',
			array(
				'label' => __( 'Venue Phone', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['venue-phone'] ),
				),
			)
		);

		$this->add_control(
			'venue_state_province',
			array(
				'label' => __( 'Venue State Province', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['venue-state-province'] ),
				),
			)
		);

		$this->add_control(
			'venue_title',
			array(
				'label' => __( 'Venue Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['venue-title'] ),
				),
			)
		);

		$this->add_control(
			'venue_url',
			array(
				'label' => __( 'Venue Url', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['venue-url'] ),
				),
			)
		);

		$this->add_control(
			'venue_website',
			array(
				'label' => __( 'Venue Website', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['venue-website'] ),
				),
			)
		);

		$this->add_control(
			'venue_zip',
			array(
				'label' => __( 'Venue Zip', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['venue-zip'] ),
				),
			)
		);

		$this->add_control(
			'venue_data_sequence',
			array(
				'label' => __( 'Venue Data Sequence', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::SELECTIZE,
				'label_block' => true,
				'options' => $this->get_venue_options(),
				'default' => array(
					'venue-title',
					'venue-map-url',
					'venue-phone',
					'venue-website',
				),
				'multiple' => true,
			)
		);

		$this->add_control(
			'venue_phone_view',
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
				'condition' => array( 'venue_data_sequence' => 'venue-phone' ),
			)
		);

		$this->add_control(
			'venue_phone_icon',
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
					'venue_data_sequence' => 'venue-phone',
					'venue_phone_view' => 'icon',
				),
			)
		);

		$this->add_control(
			'venue_website_view',
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
				'condition' => array( 'venue_data_sequence' => 'venue-website' ),
			)
		);

		$this->add_control(
			'venue_website_icon',
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
					'venue_data_sequence' => 'venue-website',
					'venue_website_view' => 'icon',
				),
			)
		);

		$this->add_control(
			'venue_map_icon',
			array(
				'label' => __( 'Google Map Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-map-marker-alt',
					'library' => 'fa-solid',
				),
				'separator' => 'before',
				'condition' => array( 'venue_data_sequence' => 'venue-map-url' ),
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
				'condition' => array( 'venue_data_sequence!' => '' ),
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
					'{{WRAPPER}}' => '--venue-text-align: {{VALUE}};',
				),
				'condition' => array( 'venue_data_sequence!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'general_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--venue-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--venue-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--venue-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--venue-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--venue-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--venue-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--venue-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--venue-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--venue-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'venue_data_sequence!' => '' ),
			)
		);

		$this->add_control(
			'general_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--venue-color: {{VALUE}};',
				),
				'condition' => array( 'venue_data_sequence!' => '' ),
			)
		);

		$this->add_control(
			'general_link_color',
			array(
				'label' => __( 'Link Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--venue-link-color: {{VALUE}};',
				),
				'condition' => array( 'venue_data_sequence!' => '' ),
			)
		);

		$this->add_control(
			'general_link_hover_color',
			array(
				'label' => __( 'Link Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--venue-link-hover-color: {{VALUE}};',
				),
				'condition' => array( 'venue_data_sequence!' => '' ),
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
					'{{WRAPPER}}' => '--venue-column-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'venue_data_sequence!' => '' ),
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
					'{{WRAPPER}}' => '--venue-row-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'venue_data_sequence!' => '' ),
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
					'{{WRAPPER}}' => '--venue-icon-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'venue_data_sequence!' => '' ),
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
							'{{SELECTOR}}' => '--venue-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => array( 'venue_data_sequence!' => '' ),
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
				'condition' => array( 'venue_data_sequence!' => '' ),
			)
		);

		$this->add_control(
			'section_cart_show_customize_elements',
			array(
				'label' => esc_html__( 'Select element of the venue to customize:', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $this->get_venue_options(),
				'render_type' => 'ui',
				'label_block' => true,
				'condition' => array( 'venue_data_sequence!' => '' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_customize_data_controls_style() {
		foreach ( $this->get_venue_options() as $data => $value ) {
			$condition = array(
				'venue_data_sequence' => $data,
				'section_cart_show_customize_elements' => $data,
			);

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

			if ( 'venue-map-url' !== $data ) {
				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name' => "customize_{$data}_typography",
						'fields_options' => array(
							'font_family' => array(
								'selectors' => array(
									'{{SELECTOR}}' => '--venue-font-family: {{VALUE}};',
								),
							),
							'font_size' => array(
								'selectors' => array(
									'{{SELECTOR}}' => '--venue-font-size: {{SIZE}}{{UNIT}};',
								),
							),
							'font_weight' => array(
								'selectors' => array(
									'{{SELECTOR}}' => '--venue-font-weight: {{VALUE}};',
								),
							),
							'text_transform' => array(
								'selectors' => array(
									'{{SELECTOR}}' => '--venue-text-transform: {{VALUE}};',
								),
							),
							'font_style' => array(
								'selectors' => array(
									'{{SELECTOR}}' => '--venue-font-style: {{VALUE}};',
								),
							),
							'text_decoration' => array(
								'selectors' => array(
									'{{SELECTOR}}' => '--venue-text-decoration: {{VALUE}}',
								),
							),
							'line_height' => array(
								'selectors' => array(
									'{{SELECTOR}}' => '--venue-line-height: {{SIZE}}{{UNIT}};',
								),
							),
							'letter_spacing' => array(
								'selectors' => array(
									'{{SELECTOR}}' => '--venue-letter-spacing: {{SIZE}}{{UNIT}};',
								),
							),
							'word_spacing' => array(
								'selectors' => array(
									'{{SELECTOR}}' => '--venue-word-spacing: {{SIZE}}{{UNIT}}',
								),
							),
						),
						'selector' => '{{WRAPPER}} ' . $this->get_widget_selector() . "__{$data}",
						'condition' => $condition,
					)
				);
			}

			if ( 'venue-map-url' !== $data && 'venue-phone' !== $data && 'venue-website' !== $data ) {
				$this->add_control(
					"customize_{$data}_color",
					array(
						'label' => __( 'Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} ' . $this->get_widget_selector() . "__{$data}" => '--venue-color: {{VALUE}};',
						),
						'condition' => $condition,
					)
				);
			}

			if ( 'venue-title' === $data || 'venue-map-url' === $data || 'venue-phone' === $data || 'venue-website' === $data ) {
				$this->add_control(
					"customize_{$data}_link_color",
					array(
						'label' => __( 'Link Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							'{{WRAPPER}} ' . $this->get_widget_selector() . "__{$data}" => '--venue-link-color: {{VALUE}};',
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
							'{{WRAPPER}} ' . $this->get_widget_selector() . "__{$data}" => '--venue-link-hover-color: {{VALUE}};',
						),
						'condition' => $condition,
					)
				);
			}

			if ( 'venue-map-url' === $data || 'venue-website' === $data ) {
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
							'{{WRAPPER}} ' . $this->get_widget_selector() . "__{$data}" => '--venue-icon-size: {{SIZE}}{{UNIT}};',
						),
						'condition' => $condition,
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
						'{{WRAPPER}} ' . $this->get_widget_selector() . "__{$data}" => '--venue-item-left-gap: {{LEFT}}{{UNIT}}; --venue-item-right-gap: {{RIGHT}}{{UNIT}};',
					),
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
								'{{WRAPPER}} ' . $this->get_widget_selector() . "__{$data}" => '--venue-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
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
	 * Render venue data on the frontend.
	 *
	 * @since 1.13.0
	 */
	protected function get_venue_data( $data ) {
		$settings = $this->get_settings_for_display();

		$value = ( isset( $settings[ str_replace( '-', '_', $data ) ] ) ? $settings[ str_replace( '-', '_', $data ) ] : '' );

		if ( ! empty( $value ) ) {
			echo '<div class="' . $this->get_widget_class() . '__' . esc_attr( $data ) . '">';

			$venue_data_label = '';

			if ( 'venue-title' === $data ) {
				$tribe_events_pro = class_exists( 'Tribe__Events__Pro__Main' );
				$version = ( $tribe_events_pro ? '_pro' : '' );

				$venue_title_link_switcher = ( isset( $settings[ "venue_title_link_switcher{$version}" ] ) ? $settings[ "venue_title_link_switcher{$version}" ] : 'no' );
				$venue_url = ( isset( $settings['venue_url'] ) ? $settings['venue_url'] : '' );
				$venue_title_custom_link = ( isset( $settings[ "venue_title_custom_link{$version}" ] ) ? $settings[ "venue_title_custom_link{$version}" ]['url'] : '' );

				if ( $tribe_events_pro && 'yes' === $venue_title_link_switcher && '' !== $venue_url ) {
					echo '<a href="' . esc_url( $venue_url ) . '">';
				} elseif ( 'custom' === $venue_title_link_switcher && '' !== $venue_title_custom_link ) {
					echo '<a href="' . esc_url( $venue_title_custom_link ) . '">';
				}

				echo esc_html( $value );

				if (
					( $tribe_events_pro && 'yes' === $venue_title_link_switcher && '' !== $venue_url ) ||
					( 'custom' === $venue_title_link_switcher && '' !== $venue_title_custom_link )
				) {
					echo '</a>';
				}
			} elseif ( 'venue-website' === $data ) {
				$venue_website = ( isset( $settings['venue_website'] ) ? $settings['venue_website'] : '' );
				$venue_website_view = ( isset( $settings['venue_website_view'] ) ? $settings['venue_website_view'] : '' );

				if ( 'icon' === $venue_website_view ) {
					$venue_data_label = ' aria-label="' . esc_attr( ucwords( str_replace( '-', ' ', $data ) ) ) . '"';
				}

				echo '<a href="' . esc_url( $venue_website ) . '"' . $venue_data_label . '>';

				if ( 'text' === $venue_website_view ) {
					echo esc_html( $value );
				}

				if ( 'icon' === $venue_website_view ) {
					Icons_Manager::render_icon( $settings['venue_website_icon'], array( 'aria-hidden' => 'true' ) );
				}

				echo '</a>';
			} elseif ( 'venue-map-url' === $data ) {
				$venue_map_url = ( isset( $settings['venue_map_url'] ) ? $settings['venue_map_url'] : '' );

				$venue_data_label = ' aria-label="' . esc_attr( ucwords( str_replace( '-', ' ', $data ) ) ) . '"';

				echo '<a href="' . esc_url( $venue_map_url ) . '"' . $venue_data_label . '>';

					Icons_Manager::render_icon( $settings['venue_map_icon'], array( 'aria-hidden' => 'true' ) );

				echo '</a>';
			} elseif ( 'venue-phone' === $data ) {
				$venue_phone = ( isset( $settings['venue_phone'] ) ? $settings['venue_phone'] : '' );
				$venue_phone_view = ( isset( $settings['venue_phone_view'] ) ? $settings['venue_phone_view'] : '' );

				if ( 'icon' === $venue_phone_view ) {
					$venue_data_label = ' aria-label="' . esc_attr( ucwords( str_replace( '-', ' ', $data ) ) ) . '"';
				}

				echo '<a href="tel:' . esc_attr( $venue_phone ) . '"' . $venue_data_label . '>';

				if ( 'text' === $venue_phone_view ) {
					echo esc_html( $value );
				}

				if ( 'icon' === $venue_phone_view ) {
					Icons_Manager::render_icon( $settings['venue_phone_icon'], array( 'aria-hidden' => 'true' ) );
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

		$venue = $event_data->venues[0];

		if ( ! get_post() || ! $venue ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['venue_data_sequence'] ) ) {
			echo '<div class="' . $this->get_widget_class() . '__wrap">';

			foreach ( $settings['venue_data_sequence'] as $data ) {
				switch ( $data ) {
					case 'venue-title':
						$this->get_venue_data( $data );

						break;
					case 'venue-address':
						$this->get_venue_data( $data );

						break;
					case 'venue-city':
						$this->get_venue_data( $data );

						break;
					case 'venue-state-province':
						$this->get_venue_data( $data );

						break;
					case 'venue-zip':
						$this->get_venue_data( $data );

						break;
					case 'venue-country':
						$this->get_venue_data( $data );

						break;
					case 'venue-map-url':
						$this->get_venue_data( $data );

						break;
					case 'venue-phone':
						$this->get_venue_data( $data );

						break;
					case 'venue-website':
						$this->get_venue_data( $data );

						break;
				}
			}

			echo '</div>';
		}
	}
}
