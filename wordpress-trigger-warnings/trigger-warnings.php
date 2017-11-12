<?php
/**
 * @package Content Warnings
 * @version 0.5
 */
/*
Plugin Name: Content Warnings
Plugin URI:  https://github.com/fragmad/trigger-warning-wordpress
Description: A plugin to provide a Wordpress shortcode to mark material which may upset potential readers and provide them with the ability to choose if they read content or not.
Author: Will Ellwood
Version: 0.5
Author URI: http://www.github.com/fragmad
*/


function compose_warnings($type) {
    $warning_string = 'triggering material';
    switch ($type) {
    case 'triggering':
        return 'material';
    case 'abuse':
        return 'abusive behaviour';
        break;
    case 'slurs':
        return 'ethnic or racist slurs';
    case 'sexual_violence':
        return 'sexual violence';
    case 'physical_violence':
        return 'physical violence';
    }

    return $warning_string;

}

function tag_post() {
    $post_id = get_the_ID();
    wp_set_post_tags($post_id, 'content-warning', true );
}

function content_warning_func( $atts) {
    $a = shortcode_atts( array(
        'type' => 'triggering',
    ), $atts );

    $warning_types = array('triggering', 'abuse', 'sexual_violence', 'physical_violence', 'slurs');


    if (in_array($a['type'], $warning_types)) {
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
    $warning_message = '<p><b>CONTENT WARNING</b> This page contains ' . $warnings . ' which may be triggering for survivors.</p>';
    return $warning_message;
}

add_shortcode('content_warning', 'content_warning_func');
add_shortcode('trigger_warning', 'content_warning_func');

// Add content warning quicktag to HTML editor
function appthemes_add_contentwarning_quicktag() {
    if (wp_script_is('quicktags')){
?>
    <script type="text/javascript">
    QTags.addButton( 'trigger_warning', 'trigger_warning', '[trigger_warning]', '', 'tw', 'Content Warning', 201 );
    QTags.addButton( 'content_warning', 'content_warning', '[content_warning]', '', 'cw', 'Content Warning', 201 );
    </script>
<?php
    }
}
add_action( 'admin_print_footer_scripts', 'appthemes_add_contentwarning_quicktag' );
?>
