<?php if ($question_answer_loop == "on" && $count_post_all > 0) {
	$author__not_in = array();
	$block_users = discy_options("block_users");
	if ($block_users == "on") {
		$user_id = get_current_user_id();
		if ($user_id > 0) {
			$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
			if (is_array($get_block_users) && !empty($get_block_users)) {
				$author__not_in = array("author__not_in" => $get_block_users);
			}
		}
	}
	if ($question_answer_show == 'best' && $the_best_answer != "") {
		$comments_args = get_comments(array_merge($author__not_in,array('parent' => 0,'number' => 1,'comment__in' => $the_best_answer)));
	}else if ($question_answer_show == 'reacted') {
		$comments_args = get_comments(array_merge($author__not_in,array('parent' => 0,'number' => 1,'post_id' => $post_data->ID,'status' => 'approve','orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),'meta_key' => 'wpqa_reactions_count','order' => 'DESC')));
	}else if ($question_answer_show == 'vote') {
		$comments_args = get_comments(array_merge($author__not_in,array('parent' => 0,'number' => 1,'post_id' => $post_data->ID,'status' => 'approve','orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),'meta_key' => 'comment_vote','order' => 'DESC')));
	}else if ($question_answer_show == 'oldest') {
		$comments_args = get_comments(array_merge($author__not_in,array('parent' => 0,'number' => 1,'post_id' => $post_data->ID,'status' => 'approve','orderby' => 'comment_date','order' => 'ASC')));
	}else {
		$comments_args = get_comments(array_merge($author__not_in,array('parent' => 0,'number' => 1,'post_id' => $post_data->ID,'status' => 'approve','orderby' => 'comment_date','order' => 'DESC')));
	}
	if (isset($comments_args) && is_array($comments_args) && !empty($comments_args)) {?>
		<div class="question-bottom">
			<ol class="commentlist clearfix">
		    	<?php $comment_item = $comments_args[0];
				$yes_private = (has_wpqa()?wpqa_private($comment_item->comment_post_ID,get_post($comment_item->comment_post_ID)->post_author,get_current_user_id()):1);
				if ($yes_private == 1) {
						$comment_id = esc_html($comment_item->comment_ID);
						if (has_wpqa()) {
							wpqa_comment($comment_item,"","","answer","","","",array("comment_read_more" => true,"inner_question" => true));
						}?>
					</li>
				<?php }?>
			</ol><!-- End commentlist -->
			<div class="clearfix"></div>
		</div><!-- End question-bottom -->
	<?php }
}?>