<?php

/* @author    2codeThemes
*  @package   WPQA/templates/profile
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$edit_profile_pages = array("edit","password","mails","privacy","financial","withdrawals","delete");
$edit_profile_items_4 = wpqa_options("edit_profile_items_4");
if (isset($edit_profile_items_4) && is_array($edit_profile_items_4)) {
	$p_count = 0;
	$edit_profile_items_4_keys = array_keys($edit_profile_items_4);
	while ($p_count < count($edit_profile_items_4)) {
		if (isset($edit_profile_items_4[$edit_profile_items_4_keys[$p_count]]["value"]) && $edit_profile_items_4[$edit_profile_items_4_keys[$p_count]]["value"] != "" && $edit_profile_items_4[$edit_profile_items_4_keys[$p_count]]["value"] != "0") {
			$profile_one_4 = $p_count;
			break;
		}
		$p_count++;
	}
}
if (!isset($profile_one_4)) {
	$edit_profile_pages = array_diff($edit_profile_pages,array("mails"));
}
$privacy_account = wpqa_options("privacy_account");
if ($privacy_account != "on") {
	$edit_profile_pages = array_diff($edit_profile_pages,array("privacy"));
}
$wpqa_user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
$pay_to_user = wpqa_pay_to_user($wpqa_user_id);
if ($pay_to_user != true) {
	$edit_profile_pages = array_diff($edit_profile_pages,array("financial","withdrawals"));
}
$delete_account = wpqa_options("delete_account");
if ($delete_account == "on") {
	$delete_account_groups = wpqa_options("delete_account_groups");
	$user_info = get_userdata($wpqa_user_id);
	$user_group = wpqa_get_user_group($user_info);
}
if ($delete_account != "on" || ($delete_account == "on" && is_array($delete_account_groups) && !in_array($user_group,$delete_account_groups))) {
	$edit_profile_pages = array_diff($edit_profile_pages,array("delete"));
}
$edit_profile_pages = apply_filters("wpqa_user_edit_profile_pages",$edit_profile_pages);
if (isset($edit_profile_pages) && is_array($edit_profile_pages) && !empty($edit_profile_pages)) {
	foreach ($edit_profile_pages as $key) {
		do_action("wpqa_action_user_edit_profile_pages",$edit_profile_pages,$key);
		if ($key == "edit") {
			$selected = (wpqa_is_user_edit_profile() || "edit" == wpqa_user_title()?true:"");
			$last_url = wpqa_edit_profile_permalink();
		}else if ($key == "password") {
			$selected = (wpqa_is_user_password_profile() || "password" == wpqa_user_title()?true:"");
			$last_url = wpqa_edit_password_permalink();
		}else if ($key == "privacy") {
			$selected = (wpqa_is_user_privacy_profile() || "privacy" == wpqa_user_title()?true:"");
			$last_url = wpqa_edit_privacy_permalink();
		}else if ($key == "withdrawals") {
			$selected = (wpqa_is_user_withdrawals_profile() || "withdrawals" == wpqa_user_title()?true:"");
			$last_url = wpqa_withdrawals_permalink();
		}else if ($key == "financial") {
			$selected = (wpqa_is_user_financial_profile() || "financial" == wpqa_user_title()?true:"");
			$last_url = wpqa_financial_permalink();
		}else if ($key == "mails") {
			$selected = (wpqa_is_user_mails_profile() || "mails" == wpqa_user_title()?true:"");
			$last_url = wpqa_edit_mails_permalink();
		}else if ($key == "delete") {
			$selected = (wpqa_is_user_delete_profile() || "delete" == wpqa_user_title()?true:"");
			$last_url = wpqa_edit_delete_permalink();
		}
		if (isset($last_url) && $last_url != "") {
			if ($list_child == "li") {?>
				<li class="menu-item menu-item-<?php echo esc_attr($key).(isset($selected) && $selected == true?" active-tab":"")?>">
					<a href="<?php echo esc_url($last_url)?>">
			<?php }else {?>
				<option<?php echo (isset($selected) && $selected == true?" selected='selected'":"")?> value="<?php echo esc_url($last_url)?>">
			<?php }
		}
		if ($key == "edit") {
			esc_html_e("Edit profile","wpqa");
		}else if ($key == "password") {
			esc_html_e("Change Password","wpqa");
		}else if ($key == "privacy") {
			esc_html_e("Privacy","wpqa");
		}else if ($key == "withdrawals") {
			esc_html_e("Withdrawals","wpqa");
		}else if ($key == "financial") {
			esc_html_e("Financial","wpqa");
		}else if ($key == "mails") {
			esc_html_e("Mail settings","wpqa");
		}else if ($key == "delete") {
			esc_html_e("Delete account","wpqa");
		}
		if (isset($last_url) && $last_url != "") {
			if ($list_child == "li") {?>
					</a>
				</li>
			<?php }else {?>
				</option>
			<?php }
			$last_url = "";
		}
	}
}?>