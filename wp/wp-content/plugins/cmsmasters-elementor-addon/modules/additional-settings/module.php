<?php
namespace CmsmastersElementor\Modules\AdditionalSettings;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Plugin;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Core\DynamicTags\Dynamic_CSS;
use Elementor\Core\Files\CSS\Post as Post_CSS_File;
use Elementor\Element_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Module extends Base_Module {

	/**
	 * Controls parent class.
	 *
	 * @since 1.0.0
	 *
	 * @var Controls_Stack
	 */
	private $parent;

	public function get_name() {
		return 'additional-settings';
	}

	protected function init_actions() {
		add_action( 'elementor/element/after_section_end', array( $this, 'register_controls' ), 10, 2 );

		add_action( 'elementor/element/after_add_attributes', array( $this, 'render_element_attributes' ) );

		add_action( 'elementor/element/parse_css', array( $this, 'add_post_css' ), 10, 2 );
		add_action( 'elementor/css-file/post/parse', array( $this, 'add_page_settings_css' ) );
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP Strict Standards.
	 *
	 * @param Controls_Stack $element
	 * @param string $section_id
	 */
	public function register_controls( $element, $section_id ) {
		$searched_section = 'section_custom_css_pro';

		if ( $searched_section !== $section_id ) {
			return;
		}

		$this->parent = $element;

		$stack_name = $this->parent->get_unique_name();
		$css_section = Plugin::elementor()->controls_manager->get_control_from_stack( $stack_name, $searched_section );

		if ( is_wp_error( $css_section ) ) {
			$css_section = Plugin::elementor()->controls_manager->get_control_from_stack( $stack_name, 'section_custom_css' );
		}

		$tab = ! is_wp_error( $css_section ) ? $css_section['tab'] : Controls_Manager::TAB_ADVANCED;

		$this->parent->start_controls_section(
			'cmsmasters_section_additional',
			array(
				'label' => __( 'Additional Settings', 'cmsmasters-elementor' ),
				'tab' => $tab,
			)
		);

		if ( 'stack' !== $this->parent->get_type() ) {
			$this->parent->add_control(
				$this->get_control_name( 'html_attributes' ),
				[
					'label' => __( 'Element custom attributes', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXTAREA,
					'dynamic' => array( 'active' => true ),
					'placeholder' => __( 'key|value', 'cmsmasters-elementor' ),
					'description' => sprintf(
						/* translators: Additional Settings module 'Custom Attributes' control description. 1: Selector, 2: Separator */
						__( 'Set custom attributes for the element wrapper. %2$sEach attribute in a separate line. Separate attribute key from the value using %1$s character.', 'cmsmasters-elementor' ),
						'<strong><code>|</code></strong>',
						'<br>'
					),
					'render_type' => 'ui',
				]
			);
		}

		$this->parent->add_control(
			$this->get_control_name( 'custom_css' ),
			array(
				'label' => __( 'Element custom CSS rules', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CODE,
				'description' => sprintf(
					/* translators: Additional Settings module 'Custom CSS' control description. 1: Selector, 2: First example, 3: Second example, 4: Third example, 5: Separator */
					esc_html__( 'Use word "%1$s" to target element wrapper. %5$s Examples: %2$s // For wrapper element %3$s // For wrapper child element %4$s // Or use any custom selector', 'cmsmasters-elementor' ),
					'selector',
					'<br><strong>selector</strong> { color: black; }',
					'<br><strong>selector</strong> .child-element { background: none; }',
					'<br>.custom-element { font-weight: bold; }',
					'<br>'
				),
				'language' => 'css',
				'render_type' => 'ui',
			)
		);

		$this->parent->end_controls_section();
	}

	private function get_control_name( $name = '' ) {
		$control_name = 'cms';

		if ( ! empty( $name ) ) {
			$control_name .= "_{$name}";
		}

		return $control_name;
	}

	/**
	 * @param Element_Base $element
	 */
	public function render_element_attributes( $element ) {
		$settings = $element->get_settings_for_display();

		if ( empty( $settings['cms_html_attributes'] ) ) {
			return;
		}

		$attributes = $this->parse_html_attributes( $settings['cms_html_attributes'] );
		$black_list = $this->get_attributes_black_list();

		foreach ( $attributes as $attribute => $value ) {
			if ( in_array( $attribute, $black_list, true ) ) {
				continue;
			}

			$element->add_render_attribute( '_wrapper', $attribute, $value );
		}
	}

	private function parse_html_attributes( $attributes_setting ) {
		$attributes = explode( "\n", $attributes_setting );
		$result = array();

		foreach ( $attributes as $attribute ) {
			list( $attr_name, $attr_value ) = array_pad( explode( '|', $attribute ), 2, '' );

			$attr_name = $this->validate_attribute( $attr_name );

			if ( ! $attr_name ) {
				continue;
			}

			if ( '' !== $attr_value ) {
				$attr_value = trim( $attr_value );
			}

			$result[ $attr_name ] = $attr_value;
		}

		return $result;
	}

	private function validate_attribute( $name ) {
		$attr_name = strtolower( $name );

		// Remove any not allowed characters.
		preg_match( '/[-_a-z0-9]+/', $attr_name, $matched_attributes );

		if ( empty( $matched_attributes[0] ) ) {
			return false;
		}

		$attr_name = $matched_attributes[0];

		// Avoid Javascript events and unescaped href.
		if ( 'href' === $attr_name || 'on' === substr( $attr_name, 0, 2 ) ) {
			return false;
		}

		return $attr_name;
	}

	private function get_attributes_black_list() {
		static $black_list = null;

		if ( null === $black_list ) {
			$black_list = array(
				'class',
				'id',
				'data-id',
				'data-settings',
				'data-element_type',
				'data-widget_type',
				'data-model-cid',
			);

			/**
			 * Element attributes black list.
			 *
			 * Filters the element attributes that won't be rendered in the wrapper element.
			 *
			 * By default Addon doesn't render some attributes to prevent Elementor editor
			 * from breaking down. But this list of attributes can be changed.
			 *
			 * @since 1.0.0
			 *
			 * @param array $black_list Black list of element attributes.
			 */
			$black_list = apply_filters( 'cmsmasters_elementor/module/additional_settings/html_attributes_black_list', $black_list );
		}

		return $black_list;
	}

	/**
	 * @param Post_CSS_File $post_css
	 * @param Element_Base $element
	 */
	public function add_post_css( $post_css, $element ) {
		if ( $post_css instanceof Dynamic_CSS ) {
			return;
		}

		$custom_css = $element->get_settings( 'cms_custom_css' );

		if ( is_null( $custom_css ) ) {
			return;
		}

		$custom_css = trim( $custom_css );

		if ( empty( $custom_css ) ) {
			return;
		}

		$custom_css = sprintf(
			'/* %1$s */ %3$s /* %2$s */',
			sprintf(
				/* translators: Additional Settings module custom CSS code start comment. 1: Element name, 2: Element unique selector */
				esc_html__( 'Start of your custom CSS rules for %1$s, class: %2$s', 'cmsmasters-elementor' ),
				$element->get_name(),
				$element->get_unique_selector()
			),
			esc_html__( 'End of your custom CSS rules', 'cmsmasters-elementor' ),
			str_replace( 'selector', $post_css->get_element_unique_selector( $element ), $custom_css )
		);

		$post_css->get_stylesheet()->add_raw_css( $custom_css );
	}

	/**
	 * @param Post_CSS_File $post_css
	 */
	public function add_page_settings_css( $post_css ) {
		$document = Plugin::elementor()->documents->get( $post_css->get_post_id() );
		$custom_css = $document->get_settings( 'cms_custom_css' );

		if ( is_null( $custom_css ) ) {
			return;
		}

		$custom_css = trim( $custom_css );

		if ( empty( $custom_css ) ) {
			return;
		}

		$custom_css = sprintf(
			'/* %1$s */ %3$s /* %2$s */',
			esc_html__( 'Start of your page custom CSS rules', 'cmsmasters-elementor' ),
			esc_html__( 'End of your page custom CSS rules', 'cmsmasters-elementor' ),
			str_replace( 'selector', $document->get_css_wrapper_selector(), $custom_css )
		);

		$post_css->get_stylesheet()->add_raw_css( $custom_css );
	}

}
