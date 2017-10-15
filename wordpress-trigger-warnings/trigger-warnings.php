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


function compose_warnings($type) {
    $warning_string = 'triggering material';
    switch ($type) {
    case 'triggering':
        $warning_string = 'material';
        break;
    case 'abuse':
        $warning_string = 'material about abusive behaviour';
        break;
    case 'slurs':
        $warning_string = 'material containing slurs';
        break;
    }

    return $warning_string;

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
        return "false <br/>";
    }
    $warning_message = '<p><b>TRIGGER WARNING</b> This page contains ' . $warnings . ' which may be triggering for survivors.</p>';
    return $warning_message;
}

add_shortcode('trigger_warning', 'trigger_warning_func');
?>
