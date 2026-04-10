<?php
namespace CmsmastersElementor\Modules\MetaData\Classes;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Classes\Separator;
use CmsmastersElementor\Controls_Manager as CmsmastersControlsManager;
use CmsmastersElementor\Controls\Groups\Group_Control_Format_Date;
use CmsmastersElementor\Controls\Groups\Group_Control_Format_Time;
use CmsmastersElementor\Modules\MetaData\Module as MetaDataModule;
use CmsmastersElementor\Modules\MetaData\Widgets\Meta_Data as MetaDataWidget;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * CMSMasters Elementor metadata class.
 *
 * @since 1.0.0
 */
class Meta_Data {

	/**
	 * Arguments.
	 *
	 * Holds all the metadata arguments.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $args = array();

	/**
	 * Addon base widget class.
	 *
	 * @since 1.0.0
	 *
	 * @var Base_Widget
	 */
	private $element;

	/**
	 * Selector with current name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $wrapper;

	/**
	 * Separator.
	 *
	 * Separator for each meta-field.
	 *
	 * @since 1.0.0
	 *
	 * @var Separator
	 */
	public $separator;

	/**
	 * Separator.
	 *
	 * Separator for each taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @var Separator
	 */
	public $separator_taxonomy;


	/**
	 * Meta Field name in loop.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $meta_field;

	/**
	 * Counter Postmeta constructor.
	 *
	 * Initializing the meta data class.
	 *
	 * @since 1.0.0
	 *
	 * @param Base_Widget $element Addon base widget class.
	 * @param array $args Arguments postmeta.
	 */
	public function __construct( Base_Widget $element, array $args = array() ) {
		$this->args = array_merge( static::get_default_args(), $args );

		$this->element = $element;

		$this->wrapper = "{{WRAPPER}} .cmsmasters-widget-meta-data[data-name=\"{$this->args['name']}\"]";

		$this->separator = new Separator(
			$this->element,
			array(
				'name' => "separator_{$this->args['name']}",
				'selector' => "{$this->wrapper} .cmsmasters-widget-meta-data-item",
			)
		);

		$this->separator_taxonomy = new Separator(
			$this->element,
			array(
				'name' => "separator_taxonomy_{$this->args['name']}",
				'label' => __( 'Separator Taxonomy', 'cmsmasters-elementor' ),
				'selector' => "{$this->wrapper} .cmsmasters-postmeta[data-name=\"taxonomy\"] .term-wrap",
			)
		);
	}

	/**
	 * Default arguments.
	 *
	 * Holds all the default arguments.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_default_args() {
		return array(
			'label' => __( 'Meta Data', 'cmsmasters-elementor' ),
			'name' => 'meta_data',
			'object_type' => array( 'post' ),
			'selector' => '{{WRAPPER}}',
		);
	}

	/**
	 *
	 * Register meta-data classes controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	public function register_controls_content() {
		$this->element->add_control(
			$this->get_prefix( 'show' ),
			array(
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Meta Data', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
			)
		);

		$this->element->add_control(
			$this->get_prefix( 'options' ),
			array(
				'label_block' => true,
				'type' => CmsmastersControlsManager::SELECTIZE,
				'groups' => array_values( MetaDataWidget::get_groups() ),
				'default' => array(
					'category',
					'date',
					'comments',
				),
				'multiple' => true,
				'condition' => array(
					$this->get_prefix( 'show!' ) => '',
				),
			)
		);

		$this->element->add_control(
			$this->get_prefix( 'author_heading' ),
			array(
				'label' => __( 'Author', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array(
					$this->get_prefix( 'show!' ) => '',
					$this->get_prefix( 'options' ) => 'author',
				),
			)
		);

		$this->element->add_control(
			$this->get_prefix( 'author_avatar' ),
			array(
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Avatar', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'condition' => array(
					$this->get_prefix( 'show!' ) => '',
					$this->get_prefix( 'options' ) => 'author',
				),
			)
		);

		$this->element->add_control(
			$this->get_prefix( 'author_size' ),
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					"{$this->wrapper} .cmsmasters-postmeta[data-name=\"author\"]" => '--avatar-size: {{SIZE}}{{UNIT}};',
				),
				'render_type' => 'template',
				'condition' => array(
					$this->get_prefix( 'show!' ) => '',
					$this->get_prefix( 'options' ) => 'author',
					$this->get_prefix( 'author_avatar!' ) => '',
				),
			)
		);

		$this->element->add_control(
			$this->get_prefix( 'author_prefix' ),
			array(
				'label' => __( 'Prefix', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'By', 'cmsmasters-elementor' ) . ' ',
				'condition' => array(
					$this->get_prefix( 'show!' ) => '',
					$this->get_prefix( 'options' ) => 'author',
				),
			)
		);

		$this->element->add_control(
			$this->get_prefix( 'date_heading' ),
			array(
				'label' => __( 'Date', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array(
					$this->get_prefix( 'show!' ) => '',
					$this->get_prefix( 'options' ) => 'date',
				),
			)
		);

		$this->element->add_group_control(
			Group_Control_Format_Date::get_type(),
			array(
				'name' => $this->get_prefix( 'date' ),
				'condition' => array(
					$this->get_prefix( 'show!' ) => '',
					$this->get_prefix( 'options' ) => 'date',
				),
			)
		);

		$this->element->add_control(
			$this->get_prefix( 'time_heading' ),
			array(
				'label' => __( 'Time', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array(
					$this->get_prefix( 'show!' ) => '',
					$this->get_prefix( 'options' ) => 'time',
				),
			)
		);

		$this->element->add_group_control(
			Group_Control_Format_Time::get_type(),
			array(
				'name' => $this->get_prefix( 'time' ),
				'condition' => array(
					$this->get_prefix( 'show!' ) => '',
					$this->get_prefix( 'options' ) => 'time',
				),
			)
		);
	}

	/**
	 * Get postmeta name with prefix.
	 *
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function get_prefix( $prefix ) {
		return join( '_', array( $this->args['name'], $prefix ) );
	}

	/**
	 *
	 * Register meta-data classes controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	public function register_controls_style() {
		$taxonomy_conditions = array(
			'relation' => 'or',
			'terms' => array(),
		);

		foreach ( Utils::get_taxonomy_options() as $taxonomy => $taxonomy_label ) {
			$taxonomy_conditions['terms'][] = array(
				'name' => $this->get_prefix( 'options' ),
				'operator' => 'contains',
				'value' => $taxonomy,
			);
		}

		$this->element->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => $this->get_prefix( 'typography' ),
				'selector' => "{$this->wrapper} .cmsmasters-widget-meta-data-item > *, {$this->wrapper} .cmsmasters-widget-meta-data-inner a",
				'condition' => array(
					$this->get_prefix( 'show!' ) => '',
					$this->get_prefix( 'options!' ) => '',
				),
			)
		);

		$this->element->start_controls_tabs( $this->get_prefix( 'style_tabs' ) );

		foreach (
			array(
				'normal' => __( 'Normal', 'cmsmasters-elementor' ),
				'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			) as $state => $label
		) {
			$is_normal = 'normal' === $state;

			if ( $is_normal ) {
				$selector = "{$this->wrapper} .cmsmasters-widget-meta-data-item";
			} else {
				$selector = "{$this->wrapper} .cmsmasters-widget-meta-data-item a:hover";
			}

			$this->element->start_controls_tab( $this->get_prefix( "style_tab_{$state}" ), array( 'label' => $label ) );

			if ( $is_normal ) {
				$this->element->add_control(
					$this->get_prefix( "color_{$state}" ),
					array(
						'label' => __( 'Text Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => 'color: {{VALUE}};',
						),
						'condition' => array(
							$this->get_prefix( 'show!' ) => '',
						),
					)
				);
			}

			$this->element->add_control(
				$this->get_prefix( "link_{$state}" ),
				array(
					'label' => __( 'Link Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						( $is_normal ? "{$this->wrapper} .cmsmasters-widget-meta-data-item a" : $selector ) => 'color: {{VALUE}};',
					),
					'condition' => array(
						$this->get_prefix( 'show!' ) => '',
					),
				)
			);

			$this->element->end_controls_tab();
		}

		$this->element->end_controls_tabs();

		$this->separator->add_controls(
			array( $this->get_prefix( 'show!' ) => '' )
		);

		$this->element->update_control( $this->separator->get_prefix( 'heading' ), array(
			'label' => __( 'Meta Data Separator', 'cmsmasters-elementor' ),
		) );

		$this->element->add_control(
			$this->get_prefix( 'taxonomy_heading' ),
			array(
				'label' => __( 'Taxonomy', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					$this->get_prefix( 'show!' ) => '',
				),
				'conditions' => $taxonomy_conditions,
			)
		);

		$this->element->start_controls_tabs(
			$this->get_prefix( 'style_taxonomy_tabs' ),
			array(
				'conditions' => $taxonomy_conditions,
			)
		);

		foreach (
			array(
				'normal' => __( 'Normal', 'cmsmasters-elementor' ),
				'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			) as $state => $label
		) {
			$is_normal = 'normal' === $state;

			$this->element->start_controls_tab(
				$this->get_prefix( "style_tab_taxonomy_{$state}" ),
				array(
					'label' => $label,
					'conditions' => $taxonomy_conditions,
				)
			);

			$this->element->add_control(
				$this->get_prefix( "taxonomy_color_{$state}" ),
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						( $is_normal ? "{$this->wrapper} a.term" : "{$this->wrapper} a.term:hover" ) => 'color: {{VALUE}};',
					),
					'condition' => array(
						$this->get_prefix( 'show!' ) => '',
					),
					'conditions' => $taxonomy_conditions,
				)
			);

			$this->element->add_control(
				$this->get_prefix( "taxonomy_bg_{$state}" ),
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						( $is_normal ? "{$this->wrapper} a.term" : "{$this->wrapper} a.term:hover" ) => 'background-color: {{VALUE}};',
					),
					'condition' => array(
						$this->get_prefix( 'show!' ) => '',
					),
					'conditions' => $taxonomy_conditions,
				)
			);

			$this->element->end_controls_tab();
		}

		$this->element->end_controls_tabs();

		$this->element->add_responsive_control(
			$this->get_prefix( 'taxonomy_padding' ),
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					"{$this->wrapper} .cmsmasters-postmeta[data-name=\"taxonomy\"] a" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					$this->get_prefix( 'show!' ) => '',
				),
				'conditions' => $taxonomy_conditions,
			)
		);

		$this->separator_taxonomy->add_controls(
			array(
				$this->get_prefix( 'show!' ) => '',
			),
			$taxonomy_conditions
		);

		$this->element->remove_control( $this->separator_taxonomy->get_prefix( 'heading' ) );

		$this->element->update_control( $this->separator_taxonomy->get_prefix( 'type' ), array(
			'label' => __( 'Separator', 'cmsmasters-elementor' ),
			'separator' => 'before',
		) );
	}

	/**
	 * Get Settings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $settings_prefix_name
	 *
	 * @return mixed
	 */
	public function get_settings( string $settings_prefix_name ) {
		return $this->element->get_settings_for_display( $this->get_prefix( $settings_prefix_name ) );
	}

	/**
	 * Get fallback settings.
	 *
	 * @since 1.0.0
	 *
	 * @param string $settings_prefix_name
	 *
	 * @return mixed
	 */
	public function get_settings_fallback( string $settings_prefix_name ) {
		return $this->element->get_settings_fallback( $this->get_prefix( $settings_prefix_name ) );
	}

	/**
	 * Render HTML.
	 *
	 * Post author.
	 *
	 * @since 1.0.0
	 */
	public function render_author() {
		$author_size = $this->get_settings( 'author_size' );
		$author_prefix = $this->get_settings_fallback( 'author_prefix' );
		$args = array(
			'avatar' => $this->is_enabled_author_avatar(),
		);

		if ( ! empty( $author_size['size'] ) ) {
			$args['avatar_size'] = $author_size['size'];
		}

		if ( $author_prefix ) {
			echo '<span class="author-prefix">' . esc_html( $author_prefix ) . '</span>';
		}

		MetaDataModule::render_author( $args );
	}

	/**
	 * Check author avatar.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	protected function is_enabled_author_avatar() {
		return (bool) $this->get_settings( 'author_avatar' );
	}

	/**
	 * Render HTML representation of current post taxonomy terms.
	 *
	 * @since 1.0.0
	 */
	public function render_taxonomy() {
		MetaDataModule::render_taxonomy(
			array(
				'taxonomy' => $this->meta_field,
				'separator' => $this->separator_taxonomy->get_render(),
			)
		);
	}

	/**
	 * Render HTML
	 *
	 * Post publication date.
	 *
	 * @since 1.0.0
	 */
	private function render_date() {
		MetaDataModule::render_date( "{$this->args['name']}_date", $this->element->get_settings() );
	}

	/**
	 * Render HTML
	 *
	 * Post publication time.
	 *
	 * @since 1.0.0
	 */
	private function render_time() {
		MetaDataModule::render_time( "{$this->args['name']}_time", $this->element->get_settings() );
	}

	/**
	 * Render HTML.
	 *
	 * Post post type.
	 *
	 * @since 1.0.0
	 */
	private function render_post_type() {
		MetaDataModule::render_post_type();
	}

	/**
	 * Render HTML.
	 *
	 * Post count comments.
	 *
	 * @since 1.0.0
	 */
	private function render_comments() {
		MetaDataModule::render_comments( array(
			'icon_enable' => true,
		) );
	}

	/**
	 * Render HTML.
	 *
	 * Post count like.
	 *
	 * @since 1.0.0
	 */
	private function render_like() {
		MetaDataModule::render_like( array(
			'icon_enable' => true,
		) );
	}

	/**
	 * Render HTML.
	 *
	 * Post count view.
	 *
	 * @since 1.0.0
	 */
	private function render_view() {
		MetaDataModule::render_view( array(
			'icon_enable' => true,
		) );
	}

	/**
	 * Check if something to display.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_visible() {
		$settings = $this->element->get_settings_for_display();
		$metadata_fields = $this->get_meta_fields();

		return $settings[ $this->get_prefix( 'show' ) ] && $metadata_fields;
	}

	/**
	 * Check if postmeta to display.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function check() {
		if ( $this->is_taxonomy() ) {
			return MetaDataModule::has_terms( $this->meta_field );
		} elseif ( 'comments' === $this->meta_field ) {
			return comments_open();
		}

		return true;
	}

	/**
	 * Retrieve the list of meta-fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_meta_fields() {
		$meta_fields = $this->get_settings( 'options' );

		if ( empty( $meta_fields ) || ! is_array( $meta_fields ) ) {
			$meta_fields = array();
		}

		return array_filter(
			$meta_fields,
			function ( $meta_field ) {
				$this->meta_field = $meta_field;

				return $this->check();
			},
			ARRAY_FILTER_USE_BOTH
		);
	}

	/**
	 * Render element.
	 *
	 * Generates the final HTML on the frontend.
	 *
	 * @since 1.0.0
	 * @since 1.12.0 Fix on separator in last meta item.
	 */
	public function render() {
		if ( ! $this->is_visible() ) {
			return;
		}

		$metadata_fields = $this->get_meta_fields();

		echo '<div class="cmsmasters-widget-meta-data" data-name="' . esc_attr( $this->args['name'] ) . '">' .
		'<div class="cmsmasters-widget-meta-data-inner">';

		foreach ( $metadata_fields as $key => $meta_field ) {
			$this->meta_field = $meta_field;

			echo '<div class="cmsmasters-widget-meta-data-item">';

			switch ( $meta_field ) {
				case 'author':
					$this->render_author();

					break;
				case 'date':
					$this->render_date();

					break;
				case 'time':
					$this->render_time();

					break;
				case 'post_type':
					$this->render_post_type();

					break;
				case 'comments':
					$this->render_comments();

					break;
				case 'like':
					$this->render_like();

					break;
				case 'view':
					$this->render_view();

					break;

				default:
					if ( $this->is_taxonomy() ) {
						$this->render_taxonomy();
					}
			}

			if ( 1 < count( $metadata_fields ) && count( $metadata_fields ) - 1 !== $key ) {
				$this->separator->render();
			}

			echo '</div>';
		}

		echo '</div>' .
		'</div>';
	}

	/**
	 * Check if taxonomy to display.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_taxonomy() {
		return isset( Utils::get_taxonomy_options()[ $this->meta_field ] );
	}
}
