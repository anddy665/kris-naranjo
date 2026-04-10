<?php
namespace CmsmastersElementor\Modules\SectionForHeader;

use CmsmastersElementor\Base\Base_Module;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends Base_Module {
	/**
	 * Controls parent class.
	 *
	 * @since 1.11.0
	 *
	 * @var Controls_Stack
	 */
	private $parent;

	/**
	 * Get module name.
	 *
	 * @since 1.11.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'section-for-header';
	}

	/**
	 * Init actions.
	 *
	 * Initialize module actions.
	 *
	 * @since 1.11.0
	 */
	protected function init_actions() {
		add_action( 'elementor/element/container/section_layout_additional_options/before_section_end', array( $this, 'container_custom_control' ) );
		add_action( 'elementor/element/section/section_layout/before_section_end', array( $this, 'section_custom_control' ) );
	}

	/**
	 * Custom Control.
	 *
	 * Custom control for container.
	 *
	 * @since 1.11.0
	 */
	public function container_custom_control( $element ) {
		$position = 'before';
		$control = 'overflow';
		$this->inject_custom_control( $element, $control, $position );
	}

	/**
	 * Custom Control.
	 *
	 * Custom control for section.
	 *
	 * @since 1.11.0
	 */
	public function section_custom_control( $element ) {
		$position = 'after';
		$control = 'stretch_section';
		$this->inject_custom_control( $element, $control, $position );
	}

	/**
	 * Inject custom control.
	 *
	 * @since 1.11.0
	 */
	public function inject_custom_control( $element, $control, $position ) {
		$this->parent = $element;

		$injection_start_control = $control;

		$this->parent->start_injection( array( 'of' => $injection_start_control, 'at' => $position ) );

		$this->parent->add_control(
			'section_for_header',
			array(
				'label' => __( 'Connect with Header', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Enable for the top content when using Overlap Header to apply the Header Gap. <a href="https://docs.cmsmasters.net/how-to-enable-header-overlap-content/">Learn more.</a>', 'cmsmasters-elementor' ),
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'prefix_class' => 'cmsmasters-section-for-header-',
			)
		);

		$this->parent->end_injection();
	}
}
