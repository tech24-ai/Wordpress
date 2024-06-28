<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Notifications ask question */
if (!function_exists('wpqa_notifications_ask_question')) :
	function wpqa_notifications_ask_question($post_id,$question_username,$user_id,$not_user,$anonymously_user,$get_current_user_id,$approved = false) {
		$way_sending_notifications = wpqa_options("way_sending_notifications_questions");
		if ($way_sending_notifications != "cronjob") {
			$send_email_and_notification_question = wpqa_options("send_email_and_notification_question");
			$send_email_new_question_value = "send_email_new_question";
			$send_email_question_groups_value = "send_email_question_groups";
			if ($send_email_and_notification_question == "both") {
				$send_email_new_question_value = "send_email_new_question_both";
				$send_email_question_groups_value = "send_email_question_groups_both";
			}
			$send_email_new_question = wpqa_options($send_email_new_question_value);
			$the_author = 0;
			if ($not_user == 0) {
				$the_author = $question_username;
			}
			if ($user_id == "") {
				$private_question = get_post_meta($post_id,"private_question",true);
				$email_title = wpqa_options("title_new_questions");
				$email_title = ($email_title != ""?$email_title:esc_html__("New question","wpqa"));
				if ($send_email_new_question == "on") {
					$user_group = wpqa_options($send_email_question_groups_value);
					$users = get_users(array("meta_query" => array('relation' => 'AND',array("key" => "received_email","compare" => "=","value" => "on"),array('relation' => 'OR',array("key" => "unsubscribe_mails","compare" => "NOT EXISTS"),array("key" => "unsubscribe_mails","compare" => "!=","value" => "on"))),"role__in" => (isset($user_group) && is_array($user_group)?$user_group:array()),"fields" => array("ID","user_email","display_name")));
					if (isset($users) && is_array($users) && !empty($users)) {
						foreach ($users as $key => $value) {
							$another_user_id = $value->ID;
							if (is_super_admin($another_user_id) && ($private_question == "on" || $private_question == 1) && (($another_user_id != $anonymously_user && $anonymously_user > 0) || ($another_user_id != $not_user && $not_user > 0))) {
								if ($send_email_and_notification_question == "both" && $approved == false && $get_current_user_id != $another_user_id) {
									wpqa_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_question","notifications","",wpqa_questions_type);
								}
							}else {
								if ($not_user != $another_user_id) {
									$yes_private = wpqa_private($post_id,$not_user,$another_user_id);
									if ($yes_private == 1) {
										if ($get_current_user_id != $another_user_id && $another_user_id > 0 && $not_user != $another_user_id) {
											$send_text = wpqa_send_mail(
												array(
													'content'          => wpqa_options("email_new_questions"),
													'post_id'          => $post_id,
													'sender_user_id'   => ($anonymously_user > 0?"anonymous":$not_user),
													'received_user_id' => $another_user_id,
												)
											);
											$email_title = wpqa_send_mail(
												array(
													'content'          => $email_title,
													'title'            => true,
													'break'            => '',
													'post_id'          => $post_id,
													'sender_user_id'   => ($anonymously_user > 0?"anonymous":$not_user),
													'received_user_id' => $another_user_id,
												)
											);
											wpqa_send_mails(
												array(
													'toEmail'     => esc_html($value->user_email),
													'toEmailName' => esc_html($value->display_name),
													'title'       => $email_title,
													'message'     => $send_text,
												)
											);
											if ($send_email_and_notification_question == "both" && $approved == false) {
												wpqa_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_question","notifications","",wpqa_questions_type);
											}
										}
									}
								}
							}
						}
					}
				}else if ($private_question == "on" || $private_question == 1) {
					$users = get_users(array("role" => "administrator","fields" => array("ID","user_email")));
					if (isset($users) && is_array($users) && !empty($users)) {
						foreach ($users as $key => $value) {
							$another_user_id = $value->ID;
							if ($another_user_id > 0 && $not_user != $another_user_id && $anonymously_user != $another_user_id && isset($send_text)) {
								wpqa_send_mails(
									array(
										'toEmail'     => esc_html($value->user_email),
										'toEmailName' => esc_html($value->display_name),
										'title'       => $email_title,
										'message'     => $send_text,
									)
								);
								if ($send_email_and_notification_question == "both") {
									wpqa_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_question","notifications","",wpqa_questions_type);
								}
							}
						}
					}
				}
				if ($send_email_and_notification_question == "separately") {
					$send_notification_new_question = wpqa_options("send_notification_new_question");
					if ($send_notification_new_question == "on") {
						$user_group = wpqa_options("send_notification_question_groups");
						$users = get_users(array("role__in" => (isset($user_group) && is_array($user_group)?$user_group:array()),"fields" => array("ID","user_email","display_name")));
						if (isset($users) && is_array($users) && !empty($users)) {
							foreach ($users as $key => $value) {
								$another_user_id = $value->ID;
								if (is_super_admin($another_user_id) && ($private_question == "on" || $private_question == 1) && (($another_user_id != $anonymously_user && $anonymously_user > 0) || ($another_user_id != $not_user && $not_user > 0))) {
									if ($approved == false && $get_current_user_id != $another_user_id) {
										wpqa_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_question","notifications","",wpqa_questions_type);
									}
								}else {
									if ($not_user != $another_user_id) {
										$yes_private = wpqa_private($post_id,$not_user,$another_user_id);
										if ($yes_private == 1) {
											if ($get_current_user_id != $another_user_id && $another_user_id > 0 && $not_user != $another_user_id && $approved == false) {
												wpqa_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_question","notifications","",wpqa_questions_type);
											}
										}
									}
								}
							}
						}
					}else if ($private_question == "on" || $private_question == 1) {
						$users = get_users(array("role" => "administrator","fields" => array("ID","user_email")));
						if (isset($users) && is_array($users) && !empty($users)) {
							foreach ($users as $key => $value) {
								$another_user_id = $value->ID;
								if ($another_user_id > 0 && $not_user != $another_user_id && $anonymously_user != $another_user_id && isset($send_text)) {
									wpqa_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_question","notifications","",wpqa_questions_type);
								}
							}
						}
					}
				}
			}
		}
	}
endif;
/* Count paid question */
if (!function_exists('wpqa_count_paid_question')) :
	function wpqa_count_paid_question( $user_id = "", $post_status = "publish" ) {
		$args = array(
			"post_type"   => wpqa_questions_type,
			"post_status" => $post_status,
			"meta_query" => array(
				array("key" => "_paid_question","compare" => "=","value" => "paid"),
			)
		);
		$the_query = new WP_Query($args);
		return $the_query->found_posts;
		wp_reset_postdata();
	}
endif;
/* Count asked question */
if (!function_exists('wpqa_count_asked_question')) :
	function wpqa_count_asked_question( $user_id = "", $asked = "=", $post_status = "publish" ) {
		$block_users = wpqa_options("block_users");
		$author__not_in = array();
		if ($block_users == "on") {
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = array("author__not_in" => $get_block_users);
				}
			}
		}
		$args = array(
			"post_type"     => wpqa_asked_questions_type,
			"post_status"   => $post_status,
			"comment_count" => array(
				"value"   => 0,
				"compare" => $asked,
			),
			"meta_query"    => array(
				array("key" => "user_id","compare" => "=","value" => $user_id),
			)
		);
		$the_query = new WP_Query(array_merge($author__not_in,$args));
		return $the_query->found_posts;
		wp_reset_postdata();
	}
endif;
/* Question sticky */
if (!function_exists('wpqa_question_sticky')) :
	function wpqa_question_sticky($post_id) {
		$end_sticky_time = get_post_meta($post_id,"end_sticky_time",true);
		$question_sticky = "";
		if (is_sticky($post_id)) {
			$question_sticky = "sticky";
			if ($end_sticky_time != "" && $end_sticky_time < strtotime(date("Y-m-d"))) {
				$question_sticky = "";
			}
		}
		return $question_sticky;
	}
endif;
/* Question content */
add_action('wpqa_before_question_content','wpqa_before_question_content',1,4);
if (!function_exists('wpqa_before_question_content')) :
	function wpqa_before_question_content($post_id,$user_id,$anonymously_user,$post_author) {
		$_paid_question = get_post_meta($post_id,"_paid_question",true);
		$end_sticky_time = get_post_meta($post_id,"end_sticky_time",true);
		if ((is_super_admin($user_id) || ($anonymously_user > 0 && $user_id == $anonymously_user) || ($post_author > 0 && $user_id == $post_author)) && (isset($_paid_question) && $_paid_question == "paid")) {
			echo '<div class="alert-message message-paid-question"><i class="icon-lamp"></i><p> '.esc_html__("This is a paid question.","wpqa").'</p></div>';
		}
		if (is_sticky()) {
			if ((is_super_admin($user_id) || ($anonymously_user > 0 && $user_id == $anonymously_user) || ($post_author > 0 && $user_id == $post_author)) && ($end_sticky_time != "" && $end_sticky_time >= strtotime(date("Y-m-d")))) {
				echo '<div class="alert-message message-paid-sticky"><i class="icon-lamp"></i><p>'.esc_html__("This question will sticky to","wpqa").': '.date("Y-m-d",$end_sticky_time).'</p></div>';
			}
		}
	}
endif;
/* Question content loop */
add_action('wpqa_question_content','wpqa_question_content_loop');
add_action('wpqa_question_content_loop','wpqa_question_content_loop');
if (!function_exists('wpqa_question_content_loop')) :
	function wpqa_question_content_loop($post_id) {
		$end_sticky_time  = get_post_meta($post_id,"end_sticky_time",true);
		if ($end_sticky_time != "" && $end_sticky_time < strtotime(date("Y-m-d"))) {
			delete_post_meta($post_id,"start_sticky_time");
			delete_post_meta($post_id,"end_sticky_time");
			delete_post_meta($post_id,'sticky');
			$sticky_questions = get_option('sticky_questions');
			if (is_array($sticky_questions) && in_array($post_id,$sticky_questions)) {
				$sticky_posts = get_option('sticky_posts');
				$sticky_posts = wpqa_remove_item_by_value($sticky_posts,$post_id);
				update_option('sticky_posts',$sticky_posts);
				$sticky_questions = wpqa_remove_item_by_value($sticky_questions,$post_id);
				update_option('sticky_questions',$sticky_questions);
			}
		}
	}
endif;
/* Bump question */
add_action('wpqa_after_question_area','wpqa_question_bump_a',1,7);
if (!function_exists('wpqa_question_bump_a')) :
	function wpqa_question_bump_a($post_id,$user_id,$anonymously_user,$post_author,$comments,$featured_image_question,$featured_position) {
		echo '<div class="clearfix"></div>
		<div class="question-custom-links'.($featured_image_question == "on" && has_post_thumbnail() && $featured_position == "after"?" after-question-area":"").'">';
		$question_bump = wpqa_options("question_bump");
		$active_points = wpqa_options("active_points");
		if (is_user_logged_in() && $question_bump == "on" && $active_points == "on" && ($comments == "" || $comments == 0) && $user_id == $post_author && $post_author > 0) {
			echo '<a href="#" class="bump-question wpqa-open-click color btn btn__link custom-post-link" data-class="bump-question-area">'.esc_html__("Bump your question","wpqa").'</a>';
		}
	}
endif;
/* Allow to sticky */
function wpqa_allow_to_sticky($post_id,$user_id,$anonymously_user,$post_author) {
	$pay_to_sticky = wpqa_options("pay_to_sticky");
	if ($pay_to_sticky == "on") {
		$end_sticky_time = get_post_meta($post_id,"end_sticky_time",true);
		if (($end_sticky_time == "" || ($end_sticky_time != "" && $end_sticky_time < strtotime(date("Y-m-d"))))) {
			$days_sticky = (int)wpqa_options("days_sticky");
			$days_sticky = ($days_sticky > 0?$days_sticky:7);
			wpqa_sticky_return($post_id,$user_id);
			if ((($anonymously_user > 0 && $user_id == $anonymously_user) || ($post_author > 0 && $user_id == $post_author)) && ($end_sticky_time == "" || ($end_sticky_time != "" && $end_sticky_time < strtotime(date("Y-m-d"))) || (!is_sticky())) && $pay_to_sticky == "on") {
				return true;
			}
		}
	}
}
/* Sticky form */
add_action('wpqa_after_question_area','wpqa_question_sticky_area',2,4);
if (!function_exists('wpqa_question_sticky_area')) :
	function wpqa_question_sticky_area($post_id,$user_id,$anonymously_user,$post_author) {
		$allow_to_sticky = wpqa_allow_to_sticky($post_id,$user_id,$anonymously_user,$post_author);
		if ($allow_to_sticky == true) {
			$payment_pages_target = wpqa_options("payment_pages_target");
			$payment_pages_target = ($payment_pages_target == "new_page"?"_blank":"_self");
			echo '<a href="#" class="pay-to-sticky wpqa-open-click color btn btn__link custom-post-link" data-class="pay-to-sticky-area">'.esc_html__("Pay for sticky question","wpqa").'</a>
			<div class="clearfix"></div>
			<div class="pay-to-sticky-area wpqa-open-div wpqa_hide">
				<a href="'.wpqa_checkout_link("sticky",$post_id).'" target="'.$payment_pages_target.'" class="button-default btn btn__primary">'.esc_html__("Pay for sticky question","wpqa").'</a>
			</div>
			<div class="clearfix"></div>';
		}
		echo '</div>';
	}
endif;
/* Bump question */
add_action('wpqa_after_question_area','wpqa_question_bump',3,5);
if (!function_exists('wpqa_question_bump')) :
	function wpqa_question_bump($post_id,$user_id,$anonymously_user,$post_author,$comments) {
		$question_bump = wpqa_options("question_bump");
		$active_points = wpqa_options("active_points");
		if (is_user_logged_in() && $question_bump == "on" && $active_points == "on" && ($comments == "" || $comments == 0) && $user_id == $post_author && $post_author != 0) {
			echo '<div class="clearfix"></div>
			<div class="bump-question-area wpqa-open-div wpqa_hide">
				<input class="form-control" id="input-add-point" name="" type="text" placeholder="'.esc_html__("Bump question with points","wpqa").'">
				<a class="button-default btn btn__primary" href="#">'.esc_html__("Bump","wpqa").'</a>
				<div class="load_span"><span class="loader_2"></span></div>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>';
		}
	}
endif;
/* Sticky 404 */
add_action('parse_query','wpqa_redirect_sticky',11);
if (!function_exists('wpqa_redirect_sticky')) :
	function wpqa_redirect_sticky() {
		if (is_404() && isset($_POST["process"]) && $_POST["process"] == "sticky" && isset($_POST["post_id"]) && $_POST["post_id"] > 0) {
			$post_id = (int)(isset($_POST["post_id"])?$_POST["post_id"]:0);
			$user_id = get_current_user_id();
			wpqa_sticky_return($post_id,$user_id);
		}
	}
endif;
/* Sticky return */
function wpqa_sticky_return($post_id,$user_id) {
	if ($user_id > 0 && isset($_POST["process"]) && $_POST["process"] == "sticky") {
		/* Pay by points */
		if (isset($_POST["points"]) && $_POST["points"] > 0) {
			$points_price = (int)$_POST["points"];
			$points_user = (int)get_user_meta($user_id,"points",true);
			if ($points_price <= $points_user) {
				update_post_meta($post_id,"sticky_points",$points_price);
				/* Insert a new payment */
				$save_pay_by_points = wpqa_options("save_pay_by_points");
				if ($save_pay_by_points == "on") {
					$array = array (
						'item_no'    => "sticky",
						'item_name'  => esc_attr__("Pay to make this Question sticky","wpqa"),
						'item_price' => 0,
						'first_name' => get_the_author_meta("first_name",$user_id),
						'last_name'  => get_the_author_meta("last_name",$user_id),
						'points'     => $points_price,
						'custom'     => 'wpqa_sticky-'.$post_id,
					);
					wpqa_insert_statement($array,$user_id);
				}
				wpqa_add_points($user_id,$points_price,"-","sticky_points");
				wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("You have just stickied the question by points.","wpqa").'</p></div>','wpqa_session');
			}else {
				wpqa_not_enough_points();
				wp_safe_redirect(get_the_permalink($post_id));
				die();
			}
		}
		update_post_meta($post_id,"sticky",1);
		$sticky_posts = get_option('sticky_posts');
		if (is_array($sticky_posts)) {
			if (!in_array($post_id,$sticky_posts)) {
				$array_merge = array_merge($sticky_posts,array($post_id));
				update_option("sticky_posts",$array_merge);
			}
		}else {
			update_option("sticky_posts",array($post_id));
		}
		$sticky_questions = get_option('sticky_questions');
		if (is_array($sticky_questions)) {
			if (!in_array($post_id,$sticky_questions)) {
				$array_merge = array_merge($sticky_questions,array($post_id));
				update_option("sticky_questions",$array_merge);
			}
		}else {
			update_option("sticky_questions",array($post_id));
		}
		update_post_meta($post_id,"paid_question_with_sticky",true);
		$days_sticky = (int)wpqa_options("days_sticky");
		$days_sticky = ($days_sticky > 0?$days_sticky:7);
		update_post_meta($post_id,"start_sticky_time",strtotime(date("Y-m-d")));
		update_post_meta($post_id,"end_sticky_time",strtotime(date("Y-m-d",strtotime(date("Y-m-d")." +$days_sticky days"))));
		wp_safe_redirect(get_the_permalink($post_id));
		die();
	}
}
/* Vote question */
add_action('wpqa_question_vote','wpqa_question_vote',1,7);
if (!function_exists('wpqa_question_vote')) :
	function wpqa_question_vote($post,$user_id,$anonymously_user,$question_vote,$question_loop_dislike,$question_single_dislike,$class = "") {
		$active_vote_unlogged = wpqa_options("active_vote_unlogged");
		$count_up = get_post_meta($post->ID,'wpqa_question_vote_up',true);
		$count_down = get_post_meta($post->ID,'wpqa_question_vote_down',true);
		$count_up = (isset($count_up) && is_array($count_up) && !empty($count_up)?$count_up:array());
		$count_down = (isset($count_down) && is_array($count_down) && !empty($count_down)?$count_down:array());
		$uniqid_cookie = wpqa_options("uniqid_cookie");?>
		<ul class="question-vote<?php echo ($class != ""?" ".$class:"")?>">
			<li class="question-vote-up"><a href="#"<?php echo ((is_user_logged_in() && $post->post_author != $user_id && $anonymously_user != $user_id) || (!is_user_logged_in() && $active_vote_unlogged == "on")?' data-id="'.$post->ID.'"':'')?> data-type="<?php echo wpqa_questions_type?>" data-vote-type="up" class="wpqa_vote question_vote_up<?php echo (is_user_logged_in() && $post->post_author != $user_id && $anonymously_user != $user_id?"":(is_user_logged_in() && (($post->post_author > 0 && $post->post_author == $user_id) || ($anonymously_user == $user_id))?" vote_not_allow":($active_vote_unlogged == "on"?"":" vote_not_user"))).((is_user_logged_in() && $post->post_author != $user_id && $anonymously_user != $user_id) || (!is_user_logged_in() && $active_vote_unlogged == "on")?' vote_allow':'').((is_user_logged_in() && is_array($count_up) && in_array($user_id,$count_up)) || (!is_user_logged_in() && isset($_COOKIE[$uniqid_cookie.'wpqa_question_vote_up'.$post->ID]) && $_COOKIE[$uniqid_cookie.'wpqa_question_vote_up'.$post->ID] == "wpqa_yes")?" wpqa_voted_already":"")?>" title="<?php esc_attr_e("Like","wpqa");?>"><i class="<?php echo apply_filters('wpqa_vote_up_icon','icon-up-dir');?>"></i></a></li>
			<?php if (is_single()) {?>
				<li class="wpqa_hide" itemprop="upvoteCount"><?php echo ($question_vote != ""?wpqa_count_number($question_vote):0)?></li>
			<?php }?>
			<li class="vote_result"><?php echo ($question_vote != ""?wpqa_count_number($question_vote):0)?></li>
			<li class="li_loader"><span class="loader_3 fa-spin"></span></li>
			<?php if ((!is_single() && $question_loop_dislike != "on") || (is_single() && $question_single_dislike != "on")) {?>
				<li class="question-vote-down"><a href="#"<?php echo ((is_user_logged_in() && $post->post_author != $user_id && $anonymously_user != $user_id) || (!is_user_logged_in() && $active_vote_unlogged == "on")?' data-id="'.$post->ID.'"':'')?> data-type="<?php echo wpqa_questions_type?>" data-vote-type="down" class="wpqa_vote question_vote_down<?php echo (is_user_logged_in() && $post->post_author != $user_id && $anonymously_user != $user_id?"":(is_user_logged_in() && (($post->post_author > 0 && $post->post_author == $user_id) || ($anonymously_user == $user_id))?" vote_not_allow":($active_vote_unlogged == "on"?"":" vote_not_user"))).((is_user_logged_in() && $post->post_author != $user_id && $anonymously_user != $user_id) || (!is_user_logged_in() && $active_vote_unlogged == "on")?' vote_allow':'').((is_user_logged_in() && is_array($count_down) && in_array($user_id,$count_down)) || (!is_user_logged_in() && isset($_COOKIE[$uniqid_cookie.'wpqa_question_vote_down'.$post->ID]) && $_COOKIE[$uniqid_cookie.'wpqa_question_vote_down'.$post->ID] == "wpqa_yes")?" wpqa_voted_already":"")?>" title="<?php esc_attr_e("Dislike","wpqa");?>"><i class="<?php echo apply_filters('wpqa_vote_down_icon','icon-down-dir');?>"></i></a></li>
			<?php }?>
		</ul>
	<?php }
endif;
/* Vote answer */
add_action('wpqa_answer_vote','wpqa_answer_vote',1,6);
if (!function_exists('wpqa_answer_vote')) :
	function wpqa_answer_vote($post_type,$user_id,$comment_user_id,$comment_id,$comment_vote,$show_dislike_answers,$class = "") {
		$comment_vote_type = apply_filters("wpqa_comment_vote_type","comment",(isset($post_type)?$post_type:""));
		$active_vote_unlogged = wpqa_options("active_vote_unlogged");
		$count_up = get_comment_meta($comment_id,'wpqa_comment_vote_up',true);
		$count_down = get_comment_meta($comment_id,'wpqa_comment_vote_down',true);
		$count_up = (isset($count_up) && is_array($count_up) && !empty($count_up)?$count_up:array());
		$count_down = (isset($count_down) && is_array($count_down) && !empty($count_down)?$count_down:array());
		$uniqid_cookie = wpqa_options("uniqid_cookie");?>
		<ul class="question-vote answer-vote<?php echo ($show_dislike_answers != "on"?" answer-vote-dislike":"").($class != ""?" ".$class:"")?>">
			<li><a href="#"<?php echo ((is_user_logged_in() && $comment_user_id != $user_id) || (!is_user_logged_in() && $active_vote_unlogged == "on")?' data-id="'.$comment_id.'"':'')?> data-type="<?php echo esc_html($comment_vote_type)?>" data-vote-type="up" class="wpqa_vote comment_vote_up<?php echo (is_user_logged_in() && $comment_user_id != $user_id?"":(is_user_logged_in() && $comment_user_id == $user_id?" vote_not_allow":($active_vote_unlogged == "on"?"":" vote_not_user"))).((is_user_logged_in() && $comment_user_id != $user_id) || (!is_user_logged_in() && $active_vote_unlogged == "on")?' vote_allow':'').((is_user_logged_in() && is_array($count_up) && in_array($user_id,$count_up)) || (!is_user_logged_in() && isset($_COOKIE[$uniqid_cookie.'wpqa_comment_vote_up'.$comment_id]) && $_COOKIE[$uniqid_cookie.'wpqa_comment_vote_up'.$comment_id] == "wpqa_yes_comment")?" wpqa_voted_already":"")?>" title="<?php esc_attr_e("Like","wpqa");?>"><i class="<?php echo apply_filters('wpqa_vote_up_icon','icon-up-dir');?>"></i></a></li>
			<li class="vote_result"<?php echo (is_single()?' itemprop="upvoteCount"':'')?>><?php echo ($comment_vote != ""?wpqa_count_number($comment_vote):0)?></li>
			<li class="li_loader"><span class="loader_3 fa-spin"></span></li>
			<?php if ($show_dislike_answers != "on") {?>
				<li class="dislike_answers"><a href="#"<?php echo ((is_user_logged_in() && $comment_user_id != $user_id) || (!is_user_logged_in() && $active_vote_unlogged == "on")?' data-id="'.$comment_id.'"':'')?> data-type="<?php echo esc_html($comment_vote_type)?>" data-vote-type="down" class="wpqa_vote comment_vote_down<?php echo (is_user_logged_in() && $comment_user_id != $user_id?"":(is_user_logged_in() && $comment_user_id == $user_id?" vote_not_allow":($active_vote_unlogged == "on"?"":" vote_not_user"))).((is_user_logged_in() && $comment_user_id != $user_id) || (!is_user_logged_in() && $active_vote_unlogged == "on")?' vote_allow':'').((is_user_logged_in() && is_array($count_down) && in_array($user_id,$count_down)) || (!is_user_logged_in() && isset($_COOKIE[$uniqid_cookie.'wpqa_comment_vote_down'.$comment_id]) && $_COOKIE[$uniqid_cookie.'wpqa_comment_vote_down'.$comment_id] == "wpqa_yes_comment")?" wpqa_voted_already":"")?>" title="<?php esc_attr_e("Dislike","wpqa");?>"><i class="<?php echo apply_filters('wpqa_vote_down_icon','icon-down-dir');?>"></i></a></li>
			<?php }?>
		</ul>
	<?php }
endif;
/* Question list */
add_action("wpqa_question_list_details","wpqa_question_list_details",1,10);
if (!function_exists('wpqa_question_list_details')) :
	function wpqa_question_list_details($post,$user_id,$anonymously_user,$question_edit,$question_delete,$question_close,$closed_question,$active_reports,$active_logged_reports,$moderators_permissions) {
		$post_author = $post->post_author;
		$is_super_admin = is_super_admin($user_id);
		echo '<ul class="question-link-list">';
			$moderator_categories = wpqa_user_moderator_categories($user_id,$post->ID);			
			$edit   = ($user_id > 0 && ($is_super_admin || ((($user_id == $post_author && $post_author > 0) || ($anonymously_user == $user_id && $post_author > 0)) && $question_edit == "on") || ($moderator_categories == true && isset($moderators_permissions['edit']) && $moderators_permissions['edit'] == "edit"))?true:false);
			$delete = ($user_id > 0 && ($is_super_admin || ((($user_id == $post_author && $post_author > 0) || ($anonymously_user == $user_id && $post_author > 0)) && $question_delete == "on") || ($moderator_categories == true && isset($moderators_permissions['delete']) && $moderators_permissions['delete'] == "delete"))?true:false);
			$close  = ($user_id > 0 && ($is_super_admin || ((($user_id == $post_author && $post_author > 0) || ($anonymously_user == $user_id && $post_author > 0) || ($moderator_categories == true && isset($moderators_permissions['close']) && $moderators_permissions['close'] == "close")) && $question_close == "on"))?true:false);
			$report = ($active_reports == "on" && ((is_user_logged_in() && (($user_id != $post_author && $post_author > 0) || $post_author == 0 || $anonymously_user == 0 || ($anonymously_user != "" && $anonymously_user != $user_id && $user_id > 0))) || $is_super_admin || (!is_user_logged_in() && $active_logged_reports != "on"))?true:false);
			if ($edit == true || $delete == true || $close == true) {
				if ($edit == true) {
					echo '<li><a class="dropdown-item" href="'.wpqa_edit_permalink($post->ID).'"><i class="icon-pencil"></i>'.esc_html__("Edit","wpqa").'</a></li>';
				}
				if ($delete == true) {
					echo '<li><a class="dropdown-item question-delete" href="'.esc_url_raw(add_query_arg(array("activate_delete" => true,"delete" => $post->ID,"wpqa_delete_nonce" => wp_create_nonce("wpqa_delete_nonce")),get_permalink($post->ID))).'"><i class="icon-trash"></i>'.esc_html__("Delete","wpqa").'</a></li>';
				}
				if ($close == true) {
					echo '<li><a class="dropdown-item '.($closed_question == 1?"question-open":"question-close").'" href="#" data-nonce="'.wp_create_nonce("wpqa_open_close_nonce").'" title="'.($closed_question == 1?esc_html__("Open the question","wpqa"):esc_html__("Close the question","wpqa")).'"><i class="icon-lock'.($closed_question == 1?"-open":"").'"></i>'.($closed_question == 1?esc_html__("Open","wpqa"):esc_html__("Close","wpqa")).'</a></li>';
				}
			}
			if ($report == true) {
				echo '<li class="report_activated"><a class="dropdown-item report_q" href="'.esc_attr($post->ID).'"><i class="icon-attention"></i>'.esc_html__("Report","wpqa").'</a></li>';
			}
		echo '</ul>';
	}
endif;
/* User moderator */
if (!function_exists('wpqa_user_moderator')) :
	function wpqa_user_moderator($user_id) {
		$active_moderators = wpqa_options("active_moderators");
		if ($active_moderators == "on") {
			$user_moderator = get_user_meta($user_id,prefix_author."user_moderator",true);
			if ($user_moderator == "on") {
				$moderators_permissions = wpqa_user_permissions($user_id);
				return $moderators_permissions;
			}
		}
	}
endif;
/* User moderator categories */
if (!function_exists('wpqa_user_moderator_categories')) :
	function wpqa_user_moderator_categories($user_id,$post_id) {
		$active_moderators = wpqa_options("active_moderators");
		if ($active_moderators == "on") {
			$moderator_categories = get_user_meta($user_id,prefix_author."moderator_categories",true);
			$post = get_post($post_id);
			$post_type = $post->post_type;
			$term_list = wp_get_post_terms($post_id,($post_type == "post"?"category":wpqa_question_categories),array("fields" => "ids"));
			$yes_for_category = false;
			if (is_array($moderator_categories) && !empty($moderator_categories) && ((($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && in_array("q-0",$moderator_categories)) || ($post_type == "post" && in_array("p-0",$moderator_categories)))) {
				$yes_for_category = true;
			}else if (is_array($term_list) && !empty($term_list)) {
				foreach ($term_list as $value) {
					if (is_array($moderator_categories) && !empty($moderator_categories) && in_array($value,$moderator_categories)) {
						$yes_for_category = true;
					}
				}
			}
		}
		return (isset($yes_for_category)?$yes_for_category:false);
	}
endif;
/* User permissions */
if (!function_exists('wpqa_user_permissions')) :
	function wpqa_user_permissions($user_id) {
		$custom_moderators_permissions = get_user_meta($user_id,prefix_author."custom_moderators_permissions",true);
		if ($custom_moderators_permissions == "on") {
			$moderators_permissions = get_user_meta($user_id,prefix_author."moderators_permissions",true);
		}else {
			$moderators_permissions = wpqa_options("moderators_permissions");
		}
		return $moderators_permissions;
	}
endif;
/* Load answers jQuery */
if (!function_exists('wpqa_question_answer_popup')) :
	function wpqa_question_answer_popup() {
		$post_id = (int)$_POST["post_id"];
		$user_id = get_current_user_id();
		if (is_user_logged_in()) {
			$user_is_login = get_userdata($user_id);
			$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
		}
		$post_number = get_option("posts_per_page");
		echo "<div class='panel-pop question-panel-pop' id='article-question-".$post_id."' data-width='690'>
			<i class='icon-cancel'></i>
			<div class='panel-pop-content'>";
				$paged         = wpqa_paged();
				$current       = max(1,$paged);
				$max_num_pages = (int)wpqa_count_comments($post_id);
				$comments_all  = get_comments(array('post_id' => $post_id,'number' => $post_number,'status' => 'approve','orderby' => 'comment_date','order' => 'DESC'));
				if (!empty($comments_all)) {?>
					<div class="page-content commentslist">
						<ol class="commentlist clearfix">
							<?php foreach($comments_all as $comment) {
								$yes_private = wpqa_private($comment->comment_post_ID,get_post($comment->comment_post_ID)->post_author,$user_id);
								if ($yes_private == 1) {
										$comment_id = esc_html($comment->comment_ID);
										wpqa_comment($comment,"","","answer");?>
									</li>
								<?php }else {?>
									<li class="comment">
										<div class="comment-body clearfix">
											<?php echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry it is a private answer.","wpqa").'</p></div>';?>
										</div>
									</li>
								<?php }
							}?>
						</ol>
					</div>
				<?php }else {
					echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("There are no answers yet.","wpqa").'</p></div>';
				}
				wpqa_load_pagination(array(
					"post_pagination" => "load_more",
					"max_num_pages" => $max_num_pages,
					"it_answer_pagination" => true,
					"its_post_type" => wpqa_questions_type,
					"its_answer" => true,
				));
				$custom_permission = wpqa_options("custom_permission");
				$get_post = get_post($post_id);
				$post_type = $get_post->post_type;
				$can_add_answer = apply_filters("wpqa_can_add_answer",true,$user_id,$custom_permission,(isset($roles)?$roles:array()),$get_post);
				if ($can_add_answer == true) {
					echo "<div class='comment-respond'>";
						if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
							do_action("wpqa_pay_to_answer",$user_id);
						}
						$allow_to_add_answer = apply_filters("wpqa_allow_to_add_answer_ajax",true,$user_id,$post_id);
						if ($allow_to_add_answer == true) {
							echo "<h3 class='section-title'>".esc_html__("Leave an answer","wpqa")."</h3>
							<form action='' method='post' class='post-section comment-form answers-form'>
								<div class='wpqa_error'></div>
								<div class='form-input form-textarea form-comment-normal'>
									<textarea class='form-control' name='comment' cols='58' rows='8' aria-required='true' placeholder='".esc_html_x("Answer","Answer textarea","wpqa")."'></textarea>
									<i class='icon-pencil'></i>
								</div>
								<div class='clearfix'></div>
								<p class='form-submit'>
									<input name='submit' type='submit' class='button-default btn btn__primary btn__block btn__large__height add-answer-ajax button-hide-click button-default-question' value='".esc_html__("Submit","wpqa")."'>
									<span class='clearfix'></span>
									<span class='load_span'><span class='loader_2'></span></span>
									<input type='hidden' name='action' value='wpqa_question_add_answer'>
									<input type='hidden' name='post_id' value='".$post_id."'>
									<input type='hidden' name='wpqa_add_answer_nonce' value='".wp_create_nonce("wpqa_add_answer_nonce")."'>
								</p>
							</form>";
						}
					echo "</div>";
				}
			echo "</div>
		</div>";
		die();
	}
endif;
add_action('wp_ajax_wpqa_question_answer_popup','wpqa_question_answer_popup');
add_action('wp_ajax_nopriv_wpqa_question_answer_popup','wpqa_question_answer_popup');
/* Add answers jQuery */
if (!function_exists('wpqa_question_add_answer')) :
	function wpqa_question_add_answer() {
		check_ajax_referer('wpqa_add_answer_nonce','wpqa_add_answer_nonce');
		$comment = wpqa_esc_textarea($_POST["comment"]);
		$post_id = (int)$_POST["post_id"];
		$current_user = wp_get_current_user();
		$time = current_time('mysql');
		$approved = 1;
		if (!is_user_logged_in()) {
			$comment_unlogged = wpqa_options("comment_unlogged");
			$approved = ($comment_unlogged == "draft"?0:1);
		}else {
			$custom_permission = wpqa_options("custom_permission");
			if ($custom_permission == "on" && !is_super_admin($current_user->ID)) {
				if (is_user_logged_in()) {
					$roles = $current_user->allcaps;
					$approved = (isset($roles["approve_answer"]) && $roles["approve_answer"] == 1?1:0);
				}
			}
		}
		$insert_data = array(
			'comment_post_ID'      => $post_id,
			'comment_author'       => $current_user->display_name,
			'comment_author_email' => $current_user->user_email,
			'comment_author_url'   => $current_user->user_url,
			'comment_content'      => $comment,
			'user_id'              => $current_user->ID,
			'comment_date'         => $time,
			'comment_approved'     => $approved,
		);
		$comment_id = wp_insert_comment($insert_data);
		update_comment_meta($comment_id,'comment_type',"question");
		update_comment_meta($comment_id,'comment_vote',0);
		update_comment_meta($comment_id,'wpqa_reactions_count',0);
		$question_user_id = get_post_meta($post_id,"user_id",true);
		if ($question_user_id != "" && $question_user_id > 0) {
			update_comment_meta($comment_id,"answer_question_user","answer_question_user");
		}
		$comment = get_comment($comment_id);
		$comment_user_id = $comment->user_id;
		$get_post = get_post($post_id);
		wpqa_answer_notifications($comment,$get_post,$comment_id,$post_id,$comment_user_id);
		wpqa_after_add_comment($comment,$comment_id,$post_id);
		wpqa_comment($comment,"","","answer");
		$can_add_answer = apply_filters("wpqa_can_add_answer",true,$current_user->ID,(isset($custom_permission)?$custom_permission:""),(isset($roles)?$roles:array()),$get_post);
		if ($can_add_answer != true) {
			echo "wpqa_cannt_answer_more";
		}
		die();
	}
endif;
add_action('wp_ajax_wpqa_question_add_answer','wpqa_question_add_answer');
add_action('wp_ajax_nopriv_wpqa_question_add_answer','wpqa_question_add_answer');
/* Get poll results */
function wpqa_show_poll_results($post_id,$user_id,$return = "all") {
	$theme_sidebar = wpqa_sidebars("sidebar_where");
	if (has_discy()) {
		if ($theme_sidebar == "centered") {
			$image_width = 181;
			$image_height = 155;
		}else if ($theme_sidebar == "menu_sidebar") {
			$image_width = 151;
			$image_height = 115;
		}else if ($theme_sidebar == "menu_left") {
			$image_width = 247;
			$image_height = 171;
		}else if ($theme_sidebar == "full") {
			$image_width = 314;
			$image_height = 245;
		}else {
			$image_width = 218;
			$image_height = 165;
		}
	}else {
		if ($theme_sidebar == "menu_sidebar") {
			$image_width = 165;
			$image_height = 125;
		}else if ($theme_sidebar == "menu_left") {
			$image_width = 265;
			$image_height = 190;
		}else if ($theme_sidebar == "full") {
			$image_width = 331;
			$image_height = 255;
		}else {
			$image_width = 198;
			$image_height = 155;
		}
	}
	$poll_user_only     = wpqa_options("poll_user_only");
	$poll_image         = wpqa_options("poll_image");
	$question_poll_yes  = false;
	$multicheck_poll    = get_post_meta($post_id,"multicheck_poll",true);
	$question_poll_num  = get_post_meta($post_id,"question_poll_num",true);
	$asks               = get_post_meta($post_id,"ask",true);
	$wpqa_polls         = get_post_meta($post_id,"wpqa_poll",true);
	$wpqa_polls         = (isset($wpqa_polls) && is_array($wpqa_polls) && !empty($wpqa_polls)?$wpqa_polls:array());
	$wpqa_question_poll = get_post_meta($post_id,"wpqa_question_poll",true);
	$wpqa_question_poll = (isset($wpqa_question_poll) && is_array($wpqa_question_poll) && !empty($wpqa_question_poll)?$wpqa_question_poll:array());
	if (isset($asks) && is_array($asks)) {
		$key_k = 0;
		foreach ($asks as $key_ask => $value_ask) {
			$key_k++;
			$sort_polls[$key_k]["image"] = (isset($asks[$key_ask]) && isset($asks[$key_ask]["image"])?$asks[$key_ask]["image"]:"");
			$sort_polls[$key_k]["id"] = (int)$asks[$key_ask]["id"];
			$sort_polls[$key_k]["title"] = (isset($asks[$key_ask]["title"])?$asks[$key_ask]["title"]:"");
			$sort_polls[$key_k]["value"] = (isset($asks[$key_ask]["value"]) && $asks[$key_ask]["value"] != ""?$asks[$key_ask]["value"]:(isset($wpqa_polls[$key_ask]["value"]) && $wpqa_polls[$key_ask]["value"] != ""?$wpqa_polls[$key_ask]["value"]:0));
			$sort_polls[$key_k]["user_ids"] = (isset($asks[$key_ask]["user_ids"]) && $asks[$key_ask]["user_ids"] != ""?$asks[$key_ask]["user_ids"]:(isset($wpqa_polls[$key_ask]["user_ids"]) && $wpqa_polls[$key_ask]["user_ids"] != ""?$wpqa_polls[$key_ask]["user_ids"]:array()));
		}
	}
	$output = '';
	if (isset($sort_polls) && is_array($sort_polls)) {
		$uniqid_cookie = wpqa_options("uniqid_cookie");
		if ($return == "results" || (is_user_logged_in() && is_array($wpqa_question_poll) && in_array($user_id,$wpqa_question_poll)) || (!is_user_logged_in() && isset($_COOKIE[$uniqid_cookie.'wpqa_question_poll'.$post_id]) && $_COOKIE[$uniqid_cookie.'wpqa_question_poll'.$post_id] == "wpqa_yes_poll")) {
			$question_poll_yes = true;
		}
		$output .= '<div class="poll-box mb-4">';
			$poll_1 = '<div class="poll_1 poll__results'.($question_poll_yes == false?" wpqa_hide":"").'">
				<h3 class="poll__title"><i class="icon-help"></i>'.esc_html__("Poll Results","wpqa").'</h3>';
				if ($poll_user_only == "on" && !is_user_logged_in()) {
					$poll_1 .= '<p class="still-not-votes">'.esc_html__("Please login to vote and see the results.","wpqa").'</p>';
				}else {
					if ($question_poll_num > 0) {
						$k = 0;
						$poll_images = array();
						foreach($sort_polls as $v_ask):
							$k++;
							$image = false;
							if ($poll_image == "on" && isset($v_ask['image']) && isset($v_ask['image']['id']) && $v_ask['image']['id'] != "" && esc_html(wpqa_image_url_id($v_ask['image'])) != "") {
								$image = wpqa_get_aq_resize_img($image_width,$image_height,"",(isset($v_ask['image']['id']) && $v_ask['image']['id'] != ""?esc_html($v_ask['image']['id']):""),"",(isset($v_ask['title']) && $v_ask['title'] != ''?esc_html($v_ask['title']):''));
								if ($image != "") {
									$poll_image_available = true;
									$poll_images[$k] = $image;
								}
							}
						endforeach;
						$poll_1 .= '<div class="progressbar-main">
							<div class="progressbar-wrap'.(isset($poll_image_available)?" poll-wrap-images":"").'">';
								$k = 0;
								foreach($sort_polls as $v_ask):
									$k++;
									$poll_voters = (int)$v_ask['value'];
									if ($question_poll_num != "" || $question_poll_num != 0) {
										$value_poll = round(($poll_voters/$question_poll_num)*100,2);
									}
									if ((isset($poll_image_available) && (isset($poll_images[$k])) || (isset($v_ask['title']) && $v_ask['title'] != '')) || !isset($poll_image_available)) {
										$poll_1 .= '<div class="main-progressbar progress'.(isset($poll_images[$k])?" progressbar-image container-poll-image":"").'">';
											if (isset($poll_images[$k])) {
												$poll_1 .= '<div class="wpqa_radio_p'.(isset($poll_images[$k])?" wpqa_result_poll_image wpqa_poll_image":"").'">
													<span>';
														if (isset($poll_images[$k])) {
															$poll_1 .= $poll_images[$k];
														}
													$poll_1 .= '</span>
													<span class="progressbar-title">
														'."<span class='progressbar-first-span'>".($question_poll_num == 0?0:$value_poll)."%</span><span class='progressbar-second-span'>".(isset($v_ask['title']) && $v_ask['title'] != ''?wp_unslash(esc_html($v_ask['title'])):'')." ".($poll_voters != ""?"( ".wpqa_count_number($poll_voters)." "._n("voter","voters",$poll_voters,"wpqa")." )":"").'</span>
													</span>';
											}else {
												$poll_1 .= '<span class="progressbar-title">
													'."<span class='progressbar-first-span'>".($question_poll_num == 0?0:$value_poll)."%</span><span class='progressbar-second-span'>".(isset($v_ask['title']) && $v_ask['title'] != ''?wp_unslash(esc_html($v_ask['title'])):'')." ".($poll_voters != ""?"( ".wpqa_count_number($poll_voters)." "._n("voter","voters",$poll_voters,"wpqa")." )":"").'</span>
												</span>';
											}
											$poll_1 .= '<div class="progressbar">
											    <div class="progressbar-percent progress-bar bg-success poll-result-'.($poll_voters == 0?0:$value_poll).'" attr-percent="'.($poll_voters == 0?100:$value_poll).'"></div>
											</div>';
										$poll_1 .= '</div>';
										if (isset($poll_images[$k])) {
											$poll_1 .= '</div>';
										}
									}
								endforeach;
							$poll_1 .= '</div><!-- End progressbar-wrap -->
							<div class="clearfix"></div>
							<div class="poll-num total__results">'.esc_html__("Based On","wpqa")." <span>".($question_poll_num > 0?wpqa_count_number($question_poll_num):0)." "._n("Vote","Votes",$question_poll_num,"wpqa")."</span>".'</div>
						</div><!-- End progressbar-main -->';
					}else {
						$poll_1 .= '<p class="still-not-votes">'.esc_html__("No votes. Be the first one to vote.","wpqa").'</p>';
					}
				}
				if ($question_poll_yes == false) {
					if (wpqa_input_button() == "button") {
						$poll_1 .= '<button type="submit" class="ed_button poll_polls btn btn__success">'.esc_attr__("Voting","wpqa").'</button>';
					}else {
						$poll_1 .= '<input type="submit" class="ed_button poll_polls" value="'.esc_attr__("Voting","wpqa").'">';
					}
				}
			$poll_1 .= '</div>';
			$output .= apply_filters("wpqa_show_poll",$poll_1,$poll_user_only,$user_id,$question_poll_yes,$question_poll_num,$sort_polls,$wpqa_polls,$poll_image);
			$output .= '<div class="clear"></div>';
			if ($question_poll_yes == false) {
				$question_poll_title = get_post_meta($post_id,prefix_meta."question_poll_title",true);
				$k = 0;
				$poll_images = array();
				foreach($sort_polls as $v_ask):
					$k++;
					$image = false;
					if ($poll_image == "on" && isset($v_ask['image']) && isset($v_ask['image']['id']) && $v_ask['image']['id'] != "" && esc_html(wpqa_image_url_id($v_ask['image'])) != "") {
						$image = wpqa_get_aq_resize_img($image_width,$image_height,"",(isset($v_ask['image']['id']) && $v_ask['image']['id'] != ""?esc_html($v_ask['image']['id']):""),"",(isset($v_ask['title']) && $v_ask['title'] != ''?esc_html($v_ask['title']):''));
						if ($image != "") {
							$poll_image_available = true;
							$poll_images[$k] = $image;
						}
					}
				endforeach;
				$output .= '<div class="poll_2">
					<h3 class="poll__title"><i class="icon-help"></i>'.($question_poll_title != ""?$question_poll_title:esc_html__("Participate in Poll, Choose Your Answer.","wpqa")).'</h3>
					<form class="wpqa_form">
						<div class="form-inputs clearfix'.(isset($poll_image_available)?" form-input-polls poll-wrap-images":"").'">';
							$k = 0;
							foreach($sort_polls as $v_ask):
								$k++;
								if ((isset($poll_image_available) && (isset($poll_images[$k])) || (isset($v_ask['title']) && $v_ask['title'] != '')) || !isset($poll_image_available)) {
									$output .= '<p class="wpqa_radio_p'.(isset($poll_images[$k])?" wpqa_poll_image container-poll-image":"").'">
										<span class="'.($multicheck_poll == "on"?"wpqa_checkbox":"wpqa_radio").(isset($poll_images[$k])?" wpqa_poll_image":"").'">
											<input class="required-item" id="ask-'.esc_attr($v_ask['id']).'-title-'.esc_attr($post_id).'" name="ask_radio" type="'.($multicheck_poll == "on"?"checkbox":"radio").'" value="poll_'.(int)$v_ask['id'].'"'.(isset($v_ask['title']) && $v_ask['title'] != ''?' data-rel="poll_'.esc_html($v_ask['title']).'"':'').'>';
											if (isset($poll_images[$k])) {
												$output .= $poll_images[$k];
											}
										$output .= '</span>
										<label for="ask-'.esc_attr($v_ask['id']).'-title-'.esc_attr($post_id).'">'.(isset($v_ask['title']) && $v_ask['title'] != ''?wp_unslash(esc_html($v_ask['title'])):'').'</label>
									</p>';
								}
							endforeach;
						$output .= '</div>';
						if ($question_poll_yes == false) {
							$output .= '<div class="load_span"><span class="loader_2"></span></div>';
							if (wpqa_input_button() == "button") {
								$output .= '<button type="submit" class="ed_button poll-submit button-default btn btn__success">'.esc_attr__("Submit","wpqa").'</button>
								<button type="submit" class="ed_button poll_results btn btn__primary">'.esc_attr__("Results","wpqa").'</button>';
							}else {
								$output .= '<input type="submit" class="ed_button poll-submit button-default" value="'.esc_attr__("Submit","wpqa").'">
								<input type="submit" class="ed_button poll_results" value="'.esc_attr__("Results","wpqa").'">';
							}
						}
					$output .= '</form>
				</div>';
			}
		$output .= '</div>';
	}
	return $output;
}
/* Show poll */
function wpqa_show_poll($post_id,$user_id,$question_poll,$pending_questions,$class = "poll-area-before",$return = "all") {
	$output = '';
	if ($question_poll == "on") {
		$output .= '<div class="all_single_post_content poll-area '.$class.($pending_questions == true?" wpqa_hide":"").'">
			'.wpqa_show_poll_results($post_id,$user_id,$return).'
		</div><!-- End poll-area -->';
	}
	return $output;
}
/* Index ask user questions */
add_filter('wp_robots','wpqa_ask_question_wp_robots');
if (!function_exists('wpqa_ask_question_wp_robots')) :
	function wpqa_ask_question_wp_robots($robots) {
		if (wpqa_is_add_questions()) {
			$get_user_id = wpqa_add_question_user();
			$user_id = get_current_user_id();
			if ($get_user_id > 0 && $user_id != $get_user_id) {
				$display_name = get_the_author_meta('display_name',$get_user_id);
			}
			if (isset($display_name) && $display_name != "") {
				$robots['noindex']  = true;
				$robots['nofollow'] = true;
			}else {
				$robots['noindex']  = false;
				$robots['index']  = true;
				$robots['nofollow'] = false;
			}
		}
		return $robots;
	}
endif;?>