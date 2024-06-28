<?php $custom_permission = discy_options("custom_permission");
$user_id = get_current_user_id();
if (is_user_logged_in()) {
	$user_is_login = get_userdata($user_id);
	$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
}
$post_type = $post->post_type;

$place_comment_form = discy_options($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?'place_answer_form':'place_comment_form');
$place_comment_form = ($place_comment_form != ""?$place_comment_form:"after");

$wpqa_server = apply_filters('wpqa_server','SCRIPT_FILENAME');
if (!empty($wpqa_server) && 'comments.php' == basename($wpqa_server)) :
	die (esc_html__('Please do not load this page directly. Thanks!',"discy"));
endif;
if ( post_password_required() ) : ?>
    <p class="no-comments">
    	<?php if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
    		esc_html_e("This question is password protected. Enter the password to view answers.","discy");
    	}else {
    		esc_html_e("This post is password protected. Enter the password to view comments.","discy");
    	}?>
    </p>
    <?php return;
endif;

if ($place_comment_form == "before") {
	include locate_template("comment-parts/comment-form.php");
}

$count_post_all = (int)(has_wpqa()?wpqa_count_comments($post->ID):get_comments_number());
if (has_wpqa() && $count_post_all == 0) {
	$get_comments_args = array('post_id' => $post->ID,'status' => 'approve');
	$comments_args = get_comments($get_comments_args);
	wpqa_update_comments_count($post->ID);
	$count_post_all = (int)wpqa_count_comments($post->ID);
}
if ( have_comments() && $count_post_all > 0 ) :
	$k_ad = 1;
	$zero_comment_index = 0;
	$filter_show_comments = apply_filters("discy_filter_show_comments",true,$post_type,$post->ID);
	if ($filter_show_comments == true) {
		if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
			$custom_answer_tabs = discy_post_meta("custom_answer_tabs");
			if ($custom_answer_tabs == "on") {
				$answers_tabs = discy_post_meta('answers_tabs');
			}else {
				$answers_tabs = discy_options('answers_tabs');
			}
			$answers_tabs = apply_filters("wpqa_answers_tabs",$answers_tabs);
			$answers_tabs_keys = array_keys($answers_tabs);
			if (isset($answers_tabs) && is_array($answers_tabs)) {
				$a_count = 0;
				while ($a_count < count($answers_tabs)) {
					if (isset($answers_tabs[$answers_tabs_keys[$a_count]]["value"]) && $answers_tabs[$answers_tabs_keys[$a_count]]["value"] != "" && $answers_tabs[$answers_tabs_keys[$a_count]]["value"] != "0") {
						$first_one = $a_count;
						break;
					}
					$a_count++;
				}
				
				if (isset($first_one) && $first_one !== "") {
					$first_one = $answers_tabs[$answers_tabs_keys[$first_one]]["value"];
				}
				
				if (isset($_GET["show"]) && $_GET["show"] != "") {
					$first_one = $_GET["show"];
				}
			}
			if (isset($first_one) && $first_one !== "") {
				$answers_tabs_foreach = apply_filters("wpqa_answers_tabs_foreach",true,$answers_tabs,$first_one);
			}
			if (isset($answers_tabs_foreach) && $answers_tabs_foreach == true && ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && isset($first_one) && $first_one !== "") {
				$show_tabs = true;
			}
		}
	}?>
	<div id="comments" class="post-section <?php echo (isset($show_tabs)?"answers-section-tabs":"answers-section-not-tabs")?>">
		<div class="post-inner">
			<?php 
			if ($filter_show_comments == true) {
				if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
					if (isset($show_tabs)) {?>
						<div class="answers-tabs">
					<?php }
				}
					do_action("discy_before_answer_title");?>
					<h3 class="section-title"><span><?php echo ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?sprintf(_n("%s Answer","%s Answers",$count_post_all,"discy"),$count_post_all):sprintf(_n("%s Comment","%s Comments",$count_post_all,"discy"),$count_post_all));?></h3>
					<?php do_action("discy_after_answer_title");
				if (isset($show_tabs)) {?>
						<div class="answers-tabs-inner">
							<ul>
								<?php foreach ($answers_tabs as $key => $value) {
									if ($key == "votes" && isset($answers_tabs["votes"]["value"]) && $answers_tabs["votes"]["value"] == "votes") {?>
										<li<?php echo ((isset($_GET["show"]) && $_GET["show"] === "votes") || $first_one === "votes"?" class='active-tab'":"")?>><a href="<?php echo esc_url_raw(add_query_arg(array("show" => "votes")))?>#comments"><?php esc_html_e("Voted","discy")?></a></li>
									<?php }else if ($key == "oldest" && isset($answers_tabs["oldest"]["value"]) && $answers_tabs["oldest"]["value"] == "oldest") {?>
										<li<?php echo ((isset($_GET["show"]) && $_GET["show"] === "oldest") || $first_one === "oldest"?" class='active-tab'":"")?>><a href="<?php echo esc_url_raw(add_query_arg(array("show" => "oldest")))?>#comments"><?php esc_html_e("Oldest","discy")?></a></li>
									<?php }else if ($key == "recent" && isset($answers_tabs["recent"]["value"]) && $answers_tabs["recent"]["value"] == "recent") {?>
										<li<?php echo ((isset($_GET["show"]) && $_GET["show"] === "recent") || $first_one === "recent"?" class='active-tab'":"")?>><a href="<?php echo esc_url_raw(add_query_arg(array("show" => "recent")))?>#comments"><?php esc_html_e("Recent","discy")?></a></li>
									<?php }else if ($key == "random" && isset($answers_tabs["random"]["value"]) && $answers_tabs["random"]["value"] == "random") {?>
										<li<?php echo ((isset($_GET["show"]) && $_GET["show"] === "random") || $first_one === "random"?" class='active-tab'":"")?>><a href="<?php echo esc_url_raw(add_query_arg(array("show" => "random")))?>#comments"><?php esc_html_e("Random","discy")?></a></li>
									<?php }
								}?>
							</ul>
						</div><!-- End answers-tabs-inner -->
						<div class="clearfix"></div>
					</div><!-- End answers-tabs -->
				<?php }
				if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && isset($first_one) && $first_one !== "" && isset($answers_tabs)) {
					do_action("wpqa_answers_after_tabs",$answers_tabs,$first_one);
				}
				$show_answer = discy_options("show_answer");
				$show_comment = discy_options("show_comment");
				$is_super_admin = is_super_admin($user_id);
				if ($user_id > 0) {
					$include_unapproved = array($user_id);
				}else {
					$unapproved_email = wp_get_unapproved_comment_author_email();
					if ($unapproved_email) {
						$include_unapproved = array($unapproved_email);
					}
				}
				$include_unapproved_args = (isset($include_unapproved)?array('include_unapproved' => $include_unapproved):array());
				$author__not_in = array();
		    	$block_users = discy_options("block_users");
				if ($block_users == "on") {
					if ($user_id > 0) {
						$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
						if (is_array($get_block_users) && !empty($get_block_users)) {
							$author__not_in = array("author__not_in" => $get_block_users);
						}
					}
				}
				$get_comments_args = array_merge($author__not_in,$include_unapproved_args,array('post_id' => $post->ID,'status' => 'approve'));
				if (($post_type != wpqa_questions_type && $post_type != wpqa_asked_questions_type && ($is_super_admin || (wpqa_plugin_version >= "5.9.7" && wpqa_is_bot()) || $custom_permission != "on" || (is_user_logged_in() && $custom_permission == "on" && isset($roles["show_comment"]) && $roles["show_comment"] == 1) || (!is_user_logged_in() && $show_comment == "on"))) || (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && ($is_super_admin || $custom_permission != "on" || (is_user_logged_in() && $custom_permission == "on" && isset($roles["show_answer"]) && $roles["show_answer"] == 1) || (!is_user_logged_in() && $show_answer == "on")))) {
					if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
						if (isset($first_one) && $first_one !== "") {
							if ($first_one == 'votes') {
								$comments_args = get_comments(array_merge($get_comments_args,array('orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),'meta_key' => 'comment_vote','order' => 'DESC')));
							}else if ($first_one == 'oldest') {
								$comments_args = get_comments(array_merge($get_comments_args,array('orderby' => 'comment_date','order' => 'ASC')));
							}else if ($first_one == 'recent') {
								$comments_args = get_comments(array_merge($get_comments_args,array('orderby' => 'comment_date','order' => 'DESC')));
							}else if ($first_one == 'random') {
								$comments_args = get_comments(array_merge($get_comments_args,array('orderby' => 'rand','order' => 'DESC')));
								shuffle($comments_args);
							}
						}else {
							$comments_args = get_comments(array_merge($get_comments_args,array('orderby' => 'comment_date','order' => 'ASC')));
						}
					}?>
					<ol class="commentlist clearfix">
					    <?php if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && isset($first_one) && $first_one !== "") {
					    	$comments_args = (isset($comments_args)?$comments_args:array());
						    $comments_args = apply_filters("wpqa_comments_args",$comments_args,$first_one,$post->ID);
						}
						$read_more_answer = discy_options("read_more_answer");
						$comment_read_more = (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && $read_more_answer == "on"?array('comment_read_more' => true):array());
					    $list_comments_args = array_merge(array('callback' => (has_wpqa()?'wpqa_comment':'')),$comment_read_more);
					    if (isset($comments_args) && is_array($comments_args) && !empty($comments_args)) {
					    	$comment_order = get_option('comment_order');
					    	if ($comment_order == "desc") {
					    		$comments_args = array_reverse($comments_args);
					    	}
					    	wp_list_comments($list_comments_args,$comments_args);
					    }else {
					    	$show_comments = apply_filters("wpqa_show_comments",true);
					    	if ($show_comments == true) {
					    		$comments_args = get_comments(array_merge($get_comments_args,array('orderby' => 'comment_date','order' => 'DESC')));
					    		if (isset($comments_args) && is_array($comments_args) && !empty($comments_args)) {
							    	wp_list_comments($list_comments_args,$comments_args);
							    }else {
							    	wp_list_comments($list_comments_args);
							    }
						    }
					    }?>
					</ol><!-- End commentlist -->
				<?php }else {
					if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
						echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to view answers.","discy").' '.(has_wpqa()?wpqa_paid_subscriptions():'').'</p></div>';
					}else {
						echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to view comments.","discy").' '.(has_wpqa()?wpqa_paid_subscriptions():'').'</p></div>';
					}
				}
			}?>
			<div class="clearfix"></div>
		</div><!-- End post-inner -->
	</div><!-- End post -->
	
	<?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
		<div class="pagination comments-pagination">
		    <?php $comment_pagination = discy_options(($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?'answer_pagination':'comment_pagination'));
		    if ($comment_pagination != "pagination") {
			    $max_num_pages = ceil($count_post_all/get_option('comments_per_page'));
			    if (has_wpqa()) {
			    	wpqa_load_pagination(array(
						"post_pagination" => $comment_pagination,
						"max_num_pages" => $max_num_pages,
						"it_answer_pagination" => true,
						"its_post_type" => wpqa_questions_type,
						"its_answer" => true,
					));
			    }
			}else {
			    paginate_comments_links(array('prev_text' => '<i class="icon-left-open"></i>', 'next_text' => '<i class="icon-right-open"></i>'));
			}?>
		</div><!-- End comments-pagination -->
		<div class="clearfix"></div>
    <?php endif;
endif;

if ($place_comment_form == "after") {
	include locate_template("comment-parts/comment-form.php");
}?>