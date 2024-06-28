<?php if (isset($GLOBALS['post'])) {
	$post_data = $post = $GLOBALS['post'];
}else {
	$post_data = $post;
}

if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) {
	$is_singular = true;
}

$place_comment_form       = discy_options("place_answer_form");
$discoura_style           = discy_options("discoura_style");
$questions_position       = discy_options("between_questions_position");
$adv_type_repeat          = discy_options("between_adv_type_repeat");
$custom_permission        = discy_options("custom_permission");
$show_question            = discy_options("show_question");
$question_delete          = discy_options("question_delete");
$question_edit            = discy_options("question_edit");
$active_vote              = discy_options("active_vote");
$vote_style               = discy_options("vote_style");
$question_meta_vote       = discy_options("question_meta_vote");
$ask_question_items       = discy_options("ask_question_items");
$active_moderators        = discy_options("active_moderators");
$video_desc               = discy_options("video_desc");
$active_reports           = discy_options("active_reports");
$active_logged_reports    = discy_options("active_logged_reports");
$private_question_content = discy_options("private_question_content");
$question_poll            = discy_post_meta("question_poll","",false);
$user_id                  = get_current_user_id();
$is_super_admin           = is_super_admin($user_id);
$get_question_user_id     = discy_post_meta("user_id","",false);
$closed_question          = discy_post_meta("closed_question","",false);
$anonymously_user         = discy_post_meta("anonymously_user","",false);
$video_desc_active        = (isset($ask_question_items["video_desc_active"]["value"]) && $ask_question_items["video_desc_active"]["value"] == "video_desc_active"?"on":"");
$post_author              = $post_data->post_author;
$question_id              = $post_data->ID;
$get_author_profile       = (has_wpqa() && $post_author > 0?wpqa_profile_url($post_author):"");
$yes_private              = (has_wpqa()?wpqa_private($question_id,$post_author,$user_id):1);
$question_sticky          = (has_wpqa()?wpqa_question_sticky($question_id):"");
$pending_questions        = (has_wpqa() && wpqa_is_pending_questions() && ($is_super_admin || $active_moderators == "on") && wpqa_is_user_owner() && ($is_super_admin || (isset($moderator_categories) && is_array($moderator_categories) && !empty($moderator_categories)))?true:false);
$pending_questions_page   = (has_wpqa() && wpqa_is_pending_questions()?true:false);
$moderators_permissions   = (has_wpqa() && $active_moderators == "on"?wpqa_user_moderator($user_id):"");
$count_post_all           = (int)(has_wpqa()?wpqa_count_comments($question_id):get_comments_number());
$question_link_target     = apply_filters("discy_question_link_target","");
if ($discoura_style == "on") {
	$profile_credential = get_user_meta($post_author,"profile_credential",true);
	$privacy_credential = (has_wpqa()?wpqa_check_user_privacy($post_author,"credential"):"");
	if (!isset($is_singular)) {
		$question_simple = "on";
	}
	$question_meta_vote = "on";
}
if ($question_columns == "style_2") {
	$asked_to = "";
	$question_meta_vote = $question_meta_icon = "on";
}
if (has_wpqa() && wpqa_plugin_version >= "5.8" && isset($k_ad_p) && (($k_ad_p == $questions_position) || ($adv_type_repeat == "on" && $k_ad_p != 0 && $k_ad_p % $questions_position == 0))) {
	echo wpqa_ads("between_adv_type","between_adv_link","between_adv_code","between_adv_href","between_adv_img","","","aalan-inside".($question_columns == "style_2"?" post-with-columns article-question col ".($theme_sidebar == "full"?"col4":"col6"):""),"on","style_2");
}
if (((!isset($is_singular) && $vote_question_loop == "on") || (isset($is_singular) && $vote_question_single == "on")) && $active_vote == "on") {
	$wpqa_vote = true;
}
if (isset($wpqa_vote) && $question_meta_vote == "on") {
	$wpqa_vote_meta = true;
}
if (((!isset($is_singular) && $author_image == "on") || (isset($is_singular) && $author_image_single == "on"))) {
	$wpqa_image = true;
}

if (is_user_logged_in()) {
	$user_is_login = get_userdata($user_id);
	$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
}

if (!$is_super_admin && $yes_private != 1 && $private_question_content != "on") {
	echo '<article class="article-question private-question article-post clearfix">
		<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry it's a private question.","discy").'</p></div>
	</article>';
}else {
	if ($custom_permission != "on" || $is_super_admin || (is_user_logged_in() && isset($roles["show_question"]) && $roles["show_question"] == 1) || (!is_user_logged_in() && $show_question == "on") || ($user_id > 0 && $user_id == $post_author) || ($user_id > 0 && $user_id == $anonymously_user)) {
		$the_best_answer = discy_post_meta("the_best_answer","",false);
		if (isset($is_singular)) {
			$share_facebook = (isset($post_share["share_facebook"]["value"])?$post_share["share_facebook"]["value"]:"");
			$share_twitter  = (isset($post_share["share_twitter"]["value"])?$post_share["share_twitter"]["value"]:"");
			$share_linkedin = (isset($post_share["share_linkedin"]["value"])?$post_share["share_linkedin"]["value"]:"");
			$share_whatsapp = (isset($post_share["share_whatsapp"]["value"])?$post_share["share_whatsapp"]["value"]:"");
			if (isset($the_best_answer) && $the_best_answer != "" && $the_best_answer > 0) {
				$get_comment = get_comment($the_best_answer);
				if (empty($get_comment) || (isset($get_comment->comment_post_ID) && $get_comment->comment_post_ID != $question_id)) {
					delete_post_meta($question_id,"the_best_answer");
					$the_best_answer = "";
				}
			}
			$question_close_admin = discy_options("question_close_admin");
			if (!$is_super_admin && $question_close == "on" && $question_close_admin == "on") {
				$question_close = "0";
			}
		}

		$discy_thumbnail_id = discy_post_meta("_thumbnail_id","",false);
		$question_email     = "";
		
		$comment_count = discy_post_meta("comment_count","",false);
		if ($post_data->comment_count > 0 || $comment_count == "") {
			update_post_meta($question_id,"comment_count",$post_data->comment_count);
		}
		
		if ($post_author > 0) {
			$question_username = get_the_author_meta('display_name',$post_author);
		}else {
			$anonymously_question = discy_post_meta("anonymously_question","",false);
			if (($anonymously_question == "on" || $anonymously_question == 1) && $anonymously_user != "") {
				$question_username = esc_html__('Anonymous','discy');
			}else {
				$question_email = discy_post_meta("question_email","",false);
				$question_username = discy_post_meta("question_username","",false);
				$question_username = ($question_username != ""?$question_username:esc_html__('[Deleted User]','discy'));
			}
		}
		if ($yes_private != 1 && $private_question_content == "on") {
		}else {
			if (isset($is_singular)) {
				$featured_image_question = discy_options("featured_image_single");
				$featured_image_question_width = discy_options("featured_image_inner_question_width");
				$featured_image_question_height = discy_options("featured_image_inner_question_height");
			}else {
				$featured_image_question = discy_options("featured_image_loop");
				$featured_image_question_width = discy_options("featured_image_question_width");
				$featured_image_question_height = discy_options("featured_image_question_height");
			}
			
			if ($featured_image_question == "on") {
				$custom_featured_image_size = discy_post_meta('custom_featured_image_size');
				if ($custom_featured_image_size == "on") {
					$featured_image_question_width = discy_post_meta('featured_image_width');
					$featured_image_question_height = discy_post_meta('featured_image_height');
				}
				$featured_image_question_lightbox = discy_options("featured_image_question_lightbox");
				$featured_image_question_width = ($featured_image_question_width != ""?$featured_image_question_width:260);
				$featured_image_question_height = ($featured_image_question_height != ""?$featured_image_question_height:185);
				$featured_image_question_width = apply_filters("discy_featured_image_".(isset($is_singular)?"single_":"")."question_width",$featured_image_question_width);
				$featured_image_question_height = apply_filters("discy_featured_image_".(isset($is_singular)?"single_":"")."question_height",$featured_image_question_height);
				$img_lightbox = ($featured_image_question_lightbox == "on"?"lightbox":false);
			}

			if (($pending_questions || !isset($is_singular)) && ($featured_image_question == "on" && has_post_thumbnail())) {
				$question_url_1 = ($featured_image_question_lightbox == "on"?"":"<a".$question_link_target." href='".get_permalink($question_id)."'>");
				$question_url_2 = ($featured_image_question_lightbox == "on"?"":"</a>");
			}
		}
		$get_featured_image = $get_featured_image_filter = false;
		if ($featured_image_question == "on" && has_post_thumbnail()) {
			$get_featured_image = discy_get_aq_resize_img($featured_image_question_width,$featured_image_question_height,$img_lightbox);
			$get_featured_image_filter = apply_filters("discy_featured_image_question",$get_featured_image,$question_id);
		}
		$question_class = "article-question article-post question clearfix".(isset($is_singular)?" single-question":"");
		$question_class .= (!isset($is_singular) && $question_answer_place == "before"?" question-answer-before":"");
		$question_class .= (isset($wpqa_vote)?" question-vote-".$vote_style:"");
		$question_class .= ($count_post_all > 0?" question-with-comments":" question-no-comments");
		$question_class .= (!$pending_questions_page && !isset($is_singular) && $read_more_question == "on" && $read_jquery_question == "on"?" load-question-jquery":"");
		$question_class .= (!$pending_questions_page && is_user_logged_in() && !isset($is_singular) && $answer_question_jquery == "on"?" answer-question-jquery":" answer-question-not-jquery");
		$question_class .= ($question_columns == "style_2"?" question-2-columns post-with-columns col ".($theme_sidebar == "full"?"col4":"col6").($masonry_style == "on"?" question-masonry":""):"");
		$question_class .= ($discoura_style == "on" || (!isset($wpqa_image) && (!isset($wpqa_vote) || (isset($wpqa_vote) && isset($wpqa_vote_meta))))?" question-full-width":"");
		$question_class .= (isset($wpqa_vote) && !isset($wpqa_image) && !isset($wpqa_vote_meta)?" question-vote-only":"");
		$question_class .= (isset($wpqa_vote_meta)?" question-meta-vote":"");
		$question_class .= ($question_simple == "on"?" question-simple":"");
		$question_class .= ($post_data->post_content == " post-no-content"?" post--content":"");
		$question_class .= (isset($is_singular) && ($share_style == "style_2" || $question_simple == "on")?" question-share-2":"");
		$question_class .= ((isset($wpqa_vote) && !isset($wpqa_vote_meta)) || (isset($wpqa_image) && $discoura_style != "on")?" question-vote-image":"");
		$question_class .= ($discoura_style == "on" && is_user_logged_in() && $privacy_credential == true && $profile_credential != ""?" discoura-credential":" discoura-not-credential");
		$question_class .= ($question_poll == "on"?" question-type-poll":" question-type-normal");?>
		<article id="post-<?php echo (int)$question_id?>" <?php post_class($question_class);echo (isset($is_singular)?' itemprop="mainEntity" itemscope itemtype="https://schema.org/Question"':'')?>>
			<?php if (isset($is_singular)) {
				do_action("wpqa_before_question_content",$question_id,$user_id,$anonymously_user,$post_author);
			}else {
				do_action("wpqa_before_question_content_loop",$question_id,$user_id,$anonymously_user,$post_author);
			}
			if ($question_columns == "style_2") {?>
				<div class="post-with-columns-border"></div>
			<?php }
			do_action("wpqa_question_article",$question_id,(isset($show_sticky) && $show_sticky == true?"sticky":""),$user_id,$anonymously_user,$post_author);
			if ((isset($show_sticky) && $show_sticky == true) || isset($is_singular)) {
				if ((isset($show_sticky) && $show_sticky == true && !isset($is_singular) && $question_sticky == "sticky") || (isset($is_singular) && $question_sticky == "sticky")) {?>
					<div class="question-sticky-ribbon"><div><?php esc_html_e("Pinned","discy")?></div></div>
				<?php }
				
				if (isset($is_singular)) {
					do_action("wpqa_question_content",$question_id,$user_id,$anonymously_user,$post_author);
				}else {
					do_action("wpqa_question_content_loop",$question_id,$user_id,$anonymously_user,$post_author);
				}
			}
			if (is_user_logged_in() && !isset($is_singular) && $answer_question_jquery == "on") {?>
				<div class='question-fixed-area discy_hide'><div class='load_span'><span class='loader_2'></span></div></div>
			<?php }
			if ($pending_questions) {?>
				<div class="load_span"><span class="loader_2"></span></div>
			<?php }?>
			<div class="single-inner-content">
				<div class="question-inner">
					<?php $question_vote = discy_post_meta("question_vote","",false);
					if ($question_vote == "") {
						update_post_meta($question_id,"question_vote",0);
					}
					$question_vote = (int)$question_vote;
					if ($discoura_style != "on" && $question_columns != "style_2" && (isset($wpqa_vote) || isset($wpqa_image))) {?>
						<div class="question-image-vote<?php echo (isset($wpqa_vote) && !isset($wpqa_vote_meta)?"":" question-image-not-vote")?>">
							<?php if (isset($wpqa_image)) {
								do_action("wpqa_action_avatar_link",array("user_id" => (isset($post_author) && $post_author > 0?$post_author:0),"size" => apply_filters("discy_question_profile_size","42"),"span" => "span","pop" => "pop","post" => $post,"name" => $question_username,"email" => (isset($question_email) && $question_email != ""?$question_email:"")));
							}
							if (isset($wpqa_vote) && !isset($wpqa_vote_meta)) {
								do_action("wpqa_question_vote",$post,$user_id,$anonymously_user,$question_vote,$question_loop_dislike,$question_single_dislike,"question-mobile");
							}?>
						</div><!-- End question-image-vote -->
					<?php }?>
					<div class="question-content question-content-first<?php echo ($discoura_style != "on" && isset($wpqa_image) && (isset($wpqa_vote_meta) || !isset($wpqa_vote))?" question-third-image":"").($question_date != "on" && $category_question != "on"?" no-data-category":"")?>">
						<?php if ($discoura_style != "on" && $question_columns == "style_2" && isset($wpqa_image)) {
							do_action("wpqa_action_avatar_link",array("user_id" => (isset($post_author) && $post_author > 0?$post_author:0),"size" => apply_filters("discy_question_profile_size","42"),"span" => "span","pop" => "pop","post" => $post,"name" => $question_username,"email" => (isset($question_email) && $question_email != ""?$question_email:"")));
						}
						do_action("wpqa_before_question_article_header",$post_data,$pending_questions,(isset($share_style)?$share_style:""),$post_share,$question_simple);?>
						<header class="article-header">
							<?php if ($question_poll == "on") {?>
								<a class="question-poll" href="<?php echo esc_url_raw(add_query_arg(array("type" => "poll"),get_post_type_archive_link(wpqa_questions_type)))?>"><?php esc_html_e("Poll","discy")?></a>
							<?php }
							do_action("discy_after_question_poll_mark",$question_id,$video_desc_active)?>
							<div class="question-header">
								<?php do_action("wpqa_before_question_author",$post_data);
								$wpqa_question_top_meta = apply_filters('wpqa_question_top_meta',true);
								if ($discoura_style != "on" && $wpqa_question_top_meta == true && $author_by == "on") {
									if ($post_author > 0) {
										if (isset($is_singular)) {
											echo '<span itemprop="author" itemscope itemtype="http://schema.org/Person">';
										}
										if (isset($get_author_profile)) {
											echo '<a class="post-author"'.(isset($is_singular)?' itemprop="url"':'').' href="'.esc_url($get_author_profile).'">';
										}else {
											echo '<a class="discy_hide"'.(isset($is_singular)?' itemprop="url"':'').' href="'.esc_url(home_url("/")).'"></a>';
										}
										if (isset($is_singular)) {
											echo '<span itemprop="name">';
										}
										echo apply_filters('wpqa_question_before_author',false).esc_html($question_username);
										if (isset($is_singular)) {
											echo '</span>';
										}
										if (isset($get_author_profile)) {
											echo '</a>';
										}
										if (isset($is_singular)) {
											echo '</span>';
										}
										do_action("wpqa_verified_user",$post_author);
										$active_points_category = discy_options("active_points_category");
										if ($active_points_category == "on") {
											$get_terms = wp_get_post_terms($question_id,wpqa_question_categories,array('fields' => 'ids'));
											if (!empty($get_terms) && is_array($get_terms) && isset($get_terms[0])) {
												$points_category_user = (int)get_user_meta($post_author,"points_category".$get_terms[0],true);
											}
										}
										do_action('discy_action_after_question_author',$question_id);
										do_action("wpqa_get_badge",$post_author,"",(isset($points_category_user)?$points_category_user:""));
									}else {
										echo '<span class="question-author-un"><a class="discy_hide"'.(isset($is_singular)?' itemprop="url"':'').' href="'.esc_url(home_url("/")).'"></a>';
										if (isset($is_singular)) {
											echo '<span itemprop="author" itemscope itemtype="http://schema.org/Person"><span itemprop="name">';
										}
										echo apply_filters('wpqa_question_before_author',false).esc_html($question_username);
										if (isset($is_singular)) {
											echo '</span></span>';
										}
										echo '</span>';
									}
								}
								if ($wpqa_question_top_meta == true && $question_date == "on" || ($get_question_user_id == "" && $category_question == "on") || $asked_to == "on") {?>
									<div class="post-meta">
										<?php discy_meta(apply_filters("discy_filter_question_date",$question_date),apply_filters("discy_filter_category_question",$category_question),"",$asked_to,"","",$question_id,$post_data);
										do_action("discy_action_after_question_meta",$question_id)?>
									</div>
								<?php }
								do_action("discy_action_after_question_header",$question_id,$post_author,$anonymously_user)?>
							</div>
						</header>
						<?php $show_title_question = apply_filters("discy_filter_show_title_question",true);
						if ($show_title_question == true) {?>
							<div>
								<?php if (isset($is_singular)) {
									$custom_page_setting = discy_post_meta("custom_page_setting");
									if ($custom_page_setting == "on") {
										$breadcrumbs = discy_post_meta("breadcrumbs");
									}else {
										$breadcrumbs = discy_options("breadcrumbs");
									}
									$breadcrumbs_style = discy_options("breadcrumbs_style");
									$breadcrumbs_content_title = discy_options("breadcrumbs_content_title");
									if ($breadcrumbs != "on" || ($breadcrumbs == "on" && ($breadcrumbs_style != "style_2" || ($breadcrumbs_content_title != "on" && $breadcrumbs_style == "style_2")))) {
										the_title( '<'.($breadcrumbs == "on" && $breadcrumbs_style == "style_2"?"h2":"h1").' class="'.(isset($title_post_style) && $title_post_style == "style_2"?"post-title-2":"post-title").'">'.(isset($title_post_style) && $title_post_style == "style_2" && isset($title_post_icon) && $title_post_icon != ""?"<i class='".$title_post_icon."'></i>":"").'<span'.(isset($is_singular)?' itemprop="name"':'').'>', apply_filters("discy_filter_after_title_question",false).'</span></'.($breadcrumbs == "on" && $breadcrumbs_style == "style_2"?"h2":"h1").'>' );
									}else if (isset($is_singular)) {
										echo '<span class="discy_hide" itemprop="name">'.$post_data->post_title.'</span>';
									}
								}else {
									if ($pending_questions) {
										the_title( '<h2 class="'.(isset($title_post_style) && $title_post_style == "style_2"?"post-title-2":"post-title").'">'.(isset($title_post_style) && $title_post_style == "style_2" && isset($title_post_icon) && $title_post_icon != ""?"<i class='".$title_post_icon."'></i>":""), apply_filters("discy_filter_after_title_question",false).'</h2>' );
									}else {
										the_title( '<h2 class="post-title"><a'.$question_link_target.' class="post-title" href="' . esc_url( get_permalink() ) . '" rel="bookmark">', apply_filters("discy_filter_after_title_question",false).'</a></h2>' );
									}
								}?>
							</div>
							<?php do_action("discy_action_after_question_title",$question_id);
						}?>
					</div><!-- End question-content-first -->
					<?php if ($discoura_style == "on") {?>
						<div class="question-image-vote<?php echo (isset($wpqa_vote)?"":" question-image-not-vote")?>">
							<?php if (isset($wpqa_image)) {
								do_action("wpqa_action_avatar_link",array("user_id" => (isset($post_author) && $post_author > 0?$post_author:0),"size" => apply_filters("discy_question_profile_size","42"),"span" => "span","pop" => "pop","post" => $post,"name" => $question_username,"email" => (isset($question_email) && $question_email != ""?$question_email:"")));
							}
							if ($post_author > 0) {
								if (isset($is_singular)) {
									echo '<span itemprop="author" itemscope itemtype="http://schema.org/Person">';
								}
								if (isset($get_author_profile)) {
									echo '<a class="post-author" itemprop="url" href="'.esc_url($get_author_profile).'">';
								}else {
									echo '<a class="discy_hide"'.(isset($is_singular)?' itemprop="url"':'').' href="'.esc_url(home_url("/")).'"></a>';
								}
								if (isset($is_singular)) {
									echo '<span itemprop="name">';
								}
								echo apply_filters('wpqa_question_before_author',false).esc_html($question_username);
								if (isset($is_singular)) {
									echo '</span>';
								}
								if (isset($get_author_profile)) {
									echo '</a>';
								}
								if (isset($is_singular)) {
									echo '</span>';
								}
								do_action("wpqa_verified_user",$post_author);
								$active_points_category = discy_options("active_points_category");
								if ($active_points_category == "on") {
									$get_terms = wp_get_post_terms($question_id,wpqa_question_categories,array('fields' => 'ids'));
									if (!empty($get_terms) && is_array($get_terms) && isset($get_terms[0])) {
										$points_category_user = (int)get_user_meta($post_author,"points_category".$get_terms[0],true);
									}
								}
								do_action('discy_action_after_question_author',$question_id);
								do_action("wpqa_get_badge",$post_author,"",(isset($points_category_user)?$points_category_user:""));
								if ($privacy_credential == true && $profile_credential != "") {?>
	                        		<span class="profile-credential"><?php echo esc_html($profile_credential)?></span>
	                        	<?php }
							}else {
								echo '<span class="question-author-un"><a class="discy_hide"'.(isset($is_singular)?' itemprop="url"':'').' href="'.esc_url(home_url("/")).'"></a>';
								if (isset($is_singular)) {
									echo '<span itemprop="author" itemscope itemtype="http://schema.org/Person"><span itemprop="name">';
								}
								echo apply_filters('wpqa_question_before_author',false).esc_html($question_username);
								if (isset($is_singular)) {
									echo '</span></span>';
								}
								echo '</span>';
							}
                        	?>
						</div>
					<?php }
					if (isset($wpqa_vote) || isset($wpqa_image)) { ?>
						<div class="question-not-mobile question-image-vote question-vote-sticky<?php echo (isset($wpqa_vote)?"":" question-image-not-vote")?>">
							<div class="<?php echo apply_filters('wpqa_question_sticky_image_vote','question-sticky-stop')?>">
								<?php if (isset($wpqa_vote) && !isset($wpqa_vote_meta)) {
									do_action("wpqa_question_vote",$post,$user_id,$anonymously_user,$question_vote,$question_loop_dislike,$question_single_dislike,"");
								}?>
							</div><!-- End question-sticky -->
						</div><!-- End question-image-vote -->
					<?php }?>
					<div class="question-content question-content-second<?php echo ($discoura_style != "on" && isset($wpqa_image) && (isset($wpqa_vote_meta) || !isset($wpqa_vote))?" question-third-image":"").($question_date != "on" && $category_question != "on"?" no-data-category":"")?>">
						<?php if (isset($is_singular)) {?>
							<div class="wpqa_error"></div>
							<div class="wpqa_success"></div>
						<?php }
						if ($yes_private != 1 && $private_question_content == "on") {?>
							<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p><?php esc_html_e("Sorry it's a private question.","discy");?></p></div>
						<?php }else {?>
							<div class="post-wrap-content">
								<?php do_action("discy_action_before_question_content_1",(isset($question_url_1)?$question_url_1:""),(isset($question_url_2)?$question_url_2:""),$featured_image_question_width,$featured_image_question_height,(isset($img_lightbox)?$img_lightbox:""));?>
								<div class="question-content-text">
									<?php do_action("discy_action_before_question_content",$pending_questions,(isset($title_post_style)?$title_post_style:""),(isset($title_post_icon)?$title_post_icon:""));
									$filter_question_image = apply_filters("discy_filter_question_image",true);
									$filter_question_content = apply_filters("discy_filter_question_content",true,array("post" => $post,"discy_sidebar" => $theme_sidebar,"excerpt_type" => $excerpt_type,"excerpt_questions" => $excerpt_questions,"question_excerpt" => $question_excerpt,"read_more_question" => $read_more_question,"question_date" => $question_date,"category_question" => $category_question,"question_columns" => $question_columns,"question_poll" => $question_poll,"author_by" => $author_by,"get_question_user_id" => $get_question_user_id,"asked_to" => $asked_to,"question_username" => $question_username,"question_email" => $question_email,"wpqa_image" => (isset($wpqa_image)?$wpqa_image:false),"anonymously_user" => $anonymously_user));

									do_action("discy_action_question_content",array("post" => $post,"discy_sidebar" => $theme_sidebar,"excerpt_type" => $excerpt_type,"excerpt_questions" => $excerpt_questions,"question_excerpt" => $question_excerpt,"read_more_question" => $read_more_question,"question_date" => $question_date,"category_question" => $category_question,"question_columns" => $question_columns,"question_poll" => $question_poll,"author_by" => $author_by,"get_question_user_id" => $get_question_user_id,"asked_to" => $asked_to,"question_username" => $question_username,"question_email" => $question_email,"wpqa_image" => (isset($wpqa_image)?$wpqa_image:false),"anonymously_user" => $anonymously_user));

									if ($filter_question_content == true) {
										$get_the_content = get_the_content();
										$get_the_content = apply_filters('the_content',$get_the_content);
										if ($pending_questions || isset($is_singular)) {
											// In single page
											echo "<div class='all_single_post_content".($pending_questions?" discy_hide":"")."'>";
												if (($featured_position == "before" && $poll_position == "before") || ($poll_position == "before_content")) {
													echo (has_wpqa() && wpqa_plugin_version >= "5.8.1"?wpqa_show_poll($question_id,$user_id,$question_poll,$pending_questions):"");
												}
												if ($featured_position == "before") {
													if ($filter_question_image == true && isset($get_featured_image_filter) && $get_featured_image_filter != "") {
														echo "<div class='featured_image_question'>".$get_featured_image_filter."</div><div class='clearfix'></div>";
													}
													if ($poll_position == "after") {
														echo (has_wpqa() && wpqa_plugin_version >= "5.8.1"?wpqa_show_poll($question_id,$user_id,$question_poll,$pending_questions,"poll-area-after-image"):"");
													}
												}
												$filter_question_video = apply_filters("discy_filter_question_video",true);
												if ($filter_question_video == true) {
													$video_description = discy_post_meta("video_description","",false);
													if ($video_desc_active == "on" && $video_description == "on") {
														$video_id = discy_post_meta("video_id","",false);
														$video_type = discy_post_meta("video_type","",false);
														if ($video_id != "") {
															$type = (has_wpqa()?wpqa_video_iframe($video_type,$video_id,"post_meta","video_id",$question_id):"");
															$video_desc_height = discy_options("video_desc_height");
															if ($video_desc_height == "") {
																if ($theme_sidebar == "menu_sidebar") {
																	$video_height = "420";
																}else if ($theme_sidebar == "menu_left") {
																	$video_height = "600";
																}else if ($theme_sidebar == "full") {
																	$video_height = "700";
																}else if ($theme_sidebar == "centered") {
																	$video_height = "510";
																}else {
																	$video_height = "550";
																}
															}else {
																$video_height = $video_desc_height;
															}
															
															$last_video = '<div class="question-video video-type-'.$video_type.'"><iframe frameborder="0" allowfullscreen height="'.$video_height.'" src="'.$type.apply_filters('discy_after_video_type',false,$question_id).'"></iframe></div>';
															
															if ($video_desc == "before" && $video_desc_active == "on" && isset($video_id) && $video_id != "" && $video_description == "on") {
																echo ($last_video);
															}
														}
													}
												}?>
												<div class="content-text<?php echo (isset($is_singular) && $get_the_content == ""?" discy_hide":"")?>"<?php echo (isset($is_singular)?' itemprop="text"':'')?>>
													<?php if (isset($is_singular) && $get_the_content == "") {
														the_title();
													}
													echo make_clickable($get_the_content)?>
												</div>
												
												<?php if ($featured_position == "after") {
													if ($poll_position == "before") {
														echo (has_wpqa() && wpqa_plugin_version >= "5.8.1"?wpqa_show_poll($question_id,$user_id,$question_poll,$pending_questions):"");
													}
													if ($filter_question_image == true && isset($get_featured_image_filter) && $get_featured_image_filter != "") {
														echo "<div class='featured_image_question featured_image_after'>".$get_featured_image_filter."</div><div class='clearfix'></div>";
													}
												}
												if (($poll_position == "after" && $featured_position == "after") || $poll_position == "after_content") {
													echo (has_wpqa() && wpqa_plugin_version >= "5.8.1"?wpqa_show_poll($question_id,$user_id,$question_poll,$pending_questions,"poll-area-after-content"):"");
												}
												
												if ($filter_question_video == true) {
													if ($video_desc == "after" && $video_desc_active == "on" && isset($video_id) && $video_id != "" && $video_description == "on") {
														echo ($last_video);
													}
												}
												do_action("discy_action_question_video_after");
												
												if (isset($is_singular)) {
													do_action("wpqa_after_question_area",$question_id,$user_id,$anonymously_user,$post_author,$count_post_all,$featured_image_question,$featured_position);
												}
												
												$show_attachment_filter = apply_filters("discy_show_attachment_filter",true);
												if ($show_attachment_filter == true) {
													$added_file = discy_post_meta("added_file","",false);
													$attachment_m = discy_post_meta("attachment_m","",false);
													if ($added_file != "" || (isset($attachment_m) && is_array($attachment_m) && !empty($attachment_m))) {
														echo "<div class='attachment-links'>";
															if ($added_file != "") {
																$img_url = wp_get_attachment_url($added_file);
																$file = get_attached_file($added_file);
																echo "<a class='attachment-link' title='".esc_html(wp_basename($file))."' href='".$img_url."'><i class='icon-link'></i>".esc_html__("Attachment","discy")."</a>";
															}
															
															if (isset($attachment_m) && is_array($attachment_m) && !empty($attachment_m)) {
																foreach ($attachment_m as $key => $value) {
																	$img_url = wp_get_attachment_url($value["added_file"]);
																	$file = get_attached_file($value["added_file"]);
																	echo "<a class='attachment-link' title='".esc_html(wp_basename($file))."' href='".$img_url."'><i class='icon-link'></i>".esc_html__("Attachment","discy")."</a>";
																}
															}
														echo "</div>";
													}
												}
											echo "</div><!-- End all_single_post_content -->";
										}
										if ($pending_questions || !isset($is_singular)) {
											// NOT single page
											echo "<div class='all_not_single_post_content'>";
												if ($question_poll_loop == "on" && (($featured_position == "before" && $poll_position == "before") || $poll_position == "before_content")) {
													echo (has_wpqa() && wpqa_plugin_version >= "5.8.1"?wpqa_show_poll($question_id,$user_id,$question_poll,$pending_questions):"");
												}
												if ($featured_position == "before") {
													if ($filter_question_image == true && isset($get_featured_image) && $get_featured_image != "") {
														echo "<div class='featured_image_question'>".(isset($question_url_1)?$question_url_1:"").$get_featured_image.(isset($question_url_2)?$question_url_2:"")."</div><div class='clearfix'></div>";
													}
													if ($question_poll_loop == "on" && $poll_position == "after") {
														echo (has_wpqa() && wpqa_plugin_version >= "5.8.1"?wpqa_show_poll($question_id,$user_id,$question_poll,$pending_questions,"poll-area-after-image"):"");
													}
												}
												$filter_question_video = apply_filters("discy_filter_question_video",true);
												if ($filter_question_video == true) {
													$video_desc_active_loop = discy_options("video_desc_active_loop");
													if ($video_desc_active_loop == "on") {
														$video_desc_loop = discy_options("video_desc_loop");
														$video_description_width = discy_options("video_description_width");
														$video_desc_100_loop = discy_options("video_desc_100_loop");
														$video_description_height = discy_options("video_description_height");
														
														$video_description = discy_post_meta("video_description","",false);
														if ($video_desc_active == "on" && $video_description == "on") {
															$video_desc = discy_post_meta("video_desc","",false);
															$video_id = discy_post_meta("video_id","",false);
															$video_type = discy_post_meta("video_type","",false);
															if ($video_id != "") {
																$type = (has_wpqa()?wpqa_video_iframe($video_type,$video_id,"post_meta","video_id",$question_id):"");
																$last_video = '<div class="question-video-loop video-type-'.$video_type.($video_desc_100_loop == "on"?' question-video-loop-100':'').($video_desc_loop == "after"?' question-video-loop-after':'').'"><iframe'.apply_filters('discy_video_iframe',false,$question_id).' frameborder="0" allowfullscreen height="'.$video_description_height.'" width="'.$video_description_width.'" src="'.$type.apply_filters('discy_after_video_type',false,$question_id).'"></iframe></div>';
																if ($video_desc_loop == "before") {
																	echo ($last_video);
																}
															}
														}
													}
												}
												$show_full_text = apply_filters("discy_question_show_full_text",true);
												if ($show_full_text == true) {
													if ($excerpt_questions != "on") {
														$question_excerpt = (isset($question_excerpt) && $question_excerpt != ""?$question_excerpt:40);?>
														<p class="excerpt-question"><?php echo apply_filters("discy_question_excerpt",discy_excerpt($question_excerpt,$excerpt_type,$read_more_question,"return"));?></p>
														<?php if ($read_more_question == "on" && $read_jquery_question == "on") {?>
															<div class="content-question-jquery discy_hide"><?php echo make_clickable($get_the_content);?><a class="question-read-less" href="#" title="<?php echo esc_attr__('Read less','discy').' '.get_the_title($question_id)?>"><?php esc_html_e('Read less','discy')?></a></div>
														<?php }
													}
												}else {
													echo make_clickable($get_the_content);
												}
												
												if ($featured_position == "after") {
													if ($question_poll_loop == "on" && $poll_position == "before") {
														echo (has_wpqa() && wpqa_plugin_version >= "5.8.1"?wpqa_show_poll($question_id,$user_id,$question_poll,$pending_questions):"");
													}
													$filter_question_image_loop = apply_filters("discy_filter_question_image_loop",true);
													if ($filter_question_image_loop == true && $filter_question_image == true && isset($get_featured_image) && $get_featured_image != "") {
														echo "<div class='featured_image_question featured_image_after'>".(isset($question_url_1)?$question_url_1:"").$get_featured_image.(isset($question_url_2)?$question_url_2:"")."</div><div class='clearfix'></div>";
													}
												}
												if ($question_poll_loop == "on" && (($poll_position == "after" && $featured_position == "after") || $poll_position == "after_content")) {
													echo (has_wpqa() && wpqa_plugin_version >= "5.8.1"?wpqa_show_poll($question_id,$user_id,$question_poll,$pending_questions,"poll-area-after-content"):"");
												}
												
												if ($filter_question_video == true) {
													if ($video_desc_active_loop == "on" && isset($video_desc_loop) && $video_desc_loop == "after" && isset($video_desc_active) && $video_desc_active == "on" && isset($video_id) && $video_id != "" && isset($video_description) && $video_description == "on") {
														echo ($last_video);
													}
												}
												do_action("discy_action_question_video_after");
											echo "</div><!-- End all_not_single_post_content -->";
										}
									}
									do_action("discy_action_after_question_content",$question_id);?>
								</div>
								<?php do_action("wpqa_question_after_content",$post_data,$user_id,$anonymously_user,$question_edit,$question_delete,$question_close,$closed_question,$active_reports,$active_logged_reports,$moderators_permissions,$get_the_content);
								if (isset($is_singular)) {
									wp_link_pages(array('before' => '<div class="pagination post-pagination">','after' => '</div>','link_before' => '<span>','link_after' => '</span>'));
								}
								if ((isset($is_singular) && $question_tags == "on") || (!isset($is_singular) && $question_tags_loop == "on")) {
									$terms = wp_get_object_terms( $question_id, wpqa_question_tags );
									if (isset($terms) && is_array($terms) && !empty($terms)) {
										echo '<div class="tagcloud"><div class="question-tags"><i class="icon-tags"></i>';
											$terms_array = array();
											foreach ($terms as $term) :
												if (isset($term->slug) && isset($term->name)) {
													$get_term_link = get_term_link($term);
													if (is_string($get_term_link)) {
														echo '<a href="'.$get_term_link.'">'.$term->name.'</a>';
													}
												}
											endforeach;
										echo '</div></div>';
									}
								}
								do_action("discy_after_question_tags",$question_id);?>
							</div>
							<?php if (!isset($is_singular)) {
								do_action('wpqa_question_after_tags',$question_id,$post,$author_by,$anonymously_user,$question_date,$category_question,$asked_to,$get_question_user_id);?>
								<div class="wpqa_error"></div>
								<div class="wpqa_success"></div>
								<?php if ($question_answer_place == "before") {
									include locate_template("comment-parts/inner-answer.php");
								}
							}
						}
						do_action("wpqa_before_question_meta",$question_id,$post,$anonymously_user,$user_id);
						$following_questions = discy_post_meta("following_questions","",false);
						$following_questions = (is_array($following_questions) && !empty($following_questions)?get_users(array('fields' => 'ID','include' => $following_questions,'orderby' => 'registered')):array());
						if ($pending_questions || $question_answer == "on" || $question_views == "on" || ($question_follow_loop == "on" && !isset($is_singular)) || ($question_follow == "on" && isset($is_singular)) || ($active_vote == "on" && $vote_question_loop != "on" && !isset($is_singular)) || ($question_favorite == "on" && (isset($is_singular) || (isset($first_one) && $first_one === "favorites"))) || ($question_answer == "on" && (!isset($is_singular) || (isset($is_singular) && $user_id != $get_question_user_id && (($user_id == $post_author && $user_id > 0) || ($anonymously_user != "" && $anonymously_user == $user_id)))))) {?>
							<footer class="question-footer<?php echo ($pending_questions?" pending-post-footer":"").($question_meta_icon == "on"?" question-footer-icons":"")?>">
								<?php if (has_wpqa() && $pending_questions) {
									wpqa_review_post($post,$is_super_admin,$moderators_permissions);
								}else {
									if (isset($wpqa_vote_meta)) {
										do_action("wpqa_question_vote",$post,$user_id,$anonymously_user,$question_vote,$question_loop_dislike,$question_single_dislike);
									}?>
									<ul class="footer-meta">
										<?php if ($question_answer == "on") {?>
											<li class="best-answer-meta<?php echo ($the_best_answer != ""?" meta-best-answer":"")?>"><a<?php echo esc_attr($question_link_target)?> href="<?php echo get_permalink()?>#comments"><i class="icon-comment"></i><?php discy_meta("","",$question_answer,"",$question_meta_icon,"",$question_id,$post_data)?></a></li>
										<?php }
										$active_post_stats = discy_options("active_post_stats");
										if (has_wpqa() && $question_views == "on" && $active_post_stats == "on") {
											$post_stats = wpqa_get_post_stats($question_id);?>
											<li class="view-stats-meta"><i class="icon-eye"></i><?php echo discy_count_number($post_stats).($question_meta_icon != "on"?" <span class='question-span'>"._n("View","Views",$post_stats,"discy")."</span>":"")?></li>
										<?php }

										if ($active_vote == "on" && $vote_question_loop != "on" && !isset($is_singular) && $vote_question_single == "on") {?>
											<li class="votes-meta"><i class="icon-chart-bar"></i><?php echo discy_count_number($question_vote).($question_meta_icon != "on"?" <span class='question-span'>"._n("Vote","Votes",$question_vote,"discy")."</span>":"")?></li>
										<?php }
										
										$question_bump = discy_options("question_bump");
										$active_points = discy_options("active_points");
										if ($bump_meta == "on" && $question_bump == "on" && $active_points == "on" && ((isset($is_singular)) || (!isset($is_singular) && isset($orderby_post) && $orderby_post == "question_bump"))) {
											if ($count_post_all == 0) {
												$question_points = (int)discy_post_meta("question_points","",false);
												if ($question_points > 0) {
													$bump_meta_show = true;?>
													<li class="question-bump-meta"><i class="icon-heart"></i><?php $question_points = (int)discy_post_meta("question_points","",false);
													echo (int)$question_points.($question_meta_icon != "on"?" <span class='question-span'>"._n("Point","Points",$question_points,"discy")."</span>":"")?></li>
												<?php }
											}
										}
										$activate_login = discy_options("activate_login");
										$show_the_follow_question = apply_filters("discy_show_the_follow_question",false);
										if (($question_columns == "style_2" && $question_follow_loop == "on" && !isset($is_singular) && $show_the_follow_question == true) || ($question_columns != "style_2" && $question_follow_loop == "on" && !isset($is_singular)) || ($question_follow == "on" && isset($is_singular))) {
											$follow_meta_show = true;
											$what_follow = $what_unfollow = false;
											$following_questions_number = (int)(isset($following_questions) && is_array($following_questions)?discy_count_number(count($following_questions)):0);
											if ($user_id > 0 && $user_id != $get_question_user_id && is_user_logged_in() && ($user_id != $post_author || ($anonymously_user != "" && $anonymously_user != $user_id))) {
												$what_follow = true;
											}
											if ((isset($following_questions) && is_array($following_questions) && in_array($user_id,$following_questions))) {
												$what_unfollow = true;
											}?>
											<li class="question-followers<?php echo ($what_follow == true?"":" question-followers-no-link").($what_follow == true && isset($following_questions) && is_array($following_questions) && in_array($user_id,$following_questions)?" li-follow-question":"")?>">
												<?php if ($what_follow == true) {?>
													<div class="small_loader loader_2"></div>
													<a href="#"<?php echo ($what_unfollow == true?' class="unfollow-question"':'')?> title="<?php echo ($what_unfollow == true?esc_attr__("Unfollow the question","discy"):esc_attr__("Follow the question","discy"))?>"><i class="<?php echo ($what_unfollow == true?"icon-minus":"icon-plus")?>"></i>
												<?php }else {
													if ($activate_login != 'disabled' && !is_user_logged_in()) {?>
														<a href="<?php echo (has_wpqa()?wpqa_login_permalink():"#")?>" class="login-panel<?php echo apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_login','')?>" title="<?php esc_attr_e("Follow the question","discy")?>">
													<?php }?>
													<i class="icon-users"></i>
												<?php }
													echo "<span class='question-follow-count'>".discy_count_number($following_questions_number)."</span>".($question_meta_icon != "on"?" <span class='question-span'>"._n("Follower","Followers",$following_questions_number,"discy")."</span>":"");
												if ($what_follow == true || ($activate_login != 'disabled' && !is_user_logged_in())) {?>
													</a>
												<?php }?>
											</li>
											<?php do_action("discy_end_footer_meta_list",$question_id);
										}

										if ($question_favorite == "on" && (isset($is_singular) || (has_wpqa() && wpqa_is_user_favorites()) || (isset($first_one) && $first_one === "favorites"))) {
											$question_favorites = discy_post_meta("question_favorites","",false);
											if (is_user_logged_in() && $user_id != $get_question_user_id && (($user_id != $post_author && $user_id > 0) || ($anonymously_user != "" && $anonymously_user != $user_id))) {
												$_favorites = get_user_meta($user_id,$user_id."_favorites",true);
												if (isset($_favorites) && is_array($_favorites) && in_array($question_id,$_favorites)) {
													$what_favorite = "remove_favorite";
												}else {
													$what_favorite = "add_favorite";
												}
											}?>
											<li class="question-favorites<?php echo ((isset($what_favorite) && ($what_favorite == "add_favorite" || $what_favorite == "remove_favorite"))?"":" question-favorites-no-link").(isset($what_favorite) && $what_favorite == "remove_favorite"?" active-favorite":"");?>">
												<div class="small_loader loader_2"></div>
												<?php if (isset($what_favorite) && ($what_favorite == "add_favorite" || $what_favorite == "remove_favorite")) {
													echo '<a class="'.($what_favorite == "add_favorite"?'add_favorite':'remove_favorite').'" title="'.($what_favorite == "add_favorite"?esc_html__("Add this question to favorites","discy"):esc_html__("Remove this question of my favorites","discy")).'" href="#">';
												}else if ($activate_login != 'disabled' && !is_user_logged_in()) {?>
													<a href="<?php echo (has_wpqa()?wpqa_login_permalink():"#")?>" class="login-panel<?php echo apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_login','')?>" title="<?php esc_attr_e("Add this question to favorites","discy")?>">
												<?php }?>
												<i class="icon-star"></i>
												<span><?php echo ($question_favorites != ""?discy_count_number($question_favorites):0);?></span>
												<?php echo ((isset($what_favorite) && ($what_favorite == "add_favorite" || $what_favorite == "remove_favorite")) || ($activate_login != 'disabled' && !is_user_logged_in())?'</a>':'')?>
											</li>
										<?php }
										
										if (isset($is_singular) && $question_simple == "on" && ((is_user_logged_in() && ($question_edit == "on" || $question_delete == "on" || $question_close == "on" || $is_super_admin)) || ($active_reports == "on" && (is_user_logged_in() || (!is_user_logged_in() && $active_logged_reports != "on"))))) {?>
											<li class="question-list-details">
												<i class="icon-dot-3"></i>
												<?php do_action("wpqa_question_list_details",$post,$user_id,$anonymously_user,$question_edit,$question_delete,$question_close,$closed_question,$active_reports,$active_logged_reports,$moderators_permissions);?>
											</li>
										<?php }
										
										if (has_wpqa() && isset($is_singular) && ($share_style == "style_2" || $question_simple == "on") && ($share_facebook == "share_facebook" || $share_twitter == "share_twitter" || $share_linkedin == "share_linkedin" || $share_whatsapp == "share_whatsapp")) {?>
											<li class="question-share">
												<i class="icon-share"></i><?php echo ($question_meta_icon != "on"?"<span class='question-span'>".esc_html__("Share","discy")."</span>":"");
												wpqa_share($post_share,$share_facebook,$share_twitter,$share_linkedin,$share_whatsapp,($question_simple == "on"?"style_2":$share_style));?>
											</li>
										<?php }?>
									</ul>
									<?php if (isset($follow_meta_show) && isset($bump_meta_show)) {
										$not_show_answer = true;
									}
									if ($post_data->comment_status == "open" && $question_simple != "on" && $question_answer == "on" && (!isset($is_singular) || (isset($is_singular) && $place_comment_form == "after" && !isset($not_show_answer)))) {?>
										<a<?php echo esc_attr($question_link_target)?> class="meta-answer meta-answer-a" href="<?php echo get_permalink()?>#respond"><?php echo esc_html_x("Answer","Answer button on question","discy")?></a>
									<?php }
								}?>
							</footer>
						<?php }
						do_action("wpqa_after_question_meta",$question_id,$post,$anonymously_user,$user_id);?>
					</div><!-- End question-content-second -->
					<div class="clearfix"></div>
				</div><!-- End question-inner -->
				<?php if (isset($is_singular)) {
					if ($question_simple != "on" && (($question_edit == "on" || $question_delete == "on" || $question_close == "on" || $is_super_admin) || ($active_reports == "on" && (is_user_logged_in() || (!is_user_logged_in() && $active_logged_reports != "on"))) || ($share_style != "style_2" && ($share_facebook == "share_facebook" || $share_twitter == "share_twitter" || $share_linkedin == "share_linkedin" || $share_whatsapp == "share_whatsapp")))) {?>
						<div class="question-bottom">
							<?php if (has_wpqa() && $share_style != "style_2" && $question_simple != "on") {
								wpqa_share($post_share,$share_facebook,$share_twitter,$share_linkedin,$share_whatsapp);
							}
							do_action("wpqa_question_list_details",$post,$user_id,$anonymously_user,$question_edit,$question_delete,$question_close,$closed_question,$active_reports,$active_logged_reports,$moderators_permissions);?>
							<div class="clearfix"></div>
						</div><!-- End question-bottom -->
					<?php }
				}else if ($question_answer_place == "after") {
					include locate_template("comment-parts/inner-answer.php");
				}?>
			</div><!-- End single-inner-content -->
			<?php if (isset($is_singular)) {
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
					
					if (isset($first_one) && $first_one != "") {
						$first_one = $answers_tabs[$answers_tabs_keys[$first_one]]["value"];
					}
					
					if (isset($_GET["show"]) && $_GET["show"] != "") {
						$first_one = $_GET["show"];
					}
				}
				if ($question_related == "on" && $question_related_position != "after_answers") {
					include locate_template("theme-parts/related.php");
				}?>
				<div class="question-adv-comments <?php echo ($place_comment_form == "before"?"question-comments-before":"question-after-before").' '.($count_post_all > 0 && $question_answers == "on"?"question-has-comments":"question-not-comments").(isset($first_one) && $first_one != ""?" question-has-tabs":"")?>">
					<?php if (has_wpqa() && wpqa_plugin_version >= "5.8") {
						echo wpqa_ads("share_adv_type","share_adv_link","share_adv_code","share_adv_href","share_adv_img","","on","aalan-inside");
					}
					if ((comments_open() || $count_post_all > 0) && $question_answers == "on") {
						comments_template();
					}?>
				</div>
				<?php if ($question_related == "on" && $question_related_position == "after_answers") {
					include locate_template("theme-parts/related.php");
				}
			}?>
		</article><!-- End article -->
	<?php }else {
		echo '<article class="article-question private-question article-post clearfix">
			<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to view questions.","discy").'</p></div>
			'.(has_wpqa()?wpqa_paid_subscriptions(true):'').'
		</article>';
	}
}?>