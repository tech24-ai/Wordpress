<?php if (isset($has_have_posts)) {
	include wpqa_get_template("content.php","theme-parts/");
}else {
	if ( have_posts() ) :
		while (have_posts()) : the_post();
			include wpqa_get_template("content.php","theme-parts/");	
		endwhile;
	endif;
}?>