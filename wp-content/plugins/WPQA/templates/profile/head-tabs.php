<?php

/* @author    2codeThemes
*  @package   WPQA/templates/profile
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$author_widget = wpqa_options("author_widget");
$wpqa_user_title = wpqa_user_title();
$wpqa_user_title_2 = wpqa_user_title();
$type_slug = explode("-",$wpqa_user_title_2);
$wpqa_user_title_2 = $type_slug[0].(isset($type_slug[1])?'_'.$type_slug[1]:'');

include wpqa_get_template("profile-page-tabs.php","profile/");

if (!isset($profile_page_menu_available)) {
	if ($list_child == "li") {
		echo "<ul class='".(isset($menu_class)?$menu_class:'sub-menu')."'>";
	}
}

if ($author_widget != "on") {
	if ($list_child == "li") {?>
		<li class="menu-item<?php echo (!$wpqa_user_title?" active-tab":"")?>"><a href="<?php echo esc_url(wpqa_profile_url($wpqa_user_id))?>">
	<?php }else {?>
		<option<?php echo (!$wpqa_user_title?" selected='selected'":"")?> value="<?php echo esc_url(wpqa_profile_url($wpqa_user_id))?>">
	<?php }
			esc_html_e("About","wpqa");
	if ($list_child == "li") {?>
		</a></li>
	<?php }else {?>
		</option>
	<?php }
}
	
if (!isset($profile_page_menu_available) && isset($menu_profile_items) && is_array($menu_profile_items) && !empty($menu_profile_items)) {
	foreach ($menu_profile_items as $menu_key => $menu_value) {
		if (strpos($menu_value->url,'#wpqa-') !== false) {
			$tab_item = str_ireplace("#wpqa-","",$menu_value->url);
			$menu_profile_items[$menu_key]->wpqa_tab_item = $tab_item;
		}else {
			$menu_profile_items[$menu_key]->wpqa_tab_item = $menu_value->url;
			$tab_item = $menu_value->url;
		}
		if (isset($tab_item)) {
			if ($tab_item == "asked") {
				if ($ask_question_to_users == "on") {
					$tab_item_available = true;
				}else {
					unset($menu_profile_items[$menu_key]);
				}
			}else if ($tab_item == "asked_questions" || $tab_item == "my_asked") {
				if ($ask_question_to_users == "on" && wpqa_is_user_owner()) {
					$tab_item_available = true;
				}else {
					unset($menu_profile_items[$menu_key]);
				}
			}else if ($tab_item == "paid_questions") {
				if ($pay_ask == "on" && ($show_point_favorite == "on" || wpqa_is_user_owner())) {
					$tab_item_available = true;
				}else {
					unset($menu_profile_items[$menu_key]);
				}
			}else if ($tab_item == "favorites" || $tab_item == "followed" || $tab_item == "followers_questions" || $tab_item == "followers_answers" || $tab_item == "followers_posts" || $tab_item == "followers_comments") {
				if ($show_point_favorite == "on" || wpqa_is_user_owner()) {
					$tab_item_available = true;
				}else {
					unset($menu_profile_items[$menu_key]);
				}
			}
		}
	}
	foreach ($menu_profile_items as $menu_key => $menu_value) {
		$tab_item = $menu_value->wpqa_tab_item;
		if ($tab_item != "") {
			$selected = (($author_widget == "on"&& !$wpqa_user_title && isset($first_one) && $tab_item == $first_one) || $tab_item == $wpqa_user_title || $tab_item == $wpqa_user_title_2?true:"");
			$last_url = wpqa_profile_links($wpqa_user_id,$tab_item);
		}else {
			$last_url = $menu_value->url;
		}
		if ($last_url != "") {
			if ($list_child == "li") {?>
				<li class="menu-item<?php echo (isset($tab_item) && $tab_item != ""?" wpqa-".$tab_item:"").(isset($selected) && $selected == true?" active-tab":"")?>">
					<a href="<?php echo esc_url($last_url)?>">
			<?php }else {?>
				<option<?php echo (isset($selected) && $selected == true?" selected='selected'":"")?> value="<?php echo esc_url($last_url)?>">
			<?php }
			echo apply_filters('wpqa_menu_title',$menu_value->title,$menu_value,"profile_page_menu");
			include wpqa_get_template("menu-counts.php","profile/");
			if ($list_child == "li") {
					echo "</a>
				</li>";
			}else {
				echo "</option>";
			}
		}
	}
	if ($list_child == "li") {
		echo "</ul>";
	}
}?>