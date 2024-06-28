<?php

/* @author    2codeThemes
*  @package   WPQA/widgets
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Profile Strength */
add_action( 'widgets_init', 'wpqa_widget_profile_strength_widget' );
function wpqa_widget_profile_strength_widget() {
	register_widget( 'Widget_profile_strength' );
}
class Widget_profile_strength extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget-profile-strength' );
		$control_ops = array( 'id_base' => 'widget_profile_strength' );
		parent::__construct( 'widget_profile_strength',wpqa_widgets.' - Profile Strength', $widget_ops, $control_ops );
	}
	
	public function widget( $args, $instance ) {
		extract( $args );
		if (is_user_logged_in()) {
			$user_id              = get_current_user_id();
			$items_left = $total  = 0;
			$profile_strength     = wpqa_options("profile_strength");
			$percent_avatar       = wpqa_options("percent_avatar");
			$percent_cover        = wpqa_options("percent_cover");
			$percent_credential   = wpqa_options("percent_credential");
			$percent_follow_cats  = wpqa_options("percent_follow_cats");
			$percent_follow_tags  = wpqa_options("percent_follow_tags");
			$percent_follow_user  = wpqa_options("percent_follow_user");
			$percent_ask_question = wpqa_options("percent_ask_question");
			$percent_answer       = wpqa_options("percent_answer");
			if (is_array($profile_strength)) {
				foreach ($profile_strength as $value) {
					if ($value != "0") {
						$items_left++; 
						$first_item = $value;
					}
				}
			}
			$done_avatar = $done_cover = $done_credential = $done_cats = $done_tags = $done_users = $done_question = $done_answer = $done_filter = true;
			$done_filter = apply_filters("wpqa_done_filter",$done_filter);
			if (isset($profile_strength["avatar"]) && $profile_strength["avatar"] == "avatar") {
				$done_avatar = false;
				$user_meta_avatar = wpqa_avatar_name();
				$your_avatar = get_the_author_meta($user_meta_avatar,$user_id);
				if ((($your_avatar && !is_array($your_avatar)) || (is_array($your_avatar) && isset($your_avatar["id"]) && $your_avatar["id"] != 0))) {
					$avatar_done = true;
				}else {
					$email = get_the_author_meta('user_email',$user_id);
					$hashkey = md5(strtolower(trim($email)));
					$uri = 'https://www.gravatar.com/avatar/'.$hashkey.'?d=404';
					$data = get_transient($hashkey);
					if (false === $data) {
						$response = wp_remote_head($uri);
						if (is_wp_error($response)) {
							$data = 'not200';
						}else {
							$data = $response['response']['code'];
						}
						set_transient($hashkey,$data,60*60*12);
					}
					if (isset($data) && $data == '200') {
						$avatar_done = true;
					}
				}
				if (isset($avatar_done)) {
					$done_avatar = true;
					$items_left--;
					$total = $total+$percent_avatar;
				}
			}
			if (isset($profile_strength["cover"]) && $profile_strength["cover"] == "cover") {
				$done_cover = false;
				$user_meta_cover = wpqa_cover_name();
				$your_cover = get_the_author_meta($user_meta_cover,$user_id);
				if ((($your_cover && !is_array($your_cover)) || (is_array($your_cover) && isset($your_cover["id"]) && $your_cover["id"] != 0)) && $user_id > 0) {
					$done_cover = true;
					$items_left--;
					$total = $total+$percent_cover;
				}
			}
			if (isset($profile_strength["credential"]) && $profile_strength["credential"] == "credential") {
				$done_credential = false;
				$profile_credential = get_the_author_meta('profile_credential',$user_id);
				if (isset($profile_credential) && $profile_credential != "") {
					$done_credential = true;
					$items_left--;
					$total = $total+$percent_credential;
				}
			}
			if (isset($profile_strength["follow_cats"]) && $profile_strength["follow_cats"] == "follow_cats") {
				$done_cats = false;
				$profile_follow_cats = (int)wpqa_options("profile_follow_cats");
				$user_cat_follow = get_user_meta($user_id,"user_cat_follow",true);
				if (is_array($user_cat_follow) && !empty($user_cat_follow)) {
					if (count($user_cat_follow) >= $profile_follow_cats) {
						$done_cats = true;
						$items_left--;
						$total = $total+$percent_follow_cats;
					}
				}
			}
			if (isset($profile_strength["follow_tags"]) && $profile_strength["follow_tags"] == "follow_tags") {
				$done_tags = false;
				$profile_follow_tags = (int)wpqa_options("profile_follow_tags");
				$user_tag_follow = get_user_meta($user_id,"user_tag_follow",true);
				if (is_array($user_tag_follow) && !empty($user_tag_follow)) {
					if (count($user_tag_follow) >= $profile_follow_tags) {
						$done_tags = true;
						$items_left--;
						$total = $total+$percent_follow_tags;
					}
				}
			}
			if (isset($profile_strength["follow_user"]) && $profile_strength["follow_user"] == "follow_user") {
				$done_users = false;
				$profile_follow_users = (int)wpqa_options("profile_follow_users");
				$count_following_you = get_user_meta($user_id,"count_following_me",true);
				$count_following_you = (int)($count_following_you > 0?$count_following_you:0);
				if ($count_following_you >= $profile_follow_users) {
					$done_users = true;
					$items_left--;
					$total = $total+$percent_follow_user;
				}
			}
			if (isset($profile_strength["ask_question"]) && $profile_strength["ask_question"] == "ask_question") {
				$done_question = false;
				$questions_count = wpqa_count_posts_meta(wpqa_questions_type,$user_id);
				if ($questions_count > 0) {
					$done_question = true;
					$items_left--;
					$total = $total+$percent_ask_question;
				}
			}
			if (isset($profile_strength["answer"]) && $profile_strength["answer"] == "answer") {
				$done_answer = false;
				$profile_answer = (int)wpqa_options("profile_answer");
				$answers_count = wpqa_count_comments_meta(wpqa_questions_type,$user_id);
				if ($answers_count >= $profile_answer) {
					$done_answer = true;
					$items_left--;
					$total = $total+$percent_answer;
				}
			}
			$total = apply_filters("wpqa_widget_total",$total);
			$items_left = apply_filters("wpqa_widget_items_left",$items_left);
			if ((isset($first_item) && $first_item != "") && ($done_avatar == false || $done_cover == false || $done_credential == false || $done_cats == false || $done_tags == false || $done_users == false || $done_question == false || $done_answer == false || $done_filter == false)) {
				$title = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
				echo ($before_widget);
					if ($title) {
						echo ($title == "empty"?"<div class='empty-title'>":"").($before_title.($title == "empty"?"":esc_html($title)).$after_title).($title == "empty"?"</div>":"");
					}else {
						echo "<h3 class='screen-reader-text'>".esc_html__("Profile Strength","wpqa")."</h3>";
					}?>
					<div class="widget-wrap">
						<p><?php esc_html_e("You must have a total score of 100 for your profile.","wpqa")?></p>
						<div class="progressbar-wrap">
							<span class="progressbar-title">
								<span><?php echo (int)$total?>%</span><?php echo sprintf(_n("%s step left!","%s steps left!",$items_left,"wpqa"),$items_left)?>
							</span>
							<div class="progressbar progress">
							    <div class="progress-bar bg-success progressbar-percent poll-result-<?php echo floatval($total == 0?100:$total)?>" attr-percent="<?php echo floatval($total == 0?100:$total)?>"></div>
							</div>
						</div><!-- End progressbar-wrap -->
						<ul class="list-with-arrows list-unstyled mb-3">
							<?php foreach ($profile_strength as $value) {
								if ($value == "avatar") {
									echo "<li class='widget-li".($done_avatar == true?" profile-done":"")."'>".($done_avatar == true?"":"<a href='".esc_url(wpqa_edit_profile_permalink())."' target='_blank'>").esc_html__("Upload your avatar image","wpqa").($done_avatar == true?"":"</a>")."</li>";
								}else if ($value == "cover") {
									echo "<li class='widget-li".($done_cover == true?" profile-done":"")."'>".($done_cover == true?"":"<a href='".esc_url(wpqa_edit_profile_permalink())."' target='_blank'>").esc_html__("Upload your cover image","wpqa").($done_cover == true?"":"</a>")."</li>";
								}else if ($value == "credential") {
									echo "<li class='widget-li".($done_credential == true?" profile-done":"")."'>".($done_credential == true?"":"<a href='".esc_url(wpqa_edit_profile_permalink())."' target='_blank'>").esc_html__("Add your profile credential","wpqa").($done_credential == true?"":"</a>")."</li>";
								}else if ($value == "follow_cats") {
									if ($done_cats != true) {
										$pages = get_pages(array('meta_key' => '_wp_page_template','meta_value' => 'template-categories.php'));
										if (isset($pages) && isset($pages[0]) && isset($pages[0]->ID)) {
											$cats_tax = get_post_meta($pages[0]->ID,prefix_meta."cats_tax",true);
											$question_cats = ($cats_tax == wpqa_questions_type?true:false);
										}
									}
									echo "<li class='widget-li".($done_cats == true?" profile-done":"")."'>".($done_cats != true && isset($question_cats) && $question_cats == true?"<a href='".esc_url(get_permalink($pages[0]->ID))."' target='_blank'>":"").sprintf(esc_html__("Follow %s categories","wpqa"),$profile_follow_cats).($done_cats != true && isset($pages) && isset($pages[0]) && isset($pages[0]->ID)?"</a>":"")."</li>";
								}else if ($value == "follow_tags") {
									if ($done_tags != true) {
										$pages = get_pages(array('meta_key' => '_wp_page_template','meta_value' => 'template-tags.php'));
										if (isset($pages) && isset($pages[0]) && isset($pages[0]->ID)) {
											$tags_tax = get_post_meta($pages[0]->ID,prefix_meta."tags_tax",true);
											$question_tags = ($tags_tax == wpqa_questions_type?true:false);
										}
									}
									echo "<li class='widget-li".($done_tags == true?" profile-done":"")."'>".($done_tags != true && isset($question_tags) && $question_tags == true?"<a href='".esc_url(get_permalink($pages[0]->ID))."' target='_blank'>":"").sprintf(esc_html__("Follow %s tags","wpqa"),$profile_follow_tags).($done_tags != true && isset($pages) && isset($pages[0]) && isset($pages[0]->ID)?"</a>":"")."</li>";
								}else if ($value == "follow_user") {
									if ($done_users != true) {
										$pages = get_pages(array('meta_key' => '_wp_page_template','meta_value' => 'template-users.php'));
									}
									echo "<li class='widget-li".($done_users == true?" profile-done":"")."'>".($done_users != true && isset($pages) && isset($pages[0]) && isset($pages[0]->ID)?"<a href='".esc_url(get_permalink($pages[0]->ID))."' target='_blank'>":"").sprintf(esc_html__("Follow %s users","wpqa"),$profile_follow_users).($done_users != true && isset($pages) && isset($pages[0]) && isset($pages[0]->ID)?"</a>":"")."</li>";
								}else if ($value == "ask_question") {
									echo "<li class='widget-li".($done_question == true?" profile-done":"")."'>".($done_question == true?"":"<a class='wpqa-question".apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_question','')."' href='".esc_url(wpqa_add_question_permalink())."' target='_blank'>").esc_html__("Ask your first question","wpqa").($done_question == true?"":"</a>")."</li>";
								}else if ($value == "answer") {
									echo "<li class='widget-li".($done_answer == true?" profile-done":"")."'>".($done_answer == true?"":"<a href='".esc_url(get_post_type_archive_link(wpqa_questions_type))."' target='_blank'>").sprintf(esc_html__("Answer %s questions","wpqa"),$profile_answer).($done_answer == true?"":"</a>")."</li>";
								}
								do_action("wpqa_profile_strength",$value);
							}?>
						</ul>
						<a class="button-default profile-button btn btn__primary btn__block btn__semi__height" href="<?php echo wpqa_edit_profile_permalink()?>"><?php esc_html_e("Start Now","wpqa")?></a>
					</div>
				<?php echo ($after_widget);
			}
		}
	}

	public function form( $instance ) {
		/* Save Button */
	}
}?>