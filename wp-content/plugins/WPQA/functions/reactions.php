<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Get reactions action */
add_action("wpqa_show_reactions","wpqa_action_show_reactions",1,3);
if (!function_exists('wpqa_action_show_reactions')) :
	function wpqa_action_show_reactions($style = '',$post_id = 0,$comment_d = 0) {
		echo wpqa_show_reactions($style,$post_id,$comment_d);
	}
endif;
/* Reactions */
if (!function_exists('wpqa_reactions')) :
	function wpqa_reactions($data = array()) {
		$mobile = (is_array($data) && !empty($data)?true:false);
		$data = (is_array($data) && !empty($data)?$data:$_POST);

		$get_current_user_id = get_current_user_id();
		
		$post_id = (isset($data["post_id"])?(int)$data["post_id"]:0);
		$comment_id = (isset($data["comment_id"])?(int)$data["comment_id"]:0);
		$type = esc_html($data["type"]);
		$like = (isset($data["like"])?esc_html($data["like"]):"");

		if ($comment_id > 0) {
			$get_comment = get_comment($comment_id);
			$post_id = (int)$get_comment->comment_post_ID;
		}

		$get_post = get_post($post_id);
		$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
		$user_id = $get_post->post_author;
		if ($comment_id > 0) {
			$user_id = $get_comment->user_id;
			$anonymously_user = get_comment_meta($comment_id,"anonymously_user",true);
		}

		$post_type = (isset($get_post->post_type)?$get_post->post_type:get_post_type($post_id));

		$gender_author = get_user_meta($get_current_user_id,'gender',true);
		$key_gender = "other";
		if ($gender_author == 1) {
			$key_gender = "male";
		}else if ($gender_author == 2) {
			$key_gender = "female";
		}

		$meta = "wpqa_reactions";
		
		$meta_like = "wpqa_reaction_like";
		$meta_love = "wpqa_reaction_love";
		$meta_hug = "wpqa_reaction_hug";
		$meta_haha = "wpqa_reaction_haha";
		$meta_wow = "wpqa_reaction_wow";
		$meta_sad = "wpqa_reaction_sad";
		$meta_angry = "wpqa_reaction_angry";

		$array_main_reaction = array(
			"main" => $meta,

			"like" => $meta_like,
			"love" => $meta_love,
			"hug" => $meta_hug,
			"haha" => $meta_haha,
			"wow" => $meta_wow,
			"sad" => $meta_sad,
			"angry" => $meta_angry,

			"gender_like" => $meta_like."_".$key_gender,
			"gender_love" => $meta_love."_".$key_gender,
			"gender_hug" => $meta_hug."_".$key_gender,
			"gender_haha" => $meta_haha."_".$key_gender,
			"gender_wow" => $meta_wow."_".$key_gender,
			"gender_sad" => $meta_sad."_".$key_gender,
			"gender_angry" => $meta_angry."_".$key_gender
		);

		$count_reaction_gender = array(
			$meta."_count",
			$meta."_count_".$key_gender,
		);

		$get_reactions = wpqa_get_meta($meta,$post_id,$comment_id);
		$get_reactions = (is_array($get_reactions) && !empty($get_reactions)?$get_reactions:array());

		$notifications_activities_type = ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type?wpqa_questions_type:$post_type);
		if ($comment_id > 0) {
			$notifications_activities_type = "comment";
			if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
				$notifications_activities_type = "answer";
			}
		}
		
		if (is_array($array_main_reaction) && !empty($array_main_reaction)) {
			foreach ($array_main_reaction as $key_reaction => $value_reaction) {
				if (isset($get_reactions[$value_reaction]) && is_array($get_reactions[$value_reaction]) && in_array($get_current_user_id,$get_reactions[$value_reaction])) {
					$found_user = true;
				}
			}
			foreach ($array_main_reaction as $key_reaction => $value_reaction) {
				if (isset($get_reactions[$value_reaction]) && is_array($get_reactions[$value_reaction]) && in_array($get_current_user_id,$get_reactions[$value_reaction])) {
					if ((isset($found_user) && $like == "main-react") || (isset($found_user) && $key_reaction != "main" && $like != "main-react" && $key_reaction != $type) || (!isset($found_user) && $like != "main-react" && $key_reaction == "main")) {
						$get_reactions[$value_reaction] = wpqa_remove_item_by_value($get_reactions[$value_reaction],$get_current_user_id);
					}
					if ($key_reaction == "main" && (!isset($found_user) || (isset($found_user) && $like == "main-react"))) {
						foreach ($count_reaction_gender as $value_reaction_gender) {
							$count_reactions = (int)wpqa_get_meta($value_reaction_gender,$post_id,$comment_id);
							$count_reactions--;
							wpqa_update_meta($value_reaction_gender,($count_reactions > 0?$count_reactions:0),$post_id,$comment_id);
						}
						wpqa_remove_for_you($get_current_user_id,$post_id);
						if ($get_current_user_id > 0) {
							wpqa_notifications_activities($get_current_user_id,"","",$post_id,($comment_id > 0?$comment_id:""),$notifications_activities_type."_remove_reaction","activities","",$notifications_activities_type);
						}
					}
				}else {
					if (((isset($found_user) && $like != "main-react") || !isset($found_user)) && ($key_reaction == "main" || $key_reaction == $type || $key_reaction == "gender_".$type)) {
						if ($get_current_user_id > 0) {
							if (empty($get_reactions[$value_reaction])) {
								$get_reactions[$value_reaction] = array($get_current_user_id);
							}else if (isset($get_reactions[$value_reaction]) && is_array($get_reactions[$value_reaction]) && !in_array($get_current_user_id,$get_reactions[$value_reaction])) {
								$get_reactions[$value_reaction] = array_merge($get_reactions[$value_reaction],array($get_current_user_id));
							}
						}
						if ($key_reaction == "main") {
							foreach ($count_reaction_gender as $value_reaction_gender) {
								$count_reactions = (int)wpqa_get_meta($value_reaction_gender,$post_id,$comment_id);
								$count_reactions++;
								wpqa_update_meta($value_reaction_gender,($count_reactions > 0?$count_reactions:0),$post_id,$comment_id);
							}
							wpqa_update_for_you($get_current_user_id,$post_id);
							if (($get_current_user_id > 0 && $user_id > 0 && $get_current_user_id != $user_id) || ($get_current_user_id > 0 && $anonymously_user > 0 && $get_current_user_id != $anonymously_user)) {
								wpqa_notifications_activities(($user_id > 0?$user_id:$anonymously_user),$get_current_user_id,"",$post_id,($comment_id > 0?$comment_id:""),$notifications_activities_type."_reaction_".$type,"notifications","",$notifications_activities_type);
							}
							if ($get_current_user_id > 0) {
								wpqa_notifications_activities($get_current_user_id,"","",$post_id,($comment_id > 0?$comment_id:""),$notifications_activities_type."_reaction_".$type,"activities","",$notifications_activities_type);
							}
						}
					}
				}
			}
			wpqa_update_meta($meta,$get_reactions,$post_id,$comment_id);
		}

		$result = wpqa_reaction_results($meta,$get_current_user_id,$post_id,$comment_id);

		if ($mobile == true) {
			return $result;
		}
		echo json_encode(apply_filters('wpqa_json_reactions',$result));
		die();
	}
endif;
add_action('wp_ajax_wpqa_reactions','wpqa_reactions');
add_action('wp_ajax_nopriv_wpqa_reactions','wpqa_reactions');
/* Show reactions */
function wpqa_show_reactions($style = '',$post_id = 0,$comment_id = 0) {
	$output = $reaction_small = '';
	$user_id = get_current_user_id();
	$meta = "wpqa_reactions";

	$result = wpqa_reaction_results($meta,$user_id,$post_id,$comment_id);
	$react = $result["react"];
	$count = $result["number"];
	$count_male = $result["count_male"];
	$count_female = $result["count_female"];
	$images = $result["image"];
	$reactions = $result["reactions"];
	$strong = $result["strong"];
	
	$output .= '<div class="react__area'.($style == 'style_2'?' react__area-layout2':'').($style == 'answers'?' react__area-layout3':'').'">';
		if ($style == 'style_2') {
			$output .= '<div class="d-flex align-items-center justify-content-center">
				<div class="reactions__count him-user">'.wpqa_count_number($count_male).'</div>';
		}
		$output .= '<div data-type="like" data-like="main-react" class="like__button reactions_action '.$react.($style == 'style_2'?' like__button__2':'').(is_user_logged_in()?'':' login-panel'.apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_login','')).'">';
			if ($react == "reacted" && $style != 'style_2') {
				$output .= $images;
			}else {
				if ($style == 'answers') {
					$output .= '<i class="icon-android-happy"></i><span class="like__button-text">'.esc_html__("React","wpqa").'</span>';
				}else {
					$output .= '<i class="icon-thumbsup"></i>';
				}
			}
		$output .= '</div>';
		if ($style == 'style_2') {
			$output .= '<div class="reactions__count her-user">'.wpqa_count_number($count_female).'</div>
			</div>';
		}
		
		if ($style != 'style_2') {
			$output .= '<div class="reaction-small'.($count > 0?" reaction-small-reacted":"").'">'.$reactions.'</div>';
		}
		$reaction_items = wpqa_options("reaction_items");
		if (!is_array($reaction_items) || (is_array($reaction_items) && empty($reaction_items))) {
			$reaction_items = array(
				"love"  => array("sort" => esc_html__('Love','wpqa'),"value" => "love"),
				"hug"   => array("sort" => esc_html__('Hug','wpqa'),"value" => "hug"),
				"haha"  => array("sort" => esc_html__('Haha','wpqa'),"value" => "haha"),
				"wow"   => array("sort" => esc_html__('Wow','wpqa'),"value" => "wow"),
				"sad"   => array("sort" => esc_html__('Sad','wpqa'),"value" => "sad"),
				"angry" => array("sort" => esc_html__('Angry','wpqa'),"value" => "angry"),
			);
		}
		if (is_user_logged_in()) {
			$output .= '<ul class="reactions__list list-unstyled mb-0">
				<li><div class="tooltip-n reactions_action" data-type="like" original-title="'.esc_attr__("Like","wpqa").'"><img width="40" height="40" src="'.plugin_dir_url(dirname(__FILE__)).'/images/reactions/like.png" alt="'.esc_attr__("Like","wpqa").'"></div></li>';
				if (is_array($reaction_items) && !empty($reaction_items)) {
					foreach ($reaction_items as $key => $value) {
						if (isset($value["sort"]) && isset($value["value"]) && $value["value"] == $key) {
							$output .= '<li><div class="tooltip-n reactions_action" data-type="'.$key.'" original-title="'.$value["sort"].'"><img width="40" height="40" src="'.plugin_dir_url(dirname(__FILE__)).'/images/reactions/'.$key.'.png" alt="'.$value["sort"].'"></div></li>';
						}
					}
				}
			$output .= '</ul>';
		}
	$output .= '</div>';
	if ($style == 'style_2') {
		$output .= '<div class="react__details react__details_2">
			<div class="reaction-small'.($count > 0?" reaction-small-reacted":"").'">'.$reactions.'</div>
			<strong class="reaction_strong">'.$strong.'</strong>
		</div>';
	}
	return $output;
}
/* Reaction results */
function wpqa_reaction_results($meta,$user_id = 0,$post_id = 0,$comment_id = 0) {
	$gender_author = get_user_meta($user_id,'gender',true);
	$key_gender = "other";
	if ($gender_author == 1) {
		$key_gender = "male";
	}else if ($gender_author == 2) {
		$key_gender = "female";
	}

	$meta_like = "wpqa_reaction_like";
	$meta_love = "wpqa_reaction_love";
	$meta_hug = "wpqa_reaction_hug";
	$meta_haha = "wpqa_reaction_haha";
	$meta_wow = "wpqa_reaction_wow";
	$meta_sad = "wpqa_reaction_sad";
	$meta_angry = "wpqa_reaction_angry";

	$array_main_reaction = array(
		"main" => $meta,

		"like" => $meta_like,
		"love" => $meta_love,
		"hug" => $meta_hug,
		"haha" => $meta_haha,
		"wow" => $meta_wow,
		"sad" => $meta_sad,
		"angry" => $meta_angry,

		"gender_like" => $meta_like."_".$key_gender,
		"gender_love" => $meta_love."_".$key_gender,
		"gender_hug" => $meta_hug."_".$key_gender,
		"gender_haha" => $meta_haha."_".$key_gender,
		"gender_wow" => $meta_wow."_".$key_gender,
		"gender_sad" => $meta_sad."_".$key_gender,
		"gender_angry" => $meta_angry."_".$key_gender
	);

	$count_reactions = (int)wpqa_get_meta($meta."_count",$post_id,$comment_id);
	$count = ($count_reactions > 0?$count_reactions:0);

	$count_reactions_female = (int)wpqa_get_meta($meta."_count_female",$post_id,$comment_id);
	$count_female = ($count_reactions_female > 0?$count_reactions_female:0);

	$count_reactions_male = (int)wpqa_get_meta($meta."_count_male",$post_id,$comment_id);
	$count_male = ($count_reactions_male > 0?$count_reactions_male:0);

	$get_reactions = wpqa_get_meta($meta,$post_id,$comment_id);
	$get_reactions = (is_array($get_reactions) && !empty($get_reactions)?$get_reactions:array());

	$count_all = array(
		"like" => array("name" => esc_attr__("Like","wpqa"),"count" => 0),
		"love" => array("name" => esc_attr__("Love","wpqa"),"count" => 0),
		"hug" => array("name" => esc_attr__("Hug","wpqa"),"count" => 0),
		"haha" => array("name" => esc_attr__("Haha","wpqa"),"count" => 0),
		"wow" => array("name" => esc_attr__("Wow","wpqa"),"count" => 0),
		"sad" => array("name" => esc_attr__("Sad","wpqa"),"count" => 0),
		"angry" => array("name" => esc_attr__("Angry","wpqa"),"count" => 0)
	);
	$counts_all = 0;
	if (is_array($array_main_reaction) && !empty($array_main_reaction)) {
		foreach ($array_main_reaction as $key_reaction => $value_reaction) {
			if (isset($get_reactions[$value_reaction]) && is_array($get_reactions[$value_reaction])) {
				if ($key_reaction == "like" || $key_reaction == "love" || $key_reaction == "hug" || $key_reaction == "haha" || $key_reaction == "wow" || $key_reaction == "sad" || $key_reaction == "angry") {
					if (count($get_reactions[$value_reaction]) > 0) {
						$count_all[$key_reaction]["count"] = count($get_reactions[$value_reaction]);
					}
					$counts_all += count($get_reactions[$value_reaction]);
				}
				if (in_array($user_id,$get_reactions[$value_reaction])) {
					$found_user = true;
					if ($key_reaction == "like" || $key_reaction == "love" || $key_reaction == "hug" || $key_reaction == "haha" || $key_reaction == "wow" || $key_reaction == "sad" || $key_reaction == "angry") {
						$found_reaction = $key_reaction;
					}
				}else if (in_array(0,$get_reactions[$value_reaction])) {
					$update_meta = true;
					$get_reactions[$value_reaction] = wpqa_remove_item_by_value($get_reactions[$value_reaction],0);
				}
			}
		}
	}
	if (is_array($count_all) && !empty($count_all)) {
		$counts = array_column($count_all,'count');
		array_multisort($counts,SORT_DESC,$count_all);
		$reactions = array_slice($count_all,0,3);
	}
	if (isset($update_meta)) {
		wpqa_update_meta($meta,$get_reactions,$post_id,$comment_id);
		$get_reactions = wpqa_get_meta($meta,$post_id,$comment_id);
	}
	if ($counts_all != $count) {
		wpqa_update_meta($meta."_count",$counts_all,$post_id,$comment_id);
		$count_reactions = (int)wpqa_get_meta($meta."_count",$post_id,$comment_id);
		$count = ($count_reactions > 0?$count_reactions:0);
	}

	$images = '';
	$reaction_keys = array();
	if (isset($reactions) && is_array($reactions) && !empty($reactions)) {
		if ($count > 0) {
			foreach ($reactions as $key => $value) {
				if ($value["count"] > 0) {
					$reaction_keys[] = $key;
					$images .= '<span><img width="16" height="16" alt="'.$value["name"].'" src="'.plugin_dir_url(dirname(__FILE__)).'/images/reactions/'.$key.'.png"></span>';
				}
			}
			$images .= '<span class="reactions_count">'.sprintf(_n("%s User","%s Users",$count,"wpqa"),$count).'</span>';
		}
	}

	$result["number"]        = $count;
	$result["count"]         = wpqa_count_number($count);
	$result["react_value"]   = wpqa_count_number($count).' '._n("User","Users",$count,"wpqa");
	$result["count_female"]  = wpqa_count_number($count_female);
	$result["count_male"]    = wpqa_count_number($count_male);
	$result["post"]          = '<i class="icon-thumbsup"></i>';
	$result["comment"]       = '<i class="icon-android-happy"></i><span class="like__button-text">'.esc_html__('React','wpqa').'</span>';
	$result["image"]         = (isset($found_reaction)?'<img width="40" height="40" src="'.plugin_dir_url(dirname(__FILE__)).'/images/reactions/'.$found_reaction.'.png">':'');
	$result["current_react"] = (isset($found_reaction)?$found_reaction:'');
	$result["strong"]        = ($count > 0?(isset($found_user) && isset($found_reaction)?($count > 1?sprintf(esc_html__("%sYou%s and %s others","wpqa"),'<span class="color-primary">','</span>',wpqa_count_number($count-1)):'<span class="color-primary">'.esc_html__("You","wpqa").'</span>'):sprintf(_n("%s User","%s Users",$count,"wpqa"),wpqa_count_number($count))):esc_html__("Be the first one react","wpqa"));
	$result["react"]         = (isset($found_user) && isset($found_reaction)?"reacted":"unreact");
	$result["reactions"]     = $images;
	$result["reaction_keys"] = $reaction_keys;

	return $result;
}?>