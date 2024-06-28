<?php /* Template Name: Badges */
get_header();
	include locate_template("includes/header-part.php");
	if (has_wpqa() && wpqa_plugin_version >= "5.9.8") {
		include wpqa_get_template("logged-only.php","theme-parts/");
	}
	$page_id        = $post_id_main = $post->ID;
	$active_points  = discy_options("active_points");
	$badges_details = discy_options("badges_details");
	$badges_style   = discy_options("badges_style");
	$badges_groups  = discy_options("badges_groups");
	$badge_key = "badge_points";
	if ($badges_style == "by_groups_points") {
		$badges = discy_options("badges_groups_points");
	}else if ($badges_style == "by_questions") {
		$badges = discy_options("badges_questions");
		$badge_key = "badge_questions";
	}else if ($badges_style == "by_answers") {
		$badges = discy_options("badges_answers");
		$badge_key = "badge_answers";
	}else {
		$badges = discy_options("badges");
	}
	if (has_wpqa() && wpqa_plugin_version >= "5.9.8") {
		include wpqa_get_template("the-content.php","theme-parts/");
	}?>
	<div class="page-sections">
		<?php if ($active_points == "on") {
			$points_columns = discy_post_meta("badges_points_columns");
			$points_details = discy_options("points_details");
			$points_array = (has_wpqa()?wpqa_get_points():array());
			if (is_array($points_array) && !empty($points_array)) {?>
				<div class="page-section">
					<div class="page-wrap-content">
						<h2 class="post-title-3"><i class="icon-bucket"></i><?php esc_html_e("Points System","discy")?></h2>
						<?php if (isset($points_details) && $points_details != "") {?>
							<div class="post-content-text"><p><?php echo do_shortcode(discy_kses_stip(nl2br(stripslashes($points_details))))?></p></div>
						<?php }?>
						<div class="points-section">
							<ul class="row row-warp">
								<?php foreach ($points_array as $key => $value) {
									if (isset($value["points"]) && $value["points"] > 0) {
										$value_points = (int)$value["points"];?>
										<li class="col <?php echo ($points_columns == "2col"?"col6":"col4")?>">
											<div class="point-section">
												<div class="point-div">
													<i class="icon-bucket"></i>
													<span><?php echo discy_count_number($value_points)?></span><?php echo _n("Point","Points",$value_points,"discy")?>
												</div>
												<p><?php echo wpqa_get_points_name($key)?></p>
											</div>
										</li>
									<?php }
								}?>
							</ul>
						</div>
					</div><!-- End page-wrap-content -->
				</div><!-- End page-section -->
			<?php }
		}

		$buy_points_section = discy_post_meta("buy_points_section");
		if ($buy_points_section == "on") {
			$active_points = wpqa_options("active_points");
			$buy_points_payment = wpqa_options("buy_points_payment");
			if ($active_points == "on" && $buy_points_payment == "on") {
				$buy_points = wpqa_options("buy_points");
				echo '<div class="page-section page-section-buy-points">'.wpqa_buy_points_section($buy_points).'</div>';
			}
		}
		
		if (($badges_style != "by_groups" && isset($badges) && is_array($badges)) || ($badges_style == "by_groups" && isset($badges_groups) && is_array($badges_groups) && isset($badges_details) && $badges_details != "")) {?>
			<div class="page-section">
				<div class="page-wrap-content">
					<h2 class="post-title-3"><i class="icon-trophy"></i><?php esc_html_e("Badges System","discy")?></h2>
					<?php if (isset($badges_details) && $badges_details != "") {?>
						<div class="post-content-text"><p><?php echo do_shortcode(discy_kses_stip(nl2br(stripslashes($badges_details))))?></p></div>
					<?php }
					if ($badges_style != "by_groups") {?>
						<div class="badges-section">
							<ul>
								<?php $points_badges = array_column($badges,$badge_key);
								array_multisort($points_badges,SORT_ASC,$badges);
								foreach ($badges as $badges_k => $badges_v) {
									if (isset($badges_v[$badge_key]) && $badges_v[$badge_key] != "") {
										$badge_values = (int)$badges_v[$badge_key];?>
										<li>
											<div class="badge-section">
												<div class="badge-div">
													<span class="badge-span" style="background-color: <?php echo esc_html($badges_v["badge_color"])?>;"><?php echo strip_tags(stripslashes($badges_v["badge_name"]),"<i>")?></span>
													<div class="point-div">
														<i class="icon-bucket"></i>
														<span><?php echo discy_count_number($badge_values)?></span>
														<?php if ($badges_style == "by_questions") {
															echo _n("Question","Questions",$badge_values,"discy");
														}else if ($badges_style == "by_answers") {
															echo _n("Answer","Answers",$badge_values,"discy");
														}else {
															echo _n("Point","Points",$badge_values,"discy");
														}?>
													</div>
												</div>
												<p><?php echo discy_kses_stip(stripslashes($badges_v["badge_details"]))?></p>
											</div>
										</li>
									<?php }
								}?>
							</ul>
						</div>
					<?php }?>
				</div><!-- End page-wrap-content -->
			</div><!-- End page-section -->
		<?php }
		do_action("discy_after_badge_section");?>
	</div><!-- End page-sections -->
	<?php include locate_template("includes/footer-part.php");
get_footer();?>