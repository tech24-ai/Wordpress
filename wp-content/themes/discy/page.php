<?php get_header();
	include locate_template("includes/header-part.php");
	if (has_wpqa() && wpqa_plugin_version >= "5.9.8") {
		include wpqa_get_template("logged-only.php","theme-parts/");
	}
	$theme_sidebar_all = $theme_sidebar = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars("sidebar_where"):"");
	if (has_wpqa() && wpqa_plugin_version >= "5.9.8") {
		include wpqa_get_template("loop-setting.php","includes/");
	}else {
		include locate_template("includes/loop-setting.php");
	}
	if ( have_posts() ) :?>
		<div class="post-articles">
			<?php while ( have_posts() ) : the_post();
				include locate_template("theme-parts/content.php");
			endwhile;?>
		</div><!-- End post-articles -->
	<?php elseif (has_wpqa() && wpqa_plugin_version >= "5.9.8") :
		include wpqa_get_template("content-none.php","theme-parts/");
	endif;
	include locate_template("includes/footer-part.php");
get_footer();?>