<?php
/**
 * @package Content Warnings
 * @version 0.6.0
 */
/*
Plugin Name: Content Warnings
Plugin URI:  https://github.com/fragmad/trigger-warning-wordpress
Description: A plugin to provide a Wordpress shortcode to mark material which may upset potential readers and provide them with the ability to choose if they read content or not.
Author: Will Ellwood
Version: 0.6.0
Author URI: http://www.github.com/fragmad
*/

function get_translation_path($iso_code) {
    $dir = plugin_dir_path( __FILE__ ) . "translations/";
    $translation_file_path = $dir . basename($iso_code) . ".json"; // Wrapping $lang in basename() stops bad input from allowing access to other parts of the filesystem.

    if (file_exists($translation_file_path)) {
        return $translation_file_path;
    } else {
        return null;
    }
}

function get_translation($lang) {
    // Set the default language
    $default_language = "en-US";

    // Get the file path
    $translation_file_path = get_translation_path($lang);

    // If that file doesn't exist, use the default language
    if (is_null($translation_file_path)) {
        $translation_file_path = get_translation_path($default_language);
    }

    // Get the contents of the file as an associative array
    $json = file_get_contents($translation_file_path);
    $json_data = json_decode($json, true);

    // Get information about the fallback languages for this language
    $fallback_iso_codes = $json_data["meta"]["fallback"];
    $fallback_translations = array();

    // If it has fallbacks...
    if (!empty($fallback_iso_codes)) {
	foreach($fallback_iso_codes as $iso) {
            $file_path = get_translation_path($iso);
            
            if (is_null($file_path)) {
                continue;  // It's safe to ignore incorrect fallbacks because the result will be visible on the page later on.
            }

            $fallback_json = file_get_contents($file_path);
            $fallback_json_data = json_decode($fallback_json, true);

            $fallback_translations[] = $fallback_json_data;
        }
    }

    // add fallback translations to the main translation json
    $json_data["fallbacks"] = $fallback_translations;
    
    return $json_data;
}
  

function parse_warning($warning_code, $translation) {
    // if the warning code is present in the main translation array, great
    if (array_key_exists($warning_code, $translation["terms"])) {
        return $translation["terms"][$warning_code];
    }
    
    // if the translation has no fallback translations, return the warning code
    if (empty($translation["fallbacks"])) {
        return $warning_code;
    }

    // Check each fallback in order of addition
    // The first fallback in the list will be preferred
    foreach($translation["fallbacks"] as $fallback) {
        if (array_key_exists($warning_code, $fallback["terms"])) {
            return $fallback["terms"][$warning_code];
        }
    }

    // if the warning code has no translation in the main translation or its fallback translations, return the warning code
    return $warning_code;
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

// sanitization is the same as sanitize_html_class except dashes aren't allowed
function sanitize_js_function_name($string) {
    // Strip out any %-encoded octets.
    $sanitized = preg_replace( '|%[a-fA-F0-9][a-fA-F0-9]|', '', $string );
 
    // Limit to A-Z, a-z, 0-9, '_'
    $sanitized = preg_replace( '/[^A-Za-z0-9_]/', '', $sanitized );

    return $sanitized;
}

function content_warning_func($atts) {
    // Define the attributes of the shortcode and their default contents
    $a = shortcode_atts( array(
        'type' => 'none',
        'lang' => 'en-EN'
    ), $atts );

    // Split out the comma-seperated warning codes
    $warning_codes = array_map('trim',
        explode(',', $a['type'])
    );

    // Sort the warning codes alphabetically.
    // todo: make this sort the warnings after parsing - will require the intl library

    asort($warning_codes);

    // Get the translation for the language
    $translation = get_translation($a['lang']);

    // Go over each warning code and get a translation for it
    $parsed_warnings = array();
    
    foreach ($warning_codes as $code) {
        $parsed_warnings[] = parse_warning($code, $translation);
    }

    // Join each array element with a closing and opening <li> tag
    $warnings =  implode('</li><li> ', $parsed_warnings);

    // Set the post-has-warning WP tag if necessary
    $set_warning_tag = true;

    if (in_array('none', $warning_codes)) {
        $set_warning_tag = false;
    }
    elseif (sizeof($warning_codes) == 1) {
        $set_warning_tag = false;
    }
    else {
        // value already set. Do nothing.
    }

    tag_post($set_warning_tag);

    $js_friendly_iso_code = sanitize_js_function_name($translation["meta"]["iso"]); 

    // Construct HTML to insert into page
    $warning_javascript = '<script>
function showWarning_' . $js_friendly_iso_code . '() {
    var content_warning_list = document.getElementById("ContentWarning_' . $js_friendly_iso_code . '");

    if (content_warning_list.style.display === "none") {
        content_warning_list.style.display = "block";
    } else {
        content_warning_list.style.display = "none";
    }
}
</script>';

    $warning_message = '<div class="ContentWarning" id="ContentWarning_' . $js_friendly_iso_code . '" style="display: none; background-color: none; border-style: solid; border-color: black; " ><p>' . $translation["ui"]["this_page_contains"] . ' </p><ul><li>' . $warnings . '</li></ul></div><br/>';

    $warning_body = '<div dir="' . $translation["meta"]["directionality"] . '" class="ContentWarningContainer_' . $translation["meta"]["directionality"] . '"><p><strong class="ContentWarningTitle">' . $translation["ui"]["content_warning"] . '</strong><br/><button onclick="showWarning_' . $js_friendly_iso_code .'()">' . $translation["ui"]["show_warnings"] . '</button></p>'. $warning_message . "</div>";

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
