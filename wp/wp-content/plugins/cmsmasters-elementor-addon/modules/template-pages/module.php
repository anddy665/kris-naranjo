<?php
namespace CmsmastersElementor\Modules\TemplatePages;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\TemplateDocuments\Module as DocumentsModule;
use CmsmastersElementor\Modules\TemplatePages\Documents;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;

use function MailPoetVendor\Symfony\Component\DependencyInjection\Loader\Configurator\ref;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * CMSMasters template pages module.
 *
 * CMSMasters template pages module handler class is responsible for registering
 * and managing Elementor templates library page document types.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	/**
	 * Post excerpt default length.
	 */
	const EXCERPT_LENGTH = 55;

	/**
	 * Post excerpt default more text.
	 */
	const EXCERPT_MORE = ' &hellip;';

	/**
	 * Maximum post excerpt content break length.
	 */
	const MAX_CONTENT_LENGTH = 250;

	/**
	 * Post excerpt content break text.
	 */
	const CONTENT_BREAK = '{{CMSMS_CONTENT_BREAK}}';

	/**
	 * Get module name.
	 *
	 * Retrieve the elementor module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'template-pages';
	}

	/**
	 * Module activation.
	 *
	 * Check if module is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_active() {
		return class_exists( DocumentsModule::class );
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_widgets() {
		return array(
			'Heading',
			'Icon_List',

			'Post_Title',
			'Page_Title',

			'Archive_Title',
			'Archive_Description',

			'Post_Excerpt',
			'Post_Featured_Image',
			'Post_Media',

			'Post_Navigation',
			'Post_Navigation_Fixed',
			'Author_Box',
			'Post_Comments',

			'Post_Content',
		);
	}

	/**
	 * Add filters initialization.
	 *
	 * Register filters for the Template Pages module.
	 *
	 * @since 1.0.0
	 */
	protected function init_filters() {
		// Common
		add_filter( 'cmsmasters_elementor/documents/set_document_types', array( $this, 'set_document_types' ) );
		add_filter( 'cmsmasters_elementor/documents/set_elementor_documents', array( $this, 'set_elementor_documents' ) );

		if ( function_exists( 'pmpro_is_plugin_active' ) ) {
			remove_filter('the_excerpt', 'pmpro_membership_excerpt_filter', 15);
		}
	}

	public function set_document_types( $document_types ) {
		$module_document_types = array(
			'cmsmasters_singular' => Documents\Singular::get_class_full_name(),
			'cmsmasters_archive' => Documents\Archive::get_class_full_name(),
			'page' => Documents\Elementor\Page::get_class_full_name(),
			'wp-post' => Documents\Wordpress\Post::get_class_full_name(),
			'wp-page' => Documents\Wordpress\Page::get_class_full_name(),
		);

		$document_types = array_merge( $document_types, $module_document_types );

		return $document_types;
	}

	public function set_elementor_documents( $elementor_documents ) {
		$elementor_documents[] = 'page';
		$elementor_documents[] = 'wp-post';
		$elementor_documents[] = 'wp-page';

		return $elementor_documents;
	}

	/**
	 * Add actions initialization.
	 *
	 * Register actions for the Template Documents module.
	 *
	 * @since 1.0.0
	 */
	protected function init_actions() {
		add_action( 'show_user_profile', array( $this, 'add_customer_meta_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'add_customer_meta_fields' ) );

		add_action( 'personal_options_update', array( $this, 'save_customer_meta_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_customer_meta_fields' ) );
	}

	public function get_customer_meta_fields() {
		$show_fields = apply_filters( 'customer_meta_fields', array(
			'social_media' => array(
				'title' => __( 'Social Media', 'cmsmasters-elementor' ),
				'fields' => array(
					'facebook' => array(
						'label' => __( 'Facebook', 'cmsmasters-elementor' ),
						'description' => '',
					),
					'vk' => array(
						'label' => __( 'VK', 'cmsmasters-elementor' ),
						'description' => '',
					),
					'pinterest' => array(
						'label' => __( 'Pinterest', 'cmsmasters-elementor' ),
						'description' => '',
					),
					'instagram' => array(
						'label' => __( 'Instagram', 'cmsmasters-elementor' ),
						'description' => '',
					),
				),
			),
		) );

		return $show_fields;
	}

	/**
	 * Show Address Fields on edit user pages.
	 *
	 * @param WP_User $user
	 */
	public function add_customer_meta_fields( $user ) {
		if ( ! apply_filters( 'current_user_can_edit_custom_meta_fields', current_user_can( 'manage_options' ), $user->ID ) ) {
			return;
		}

		$show_fields = $this->get_customer_meta_fields();

		foreach ( $show_fields as $fieldset_key => $fieldset ) {
			?>
			<h2><?php echo $fieldset['title']; ?></h2>
			<table class="form-table" id="<?php echo esc_attr( "fieldset-{$fieldset_key}" ); ?>">
			<?php foreach ( $fieldset['fields'] as $key => $field ) { ?>
				<tr>
					<th>
						<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
					</th>
					<td>
					<?php
						$field_class = 'regular-text';

					if ( ! empty( $field['class'] ) ) {
						$field_class = $field['class'];
					}

						$value = $this->get_user_meta( $user->ID, $key );

						echo '<input type="text"' .
							' name="' . esc_attr( $key ) . '"' .
							' id="' . esc_attr( $key ) . '"' .
							' value="' . esc_attr( $value ) . '"' .
							' class="' . esc_attr( $field_class ) . '" />';
					?>
						<p class="description"><?php echo wp_kses_post( $field['description'] ); ?></p>
					</td>
				</tr>
			<?php } ?>
			</table>
			<?php
		}
	}

	/**
	 * Clear data.
	 *
	 * Clean variables using sanitize_text_field. Arrays are cleaned recursively. Non-scalar values are ignored.
	 *
	 * @param string|array $var User ID of the user being saved
	 *
	 * @since 1.0.0
	 */
	public function clean( $var ) {
		if ( is_array( $var ) ) {
			return array_map( 'clean', $var );
		} else {
			return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
		}
	}

	/**
	 * Save Address Fields on edit user pages.
	 *
	 * @param int $user_id User ID of the user being saved
	 *
	 * @since 1.0.0
	 */
	public function save_customer_meta_fields( $user_id ) {
		if ( ! apply_filters( 'current_user_can_edit_custom_meta_fields', current_user_can( 'manage_options' ), $user_id ) ) {
			return;
		}

		$fields_to_save = $this->get_customer_meta_fields();

		foreach ( $fields_to_save as $fieldset ) {
			foreach ( $fieldset['fields'] as $meta_key => $field ) {
				$meta_key_value = $_POST[ $meta_key ];
				$is_meta_key_value = isset( $meta_key_value );

				if ( isset( $field['type'] ) && 'checkbox' === $field['type'] ) {
					update_user_meta( $user_id, $meta_key, $is_meta_key_value );
				} elseif ( $is_meta_key_value ) {
					update_user_meta( $user_id, $meta_key, $this->clean( $meta_key_value ) );
				}
			}
		}
	}

	/**
	 * Get user meta.
	 *
	 * Get user meta for a given key, with fallbacks to
	 * core user info for pre-existing fields.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id The ID of the user being edited.
	 * @param string $key User meta field key.
	 *
	 * @return string User meta for a given key.
	 */
	protected function get_user_meta( $user_id, $key ) {
		return get_user_meta( $user_id, $key, true );
	}

	/**
	 * Undocumented function
	 *
	 * @param Controls_Stack $instance
	 */
	public function add_global_excerpt_controls( $instance ) {
		$instance->add_control(
			'excerpt_length',
			array(
				'label' => __( 'Length', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 10,
				'max' => 250,
				'placeholder' => 55,
				'condition' => array( 'full_excerpt!' => 'yes' ),
			)
		);

		$instance->add_control(
			'full_excerpt', array(
				'label' => __( 'Full Excerpt Length', 'cmsmasters-elementor' ),
				'description' => __( 'Full excerpt text will be shown with no word number limit applied.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
			)
		);

		$instance->add_control(
			'full_excerpt_message', array(
				'raw' => sprintf(
					'<strong>%1$s</strong> %2$s',
					__( 'Please note:', 'cmsmasters-elementor' ),
					sprintf(
						/* translators: Addon 'Post Excerpt' widget 'Full Excerpt Length' control admin notice. %d: Maximum words count  */
						__( 'This option affects only post excerpt. Excerpt generated from content will still be limited by %d words.', 'cmsmasters-elementor' ),
						self::get_excerpt_length()
					)
				),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type' => 'ui',
			)
		);

		$instance->add_control(
			'use_content', array(
				'label' => __( 'Generate From Content', 'cmsmasters-elementor' ),
				'description' => __( 'If excerpt field is empty, excerpt will be generated from content.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
			)
		);

		$instance->add_control(
			'use_content_message', array(
				'raw' => sprintf(
					'<strong>%1$s</strong> %2$s',
					__( 'Please note:', 'cmsmasters-elementor' ),
					__( 'Excerpt generated from content will not be shown on open post.', 'cmsmasters-elementor' )
				),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type' => 'ui',
			)
		);

		$instance->add_control(
			'excerpt_more',
			array(
				'label' => __( 'More', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( '...', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'full_excerpt',
							'operator' => '!==',
							'value' => 'yes',
						),
						array(
							'terms' => array(
								array(
									'name' => 'full_excerpt',
									'value' => 'yes',
								),
								array(
									'name' => 'use_content',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);
	}

	/**
	 * Get excerpt length.
	 *
	 * Retrieves post except maximum content cut length.
	 *
	 * Fired by `excerpt_length` WordPress filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @return int Content cut length.
	 */
	public static function get_excerpt_length() {
		/**
		 * Filters post except maximum content cut length.
		 *
		 * @since 1.0.0
		 *
		 * @param int $length Default content cut length.
		 */
		$max_content_length = apply_filters( 'cmsmasters_elementor/post_excerpt/content_cut_length', self::MAX_CONTENT_LENGTH );

		return (int) $max_content_length;
	}

	/**
	 * Set excerpt settings.
	 *
	 * Retrieves post except maximum content cut length.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Settings.
	 *
	 * @return int Content cut length.
	 */
	public static function set_excerpt_settings( $settings ) {
		$length = $settings['excerpt_length'];
		$more = $settings['excerpt_more'];

		if ( '' === $length ) {
			$length = self::EXCERPT_LENGTH;
		}

		if ( is_null( $length ) || $settings['full_excerpt'] ) {
			$length = self::get_excerpt_length();
		}

		if ( '' === $more ) {
			$more = self::EXCERPT_MORE;
		}

		return array(
			$length,
			$more,
		);
	}

	/**
	 * Get custom excerpt.
	 *
	 * Retrieves post excerpt with custom length.
	 *
	 * @since 1.0.0
	 *
	 * @param int $length Excerpt length.
	 * @param string $more Excerpt `more` text.
	 * @param string $post_excerpt Post excerpt text.
	 *
	 * @return string Post excerpt with custom length.
	 */
	public static function get_custom_excerpt( $length, $more, $post_excerpt = false ) {
		if ( ! $post_excerpt ) {
			$post_excerpt = self::get_wp_trim_excerpt( '', $more );
		}

		if ( '' === $post_excerpt ) {
			return '';
		}

		$excerpt = wp_trim_words( $post_excerpt, $length, $more );

		return $excerpt;
	}

	/**
	 * Get WordPress trim excerpt.
	 *
	 * Generates an excerpt from the content, if needed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text The excerpt text.
	 * @param string $more The excerpt more.
	 * @param \WP_Post|object|int $post WP_Post instance or Post ID/object.
	 *
	 * @return string The excerpt.
	 */
	public static function get_wp_trim_excerpt( $text = '', $more = ' [&hellip;]', $post = null ) {
		if ( '' !== trim( $text ) ) {
			return $text;
		}

		$text = get_the_content( '', false, $post );

		$text = strip_shortcodes( $text );
		$text = excerpt_remove_blocks( $text );

		$text = str_replace( ']]>', ']]&gt;', $text );

		return wp_trim_words( $text, self::get_excerpt_length(), $more );
	}

	/**
	 * Get post comment HTML.
	 *
	 * Uses as replacement to WordPress comment method.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added separate block for background color.
	 */
	public function cmsmasters_single_comment( $comment, $args, $depth ) {
		$parent_class = 'cmsmasters-single-post-comment';

		$data = apply_filters( 'cmsmasters_elementor/widgets/cmsmasters-post-comments/template_variables', array() );

		$comment_meta = $data['comment_meta'];

		$post = get_post();

		$edit_text = $this->get_edit_text( $data );

		if ( $comment->user_id === $post->post_author ) {
			$post_author = $comment_meta['post_author'];
		} else {
			$post_author = '';
		}

		echo '<li id="li-comment-' . get_comment_ID() . '" class="' . join( ' ', get_comment_class( $parent_class ) ) . '">' .
			'<div id="comment-' . get_comment_ID() . '" class="' . esc_attr( $parent_class ) . '__body comment-body">' .
			'<div class="' . esc_attr( $parent_class ) . '__bg"></div>' .
				'<div class="' . esc_attr( $parent_class ) . '__outer">' .
					'<div class="' . esc_attr( $parent_class ) . '__info">' .
						'<figure class="' . esc_attr( $parent_class ) . '__avatar">' .
							get_avatar( $comment->comment_author_email, 170, get_option( 'avatar_default' ) ) .
						'</figure>' .
						'<div class="' . esc_attr( $parent_class ) . '__info-inner">' .
							'<h4 class="' . esc_attr( $parent_class ) . '__author fn">' .
								get_comment_author_link() . $post_author . $comment_meta['author_text_after'] .
							'</h4>';

		if ( 'inline' === $data['settings']['custom_date_position'] ) {
			$this->get_date_html( $comment_meta, $parent_class );
		}

		if ( 'inline' === $data['settings']['custom_reply_position'] && 'default' === $data['settings']['custom_button_position'] ) {
			$this->get_reply_html( $data, $parent_class, $args, $depth );
		}

		if ( 'top' === $data['settings']['custom_button_position'] ) {
			$this->get_comments_butons(  $data, $parent_class, $args, $depth, $edit_text );
		}

		echo '</div>';

		if ( 'block' === $data['settings']['custom_date_position'] ) {
			$this->get_date_html( $comment_meta, $parent_class );
		}

		echo '</div>' .
		'<div class="' . esc_attr( $parent_class ) . '__content comment-content">';

		comment_text();

		if ( '0' === $comment->comment_approved ) {
			echo '<p>' .
				'<em>' . esc_html__( 'Your comment is awaiting moderation.', 'cmsmasters-elementor' ) . '</em>' .
			'</p>';
		}

		echo '</div>';

		if ( 'block' === $data['settings']['custom_reply_position'] && 'default' === $data['settings']['custom_button_position'] ) {
			$this->get_reply_html( $data, $parent_class, $args, $depth );
		}

		if ( 'default' === $data['settings']['custom_button_position'] ) {
			echo edit_comment_link( $edit_text, '', '' );
		}

		if ( 'bottom' === $data['settings']['custom_button_position'] ) {
			$this->get_comments_butons(  $data, $parent_class, $args, $depth, $edit_text );
		}

		echo '</div>' .
		'</div>';
	}

	protected function get_comments_butons(  $data, $parent_class, $args, $depth, $edit_text ) {
		echo '<div class="' . esc_attr( $parent_class ) . '__button-wrapper">';
			$this->get_reply_html( $data, $parent_class, $args, $depth );
			edit_comment_link( $edit_text, '', '' );
		echo '</div>';
	}

	protected function get_edit_text( $data ) {
		$edit_to_text = ( '' !== $data['settings']['custom_edit_text'] ) ?
			$data['settings']['custom_edit_text'] :
			__( 'Edit', 'cmsmasters-elementor' ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment

		return $edit_to_text;
	}

	/**
	 * Get comment date html.
	 *
	 * @since 1.0.0
	 *
	 * @param array $comment_meta Array of comment data from widget file.
	 * @param string $parent_class Parent`s widget class.
	 */
	public function get_date_html( $comment_meta, $parent_class ) {
		if ( 'disable' === $comment_meta['date_format'] ) {
			return;
		}

		echo '<div class="' . esc_attr( $parent_class ) . '__date-wrap">' .
			$comment_meta['date_icon'];

		if ( $comment_meta['human_readable'] ) {
			echo $this->smk_get_comment_time( get_comment_ID() );
		} else {
			echo '<abbr class="' . esc_attr( $parent_class ) . '__date published" title="' . get_comment_date() . '">' .
				sprintf(
					'%1$s %3$s %2$s',
					get_comment_date(),
					( 'yes' === $comment_meta['time_enable'] ) ? get_comment_time() : '',
					( 'yes' === $comment_meta['time_enable'] ) ? $comment_meta['date_separator'] : ''
				) .
			'</abbr>';
		}

		echo '</div>';
	}

	/**
	 * Get age of comment in "%s ago" mask.
	 *
	 * @since 1.0.0
	 *
	 * @param string $comment_id ID of current comment.
	 */
	public function smk_get_comment_time( $comment_id ) {
		printf(
			/* translators: Addon comments template get comment time ago text. %s: time ago */
			__( '%s ago', 'cmsmasters-elementor' ),
			human_time_diff(
				get_comment_date( 'U', $comment_id ),
				current_time( 'timestamp' ) // TODO NOTICE "A non well formed numeric value encountered"
			)
		);
	}


	/**
	 * Get reply html for post comments.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added 'cmsmasters-theme-button' class to reply.
	 *
	 * @param array $data Array of data from widget file.
	 * @param string $parent_class Class of the widget.
	 * @param array $args Comment arguments.
	 * @param int $depth Depth of comment.
	 */
	public function get_reply_html( $data, $parent_class, $args, $depth ) {
		$reply_to_text = ( '' !== $data['settings']['custom_leave_reply_to_text'] ) ?
			$data['settings']['custom_leave_reply_to_text'] :
			__( 'Reply to %s', 'cmsmasters-elementor' ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment

		$comment_meta = $data['comment_meta'];

		$button_class = ( 'button' === $data['settings']['custom_reply_view'] ) ? ' cmsmasters-reply-button' : '';

		echo '<div class="' . esc_attr( $parent_class ) . '__reply">' .
			preg_replace( '/comment-reply-link/', 'comment-reply-link' . $button_class,
				get_comment_reply_link( array_merge( $args, array(
					'depth' => $depth,
					'max_depth' => $args['max_depth'],
					'reply_to_text' => $reply_to_text,
					'reply_text' => $comment_meta['reply_icon'] . $comment_meta['reply_text'],
				) ) )
			) .
		'</div>';
	}

	/**
	 * Get custom comments text.
	 *
	 * Retrieves title that can be changed through controls in widget.
	 *
	 * @since 1.1.0 Moved from comments template. Added checking and converting string to int.
	 *
	 * @param string $output Title output.
	 * @param int $number Comments number.
	 *
	 * @return string Title output.
	 */
	public function custom_comments_number( $output, $number ) {
		$data = apply_filters( 'cmsmasters_elementor/widgets/cmsmasters-post-comments/template_variables', array() );

		$number = is_string( $number ) ? intval( $number ) : $number;

		if ( 0 === $number && '' !== $data['comments_meta']['title_text_only'] ) {
			$output = $data['comments_meta']['title_text_only'];
		} elseif ( 1 === $number && '' !== $data['comments_meta']['title_single_text'] ) {
			$output = $data['comments_meta']['title_single_text'];
		} elseif ( 1 < $number && '' !== $data['comments_meta']['title_multiple_text'] ) {
			$output = sprintf( $data['comments_meta']['title_multiple_text'], $number );
		}

		return $output;
	}

}
