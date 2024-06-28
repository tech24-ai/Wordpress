<?php if ($mobile_bar_layout == "top" && $mobile_bar_apps == "on" && ($mobile_bar_apps_iphone != "" || $mobile_bar_apps_android != "")) {?>
	<div class="mobile-bar mobile-bar-apps mobile-bar-apps-<?php echo esc_attr($mobile_apps_bar_skin)?>">
		<div class="the-main-container">
			<div class="mobile-bar-content">
				<div class="the-main-container">
					<div class="mobile-bar-apps-left">
						<span><?php esc_html_e("Open your app","discy")?></span>
					</div>
					<div class="mobile-bar-apps-right">
						<?php if ($mobile_bar_apps_iphone != "") {?>
							<a href="<?php echo esc_url($mobile_bar_apps_iphone)?>" target="_blank" title="<?php esc_attr_e("iPhone","discy")?>"><i class="fab fa-apple"></i></a>
						<?php }
						if ($mobile_bar_apps_android != "") {?>
							<a href="<?php echo esc_url($mobile_bar_apps_android)?>" target="_blank" title="<?php esc_attr_e("Android","discy")?>"><i class="fab fa-android"></i></a>
						<?php }?>
					</div>
				</div><!-- End the-main-container -->
			</div><!-- End mobile-bar-content -->
		</div><!-- End the-main-container -->
	</div><!-- End mobile-bar -->
<?php }else if ($mobile_bar_layout == "bottom" && $confirm_email != "yes" && $mobile_bar == "on") {?>
	<div class="mobile-bar main-mobile-bar">
		<div class="the-main-container">
			<div class="mobile-bar-content">
				<div class="the-main-container">
					<div class="mobile-bar-search">
						<a href="<?php do_action("wpqa_search_permalink")?>"><i class="icon-search"></i><?php esc_html_e("Search","discy")?></a>
						<?php $show_search = apply_filters("discy_show_search",true);
						if ($show_search == true) {?>
							<form role="search" method="get" class="searchform main-search-form" action="<?php do_action("wpqa_search_permalink")?>">
								<i class="icon-left-open"></i>
								<input type="search"<?php echo ($live_search == "on"?" class='live-search' autocomplete='off'":"")?> name="search" value="<?php if ($search_value != "") {echo esc_html($search_value);}else {esc_attr_e("Hit enter to search","discy");}?>" onfocus="if(this.value=='<?php esc_attr_e("Hit enter to search","discy")?>')this.value='';" onblur="if(this.value=='')this.value='<?php esc_attr_e("Hit enter to search","discy")?>';">
								<?php if ($live_search == "on") {?>
									<div class="loader_2 search_loader"></div>
									<div class="search-results results-empty"></div>
								<?php }?>
								<input type="hidden" name="search_type" class="search_type" value="<?php do_action("wpqa_search_type")?>">
							</form>
						<?php }?>
					</div>
					<div class="mobile-bar-ask">
						<?php if (has_wpqa()) {
							$mobile_button = discy_options("mobile_button");
							$mobile_button = ($mobile_button != ""?$mobile_button:"question");
							if ($mobile_button == "question") {
								$icon_class = "icon-help-circled";
								$filter_class = "question";
								$mobile_button_class = "wpqa-question";
								$mobile_button_link = (has_wpqa()?wpqa_add_question_permalink():"#");
								$mobile_button_text = esc_html__("Ask A Question","discy");
							}else if ($mobile_button == "poll") {
								$icon_class = "icon-chart-bar";
								$filter_class = "question";
								$mobile_button_class = "wpqa-question";
								$mobile_button_link = (has_wpqa()?esc_url_raw(add_query_arg(array("type" => "poll"),wpqa_add_question_permalink())):"#");
								$mobile_button_text = esc_html__("Add A Poll","discy");
							}else if ($mobile_button == "post") {
								$icon_class = "icon-newspaper";
								$filter_class = "post";
								$mobile_button_class = "wpqa-post";
								$mobile_button_link = (has_wpqa()?wpqa_add_post_permalink():"#");
								$mobile_button_text = esc_html__("Add A New Post","discy");
							}else if ($mobile_button == "group") {
								$icon_class = "icon-network";
								$filter_class = "group";
								$mobile_button_class = "wpqa-group";
								$mobile_button_link = (has_wpqa()?wpqa_add_group_permalink():"#");
								$mobile_button_text = esc_html__("Create A New Group","discy");
							}else {
								$icon_class = "icon-newspaper";
								$filter_class = $mobile_button_class = "";
								$mobile_button_target = discy_options("mobile_button_target");
								$mobile_button_link = discy_options("mobile_button_link");
								$mobile_button_text = discy_options("mobile_button_text");
							}
							$mobile_button_target = ($mobile_button == "custom" && isset($mobile_button_target) && $mobile_button_target == "new_page"?"_blank":"_self");?>
							<a target="<?php echo esc_attr($mobile_button_target)?>" class="<?php echo esc_attr($mobile_button_class)?> <?php echo apply_filters('wpqa_pop_up_class','').(isset($filter_class) && $filter_class != ''?apply_filters('wpqa_pop_up_class_'.$filter_class,''):'')?>" href="<?php echo esc_url($mobile_button_link)?>"><i class="<?php echo esc_attr($icon_class)?>"></i><?php echo esc_html($mobile_button_text)?></a>
						<?php }?>
					</div>
				</div><!-- End the-main-container -->
			</div><!-- End mobile-bar-content -->
		</div><!-- End the-main-container -->
	</div><!-- End mobile-bar -->
<?php }?>