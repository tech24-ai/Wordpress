<?php if ($follow_category == "on" && $tag_style == "simple_follow" && (!is_array($tags_tax) && ($tags_tax == wpqa_question_tags || $tags_tax == wpqa_questions_type) || (is_array($tags_tax) && !empty($tags_tax) && (in_array(wpqa_question_tags,$tags_tax) || in_array(wpqa_questions_type,$tags_tax))))) {
	$tax_id = $term->term_id;
	if ($follow_category == "on") {
		$tag_follow = get_term_meta($tax_id,"tag_follow",true);
		$tags_follwers = (int)(is_array($tag_follow)?count($tag_follow):0);
	}
	echo '<div class="col '.(((!is_array($tags_tax) && $tags_tax == wpqa_question_tags) || (is_array($tags_tax) && in_array(wpqa_question_tags,$tags_tax))) && $theme_sidebar == "full"?"col4":"col6").'">
		<div class="cat-sections-follow">
		<div class="cat-sections">
			<a href="'.esc_url(get_term_link($term)).'" title="'.esc_attr(sprintf(esc_html__('View all questions under %s','discy'),$term->name)).'"><i class="icon-tag"></i>'.$term->name.'</a>
		</div>';
		if ($follow_category == "on") {
			echo '<div class="cat-section-follow">
				<div class="cat-follow-button"><i class="icon-users"></i><span class="follow-cat-count">'.discy_count_number($tags_follwers)."</span>"._n("Follower","Followers",$tags_follwers,"discy").'</div>
					'.(has_wpqa()?wpqa_follow_cat_button($tax_id,$user_id,'tag',true,'button-default-4','cat-section-follow','follow-cat-count'):"").'
					<div class="clearfix"></div>
				</div>
			</div>';
		}
	echo '</div>';
}else {
	if ($tag_style == "advanced") {
		echo '<div class="col '.($theme_sidebar == "full"?"col3":"col4").'">
			<div class="tag-sections">
				<div class="tag-counter">';
	}
					echo '<a href="'.esc_url(get_term_link($term)).'" title="'.esc_attr(sprintf(($tags_tax == 'post' || $tags_tax == 'post_tag'?esc_html__('View all posts under %s','discy'):esc_html__('View all questions under %s','discy')),$term->name)).'">'.$term->name.'</a>';
	if ($tag_style == "advanced") {
					echo '<span> x '.discy_count_number($term->count).'</span>
				</div>
				<div class="tag-section">';
					$today = getdate();
					$tag = $term->term_id;
					$today_query = new WP_Query(array('post_type' => $post_type_tags,'year' => $today["year"],'monthnum' => $today["mon"],'day' => $today["mday"],'tax_query' => array(array('taxonomy' => $tag_type,'field' => 'term_id','terms' => $tag))));
					$week  = date('W');
					$year  = date('Y');
					$month = date('m');
					$week_query   = new WP_Query(array('post_type' => $post_type_tags,'year' => $year,'w' => $week,'tax_query' => array(array('taxonomy' => $tag_type,'field' => 'term_id','terms' => $tag))));
					$month_query  = new WP_Query(array('post_type' => $post_type_tags,'year' => $year,'monthnum' => $month,'tax_query' => array(array('taxonomy' => $tag_type,'field' => 'term_id','terms' => $tag))));
					echo "<span>".sprintf(esc_html__('%s asked today','discy'),discy_count_number($today_query->found_posts))."</span>";
					echo "<span>".sprintf(esc_html__('%s this week','discy'),discy_count_number($week_query->found_posts))."</span>";
					echo "<span>".sprintf(esc_html__('%s this month','discy'),discy_count_number($month_query->found_posts))."</span>";
				echo '</div>
			</div>
		</div>';
	}
}?>