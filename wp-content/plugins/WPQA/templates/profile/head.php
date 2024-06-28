<?php

/* @author    2codeThemes
*  @package   WPQA/templates/profile
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$author_name           = esc_html(get_query_var("author_name"));
$wpqa_user_id          = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
$author_widget         = wpqa_options("author_widget");
$author_box            = wpqa_options("author_box");
$active_points         = wpqa_options('active_points');
$user_stats            = wpqa_options('user_stats');
$author_visits         = wpqa_options('author_visits');
$show_point_favorite   = get_user_meta($wpqa_user_id,"show_point_favorite",true);
$ask_question_to_users = wpqa_options("ask_question_to_users");
$pay_ask               = wpqa_options("pay_ask");
if ($ask_question_to_users == "on") {
	$asked_questions = wpqa_count_asked_question($wpqa_user_id,"=");
}

include wpqa_get_template("profile-page-tabs.php","profile/");

if (wpqa_second_menu() == true && $wpqa_user_id > 0) {
	$list_child = "li";
	echo '<div class="wrap-tabs"><div class="menu-tabs">';
		$menu_class = "menu flex menu-tabs-desktop navbar-nav navbar-secondary";
		include wpqa_get_template("head-tabs.php","profile/");
	echo '</div></div>';
	$list_child = "option";
	echo '<div class="wpqa_hide mobile-tabs"><span class="styled-select"><select class="form-control home_categories">';
		include wpqa_get_template("head-tabs.php","profile/");
	echo '</select></span></div>';
}?>

<?php if ($author_widget != "on" && !wpqa_user_title()) {?>
	<div class="user-area-content <?php echo ((isset($user_stats["followers"]) && $user_stats["followers"] == "followers") || (isset($user_stats["i_follow"]) && $user_stats["i_follow"] == "i_follow")?"page-has-following":"page-not-following")?>">
		<?php if ($author_box == "on") {
			$cover_image = wpqa_options("cover_image");
			echo wpqa_author($wpqa_user_id,"advanced",wpqa_is_user_owner(),"","","user-area-head block-section-div",($cover_image == "on"?"cover":""));
		}
		
		/* Following */
		$author__not_in = array();
		$block_users = wpqa_options("block_users");
		if ($block_users == "on") {
			$user_id = $wpqa_user_id;
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = $get_block_users;
				}
			}
		}
		$following_me  = get_user_meta($wpqa_user_id,"following_me",true);
		if (is_array($following_me) && !empty($following_me)) {
			$following_me = array_diff($following_me,$author__not_in);
		}
		$following_me  = (is_array($following_me) && !empty($following_me)?get_users(array('fields' => 'ID','include' => $following_me,'orderby' => 'registered')):array());
		$following_you = get_user_meta($wpqa_user_id,"following_you",true);
		if (is_array($following_you) && !empty($following_you)) {
			$following_you = array_diff($following_you,$author__not_in);
		}
		$following_you = (is_array($following_you) && !empty($following_you)?get_users(array('fields' => 'ID','include' => $following_you,'orderby' => 'registered')):array());

		if ($author_visits == "on") {
			$user_stats["visits"] = "visits";
		}
		
		echo wpqa_get_user_stats($wpqa_user_id,$user_stats,$active_points,$show_point_favorite);
		
		if ((isset($user_stats["followers"]) && $user_stats["followers"] == "followers") || (isset($user_stats["i_follow"]) && $user_stats["i_follow"] == "i_follow")) {
			$size = 29;
			if ((isset($user_stats["followers"]) && $user_stats["followers"] != "followers") || ((isset($user_stats["i_follow"]) && $user_stats["i_follow"] != "i_follow"))) {
				$column_follow = "col12 col-boot-sm-12";
			}else {
				$column_follow = "col6 col-boot-sm-6";
			}?>
			<div class="user-follower">
				<ul class="row row-warp row-boot list-unstyled mb-0">
					<?php if (isset($user_stats["followers"]) && $user_stats["followers"] == "followers") {?>
						<li class="col <?php echo esc_attr($column_follow)?> user-followers">
							<div class="block-section-div">
								<a href="<?php echo esc_url(wpqa_get_profile_permalink($wpqa_user_id,"followers"))?>"></a>
								<h4><i class="icon-users"></i><?php esc_html_e("Followers","wpqa")?></h4>
								<div>
									<?php $followers = $last_followers = 0;
									if (isset($following_you) && is_array($following_you)) {
										$followers = count($following_you);
									}
									
									if ($followers > 0) {
										$last_followers = $followers-4;
										if (isset($following_you) && is_array($following_you)) {
											$sliced_array = array_slice($following_you,0,4);
											foreach ($sliced_array as $key => $value) {
												echo wpqa_get_user_avatar(array("user_id" => $value,"size" => $size,"class" => "rounded-circle"));
											}
										}
									}?>
									<span>
										<?php if ($last_followers > 0) {?>
											<span>+ <?php echo wpqa_count_number($last_followers)?></span> <?php echo _n("Follower","Followers",$last_followers,"wpqa")?>
										<?php }else if ($followers == 0) {
											esc_html_e("User doesn't have any followers yet.","wpqa");
										}?>
									</span>
								</div>
							</div>
						</li>
					<?php }
					if (isset($user_stats["i_follow"]) && $user_stats["i_follow"] == "i_follow") {?>
						<li class="col <?php echo esc_attr($column_follow)?> user-following">
							<div class="block-section-div">
								<a href="<?php echo esc_url(wpqa_get_profile_permalink($wpqa_user_id,"following"))?>"></a>
								<h4><i class="icon-users"></i><?php esc_html_e("Following","wpqa")?></h4>
								<div>
									<?php $following = $last_following = 0;
									if (isset($following_me) && is_array($following_me)) {
										$following = count($following_me);
									}
									if ($following > 0) {
										$last_following = $following-4;
										if (isset($following_me) && is_array($following_me)) {
											$sliced_array = array_slice($following_me,0,4);
											foreach ($sliced_array as $key => $value) {
												echo wpqa_get_user_avatar(array("user_id" => $value,"size" => $size,"class" => "rounded-circle"));
											}
										}
									}?>
									<span>
										<?php if ($last_following > 0) {?>
											<span>+ <?php echo wpqa_count_number($last_following)?></span> <?php echo _n("Member","Members",$last_following,"wpqa")?>
										<?php }else if ($following == 0) {
											esc_html_e("User doesn't follow anyone.","wpqa");
										}?>
									</span>
								</div>
							</div>
						</li>
					<?php }?>
				</ul>
			</div><!-- End user-follower -->
		<?php }?>
	</div><!-- End user-area-content -->
<?php }

do_action("wpqa_after_head_content_profile",$wpqa_user_id);?>