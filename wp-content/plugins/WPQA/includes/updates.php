<?php

/* @author    2codeThemes
*  @package   WPQA/includes
*  @version   1.0
*/

add_action('wpqa_init','wpqa_update_plugin');
function wpqa_update_plugin() {
	$wpqa_custom_queries = get_option("wpqa_custom_queries");
	$wpqa_custom_queries = (is_array($wpqa_custom_queries) && !empty($wpqa_custom_queries)?$wpqa_custom_queries:array());
	/* Old emails */
	if (!isset($wpqa_custom_queries["wpqa_fixed_old_mail"])) {
		$wpqa_custom_queries["wpqa_fixed_old_mail"] = true;
		update_option("wpqa_custom_queries",$wpqa_custom_queries);
		$wpqa_options = get_option(wpqa_options);
		if ((isset($wpqa_options['email_template']) && $wpqa_options['email_template'] != "") || (isset($wpqa_options['email_template_to']) && $wpqa_options['email_template_to'] != "")) {
			$parse = parse_url(get_site_url());
			$whitelist = array(
				'127.0.0.1',
				'::1'
			);
			if (in_array($_SERVER['REMOTE_ADDR'],$whitelist) || $parse['host'] == "2code.info" || $parse['host'] == "2code.net") {
				$not_replace = true;
			}
			
			if (isset($wpqa_options['email_template']) && $wpqa_options['email_template'] != "" && !isset($not_replace)) {
				if (strpos($wpqa_options['email_template'],'@2code.info') !== false || strpos($wpqa_options['email_template'],'@2code.net') !== false) {
					$wpqa_options['email_template'] = "no_reply@".$parse['host'];
					$change_it = true;
				}
			}
			if (isset($wpqa_options['email_template_to']) && $wpqa_options['email_template_to'] != "" && !isset($not_replace)) {
				if (strpos($wpqa_options['email_template_to'],'@2code.info') !== false || strpos($wpqa_options['email_template_to'],'@2code.net') !== false || strpos($wpqa_options['email_template_to'],'2codethemes@') !== false || strpos($wpqa_options['email_template_to'],'vbegy.info@') !== false) {
					$wpqa_options['email_template_to'] = get_bloginfo("admin_email");
					$change_it = true;
				}
			}
			if (isset($change_it)) {
				update_option(wpqa_options,$wpqa_options);
			}
		}
	}
	/* wpqa_5_9_8 */
	if (!isset($wpqa_custom_queries["5_9_8_added_new_permissions"])) {
		$wpqa_custom_queries["5_9_8_added_new_permissions"] = true;
		update_option("wpqa_custom_queries",$wpqa_custom_queries);
		$wpqa_options = get_option(wpqa_options);
		$roles = wpqa_options_roles();
		$array = array("add_poll","ask_other_users","pay_to_user","pay_maximum");
		if (is_array($roles) && !empty($roles)) {
			foreach ($roles as $key => $role) {
				if ($key != "wpqa_under_review" && $key != "activation" && $key != "ban_group") {
					$is_group = get_role($key);
					foreach ($array as $permission) {
						if (isset($wpqa_options["roles"][$key])) {
							$wpqa_options["roles"][$key][$permission] = "on";
						}
						if (isset($is_group)) {
							$is_group->add_cap($permission);
						}
					}
				}
			}
		}
		update_option(wpqa_options,$wpqa_options);
	}
	/* wpqa_5_9_9 */
	if (!isset($wpqa_custom_queries["5_9_9_added_new_options"])) {
		$wpqa_custom_queries["5_9_9_added_new_options"] = true;
		update_option("wpqa_custom_queries",$wpqa_custom_queries);
		$wpqa_options = get_option(wpqa_options);
		update_option(wpqa_styling_options,$wpqa_options);
		update_option(wpqa_mobile_options,$wpqa_options);
	}
	/* wpqa_6_0_0 */
	if (!isset($wpqa_custom_queries["6_0_0_added_new_options"])) {
		$wpqa_options = get_option(wpqa_options);
		$wpqa_custom_queries["6_0_0_added_new_options"] = true;
		update_option("wpqa_custom_queries",$wpqa_custom_queries);
		if (isset($wpqa_options["payment_methodes"])) {
			$wpqa_options["payment_methods"] = $wpqa_options["payment_methodes"];
			update_option(wpqa_options,$wpqa_options);
		}
	}
	/* wpqa_6_1_1 */
	if (!isset($wpqa_custom_queries[wpqa_plugin_version."_added_new_options"])) {
		$wpqa_options = get_option(wpqa_options);
		$wpqa_custom_queries[wpqa_plugin_version."_added_new_options"] = true;
		update_option("wpqa_custom_queries",$wpqa_custom_queries);
		update_option(wpqa_plugin_version."_".wpqa_options,$wpqa_options);
		if (isset($wpqa_options["activate_header"])) {
			$wpqa_options["activate_header"] = "on";
			update_option(wpqa_options,$wpqa_options);
		}
	}
}?>