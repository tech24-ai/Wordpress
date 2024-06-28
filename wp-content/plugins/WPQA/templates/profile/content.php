<?php

/* @author    2codeThemes
*  @package   WPQA/templates/profile
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$active_points        = wpqa_options("active_points");
$active_moderators    = wpqa_options("active_moderators");
$active_notifications = wpqa_options("active_notifications");
$active_activity_log  = wpqa_options("active_activity_log");
$transactions_page    = wpqa_options("transactions_page");
$active_message       = wpqa_options("active_message");
$active_referral      = wpqa_options("active_referral");
$old_themes           = wpqa_options("old_themes");
$rows_per_page        = get_option("posts_per_page");
$paged                = wpqa_paged();
$current              = max(1,$paged);
$show_custom_error    = true;
$last_one             = "";
$wpqa_user_title      = wpqa_user_title();
$is_super_admin       = is_super_admin($user_id);
$first_one            = (isset($first_one) && $first_one != ""?$first_one:"");
$first_one_filter     = apply_filters("wpqa_filter_profile_user_title",false);
$payment_available    = wpqa_payment_available();
$author_widget        = wpqa_options("author_widget");

include wpqa_get_template("profile-page-tabs.php","profile/");

if ($wpqa_user_title == "" && $first_one != "") {
	$last_one = $first_one;
}else if ($wpqa_user_title != "" && ($first_one_filter == true || $wpqa_user_title != "")) {
	$last_one = $wpqa_user_title;
}else if ($first_one == "") {
	$no_pages = true;
}
if ($last_one != "") {
	$type_slug = explode("_",$last_one);
	$last_one = $type_slug[0].(isset($type_slug[1])?'-'.$type_slug[1]:'');
}

$profile_last_one = apply_filters("wpqa_content_profile_last_one",false,$last_one);
if (($profile_last_one == true || $last_one == "questions" || $last_one == "posts" || ($last_one == "favorites" && ($show_point_favorite == "on" || wpqa_is_user_owner())) || ($last_one == "asked" && $ask_question_to_users == "on") || (($last_one == "my-asked" || $last_one == "asked-questions") && $ask_question_to_users == "on" && wpqa_is_user_owner()) || ($pay_ask == "on" && $last_one == "paid-questions") || ($last_one == "followed" && ($show_point_favorite == "on" || wpqa_is_user_owner())) || $last_one == "polls" || $last_one == "followers-questions" || $last_one == "followers-posts")) {
	if ($profile_last_one == true || $last_one == "questions" || ($last_one == "favorites" && ($show_point_favorite == "on" || wpqa_is_user_owner())) || ($last_one == "followed" && ($show_point_favorite == "on" || wpqa_is_user_owner())) || $last_one == "polls" || $last_one == "followers-questions" || ($last_one == "asked" && $ask_question_to_users == "on") || (($last_one == "my-asked" || $last_one == "asked-questions") && $ask_question_to_users == "on" && wpqa_is_user_owner()) || ($pay_ask == "on" && $last_one == "paid-questions")) {
		$its_post_type = wpqa_questions_type;
	}
	$is_questions_sticky = false;
	global $post;
	include locate_template("theme-parts/loop.php");
}else if (wpqa_is_user_followers() || wpqa_is_user_following()) {
	include wpqa_get_template("follow.php","profile/");
}else if (wpqa_is_user_blocking()) {
	include wpqa_get_template("blocking.php","profile/");
}else if (wpqa_is_user_groups() || wpqa_is_user_joined_groups() || wpqa_is_user_managed_groups()) {
	include locate_template("theme-parts/loop-groups.php");
}else if (wpqa_is_user_referrals() && $active_referral == "on" && wpqa_is_user_owner()) {
	include wpqa_get_template("referrals.php","profile/");
}else if (wpqa_is_user_messages() && $active_message == "on" && wpqa_is_user_owner()) {
	include wpqa_get_template("messages.php","profile/");
}else if ($transactions_page == "on" && $payment_available == true && wpqa_is_user_transactions_profile()) {
	$the_currency = get_option("the_currency");
	if (isset($the_currency) && is_array($the_currency)) {
		$count = $k = 0;
		foreach ($the_currency as $key => $currency) {
			if (isset($currency) && $currency != "") {
				$_all_my_payment = get_user_meta($user_id,$user_id."_all_my_payment_".$currency,true);
				if ($_all_my_payment > 0) {
					$_all_my_payment_array[$currency] = $_all_my_payment;
					$count++;
				}
			}
		}
		if (isset($_all_my_payment_array) && is_array($_all_my_payment_array)) {
			foreach ($_all_my_payment_array as $currency => $money) {
				$k++;
				if ($money > 0) {
					if ($k == 1) {
						echo '<div class="alert-message alert-message-money"><i class="icon-basket"></i>';
					}else {
						echo '<div class="w-100"><i class="icon-basket"></i>';
					}
					echo '<span>'.esc_html__("You spent:","wpqa").'</span> '.(isset($money) && $money != ""?$money:0)." ".$currency;
					if ($k != 1) {
						echo '</div>';
					}
					if ($k == $count) {
						echo '</div>';
					}
				}
			}
		}
	}
	$args = array('author' => $user_id,'post_type' => 'statement','posts_per_page' => $rows_per_page,'paged' => $paged);
	$statements_query = new WP_Query( $args );
	if ($statements_query->have_posts()) {
		echo '<div id="section-'.$wpqa_user_title.'" class="user-notifications user-profile-area section-page-div">
			<div class="notifications-dropdown-menu">
				<ul class="list-unstyled mb-0">';
					while ( $statements_query->have_posts() ) { $statements_query->the_post();
						$statement_post = $statements_query->post;?>
						<li>
							<?php 
							echo "<div>";
								$payment_method = get_post_meta($statement_post->ID,"payment_method",true);
								$item_price = get_post_meta($statement_post->ID,"payment_item_price",true);
								$item_currency = get_post_meta($statement_post->ID,"payment_item_currency",true);
								$coupon = get_post_meta($statement_post->ID,"payment_coupon",true);
								$points = get_post_meta($statement_post->ID,"payment_points",true);
								$item_transaction = get_post_meta($statement_post->ID,"payment_item_transaction",true);
								$statement_type = get_post_meta($statement_post->ID,"statement_type",true);
								$payment_refund = get_post_meta($statement_post->ID,"payment_refund",true);
								$original_transaction = get_post_meta($statement_post->ID,"payment_original_transaction",true);
								$payment_type = get_post_meta($statement_post->ID,"payment_type",true);
								echo '<i class="icon-export"></i>
								<div>
									<span class="point-span price-span'.($payment_refund == "refund" || $statement_type == "refund"?" refund-span":"").'">'.($item_price > 0?($payment_refund == "refund" || $statement_type == "refund"?"-":"").esc_html($item_price)." ".$item_currency:($points != ""?$points." ".esc_html__("points","wpqa"):"")).(isset($coupon) && $coupon != ""?" - (".$coupon.")":"").'</span>';
									the_title();
								echo '</div>
							</div>';
							if ($payment_method != "" || $item_transaction != "") {
								echo '<div>
									<i class="icon-basket"></i>
									<div>
										<span class="point-span transaction-span">'.($payment_method != ""?($payment_method == "woocommerce"?"WooCommerce":esc_html($payment_method)):"").'</span>'.($item_transaction != ""?esc_html($item_transaction):"").($original_transaction != ""?" - ".esc_html($original_transaction):"").'
									</div>
								</div>';
							}
							$time_format = wpqa_options("time_format");
							$time_format = ($time_format?$time_format:get_option("time_format"));
							$date_format = wpqa_options("date_format");
							$date_format = ($date_format?$date_format:get_option("date_format"));
							$human_time_diff = human_time_diff(get_the_time('U'), current_time('timestamp'));
							echo '<div>
								<i class="icon-clock"></i>
								<div class="notification__date">
									'.sprintf(esc_html__('%1$s at %2$s','wpqa'),mysql2date($date_format,date($date_format,get_the_time('U'))),mysql2date($time_format,date($time_format,get_the_time('U')))).'
								</div>
							</div>';
							$payment_canceled = get_post_meta($statement_post->ID,"payment_canceled",true);
							echo wpqa_download_button_payment($statement_post->ID,$payment_canceled);
							if ($payment_type == "subscribe") {
								$add_canceled_message = $add_cancel_message = false;
								$cancel_subscription = wpqa_options("cancel_subscription");
								$payment_item_number = get_post_meta($statement_post->ID,"payment_item_number",true);
								$payment_custom = get_post_meta($statement_post->ID,"payment_custom",true);
								$payment_subscr_id = get_post_meta($statement_post->ID,"payment_subscr_id",true);
								$str_replace = str_replace("wpqa_".$payment_item_number."-","",$payment_custom);
								$get_post = get_post($str_replace);
								if (isset($get_post->ID)) {
									$payment_kind = get_post_meta($get_post->ID,prefix_meta."payment_kind",true);
									$subscriptions_options = get_post_meta($get_post->ID,prefix_meta."subscriptions_options",true);
									$payment_action = get_post_meta($get_post->ID,prefix_meta."payment_action",true);
									$payment_role = get_post_meta($get_post->ID,prefix_meta."payment_role",true);
									if ($payment_action == "role" && $payment_role != "") {
										$subscriptions_group = $payment_role;
										$get_post_id = $get_post->ID;
										$cancel_subscription = get_post_meta($get_post->ID,prefix_meta."cancel_subscription",true);
									}
								}
								if ($cancel_subscription == "on") {
									$get_post_id = (isset($get_post_id) && $get_post_id > 0?$get_post_id:"");
									if ($payment_subscr_id != "") {
										$canceled_subscription = get_user_meta($user_id,"wpqa_canceled_subscription".$get_post_id,true);
										if ($canceled_subscription == true) {
											$end_subscribe_time = get_user_meta($user_id,"end_subscribe_time".$get_post_id,true);
											$add_canceled_message = true;
										}else {
											$payment_method_user = get_user_meta($user_id,"wpqa_payment_method",true);
											$payment_method = ($payment_method_user == "Stripe" || $payment_method_user == "PayPal"?$payment_method_user:$payment_method);
											if (($payment_method == "Stripe" || $payment_method == "PayPal") && $payment_canceled != "canceled") {
												$add_cancel_message = true;
											}
										}
									}
								}
								if (($add_canceled_message == true && $end_subscribe_time != "") || $add_cancel_message == true) {
									echo '<div>
										<div>';
											if ($add_canceled_message == true && $end_subscribe_time != "") {
												echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("You have canceled your subscription, your membership will be canceled on:","wpqa").' '.date($date_format,$end_subscribe_time).'</p></div>';
											}
											if ($add_cancel_message == true) {
												if (($payment_method == "Stripe" && strpos($payment_subscr_id,'sub_') !== false) || $payment_method == "PayPal") {
													echo '<p class="cancel-subscription"><a class="button-default-2 btn btn__secondary"'.($get_post_id > 0?' data-id="'.$get_post_id.'"':'').' href="#">'.esc_html__("Cancel your subscription","wpqa").'</a></p>';
												}else {
													echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("You need to contact the admin to cancel your subscription.","wpqa").'</p></div>';
												}
											}
										echo '</div>
									</div>';
								}
							}?>
						</li>
					<?php }
				echo '</ul>
			</div>
		</div>';
		if ($statements_query->max_num_pages > 1) :
			$current = max(1,$paged);
			$count_total = (int)$statements_query->found_posts;
			$max_num_pages = ceil($count_total/$rows_per_page);
			$pagination_args = array(
				'total'     => $max_num_pages,
				'current'   => $current,
				'show_all'  => false,
				'prev_text' => '<i class="icon-left-open"></i>',
				'next_text' => '<i class="icon-right-open"></i>',
			);
			if (!get_option('permalink_structure')) {
				$pagination_args['base'] = esc_url_raw(add_query_arg('paged','%#%'));
			}
			$paginate_links = paginate_links($pagination_args);?>
			<div class="main-pagination"><div class='comment-pagination pagination'><?php echo ($paginate_links != ""?$paginate_links:"")?></div></div>
		<?php endif;
		wp_reset_postdata();
	}else {
		echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("There are no transactions yet.","wpqa").'</p></div>';
	}
}else if ((wpqa_is_user_notifications() && $active_notifications == "on" && wpqa_is_user_owner()) || (wpqa_is_user_activities() && $active_activity_log == "on" && wpqa_is_user_owner()) || (wpqa_is_user_points() && $active_points == "on" && ($show_point_favorite == "on" || wpqa_is_user_owner()))) {
	if (wpqa_is_user_points()) {
		$whats_type = $post_type = "point";
		$message_found = esc_html__("There are no points yet.","wpqa");
		$head_title = esc_html__("Points","wpqa");
		$head_icon = "icon-trophy";
	}else if (wpqa_is_user_notifications()) {
		$whats_type = $post_type = "notification";
		$message_found = esc_html__("There are no notifications yet.","wpqa");
		$head_title = esc_html__("Notifications","wpqa");
		$head_icon = "icon-ios-bell";
	}else {
		$whats_type = "activitie";
		$post_type = "activity";
		$message_found = esc_html__("There are no activities yet.","wpqa");
		$head_title = esc_html__("Activities Log","wpqa");
		$head_icon = "icon-ios-pulse-strong";
	}
	$args = array('author' => $wpqa_user_id,'post_type' => $post_type,'posts_per_page' => $rows_per_page,'paged' => $paged);
	$types_query = new WP_Query( $args );
	if ($types_query->have_posts()) {
		if ($post_type == "notification") {
			update_user_meta($wpqa_user_id,$wpqa_user_id.'_new_notification',0);
		}
		echo '<div id="section-'.$wpqa_user_title.'" class="user-notifications user-profile-area section-page-div custom__notifications">
			<div class="notifications-dropdown-menu">';
				if (has_himer() || has_knowly()) {
					echo '<div class="post-title-2 d-flex align-items-center justify-content-between">
						<h2 class="card-title mb-0 d-flex align-items-center">
							<i class="'.$head_icon.' font-xl card-title__icon"></i>
							<span>'.$head_title.'</span>
						</h2>
					</div>';
				}
				echo '<ul class="list-unstyled mb-0">';
					while ( $types_query->have_posts() ) { $types_query->the_post();
						$type_post = $types_query->post;
						if ($post_type == "notification") {
							$new_notification = get_post_meta($type_post->ID,"notification_new",true);
							update_post_meta($type_post->ID,"notification_new",0);
						}
						if ($post_type == "point") {
							$type_result = wpqa_point_result($type_post,$post_type);
						}else {
							$type_result = wpqa_notification_activity_result($type_post,$post_type);
							if ($post_type == "notification" && isset($type_result['another_user_id'])) {
								$gender_class = $type_result['another_user_id'];
							}else if ($post_type == "activity" && isset($type_result['user_id'])) {
								$gender_class = $type_result['user_id'];
							}
						}
						$class = (isset($type_result["text"])?preg_replace('/[^a-zA-Z0-9._\-]/','',strtolower($type_result["text"])):"");?>
						<li class="notifications__item d-flex<?php echo (isset($gender_class)?" ".wpqa_get_gender_class($gender_class,(isset($type_result["post_id"])?$type_result["post_id"]:0),(isset($type_result["comment_id"])?$type_result["comment_id"]:0)):'').($class != ""?($post_type == "point"?" points__".$class:" notifications__".$class):"").(isset($new_notification) && $new_notification == 1?" notifications__new":"")?>">
							<?php if ($post_type == "point") {
								if (isset($type_result["relation"]) && $type_result["relation"] == "+") {
									echo "<i class='icon-thumbs-up'></i>";
								}else {
									echo "<i class='icon-thumbs-down'></i>";
								}
								echo wpqa_show_points($type_result);
							}else if ($post_type == "notification") {
								echo wpqa_show_notifications($type_result);
							}else {
								echo wpqa_show_activities($type_result);
							}
							if (!isset($not_show_date) && isset($type_result['time'])) {?>
								<span class='notifications-date notification__date d-block mt-2'><?php echo ($type_result['time'])?></span>
							<?php }?>
							</div>
						</li>
					<?php }
				echo '</ul>
			</div>
		</div>';
		if ($types_query->max_num_pages > 1) :
			$current = max(1,$paged);
			$count_total = (int)$types_query->found_posts;
			$max_num_pages = ceil($count_total/$rows_per_page);
			$pagination_args = array(
				'total'     => $max_num_pages,
				'current'   => $current,
				'show_all'  => false,
				'prev_text' => '<i class="icon-left-open"></i>',
				'next_text' => '<i class="icon-right-open"></i>',
			);
			if (!get_option('permalink_structure')) {
				$pagination_args['base'] = esc_url_raw(add_query_arg('paged','%#%'));
			}
			$paginate_links = paginate_links($pagination_args);?>
			<div class="main-pagination"><div class='comment-pagination pagination'><?php echo ($paginate_links != ""?$paginate_links:"")?></div></div>
		<?php endif;
		wp_reset_postdata();
	}else {
		echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.$message_found.'</p></div>';
	}
}else if ((wpqa_is_pending_questions() || wpqa_is_pending_posts()) && ($is_super_admin || $active_moderators == "on") && wpqa_is_user_owner()) {
	$user_moderator = get_user_meta($user_id,prefix_author."user_moderator",true);
	if ($is_super_admin || $user_moderator == "on") {
		$moderator_categories = get_user_meta($user_id,prefix_author."moderator_categories",true);
		if ($is_super_admin || is_array($moderator_categories) && !empty($moderator_categories)) {
			if (wpqa_is_pending_questions()) {
				$its_post_type = wpqa_questions_type;
			}
			global $post;
			include locate_template("theme-parts/loop.php");
		}else if (!$is_super_admin && (!is_array($moderator_categories) || (is_array($moderator_categories) && empty($moderator_categories)))) {
			echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry, You are not moderator yet.","wpqa").'</p></div>';
		}
	}else {
		echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry, this is a private page.","wpqa").'</p></div>';
	}
}else if ($last_one == "answers" || $last_one == "best-answers" || $last_one == "comments" || $last_one == "followers-answers" || $last_one == "followers-comments") {
	$offset = ($paged -1) * $rows_per_page;
	if ($last_one == "answers" || $last_one == "comments" || $last_one == "followers-answers" || $last_one == "followers-comments") {
		if ($last_one == "followers-answers" || $last_one == "followers-comments") {
			$following_me = get_user_meta($wpqa_user_id,"following_me",true);
			$block_users = wpqa_options("block_users");
			$author__not_in = array();
			if ($block_users == "on") {
				if ($wpqa_user_id > 0) {
					$get_block_users = get_user_meta($wpqa_user_id,"wpqa_block_users",true);
				}
			}
			if (is_array($following_me) && !empty($following_me) && isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)) {
				$following_me = array_diff($following_me,$get_block_users);
			}
		}
		$comments_merge = array(($last_one == "followers-answers" || $last_one == "followers-comments"?"author__in":"user_id") => ($last_one == "followers-answers" || $last_one == "followers-comments"?$following_me:$wpqa_user_id));
		$post_type_for_comments = ($last_one == "answers" || $last_one == "followers-answers" || $last_one == "best-answers"?wpqa_questions_type:"post");
		$comments_all = get_comments(array_merge($comments_merge,array("status" => "approve",'number' => $rows_per_page,'offset' => $offset,'post_type' => $post_type_for_comments)));
		if (($last_one == "followers-answers" || $last_one == "followers-comments") && empty($following_me)) {
			$comments_all = array();
		}
		$user_ids = ($last_one == "followers-answers" || $last_one == "followers-comments"?(is_array($following_me) && !empty($following_me)?$following_me:array()):array($wpqa_user_id));
		$total = wpqa_count_the_comments(($last_one == "comments" || $last_one == "followers-comments"?"comments":"answers"),$user_ids,"","",(isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)?$get_block_users:array()));
	}else {
		$comments_all = get_comments(array('user_id' => $wpqa_user_id,"status" => "approve",'number' => $rows_per_page,'offset' => $offset,'post_type' => wpqa_questions_type,"meta_query" => array(array("key" => "best_answer_comment","compare" => "=","value" => "best_answer_comment"))));
		$total = wpqa_count_best_answers_meta($wpqa_user_id);
	}
	if (!empty($comments_all)) {
		$max_num_pages = ceil($total/$rows_per_page);
		$k_ad = -1;
		$zero_comment_index = 0;
		$pagination_args = array(
			'total'     => $max_num_pages,
			'current'   => $current,
			'show_all'  => false,
			'prev_text' => '<i class="icon-left-open"></i>',
			'next_text' => '<i class="icon-right-open"></i>',
		);
		if (!get_option('permalink_structure')) {
			$pagination_args['base'] = esc_url_raw(add_query_arg('paged','%#%'));
		}
		?>
		<div<?php echo ($last_one == "answers" || $last_one == "best-answers" || $last_one == "comments" || $last_one == "followers-answers" || $last_one == "followers-comments"?" id='section-".$wpqa_user_title."'":"")?> class="page-content commentslist section-page-div">
			<ol class="commentlist clearfix">
				<?php foreach ($comments_all as $comment_item) {$k_ad++;
					if ($last_one == "answers" || $last_one == "followers-answers") {
						$yes_private = wpqa_private($comment_item->comment_post_ID,get_post($comment_item->comment_post_ID)->post_author,get_current_user_id());
					}else {
						$yes_private = 1;
					}
					if ($yes_private == 1) {
							$comment_id = esc_html($comment_item->comment_ID);
							wpqa_comment($comment_item,"","",($last_one == "answers" || $last_one == "best-answers" || $last_one == "followers-answers"?"answer":"comment"),wpqa_is_user_owner(),$k_ad,($last_one == "best-answers"?"not show":""),array("comment_with_title" => true,"show_replies" => false));?>
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
		echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.($last_one == "best-answers"?esc_html__("There are no best answers yet.","wpqa"):($last_one == "answers" || $last_one == "followers-answers"?esc_html__("There are no answers yet","wpqa"):esc_html__("There are no comments yet","wpqa"))).'</p></div>';
	}
	if ($comments_all && $pagination_args["total"] > 1) {?>
		<div class="main-pagination"><div class='pagination'><?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?></div></div>
	<?php }
}else {
	if (!is_author()) {
		if (wpqa_is_user_notifications() || wpqa_is_user_referrals() || wpqa_is_user_messages() || wpqa_is_user_activities() || (((wpqa_is_user_points() && $active_points == "on") || $last_one == "followed" || $last_one == "favorites") && $show_point_favorite != "on" && !wpqa_is_user_owner())) {
			echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry, this is a private page.","wpqa").'</p></div>';
		}else if ($first_one_filter == false && (!isset($no_pages) || ($wpqa_user_title != "" && isset($no_pages)))) {
			echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, this page is not found.","wpqa").'</p></div>';
		}
	}
	do_action("wpqa_action_after_profile_content");
}?>