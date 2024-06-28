<?php

/* @author    2codeThemes
*  @package   WPQA/templates/profile
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$rows_per_page = get_option("posts_per_page");
$user_id = get_current_user_id();?>
<div id='section-<?php echo wpqa_user_title()?>' class="section-referral">
	<div class="referral-cover all-things-boxed">
		<?php if (has_himer() || has_knowly()) {?>
			<div class="post-title-2 d-flex align-items-center justify-content-between">
				<h2 class="card-title mb-0 d-flex align-items-center">
					<i class="icon-fireball font-xl card-title__icon"></i>
					<span><?php esc_html_e("Referrals","wpqa")?></span>
				</h2>
			</div>
		<?php }?>
		<div class="referral-cover-background">
			<div class="cover-opacity"></div>
			<div class="referral-cover-inner">
				<?php $user_referral = get_user_meta($wpqa_user_id,"wpqa_referral",true);
				if ($user_referral == "") {
					$user_referral = wpqa_token(15);
					update_user_meta($wpqa_user_id,"wpqa_referral",$user_referral);
				}
				$invitation_link = add_query_arg(array("invite" => $user_referral),esc_url(home_url('/')));
				$active_points = wpqa_options("active_points");
				$referrals_headline = wpqa_options("referrals_headline");
				$referrals_paragraph = wpqa_options("referrals_paragraph");
				$referrals_share_on = wpqa_options("referrals_share_on");
				$referrals_share = wpqa_options("referrals_share");
				$share_facebook = (isset($referrals_share["share_facebook"]["value"])?$referrals_share["share_facebook"]["value"]:"");
        	    $share_twitter  = (isset($referrals_share["share_twitter"]["value"])?$referrals_share["share_twitter"]["value"]:"");
        	    $share_linkedin = (isset($referrals_share["share_linkedin"]["value"])?$referrals_share["share_linkedin"]["value"]:"");
        	    $share_whatsapp = (isset($referrals_share["share_whatsapp"]["value"])?$referrals_share["share_whatsapp"]["value"]:"");
				if ($referrals_headline != "") {?>
					<h3><?php echo esc_html($referrals_headline)?></h3>
				<?php }
				if ($referrals_paragraph != "") {?>
					<p><?php echo do_shortcode(stripslashes($referrals_paragraph))?></p>
				<?php }?>
				<p><?php esc_html_e("Send a referral by email or share it with your friends.","wpqa")?></p>
				<div class="referral-form">
					<div class="wpqa_error"></div>
					<div class="wpqa_success"></div>
					<form action="" method="post">
						<i class="icon-mail"></i>
				        <input class="form-control" name="email" type="email" value="<?php esc_attr_e("Send an email","wpqa");?>" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;">
				        <button name="submit" type="submit" class="button-default btn btn__info btn__sm submit__btn"><?php if (has_himer() || has_knowly()) {esc_html_e("Send","wpqa");}else {?><i class="icon-right-open"></i><?php }?></button>
				        <?php wp_nonce_field('invitation_nonce','invitation_nonce',false)?>
				    </form>
				</div>
				<div class="referral-invitation">
					<p><?php esc_html_e("Copy and paste your referral link anywhere.","wpqa")?></p>
					<div>
						<input class="form-control" type="text" value="<?php echo esc_url($invitation_link)?>"><a title="<?php esc_html_e("Copy","wpqa")?>" href="<?php echo esc_url($invitation_link)?>"><i class="icon-clipboard"></i></a>
					</div>
				</div>
				<?php if ($referrals_share_on == "on") {?>
					<div class="referral-share">
						<p><?php esc_html_e("Share the referral link on social media.","wpqa")?></p>
						<?php wpqa_share($referrals_share,$share_facebook,$share_twitter,$share_linkedin,$share_whatsapp,"style_1","","",$invitation_link,esc_html__("Share","wpqa"))?>
					</div>
				<?php }?>
			</div>
		</div>
	</div>
	<div class="referral-stats referrals-card all-things-boxed">
		<ul class="row row-warp row-boot stats-card">
			<?php $columns = ($active_points == "on"?(has_himer() || has_knowly()?"col-boot-sm-6":"col3"):"col4 col-boot-sm-4");
			$invitations_sent = get_user_meta($wpqa_user_id,"invitations_sent",true);
			$invitations_pending = get_user_meta($wpqa_user_id,"invitations_pending",true);
			$invitations_completed = get_user_meta($wpqa_user_id,"invitations_completed",true);
			$points_referral = get_user_meta($wpqa_user_id,"points_referral",true);?>
			<li class="col <?php echo esc_attr($columns)?> referral-sent stats-card__item">
				<div>
					<i class="icon-forward"></i>
					<div>
						<span><?php echo (int)$invitations_sent?></span>
						<h4><?php esc_html_e("Invitations Sent","wpqa")?></h4>
					</div>
				</div>
			</li>
			<li class="col <?php echo esc_attr($columns)?> referral-pending stats-card__item">
				<div>
					<i class="icon-clock"></i>
					<div>
						<span><?php echo (int)$invitations_pending?></span>
						<h4><?php esc_html_e("Pending","wpqa")?></h4>
					</div>
				</div>
			</li>
			<li class="col <?php echo esc_attr($columns)?> referral-completed stats-card__item">
				<div>
					<i class="icon-check"></i>
					<div>
						<span><?php echo (int)$invitations_completed?></span>
						<h4><?php esc_html_e("Completed","wpqa")?></h4>
					</div>
				</div>
			</li>
			<?php if ($active_points == "on") {?>
				<li class="col <?php echo (has_himer() || has_knowly()?"col-boot-sm-6":"col3")?> referral-earned stats-card__item">
					<div>
						<i class="icon-bucket"></i>
						<div>
							<span><?php echo (int)$points_referral?></span>
							<h4><?php esc_html_e("Earned","wpqa")?></h4>
						</div>
					</div>
				</li>
			<?php }?>
		</ul>
	</div>
	<?php $points_referrals_meta = get_user_meta($wpqa_user_id,"points_referrals",true);
	if (is_array($points_referrals_meta) && !empty($points_referrals_meta)) {
		$points_referrals_count = count($points_referrals_meta);
		$points_referrals_count = (int)($points_referrals_count > 0?$points_referrals_count:0);
		$current = max(1,$paged);
		$rows_per_page;
		$pagination_args = array(
			'total'     => ceil($points_referrals_count/$rows_per_page),
			'current'   => $current,
			'show_all'  => false,
			'prev_text' => '<i class="icon-left-open"></i>',
			'next_text' => '<i class="icon-right-open"></i>',
		);
		if (!get_option('permalink_structure')) {
			$pagination_args['base'] = esc_url_raw(add_query_arg('paged','%#%'));
		}
		
		$start = ($current - 1) * $rows_per_page;
		$end = $start + $rows_per_page;
		$end = ($points_referrals_count < $end) ? $points_referrals_count : $end;?>
		<div class="referral-invitations user-notifications user-profile-area section-page-div all-things-boxed custom__notifications">
			<div>
				<ul>
					<?php $k = 0;
					$points_referrals_count = ($points_referrals_count > 0?$points_referrals_count-1:$points_referrals_count);
					for ($i = $points_referrals_count-$start; $i > $points_referrals_count-$end; $i--) {
						if ($k == 0) {?>
							<li class="notifications__item referral__head referral__item d-flex <?php echo (++$k%2?"referral-odd":"referral-even")?>">
								<div class="wpqa_success"></div>
								<div>
									<div class="row row-boot row-warp">
										<span class="col <?php echo ($active_points == "on"?"col5 col-boot-sm-5":"col6 col-boot-sm-6")?> referral-email"><?php esc_html_e("Email","wpqa")?></span>
										<span class="col col3 col-boot-sm-3 referral-status"><?php esc_html_e("Status","wpqa")?></span>
										<span class="col <?php echo ($active_points == "on"?"col2 col-boot-sm-2":"col3 col-boot-sm-3")?> referral-action"><?php esc_html_e("Action","wpqa")?></span>
										<?php if ($active_points == "on") {?>
											<span class="col col2 col-boot-sm-2 referral-points"><?php esc_html_e("Points","wpqa")?></span>
										<?php }?>
									</div>
								</div>
							</li>
						<?php }
						$points_referral = get_user_meta($wpqa_user_id,$points_referrals_meta[$i],true);?>
						<li class="notifications__item referral__item d-flex <?php echo (++$k%2?"referral-odd":"referral-even")?>">
							<div class="wpqa_success"></div>
							<div>
								<div class="row row-boot row-warp">
									<span class="col <?php echo ($active_points == "on"?"col5 col-boot-sm-5":"col6 col-boot-sm-6")?> referral-email"><?php echo esc_html($points_referral["email"])?></span>
									<span class="col col3 col-boot-sm-3 referral-status">
										<?php if ($points_referral["status"] == "sent") {
											esc_html_e("Sent","wpqa");
										}else if ($points_referral["status"] == "pending") {
											esc_html_e("Pending","wpqa");
										}else if ($points_referral["status"] == "completed") {
											esc_html_e("Completed","wpqa");
										}else {
											echo esc_html($points_referral["status"]);
										}?>
									</span>
									<span class="col <?php echo ($active_points == "on"?"col2 col-boot-sm-2":"col3 col-boot-sm-3")?> referral-action">
										<?php if (isset($points_referral["status"]) && ($points_referral["status"] == "sent" || $points_referral["status"] == "pending")) {?>
											<a class="resend-invitation btn btn__sm btn__info" data-nonce="<?php echo wp_create_nonce("invitation_resend_nonce")?>" data-invite="<?php echo esc_attr($points_referrals_meta[$i])?>" data-email="<?php echo esc_attr($points_referral["email"])?>" href="#"><?php esc_html_e("Resend","wpqa")?></a>
										<?php }else {
											echo "-";
										}?>
									</span>
									<?php if ($active_points == "on") {?>
										<span class="col col2 col-boot-sm-2 referral-points"><?php echo (int)$points_referral["points"]?></span>
									<?php }?>
								</div>
							</div>
						</li>
					<?php }?>
				</ul>
			</div>
		</div>
		<?php if (isset($points_referrals_count) && $points_referrals_count > 0 && $pagination_args["total"] > 1) {?>
			<div class="main-pagination"><div class='pagination'><?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?></div></div>
		<?php }
	}
	
	$activate_faqs_referrals = wpqa_options("activate_faqs_referrals");
	if ($activate_faqs_referrals == "on") {
		$faqs_referrals_text = wpqa_options("faqs_referrals_text");
		$custom_faqs = wpqa_options("faqs_referrals");?>
		<div class="page-sections">
			<div class="page-section page-section-faqs">
				<div class="page-wrap-content">
					<h2 class="post-title-3"><i class="icon-help-circled"></i><?php esc_html_e("Frequently Asked Questions","wpqa")?></h2>
					<?php if ($faqs_referrals_text != "") {?>
						<div class="post-content-text">
							<?php echo do_shortcode($faqs_referrals_text);?>
						</div>
					<?php }
					include wpqa_get_template("faqs.php","theme-parts/");?>
				</div><!-- End page-wrap-content -->
			</div><!-- End page-section -->
		</div>
	<?php }?>
</div>