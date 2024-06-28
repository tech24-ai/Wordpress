<?php $show_header_menu = apply_filters("discy_show_header_menu",true,$user_id);
if ($show_header_menu == true && !isset($user_link_only) && has_wpqa()) {?>
	<i class="<?php echo apply_filters("discy_profile_menu_icon","icon-down-open-mini")?>"></i>
	<?php include wpqa_get_template("header-profile-menu.php","profile/");
}?>