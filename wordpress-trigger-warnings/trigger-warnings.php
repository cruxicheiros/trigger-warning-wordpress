<?php
/**
 * @package Trigger Warnings
 * @version 0.1
 */
/*
Plugin Name: Trigger Warnings
Plugin URI:  Nothing yet
Description: This is the start of the project.
Author: Will Ellwood
Version: 0.1
Author URI: http://www.github.com/fragmad
*/

function noise() {
	$noise = "NOISE!";

	// Here we split it into lines
	$noise = explode( "\n", $lyrics );

	// And then randomly choose a line
	return wptexturize( $lyrics[ mt_rand( 0, count( $lyrics ) - 1 ) ] );
}

// This just echoes the chosen line, we'll position it later
function make_noise() {
	$chosen = noise();
	echo "<p id='nin'>$chosen</p>";
}

// Now we set that function up to execute when the admin_notices action is called
add_action( 'admin_notices', 'make_noise' );

// We need some CSS to position the paragraph
function noise_css() {
	// This makes sure that the positioning is also good for right-to-left languages
	$x = is_rtl() ? 'left' : 'right';

	echo "
	<style type='text/css'>
	#dolly {
		float: $x;
		padding-$x: 15px;
		padding-top: 5px;
		margin: 0;
		font-size: 11px;
	}
	</style>
	";
}

add_action( 'admin_head', 'noise_css' );

?>
