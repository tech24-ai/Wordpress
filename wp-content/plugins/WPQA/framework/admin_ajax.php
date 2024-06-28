<?php

/* @author    2codeThemes
*  @package   WPQA/framework
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Fetch options */
function wpqa_parse_str($string) {
	if ('' == $string) {
		return false;
	}
	$result = array();
	$pairs  = explode('&',$string);
	foreach ($pairs as $key => $pair) {
		parse_str($pair,$params);
		$k = key($params);
		if (!isset($result[$k])) {
			$result += $params;
		}else {
			if (is_array($result[$k]) && is_array($params[$k])) {
				$result[$k] = wpqa_array_merge_distinct($result[$k],$params[$k]);
			}
		}
	}

	return $result;
}
function wpqa_array_merge_distinct(array $array1,array $array2) {
	$merged = $array1;
	foreach ($array2 as $key => $value) {
		if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
			$merged[$key] = wpqa_array_merge_distinct($merged[$key],$value);
		}else if (is_numeric($key) && isset($merged[$key])) {
			$merged[] = $value;
		}else {
			$merged[$key] = $value;
		}
	}
	return $merged;
}
/* Update options */
function wpqa_update_options() {
	$user_id = get_current_user_id();
	if (is_super_admin($user_id)) {
		$_POST['data'] = stripslashes($_POST['data']);
		$option_settings = esc_html($_POST['option_settings']);
		$values = wpqa_parse_str($_POST['data']);
		$white_label_array = apply_filters("wpqa_white_label_array",array());
		$white_label_settings = false;
		$white_label_option = "";
		if (is_array($white_label_array) && !empty($white_label_array)) {
			foreach ($white_label_array as $value) {
				if ($option_settings == $value) {
					$white_label_settings = true;
					$white_label_option = $value;
				}
			}
		}
		if (!isset($values['saving_nonce']) || !wp_verify_nonce($values['saving_nonce'],'saving_nonce')) {
			echo 3;
		}else if ($option_settings == "styling") {
			do_action(wpqa_prefix_theme."_update_styling_options",$values);
			$setting_options = $values[wpqa_styling_options];
			$setting_options = apply_filters("wpqa_styling_options_values",$setting_options);
			/* Sidebar */
			$sidebars_widgets = get_option("sidebars_widgets");
			if (isset($setting_options["sidebars"]) && is_array($setting_options["sidebars"]) && !empty($setting_options["sidebars"])) {
				foreach ($setting_options["sidebars"] as $sidebar) {
					$sidebar_name = sanitize_title(esc_html($sidebar["name"]));
					$key = array($sidebar_name => (isset($sidebars_widgets[$sidebar_name]) && is_array($sidebars_widgets[$sidebar_name])?$sidebars_widgets[$sidebar_name]:array()));
					if ((is_array($sidebars_widgets) && empty($sidebars_widgets)) || !is_array($sidebars_widgets) || $sidebars_widgets == "") {
						$sidebars_widgets = $key;
					}else if (is_array($sidebars_widgets) && !in_array($key,$sidebars_widgets)) {
						$sidebars_widgets = array_merge($sidebars_widgets,$key);
					}
				}
			}
			update_option("sidebars_widgets",$sidebars_widgets);
			$wpqa_registered_sidebars = array();
			foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) {
				$wpqa_registered_sidebars[$sidebar['id']] = $sidebar['name'];
			}
			update_option("wpqa_registered_sidebars",$wpqa_registered_sidebars);
			update_option(wpqa_styling_options,$setting_options);
		}else if ($option_settings == "mobile") {
			do_action(wpqa_prefix_theme."_update_mobile_options",$values);
			$setting_options = $values[wpqa_mobile_options];
			$setting_options = apply_filters("wpqa_mobile_options_values",$setting_options);
			update_option(wpqa_mobile_options,$setting_options);
		}else if ($white_label_settings == true) {
			do_action(wpqa_prefix_theme."_update_".$white_label_option."_options",$values);
			$setting_options = $values[wpqa_prefix_theme."_".$white_label_option."_options"];
			$setting_options = apply_filters("wpqa_".$white_label_option."_options_values",$setting_options);
			update_option(wpqa_prefix_theme."_".$white_label_option."_options",$setting_options);
		}else {
			do_action(wpqa_prefix_theme."_update_options",$values);
			$setting_options = $values[wpqa_options];
			unset($setting_options['export_setting']);
			$setting_options = apply_filters(wpqa_prefix_theme."_options_values",$setting_options);
			/* Roles */
			if (isset($setting_options["roles"])) {
				foreach ($setting_options["roles"] as $value_roles) {
					$is_group = get_role($value_roles["id"]);
					if (isset($value_roles["new"]) && $value_roles["new"] == "new") {
						if (!isset($is_group)) {
							$is_group = add_role($value_roles["id"],ucfirst($value_roles["group"]),array('read' => false));
							$is_group->add_cap('new');
						}
					}
					if (isset($is_group)) {
						$roles_array = wpqa_roles_array();
						if (isset($roles_array) && !empty($roles_array)) {
							foreach ($roles_array as $roles_key => $roles_value) {
								if (isset($value_roles[$roles_key]) && $value_roles[$roles_key] == "on") {
									$is_group->add_cap($roles_key);
								}else {
									$is_group->remove_cap($roles_key);
								}
							}
						}
					}
				}
			}
			/* Schedules */
			if (isset($setting_options["schedules_time_hour"])) {
				$schedules_time_hour = get_option("schedules_time_hour");
				if ($setting_options["schedules_time_hour"] != $schedules_time_hour) {
					update_option("schedules_time_hour",$setting_options["schedules_time_hour"]);
					delete_option(wpqa_prefix_theme."_schedules_time");
				}
			}
			if (isset($setting_options["schedules_time_day"])) {
				$schedules_time_day = get_option("schedules_time_day");
				if ($setting_options["schedules_time_day"] != $schedules_time_day) {
					update_option("schedules_time_day",$setting_options["schedules_time_day"]);
					delete_option(wpqa_prefix_theme."_schedules_time");
				}
			}
			if (isset($setting_options["schedules_time_hour_post"])) {
				$schedules_time_hour_post = get_option("schedules_time_hour_post");
				if ($setting_options["schedules_time_hour_post"] != $schedules_time_hour_post) {
					update_option("schedules_time_hour_post",$setting_options["schedules_time_hour_post"]);
					delete_option(wpqa_prefix_theme."_schedules_time_post");
				}
			}
			if (isset($setting_options["schedules_time_day_post"])) {
				$schedules_time_day_post = get_option("schedules_time_day_post");
				if ($setting_options["schedules_time_day_post"] != $schedules_time_day_post) {
					update_option("schedules_time_day_post",$setting_options["schedules_time_day_post"]);
					delete_option(wpqa_prefix_theme."_schedules_time_post");
				}
			}
			if (isset($setting_options["activate_currencies"])) {
				$activate_currencies = get_option("activate_currencies");
				if ($setting_options["activate_currencies"] != $activate_currencies) {
					update_option("activate_currencies",$setting_options["activate_currencies"]);
					echo 2;
				}
			}
			if (isset($setting_options["question_schedules"]) && $setting_options["question_schedules"] != "on") {
				wp_clear_scheduled_hook("wpqa_scheduled_mails_daily");
				wp_clear_scheduled_hook("wpqa_scheduled_mails_weekly");
				wp_clear_scheduled_hook("wpqa_scheduled_mails_monthly");
			}
			if (isset($setting_options["post_schedules"]) && $setting_options["post_schedules"] != "on") {
				wp_clear_scheduled_hook("wpqa_scheduled_mails_daily_post");
				wp_clear_scheduled_hook("wpqa_scheduled_mails_weekly_post");
				wp_clear_scheduled_hook("wpqa_scheduled_mails_monthly_post");
			}
			if (isset($setting_options["way_sending_notifications_questions"]) && $setting_options["way_sending_notifications_questions"] != "cronjob") {
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_daily_question");
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicedaily_question");
			}
			if (isset($setting_options["way_sending_notifications_posts"]) && $setting_options["way_sending_notifications_posts"] != "cronjob") {
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_daily_post");
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicedaily_post");
			}
			if (isset($setting_options["way_sending_notifications_answers"]) && $setting_options["way_sending_notifications_answers"] != "cronjob") {
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_daily_answer");
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicedaily_answer");
			}
			if (isset($setting_options["way_sending_notifications_questions"]) && $setting_options["way_sending_notifications_questions"] != "cronjob" && isset($setting_options["way_sending_notifications_posts"]) && $setting_options["way_sending_notifications_posts"] != "cronjob" && isset($setting_options["way_sending_notifications_answers"]) && $setting_options["way_sending_notifications_answers"] != "cronjob") {
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_hourly");
				wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicehourly");
			}
			/* Payments */
			$pay_ask               = (isset($setting_options['pay_ask'])?$setting_options['pay_ask']:0);
			$payment_type_ask      = (isset($setting_options['payment_type_ask'])?$setting_options['payment_type_ask']:0);
			$pay_to_sticky         = (isset($setting_options['pay_to_sticky'])?$setting_options['pay_to_sticky']:0);
			$payment_type_sticky   = (isset($setting_options['payment_type_sticky'])?$setting_options['payment_type_sticky']:0);
			$subscriptions_payment = (isset($setting_options['subscriptions_payment'])?$setting_options['subscriptions_payment']:0);
			$buy_points_payment    = (isset($setting_options['buy_points_payment'])?$setting_options['buy_points_payment']:0);
			$pay_answer            = (isset($setting_options['pay_answer'])?$setting_options['pay_answer']:0);
			$payment_type_answer   = (isset($setting_options['payment_type_answer'])?$setting_options['payment_type_answer']:0);
			$currency_code         = (isset($setting_options['currency_code'])?$setting_options['currency_code']:"USD");
			$pay_to_anything       = apply_filters("wpqa_filter_pay_to_anything",false);
			if (($pay_ask == "on" && $payment_type_ask != "points") || ($pay_to_sticky == "on" && $payment_type_sticky != "points") || $subscriptions_payment == "on" || $buy_points_payment == "on" || ($pay_answer == "on" && $payment_type_answer != "points") || $pay_to_anything == true) {
				$payment_methods = (isset($setting_options['payment_methods'])?$setting_options['payment_methods']:array());
				$stripe_test = (isset($setting_options['stripe_test'])?$setting_options['stripe_test']:"");
				$secret_key = (isset($setting_options[($stripe_test == "on"?"test_":"")."secret_key"])?$setting_options[($stripe_test == "on"?"test_":"")."secret_key"]:"");
				if (isset($payment_methods["stripe"]["value"]) && $payment_methods["stripe"]["value"] == "stripe" && $secret_key != "") {
					require_once plugin_dir_path(dirname(__FILE__)).'payments/stripe/init.php';
					$stripe = new \Stripe\StripeClient($secret_key);
					if (isset($setting_options["coupons"]) && is_array($setting_options["coupons"]) && !empty($setting_options["coupons"])) {
						foreach ($setting_options["coupons"] as $key => $value) {
							$coupon_name = preg_replace('/[^a-zA-Z0-9._\-]/','',strtolower($value['coupon_name']));
							$coupon_amount = (int)$value['coupon_amount'];
							$coupon_type = $value['coupon_type'];
							$coupon_id = $coupon_amount.'_'.$coupon_name;
							try {
								$get_coupon = $stripe->coupons->retrieve($coupon_id);
							}catch ( \Stripe\Exception\CardException $e ) {
								$result_error_coupon = $e->getError()->message;
							}catch ( Exception $e ) {
								$result_error_coupon = $e->getMessage();
							}
							if (!isset($result_error_coupon) || (isset($result_error_coupon) && $result_error_coupon != "")) {
								if ($coupon_type == "percent") {
									$coupon_type = array('percent_off' => $coupon_amount);
								}else {
									$coupon_type = array('amount_off' => $coupon_amount);
								}
								$coupon_array = array(
									'duration' => 'once',//repeating
									'id'       => $coupon_id
								);
								try {
									$coupon = $stripe->coupons->create(array_merge($coupon_type,$coupon_array));
								}catch ( \Stripe\Exception\CardException $e ) {
									$result_error_coupon = $e->getError()->message;
								}catch ( Exception $e ) {
									$result_error_coupon = $e->getMessage();
								}
							}
						}
					}
				}
			}
			/* Register */
			if (isset($setting_options['activate_register']) && $setting_options['activate_register'] == "enabled") {
				update_option("users_can_register",true);
			}else {
				delete_option("users_can_register");
			}
			/* Old themes */
			if (isset($setting_options['old_themes']) && $setting_options['old_themes'] != "nothing" && $setting_options['old_themes'] != "") {
				$old_theme_name = $setting_options['old_themes'];
				$old_theme = get_option("old_".$old_theme_name);
				if ($old_theme != "done") {
					update_option("old_".$old_theme_name,"done");
					$wpqa_options = get_option($old_theme_name."_options");
					if (is_array($wpqa_options) && !empty($wpqa_options)) {
						$setting_options = $wpqa_options;
					}
					$wpqa_options = get_option($old_theme_name."_styling_options");
					if (is_array($wpqa_options) && !empty($wpqa_options)) {
						update_option(wpqa_styling_options,$wpqa_options);
					}
					$wpqa_options = get_option($old_theme_name."_mobile_options");
					if (is_array($wpqa_options) && !empty($wpqa_options)) {
						update_option(wpqa_mobile_options,$wpqa_options);
					}
					$white_label_array = apply_filters("wpqa_white_label_array",array());
					$white_label_settings = false;
					if (is_array($white_label_array) && !empty($white_label_array)) {
						foreach ($white_label_array as $value) {
							if ($option_settings == $value) {
								$wpqa_options = get_option($old_theme_name."_".$value."_options");
								if (is_array($wpqa_options) && !empty($wpqa_options)) {
									update_option(wpqa_prefix_theme."_".$value."_options",$wpqa_options);
								}
							}
						}
					}
					echo 2;
				}
			}
			/* Save */
			update_option(wpqa_options,$setting_options);
			update_option("FlushRewriteRules",true);
		}
	}
	die();
}
add_action('wp_ajax_wpqa_update_options','wpqa_update_options');
/* Import options */
function wpqa_import_options() {
	$user_id = get_current_user_id();
	if (is_super_admin($user_id)) {
		$saving_nonce = (isset($_POST["saving_nonce"])?esc_html($_POST["saving_nonce"]):"");
		if (!wp_verify_nonce($saving_nonce,'saving_nonce')) {
			echo 3;
		}else {
			$values = $_POST['data'];
			if ($values != "") {
				$data = wpqa_base_decode($values);
				$data = json_decode($data,true);
				$array_options = array(wpqa_options,wpqa_styling_options,wpqa_mobile_options,"sidebars");
				$array_options = apply_filters("wpqa_export_settings",$array_options);
				foreach ($array_options as $option) {
					if (isset($data[$option])) {
						update_option($option,$data[$option]);
					}else{
						delete_option($option);
					}
				}
				echo 2;
				update_option("FlushRewriteRules",true);
				die();
			}
			update_option("FlushRewriteRules",true);
		}
	}
	die();
}
add_action('wp_ajax_wpqa_import_options','wpqa_import_options');
/* Reset options */
function wpqa_reset_options() {
	$user_id = get_current_user_id();
	if (is_super_admin($user_id)) {
		$saving_nonce = (isset($_POST["saving_nonce"])?esc_html($_POST["saving_nonce"]):"");
		$option_settings = esc_html($_POST['option_settings']);
		if (!wp_verify_nonce($saving_nonce,'saving_nonce')) {
			echo 3;
		}else {
			$white_label_array = apply_filters("wpqa_white_label_array",array());
			$white_label_settings = false;
			$white_label_option = "";
			if (is_array($white_label_array) && !empty($white_label_array)) {
				foreach ($white_label_array as $value) {
					if ($option_settings == $value) {
						$white_label_settings = true;
						$white_label_option = $value;
					}
				}
			}
			if ($option_settings == "styling") {
				$options = wpqa_admin_setting_styling();
				$wpqa_options = wpqa_styling_options;
			}else if ($option_settings == "mobile") {
				$options = wpqa_admin_setting_mobile();
				$wpqa_options = wpqa_mobile_options;
			}else if ($white_label_settings == true && $white_label_option != "") {
				$options = wpqa_admin_option_setting("",$white_label_option);
				$wpqa_options = wpqa_prefix_theme."_".$white_label_option."_options";
			}else {
				$options = wpqa_admin_setting_options();
				$wpqa_options = wpqa_options;
			}
			foreach ($options as $option) {
				if (isset($option['id']) && isset($option['std'])) {
					$option_res[$option['id']] = $option['std'];
				}
			}
			update_option($wpqa_options,$option_res);
			update_option("FlushRewriteRules",true);
		}
	}
	die();
}
add_action('wp_ajax_wpqa_reset_options','wpqa_reset_options');
/* Delete role */
function wpqa_delete_role() {
	$roles_val = $_POST["roles_val"];
	if (get_role($roles_val)) {
		remove_role($roles_val);
	}
}
add_action('wp_ajax_wpqa_delete_role','wpqa_delete_role');
/* Admin live search */
function wpqa_admin_live_search() {
	$search_value = esc_html($_POST['search_value']);
	if ($search_value != "") {
		$search_value_ucfirst = ucfirst(esc_html($_POST['search_value']));
		$option_settings = esc_html($_POST['option_settings']);
		$white_label_array = apply_filters("wpqa_white_label_array",array());
		$white_label_settings = false;
		$white_label_option = "";
		if (is_array($white_label_array) && !empty($white_label_array)) {
			foreach ($white_label_array as $value) {
				if ($option_settings == $value) {
					$white_label_settings = true;
					$white_label_option = $value;
				}
			}
		}
		if ($option_settings == "styling") {
			$wpqa_option_settings = wpqa_admin_setting_styling();
		}else if ($option_settings == "mobile") {
			$wpqa_option_settings = wpqa_admin_setting_mobile();
		}else if ($white_label_settings == true && $white_label_option != "") {
			$wpqa_option_settings = wpqa_admin_option_setting($option_settings,$white_label_option);
		}else {
			$wpqa_option_settings = wpqa_admin_setting_options();
		}
		$k = 0;
		if (isset($wpqa_option_settings) && is_array($wpqa_option_settings)) {?>
			<ul>
				<?php foreach ($wpqa_option_settings as $key => $value) {
					if (isset($value["type"]) && $value["type"] != "content" && $value["type"] != "info" && $value["type"] != "heading" && $value["type"] != "heading-2" && $value['type'] != "heading-3" && ((isset($value["name"]) && $value["name"] != "" && (strpos($value["name"],$search_value) !== false || strpos($value["name"],$search_value_ucfirst) !== false)) || (isset($value["desc"]) && $value["desc"] != "" && (strpos($value["desc"],$search_value) !== false || strpos($value["desc"],$search_value_ucfirst) !== false)))) {
						$find_resluts = true;
						$k++;
						if ((isset($value["name"]) && $value["name"] != "" && (strpos($value["name"],$search_value) !== false || strpos($value["name"],$search_value_ucfirst) !== false))) {?>
							<li><a href="section-<?php echo esc_html($value["id"])?>"><?php echo str_ireplace($search_value,"<strong>".$search_value."</strong>",esc_html($value["name"]))?></a></li>
						<?php }else {?>
							<li><a href="section-<?php echo esc_html($value["id"])?>"><?php echo str_ireplace($search_value,"<strong>".$search_value."</strong>",esc_html($value["desc"]))?></a></li>
						<?php }
						if ($k == 10) {
							break;
						}
					}
				}
				if (!isset($find_resluts)) {?>
					<li><?php esc_html_e("Sorry, no results.","wpqa")?></li>
				<?php }?>
			</ul>
		<?php }
	}
	die();
}
add_action('wp_ajax_wpqa_admin_live_search','wpqa_admin_live_search');
/* Categories ajax */
function wpqa_categories_ajax() {
	$name = (isset($_POST["name"])?esc_html($_POST["name"]):"");
	$name_2 = (isset($_POST["name_2"])?esc_html($_POST["name_2"]):"");
	$tabs = (isset($_POST["tabs"])?esc_html($_POST["tabs"]):"");
	if ($tabs == "yes") {
		echo '<li><label class="selectit"><input value="on" type="checkbox" name="'.$name.'[show_all_categories]">'.esc_html__('Show All Categories',"wpqa").'</label></li>';
	}
	echo wpqa_categories_checklist_admin(array("name" => $name.$name_2,"id" => $name.$name_2));
	die();
}
add_action('wp_ajax_wpqa_categories_ajax','wpqa_categories_ajax');
/* Recreate menus */
function wpqa_recreate_menus() {
	$wpqa_custom_queries = get_option("wpqa_custom_queries");
	wp_delete_nav_menu("Profile Page Tabs");
	wp_delete_nav_menu("Header Profile Menu");
	$wpqa_custom_queries = wpqa_remove_item_by_value($wpqa_custom_queries,"profile_page_menu");
	$wpqa_custom_queries = wpqa_remove_item_by_value($wpqa_custom_queries,"header_profile_menu");
	update_option("wpqa_custom_queries",$wpqa_custom_queries);
	die();
}
add_action('wp_ajax_wpqa_recreate_menus','wpqa_recreate_menus');?>