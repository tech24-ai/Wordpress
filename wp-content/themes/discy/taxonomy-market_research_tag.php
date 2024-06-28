<?php get_header(); 
include locate_template("includes/header-part.php");
?>

<?php 
if (isset($tab_tag) && $tab_tag == true && has_wpqa() && wpqa_plugin_version >= "5.9.8") {
    include wpqa_get_template("tabs.php","theme-parts/");
}else {
    $active_sticky = $show_sticky = true;
    include locate_template("theme-parts/loop.php");
}
include locate_template("includes/footer-part.php");
get_footer(); ?>
