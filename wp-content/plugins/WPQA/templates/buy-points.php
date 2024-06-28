<?php

/* @author    2codeThemes
*  @package   WPQA/templates
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action("wpqa_before_buy_points");?>

<div class='wpqa-templates wpqa-buy-points-template'>
	<div class="page-sections">
		<div class="page-section">
			<?php $active_points = wpqa_options("active_points");
			$buy_points_payment = wpqa_options("buy_points_payment");
			if ($active_points == "on" && $buy_points_payment == "on") {
				$user_id = get_current_user_id();
				$buy_points = wpqa_options("buy_points");
				if (isset($buy_points) && is_array($buy_points)) {
					if ($user_id > 0 && isset($_POST["process"]) && $_POST["process"] == "points" && isset($_POST["package_points"]) && $_POST["package_points"] != "") {
						wpqa_add_points($user_id,(int)$_POST["package_points"],"+","buy_points");
						wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.esc_html__("You have got a new free points.","wpqa").'</p></div>','wpqa_session');
						wp_safe_redirect(esc_url(wpqa_get_profile_permalink($user_id,"points")));
						die();
					}
					echo wpqa_buy_points_section($buy_points);
				}
			}else {
				echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, this page is not available.","wpqa").'</p></div>';
			}?>
		</div><!-- End page-section -->
	</div><!-- End page-sections -->
</div><!-- End wpqa-buy-points-template -->

<?php do_action("wpqa_after_buy_points");?>