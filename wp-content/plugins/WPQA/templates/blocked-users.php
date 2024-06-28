<?php

/* @author    2codeThemes
*  @package   WPQA/templates
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action("wpqa_before_blocked_users");

$group_id = (int)get_query_var(apply_filters('wpqa_blocked_users','blocked_user'));
$post = get_post($group_id);
$user_id = get_current_user_id();
$is_super_admin = is_super_admin($user_id);
$group_moderators = get_post_meta($group_id,"group_moderators",true);
if ($user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
	$allow_group_moderators = true;
}
if ($is_super_admin || $post->post_author == $user_id || isset($allow_group_moderators)) {
	do_action("wpqa_group_tabs",$group_id,$user_id,$is_super_admin,$post->post_author,$group_moderators);
}
echo "<div class='wpqa-templates wpqa-blocked-user-template wpqa-default-template'>";
	if ($is_super_admin || $post->post_author == $user_id || isset($allow_group_moderators)) {
		$wpqa_sidebar = wpqa_sidebars("sidebar_where");
		$blocked_users_array = get_post_meta($group_id,"blocked_users_array",true);
		if (isset($blocked_users_array) && is_array($blocked_users_array) && !empty($blocked_users_array)) {
			echo "<div id='section-blocked-users' class='user-section row row-warp user-not-normal user-section-columns block-user-div'>
				<div class='row-boot'>";
					$number      = wpqa_options("users_per_page");
					$number      = (isset($number) && $number > 0?$number:get_option('posts_per_page'));
					$number      = apply_filters('users_per_page',$number);
					$paged       = wpqa_paged();
					$offset      = ($paged-1)*$number;
					$query       = new WP_User_Query(array('offset' => $offset,'number' => $number,'include' => $blocked_users_array));
					$total_query = $query->get_total();
					$total_pages = ceil($total_query/$number);
					$current     = max(1,$paged);
					$user_col    = "col6 col-boot-sm-6";
					if ($wpqa_sidebar == "full") {
						$user_col = "col3 col-boot-sm-4";
					}
					$get_results = $query->get_results();
					foreach ($get_results as $user) {
						echo "<div class='col col-boot ".$user_col."'>".wpqa_author($user->ID,"columns","","","","","","","","",$group_id,"block",$blocked_users_array,$group_moderators)."</div>";
					}
				echo "</div>
			</div>";
			
			if ($total_pages > 1) {
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
				echo '<div class="pagination-users main-pagination"><div class="pagination">'.paginate_links($pagination_args).'</div></div>';
			}
			if (empty($query)) {
				echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Group doesn't have any blocked users yet.","wpqa").'</p></div>';
			}
		}else {
			echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Group doesn't have any blocked users yet.","wpqa").'</p></div>';
		}
	}else {
		echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry, this is a private page.","wpqa").'</p></div>';
	}
echo "</div>";

do_action("wpqa_after_blocked_users");?>