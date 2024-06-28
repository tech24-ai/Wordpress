<?php

/* @author    2codeThemes
*  @package   WPQA/templates/profile
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$rows_per_page = get_option("posts_per_page");
$user_id = get_current_user_id();
$count_new_message = (int)get_user_meta($user_id,"wpqa_new_messages_count",true);?>
<div id='section-<?php echo wpqa_user_title()?>' class="section-page-div">
	<?php if (has_himer() || has_knowly()) {?>
		<div class="post-title-2 d-flex align-items-center justify-content-between">
			<h2 class="card-title mb-0 d-flex align-items-center">
				<i class="icon-android-mail font-xl card-title__icon"></i>
				<span><?php esc_html_e("Messages","wpqa")?></span>
			</h2>
		</div>
	<?php }?>
	<div class="answers-tabs messages-tabs">
		<?php if (has_discy()) {?>
			<h3 class="section-title"><?php esc_html_e("Messages","wpqa")?></h3>
		<?php }?>
		<div class="answers-tabs-inner messages-tabs-inner">
			<ul>
				<li<?php echo (empty($_GET["show"]) || (isset($_GET["show"]) && $_GET["show"] != "send")?' class="active-tab"':'')?>><a href="<?php echo esc_url_raw(wpqa_messages_permalink())?>"><?php esc_html_e("Inbox","wpqa")?> <?php echo($count_new_message > 0?"<span>( ".$count_new_message." )</span>":"")?></a></li>
				<li<?php echo (isset($_GET["show"]) && $_GET["show"] == "send"?' class="active-tab"':'')?>><a href="<?php echo esc_url_raw(add_query_arg("show","send"),wpqa_messages_permalink())?>"><?php esc_html_e("Sent","wpqa")?></a></li>
			</ul>
		</div><!-- End answers-tabs-inner -->
		<div class="clearfix"></div>
	</div>
	
	<?php $time_format = wpqa_options("time_format");
	$time_format = ($time_format?$time_format:get_option("time_format"));
	$date_format = wpqa_options("date_format");
	$date_format = ($date_format?$date_format:get_option("date_format"));
	$paged = wpqa_paged();
	if (isset($_GET["show"]) && $_GET["show"] == "send") {
		$message_show = "send";
		$attrs = array("author" => $user_id,"meta_query" => array(array("key" => "delete_send_message","compare" => "NOT EXISTS"),array("key" => "message_user_array","compare" => "NOT EXISTS")));
	}else {
		$message_show = "inbox";
		$attrs = array("meta_query" => array('relation' => 'AND',array("key" => "delete_inbox_message","compare" => "NOT EXISTS"),array('relation' => 'OR',array("key" => "message_user_id","compare" => "=","value" => $user_id),array("key" => "message_user_".$user_id,"compare" => "EXISTS"))));
	}
	$args = array_merge(array('post_type' => 'message','posts_per_page' => $rows_per_page,'paged' => $paged),$attrs);
	$messages_query = new WP_Query( $args );
	if ($messages_query->have_posts()) {
		echo "<ol class='commentlist clearfix".($messages_query->max_num_pages > 1?" section-message-paged":"")."'>";
			while ( $messages_query->have_posts() ) { $messages_query->the_post();
				$message_post = $messages_query->post;
				$message_id = $message_post->ID;
				$message_author = $message_post->post_author;
				$message_user_id = get_post_meta($message_id,'message_user_id',true);
				$message_user_array = get_post_meta($message_id,'message_user_array',true);
				$message_delete = wpqa_options("message_delete");
				if (isset($_GET['wpqa_delete_nonce']) && wp_verify_nonce($_GET['wpqa_delete_nonce'],'wpqa_delete_nonce') && ($message_delete == 1 || $message_delete == "on" || is_super_admin($user_id)) && isset($_GET) && isset($_GET["delete_message"]) && $_GET["delete_message"] == $message_id) {
					wpqa_delete_messages($message_id,$message_author,$user_id,$message_user_id,$message_user_array);
				}
				$get_gender_class = wpqa_get_gender_class($message_user_id,$message_id);
				$activate_male_female = apply_filters("wpqa_activate_male_female",false);
				if ($activate_male_female == true) {
					$info_edited = '';
					$gender_author = ($message_author > 0?get_user_meta($message_author,'gender',true):"");
					$gender_post = get_post_meta($message_id,'wpqa_post_gender',true);
					if ($gender_author != "" && $gender_author != $gender_post) {
						$info_edited = esc_html__("Some of main user info (Ex: name or gender) has been edited since this message has been sent","wpqa");
					}
				}?>
				<li class="comment<?php echo " ".$get_gender_class?>">
					<div class="comment-body clearfix">
						<div class="comment-text">
							<div class="d-flex align-items-center header-of-comment">
								<?php do_action("wpqa_action_avatar_link",array("user_id" => (isset($_GET["show"]) && $_GET["show"] == "send"?$message_user_id:$message_author),"size" => 42,"span" => "span","class" => "rounded-circle","pop" => "pop"));?>
								<div class="author clearfix">
									<div class="comment-meta">
										<div class="comment-author">
											<?php if (isset($_GET["show"]) && $_GET["show"] == "send") {
												$display_name = get_the_author_meta('display_name',$message_user_id);
												$deleted_user = ($message_user_id > 0 && $display_name != ""?$display_name:($message_user_id == 0?"":"delete"));
												$message_author_name = ($deleted_user == "delete" || $deleted_user == ""?esc_html__("[Deleted User]","wpqa"):$deleted_user);
												$user_profile_page = wpqa_profile_url($message_user_id);
											}else {
												$display_name = get_the_author_meta('display_name',$message_author);
												$deleted_user = ($message_author > 0 && $display_name != ""?$display_name:($message_author == 0?get_post_meta($message_id,'message_username',true):"delete"));
												$message_author_name = ($deleted_user == "delete" || $deleted_user == ""?esc_html__("[Deleted User]","wpqa"):$deleted_user);
												$user_profile_page = wpqa_profile_url($message_author);
											}
											echo ($user_profile_page != '' && $deleted_user != 'delete' && $deleted_user != ''?'<a href="'.wpqa_profile_url($message_author).'">':'').$message_author_name.($user_profile_page != '' && $deleted_user != 'delete' && $deleted_user != ''?'</a>':'');?>
										</div>
										<span class="comment-date"><?php echo sprintf(esc_html__('Added a message on %1$s at %2$s','wpqa'),get_the_time($date_format),get_the_time($time_format));?></span>
									</div>
								</div>
							</div>
							<div class="text">
								<h2 class="post-title message-title">
									<?php if (empty($_GET["show"]) || (isset($_GET["show"]) && $_GET["show"] != "send")) {
										if (is_array($message_user_array) && !empty($message_user_array) && in_array($user_id,$message_user_array)) {
											$message_not_new = get_post_meta($message_id,'message_not_new_'.$user_id,true);
										}
										$message_not_new = (isset($message_not_new) && $message_not_new != "" && $message_not_new != "no"?$message_not_new:"no");
										$message_new = get_post_meta($message_id,'message_new',true);?>
										<i class="message_new<?php echo ($message_new == 1 || $message_new == "on" || (is_array($message_user_array) && !empty($message_user_array) && in_array($user_id,$message_user_array) && $message_not_new == "no")?" message-new":"")?> icon-mail"></i>
									<?php }
									echo "<a href='#' class='view-message tooltip-n' title='".esc_html__("View the message","wpqa")."' data-id='".$message_id."' data-show='".$message_show."'><i class='message-open-close icon-plus'></i>".get_the_title()."</a>"?>
									<div class="small_loader_message small_loader loader_2"></div>
								</h2>
								<?php do_action("wpqa_action_message_after_title",$message_post)?>
								<div class="message-content"></div>
								<?php if (($message_author > 0 && (empty($_GET["show"]) || (isset($_GET["show"]) && $_GET["show"] != "send"))) || ((($message_author > 0 && $message_author == $user_id) || $message_user_id == $user_id || (is_array($message_user_array) && !empty($message_user_array) && in_array($user_id,$message_user_array))) && ($message_delete == 1 || $message_delete == "on" || is_super_admin($user_id)))) {?>
									<ul class="comment-reply">
										<?php if ($message_author > 0 && (empty($_GET["show"]) || (isset($_GET["show"]) && $_GET["show"] != "send"))) {
											$user_block_message = get_user_meta($message_author,"user_block_message",true);
											$received_message = get_user_meta($message_author,"received_message",true);
											$block_message = get_user_meta($message_author,"block_message",true);
											$custom_permission = wpqa_options("custom_permission");
											$send_message = wpqa_options("send_message");
											if (is_user_logged_in()) {
												$user_is_login = get_userdata($user_id);
												$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
											}
											if ($is_super_admin || $custom_permission != "on" || ($custom_permission == "on" && (is_user_logged_in() && !$is_super_admin && isset($roles["send_message"])) || (!is_user_logged_in() && $send_message == "on"))) {
												if (((is_user_logged_in() && (empty($user_block_message) || (isset($user_block_message) && is_array($user_block_message) && !in_array($user_id,$user_block_message))) && ($block_message != "on" || is_super_admin($user_id)) && isset($received_message) && $received_message == "on"))) {?>
													<li class="message-reply"><a href="#" data-width="690" data-id="<?php echo esc_attr($message_id)?>" data-user-id="<?php echo esc_attr($message_author)?>"><i class="icon-reply"></i><?php esc_html_e("Reply","wpqa");?></a></li>
												<?php }
											}
										}
										if ((($message_author > 0 && $message_author == $user_id) || $message_user_id == $user_id || (is_array($message_user_array) && !empty($message_user_array) && in_array($user_id,$message_user_array))) && ($message_delete == 1 || $message_delete == "on" || is_super_admin($user_id))) {?>
											<li class="message-delete"><a href="<?php echo (empty($_GET["show"]) || (isset($_GET["show"]) && $_GET["show"] != "send")?esc_url_raw(add_query_arg(array("activate_delete" => true,"delete_message" => $message_id,"wpqa_delete_nonce" => wp_create_nonce("wpqa_delete_nonce")),wpqa_messages_permalink())):esc_url_raw(add_query_arg(array("activate_delete" => true,"delete_message" => $message_id,"wpqa_delete_nonce" => wp_create_nonce("wpqa_delete_nonce")),add_query_arg("show","send"),wpqa_messages_permalink())))?>" data-id="<?php echo esc_attr($message_id)?>"><i class="icon-trash"></i><?php esc_html_e("Delete","wpqa");?></a></li>
										<?php }
										if ($message_author > 0 && (empty($_GET["show"]) || (isset($_GET["show"]) && $_GET["show"] != "send"))) {
											$user_block_message = get_user_meta($user_id,"user_block_message",true);?>
											<li class="message-block"><i class="icon-cancel"></i>
												<?php echo '<a href="#" class="block_message block_message_'.$message_author.($message_user_id != $message_author && $message_author > 0 && isset($user_block_message) && is_array($user_block_message) && in_array($message_author,$user_block_message)?' unblock_message':'').'" data-id="'.(int)$message_author.'" data-nonce="'.wp_create_nonce("block_message_nonce").'">'.($message_user_id != $message_author && $message_author > 0 && isset($user_block_message) && is_array($user_block_message) && in_array($message_author,$user_block_message)?esc_html__("Unblock Message","wpqa"):esc_html__("Block Message","wpqa")).'</a>';?>
											</li>
										<?php }?>
										<li class="clearfix"></li>
									</ul>
								<?php }
								if (isset($info_edited) && $info_edited != "") {
									echo '<i class="icon-alert tooltip-n icon-info-edited" title="'.$info_edited.'"></i>';
								}?>
							</div>
						</div>
					</div>
				</li>
			<?php }
		echo "</ol>";
	}else {
		echo "<p class='no-item'>".esc_html__("There are no messages yet.","wpqa")."</p>";
	}

	if ($messages_query->max_num_pages > 1) :
		$current = max(1,$paged);
		$count = $rows_per_page;
		$found_posts = (int) $messages_query->found_posts;
		$max_num_pages = ceil($found_posts/$count);
		$pagination_args = array(
			'total'     => $max_num_pages,
			'current'   => $current,
			'show_all'  => false,
			'prev_text' => '<i class="icon-left-open"></i>',
			'next_text' => '<i class="icon-right-open"></i>',
		);
		if (!get_option('permalink_structure')) {
			$pagination_args['base'] = esc_url_raw(add_query_arg('paged','%#%'));
		}
		$paginate_links = paginate_links($pagination_args);?>
		<div class="main-pagination"><div class='comment-pagination pagination'><?php echo ($paginate_links != ""?$paginate_links:"")?></div></div>
	<?php endif;
	wp_reset_postdata();?>
</div>