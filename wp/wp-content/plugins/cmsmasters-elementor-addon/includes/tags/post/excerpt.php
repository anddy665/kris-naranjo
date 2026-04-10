<?php
namespace CmsmastersElementor\Tags\Post;

use CmsmastersElementor\Base\Traits\Base_Tag;
use CmsmastersElementor\Modules\TemplatePages\Module as TemplatePagesModule;
use CmsmastersElementor\Modules\TemplatePages\Widgets\Post_Excerpt;
use CmsmastersElementor\Tags\Post\Traits\Post_Group;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters excerpt.
 *
 * Retrieves an excerpt for the current post.
 *
 * @since 1.0.0
 */
class Excerpt extends Tag {

	use Base_Tag, Post_Group;

	/**
	* Get tag name.
	*
	* Returns the name of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag name.
	*/
	public static function tag_name() {
		return 'excerpt';
	}

	/**
	* Get tag title.
	*
	* Returns the title of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return string Tag title.
	*/
	public static function tag_title() {
		return __( 'Excerpt', 'cmsmasters-elementor' );
	}

	/**
	* Register controls.
	*
	* Registers the controls of the dynamic tag.
	*
	* @since 1.0.0
	*
	* @return void Tag controls.
	*/
	protected function register_controls() {
		/** @var TemplatePagesModule $pages_module */
		$pages_module = TemplatePagesModule::instance();

		$pages_module->add_global_excerpt_controls( $this );

		$this->add_control(
			'excerpt_wrap',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 'yes',
			)
		);
	}

	/**
	* Tag render.
	*
	* Prints out the value of the dynamic tag.
	*
	* @since 1.0.0
	* @since 1.22.0 Fixed empty excerpt issue.
	*
	* @return void Tag render result.
	*/
	protected function render() {
		$post = get_post();

		if ( ! $post ) {
			return;
		}

		$settings = $this->get_settings();

		list( $length, $more ) = TemplatePagesModule::set_excerpt_settings( $settings );

		if ( false === $more ) {
			$more = TemplatePagesModule::CONTENT_BREAK;
		}

		$post_excerpt = $post->post_excerpt;
		$has_post_excerpt = ( '' !== trim( (string) $post->post_excerpt ) );
		$excerpt = '';

		if ( $has_post_excerpt ) {
			if ( $settings['full_excerpt'] ) {
				$excerpt = self::get_wp_excerpt();
			} else {
				$excerpt = TemplatePagesModule::get_custom_excerpt( $length, $more, $post_excerpt );
			}
		} elseif ( $settings['use_content'] ) {
			$excerpt = TemplatePagesModule::get_custom_excerpt( $length, $more );
		}

		if ( '' === $excerpt ) {
			return;
		}

		if ( $settings['excerpt_wrap'] ) {
			$excerpt = wpautop( $excerpt );
		}

		echo wp_kses_post( $excerpt );
	}

	/**
	 * Get WordPress excerpt.
	 *
	 * Retrieves full WordPress post excerpt.
	 *
	 * @since 1.0.0
	 *
	 * @return string WordPress post excerpt.
	 */
	public static function get_wp_excerpt() {
		add_filter( 'excerpt_length', array( 'TemplatePagesModule', 'get_excerpt_length' ) );
		remove_filter( 'the_excerpt', 'wpautop' );

		ob_start();

		the_excerpt();

		$excerpt = ob_get_clean();

		add_filter( 'the_excerpt', 'wpautop' );
		remove_filter( 'excerpt_length', array( 'TemplatePagesModule', 'get_excerpt_length' ) );

		return $excerpt;
	}

}
