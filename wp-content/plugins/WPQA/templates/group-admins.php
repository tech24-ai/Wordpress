<?php

/* @author    2codeThemes
*  @package   WPQA/templates
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action("wpqa_before_group_admins");

$group_id = (int)get_query_var(apply_filters('wpqa_group_admins','group_admin'));
$post = get_post($group_id);
$user_id = get_current_user_id();
$is_super_admin = is_super_admin($user_id);
$group_moderators = get_post_meta($group_id,"group_moderators",true);
if ($user_id > 0 && isset($group_moderators) && is_array($group_moderators) && in_array($user_id,$group_moderators)) {
	$allow_group_moderators = true;
}
if ($is_super_admin || ($post->post_author == $user_id && $user_id > 0) || isset($allow_group_moderators)) {
	do_action("wpqa_group_tabs",$group_id,$user_id,$is_super_admin,$post->post_author,$group_moderators);
}
if ($is_super_admin || ($post->post_author == $user_id && $user_id > 0)) {?>
	<div class="page-section add-user-form">
		<h2 class="post-title-2"><i class="icon-vcard"></i><?php esc_html_e("Assign a new moderator","wpqa")?></h2>
		<div class="row row-warp row-boot">
			<div class="col col9 col-boot col-boot-sm-9">
				<input data-id="<?php echo (int)$group_id?>" type="text" placeholder="<?php esc_html_e("Type a name or email","wpqa")?>" class="add-new-user add-new-moderator form-control">
				<div class="loader_2 search_loader user_loader"></div>
				<div class="live-search-results mt-2 search-results results-empty user-results"></div>
			</div>
			<div class="col col3 col-boot col-boot-sm-3 button-user-col user-col-not-activate">
				<div></div>
				<a type="text" data-nonce="<?php echo wp_create_nonce("wpqa_group_nonce")?>" class="button-default button-hide-click new-user-button new-moderator-button btn btn__primary w-100"><?php esc_html_e("Add","wpqa")?></a>
			<span class="load_span"><span class="loader_2"></span></span>
			</div>
		</div>
	</div>
<?php }
echo "<div class='wpqa-templates wpqa-group-admins-template wpqa-default-template'>";
	if ($is_super_admin || $post->post_author == $user_id) {
		$wpqa_sidebar = wpqa_sidebars("sidebar_where");
		if (isset($group_moderators) && is_array($group_moderators) && !empty($group_moderators)) {
			echo "<div id='section-group-admins' class='block-user-div user-section row row-warp user-not-normal user-section-columns'>
				<div class='row-boot'>";
					$number      = wpqa_options("users_per_page");
					$number      = (isset($number) && $number > 0?$number:get_option('posts_per_page'));
					$number      = apply_filters('users_per_page',$number);
					$paged       = wpqa_paged();
					$offset      = ($paged-1)*$number;
					$query       = new WP_User_Query(array('offset' => $offset,'number' => $number,'include' => $group_moderators));
					$total_query = $query->get_total();
					$total_pages = ceil($total_query/$number);
					$current     = max(1,$paged);
					$user_col    = "col6 col-boot-sm-6";
					if ($wpqa_sidebar == "full") {
						$user_col = "col3 col-boot-sm-4";
					}
					$get_results = $query->get_results();
					foreach ($get_results as $user) {
						echo "<div class='col col-boot ".$user_col."'>".wpqa_author($user->ID,"columns","","","","","","","","",$group_id,"moderators",array(),$group_moderators,$post->post_author)."</div>";
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
				echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Group doesn't have any admins yet.","wpqa").'</p></div>';
			}
		}else {
			echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Group doesn't have any admins yet.","wpqa").'</p></div>';
		}
	}else {
		echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry, this is a private page.","wpqa").'</p></div>';
	}
echo "</div>";

do_action("wpqa_after_group_admins");?>