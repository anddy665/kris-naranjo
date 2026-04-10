<?php
namespace CmsmastersElementor\Modules\TribeEvents\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Modules\TribeEvents\Traits\Tribe_Events_Singular_Widget;

use Elementor\Controls_Manager;
use Elementor\Plugin;

use Tribe__Events__Google__Maps_API_Key as GMaps;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Event venue map widget.
 *
 * Addon widget that displays venue map of current Event.
 *
 * @since 1.18.0
 */
class Event_Venue_Map extends Base_Widget {

	use Tribe_Events_Singular_Widget;

	protected $post_id;

	/**
	 * Get widget venue map.
	 *
	 * Retrieve widget venue map.
	 *
	 * @since 1.18.0
	 *
	 * @return string Widget venue map.
	 */
	public function get_title() {
		return __( 'Event Venue Map', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.18.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-event-venue-map';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.18.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'event',
			'singular',
			'venue',
			'map',
		);
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.18.0
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Show in panel.
	 *
	 * Whether to show the widget in the panel or not.
	 *
	 * @since 1.18.0
	 *
	 * @return bool Whether to show the widget in the panel or not.
	 */
	public function show_in_panel() {
		return false;
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.18.0
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'venue_map_section_content',
			array(
				'label' => esc_html__( 'Venue Map', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'venue_map_aspect_ratio',
			array(
				'label' => esc_html__( 'Aspect Ratio', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'169' => '16:9',
					'219' => '21:9',
					'43' => '4:3',
					'32' => '3:2',
					'11' => '1:1',
					'916' => '9:16',
					'custom' => 'Custom',
				),
				'selectors_dictionary' => array(
					'169' => '1.77777',
					'219' => '2.33333',
					'43' => '1.33333',
					'32' => '1.5',
					'11' => '1',
					'916' => '0.5625',
					'custom' => '2',
				),
				'default' => '169',
				'selectors' => array(
					'{{WRAPPER}}' => '--venue-map-aspect-ratio: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'venue_map_aspect_ratio_custom_height',
			array(
				'label' => esc_html__( 'Height (%)', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default' => array(
					'unit' => '%',
					'size' => 50,
				),
				'range' => array(
					'%' => array(
						'min' => 40,
						'max' => 150,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--venue-map-aspect-ratio: calc(100 / {{SIZE}}) !important;',
				),
				'condition' => array( 'venue_map_aspect_ratio' => 'custom' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render_pro_venue_map( $event_data ) {
		$venue_id = $event_data->ID;

		if ( ! empty( $venue_id ) ) {
			$venue = tribe_get_venue_object( $venue_id );
		}

		$url = '';

		$api_key = (string) tribe_get_option( GMaps::$api_key_option_name, false );

		if ( empty( $api_key ) ) {
			// If an API key has not been set yet, set it now.
			tribe_update_option( GMaps::$api_key_option_name, GMaps::$default_api_key );

			$api_key = GMaps::$default_api_key;
		}

		$map_provider = (object) array(
			'ID' => 'google_maps',
			'api_key' => $api_key,
			'is_premium' => ! tribe_is_using_basic_gmaps_api(),
			'javascript_url' => 'https://maps.googleapis.com/maps/api/js',
			'iframe_url' => 'https://www.google.com/maps/embed/v1/place',
			'map_pin_url' => trailingslashit( \Tribe__Events__Pro__Main::instance()->pluginUrl ) . 'src/resources/images/map-pin.svg',
			'zoom' => (int) tribe_get_option( 'embedGoogleMapsZoom', 10 ),
			'callback' => 'Function.prototype',
		);

		// Verifies if that event has a venue.
		if ( ! empty( $venue->geolocation->address ) ) {
			$url = add_query_arg(
				array(
					'key' => $map_provider->api_key,
					'q' => rawurlencode( $venue->geolocation->address ),
					'zoom' => (int) tribe_get_option( 'embedGoogleMapsZoom', 15 ),
				),
				$map_provider->iframe_url
			);
		}

		// Display the map based on the latitude and longitude if the values
		// are available and the `Use latitude + longitude` setting is enabled.
		if (
			get_post_meta( $venue->ID, '_VenueOverwriteCoords', true )
			&& ! empty( $venue->geolocation->latitude )
			&& ! empty( $venue->geolocation->longitude )
		) {
			$url = add_query_arg(
				array(
					'key' => $map_provider->api_key,
					'q' => rawurlencode( $venue->geolocation->latitude . ',' . $venue->geolocation->longitude ),
					'zoom' => (int) tribe_get_option( 'embedGoogleMapsZoom', 15 ),
				),
				$map_provider->iframe_url
			);
		}

		$venue = tribe_get_venue();

		echo '<iframe
			class="tribe-events-pro-venue__meta-data-google-maps-default"
			title="' . sprintf( esc_attr__( "Google maps iframe displaying the address to %s", 'cmsmasters-elementor' ), esc_attr( $venue ) ) . '"
			src="' . esc_url( $url ) . '"
			aria-label="' . esc_attr__( 'Venue location map', 'cmsmasters-elementor' ) . '"
		></iframe>';
	}

	protected function render_venue_map() {
		$map = tribe_get_embedded_map();

		if ( empty( $map ) ) {
			return;
		}

		do_action( 'tribe_events_single_meta_map_section_start' );

		echo $map; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'tribe_events_single_meta_map_section_end' );
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.18.0
	 */
	protected function render() {
		$event_data = tribe_get_event();

		if ( ! $event_data ) {
			return;
		}

		$tribe_events_pro = class_exists( 'Tribe__Events__Pro__Main' );
		$post_type = $event_data->post_type;

		if ( $tribe_events_pro && 'tribe_venue' === $post_type ) {
			$this->render_pro_venue_map( $event_data );
		} else {
			$this->render_venue_map();
		}
	}
}
