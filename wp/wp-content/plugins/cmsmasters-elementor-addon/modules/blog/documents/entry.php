<?php
namespace CmsmastersElementor\Modules\Blog\Documents;

use CmsmastersElementor\Modules\TemplateDocuments\Base\Section_Document;
use CmsmastersElementor\Modules\TemplatePreview\Traits\Preview_Type;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\User;
use Elementor\Utils as ElementorUtils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * CMSmasters entry library document.
 *
 * CMSmasters entry library document handler class is responsible for
 * handling a document of a entry type.
 *
 * @since 1.0.0
 */
class Entry extends Section_Document {

	use Preview_Type;

	/**
	 * Document post type meta key.
	 */
	const ENTRY_TEMPLATE_TYPE_META = '_cmsmasters_entry_post_type';

	/**
	 * @since 1.0.0
	 */
	public static $widgets_visibility = array(
		'Author_Box' => false,
		'Post_Comments' => false,
		'Post_Navigation_Fixed' => false,
		'Post_Navigation' => false,
	);

	/**
	 * @since 1.0.0
	 */
	protected $preview_type_default = '';

	/**
	 * @since 1.0.0
	 */
	protected $preview_id_default = '';

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @since 1.0.0
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['location_type'] = 'disabled';
		$properties['locations_category'] = 'disabled';

		$properties = apply_filters( 'cmsmasters_elementor/documents/entry/get_properties', $properties );

		return $properties;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_name() {
		return 'cmsmasters_entry';
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_title() {
		return __( 'Entry', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_initial_config() {
		$config = parent::get_initial_config();

		$config['container'] = '.cmsmasters-widget-template-modal .dialog-widget-content';

		return $config;
	}

	/**
	 * Get editor widgets panel categories.
	 *
	 * Retrieve an array of editor widgets panel categories.
	 *
	 * Move singular category widgets to the beginning of editor panel.
	 *
	 * @since 1.0.0
	 *
	 * @return array Reordered array of widget categories.
	 */
	protected static function get_editor_panel_categories() {
		$categories = array(
			self::SINGULAR_WIDGETS_CATEGORY => array( 'title' => self::get_title() ),
		);

		if ( Utils::is_pro() ) {
			$categories['theme-elements-single'] = array(
				'title' => __( 'Single', 'cmsmasters-elementor' ),
				'active' => true,
			);
		}

		$categories += parent::get_editor_panel_categories();

		unset( $categories[ self::SITE_WIDGETS_CATEGORY ] );
		unset( $categories[ self::WOO_WIDGETS_CATEGORY ] );
		unset( $categories[ self::TRIBE_EVENTS_WIDGETS_CATEGORY ] );

		if ( Utils::is_pro() ) {
			unset( $categories['theme-elements'] );
			unset( $categories['woocommerce-elements'] );
		} else {
			$categories['pro-elements']['active'] = false;
		}

		return $categories;
	}

	/**
	 * Save template type.
	 *
	 * Set document name as template type meta key.
	 *
	 * Set singular template post type meta key if selected.
	 *
	 * @since 1.0.0
	 */
	public function save_template_type() {
		parent::save_template_type();

		if ( ! empty( $_REQUEST[ self::ENTRY_TEMPLATE_TYPE_META ] ) ) {
			$template_type = $_REQUEST[ self::ENTRY_TEMPLATE_TYPE_META ];

			$this->update_meta( self::ENTRY_TEMPLATE_TYPE_META, $template_type );
		}
	}

	/**
	 * Document constructor.
	 *
	 * Initializing the Addon Entry document.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Class initial data.
	 */
	public function __construct( array $data = array() ) {
		if ( $data ) {
			add_filter( 'body_class', array( $this, 'filter_body_classes' ) );
		}

		parent::__construct( $data );
	}

	/**
	 * Add body classes.
	 *
	 * Filters body classes for the `style` controls selector.
	 *
	 * Fires by `body_class` WordPress filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $body_classes Body classes array.
	 *
	 * @return array Filtered body classes array.
	 */
	public function filter_body_classes( $body_classes ) {
		$template_type = Source_Local::get_template_type( get_the_ID() );

		if ( ( is_singular() || is_404() ) && 'archive' !== $template_type ) {
			$body_classes[] = 'elementor-page-' . $this->get_main_id();
		}

		return $body_classes;
	}

	/**
	 * Register document controls.
	 *
	 * Used to add new controls to entry documents settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		parent::register_controls();

		if ( $this->is_edit() ) {
			$this->register_preview_controls();
		}

		$this->register_style_controls();
	}

	/**
	 * Check edit mode.
	 *
	 * Checks if document opened in edit mode.
	 *
	 * @since 1.0.0
	 */
	public function is_edit() {
		if ( ! is_admin() ) {
			return false;
		}

		$document_id = Utils::get_document_id();

		return $document_id && $document_id === $this->get_main_id();
	}

	/**
	 * Register preview controls.
	 *
	 * Creates additional document preview controls.
	 *
	 * @since 1.0.0
	 * @since 1.0.2 Fixed preview background color control in updated elementor markup.
	 */
	protected function register_preview_controls() {
		$this->start_injection( array(
			'of' => 'preview_settings_heading',
			'at' => 'before',
		) );

		$this->add_responsive_control(
			'preview_width',
			array(
				'label' => __( 'Preview Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 100,
						'max' => 1000,
					),
					'vh' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'size_units' => array( 'px', 'vw' ),
				'default' => array(
					'size' => 500,
				),
				'selectors' => array(
					'.cmsmasters-widget-template-modal [data-elementor-id]{{WRAPPER}}' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'preview_background',
			array(
				'label' => __( 'Preview Background', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => array(
					'.cmsmasters-widget-template-modal [data-elementor-id]{{WRAPPER}} .elementor-section-wrap' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_injection();
	}

	/**
	 * Register document controls.
	 *
	 * Used to add new controls to entry documents settings.
	 *
	 * @since 1.0.0
	 */
	public function register_style_controls() {
		$this->start_injection(
			array(
				'type' => 'section',
				'at' => 'end',
				'of' => 'section_page_style',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'  => 'border',
				'separator' => 'before',
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_responsive_control(
			'border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'separator' => 'after',
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->end_injection();

		$this->start_controls_section(
			'section_advanced',
			array(
				'label' => __( 'Advanced', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			)
		);

		$this->add_control(
			'classes',
			array(
				'label' => __( 'CSS Classes', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'title' => __( 'Add your custom class WITHOUT the dot. e.g: my-class', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'overflow',
			array(
				'label' => __( 'Overflow', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'hidden' => __( 'Hidden', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => 'overflow: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Print element with wrapper.
	 *
	 * Used to generate the element final HTML inside user-selected
	 * wrapper tag on the frontend and the editor.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function print_elements_with_wrapper( $elements_data = null ) {
		static $first = true;

		if ( $first && $this->is_on_preview() ) {
			$first = false;

			$elements_data = $this->get_preview_elements_data();

			parent::print_elements( $elements_data );

			return;
		}

		parent::print_elements_with_wrapper( $elements_data );
	}

	/**
	 * Check if the document is on preview.
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function is_on_preview() {
		if ( ! User::is_current_user_can_edit( get_the_ID() ) ) {
			return false;
		}

		if ( ! isset( $_GET['preview'] ) || ! $_GET['preview'] ) {
			return false;
		}

		if ( ! isset( $_GET['preview_id'] ) || $this->get_main_id() !== (int) $_GET['preview_id'] ) {
			return false;
		}

		return true;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return array Document preview type options.
	 */
	public static function get_preview_type_options() {
		return array_merge(
			array( '' => __( 'Select preview', 'cmsmasters-elementor' ) ),
			static::get_preview_type_options_choices()
		);
	}

	/**
	 * @since 1.0.0
	 */
	public static function get_preview_type_options_choices() {
		$preview_type_choices = self::get_singular_preview_type_options_choices( false, false );

		unset( $preview_type_choices['error_404'] );
		unset( $preview_type_choices['singular']['options']['singular/attachment'] );

		return $preview_type_choices;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_preview_type_default() {
		$this->set_default_preview();

		return $this->preview_type_default;
	}

	/**
	 * @since 1.0.0
	 */
	public function get_preview_id_default() {
		$this->set_default_preview();

		return $this->preview_id_default;
	}

	/**
	 * @since 1.0.0
	 */
	protected function set_default_preview() {
		if ( ! empty( $this->preview_type_default ) ) {
			return;
		}

		$template_type = $this->get_main_meta( self::ENTRY_TEMPLATE_TYPE_META );

		list( $rule_type, $rule_subtype ) = array_pad( explode( '/', $template_type ), 2, '' );

		if ( 'page' === $rule_type ) {
			$this->preview_type_default = $template_type;

			return;
		}

		$latest_post = get_posts( array(
			'post_type' => $rule_subtype,
			'fields' => 'ids',
			'numberposts' => 1,
		) );

		if ( empty( $latest_post ) ) {
			return;
		}

		$this->preview_type_default = $template_type;
		$this->preview_id_default = $latest_post[0];
	}

	/**
	 * @since 1.0.0
	 */
	public function get_wp_preview_url() {
		$main_post_id = $this->get_main_id();

		return get_preview_post_link(
			$main_post_id,
			array(
				'preview_id' => $main_post_id,
				'preview_nonce' => wp_create_nonce( 'post_preview_' . $main_post_id ),
			)
		);
	}

	/**
	 * Get elements data for on preview page.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_preview_elements_data() {
		$widget = Plugin::elementor()->widgets_manager->get_widget_types( 'cmsmasters-blog-grid' );

		return array(
			array(
				'id' => ElementorUtils::generate_random_string(),
				'elType' => 'section',
				'elements' => array(
					array(
						'id' => ElementorUtils::generate_random_string(),
						'elType' => 'column',
						'settings' => array( '_column_size' => 100 ),
						'elements' => array(
							array(
								'id' => ElementorUtils::generate_random_string(),
								'elType' => $widget::get_type(),
								'widgetType' => $widget->get_name(),
								'settings' => array(
									'blog_layout' => 'custom',
									'blog_template_id' => $this->get_main_id(),
									'columns' => 3,
									'posts_per_page' => 6,
									'pagination_show' => '',
								),
							),
						),
					),
				),
			),
		);
	}

}
