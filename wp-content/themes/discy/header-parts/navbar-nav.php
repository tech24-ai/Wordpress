<?php if (($header_search == "on" && $big_search != "on") || $header_search != "on") {
	$main_menu_s = ((is_single() || is_page()) && isset($main_menu)?$main_menu:"");
	$main_menu   = (isset($is_user_logged_in)?"header_menu_login":"header_menu");
	$main_menu_s = ($main_menu_s != "" && $main_menu_s != 0?$main_menu_s:"");?>
	<nav class="nav float_l" itemscope="" itemtype="https://schema.org/SiteNavigationElement">
		<h3 class="screen-reader-text"><?php echo esc_html(get_bloginfo('name','display'))?> <?php esc_html_e('Navigation','discy')?></h3>
		<?php wp_nav_menu(array('container' => '','container_class' => 'nav top-nav clearfix',($main_menu_s != ""?"menu":"theme_location") => ($main_menu_s != "" && $main_menu_s != 0?$main_menu_s:$main_menu),'fallback_cb' => 'discy_nav_fallback'));?>
	</nav><!-- End nav -->
<?php }?>