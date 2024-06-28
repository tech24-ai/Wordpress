<?php
/* ElitePack Theme Updater */

/* Access the object */
function discy_updater() {
	global $discy_updater;
	if (!isset($discy_updater)) {
		// Include ElitePack SDK.
		include(dirname(__FILE__).'/ep-updater-admin-class.php');

		// Loads the updater classes
		$discy_updater = new ElitePack_Theme_Updater_Admin (
			// Config settings
			$config = array(
				'api_url'      => 'https://2code.info/support/',
				'item_id'      => '19281265',
				'name'         => discy_theme_name.' - Social Questions and Answers WordPress Theme',
				'version'      => discy_theme_version,
				'capability'   => 'manage_options',
				'notice_pages' => false,
				'redirect_url' => add_query_arg(array('page' => 'registration'),admin_url('admin.php')),
				'theme_folder' => 'discy',
			),
			// Strings
			$strings = array(
				'purchase-license'            => esc_html__('Purchase a License','discy'),
				'renew-support'               => esc_html__('Renew Support','discy'),
				'need-help'                   => esc_html__('Need Help?','discy'),
				'try-again'                   => esc_html__('Try Again','discy'),
				'register-item'               => esc_html__('Register %s','discy'),
				'register-message'            => esc_html__('Thank you for choosing %s! Your product must be registered to see the theme options, import our awesome demos, install bundled plugins, receive automatic updates, and access to support.','discy'),
				'register-button'             => esc_html__('Register Now!','discy'),
				'register-success-title'      => esc_html__('Congratulations','discy'),
				'register-success-text'       => esc_html__('Your License is now registered!, theme options, demo import, and bundeled plugins is now unlocked.','discy'),
				'api-error'                   => esc_html__('An error occurred, please try again.','discy'),
				'inline-register-item-notice' => esc_html__('Register the theme to unlock automatic updates.','discy'),
				'inline-renew-support-notice' => esc_html__('Renew your support to unlock automatic updates.','discy'),
				'date-at-time'                => esc_html__('%1$s at %2$s','discy'),
				'support-expiring-notice'     => esc_html__('Your support will Expire in %s. Renew your license today and save 25%% to keep getting auto updates and premium support, remember purchasing a new license extends support for all licenses.','discy'),
				'support-update-failed'       => esc_html__('Failed, try again later.','discy'),
				'support-not-updated'         => esc_html__('Did not updated, Your support expires on %s','discy'),
				'support-updated'             => esc_html__('Updated successfully, your support expires on %s','discy'),
				'update-available'            => esc_html__('There is a new version of %1$s available.','discy'),
				'update-available-changelog'  => esc_html__('There is a new version of %1$s available, %2$sView version %3$s details%4$s.','discy'),
				'update-now'                  => esc_html__('update now','discy'),
				'revoke-license-success'      => esc_html__('License Deactivated Successfully.','discy'),
				'cancel'                      => esc_html__('Cancel','discy'),
				'skip'                        => esc_html__('Skip & Switch','discy'),
				'send'                        => esc_html__('Send & Switch','discy'),
				'feedback'                    => esc_html__('%s feedback','discy'),
				'deactivation-share-reason'   => esc_html__('May we have a little info about why you are switching?','discy'),
			)
		);
	}
	return $discy_updater;
}
discy_updater();
/* Disable the notices in the Registeration page */
add_filter('discy/notice/show','discy_updater_notices',10,2);
function discy_updater_notices($status,$id) {
	if (get_current_screen()->id == 'theme-options_page_options') {
		if ($id == 'discy-activated' || $id == 'discy-activation-notice') {
			return false;
		}
	}
	return $status;
}
/* Add the registeration custom page */
add_action('admin_menu','discy_registeration_menu',14);
function discy_registeration_menu() {
	$support_activate = discy_updater()->is_active();
	if ($support_activate && has_wpqa()) {
		add_submenu_page('options','Registration','Registration','manage_options','registration','discy_registeration_page');
	}else {
		add_menu_page('Register '.discy_theme_name,'Register '.discy_theme_name,'manage_options','registration','discy_registeration_page','dashicons-admin-site');
		add_submenu_page('registration',discy_theme_name.' Settings', discy_theme_name,'manage_options','options','discy_options_page');
		add_submenu_page('registration','Import demo','Import demo','manage_options','demo-import','discy_options_page');
	}
}
/* Check the WPQA plugin */
function discy_options_page() {
	$title = esc_html__('Congratulations! Your product is registered now.','discy');
	$icon  = 'yes';
	$class = 'is-registered';
	$all_plugins = get_plugins();?>
	<div id="framework-registration-wrap" class="framework-demos-container <?php echo esc_attr($class)?>">
		<div class="framework-dash-container framework-dash-container-medium">
			<div class="postbox">
				<h2><span><?php echo esc_html__('Welcome to','discy').' '.discy_theme_name.'!';?></span></h2>
				<div class="inside">
					<div class="main">
						<h3 class="framework-dash-margin-remove"><span class="dashicons dashicons-<?php echo esc_attr($icon);?> library-icon-key"></span> <?php echo stripcslashes($title);?></h3>
						<div class="notice notice-warning settings-error"> 
							<p>
								<strong>
									<span><?php esc_html_e("Required plugin:","discy")?> <em>WPQA - The WordPress Questions And Answers Plugin</em>.</span>
									<span>
										<?php if (array_key_exists('WPQA/wpqa.php',$all_plugins)) {?>
											<a href="<?php echo admin_url('admin.php?page=install-required-plugins&amp;plugin_status=activate')?>"><?php echo esc_html_x("Begin activating plugin","Activate plugin link","discy")?></a>
										<?php }else {?>
											<a href="<?php echo admin_url('admin.php?page=install-required-plugins&amp;plugin_status=install')?>"><?php echo esc_html_x("Begin installing plugin","Install plugin link","discy")?></a>
										<?php }?>
									</span>
								</strong>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div><!-- framework-dash-container -->
	</div><!-- framework-demos-container -->
<?php }
/* The registeration page content */
function discy_registeration_page() {
	$support_activate = discy_updater()->is_active();
	if ($support_activate) {
		$intro = esc_html__('Thank you for choosing '.discy_theme_name.'! Your product is already registered, so you have access to:','discy');
		$title = esc_html__('Congratulations! Your product is registered now.','discy');
		$icon  = 'yes';
		$class = 'is-registered';
	}else {
		$intro = esc_html__('Thank you for choosing '.discy_theme_name.'! Your product must be registered to:','discy');
		$title = esc_html__('Click on the button below to begin the registration process.','discy');
		$icon  = 'no';
		$class = 'not-registered';
	}
	$foreach = array(
		"admin-site"       => esc_html__('See the theme options','discy'),
		"admin-appearance" => esc_html__('Import our awesome demos','discy'),
		"admin-plugins"    => esc_html__('Install the included plugins','discy'),
		"update"           => esc_html__('Receive automatic updates','discy'),
		"businessman"      => esc_html__('Access to support','discy')
	);?>
	<div id="framework-registration-wrap" class="framework-demos-container <?php echo esc_attr($class)?>">
		<div class="framework-dash-container framework-dash-container-medium">
			<div class="postbox">
				<h2><span><?php echo esc_html__('Welcome to','discy').' '.discy_theme_name.'!';?></span></h2>
				<div class="inside">
					<div class="main">
						<h3 class="framework-dash-margin-remove"><span class="dashicons dashicons-<?php echo esc_attr($icon);?> library-icon-key"></span> <?php echo ($title);?></h3>
						<p class="framework-dash-text-lead"><?php echo ($intro);?></p>
						<ul>
							<?php foreach ($foreach as $icon => $item) {
								if ($icon == "admin-site") {
									$link = admin_url('admin.php?page=options');
								}else if ($icon == "admin-appearance") {
									$link = admin_url('admin.php?page=demo-import');
								}else if (!has_wpqa() && $icon == "admin-plugins") {
									$link = admin_url('admin.php?page=install-required-plugins');
								}else if ($icon == "businessman") {
									$link = 'https://2code.info/support/';
								}else {
									$link = "";
								}?>
								<li><i class="dashicons dashicons-<?php echo esc_attr($icon)?>"></i><?php echo ($link != ""?"<a target='_blank' href='".$link."'>":"").($item).($link != ""?"</a>":"")?></li>
							<?php }?>
						</ul>
					</div>
				</div>
				<div class="community-events-footer">
					<?php if (!$support_activate) {?>
						<div class="framework-registration-wrap">
							<a href="<?php echo discy_updater()->activate_link();?>" class="button button-primary"><?php esc_html_e('Register Now!','discy');?></a>
							<a href="<?php echo discy_updater()->purchase_url();?>" class="button" target="_blank"><?php esc_html_e('Purchase a License','discy');?></a>
						</div>
					<?php }else {?>
						<div class="framework-support-status framework-support-status-active">
							<?php esc_html_e('License Status:','discy');?> <span><?php esc_html_e('Active','discy')?></span>
							<a class="button" href="<?php echo discy_updater()->deactivate_license_link()?>"><?php esc_html_e('Revoke License','discy')?></a>
						</div>
						<?php $support_info = discy_updater()->support_period_info();
						if (!empty($support_info['status'])) {
							switch ($support_info['status']) {
								case 'expiring':
									$support_message = sprintf(esc_html__('Expiring! will expire on %s','discy'),$support_info['date']);
									break;
								case 'active':
									$support_message = esc_html__('Active','discy');
									break;
								default:
									$support_message = esc_html__('Expired','discy');
									break;
							}
						}
						if (!empty($support_message)) {?>
							<div class="framework-support-status framework-support-status-<?php echo ($support_info['status'])?>">
								<?php esc_html_e('Support Status:','discy');?> <span><?php echo ($support_message)?></span>
								<a class="button" href="<?php echo discy_updater()->refresh_support_expiration_link()?>"><?php esc_html_e('Refresh Expiration Date','discy')?></a>
							</div>
						<?php }
					}?>
				</div>
			</div>
		</div><!-- framework-dash-container -->
	</div><!-- framework-demos-container -->
	<?php
}?>