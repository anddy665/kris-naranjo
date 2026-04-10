<?php
namespace CmsmastersElementor\Modules\TemplatePages\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TemplatePages\Widgets\Heading;
use CmsmastersElementor\Modules\TemplatePages\Traits\Singular_Widget;
use CmsmastersElementor\Plugin as CmsmastersPlugin;
use CmsmastersElementor\Traits\Extendable_Widget;

use Elementor\Controls_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Post title widget.
 *
 * Addon widget that displays the title of current post.
 *
 * @since 1.0.0
 */
class Post_Title extends Heading {

	use Singular_Widget;
	use Extendable_Widget;

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return $this->get_name_prefix() . 'title';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Post Title', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-post-title';
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
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to
	 * change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Fixed display of controls on the Pointer Animation tab.
	 */
	protected function register_controls() {
		parent::register_controls();

		$dynamic_tags = CmsmastersPlugin::elementor()->dynamic_tags;
		$tag_names = $this->get_tag_names();

		$this->update_control(
			'title',
			array(
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['title'] ),
				),
			),
			array( 'recursive' => true )
		);

		$this->update_control(
			'link',
			array(
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['link'] ),
				),
				'condition' => array( 'title_link_switcher!' => 'no' ),
			),
			array( 'recursive' => true )
		);

		$this->update_control(
			'title_tag',
			array( 'separator' => 'before' ),
			array( 'recursive' => true )
		);

		$this->update_control(
			'animation',
			array( 'conditions' => '' ),
			array( 'recursive' => true )
		);

		$this->start_injection( array(
			'of' => 'title_tag',
			'at' => 'before',
		) );

		$this->add_control(
			'title_link_switcher',
			array(
				'label' => __( 'Link To', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'no' => array(
						'title' => __( 'None', 'cmsmasters-elementor' ),
					),
					'yes' => array(
						'title' => __( 'Post', 'cmsmasters-elementor' ),
						'description' => __( 'Open Post', 'cmsmasters-elementor' ),
					),
					'custom' => array(
						'title' => __( 'Custom', 'cmsmasters-elementor' ),
						'description' => __( 'Custom URL', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'no',
			)
		);

		$this->add_control(
			'custom_link',
			array(
				'label' => __( 'Custom Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'show_label' => false,
				'condition' => array( 'title_link_switcher' => 'custom' ),
			)
		);

		$this->end_injection();

		$this->start_injection( array( 'of' => 'line_clamp_count' ) );

		$this->add_control(
			'title_advanced_popover',
			array(
				'label' => __( 'Advanced', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'separator' => 'before',
			)
		);

		$this->start_popover();

		$this->add_control(
			'title_before',
			array(
				'label' => __( 'Before', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'condition' => array( 'title_advanced_popover' => 'yes' ),
			)
		);

		$this->add_control(
			'title_after',
			array(
				'label' => __( 'After', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'condition' => array( 'title_advanced_popover' => 'yes' ),
			)
		);

		$this->add_control(
			'title_fallback',
			array(
				'label' => __( 'Fallback', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'condition' => array( 'title_advanced_popover' => 'yes' ),
			)
		);

		$this->end_popover();

		$this->end_injection();
	}

	/**
	 * Get tag names.
	 *
	 * Retrieve widget dynamic controls tag names.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget dynamic controls tag names.
	 */
	protected function get_tag_names() {
		return array(
			'title' => 'cmsmasters-post-title',
			'link' => 'cmsmasters-post-url',
		);
	}

	protected function set_condition_sets() {
		$this->add_conditions_set( 'link_visible', array( 'title_link_switcher!' => 'no' ) );
		$this->add_conditions_set( 'link_hidden', array( 'title_link_switcher' => 'no' ) );

		$this->add_conditions_set( 'link_visible_term', array(
			'name' => 'title_link_switcher',
			'operator' => '!==',
			'value' => 'no',
		) );
		$this->add_conditions_set( 'link_hidden_term', array(
			'name' => 'title_link_switcher',
			'value' => 'no',
		) );
	}

	public function stop_editing() {
		return true;
	}

	public function render() {
		if ( ! $this->should_show_page_title() ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'title', 'class', 'entry-title' );

		$is_custom_link = isset( $settings['title_link_switcher'] ) && 'custom' === $settings['title_link_switcher'];
		$is_custom_link_url = isset( $settings['custom_link'] ) &&
			is_array( $settings['custom_link'] ) &&
			! empty( $settings['custom_link']['url'] );

		if ( $is_custom_link && $is_custom_link_url ) {
			$this->add_link_attributes( 'link', $settings['custom_link'] );

			$this->link_active = true;
		}

		return parent::render();
	}

	protected function should_show_page_title() {
		if ( is_archive() || is_home() || is_search() ) {
			return true;
		}

		$current_doc = CmsmastersPlugin::elementor()->documents->get( get_the_ID() );

		if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
			return false;
		}

		return true;
	}

	protected function get_title_render_text() {
		$title = parent::get_title_render_text();

		$settings = $this->get_settings_for_display();

		if ( empty( $title ) ) {
			if ( ! empty( $settings['title_fallback'] ) ) {
				$title = $settings['title_fallback'];
			} else {
				return '';
			}
		}

		if ( ! empty( $settings['title_before'] ) ) {
			$title = $settings['title_before'] . $title;
		}

		if ( ! empty( $settings['title_after'] ) ) {
			$title .= $settings['title_after'];
		}

		return $title;
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to
	 * generate the live preview.
	 *
	 * @since 1.0.0
	 */
	protected function content_template() {
		?>
		<#
		if ( ! settings.title_link_switcher || 'no' === settings.title_link_switcher ) {
			var titleUrl = '';
		} else if ( 'custom' === settings.title_link_switcher && '' !== settings.custom_link.url ) {
			var titleUrl = settings.custom_link.url;
		}

		if ( ! modifiedTitle ) {
			var modifiedTitle = settings.title;
		}

		if ( 'yes' === settings.title_advanced_popover ) {
			if ( '' === modifiedTitle ) {
				if ( '' !== settings.title_fallback ) {
					modifiedTitle = settings.title_fallback;
				} else {
					return false;
				}
			}

			if ( '' !== settings.title_before ) {
				modifiedTitle = settings.title_before + modifiedTitle;
			}

			if ( '' !== settings.title_after ) {
				modifiedTitle += settings.title_after;
			}
		}

		var stopEditing = true;

		view.addRenderAttribute( 'title', 'class', 'entry-title' );
		#>
		<?php

		parent::content_template();
	}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			'custom_link' => array(
				'field' => 'url',
				'type' => esc_html__( 'Title Custom Link', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'title_before',
				'type' => esc_html__( 'Title Before', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'title_after',
				'type' => esc_html__( 'Title After', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'title_fallback',
				'type' => esc_html__( 'Title Fallback', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
