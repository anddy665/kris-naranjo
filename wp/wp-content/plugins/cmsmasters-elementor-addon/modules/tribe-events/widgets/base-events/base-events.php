<?php
namespace CmsmastersElementor\Modules\TribeEvents\Widgets\Base_Events;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TribeEvents\Module as TribeEventsModule;
use CmsmastersElementor\Modules\Wordpress\Module as WordpressModule;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon tribe events widget class.
 *
 * An abstract class to register new Tribe Events widgets.
 *
 * @since 1.13.0
 */
abstract class Base_Events extends Base_Widget {

	const QUERY_CONTROL_PREFIX = 'tribe-events';

	/**
	 * Query variables for setting up the WordPress query loop.
	 *
	 * @var array
	 */
	private $query_vars = array();

	/**
	 * The WordPress query instance.
	 *
	* @var \WP_Query
	*/
	private $query;

	/**
	 * Get group name.
	 *
	 * @since 1.13.0
	 *
	 * @return string Group name.
	 */
	public function get_group_name() {
		return 'cmsmasters-tribe-events';
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
			'events',
			'query',
			'loop',
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.16.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		$style_depends = array(
			'widget-cmsmasters-tribe-events',
		);

		if ( Icons_Manager::is_migration_allowed() ) {
			$style_depends = array_merge( $style_depends, array(
				'elementor-icons-fa-solid',
				'elementor-icons-fa-brands',
				'elementor-icons-fa-regular',
			) );
		}

		return $style_depends;
	}

	/**
	 * @since 1.13.0
	 */
	public function register_controls() {
		$this->register_event_query_content_controls();

		$this->register_event_advanced_content_section_controls();

		$this->register_event_advanced_style_section_controls();
	}

	/**
	 * Register query controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.13.0
	 * @since 1.14.4 Added `Manual Selection` in Query Source.
	 * @since 1.16.4 Added `Event Start Date` and `Event End Date` query option for `Order By` control.
	 */
	protected function register_event_query_content_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_group_control(
			CmsmastersControls::QUERY_GROUP,
			array(
				'name' => self::QUERY_CONTROL_PREFIX,
				'post_type' => TribeEventsModule::$post_type,
				'presets' => array(
					'include',
					'exclude',
					'order',
				),
				'fields_options' => array(
					'post_type' => array(
						'default' => TribeEventsModule::$post_type,
						'options' => array(
							TribeEventsModule::$post_type => __( 'Events', 'cmsmasters-elementor' ),
							'tribe_venue' => __( 'Venues', 'cmsmasters-elementor' ),
							'tribe_organizer' => __( 'Organizers', 'cmsmasters-elementor' ),
							'current_query' => __( 'Current Query', 'cmsmasters-elementor' ),
							'manual_selection' => __( 'Manual Selection', 'cmsmasters-elementor' ),
						),
					),
					'orderby' => array(
						'default' => 'date',
						'options' => array(
							'date' => __( 'Date', 'cmsmasters-elementor' ),
							'title' => __( 'Title', 'cmsmasters-elementor' ),
							'popularity' => __( 'Popularity', 'cmsmasters-elementor' ),
							'rating' => __( 'Rating', 'cmsmasters-elementor' ),
							'reviews_count' => __( 'Reviews Number', 'cmsmasters-elementor' ),
							'rand' => __( 'Random', 'cmsmasters-elementor' ),
							'menu_order' => __( 'Menu Order', 'cmsmasters-elementor' ),
							'event_start_date' => __( 'Event Start Date', 'cmsmasters-elementor' ),
							'event_end_date' => __( 'Event End Date', 'cmsmasters-elementor' ),
						),
					),
				),
				'exclude' => array(
					'posts_per_page',
					'author_query',
					'selected_authors',
					'ignore_sticky_posts',
					'prevent_duplicates',
					'offset',
					'filter_id',
					'current_author',
					'related_fallback',
					'fallback_posts_in',
					'posts_not_in',
				),
			)
		);

		$this->update_control(
			self::QUERY_CONTROL_PREFIX . '_query_args',
			array(
				'condition' => array( self::QUERY_CONTROL_PREFIX . '_post_type' => TribeEventsModule::$post_type ),
			)
		);

		$this->update_control(
			self::QUERY_CONTROL_PREFIX . '_include_term_ids',
			array(
				'condition' => array( self::QUERY_CONTROL_PREFIX . '_post_type' => TribeEventsModule::$post_type ),
			)
		);

		$this->update_control(
			self::QUERY_CONTROL_PREFIX . '_exclude_term_ids',
			array(
				'condition' => array( self::QUERY_CONTROL_PREFIX . '_post_type' => TribeEventsModule::$post_type ),
			)
		);

		// Manual events selection control
		$this->update_control(
			self::QUERY_CONTROL_PREFIX . '_posts_in',
			array(
				'autocomplete' => array(
					'object' => Query_Manager::POST_OBJECT,
					'query' => array( 'post_type' => TribeEventsModule::$post_type ),
					'display' => 'detailed',
				),
				'condition' => array( self::QUERY_CONTROL_PREFIX . '_post_type' => 'manual_selection' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.13.0
	 */
	protected function register_event_advanced_content_section_controls() {
		$this->start_controls_section(
			'section_advanced',
			array(
				'label' => __( 'Advanced', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => array( self::QUERY_CONTROL_PREFIX . '_post_type' => 'current_query' ),
			)
		);

		$this->add_control(
			'nothing_found_message',
			array(
				'label' => __( 'Nothing Found Message', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'It seems we can\'t find what you\'re looking for.', 'cmsmasters-elementor' ),
				'condition' => array( self::QUERY_CONTROL_PREFIX . '_post_type' => 'current_query' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.13.0
	 */
	protected function register_event_advanced_style_section_controls() {
		$condition = array(
			'nothing_found_message!' => '',
			self::QUERY_CONTROL_PREFIX . '_post_type' => 'current_query',
		);

		$this->start_controls_section(
			'section_nothing_found_style',
			array(
				'tab' => Controls_Manager::TAB_STYLE,
				'label' => __( 'Nothing Found Message', 'cmsmasters-elementor' ),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'nothing_found_text_shadow_text_align',
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
				'selectors' => array(
					'{{WRAPPER}}' => '--event-nothing-found-text-align: {{VALUE}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'nothing_found_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-nothing-found-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-nothing-found-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-nothing-found-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-nothing-found-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-nothing-found-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-nothing-found-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-nothing-found-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-nothing-found-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-nothing-found-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'nothing_found_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--event-nothing-found-color: {{VALUE}};',
				),
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'nothing_found_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--event-nothing-found-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
				'condition' => $condition,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get Custom Border Type Options
	 *
	 * Return a set of border options to be used in different Tribe Events widgets.
	 *
	 * This will be used in cases where the Group Border Control could not be used.
	 *
	 * @since 1.13.0
	 *
	 * @return array
	 */
	public static function get_custom_border_type_options() {
		return array(
			'' => esc_html__( 'Default', 'cmsmasters-elementor' ),
			'none' => esc_html__( 'None', 'cmsmasters-elementor' ),
			'solid' => esc_html__( 'Solid', 'cmsmasters-elementor' ),
			'double' => esc_html__( 'Double', 'cmsmasters-elementor' ),
			'dotted' => esc_html__( 'Dotted', 'cmsmasters-elementor' ),
			'dashed' => esc_html__( 'Dashed', 'cmsmasters-elementor' ),
			'groove' => esc_html__( 'Groove', 'cmsmasters-elementor' ),
		);
	}

	/**
	 * Prepare the WordPress Query.
	 *
	 * @since 1.13.0
	 */
	public function init_query() {
		/** @var WordpressModule $wordpress_module */
		$wordpress_module = WordpressModule::instance();

		$this->query = $wordpress_module->get_query_manager()->get_query(
			$this,
			static::QUERY_CONTROL_PREFIX, /* 'events' */
			$this->get_query_vars()
		);
	}

	/**
	 * Get query variables for setting up the WordPress query loop.
	 *
	 * @since 1.13.0
	 * @since 1.16.4 Fixed render past events.
	 *
	 * @return array
	 */
	public function get_query_vars() {
		$events_per_page = $this->get_events_per_page();
		$query_vars = array();

		if ( $events_per_page ) {
			$query_vars[ static::QUERY_CONTROL_PREFIX . '_posts_per_page' ] = $events_per_page;
		}

		$query_vars['meta_query'] = array(
			array(
				'key' => '_EventEndDate',
				'value' => current_time( 'Y-m-d H:i:s' ),
				'compare' => '>=',
				'type' => 'DATETIME',
			),
		);

		$orderby = $this->get_settings_for_display( self::QUERY_CONTROL_PREFIX . '_orderby' );

		if ( 'event_start_date' === $orderby ) {
			$query_vars['orderby'] = 'meta_value';
			$query_vars['meta_key'] = '_EventStartDate';
		}

		if ( 'event_end_date' === $orderby ) {
			$query_vars['orderby'] = 'meta_value';
			$query_vars['meta_key'] = '_EventEndDate';
		}

		return array_merge( $this->query_vars, $query_vars );
	}

	/**
	 * Check if current query is archive.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_current_query() {
		return 'current_query' === $this->get_settings_for_display( self::QUERY_CONTROL_PREFIX . '_post_type' );
	}

	/**
	 * Get number of events per page.
	 *
	 * @since 1.13.0
	 *
	 * @return int
	 */
	protected function get_events_per_page() {
		return (int) $this->get_settings_for_display( 'posts_per_page' );
	}

	/**
	 * Get tribe events css classes.
	 *
	 * @since 1.13.0
	 *
	 * @return string[]
	 */
	protected function get_wrap_classes() {
		return array( 'cmsmasters-tribe-events', $this->get_name() );
	}

	/**
	 * Render tribe events.
	 *
	 * @since 1.13.0
	 */
	abstract protected function render_wrapper();

	/**
	 * Get the WordPress query.
	 *
	 * @since 1.13.0
	 *
	 * @return \WP_Query
	 */
	public function get_query() {
		return $this->query;
	}

	/**
	 * Start event rendering.
	 *
	 * @since 1.13.0
	 */
	public function render_event_open() {
		$this->add_render_attribute(
			'event',
			array(
				'id' => 'event-' . get_the_ID(),
				'class' => get_post_class( 'cmsmasters-tribe-events__event' ),
			),
			null,
			true
		);

		echo '<article ' . $this->get_render_attribute_string( 'event' ) . '>' .
			'<div class="cmsmasters-tribe-events__event-cont-wrap">';
	}

	/**
	 * Render all event insides.
	 *
	 * @since 1.13.0
	 */
	abstract protected function render_event_inner();

	/**
	 * End event rendering.
	 *
	 * @since 1.13.0
	 */
	public function render_event_close() {
			echo '</div>' .
		'</article>';
	}

	/**
	 * Render the event.
	 *
	 * @since 1.13.0
	 */
	protected function render_event() {
		$this->render_event_open();

		$this->render_event_inner();

		$this->render_event_close();
	}

	/**
	 * Wrapper insides for events.
	 *
	 * @since 1.13.0
	 * @since 1.16.0 Fixed recurrence events for The Events Calendar Pro.
	 */
	protected function render_events_inner() {
		$query = $this->get_query();
		$excluded_events = array();

		if ( class_exists( 'Tribe__Events__Pro__Main', false ) ) {
			$event_recurrences = tribe_get_option( 'hideSubsequentRecurrencesDefault', false );

			if ( $event_recurrences ) {
				$all_events_query = new \WP_Query( array(
					'post_type' => 'tribe_events',
					'posts_per_page' => -1,
				) );
				$displayed_series_ids = array();

				while ( $all_events_query->have_posts() ) {
					$all_events_query->the_post();
					$event_id = get_the_ID();

					if ( tribe_is_recurring_event( $event_id ) ) {
						$series_id = get_post_meta( $event_id, '_EventRecurrence', true );

						if ( in_array( $series_id, $displayed_series_ids, true ) ) {
							$excluded_events[] = $event_id;
						} else {
							$displayed_series_ids[] = $series_id;
						}
					}
				}
			}
		}

		if ( ! empty( $excluded_events ) ) {
			$event_recurrences = tribe_get_option( 'hideSubsequentRecurrencesDefault', false );

			if ( $event_recurrences ) {
				$query->set( 'post__not_in', $excluded_events );
				$query->get_posts();
			}
		}

		while ( $query->have_posts() ) {
			$query->the_post();

			$this->render_event();
		}
	}

	/**
	 * Wrapper for events.
	 *
	 * @since 1.13.0
	 */
	protected function render_events() {
		echo '<div class="cmsmasters-tribe-events__events-wrap">' .
			'<div class="cmsmasters-tribe-events__events">';

				$this->render_events_inner();

			echo '</div>' .
		'</div>';
	}

	/**
	 * Sets up custom WordPress query.
	 *
	 * @param array $query_vars
	 *
	 * @since 1.13.0
	 */
	protected function set_query_vars( array $query_vars = array() ) {
		if ( empty( $query_vars ) ) {
			return;
		}

		$this->query_vars = array_merge( $query_vars, $this->query_vars );
	}

	/**
	 * @since 1.13.0
	 */
	protected function render() {
		$this->init_query();

		$wp_query = $this->get_query();

		if ( ! $wp_query->found_posts ) {
			if ( $this->is_current_query() ) {
				echo '<h4 class="cmsmasters-tribe-events__nothing-found">' .
					esc_html( $this->get_settings_fallback( 'nothing_found_message' ) ) .
				'</h4>';
			} else {
				CmsmastersUtils::render_alert( esc_html__( 'Events not found!', 'cmsmasters-elementor' ) );
			}

			return;
		}

		$this->add_render_attribute( 'tribe-events', 'class', $this->get_wrap_classes() );

		echo '<div ' . $this->get_render_attribute_string( 'tribe-events' ) . '>';

			$this->render_wrapper();

		echo '</div>';

		wp_reset_postdata();
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.13.0
	 */
	public function render_plain_content() {}
}
