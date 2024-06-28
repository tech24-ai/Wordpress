<?php if (isset($post_data)) {
	$post_data = $post_data;
}else if (isset($GLOBALS['post'])) {
	$post_data = $post = $GLOBALS['post'];
}else {
	$post_data = $post;
}
$post_author = $post_data->post_author;
$post_id = $post_data->ID;
$is_sticky = ($post_data->post_status == 'publish'?is_sticky($post_id):false);
$edit_delete_posts_comments = discy_options("edit_delete_posts_comments");
$group_id = get_post_meta($post_id,"group_id",true);
$group_moderators = get_post_meta($group_id,"group_moderators",true);
if ($user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
	$allow_group_moderators = true;
}
$format_date_ago = discy_options("format_date_ago");
$format_date_ago_types = discy_options("format_date_ago_types");
if (!isset($format_date_ago_types["group_posts"]) || ($format_date_ago == "on" && isset($format_date_ago_types["group_posts"]) && $format_date_ago_types["group_posts"] == "group_posts")) {
	$last_time = human_time_diff(get_the_time('U',$post_id),current_time('timestamp'))." ".esc_html__("ago","discy");
}else {
	$last_time = get_the_time(discy_date_format,$post_id);
}
$posts_class = 'article-post clearfix content_group_item';?>
<article id="post-<?php echo (int)$post_id?>" <?php post_class($posts_class)?>>
	<?php if ($is_sticky == true) {?>
		<div class="question-sticky-ribbon posts-sticky-ribbon"><div><?php esc_html_e("Pinned","discy")?></div></div>
	<?php }?>
	<div class="content_group_item_header">
		<div class="group_avatar"><?php do_action("wpqa_action_avatar_link",array("user_id" => $post_author,"size" => 50,"span" => "span","pop" => "pop"));?></div>
		<div class="col12">
			<div class="header-info">
				<div class="title">
					<h3>
						<?php $display_name = get_the_author_meta("display_name",$post_author);?>
						<a href="<?php echo (has_wpqa()?wpqa_profile_url($post_author):"");?>" title="<?php echo esc_attr($display_name);?>"><?php echo esc_html($display_name);?></a>
						<?php do_action("wpqa_verified_user",$post_author);
						do_action("wpqa_get_badge",$post_author,"","","category_points");?>
					</h3>
					<div class="posts-action">
						<a href="<?php echo wpqa_custom_permalink($post_id,"view_group_posts","view_group_post")?>"><i class="icon-lifebuoy"></i><?php echo esc_html($last_time);?></a>
						<?php $posts_delete = discy_options("posts_delete");
						if (($post_data->post_status == 'publish' && $posts_delete == "on" && $post_author == $user_id) || (isset($edit_delete_posts_comments["delete"]) && $edit_delete_posts_comments["delete"] == "delete" && isset($allow_group_moderators)) || $is_super_admin) {?>
							<a class="posts-delete" href="<?php echo esc_url_raw(add_query_arg(array("activate_delete" => true,"delete" => $post_id,"wpqa_delete_nonce" => wp_create_nonce("wpqa_delete_nonce")),wpqa_custom_permalink($post_id,"view_group_posts","view_group_post")))?>"><i class="icon-trash"></i><?php esc_html_e("Delete","discy")?></a>
						<?php }?>
					</div>
				</div>
				
			</div>
		</div>
	</div>
	<?php $featured_image_group_posts = discy_options("featured_image_group_posts");
	if ($featured_image_group_posts == "on") {
		$featured_image = get_post_meta($post_id,"_thumbnail_id",true);
		if ($featured_image != "") {
			$img_url = wp_get_attachment_url($featured_image,"full");
			if ($img_url != "") {
    			$featured_image_group_posts_lightbox = discy_options("featured_image_group_posts_lightbox");
    			$featured_image_group_posts_width = discy_options("featured_image_group_posts_width");
    			$featured_image_group_posts_height = discy_options("featured_image_group_posts_height");
    			$featured_image_group_posts_width = ($featured_image_group_posts_width != ""?$featured_image_group_posts_width:260);
    			$featured_image_group_posts_height = ($featured_image_group_posts_height != ""?$featured_image_group_posts_height:185);
    			$link_url = ($featured_image_group_posts_lightbox == "on"?$img_url:wpqa_custom_permalink($post_id,"view_group_posts","view_group_post"));
    			$last_image = discy_get_aq_resize_img($featured_image_group_posts_width,$featured_image_group_posts_height,"",$featured_image);
    			if (isset($last_image) && $last_image != "") {
    	    		echo "<div class='featured_image_group_posts'><a href='".$link_url."'>".$last_image."</a></div>
    	    		<div class='clearfix'></div>";
    			}
    		}
		}
	}
	$get_the_content = get_the_content();
	$get_the_content = apply_filters('the_content',$get_the_content);
	echo make_clickable($get_the_content);
	$editor_group_post_comments = discy_options("editor_group_post_comments");
	$featured_image_group_post_comments = discy_options("featured_image_group_post_comments");
	$posts_like = get_post_meta($post_id,"posts_like",true);
	$post_like_all = (is_array($posts_like)?count($posts_like):0);
	$group_comments = get_post_meta($group_id,"group_comments",true);
	$custom_posts_edit = discy_options("posts_edit");
	$count_post_all = (int)wpqa_count_comments($post_id);

	if (is_user_logged_in() && ($is_super_admin || ($post_data->post_status == 'publish' && $custom_posts_edit == "on" && $post_author == $user_id) || (isset($edit_delete_posts_comments["edit"]) && $edit_delete_posts_comments["edit"] == "edit" && isset($allow_group_moderators)))) {
		$show_group_footer = true;
	}

	if ($post_data->post_status == 'publish') {
		if ((is_user_logged_in() && $group_comments == "on") || isset($allow_group_moderators) || $is_super_admin) {
			$show_group_footer = true;
		}
	}else if (is_user_logged_in() && ($is_super_admin || (isset($edit_delete_posts_comments["edit"]) && $edit_delete_posts_comments["edit"] == "edit" && isset($allow_group_moderators)))) {
		$show_group_footer = true;
	}

	if (isset($show_group_footer)) {?>
		<footer class="question-footer posts-footer">
			<?php if ($post_data->post_status == 'publish') {?>
				<ul class="footer-meta">
					<li class="posts-likes">
						<div class="small_loader loader_2"></div>
						<?php if (is_user_logged_in()) {
							if (is_array($posts_like) && in_array($user_id,$posts_like)) {
								$class = "unlike-posts";
								$title = esc_html__("Unlike","discy");
							}else {
								$class = "like-posts";
								$title = esc_html_x("Like","Like group posts","discy");
							}?>
							<a href="#" class="<?php echo esc_attr($class)?> tooltip-n" data-id="<?php echo (int)$post_id?>" original-title="<?php echo esc_attr($title)?>">
						<?php }?>
						<i class="icon-heart"></i>
						<span><?php echo discy_count_number($post_like_all)?></span>
						<?php echo " ".sprintf(_n("Like","Likes",$post_like_all,"discy"),$post_like_all);
						if (is_user_logged_in()) {?>
							</a>
						<?php }?>
					</li>
					<li class="posts-comments"><a href="<?php echo wpqa_custom_permalink($post_id,"view_group_posts","view_group_post")?>#group-comments"><i class="icon-comment"></i><span class="question-span"><?php echo sprintf(_n("%s Comment","%s Comments",$count_post_all,"discy"),$count_post_all)?></span></a></li>
				</ul>
			<?php }
			if (is_user_logged_in() && ($is_super_admin || ($post_data->post_status == 'publish' && $custom_posts_edit == "on" && $post_author == $user_id) || (isset($edit_delete_posts_comments["edit"]) && $edit_delete_posts_comments["edit"] == "edit" && isset($allow_group_moderators)))) {?>
				<a class="button-default edit-group-posts" href="<?php echo wpqa_custom_permalink($post_id,"edit_group_posts","edit_group_post")?>"><i class="icon-pencil"></i></a>
			<?php }
			if ($post_data->post_status == 'publish') {
				if ((is_user_logged_in() && $group_comments == "on") || isset($allow_group_moderators) || $is_super_admin) {?>
					<a class="meta-answer meta-comment-a meta-group-comments<?php echo (!wpqa_is_view_posts_group() && $editor_group_post_comments == "on"?"":" meta-group-comments-ajax")?>" href="<?php echo wpqa_custom_permalink($post_id,"view_group_posts","view_group_post")?>#respond"><?php echo esc_html_x("Comment","Comment button group posts","discy")?></a>
				<?php }
			}else if (is_user_logged_in() && ($is_super_admin || (isset($edit_delete_posts_comments["edit"]) && $edit_delete_posts_comments["edit"] == "edit" && isset($allow_group_moderators)))) {
				$group_users_array = get_post_meta($group_id,"group_users_array",true);
				echo '<div class="group_review_button">
					<div class="cover_loader discy_hide"><div class="small_loader loader_2"></div></div>';
					echo '<a href="#" class="button-default agree_posts_group" data-nonce="'.wp_create_nonce("wpqa_group_nonce").'" data-group="'.$post_id.'" data-user="'.$post_author.'">'.esc_html__("Agree","discy").'</a>';
					if (isset($group_users_array) && is_array($group_users_array) && in_array($post_author,$group_users_array)) {
						$blocked_users = get_post_meta($group_id,"blocked_users_array",true);
						if (isset($blocked_users) && is_array($blocked_users) && in_array($post_author,$blocked_users)) {
							echo '<a href="#" class="button-default unblock_user_group" data-nonce="'.wp_create_nonce("wpqa_group_nonce").'" data-group="'.$group_id.'" data-user="'.$post_author.'">'.esc_html__("Unblock","discy").'</a>';
						}else {
							echo '<a href="#" class="button-default remove_user_group" data-nonce="'.wp_create_nonce("wpqa_group_nonce").'" data-group="'.$group_id.'" data-user="'.$post_author.'">'.esc_html__("Remove","discy").'</a>
							<a href="#" class="button-default block_user_group" data-nonce="'.wp_create_nonce("wpqa_group_nonce").'" data-group="'.$group_id.'" data-user="'.$post_author.'">'.esc_html__("Block","discy").'</a>';
						}
					}
				echo '</div>';
			}?>
		</footer>
	<?php }?>
	<div class="embed_comments">
		<div class="clearfix"></div>
		<?php if ((is_user_logged_in() && $group_comments == "on") || isset($allow_group_moderators) || $is_super_admin) {?>
			<!-- Write-comment -->
			<div class="write_comment discy_hide">
				<?php include locate_template("comment-parts/comment-group-form.php");?>
			</div>
		<?php }
		$show_number = 2;
		$number = (!wpqa_is_view_posts_group()?array('number' => $show_number):array());
		$paged = discy_paged();
		$comments_per_page = get_option("comments_per_page");
		$offset = ($paged -1) * $comments_per_page;
		$offset = (wpqa_is_view_posts_group() && get_option('page_comments')?array('offset' => $offset,'number' => $comments_per_page):array());
		$args = array('post_id' => $post_id,'status' => 'approve','orderby' => 'comment_date','order' => 'DESC');
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
		$comments_args = get_comments(array_merge($author__not_in,$number,$offset,$args));
		if (is_array($comments_args) && !empty($comments_args)) {
			if (!wpqa_is_view_posts_group() && $count_post_all > $show_number) {
				echo '<a class="button-default load-more-comments" href="'.wpqa_custom_permalink($post_id,"view_group_posts","view_group_post").'#group-comments">'.sprintf(_n("View %s more comment","View %s more comments",($count_post_all-$show_number),"discy"),($count_post_all-$show_number)).'</a>';
			}?>
			<ol<?php echo (wpqa_is_view_posts_group()?' id="group-comments"':"")?> class="commentlist clearfix">
				<?php wp_list_comments(array("callback" => "wpqa_comment","comment_type" => "comment_group","group_comments" => $group_comments,"allow_group_moderators" => (isset($allow_group_moderators)?$allow_group_moderators:false)),$comments_args);?>
				</li>
			</ol>
		<?php }?>
	</div>
</article>