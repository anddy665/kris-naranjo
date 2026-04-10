<?php
/**
 * The template for displaying comments.
 *
 * @since 1.0.0
 * @since 1.1.0 `custom_comments_number` method moved to module file to add redeclare possibility.
 * Added control 'HTML Tag' for both titles.
 * @since 1.2.4 Added a wrapper for the comments of the form so that the `width control` would work.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 */

use CmsmastersElementor\Modules\TemplatePages\Module;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( post_password_required() ) {
	echo '<p class="nocomments">' .
		esc_html__( 'This post is password protected. Enter the password to view comments.', 'cmsmasters-elementor' ) .
	'</p>';

	return;
}

$data = apply_filters( 'cmsmasters_elementor/widgets/cmsmasters-post-comments/template_variables', array() );

$parent_class = 'cmsmasters-single-post-comments';
$is_user_log_in = is_user_logged_in() ? ' cmsmasters-logged-user' : '';
$comment_anchor = 'comments';

echo '<div id="' . esc_attr( $comment_anchor ) . '" class="' . esc_attr( $parent_class ) . esc_attr( $is_user_log_in ) . '">';

add_filter( 'comments_number', array( Module::instance(), 'custom_comments_number' ), 10, 2 );

if ( have_comments() ) {
	$comments_nav = get_the_comments_navigation( array(
		'prev_text' => '<span class="' . esc_attr( $parent_class ) . '__nav-wrap">' .
			$data['navigation']['prev_icon'] .
			$data['navigation']['prev_text'] .
		'</span>',
		'next_text' => '<span class="' . esc_attr( $parent_class ) . '__nav-wrap">' .
			$data['navigation']['next_text'] .
			$data['navigation']['next_icon'] .
		'</span>',
	) );

	$comment_title_tag = $data['settings']['custom_comment_title_tag'];

	echo '<' . $comment_title_tag . ' class="' . esc_attr( $parent_class ) . '__title">' .
		'<span>' . $data['icon']['comment_title'] . get_comments_number_text() . '</span>' .
	'</' . $comment_title_tag . '>' .
	wp_kses_post( $comments_nav ) .
	'<ol class="commentlist ' . esc_attr( $parent_class ) . '__list">' .
		wp_list_comments( array(
			'type' => 'comment',
			'callback' => array( Module::instance(), 'cmsmasters_single_comment' ),
			'echo' => false,
		) ) .
	'</ol>' .
	wp_kses_post( $comments_nav );
}

if (
	! comments_open() &&
	get_comments_number() &&
	post_type_supports( get_post_type(), 'comments' )
) {
	echo '<h5 class="no-comments ' . esc_attr( $parent_class ) . '_closed">' .
		esc_html__( 'Comments are closed.', 'cmsmasters-elementor' ) .
	'</h5>';
}

$email_end = '';
$website_out = '';

$is_placeholder = 'yes' === $data['settings']['custom_use_label_instead_placeholder'];
$website_text = Utils::get_if_not_empty( $data['settings'], 'custom_website_text', esc_attr__( 'Website', 'cmsmasters-elementor' ) );
$website_uniqid = uniqid( 'url-' );

if ( 'yes' === $data['settings']['custom_website_input'] ) {
	$website_out = '<p class="comment-form-url">' .
		'<label for="' . $website_uniqid . '"' . ( $is_placeholder ? ' class="screen-reader-text"' : '' ) . '>' .
			$data['icon']['website'] . $website_text .
		'</label>' .
		'<input id="' . $website_uniqid . '" type="url" name="url"
			value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="35"
			placeholder="' . ( $is_placeholder ? $website_text : '' ) .
		'" />' .
		( $is_placeholder ? $data['icon']['website'] : '' ) .
	'</p></div>';
} else {
	$email_end = '</div>';
}

$is_required = ( ( $req ) ? ' *' : '' );
$name_text = ( ( ! $data['name_text'] ) ? esc_attr__( 'Name', 'cmsmasters-elementor' ) : $data['name_text'] );
$email_text = ( ( ! $data['email_text'] ) ? esc_attr__( 'Email', 'cmsmasters-elementor' ) : $data['email_text'] );
$author_uniqid = uniqid( 'author-' );
$email_uniqid = uniqid( 'email-' );

$form_fields = array(
	'author' => '<div class="cmsmasters-input-wrap"><p class="comment-form-author">' .
		'<label for="' . $author_uniqid . '"' . ( $is_placeholder ? ' class="screen-reader-text"' : '' ) . '>' .
			$data['icon']['name'] . $name_text . $is_required .
		'</label>' .
		'<input id="' . $author_uniqid . '" type="text" name="author"
			value="' . esc_attr( $commenter['comment_author'] ) . '"
			size="35"' . ( ( isset( $aria_req ) ) ? $aria_req : '' ) .
			' placeholder="' . ( ( $is_placeholder ) ? $name_text . $is_required : '' ) .
		'" />' .
		( ( $is_placeholder ) ? $data['icon']['name'] : '' ) .
	'</p>',
	'email' => '<p class="comment-form-email">' .
		'<label for="' . $email_uniqid . '"' . ( $is_placeholder ? ' class="screen-reader-text"' : '' ) . '>' .
			$data['icon']['email'] . $email_text . $is_required .
		'</label>' .
		'<input id="' . $email_uniqid . '" type="text" name="email"
			value="' . esc_attr( $commenter['comment_author_email'] ) . '"
			size="35"' . ( ( isset( $aria_req ) ) ? $aria_req : '' ) .
			' placeholder="' . ( ( $is_placeholder ) ? $email_text . $is_required : '' ) .
		'" />' .
		( ( $is_placeholder ) ? $data['icon']['email'] : '' ) .
	'</p>' . $email_end,
	'url' => $website_out,
);

if ( '1' === get_option( 'show_comments_cookies_opt_in' ) ) {
	$cookies_consent_uniqid = uniqid( 'comment-form-cookies-consent-' );

	$form_fields['cookies'] = '<p class="comment-form-cookies-consent">' .
		'<input id="' . $cookies_consent_uniqid . '" type="checkbox" name="comment-form-cookies-consent" value="yes"' . ( empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"' ) . ' />' .
		'<label for="' . $cookies_consent_uniqid . '">' .
			esc_html__( 'Save my name, email, and website in this browser for the next time I comment.', 'cmsmasters-elementor' ) .
		'</label>' .
	'</p>';
}

$reply_text = ( '' !== $data['settings']['custom_leave_reply_text'] ) ? // TODO: why custom_leave_reply_text? maybe leave_reply_text?
	$data['settings']['custom_leave_reply_text'] :
	__( 'Leave A Reply', 'cmsmasters-elementor' );

$reply_to_text = ( '' !== $data['settings']['custom_leave_reply_to_text'] ) ? // TODO: same as above
	$data['settings']['custom_leave_reply_to_text'] :
	__( 'Leave A Reply to %s', 'cmsmasters-elementor' ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment

$comment_text = ( ( ! $data['comment_text'] ) ? esc_attr__( 'Comment', 'cmsmasters-elementor' ) . $is_required : $data['comment_text'] . $is_required );

$custom_submit_button_type = ( isset( $data['settings']['custom_submit_button_type'] ) ? $data['settings']['custom_submit_button_type'] : '' );
$submit_text = ( 'icon' !== $custom_submit_button_type ) ? $data['submit_text'] : '';
$submit_icon = $data['icon']['submit'];

if ( 'start' === $data['settings']['custom_submit_button_icon_position'] ) {
	$submit_icon_before = $submit_icon;
	$submit_icon_after = '';
} else {
	$submit_icon_after = $submit_icon;
	$submit_icon_before = '';
}

$leave_reply_tag = $data['settings']['custom_leave_reply_tag'];

echo '<div class="cmsmasters-respond-wrapper">';

if ( 'row' !== $data['settings']['custom_comment_direction'] ) {
	echo '<div class="cmsmasters-reviews-wrapper">';
}

$comment_uniqid = uniqid( 'comment-' );

comment_form( array(
	'fields' => apply_filters( 'comment_form_default_fields', $form_fields ),
	'title_reply' => $reply_text,
	'title_reply_to' => $reply_to_text,
	'title_reply_before' => '<' . $leave_reply_tag . ' id="reply-title" class="comment-reply-title ">' . $data['icon']['form_title'],
	'title_reply_after' => '</' . $leave_reply_tag . '>',
	'comment_field' => '<p class="comment-form-comment">' .
		'<label for="' . $comment_uniqid . '"' . ( $is_placeholder ? ' class="screen-reader-text"' : '' ) . '>' .
			$data['icon']['comment'] . $comment_text .
		'</label>' .
		'<textarea id="' . $comment_uniqid . '" name="comment" cols="67"
			rows="' . $data['settings']['custom_form_elements_comment_rows']['size'] . '"
			placeholder="' . $comment_text .
		'"></textarea>' .
		( ( $is_placeholder ) ? $data['icon']['comment'] : '' ) .
	'</p>',
	'label_submit' => $submit_text,
	'submit_button' => '<button name="%1$s" type="submit" id="%2$s" class="%3$s"' . ( 'icon' === $custom_submit_button_type ? ' aria-label="Submit button"' : '' ) . '>' .
		$submit_icon_before . '%4$s' . $submit_icon_after .
	'</button>',
) );

if ( 'row' !== $data['settings']['custom_comment_direction'] ) {
	echo '</div>';
}

echo '</div>
</div>';
