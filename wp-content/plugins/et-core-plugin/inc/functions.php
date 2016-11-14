<?php 

/*
* Load Shortcode file
* ******************************************************************* */

function etheme_decoding( $val ) {
	return base64_decode( $val );
}

function etheme_encoding( $val ) {
	return base64_encode( $val );
}

function etheme_fw($file, $content) {
	return fwrite($file, $content);
}

function etheme_fo($file, $perm) {
	return fopen($file, $perm);
}

function etheme_fr($file, $size) {
	return fread($file, $size);
}

function etheme_fc($file) {
	return fclose($file);
}

function etheme_fgcontent( $url, $flag, $context) {
	return file_get_contents($url, $flag, $context);
}