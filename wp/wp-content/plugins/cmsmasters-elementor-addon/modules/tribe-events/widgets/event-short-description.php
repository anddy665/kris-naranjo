<?php
namespace CmsmastersElementor\Modules\TribeEvents\Widgets;

use CmsmastersElementor\Modules\TemplatePages\Module as TemplatePagesModule;
use CmsmastersElementor\Modules\TemplatePages\Widgets\Post_Excerpt;
use CmsmastersElementor\Modules\TribeEvents\Traits\Tribe_Events_Singular_Widget;
use CmsmastersElementor\Utils;

use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Event_Short_Description extends Post_Excerpt {

	use Tribe_Events_Singular_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.13.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Event Short Description', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve test widget icon.
	 *
	 * @since 1.13.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-event-short-description';
	}

	/**
	 * Get tag names.
	 *
	 * Retrieve widget dynamic controls tag names.
	 *
	 * @since 1.13.0
	 *
	 * @return array Widget dynamic controls tag names.
	 */
	protected function get_tag_names() {
		return array(
			'content' => 'cmsmasters-tribe-events-event-description',
			'no_content' => 'cmsmasters-tribe-events-event-short-description',
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
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.13.0
	 */
	protected function render() {
		$post = get_post();

		if ( ! $post ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		list( $length, $more ) = TemplatePagesModule::set_excerpt_settings( $settings );

		$has_post_excerpt = ! empty( $post->post_excerpt );
		$post_excerpt = $settings['no_content_excerpt'];
		$post_content = $settings['content'];
		$excerpt = '';

		if ( $has_post_excerpt ) {
			if ( $settings['full_excerpt'] ) {
				$excerpt = $post_excerpt;
			} else {
				$excerpt = TemplatePagesModule::get_custom_excerpt( $length, $more, $post_excerpt );
			}
		} elseif ( $settings['use_content'] && get_the_ID() !== Utils::get_document_id() ) {
			$excerpt = TemplatePagesModule::get_custom_excerpt( $length, $more, $post_content );

			$excerpt = self::check_content_break( $excerpt, $more );
		}

		if ( '' === $excerpt ) {
			return;
		}

		echo '<div class="entry-content">';

			echo wp_kses_post( wpautop( $excerpt ) );

		echo '</div>';
	}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and
	 * used to generate editor live preview.
	 *
	 * @since 1.13.0
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

		var excerpt = settings.no_content_excerpt;
		var content = settings.content;

		var truncateExcerpt = function( useContent ) {
			var excerptText = ( useContent ) ? content : excerpt;

			excerptText = excerptText.replace( /(<([^>]+)>)/ig, '' );

			var excerptArray = excerptText.split( ' ' );
			var truncatedExcerpt = excerptArray.splice( 0, Number( excerptLength ) );
			var newExcerpt = truncatedExcerpt.join( ' ' );

			if ( newExcerpt !== excerptText ) {
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

		if ( '' !== excerpt ) {
			if ( settings.full_excerpt ) {
				output = excerpt;
			} else {
				output = truncateExcerpt( false );
			}
		} else if ( settings.use_content ) {
			output = truncateExcerpt( true );
		}

		if ( '' === output ) {
			return false;
		}

		print( '<div class="entry-content"><p>' + output + '</p></div>' );
		#>
		<?php
	}
}
