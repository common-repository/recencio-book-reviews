<?php

$author             = get_the_author();
$author_description = wpautop( get_the_author_meta( 'description' ) );
$author_url         = get_the_author_meta( 'url' );
$author_archive     = get_author_posts_url( get_the_author_meta( 'ID' ) );
$gravatar           = apply_filters( 'rcno_author_box_gravatar', get_avatar_url( get_the_author_meta( 'user_email' ) ), (int) get_the_author_meta( 'ID' ) );

$facebook_url = get_the_author_meta( 'facebook' );
$twitter_url  = get_the_author_meta( 'twitter' );
$google_url   = get_the_author_meta( 'google' );
$linkedin_url = get_the_author_meta( 'linkedin' );
$tumblr_url   = get_the_author_meta( 'tumblr' );

$out = '';
$out .= '<div class="' . $this->id . ' rcno-author-box-container">';

$out .= '<div class="rcno-author-box-gravatar-container">';
$out .= '<img class="rcno-author-box-gravatar" src="' . $gravatar . '" alt="the gravatar profile photo">';
$out .= '</div>'; // .rcno-author-box-gravatar-container

$out .= '<div class="rcno-author-box-bio-container">';
$out .= '<h3>' . $this->get_setting( 'author_box_title' ) . ' ' . $author . '</h3>';
$out .= $author_description;

$out .= '<div class="rcno-author-social-icons">';

if ( '' !== $facebook_url ) { // Facebook
	$out .= '<a href="' . esc_url( $facebook_url ) . '" rel="noreferrer noopener" ';
	$out .= 'style="background: ' . esc_attr( $this->get_setting( 'facebook_color' ) ) . ';" >';
	$out .= '<span class="social-icon socicon-facebook"></span>';
	$out .= '</a>';
}

if ( '' !== $twitter_url ) { // Twitter
	$out .= '<a href="https://twitter.com/' . esc_attr( $twitter_url ) . '" rel="noreferrer noopener" ';
	$out .= 'style="background: ' . esc_attr( $this->get_setting( 'twitter_color' ) ) . ';" >';
	$out .= '<span class="social-icon socicon-twitter"></span>';
	$out .= '</a>';
}

if ( '' !== $google_url ) { // Google+
	$out .= '<a href="' . esc_url( $google_url ) . '" rel="noreferrer noopener" ';
	$out .= 'style="background: ' . esc_attr( $this->get_setting( 'google_color' ) ) . ';" >';
	$out .= '<span class="social-icon socicon-google-plus"></span>';
	$out .= '</a>';
}

if ( '' !== $linkedin_url ) { // LinkedIn
	$out .= '<a href="' . esc_url( $linkedin_url ) . '" rel="noreferrer noopener" ';
	$out .= 'style="background: ' . esc_attr( $this->get_setting( 'linkedin_color' ) ) . ';" >';
	$out .= '<span class="social-icon socicon-linkedin"></span>';
	$out .= '</a>';
}

if ( '' !== $tumblr_url ) { // Tumblr
	$out .= '<a href="' . esc_url( $tumblr_url ) . '" rel="noreferrer noopener" ';
	$out .= 'style="background: ' . esc_attr( $this->get_setting( 'tumblr_color' ) ) . ';" >';
	$out .= '<span class="social-icon socicon-tumblr"></span>';
	$out .= '</a>';
}

$out .= '</div>'; // .rcno-author-archive-link

$out .= '<div class="rcno-author-archive-link">';
$out .= '<a href="' . $author_archive . '" rel="author">';
$out .= sprintf( '%s %s &rarr;', __( 'View all reviews by', 'recencio-book-reviews' ), $author );
$out .= '</a>';
$out .= '</div>'; // .rcno-author-archive-link

$out .= '</div>'; // .rcno-author-box-bio-container
$out .= '</div>'; // .rcno-author-box-container

echo $out;
