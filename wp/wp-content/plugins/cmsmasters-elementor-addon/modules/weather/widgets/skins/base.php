<?php
namespace CmsmastersElementor\Modules\Weather\Widgets\Skins;

use CmsmastersElementor\Modules\AjaxWidget\Module as AjaxWidgetModule;
use CmsmastersElementor\Modules\Weather\Module as WeatherModule;
use CmsmastersElementor\Modules\Weather\Widgets\Weather;
use CmsmastersElementor\Utils;

use Elementor\Skin_Base as ElementorSkinBase;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Base extends ElementorSkinBase {
	/**
	 * Parent widget.
	 *
	 * Holds the parent widget of the skin. Default value is null, no parent widget.
	 *
	 * @var Weather|null
	 */
	protected $parent = null;

	protected function _register_controls_actions() {
		add_action( 'cmsmasters_elementor/element/cmsmasters-weather/after_init_controls', array( $this, 'register_controls' ) );
	}

	/**
	 * Register controls.
	 *
	 * @since 1.5.0 Fixed notice "_skin".
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;
	}

	public function render() {
		if ( '' === $this->parent->get_appID() || $this->parent->is_error() ) {
			return;
		}

		if ( AjaxWidgetModule::is_active_ajax() ) {
			echo 'is_ajax';
		}

		$attr = array(
			'class' => array(
				'cmsmasters-weather',
			),
			'title' => $this->parent->get_address(),
			'data-temperature-scale' => $this->parent::get_temperature_scale(),
			'data-description-type' => $this->parent->get_weather_type(),
			'data-direction' => Weather::CARDINALS,
			'data-set-cookie' => $this->parent->get_cookie_id() . '=' . implode( ',', $this->parent->get_geo() ),
			'data-ip-error' => $this->parent->is_error() ? 'error' : 'checked',
		);

		if ( $this->parent->get_settings_for_display( 'wind_icon_rotate' ) ) {
			$attr['style'] = "--cmsmasters-weather-wind-deg: {$this->parent->get_wind_degree()}deg";
		}

		$this->parent->add_render_attribute( 'main', $attr );

		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'main' ); ?>>
			<div class="cmsmasters-weather-inner"><?php $this->render_inner(); ?></div>
		</div>
		<?php
	}

	protected function render_icon( $item ) {
		if ( 'description' === $item ) {
			$icon = $this->parent->get_icon_description();
		} else {
			$icon = $this->parent->get_settings_for_display( "{$item}_icon" );
		}

		if ( empty( $icon['value'] ) ) {
			return;
		}

		echo '<div class="weather-icon">';
			Utils::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
		echo '</div>';
	}

	/**
	 * @since 1.4.0
	 */
	protected function render_field( $item, $icon = true ) {
		$this->render_field_open( $item );

		echo '<div class="weather-field-outer">';

		if ( $icon ) {
			$this->render_icon( $item );
		}

		echo '<div class="weather-field-inner">';

		switch ( $item ) {
			case 'region':
				$this->render_field_inner_region();

				break;
			case 'temperature':
				$this->render_field_inner_temperature();

				break;
			case 'temperature_feels':
				$this->render_field_inner_temperature_feels();

				break;
			case 'description':
				$this->render_field_inner_description();

				break;
			case 'humidity':
				$this->render_field_inner_humidity();

				break;
			case 'pressure':
				$this->render_field_inner_pressure();

				break;
			case 'wind':
				$this->render_field_inner_wind();

				break;
		}

		echo '</div>' .
		'</div>';

		$this->render_field_close( $item );
	}

	/**
	 * @since 1.4.0
	 */
	protected function render_field_open( $item ) {
		echo '<div class="weather-field weather-field--' . esc_attr( $item ) . '">';
	}

	protected function render_field_close() {
		echo '</div>';
	}

	protected function render_field_inner_region() {
		$settings = $this->parent->get_settings_for_display();
		$weather = $this->parent->get_weather();

		$skin = $settings['_skin'];
		$region_column = ( ( 'standard' === $skin && 'column' === $settings['region_display'] ) ? true : false );

		echo '<span class="weather-region weather-region--city' . ( $region_column ? ' region_display' : '' ) . '">' .
			esc_html( $weather['name'] ) .
		'</span>';

		if ( $settings['region_country'] ) {
			if (
				( 'standard' === $skin && 'row' === $settings['region_display'] ) ||
				'line' === $skin
			) {
				echo '<span class="weather-region weather-region--sep"></span>';
			}

			$geo_country_iso = $this->parent->get_geo( 'country_iso' );
			$geo_country = $this->parent->get_geo( 'country' );

			if ( $settings['region_reduction'] ) {
				$country = $geo_country_iso;
			} else {
				$country = $geo_country;
			}

			echo '<span class="weather-region weather-region--country' . ( $region_column ? ' region_display' : '' ) . '">' .
				esc_html( $country ) .
			'</span>';
		}
	}

	protected function render_field_inner_temperature() {
		echo '<span class="cmsmasters_weather_temperature">' .
			esc_html( $this->parent->get_temperature() ) .
		'</span>';
	}

	protected function render_field_inner_temperature_feels() {
		$before = $this->parent->get_settings_for_display( 'temperature_feels_before' );

		if ( $before ) {
			echo '<span class="cmsmasters_weather_field_before cmsmasters_weather_temperature_feels_before">' .
				esc_html( $before ) .
			'</span>';
		}

		echo '<span class="cmsmasters_weather_temperature_feels">' .
			esc_html( $this->parent->get_temperature_feels() ) .
		'</span>';
	}

	protected function render_field_inner_description() {
		if ( 'icon' !== $this->parent->get_settings_for_display( 'description_view' ) ) {
			echo esc_html( $this->parent->get_weather()['weather'][0]['description'] );
		}
	}

	protected function render_field_inner_humidity() {
		$before = $this->parent->get_settings_for_display( 'humidity_before' );

		if ( $before ) {
			echo '<span class="cmsmasters_weather_field_before cmsmasters_weather_humidity_before">' .
				esc_html( $before ) .
			'</span>';
		}

		echo '<span class="cmsmasters_weather_additional_info_field cmsmasters_weather_humidity">' .
			esc_html( $this->parent->get_weather()['main']['humidity'] ) .
		'</span>';
	}

	protected function render_field_inner_pressure() {
		$before = $this->parent->get_settings_for_display( 'pressure_before' );

		if ( $before ) {
			echo '<span class="cmsmasters_weather_field_before cmsmasters_weather_pressure_before">' .
				esc_html( $before ) .
			'</span>';
		}

		echo '<span class="cmsmasters_weather_additional_info_field cmsmasters_weather_pressure">' .
			'<span class="cmsmasters_weather_pressure_size">' .
				esc_html( $this->parent->get_weather()['main']['pressure'] ) .
			'</span>' .
			'<span class="cmsmasters_weather_pressure_unit">' .
				esc_html__( 'hPa', 'cmsmasters-elementor' ) .
			'</span>' .
		'</span>';
	}

	protected function render_field_inner_wind() {
		$setting = $this->parent->get_settings_for_display();

		if ( $setting['wind_before'] ) {
			echo '<span class="cmsmasters_weather_field_before cmsmasters_weather_wind_before">' .
				esc_html( $setting['wind_before'] ) .
			'</span>';
		}

		echo '<span class="cmsmasters_weather_additional_info_field cmsmasters_weather_wind">';

		if ( $setting['wind_direction'] ) {
			echo '<span class="wind-direction">' .
				esc_html( $this->parent->get_wind_cardinal_direction_frontend() ) .
			'</span> ';
		}

		if ( 'short' === $setting['wind_speed_format'] ) {
			$unit = esc_html__( 'm/s', 'cmsmasters-elementor' );
		} else {
			$is_average_format = 'average' === $setting['wind_speed_format'];

			if ( $this->parent::is_miles_scale() ) {
				$unit = $is_average_format ? __( 'Mph', 'cmsmasters-elementor' ) : __( 'Milles', 'cmsmasters-elementor' );
			} else {
				$unit = $is_average_format ? __( 'Km/H', 'cmsmasters-elementor' ) : __( 'Kilometers', 'cmsmasters-elementor' );
			}
		}

			$speed = $this->parent->get_wind_speed();

			echo '<span class="size">' .
				esc_html( round( $speed, 1 ) ) .
			'</span>';

			echo '<span class="unit">' .
				esc_html( $unit ) .
			'</span>';

		echo '</span>';
	}

	abstract protected function render_inner();
}
