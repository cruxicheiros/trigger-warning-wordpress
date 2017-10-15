<?php
/**
 * @package Trigger Warnings
 * @version 0.5
 */
/*
Plugin Name: Trigger Warnings
Plugin URI:  https://github.com/fragmad/trigger-warning-wordpress
Description: A plugin to provide a Wordpress shortcode to mark material which may upset potential readers and provide them
with the ability to choose if they read content or not.
Author: Will Ellwood
Version: 0.5
Author URI: http://www.github.com/fragmad
*/


function compose_warnings($type) {
    $warning_string = 'triggering material';
    switch ($type) {
    case 'triggering':
        return 'triggering material';
    case 'abuse':
        return 'material about abusive behaviour';
        break;
    case 'slurs':
        return 'material involving ethnic or racist slurs';
    case 'sexual_violence':
        return 'material contaning references to sexual violence';
    case 'physcal_violence':
        return 'material contaning references to physical violence';
    }

    return $warning_string;

}

function tag_post() {
    $post_id = get_the_ID();
    wp_set_post_tags($post_id, 'trigger-warning', true );
}

function trigger_warning_func( $atts) {
    $a = shortcode_atts( array(
        'type' => 'triggering',
    ), $atts );

    $trigger_types = array('triggering', 'abuse', 'sexual_violence', 'physical_violence', 'slurs');


    if (in_array($a['type'], $trigger_types)) {
        $warnings = compose_warnings($a['type']);
    }
    else {
        $warnings = ' ';
        $arguments = array_map(
            'trim',
            explode(',', $a['type'])
        );

        $parsed_warnings = array_map('compose_warnings', $arguments);
        $warnings =  implode(' and/or ', $parsed_warnings);
    }

    tag_post();
    $warning_message = '<p><b>TRIGGER WARNING</b> This page contains ' . $warnings . ' which may be triggering for survivors.</p>';
    return $warning_message;
}

add_shortcode('trigger_warning', 'trigger_warning_func');
?>
