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
Version: 0.5.1
Author URI: http://www.github.com/fragmad
*/


function compose_warnings($type) {

    $READ_JSON = true;

    if ($READ_JSON == false) {
        switch ($type) {
            case 'triggering':
                return 'material';
            case 'ableism':
                return 'Ableism';
            case 'abortion_miscarriage':
                return 'Abortion/miscarriage';
            case 'abuse':
                return 'Abuse';
            case 'animal':
                return 'Animal cruelty/death';
            case 'blood':
                return 'Blood';
            case 'body_transformation':
                return 'Body transformation';
            case 'cancer':
                return 'Cancer';
            case 'child_abuse':
                return 'Child abuse';
            case 'childbirth':
                return 'Childbirth';
            case 'child_death':
                return 'Child death';
            case 'child_sexual_abuse':
                return 'Child sexual abuse';
            case 'corpse_eating':
                return 'Corpse-eating';
            case 'death':
                return 'Death/dying';
            case 'death_pregnant':
                return 'Death of a pregnant person';
            case 'autonomy':
                return 'Disregard for personal autonomy';
            case 'drugs':
                return 'Drug use';
            case 'dysphoria':
                return 'Dysphoria';
            case 'incest':
                return 'Incest';
            case 'mental_disorders':
                return 'Mental disorders';
            case 'murder':
                return 'Murder';
            case 'needles':
                return 'Needles';
            case 'pregnancy':
                return 'Pregnancy';
            case 'sexual_violence':
                return 'Rape/sexual assault';
            case 'shaming':
                return 'Shaming';
            case 'self-harm':
                return 'Self-harming behaviors';
            case 'sex':
                return 'Sex';
            case 'slurs':
                return 'Slurs';
            case 'snakes':
                return 'Snakes';
            case 'spiders_insects':
                return 'Spiders/insects';
            case 'suicide':
                return 'Suicide';
            case 'transphobia':
                return 'Trans misgendering or other transphobic depictions';
            case 'violence':
                return 'Violence/combat';
            case 'vomit':
                return 'Vomit';
            case 'xenophobia':
                return 'Xenophobia';
            default:
                return $type;
        }
    }
    else {
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


    $warnings = ' ';
    $arguments = array_map('trim',
        explode(',', $a['type'])
    );
    asort($arguments);
    $parsed_warnings = array_map('compose_warnings', $arguments);
    //$warnings =  implode(' and/or ', $parsed_warnings);
    $warnings =  implode('</li><li> ', $parsed_warnings);

    tag_post();

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