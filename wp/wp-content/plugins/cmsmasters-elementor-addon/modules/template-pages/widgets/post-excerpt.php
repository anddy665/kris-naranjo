<?php
namespace CmsmastersElementor\Modules\TemplatePages\Widgets;

use CmsmastersElementor\Modules\TemplatePages\Module as TemplatePagesModule;
use CmsmastersElementor\Modules\TemplatePages\Traits\Singular_Widget;
use CmsmastersElementor\Modules\TemplatePages\Widgets\Base\Short_Text;
use CmsmastersElementor\Plugin as CmsmastersPlugin;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon Post Excerpt widget.
 *
 * Addon widget that displays excerpt of current post or part of
 * text content which uses instead of excerpt if it`s empty.
 *
 * @since 1.0.0
 */
class Post_Excerpt extends Short_Text {

	use Singular_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Post Excerpt', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve test widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-post-excerpt';
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
		return array_merge(
			parent::get_unique_keywords(),
			array(
				'excerpt',
				'description',
			)
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
	 * Register widget content controls.
	 *
	 * Adds widget content control fields.
	 *
	 * @since 1.0.0
	 */
	protected function register_widget_content_controls() {
		$dynamic_tags = CmsmastersPlugin::elementor()->dynamic_tags;
		$tag_names = $this->get_tag_names();

		$excerpt_settings = array(
			'excerpt_length' => TemplatePagesModule::get_excerpt_length(),
			'full_excerpt' => 'yes',
			'use_content' => 'yes',
			'excerpt_more' => false,
			'excerpt_wrap' => false,
		);

		$this->add_control(
			'content',
			array(
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['content'], $excerpt_settings ),
				),
			)
		);

		$no_content_settings = $excerpt_settings;

		$no_content_settings['use_content'] = false;

		$this->add_control(
			'no_content_excerpt',
			array(
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['no_content'], $no_content_settings ),
				),
			)
		);

		/** @var TemplatePagesModule $pages_module */
		$pages_module = TemplatePagesModule::instance();

		$pages_module->add_global_excerpt_controls( $this );
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
			'content' => 'cmsmasters-post-excerpt',
			'no_content' => 'cmsmasters-post-excerpt',
		);
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.22.0 Fixed empty excerpt issue.
	 */
	protected function render() {
		$post = get_post();

		if ( ! $post ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		list( $length, $more ) = TemplatePagesModule::set_excerpt_settings( $settings );

		$has_post_excerpt = ( '' !== trim( (string) $post->post_excerpt ) );
		$post_excerpt = $settings['content'];
		$excerpt = '';

		if ( $has_post_excerpt ) {
			if ( $settings['full_excerpt'] ) {
				$excerpt = $post_excerpt;
			} else {
				$excerpt = TemplatePagesModule::get_custom_excerpt( $length, $more, $post_excerpt );
			}
		} elseif ( $settings['use_content'] && get_the_ID() !== Utils::get_document_id() ) {
			$excerpt = TemplatePagesModule::get_custom_excerpt( $length, $more, $post_excerpt );

			$excerpt = self::check_content_break( $excerpt, $more );
		}

		if ( '' === $excerpt ) {
			return;
		}

		echo '<div class="entry-content">' .
			wp_kses_post( wpautop( $excerpt ) ) .
		'</div>';
	}

	/**
	 * Check content break.
	 *
	 * Checks post excerpt for content break and
	 * replace it with excerpt `more` text.
	 *
	 * @since 1.0.0
	 *
	 * @param string $excerpt Post excerpt text.
	 * @param string $more Excerpt more text.
	 *
	 * @return string Checked for content break post excerpt.
	 */
	public static function check_content_break( $excerpt, $more = '' ) {
		$content_break = TemplatePagesModule::CONTENT_BREAK;
		$excerpt_end = substr( $excerpt, -strlen( $content_break ) );

		if ( $content_break === $excerpt_end ) {
			$excerpt = str_replace( $content_break, $more, $excerpt );
		}

		return $excerpt;
	}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and
	 * used to generate editor live preview.
	 *
	 * @since 1.0.0
	 */
	protected function content_template() {
		?>
		<#
		var excerptLength = settings.excerpt_length;
		var excerptMore = settings.excerpt_more;

		if ( '' === excerptLength ) {
			excerptLength = <?php echo TemplatePagesModule::EXCERPT_LENGTH; ?>;
		}

		if ( settings.full_excerpt ) {
			excerptLength = <?php echo TemplatePagesModule::get_excerpt_length(); ?>;
		}

		if ( '' === excerptMore ) {
			excerptMore = '<?php echo TemplatePagesModule::EXCERPT_MORE; ?>';
		}

		var excerpt = settings.content;

		var truncateExcerpt = function() {
			var excerptArray = excerpt.split( ' ' );
			var truncatedExcerpt = excerptArray.splice( 0, Number( excerptLength ) );
			var newExcerpt = truncatedExcerpt.join( ' ' );

			if ( newExcerpt !== excerpt ) {
				newExcerpt += excerptMore;
			} else {
				newExcerpt = checkContentBreak( newExcerpt );
			}

			return newExcerpt;
		};

		var checkContentBreak = function( newExcerpt ) {
			var contentBreak = '<?php echo TemplatePagesModule::CONTENT_BREAK; ?>';
			var newExcerptEnd = newExcerpt.substr( -contentBreak.length );

			if ( contentBreak === newExcerptEnd ) {
				newExcerpt = newExcerpt.replace( contentBreak, excerptMore );
			}

			return newExcerpt;
		};

		var output = '';

		if ( '' !== settings.no_content_excerpt ) {
			if ( settings.full_excerpt ) {
				output = excerpt;
			} else {
				output = truncateExcerpt();
			}
		} else if ( settings.use_content ) {
			output = truncateExcerpt();
		}

		if ( '' === output ) {
			return false;
		}

		print( '<div class="entry-content"><p>' + output + '</p></div>' );
		#>
		<?php
	}
}
