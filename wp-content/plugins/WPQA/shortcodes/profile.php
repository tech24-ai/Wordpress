<?php

/* @author    2codeThemes
*  @package   WPQA/shortcodes
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Edit profile */
if (!function_exists('wpqa_edit_profile')) :
	function wpqa_edit_profile($atts, $content = null) {
		$a = shortcode_atts( array(
		    'type'  => ''
		), $atts );
		$out = '';
		if (!is_user_logged_in()) {
			$activate_login = wpqa_options("activate_login");
			if ($activate_login != "disabled") {
				if ($a['type'] == "delete") {
					$out .= wpqa_login_message(esc_html__("Please login to delete account.","wpqa"),"info","block");
				}else {
					$out .= wpqa_login_message(esc_html__("Please login to edit profile.","wpqa"),"info","block");
				}
			}else {
				$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to edit profile.","wpqa").'.</p></div>';
			}
		}else {
			if (isset($_POST['wpqa_profile_nonce']) && wp_verify_nonce($_POST['wpqa_profile_nonce'],'wpqa_profile_nonce')) {
				$valid_form = true;
			}
			$user_id = get_current_user_id();
			$user_info = get_userdata($user_id);
			$profile_user_id = $user_info->ID;

			$show_user_login = apply_filters("wpqa_show_user_login",false,$user_id);
			if ($a['type'] != "delete") {
				$breadcrumbs = wpqa_options("breadcrumbs");

				$user_meta_avatar = wpqa_avatar_name();
				$user_meta_cover = wpqa_cover_name();

				$edit_profile_items_1 = wpqa_options("edit_profile_items_1");
				$edit_profile_items_2 = wpqa_options("edit_profile_items_2");
				$edit_profile_items_3 = wpqa_options("edit_profile_items_3");
				
				$first_name_required = wpqa_options("first_name_required");
				$last_name_required = wpqa_options("last_name_required");
				$display_name_required = wpqa_options("display_name_required");
				$profile_picture_required = wpqa_options("profile_picture_required");
				$profile_cover_required = wpqa_options("profile_cover_required");
				$country_required = wpqa_options("country_required");
				$city_required = wpqa_options("city_required");
				$phone_required = wpqa_options("phone_required");
				$gender_required = wpqa_options("gender_required");
				$age_required = wpqa_options("age_required");

				$url_required_profile = wpqa_options("url_required_profile");
				$profile_credential_required = wpqa_options("profile_credential_required");
				$question_schedules = wpqa_options("question_schedules");
				$question_schedules_groups = wpqa_options("question_schedules_groups");
				$post_schedules = wpqa_options("post_schedules");
				$post_schedules_groups = wpqa_options("post_schedules_groups");

				$send_email_and_notification_question = wpqa_options("send_email_and_notification_question");
				$send_email_new_question_value = "send_email_new_question";
				$send_email_question_groups_value = "send_email_question_groups";
				if ($send_email_and_notification_question == "both") {
					$send_email_new_question_value = "send_email_new_question_both";
					$send_email_question_groups_value = "send_email_question_groups_both";
				}
				$send_email_new_question = wpqa_options($send_email_new_question_value);
				$send_email_question_groups = wpqa_options($send_email_question_groups_value);
				
				$send_email_and_notification_post = wpqa_options("send_email_and_notification_post");
				$send_email_new_post_value = "send_email_new_post";
				$send_email_post_groups_value = "send_email_post_groups";
				if ($send_email_and_notification_post == "both") {
					$send_email_new_post_value = "send_email_new_post_both";
					$send_email_post_groups_value = "send_email_post_groups_both";
				}
				$send_email_new_post = wpqa_options($send_email_new_post_value);
				$send_email_post_groups = wpqa_options($send_email_post_groups_value);

				$custom_left_menu = wpqa_options("custom_left_menu");
			}
			$edit_profile_sections = array(
				array(
					'name'    => esc_html__('Basic Information','wpqa'),
					'value'   => 'basic',
					'default' => 'yes'
				),
				array(
					'name'    => esc_html__('About Me','wpqa'),
					'value'   => 'about',
					'default' => 'yes'
				),
				array(
					'name'    => esc_html__('Social Profiles','wpqa'),
					'value'   => 'social',
					'default' => 'yes'
				),
				array(
					'name'    => (has_discy()?esc_html__('Custom Left Menu With Categories','wpqa'):esc_html__('Custom Second Menu With Categories','wpqa')),
					'value'   => 'categories',
					'default' => 'yes'
				),
				array(
					'name'    => esc_html__('Mails Setting','wpqa'),
					'value'   => 'mails_setting',
					'default' => 'yes'
				),
				array(
					'name'    => esc_html__('Privacy','wpqa'),
					'value'   => 'privacy',
					'default' => 'yes'
				),
				array(
					'name'    => esc_html__('Financial','wpqa'),
					'value'   => 'financial',
					'default' => 'yes'
				),
				array(
					'name'    => esc_html__('Delete Account','wpqa'),
					'value'   => 'delete_account',
					'default' => 'yes'
				),
			);
			$edit_profile_sections = apply_filters("wpqa_profile_page_sections",$edit_profile_sections,$profile_user_id);
			$edit_profile_items_4  = wpqa_options("edit_profile_items_4");
			$edit_profile_items_5  = wpqa_options("edit_profile_items_5");
			$privacy_account       = wpqa_options("privacy_account");
			$delete_account        = wpqa_options("delete_account");
			$delete_account_groups = wpqa_options("delete_account_groups");
			$edit_profile_pages    = array("edit","password","mails","privacy","financial","withdrawals","delete");
			if ($privacy_account != "on") {
				$edit_profile_pages = array_diff($edit_profile_pages,array("privacy"));
			}
			
			$user_group = wpqa_get_user_group($user_info);
			$pay_to_user = wpqa_pay_to_user($profile_user_id,$user_info);
			if ($pay_to_user != true) {
				$edit_profile_pages = array_diff($edit_profile_pages,array("financial","withdrawals"));
			}
			if ($delete_account != "on" || ($delete_account == "on" && is_array($delete_account_groups) && !in_array($user_group,$delete_account_groups))) {
				$edit_profile_pages = array_diff($edit_profile_pages,array("delete"));
			}
			$edit_profile_pages = apply_filters("wpqa_user_edit_profile_pages",$edit_profile_pages);
			$out .= '<form class="edit-profile-form block-section-div wpqa_form wpqa-readonly'.apply_filters('wpqa_edit_profile_form_class',false,(isset($a['type'])?$a['type']:"")).'" method="post" enctype="multipart/form-data">';
				if ((has_himer() || has_knowly()) && ($a['type'] == "password" || $a['type'] == "mails" || $a['type'] == "privacy" || $a['type'] == "delete" || ($a['type'] != "password" && $a['type'] != "mails" && $a['type'] != "financial" && $a['type'] != "privacy" && $a['type'] != "delete"))) {
					$out .= '<div class="card-header d-flex align-items-center flex-wrap justify-content-between">
						<h2 class="card-title mb-0 d-flex align-items-center">
							<i class="icon-android-settings font-xl card-title__icon"></i>
							<span>'.esc_html__("Edit Profile","wpqa").'</span>
						</h2>
						<div>
							<a href="'.wpqa_edit_profile_permalink().'" class="btn btn__primary btn__semi__height mr-2">'.esc_html__("Edit Profile","wpqa").'</a>
							<a href="'.wpqa_edit_password_permalink().'" class="btn btn__secondary btn__semi__height">'.esc_html__("Change Password","wpqa").'</a>
						</div>
					</div>';
				}
            	$out .= apply_filters('wpqa_edit_profile_form','edit_profile');
				if ($a['type'] != "delete") {
					$profile_credential = get_the_author_meta('profile_credential',$profile_user_id);
					$url = get_the_author_meta('url',$profile_user_id);
					$twitter = get_the_author_meta('twitter',$profile_user_id);
					$facebook = get_the_author_meta('facebook',$profile_user_id);
					$tiktok = get_the_author_meta('tiktok',$profile_user_id);
					$youtube = get_the_author_meta('youtube',$profile_user_id);
					$vimeo = get_the_author_meta('vimeo',$profile_user_id);
					$linkedin = get_the_author_meta('linkedin',$profile_user_id);
					$instagram = get_the_author_meta('instagram',$profile_user_id);
					$pinterest = get_the_author_meta('pinterest',$profile_user_id);
					$country = get_the_author_meta('country',$profile_user_id);
					$city = get_the_author_meta('city',$profile_user_id);
					$age = get_the_author_meta('age',$profile_user_id);
					$phone = get_the_author_meta('phone',$profile_user_id);
					$gender = get_the_author_meta('gender',$profile_user_id);
					$display_name = get_the_author_meta('display_name',$profile_user_id);
					$show_point_favorite = get_the_author_meta('show_point_favorite',$profile_user_id);
					$question_schedules_user = get_the_author_meta('question_schedules',$profile_user_id);
					$post_schedules_user = get_the_author_meta('post_schedules',$profile_user_id);
					$received_email = get_the_author_meta('received_email',$profile_user_id);
					$received_email_post = get_the_author_meta('received_email_post',$profile_user_id);
					$active_message = wpqa_options("active_message");
					$received_message = get_the_author_meta('received_message',$profile_user_id);
					$unsubscribe_mails = get_the_author_meta('unsubscribe_mails',$profile_user_id);
					$new_payment_mail = get_the_author_meta('new_payment_mail',$profile_user_id);
					$send_message_mail = get_the_author_meta('send_message_mail',$profile_user_id);
					$answer_on_your_question = get_the_author_meta('answer_on_your_question',$profile_user_id);
					$answer_question_follow = get_the_author_meta('answer_question_follow',$profile_user_id);
					$notified_reply = get_the_author_meta('notified_reply',$profile_user_id);
					$notification_mention = get_the_author_meta('notification_mention',$profile_user_id);
					$your_avatar = get_the_author_meta($user_meta_avatar,$profile_user_id);
					$your_cover = get_the_author_meta($user_meta_cover,$profile_user_id);

					$categories_left_menu = get_the_author_meta("categories_left_menu",$profile_user_id);
				}
				$rand_e = rand(1,1000);
				if ($a['type'] != "password" && $a['type'] != "mails" && $a['type'] != "financial" && $a['type'] != "privacy" && $a['type'] != "delete") {
					$save_available = true;
				}
				$out .= '<div class="form-inputs clearfix">
					<div class="page-sections'.($a['type'] == "password" || $a['type'] == "mails" || $a['type'] == "financial" || $a['type'] == "privacy"?" wpqa_hide":"").'" id="edit-profile">';
						if ($a['type'] != "delete" && isset($edit_profile_sections) && is_array($edit_profile_sections)) {
							if (isset($edit_profile_items_1["names"]) && isset($edit_profile_items_1["names"]["value"]) && $edit_profile_items_1["names"]["value"] == "names") {
								$edit_profile_items_1["nickname"] = array("sort" => esc_html__("Nickname","wpqa"),"value" => "nickname");
								$edit_profile_items_1["first_name"] = array("sort" => esc_html__("First Name","wpqa"),"value" => "first_name");
								$edit_profile_items_1["last_name"] = array("sort" => esc_html__("Last Name","wpqa"),"value" => "last_name");
								$edit_profile_items_1["display_name"] = array("sort" => esc_html__("Display Name","wpqa"),"value" => "display_name");
							}
							if (!isset($edit_profile_items_1["email"])) {
								$edit_profile_items_1["email"] = array("sort" => esc_html__("E-Mail","wpqa"),"value" => "email");
							}
							foreach ($edit_profile_sections as $key_sections => $value_sections) {
								$out = apply_filters("wpqa_profile_page_section",$out,$edit_profile_sections,$key_sections,$value_sections,$profile_user_id);
								if (isset($value_sections["value"]) && $value_sections["value"] == "basic" && isset($edit_profile_items_1) && is_array($edit_profile_items_1)) {
									$out .= '<div class="page-section page-section-'.$value_sections["value"].'">
										<div class="page-wrap-content">
											<h2 class="post-title-2"><i class="icon-vcard"></i>'.esc_html__("Basic Information","wpqa").'</h2>';
											$out .= apply_filters('wpqa_edit_profile_before_email',false,$profile_user_id);
											foreach ($edit_profile_items_1 as $key_items_1 => $value_items_1) {
												$out = apply_filters("wpqa_edit_profile_sort",$out,"edit_profile_items_1",$edit_profile_items_1,$key_items_1,$value_items_1,"edit",$_POST,$profile_user_id,(isset($valid_form)?true:false),$user_info);
												$out .= wpqa_register_edit_fields($key_items_1,$value_items_1,"edit",$rand_e,$user_info,(isset($valid_form)?true:false));
											}
										$out .= '</div>
										<div class="clearfix"></div>
									</div><!-- End page-section -->';
								}else if (isset($value_sections["value"]) && $value_sections["value"] == "social" && isset($edit_profile_items_2) && !empty($edit_profile_items_2) && is_array($edit_profile_items_2)) {
									$p_count = 0;
									$edit_profile_items_2_keys = array_keys($edit_profile_items_2);
									while ($p_count < count($edit_profile_items_2)) {
										if (isset($edit_profile_items_2[$edit_profile_items_2_keys[$p_count]]["value"]) && $edit_profile_items_2[$edit_profile_items_2_keys[$p_count]]["value"] != "" && $edit_profile_items_2[$edit_profile_items_2_keys[$p_count]]["value"] != "0") {
											$profile_one_2 = $p_count;
											break;
										}
										$p_count++;
									}
									if (isset($profile_one_2)) {
										$out .= '<div class="page-section page-section-'.$value_sections["value"].'">
											<div class="page-wrap-content">
												<h2 class="post-title-2"><i class="icon-globe"></i>'.esc_html__("Social Profiles","wpqa").'</h2>
												<div class="wpqa_form_2">';
													foreach ($edit_profile_items_2 as $key_items_2 => $value_items_2) {
														$out = apply_filters("wpqa_edit_profile_social_sort",$out,"edit_profile_items_2",$edit_profile_items_2,$key_items_2,$value_items_2,"edit",$_POST,$profile_user_id,(isset($valid_form)?true:false));
														if ($key_items_2 == "facebook" && isset($value_items_2["value"]) && $value_items_2["value"] == "facebook") {
															$out .= '<p class="facebook_field">
																<label for="facebook_'.$rand_e.'">'.esc_html__("Facebook","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input class="form-control" readonly="readonly" type="text" name="facebook" id="facebook_'.$rand_e.'" value="'.esc_url(isset($_POST["facebook"]) && isset($valid_form)?$_POST["facebook"]:$facebook).'">
																<i class="icon-facebook"></i>
															</p>';
														}else if ($key_items_2 == "twitter" && isset($value_items_2["value"]) && $value_items_2["value"] == "twitter") {
															$out .= '<p class="twitter_field">
																<label for="twitter_'.$rand_e.'">'.esc_html__("Twitter","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input class="form-control" readonly="readonly" type="text" name="twitter" id="twitter_'.$rand_e.'" value="'.esc_url(isset($_POST["twitter"]) && isset($valid_form)?$_POST["twitter"]:$twitter).'">
																<i class="icon-twitter"></i>
															</p>';
														}else if ($key_items_2 == "tiktok" && isset($value_items_2["value"]) && $value_items_2["value"] == "tiktok") {
															$out .= '<p class="tiktok_field">
																<label for="tiktok_'.$rand_e.'">'.esc_html__("TikTok","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input class="form-control" readonly="readonly" type="text" name="tiktok" id="tiktok_'.$rand_e.'" value="'.esc_url(isset($_POST["tiktok"]) && isset($valid_form)?$_POST["tiktok"]:$tiktok).'">
																<i class="fab fa-tiktok"></i>
															</p>';
														}else if ($key_items_2 == "youtube" && isset($value_items_2["value"]) && $value_items_2["value"] == "youtube") {
															$out .= '<p class="youtube_field">
																<label for="youtube_'.$rand_e.'">'.esc_html__("Youtube","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input class="form-control" readonly="readonly" type="text" name="youtube" id="youtube_'.$rand_e.'" value="'.esc_url(isset($_POST["youtube"]) && isset($valid_form)?$_POST["youtube"]:$youtube).'">
																<i class="icon-play"></i>
															</p>';
														}else if ($key_items_2 == "vimeo" && isset($value_items_2["value"]) && $value_items_2["value"] == "vimeo") {
															$out .= '<p class="vimeo_field">
																<label for="vimeo_'.$rand_e.'">'.esc_html__("Vimeo","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input class="form-control" readonly="readonly" type="text" name="vimeo" id="vimeo_'.$rand_e.'" value="'.esc_url(isset($_POST["vimeo"]) && isset($valid_form)?$_POST["vimeo"]:$vimeo).'">
																<i class="icon-vimeo"></i>
															</p>';
														}else if ($key_items_2 == "linkedin" && isset($value_items_2["value"]) && $value_items_2["value"] == "linkedin") {
															$out .= '<p class="linkedin_field">
																<label for="linkedin_'.$rand_e.'">'.esc_html__("Linkedin","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input class="form-control" readonly="readonly" type="text" name="linkedin" id="linkedin_'.$rand_e.'" value="'.esc_url(isset($_POST["linkedin"]) && isset($valid_form)?$_POST["linkedin"]:$linkedin).'">
																<i class="icon-linkedin"></i>
															</p>';
														}else if ($key_items_2 == "instagram" && isset($value_items_2["value"]) && $value_items_2["value"] == "instagram") {
															$out .= '<p class="instagram_field">
																<label for="instagram_'.$rand_e.'">'.esc_html__("Instagram","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input class="form-control" readonly="readonly" type="text" name="instagram" id="instagram_'.$rand_e.'" value="'.esc_url(isset($_POST["instagram"]) && isset($valid_form)?$_POST["instagram"]:$instagram).'">
																<i class="icon-instagram"></i>
															</p>';
														}else if ($key_items_2 == "pinterest" && isset($value_items_2["value"]) && $value_items_2["value"] == "pinterest") {
															$out .= '<p class="pinterest_field">
																<label for="pinterest_'.$rand_e.'">'.esc_html__("Pinterest","wpqa").'  '.esc_html__("(Put the full URL)","wpqa").'</label>
																<input class="form-control" readonly="readonly" type="text" name="pinterest" id="pinterest_'.$rand_e.'" value="'.esc_url(isset($_POST["pinterest"]) && isset($valid_form)?$_POST["pinterest"]:$pinterest).'">
																<i class="icon-pinterest"></i>
															</p>';
														}
														$out = apply_filters("wpqa_edit_profile_social_sort_after",$out,"edit_profile_items_2",$edit_profile_items_2,$key_items_2,$value_items_2,"edit",$_POST,$profile_user_id,(isset($valid_form)?true:false));
													}
												$out .= '</div>'.apply_filters("wpqa_filter_after_social_profile",false).'
											</div>
											<div class="clearfix"></div>
										</div><!-- End page-section -->';
									}
								}else if (isset($value_sections["value"]) && $value_sections["value"] == "about" && isset($edit_profile_items_3) && is_array($edit_profile_items_3)) {
									$p_count = 0;
									$edit_profile_items_3_keys = array_keys($edit_profile_items_3);
									while ($p_count < count($edit_profile_items_3)) {
										if (isset($edit_profile_items_3[$edit_profile_items_3_keys[$p_count]]["value"]) && $edit_profile_items_3[$edit_profile_items_3_keys[$p_count]]["value"] != "" && $edit_profile_items_3[$edit_profile_items_3_keys[$p_count]]["value"] != "0") {
											$profile_one_3 = $p_count;
											break;
										}
										$p_count++;
									}
									if (isset($profile_one_3)) {
										$out .= '<div class="page-section page-section-'.$value_sections["value"].'">
											<div class="page-wrap-content">
												<h2 class="post-title-2"><i class="icon-graduation-cap"></i>'.esc_html__("About Me","wpqa").'</h2>';
												foreach ($edit_profile_items_3 as $key_items_3 => $value_items_3) {
													if ($key_items_3 == "profile_credential" && isset($value_items_3["value"]) && $value_items_3["value"] == "profile_credential") {
														$out .= '<p class="profile_credential_field">
															<label for="profile_credential_'.$rand_e.'">'.esc_html__("Add profile credential","wpqa").($profile_credential_required == "on"?'<span class="required">*</span>':'').'</label>
															<input class="form-control" readonly="readonly" type="text" name="profile_credential" id="profile_credential_'.$rand_e.'" value="'.esc_attr(isset($_POST["profile_credential"]) && isset($valid_form)?$_POST["profile_credential"]:$profile_credential).'">
															<i class="icon-info"></i>
														</p>';
													}else if ($key_items_3 == "website" && isset($value_items_3["value"]) && $value_items_3["value"] == "website") {
														$out .= '<p class="website_field">
															<label for="url_'.$rand_e.'">'.esc_html__("Website","wpqa").($url_required_profile == "on"?'<span class="required">*</span>':'').'</label>
															<input class="form-control" readonly="readonly" type="text" name="url" id="url_'.$rand_e.'" value="'.esc_url(isset($_POST["url"]) && isset($valid_form)?$_POST["url"]:$url).'">
															<i class="icon-link"></i>
														</p>';
													}else if ($key_items_3 == "bio" && isset($value_items_3["value"]) && $value_items_3["value"] == "bio") {
														$bio_editor = wpqa_options("bio_editor");
														$description_value = (isset($_POST["description"]) && isset($valid_form)?wpqa_esc_textarea($_POST["description"]):wpqa_esc_textarea($user_info->description));
														if ($bio_editor == "on") {
															$settings = array("textarea_name" => "description","media_buttons" => true,"textarea_rows" => 10);
															$settings = apply_filters('wpqa_description_editor_setting',$settings);
															ob_start();
															wp_editor($description_value,"description_".$rand_e,$settings);
															$editor_contents = ob_get_clean();
															$out .= '<div class="the-description wpqa_textarea the-textarea">'.$editor_contents.'</div>';
														}else {
															$out .= '<p class="bio_field">
																<label for="description_'.$rand_e.'">'.esc_html__("Professional Bio","wpqa").'</label>
																<textarea class="form-control" name="description" id="description_'.$rand_e.'" cols="58" rows="8">'.$description_value.'</textarea>
																<i class="icon-pencil"></i>
															</p>';
														}
													}else if ($key_items_3 == "private_pages" && isset($value_items_3["value"]) && $value_items_3["value"] == "private_pages") {
														$out .= '<p class="wpqa_checkbox_p show_point_favorite_field normal_label">
															<label for="show_point_favorite_'.$rand_e.'">
																<span class="wpqa_checkbox"><input type="checkbox" name="show_point_favorite" id="show_point_favorite_'.$rand_e.'" value="on" '.checked(esc_attr(isset($_POST["show_point_favorite"]) && isset($valid_form)?$_POST["show_point_favorite"]:(!empty($_POST) && empty($_POST["show_point_favorite"])?"":$show_point_favorite)),"on",false).'></span>
																<span class="wpqa_checkbox_span">'.esc_html__("Show your private pages for all the users?","wpqa").'</span><span> '.esc_html__("(Points, favorite and followed pages).","wpqa").'</span>
															</label>
														</p>';
													}else if ($key_items_3 == "received_message" && isset($value_items_3["value"]) && $value_items_3["value"] == "received_message" && $active_message == "on") {
														$out .= '<p class="wpqa_checkbox_p received_message_field normal_label">
															<label for="received_message_'.$rand_e.'">
																<span class="wpqa_checkbox"><input type="checkbox" name="received_message" id="received_message_'.$rand_e.'" value="on" '.checked(esc_attr(isset($_POST["received_message"]) && isset($valid_form)?$_POST["received_message"]:(!empty($_POST) && empty($_POST["received_message"])?"":$received_message)),"on",false).'></span>
																<span class="wpqa_checkbox_span">'.esc_html__("Do you like to receive message from other users?","wpqa").'</span>
															</label>
														</p>';
													}
												}
											$out .= '</div>
											<div class="clearfix"></div>
										</div><!-- End page-section -->';
									}
								}else if (isset($value_sections["value"]) && $value_sections["value"] == "categories" && $custom_left_menu == "on") {
									$exclude = apply_filters('wpqa_exclude_question_category',array());
									$dropdown_categories = wp_dropdown_categories(array_merge($exclude,array(
										'taxonomy'     => wpqa_question_categories,
									    'orderby'      => 'name',
									    'echo'         => 0,
									    'hide_empty'   => 0,
									    'hierarchical' => 1,
									    'id'           => "add_categories_left_menu",
									    'class'        => "form-control wpqa-custom-select",
									    'name'         => "",
									)));
									if ($dropdown_categories != "" && strpos($dropdown_categories,"<option") !== false) {
										$out .= '<div class="page-section page-section-'.$value_sections["value"].'">
											<div class="page-wrap-content">
												<h2 class="post-title-2"><i class="icon-folder"></i>'.esc_html__("Custom Categories","wpqa").'</h2>
												<p class="custom_categories_field">
													<span class="styled-select">'.$dropdown_categories.'</span>
													<i class="icon-folder"></i>
												</p>
												<div class="clearfix"></div>
												<a data-name="categories_left_menu" data-id="categories_left_menu_items" class="button-default-3 add_categories_left_menu btn btn__primary btn__semi__height">'.esc_html__("Add category","wpqa").'</a>
												<ul class="profile_items list-unstyled sorting-area ui-sortable" id="categories_left_menu_items">';
												if ((isset($_POST["categories_left_menu"]) && is_array($_POST["categories_left_menu"]) && !empty($_POST["categories_left_menu"]) && isset($valid_form)) || (is_array($categories_left_menu) && !empty($categories_left_menu))) {
													$categories_left_menu = (isset($_POST["categories_left_menu"]) && is_array($_POST["categories_left_menu"]) && !empty($_POST["categories_left_menu"]) && isset($valid_form)?$_POST["categories_left_menu"]:$categories_left_menu);
													foreach ($categories_left_menu as $key => $value) {
														$cat_id = (isset($value["value"]) && $value["value"] != ""?(int)$value["value"]:0);
														if ($cat_id > 0) {
															$get_term = get_term($cat_id,wpqa_question_categories);
															if (isset($get_term->name)) {
																$out .= '<li class="categories ui-sortable-handle" id="categories_left_menu_items_'.$cat_id.'">
																	<label>'.$get_term->name.'</label>
																	<input name="categories_left_menu[cat-'.$cat_id.'][value]" value="'.$cat_id.'" type="hidden">
																	<div>
																		<div class="del-item-li remove-answer"><i class="icon-cancel"></i></div>
																		<div class="move-poll-li ui-icon darg-icon"><i class="icon-menu"></i></div>
																	</div>
																</li>';
															}
														}
													}
												}
												$out .= '</ul>
											</div>
											<div class="clearfix"></div>
										</div><!-- End page-section -->';
									}
								}
							}
						}
					$out .= '</div><!-- End page-sections -->';

					if ($a['type'] == "financial" && isset($edit_profile_sections) && is_array($edit_profile_sections) && isset($edit_profile_items_5) && is_array($edit_profile_items_5)) {
						foreach ($edit_profile_sections as $key_sections => $value_sections) {
							if (isset($value_sections["value"]) && $value_sections["value"] == "financial") {
								$p_count = 0;
								$edit_profile_items_5_keys = array_keys($edit_profile_items_5);
								while ($p_count < count($edit_profile_items_5)) {
									if (isset($edit_profile_items_5[$edit_profile_items_5_keys[$p_count]]["value"]) && $edit_profile_items_5[$edit_profile_items_5_keys[$p_count]]["value"] != "" && $edit_profile_items_5[$edit_profile_items_5_keys[$p_count]]["value"] != "0") {
										$profile_one_5 = $p_count;
										break;
									}
									$p_count++;
								}
								if (isset($profile_one_5)) {
									if ($pay_to_user == true) {
										$save_available = true;
										$out .= '<div class="page-sections" id="financial-profile">
											<div class="payment_content page-section page-section-'.$value_sections["value"].'">
												<div class="financial_payments_div">
													<h2 class="post-title-2"><i class="icon-vcard"></i>'.esc_html__("Financial Payments","wpqa").'</h2>';
													$financial_payments = get_the_author_meta('financial_payments',$profile_user_id);
													$last_financial_payments = (isset($_POST["financial_payments"]) && isset($valid_form) && $_POST["financial_payments"] != ""?esc_html($_POST["financial_payments"]):($financial_payments != ""?esc_html($financial_payments):$edit_profile_items_5_keys[$profile_one_5]));
													$out .= '<div class="financial_payments">
														<p class="financial_payments_field wpqa_radio_p"><label>'.esc_html__("Kindly choose your favorite Financial Payments way","wpqa").'<span class="required">*</span></label></p>
														<div class="wpqa_radio_div">';
															foreach ($edit_profile_items_5 as $key_edit_profile_items_5 => $value_edit_profile_items_5) {
																if (isset($value_edit_profile_items_5["value"]) && $value_edit_profile_items_5["value"] == "paypal") {
																	$out .= '<p>
																		<span class="wpqa_radio"><input id="financial_payments_paypal_'.$rand_e.'" name="financial_payments" type="radio" value="paypal"'.($last_financial_payments == "paypal"?' checked="checked"':'').'></span>
																		<label for="financial_payments_paypal_'.$rand_e.'">'.esc_html__("PayPal","wpqa").'</label>
																	</p>';
																}else if (isset($value_edit_profile_items_5["value"]) && $value_edit_profile_items_5["value"] == "payoneer") {
																	$out .= '<p>
																		<span class="wpqa_radio"><input id="financial_payments_payoneer_'.$rand_e.'" name="financial_payments" type="radio" value="payoneer"'.($last_financial_payments == "payoneer"?' checked="checked"':'').'></span>
																		<label for="financial_payments_payoneer_'.$rand_e.'">'.esc_html__("Payoneer","wpqa").'</label>
																	</p>';
																}else if (isset($value_edit_profile_items_5["value"]) && $value_edit_profile_items_5["value"] == "bank") {
																	$out .= '<p>
																		<span class="wpqa_radio"><input id="financial_payments_bank_'.$rand_e.'" name="financial_payments" type="radio" value="bank"'.($last_financial_payments == "bank"?' checked="checked"':'').'></span>
																		<label for="financial_payments_bank_'.$rand_e.'">'.esc_html__("Bank Transfer","wpqa").'</label>
																	</p>';
																}else if (isset($value_edit_profile_items_5["value"]) && $value_edit_profile_items_5["value"] == "crypto") {
																	$out .= '<p>
																		<span class="wpqa_radio"><input id="financial_payments_crypto_'.$rand_e.'" name="financial_payments" type="radio" value="crypto"'.($last_financial_payments == "crypto"?' checked="checked"':'').'></span>
																		<label for="financial_payments_crypto_'.$rand_e.'">'.esc_html__("Cryptocurrency","wpqa").'</label>
																	</p>';
																}
															}
															$out .= '<div class="clearfix"></div>
														</div>
													</div>
												</div>';
												foreach ($edit_profile_items_5 as $key_items_5 => $value_items_5) {
													if ($key_items_5 == "paypal" && isset($value_items_5["value"]) && $value_items_5["value"] == "paypal") {
														$paypal_email = get_the_author_meta('paypal_email',$profile_user_id);
														$out .= '<div class="financial_payments_forms paypal_form'.($last_financial_payments == "paypal"?"":" wpqa_hide").'">
															<h2 class="post-title-2"><i class="icon-paypal"></i>'.esc_html__("PayPal Information","wpqa").'</h2>
															<p class="paypal_email_field normal_label">
																<label for="paypal_email_'.$rand_e.'">'.esc_html__("PayPal E-Mail","wpqa").'<span class="required">*</span></label>
																<input class="form-control" type="text" name="paypal_email" id="paypal_email_'.$rand_e.'" value="'.(isset($_POST["paypal_email"]) && isset($valid_form)?esc_attr($_POST["paypal_email"]):$paypal_email).'">
																<i class="icon-mail"></i>
															</p>
														</div>';
													}else if ($key_items_5 == "payoneer" && isset($value_items_5["value"]) && $value_items_5["value"] == "payoneer") {
														$payoneer_email = get_the_author_meta('payoneer_email',$profile_user_id);
														$out .= '<div class="financial_payments_forms payoneer_form'.($last_financial_payments == "payoneer"?"":" wpqa_hide").'">
															<h2 class="post-title-2"><i class="icon-credit-card"></i>'.esc_html__("Payoneer Information","wpqa").'</h2>
															<p class="payoneer_email_field normal_label">
																<label for="payoneer_email_'.$rand_e.'">'.esc_html__("Payoneer E-Mail","wpqa").'<span class="required">*</span></label>
																<input class="form-control" type="text" name="payoneer_email" id="payoneer_email_'.$rand_e.'" value="'.(isset($_POST["payoneer_email"]) && isset($valid_form)?esc_attr($_POST["payoneer_email"]):$payoneer_email).'">
																<i class="icon-mail"></i>
															</p>
														</div>';
													}else if ($key_items_5 == "bank" && isset($value_items_5["value"]) && $value_items_5["value"] == "bank") {
														$bank_account_holder = get_the_author_meta('bank_account_holder',$profile_user_id);
														$bank_your_address = get_the_author_meta('bank_your_address',$profile_user_id);
														$bank_name = get_the_author_meta('bank_name',$profile_user_id);
														$bank_address = get_the_author_meta('bank_address',$profile_user_id);
														$bank_swift_iban = get_the_author_meta('bank_swift_iban',$profile_user_id);
														$bank_account_number = get_the_author_meta('bank_account_number',$profile_user_id);
														$bank_extra_note = get_the_author_meta('bank_extra_note',$profile_user_id);
														$out .= '<div class="financial_payments_forms bank_form'.($last_financial_payments == "bank"?"":" wpqa_hide").'">
															<h2 class="post-title-2"><i class="icon-briefcase"></i>'.esc_html__("Bank Transfer","wpqa").'</h2>
															<strong>'.esc_html__("Account Holder","wpqa").'</strong>
															<p class="bank_account_holder_field normal_label">
																<label for="bank_account_holder_'.$rand_e.'">'.esc_html__("Name of the Account Holder","wpqa").'<span class="required">*</span></label>
																<input class="form-control" type="text" name="bank_account_holder" id="bank_account_holder_'.$rand_e.'" value="'.(isset($_POST["bank_account_holder"]) && isset($valid_form)?esc_attr($_POST["bank_account_holder"]):$bank_account_holder).'">
																<i class="icon-user"></i>
															</p>
															<p class="bank_your_address_field normal_label">
																<label for="bank_your_address_'.$rand_e.'">'.esc_html__("Your Address","wpqa").'<span class="required">*</span></label>
																<input class="form-control" type="text" name="bank_your_address" id="bank_your_address_'.$rand_e.'" value="'.(isset($_POST["bank_your_address"]) && isset($valid_form)?esc_attr($_POST["bank_your_address"]):$bank_your_address).'">
																<i class="icon-map"></i>
															</p>
															<strong>'.esc_html__("Bank Information","wpqa").'</strong>
															<p class="bank_name_field normal_label">
																<label for="bank_name_'.$rand_e.'">'.esc_html__("Bank Name","wpqa").'<span class="required">*</span></label>
																<input class="form-control" type="text" name="bank_name" id="bank_name_'.$rand_e.'" value="'.(isset($_POST["bank_name"]) && isset($valid_form)?esc_attr($_POST["bank_name"]):$bank_name).'">
																<i class="icon-flag"></i>
															</p>
															<p class="bank_address_field normal_label">
																<label for="bank_address_'.$rand_e.'">'.esc_html__("Bank Address","wpqa").'<span class="required">*</span></label>
																<input class="form-control" type="text" name="bank_address" id="bank_address_'.$rand_e.'" value="'.(isset($_POST["bank_address"]) && isset($valid_form)?esc_attr($_POST["bank_address"]):$bank_address).'">
																<i class="icon-location"></i>
															</p>
															<p class="bank_swift_iban_field normal_label">
																<label for="bank_swift_iban_'.$rand_e.'">'.esc_html__("SWIFT/IBAN Code","wpqa").'<span class="required">*</span></label>
																<input class="form-control" type="text" name="bank_swift_iban" id="bank_swift_iban_'.$rand_e.'" value="'.(isset($_POST["bank_swift_iban"]) && isset($valid_form)?esc_attr($_POST["bank_swift_iban"]):$bank_swift_iban).'">
																<i class="icon-pencil"></i>
															</p>
															<p class="bank_account_number_field normal_label">
																<label for="bank_account_number_'.$rand_e.'">'.esc_html__("Account Number","wpqa").'<span class="required">*</span></label>
																<input class="form-control" type="text" name="bank_account_number" id="bank_account_number_'.$rand_e.'" value="'.(isset($_POST["bank_account_number"]) && isset($valid_form)?esc_attr($_POST["bank_account_number"]):$bank_account_number).'">
																<i class="icon-credit-card"></i>
															</p>
															<p class="bank_extra_note_field normal_label">
																<label for="bank_extra_note_'.$rand_e.'">'.esc_html__("Extra note","wpqa").'</label>
																<textarea class="form-control" name="bank_extra_note" id="bank_extra_note_'.$rand_e.'" rows="8">'.(isset($_POST["bank_extra_note"]) && isset($valid_form)?esc_attr($_POST["bank_extra_note"]):$bank_extra_note).'</textarea>
																<i class="icon-pencil"></i>
															</p>
														</div>';
													}else if ($key_items_5 == "crypto" && isset($value_items_5["value"]) && $value_items_5["value"] == "crypto") {
														$icon = (wpqa_options("active_awesome") == "on"?"fab fa-btc":"icon-vkontakte");
														$crypto_token_name = get_the_author_meta('crypto_token_name',$profile_user_id);
														$crypto_wallet_address = get_the_author_meta('crypto_wallet_address',$profile_user_id);
														$out .= '<div class="financial_payments_forms crypto_form'.($last_financial_payments == "crypto"?"":" wpqa_hide").'">
															<h2 class="post-title-2"><i class="'.$icon.'"></i>'.esc_html__("Cryptocurrency","wpqa").'</h2>
															<p class="crypto_token_name_field normal_label">
																<label for="crypto_token_name_'.$rand_e.'">'.esc_html__("Coin/Token Name","wpqa").'<span class="required">*</span></label>
																<input class="form-control" type="text" name="crypto_token_name" id="crypto_token_name_'.$rand_e.'" value="'.(isset($_POST["crypto_token_name"]) && isset($valid_form)?esc_attr($_POST["crypto_token_name"]):$crypto_token_name).'">
																<i class="icon-pencil"></i>
															</p>
															<p class="crypto_wallet_address_field normal_label">
																<label for="crypto_wallet_address_field_'.$rand_e.'">'.esc_html__("Wallet Address","wpqa").'<span class="required">*</span></label>
																<input class="form-control" type="text" name="crypto_wallet_address" id="crypto_wallet_address_'.$rand_e.'" value="'.(isset($_POST["crypto_wallet_address"]) && isset($valid_form)?esc_attr($_POST["crypto_wallet_address"]):$crypto_wallet_address).'">
																<i class="icon-credit-card"></i>
															</p>
														</div>';
													}
												}
											$out .= '</div><!-- End page-section -->
										</div><!-- End page-sections -->';
									}else {
										$out .= '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, this page is not available.","wpqa").'</p></div>';
									}
								}
							}
						}
					}

					if ($a['type'] == "mails" && isset($edit_profile_sections) && is_array($edit_profile_sections) && isset($edit_profile_items_4) && is_array($edit_profile_items_4)) {
						$p_count = 0;
						$edit_profile_items_4_keys = array_keys($edit_profile_items_4);
						while ($p_count < count($edit_profile_items_4)) {
							if (isset($edit_profile_items_4[$edit_profile_items_4_keys[$p_count]]["value"]) && $edit_profile_items_4[$edit_profile_items_4_keys[$p_count]]["value"] != "" && $edit_profile_items_4[$edit_profile_items_4_keys[$p_count]]["value"] != "0") {
								$profile_one_4 = $p_count;
								break;
							}
							$p_count++;
						}
						if (isset($profile_one_4)) {
							$save_available = true;
						}
						foreach ($edit_profile_sections as $key_sections => $value_sections) {
							if (isset($value_sections["value"]) && $value_sections["value"] == "mails_setting") {
								$unsubscribe_mails_value = (esc_html(isset($_POST["unsubscribe_mails"]) && isset($valid_form)?$_POST["unsubscribe_mails"]:(!empty($_POST) && empty($_POST["unsubscribe_mails"])?"":$unsubscribe_mails)));
								if (isset($profile_one_4)) {
									$payment_available = wpqa_payment_available();
									$out .= '<div class="page-sections" id="mails-profile">
										<div class="page-section page-section-'.$value_sections["value"].'">
											<div class="page-wrap-content">
												<h2 class="post-title-2"><i class="icon-mail"></i>'.esc_html__("Mail Settings","wpqa").'</h2>';
												foreach ($edit_profile_items_4 as $key_items_4 => $value_items_4) {
													if ($key_items_4 == "question_schedules" && isset($value_items_4["value"]) && $value_items_4["value"] == "question_schedules" && $question_schedules == "on" && is_array($question_schedules_groups) && in_array($user_group,$question_schedules_groups)) {
														$out .= '<p class="wpqa_checkbox_p question_schedules_field normal_label'.($unsubscribe_mails_value == "on"?" wpqa_hide":"").'">
															<label for="question_schedules_'.$rand_e.'">
																<span class="wpqa_checkbox"><input type="checkbox" name="question_schedules" id="question_schedules_'.$rand_e.'" value="on" '.checked(esc_attr(isset($_POST["question_schedules"]) && isset($valid_form)?$_POST["question_schedules"]:(!empty($_POST) && empty($_POST["question_schedules"])?"":$question_schedules_user)),"on",false).'></span>
																<span class="wpqa_checkbox_span">'.esc_html__("Would you like to receive scheduled mails for the recent questions?","wpqa").'</span>
															</label>
														</p>';
													}else if ($key_items_4 == "send_emails" && isset($value_items_4["value"]) && $value_items_4["value"] == "send_emails" && $send_email_new_question == "on" && is_array($send_email_question_groups) && in_array($user_group,$send_email_question_groups)) {
														$out .= '<p class="wpqa_checkbox_p received_email_field normal_label'.($unsubscribe_mails_value == "on"?" wpqa_hide":"").'">
															<label for="received_email_'.$rand_e.'">
																<span class="wpqa_checkbox"><input type="checkbox" name="received_email" id="received_email_'.$rand_e.'" value="on" '.checked(esc_attr(isset($_POST["received_email"]) && isset($valid_form)?$_POST["received_email"]:(!empty($_POST) && empty($_POST["received_email"])?"":$received_email)),"on",false).'></span>
																<span class="wpqa_checkbox_span">'.esc_html__("Would you like to receive an email when new questions are added?","wpqa").'</span>
															</label>
														</p>';
													}else if ($key_items_4 == "post_schedules" && isset($value_items_4["value"]) && $value_items_4["value"] == "post_schedules" && $post_schedules == "on" && is_array($post_schedules_groups) && in_array($user_group,$post_schedules_groups)) {
														$out .= '<p class="wpqa_checkbox_p post_schedules_field normal_label'.($unsubscribe_mails_value == "on"?" wpqa_hide":"").'">
															<label for="post_schedules_'.$rand_e.'">
																<span class="wpqa_checkbox"><input type="checkbox" name="post_schedules" id="post_schedules_'.$rand_e.'" value="on" '.checked(esc_attr(isset($_POST["post_schedules"]) && isset($valid_form)?$_POST["post_schedules"]:(!empty($_POST) && empty($_POST["post_schedules"])?"":$post_schedules_user)),"on",false).'></span>
																<span class="wpqa_checkbox_span">'.esc_html__("Would you like to receive scheduled mails for the recent posts?","wpqa").'</span>
															</label>
														</p>';
													}else if ($key_items_4 == "send_emails_post" && isset($value_items_4["value"]) && $value_items_4["value"] == "send_emails_post" && $send_email_new_post == "on" && is_array($send_email_post_groups) && in_array($user_group,$send_email_post_groups)) {
														$out .= '<p class="wpqa_checkbox_p received_email_post_field normal_label'.($unsubscribe_mails_value == "on"?" wpqa_hide":"").'">
															<label for="received_email_post_'.$rand_e.'">
																<span class="wpqa_checkbox"><input type="checkbox" name="received_email_post" id="received_email_post_'.$rand_e.'" value="on" '.checked(esc_attr(isset($_POST["received_email_post"]) && isset($valid_form)?$_POST["received_email_post"]:(!empty($_POST) && empty($_POST["received_email_post"])?"":$received_email_post)),"on",false).'></span>
																<span class="wpqa_checkbox_span">'.esc_html__("Would you like to receive an email when new posts are added?","wpqa").'</span>
															</label>
														</p>';
													}else if ($key_items_4 == "new_payment_mail" && isset($value_items_4["value"]) && $value_items_4["value"] == "new_payment_mail" && $payment_available == true) {
														$out .= '<p class="wpqa_checkbox_p new_payment_mail_field normal_label'.($unsubscribe_mails_value == "on"?" wpqa_hide":"").'">
															<label for="new_payment_mail_'.$rand_e.'">
																<span class="wpqa_checkbox"><input type="checkbox" name="new_payment_mail" class="new_payment_mail" id="new_payment_mail_'.$rand_e.'" value="on" '.checked(esc_attr(isset($_POST["new_payment_mail"]) && isset($valid_form)?$_POST["new_payment_mail"]:(!empty($_POST) && empty($_POST["new_payment_mail"])?"":$new_payment_mail)),"on",false).'></span>
																<span class="wpqa_checkbox_span">'.esc_html__("Would you like to receive an email for the new payments?","wpqa").'</span>
															</label>
														</p>';
													}else if ($key_items_4 == "send_message_mail" && isset($value_items_4["value"]) && $value_items_4["value"] == "send_message_mail" && $active_message == "on") {
														$out .= '<p class="wpqa_checkbox_p send_message_mail_field normal_label'.($unsubscribe_mails_value == "on"?" wpqa_hide":"").'">
															<label for="send_message_mail_'.$rand_e.'">
																<span class="wpqa_checkbox"><input type="checkbox" name="send_message_mail" class="send_message_mail" id="send_message_mail_'.$rand_e.'" value="on" '.checked(esc_attr(isset($_POST["send_message_mail"]) && isset($valid_form)?$_POST["send_message_mail"]:(!empty($_POST) && empty($_POST["send_message_mail"])?"":$send_message_mail)),"on",false).'></span>
																<span class="wpqa_checkbox_span">'.esc_html__("Would you like to receive an email when you receive new messages?","wpqa").'</span>
															</label>
														</p>';
													}else if ($key_items_4 == "answer_on_your_question" && isset($value_items_4["value"]) && $value_items_4["value"] == "answer_on_your_question") {
														$out .= '<p class="wpqa_checkbox_p answer_on_your_question_field normal_label'.($unsubscribe_mails_value == "on"?" wpqa_hide":"").'">
															<label for="answer_on_your_question_'.$rand_e.'">
																<span class="wpqa_checkbox"><input type="checkbox" name="answer_on_your_question" class="answer_on_your_question" id="answer_on_your_question_'.$rand_e.'" value="on" '.checked(esc_attr(isset($_POST["answer_on_your_question"]) && isset($valid_form)?$_POST["answer_on_your_question"]:(!empty($_POST) && empty($_POST["answer_on_your_question"])?"":$answer_on_your_question)),"on",false).'></span>
																<span class="wpqa_checkbox_span">'.esc_html__("Would you like to receive an email when new answers are added to your questions?","wpqa").'</span>
															</label>
														</p>';
													}else if ($key_items_4 == "answer_question_follow" && isset($value_items_4["value"]) && $value_items_4["value"] == "answer_question_follow") {
														$out .= '<p class="wpqa_checkbox_p answer_question_follow_field normal_label'.($unsubscribe_mails_value == "on"?" wpqa_hide":"").'">
															<label for="answer_question_follow_'.$rand_e.'">
																<span class="wpqa_checkbox"><input type="checkbox" name="answer_question_follow" class="answer_question_follow" id="answer_question_follow_'.$rand_e.'" value="on" '.checked(esc_attr(isset($_POST["answer_question_follow"]) && isset($valid_form)?$_POST["answer_question_follow"]:(!empty($_POST) && empty($_POST["answer_question_follow"])?"":$answer_question_follow)),"on",false).'></span>
																<span class="wpqa_checkbox_span">'.esc_html__("Would you like to receive an email when new answers are added to the question that you follow?","wpqa").'</span>
															</label>
														</p>'.apply_filters('wpqa_after_answer_question_follow',false,$profile_user_id);
													}else if ($key_items_4 == "notified_reply" && isset($value_items_4["value"]) && $value_items_4["value"] == "notified_reply") {
														$out .= '<p class="wpqa_checkbox_p notified_reply_field normal_label'.($unsubscribe_mails_value == "on"?" wpqa_hide":"").'">
															<label for="notified_reply_'.$rand_e.'">
																<span class="wpqa_checkbox"><input type="checkbox" name="notified_reply" class="notified_reply" id="notified_reply_'.$rand_e.'" value="on" '.checked(esc_attr(isset($_POST["notified_reply"]) && isset($valid_form)?$_POST["notified_reply"]:(!empty($_POST) && empty($_POST["notified_reply"])?"":$notified_reply)),"on",false).'></span>
																<span class="wpqa_checkbox_span">'.esc_html__("Would you like to receive an email when new replies are added to your answers?","wpqa").'</span>
															</label>
														</p>';
													}else if ($key_items_4 == "notification_mention" && isset($value_items_4["value"]) && $value_items_4["value"] == "notification_mention") {
														$out .= '<p class="wpqa_checkbox_p notification_mention_field normal_label'.($unsubscribe_mails_value == "on"?" wpqa_hide":"").'">
															<label for="notification_mention_'.$rand_e.'">
																<span class="wpqa_checkbox"><input type="checkbox" name="notification_mention" class="notification_mention" id="notification_mention_'.$rand_e.'" value="on" '.checked(esc_attr(isset($_POST["notification_mention"]) && isset($valid_form)?$_POST["notification_mention"]:(!empty($_POST) && empty($_POST["notification_mention"])?"":$notification_mention)),"on",false).'></span>
																<span class="wpqa_checkbox_span">'.esc_html__("Would you like to receive an email when user mentioned you?","wpqa").'</span>
															</label>
														</p>';
													}else if ($key_items_4 == "unsubscribe_mails" && isset($value_items_4["value"]) && $value_items_4["value"] == "unsubscribe_mails") {
														$out .= '<p class="wpqa_checkbox_p unsubscribe_mails_field normal_label">
															<label for="unsubscribe_mails_'.$rand_e.'">
																<span class="wpqa_checkbox"><input type="checkbox" name="unsubscribe_mails" class="unsubscribe_mails" id="unsubscribe_mails_'.$rand_e.'" value="on" '.checked($unsubscribe_mails_value,"on",false).'></span>
																<span class="wpqa_checkbox_span">'.esc_html__("Would you like to unsubscribe from all the mails?","wpqa").'</span>
															</label>
														</p>';
													}
													$out .= apply_filters('wpqa_edit_profile_'.$key_items_4,false,$profile_user_id);
												}
											$out .= '</div>
											<div class="clearfix"></div>
										</div><!-- End page-section -->
									</div><!-- End page-sections -->';
								}
							}
						}
					}

					if ($a['type'] == "privacy" && isset($edit_profile_sections) && is_array($edit_profile_sections) && isset($edit_profile_items_4) && is_array($edit_profile_items_4)) {
						$save_available = true;
						$register_items = wpqa_options("register_items");
						foreach ($edit_profile_sections as $key_sections => $value_sections) {
							if (isset($value_sections["value"]) && $value_sections["value"] == "privacy") {
								$out .= '<div class="page-sections" id="privacy-profile">
									<div class="page-section page-section-'.$value_sections["value"].'">
										<div class="page-wrap-content">
											<h2 class="post-title-2"><i class="icon-lock-open"></i>'.esc_html__("Privacy Settings","wpqa").'</h2>
											<p>'.esc_html__("Select who may see your profile details","wpqa").'</p>';
											$privacy_array = array(
												"email" => array("value" => esc_html__("Email","wpqa"),"icon" => "icon-mail")
											);
											if ((isset($edit_profile_items_1["country"]) && isset($edit_profile_items_1["country"]["value"]) && $edit_profile_items_1["country"]["value"] == "country") || (isset($register_items["country"]) && isset($register_items["country"]["value"]) && $register_items["country"]["value"] == "country")) {
												$privacy_array["country"] = array("value" => esc_html__("Country","wpqa"),"icon" => "icon-location");
											}
											if ((isset($edit_profile_items_1["city"]) && isset($edit_profile_items_1["city"]["value"]) && $edit_profile_items_1["city"]["value"] == "city") || (isset($register_items["city"]) && isset($register_items["city"]["value"]) && $register_items["city"]["value"] == "city")) {
												$privacy_array["city"] = array("value" => esc_html__("City","wpqa"),"icon" => "icon-address");
											}
											if ((isset($edit_profile_items_1["phone"]) && isset($edit_profile_items_1["phone"]["value"]) && $edit_profile_items_1["phone"]["value"] == "phone") || (isset($register_items["phone"]) && isset($register_items["phone"]["value"]) && $register_items["phone"]["value"] == "phone")) {
												$privacy_array["phone"] = array("value" => esc_html__("Phone","wpqa"),"icon" => "icon-phone");
											}
											if ((isset($edit_profile_items_1["gender"]) && isset($edit_profile_items_1["gender"]["value"]) && $edit_profile_items_1["gender"]["value"] == "gender") || (isset($register_items["gender"]) && isset($register_items["gender"]["value"]) && $register_items["gender"]["value"] == "gender")) {
												$privacy_array["gender"] = array("value" => esc_html__("Gender","wpqa"),"icon" => "icon-heart");
											}
											if ((isset($edit_profile_items_1["age"]) && isset($edit_profile_items_1["age"]["value"]) && $edit_profile_items_1["age"]["value"] == "age") || (isset($register_items["age"]) && isset($register_items["age"]["value"]) && $register_items["age"]["value"] == "age")) {
												$privacy_array["age"] = array("value" => esc_html__("Age","wpqa"),"icon" => "icon-progress-2");
											}
											$p_count = 0;
											$edit_profile_items_2_keys = array_keys($edit_profile_items_2);
											while ($p_count < count($edit_profile_items_2)) {
												if (isset($edit_profile_items_2[$edit_profile_items_2_keys[$p_count]]["value"]) && $edit_profile_items_2[$edit_profile_items_2_keys[$p_count]]["value"] != "" && $edit_profile_items_2[$edit_profile_items_2_keys[$p_count]]["value"] != "0") {
													$profile_one_2 = $p_count;
													break;
												}
												$p_count++;
											}
											if (isset($profile_one_2)) {
												$privacy_array["social"] = array("value" => esc_html__("Social links","wpqa"),"icon" => "icon-globe");
											}
											if (isset($edit_profile_items_3["website"]) && isset($edit_profile_items_3["website"]["value"]) && $edit_profile_items_3["website"]["value"] == "website") {
												$privacy_array["website"] = array("value" => esc_html__("Website","wpqa"),"icon" => "icon-link");
											}
											if (isset($edit_profile_items_3["bio"]) && isset($edit_profile_items_3["bio"]["value"]) && $edit_profile_items_3["bio"]["value"] == "bio") {
												$privacy_array["bio"] = array("value" => esc_html__("Biography","wpqa"),"icon" => "icon-pencil");
											}
											if (isset($edit_profile_items_3["profile_credential"]) && isset($edit_profile_items_3["profile_credential"]["value"]) && $edit_profile_items_3["profile_credential"]["value"] == "profile_credential") {
												$privacy_array["credential"] = array("value" => esc_html__("Profile credential","wpqa"),"icon" => "icon-info");
											}
											foreach ($privacy_array as $key_privacy => $value_privacy) {
												$meta_value = get_user_meta($profile_user_id,"privacy_".$key_privacy,true);
												$selected_value = esc_html(isset($_POST["privacy_".$key_privacy]) && $_POST["privacy_".$key_privacy] != "" && isset($valid_form)?$_POST["privacy_".$key_privacy]:($meta_value != ""?$meta_value:""));
												$out .= '<p class="'.$key_privacy.'_field">
													<label for="'.$key_privacy.'_'.$rand_e.'">'.$value_privacy["value"].'</label>
													<span class="styled-select">
														<select class="form-control" name="privacy_'.$key_privacy.'" id="'.$key_privacy.'_'.$rand_e.'">
															<option value="">'.esc_html__('Select visibility','wpqa').'</option>
															<option '.selected($selected_value,"public",false).' value="public">'.esc_html__('Public','wpqa').'</option>
															<option '.selected($selected_value,"members",false).' value="members">'.esc_html__('All members','wpqa').'</option>
															<option '.selected($selected_value,"me",false).' value="me">'.esc_html__('Only me','wpqa').'</option>
														</select>
													</span>
													<i class="'.$value_privacy["icon"].'"></i>
												</p>';
											}
										$out .= '</div>
										<div class="clearfix"></div>
									</div><!-- End page-section -->
								</div><!-- End page-sections -->';
							}
						}
					}

					if ($a['type'] == "delete" && isset($edit_profile_sections) && is_array($edit_profile_sections) && $delete_account == "on" && is_array($delete_account_groups) && in_array($user_group,$delete_account_groups)) {
						$save_available = true;
						foreach ($edit_profile_sections as $key_sections => $value_sections) {
							if (isset($value_sections["value"]) && $value_sections["value"] == "delete_account") {
								$out .= '<div class="page-sections" id="delete-profile">
									<div class="page-section page-section-'.$value_sections["value"].'">
										<div class="page-wrap-content">
											<h2 class="post-title-2"><i class="icon-trash"></i>'.esc_html__("Delete account","wpqa").'</h2>
											<p class="wpqa_checkbox_p delete_account_field normal_label">
												<label for="delete_account_'.$rand_e.'">
													<span class="wpqa_checkbox"><input type="checkbox" name="delete_account" class="delete_account" id="delete_account_'.$rand_e.'"></span>
													<span class="wpqa_checkbox_span">'.esc_html__("Delete your account?","wpqa").'</span>
												</label>
											</p>
										</div>
										<div class="clearfix"></div>
									</div><!-- End page-section -->
								</div><!-- End page-sections -->';
							}
						}
					}

					if ($a['type'] == "password") {
						$save_available = true;
					}
					
					$out .= '<div class="page-sections'.($a['type'] != "password"?" wpqa_hide":"").'" id="change-password">
						<div class="page-section">
							<div class="page-wrap-content">
								<h2 class="post-title-2"><i class="icon-lock"></i>'.esc_html__("Change password","wpqa").'</h2>
								<p class="login-password">
									<label for="newpassword_'.$rand_e.'">'.esc_html__("New Password","wpqa").'<span class="required">*</span></label>
									<input readonly="readonly" id="newpassword_'.$rand_e.'" class="required-item form-control" autocomplete="new-password" type="password" name="pass1">
									<i class="icon-lock-open"></i>
								</p>
								<p class="login-password">
									<label for="newpassword2_'.$rand_e.'">'.esc_html__("Confirm Password","wpqa").'<span class="required">*</span></label>
									<input readonly="readonly" id="newpassword2_'.$rand_e.'" class="required-item form-control" autocomplete="new-password" type="password" name="pass2">
									<i class="icon-lock-open"></i>
								</p>
							</div>
						</div><!-- End page-section -->
					</div><!-- End page-sections -->
				</div>';
				
				if (isset($save_available)) {
					$out .= '<p class="form-submit mb-0">
						<span class="load_span"><span class="loader_2"></span></span>
						<input type="hidden" name="user_action" value="edit_profile">
						<input type="hidden" name="action" value="update">
						<input type="hidden" name="admin_bar_front" value="1">
						<input type="hidden" name="user_id" id="user_id_'.$rand_e.'" value="'.esc_attr($profile_user_id).'">
						'.($show_user_login == false?'<input type="hidden" name="user_login" id="user_login_'.$rand_e.'" value="'.esc_attr($user_info->user_login).'">':'').'
						'.wp_nonce_field('wpqa_profile_nonce','wpqa_profile_nonce',true,false).
						(wpqa_input_button() == "button"?'<button type="submit" class="button-hide-click login-submit submit btn btn__primary btn__block btn__large__height">'.($a['type'] == "delete"?esc_attr__("Delete","wpqa"):esc_attr__("Save","wpqa")).'</button>':'<input type="submit" value="'.($a['type'] == "delete"?esc_attr__("Delete","wpqa"):esc_attr__("Save","wpqa")).'" class="button-default button-hide-click login-submit submit">').'
					</p>';
				}
			
			$out .= '</form>';
		}
		return $out;
	}
endif;
/* Edit profile return */
if (!function_exists('wpqa_edit_profile_form')) :
	function wpqa_edit_profile_form($edit) {
		if (isset($_POST["user_action"]) && $_POST["user_action"] == $edit) :
			$return = wpqa_check_edit_profile($_POST);
			if (is_wp_error($return)) :
	   			return '<div class="wpqa_error">'.$return->get_error_message().'</div>';
	   		else :
	   			return '<div class="wpqa_success">'.esc_html__("Profile has been updated.","wpqa").'</div>';
	   		endif;
		endif;
	}
endif;
add_filter('wpqa_edit_profile_form','wpqa_edit_profile_form');
/* Check the edit profile */
function wpqa_check_edit_profile($data = array()) {
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	if (isset($data['wpqa_profile_nonce']) && wp_verify_nonce($data['wpqa_profile_nonce'],'wpqa_profile_nonce')) {
		$user_id = get_current_user_id();
		return wpqa_process_edit_profile_form($data,$user_id);
	}else {
		$errors = new WP_Error();
		$errors->add('nonce-error','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There is an error, Please reload the page and try again.","wpqa"));
		return $errors;
	}
}
/* Process edit profile form */
if (!function_exists('wpqa_process_edit_profile_form')) :
	function wpqa_process_edit_profile_form($data = array(),$user_id = 0) {
		if ($user_id > 0) {
			$data = (is_array($data) && !empty($data)?$data:$_POST);
			return wpqa_complete_edit_profile($data,$user_id);
		}
	}
endif;
/* Complete edit profile form */
if (!function_exists('wpqa_complete_edit_profile')) :
	function wpqa_complete_edit_profile($data = array(),$user_id = 0) {
		if ($user_id > 0) {
			$data = (is_array($data) && !empty($data)?$data:$_POST);
			$user_meta_avatar = wpqa_avatar_name();
			$user_meta_cover = wpqa_cover_name();

			$edit_profile_items_1 = wpqa_options("edit_profile_items_1");
			$edit_profile_items_3 = wpqa_options("edit_profile_items_3");
			
			$website_register = (isset($edit_profile_items_3["website"]["value"]) && $edit_profile_items_3["website"]["value"] == "website"?"on":0);
			$profile_credential_register = (isset($edit_profile_items_3["profile_credential"]["value"]) && $edit_profile_items_3["profile_credential"]["value"] == "profile_credential"?"on":0);
			$nickname = (isset($edit_profile_items_1["nickname"]["value"]) && $edit_profile_items_1["nickname"]["value"] == "nickname"?"on":0);
			
			$url_required_profile = wpqa_options("url_required_profile");
			$profile_credential_required = wpqa_options("profile_credential_required");
			$profile_credential_maximum = wpqa_options("profile_credential_maximum");
			$user_id = get_current_user_id();
			$get_your_avatar = get_user_meta($user_id,$user_meta_avatar,true);
			$get_your_cover = get_user_meta($user_id,$user_meta_cover,true);
			$bio_editor = wpqa_options("bio_editor");

			$change_password_page = (wpqa_is_user_password_profile() || (isset($data["ajax_password"]) && $data["ajax_password"] == true)?true:false);
			
			require_once(ABSPATH . 'wp-admin/includes/user.php');
			
			$errors = new WP_Error();
			$posted = array(
				'email'                   => (isset($data['email']) && $data['email'] != ""?esc_html($data['email']):""),
				'pass1'                   => ((isset($data["mobile"]) || ($change_password_page == true && isset($data['pass1']))) && $data['pass1'] != ""?esc_html($data['pass1']):""),
				'pass2'                   => ((isset($data["mobile"]) || ($change_password_page == true && isset($data['pass2']))) && $data['pass2'] != ""?esc_html($data['pass2']):""),
				'first_name'              => (isset($data['first_name']) && $data['first_name'] != ""?esc_html($data['first_name']):""),
				'last_name'               => (isset($data['last_name']) && $data['last_name'] != ""?esc_html($data['last_name']):""),
				'nickname'                => (isset($data['nickname']) && $data['nickname'] != ""?esc_html($data['nickname']):""),
				'display_name'            => (isset($data['display_name']) && $data['display_name'] != ""?esc_html($data['display_name']):""),
				'country'                 => (isset($data['country']) && $data['country'] != ""?esc_html($data['country']):""),
				'city'                    => (isset($data['city']) && $data['city'] != ""?esc_html($data['city']):""),
				'phone'                   => (isset($data['phone']) && $data['phone'] != ""?esc_html($data['phone']):""),
				'gender'                  => (isset($data['gender']) && $data['gender'] != ""?esc_html($data['gender']):""),
				'age'                     => (isset($data['age']) && $data['age'] != ""?esc_html($data['age']):""),
				'profile_credential'      => (isset($data['profile_credential']) && $data['profile_credential'] != ""?esc_html($data['profile_credential']):""),
				'facebook'                => (isset($data['facebook']) && $data['facebook'] != ""?esc_url($data['facebook']):""),
				'tiktok'                  => (isset($data['tiktok']) && $data['tiktok'] != ""?esc_url($data['tiktok']):""),
				'twitter'                 => (isset($data['twitter']) && $data['twitter'] != ""?esc_url($data['twitter']):""),
				'youtube'                 => (isset($data['youtube']) && $data['youtube'] != ""?esc_url($data['youtube']):""),
				'vimeo'                   => (isset($data['vimeo']) && $data['vimeo'] != ""?esc_url($data['vimeo']):""),
				'linkedin'                => (isset($data['linkedin']) && $data['linkedin'] != ""?esc_url($data['linkedin']):""),
				'instagram'               => (isset($data['instagram']) && $data['instagram'] != ""?esc_url($data['instagram']):""),
				'pinterest'               => (isset($data['pinterest']) && $data['pinterest'] != ""?esc_url($data['pinterest']):""),
				'show_point_favorite'     => (isset($data['show_point_favorite']) && $data['show_point_favorite'] != ""?esc_html($data['show_point_favorite']):""),
				'question_schedules'      => (isset($data['question_schedules']) && $data['question_schedules'] != ""?esc_html($data['question_schedules']):""),
				'post_schedules'          => (isset($data['post_schedules']) && $data['post_schedules'] != ""?esc_html($data['post_schedules']):""),
				'received_email'          => (isset($data['received_email']) && $data['received_email'] != ""?esc_html($data['received_email']):""),
				'received_email_post'     => (isset($data['received_email_post']) && $data['received_email_post'] != ""?esc_html($data['received_email_post']):""),
				'received_message'        => (isset($data['received_message']) && $data['received_message'] != ""?esc_html($data['received_message']):""),
				'unsubscribe_mails'       => (isset($data['unsubscribe_mails']) && $data['unsubscribe_mails'] != ""?esc_html($data['unsubscribe_mails']):""),
				'new_payment_mail'        => (isset($data['new_payment_mail']) && $data['new_payment_mail'] != ""?esc_html($data['new_payment_mail']):""),
				'send_message_mail'       => (isset($data['send_message_mail']) && $data['send_message_mail'] != ""?esc_html($data['send_message_mail']):""),
				'answer_on_your_question' => (isset($data['answer_on_your_question']) && $data['answer_on_your_question'] != ""?esc_html($data['answer_on_your_question']):""),
				'answer_question_follow'  => (isset($data['answer_question_follow']) && $data['answer_question_follow'] != ""?esc_html($data['answer_question_follow']):""),
				'notified_reply'          => (isset($data['notified_reply']) && $data['notified_reply'] != ""?esc_html($data['notified_reply']):""),
				'notification_mention'    => (isset($data['notification_mention']) && $data['notification_mention'] != ""?esc_html($data['notification_mention']):""),
				'delete_account'          => (isset($data['delete_account']) && $data['delete_account'] != ""?esc_html($data['delete_account']):""),
				'url'                     => (isset($data['url']) && $data['url'] != ""?esc_url($data['url']):""),
				'description'             => (isset($data['description']) && $data['description'] != ""?($bio_editor == "on"?wpqa_esc_textarea($data['description']):wpqa_esc_textarea($data['description'])):""),
				'categories_left_menu'    => (isset($data['categories_left_menu']) && $data['categories_left_menu'] != ""?$data['categories_left_menu']:""),
				'wpqa_profile_nonce'      => (isset($data['wpqa_profile_nonce']) && $data['wpqa_profile_nonce'] != ""?esc_html($data['wpqa_profile_nonce']):""),
				'financial_payments'      => (isset($data['financial_payments']) && $data['financial_payments'] != ""?esc_html($data['financial_payments']):""),
				'paypal_email'            => (isset($data['paypal_email']) && $data['paypal_email'] != ""?esc_html($data['paypal_email']):""),
				'payoneer_email'          => (isset($data['payoneer_email']) && $data['payoneer_email'] != ""?esc_html($data['payoneer_email']):""),
				'bank_account_holder'     => (isset($data['bank_account_holder']) && $data['bank_account_holder'] != ""?esc_html($data['bank_account_holder']):""),
				'bank_your_address'       => (isset($data['bank_your_address']) && $data['bank_your_address'] != ""?esc_html($data['bank_your_address']):""),
				'bank_name'               => (isset($data['bank_name']) && $data['bank_name'] != ""?esc_html($data['bank_name']):""),
				'bank_address'            => (isset($data['bank_address']) && $data['bank_address'] != ""?esc_html($data['bank_address']):""),
				'bank_swift_iban'         => (isset($data['bank_swift_iban']) && $data['bank_swift_iban'] != ""?esc_html($data['bank_swift_iban']):""),
				'bank_account_number'     => (isset($data['bank_account_number']) && $data['bank_account_number'] != ""?esc_html($data['bank_account_number']):""),
				'bank_extra_note'         => (isset($data['bank_extra_note']) && $data['bank_extra_note'] != ""?esc_html($data['bank_extra_note']):""),
				'crypto_token_name'       => (isset($data['crypto_token_name']) && $data['crypto_token_name'] != ""?esc_html($data['crypto_token_name']):""),
				'crypto_wallet_address'   => (isset($data['crypto_wallet_address']) && $data['crypto_wallet_address'] != ""?esc_html($data['crypto_wallet_address']):""),
				'privacy_email'           => (isset($data['privacy_email']) && $data['privacy_email'] != ""?esc_html($data['privacy_email']):""),
				'privacy_country'         => (isset($data['privacy_country']) && $data['privacy_country'] != ""?esc_html($data['privacy_country']):""),
				'privacy_city'            => (isset($data['privacy_city']) && $data['privacy_city'] != ""?esc_html($data['privacy_city']):""),
				'privacy_phone'           => (isset($data['privacy_phone']) && $data['privacy_phone'] != ""?esc_html($data['privacy_phone']):""),
				'privacy_gender'          => (isset($data['privacy_gender']) && $data['privacy_gender'] != ""?esc_html($data['privacy_gender']):""),
				'privacy_age'             => (isset($data['privacy_age']) && $data['privacy_age'] != ""?esc_html($data['privacy_age']):""),
				'privacy_social'          => (isset($data['privacy_social']) && $data['privacy_social'] != ""?esc_html($data['privacy_social']):""),
				'privacy_website'         => (isset($data['privacy_website']) && $data['privacy_website'] != ""?esc_html($data['privacy_website']):""),
				'privacy_bio'             => (isset($data['privacy_bio']) && $data['privacy_bio'] != ""?esc_html($data['privacy_bio']):""),
				'privacy_credential'      => (isset($data['privacy_email']) && $data['privacy_email'] != ""?esc_html($data['privacy_email']):""),
				'mobile'                  => (isset($data['mobile']) && $data['mobile'] != ""?true:false),
			);
			$posted = apply_filters("wpqa_edit_profile_posted",$posted);

			$delete_account_groups = wpqa_options("delete_account_groups");
			if (isset($data['delete_account']) && $data['delete_account'] == "on") {
				$user_info = get_userdata($user_id);
				$user_group = wpqa_get_user_group($user_info);
				if (is_array($delete_account_groups) && in_array($user_group,$delete_account_groups)) {
					wp_delete_user($user_id,0);
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Profile has been deleted.","wpqa").'</p></div>','wpqa_session');
					wp_safe_redirect(esc_url(home_url('/')));
					exit;
				}
			}

			if (!isset($data["mobile"]) && (!isset($data['wpqa_profile_nonce']) || !wp_verify_nonce($data['wpqa_profile_nonce'],'wpqa_profile_nonce'))) {
				$errors->add('nonce-error','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There is an error, Please reload the page and try again.","wpqa"));
			}
			
			if (isset($data["mobile"]) || wpqa_is_user_edit_profile()) {
				if (empty($data['email'])) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Email).","wpqa"));
				}
			}

			if (isset($data["mobile"]) || $change_password_page == true) {
				if (!isset($data["mobile"]) &&(empty($data['pass1']) || empty($data['pass1']))) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Password).","wpqa"));
				}
				if ($data['pass1'] !== $data['pass2']) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Password does not match.","wpqa"));
				}
			}else {
				unset($data['pass1']);
				unset($data['pass2']);
			}

			if (wpqa_is_user_financial_profile()) {
				if (empty($data['financial_payments'])) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Financial Payments).","wpqa"));
				}
				if (isset($data['financial_payments'])) {
					if ($data['financial_payments'] == "paypal" && empty($data['paypal_email'])) {
						$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (PayPal E-Mail).","wpqa"));
					}else if ($data['financial_payments'] == "payoneer" && empty($data['payoneer_email'])) {
						$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Payoneer E-Mail).","wpqa"));
					}else if ($data['financial_payments'] == "bank") {
						if (empty($data['bank_account_holder'])) {
							$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Name of the Account Holder).","wpqa"));
						}
						if (empty($data['bank_your_address'])) {
							$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Your Address).","wpqa"));
						}
						if (empty($data['bank_name'])) {
							$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Bank Name).","wpqa"));
						}
						if (empty($data['bank_address'])) {
							$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Bank Address).","wpqa"));
						}
						if (empty($data['bank_swift_iban'])) {
							$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (SWIFT/IBAN Code).","wpqa"));
						}
						if (empty($data['bank_account_number'])) {
							$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Account Number).","wpqa"));
						}
					}else if ($data['financial_payments'] == "crypto") {
						if (empty($data['crypto_token_name'])) {
							$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Coin/Token Name).","wpqa"));
						}
						if (empty($data['crypto_wallet_address'])) {
							$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Wallet Address).","wpqa"));
						}
					}
				}
			}else {
				unset($data['financial_payments']);
				unset($data['paypal_email']);
				unset($data['payoneer_email']);
				unset($data['bank_account_holder']);
				unset($data['bank_your_address']);
				unset($data['bank_name']);
				unset($data['bank_address']);
				unset($data['bank_swift_iban']);
				unset($data['bank_account_number']);
				unset($data['bank_extra_note']);
				unset($data['crypto_token_name']);
				unset($data['crypto_wallet_address']);
			}

			if (isset($data["mobile"]) || wpqa_is_user_edit_profile()) {
				do_action('wpqa_edit_profile_errors_main',$errors,$posted,$edit_profile_items_1,"edit",$user_id,array());
				
				if (empty($data['url']) && $website_register === "on" && $url_required_profile == "on") {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Website).","wpqa"));
				}
				if (empty($data['profile_credential']) && $profile_credential_register === "on" && $profile_credential_required == "on") {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("There are required fields (Profile credential).","wpqa"));
				}
				if (isset($data['profile_credential']) && $profile_credential_maximum > 0 && strlen($data['profile_credential']) > $profile_credential_maximum) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Sorry, The maximum characters for the profile credential is","wpqa")." ".$profile_credential_maximum);
				}
				if (isset($data['facebook']) && $data['facebook'] != "" && filter_var($data['facebook'],FILTER_VALIDATE_URL) === FALSE) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Not a valid URL (Facebook).","wpqa"));
				}
				if (isset($data['twitter']) && $data['twitter'] != "" && filter_var($data['twitter'],FILTER_VALIDATE_URL) === FALSE) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Not a valid URL (Twitter).","wpqa"));
				}
				if (isset($data['tiktok']) && $data['tiktok'] != "" && filter_var($data['tiktok'],FILTER_VALIDATE_URL) === FALSE) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Not a valid URL (TikTok).","wpqa"));
				}
				if (isset($data['youtube']) && $data['youtube'] != "" && filter_var($data['youtube'],FILTER_VALIDATE_URL) === FALSE) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Not a valid URL (Youtube).","wpqa"));
				}
				if (isset($data['vimeo']) && $data['vimeo'] != "" && filter_var($data['vimeo'],FILTER_VALIDATE_URL) === FALSE) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Not a valid URL (Vimeo).","wpqa"));
				}
				if (isset($data['linkedin']) && $data['linkedin'] != "" && filter_var($data['linkedin'],FILTER_VALIDATE_URL) === FALSE) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Not a valid URL (Linkedin).","wpqa"));
				}
				if (isset($data['instagram']) && $data['instagram'] != "" && filter_var($data['instagram'],FILTER_VALIDATE_URL) === FALSE) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Not a valid URL (Instagram).","wpqa"));
				}
				if (isset($data['pinterest']) && $data['pinterest'] != "" && filter_var($data['pinterest'],FILTER_VALIDATE_URL) === FALSE) {
					$errors->add('required-field','<strong>'.esc_html__("Error","wpqa").':&nbsp;</strong> '.esc_html__("Not a valid URL (Pinterest).","wpqa"));
				}
			}

			do_action('wpqa_edit_profile_errors',$errors,$posted,$edit_profile_items_1,"edit",$user_id);
			
			if ($errors->get_error_code()) {
				return $errors;
			}

			$confirm_edit_email = wpqa_options("confirm_edit_email");
			if ($confirm_edit_email == "on") {
				$data_email = $data["email"];
				if (isset($_POST) || isset($data)) {
					$user = get_userdata($user_id);
					$user_email = $user->user_email;
					$data["email"] = $_POST["email"] = esc_html($user_email);
				}
			}
			
			if (isset($data["mobile"]) || wpqa_is_user_edit_profile() || $change_password_page == true) {
				isset($data['admin_bar_front']) ? 'true' : 'false';
				if ($nickname !== 'on' && isset($data['nickname'])) {
					$data['nickname'] = get_the_author_meta("user_login",$user_id);
				}
				if (isset($data) && !isset($data['nickname'])) {
					$nicename_nickname = (isset($data['nickname']) && $data['nickname'] != ""?sanitize_text_field($data['nickname']):(isset($data['user_name']) && $data['user_name'] != ""?sanitize_text_field($data['user_name']):get_the_author_meta('user_login',$user_id)));
					$data['nickname'] = $_POST['nickname'] = get_the_author_meta("user_login",$user_id);
				}
				$errors_user = edit_user($user_id);
				if (is_wp_error($errors_user)) {
					return $errors_user;
				}
			}

			if ($confirm_edit_email == "on" && isset($data_email)) {
				$data["email"] = $data_email;
			}

			do_action("wpqa_personal_update_profile",$user_id,$posted,isset($_FILES)?$_FILES:array(),"edit");

			if (sizeof($errors->errors) > 0) {
				return $errors;
			}

			do_action("wpqa_after_edit_profile",$user_id,$posted,isset($_FILES)?$_FILES:array(),"edit",(isset($user_email)?$user_email:""));

			$update_profile = get_user_meta($user_id,"update_profile",true);
			if ($update_profile == "yes") {
				delete_user_meta($user_id,"update_profile");
				if (!isset($data["mobile"])) {
		  			wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Profile has been updated.","wpqa").'</p></div>','wpqa_session');
					wp_safe_redirect(esc_url(home_url('/')));
					exit;
				}
			}
			if (isset($data["mobile"])) {
				return $user_id;
			}else {
				return;
			}
		}
	}
endif;
/* Show profile fields */
add_action('show_user_profile','wpqa_show_extra_profile_fields');
add_action('edit_user_profile','wpqa_show_extra_profile_fields');
if (!function_exists('wpqa_show_extra_profile_fields')) :
	function wpqa_show_extra_profile_fields($user) {?>
		<table class="form-table">
			<tr class="form-terms">
				<th colspan="2" scope="row" valign="top">
					<div class="framework-main">
						<?php wpqa_admin_fields_class::wpqa_admin_fields("author",wpqa_author,"user",$user->ID,wpqa_admin_author($user->ID));?>
					</div>
				</th>
			</tr>
		</table>
	<?php }
endif;
/* Save user's meta */
add_action('wpqa_personal_update_profile','wpqa_save_extra_profile_fields',1,2);
add_action('personal_options_update','wpqa_save_extra_profile_fields');
add_action('edit_user_profile_update','wpqa_save_extra_profile_fields');
if (!function_exists('wpqa_save_extra_profile_fields')) :
	function wpqa_save_extra_profile_fields($user_id,$data = array()) {
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		if ( (!isset($data["mobile"]) || (isset($data["mobile"]) && $data["mobile"] != true)) && !current_user_can('edit_user',$user_id)) return false;

		$get_current_user_id = get_current_user_id();
		
		if (isset($_POST['admin']) && $_POST['admin'] == "save") {
			do_action("wpqa_user_register",$user_id);
			
			if (isset($_POST['user_best_answer'])) {
				$user_best_answer = sanitize_text_field($_POST['user_best_answer']);
				update_user_meta( $user_id, 'user_best_answer', $user_best_answer );
			}
		}

		$user_data = get_userdata($user_id);
		$default_group = wpqa_get_user_group($user_data);
		if (isset($_POST['role']) && $_POST['role'] != "" && $default_group != $_POST['role']) {
			$default_group = esc_html($_POST['role']);
		}

		if (is_super_admin($get_current_user_id) && ((isset($_POST['remove_subscription']) && $_POST['remove_subscription'] == "on") || (isset($_POST['add_subscription']) && $_POST['add_subscription'] == "on" && isset($_POST['subscription_plan']) && $_POST['subscription_plan'] != "") || (isset($_POST['activate_user']) && $_POST['activate_user'] == "on") || (isset($_POST['approve_user']) && $_POST['approve_user'] == "on"))) {
			$default_group = wpqa_options("default_group");
			$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
			$default_group = apply_filters("wpqa_default_group",$default_group,$user_id);
			if (isset($_POST['add_subscription']) && $_POST['add_subscription'] == "on" && isset($_POST['subscription_plan']) && $_POST['subscription_plan'] != "") {
				wpqa_cancel_subscription($user_id,0);
				update_user_meta($user_id,"subscribe_from_admin",true);
				$subscription_plan = esc_html($_POST['subscription_plan']);
				$subscription_plans = wpqa_subscription_plans();
				$subscriptions_group = (isset($subscription_plans[$subscription_plan]) && isset($subscription_plans[$subscription_plan]["role"])?$subscription_plans[$subscription_plan]["role"]:"");
				$default_group = ($subscriptions_group != ""?$subscriptions_group:"author");
				update_user_meta($user_id,"wpqa_subscribe_module",true);
				update_user_meta($user_id,"wpqa_plan_key",$subscription_plan);
				$explode = explode("_",$subscription_plan);
				$subscription_plan = (isset($explode[1])?$explode[1]:$subscription_plan);
				update_user_meta($user_id,"package_subscribe",$subscription_plan);
				if ($subscription_plan != "lifetime") {
					$interval = ($subscription_plan == "yearly"?"year":"month");
					$interval_count = ($subscription_plan == "monthly" || $subscription_plan == "yearly"?1:($subscription_plan == "3months"?3:6));
					update_user_meta($user_id,"start_subscribe_time",strtotime(date("Y-m-d H:i:s")));
					update_user_meta($user_id,"end_subscribe_time",strtotime(date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +$interval_count $interval +7 hour"))));
				}
			}else if (isset($_POST['remove_subscription']) && $_POST['remove_subscription'] == "on") {
				$trial_subscribe = get_user_meta($user_id,"trial_subscribe",true);
				$points_subscribe = get_user_meta($user_id,"points_subscribe",true);
				wpqa_stop_subscription($user_id,0,($trial_subscribe == "" && $points_subscribe == ""?"cancel":""));
			}else {
				$activate_user_meta = (isset($_POST['activate_user']) && $_POST['activate_user'] == "on"?"activate_user":"approve_user");
				$activate_user = get_user_meta($user_id,$activate_user_meta,true);
				if ($activate_user == "") {
					$send_text = wpqa_send_mail(
						array(
							'content' => wpqa_options("email_approve_user"),
							'user_id' => $user_id,
						)
					);
					$email_title = wpqa_options("title_approve_user");
					$email_title = ($email_title != ""?$email_title:esc_html__("Confirm account","wpqa"));
					$email_title = wpqa_send_mail(
						array(
							'content' => $email_title,
							'title'   => true,
							'break'   => '',
							'user_id' => $user_id,
						)
					);
					wpqa_send_mails(
						array(
							'toEmail'     => esc_html($user_data->user_email),
							'toEmailName' => esc_html($user_data->display_name),
							'title'       => $email_title,
							'message'     => $send_text,
						)
					);
					update_user_meta($user_id,$activate_user_meta,"on");
					update_user_meta($user_id,"user_activated","activated");
					do_action("wpqa_user_activated",$user_id);
					if (isset($_POST['activate_user']) && $_POST['activate_user'] == "on") {
						delete_user_meta($user_id,"activation");
					}
				}
			}
			delete_user_meta($user_id,"wpqa_default_group");
			do_action("wpqa_after_registration",$user_id);
		}
		
		if (isset($_POST['from_admin']) && $_POST['from_admin'] == "yes") {
			$active_points = wpqa_options("active_points");
			if (is_super_admin($get_current_user_id) && $active_points == "on") {
				$add_remove_point = "";
				$the_points = "";
				$the_reason = "";
				if (isset($_POST['add_remove_point'])) {
					$add_remove_point = esc_html($_POST['add_remove_point']);
				}
				if (isset($_POST['the_points'])) {
					$the_points = (int)esc_html($_POST['the_points']);
				}
				if (isset($_POST['the_reason'])) {
					$the_reason = esc_html($_POST['the_reason']);
				}
				if ($the_points > 0) {
					if ($add_remove_point == "remove") {
						$add_remove_point_last = "-";
						$the_reason_last = "admin_remove_points";
					}else {
						$add_remove_point_last = "+";
						$the_reason_last = "admin_add_points";
					}
					$the_reason = (isset($the_reason) && $the_reason != ""?$the_reason:"");
					wpqa_add_points($user_id,$the_points,$add_remove_point_last,$the_reason_last);
					if ($get_current_user_id > 0 && $user_id > 0) {
						wpqa_notifications_activities($user_id,$get_current_user_id,"","","",$the_reason_last,"notifications",($the_reason != ""?$the_reason:""));
					}
				}
			}

			$new_moderator_categories = array();
			$moderator_categories = (isset($_POST[prefix_author."moderator_categories"])?$_POST[prefix_author."moderator_categories"]:array());
			$moderator_categories = (is_array($moderator_categories) && !empty($moderator_categories)?$moderator_categories:array());
			foreach ($moderator_categories as $key => $value) {
				$key = str_replace("cat-","",$key);
				$new_moderator_categories[] = $key;
			}
			update_user_meta($user_id,prefix_author."moderator_categories",$new_moderator_categories);
			$options = wpqa_admin_author($user_id);
			foreach ($options as $value) {
				if (!isset($value['unset']) && $value['type'] != 'heading' && $value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != "html" && $value['type'] != 'info' && $value['type'] != 'group' && $value['type'] != 'html' && $value['type'] != 'content') {
					$val = '';
					if (isset($value['std'])) {
						$val = $value['std'];
					}
					
					$field_name = $value['id'];
					if (isset($_POST[$field_name])) {
						$val = $_POST[$field_name];
					}
					
					if (!isset($_POST[$field_name]) && $value['type'] == "checkbox") {
						$val = 0;
					}
					
					if (array() === $val) {
						delete_user_meta($user_id,$field_name);
					}else {
						update_user_meta($user_id,$field_name,$val);
					}
				}
			}
		}else {
			$points_social = (int)wpqa_options("points_social");
			if (wpqa_is_user_mails_profile()) {
				$post_array = array('question_schedules','post_schedules','received_email','received_email_post','unsubscribe_mails','new_payment_mail','send_message_mail','answer_on_your_question','answer_question_follow','notified_reply','notification_mention');
			}else if (wpqa_is_user_privacy_profile()) {
				$post_array = array('privacy_email','privacy_country','privacy_city','privacy_phone','privacy_gender','privacy_age','privacy_social','privacy_website','privacy_bio','privacy_credential');
			}else if (wpqa_is_user_financial_profile()) {
				$post_array = array('financial_payments','paypal_email','payoneer_email','bank_account_holder','bank_your_address','bank_name','bank_address','bank_swift_iban','bank_account_number','bank_extra_note','crypto_token_name','crypto_wallet_address');
			}else {
				$post_array = array('country','city','phone','gender','age','facebook','tiktok','twitter','youtube','vimeo','linkedin','instagram','pinterest','show_point_favorite','received_message','profile_credential','categories_left_menu');
			}
			
			if (isset($data["mobile"]) && $data["mobile"] == true) {
				$post_array = array('country','city','phone','gender','age','facebook','tiktok','twitter','youtube','vimeo','linkedin','instagram','pinterest','show_point_favorite','received_message','profile_credential','categories_left_menu','question_schedules','post_schedules','received_email','received_email_post','unsubscribe_mails','new_payment_mail','send_message_mail','answer_on_your_question','answer_question_follow','notified_reply','notification_mention','privacy_email','privacy_country','privacy_city','privacy_phone','privacy_gender','privacy_age','privacy_social','privacy_website','privacy_bio','privacy_credential','financial_payments','paypal_email','payoneer_email','bank_account_holder','bank_your_address','bank_name','bank_address','bank_swift_iban','bank_account_number','bank_extra_note','crypto_token_name','crypto_wallet_address');
			}
			
			$post_array = apply_filters("wpqa_edit_profile_post_array",$post_array);

			do_action("wpqa_user_profile_after_saved",$user_id);

			if (isset($_POST["categories_left_menu"]) && is_array($_POST["categories_left_menu"]) && !empty($_POST["categories_left_menu"])) {
				foreach ($_POST["categories_left_menu"] as $key => $value) {
					$_POST["categories_left_menu"][$key]["value"] = (int)$value["value"];
				}
			}
			foreach ($post_array as $field_name) {
				$val = '';
				
				if (isset($_POST[$field_name])) {
					$val = $_POST[$field_name];
				}
				
				if ('' === $val || array() === $val) {
					if ($field_name == "facebook" || $field_name == "tiktok" || $field_name == "twitter" || $field_name == "youtube" || $field_name == "vimeo" || $field_name == "linkedin" || $field_name == "instagram" || $field_name == "pinterest") {
						if ($points_social > 0) {
							delete_user_meta($user_id,"add_".$field_name);
							if (get_user_meta($user_id,$field_name,true) != "" && get_user_meta($user_id,"remove_".$field_name,true) != true) {
								wpqa_add_points($user_id,$points_social,"-","remove_".$field_name);
							}
							update_user_meta($user_id,"remove_".$field_name,true);
						}
					}
					delete_user_meta($user_id,$field_name);
				}else {
					update_user_meta($user_id,$field_name,$val);
					if ($field_name == "facebook" || $field_name == "tiktok" || $field_name == "twitter" || $field_name == "youtube" || $field_name == "vimeo" || $field_name == "linkedin" || $field_name == "instagram" || $field_name == "pinterest") {
						if ($points_social > 0) {
							delete_user_meta($user_id,"remove_".$field_name);
							if (get_user_meta($user_id,"add_".$field_name,true) != true) {
								wpqa_add_points($user_id,$points_social,"+","add_".$field_name);
							}
							update_user_meta($user_id,"add_".$field_name,true);
						}
					}
				}
			}
		}

		if (isset($_POST['admin']) && $_POST['admin'] == "save") {
			do_action("wpqa_user_register_after_saved",$user_id);
		}

		if ((isset($data["mobile"]) && $data["mobile"] == true) || (isset($_POST['admin']) && $_POST['admin'] == "save") || wpqa_is_user_edit_profile() || wpqa_is_user_password_profile()) {
			$nicename_nickname = (isset($_POST['nickname']) && $_POST['nickname'] != ""?sanitize_text_field($_POST['nickname']):(isset($_POST['user_name']) && $_POST['user_name'] != ""?sanitize_text_field($_POST['user_name']):get_the_author_meta('user_login',$user_id)));
			$show_edit_user = apply_filters("wpqa_show_edit_user",true);
			if ($show_edit_user == true) {
				$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
				$_POST['role'] = $default_group;
				edit_user($user_id);
			}
			wp_update_user(array('ID' => $user_id,'user_nicename' => $nicename_nickname,'nickname' => $nicename_nickname,'role' => $default_group));
			if ((!isset($data["mobile"]) || (isset($data["mobile"]) && $data["mobile"] != true)) && isset($_POST["redirect_to"]) && $_POST["redirect_to"] != "") {
				wp_redirect(esc_url($_POST["redirect_to"]));
				die();
			}
		}
		if (isset($data["mobile"]) && $data["mobile"] == true) {
			return $user_id;
		}
	}
endif;
/* After edit profile */
add_action("wpqa_after_edit_profile","wpqa_after_edit_profile",1,5);
function wpqa_after_edit_profile($user_id,$posted,$files = array(),$edit = "edit",$user_email = "") {
	$confirm_edit_email = wpqa_options("confirm_edit_email");
	if ($posted['email'] != $user_email && $confirm_edit_email == "on") {
		update_user_meta($user_id,"wpqa_edit_email",esc_html($posted['email']));
		$rand_a = wpqa_token(15);
		update_user_meta($user_id,"activation",$rand_a);
		$confirm_link = esc_url_raw(add_query_arg(array("u" => $user_id,"activate" => $rand_a,"edit" => true),esc_url(home_url('/'))));
		$send_text = wpqa_send_mail(
			array(
				'content'            => wpqa_options("edit_email_confirm_link"),
				'user_id'            => $user_id,
				'confirm_link_email' => $confirm_link,
			)
		);
		$email_title = wpqa_options("title_confirm_edit_email_link");
		$email_title = ($email_title != ""?$email_title:esc_html__("Confirm account","wpqa"));
		$email_title = wpqa_send_mail(
			array(
				'content'            => $email_title,
				'title'              => true,
				'break'              => '',
				'user_id'            => $user_id,
				'confirm_link_email' => $confirm_link,
			)
		);
		if (!isset($posted['display_name'])) {
			$get_user = get_userdata($user_id);
		}
		wpqa_send_mails(
			array(
				'toEmail'     => esc_html($posted['email']),
				'toEmailName' => esc_html(isset($posted['display_name'])?$posted['display_name']:$get_user->display_name),
				'title'       => $email_title,
				'message'     => $send_text,
			)
		);
		if (!isset($posted['mobile'])) {
			wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("Check your email please to activate your membership.","wpqa").'</p></div>','wpqa_session');
		}
	}
}
/* Exporter data */
add_filter('wp_privacy_personal_data_exporters','wpqa_register_exporter');
if (!function_exists('wpqa_register_exporter')) :
	function wpqa_register_exporter($exporters) {
		$exporters['wpqa-data'] = array(
			'exporter_friendly_name' => esc_html__('Custom fields','wpqa'),
			'callback' => 'wpqa_exporter_data',
		);
	    return $exporters;
	}
endif;
if (!function_exists('wpqa_exporter_data')) :
	function wpqa_exporter_data($email_address,$page = 1) {
		$export_items = array();
		$user         = get_user_by('email',$email_address);
		$user_id      = $user->ID;

		$profile_credential = get_the_author_meta('profile_credential',$user_id);
		$twitter            = get_the_author_meta('twitter',$user_id);
		$facebook           = get_the_author_meta('facebook',$user_id);
		$tiktok             = get_the_author_meta('tiktok',$user_id);
		$youtube            = get_the_author_meta('youtube',$user_id);
		$vimeo              = get_the_author_meta('vimeo',$user_id);
		$linkedin           = get_the_author_meta('linkedin',$user_id);
		$instagram          = get_the_author_meta('instagram',$user_id);
		$pinterest          = get_the_author_meta('pinterest',$user_id);
		$country            = get_the_author_meta('country',$user_id);
		$city               = get_the_author_meta('city',$user_id);
		$age                = get_the_author_meta('age',$user_id);
		$phone              = get_the_author_meta('phone',$user_id);
		$gender             = get_the_author_meta('gender',$user_id);

		$export_data = array(
			array(
				'name'  => esc_html__('Profile credential','wpqa'),
				'value' => $profile_credential !== ''?esc_html($profile_credential):'',
			),
			array(
				'name'  => esc_html__('Twitter','wpqa'),
				'value' => $twitter !== ''?esc_url($twitter):'',
			),
			array(
				'name'  => esc_html__('Facebook','wpqa'),
				'value' => $facebook !== ''?esc_url($facebook):'',
			),
			array(
				'name'  => esc_html__('TikTok','wpqa'),
				'value' => $tiktok !== ''?esc_url($tiktok):'',
			),
			array(
				'name'  => esc_html__('Youtube','wpqa'),
				'value' => $youtube !== ''?esc_url($youtube):'',
			),
			array(
				'name'  => esc_html__('Vimeo','wpqa'),
				'value' => $vimeo !== ''?esc_url($vimeo):'',
			),
			array(
				'name'  => esc_html__('Linkedin','wpqa'),
				'value' => $linkedin !== ''?esc_url($linkedin):'',
			),
			array(
				'name'  => esc_html__('Instagram','wpqa'),
				'value' => $instagram !== ''?esc_url($instagram):'',
			),
			array(
				'name'  => esc_html__('Pinterest','wpqa'),
				'value' => $pinterest !== ''?esc_url($pinterest):'',
			),
			array(
				'name'  => esc_html__('Country','wpqa'),
				'value' => $country !== ''?esc_html($country):'',
			),
			array(
				'name'  => esc_html__('City','wpqa'),
				'value' => $city !== ''?esc_html($city):'',
			),
			array(
				'name'  => esc_html__('Age','wpqa'),
				'value' => $age !== ''?esc_html($age):'',
			),
			array(
				'name'  => esc_html__('Phone','wpqa'),
				'value' => $phone !== ''?esc_html($phone):'',
			),
			array(
				'name'  => esc_html__('Gender','wpqa'),
				'value' => $gender !== ''?($gender == "male" || $gender == 1?esc_html__("Male","wpqa"):"").($gender == "female" || $gender == 2?esc_html__("Female","wpqa"):"").($gender == "other" || $gender == 3?esc_html__("Other","wpqa"):""):'',
			),
		);

		$export_items[] = array(
			'group_id'    => 'custom_fields',
			'group_label' => esc_html__('Custom fields','wpqa'),
			'item_id'     => $user_id,
			'data'        => $export_data,
		);

		return array(
			'data' => $export_items,
			'done' => true,
		);
	}
endif;
/* Add class to change password form */
add_filter("wpqa_edit_profile_form_class","wpqa_edit_profile_form_class",1,2);
function wpqa_edit_profile_form_class($class,$return) {
	if ($return == "password") {
		$class = " change-password-ajax";
	}
	return $class;
}
function wpqa_edit_profile_password() {
	check_ajax_referer('wpqa_profile_nonce','wpqa_profile_nonce');
	$user_id = get_current_user_id();
	$_POST["ajax_password"] = true;
	$return = wpqa_check_edit_profile($_POST);
	$results = array();
	if (is_wp_error($return)) {
		$results["success"] = 0;
		$results["error"] = '<div class="wpqa_error">'.$return->get_error_message().'</div>';
	}else {
		$results["error"] = 0;
		$results["success"] = '<div class="wpqa_success">'.esc_html__("Profile has been updated.","wpqa").'</div>';
	}
	echo json_encode($results);
	die();
}
add_action('wp_ajax_wpqa_edit_profile_password','wpqa_edit_profile_password');
add_action('wp_ajax_nopriv_wpqa_edit_profile_password','wpqa_edit_profile_password');?>