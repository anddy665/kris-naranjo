<?php
namespace CmsmastersElementor\Modules\TemplateSections\Documents;

use CmsmastersElementor\Modules\TemplateSections\Documents\Base\Header_Footer_Document;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Header extends Header_Footer_Document {

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Document name.
	 */
	public function get_name() {
		return 'cmsmasters_header';
	}

	/**
	 * Get document title.
	 *
	 * Retrieve the document title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Document title.
	 */
	public static function get_title() {
		return __( 'Header', 'cmsmasters-elementor' );
	}

	/**
	 * Document constructor.
	 *
	 * Initializing the Addon Entry document.
	 *
	 * @since 1.11.0
	 *
	 * @param array $data Class initial data.
	 */
	public function __construct( array $data = array() ) {
		if ( $data ) {
			add_filter( 'cmsmasters_elementor/documents/container_attributes', array( $this, 'add_type_class' ) );
		}

		parent::__construct( $data );
	}

	/**
	 * Register document controls.
	 *
	 * Used to add new controls to page documents settings.
	 *
	 * @since 1.0.0
	 * @since 1.11.0 Added header type controls.
	 */
	protected function register_controls() {
		parent::register_controls();

		if ( 'cmsmasters_header' === $this->get_name() ) {
			$this->inject_overlap_content_controls();
		}


		/**
		 * Register Header and Footer document controls.
		 *
		 * Used to add new controls to the header and footer document settings.
		 *
		 * Fires after Elementor registers the document controls.
		 *
		 * @since 1.11.0
		 *
		 * @param Header_Footer_Document $this Header and footer base document instance.
		 */
		do_action( 'cmsmasters_elementor/documents/header/register_controls', $this );
	}

	/**
	 * Inject the control.
	 *
	 * Injects the control
	 *
	 * @since 1.11.0
	 */
	private function inject_overlap_content_controls() {

		$this->start_injection( array(
			'of' => 'post_status',
			'fallback' => array( 'of' => 'post_title' ),
		) );

		$this->add_control(
			'cms_header_type_absolute',
			array(
				'label' => __( 'Overlap Content', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'Enable to make the Header overlap the content.', 'cmsmasters-elementor' ),
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'prefix_class' => 'cmsmasters-header-type-absolute-',
				'render_type' => 'template',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'cms_section_for_header_pdd',
			array(
				'label' => __( 'Header Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'Set the value in px that will be applied as a padding-top to the top content (when it is Connected with Header) to add space and avoid content overlaying. <a href="https://docs.cmsmasters.net/how-to-enable-header-overlap-content/">Learn more.</a>', 'cmsmasters-elementor' ),
				'size_units' => array( 'px', '%', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'body' => '--cmsmasters-section-for-header-pdd: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'cms_header_type_absolute' => 'yes',
				),
			)
		);

		$this->end_injection();
	}

	public function add_type_class( $attributes ) {
		$settings = $this->get_settings_for_display();
		$attributes['class'] .= ' cmsmasters-header-position-absolute-' . $settings['cms_header_type_absolute'];
		
		return $attributes;
	}
}
