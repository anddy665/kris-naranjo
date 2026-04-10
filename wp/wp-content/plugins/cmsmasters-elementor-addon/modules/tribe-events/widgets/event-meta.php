<?php
namespace CmsmastersElementor\Modules\TribeEvents\Widgets;

use CmsmastersElementor\Modules\MetaData\Widgets\Meta_Data;
use CmsmastersElementor\Modules\TribeEvents\Module as TribeEventsModule;
use CmsmastersElementor\Modules\TribeEvents\Traits\Tribe_Events_Singular_Widget;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon Event Date widget.
 *
 * Addon widget that displays date of current event.
 *
 * @since 1.13.0
 */
class Event_Meta extends Meta_Data {

	use Tribe_Events_Singular_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.13.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Event Meta', 'cmsmasters-elementor' );
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
		return 'cmsicon-event-meta';
	}

	/**
	 * @since 1.13.0
	 */
	public function get_unique_keywords() {
		$keywords = array( 'featured' );

		if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
			$keywords += array( 'series' );
		}

		return array_merge( parent::get_unique_keywords(), $keywords );
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
	 * @since 1.13.0
	 */
	protected function get_default_meta_fields() {
		return array(
			array(
				'group' => 'standard',
				'group_type_standard' => 'date',
			),
			array(
				'group' => 'taxonomy',
				'group_type_taxonomy' => 'tribe_events_cat',
			),
		);
	}

	protected function register_controls_content() {
		parent::register_controls_content();

		$repeater = $this->get_controls( 'meta_fields' )['fields'];

		$icon_enable_index = array_search( 'icon_enable', array_keys( $repeater ) );

		if ( false !== $icon_enable_index ) {
			$new_repeater = array();

			foreach ( $repeater as $key => $value ) {
				$new_repeater[ $key ] = $value;

				if ( 'comments_text_disabled' === $key ) {
					$new_repeater['featured_text'] = array(
						'name' => 'featured_text',
						'label' => __( 'Label', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::TEXT,
						'placeholder' => __( 'Featured', 'cmsmasters-elementor' ),
						'default' => __( 'Featured', 'cmsmasters-elementor' ),
						'condition' => array(
							'group' => 'standard',
							'group_type_standard' => 'featured',
						),
					);

					if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
						$new_repeater['recurring_text'] = array(
							'name' => 'recurring_text',
							'label' => __( 'Label', 'cmsmasters-elementor' ),
							'type' => Controls_Manager::TEXT,
							'placeholder' => __( 'Recurring', 'cmsmasters-elementor' ),
							'default' => __( 'Recurring', 'cmsmasters-elementor' ),
							'condition' => array(
								'group' => 'standard',
								'group_type_standard' => 'recurring',
							),
						);
					}
				}
			}

			$repeater = $new_repeater;
		}

		$this->update_control(
			'meta_fields',
			array( 'fields' => $repeater )
		);
	}

	/**
	 * @since 1.13.0
	 */
	protected static function get_standard_options() {
		$options = array( 'featured' => __( 'Featured', 'cmsmasters-elementor' ) );

		if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
			$options += array( 'recurring' => __( 'Series', 'cmsmasters-elementor' ) );
		}

		return array_merge( parent::get_standard_options(), $options );
	}

	/**
	 * @since 1.13.0
	 */
	protected static function get_allowed_post_types() {
		return array( TribeEventsModule::$post_type );
	}

	/**
	 * Render elements of standard group.
	 *
	 * @since 1.13.0
	 */
	protected function render_standard() {
		parent::render_standard();

		switch ( $this->get_group_type() ) {
			case 'featured':
				$this->render_featured();

				break;
			case 'recurring':
				if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
					$this->render_recurring();
				}

				break;
		}
	}

	/**
	 * Get event data.
	 *
	 * @since 1.13.0
	 */
	protected function get_event_data( $data, $link = false ) {
		$event_data = tribe_get_event();

		if ( ! $event_data || ! $event_data->$data ) {
			return;
		}

		$data_link = '';

		if ( $link && isset( $event_data->$link ) ) {
			$data_link = ' href="' . esc_url( $event_data->$link ) . '"';
		}

		$tag = ( $link ? 'a' : 'div' );

		echo '<' . Utils::validate_html_tag( $tag ) . ' class="cmsmasters-postmeta"' . esc_attr( $data_link ) . '">' .
			'<div class="cmsmasters-postmeta__inner">';

		if ( $this->meta_field['icon_enable'] && ! empty( $this->meta_field['icon']['value'] ) ) {
			echo '<span class="cmsmasters-wrap-icon">';

				Icons_Manager::render_icon( $this->meta_field['icon'], array( 'aria-hidden' => 'true' ) );

			echo '</span>';
		}

		$data_text = ( isset( $this->meta_field[ "{$data}_text" ] ) ? $this->meta_field[ "{$data}_text" ] : '' );

		if ( ! empty( $data_text ) ) {
			echo '<span class="cmsmasters-postmeta__content">' .
				'<span rel="' . esc_attr( $data ) . '">' .
					'<span>' .
						esc_html( $data_text ) .
					'</span>' .
				'</span>' .
			'</span>';
		}

			echo '</div>' .
		'</' . Utils::validate_html_tag( $tag ) . '>';
	}

	/**
	 * Render recurring element.
	 *
	 * @since 1.13.0
	 */
	protected function render_recurring() {
		$this->get_event_data( 'recurring', 'permalink_all' );
	}

	/**
	 * Render featured element.
	 *
	 * @since 1.13.0
	 */
	protected function render_featured() {
		$this->get_event_data( 'featured' );
	}

	protected function render_meta_field( $meta_fields ) {
		$event_data = tribe_get_event();

		if ( 'featured' === parent::get_group_type() && ! $event_data->featured ) {
			return;
		}

		parent::render_meta_field( $meta_fields );
	}

	/**
	 * Render element.
	 *
	 * Generates the final HTML on the frontend.
	 *
	 * @since 1.13.0
	 */
	protected function render() {
		$meta_fields = parent::get_meta_fields();
		$event_data = tribe_get_event();

		if ( ! $meta_fields || ( 'featured' === parent::get_group_type() && ! $event_data->featured ) ) {
			return;
		}

		parent::render();
	}
}
