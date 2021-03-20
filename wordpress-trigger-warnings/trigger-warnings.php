<?php
/**
 * @package Content Warnings
 * @version 0.5.4
 */
/*
Plugin Name: Content Warnings
Plugin URI:  https://github.com/fragmad/trigger-warning-wordpress
Description: A plugin to provide a Wordpress shortcode to mark material which may upset potential readers and provide them with the ability to choose if they read content or not.
Author: Will Ellwood
Version: 0.6.0
Author URI: http://www.github.com/fragmad
*/


function compose_warnings($type) {
        $dir = plugin_dir_path( __FILE__ );

        $warning_file = $dir . "warnings.json";
        $json = file_get_contents($warning_file);
        $json_data = json_decode($json,true);

            if (array_key_exists($type, $json_data)) {
                return $json_data[$type];
            }
            else {
                return $type;
            }
}

function tag_post($set_content_warning) {
    $post_id = get_the_ID();

    if ($set_content_warning){
        wp_set_post_tags($post_id, 'content-warning', true );
    }
    else {
        wp_set_post_tags($post_id, 'no-content-warning', true);
    }
}

function content_warning_func( $atts) {
    $a = shortcode_atts( array(
        'type' => 'No content warnings',
    ), $atts );


//    $a = shortcode_atts( $atts );

    $arguments = array_map('trim',
        explode(',', $a['type'])
    );
    asort($arguments);
    $parsed_warnings = array_map('compose_warnings', $arguments);
    $warnings =  implode('</li><li> ', $parsed_warnings);

    $set_warning_tag = true;

    if (in_array('none',$arguments)) {
        $set_warning_tag = false;
    }
    elseif (sizeof($arguments) == 1) {
        $set_warning_tag = false;
    }
    else {
        // value already set. Do nothing.
    }

    tag_post($set_warning_tag);

    $warning_javascript = '<script>
function showWarning() {

    var x = document.getElementsByClassName("ContentWarning");

    for (i = 0; i < x.length; i++) {
        if (x[i].style.display === "none") {
            x[i].style.display = "block";
        } else {
            x[i].style.display = "none";
        }
    }
}
</script>';

    $warning_message = '<div class="ContentWarning" style="display: none; background-color: none; border-style: solid; border-color: black; " ><p>This page contains: </p><ul><li>' . $warnings . '</li></ul></div><br/>';

    $warning_body = '<p><b>CONTENT WARNING: </b><br/><button onclick="showWarning()">show warnings</button></p>'.$warning_message;

    $warning_html = $warning_javascript . $warning_body ;

    return $warning_html;
}

add_shortcode('content_warning', 'content_warning_func');
add_shortcode('trigger_warning', 'content_warning_func');

// Add content warning quicktag to HTML editor
function appthemes_add_contentwarning_quicktag() {
    if (wp_script_is('quicktags')){
        ?>
        <script type="text/javascript">
            QTags.addButton( 'trigger_warning', 'trigger_warning', '[trigger_warning = 'none']', '', 'tw', 'Content Warning', 201 );
            QTags.addButton( 'content_warning', 'content_warning', '[content_warning = 'none']', '', 'cw', 'Content Warning', 201 );
        </script>
        <?php
    }
}
add_action( 'admin_print_footer_scripts', 'appthemes_add_contentwarning_quicktag' );
?>
