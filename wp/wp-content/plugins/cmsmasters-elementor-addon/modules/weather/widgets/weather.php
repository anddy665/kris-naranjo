<?php
namespace CmsmastersElementor\Modules\Weather\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Weather\Module as WeatherModule;
use CmsmastersElementor\Modules\Weather\Widgets\Skins;
use CmsmastersElementor\Modules\Settings\Module as SettingsModule;
use CmsmastersElementor\Modules\Settings\Settings_Page;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Weather extends Base_Widget {
	// Fallback ip.
	const IP_PLACEHOLDER = '161.185.160.93'; // New york, United states

	const URL_WEATHER_API = 'https://api.openweathermap.org/data/2.5/weather';

	const CACHE_EXPIRE = HOUR_IN_SECONDS * 2;
	const CACHE_PREFIX = 'cmsmasters_weather_';
	const CARDINALS = array(
		'N',
		'NNE',
		'NE',
		'ENE',
		'E',
		'ESE',
		'SE',
		'SSE',
		'S',
		'SSW',
		'SW',
		'WSW',
		'W',
		'WNW',
		'NW',
		'NNW',
	);
	const ISO_COUNTRY_FAHRENHEIT = array(
		'BS',
		'BZ',
		'KY',
		'PW',
		'US',
	);
	const ISO_COUNTRY_MILES = array(
		'US',
		'MM',
		'LR',
	);

	protected $_has_template_content = false; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	private $weather;

	public function get_title() {
		return __( 'Weather', 'cmsmasters-elementor' );
	}

	public function get_icon() {
		return 'cmsicon-weather';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.4.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'weather',
			'forecast',
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
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.16.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'widget-cmsmasters-weather',
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
	 * Register widget skins.
	 *
	 * This method is activated while initializing the widget base class.
	 * It is used to assign skins to widgets with `add_skin()` method.
	 *
	 * @since 1.4.0
	 */
	protected function register_skins() {
		$this->add_skin( new Skins\Line( $this ) );
		$this->add_skin( new Skins\Standard( $this ) );
	}

	public function register_controls() {
		$this->register_controls_content();
		$this->register_controls_style_region();
		$this->register_controls_style_temperature();
		$this->register_controls_style_temperature_feels();
		$this->register_controls_style_description();
		$this->register_controls_style_humidity();
		$this->register_controls_style_pressure();
		$this->register_controls_style_wind();
	}

	protected function register_controls_content() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		if ( '' === static::get_appID() || static::is_error() ) {
			if ( empty( static::get_appID() ) ) {
				$message = esc_html__( 'Please go to the ', 'cmsmasters-elementor' ) . '<a href="' . esc_url( admin_url( 'admin.php?page=cmsmasters-addon-settings' ) ) . '" target="_blank">' . __( 'settings page', 'cmsmasters-elementor' ) . '</a>' . esc_html__( ' and add your Open Weather api key', 'cmsmasters-elementor' );
			} elseif ( static::is_error() ) {
				$weather = static::get_weather();

				if ( isset( $weather['message'] ) ) {
					$message = esc_html( $weather['message'] );
				} else {
					$message = esc_html__( '"Open Weather Map" is not configured', 'cmsmasters-elementor' );
				}
			}

			$this->add_control(
				'warning',
				array(
					'raw' => $message,
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'render_type' => 'ui',
				)
			);
		}

		$this->add_control(
			'api_key',
			array(
				'label' => __( 'Api Key', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => static::get_appID(),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	protected function register_controls_style_region() {
		$weather_type = 'region';

		$this->start_controls_section(
			"{$weather_type}_section_style",
			array(
				'label' => __( 'Region', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			"{$weather_type}_country",
			array(
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Country', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			"{$weather_type}_reduction",
			array(
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Abbreviate to country code', 'cmsmasters-elementor' ),
				'condition' => array( "{$weather_type}_country!" => '' ),
			)
		);

		$this->add_control(
			"{$weather_type}_display",
			array(
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'label' => __( 'Display', 'cmsmasters-elementor' ),
				'label_block' => false,
				'options' => array(
					'column' => __( 'Block', 'cmsmasters-elementor' ),
					'row' => __( 'Inline', 'cmsmasters-elementor' ),
				),
				'default' => 'column',
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} .weather-field--region .weather-field-inner' => 'flex-direction: {{VALUE}};',
				),
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_country!" => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => "{$weather_type}_typography",
				'selector' => '{{WRAPPER}} .weather-field--region',
				'condition' => array( '_skin' => 'standard' ),
			)
		);

		$this->add_control(
			"{$weather_type}_color",
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .weather-field--region' => 'color: {{VALUE}};',
				),
				'condition' => array( '_skin' => 'standard' ),
			)
		);

		$this->add_control(
			"{$weather_type}_icon",
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'separator' => 'before',
			)
		);

		$this->add_control(
			"{$weather_type}_icon_color",
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-weather .weather-field--region .weather-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'condition' => array( "{$weather_type}_icon[value]!" => '' ),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_icon_size",
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--region .weather-field-outer .weather-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .weather-field--region .weather-field-outer .weather-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_icon[value]!" => '',
				),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_icon_spacing",
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--region .weather-field-outer .weather-icon + .weather-field-inner' => '--cmsmasters-weather-icon-spacing: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_icon[value]!" => '',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register temperature controls.
	 *
	 * Adds different input fields to allow the user to change and customize the temperature settings.
	 *
	 * @since 1.4.0
	 * @since 1.11.10 Added `Color` control for Temperature in Line Skin.
	 *
	 * @access protected
	 */
	protected function register_controls_style_temperature() {
		$weather_type = 'temperature';

		$this->start_controls_section(
			"{$weather_type}_section_style",
			array(
				'label' => __( 'Temperature', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => "{$weather_type}_typography",
				'selector' => '{{WRAPPER}} .weather-field--temperature',
				'condition' => array( '_skin' => 'standard' ),
			)
		);

		$this->add_control(
			"{$weather_type}_color",
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .weather-field--temperature' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_gap",
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--temperature' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( '_skin' => 'standard' ),
			)
		);

		$this->add_control(
			"{$weather_type}_icon_hr",
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => array( '_skin' => 'standard' ),
			)
		);

		$this->add_control(
			"{$weather_type}_icon",
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
			)
		);

		$this->add_control(
			"{$weather_type}_icon_color",
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-weather .weather-field--temperature .weather-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'condition' => array( "{$weather_type}_icon[value]!" => '' ),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_icon_size",
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--temperature .weather-field-outer .weather-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .weather-field--temperature .weather-field-outer .weather-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_icon[value]!" => '',
				),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_icon_spacing",
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--temperature .weather-field-outer .weather-icon + .weather-field-inner' => '--cmsmasters-weather-icon-spacing: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_icon[value]!" => '',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_controls_style_temperature_feels() {
		$weather_type = 'temperature_feels';

		$this->start_controls_section(
			"{$weather_type}_section_style",
			array(
				'label' => __( 'Temperature Feels', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => "{$weather_type}_typography",
				'selector' => '{{WRAPPER}} .weather-field--temperature_feels',
				'condition' => array( '_skin' => 'standard' ),
			)
		);

		$this->add_control(
			"{$weather_type}_color",
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .weather-field--temperature_feels' => 'color: {{VALUE}};',
				),
				'condition' => array( '_skin' => 'standard' ),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_gap",
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--temperature_feels' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( '_skin' => 'standard' ),
			)
		);

		$this->add_control(
			"{$weather_type}_before_hr",
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => array( '_skin' => 'standard' ),
			)
		);

		$this->add_control(
			"{$weather_type}_before",
			array(
				'type' => Controls_Manager::TEXT,
				'label' => esc_html__( 'Before Text', 'cmsmasters-elementor' ),
				'default' => 'Feels like',
				'label_block' => true,
			)
		);

		$this->add_control(
			"{$weather_type}_icon",
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'separator' => 'before',
			)
		);

		$this->add_control(
			"{$weather_type}_icon_color",
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-weather .weather-field--temperature_feels .weather-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'condition' => array( "{$weather_type}_icon[value]!" => '' ),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_icon_size",
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--temperature_feels .weather-field-outer .weather-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .weather-field--temperature_feels .weather-field-outer .weather-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_icon[value]!" => '',
				),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_icon_spacing",
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--temperature_feels .weather-field-outer .weather-icon + .weather-field-inner' => '--cmsmasters-weather-icon-spacing: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_icon[value]!" => '',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_controls_style_description() {
		$weather_type = 'description';

		$this->start_controls_section(
			"{$weather_type}_section_style",
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			"{$weather_type}_view",
			array(
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'label_block' => false,
				'default' => 'both',
				'options' => array(
					'text' => __( 'Text', 'cmsmasters-elementor' ),
					'icon' => __( 'Icons', 'cmsmasters-elementor' ),
					'both' => __( 'Both', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => "{$weather_type}_typography",
				'selector' => '{{WRAPPER}} .weather-field--description',
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_view!" => 'icon',
				),
			)
		);

		$this->add_control(
			"{$weather_type}_color",
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .weather-field--description' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_view!" => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_gap",
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'description' => 'For Horizontal type applies to mobile devices only.',
				'selectors' => array(
					'{{WRAPPER}} .weather-field--description' => '--cmsmasters-weather-description-spacing: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( '_skin' => 'standard' ),
			)
		);

		$this->add_control(
			"{$weather_type}_icon_hr",
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => array( "{$weather_type}_view!" => 'text' ),
			)
		);

		$types = array(
			'clear' => array(
				'label' => __( 'Clear', 'cmsmasters-elementor' ),
				'icon' => array(
					'library' => 'fa-solid',
					'value' => 'fas fa-sun',
				),
			),
			'clouds' => array(
				'label' => __( 'Clouds', 'cmsmasters-elementor' ),
				'icon' => array(
					'library' => 'fa-solid',
					'value' => 'fas fa-cloud',
				),
			),
			'rain' => array(
				'label' => __( 'Rain', 'cmsmasters-elementor' ),
				'icon' => array(
					'library' => 'fa-solid',
					'value' => 'fas fa-cloud-rain',
				),
			),
			'snow' => array(
				'label' => __( 'Snow', 'cmsmasters-elementor' ),
				'icon' => array(
					'library' => 'fa-regular',
					'value' => 'far fa-snowflake',
				),
			),
			'storm' => array(
				'label' => __( 'Storm', 'cmsmasters-elementor' ),
				'icon' => array(
					'library' => 'fa-solid',
					'value' => 'fas fa-bolt',
				),
			),
			'mist' => array(
				'label' => __( 'Mist', 'cmsmasters-elementor' ),
				'icon' => array(
					'library' => 'fa-solid',
					'value' => 'fas fa-smog',
				),
			),
		);

		foreach ( $types as $type_name => $type ) {
			$this->add_control(
				"{$weather_type}_{$type_name}_icon",
				array(
					'label' => $type['label'] . ' ' . __( 'Icon', 'cmsmasters-elementor' ),
					'label_block' => false,
					'type' => Controls_Manager::ICONS,
					'default' => $type['icon'],
					'skin' => 'inline',
					'condition' => array( "{$weather_type}_view!" => 'text' ),
				)
			);
		}

		$this->add_control(
			"{$weather_type}_icon_color",
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-weather .weather-field--description .weather-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'condition' => array( "{$weather_type}_view!" => 'text' ),
			)
		);

		$description_icon_conditions = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => '_skin',
					'operator' => '=',
					'value' => 'standard',
				),
				array(
					'name' => "{$weather_type}_view!",
					'operator' => '!==',
					'value' => 'text',
				),
				array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => "{$weather_type}_clear_icon[value]",
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => "{$weather_type}_clouds_icon[value]",
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => "{$weather_type}_rain_icon[value]",
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => "{$weather_type}_snow_icon[value]",
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => "{$weather_type}_storm_icon[value]",
							'operator' => '!==',
							'value' => '',
						),
						array(
							'name' => "{$weather_type}_mist_icon[value]",
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			),
		);

		$this->add_responsive_control(
			"{$weather_type}_icon_size",
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--description .weather-field-outer .weather-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .weather-field--description .weather-field-outer .weather-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $description_icon_conditions,
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_icon_spacing",
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--description .weather-field-outer .weather-icon + .weather-field-inner' => '--cmsmasters-weather-icon-spacing: {{SIZE}}{{UNIT}};',
				),
				'conditions' => $description_icon_conditions,
			)
		);

		$this->end_controls_section();
	}

	protected function register_controls_style_humidity() {
		$weather_type = 'humidity';

		$this->start_controls_section(
			"{$weather_type}_section_style",
			array(
				'label' => __( 'Humidity', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			"{$weather_type}_before",
			array(
				'type' => Controls_Manager::TEXT,
				'label' => esc_html__( 'Before Text', 'cmsmasters-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			"{$weather_type}_icon",
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-tint',
					'library' => 'fa-solid',
				),
				'skin' => 'inline',
				'separator' => 'before',
			)
		);

		$this->add_control(
			"{$weather_type}_icon_color",
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-weather .weather-field--humidity .weather-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'condition' => array( "{$weather_type}_icon[value]!" => '' ),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_icon_size",
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--humidity .weather-field-outer .weather-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .weather-field--humidity .weather-field-outer .weather-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_icon[value]!" => '',
				),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_icon_spacing",
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--humidity .weather-field-outer .weather-icon + .weather-field-inner' => '--cmsmasters-weather-icon-spacing: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_icon[value]!" => '',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_controls_style_pressure() {
		$weather_type = 'pressure';

		$this->start_controls_section(
			"{$weather_type}_section_style",
			array(
				'label' => __( 'Pressure', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			"{$weather_type}_before",
			array(
				'type' => Controls_Manager::TEXT,
				'label' => esc_html__( 'Before Text', 'cmsmasters-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			"{$weather_type}_icon",
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-thermometer-half',
					'library' => 'fa-solid',
				),
				'skin' => 'inline',
				'separator' => 'before',
			)
		);

		$this->add_control(
			"{$weather_type}_icon_color",
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-weather .weather-field--pressure .weather-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'condition' => array( "{$weather_type}_icon[value]!" => '' ),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_icon_size",
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--pressure .weather-field-outer .weather-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .weather-field--pressure .weather-field-outer .weather-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_icon[value]!" => '',
				),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_icon_spacing",
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--pressure .weather-field-outer .weather-icon + .weather-field-inner' => '--cmsmasters-weather-icon-spacing: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_icon[value]!" => '',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_controls_style_wind() {
		$is_miles_scale = self::is_miles_scale();
		$weather_type = 'wind';

		$this->start_controls_section(
			"{$weather_type}_section_style",
			array(
				'label' => __( 'Wind', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			"{$weather_type}_speed_format",
			array(
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'label' => __( 'Speed Format', 'cmsmasters-elementor' ),
				'label_block' => false,
				'default' => 'short',
				'options' => array(
					'short' => __( 'm/s', 'cmsmasters-elementor' ),
					'average' => $is_miles_scale ? __( 'Mph', 'cmsmasters-elementor' ) : __( 'Km/H', 'cmsmasters-elementor' ),
					'full' => $is_miles_scale ? __( 'Milles', 'cmsmasters-elementor' ) : __( 'Kilometers', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->add_control(
			"{$weather_type}_direction",
			array(
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Wind Direction', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			"{$weather_type}_cardinal_direction_names_popover_toggle",
			array(
				'label' => __( 'Customize direction names', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'render_type' => 'none',
				'condition' => array( "{$weather_type}_direction!" => '' ),
			)
		);

		$this->start_popover();

		foreach ( self::CARDINALS as $cardinal ) {
			$this->add_control(
				"{$weather_type}_cardinal_direction_name_{$cardinal}",
				array(
					'label' => $cardinal,
					'type' => Controls_Manager::TEXT,
					'placeholder' => $cardinal,
					'condition' => array(
						"{$weather_type}_direction!" => '',
						"{$weather_type}_cardinal_direction_names_popover_toggle" => 'yes',
					),
				)
			);
		}

		$this->end_popover();

		$this->add_control(
			"{$weather_type}_before",
			array(
				'type' => Controls_Manager::TEXT,
				'label' => esc_html__( 'Before Text', 'cmsmasters-elementor' ),
				'label_block' => true,
				'separator' => 'before',
			)
		);

		$this->add_control(
			"{$weather_type}_icon",
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-long-arrow-alt-up',
					'library' => 'fa-solid',
				),
				'skin' => 'inline',
				'separator' => 'before',
			)
		);

		$this->add_control(
			"{$weather_type}_icon_rotate",
			array(
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Rotate to wind direction', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'condition' => array( "{$weather_type}_icon[value]!" => '' ),
			)
		);

		$this->add_control(
			"{$weather_type}_icon_color",
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-weather .weather-field--wind .weather-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'condition' => array( "{$weather_type}_icon[value]!" => '' ),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_icon_size",
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--wind .weather-field-outer .weather-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .weather-field--wind .weather-field-outer .weather-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_icon[value]!" => '',
				),
			)
		);

		$this->add_responsive_control(
			"{$weather_type}_icon_spacing",
			array(
				'label' => __( 'Icon Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .weather-field--wind .weather-field-outer .weather-icon + .weather-field-inner' => '--cmsmasters-weather-icon-spacing: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'_skin' => 'standard',
					"{$weather_type}_icon[value]!" => '',
				),
			)
		);

		$this->end_controls_section();
	}

	public static function client_ip() {
		$client = isset( $_SERVER['HTTP_CLIENT_IP'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) ) : '';
		$forward = isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) : '';
		$remote = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

		if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {
			$ip = $client;
		} elseif ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
			$ip = $forward;
		} else {
			$ip = $remote;
		}

		return $ip;
	}

	public static function geo_data() {
		$url = 'https://ipapi.co/' . self::client_ip() . '/json';
		$response = wp_remote_get( $url );

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return array();
		}

		$geo_data = json_decode( wp_remote_retrieve_body( $response ), true );

		return $geo_data;
	}

	public static function get_cookie_id() {
		return self::CACHE_PREFIX . str_replace( '.', '_', self::client_ip() );
	}

	public static function get_geo( $parameter = null ) {
		$cookie_name = self::get_cookie_id();

		if ( isset( $_COOKIE[ $cookie_name ] ) ) {
			$cookie_data = explode( ',', sanitize_text_field( wp_unslash( $_COOKIE[ $cookie_name ] ) ) );

			$geo = array(
				'id' => $cookie_data[0],
				'city' => $cookie_data[1],
				'country_iso' => $cookie_data[2],
				'country' => $cookie_data[3],
			);

			if ( ! empty( self::geo_data() ) && is_array( self::geo_data() ) && ! in_array( 'error', self::geo_data() ) ) {
				$geo['region'] = ( isset( $cookie_data[4] ) ? $cookie_data[4] : '' );
				$geo['currency'] = ( isset( $cookie_data[5] ) ? $cookie_data[5] : '' );
				$geo['latitude'] = ( isset( $cookie_data[6] ) ? $cookie_data[6] : '' );
				$geo['longitude'] = ( isset( $cookie_data[7] ) ? $cookie_data[7] : '' );
				$geo['region_code'] = ( isset( $cookie_data[8] ) ? $cookie_data[8] : '' );
			}
		} else {
			$geo_data = self::geo_data();

			if ( ! empty( $geo_data ) && is_array( $geo_data ) && ! in_array( 'error', $geo_data ) ) {
				$geo = array(
					'id' => $geo_data['ip'],
					'city' => $geo_data['city'],
					'country_iso' => $geo_data['country'],
					'country' => $geo_data['country_name'],
					'region' => $geo_data['region'],
					'currency' => $geo_data['currency'],
					'latitude' => $geo_data['latitude'],
					'longitude' => $geo_data['longitude'],
					'region_code' => $geo_data['region_code'],
				);
			} else {
				$city = explode( ', ', static::get_default_location_options() )[0];
				$country = explode( ', ', static::get_default_location_options() )[1];
				$url = 'https://restcountries.com/v3.1/name/' . $country;
				$response = wp_remote_get( $url );
				$country_data = json_decode( wp_remote_retrieve_body( $response ), true );
				$country_code = ( isset( $country_data ) ? $country_data[0]['cca2'] : '' );

				$geo = array(
					'id' => self::client_ip(),
					'city' => $city,
					'country_iso' => $country_code,
					'country' => $country,
				);
			}
		}

		if ( $parameter ) {
			return Utils::get_if_isset( $geo, $parameter );
		}

		return $geo;
	}

	public function get_address() {
		$geo = self::get_geo();
		$address = join( ',', array( $geo['city'], $geo['country'] ) );

		return $address;
	}

	final public static function get_appID() {
		return get_option( 'elementor_' . WeatherModule::OPTION_NAME_API_KEY );
	}

	private function init_weather() {
		$address = $this->get_address();

		if ( ! $address ) {
			return;
		}

		$parameters = array(
			'q' => $address,
			'appid' => static::get_appID(),
		);

		$response = wp_remote_get( add_query_arg( $parameters, self::URL_WEATHER_API ) );

		if ( is_wp_error( $response ) ) {
			return;
		}

		$weather = json_decode( wp_remote_retrieve_body( $response ), true );

		$this->weather = $weather;

		return $this->weather;
	}

	public function get_weather() {
		if ( ! $this->weather ) {
			$this->init_weather();
		}

		return $this->weather;
	}

	/**
	 * Getting default location options.
	 *
	 * @since 1.0.0
	 * @since 1.5.1 Fixed notice if empty key in weather widget.
	 */
	final protected static function get_default_location_options() {
		$location_option = WeatherModule::OPTION_NAME_LOCATION_DEFAULT;

		if ( ! empty( get_option( 'elementor_' . $location_option ) ) ) {
			$location = get_option( 'elementor_' . $location_option );
		} else {
			$location = 'New York, United States of America';
		}

		return $location;
	}

	public static function is_fahrenheit_scale() {
		return in_array( self::get_geo( 'country_iso' ), self::ISO_COUNTRY_FAHRENHEIT, true );
	}

	public function get_temperature() {
		/**
		 * Default 'temp' is in scale Kelvin
		 * $temperature degrees Celsius by Kelvin
		 */
		$temperature = $this->get_weather()['main']['temp'] - 273.15;

		if ( self::is_fahrenheit_scale() ) {
			// Degrees Fahrenheit by Celsius.
			$temperature = $temperature * 1.8 + 32;
		}

		return round( $temperature );
	}

	public function get_temperature_feels() {
		/**
		 * Default 'temp' is in scale Kelvin
		 * $temperature degrees Celsius by Kelvin
		 */
		$temperature = $this->get_weather()['main']['feels_like'] - 273.15;

		if ( self::is_fahrenheit_scale() ) {
			// Degrees Fahrenheit by Celsius.
			$temperature = $temperature * 1.8 + 32;
		}

		return round( $temperature );
	}

	public static function get_temperature_scale() {
		if ( self::is_fahrenheit_scale() ) {
			return 'f';
		}

		return 'c';
	}

	public static function is_miles_scale() {
		return in_array( self::get_geo( 'country_iso' ), self::ISO_COUNTRY_MILES, true );
	}

	public function get_wind_speed() {
		$settings = $this->get_settings_for_display();

		// Default 'speed' in scale meter/sec.
		$data_wind_speed = $this->get_weather()['wind']['speed'];

		if ( 'short' === $settings['wind_speed_format'] ) {
			// meter/sec.
			$speed = $data_wind_speed;
		} elseif ( 'short' !== $settings['wind_speed_format'] ) {
			if ( self::is_miles_scale() ) {
				// Convert meter/sec in mph.
				$speed = $data_wind_speed * 2.2369363;
			} else {
				// Convert meter/sec in km/h.
				$speed = $data_wind_speed * 3.6;
			}
		}

		return $speed;
	}

	public function get_wind_degree() {
		$weather = $this->get_weather();

		if ( isset( $weather['wind']['deg'] ) ) {
			return $weather['wind']['deg'] % 360;
		}

		return 0;
	}

	public function get_wind_cardinal_direction_frontend() {
		$direction = $this->get_wind_cardinal_direction();
		$direction_frontend = $this->get_settings_for_display( "wind_cardinal_direction_name_{$direction}" );

		if ( $direction_frontend ) {
			return $direction_frontend;
		}

		return $direction;
	}

	public function get_wind_cardinal_direction() {
		$degree = $this->get_wind_degree();
		$cardinals_count = count( self::CARDINALS );
		$ix = round( $degree / ( 360 / $cardinals_count ) );

		return self::CARDINALS[ $ix % $cardinals_count ];
	}

	final public function is_error( $weather = null ) {
		if ( ! $weather ) {
			$weather = $this->get_weather();
		}

		return ! $weather || 200 !== $weather['cod'];
	}

	public function get_weather_type() {
		switch ( $this->get_weather()['weather'][0]['icon'] ) {
			case '02d':
			case '02n':
			case '03d':
			case '03n':
			case '04d':
			case '04n':
				return 'clouds';
			case '09d':
			case '09n':
			case '10d':
			case '10n':
				return 'rain';
			case '11d':
			case '11n':
				return 'storm';
			case '13d':
			case '13n':
				return 'snow';
			case '50d':
			case '50n':
				return 'mist';
			case '01d':
			case '01n':
			default:
				return 'clear';
		}
	}

	public function get_icon_description() {
		$weather_type = $this->get_weather_type();

		return $this->get_settings_for_display( "description_{$weather_type}_icon" );
	}

	public function filter_client_ip( $ip ) {
		if ( Utils::IP_LOCAL === $ip ) {
			$ip = self::IP_PLACEHOLDER;
		}

		return $ip;
	}

	public function render_content() {
		add_filter( 'cmsmasters_elementor/utils/client_ip', array( $this, 'filter_client_ip' ) );

		parent::render_content();

		remove_filter( 'cmsmasters_elementor/utils/client_ip', array( $this, 'filter_client_ip' ) );
	}

	public function render() {
		if ( is_admin() && ( static::is_error() || empty( static::get_appID() ) ) ) {
			echo '<div class="elementor-alert elementor-alert-danger">';

				if ( empty( static::get_appID() ) ) {
					echo esc_html__( 'Please go to the ', 'cmsmasters-elementor' ) . '<a href="' . esc_url( admin_url( 'admin.php?page=cmsmasters-addon-settings' ) ) . '" target="_blank">' . esc_html__( 'settings page', 'cmsmasters-elementor' ) . '</a>' . esc_html__( ' and add your Open Weather api key', 'cmsmasters-elementor' );
				} elseif ( static::is_error() ) {
					$weather = static::get_weather();

					if ( isset( $weather['message'] ) ) {
						echo esc_html( $weather['message'] );
					} else {
						echo esc_html__( '"Open Weather Map" is not configured', 'cmsmasters-elementor' );
					}
				}

			echo '</div>';

			return;
		}

		parent::render();
	}
}
