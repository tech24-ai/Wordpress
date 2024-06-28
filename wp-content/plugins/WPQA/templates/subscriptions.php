<?php

/* @author    2codeThemes
*  @package   WPQA/templates
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action("wpqa_before_subscriptions");?>

<div class='wpqa-templates wpqa-subscriptions-template'>
	<div class="page-sections">
		<div class="page-section">
			<?php $subscriptions_payment = wpqa_options("subscriptions_payment");
			if ($subscriptions_payment == "on") {
				$subscriptions_style = wpqa_options("subscriptions_style");
				$unlogged_pay = wpqa_options("unlogged_pay");
				$payment_pages_target = wpqa_options("payment_pages_target");
				$payment_pages_target = ($payment_pages_target == "new_page"?"_blank":"_self");
				$activate_register = wpqa_options("activate_register");
				$payment_type_subscriptions = wpqa_options("payment_type_subscriptions");
				$cancel_subscription = wpqa_options("cancel_subscription");
				$change_subscription = wpqa_options("change_subscription");
				$default_group = wpqa_options("default_group");
				$trial_subscription = wpqa_options("trial_subscription");
				$reward_subscription = wpqa_options("reward_subscription");
				$roles = wpqa_options("roles");
				$user_id = get_current_user_id();
				$currency_code = wpqa_get_currency($user_id);
				$currency = (wpqa_options("activate_currencies") == "on"?"_".strtolower($currency_code):"");
				wpqa_free_subscriptions($user_id);
				$package_subscribe = get_user_meta($user_id,"package_subscribe",true);
				$wpqa_plan_key = get_user_meta($user_id,"wpqa_plan_key",true);
				$wpqa_plan_key = ($wpqa_plan_key != ""?str_replace("-","_",$wpqa_plan_key):"");
				$trial_subscribe = get_user_meta($user_id,"trial_subscribe",true);
				$date_format = wpqa_options("date_format");
				$date_format = ($date_format?$date_format:get_option("date_format"));
				$array = wpqa_subscription_plans($currency,$payment_type_subscriptions);
				$switch_yearly_monthly_plans = wpqa_options("switch_yearly_monthly_plans");
				echo '<div class="page-wrap-content">
					<h2 class="post-title-3"><i class="icon-basket"></i>'.esc_html__("Subscriptions","wpqa").'</h2>
					<div class="points-section buy-points-section subscriptions-section">';
						if ($package_subscribe != "") {
							$end_subscribe_time = get_user_meta($user_id,"end_subscribe_time",true);
							$reward_subscribe = get_user_meta($user_id,"reward_subscribe",true);
							if ($reward_subscribe != "" && ($package_subscribe == "week" || $package_subscribe == "month")) {
								if ($reward_subscription == "on") {
									echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Your membership will be ended on:","wpqa").' '.date($date_format,$end_subscribe_time).'</p></div>';
								}
							}else if ($trial_subscribe != "" && ($package_subscribe == "hour" || $package_subscribe == "week" || $package_subscribe == "month")) {
								if ($trial_subscription == "on") {
									$trial_subscription_rang = get_user_meta($user_id,"trial_rang",true);
									if ($trial_subscription_rang == "") {
										$trial_subscription_rang = wpqa_options("trial_subscription_rang");
									}
									if ($package_subscribe == "hour") {
										$package_trial = sprintf(_n("%s hour","%s hours",$trial_subscription_rang,"wpqa"),$trial_subscription_rang);
									}else if ($package_subscribe == "week") {
										$package_trial = sprintf(_n("%s week","%s weeks",$trial_subscription_rang,"wpqa"),$trial_subscription_rang);
									}else if ($package_subscribe == "month") {
										$package_trial = sprintf(_n("%s month","%s months",$trial_subscription_rang,"wpqa"),$trial_subscription_rang);
									}
									echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.sprintf(esc_html__("You have a trial membership for %s.","wpqa"),$package_trial).'</p></div>';
								}
							}else {
								if ($package_subscribe != "lifetime") {
									if ($cancel_subscription == "on") {
										$subscr_id = get_user_meta($user_id,"wpqa_subscr_id",true);
										if ($subscr_id != "") {
											$canceled_subscription = get_user_meta($user_id,"wpqa_canceled_subscription",true);
											if ($canceled_subscription == true) {
												$package_subscribe = "";
												$add_canceled_message = true;
											}else {
												$add_cancel_message = true;
											}
										}
									}
									if ($change_subscription == "on") {
										$add_change_plan = true;
									}
								}
								if (empty($custom_args) && (!isset($canceled_subscription) || !isset($_GET["change"]) || (isset($canceled_subscription) && $canceled_subscription != true))) {
									echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Your membership will be ended on:","wpqa").' '.date($date_format,$end_subscribe_time).'</p></div>';
								}
							}
							if (isset($add_canceled_message)) {
								echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("You have canceled your subscription, your membership will be canceled on:","wpqa").' '.date($date_format,$end_subscribe_time).'</p></div>';
							}else if (isset($add_change_plan) && !isset($_GET["change"])) {
								echo '<p class="change-plan"><a class="button-default-3 btn btn__primary mb-3" href="'.esc_url_raw(add_query_arg(array('change' => 'plan'))).'">'.esc_html__("Change your plan","wpqa").'</a></p>';
							}
							if (isset($add_cancel_message)) {
								$payment_method = get_user_meta($user_id,"wpqa_payment_method",true);
								if ($payment_method == "Stripe" || $payment_method == "PayPal") {
									$subscr_id = get_user_meta($user_id,"wpqa_subscr_id",true);
									if (($payment_method == "Stripe" && strpos($subscr_id,'sub_') !== false) || $payment_method == "PayPal") {
										echo '<p class="cancel-subscription"><a class="button-default-2 btn btn__secondary mb-3" href="#">'.esc_html__("Cancel your subscription","wpqa").'</a></p>';
									}else {
										echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("You need to contact the admin to cancel your subscription.","wpqa").'</p></div>';
									}
								}
							}
						}else if ($trial_subscription == "on" && !is_super_admin($user_id)) {
							$account_activation = wpqa_check_user_account_activation($user_id);
							if ($account_activation == "done" && $trial_subscribe == "" && $activate_register != "disabled") {
								$trial_subscription_rang = wpqa_options("trial_subscription_rang");
								$trial_subscription_plan = wpqa_options("trial_subscription_plan");
								if ($trial_subscription_plan == "hour") {
									$package_trial = sprintf(_n("%s hour","%s hours",$trial_subscription_rang,"wpqa"),$trial_subscription_rang);
								}else if ($trial_subscription_plan == "week") {
									$package_trial = sprintf(_n("%s week","%s weeks",$trial_subscription_rang,"wpqa"),$trial_subscription_rang);
								}else if ($trial_subscription_plan == "month") {
									$package_trial = sprintf(_n("%s month","%s months",$trial_subscription_rang,"wpqa"),$trial_subscription_rang);
								}
								echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.sprintf(esc_html__("You will get a free trial membership for %s.","wpqa"),$package_trial).'</p></div>
								<p class="trial-plan"><a class="button-default-3 btn btn__secondary mb-3'.(!is_user_logged_in()?' signup-panel '.apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_signup',''):'').'" href="'.(!is_user_logged_in()?wpqa_signup_permalink():esc_url_raw(add_query_arg(array('trial' => 'plan')))).'">'.esc_html__("Get a free trial","wpqa").'</a></p>';
							}
						}
						do_action("wpqa_before_subscription_plans",(isset($custom_args)?$custom_args:array()));
						$show_main_plans = apply_filters("wpqa_show_main_plans",true);
						if ($switch_yearly_monthly_plans == "on") {
							if ((($package_subscribe == "" || $package_subscribe == "hour" || $package_subscribe == "week" || $package_subscribe == "month" || (isset($_GET["change"]) && $_GET["change"] == "plan")))) {
								echo '<div class="subscription-switcher'.($subscriptions_style == "style_2"?" subscription-switcher-2":"").'">
									<button class="subscription-switcher-active" type="button" data-type="monthly"><span>'.esc_html__("Pay Monthly","wpqa").'</span></button>
									<div class="switch-button" data-type="yearly">
										<input class="switch-button-checkbox" type="checkbox"></input>
										<label class="switch-button-label" for=""><span class="switch-button-label-span"></span></label>
									</div>
									<button type="button" data-type="yearly"><span>'.esc_html__("Pay Yearly","wpqa").'</span></button>
									<span>'.esc_html__("SAVE MORE","wpqa").'</span>
								</div>';
							}
						}
						echo '<ul class="'.($subscriptions_style == "style_2"?"pricing-plan":"row row-warp row-boot list-unstyled mb-0")." subscriptions_".$subscriptions_style.'">';
							if ($show_main_plans == true && is_array($array) && !empty($array)) {
								foreach ($array as $key => $value) {
									if (((($wpqa_plan_key == $key && !isset($_GET["change"])) || $package_subscribe == "" || $package_subscribe == "hour" || $package_subscribe == "week" || $package_subscribe == "month" || (isset($_GET["change"]) && $_GET["change"] == "plan")) && ($key == "free" || (($switch_yearly_monthly_plans != "on" || ($switch_yearly_monthly_plans == "on" && isset($value["type"]) && ($value["type"] == "monthly" || $value["type"] == "yearly"))) && isset($value["type"]) && $value["type"] != "")))) {
										$plan_name = (isset($value["plan"]) && $value["plan"] != ""?$value["plan"]:(isset($value["role"])?ucfirst($value["role"]):"")).(isset($value["name"]) && $value["name"] != ""?(isset($value["role"])?" - ":"").$value["name"]:"");
										echo '<li id="li-subscribe-'.$key.'" class="li-subscribe-plans li-subscribe-'.$key.(isset($value["type"]) && $value["type"] != ""?' li-subscribe-plan-'.$value["type"]:'').($switch_yearly_monthly_plans == "on" && isset($value["type"]) && $value["type"] == "yearly"?' wpqa_hide':'').(isset($value["popular"]) && $value["popular"] == "on"?' li-subscribe-popular':'').($subscriptions_style == "style_2"?" subscriptions-plan":" col col12 col-boot-12").'">
											'.(isset($value["popular"]) && $value["popular"] == "on"?'<span class>'.esc_html__("Popular","wpqa").'</span>':'');
											if ($subscriptions_style == "style_2") {
												echo '<h2>'.$plan_name.'</h2>
												<div class="price">'.($payment_type_subscriptions == "points"?(int)$value['price'].' '.esc_html__("Points","wpqa"):floatval(($key == "free"?0:$value['price'])).' '.$currency_code).'</div>';
												if (isset($value["subscription_features"]) && is_array($value["subscription_features"]) && !empty($value["subscription_features"])) {
													echo '<ul class="features">';
														foreach ($value["subscription_features"] as $item_text) {
															if (isset($item_text["feature"]) && $item_text["feature"] != "") {
																echo '<li><i class="fas fa-check-circle"></i>'.$item_text["feature"].'</li>';
															}
														}
													echo '</ul>';
												}
											}
											if ($subscriptions_style != "style_2") {
												echo '<div class="point-section points-card subscribe-section subscribe-section-div'.(is_user_logged_in()?' subscribe-'.$key:'').($package_subscribe != "" && $package_subscribe != "hour" && $package_subscribe != "week" && $package_subscribe != "month"?" paid-subscribe":"").($wpqa_plan_key == $key && $key != "free"?" paid-subscribe-div":"").'">
													<div class="point-div">
														<span class="points__count mr-2">'.$plan_name.'</span>
														<span class="points__name mr-2">'.($key == "free"?esc_html__("Free","wpqa"):esc_html__("Paid","wpqa")).'</span>
														<span class="points-price points__count">'.($payment_type_subscriptions == "points"?(int)$value['price'].' '.esc_html__("Points","wpqa"):floatval(($key == "free"?0:$value['price'])).' '.$currency_code).'</span>
													</div>
													<div class="buy-points-content">';
											}
													if ($key == "free") {
														if (!is_user_logged_in() && $activate_register != "disabled") {
															echo '<a href="'.wpqa_signup_permalink().'" class="button-default btn btn__sm btn__info signup-panel '.apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_signup','').'">'.esc_html__("Sign Up","wpqa").'</a>';
														}
													}else if ($wpqa_plan_key == $key) {
														echo '<div class="alert-message alert-message-warning margin_0"><i class="icon-flag"></i><p>'.($subscriptions_style == "style_2"?esc_html__("Your plan.","wpqa"):esc_html__("This is your plan.","wpqa")).'</p></div>';
													}else if ($package_subscribe == "" || $package_subscribe == "hour" || $package_subscribe == "week" || $package_subscribe == "month" || (isset($_GET["change"]) && $_GET["change"] == "plan")) {
														if (!is_user_logged_in() && $unlogged_pay != "on") {
															if ($activate_register != "disabled") {
																echo '<a data-subscribe="'.$key.'" href="'.wpqa_signup_permalink().'" class="button-default btn btn__sm btn__info signup-panel subscribe-signup '.apply_filters('wpqa_pop_up_class','').apply_filters('wpqa_pop_up_class_signup','').'">'.esc_html__("Subscribe","wpqa").'</a>';
															}
														}else {
															if ($value['price'] > 0) {
																if ($payment_type_subscriptions == "points") {
																	echo wpqa_process_points_form(wpqa_subscriptions_permalink(),"subscribe",$value['price'],0,"","",$key);
																}else {
																	echo '<a href="'.wpqa_checkout_link("subscribe",$value["type"],$value["key"]).'" target="'.$payment_pages_target.'" class="button-default btn btn__sm btn__info">'.esc_html__("Subscribe","wpqa").'</a><div class="clearfix"></div>';
																}
															}
														}
													}
											if ($subscriptions_style != "style_2") {
													echo '</div>
												</div>';
											}
										echo '</li>';
									}
								}
							}
						echo '</ul>

						<ul class="row row-warp row-boot list-unstyled mb-0">';
							$roles_array = array(
								"ask_question"           => esc_html__("Can ask a question.","wpqa"),
								"ask_question_payment"   => esc_html__("Can ask a question without payment.","wpqa"),
								"add_poll"               => esc_html__("Can add a poll.","wpqa"),
								"ask_other_users"        => esc_html__("Can ask other users questions.","wpqa"),
								"show_question"          => esc_html__("Can view questions.","wpqa"),
								"add_answer"             => esc_html__("Can add an answer.","wpqa"),
								"add_answer_payment"     => esc_html__("Can add an answer without payment.","wpqa"),
								"show_answer"            => esc_html__("Can view answers.","wpqa"),
								"add_group"              => esc_html__("Can create a group.","wpqa"),
								"add_group_payment"      => esc_html__("Can create a group without payment.","wpqa"),
								"add_post"               => esc_html__("Can add a post.","wpqa"),
								"add_post_payment"       => esc_html__("Can add a post without payment.","wpqa"),
								"add_category"           => esc_html__("Can add a category.","wpqa"),
								"send_message"           => esc_html__("Can send message.","wpqa"),
								"upload_files"           => esc_html__("Can upload files.","wpqa"),
								"approve_question"       => esc_html__("Can get auto approve to your questions.","wpqa"),
								"approve_answer"         => esc_html__("Can get auto approve to your answers.","wpqa"),
								"approve_group"          => esc_html__("Can get auto approve to your group.","wpqa"),
								"edit_other_groups"      => esc_html__("Can edit any group with full editing permissions.","wpqa"),
								"approve_post"           => esc_html__("Can get auto approve to your posts.","wpqa"),
								"approve_comment"        => esc_html__("Can get auto approve to your comments.","wpqa"),
								"approve_question_media" => esc_html__("Can get auto approve to your questions when media is attached.","wpqa"),
								"approve_answer_media"   => esc_html__("Can get auto approve to your answers when media is attached.","wpqa"),
								"pay_to_user"            => esc_html__("Can withdrawal points to money.","wpqa"),
								"pay_maximum"            => esc_html__("Can withdrawal the maximum payment.","wpqa"),
								"without_ads"            => esc_html__("You will not see any ads on the site.","wpqa"),
								"show_post"              => esc_html__("Can view posts.","wpqa"),
								"add_comment"            => esc_html__("Can add a comment.","wpqa"),
								"show_comment"           => esc_html__("Can view comments.","wpqa"),
							);
							$activate_knowledgebase = apply_filters("wpqa_activate_knowledgebase",false);
							if ($activate_knowledgebase == true) {
								$roles_array["show_knowledgebase"] = esc_html__("Can view articles.","wpqa");
							}
							$activate_features_subscriptions = wpqa_options("activate_features_subscriptions");
							$features_subscriptions_type = wpqa_options("features_subscriptions_type");
							$features_subscriptions_text = wpqa_options("features_subscriptions_text");
							$features_subscriptions = wpqa_options("features_subscriptions");
							if ($activate_features_subscriptions == "on" && $features_subscriptions_type == "permissions") {
								$subscription_role_options = wpqa_subscription_role_options("role");
								if (is_array($subscription_role_options) && !empty($subscription_role_options)) {
									foreach ($subscription_role_options as $key => $value) {
										if ($key != "free" && $key != "") {
											echo '<li class="col col12 col-boot-12">
												<div class="point-section points-card subscribe-section subscribe-paid">
													<div class="point-div subscribe-title"><span>'.$value.'</span></div>
													<ul class="mb-0 list-with-arrows list-unstyled">';
														$roles_can = (isset($roles[$key]) && is_array($roles[$key]) && !empty($roles[$key])?$roles[$key]:array());
														if (is_array($roles_can) && !empty($roles_can)) {
															foreach ($roles_can as $roles_key => $roles_value) {
																if ($roles_value == "on" && isset($roles_array[$roles_key])) {
																	echo '<li class="paid-membership">'.$roles_array[$roles_key].'</li>';
																}
															}
														}
													echo '</ul>
												</div>
											</li>';
										}
									}
								}
							}else if ($activate_features_subscriptions == "on" && (($features_subscriptions_type == "list" && is_array($features_subscriptions) && !empty($features_subscriptions)) || ($features_subscriptions_type == "text" && $features_subscriptions_text != ""))) {
								echo '<li class="col col12 col-boot-12">
									<div class="point-section points-card subscribe-section subscribe-paid">
										<div class="point-div subscribe-title"><span>'.esc_html__("Memberships Features","wpqa").'</span></div>';
										if ($features_subscriptions_type == "list" && is_array($features_subscriptions) && !empty($features_subscriptions)) {
											echo '<ul class="mb-0 list-with-arrows list-unstyled">';
												if ($features_subscriptions_type == "list" && is_array($features_subscriptions) && !empty($features_subscriptions)) {
													foreach ($features_subscriptions as $item_text) {
														if (isset($item_text["text"]) && $item_text["text"] != "") {
															echo '<li class="paid-membership">'.$item_text["text"].'</li>';
														}
													}
												}
											echo '</ul>';
										}else if ($features_subscriptions_type == "text" && $features_subscriptions_text != "") {
											echo do_shortcode(wpqa_kses_stip(nl2br(stripslashes($features_subscriptions_text)),"yes","yes"));
										}
									echo '</div>
								</li>';
							}
						echo '</ul>
					</div><!-- End subscriptions-section -->
				</div><!-- End page-wrap-content -->';
			}else {
				echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, this page is not available.","wpqa").'</p></div>';
			}?>
		</div><!-- End page-section -->
	</div><!-- End page-sections -->
</div><!-- End wpqa-subscriptions-template -->

<?php $activate_faqs_subscriptions = wpqa_options("activate_faqs_subscriptions");
if ($subscriptions_payment == "on" && $activate_faqs_subscriptions == "on") {
	$faqs_subscriptions_text = wpqa_options("faqs_subscriptions_text");
	$custom_faqs = wpqa_options("faqs_subscriptions");?>
	<div class="page-sections">
		<div class="page-section page-section-faqs">
			<div class="page-wrap-content">
				<h2 class="post-title-3"><i class="icon-help-circled"></i><?php esc_html_e("Frequently Asked Questions","wpqa")?></h2>
				<?php if ($faqs_subscriptions_text != "") {?>
					<div class="post-content-text">
						<?php echo do_shortcode($faqs_subscriptions_text);?>
					</div>
				<?php }
				include wpqa_get_template("faqs.php","theme-parts/");
				?>
			</div><!-- End page-wrap-content -->
		</div><!-- End page-section -->
	</div>
<?php }?>

<?php do_action("wpqa_after_subscriptions");?>