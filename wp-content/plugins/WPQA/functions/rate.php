<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Rate article */
function wpqa_rate_article($style = "style_1",$post_id = 0) {
	$out = '';
	if (is_single()) {
		if ($style == "style_1" || $style == "style_2") {
			$out = wpqa_rate_article_style_1($post_id,$style);
		}else {
			$out = wpqa_rate_article_style_2($post_id,$style);
		}
	}
	return $out;
}
function wpqa_found_rate($post_id,$user_id) {
	$up_count = (int)get_post_meta($post_id,"wpqa_rate_up",true);
	$down_count = (int)get_post_meta($post_id,"wpqa_rate_down",true);
	if ($user_id > 0) {
		$up_posts_rate = get_user_meta($user_id,"wpqa_posts_rate_up",true);
		$up_posts_rate = (is_array($up_posts_rate) && !empty($up_posts_rate)?$up_posts_rate:array());
		$down_posts_rate = get_user_meta($user_id,"wpqa_posts_rate_down",true);
		$down_posts_rate = (is_array($down_posts_rate) && !empty($down_posts_rate)?$down_posts_rate:array());

		$up_users_rate = get_post_meta($post_id,"wpqa_users_rate_up",true);
		$up_users_rate = (is_array($up_users_rate) && !empty($up_users_rate)?$up_users_rate:array());
		$down_users_rate = get_post_meta($post_id,"wpqa_users_rate_down",true);
		$down_users_rate = (is_array($down_users_rate) && !empty($down_users_rate)?$down_users_rate:array());
		if (in_array($user_id,$up_users_rate) || in_array($user_id,$down_users_rate)) {
			$found_rate = true;
			if (in_array($user_id,$up_users_rate)) {
				$found_up_rate = true;
			}
			if (in_array($user_id,$down_users_rate)) {
				$found_down_rate = true;
			}
		}
	}else {
		$uniqid_cookie = wpqa_options('uniqid_cookie');
		$wpqa_rate_up = (isset($_COOKIE[$uniqid_cookie.'wpqa_rate_up_'.$post_id])?$_COOKIE[$uniqid_cookie.'wpqa_rate_up_'.$post_id]:'');
		$wpqa_rate_down = (isset($_COOKIE[$uniqid_cookie.'wpqa_rate_down_'.$post_id])?$_COOKIE[$uniqid_cookie.'wpqa_rate_down_'.$post_id]:'');
		if ($wpqa_rate_up != "" || $wpqa_rate_down != "") {
			$found_rate = true;
			if ($wpqa_rate_up != "") {
				$found_up_rate = true;
			}
			if ($wpqa_rate_down != "") {
				$found_down_rate = true;
			}
		}
	}
	$result = array(
		"up_count" => $up_count,
		"down_count" => $down_count,
		"found_rate" => (isset($found_rate)?true:false),
		"found_up_rate" => (isset($found_up_rate)?true:false),
		"found_down_rate" => (isset($found_down_rate)?true:false),
		"up_posts_rate" => (isset($up_posts_rate)?$up_posts_rate:array()),
		"down_posts_rate" => (isset($down_posts_rate)?$down_posts_rate:array()),
		"up_users_rate" => (isset($up_users_rate)?$up_users_rate:array()),
		"down_users_rate" => (isset($down_users_rate)?$down_users_rate:array()),
	);
	return $result;
}
/* Rate article style 1 */
function wpqa_rate_article_style_1($post_id,$style = "style_1") {
	$user_id = get_current_user_id();
	$result = wpqa_found_rate($post_id,$user_id);
	$out = '<div class="rate-article rate-article-1'.($style == "style_2"?" rate-article-2":"").'">
		<div class="rate-article-top">
			<div class="rate-article-head">'.esc_html__("Was this helpful?","wpqa").'</div>
			<div class="small_loader loader_2"></div>
			<div class="rate-article-content">
				<div class="rate-article-up rate-article-link'.($result['found_rate'] == true?" rate-already-voted":"").($result['found_up_rate'] == true?" rate-already-up-voted":"").'" data-post="'.$post_id.'" data-type="up">
					<i class="icon-thumbsup"></i><span>'.$result['up_count'].'</span> '.esc_html__("Yes","wpqa").'
				</div>
				<div class="rate-article-down rate-article-link'.($result['found_rate'] == true?" rate-already-voted":"").($result['found_down_rate'] == true?" rate-already-down-voted":"").'" data-post="'.$post_id.'" data-type="down">
					<i class="icon-thumbsdown"></i><span>'.$result['down_count'].'</span> '.esc_html__("No","wpqa").'
				</div>
			</div>
		</div>'.
		wpqa_rate_article_bottom($post_id);
	$out .= '</div>';
	return $out;
}
/* Rate article style 2 */
function wpqa_rate_article_style_2($post_id,$style = "style_3") {
	$user_id = get_current_user_id();
	$uniqid_cookie = wpqa_options("uniqid_cookie");
	$rate_items = array(
		"sad"      => array("sort" => esc_html__('Sad','wpqa'),"value" => "sad"),
		"angry"    => array("sort" => esc_html__('Angry','wpqa'),"value" => "angry"),
		"confused" => array("sort" => esc_html__('Confused','wpqa'),"value" => "confused"),
		"happy"    => array("sort" => esc_html__('Happy','wpqa'),"value" => "happy"),
		"love"     => array("sort" => esc_html__('Love','wpqa'),"value" => "love")
	);
	$meta = "wpqa_rates";
	$get_rates = wpqa_get_meta($meta,$post_id);
	$get_rates = (is_array($get_rates) && !empty($get_rates)?$get_rates:array());
	if (is_array($rate_items) && !empty($rate_items)) {
		foreach ($rate_items as $key_rate => $value_rate) {
			if ($user_id == 0 && isset($_COOKIE[$uniqid_cookie.'wpqa_rate_'.$post_id]) && $_COOKIE[$uniqid_cookie.'wpqa_rate_'.$post_id] != "") {
				$found_user = true;
				$found_rate = $_COOKIE[$uniqid_cookie.'wpqa_rate_'.$post_id];
			}
			$wpqa_key_rate = "wpqa_rate_".$key_rate;
			if (isset($get_rates[$wpqa_key_rate]) && is_array($get_rates[$wpqa_key_rate])) {
				if (in_array($user_id,$get_rates[$wpqa_key_rate])) {
					$found_user = true;
					if ($key_rate == "sad" || $key_rate == "angry" || $key_rate == "confused" || $key_rate == "happy" || $key_rate == "love") {
						$found_rate = $key_rate;
					}
				}
			}
		}
	}
	
	$out = '<div class="rate-article'.($style == "style_3"?" rate-article-3":" rate-article-4").'">
		<div class="rate-article-top">
			<div class="rate-article-head">'.esc_html__("Was this helpful?","wpqa").'</div>
			<div class="small_loader loader_2"></div>
			<div class="rate-article-content">
			<ul class="rates__list list-unstyled mb-0 rate-id" data-id="'.$post_id.'">';
				if (is_array($rate_items) && !empty($rate_items)) {
					foreach ($rate_items as $key => $value) {
						if (isset($value["sort"]) && isset($value["value"]) && $value["value"] == $key) {
							$out .= '<li'.(isset($found_rate) && $found_rate == $key?" class='rates__activated'":"").'><div class="tooltip-n rates_action" data-type="'.$key.'" original-title="'.$value["sort"].'"><img src="'.plugin_dir_url(dirname(__FILE__)).'/images/rates/'.$key.'.png" alt="'.$value["sort"].'"></div></li>';
						}
					}
				}
			$out .= '</ul>
			</div>
		</div>'.
		wpqa_rate_article_bottom($post_id);
	$out .= '</div>';
	return $out;
}
/* Rate article bottom */
function wpqa_rate_article_bottom($post_id) {
	$custom_page_setting = get_post_meta($post_id,prefix_meta."custom_page_setting",true);
	$didnt_find_answer_link = wpqa_options("didnt_find_answer_link");
	if ($custom_page_setting == "on") {
		$didnt_find_answer_link = get_post_meta($post_id,prefix_meta."didnt_find_answer_link",true);
	}
	if ($didnt_find_answer_link == "on") {
		$didnt_find_answer_page = wpqa_options("didnt_find_answer_page");
		if ($custom_page_setting == "on") {
			$didnt_find_answer_page = get_post_meta($post_id,prefix_meta."didnt_find_answer_page",true);
		}
		if ($didnt_find_answer_page == "contact") {
			$pages = get_pages(array('meta_key' => '_wp_page_template','meta_value' => 'template-contact.php'));
			$page_link = (isset($pages) && isset($pages[0]) && isset($pages[0]->ID)?get_permalink($pages[0]->ID):'');
			$text = esc_html__("Contact Us","wpqa");
		}else if ($didnt_find_answer_page == "question") {
			$page_link = wpqa_add_question_permalink();
			$text = esc_html__("Ask A Question","wpqa");
		}else if ($didnt_find_answer_page == "custom") {
			$didnt_find_answer_custom = wpqa_options("didnt_find_answer_custom");
			$didnt_find_answer_text = wpqa_options("didnt_find_answer_text");
			if ($custom_page_setting == "on") {
				$didnt_find_answer_custom = get_post_meta($post_id,prefix_meta."didnt_find_answer_custom",true);
				$didnt_find_answer_text = get_post_meta($post_id,prefix_meta."didnt_find_answer_text",true);
			}
			$page_link = $didnt_find_answer_custom;
			$text = $didnt_find_answer_text;
		}
		$out = '<div class="rate-article-bottom">'.esc_html__("Didn't find your answer?","wpqa").' '.(isset($page_link) && $page_link != '' && isset($text) && $text != ''?'<a href="'.$page_link.'" target="_blank">'.$text.'</a>':'').'</div>';
	}
	return (isset($out)?$out:'');
}
/* Show the rate block */
add_action(wpqa_prefix_theme."_after_knowledgebase_tags","wpqa_after_knowledgebase_tags",1,3);
function wpqa_after_knowledgebase_tags($post_id,$didnt_find_answer,$didnt_find_answer_style) {
	if ($didnt_find_answer == "on") {
		echo wpqa_rate_article($didnt_find_answer_style,$post_id);
	}
}
/* Rate article ajax */
function wpqa_rate($data = array()) {
	$result = array();
	$mobile = (is_array($data) && !empty($data)?true:false);
	$data = (is_array($data) && !empty($data)?$data:$_POST);

	$user_id = get_current_user_id();
	
	$post_id = (int)$data["post_id"];
	$type = esc_html($data["type"]);
	$type = ($type == "up"?"up":"down");
	$type_reverse = ($type == "up"?"down":"up");

	$wpqa_result = wpqa_found_rate($post_id,$user_id);
	if ($wpqa_result['found_rate'] == true && $wpqa_result['found_'.$type.'_rate'] == true) {
		$wpqa_result = wpqa_remove_rate($wpqa_result,$user_id,$post_id,$type);
	}else {
		if ($wpqa_result['found_rate'] == true && $wpqa_result['found_'.$type_reverse.'_rate'] == true) {
			$wpqa_result = wpqa_remove_rate($wpqa_result,$user_id,$post_id,$type_reverse);
		}
		$wpqa_result = wpqa_add_rate($wpqa_result,$user_id,$post_id,$type);
	}

	$result = $wpqa_result;
	if ($mobile == true) {
		return $result;
	}
	echo json_encode(apply_filters('wpqa_json_rate',$result));
	die();
}
add_action('wp_ajax_wpqa_rate','wpqa_rate');
add_action('wp_ajax_nopriv_wpqa_rate','wpqa_rate');
/* Add rate */
function wpqa_add_rate($wpqa_result,$user_id,$post_id,$type) {
	if ($user_id > 0) {
		if (empty($wpqa_result[$type.'_posts_rate'])) {
			$wpqa_result[$type.'_posts_rate'] = array($post_id);
		}else if (is_array($wpqa_result[$type.'_posts_rate']) && !in_array($post_id,$wpqa_result[$type.'_posts_rate'])) {
			$wpqa_result[$type.'_posts_rate'] = array_merge($wpqa_result[$type.'_posts_rate'],array($post_id));
		}
		update_user_meta($user_id,"wpqa_posts_rate_".$type,$wpqa_result[$type.'_posts_rate']);

		if (empty($wpqa_result[$type.'_users_rate'])) {
			$wpqa_result[$type.'_users_rate'] = array($user_id);
		}else if (is_array($wpqa_result[$type.'_users_rate']) && !in_array($user_id,$wpqa_result[$type.'_users_rate'])) {
			$wpqa_result[$type.'_users_rate'] = array_merge($wpqa_result[$type.'_users_rate'],array($user_id));
		}
		update_post_meta($post_id,"wpqa_users_rate_".$type,$wpqa_result[$type.'_users_rate']);
	}else {
		$uniqid_cookie = wpqa_options("uniqid_cookie");
		setcookie($uniqid_cookie.'wpqa_rate_'.$type.'_'.$post_id,"wpqa_yes_rated",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
	}
	$wpqa_result[$type.'_count']++;
	update_post_meta($post_id,"wpqa_rate_".$type,$wpqa_result[$type.'_count']);
	return $wpqa_result;
}
/* Remove rate */
function wpqa_remove_rate($wpqa_result,$user_id,$post_id,$type) {
	if ($user_id > 0) {
		$wpqa_result[$type.'_posts_rate'] = wpqa_remove_item_by_value($wpqa_result[$type.'_posts_rate'],$post_id);
		update_user_meta($user_id,"wpqa_posts_rate_".$type,$wpqa_result[$type.'_posts_rate']);

		$wpqa_result[$type.'_users_rate'] = wpqa_remove_item_by_value($wpqa_result[$type.'_users_rate'],$user_id);
		update_post_meta($post_id,"wpqa_users_rate_".$type,$wpqa_result[$type.'_users_rate']);
	}else {
		$uniqid_cookie = wpqa_options("uniqid_cookie");
		if (isset($_COOKIE[$uniqid_cookie.'wpqa_rate_'.$type.'_'.$post_id]) && $_COOKIE[$uniqid_cookie.'wpqa_rate_'.$type.'_'.$post_id] == "wpqa_yes_rated") {
			unset($_COOKIE[$uniqid_cookie.'wpqa_rate_'.$type.'_'.$post_id]);
			setcookie($uniqid_cookie.'wpqa_rate_'.$type.'_'.$post_id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
		}
		setcookie($uniqid_cookie.'wpqa_rate_'.$type.'_'.$post_id,"wpqa_yes_rated",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
	}
	$wpqa_result[$type.'_count']--;
	update_post_meta($post_id,"wpqa_rate_".$type,$wpqa_result[$type.'_count']);
	return $wpqa_result;
}
/* Rates */
if (!function_exists('wpqa_rates')) :
	function wpqa_rates($data = array()) {
		$uniqid_cookie = wpqa_options("uniqid_cookie");

		$mobile = (is_array($data) && !empty($data)?true:false);
		$data = (is_array($data) && !empty($data)?$data:$_POST);

		$get_current_user_id = get_current_user_id();
		
		$post_id = (isset($data["post_id"])?(int)$data["post_id"]:0);
		$type = esc_html($data["type"]);

		$get_post = get_post($post_id);
		$post_type = (isset($get_post->post_type)?$get_post->post_type:get_post_type($post_id));

		$gender_author = get_user_meta($get_current_user_id,'gender',true);
		$key_gender = "other";
		if ($gender_author == 1) {
			$key_gender = "male";
		}else if ($gender_author == 2) {
			$key_gender = "female";
		}

		$meta = "wpqa_rates";
		
		$meta_sad = "wpqa_rate_sad";
		$meta_angry = "wpqa_rate_angry";
		$meta_confused = "wpqa_rate_confused";
		$meta_happy = "wpqa_rate_happy";
		$meta_love = "wpqa_rate_love";

		$array_main_rate = array(
			"main" => $meta,

			"sad" => $meta_sad,
			"angry" => $meta_angry,
			"confused" => $meta_confused,
			"happy" => $meta_happy,
			"love" => $meta_love,

			"gender_sad" => $meta_sad."_".$key_gender,
			"gender_angry" => $meta_angry."_".$key_gender,
			"gender_confused" => $meta_confused."_".$key_gender,
			"gender_happy" => $meta_happy."_".$key_gender,
			"gender_love" => $meta_love."_".$key_gender
		);

		$count_rate_gender = array(
			$meta."_count",
			$meta."_count_".$key_gender,
		);

		$get_rates = wpqa_get_meta($meta,$post_id);
		$get_rates = (is_array($get_rates) && !empty($get_rates)?$get_rates:array());
		$notifications_activities_type = ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?wpqa_questions_type:$post_type);
		
		if (is_array($array_main_rate) && !empty($array_main_rate)) {
			foreach ($array_main_rate as $key_rate => $value_rate) {
				if (($get_current_user_id == 0 && isset($_COOKIE[$uniqid_cookie.'wpqa_rate_'.$post_id]) && $_COOKIE[$uniqid_cookie.'wpqa_rate_'.$post_id] != "") || ($get_current_user_id > 0 && isset($get_rates[$value_rate]) && is_array($get_rates[$value_rate]) && in_array($get_current_user_id,$get_rates[$value_rate]))) {
					$found_user = true;
				}
			}
			if ($get_current_user_id == 0) {
				if (isset($_COOKIE[$uniqid_cookie.'wpqa_rate_'.$post_id]) && $_COOKIE[$uniqid_cookie.'wpqa_rate_'.$post_id] != "") {
					unset($_COOKIE[$uniqid_cookie.'wpqa_rate_'.$post_id]);
					setcookie($uniqid_cookie.'wpqa_rate_'.$post_id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
				}
				if (isset($found_user)) {
					setcookie($uniqid_cookie.'wpqa_rate_'.$post_id,$type,time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
				}
			}
			foreach ($array_main_rate as $key_rate => $value_rate) {
				if ($get_current_user_id > 0 && isset($get_rates[$value_rate]) && is_array($get_rates[$value_rate]) && in_array($get_current_user_id,$get_rates[$value_rate])) {
					if ((isset($found_user) && $key_rate != "main" && $key_rate != $type) || (!isset($found_user) && $key_rate == "main")) {
						if ($get_current_user_id > 0) {
							$get_rates[$value_rate] = wpqa_remove_item_by_value($get_rates[$value_rate],$get_current_user_id);
						}
					}
					if ($key_rate == "main" && !isset($found_user)) {
						foreach ($count_rate_gender as $value_rate_gender) {
							$count_rates = (int)wpqa_get_meta($value_rate_gender,$post_id);
							$count_rates--;
							wpqa_update_meta($value_rate_gender,($count_rates > 0?$count_rates:0),$post_id);
						}
						if ($get_current_user_id > 0) {
							wpqa_notifications_activities($get_current_user_id,"","",$post_id,"",$notifications_activities_type."_remove_rate","activities","",$notifications_activities_type);
						}
					}
				}else {
					if ((isset($found_user) || !isset($found_user)) && ($key_rate == "main" || $key_rate == $type || $key_rate == "gender_".$type)) {
						if ($get_current_user_id > 0) {
							if (empty($get_rates[$value_rate])) {
								$get_rates[$value_rate] = array($get_current_user_id);
							}else if (isset($get_rates[$value_rate]) && is_array($get_rates[$value_rate]) && !in_array($get_current_user_id,$get_rates[$value_rate])) {
								$get_rates[$value_rate] = array_merge($get_rates[$value_rate],array($get_current_user_id));
							}
						}
						if ($key_rate == "main") {
							foreach ($count_rate_gender as $value_rate_gender) {
								$count_rates = (int)wpqa_get_meta($value_rate_gender,$post_id);
								$count_rates++;
								wpqa_update_meta($value_rate_gender,($count_rates > 0?$count_rates:0),$post_id);
							}
							if ($get_current_user_id > 0) {
								wpqa_notifications_activities($get_current_user_id,"","",$post_id,"",$notifications_activities_type."_rate_".$type,"activities","",$notifications_activities_type);
							}
						}
					}
				}
			}
			wpqa_update_meta($meta,$get_rates,$post_id);
		}

		$result = wpqa_rate_results($meta,$get_current_user_id,$post_id);

		if ($mobile == true) {
			return $result;
		}
		echo json_encode(apply_filters('wpqa_json_rates',$result));
		die();
	}
endif;
add_action('wp_ajax_wpqa_rates','wpqa_rates');
add_action('wp_ajax_nopriv_wpqa_rates','wpqa_rates');
/* Rate results */
function wpqa_rate_results($meta,$user_id = 0,$post_id = 0) {
	$uniqid_cookie = wpqa_options("uniqid_cookie");
	$gender_author = get_user_meta($user_id,'gender',true);
	$key_gender = "other";
	if ($gender_author == 1) {
		$key_gender = "male";
	}else if ($gender_author == 2) {
		$key_gender = "female";
	}

	$meta_sad = "wpqa_rate_sad";
	$meta_angry = "wpqa_rate_angry";
	$meta_confused = "wpqa_rate_confused";
	$meta_happy = "wpqa_rate_happy";
	$meta_love = "wpqa_rate_love";

	$array_main_rate = array(
		"main" => $meta,

		"sad" => $meta_sad,
		"angry" => $meta_angry,
		"confused" => $meta_confused,
		"happy" => $meta_happy,
		"love" => $meta_love,

		"gender_sad" => $meta_sad."_".$key_gender,
		"gender_angry" => $meta_angry."_".$key_gender,
		"gender_confused" => $meta_confused."_".$key_gender,
		"gender_happy" => $meta_happy."_".$key_gender,
		"gender_love" => $meta_love."_".$key_gender
	);

	$count_rates = (int)wpqa_get_meta($meta."_count",$post_id);
	$count = ($count_rates > 0?$count_rates:0);

	$count_rates_female = (int)wpqa_get_meta($meta."_count_female",$post_id);
	$count_female = ($count_rates_female > 0?$count_rates_female:0);

	$count_rates_male = (int)wpqa_get_meta($meta."_count_male",$post_id);
	$count_male = ($count_rates_male > 0?$count_rates_male:0);

	$get_rates = wpqa_get_meta($meta,$post_id);
	$get_rates = (is_array($get_rates) && !empty($get_rates)?$get_rates:array());

	$count_all = array(
		"sad" => array("name" => esc_attr__("Sad","wpqa"),"count" => 0),
		"angry" => array("name" => esc_attr__("Angry","wpqa"),"count" => 0),
		"confused" => array("name" => esc_attr__("Confused","wpqa"),"count" => 0),
		"happy" => array("name" => esc_attr__("Happy","wpqa"),"count" => 0),
		"love" => array("name" => esc_attr__("Love","wpqa"),"count" => 0)
	);
	$counts_all = 0;
	if (is_array($array_main_rate) && !empty($array_main_rate)) {
		foreach ($array_main_rate as $key_rate => $value_rate) {
			if ($user_id == 0 && isset($_COOKIE[$uniqid_cookie.'wpqa_rate_'.$post_id]) && $_COOKIE[$uniqid_cookie.'wpqa_rate_'.$post_id] != "") {
				$found_user = true;
				$found_rate = $_COOKIE[$uniqid_cookie.'wpqa_rate_'.$post_id];
			}
			if ($user_id > 0 && isset($get_rates[$value_rate]) && is_array($get_rates[$value_rate])) {
				if ($key_rate == "sad" || $key_rate == "angry" || $key_rate == "confused" || $key_rate == "happy" || $key_rate == "love") {
					if (count($get_rates[$value_rate]) > 0) {
						$count_all[$key_rate]["count"] = count($get_rates[$value_rate]);
					}
					$counts_all += count($get_rates[$value_rate]);
				}
				if ($user_id > 0 && in_array($user_id,$get_rates[$value_rate])) {
					$found_user = true;
					if ($key_rate == "sad" || $key_rate == "angry" || $key_rate == "confused" || $key_rate == "happy" || $key_rate == "love") {
						$found_rate = $key_rate;
					}
				}else if (in_array(0,$get_rates[$value_rate])) {
					$update_meta = true;
					$get_rates[$value_rate] = wpqa_remove_item_by_value($get_rates[$value_rate],0);
				}
			}
		}
	}
	
	if (isset($update_meta)) {
		wpqa_update_meta($meta,$get_rates,$post_id);
		$get_rates = wpqa_get_meta($meta,$post_id);
	}
	if ($counts_all != $count) {
		wpqa_update_meta($meta."_count",$counts_all,$post_id);
		$count_rates = (int)wpqa_get_meta($meta."_count",$post_id);
		$count = ($count_rates > 0?$count_rates:0);
	}

	$result["number"]       = $count;
	$result["count"]        = wpqa_count_number($count);
	$result["count_female"] = wpqa_count_number($count_female);
	$result["count_male"]   = wpqa_count_number($count_male);
	$result["current_rate"] = (isset($found_rate)?$found_rate:'');
	$result["rate"]         = (isset($found_user) && isset($found_rate)?"rated":"unrate");

	return $result;
}?>