<?php

/* @author    2codeThemes
*  @package   WPQA/payments
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Get the redirect link */
function wpqa_get_redirect_link($custom,$item_no,$user_id) {
	$str_replace = str_replace("wpqa_".$item_no."-","",$custom);
	if (strpos($custom,'wpqa_pay-') !== false) {
		$uniqid_cookie = wpqa_options('uniqid_cookie');
		$return_url = (isset($_COOKIE[$uniqid_cookie.'wpqa_pay_'.$str_replace]) && $_COOKIE[$uniqid_cookie.'wpqa_pay_'.$str_replace] != ""?$_COOKIE[$uniqid_cookie.'wpqa_pay_'.$str_replace]:home_url('/'));
		$download_button = wpqa_download_button_payment($str_replace);
		$redirect_to = ($download_button != ""?esc_url(wpqa_transactions_permalink()):esc_url($return_url));
	}else if (strpos($custom,'wpqa_pay_answer-') !== false) {
		$redirect_to = esc_url(get_the_permalink($str_replace));
	}else if (strpos($custom,'wpqa_pay_sticky-') !== false) {
		$redirect_to = esc_url(get_the_permalink($str_replace));
	}else if (strpos($custom,'wpqa_buy_points-') !== false) {
		$redirect_to = esc_url(wpqa_get_profile_permalink($user_id,"points"));
	}else if (strpos($custom,'wpqa_subscribe-') !== false) {
		$redirect_to = esc_url(wpqa_profile_url($user_id));
	}else if (strpos($custom,'wpqa_ask_question-') !== false) {
		if (is_numeric($str_replace)) {
			$redirect_to = esc_url(wpqa_add_question_permalink("user",$str_replace));
		}else {
			$redirect_to = esc_url(wpqa_add_question_permalink());
		}
	}else if (strpos($custom,'wpqa_buy_questions-') !== false) {
		$explode = explode("-",$str_replace);
		if (isset($explode[1]) && $explode[1] != "") {
			$payment_asked = $explode[1];
		}
		if (isset($payment_asked) && is_numeric($payment_asked)) {
			$redirect_to = esc_url(wpqa_add_question_permalink("user",$payment_asked));
		}else {
			$redirect_to = esc_url(wpqa_add_question_permalink());
		}
	}else if (strpos($custom,'wpqa_add_group-') !== false || strpos($custom,'wpqa_buy_groups-') !== false) {
		$redirect_to = esc_url(wpqa_add_group_permalink());
	}else if (strpos($custom,'wpqa_add_post-') !== false || strpos($custom,'wpqa_buy_posts-') !== false) {
		$redirect_to = esc_url(wpqa_add_post_permalink());
	}else {
		$redirect_to = esc_url(home_url('/'));
	}
	$redirect_to = apply_filters("wpqa_payment_redirect_to",$redirect_to,$custom,$item_no,$user_id);
	return $redirect_to;
}
/* Get the success meesage */
function wpqa_get_payment_success($custom,$item_transaction = "",$session = false) {
	if (strpos($custom,'wpqa_pay-') !== false) {
		$payment_success = esc_html__("Thank you for your payment","wpqa");
	}else if (strpos($custom,'wpqa_pay_answer-') !== false) {
		$payment_success = esc_html__("Thank you for your payment. You can now add a new answer","wpqa");
	}else if (strpos($custom,'wpqa_pay_sticky-') !== false) {
		$payment_success = esc_html__('Thank you for your payment. Your question now is "sticky"','wpqa');
	}else if (strpos($custom,'wpqa_buy_points-') !== false) {
		$payment_success = esc_html__("Thank you for your payment. Your points has been added","wpqa");
	}else if (strpos($custom,'wpqa_subscribe-') !== false) {
		$payment_success = esc_html__("Thank you for your payment. Your membership has been upgraded","wpqa");
	}else if (strpos($custom,'wpqa_ask_question-') !== false || strpos($custom,'wpqa_buy_questions-') !== false) {
		$payment_success = esc_html__("Thank you for your payment. You can now ask a new question","wpqa");
	}else if (strpos($custom,'wpqa_add_group-') !== false || strpos($custom,'wpqa_buy_groups-') !== false) {
		$payment_success = esc_html__("Thank you for your payment. You can now create a new group","wpqa");
	}else if (strpos($custom,'wpqa_add_post-') !== false || strpos($custom,'wpqa_buy_posts-') !== false) {
		$payment_success = esc_html__("Thank you for your payment. You can now add a new post","wpqa");
	}
	if (isset($payment_success)) {
		if ($session = true) {
			$return = $payment_success.($item_transaction != ""?", ".sprintf(esc_html__("Your transaction id %s.","wpqa"),$item_transaction):"");
		}else {
			$return = $payment_success;
		}
	}
	return apply_filters("wpqa_payment_success",(isset($return)?$return:""),$custom,$item_transaction,$session);
}
/* Get currency */
function wpqa_get_currency($user_id = 0) {
	$currency_code = wpqa_options("currency_code");
	$activate_currencies = wpqa_options("activate_currencies");
	if ($user_id > 0) {
		$wpqa_currency_code = get_user_meta($user_id,"wpqa_currency_code",true);
	}
	$currency_code = ($user_id > 0 && $activate_currencies == "on" && isset($wpqa_currency_code) && $wpqa_currency_code != ""?$wpqa_currency_code:$currency_code);
	$currency_code = (isset($_POST['multi_currencies']) && $_POST['multi_currencies'] != ""?esc_html($_POST['multi_currencies']):$currency_code);
	$currency_code = (isset($currency_code) && $currency_code != ""?$currency_code:"USD");
	return $currency_code;
}
/* Get item name */
function wpqa_get_item_name($item_number,$custom) {
	$str_replace = str_replace("wpqa_".$item_number."-","",$custom);
	$exclude = explode("-",$str_replace);
	$exclude_1 = (isset($exclude[1]) && $exclude[1] != ""?$exclude[1]:$exclude[0]);
	$exclude_2 = (isset($exclude[1]) && $exclude[1] != ""?$exclude[0]:"");
	if ($item_number == "pay") {
		$item_number = "pay";
	}else if ($item_number == "pay_answer") {
		$item_number = "answer";
	}else if ($item_number == "pay_sticky") {
		$item_number = "sticky";
	}else if ($item_number == "ask_packages") {
		$item_number = "buy_questions";
	}else if ($item_number == "group_packages") {
		$item_number = "buy_groups";
	}else if ($item_number == "post_packages") {
		$item_number = "buy_posts";
	}
	return array("checkout_value" => $item_number,"checkout_item" => $exclude_1,"checkout_related" => $exclude_2);
}
/* Get item price */
function wpqa_get_item_price($checkout_value,$checkout_item,$checkout_related) {
	if ($checkout_value != "") {
		$user_id = get_current_user_id();
		$activate_currencies = wpqa_options("activate_currencies");
		$multi_currencies = wpqa_options("multi_currencies");
		$currency_code = wpqa_get_currency($user_id);
		$currency = ($activate_currencies == "on"?"_".strtolower($currency_code):"");
		$payment_option = "";
		if ($checkout_value == "pay") {
			if ($checkout_item != "") {
				$get_post = get_post($checkout_item);
				if (isset($get_post->ID)) {
					$payment_kind = get_post_meta($get_post->ID,prefix_meta."payment_kind",true);
					$subscriptions_options = get_post_meta($get_post->ID,prefix_meta."subscriptions_options",true);
					$price = get_post_meta($get_post->ID,prefix_meta.($payment_kind == "payment"?"payment_price":"subscribe_".$subscriptions_options).$currency,true);
				}
			}
		}else if ($checkout_value == "answer") {
			if ($checkout_item != "") {
				$get_post = get_post($checkout_item);
				if (isset($get_post->ID)) {
					$post_id = $get_post->ID;
					$pay_answer = wpqa_options("pay_answer");
					$custom_pay_answer = get_post_meta($post_id,"custom_pay_answer",true);
					if ($custom_pay_answer == "on") {
						$pay_answer = get_post_meta($post_id,"pay_answer",true);
					}
					$pay_answer = apply_filters('wpqa_pay_answer',$pay_answer);
					if ($pay_answer == "on") {
						$post_author = $get_post->post_author;
						$payment_option = "pay_answer_payment".$currency;
						if ($custom_pay_answer == "on") {
							$price = get_post_meta($post_id,$payment_option,true);
						}
						$price = apply_filters("wpqa_answer_price",(isset($price)?$price:0));
					}
				}
			}
		}else if ($checkout_value == "sticky") {
			if ($checkout_item != "") {
				$get_post = get_post($checkout_item);
				if (isset($get_post->ID)) {
					$payment_option = "pay_sticky_payment".$currency;
				}
			}
		}else if ($checkout_value == "buy_points") {
			$active_points = wpqa_options("active_points");
			$buy_points_payment = wpqa_options("buy_points_payment");
			if ($active_points == "on" && $buy_points_payment == "on") {
				$buy_points = wpqa_options("buy_points");
				if (isset($buy_points) && is_array($buy_points)) {
					$buy_points = array_values($buy_points);
					$found_key = trim(array_search($checkout_item,array_column($buy_points,'package_points')));
					if ($found_key !== "" && isset($buy_points[$found_key]) && is_array($buy_points[$found_key]) && !empty($buy_points[$found_key])) {
						$price = (isset($buy_points[$found_key]["package_price".$currency])?$buy_points[$found_key]["package_price".$currency]:0);
					}
				}
			}
		}else if ($checkout_value == "subscribe") {
			$subscriptions_payment = wpqa_options("subscriptions_payment");
			if ($subscriptions_payment == "on") {
				$payment_type_subscriptions = wpqa_options("payment_type_subscriptions");
				$array = wpqa_subscription_plans($currency,$payment_type_subscriptions);
				$subscriptions_key = $checkout_related."_".$checkout_item;
				$package_subscribe = get_user_meta($user_id,"package_subscribe",true);
				if (($package_subscribe == "" || ($package_subscribe != "" && isset($array[$subscriptions_key]) && isset($array[$subscriptions_key]["type"]) && $subscriptions_key != $package_subscribe)) && isset($array[$subscriptions_key]) && isset($array[$subscriptions_key]["type"]) && isset($array[$subscriptions_key]["key"]) && $subscriptions_key == $array[$subscriptions_key]["key"]."_".$array[$subscriptions_key]["type"]) {
					$price = (isset($array[$subscriptions_key]["price"])?$array[$subscriptions_key]["price"]:0);
				}
			}
		}else if ($checkout_value == "ask_question") {
			$payment_style = wpqa_options("ask_payment_style");
			if ($payment_style != "packages" && ($checkout_item == "" || ($checkout_item != "" && is_numeric($checkout_item) && $checkout_item != $user_id))) {
				$allow_to_continue = true;
				$payment_option = "pay_ask_payment".$currency;
			}
		}else if ($checkout_value == "buy_questions") {
			$payment_style = wpqa_options("ask_payment_style");
			if ($payment_style == "packages") {
				$packages_payment = wpqa_options("ask_packages");
				if (isset($packages_payment) && is_array($packages_payment)) {
					$packages_payment = array_values($packages_payment);
					$found_key = trim(array_search($checkout_item,array_column($packages_payment,'package_posts')));
					if ($found_key !== "" && isset($packages_payment[$found_key]) && is_array($packages_payment[$found_key]) && !empty($packages_payment[$found_key])) {
						$price = $packages_payment[$found_key]["package_price".$currency];
					}
				}
			}
		}else if ($checkout_value == "add_group") {
			$payment_style = wpqa_options("group_payment_style");
			if ($payment_style != "packages") {
				$payment_option = "pay_group_payment".$currency;
			}
		}else if ($checkout_value == "buy_groups") {
			$payment_style = wpqa_options("group_payment_style");
			if ($payment_style == "packages") {
				$packages_payment = wpqa_options("group_packages");
				if (isset($packages_payment) && is_array($packages_payment)) {
					$packages_payment = array_values($packages_payment);
					$found_key = trim(array_search($checkout_item,array_column($packages_payment,'package_posts')));
					if ($found_key !== "" && isset($packages_payment[$found_key]) && is_array($packages_payment[$found_key]) && !empty($packages_payment[$found_key])) {
						$price = $packages_payment[$found_key]["package_price".$currency];
					}
				}
			}
		}else if ($checkout_value == "add_post") {
			$payment_style = wpqa_options("post_payment_style");
			if ($payment_style != "packages") {
				$payment_option = "pay_post_payment".$currency;
			}
		}else if ($checkout_value == "buy_posts") {
			$payment_style = wpqa_options("post_payment_style");
			if ($payment_style == "packages") {
				$packages_payment = wpqa_options("post_packages");
				if (isset($packages_payment) && is_array($packages_payment)) {
					$packages_payment = array_values($packages_payment);
					$found_key = trim(array_search($checkout_item,array_column($packages_payment,'package_posts')));
					if ($found_key !== "" && isset($packages_payment[$found_key]) && is_array($packages_payment[$found_key]) && !empty($packages_payment[$found_key])) {
						$price = $packages_payment[$found_key]["package_price".$currency];
					}
				}
			}
		}
		$price = floatval(isset($price) && $price > 0?$price:(isset($payment_option)?wpqa_options($payment_option):0));
		return apply_filters("wpqa_checkout_price",$price,$checkout_value);
	}
}
/* Site and user money */
function wpqa_site_user_money($price,$relation = "+",$currency = "USD",$user_id = 0) {
	if ($user_id > 0) {
		/* Money user spent */
		$_all_my_payment = get_user_meta($user_id,$user_id."_all_my_payment_".$currency,true);
		if ($_all_my_payment == "" || !$_all_my_payment || $_all_my_payment == 0 || $_all_my_payment < 0) {
			$_all_my_payment = 0;
		}
		$spent = ($relation == "-"?($_all_my_payment-$price > 0?$_all_my_payment-$price:0):($_all_my_payment+$price));
		update_user_meta($user_id,$user_id."_all_my_payment_".$currency,$spent);
	}

	/* All money */
	$all_money = get_option("all_money_".$currency);
	if ($all_money == "" || !$all_money || $all_money == 0 || $all_money < 0) {
		$all_money = 0;
	}
	$money = ($relation == "-"?($all_money-$price > 0?$all_money-$price:0):($all_money+$price));
	update_option("all_money_".$currency,$money);

	/* The currency */
	$the_currency = get_option("the_currency");
	if (is_string($the_currency) || (is_array($the_currency) && empty($the_currency))) {
		delete_option("the_currency");
		update_option("the_currency",array($currency));
		$the_currency = get_option("the_currency");
	}
	$the_currency = (is_array($the_currency)?$the_currency:array());
	if (!in_array($currency,$the_currency)) {
		array_push($the_currency,$currency);
	}
	update_option("the_currency",$the_currency);
}
/* Payment succeed */
function wpqa_payment_succeeded($user_id,$response) {
	$item_transaction = get_user_meta($user_id,"wpqa_item_transaction",true);
	$payment_item_transaction = (isset($response['txn_id'])?esc_html($response['txn_id']):(isset($response['item_transaction'])?esc_html($response['item_transaction']):""));
	if (($payment_item_transaction != "" && $item_transaction != $payment_item_transaction) || (isset($_POST["process"]) && $_POST["process"] == "subscribe" && isset($_POST["package_subscribe"]) && $_POST["package_subscribe"] != "") || (isset($response['reward']) && $response['reward'] != "") || (isset($response['trial']) && $response['trial'] != "")) {
		if ($payment_item_transaction != "") {
			$args = array(
				's'              => $payment_item_transaction,
				'post_type'      => 'statement',
				'posts_per_page' => -1,
				'date_query'     => array(array('after' => '10 minute ago'))
			);
			$query = new WP_Query($args);
			if ($query->have_posts()) {
				$have_posts = true;
			}
		}
		if (!isset($have_posts)) {
			if (isset($response["free"]) || (!isset($response["renew"]) && !isset($response["free"]))) {
				$user_id = apply_filters("wpqa_payment_succeeded_user_id",$user_id,$response);
				/* Coupon */
				$_coupon = get_user_meta($user_id,$user_id."_coupon",true);
				$_coupon_value = get_user_meta($user_id,$user_id."_coupon_value",true);
			}

			if (!isset($response["renew"])) {
				delete_user_meta($user_id,$user_id."_coupon",true);
				delete_user_meta($user_id,$user_id."_coupon_value",true);
			}

			$custom = esc_html(isset($response['custom'])?$response['custom']:'');
			$str_replace = str_replace("wpqa_".$response['item_no']."-","",$custom);

			/* All the payments */
			update_user_meta($user_id,"wpqa_paypal_last_transaction",$payment_item_transaction);
			if (isset($response['payment']) && $response['payment'] != "") {
				update_user_meta($user_id,"wpqa_payment_method",$response['payment']);
			}

			/* Insert a new payment */
			wpqa_insert_new_payment($response,$user_id,(isset($_coupon)?$_coupon:""),(isset($_coupon_value)?$_coupon_value:""));

			if ($response['item_no'] != "pay_sticky") {
				if ($payment_item_transaction != "") {
					update_user_meta($user_id,"wpqa_item_transaction",$payment_item_transaction);
				}
				if (isset($response['sandbox']) && $response['sandbox'] == "on") {
					update_user_meta($user_id,"paypal_sandbox","sandbox");
				}
			}
			$payer_email = (isset($response['payer_email'])?$response['payer_email']:get_the_author_meta('user_email',$user_id));
			$first_name = (isset($response['first_name'])?$response['first_name']:get_the_author_meta('display_name',$user_id));
			$last_name = (isset($response['last_name'])?$response['last_name']:'');
			do_action("wpqa_successfull_payment",$user_id);
			$payment_success = wpqa_get_payment_success($custom,$payment_item_transaction);
			$show_payment_success = apply_filters("wpqa_show_payment_success",true,$response['item_no'],$payment_success);
			if (!isset($response["renew"]) && $payment_success != "" && $show_payment_success == true) {
				if (isset($response['payment']) && $response['payment'] == "PayPal") {
					echo '<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.$payment_success.'.</p></div>';
				}
				wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.$payment_success.'</p></div>','wpqa_session');
			}
			$another_way_payment_filter = apply_filters("wpqa_another_way_payment_filter",true,array("user_id" => $user_id,"item_transaction" => $payment_item_transaction,"item_price" => $response['item_price'],"item_name" => $response['item_no'],"item_currency" => $response['item_currency'],"payer_email" => $payer_email,"first_name" => $first_name,"last_name" => $last_name,"custom" => $custom,"str_replace" => $str_replace,"customer" => (isset($response['customer'])?$response['customer']:""),"subscr_id" => (isset($response['subscr_id'])?$response['subscr_id']:""),"payment_success" => (isset($payment_success)?$payment_success:"")));
			if ($another_way_payment_filter == true) {
				wpqa_after_paid_service($response,$user_id,$custom);

				if ($custom != "") {
					$explode = explode("-",$custom);
					if (isset($explode[0])) {
						do_action($explode[0],$user_id);
					}
				}

				if ((isset($response['reward']) && $response['reward'] != '') || (isset($response['trial']) && $response['trial'] != '') || (isset($response['points']) && $response['points'] != "")) {
					// Trial, reward or with points
				}else {
					$send_text = wpqa_send_mail(
						array(
							'content'          => wpqa_options("email_new_payment"),
							'item_price'       => (isset($_coupon_value) && $_coupon_value > 0?$_coupon_value:$response['item_price']),
							'item_name'        => $response['item_name'],
							'item_currency'    => $response['item_currency'],
							'payer_email'      => $payer_email,
							'first_name'       => $first_name,
							'last_name'        => $last_name,
							'item_transaction' => $payment_item_transaction,
							'date'             => date('m/d/Y'),
							'time'             => date('g:i A'),
						)
					);
					$email_title = wpqa_options("title_new_payment");
					$email_title = ($email_title != ""?$email_title:esc_html__("Instant Payment Notification - Received Payment","wpqa"));
					$email_title = wpqa_send_mail(array(
							'content'          => $email_title,
							'title'            => true,
							'break'            => '',
							'item_price'       => (isset($_coupon_value) && $_coupon_value > 0?$_coupon_value:$response['item_price']),
							'item_name'        => $response['item_name'],
							'item_currency'    => $response['item_currency'],
							'payer_email'      => $payer_email,
							'first_name'       => $first_name,
							'last_name'        => $last_name,
							'item_transaction' => $payment_item_transaction,
							'date'             => date('m/d/Y'),
							'time'             => date('g:i A'),
						)
					);
					wpqa_send_mails(
						array(
							'title'   => $email_title,
							'message' => $send_text,
						)
					);
					$email_template = wpqa_options("email_template");
					if ($payer_email != $email_template) {
						$unsubscribe_mails = get_the_author_meta('unsubscribe_mails',$user_id);
						$new_payment_mail = get_the_author_meta('new_payment_mail',$user_id);
						if ($unsubscribe_mails != "on" && $new_payment_mail == "on") {
							wpqa_send_mails(
								array(
									'toEmail'     => $payer_email,
									'toEmailName' => $first_name,
									'title'       => $email_title,
									'message'     => $send_text,
								)
							);
						}
					}
				}
			}
		}
	}
}
/* Insert new payment  */
function wpqa_insert_new_payment($response,$user_id,$_coupon = "",$_coupon_value = "") {
	/* New */
	$new_payments = get_option("new_payments");
	if ($new_payments == "" || !$new_payments) {
		$new_payments = 0;
	}
	$new_payments++;
	update_option('new_payments',$new_payments);

	/* Money user spent and site money */
	wpqa_site_user_money($response['item_price'],"+",$response['item_currency'],$user_id);

	/* Insert a new payment */
	wpqa_insert_statement($response,$user_id,($_coupon != ""?$_coupon:""),($_coupon_value != ""?$_coupon_value:""));
}
/* After paid service */
function wpqa_after_paid_service($response,$user_id,$custom) {
	$item_no = $response['item_no'];
	$price = (isset($response['item_price']) && $response['item_price'] > 0?$response['item_price']:"");
	$customer = (isset($response['customer'])?$response['customer']:"");
	$subscr_id = (isset($response['subscr_id'])?$response['subscr_id']:"");
	$str_replace = str_replace("wpqa_".$item_no."-","",$custom);
	if (strpos($custom,'wpqa_pay-') !== false) {
		wpqa_paid_checkout($user_id,$str_replace,$price,$customer,$subscr_id,(isset($response['payment']) && $response['payment'])?$response['payment']:'');
	}else if (strpos($custom,'wpqa_pay_answer-') !== false) {
		/* Number allow to add answer */
		$_allow_to_answer = (int)get_user_meta($user_id,$user_id."_allow_to_answer",true);
		if ($_allow_to_answer == "" || $_allow_to_answer < 0) {
			$_allow_to_answer = 0;
		}
		$_allow_to_answer++;
		update_user_meta($user_id,$user_id."_allow_to_answer",$_allow_to_answer);
		update_user_meta($user_id,"pay_to_answer_".$str_replace,"paid");
	}else if (strpos($custom,'wpqa_pay_sticky-') !== false) {
		update_post_meta($str_replace,"sticky",1);
		$sticky_questions = get_option('sticky_questions');
		if (is_array($sticky_questions)) {
			if (!in_array($str_replace,$sticky_questions)) {
				$array_merge = array_merge($sticky_questions,array($str_replace));
				update_option("sticky_questions",$array_merge);
			}
		}else {
			update_option("sticky_questions",array($str_replace));
		}
		$sticky_posts = get_option('sticky_posts');
		if (is_array($sticky_posts)) {
			if (!in_array($str_replace,$sticky_posts)) {
				$array_merge = array_merge($sticky_posts,array($str_replace));
				update_option("sticky_posts",$array_merge);
			}
		}else {
			update_option("sticky_posts",array($str_replace));
		}
		$days_sticky = (int)wpqa_options("days_sticky");
		$days_sticky = ($days_sticky > 0?$days_sticky:7);
		update_post_meta($str_replace,"start_sticky_time",strtotime(date("Y-m-d")));
		update_post_meta($str_replace,"end_sticky_time",strtotime(date("Y-m-d",strtotime(date("Y-m-d")." +$days_sticky days"))));
	}else if (strpos($custom,'wpqa_buy_points-') !== false) {
		wpqa_add_points($user_id,$str_replace,"+","buy_points");
	}else if (strpos($custom,'wpqa_subscribe-') !== false) {
		$explode = explode("-",$str_replace);
		$subscription_plans = wpqa_subscription_plans();
		if (isset($explode[1]) && $explode[1] != "" && isset($explode[2]) && $explode[2] != "") {
			$subscriptions_paid_group = (isset($subscription_plans[$explode[1]."_".$explode[2]]) && isset($subscription_plans[$explode[1]."_".$explode[2]]["role"])?$subscription_plans[$explode[1]."_".$explode[2]]["role"]:"");
		}else if (isset($explode[0]) && $explode[0] != "" && isset($explode[1]) && $explode[1] != "") {
			$subscriptions_paid_group = (isset($subscription_plans[$explode[0]."_".$explode[1]]) && isset($subscription_plans[$explode[0]."_".$explode[1]]["role"])?$subscription_plans[$explode[0]."_".$explode[1]]["role"]:"");
		}else if (isset($explode[0]) && $explode[0] != "" && (!isset($explode[1]) || (isset($explode[1]) && $explode[1] == ""))) {
			$subscriptions_paid_group = (isset($subscription_plans[$explode[0]]) && isset($subscription_plans[$explode[0]]["role"])?$subscription_plans[$explode[0]]["role"]:"");
		}
		if (isset($response['trial_group']) && $response['trial_group'] != "") {
			$subscriptions_group = $response['trial_group'];
		}else if (isset($response['reward_group']) && $response['reward_group'] != "") {
			$subscriptions_group = $response['reward_group'];
		}else {
			$subscriptions_group = (isset($subscriptions_paid_group) && $subscriptions_paid_group != ""?$subscriptions_paid_group:"");
		}
		$package_subscribe = (isset($explode[2])?$explode[2]:(isset($explode[1])?$explode[1]:$str_replace));
		if (isset($response['payment']) && $response['payment'] == "PayPal") {
			wpqa_upgrade_downgrade_subscription($user_id,$str_replace,$package_subscribe,"");
		}
		$plan_key = $str_replace;
		$trial = (isset($response['trial'])?$response['trial']:"");
		$reward = (isset($response['reward'])?$response['reward']:"");
		$reward_type = (isset($response['reward_type'])?$response['reward_type']:"");
		wpqa_move_user_to_subscription($user_id,$package_subscribe,$subscriptions_group,$plan_key,$price,$customer,$subscr_id,$trial,$reward,$reward_type);
	}else if (strpos($custom,'wpqa_ask_question-') !== false || strpos($custom,'wpqa_buy_questions-') !== false) {
		/* Number allow to ask question */
		$_allow_to_ask = (int)get_user_meta($user_id,$user_id."_allow_to_ask",true);
		if ($_allow_to_ask == "" || $_allow_to_ask < 0) {
			$_allow_to_ask = 0;
		}
		if (strpos($custom,'wpqa_buy_questions-') !== false) {
			$explode = explode("-",$str_replace);
			if (isset($explode[0]) && $explode[0] != "") {
				$number_of_questions = $explode[0];
			}
			$_allow_to_ask = $_allow_to_ask+$number_of_questions;
			wpqa_update_sticky_numbers("ask_packages",$user_id,$number_of_questions);
		}else {
			$_allow_to_ask++;
		}
		update_user_meta($user_id,$user_id."_allow_to_ask",$_allow_to_ask);
	}else if (strpos($custom,'wpqa_add_group-') !== false || strpos($custom,'wpqa_buy_groups-') !== false) {
		/* Number allow to add group */
		$_allow_to_group = (int)get_user_meta($user_id,"_allow_to_group",true);
		if ($_allow_to_group == "" || $_allow_to_group < 0) {
			$_allow_to_group = 0;
		}
		if (strpos($custom,'wpqa_buy_groups-') !== false) {
			$number_of_groups = $str_replace;
			$_allow_to_group = $_allow_to_group+$number_of_groups;
		}else {
			$_allow_to_group++;
		}
		update_user_meta($user_id,"_allow_to_group",$_allow_to_group);
	}else if (strpos($custom,'wpqa_add_post-') !== false || strpos($custom,'wpqa_buy_posts-') !== false) {
		/* Number allow to add post */
		$_allow_to_post = (int)get_user_meta($user_id,$user_id."_allow_to_post",true);
		if ($_allow_to_post == "" || $_allow_to_post < 0) {
			$_allow_to_post = 0;
		}
		if (strpos($custom,'wpqa_buy_posts-') !== false) {
			$number_of_posts = $str_replace;
			$_allow_to_post = $_allow_to_post+$number_of_posts;
			wpqa_update_sticky_numbers("post_packages",$user_id,$number_of_posts);
		}else {
			$_allow_to_post++;
		}
		update_user_meta($user_id,$user_id."_allow_to_post",$_allow_to_post);
	}
}
/* Paid the checkout */
function wpqa_get_pay_subscribe_details($str_replace) {
	$get_post = get_post($str_replace);
	if (isset($get_post->ID)) {
		$post_id = $get_post->ID;
		$payment_description = $get_post->post_title;
		$payment_kind = get_post_meta($post_id,prefix_meta."payment_kind",true);
		$subscriptions_options = get_post_meta($post_id,prefix_meta."subscriptions_options",true);
		$payment_action = get_post_meta($get_post->ID,prefix_meta."payment_action",true);
		$payment_role = get_post_meta($post_id,prefix_meta."payment_role",true);
		$payment_file = get_post_meta($post_id,prefix_meta."payment_file",true);
	}
	$array = array(
		"payment_kind" => (isset($payment_kind)?$payment_kind:""),
		"payment_description" => (isset($payment_description)?$payment_description:""),
		"subscriptions_options" => (isset($subscriptions_options)?$subscriptions_options:""),
		"payment_action" => (isset($payment_action)?$payment_action:""),
		"payment_role" => (isset($payment_role)?$payment_role:""),
		"payment_file" => (isset($payment_file)?$payment_file:""),
	);
	return $array;
}
/* Paid the checkout */
function wpqa_paid_checkout($user_id,$post_id,$price,$customer,$subscr_id,$payment_type) {
	$allow_to_pay_user = get_user_meta($user_id,"wpqa_allow_to_pay",true);
	if (empty($allow_to_pay_user)) {
		update_user_meta($user_id,"wpqa_allow_to_pay",array($post_id));
	}else if (is_array($allow_to_pay_user) && !in_array($post_id,$allow_to_pay_user)) {
		update_user_meta($user_id,"wpqa_allow_to_pay",array_merge($allow_to_pay_user,array($post_id)));
	}

	$allow_to_pay_post = get_post_meta($post_id,"wpqa_allow_to_pay",true);
	if (empty($allow_to_pay_post)) {
		update_post_meta($post_id,"wpqa_allow_to_pay",array($user_id));
	}else if (is_array($allow_to_pay_post) && !in_array($user_id,$allow_to_pay_post)) {
		update_post_meta($post_id,"wpqa_allow_to_pay",array_merge($allow_to_pay_post,array($user_id)));
	}

	$wpqa_number_payments = (int)get_post_meta($post_id,"wpqa_number_payments",true);
	if ($wpqa_number_payments == "" || $wpqa_number_payments < 0) {
		$wpqa_number_payments = 0;
	}
	$wpqa_number_payments++;
	update_post_meta($post_id,"wpqa_number_payments",$wpqa_number_payments);

	$array = wpqa_get_pay_subscribe_details($post_id);
	$payment_kind = $array["payment_kind"];
	$payment_action = $array["payment_action"];
	$package_subscribe = $array["subscriptions_options"];
	$subscriptions_group = $array["payment_role"];
	if ($payment_action == "role" && $payment_kind == "payment") {
		wp_update_user(array('ID' => $user_id,'role' => $subscriptions_group));
	}
	if ($payment_kind == "subscribe") {
		if ($payment_type == "PayPal") {
			wpqa_upgrade_downgrade_subscription($user_id,$post_id,$package_subscribe,false,$subscriptions_group);
		}
		wpqa_move_user_to_subscription($user_id,$package_subscribe,($payment_action == "role"?$subscriptions_group:""),$post_id,$price,$customer,$subscr_id);
	}
}
/* Coupon valid */
if (!function_exists('wpqa_coupon_valid')) :
	function wpqa_coupon_valid ($coupons,$coupon_name,$coupons_not_exist,$payment_price,$what_return = '') {
		if (isset($coupons) && is_array($coupons)) {
			foreach ($coupons as $coupons_k => $coupons_v) {
				if (is_array($coupons_v) && in_array($coupon_name,$coupons_v)) {
					if ($what_return == "coupons_not_exist") {
						return "yes";
					}
					if (isset($coupons_v["coupon_date"]) && $coupons_v["coupon_date"] !="" && $coupons_v["coupon_date"] < date_i18n('m/d/Y',current_time('timestamp'))) {
						return '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("This coupon has expired.","wpqa").'</p></div>';
					}else if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "percent") {
						if ((int)$coupons_v["coupon_amount"] > 100) {
							return '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("This coupon is not valid.","wpqa").'</p></div>';
						}else {
							$the_discount = ($payment_price*$coupons_v["coupon_amount"])/100;
							$last_payment = $payment_price-$the_discount;
							if ($what_return == "last_payment") {
								return $last_payment;
							}
						}
					}else if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "discount") {
						if ((int)$coupons_v["coupon_amount"] > $payment_price) {
							return '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("This coupon is not valid.","wpqa").'</p></div>';
						}else {
							$last_payment = $payment_price-$coupons_v["coupon_amount"];
							if ($what_return == "last_payment") {
								return $last_payment;
							}
						}
					}else {
						return '<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Coupon code applied successfully.","wpqa").'</p></div>';
					}
				}
			}
		}
	}
endif;
/* Find coupons */
if (!function_exists('wpqa_find_coupons')) :
	function wpqa_find_coupons($coupons,$coupon_name) {
		if (isset($coupons) && is_array($coupons)) {
			foreach ($coupons as $coupons_k => $coupons_v) {
				if (is_array($coupons_v) && ((isset($coupons_v["coupon_name"]) && $coupons_v["coupon_name"] == $coupon_name)) || in_array($coupon_name,$coupons_v)) {
					return $coupons_k;
				}
			}
		}
		return false;
	}
endif;
/* Check payment available */
function wpqa_payment_available() {
	$pay_ask               = wpqa_options('pay_ask');
	$pay_group             = wpqa_options('pay_group');
	$pay_post              = wpqa_options('pay_post');
	$pay_to_sticky         = wpqa_options('pay_to_sticky');
	$subscriptions_payment = wpqa_options('subscriptions_payment');
	$buy_points_payment    = wpqa_options('buy_points_payment');
	$pay_answer            = wpqa_options('pay_answer');
	$pay_to_anything       = apply_filters("wpqa_filter_pay_to_anything",false);
	if ($pay_ask == "on" || $pay_group == "on" || $pay_post == "on" || $pay_to_sticky == "on" || $subscriptions_payment == "on" || $buy_points_payment == "on" || $pay_answer == "on" || $pay_to_anything == true) {
		return true;
	}
}
/* Not enough points message */
function wpqa_not_enough_points($session = "session") {
	$buy_points_payment = wpqa_options("buy_points_payment");
	$message = esc_html__("Sorry, you don't have enough points","wpqa").($buy_points_payment == "on"?', <a href="'.wpqa_buy_points_permalink().'">'.esc_html__("You can buy the points from here.","wpqa").'</a>':'.');
	if ($session == "session") {
		wpqa_session('<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.$message.'</p></div>','wpqa_session');
	}else {
		return $message;
	}
}
/* Sticky numbers */
function wpqa_update_sticky_numbers($option_id,$user_id,$buy_package) {
	$posts = "";
	if ($option_id == "post_packages") {
		$posts = "_post";
	}
	$packages_payment = wpqa_options($option_id);
	$packages_payment = array_values($packages_payment);
	$found_key = trim(array_search($buy_package,array_column($packages_payment,'package_posts')));
	$sticky = $packages_payment[$found_key]["sticky"];
	if ($sticky == "on") {
		$days_sticky = $packages_payment[$found_key]["days_sticky"];
		$_allow_to_sticky = get_user_meta($user_id,$user_id."_allow_to_sticky".$posts,true);
		if ($_allow_to_sticky == "" || $_allow_to_sticky < 0) {
			$_allow_to_sticky = 0;
		}
		update_user_meta($user_id,$user_id."_allow_to_sticky".$posts,$_allow_to_sticky+$buy_package);
		$sticky_numbers = get_user_meta($user_id,$user_id."_sticky_numbers".$posts,true);
		if (is_array($sticky_numbers) && !empty($sticky_numbers)) {
			$sticky_numbers[] = array("numbers" => $buy_package,"days" => $days_sticky);
			update_user_meta($user_id,$user_id."_sticky_numbers".$posts,$sticky_numbers);
		}else {
			update_user_meta($user_id,$user_id."_sticky_numbers".$posts,array(array("numbers" => $buy_package,"days" => $days_sticky)));
		}
	}
}
/* Refund the payment */
function wpqa_refund_payment() {
	$post_id = (int)$_POST["post_id"];
	$user_id = (int)$_POST["user_id"];
	$payment_id = esc_html($_POST["payment_id"]);
	$item_number = get_post_meta($post_id,"payment_item_number",true);
	$item_price = get_post_meta($post_id,"payment_item_price",true);
	$item_transaction = get_post_meta($post_id,"payment_item_transaction",true);
	$payment_method = get_post_meta($post_id,"payment_method",true);
	$item_currency = get_post_meta($post_id,"payment_item_currency",true);
	$payment_custom = get_post_meta($post_id,"payment_custom",true);
	if ($payment_method == "PayPal") {
		$result = wpqa_paypal_refund($item_transaction,$item_currency,"full");
		if (isset($result['ACK']) && $result['ACK'] == "Success") {
			$refund = true;
			$item_transaction_refund = (isset($result["REFUNDTRANSACTIONID"])?$result["REFUNDTRANSACTIONID"]:"");
			echo esc_html($item_transaction_refund);
			$item_price = (isset($result["TOTALREFUNDEDAMOUNT"])?$result["TOTALREFUNDEDAMOUNT"]:$item_price);
		}
	}else if ($payment_method == "Stripe" && $payment_id != "") {
		require_once plugin_dir_path(dirname(__FILE__)).'payments/stripe/init.php';
		$stripe_test = wpqa_options("stripe_test");
		$stripe = new \Stripe\StripeClient(wpqa_options(($stripe_test == "on"?"test_":"").'secret_key'));
		try {
			$re = $stripe->refunds->create(['payment_intent' => $payment_id]);
			if (isset($re->id) && $re->id != "") {
				$refund = true;
				$item_transaction_refund = $re->id;
				echo esc_html($item_transaction_refund);
				$item_price = (isset($re->amount)?floatval($re->amount/100):$item_price);
			}
		}catch ( \Stripe\Exception\CardException $e ) {
			error_log(print_r($e->getError()->message,true));
		}catch ( Exception $e ) {
			error_log(print_r($e->getMessage(),true));
		}
	}
	if (isset($refund)) {
		wpqa_refund_canceled_payment($user_id,$post_id,$item_number);
		$response = array(
			"item_name"            => get_the_title($post_id),
			"item_price"           => $item_price,
			"item_currency"        => $item_currency,
			"item_transaction"     => $item_transaction_refund,
			"original_transaction" => $item_transaction,
		);
		wpqa_insert_refund($response,$user_id,"refund");
		wpqa_site_user_money($item_price,"-",$item_currency,$user_id);
		update_post_meta($post_id,"payment_refund","refund");
		update_post_meta($post_id,"payment_original_transaction",$item_transaction_refund);
	}
	die();
}
add_action('wp_ajax_wpqa_refund_payment','wpqa_refund_payment');
/* Refund or canceled the payment */
function wpqa_refund_canceled_payment($user_id,$post_id,$item_number) {
	do_action("wpqa_update_payment_status",$user_id,($post_id > 0?$post_id:0));
	$payment_custom = get_post_meta($post_id,"payment_custom",true);
	$str_replace = str_replace("wpqa_".$item_number."-","",$payment_custom);
	$item_price = get_post_meta($post_id,"payment_item_price",true);
	$another_way_payment_filter = apply_filters("wpqa_another_way_refund_filter",true,array("user_id" => $user_id,"post_id" => $post_id,"item_number" => $item_number,"str_replace" => $str_replace,"item_price" => $item_price));
	if ($another_way_payment_filter == true) {
		if (strpos($payment_custom,'wpqa_pay-') !== false) {
			$allow_to_pay_user = get_user_meta($user_id,"wpqa_allow_to_pay",true);
			if (is_array($allow_to_pay_user) && in_array($str_replace,$allow_to_pay_user)) {
				$allow_to_pay_user = wpqa_remove_item_by_value($allow_to_pay_user,$str_replace);
				update_user_meta($user_id,"wpqa_allow_to_pay",$allow_to_pay_user);
			}

			$allow_to_pay_post = get_post_meta($user_id,"wpqa_allow_to_pay",true);
			if (is_array($allow_to_pay_post) && in_array($user_id,$allow_to_pay_post)) {
				$allow_to_pay_post = wpqa_remove_item_by_value($allow_to_pay_post,$user_id);
				update_post_meta($str_replace,"wpqa_allow_to_pay",$allow_to_pay_post);
			}

			$wpqa_number_payments = (int)get_post_meta($str_replace,"wpqa_number_payments",true);
			$wpqa_number_payments--;
			update_post_meta($str_replace,"wpqa_number_payments",$wpqa_number_payments);
			if (is_numeric($str_replace)) {
				$post_id = (int)$str_replace;
				$get_post = get_post($post_id);
				if (isset($get_post->ID)) {
					$payment_action = get_post_meta($get_post->ID,prefix_meta."payment_action",true);
					$payment_role = get_post_meta($get_post->ID,prefix_meta."payment_role",true);
					if ($payment_action == "role" && $payment_role != "") {
						wpqa_stop_subscription($user_id,$get_post->ID,"cancel");
						$default_group = wpqa_options("default_group");
						$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
						$default_group = apply_filters("wpqa_default_group",$default_group,$user_id);
						wp_update_user(array('ID' => $user_id,'role' => $default_group));
					}
				}
			}
		}else if (strpos($payment_custom,'wpqa_pay_answer-') !== false) {
			$_allow_to_answer = (int)get_user_meta($user_id,$user_id."_allow_to_answer",true);
			$_allow_to_answer--;
			update_user_meta($user_id,$user_id."_allow_to_answer",$_allow_to_answer);
			delete_user_meta($user_id,"pay_to_answer_".$str_replace);
		}else if (strpos($payment_custom,'wpqa_pay_sticky-') !== false) {
			$_allow_to_sticky = (int)get_user_meta($user_id,$user_id."_allow_to_sticky",true);
			$_allow_to_sticky--;
			update_user_meta($user_id,$user_id."_allow_to_sticky",$_allow_to_sticky);
			delete_post_meta($str_replace,"end_sticky_time");
			$sticky_questions = get_option('sticky_questions');
			if (is_array($sticky_questions) && in_array($str_replace,$sticky_questions)) {
				$sticky_questions = wpqa_remove_item_by_value($sticky_questions,$str_replace);
				update_option('sticky_questions',$sticky_questions);
			}
			$sticky_posts = get_option('sticky_posts');
			if (is_array($sticky_posts) && in_array($str_replace,$sticky_posts)) {
				$sticky_posts = wpqa_remove_item_by_value($sticky_posts,$str_replace);
				update_option('sticky_posts',$sticky_posts);
			}
			delete_post_meta($str_replace,'sticky');
		}else if (strpos($payment_custom,'wpqa_buy_points-') !== false) {
			wpqa_add_points($user_id,$str_replace,"-","refund_points");
		}else if (strpos($payment_custom,'wpqa_subscribe-') !== false) {
			wpqa_stop_subscription($user_id,0,"cancel");
			$default_group = wpqa_options("default_group");
			$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
			$default_group = apply_filters("wpqa_default_group",$default_group,$user_id);
			wp_update_user(array('ID' => $user_id,'role' => $default_group));
		}else if (strpos($payment_custom,'wpqa_ask_question-') !== false || strpos($payment_custom,'wpqa_buy_questions-') !== false) {
			$_allow_to_ask = (int)get_user_meta($user_id,$user_id."_allow_to_ask",true);
			if (strpos($payment_custom,'wpqa_buy_questions-') !== false) {
				$explode = explode("-",$str_replace);
				if (isset($explode[0]) && $explode[0] != "") {
					$number_of_questions = $explode[0];
				}
				$_allow_to_ask = $_allow_to_ask-$number_of_questions;
				$_allow_to_sticky = (int)get_user_meta($user_id,$user_id."_allow_to_sticky",true);
				$_sticky_numbers = get_user_meta($user_id,$user_id."_sticky_numbers_post",true);
				if ($_allow_to_sticky > 0 && is_array($_sticky_numbers) && !empty($_sticky_numbers)) {
					$_allow_to_sticky = (int)get_user_meta($user_id,$user_id."_allow_to_sticky",true);
					update_user_meta($user_id,$user_id."_allow_to_sticky",$_allow_to_sticky-$number_of_questions);
					$k = 0;
					foreach ($_sticky_numbers as $key => $value) {$k++;
						if ($k == 1 && isset($value["numbers"]) && $value["numbers"] > 0) {
							$days_sticky = $value["days"];
							$_sticky_numbers[$key]["numbers"] = $value["numbers"]-$number_of_questions;
							if ($_sticky_numbers[$key]["numbers"] <= 0) {
								unset($_sticky_numbers[$key]);
							}
						}
					}
					update_user_meta($user_id,$user_id."_sticky_numbers_post",$_sticky_numbers);
				}
			}else {
				$_allow_to_ask--;
			}
			update_user_meta($user_id,$user_id."_allow_to_ask",$_allow_to_ask);
		}else if (strpos($payment_custom,'wpqa_add_group-') !== false || strpos($payment_custom,'wpqa_buy_groups-') !== false) {
			$_allow_to_group = (int)get_user_meta($user_id,"_allow_to_group",true);
			if (strpos($payment_custom,'wpqa_buy_groups-') !== false) {
				$number_of_groups = $str_replace;
				$_allow_to_group = $_allow_to_group-$number_of_groups;
			}else {
				$_allow_to_group--;
			}
			update_user_meta($user_id,"_allow_to_group",$_allow_to_group);
		}else if (strpos($payment_custom,'wpqa_add_post-') !== false || strpos($payment_custom,'wpqa_buy_posts-') !== false) {
			$_allow_to_post = (int)get_user_meta($user_id,$user_id."_allow_to_post",true);
			if (strpos($payment_custom,'wpqa_buy_posts-') !== false) {
				$number_of_posts = $str_replace;
				$_allow_to_post = $_allow_to_post-$number_of_posts;
				$_allow_to_sticky_post = (int)get_user_meta($user_id,$user_id."_allow_to_sticky_post",true);
				$_sticky_numbers = get_user_meta($user_id,$user_id."_sticky_numbers_post",true);
				if ($_allow_to_sticky_post > 0 && is_array($_sticky_numbers) && !empty($_sticky_numbers)) {
					$_allow_to_sticky_post = (int)get_user_meta($user_id,$user_id."_allow_to_sticky_post",true);
					update_user_meta($user_id,$user_id."_allow_to_sticky_post",$_allow_to_sticky_post-$number_of_posts);
					$k = 0;
					foreach ($_sticky_numbers as $key => $value) {$k++;
						if ($k == 1 && isset($value["numbers"]) && $value["numbers"] > 0) {
							$days_sticky = $value["days"];
							$_sticky_numbers[$key]["numbers"] = $value["numbers"]-$number_of_posts;
							if ($_sticky_numbers[$key]["numbers"] <= 0) {
								unset($_sticky_numbers[$key]);
							}
						}
					}
					update_user_meta($user_id,$user_id."_sticky_numbers_post",$_sticky_numbers);
				}
			}else {
				$_allow_to_post--;
			}
			update_user_meta($user_id,$user_id."_allow_to_post",$_allow_to_post);
		}
	}
}?>