<?php

/* @author    2codeThemes
*  @package   WPQA/payments
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Pay to answer in question page */
add_filter(wpqa_prefix_theme."_allow_to_add_answer","wpqa_allow_to_add_answer",1,6);
add_filter("wpqa_allow_to_add_answer","wpqa_allow_to_add_answer",1,6);
function wpqa_allow_to_add_answer($return,$user_id,$custom_permission,$roles,$post_id,$post_type) {
	$pay_answer = wpqa_options("pay_answer");
	$custom_pay_answer = get_post_meta($post_id,"custom_pay_answer",true);
	if ($custom_pay_answer == "on") {
		$pay_answer = get_post_meta($post_id,"pay_answer",true);
	}
	$pay_answer = apply_filters('wpqa_pay_answer',$pay_answer);
	if (($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) && $pay_answer === "on") {
		$return = wpqa_pay_to_answer_link($return,$user_id,$custom_permission,$roles,$post_id);
	}
	if ($return === true) {
		return $return;
	}else {
		echo '<div id="respond-all"><div id="respond" class="comment-respond">'.$return.'</div></div>';
	}
}
/* Pay to answer with popup */
add_filter("wpqa_allow_to_add_answer_ajax","wpqa_allow_to_add_answer_ajax",1,3);
function wpqa_allow_to_add_answer_ajax($return,$user_id,$post_id) {
	$pay_answer = wpqa_options("pay_answer");
	$custom_pay_answer = get_post_meta($post_id,"custom_pay_answer",true);
	if ($custom_pay_answer == "on") {
		$pay_answer = get_post_meta($post_id,"pay_answer",true);
	}
	$pay_answer = apply_filters('wpqa_pay_answer',$pay_answer);
	if ($pay_answer === "on") {
		$custom_permission = wpqa_options("custom_permission");
		if (is_user_logged_in()) {
			$user_is_login = get_userdata($user_id);
			$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
		}
		$return = wpqa_pay_to_answer_link($return,$user_id,$custom_permission,$roles,$post_id);
	}
	if ($return === true) {
		return $return;
	}else {
		echo ($return);
	}
}
/* Check if need pay to answer */
function wpqa_check_pay_to_answer($user_id,$custom_permission,$roles,$post_id = 0) {
	$post_id = (int)($post_id > 0?$post_id:(isset($_REQUEST["post_id"])?"post_id":0));
	$return = false;
	$_allow_to_answer = (int)(isset($user_id) && $user_id != ""?get_user_meta($user_id,$user_id."_allow_to_answer",true):"");
	$pay_to_answer = get_user_meta($user_id,"pay_to_answer_".$post_id,true);
	if (!is_super_admin($user_id) && (($pay_to_answer != "paid" && $_allow_to_answer > 0) || $_allow_to_answer < 1) && ($custom_permission != "on" || ($custom_permission == "on" && empty($roles["add_answer_payment"])))) {
		$return = true;
	}
	return $return;
}
/* Pay to answer */
function wpqa_pay_to_answer_link($return,$user_id,$custom_permission,$roles,$post_id = 0) {
	$post_id = (int)($post_id > 0?$post_id:(isset($_REQUEST["post_id"])?"post_id":0));
	$check_pay_to_answer = wpqa_check_pay_to_answer($user_id,$custom_permission,$roles,$post_id);
	if ($check_pay_to_answer == true) {
		$payment_pages_target = wpqa_options("payment_pages_target");
		$payment_pages_target = ($payment_pages_target == "new_page"?"_blank":"_self");
		$return = '<div class="card widget"><div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Please make a payment to be able to add an answer.","wpqa").'</p></div>
		<a href="'.wpqa_checkout_link("answer",($post_id > 0?$post_id:"")).'" class="button-default btn btn__primary button-pay-answer" target="'.$payment_pages_target.'">'.esc_html__("Pay to add an answer","wpqa").'</a></div>';
	}
	return $return;
}
/* Pay answer by points */
add_action("wpqa_pay_to_answer","wpqa_pay_to_answer");
add_action(wpqa_prefix_theme."_pay_to_answer","wpqa_pay_to_answer");
function wpqa_pay_to_answer($user_id) {
	$points_user = (int)(is_user_logged_in()?get_user_meta($user_id,"points",true):0);
	$_allow_to_answer = (int)(isset($user_id) && $user_id != ""?get_user_meta($user_id,$user_id."_allow_to_answer",true):"");
	$protocol = is_ssl() ? 'https' : 'http';
	$return_url = wp_unslash($protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
	if ($user_id > 0 && isset($_POST["process"]) && $_POST["process"] == "answer") {
		if (isset($_POST["points"]) && $_POST["points"] > 0) {
			$points_price = (int)$_POST["points"];
			$points_user = (int)(is_user_logged_in()?get_user_meta($user_id,"points",true):0);
			if ($points_price <= $points_user) {
				wpqa_add_points($user_id,$points_price,"-","answer_points");
				/* Insert a new payment */
				$item_no = esc_html($_POST["process"]);
				$item_id = (isset($_POST["item_id"]) && $_POST["item_id"] != ""?esc_html($_POST["item_id"]):"");
				if ($item_id > 0) {
					$authordata = get_userdata($item_id);
					$author_display_name = (isset($authordata->display_name)?$authordata->display_name:"");
				}
				$payment_description = esc_attr__("Add a new answer","wpqa");
				$save_pay_by_points = wpqa_options("save_pay_by_points");
				if ($save_pay_by_points == "on") {
					$array = array (
						'item_no'    => $item_no,
						'item_name'  => $payment_description,
						'item_price' => 0,
						'first_name' => get_the_author_meta("first_name",$user_id),
						'last_name'  => get_the_author_meta("last_name",$user_id),
						'points'     => $points_price,
						'custom'     => 'wpqa_'.$item_no.'-'.$item_id,
					);
					if ($item_id > 0) {
						$array["payment_asked"] = $item_id;
					}
					if (isset($_POST["buy_package"])) {
						$array["payment_package"] = esc_html($_POST["buy_package"]);
					}
					wpqa_insert_statement($array,$user_id);
				}
				$message = esc_html__("You have just bought to add an answer by points.","wpqa");
				wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.$message.'</p></div>','wpqa_session');
			}else {
				wpqa_not_enough_points();
				wp_safe_redirect(esc_url($return_url));
				die();
			}
		}
		/* Number allow to add answer */
		if ($_allow_to_answer == "" || $_allow_to_answer < 0) {
			$_allow_to_answer = 0;
		}
		$_allow_to_answer++;
		update_user_meta($user_id,$user_id."_allow_to_answer",$_allow_to_answer);
		if (isset($_POST["post_id"])) {
			$post_id = (int)$_POST["post_id"];
			update_user_meta($user_id,"pay_to_answer_".$post_id,"paid");
		}
		wp_safe_redirect(esc_url($return_url));
		die();
	}
}
?>