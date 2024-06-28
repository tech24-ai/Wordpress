<?php

/* @author    2codeThemes
*  @package   WPQA/templates/profile
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if (isset($header_profile_items) && is_array($header_profile_items) && !empty($header_profile_items) && isset($user_id)) {
	echo "<ul class='sub-menu'>";
		foreach ($header_profile_items as $menu_key => $menu_value) {
			if (strpos($menu_value->url,'#wpqa-') !== false) {
				$tab_item = str_ireplace("#wpqa-","",$menu_value->url);
				$header_profile_items[$menu_key]->wpqa_tab_item = $tab_item;
			}else {
				$header_profile_items[$menu_key]->wpqa_tab_item = $menu_value->url;
				$tab_item = $menu_value->url;
			}
			if (isset($tab_item)) {
				if ($tab_item == "asked" || $tab_item == "asked_questions" || $tab_item == "my_asked") {
					$ask_question_to_users = wpqa_options("ask_question_to_users");
					if ($ask_question_to_users == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "paid_questions") {
					$pay_ask = wpqa_options("pay_ask");
					if ($pay_ask == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "points") {
					if ($active_points == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "activities") {
					if ($active_activity_log == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "notifications") {
					if ($active_notifications == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "referrals") {
					if ($active_referral == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "subscriptions") {
					$subscriptions_payment = wpqa_options("subscriptions_payment");
					if ($subscriptions_payment == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "messages") {
					if ($active_message == "on") {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}else if ($tab_item == "pending-questions" || $tab_item == "pending-posts") {
					if (isset($user_moderator) && ($is_super_admin || ($user_moderator == "on" && $active_moderators == "on"))) {
						$tab_item_available = true;
					}else {
						unset($header_profile_items[$menu_key]);
					}
				}
			}
		}
		foreach ($header_profile_items as $menu_key => $menu_value) {
			$tab_item = $menu_value->wpqa_tab_item;
			if ($tab_item != "") {
				$tab_class = "";
				$last_url = wpqa_profile_links($user_id,$tab_item);
			}else {
				$last_url = $menu_value->url;
			}
			if (isset($last_url) && $last_url != "") {
				echo "<li class='menu-item".(isset($tab_item) && $tab_item != ""?" wpqa-".$tab_item:"").(isset($selected) && $selected == true?" active-tab":"")."'>
					<a".(isset($tab_class)?" class='".$tab_class."'":"")." href='".esc_url($last_url)."'>
					".apply_filters('wpqa_menu_title',$menu_value->title,$menu_value,"header_profile_menu");
					include wpqa_get_template("menu-counts.php","profile/");
				echo "</a>
				</li>";
			}
		}
	echo "</ul>";
}?>