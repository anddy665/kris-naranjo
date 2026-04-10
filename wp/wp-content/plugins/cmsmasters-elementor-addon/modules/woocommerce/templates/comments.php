<?php
/**
 * The template for displaying comments.
 *
 * @since 1.0.0
 * @since 1.1.0 Added control 'HTML Tag' for both titles.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 */

use CmsmastersElementor\Modules\Woocommerce\Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if ( post_password_required() ) {
	echo '<p class="nocomments">' .
		esc_html__( 'This post is password protected. Enter the password to view comments.', 'cmsmasters-elementor' ) .
	'</p>';

	return;
}

if ( ! function_exists( 'cmsmasters_get_standard_nav' ) ) {
	function cmsmasters_get_standard_nav( $data, $parent_class ) {
		$standard_nav = '';

		if ( 'standard' === $data['settings']['custom_navigation_type'] ) {
			$standard_nav = get_the_comments_navigation( array(
				'prev_text' => cmsmasters_get_standard_nav_item(
					$data['navigation']['prev_icon'],
					$data['navigation']['prev_text'],
					$parent_class
				),
				'next_text' => cmsmasters_get_standard_nav_item(
					$data['navigation']['next_text'],
					$data['navigation']['next_icon'],
					$parent_class
				),
			) );
		}

		return $standard_nav;
	}
}

if ( ! function_exists( 'cmsmasters_get_standard_nav_item' ) ) {
	function cmsmasters_get_standard_nav_item( $first_element, $second_element, $parent_class ) {
		return "<span class=\"{$parent_class}__nav-wrap\">{$first_element}{$second_element}</span>";
	}
}

if ( ! function_exists( 'cmsmasters_get_review_title' ) ) {
	function cmsmasters_get_review_title( $data ) {
		global $product;

		$count = $product->get_review_count();

		if ( ! $count || ! wc_review_ratings_enabled() ) {
			return esc_html__( 'Reviews', 'cmsmasters-elementor' );
		}

		$review_list_title_style = $data['settings']['custom_review_list_title_style'];
		$reviews_title = '';

		if ( 'default' === $review_list_title_style ) {
			$reviews_title = sprintf(
				/* translators: Addon WooCommerce comments template number of reviews for product. 1: Reviews count, 2: Product name */
				esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'cmsmasters-elementor' ) ),
				esc_html( $count ),
				sprintf( '<span>%s</span>', get_the_title() )
			);
		} elseif ( '' !== $review_list_title_style ) {
			$reviews_title = sprintf(
				/* translators: Addon WooCommerce comments template reviews number. %s: Reviews count */
				esc_html( _n( '%s review', '%s reviews', $count, 'cmsmasters-elementor' ) ),
				esc_html( $count )
			);
		}

		return apply_filters( 'woocommerce_reviews_title', $reviews_title, $count, $product );
	}
}

if ( ! function_exists( 'cmsmasters_get_pagination_nav' ) ) {
	function cmsmasters_get_pagination_nav( $data ) {
		$pagination_nav = '';

		if (
			'pagination' === $data['settings']['custom_navigation_type'] &&
			1 < get_comment_pages_count() &&
			get_option( 'page_comments' )
		) {
			$pagination_nav = paginate_comments_links(
				apply_filters( 'woocommerce_comment_pagination_args', array(
					'prev_text' => $data['navigation']['prev_icon'],
					'next_text' => $data['navigation']['next_icon'],
					'type' => 'list',
					'echo' => false,
				) )
			);
		}

		return $pagination_nav;
	}
}

$data = apply_filters( 'cmsmasters_elementor/widgets/cmsmasters-product-review/template_variables', array() );

$parent_class = 'cmsmasters-product-reviews';
$is_user_log_in = is_user_logged_in() ? ' cmsmasters-logged-user' : '';

echo '<div id="reviews" class="' . esc_attr( $parent_class ) . esc_attr( $is_user_log_in ) . '">';

if ( have_comments() ) {
	$review_title_tag = $data['settings']['custom_review_list_title_tag'];

	echo '<div class="' . esc_attr( $parent_class ) . '__wrapper">';

		if ( ! empty( cmsmasters_get_review_title( $data ) ) ) {
			echo '<' . $review_title_tag . ' class="' . esc_attr( $parent_class ) . '__title">' .
				cmsmasters_get_review_title( $data ) .
			'</' . $review_title_tag . '>';
		}

		echo cmsmasters_get_standard_nav( $data, $parent_class ) .

		'<ol class="commentlist ' . esc_attr( $parent_class ) . '__list">' .
			wp_list_comments(
				apply_filters( 'woocommerce_product_review_list_args', array(
					'callback' => array( Module::instance(), 'product_review' ),
					'echo' => false,
				) )
			) .
		'</ol>' .
		cmsmasters_get_standard_nav( $data, $parent_class ) .
		'<nav class="cmsmasters-pagination">' .
			cmsmasters_get_pagination_nav( $data ) .
		'</nav>' .
	'</div>';
}

if (
	! comments_open() &&
	get_comments_number() &&
	post_type_supports( get_post_type(), 'comments' )
) {
	echo '<h5 class="no-comments ' . esc_attr( $parent_class ) . '_closed">' .
		esc_html__( 'Reviews are closed.', 'cmsmasters-elementor' ) .
	'</h5>';
}

$is_placeholder = 'yes' === $data['settings']['custom_use_label_instead_placeholder'];
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
	'</p></div>',
);

if ( '1' === get_option( 'show_comments_cookies_opt_in' ) ) {
	$cookies_consent_uniqid = uniqid( 'comment-form-cookies-consent-' );

	$form_fields['cookies'] = '<p class="comment-form-cookies-consent">' .
		'<input id="' . $cookies_consent_uniqid . '" type="checkbox" name="comment-form-cookies-consent" value="yes"' . ( empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"' ) . ' />' .
		'<label for="' . $cookies_consent_uniqid . '">' .
			esc_html__( 'Save my name and email in this browser for the next time I review.', 'cmsmasters-elementor' ) .
		'</label>' .
	'</p>';
}

if ( wc_review_ratings_enabled() ) {
	$comment_field = '<div class="comment-form-rating">
		<label for="rating">' .
			esc_html__( 'Your rating', 'cmsmasters-elementor' ) .
		'</label>
		<select id="rating" name="rating" required>
			<option value="">' . esc_html__( 'Rate&hellip;', 'cmsmasters-elementor' ) . '</option>
			<option value="5">' . esc_html__( 'Perfect', 'cmsmasters-elementor' ) . '</option>
			<option value="4">' . esc_html__( 'Good', 'cmsmasters-elementor' ) . '</option>
			<option value="3">' . esc_html__( 'Average', 'cmsmasters-elementor' ) . '</option>
			<option value="2">' . esc_html__( 'Not that bad', 'cmsmasters-elementor' ) . '</option>
			<option value="1">' . esc_html__( 'Very poor', 'cmsmasters-elementor' ) . '</option>
		</select>
	</div>';
}

$rating_text = ( '' !== $data['settings']['custom_add_review_text'] ) ?
	$data['settings']['custom_add_review_text'] :
	__( 'Add Review', 'cmsmasters-elementor' );

if ( 'yes' === $data['settings']['custom_add_review_hide'] ) {
	$rating_text = '';
}

$review_text = ( ! $data['review_text'] ) ?
	esc_attr__( 'Review', 'cmsmasters-elementor' ) :
	$data['review_text'];

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

$add_review_tag = $data['settings']['custom_add_review_tag'];
$comment_uniqid = uniqid( 'comment-' );

comment_form( array(
	'fields' => apply_filters( 'comment_form_default_fields', $form_fields ),
	'title_reply' => $rating_text,
	'title_reply_before' => '<' . $add_review_tag . ' id="reply-title" class="comment-reply-title ">',
	'title_reply_after' => '</' . $add_review_tag . '>',
	'comment_field' => $comment_field .
		'<p class="comment-form-comment">' .
			'<label for="' . $comment_uniqid . '"' . ( $is_placeholder ? ' class="screen-reader-text"' : '' ) . '>' .
				$review_text .
			'</label>' .
			'<textarea id="' . $comment_uniqid . '" name="comment" cols="67" ' .
				'rows="' . $data['settings']['custom_form_elements_comment_rows']['size'] . '" ' .
				'placeholder="' . $review_text .
			'"></textarea>' .
			( ( $is_placeholder ) ? $data['icon']['review'] : '' ) .
		'</p>',
	'label_submit' => $submit_text,
	'submit_field' => '<p class="form-submit">%1$s %2$s</p>',
	'submit_button' => '<button name="%1$s" type="submit" id="%2$s" class="%3$s"' . ( 'icon' === $custom_submit_button_type ? ' aria-label="Submit button"' : '' ) . '>' .
		$submit_icon_before . '%4$s' . $submit_icon_after .
	'</button>',
) );

echo '</div>';
