<?php if (isset($questions_sticky_isset) && is_array($questions_sticky_isset) && !empty($questions_sticky_isset)) {
	if (have_posts() ) :
		$is_questions_sticky = true;
		while (have_posts() ) : the_post();
			$k_ad_p++;
			include locate_template("theme-parts/content-question.php");
		endwhile;
	endif;
	wp_reset_query();
}?>