<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Subscription plans */
if (!function_exists('wpqa_subscription_plans')) :
	function wpqa_subscription_plans($currency = "",$payment_type_subscriptions = "") {
		$array = array();
		$subscriptions_style = wpqa_options("subscriptions_style");
		$user_id = get_current_user_id();
		$array_plans_name = array(
			"monthly"  => esc_html__("Monthly membership","wpqa"),
			"3months"  => esc_html__("Three months membership","wpqa"),
			"6months"  => esc_html__("Six Months membership","wpqa"),
			"yearly"   => esc_html__("Yearly membership","wpqa"),
			"lifetime" => esc_html__("Lifetime membership","wpqa"),
		);
		$subscription_plans = wpqa_options("subscription_plans");
		if (is_array($subscription_plans) && !empty($subscription_plans)) {
			$array = array(
				"free" => array("key" => "free","name" => esc_html__("Free membership","wpqa")),
			);
			if ((isset($canceled_subscription) && $canceled_subscription == true) || is_user_logged_in() || is_super_admin($user_id)) {
				unset($array["free"]);
			}
			foreach ($subscription_plans as $key_subscription => $value_subscription) {
				if (is_array($value_subscription["subscriptions_options"]) && !empty($value_subscription["subscriptions_options"])) {
					foreach ($value_subscription["subscriptions_options"] as $key_plan => $value_plan) {
						if ($key_plan == $value_plan) {
							$array[$value_subscription["id"]."_".$value_plan] = array(
								"popular" => $value_subscription["popular"],
								"plan" => $value_subscription["subscriptions_name"],
								"key" => $value_subscription["id"],
								"role" => $value_subscription["subscriptions_group"],
								"type" => $value_plan,
								"name" => $array_plans_name[$value_plan],
								"price" => (isset($value_subscription[($payment_type_subscriptions == "points"?"subscribe_".$value_plan."_points":"subscribe_".$value_plan.$currency)])?$value_subscription[($payment_type_subscriptions == "points"?"subscribe_".$value_plan."_points":"subscribe_".$value_plan.$currency)]:0),
								"points_price" => (isset($value_subscription["subscribe_".$value_plan."_points"])?$value_subscription["subscribe_".$value_plan."_points"]:0),
								"subscription_features" => ($subscriptions_style == "style_2" && isset($value_subscription["subscription_features"]) && is_array($value_subscription["subscription_features"]) && !empty($value_subscription["subscription_features"])?$value_subscription["subscription_features"]:array())
							);
						}
					}
				}
			}
		}
		$array = apply_filters("wpqa_get_subscription_plans",$array);
		return $array;
	}
endif;
/* Subscription roles */
if (!function_exists('wpqa_subscription_roles')) :
	function wpqa_subscription_roles() {
		$role_plans = array();
		$subscription_plans = wpqa_subscription_plans();
		if (is_array($subscription_plans) && !empty($subscription_plans)) {
			foreach ($subscription_plans as $key_plan => $value_plan) {
				if (isset($value_plan["role"])) {
					$role_plans[$value_plan["role"]] = (isset($value_plan["plan"]) && $value_plan["plan"] != ""?$value_plan["plan"]:ucfirst($value_plan["role"])).(isset($value_plan["name"]) && $value_plan["name"] != ""?" - ".$value_plan["name"]:"");
				}
			}
		}
		return $role_plans;
	}
endif;
/* Subscription role options */
if (!function_exists('wpqa_subscription_role_options')) :
	function wpqa_subscription_role_options($array_key = "key") {
		$role_plans = array();
		$array = wpqa_subscription_plans();
		foreach ($array as $key => $value) {
			$role_plans[($array_key == "key"?$key:(isset($value[$array_key])?$value[$array_key]:$key))] = (isset($value["plan"]) && $value["plan"] != ""?$value["plan"]:(isset($value["role"])?ucfirst($value["role"]):"")).(isset($value["name"]) && $value["name"] != ""?" - ".$value["name"]:"");
		}
		return $role_plans;
	}
endif;
/* Check the user limit */
function wpqa_check_user_limit($user_id,$user_info,$type) {
	$user_group = wpqa_get_user_group($user_info);
	$roles = wpqa_options("roles");
	$allow_setting_day = (isset($roles[$user_group][$type."s_day"])?$roles[$user_group][$type."s_day"]:0);
	$allow_setting_month = (isset($roles[$user_group][$type."s_month"])?$roles[$user_group][$type."s_month"]:0);
	$date = array("year" => date("Y"),"month" => date("m"));
	$date_day = array_merge($date,array("day" => date("d")));
	$count_day = $count_month = 0;
	if ($type == "question") {
		$count_day = ($allow_setting_day > 0?wpqa_count_posts_by_user($user_id,array(wpqa_questions_type,wpqa_asked_questions_type),"publish",0,$date_day):0);
		$count_month = ($count_month > 0?wpqa_count_posts_by_user($user_id,array(wpqa_questions_type,wpqa_asked_questions_type),"publish",0,$date):0);
	}else if ($type == "answer") {
		$count_day = ($allow_setting_day > 0?wpqa_comments_of_post_type(array(wpqa_questions_type,wpqa_asked_questions_type),$user_id,$date_day):0);
		$count_month = ($count_month > 0?wpqa_comments_of_post_type(array(wpqa_questions_type,wpqa_asked_questions_type),$user_id,$date):0);
	}
	$expired_day = ($allow_setting_day > 0 && $count_day >= $allow_setting_day?"expired":"not");
	$expired_month = ($allow_setting_month > 0 && $count_month >= $allow_setting_month?"expired":"not");
	$return = array(
		"expired" => ($expired_day == "expired" || $expired_month == "expired"?"expired":"not"),
		"expired_day" => $expired_day,
		"expired_month" => $expired_month,
		"limit_day" => $allow_setting_day,
		"limit_month" => $allow_setting_month,
		"count_day" => $count_day,
		"count_month" => $count_month
	);
	return $return;
}
/* Paid subscriptions button */
if (!function_exists('wpqa_paid_subscriptions')) :
	function wpqa_paid_subscriptions($show = '') {
		$subscriptions_payment = wpqa_options("subscriptions_payment");
		if ($subscriptions_payment == "on") {
			$payment_pages_target = wpqa_options("payment_pages_target");
			$payment_pages_target = ($payment_pages_target == "new_page"?"_blank":"_self");
			$out = ($show == true?'<div class="pop-footer pop-footer-subscriptions">':'').'<a class="subscriptions-link" href="'.wpqa_subscriptions_permalink().'" target="'.$payment_pages_target.'">'.esc_html__("Please subscribe to paid membership","wpqa").'<i class="icon-sound"></i></a>'.($show == true?'</div>':'');
			return $out;
		}
	}
endif;
/* Paid subscriptions message */
if (!function_exists('wpqa_paid_subscriptions_message')) :
	function wpqa_paid_subscriptions_message($message,$alert = 'error',$class = '') {
		if ($alert == 'info') {
			$alert = '';
			$icon = 'icon-lamp';
		}else if ($alert == 'warning') {
			$alert = 'alert-message-warning';
			$icon = 'icon-flag';
		}else {
			$alert = 'alert-message-error';
			$icon = 'icon-cancel';
		}
		$out = '<div class="alert-message '.$alert.($class != ""?" ".$class:"").'"><i class="'.$icon.'"></i><p>'.$message.' '.wpqa_paid_subscriptions().'</p></div>';
		return $out;
	}
endif;
/* Check the subscriptions */
add_action("wpqa_init","wpqa_check_get_subscriptions");
function wpqa_check_get_subscriptions() {
	$subscriptions_payment = wpqa_options("subscriptions_payment");
	if ($subscriptions_payment == "on") {
		/* Check the subscription */
		wpqa_check_subscription();
	}
}
/* Check the subscription */
function wpqa_check_subscription() {
	if (is_user_logged_in()) {
		$user_id = get_current_user_id();
		$wpqa_allow_to_pay = get_user_meta($user_id,"wpqa_allow_to_pay",true);
		if (is_array($wpqa_allow_to_pay) && !empty($wpqa_allow_to_pay)) {
			foreach ($wpqa_allow_to_pay as $post_id) {
				if ($post_id > 0) {
					$end_subscribe_time = get_user_meta($user_id,"end_subscribe_time".$post_id,true);
					$package_subscribe = get_user_meta($user_id,"package_subscribe".$post_id,true);
					if ($package_subscribe == "lifetime") {
						$stop_it = false;
					}else if ($end_subscribe_time != "" && $end_subscribe_time < strtotime(date("Y-m-d H:i:s"))) {
						$stop_it = true;
					}
					if (isset($stop_it) && $stop_it == true) {
						if (!is_super_admin($user_id)) {
							$default_group = wpqa_options("default_group");
							$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
							wp_update_user(array('ID' => $user_id,'role' => $default_group));
							do_action("wpqa_end_subscription_time".$post_id,$user_id);
						}
						wpqa_stop_subscription($user_id,$post_id,"");
						delete_user_meta($user_id,"reward_subscribe".$post_id);
						delete_user_meta($user_id,"points_subscribe".$post_id);
					}
				}
			}
		}

		$end_subscribe_time = get_user_meta($user_id,"end_subscribe_time",true);
		$package_subscribe = get_user_meta($user_id,"package_subscribe",true);
		if ($package_subscribe == "lifetime") {
			$stop_it = false;
		}else if ($end_subscribe_time != "" && $end_subscribe_time < strtotime(date("Y-m-d H:i:s"))) {
			$stop_it = true;
		}
		if (isset($stop_it) && $stop_it == true) {
			if (!is_super_admin($user_id)) {
				$default_group = wpqa_options("default_group");
				$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
				wp_update_user(array('ID' => $user_id,'role' => $default_group));
				do_action("wpqa_end_subscription_time",$user_id);
			}
			wpqa_stop_subscription($user_id,0,"");
			delete_user_meta($user_id,"reward_subscribe");
			delete_user_meta($user_id,"points_subscribe");
		}
	}
}
/* Stop the subscription */
function wpqa_stop_subscription($user_id,$post_id = 0,$cancel = "cancel",$wpqa_subscr_id = "",$delete_extra_subscribe = true) {
	$post_id = ($post_id > 0?$post_id:"");
	delete_user_meta($user_id,"end_subscribe_time".$post_id);
	if ($delete_extra_subscribe == true) {
		delete_user_meta($user_id,"start_subscribe_time".$post_id);
	}
	delete_user_meta($user_id,"package_subscribe".$post_id);
	delete_user_meta($user_id,"wpqa_plan_key".$post_id);
	delete_user_meta($user_id,"wpqa_plan_price".$post_id);
	delete_user_meta($user_id,"wpqa_subscribe_module");
	if ($cancel == "cancel") {
		wpqa_cancel_subscription($user_id,$post_id,true,$wpqa_subscr_id);
	}
}
/* Upgrade and downgrade subscription */
function wpqa_upgrade_downgrade_subscription($user_id,$post_id,$new_package,$new_role = "",$stop_subscription = true,$wpqa_subscr_id = "") {
	$post_id = ($post_id > 0?$post_id:"");
	$delete_extra_subscribe = true;
	$old_plan_key = get_user_meta($user_id,"wpqa_plan_key".$post_id,true);

	$user_info = get_userdata($user_id);
	$user_group = wpqa_get_user_group($user_info);

	$new_package = ($new_package != ""?str_replace("-","_",$new_package):"");
	if ($new_role == "") {
		$subscription_plans = wpqa_subscription_plans();
		$new_role = (isset($subscription_plans[$new_package]["role"]) && $subscription_plans[$new_package]["role"] != ""?$subscription_plans[$new_package]["role"]:"");
	}
	if (($user_group == $new_role && $old_plan_key != "")) {
		$end_subscribe_time = get_user_meta($user_id,"end_subscribe_time".$post_id,true);
		if ($end_subscribe_time != "" && $end_subscribe_time >= strtotime(date("Y-m-d H:i:s"))) {
			$delete_extra_subscribe = false;
			update_user_meta($user_id,"extra_subscribe_time".$post_id,$end_subscribe_time);
		}
	}
	if ($delete_extra_subscribe == true && $stop_subscription == true) {
		delete_user_meta($user_id,"extra_subscribe_time".$post_id);
		wpqa_stop_subscription($user_id,$post_id,"cancel",$wpqa_subscr_id,$delete_extra_subscribe);
	}
}
/* Check upgrade and downgrade subscription */
function wpqa_check_upgrade_downgrade_time($user_id,$new_package,$post_id = 0) {
	$post_id = ($post_id > 0?$post_id:"");
	$subscription_plans = wpqa_subscription_plans();
	$old_plan_key = get_user_meta($user_id,"wpqa_plan_key".$post_id,true);
	
	$user_info = get_userdata($user_id);
	$user_group = wpqa_get_user_group($user_info);

	$str_replace = $new_package;
	$new_package = ($new_package != ""?str_replace("-","_",$new_package):"");
	$new_role = (isset($subscription_plans[$new_package]["role"]) && $subscription_plans[$new_package]["role"] != ""?$subscription_plans[$new_package]["role"]:"");
	if ($user_group == $new_role && $old_plan_key != "") {
		$end_subscribe_time = get_user_meta($user_id,"end_subscribe_time".$post_id,true);
		if ($end_subscribe_time != "" && $end_subscribe_time >= strtotime(date("Y-m-d H:i:s"))) {
			$extra_subscribe_time = $end_subscribe_time;
			$interval_count = explode("-",$new_package);
			if (isset($interval_count[0]) && ($interval_count[0] == "monthly" || $interval_count[0] == "3months" || $interval_count[0] == "6months" || $interval_count[0] == "yearly")) {
				$interval_count = (isset($interval_count[0])?$interval_count[0]:$str_replace);
			}else {
				$interval_count = explode("-",$str_replace);
				if (isset($interval_count[1]) && ($interval_count[1] == "monthly" || $interval_count[1] == "3months" || $interval_count[1] == "6months" || $interval_count[1] == "yearly")) {
					$interval_count = (isset($interval_count[1])?$interval_count[1]:$str_replace);
				}
			}
			$interval = ($interval_count == "yearly"?"year":"month")." +7 hour";
			$interval_count = ($interval_count == "monthly" || $interval_count == "yearly"?1:($interval_count == "3months"?3:6));
			$extra_subscribe = strtotime(date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s",$extra_subscribe_time)." +$interval_count $interval")));
			$date = date("Y-m-d H:i:s",$extra_subscribe);

			$stripe = strtotime(date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s",$extra_subscribe)." +$interval_count $interval")));

			$now_date = time();
			$your_date = strtotime($date);
			$datediff = $your_date - $now_date;
			$diff_days = round($datediff / (60 * 60 * 24));
			$main_diff_days = $diff_days;
			$diff_days = (round($diff_days / 30) > 12?round(round($diff_days / 30) / 12):($diff_days > 30?round($diff_days / 30):$diff_days));
			
			$date_format = wpqa_options("date_format");
			$date_format = ($date_format?$date_format:get_option("date_format"));
			$return = array(
				"days" => $main_diff_days,
				"stripe" => $extra_subscribe,
				"stripe_date" => date($date_format,$extra_subscribe),
				"paypal" => $diff_days,
				"paypal_style" => (round($diff_days / 30) > 12?"Y":($main_diff_days > 30?"M":"D"))
			);
		}
	}
	return (isset($return)?$return:array());
}
/* Check the subscription */
function wpqa_check_if_user_subscribe($user_id = 0) {
	$return = false;
	if (is_user_logged_in()) {
		if ($user_id == 0) {
			$user_id = get_current_user_id();
		}
		$subscriptions_payment = wpqa_options("subscriptions_payment");
		if ($subscriptions_payment == "on") {
			$end_subscribe_time = get_user_meta($user_id,"end_subscribe_time",true);
			$package_subscribe = get_user_meta($user_id,"package_subscribe",true);
			if (!is_super_admin($user_id) && $end_subscribe_time != "" && $end_subscribe_time < strtotime(date("Y-m-d H:i:s"))) {
				$return = false;
			}else if (!is_super_admin($user_id) && ($package_subscribe == "lifetime" || ($end_subscribe_time != "" && $end_subscribe_time >= strtotime(date("Y-m-d H:i:s"))))) {
				$return = true;
			}
		}
	}
	return $return;
}
/* Move user to subscription */
function wpqa_move_user_to_subscription($user_id,$package_subscribe,$subscriptions_group = "author",$plan_key = "",$price = "",$customer = "",$subscr_id = "",$trial = "",$reward = "",$reward_type = "") {
	if (is_numeric($plan_key)) {
		$post_id = (int)$plan_key;
		$get_post = get_post($post_id);
		if (isset($get_post->ID)) {
			$payment_kind = get_post_meta($get_post->ID,prefix_meta."payment_kind",true);
			$subscriptions_options = get_post_meta($get_post->ID,prefix_meta."subscriptions_options",true);
			$payment_action = get_post_meta($get_post->ID,prefix_meta."payment_action",true);
			$payment_role = get_post_meta($get_post->ID,prefix_meta."payment_role",true);
			if ($payment_action == "role" && $payment_role != "") {
				$subscriptions_group = $payment_role;
				$get_post_id = $get_post->ID;
			}
		}
	}
	$get_post_id = (isset($get_post_id) && $get_post_id > 0?$get_post_id:"");
	if ($subscriptions_group != "") {
		wp_update_user(array('ID' => $user_id,'role' => $subscriptions_group));
	}
	if ($customer != "") {
		update_user_meta($user_id,"wpqa_paypal_customer".$get_post_id,esc_html($customer));
	}
	if ($plan_key != "") {
		update_user_meta($user_id,"wpqa_plan_key".$get_post_id,$plan_key);
	}
	if ($price != "") {
		update_user_meta($user_id,"wpqa_plan_price".$get_post_id,$price);
	}
	update_user_meta($user_id,"package_subscribe".$get_post_id,$package_subscribe);
	if ($subscr_id != "") {
		update_user_meta($user_id,"wpqa_subscr_id".$get_post_id,esc_html($subscr_id));
	}
	delete_user_meta($user_id,"wpqa_canceled_subscription".$get_post_id);
	if ($package_subscribe == "lifetime") {
		delete_user_meta($user_id,"start_subscribe_time".$get_post_id);
		delete_user_meta($user_id,"end_subscribe_time".$get_post_id);
		delete_user_meta($user_id,"extra_subscribe_time".$get_post_id);
	}else {
		if ($reward != "") {
			update_user_meta($user_id,"reward_subscribe",$package_subscribe);
			$interval = $package_subscribe;
			$interval_count = $reward;
			$reward_type_value = (int)get_user_meta($user_id,"wpqa_reward_".$reward_type."s_".date("m"),true);
			$reward_type_value++;
			update_user_meta($user_id,"wpqa_reward_".$reward_type."s_".date("m"),$reward_type_value);
		}else if ($trial != "") {
			update_user_meta($user_id,"trial_subscribe",$package_subscribe);
			update_user_meta($user_id,"trial_rang",$trial);
			$interval = $package_subscribe;
			$interval_count = $trial;
		}else {
			delete_user_meta($user_id,"trial_subscribe");
			delete_user_meta($user_id,"reward_subscribe");
			delete_user_meta($user_id,"points_subscribe".$get_post_id);
			$interval = ($package_subscribe == "yearly"?"year":"month")." +7 hour";
			$interval_count = ($package_subscribe == "monthly" || $package_subscribe == "yearly"?1:($package_subscribe == "3months"?3:6));
			$user_id_invite = get_user_meta($user_id,"wpqa_invitations",true);
			if ($user_id_invite != "" && $user_id_invite > 0) {
				$referral_membership = wpqa_options("referral_membership");
				wpqa_add_points($user_id_invite,$referral_membership,"+","referral_membership");
				wpqa_notifications_activities($user_id_invite,$user_id,"","","","referral_membership","notifications",$referral_membership." "._n("Point","Points",$referral_membership,"wpqa"));
			}
		}
		$start_subscribe_time = get_user_meta($user_id,"start_subscribe_time".$get_post_id,true);
		$end_subscribe_time = get_user_meta($user_id,"end_subscribe_time".$get_post_id,true);
		$extra_subscribe_time = get_user_meta($user_id,"extra_subscribe_time".$get_post_id,true);
		if ($extra_subscribe_time != "") {
			delete_user_meta($user_id,"extra_subscribe_time".$get_post_id);
		}
		if (($start_subscribe_time != "" && $end_subscribe_time != "") || ($start_subscribe_time != "" && $extra_subscribe_time != "")) {
			update_user_meta($user_id,"end_subscribe_time".$get_post_id,strtotime(date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s",($extra_subscribe_time != ""?$extra_subscribe_time:$end_subscribe_time))." +$interval_count $interval"))));
		}else {
			update_user_meta($user_id,"start_subscribe_time".$get_post_id,strtotime(date("Y-m-d H:i:s")));
			update_user_meta($user_id,"end_subscribe_time".$get_post_id,strtotime(date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +$interval_count $interval"))));
		}
	}
}
/* Free, trial and reward  subscriptions */
function wpqa_free_subscriptions($user_id) {
	if ($user_id > 0 && ((isset($_POST["process"]) && $_POST["process"] == "subscribe" && isset($_POST["package_subscribe"]) && $_POST["package_subscribe"] != "") || (isset($_GET["reward"]) && $_GET["reward"] == "subscription") || (isset($_GET["trial"]) && $_GET["trial"] == "plan"))) {
		if (isset($_GET["reward"]) && $_GET["reward"] == "subscription") {
			$reward_subscription_plan = wpqa_options("reward_subscription_plan");
			$reward_subscription_rang = wpqa_options("reward_subscription_rang");
			$reward_group = wpqa_options("reward_group");
			$item_name = esc_html__("Reward membership","wpqa");
			$subscribe = $reward_subscription_plan;
			$reward_type = (isset($_GET["type"])?esc_html($_GET["type"]):"");
		}else if (isset($_GET["trial"]) && $_GET["trial"] == "plan") {
			$trial_subscription_plan = wpqa_options("trial_subscription_plan");
			$trial_subscription_rang = wpqa_options("trial_subscription_rang");
			$trial_group = wpqa_options("trial_group");
			$item_name = esc_html__("Trial membership","wpqa");
			$subscribe = $trial_subscription_plan;
		}else {
			$package_subscribe = esc_html($_POST["package_subscribe"]);
			$package_replace = ($package_subscribe != ""?str_replace("-","_",$package_subscribe):"");
			$subscription_plans = wpqa_subscription_plans();
			$item_name = (isset($subscription_plans[$package_replace])?(isset($subscription_plans[$package_replace]["plan"]) && $subscription_plans[$package_replace]["plan"] != ""?$subscription_plans[$package_replace]["plan"]:ucfirst($subscription_plans[$package_replace]["role"])).(isset($subscription_plans[$package_replace]["name"]) && $subscription_plans[$package_replace]["name"] != ""?" - ".$subscription_plans[$package_replace]["name"]:""):"");
			$subscribe = $package_subscribe;
		}
		if (isset($_POST["process"]) && $_POST["process"] == "subscribe" && isset($_POST["package_subscribe"]) && $_POST["package_subscribe"] != "") {
			if (isset($_POST["points"]) && $_POST["points"] > 0) {
				$free_subscribe_points = true;
				$package_subscribe_key = $package_subscribe;
				$package_subscribe = ($package_subscribe != ""?str_replace("-","_",$package_subscribe):"");
				$subscription_plans = wpqa_subscription_plans();
				$points_price = (isset($subscription_plans[$package_subscribe]) && isset($subscription_plans[$package_subscribe]["points_price"]) && $subscription_plans[$package_subscribe]["points_price"] > 0?$subscription_plans[$package_subscribe]["points_price"]:0);
				$points_user = (int)get_user_meta($user_id,"points",true);
				if ($points_price > $points_user) {
					wpqa_not_enough_points();
					wp_safe_redirect(esc_url(wpqa_subscriptions_permalink()));
					die();
				}
				update_user_meta($user_id,"wpqa_plan_key",$package_subscribe_key);
				update_user_meta($user_id,"points_subscribe",$package_subscribe);
			}
		}
		$currency_code = wpqa_get_currency($user_id);
		$array = array (
			'item_no' => 'subscribe',
			'item_name' => $item_name,
			'item_price' => 0,
			'item_currency' => $currency_code,
			'first_name' => get_the_author_meta("first_name",$user_id),
			'last_name' => get_the_author_meta("last_name",$user_id),
			'subscribe' => $subscribe,
			'trial' => (isset($trial_subscription_rang)?$trial_subscription_rang:""),
			'trial_group' => (isset($trial_group)?$trial_group:""),
			'reward' => (isset($reward_subscription_rang)?$reward_subscription_rang:""),
			'reward_group' => (isset($reward_group)?$reward_group:""),
			'reward_type' => (isset($reward_type)?$reward_type:""),
			'points' => (isset($_POST["process"]) && $_POST["process"] == "subscribe" && isset($_POST["package_subscribe"]) && $_POST["package_subscribe"] != "" && isset($_POST["points"])?esc_html($_POST["points"]):""),
			'custom' => 'wpqa_subscribe-'.$subscribe,
		);
		wpqa_payment_succeeded($user_id,$array);
		if (isset($_POST["process"]) && $_POST["process"] == "subscribe" && isset($_POST["package_subscribe"]) && $_POST["package_subscribe"] != "" && isset($free_subscribe_points)) {
			if (!isset($points_price) || (isset($points_price) && $points_price <= 0)) {
				$package_subscribe = ($package_subscribe != ""?str_replace("-","_",$package_subscribe):"");
				$subscription_plans = wpqa_subscription_plans();
				$points_price = (isset($subscription_plans[$package_subscribe]) && isset($subscription_plans[$package_subscribe]["points_price"]) && $subscription_plans[$package_subscribe]["points_price"] > 0?$subscription_plans[$package_subscribe]["points_price"]:0);
			}
			wpqa_add_points($user_id,$points_price,"-","subscribe_points");
			$message = esc_html__("You have subscribed to paid membership by points.","wpqa");
		}else {
			$message = esc_html__("You have got a new free membership.","wpqa");
		}
		wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.$message.'</p></div>','wpqa_session');
		wp_safe_redirect(esc_url(wpqa_profile_url($user_id)));
		die();
	}
}
/* Get payment id by subscr_id */
function wpqa_get_payment_id($user_id,$subscr_id,$post_id = 0) {
	$post_id = ($post_id > 0?$post_id:"");
	$args = array(
		'author'         => $user_id,
		'meta_key'       => 'payment_subscr_id',
		'meta_value'     => $subscr_id,
		'post_type'      => 'statement',
		'posts_per_page' => -1
	);
	$query = new WP_Query($args);
	if ($query->have_posts()) {
		$post_id = (isset($query->posts[0]->ID)?$query->posts[0]->ID:0);
		if ($post_id > 0) {
			return $post_id;
		}
	}
}
/* Cancel the subscription */
function wpqa_cancel_subscription($user_id,$post_id = 0,$move_user = true,$subscr_id = "") {
	$post_id = ($post_id > 0?$post_id:"");
	$subscr_id = ($subscr_id != ""?$subscr_id:get_user_meta($user_id,"wpqa_subscr_id".$post_id,true));
	$post_id_statement = wpqa_get_payment_id($user_id,$subscr_id,$post_id);
	if ($post_id_statement > 0) {
		$payment_method = get_post_meta($post_id_statement,"payment_method",true);
		update_post_meta($post_id_statement,"payment_canceled","canceled");
		wpqa_delete_subscription("",$post_id,$move_user,$user_id);
		if ($payment_method == "PayPal") {
			wpqa_change_subscription_status($subscr_id,'Cancel',$post_id_statement);
		}else if ($payment_method == "Stripe") {
			wpqa_delete_paid_subscription($subscr_id);
		}
	}
}
/* Cancel the subscription ajax */
add_action('wp_ajax_wpqa_cancel_subscription_ajax','wpqa_cancel_subscription_ajax');
add_action('wp_ajax_nopriv_wpqa_cancel_subscription_ajax','wpqa_cancel_subscription_ajax');
function wpqa_cancel_subscription_ajax() {
	$user_id = get_current_user_id();
	$post_id = (int)(isset($_POST["post_id"])?$_POST["post_id"]:0);
	$post_id = ($post_id > 0?$post_id:"");
	wpqa_cancel_subscription($user_id,$post_id,false);
	die();
}
/* Delete paid subscription */
function wpqa_delete_paid_subscription($subscr_id) {
	if (strpos($subscr_id,'sub_') !== false) {
		$stripe_test = wpqa_options("stripe_test");
		require_once plugin_dir_path(dirname(__FILE__)).'payments/stripe/init.php';
		try {
			$stripe = new \Stripe\StripeClient(wpqa_options(($stripe_test == "on"?"test_":"").'secret_key'));
			$subscription = $stripe->subscriptions->retrieve($subscr_id);
			if ($subscription->canceled_at == "") {
				$stripe->subscriptions->cancel($subscr_id, []);
			}
		}catch ( \Stripe\Exception\CardException $e ) {
			error_log(print_r($e->getError()->message,true));
		}catch ( Exception $e ) {
			error_log(print_r($e->getMessage(),true));
		}
	}
}
/* Delete trial subscription ajax */
add_action('wp_ajax_wpqa_delete_trial_subscription_ajax','wpqa_delete_trial_subscription_ajax');
add_action('wp_ajax_nopriv_wpqa_delete_trial_subscription_ajax','wpqa_delete_trial_subscription_ajax');
function wpqa_delete_trial_subscription_ajax() {
	$subscription = esc_html($_POST["subscription"]);
	wpqa_delete_paid_subscription($subscription);
	die();
}
/* Delete the subscription */
function wpqa_delete_subscription($subscr_id,$post_id,$move_user = true,$user_id = 0) {
	$post_id = ($post_id > 0?$post_id:"");
	if ($user_id > 0) {
		update_user_meta($user_id,"wpqa_canceled_subscription".$post_id,true);
	}else if ($subscr_id != "") {
		$users = get_users(array("meta_key" => "wpqa_subscr_id".$post_id,"meta_value" => $subscr_id,"number" => 1,"count_total" => false));
		if (isset($users[0]) && isset($users[0]->ID) && $users[0]->ID > 0) {
			$user_id = $users[0]->ID;
			update_user_meta($user_id,"wpqa_canceled_subscription".$post_id,true);
		}
	}
	if (isset($user_id) && $user_id > 0 && $move_user == true) {
		$subscribe_module = get_user_meta($user_id,"wpqa_subscribe_module",true);
		if ($subscribe_module != true) {
			$default_group = wpqa_options("default_group");
			$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
			wp_update_user(array('ID' => $user_id,'role' => $default_group));
		}
	}
}
/* Download button payment */
function wpqa_download_button_payment($post_id,$prefix = null,$payment_canceled = "",$new_line = "") {
	if ($payment_canceled != "canceled") {
		$payment_file = get_post_meta($post_id,($prefix == true?prefix_meta:"")."payment_file",true);
		$file_number = (isset($payment_file["id"]) && $payment_file["id"] != ""?$payment_file["id"]:"");
		if ($file_number != "") {
			$file_url = wp_get_attachment_url($file_number);
		}else if (isset($payment_file["url"]) && $payment_file["url"] != "") {
			$file_url = esc_url($payment_file["url"]);
		}
		if (isset($file_url) && $file_url != "") {
			return ($new_line != ""?$new_line:"").'<a class="button-default btn btn__primary" target="_blank" href="'.$file_url.'">'.esc_html__("Download","wpqa").'</a>';
		}
	}
}
/* Reward subscription */
add_filter("wpqa_action_before_user_stats","wpqa_reward_subscription",1,2);
function wpqa_reward_subscription($return,$user_id) {
	$out = '';
	if (wpqa_is_home_profile()) {
		$package_subscribe = get_user_meta($user_id,"package_subscribe",true);
		$show_reward = apply_filters("wpqa_show_reward",true,$user_id);
		if ($package_subscribe != "lifetime" && $show_reward == true) {
			$subscriptions_payment = wpqa_options("subscriptions_payment");
			$reward_subscription = wpqa_options("reward_subscription");
			if ($subscriptions_payment == "on" && $reward_subscription == "on" && wpqa_is_user_owner()) {
				$date = array("year" => date("Y"),"month" => date("m"));
				$reward_subscription_plan = wpqa_options("reward_subscription_plan");
				$reward_subscription_rang = wpqa_options("reward_subscription_rang");
				if ($reward_subscription_plan == "week") {
					$plan_name = sprintf(_n("%s week","%s weeks",$reward_subscription_rang,"wpqa"),$reward_subscription_rang);
				}else {
					$plan_name = sprintf(_n("%s month","%s months",$reward_subscription_rang,"wpqa"),$reward_subscription_rang);
				}
				if ($plan_name != "") {
					$reward_questions_subscription = wpqa_options("reward_questions_subscription");
					$reward_answers_subscription = wpqa_options("reward_answers_subscription");
					$reward_best_answers_subscription = wpqa_options("reward_best_answers_subscription");
					$reward_posts_subscription = wpqa_options("reward_posts_subscription");

					if ($reward_questions_subscription > 0 || $reward_answers_subscription > 0 || $reward_best_answers_subscription > 0 || $reward_posts_subscription > 0 || isset($request_subscription)) {
						$out .= '<div class="block-section-div subscription-alerts">';
					}
					
					if ($reward_questions_subscription > 0) {
						$add_questions = wpqa_count_posts_by_user($user_id,array(wpqa_questions_type,wpqa_asked_questions_type),"publish",0,$date);
						if ($add_questions >= $reward_questions_subscription) {
							$reward_questions = (int)get_user_meta($user_id,"wpqa_reward_questions_".date("m"),true);
							if ($reward_questions == 0 || ($reward_questions > 0 && ($add_questions - ($reward_questions * $reward_questions_subscription)) >= $reward_questions_subscription)) {
								$reward_type = "question";
								$request_subscription = true;
							}
						}
						$out .= '<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.sprintf(esc_html__("This month you have %s, you need to ask %s to join the %s plan","wpqa"),sprintf(_n("%s question","%s questions",$add_questions,"wpqa"),$add_questions),sprintf(_n("%s question","%s questions",$reward_questions_subscription,"wpqa"),$reward_questions_subscription),$plan_name).(isset($reward_questions) && $reward_questions > 0?sprintf(esc_html__(", you earned it %s"),sprintf(_n("%s time before","%s times before",$reward_questions,"wpqa"),$reward_questions)):"").'.</p></div>';
					}
					if ($reward_answers_subscription > 0) {
						$add_answers = wpqa_comments_of_post_type(array(wpqa_questions_type,wpqa_asked_questions_type),$user_id,$date);
						if (!isset($request_subscription) && $add_answers >= $reward_answers_subscription) {
							$reward_answers = (int)get_user_meta($user_id,"wpqa_reward_answers_".date("m"),true);
							if ($reward_answers == 0 || ($reward_answers > 0 && ($add_answers - ($reward_answers * $reward_answers_subscription)) >= $reward_answers_subscription)) {
								$reward_type = "answer";
								$request_subscription = true;
							}
						}
						$out .= '<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.sprintf(esc_html__("This month you have %s, you need to add %s to join the %s plan","wpqa"),sprintf(_n("%s answer","%s answers",$add_answers,"wpqa"),$add_answers),sprintf(_n("%s answer","%s answers",$reward_answers_subscription,"wpqa"),$reward_answers_subscription),$plan_name).(isset($reward_answers) && $reward_answers > 0?sprintf(esc_html__(", you earned it %s"),sprintf(_n("%s time before","%s times before",$reward_answers,"wpqa"),$reward_answers)):"").'.</p></div>';
					}
					if ($reward_best_answers_subscription > 0) {
						$the_best_answers = wpqa_count_the_comments("best_answer",array($user_id),$date);
						if (!isset($request_subscription) && $the_best_answers >= $reward_best_answers_subscription) {
							$reward_best_answers = (int)get_user_meta($user_id,"wpqa_reward_best_answers_".date("m"),true);
							if ($reward_best_answers == 0 || ($reward_best_answers > 0 && ($the_best_answers - ($reward_best_answers * $reward_best_answers_subscription)) >= $reward_best_answers_subscription)) {
								$reward_type = "best_answer";
								$request_subscription = true;
							}
						}
						$out .= '<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.sprintf(esc_html__("This month you have %s, you need to get %s to join the %s plan","wpqa"),sprintf(_n("%s best answer","%s best answers",$the_best_answers,"wpqa"),$the_best_answers),sprintf(_n("%s best answer","%s best answers",$reward_best_answers_subscription,"wpqa"),$reward_best_answers_subscription),$plan_name).(isset($reward_best_answers) && $reward_best_answers > 0?sprintf(esc_html__(", you earned it %s"),sprintf(_n("%s time before","%s times before",$reward_best_answers,"wpqa"),$reward_best_answers)):"").'.</p></div>';
					}
					if ($reward_posts_subscription > 0) {
						$add_posts = wpqa_count_posts_by_user($user_id,"post","publish",0,$date);
						if (!isset($request_subscription) && $add_posts >= $reward_posts_subscription) {
							$reward_posts = (int)get_user_meta($user_id,"wpqa_reward_posts_".date("m"),true);
							if ($reward_posts == 0 || ($reward_posts > 0 && ($add_posts - ($reward_posts * $reward_posts_subscription)) >= $reward_posts_subscription)) {
								$reward_type = "post";
								$request_subscription = true;
							}
						}
						$out .= '<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.sprintf(esc_html__("This month you have %s, you need to add %s to join the %s plan","wpqa"),sprintf(_n("%s post","%s posts",$add_posts,"wpqa"),$add_posts),sprintf(_n("%s post","%s posts",$reward_posts_subscription,"wpqa"),$reward_posts_subscription),$plan_name).(isset($reward_posts) && $reward_posts > 0?sprintf(esc_html__(", you earned it %s"),sprintf(_n("%s time before","%s times before",$reward_posts,"wpqa"),$reward_posts)):"").'.</p></div>';
					}
					if (isset($request_subscription)) {
						if (is_super_admin($user_id)) {
							$out .= '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("You are admin, so you can't subscribe.","wpqa").'</p></div>';
						}else {
							$out .= '<p class="reward-subscription"><a class="button-default-3 btn btn__primary mb-3'.'" href="'.esc_url_raw(add_query_arg(array('reward' => 'subscription','type' => $reward_type),wpqa_subscriptions_permalink())).'">'.esc_html__("Request your paid membership","wpqa").'</a></p>';
						}
					}

					if ($reward_questions_subscription > 0 || $reward_answers_subscription > 0 || $reward_best_answers_subscription > 0 || $reward_posts_subscription > 0 || isset($request_subscription)) {
						$out .= '</div>';
					}
				}
			}
		}
	}
	return $out;
}?>