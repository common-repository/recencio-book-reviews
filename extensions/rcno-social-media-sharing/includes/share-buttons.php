<?php

/**
 * This where we are printing the buttons on the frontend.
 */

include __DIR__ . '/all-buttons.php';

do_action( 'before_rcno_social_media_sharing_buttons' );

$out = '';
$out .= '<div class="rcno-share-buttons">';
if ( '' !== $this->get_setting( 'share_buttons_title' ) ) {
	$out .= '<div class="share-this">';
	$out .= '<p>' . $this->get_setting( 'share_buttons_title' ) . '</p>';
	$out .= '</div>'; // .share-this
}
$out .=	'<div class="rcno-share-buttons-list">';
foreach ( $available_buttons as $key => $output ) {
	$out .= $output;
}
$out .=	'</div>'; // .rcno-share-buttons-list
$out .= '</div>'; // .rcno-share-buttons

$out .= '<style> #rcno-share-buttons-list div { 
	background: ' . $this->get_setting( 'buttons_color' ) . '; 
	border-color: '. $this->get_setting( 'buttons_color' ) .'; 
	}
</style>';

echo $out;

do_action( 'after_rcno_social_media_sharing_buttons' );
