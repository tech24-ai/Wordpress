<?php if (isset($knowledgebases_sticky_isset) && is_array($knowledgebases_sticky_isset) && !empty($knowledgebases_sticky_isset)) {
	if (have_posts() ) :
		$is_knowledgebases_sticky = true;
		while (have_posts() ) : the_post();
			$k_ad_p++;
			include locate_template("theme-parts/content-knowledgebase.php");
		endwhile;
	endif;
	wp_reset_query();
}?>