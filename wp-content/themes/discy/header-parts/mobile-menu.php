<?php $sidebar_where = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars("sidebar_where"):"");
$mobile_menu = discy_options("mobile_menu");
$active_mobile_menu = discy_options("active_mobile_menu");
$sort_mobile_menus = discy_options("sort_mobile_menus");
$mobile_menu_logged = discy_options("mobile_menu_logged");
$live_search = discy_options("live_search");
$big_search = discy_options("big_search");
if (isset($wp_page_template) && $wp_page_template == "template-landing.php") {
	$mobile_menu = discy_post_meta("register_menu");
}?>
<aside class="mobile-aside mobile-menu-main mobile-menu-wrap<?php echo ($mobile_menu == "light"?" light-mobile-menu":($mobile_menu == "dark"?" dark-mobile-menu":" gray-mobile-menu"))?>" data-menu="mobile-menu-main">
	<h3 class="screen-reader-text"><?php esc_html_e('Mobile menu','discy')?></h3>
	<div class="mobile-aside-inner">
		<div class="mobile-aside-inner-inner">
			<a href="#" class="mobile-aside-close"><i class="icon-cancel"></i><span class="screen-reader-text"><?php esc_html_e("Close","discy")?></span></a>
			<?php if (has_wpqa()) {
				$mobile_button_menu = discy_options("mobile_button_menu");
				if ($mobile_button_menu == "on") {
					$mobile_button_menu_type = discy_options("mobile_button_menu_type");
					$mobile_button_menu_target = discy_options("mobile_button_menu_target");
					$mobile_button_menu_link = discy_options("mobile_button_menu_link");
					$mobile_button_menu_text = discy_options("mobile_button_menu_text");

					$mobile_button_menu_type = ($mobile_button_menu_type != ""?$mobile_button_menu_type:"question");
					if ($mobile_button_menu_type == "question") {
						$filter_class = "question";
						$mobile_button_menu_class = "wpqa-question";
						$mobile_button_menu_link = (has_wpqa()?wpqa_add_question_permalink():"#");
						$mobile_button_menu_text = esc_html__("Ask A Question","discy");
					}else if ($mobile_button_menu_type == "poll") {
						$filter_class = "question";
						$mobile_button_menu_class = "wpqa-question";
						$mobile_button_menu_link = (has_wpqa()?esc_url_raw(add_query_arg(array("type" => "poll"),wpqa_add_question_permalink())):"#");
						$mobile_button_menu_text = esc_html__("Add A Poll","discy");
					}else if ($mobile_button_menu_type == "post") {
						$filter_class = "post";
						$mobile_button_menu_class = "wpqa-post";
						$mobile_button_menu_link = (has_wpqa()?wpqa_add_post_permalink():"#");
						$mobile_button_menu_text = esc_html__("Add A New Post","discy");
					}else if ($mobile_button_menu_type == "group") {
						$filter_class = "group";
						$mobile_button_menu_class = "wpqa-group";
						$mobile_button_menu_link = (has_wpqa()?wpqa_add_group_permalink():"#");
						$mobile_button_menu_text = esc_html__("Create A New Group","discy");
					}else {
						$filter_class = $mobile_button_menu_class = "";
						$mobile_button_menu_target = discy_options("mobile_button_menu_target");
						$mobile_button_menu_link = discy_options("mobile_button_menu_link");
						$mobile_button_menu_text = discy_options("mobile_button_menu_text");
					}
					$mobile_button_menu_target = ($mobile_button_menu_type == "custom" && isset($mobile_button_menu_target) && $mobile_button_menu_target == "new_page"?"_blank":"_self");?>
					<div class="mobile-menu-top mobile--top">
						<div class="widget widget_ask">
							<a target="<?php echo esc_attr($mobile_button_menu_target)?>" class="button-default <?php echo esc_attr($mobile_button_menu_class)?> <?php echo apply_filters('wpqa_pop_up_class','').(isset($filter_class) && $filter_class != ''?apply_filters('wpqa_pop_up_class_'.$filter_class,''):'')?>" href="<?php echo esc_url($mobile_button_menu_link)?>"><?php echo esc_html($mobile_button_menu_text)?></a>
						</div>
					</div><!-- End mobile-menu-top -->
				<?php }
			}
			if ($active_mobile_menu == "on") {
				wp_nav_menu(array('container' => '','container_class' => 'nav_menu','menu_id' => 'nav_menu','menu' => (is_user_logged_in()?$mobile_menu_logged:$mobile_menu),'fallback_cb' => 'discy_nav_fallback'));
			}
			if ($active_mobile_menu != "on") {
				if (is_array($sort_mobile_menus) && !empty($sort_mobile_menus)) {
					foreach ($sort_mobile_menus as $key => $value) {
						if (isset($value["value"]) && $value["value"] == "left" && !isset($its_not_login) && ($sidebar_where == "menu_sidebar" || $sidebar_where == "menu_left")) {
							$left_menu_s = (is_single() || is_page()?discy_post_meta("left_menu"):"");
							$left_menu   = (is_user_logged_in()?"discy_explore_login":"discy_explore");
							$left_menu_s = ($left_menu_s != "" && $left_menu_s != 0?$left_menu_s:"");?>
							<div class="mobile-menu-left">
								<?php wp_nav_menu(array('container' => '','container_class' => 'nav_menu','menu_id' => 'nav_menu',($left_menu_s != ""?"menu":"theme_location") => ($left_menu_s != "" && $left_menu_s != 0?$left_menu_s:$left_menu),'fallback_cb' => 'discy_empty_fallback'));?>
							</div><!-- End mobile-menu-left -->
						<?php }else if (isset($value["value"]) && $value["value"] == "top" && $big_search != "on") {?>
							<div class="mobile--top">
								<?php $register_menu_array = (isset($its_not_login)?array('menu' => $register_menu):array('theme_location' => (is_user_logged_in()?'header_menu_login':'header_menu')));
								wp_nav_menu(array_merge($register_menu_array,array('container' => '','container_class' => 'nav top-nav','fallback_cb' => 'discy_nav_fallback')));?>
							</div>
						<?php }
					}
				}
			}
			if (!isset($its_not_login)) {?>
				<div class="mobile--top post-search">
					<form role="search" method="get" class="searchform main-search-form" action="<?php do_action("wpqa_search_permalink")?>">
						<div class="row row-warp">
							<div class="col col10">
								<input type="search"<?php echo ($live_search == "on"?" class='live-search' autocomplete='off'":"")?> name="search" value="<?php if ($search_value != "") {echo esc_html($search_value);}else {esc_html_e("Hit enter to search","discy");}?>" onfocus="if(this.value=='<?php esc_attr_e("Hit enter to search","discy")?>')this.value='';" onblur="if(this.value=='')this.value='<?php esc_attr_e("Hit enter to search","discy")?>';">
								<?php if ($live_search == "on") {?>
									<div class="loader_2 search_loader"></div>
									<div class="search-results results-empty"></div>
								<?php }?>
								<input type="hidden" name="search_type" class="search_type" value="<?php do_action("wpqa_search_type")?>">
							</div><!-- End col6 -->
							<div class="wpqa_form col col2">
								<input type="submit" class="button-default" value="<?php esc_attr_e('Search','discy')?>">
							</div><!-- End col6 -->
						</div><!-- End row -->
					</form>
				</div>
			<?php }?>
		</div><!-- End mobile-aside-inner-inner -->
	</div><!-- End mobile-aside-inner -->
</aside><!-- End mobile-aside -->