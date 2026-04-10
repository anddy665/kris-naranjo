<?php
namespace CmsmastersElementor\Modules\TemplateSections\Widgets;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TemplateSections\Traits\Site_Widget;
use CmsmastersElementor\Modules\TemplateSections\Widgets\Base\Breadcrumbs_Base;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;

use RankMath\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Addon breadcrumbs widget.
 *
 * Addon widget that display breadcrumbs of the pages.
 *
 * @since 1.0.0
 */
class Breadcrumbs extends Breadcrumbs_Base {

	use Site_Widget;

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
		return __( 'Breadcrumbs', 'cmsmasters-elementor' );
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
		return 'cmsicon-breadcrumbs';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'cmsmasters',
			'breadcrumbs',
			'navigation',
			'scheme',
			'bread',
			'crumbs',
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

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-breadcrumbs';
	}

	public function register_controls() {
		$this->register_section_breadcrumbs_settings_start();

		$this->get_source();

		$this->register_section_breadcrumbs_settings_end();

		$this->register_section_additional_options_start();

		$this->prefix_label();

		$this->register_section_additional_options_end();

		$this->register_section_breadcrumbs_style();

		$this->update_controls();
	}

	protected function get_source() {
		if (
			( function_exists( 'rank_math_the_breadcrumbs' ) ) &&
			( function_exists( 'yoast_breadcrumb' ) )
		) {
			$this->add_control(
				'html_description_danger',
				array(
					'raw' => __( 'Attention! Please keep only one SEO plugin active.', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-danger',
				)
			);
		}

		if ( ! function_exists( 'rank_math_the_breadcrumbs' ) && ! function_exists( 'yoast_breadcrumb' ) ) {
			$this->add_control(
				'html_description',
				array(
					'raw' => sprintf(
						__( 'This widget supports %1$s and %2$s plugins. To enable breadcrumbs Yoast SEO or Rank Math SEO install and activate the plugin, then proceed to the Breadcrumbs tab to enable them.', 'cmsmasters-elementor' ),
						self::get_seo_plugin_link( 'install', 'Yoast+SEO', __( 'Yoast SEO', 'cmsmasters-elementor' ) ),
						self::get_seo_plugin_link( 'install', 'Rank+Math+–+SEO+Plugin+for+WordPress', __( 'Rank Math SEO', 'cmsmasters-elementor' ) )
					),
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);
		}

		if ( function_exists( 'yoast_breadcrumb' ) && ! function_exists( 'rank_math_the_breadcrumbs' ) ) {
			if ( $this->is_breadcrumbs_enabled( 'breadcrumbs-enable' ) ) {
				$this->add_control(
					'source',
					array(
						'label' => __( 'Source', 'cmsmasters-elementor' ),
						'type' => CmsmastersControls::CHOOSE_TEXT,
						'options' => array(
							'cmsmasters' => array(
								'title' => __( 'CMSMasters', 'cmsmasters-elementor' ),
								'description' => 'Default breadcrumbs',
							),
							'yoast' => array(
								'title' => __( 'Yoast SEO', 'cmsmasters-elementor' ),
								'description' => 'Breadcrumbs by Yoast SEO Plugin',
							),
						),
						'default' => 'cmsmasters',
						'render_type' => 'template',
						'label_block' => false,
						'toggle' => false,
						'prefix_class' => 'cmsmasters-breadcrumbs-type-',
					)
				);

				$this->add_control(
					'html_description_yoast',
					array(
						'raw' => sprintf(
							__( 'Additional settings are available in the Yoast SEO %1$s Panel', 'cmsmasters-elementor' ),
							self::get_seo_plugin_link( 'settings', 'wpseo_titles#top#breadcrumbs', __( 'Breadcrumbs', 'cmsmasters-elementor' ) )
						),
						'type' => Controls_Manager::RAW_HTML,
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
						'condition' => array( 'source' => 'yoast' ),
					)
				);
			} else {
				$this->add_control(
					'html_description_yoast',
					array(
						'raw' => sprintf(
							__( 'To enable Yoast SEO breadcrumbs, proceed to the %1$s tab and enable them.', 'cmsmasters-elementor' ),
							self::get_seo_plugin_link( 'settings', 'wpseo_titles#top#breadcrumbs', __( 'Breadcrumbs', 'cmsmasters-elementor' ) )
						),
						'type' => Controls_Manager::RAW_HTML,
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					)
				);
			}
		}

		if ( function_exists( 'rank_math_the_breadcrumbs' ) && ! function_exists( 'yoast_breadcrumb' ) ) {
			if ( Helper::get_settings( 'general.breadcrumbs' ) ) {
				$this->add_control(
					'source',
					array(
						'label' => __( 'Source', 'cmsmasters-elementor' ),
						'type' => CmsmastersControls::CHOOSE_TEXT,
						'options' => array(
							'cmsmasters' => array(
								'title' => __( 'CMSMasters', 'cmsmasters-elementor' ),
								'description' => 'Default breadcrumbs',
							),
							'rank' => array(
								'title' => __( 'Rank Math SEO', 'cmsmasters-elementor' ),
								'description' => 'Breadcrumbs by Rank Math SEO',
							),
						),
						'default' => 'cmsmasters',
						'render_type' => 'template',
						'label_block' => false,
						'toggle' => false,
						'prefix_class' => 'cmsmasters-breadcrumbs-type-',
					)
				);

				$this->add_control(
					'html_description_rank',
					array(
						'raw' => sprintf(
							__( 'Additional settings are available in the Rank Math SEO %1$s', 'cmsmasters-elementor' ),
							self::get_seo_plugin_link( 'settings', 'rank-math-options-general#setting-panel-breadcrumbs', __( 'Breadcrumbs Panel', 'cmsmasters-elementor' ) )
						),
						'type' => Controls_Manager::RAW_HTML,
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
						'condition' => array( 'source' => 'rank' ),
					)
				);
			} else {
				$this->add_control(
					'html_description_rank',
					array(
						'raw' => sprintf(
							__( 'To enable Rank Math SEO breadcrumbs, proceed to the %1$s tab and enable them.', 'cmsmasters-elementor' ),
							self::get_seo_plugin_link( 'settings', 'rank-math-options-general#setting-panel-breadcrumbs', __( 'Breadcrumbs', 'cmsmasters-elementor' ) )
						),
						'type' => Controls_Manager::RAW_HTML,
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					)
				);
			}
		}
	}

	private static function get_seo_plugin_link( $type, $query_name, $plugin_name ) {
		$multisite_url = is_multisite() ? 'network/' : '';

		if ( 'install' === $type ) {
			$link = "{$multisite_url}plugin-install.php?s={$query_name}&tab=search&type=term";
		} else {
			$link = "admin.php?page={$query_name}";
		}

		$plugin_query_link = admin_url( $link );

		return sprintf( '<a href="%s" target="_blank">%s</a>', $plugin_query_link, $plugin_name );
	}

	protected function prefix_label() {
		if (
			( function_exists( 'yoast_breadcrumb' ) && $this->is_breadcrumbs_enabled( 'breadcrumbs-enable' ) ) ||
			( function_exists( 'rank_math_the_breadcrumbs' ) && Helper::get_settings( 'general.breadcrumbs' ) )
		) {
			$condition_cms = array( 'source' => 'cmsmasters' );
		} else {
			$condition_cms = array();
		}

		$this->add_control(
			'archive_prefix_show',
			array(
				'label' => __( 'Archive pages prefix', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'no',
				'condition' => $condition_cms,
			)
		);

		$this->add_control(
			'archive_prefix_label',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Archives for:', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => false,
				'condition' => array_merge( $condition_cms, array( 'archive_prefix_show' => 'yes' ) ),
			)
		);

		$this->add_control(
			'search_prefix_show',
			array(
				'label' => __( 'Search page prefix', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'no',
				'condition' => $condition_cms,
			)
		);

		$this->add_control(
			'search_prefix_label',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'You searched for:', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => false,
				'condition' => array_merge( $condition_cms, array( 'search_prefix_show' => 'yes' ) ),
			)
		);

		$this->add_control(
			'breadcrumbs_404',
			array(
				'label' => __( '404 page breadcrumbs', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Error 404: Page not found', 'cmsmasters-elementor' ),
				'label_block' => true,
				'separator' => 'before',
				'condition' => $condition_cms,
			)
		);
	}

	/**
	 * Update Controls
	 *
	 * Retrieve Update Controls.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function update_controls() {
		if (
			( function_exists( 'yoast_breadcrumb' ) && $this->is_breadcrumbs_enabled( 'breadcrumbs-enable' ) ) ||
			( function_exists( 'rank_math_the_breadcrumbs' ) && Helper::get_settings( 'general.breadcrumbs' ) )
		) {
			$any_of_the_prefixes = array(
				'relation' => 'and',
				'terms' => array(
					array(
						'name' => 'source',
						'operator' => '=',
						'value' => 'cmsmasters',
					),
					array(
						'relation' => 'or',
						'terms' => array(
							array(
								'name' => 'prefix_show',
								'operator' => '=',
								'value' => 'yes',
							),
							array(
								'name' => 'archive_prefix_show',
								'operator' => '=',
								'value' => 'yes',
							),
							array(
								'name' => 'search_prefix_show',
								'operator' => '=',
								'value' => 'yes',
							),
						),
					),
				),
			);
		} else {
			$any_of_the_prefixes = array(
				'relation' => 'and',
				'terms' => array(
					array(
						'relation' => 'or',
						'terms' => array(
							array(
								'name' => 'prefix_show',
								'operator' => '=',
								'value' => 'yes',
							),
							array(
								'name' => 'archive_prefix_show',
								'operator' => '=',
								'value' => 'yes',
							),
							array(
								'name' => 'search_prefix_show',
								'operator' => '=',
								'value' => 'yes',
							),
						),
					),
				),
			);
		}

		$this->update_control(
			'breadcrumbs_prefix_heading',
			array(
				'condition' => array(),
				'conditions' => $any_of_the_prefixes,
			)
		);

		$this->update_control(
			'breadcrumbs_prefix_typography',
			array(
				'condition' => array(),
				'conditions' => $any_of_the_prefixes,
			)
		);

		$this->update_control(
			'breadcrumbs_prefix_color',
			array(
				'condition' => array(),
				'conditions' => $any_of_the_prefixes,
			)
		);

		$this->update_control(
			'breadcrumbs_prefix_gap',
			array(
				'condition' => array(),
				'conditions' => $any_of_the_prefixes,
			)
		);
	}

	public function get_rank_math() {
		if (
			function_exists( 'rank_math_the_breadcrumbs' ) &&
			Helper::get_settings( 'general.breadcrumbs' )
		) {
			return rank_math_the_breadcrumbs();
		}
	}

	/**
	 * Render breadcrumbs output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_breadcrumbs() {
		global $post;

		$settings = $this->get_settings_for_display();

		$year_format = get_the_time( 'Y' );
		$month_format = get_the_time( 'F' );
		$month_number_format = get_the_time( 'n' );
		$day_format = get_the_time( 'd' );
		$day_full_format = get_the_time( 'l' );
		$url_year = get_year_link( $year_format );
		$url_month = get_month_link( $year_format, $month_number_format );

		$source = ( isset( $settings['source'] ) ? $settings['source'] : '' );

		if (
			! function_exists( 'yoast_breadcrumb' ) &&
			function_exists( 'rank_math_the_breadcrumbs' ) &&
			Helper::get_settings( 'general.breadcrumbs' ) &&
			'rank' === $source
		) {
			echo $this->get_rank_math();
		} elseif ( $this->is_breadcrumbs_enabled( 'breadcrumbs-enable' ) && 'yoast' === $source ) {
			echo $this->get_yoast_seo();
		} else {
			echo $this->get_homepage();

			if ( 'none' !== $settings['homepage_type'] ) {
				$this->get_separator();
			}

			if ( is_single() ) {
				$this->get_single_type();
			} elseif ( is_category() ) {
				$this->get_category_type();
			} elseif ( is_tag() ) {
				echo '<span>' . single_tag_title( '', false ) . '</span>';
			} elseif ( is_day() ) {
				echo '<a href="' . esc_url( $url_year ) . '">' . esc_html( $year_format ) . '</a>';

				$this->get_separator();

				echo '<a href="' . esc_url( $url_month ) . '">' . esc_html( $month_format ) . '</a>';

				$this->get_separator();

				echo '<span>' .
					esc_html( $day_format ) . ' (' . esc_html( $day_full_format ) . ')' .
				'</span>';
			} elseif ( is_month() ) {
				echo '<a href="' . esc_url( $url_year ) . '">' .
					esc_html( $year_format ) .
				'</a>';

				$this->get_separator();

				echo '<span>' . esc_html( $month_format ) . '</span>';
			} elseif ( is_year() ) {
				echo '<span>' . esc_html( $year_format ) . '</span>';
			} elseif ( is_search() ) {
				echo '<span>' . esc_html__( 'Search results for', 'cmsmasters-elementor' ) . ": '" . esc_html( get_search_query() ) . "'</span>";
			} elseif ( is_page() && ! $post->post_parent ) {
				echo '<span>' .
					esc_html( get_the_title( get_the_ID() ) ) .
				'</span>';
			} elseif ( is_page() && $post->post_parent ) {
				$this->get_page_type();
			} elseif ( is_author() ) {
				echo '<span>' . esc_html( get_the_author() ) . '</span>';
			} elseif ( is_tax( 'post_format' ) ) {
				if ( is_tax( 'post_format', 'post-format-gallery' ) ) {
					echo '<span>' . esc_html_x( 'Galleries', 'post format archive title', 'cmsmasters-elementor' ) . '</span>';
				} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
					echo '<span>' . esc_html_x( 'Images', 'post format archive title', 'cmsmasters-elementor' ) . '</span>';
				} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
					echo '<span>' . esc_html_x( 'Videos', 'post format archive title', 'cmsmasters-elementor' ) . '</span>';
				} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
					echo '<span>' . esc_html_x( 'Audio', 'post format archive title', 'cmsmasters-elementor' ) . '</span>';
				}
			} elseif ( is_post_type_archive() ) {
				echo '<span>' . esc_html( post_type_archive_title( '', false ) ) . '</span>';
			} elseif ( is_tax() ) {
				echo '<span>' . esc_html( single_term_title( '', false ) ) . '</span>';
			} elseif ( is_404() ) {
				echo '<span>';

				$breadcrumbs_404 = $settings['breadcrumbs_404'];

				if ( isset( $breadcrumbs_404 ) && '' !== $breadcrumbs_404 ) {
					echo esc_html( $breadcrumbs_404 );
				} else {
					echo esc_html__( 'Error 404: Page not found', 'cmsmasters-elementor' );
				}

				echo '</span>';
			} else {
				echo '<span>' . esc_html__( 'No breadcrumbs', 'cmsmasters-elementor' ) . '</span>';
			}
		}
	}

	/**
	 * Render separator output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_separator() {
		$settings = $this->get_active_settings();

		$separator_type = $settings['separator_type'];

		if ( 'none' === $separator_type ) {
			return;
		}

		echo '<span class="cmsmasters-widget-breadcrumbs__sep">';

		if ( 'icon' === $separator_type ) {
			$icon_separator = $settings['icon_separator'];

			if ( '' !== $icon_separator['value'] ) {
				Icons_Manager::render_icon( $icon_separator, array( 'aria-hidden' => 'true' ) );
			} else {
				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-angle-right',
						'library' => 'fa-solid',
					),
					array( 'aria-hidden' => 'true' )
				);
			}
		}

		if ( 'text' === $separator_type ) {
			$custom_separator = $settings['custom_separator'];
			$separator_value = '/';

			if ( isset( $custom_separator ) && '' !== $custom_separator ) {
				$separator_value = esc_html( $custom_separator );
			}

			echo '<span>' .
				esc_html( mb_strimwidth( $separator_value, 0, 3 ) ) .
			'</span>';
		}

		echo '</span>';
	}

	/**
	 * Render prefix output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_prefix() {
		if ( is_archive() ) {
			$this->get_prefix_content( 'archive_' );
		} elseif ( is_search() ) {
			$this->get_prefix_content( 'search_' );
		} else {
			$this->get_prefix_content();
		}
	}

	/**
	 * Render prefix content output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_prefix_content( $style = '' ) {
		$settings = $this->get_settings_for_display();

		$static_prefix = '';
		$prefix_style_value = $settings[ $style . 'prefix_show' ];
		$prefix_style = isset( $prefix_style_value ) ? $prefix_style_value : false;

		if ( 'archive_' === $style ) {
			$static_prefix = esc_html__( 'Archives for:', 'cmsmasters-elementor' );
		} elseif ( 'search_' === $style ) {
			$static_prefix = esc_html__( 'You searched for:', 'cmsmasters-elementor' );
		} else {
			$static_prefix = esc_html__( 'Browse:', 'cmsmasters-elementor' );
		}

		if ( 'yes' === $prefix_style ) {
			echo '<div class="cmsmasters-widget-breadcrumbs__prefix">';

			$prefix_label = $settings[ $style . 'prefix_label' ];

			if ( isset( $prefix_label ) && '' !== $prefix_label ) {
				echo esc_html( $prefix_label );
			} else {
				echo esc_html( $static_prefix );
			}

			echo '</div>';
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @return Categories for single
	 */
	public function get_single_type() {
		$settings = $this->get_settings_for_display();

		$categories = get_the_category();
		$num_cat = count( $categories );
		$show_subcategories = ( isset( $settings['show_subcategories'] ) && 'yes' === $settings['show_subcategories'] ? $settings['show_subcategories'] : '' );

		if ( $num_cat < 1 ) {
			echo '<span>' .
				esc_html( get_the_title( get_the_ID() ) ) .
			'</span>';
		} elseif ( $num_cat >= 1 ) {
			if ( ! $show_subcategories ) {
				if ( ! empty( $categories ) ) {
					$top_parent_category = $this->get_top_parent_category( $categories[0] );

					$category_link = get_category_link( $top_parent_category->term_id );

					echo '<a href="' . esc_url( $category_link ) . '">' .
						esc_html( $top_parent_category->name ) .
					'</a>';
				}
			} else {
				$categories_hierarchy = array();
				$categories_lookup = array();

				foreach ( $categories as $category ) {
					$categories_lookup[ $category->term_id ] = $category;
					$category->children = array();
				}

				foreach ( $categories as $category ) {
					if ( 0 === $category->parent || ! isset( $categories_lookup[ $category->parent ] ) ) {
						$categories_hierarchy[ $category->term_id ] = $category;
					} elseif ( isset( $categories_lookup[ $category->parent ] ) ) {
						$categories_lookup[ $category->parent ]->children[ $category->term_id ] = $category;
					}
				}

				$first = true;

				foreach ( $categories_hierarchy as $category ) {
					if ( ! $first ) {
						echo '<span class="cmsmasters-widget-breadcrumbs__category__sep" style="color: black; margin-right: var(--separator-right-gap);">' .
							'<span>' .
								esc_html__( ',', 'cmsmasters-elementor' ) .
							'</span>' .
						'</span>';
					} else {
						$first = false;
					}

					$this->render_category_tree( $category );
				}

				echo $this->get_separator();

				echo '<span>' .
					esc_html( get_the_title( get_the_ID() ) ) .
				'</span>';
			}
		}
	}

	/**
	 * Render top parent category tree output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.12.1
	 */
	private function get_top_parent_category( $category ) {
		while ( 0 !== $category->parent ) {
			$category = get_category( $category->parent );
		}

		return $category;
	}

	/**
	 * Render category tree output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.12.1
	 */
	private function render_category_tree( $category ) {
		$category_link = get_category_link( $category->term_id );

		echo '<a href="' . esc_url( $category_link ) . '">' .
			esc_html( $category->name ) .
		'</a>';

		if ( ! empty( $category->children ) ) {
			foreach ( $category->children as $child_category ) {
				echo $this->get_separator();

				$this->render_category_tree( $child_category );
			}
		}
	}

	/**
	 * Render breadcrumbs for category output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_category_type() {
		global $cat;

		$multiple_cats = get_category_parents(
			$cat,
			true
		);

		if ( ! isset( $multiple_cats ) && is_string( $multiple_cats ) ) {
			$multiple_cats_array = explode(
				$this->get_separator(),
				$multiple_cats
			);
		} else {
			$category = get_queried_object();

			$multiple_cats_array = array( $category->name );
		}

		$multiple_cats_array = array_diff( $multiple_cats_array, array( '' ) );

		foreach ( $multiple_cats_array as $single_cat ) {
			if ( end( $multiple_cats_array ) !== $single_cat ) {
				echo wp_kses( stripslashes( $single_cat ), 'post' );

				echo wp_kses_post( $this->get_separator() );
			} else {
				echo '<span>' . single_cat_title( '', false ) . '</span>';
			}
		}
	}


	/**
	 * Render breadcrumbs for page output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	public function get_page_type() {
		global $post;

		$post_array = get_post_ancestors( $post );

		krsort( $post_array );

		foreach ( $post_array as $key => $postid ) {
			$post_ids = get_post( $postid );

			echo '<a href="' . esc_url( get_permalink( $post_ids ) ) . '">' .
				esc_html( $post_ids->post_title ) .
			'</a>';

			$this->get_separator();
		}

		echo '<span>' .
			esc_html( get_the_title( get_the_ID() ) ) .
		'</span>';
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
			array(
				'field' => 'homepage_text',
				'type' => esc_html__( 'Homepage Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_separator',
				'type' => esc_html__( 'Separator Symbol', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'prefix_label',
				'type' => esc_html__( 'Prefix Label Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'archive_prefix_label',
				'type' => esc_html__( 'Archive Prefix Label Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'search_prefix_label',
				'type' => esc_html__( 'Search Prefix Label Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'breadcrumbs_404',
				'type' => esc_html__( 'Error 404 Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
