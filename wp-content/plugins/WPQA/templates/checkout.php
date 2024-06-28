<?php

/* @author    2codeThemes
*  @package   WPQA/templates
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action("wpqa_before_checkout");

$unlogged_pay = wpqa_options("unlogged_pay");
echo "<div class='wpqa-templates wpqa-checkout-template ".(!is_user_logged_in() && $unlogged_pay == "on"?"checkout-unlogged":"")."'>
	<h2 class='post-title-3'><i class='icon-basket'></i>".esc_html__("Checkout","wpqa")."</h2>".
	wpqa_checkout_content($unlogged_pay)
."</div>";

do_action("wpqa_after_checkout");?>