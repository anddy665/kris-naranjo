<?php
namespace CmsmastersElementor\Modules\Popup\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as cmsmastersControls;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Plugin as cmsmastersPlugin;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Time_Popup extends Base_Widget {

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.9.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return 'cmsmasters-time-popup';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.9.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Timed Popup', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.9.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-Popup';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.9.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'popup',
			'time',
			'global',
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
	* Register controls.
	*
	* Registers the controls of the dynamic tag.
	*
	* @since 1.9.0
	* @since 1.9.2 Added setting to select the amount of popup display
	*/
	public function register_controls() {
		$this->start_controls_section(
			'section_time_popup',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'cms_popup_id',
			array(
				'label' => __( 'Choose Popup', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => cmsmastersControls::QUERY,
				'autocomplete' => array(
					'object' => Query_Manager::TEMPLATE_OBJECT,
					'query' => array(
						'meta_query' => array(
							array(
								'key' => Document::TYPE_META_KEY,
								'value' => 'cmsmasters_popup',
							),
						),
					),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'show_popup_type',
			array(
				'label' => __( 'Show only once on:', 'cmsmasters-elementor' ),
				'description' => __( 'Choose Site to display Popup every time after loading the page.
				Choose Browser to display Popup once per browser session. 
				Choose Device to display Popup once per device.', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'site' => array(
						'title' => __( 'Site', 'cmsmasters-elementor' ),
					),
					'browser' => array(
						'title' => __( 'Browser ', 'cmsmasters-elementor' ),
					),
					'device' => array(
						'title' => __( 'Device', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'site',
				'toggle' => false,
				'render_type' => 'template',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'start_popup',
			array(
				'label' => __( 'Show Popup', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'description' => __( 'Specify after what time (ms) the pop-up should appear.', 'cmsmasters-elementor' ),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	* Render widget output on the frontend.
	*
	* Written in PHP and used to generate the final HTML.
	*
	* @since 1.9.0
	*/
	protected function render() {
		$popup_id = esc_attr( $this->get_settings( 'cms_popup_id' ) );

		if ( empty( $popup_id ) || 'cmsmasters_popup' !== Source_Local::get_template_type( $popup_id ) ) {
			if ( is_admin() ) {
				Utils::render_alert( esc_html__( 'Please choose your popup template!', 'cmsmasters-elementor' ) );
			}

			return;
		}

		/** @var Plugin $addon */
		$addon = cmsmastersPlugin::instance();
		$frontend = $addon->frontend;

		echo "<a class='cmsmasters-time-popup-button' href='#cmsmasters-popup-" . $popup_id . "' aria-label='Popup Button'></a>";
		echo "<div class='animated cmsmasters-elementor-popup cmsmasters-elementor-popup-" . $popup_id . "' data-popup-id='" . $popup_id . "'>" . $frontend->get_widget_template( $popup_id, true ) . "</div>";
	}
}
