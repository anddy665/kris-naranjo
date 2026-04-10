<?php
namespace CmsmastersElementor\Modules\MetaData;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Controls\Groups\Group_Control_Format_Date;
use CmsmastersElementor\Controls\Groups\Group_Control_Format_Time;
use CmsmastersElementor\Modules\MetaData\Classes\Counters_Post_Meta;
use CmsmastersElementor\Utils;

use Elementor\Utils as ElementorUtils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters Elementor meta data module.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	/**
	 * Like.
	 *
	 * Likes handler.
	 *
	 * @since 1.0.0
	 *
	 * @var Counters_Post_Meta
	 */
	public static $like;

	/**
	 * View.
	 *
	 * Views handler.
	 *
	 * @since 1.0.0
	 *
	 * @var Counters_Post_Meta
	 */
	public static $view;

	/**
	 * Get module name.
	 *
	 * Retrieve the Meta Data module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters_meta_data';
	}

	/**
	 * Retrieve widget classes name.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_widgets() {
		return array( 'Meta_Data' );
	}

	/**
	 * Add filters initialization.
	 *
	 * Register filters for the Meta Data module.
	 *
	 * @since 1.0.0
	 */
	protected function init_filters() {
		add_filter( 'cmsmasters_elementor/frontend/settings', array( $this, 'frontend_settings' ) );
	}

	/**
	 * Meta Data module constructor.
	 *
	 * Initializing the Addon Meta Data module.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();

		self::$like = new Counters_Post_Meta( 'like' );
		self::$view = new Counters_Post_Meta( 'view', array(
			'remove' => false,
		) );
	}

	/**
	 * Filter frontend settings.
	 *
	 * Filters the Addon settings for elementor frontend.
	 *
	 * Fired by `cmsmasters_elementor/frontend/settings` Addon action hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Frontend settings.
	 *
	 * @return array Filtered frontend settings.
	 */
	public function frontend_settings( $settings ) {
		return array_replace_recursive( array(
			'nonces' => array(
				'meta_data' => wp_create_nonce( $this->get_name() ),
			),
			'i18n' => array(
				'meta_data' => array(
					'metadata_unlike' => esc_html__( 'Unlike', 'cmsmasters-elementor' ),
					'metadata_like' => esc_html__( 'Like', 'cmsmasters-elementor' ),
				),
			),
		), $settings );
	}

	/**
	 * Check if there are terms in the taxonomy of the current post.
	 *
	 * @param string $taxonomy Taxonomy name.
	 * @param int|WP_Post|null $post_id Post ID or object.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @return bool
	 */
	public static function has_terms( $taxonomy, $post_id = null ) {
		// post format taxonomy always has term.
		if ( 'post_format' === $taxonomy ) {
			return true;
		}

		$terms = get_the_terms( $post_id, $taxonomy );

		return ! ( empty( $terms ) || is_wp_error( $terms ) );
	}

	/**
	 * Render HTML representation of current post taxonomy terms
	 *
	 * @param array $args Render options
	 * @param array $postmeta_args postmeta options
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public static function render_taxonomy( $args, $postmeta_args = array() ) {
		$args = array_merge( array(
			'taxonomy' => 'category',
			'separator' => '',
			'before' => '',
			'after' => '',
		), $args );
		$terms = get_the_terms( null, $args['taxonomy'] );
		$postmeta_args = array_merge(
			array(
				'attrs' => array(
					'data-taxonomy' => $args['taxonomy'],
				),
			),
			$postmeta_args
		);

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			if ( 'post_format' === $args['taxonomy'] ) {
				$term = new \stdClass();

				$term->name = _x( 'Standard', 'Post format', 'cmsmasters-elementor' );

				if ( ! $terms ) {
					$terms = array();
				}

				$terms[] = $term;
			} else {
				return '';
			}
		}

		static::render_postmeta(
			'taxonomy',
			function() use ( $terms, $args ) {
				foreach ( $terms as $term ) {
					if ( $term instanceof \stdClass ) {
						$link = '';
					} else {
						$link = get_term_link( $term, $args['taxonomy'] );
					}

					if ( empty( $link ) || is_wp_error( $link ) ) {
						$link = false;
					}

					$tag = $link ? 'a' : 'span';
					$attr = array(
						'class' => 'term',
						'rel' => 'category tag',
					);

					if ( $link ) {
						$attr['href'] = $link;
					}

					echo '<span class="term-wrap">' .
					'<' . tag_escape( $tag ) . ' ' . ElementorUtils::render_html_attributes( $attr ) . '>';

					if ( $args['before'] ) {
						echo '<span class="taxonomy-additional-content taxonomy-additional-content--before">' .
							$args['before'] . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'</span>';
					}

					echo '<span>' . esc_html( $term->name ) . '</span>';

					if ( $args['after'] ) {
						echo '<span class="taxonomy-additional-content taxonomy-additional-content--after">' .
							wp_kses_post( $args['after'] ) .
						'</span>';
					}

					echo '</' . tag_escape( $tag ) . '>';

					if ( 1 < count( $terms ) && $args['separator'] ) {
						echo $args['separator'];
					}

					echo '</span>';
				}
			},
			$postmeta_args
		);
	}

	/**
	 * Render HTML
	 *
	 * Post publication time
	 *
	 * @param array $args Render options
	 * @param array $postmeta_args postmeta options
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 * @since 1.4.0 Added display of post update time.
	 * @since 1.15.0 Fixed post creation time taking into account Local time.
	 */
	public static function render_time( $prefix, $settings, $postmeta_args = array() ) {
		$modified = ( isset( $settings['time_modified'] ) && 'updated' === $settings['time_modified'] ? true : false );
		$time = Group_Control_Format_Time::get_render_format( $prefix, $settings, get_post_time( 'U', $modified ), $modified );

		static::render_postmeta(
			'time',
			function() use ( $time ) {
				echo '<span>' . esc_html( $time ) . '</span>';
			},
			$postmeta_args
		);
	}

	/**
	 * Render HTML.
	 *
	 * Post post type.
	 *
	 * @param array $args Render options
	 * @param array $postmeta_args postmeta options
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public static function render_post_type( $postmeta_args = array() ) {
		$post_type = get_post_type();
		$archive_link = get_post_type_archive_link( $post_type );

		if ( $archive_link ) {
			$postmeta_args['tag'] = 'a';
			$postmeta_args['attrs']['href'] = $archive_link;
		}

		static::render_postmeta(
			'post_type',
			function() use ( $post_type ) {
				$post_type_name = ucwords( preg_replace( '/-|_/', ' ', $post_type ) );

				echo esc_html( $post_type_name );
			},
			$postmeta_args
		);
	}

	/**
	 * Render HTML
	 *
	 * Post publication date
	 *
	 * @param array $args Render options
	 * @param array $postmeta_args postmeta options
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 * @since 1.4.0 Added display of post update date.
	 * @since 1.15.0 Fixed post creation date taking into account Local time.
	 */
	public static function render_date( $prefix, $settings, $postmeta_args = array() ) {
		$modified = ( isset( $settings['date_modified'] ) && 'updated' === $settings['date_modified'] ? true : false );
		$date = Group_Control_Format_Date::get_render_format( $prefix, $settings, get_post_time( 'U', $modified ), $modified );

		static::render_postmeta(
			'date',
			function() use ( $date, $settings ) {
				if ( ! empty( $settings['date_link'] ) ) {
					echo '<a href="' . get_day_link( get_post_time( 'Y' ), get_post_time( 'm' ), get_post_time( 'j' ) ) . '" tabindex="0">';
				}

				echo '<span>' . esc_html( $date ) . '</span>';

				if ( ! empty( $settings['date_link'] ) ) {
					echo '</a>';
				}
			},
			$postmeta_args
		);
	}

	/**
	 * Render HTML.
	 *
	 * Post author.
	 *
	 * @param array $args Render options
	 * @param array $postmeta_args postmeta options
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public static function render_author( $args = array(), $postmeta_args = array() ) {
		$args = array_merge( array(
			'avatar_size' => apply_filters( 'cmsmasters_elementor/meta_data/author_avatar_default_size', 35 ),
			'avatar' => true,
			'link' => true,
		), $args );

		static::render_postmeta(
			'author',
			function() use ( $args ) {
				$post = get_post();
				$user_id = $post->post_author;
				$display_name = get_the_author_meta( 'display_name', $user_id );
				$attrs = array(
					'rel' => 'author',
					'tabindex' => '0',
					'aria-label' => 'Link to the author page for the ' . $display_name,
				);
				$tag = 'span';

				if ( $args['link'] ) {
					$attrs['href'] = get_author_posts_url( $user_id );

					if ( $attrs['href'] ) {
						$tag = 'a';
					}
				}

				if ( $args['avatar'] ) {
					echo '<figure class="avatar-wrap">';

					/**
					 * Before rendering avatar author.
					 *
					 * @since 1.1.0
					 */
					do_action( 'cmsmasters_elementor/postmeta/author/avatar/render/start' );

					echo '<' . tag_escape( $tag ) . ' ' . ElementorUtils::render_html_attributes( $attrs + array( 'class' => array( 'avatar-link' ) ) ) . '>' .
						get_avatar( $user_id, $args['avatar_size'], get_option( 'avatar_default' ) ) .
					'</' . tag_escape( $tag ) . '>';

					echo '</figure>';
				}

				echo '<' . tag_escape( $tag ) . ' ' . ElementorUtils::render_html_attributes( $attrs ) . '>';

					/**
					 * After rendering avatar author.
					 *
					 * @since 1.1.0
					 * @since 1.3.3 Fixed position before text
					 */
					do_action( 'cmsmasters_elementor/postmeta/author/avatar/render/end' );

					echo '<span>' . esc_html( $display_name ) . '</span>' .
				'</' . tag_escape( $tag ) . '>';
			},
			$postmeta_args
		);
	}

	/**
	 * Render HTML.
	 *
	 * Post count comments.
	 *
	 * @param array $postmeta_args postmeta options
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 * @since 1.1.0 Add comments count with text.
	 *
	 * @return string
	 */
	public static function render_comments( $postmeta_args = array() ) {
		$args = array(
			'type' => 'comments',
			'count' => static::get_count( 'comments' ),
			'href' => get_comments_link(),
		);
		$postmeta_args['icon_default'] = array(
			'value' => 'far fa-comment',
			'library' => 'regular',
		);

		return self::render_count( $args, $postmeta_args );
	}

	/**
	 * Render HTML
	 *
	 * Post count likes
	 *
	 * @param array $postmeta_args postmeta options
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public static function render_like( $postmeta_args = array() ) {
		$args = array(
			'type' => self::$like->get_type(),
			'count' => static::get_count( 'like' ),
			'href' => '#',
			'id' => get_the_ID(),
			'active' => self::$like->exist(),
		);

		$postmeta_args['icon_default'] = array(
			'value' => 'far fa-heart',
			'library' => 'regular',
		);

		if ( $args['active'] ) {
			$postmeta_args['attrs']['title'] = esc_html__( 'Unlike', 'cmsmasters-elementor' );
		} else {
			$postmeta_args['attrs']['title'] = esc_html__( 'Like', 'cmsmasters-elementor' );
		}

		return self::render_count( $args, $postmeta_args );
	}

	/**
	 * Render HTML
	 *
	 * Post count views
	 *
	 * @param array $args Render options
	 * @param array $postmeta_args postmeta options
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public static function render_view( $postmeta_args = array() ) {
		$args = array(
			'type' => self::$view->get_type(),
			'count' => static::get_count( 'view' ),
			'active' => self::$view->exist(),
			'id' => get_the_ID(),
		);

		$postmeta_args['icon_default'] = array(
			'value' => 'far fa-eye',
			'library' => 'regular',
		);

		return self::render_count( $args, $postmeta_args );
	}

	/**
	 * Render reading time HTML.
	 *
	 * Post count reading time meta.
	 *
	 * @param array $args Render options.
	 * @param array $postmeta_args Post meta options.
	 *
	 * @since 1.3.0
	 */
	public static function render_reading_time( $postmeta_args = array() ) {
		$args = array(
			'type' => 'reading_time',
			'count' => static::get_count( 'reading_time' ),
		);

		$postmeta_args['icon_default'] = array(
			'value' => 'far fa-clock',
			'library' => 'regular',
		);

		return self::render_count( $args, $postmeta_args );
	}

	/**
	 * Render HTML
	 *
	 * Post count views
	 *
	 * @param array $args Render options
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 * @since 1.3.0 Added reading time meta.
	 */
	public static function get_count( $type ) {
		$count = 0;

		switch ( $type ) {
			case 'view':
				$count = self::$view->get_count();

				break;
			case 'like':
				$count = self::$like->get_count();

				break;
			case 'comments':
				$count = get_comments_number();

				break;
			case 'reading_time':
				$count = self::get_time_to_read();

				break;
		}

		return $count;
	}

	/**
	 * Get time to read.
	 *
	 * Retrieves post reading time.
	 *
	 * @param array $args Render options
	 * @param array $postmeta_args postmeta options
	 *
	 * @since 1.3.0
	 */
	public static function get_time_to_read() {
		$post = get_post();
		$post_content = $post->post_content;

		if ( ! $post_content ) {
			return 0;
		}

		$words_count = str_word_count( wp_strip_all_tags( $post_content ) );

		$minutes = floor( $words_count / 200 );
		$seconds = floor( $words_count % 200 / ( 200 / 60 ) );

		if ( 20 < $seconds ) {
			$minutes++;
		}

		return (int) $minutes;
	}

	/**
	 * Render HTML
	 *
	 * Post count common(ect. comments, likes...)
	 *
	 * @param array $args Render options
	 * @param array $postmeta_args postmeta options
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 * @since 1.1.0 Add comments count with text.
	 */
	public static function render_count( $args, $postmeta_args = array() ) {
		$args = Utils::array_merge_recursive( array(
			'active' => '',
			'count' => 0,
			'href' => '',
			'id' => '',
			'type' => null,
		), $args );

		if ( $args['id'] ) {
			$postmeta_args['attrs']['data-id'] = $args['id'];
		}

		if ( $args['active'] ) {
			$postmeta_args['attrs']['class'][] = 'active';
		}

		if ( $args['href'] ) {
			$postmeta_args['attrs']['href'] = $args['href'];
			$postmeta_args['tag'] = 'a';
		}

		$postmeta_args['attrs']['data-type'] = $args['type'];

		if ( empty( $postmeta_args['text'] ) ) {
			$postmeta_args['text'] = $args['count'];
		}

		static::render_postmeta(
			'count',
			function() use ( $postmeta_args ) {
				echo $postmeta_args['text'];
			},
			$postmeta_args
		);
	}

	/**
	 * Render postmeta.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	public static function render_postmeta( $name, $callback, $postmeta_args = array() ) {
		$postmeta_args_default = array(
			'icon' => '',
			'icon_default' => array(),
			'icon_enable' => false,
			'tag' => 'span',
			'attrs' => array(),
		);
		$postmeta_args = Utils::array_merge_recursive( $postmeta_args_default, $postmeta_args );

		if ( $postmeta_args['icon_enable'] ) {
			if (
				$postmeta_args['icon'] &&
				(
					is_array( $postmeta_args['icon'] ) &&
					$postmeta_args['icon']['value']
				)
			) {
				$icon = $postmeta_args['icon'];
			} else {
				$icon = $postmeta_args['icon_default'];
			}
		} else {
			$icon = false;
		}

		$postmeta_args['attrs']['class'][] = 'cmsmasters-postmeta';
		$postmeta_args['attrs']['data-name'] = $name;

		echo '<' . tag_escape( $postmeta_args['tag'] ) . ' ' . ElementorUtils::render_html_attributes( $postmeta_args['attrs'] ) . '>';

		/**
		 * Before rendering postmeta element.
		 *
		 * The dynamic portion of the hook name, `$name`, refers to the postmeta name.
		 *
		 * @since 1.0.0
		 */
		do_action( "cmsmasters_elementor/postmeta/{$name}/render/start" );

		echo '<span class="cmsmasters-postmeta__inner">';

		Utils::render_icon( $icon, array( 'aria-hidden' => 'true' ) );

		echo '<span class="cmsmasters-postmeta__content">';

		call_user_func( $callback );

		echo '</span>' .
		'</span>';

		/**
		 * After rendering postmeta element.
		 *
		 * The dynamic portion of the hook name, `$name`, refers to the postmeta name.
		 *
		 * @since 1.0.0
		 */
		do_action( "cmsmasters_elementor/postmeta/{$name}/render/end" );

		echo '</' . tag_escape( $postmeta_args['tag'] ) . '>';
	}

}
