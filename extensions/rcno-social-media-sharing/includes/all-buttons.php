<?php

$template          = new Rcno_Template_Tags( 'recencio-book-reviews', '1.14.0' );
$selected_buttons  = explode( ',', strtolower( $this->get_setting( 'social_media_sites' ) ) );
$available_buttons = array();
$button_color      = $this->get_setting( 'buttons_color' );

if ( in_array( 'facebook', $selected_buttons, true ) ) {
	$facebook  = '';
	$facebook .= '<div class="rcno-facebook-button">';
	$facebook .= '<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode( get_the_permalink() ) . '" ';
	$facebook .= 'onclick="window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;" ';
	$facebook .= 'style="background: ' . $button_color . '">';
	$facebook .= '<i class="facebook socicon-facebook"></i>';
	$facebook .= '<p>' . __( 'Facebook', 'recencio-book-reviews' ) . '</p>';
	$facebook .= '</a>';
	$facebook .= '</div>'; // .rcno-facebook-button

	$available_buttons['facebook'] = $facebook;
}

if ( in_array( 'twitter', $selected_buttons, true ) ) {
	$twitter  = '';
	$twitter .= '<div class="rcno-twitter-button">';
	$twitter .= '<a target="_blank" href="https://twitter.com/intent/tweet?url=' . urlencode( get_the_permalink() );
	$twitter .= '&text=' . apply_filters( 'rcno_social_twitter_text', urlencode( the_title_attribute( 'echo=0' ) ) );
	$twitter .= '&via=' . esc_attr( $this->get_setting( 'twitter_username' ) ) . '" ';
	$twitter .= 'onclick="window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;" ';
	$twitter .= 'style="background: ' . $button_color . '">';
	$twitter .= '<i class="twitter socicon-twitter"></i>';
	$twitter .= '<p>' . __( 'Twitter', 'recencio-book-reviews' ) . '</p>';
	$twitter .= '</a>';
	$twitter .= '</div>'; // .rcno-twitter-button

	$available_buttons['twitter'] = $twitter;
}

if ( in_array( 'google+', $selected_buttons, true ) ) {
	$google  = '';
	$google .= '<div class="rcno-google-button">';
	$google .= '<a target="_blank" href="https://plus.google.com/share?url=' . urlencode( get_the_permalink() ) . '" ';
	$google .= 'onclick="window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;" ';
	$google .= 'style="background: ' . $button_color . '">';
	$google .= '<i class="google socicon-google"></i>';
	$google .= '<p>' . __( 'Google+', 'recencio-book-reviews' ) . '</p>';
	$google .= '</a>';
	$google .= '</div>'; // .rcno-google-button

	$available_buttons['google'] = $google;
}

if ( in_array( 'pinterest', $selected_buttons, true ) ) {
	$pinterest  = '';
	$pinterest .= '<div class="rcno-pinterest-button">';
	$pinterest .= '<a data-pin-do="skipLink" target="_blank" href="https://pinterest.com/pin/create/link/?url=' . urlencode( get_the_permalink() );
	$pinterest .= '&media=' . $template->get_the_rcno_book_cover( $this->the_review_id(), 'full', false );
	$pinterest .= '&description=' . the_title_attribute( 'echo=0' ) . '" ';
	$pinterest .= 'onclick="window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;" ';
	$pinterest .= 'style="background: ' . $button_color . '">';
	$pinterest .= '<i class="pinterest socicon-pinterest"></i>';
	$pinterest .= '<p>' . __( 'Pinterest', 'recencio-book-reviews' ) . '</p>';
	$pinterest .= '</a>';
	$pinterest .= '</div>'; // .rcno-pinterest-button

	$available_buttons['pinterest'] = $pinterest;
}

if ( in_array( 'stumbleupon', $selected_buttons, true ) ) {
	$stumbleupon  = '';
	$stumbleupon .= '<div class="rcno-stumble-button">';
	$stumbleupon .= '<a href="https://www.stumbleupon.com/badge/?url=' . urlencode( get_the_permalink() );
	$stumbleupon .= '&title=' . urlencode( the_title_attribute( 'echo=0' ) ) . '" ';
	$stumbleupon .= 'onclick="window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;" ';
	$stumbleupon .= 'style="background: ' . $button_color . '">';
	$stumbleupon .= '<i class="stumbleupon socicon-stumbleupon"></i>';
	$stumbleupon .= '<p>' . __( 'StumbleUpon', 'recencio-book-reviews' ) . '</p>';
	$stumbleupon .= '</a>';
	$stumbleupon .= '</div>'; // .rcno-stumble-button

	$available_buttons['stumbleupon'] = $stumbleupon;
}

if ( in_array( 'tumblr', $selected_buttons, true ) ) {
	$tumblr  = '';
	$tumblr .= '<div class="rcno-tumblr-button">';
	$tumblr .= '<a href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=' . urlencode( get_the_permalink() );
	$tumblr .= '&title=' . urlencode( the_title_attribute( 'echo=0' ) );
	$tumblr .= '&caption=' . strip_tags( $template->get_the_rcno_book_meta( $this->the_review_id(), 'rcno_book_title', '', false ) ) . '" ';
	$tumblr .= 'onclick="window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;" ';
	$tumblr .= 'style="background: ' . $button_color . '">';
	$tumblr .= '<i class="tumblr socicon-tumblr"></i>';
	$tumblr .= '<p>' . __( 'Tumblr', 'recencio-book-reviews' ) . '</p>';
	$tumblr .= '</a>';
	$tumblr .= '</div>';

	$available_buttons['tumblr'] = $tumblr;
}

if ( in_array( 'reddit', $selected_buttons, true ) ) {
	$reddit  = '';
	$reddit .= '<div class="rcno-reddit-button">';
	$reddit .= '<a href="https://reddit.com/submit?url=' . urlencode( get_the_permalink() );
	$reddit .= '&title=' . urlencode( the_title_attribute( 'echo=0' ) ) . '" ';
	$reddit .= 'onclick="window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;" ';
	$reddit .= 'style="background: ' . $button_color . '">';
	$reddit .= '<i class="reddit socicon-reddit" ></i>';
	$reddit .= '<p>' . __( 'Reddit', 'recencio-book-reviews' ) . '</p>';
	$reddit .= '</a>';
	$reddit .= '</div>';

	$available_buttons['reddit'] = $reddit;
}

if ( in_array( 'pocket', $selected_buttons, true ) ) {
	$pocket  = '';
	$pocket .= '<div class="rcno-pocket-button">';
	$pocket .= '<a href="https://getpocket.com/edit?url=' . urlencode( get_the_permalink() ) . '" ';
	$pocket .= 'onclick="window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;" ';
	$pocket .= 'style="background: ' . $button_color . '">';
	$pocket .= '<i class="pocket socicon-pocket" ></i>';
	$pocket .= '<p>' . __( 'Pocket', 'recencio-book-reviews' ) . '</p>';
	$pocket .= '</a>';
	$pocket .= '</div>';

	$available_buttons['pocket'] = $pocket;
}

if ( in_array( 'digg', $selected_buttons, true ) ) {
	$digg  = '';
	$digg .= '<div class="rcno-digg-button">';
	$digg .= '<a href="http://digg.com/submit?url=' . urlencode( get_the_permalink() ) . '" ';
	$digg .= 'onclick="window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;" ';
	$digg .= 'style="background: ' . $button_color . '">';
	$digg .= '<i class="digg socicon-digg" ></i>';
	$digg .= '<p>' . __( 'Digg', 'recencio-book-reviews' ) . '</p>';
	$digg .= '</a>';
	$digg .= '</div>';

	$available_buttons['digg'] = $digg;
}

if ( in_array( 'instapaper', $selected_buttons, true ) ) {
	$instapaper  = '';
	$instapaper .= '<div class="rcno-instapaper-button">';
	$instapaper .= '<a href="http://www.instapaper.com/edit?url=' . urlencode( get_the_permalink() );
	$instapaper .= '&title=' . urlencode( the_title_attribute( 'echo=0' ) );
	$instapaper .= '&description=' . urlencode( $template->get_the_rcno_book_review_excerpt( $this->the_review_id() ) ) . '" ';
	$instapaper .= 'onclick="window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;" ';
	$instapaper .= 'style="background: ' . $button_color . '">';
	$instapaper .= '<i class="instapaper socicon-instapaper" ></i>';
	$instapaper .= '<p>' . __( 'InstaPaper', 'recencio-book-reviews' ) . '</p>';
	$instapaper .= '</a>';
	$instapaper .= '</div>';

	$available_buttons['instapaper'] = $instapaper;
}

if ( in_array( 'buffer', $selected_buttons, true ) ) {
	$buffer  = '';
	$buffer .= '<div class="rcno-buffer-button">';
	$buffer .= '<a href="https://buffer.com/add?text=' . urlencode( the_title_attribute( 'echo=0' ) );
	$buffer .= '&url=' . urlencode( get_the_permalink() ) . '" ';
	$buffer .= 'onclick="window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;" ';
	$buffer .= 'style="background: ' . $button_color . '">';
	$buffer .= '<i class="buffer socicon-buffer" ></i>';
	$buffer .= '<p>' . __( 'Buffer', 'recencio-book-reviews' ) . '</p>';
	$buffer .= '</a>';
	$buffer .= '</div>';

	$available_buttons['buffer'] = $buffer;
}

if ( in_array( 'email', $selected_buttons, true ) ) {
	$email  = '';
	$email .= '<div class="rcno-email-button">';
	$email .= '<a href="mailto:' . get_bloginfo( 'admin_email' ) . '" ';
	$email .= 'style="background: ' . $button_color . '">';
	$email .= '<i class="email socicon-mail" ></i>';
	$email .= '<p>' . __( 'Email', 'recencio-book-reviews' ) . '</p>';
	$email .= '</a>';
	$email .= '</div>';

	$available_buttons['email'] = $email;
}

// Sort the 'available_buttons' array using the 'selected_buttons' array to set the order
uksort( $available_buttons, function( $key1, $key2 ) use ( $selected_buttons ) {
	return ( array_search( $key1, $selected_buttons, true ) > array_search( $key2, $selected_buttons, true ) );
});
