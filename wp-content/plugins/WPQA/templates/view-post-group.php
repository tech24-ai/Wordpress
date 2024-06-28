<?php

/* @author    2codeThemes
*  @package   WPQA/templates
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action("wpqa_before_view_post_group");
$post_id = (int)get_query_var(apply_filters('wpqa_view_posts_group','view_post_group'));
$group_id = (int)get_post_meta($post_id,"group_id",true);

echo "<div class='wpqa-templates wpqa-view-post-group-template".($group_id > 0?"":" wpqa-default-template")."'>";
	if ($group_id > 0) {
		$user_id = get_current_user_id();
		$is_super_admin = is_super_admin($user_id);
		$group_moderators = get_post_meta($group_id,"group_moderators",true);
		$post_data = get_post($post_id);
		setup_postdata($post_data);
		include locate_template("theme-parts/content-group.php");
		wp_reset_postdata();
	}else {
		echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry no post has been selected or not found.","wpqa").'</p></div>';
	}
echo "</div>";

if ((isset($comments_args) && is_array($comments_args) && !empty($comments_args))) {
	if (isset($values) && is_array($values) && !empty($values)) {
		$count_post_all = max($values);
	}
	$max_page = ceil($count_post_all/$comments_per_page);
	$in_view_posts_group = wpqa_is_view_posts_group();
	if ($in_view_posts_group && $max_page > 1 && get_option('page_comments')) {?>
		<div class="clearfix"></div>
		<div class="pagination comments-pagination">
			<?php global $wp_rewrite;
			$args = array(
				'base'         => add_query_arg('page','%#%'),
				'format'       => '',
				'total'        => $max_page,
				'current'      => $paged,
				'echo'         => true,
				'type'         => 'plain',
				'add_fragment' => '#group-comments',
				'prev_text'    => '<i class="icon-arrow-left-b"></i>',
				'next_text'    => '<i class="icon-arrow-right-b"></i>'
			);
			if ($wp_rewrite->using_permalinks()) {
				$args['base'] = user_trailingslashit(trailingslashit(wpqa_custom_permalink($post_id,"view_group_posts","view_group_post")).'page/%#%/','page');
			}
			echo paginate_links($args);?>
		</div><!-- End comments-pagination -->
		<div class="clearfix"></div>
    <?php }
}

do_action("wpqa_after_view_post_group");?>