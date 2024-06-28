<?php add_action('after_setup_theme','discy_child_theme_locale');
function discy_child_theme_locale() {
    load_child_theme_textdomain('discy-child',get_stylesheet_directory().'/languages');
}
add_action('wp_enqueue_scripts','discy_enqueue_parent_theme_style',20);
function discy_enqueue_parent_theme_style() {
    wp_enqueue_style('discy-child-theme',get_stylesheet_uri(),'',1.0,'all');
}
?>