<?php

/* @author    2codeThemes
*  @package   WPQA/payments
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Packages payments */
function wpqa_packages_payment($user_id,$item_id,$payment_type,$asked_user = 0) {
	$output = '';
	$activate_login = wpqa_options("activate_login");
	$unlogged_pay = wpqa_options("unlogged_pay");
	$payment_type = wpqa_options($payment_type);
	$packages_payment = wpqa_options($item_id);
	if ($item_id == "ask_packages") {
		$item_name = "buy_questions";
		$item_description = esc_html__("Buy questions","wpqa");
	}else if ($item_id == "group_packages") {
		$item_name = "buy_groups";
		$item_description = esc_html__("Buy groups","wpqa");
	}else if ($item_id == "post_packages") {
		$item_name = "buy_posts";
		$item_description = esc_html__("Buy posts","wpqa");
	}
	$currency_code = wpqa_get_currency($user_id);
	$currency = (wpqa_options("activate_currencies") == "on"?"_".strtolower($currency_code):"");
	$payment_pages_target = wpqa_options("payment_pages_target");
	$payment_pages_target = ($payment_pages_target == "new_page"?"_blank":"_self");
	if (isset($packages_payment) && is_array($packages_payment)) {
		$output .= '<div class="points-section buy-points-section buy-packages-section">
			<ul class="row row-warp row-boot list-unstyled mb-0">';
				foreach ($packages_payment as $key => $value) {
					if (isset($value["package_posts"]) && $value["package_posts"] > 0) {
						if (isset($value["package_points"]) && $value["package_points"] > 0) {
							$price_points = sprintf(_n("%s Point","%s Points",$value["package_points"],"wpqa"),wpqa_count_number($value["package_points"]));
						}
						if ($payment_type == "payments" || $payment_type == "payments_points") {
							$price = (isset($value["package_price".$currency])?$value["package_price".$currency]:(isset($value["package_price"])?$value["package_price"]:""));
							$price = floatval($price).' '.$currency_code;
						}else if ($payment_type == "points" && isset($price_points) && isset($value["package_points"]) && $value["package_points"] > 0) {
							$price = $price_points;
						}
						$output .= '<li class="col col12 col-boot-12">
							<div class="point-section point-section-div points-card buy-points-card">
								<div>
									<div class="point-div d-flex align-items-center">
										<i class="icon-basket points__icon mr-3"></i>
										<div>';
											if (isset($price) && $price >= 0) {
												$show_payment = true;
												$output .= '<span class="points__count mr-2">'.$price.'</span>';
											}
											$output .= '<span class="points__name mr-2">'.esc_html($value["package_name"]).'</span>';
											if ($payment_type == "payments_points" && isset($value["package_points"]) && $value["package_points"] >= 0) {
												$show_payment = true;
												$output .= '<span class="points__count points-price">'.$price_points.'</span>';
											}
											if (has_himer() || has_knowly()) {
												$output .= '<p class="points__desc">'.wpqa_kses_stip($value["package_description"]).'</p>';
											}
										$output .= '</div>
									</div>';
									if (has_discy()) {
										$output .= '<p class="points__desc">'.wpqa_kses_stip($value["package_description"]).'</p>';
									}
								$output .= '</div>
								<div class="buy-points-content">';
									if (is_user_logged_in() || (!is_user_logged_in() && $unlogged_pay == "on")) {
										if (isset($show_payment)) {
											$output .= '<a href="'.wpqa_checkout_link($item_name,(int)$value["package_posts"],(isset($asked_user) && $asked_user > 0?$asked_user:"")).'" target="'.$payment_pages_target.'" class="button-default btn btn__primary btn__sm">'.$item_description.'</a>
											<div class="clearfix"></div>';
										}
									}else if ($activate_login != 'disabled') {
										$output .= '<a href="#" class="button-default btn btn__sm btn__info login-panel">'.esc_html__('Sign In','wpqa').'</a>';
									}
								$output .= '</div>
							</div>
						</li>';
					}
				}
			$output .= '</ul>
		</div><!-- End buy-points-section -->';
	}
	return apply_filters("wpqa_packages_payment",$output,$user_id,$item_id,$payment_type,$asked_user);
}?>