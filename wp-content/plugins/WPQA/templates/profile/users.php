<?php

/* @author    2codeThemes
*  @package   WPQA/templates/profile
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "<div id='section-".wpqa_user_title()."' class='block-user-div section-page-div user-section user-section-".$user_style_pages.($user_style_pages == "grid" || $user_style_pages == "small" || $user_style_pages == "columns" || $user_style_pages == "small_grid"?" row row-warp".(has_discy() && $masonry_user_style == "on"?" users-masonry":""):"").($user_style_pages != "normal"?" user-not-normal":"")."'>
	<div class='".($user_style_pages == "grid" || $user_style_pages == "small" || $user_style_pages == "columns" || $user_style_pages == "small_grid"?" row-boot".((has_himer() || has_knowly()) && $masonry_user_style == "on"?" users-masonry":""):"")."'>";
		$number         = wpqa_options("users_per_page");
		$number         = (isset($number) && $number > 0?$number:get_option('posts_per_page'));
		$number         = apply_filters('users_per_page',$number);
		$paged          = wpqa_paged();
		$offset		    = ($paged-1)*$number;
		$users		    = get_users(array('include' => $get_users,'orderby' => 'registered'));
		$query          = get_users(array('offset' => $offset,'number' => $number,'include' => $get_users,'orderby' => 'registered'));
		$total_users    = count($users);
		$total_query    = count($query);
		$total_pages    = ceil($total_users/$number);
		$current        = max(1,$paged);
		$blocking_class = (isset($blocking_class)?array("class" => $blocking_class):array());
		$user_col = "col6 col-boot-sm-6";
		if ($user_style_pages == "small_grid" && $wpqa_sidebar != "full") {
			$user_col = "col4 col-boot-sm-4";
		}else if ($wpqa_sidebar == "full") {
			$user_col = "col3 col-boot-sm-3";
		}
		foreach ($query as $user) {
			$owner_follow = false;
			if (get_current_user_id() == $user->ID) {
				$owner_follow = true;
			}
			echo ($user_style_pages == "grid" || $user_style_pages == "small" || $user_style_pages == "columns" || $user_style_pages == "small_grid"?"<div class='col col-boot ".$user_col.($masonry_user_style == "on"?" user-masonry":"")."'>":"");
				do_action("wpqa_author",array_merge($blocking_class,array("user_id" => $user->ID,"author_page" => $user_style_pages,"owner" => $owner_follow)));
			echo ($user_style_pages == "grid" || $user_style_pages == "small" || $user_style_pages == "columns" || $user_style_pages == "small_grid"?"</div>":"");
		}
	echo "</div>
</div>";

if ($total_users > $total_query) {
	$pagination_args = array(
		'current'   => $current,
		'show_all'  => false,
		'total'     => $total_pages,
		'prev_text' => '<i class="icon-left-open"></i>',
		'next_text' => '<i class="icon-right-open"></i>',
	);
	if (!get_option('permalink_structure')) {
		$pagination_args['base'] = esc_url_raw(add_query_arg('paged','%#%'));
	}
	echo '<div class="'.($user_style_pages == "grid" || $user_style_pages == "small" || $user_style_pages == "columns" || $user_style_pages == "small_grid"?"pagination-users ":"").'main-pagination"><div class="pagination">'.paginate_links($pagination_args).'</div></div>';
}
if (empty($query)) {
	echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>';
	if (wpqa_is_user_followers() || $last_one == "followers") {
		esc_html_e("User doesn't have any followers yet.","wpqa");
	}else if (wpqa_is_user_blocking() || $last_one == "blocking") {
		esc_html_e("You don't have any blocking users yet.","wpqa");
	}else {
		esc_html_e("User doesn't follow anyone.","wpqa");
	}
	echo '</p></div>';
}?>