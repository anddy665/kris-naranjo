<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( post_password_required() ) {
	echo '<p class="cmsmasters-nocomments nocomments">' . esc_html__( 'This post is password protected. Enter the password to view comments.', 'pediatric-clinic' ) . '</p>';

	return;
}

$parent_class = 'cmsmasters-single-comments';

echo '<div class="' . esc_attr( $parent_class ) . ' cmsmasters-section-container">';

$pings = get_comments( array(
	'type' => 'pings',
	'post_id' => get_the_ID(),
) );

if ( sizeof( $pings ) > 0 ) {
	echo '<div class="cmsmasters-pings">' .
		'<h4>' . esc_html__( 'Trackbacks and Pingbacks', 'pediatric-clinic' ) . '</h4>' .
		'<ol class="pingslist cmsmasters-pings-list">';

	wp_list_comments( array(
		'short_ping' => true,
		'echo' => true,
	), $pings );

	echo '</ol>' .
	'</div>';
}

if ( have_comments() ) {
	$comments_nav = get_the_comments_navigation( array(
		'prev_text' => '<span class="' . esc_attr( $parent_class ) . '__nav-arrow cmsmasters-theme-icon-comments-nav-prev"></span><span class="' . esc_attr( $parent_class ) . '__nav-text">' . esc_attr__( 'Older Comments', 'pediatric-clinic' ) . '</span>',
		'next_text' => '<span class="' . esc_attr( $parent_class ) . '__nav-text">' . esc_attr__( 'Newer Comments', 'pediatric-clinic' ) . '</span><span class="' . esc_attr( $parent_class ) . '__nav-arrow cmsmasters-theme-icon-comments-nav-next"></span>',
	) );

	echo '<h4 class="' . esc_attr( $parent_class ) . '__title">' .
			get_comments_number_text() .
		'</h4>' .
		wp_kses_post( $comments_nav ) .
		'<ol class="commentlist ' . esc_attr( $parent_class ) . '__list">' .
			wp_list_comments( array(
				'type' => 'comment',
				'callback' => 'cmsmasters_single_comment_callback',
				'echo' => false,
			) ) .
		'</ol>' .
		wp_kses_post( $comments_nav );
}

if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) {
	echo '<h5 class="no-comments ' . esc_attr( $parent_class ) . '_closed">' . esc_html__( 'Comments are closed.', 'pediatric-clinic' ) . '</h5>';
}

$form_fields = array(
	'author' => '<p class="comment-form-author">' .
		'<input type="text" id="author" name="author" value="' . esc_attr( $commenter['comment_author'] ) . '" size="35"' . ( ( isset( $aria_req ) ) ? $aria_req : '' ) . ' placeholder="' . esc_attr__( 'Your name', 'pediatric-clinic' ) . ( ( $req ) ? ' *' : '' ) . '" />' .
	'</p>',
	'email' => '<p class="comment-form-email">' .
		'<input type="text" id="email" name="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="35"' . ( ( isset( $aria_req ) ) ? $aria_req : '' ) . ' placeholder="' . esc_attr__( 'Your email', 'pediatric-clinic' ) . ( ( $req ) ? ' *' : '' ) . '" />' .
	'</p>',
);

if ( '1' === get_option( 'show_comments_cookies_opt_in' ) ) {
	$form_fields['cookies'] = '<p class="comment-form-cookies-consent">' .
		'<input type="checkbox" id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" value="yes"' . ( empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"' ) . ' />' .
		'<label for="wp-comment-cookies-consent">' . esc_html__( 'Save my name, email, and website in this browser for the next time I comment.', 'pediatric-clinic' ) . '</label>' .
	'</p>';
}

comment_form( array(
	'fields' => apply_filters( 'comment_form_default_fields', $form_fields ),
	'comment_field' => '<p class="comment-form-comment">' .
		'<textarea name="comment" id="comment" cols="67" rows="5" placeholder="' . esc_attr__( 'Comment', 'pediatric-clinic' ) . '"></textarea>' .
	'</p>',
) );

echo '</div>';

function cmsmasters_single_comment_callback( $comment, $args, $depth ) {
	$parent_class = 'cmsmasters-single-comment';

	echo '<li id="li-comment-' . get_comment_ID() . '" class="' . join( ' ', get_comment_class( $parent_class ) ) . '">' .
		'<div id="comment-' . get_comment_ID() . '" class="' . esc_attr( $parent_class ) . '__body comment-body">' .
			'<figure class="' . esc_attr( $parent_class ) . '__avatar">' .
				get_avatar( $comment, 70 ) .
			'</figure>' .
			'<div class="' . esc_attr( $parent_class ) . '__outer">' .
				'<div class="' . esc_attr( $parent_class ) . '__info">' .
					'<h5 class="' . esc_attr( $parent_class ) . '__title fn">' . get_comment_author_link() . '</h5>' .
					'<abbr class="' . esc_attr( $parent_class ) . '__date published" title="' . get_comment_date() . '">' . get_comment_date() . '</abbr>' .
				'</div>' .
				'<div class="' . esc_attr( $parent_class ) . '__content comment-content">';

					comment_text();

	if ( '0' === $comment->comment_approved ) {
		echo '<p>' .
			'<em>' . esc_html__( 'Your comment is awaiting moderation.', 'pediatric-clinic' ) . '</em>' .
		'</p>';
	}

	echo '</div>' .
	'<div class="' . esc_attr( $parent_class ) . '__reply">' .
		get_comment_reply_link( array_merge( $args, array(
			'depth' => $depth,
			'max_depth' => $args['max_depth'],
			'reply_text' => esc_attr__( 'Reply', 'pediatric-clinic' ),
		) ) );

		edit_comment_link( esc_attr__( 'Edit', 'pediatric-clinic' ), '', '' );

	echo '</div>' .
	'</div>' .
	'</div>';
}
