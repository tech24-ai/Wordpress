<?php include locate_template("includes/group-setting.php");
$user_id = get_current_user_id();
$wpqa_user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
$is_super_admin = is_super_admin($user_id);
$paged = discy_paged();
$theme_sidebar_all = $theme_sidebar = (has_wpqa()?wpqa_sidebars("sidebar_where"):"");
$group_display = ($group_display == "private" || $group_display == "public"?array("meta_query" => array(array("key" => "group_privacy","value" => $group_display,"compare" => "="))):array());
$group_search = (isset($group_search)?$group_search:array());
if (wpqa_is_user_joined_groups()) {
	$joined_groups = true;
	$groups_array = get_user_meta($wpqa_user_id,"groups_array",true);
	$user_groups = (isset($joined_groups) && $joined_groups == true?array("post__in" => $groups_array):array());
}else if (wpqa_is_user_managed_groups()) {
	$managed_groups = true;
	$groups_moderator_array = get_user_meta($wpqa_user_id,"groups_moderator_array",true);
	$user_groups = (isset($managed_groups) && $managed_groups == true?array("post__in" => $groups_moderator_array):array());
}else {
	$user_groups = (wpqa_is_user_groups()?array("author" => $wpqa_user_id):array());
}
if (isset($user_groups) && ((!isset($joined_groups) && !isset($groups_moderator_array)) || (isset($groups_array) && is_array($groups_array) && !empty($groups_array) && count($groups_array) > 0) || (isset($groups_moderator_array) && is_array($groups_moderator_array) && !empty($groups_moderator_array) && count($groups_moderator_array) > 0))) {
	$array_data = array_merge($group_search,$user_groups,$group_display,$orderby_array,array("post_type" => "group","paged" => $paged,"posts_per_page" => $group_number));
}
if (isset($array_data) && is_array($array_data) && !empty($array_data)) {
	$query_wp = new WP_Query($array_data);
}
$date_format = discy_options("date_format");
$date_format = ($date_format?$date_format:get_option("date_format"));
$site_style = discy_options("site_style");
$site_width = discy_options("site_width");
if (isset($query_wp) && $query_wp->have_posts()) :?>
	<section class="content_groups row row-warp group-articles post-articles">
		<?php $k_ad_p = -1;
		$zero_index = $post_count = 0;
		while ($query_wp->have_posts()) : $query_wp->the_post();
			if (isset($GLOBALS['post'])) {
				$group_data = $GLOBALS['post'];
			}
			$post_id = $group_data->ID;
			$the_title = get_the_title($post_id);
			$group_privacy = get_post_meta($post_id,"group_privacy",true);
			$group_cover = get_post_meta($post_id,"group_cover",true);
			$group_image = get_post_meta($post_id,"group_image",true);
			$group_users = (int)get_post_meta($post_id,"group_users",true);
			$group_posts = (int)get_post_meta($post_id,"group_posts",true);
			$human_time_diff = human_time_diff(get_the_time('U'), current_time('timestamp'));
			echo '<article class="'.join(' ',get_post_class("col col6",$post_id)).'">
				<div class="group-item">
					<div class="group_cover">';
						if ((is_array($group_cover) && isset($group_cover["id"])) || (!is_array($group_cover) && $group_cover != "")) {
							$content_width = 891;
							if ($theme_sidebar_all == "menu_sidebar") {
								$content_width = 691;
							}else if ($theme_sidebar_all == "full") {
								$content_width = $site_width;
							}else if ($theme_sidebar_all == "menu_left") {
								$content_width = 970;
							}else if ($theme_sidebar_all == "centered") {
								$content_width = round($site_width/2);
							}
							$img_width = round(($content_width-90)/2);
							$img_height = 150;
							echo '<a href="'.get_permalink($post_id).'">'.wpqa_get_aq_resize_img($img_width,$img_height,"",(is_array($group_cover) && isset($group_cover["id"])?$group_cover["id"]:$group_cover),"no",$the_title).'</a>';
						}
					echo '</div>
					<div class="group_avatar">';
						if ((is_array($group_image) && isset($group_image["id"])) || (!is_array($group_image) && $group_image != "")) {
							echo '<a href="'.get_permalink($post_id).'">'.wpqa_get_aq_resize_img(86,86,"",(is_array($group_image) && isset($group_image["id"])?$group_image["id"]:$group_image),"no",$the_title).'</a>';
						}else {
							echo '<div class="group_img"></div>';
						}
					echo '</div>
					<div class="group_title">
						<h3><a href="'.get_permalink($post_id).'">'.$the_title.'</a></h3>
						<small>'.($group_privacy == "public"?"<i class='icon-lock-open'></i>".esc_html__("Public group","discy"):"<i class='icon-lock'></i>".esc_html__("Private group","discy")).'</small>
					</div>
					<div class="group_statistics">
						<div class="tooltip-n" title="'.sprintf(_n("%s Post","%s Posts",$group_posts,"discy"),wpqa_count_number($group_posts)).'"><i class="icon-trophy"></i></div>
						<div class="tooltip-n" title="'.sprintf(_n("%s User","%s Users",$group_users,"discy"),wpqa_count_number($group_users)).'"><i class="icon-users"></i></div>';
						$active_post_stats = discy_options("active_post_stats");
						$groups_visits = discy_options("groups_visits");
						if (has_wpqa() && $active_post_stats == "on" && $groups_visits == "on") {
							$post_stats = wpqa_get_post_stats($post_id);
							echo '<div class="tooltip-n" title="'.sprintf(_n("%s View","%s Views",$post_stats,"discy"),wpqa_count_number($post_stats)).'"><i class="icon-eye"></i></div>';
						}
						echo '<div class="tooltip-n" title="'.$human_time_diff." ".esc_html__("ago","discy").'"><i class="icon-lifebuoy"></i></div>
					</div>
				</div>
			</article>';
		endwhile;?>
	</section><!-- End section -->
	<?php if (has_wpqa()) :
		$count_total = (int)$query_wp->found_posts;
		$max_num_pages = ceil($count_total/$group_number);
		if (has_wpqa()) {
			wpqa_load_pagination(array(
				"post_pagination" => ($group_pagination != ""?$group_pagination:"pagination"),
				"max_num_pages" => $max_num_pages,
				"its_post_type" => "group",
				"wpqa_query" => $query_wp,
			));
		}
	endif;
else:
	echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("There are no groups yet.","discy").'</p></div>';
endif;?>
<?php wp_reset_postdata();?>