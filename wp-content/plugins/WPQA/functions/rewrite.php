<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Remove index.php from permalink */
add_action("wpqa_init","wpqa_remove_index_permalink");
function wpqa_remove_index_permalink() {
	$permalink_structure = get_option('permalink_structure');
	$update_it = (strpos($permalink_structure,'/index.php') === 0?true:false);
	$update_it_2 = (strripos($permalink_structure,'/') === 0?true:false);
	$permalink_structure = ($update_it == true?substr($permalink_structure,strlen('/index.php')):$permalink_structure);
	$permalink_structure = ($update_it_2 == true?$permalink_structure."/":$permalink_structure);
	if ($update_it == true || $update_it_2 == true) {
		update_option("permalink_structure",$permalink_structure);
		update_option("FlushRewriteRules",true);
	}
	$page_for_posts = get_option("page_for_posts");
	if ($page_for_posts > 0) {
		update_option("page_for_posts",0);
	}
}
/* Redirect pages */
add_action('parse_query','wpqa_redirect_pages',10);
if (!function_exists('wpqa_redirect_pages')) :
	function wpqa_redirect_pages() {
		if (is_author()) {
			$user_login = get_queried_object();
			if (isset($user_login) && is_object($user_login) && isset($user_login->ID)) {
				$user_login = get_userdata(esc_html($user_login->ID));
			}
			$author_name = esc_html(get_query_var('author_name'));
			if (isset($user_login) && !is_object($user_login)) {
				$user_login = get_user_by('login',urldecode($author_name));
			}
			if (isset($user_login) && !is_object($user_login)) {
				$user_login = get_user_by('slug',urldecode($author_name));
			}
			if (isset($user_login) && !is_object($user_login)) {
				$author_name = str_ireplace('-',' ',$author_name);
				$user_login = get_user_by('slug',urldecode($author_name));
			}
			$get_user_id = (isset($user_login->ID)?esc_html($user_login->ID):'');
			if ($get_user_id != '' && !is_admin()) {
				wp_redirect(wpqa_profile_url($get_user_id));
				exit;
			}
		}else if (wpqa_is_user_settings_page()) {
			wp_redirect(wpqa_edit_profile_permalink());
			exit;
		}else if (wpqa_is_user_profile() && is_user_logged_in()) {
			if (get_option('permalink_structure')) {
				$wpqa_user = esc_html(get_query_var(apply_filters('wpqa_user','wpqa_user')));
				$user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
				$user = get_userdata($user_id);
				$nicename = (isset($user->user_nicename) && $user->user_nicename != ''?$user->user_nicename:(isset($user->user_login) && $user->user_login != ''?$user->user_login:''));
				$login = $user->user_login;
				$profile_slug_numbers = wpqa_options('profile_slug_numbers');
				if ($profile_slug_numbers != 'on') {
					$profile_type = wpqa_options('profile_type');
					if ($profile_type == 'login') {
						$user_name = trim(urldecode(esc_html($login)));
					}else {
						$user_name = trim(urldecode(esc_html($nicename)));
					}
					$user_name_2 = str_ireplace('-',' ',$user_name);
				}
				if (($profile_slug_numbers != 'on' && $user_name != $wpqa_user && $user_name_2 != $wpqa_user) || (isset($_GET['wpqa_user_id']) && $_GET['wpqa_user_id'] > 0 && $_GET['wpqa_user_id'] == $user_id)) {
					$wpqa_user_title = wpqa_user_title();
					if ($wpqa_user_title) {
						$type_slug = explode('-',$wpqa_user_title);
						$tab_item = $type_slug[0].(isset($type_slug[1])?'_'.$type_slug[1]:'');
						$last_url = wpqa_profile_links($user_id,$tab_item);
						wp_redirect($last_url);
					}else {
						wp_redirect(wpqa_profile_url($user_id));
					}
					exit;
				}
			}
		}else if (wpqa_is_add_questions()) {
			$ask_question_to_users = wpqa_options('ask_question_to_users');
			$get_user_id = wpqa_add_question_user();
			$get_user_name = get_query_var(apply_filters('wpqa_add_questions','add_question'));
			if ($ask_question_to_users != 'on' && $get_user_name != '' && $get_user_id == '') {
				wp_redirect(wpqa_add_question_permalink());
				exit;
			}
		}else if (wpqa_is_signup() && !is_user_logged_in()) {
			$activate_register = wpqa_options('activate_register');
			if ($activate_register == 'disabled') {
				wp_redirect(esc_url(home_url('/')));
				exit;
			}
		}else if (wpqa_is_login() && !is_user_logged_in()) {
			$activate_login = wpqa_options('activate_login');
			if ($activate_login == 'disabled') {
				wp_redirect(esc_url(home_url('/')));
				exit;
			}
		}else if (!is_admin() && (wpqa_is_search() || (is_search() && !isset($_GET['search']) && !wpqa_is_search()))) {
			if ((is_search() && !wpqa_is_search() && isset($_GET['s']) && !isset($_GET['search'])) || (get_option('permalink_structure') && wpqa_is_search() && isset($_GET['search']) && !isset($_GET['s']))) {
				wp_redirect(wpqa_search_link());
				exit;
			}
		}
	}
endif;
/* Rewrite tags */
add_action('wpqa_init','wpqa_rewrite_tags',2);
if (!function_exists('wpqa_rewrite_tags')) :
	function wpqa_rewrite_tags() {
		$main_pages = array(
			'wpqa_search_id'         => 'search',
			'wpqa_type'              => 'search_type',
			'wpqa_checkout_id'       => 'checkout',
			'wpqa_checkout_item'     => 'checkout_item',
			'wpqa_checkout_related'  => 'checkout_related',
			'wpqa_subscriptions'     => 'wpqa_subscriptions',
			'wpqa_buy_points'        => 'wpqa_buy_points',
			'wpqa_login'             => 'wpqa_login',
			'wpqa_signup'            => 'wpqa_signup',
			'wpqa_lost_password'     => 'wpqa_lost_password',
			'wpqa_settings_id'       => 'settings',
			'wpqa_edit_id'           => 'edit',
			'wpqa_password_id'       => 'password',
			'wpqa_privacy_id'        => 'privacy',
			'wpqa_financial_id'      => 'financial',
			'wpqa_withdrawals_id'    => 'withdrawals',
			'wpqa_transactions_id'   => 'transactions',
			'wpqa_mails_id'          => 'mails',
			'wpqa_delete_id'         => 'delete',
			'wpqa_blocking'          => 'blocking',
			'wpqa_pending_questions' => 'pending_questions',
			'wpqa_pending_posts'     => 'pending_posts',
			'wpqa_notifications'     => 'notifications',
			'wpqa_activities'        => 'activities',
			'wpqa_referrals'         => 'referrals',
			'wpqa_messages'          => 'messages',
			'wpqa_add_categories'    => 'add_category',
			'wpqa_add_questions'     => 'add_question',
			'wpqa_edit_questions'    => 'edit_question',
			'wpqa_add_groups'        => 'add_group',
			'wpqa_edit_groups'       => 'edit_group',
			'wpqa_group_requests'    => 'group_request',
			'wpqa_group_users'       => 'group_user',
			'wpqa_group_admins'      => 'group_admin',
			'wpqa_blocked_users'     => 'blocked_user',
			'wpqa_posts_group'       => 'post_group',
			'wpqa_view_posts_group'  => 'view_post_group',
			'wpqa_edit_posts_group'  => 'edit_post_group',
			'wpqa_add_posts'         => 'wpqa_add_post',
			'wpqa_edit_posts'        => 'edit_post',
			'wpqa_edit_comments'     => 'edit_comment',
			'wpqa_user_id'           => 'wpqa_user_id'
		);
		foreach ($main_pages as $key => $value) {
			add_rewrite_tag('%'.apply_filters($key,$value).'%','([^/]+)');
		}

		$user_pages = array(
			'wpqa_followers'           => 'followers',
			'wpqa_following'           => 'following',
			'wpqa_questions'           => 'questions',
			'wpqa_answers'             => 'answers',
			'wpqa_best_answers'        => 'best_answers',
			'wpqa_groups'              => 'groups',
			'wpqa_joined_groups'       => 'joined_groups',
			'wpqa_managed_groups'      => 'managed_groups',
			'wpqa_points'              => 'points',
			'wpqa_polls'               => 'polls',
			'wpqa_asked'               => 'asked',
			'wpqa_asked_questions'     => 'asked_questions',
			'wpqa_my_asked'            => 'my_asked',
			'wpqa_paid_questions'      => 'paid_questions',
			'wpqa_followed'            => 'followed',
			'wpqa_favorites'           => 'favorites',
			'wpqa_posts'               => 'posts',
			'wpqa_comments'            => 'comments',
			'wpqa_followers_questions' => 'followers_questions',
			'wpqa_followers_answers'   => 'followers_answers',
			'wpqa_followers_posts'     => 'followers_posts',
			'wpqa_followers_comments'  => 'followers_comments'
		);
		foreach ($user_pages as $key => $value) {
			add_rewrite_tag('%'.apply_filters($key,$value).'%','([1]{1,})');
		}
	}
endif;
/* After the update */
add_action('upgrader_process_complete','wpqa_upgrader_process_complete');
function wpqa_upgrader_process_complete() {
	update_option('FlushRewriteRules',true);
}
/* Rewrite tags */
add_action('wpqa_init','wpqa_rewrite_rules',2);
if (!function_exists('wpqa_rewrite_rules')) :
	function wpqa_rewrite_rules() {
		if ((bool)get_option('FlushRewriteRules')) {
			flush_rewrite_rules(true);
			delete_option('FlushRewriteRules');
		}
		$priority                 = 'top';
		$page_slug                = 'page';
		
		$search_slug              = wpqa_options('search_slug','search');
		$checkout_slug            = (class_exists('woocommerce')?'wpqa-':'').wpqa_options('checkout_slug','checkout');
		$subscriptions_slug       = wpqa_options('subscriptions_slug','subscriptions');
		$buy_points_slug          = wpqa_options('buy_points_slug','buy-points');
		$add_category_slug        = wpqa_options('add_category_slug','add-category');
		$add_questions_slug       = wpqa_options('add_questions_slug','add-question');
		$edit_questions_slug      = wpqa_options('edit_questions_slug','edit-question');
		$add_groups_slug          = wpqa_options('add_groups_slug','add-group');
		$edit_groups_slug         = wpqa_options('edit_groups_slug','edit-group');
		$group_requests_slug      = wpqa_options('group_requests_slug','user-requests');
		$group_users_slug         = wpqa_options('group_users_slug','group-users');
		$group_admins_slug        = wpqa_options('group_admins_slug','group-admins');
		$blocked_users_slug       = wpqa_options('blocked_users_slug','blocked-users');
		$group_posts_slug         = wpqa_options('group_posts_slug','group-pending-posts');
		$view_group_posts_slug    = wpqa_options('view_group_posts_slug','view-post-group');
		$edit_group_posts_slug    = wpqa_options('edit_group_posts_slug','edit-post-group');
		$add_posts_slug           = wpqa_options('add_posts_slug','add-post');
		$edit_posts_slug          = wpqa_options('edit_posts_slug','edit-post');
		$edit_comments_slug       = wpqa_options('edit_comments_slug','edit-comment');

		$login_slug               = wpqa_options('login_slug','log-in');
		$signup_slug              = wpqa_options('signup_slug','sign-up');
		$lost_password_slug       = wpqa_options('lost_password_slug','lost-password');
		$settings_slug            = wpqa_options('settings_slug','settings');
		$edit_slug                = wpqa_options('edit_slug','edit');
		$password_slug            = wpqa_options('password_slug','change-password');
		$privacy_slug             = wpqa_options('privacy_slug','privacy');
		$financial_slug           = wpqa_options('financial_slug','financial');
		$withdrawals_slug         = wpqa_options('withdrawals_slug','withdrawals');
		$transactions_slug        = wpqa_options('transactions_slug','transactions');
		$mails_slug               = wpqa_options('mails_slug','mails');
		$delete_slug              = wpqa_options('delete_slug','delete');
		$blocking_slug            = wpqa_options('blocking_slug','blocking');
		$pending_questions_slug   = wpqa_options('pending_questions_slug','pending-questions');
		$pending_posts_slug       = wpqa_options('pending_posts_slug','pending-posts');
		$notifications_slug       = wpqa_options('notifications_slug','notifications');
		$activities_slug          = wpqa_options('activities_slug','activities');
		$referrals_slug           = wpqa_options('referrals_slug','referrals');
		$messages_slug            = wpqa_options('messages_slug','messages');
		$user_slug                = wpqa_options('profile_slug','profile');
		
		$user_followers_slug      = wpqa_options('followers_slug','followers');
		$user_following_slug      = wpqa_options('following_slug','following');
		$user_questions_slug      = wpqa_options('questions_slug','questions');
		$user_answers_slug        = wpqa_options('answers_slug','answers');
		$best_answers_slug        = wpqa_options('best_answers_slug','best-answers');
		$user_groups_slug         = wpqa_options('groups_slug','groups');
		$user_joined_groups_slug  = wpqa_options('joined_groups_slug','joined-groups');
		$user_managed_groups_slug = wpqa_options('managed_groups_slug','managed-groups');
		$user_points_slug         = wpqa_options('points_slug','points');
		$user_polls_slug          = wpqa_options('polls_slug','polls');
		$user_asked_slug          = wpqa_options('asked_slug','asked');
		$asked_questions_slug     = wpqa_options('asked_questions_slug','asked-questions');
		$my_asked_slug            = wpqa_options('my_asked_slug','my-asked');
		$paid_questions_slug      = wpqa_options('paid_questions_slug','paid-questions');
		$user_followed_slug       = wpqa_options('followed_slug','followed');
		$user_favorites_slug      = wpqa_options('favorites_slug','favorites');
		$user_posts_slug          = wpqa_options('posts_slug','posts');
		$user_comments_slug       = wpqa_options('comments_slug','comments');
		$followers_questions_slug = wpqa_options('followers_questions_slug','followers-questions');
		$followers_answers_slug   = wpqa_options('followers_answers_slug','followers-answers');
		$followers_posts_slug     = wpqa_options('followers_posts_slug','followers-posts');
		$followers_comments_slug  = wpqa_options('followers_comments_slug','followers-comments');
		
		$search_id                = apply_filters('wpqa_search_id','search');
		$search_type              = apply_filters('wpqa_type','search_type');
		$checkout_id              = apply_filters('wpqa_checkout_id','checkout');
		$checkout_item            = apply_filters('wpqa_checkout_item','checkout_item');
		$checkout_related         = apply_filters('wpqa_checkout_related','checkout_related');
		$subscriptions_id         = apply_filters('wpqa_subscriptions','wpqa_subscriptions');
		$buy_points_id            = apply_filters('wpqa_buy_points','wpqa_buy_points');
		$login_id                 = apply_filters('wpqa_login','wpqa_login');
		$signup_id                = apply_filters('wpqa_signup','wpqa_signup');
		$lost_password_id         = apply_filters('wpqa_lost_password','wpqa_lost_password');
		$settings_id              = apply_filters('wpqa_settings_id','settings');
		$edit_id                  = apply_filters('wpqa_edit_id','edit');
		$password_id              = apply_filters('wpqa_password_id','password');
		$privacy_id               = apply_filters('wpqa_privacy_id','privacy');
		$financial_id             = apply_filters('wpqa_financial_id','financial');
		$withdrawals_id           = apply_filters('wpqa_withdrawals_id','withdrawals');
		$transactions_id          = apply_filters('wpqa_transactions_id','transactions');
		$mails_id                 = apply_filters('wpqa_mails_id','mails');
		$delete_id                = apply_filters('wpqa_delete_id','delete');
		$blocking_id              = apply_filters('wpqa_blocking','blocking');
		$pending_questions_id     = apply_filters('wpqa_pending_questions','pending_questions');
		$pending_posts_id         = apply_filters('wpqa_pending_posts','pending_posts');
		$notifications_id         = apply_filters('wpqa_notifications','notifications');
		$activities_id            = apply_filters('wpqa_activities','activities');
		$referrals_id             = apply_filters('wpqa_referrals','referrals');
		$messages_id              = apply_filters('wpqa_messages','messages');
		$add_category_id          = apply_filters('wpqa_add_categories','add_category');
		$add_questions_id         = apply_filters('wpqa_add_questions','add_question');
		$edit_questions_id        = apply_filters('wpqa_edit_questions','edit_question');
		$add_groups_id            = apply_filters('wpqa_add_groups','add_group');
		$edit_groups_id           = apply_filters('wpqa_edit_groups','edit_group');
		$group_requests_id        = apply_filters('wpqa_group_requests','group_request');
		$group_users_id           = apply_filters('wpqa_group_users','group_user');
		$group_admins_id          = apply_filters('wpqa_group_admins','group_admin');
		$blocked_users_id         = apply_filters('wpqa_blocked_users','blocked_user');
		$posts_group_id           = apply_filters('wpqa_posts_group','post_group');
		$view_posts_group_id      = apply_filters('wpqa_view_posts_group','view_post_group');
		$edit_posts_group_id      = apply_filters('wpqa_edit_posts_group','edit_post_group');
		$add_posts_id             = apply_filters('wpqa_add_posts','wpqa_add_post');
		$edit_posts_id            = apply_filters('wpqa_edit_posts','edit_post');
		$edit_comments_id         = apply_filters('wpqa_edit_comments','edit_comment');
		$page_id                  = apply_filters('wpqa_page_id','page');
		$user_id                  = apply_filters('wpqa_user_id','wpqa_user_id');
		$user_followers_id        = apply_filters('wpqa_followers','followers');
		$user_following_id        = apply_filters('wpqa_following','following');
		$user_questions_id        = apply_filters('wpqa_questions','questions');
		$user_answers_id          = apply_filters('wpqa_answers','answers');
		$best_answers_id          = apply_filters('wpqa_best_answers','best_answers');
		$user_groups_id           = apply_filters('wpqa_groups','groups');
		$user_joined_groups_id    = apply_filters('wpqa_joined_groups','joined_groups');
		$user_managed_groups_id   = apply_filters('wpqa_managed_groups','managed_groups');
		$user_points_id           = apply_filters('wpqa_points','points');
		$user_polls_id            = apply_filters('wpqa_polls','polls');
		$user_wpqa_asked          = apply_filters('wpqa_asked','asked');
		$asked_questions_id       = apply_filters('wpqa_asked_questions','asked_questions');
		$my_asked_id              = apply_filters('wpqa_my_asked','my_asked');
		$paid_questions_id        = apply_filters('wpqa_paid_questions','paid_questions');
		$user_followed_id         = apply_filters('wpqa_followed','followed');
		$user_favorites_id        = apply_filters('wpqa_favorites','favorites');
		$user_posts_id            = apply_filters('wpqa_posts','posts');
		$user_comments_id         = apply_filters('wpqa_comments','comments');
		$followers_questions_id   = apply_filters('wpqa_followers_questions','followers_questions');
		$followers_answers_id     = apply_filters('wpqa_followers_answers','followers_answers');
		$followers_posts_id       = apply_filters('wpqa_followers_posts','followers_posts');
		$followers_comments_id    = apply_filters('wpqa_followers_comments','followers_comments');
		
		$main_rule = $user_rule   = '/([^/]+)/';
		$root_rule                = $main_rule.'?$';
		$one_parameter            = $main_rule.'([^/]+)/?$';
		$two_parameter            = $main_rule.'([^/]+)/([^/]+)/?$';
		$three_parameter          = $main_rule.'([^/]+)/([^/]+)/([^/]+)/?$';
		$four_parameter           = $main_rule.'([^/]+)/([^/]+)/([^/]+)/([^/]+)/?$';
		$settings_slug_rule       = ($settings_slug != ''?$settings_slug.'/':'');
		
		$settings_root_rule = $edit_root_rule = $password_root_rule = $privacy_root_rule = $financial_root_rule = $withdrawals_root_rule = $transactions_root_rule = $mails_root_rule = $delete_root_rule = $blocking_root_rule = $pending_questions_root_rule = $pending_posts_root_rule = $notifications_root_rule = $activities_root_rule = $referrals_root_rule = $messages_root_rule = $search_root_rule = $checkout_root_rule = $subscriptions_root_rule = $buy_points_root_rule = $login_root_rule = $signup_root_rule = $lost_password_root_rule = $add_category_root_rule = $add_questions_root_rule = $edit_questions_root_rule = $add_groups_root_rule = $edit_groups_root_rule = $group_requests_root_rule = $group_users_root_rule = $group_admins_root_rule = $blocked_users_root_rule = $posts_group_root_rule = $view_posts_group_root_rule = $edit_posts_group_root_rule = $add_posts_root_rule = $edit_posts_root_rule = $edit_comments_root_rule  = '/?$';
		
		// Search
		add_rewrite_rule( $search_slug . $three_parameter,                                  'index.php?' . $search_id . '=$matches[1]&' . $search_type .  '=$matches[2]&' . $page_id . '=$matches[4]', $priority );
		add_rewrite_rule( $search_slug . $one_parameter,                                    'index.php?' . $search_id . '=$matches[1]&' . $search_type .  '=$matches[2]',                              $priority );
		add_rewrite_rule( $search_slug . $search_root_rule,                                 'index.php?' . $search_id . '=$matches[1]',                                                                $priority );

		// Checkout
		add_rewrite_rule( $checkout_slug . $two_parameter,                                 'index.php?' . $checkout_id . '=$matches[1]&' . $checkout_item . '=$matches[2]&' . $checkout_related . '=$matches[3]', $priority );
		add_rewrite_rule( $checkout_slug . $one_parameter,                                  'index.php?' . $checkout_id . '=$matches[1]&' . $checkout_item . '=$matches[2]',                                       $priority );
		add_rewrite_rule( $checkout_slug . $checkout_root_rule,                             'index.php?' . $checkout_id . '=$matches[1]',                                                                          $priority );

		// Withdrawals
		add_rewrite_rule( $settings_slug_rule . $withdrawals_slug . $one_parameter,         'index.php?' . $withdrawals_id . '=1&' . $page_id . '=$matches[2]', $priority );
		add_rewrite_rule( $settings_slug_rule . $withdrawals_slug . $withdrawals_root_rule, 'index.php?' . $withdrawals_id . '=1',                              $priority );

		// Transactions
		add_rewrite_rule( $transactions_slug . $one_parameter,                              'index.php?' . $transactions_id . '=$matches[1]&' . $page_id . '=$matches[2]', $priority );
		add_rewrite_rule( $transactions_slug . $transactions_root_rule,                     'index.php?' . $transactions_id . '=$matches[1]',                              $priority );

		// Settings
		add_rewrite_rule( $settings_slug. $settings_root_rule,                              'index.php?' . $settings_id, $priority );

		// Edit
		add_rewrite_rule( $settings_slug_rule . $edit_slug. $edit_root_rule,                'index.php?' . $edit_id, $priority );

		// Password
		add_rewrite_rule( $settings_slug_rule . $password_slug . $password_root_rule,       'index.php?' . $password_id, $priority );

		// Privacy
		add_rewrite_rule( $settings_slug_rule . $privacy_slug . $privacy_root_rule,         'index.php?' . $privacy_id, $priority );

		// Financial
		add_rewrite_rule( $settings_slug_rule . $financial_slug . $financial_root_rule,     'index.php?' . $financial_id, $priority );

		// Mails
		add_rewrite_rule( $settings_slug_rule . $mails_slug . $mails_root_rule,             'index.php?' . $mails_id, $priority );

		// Delete
		add_rewrite_rule( $settings_slug_rule . $delete_slug . $delete_root_rule,           'index.php?' . $delete_id, $priority );

		// Blocking
		add_rewrite_rule( $blocking_slug . $one_parameter,                                  'index.php?' . $blocking_id . '=$matches[1]&' . $page_id . '=$matches[2]', $priority );
		add_rewrite_rule( $blocking_slug . $blocking_root_rule,                             'index.php?' . $blocking_id . '=$matches[1]',                              $priority );

		// Pending questions
		add_rewrite_rule( $pending_questions_slug . $one_parameter,                         'index.php?' . $pending_questions_id . '=$matches[1]&' . $page_id . '=$matches[2]', $priority );
		add_rewrite_rule( $pending_questions_slug . $pending_questions_root_rule,           'index.php?' . $pending_questions_id . '=$matches[1]',                              $priority );

		// Pending posts
		add_rewrite_rule( $pending_posts_slug . $one_parameter,                             'index.php?' . $pending_posts_id . '=$matches[1]&' . $page_id . '=$matches[2]', $priority );
		add_rewrite_rule( $pending_posts_slug . $pending_posts_root_rule,                   'index.php?' . $pending_posts_id . '=$matches[1]',                              $priority );

		// Notifications
		add_rewrite_rule( $notifications_slug . $one_parameter,                             'index.php?' . $notifications_id . '=$matches[1]&' . $page_id . '=$matches[2]', $priority );
		add_rewrite_rule( $notifications_slug . $notifications_root_rule,                   'index.php?' . $notifications_id . '=$matches[1]',                              $priority );

		// Activities
		add_rewrite_rule( $activities_slug . $one_parameter,                                'index.php?' . $activities_id . '=$matches[1]&' . $page_id . '=$matches[2]', $priority );
		add_rewrite_rule( $activities_slug . $activities_root_rule,                         'index.php?' . $activities_id . '=$matches[1]',                              $priority );

		// Referrals
		add_rewrite_rule( $referrals_slug . $one_parameter,                                 'index.php?' . $referrals_id . '=$matches[1]&' . $page_id . '=$matches[2]', $priority );
		add_rewrite_rule( $referrals_slug . $referrals_root_rule,                           'index.php?' . $referrals_id . '=$matches[1]',                              $priority );

		// Messages
		add_rewrite_rule( $messages_slug . $one_parameter,                                  'index.php?' . $messages_id . '=$matches[1]&' . $page_id . '=$matches[2]', $priority );
		add_rewrite_rule( $messages_slug . $messages_root_rule,                             'index.php?' . $messages_id . '=$matches[1]',                              $priority );
		
		// Subscriptions
		add_rewrite_rule( $subscriptions_slug . $subscriptions_root_rule,                   'index.php?' . $subscriptions_id, $priority );

		// Buy points
		add_rewrite_rule( $buy_points_slug . $buy_points_root_rule,                         'index.php?' . $buy_points_id, $priority );
		
		// Login
		add_rewrite_rule( $login_slug . $login_root_rule,                                   'index.php?' . $login_id, $priority );
		
		// Signup
		add_rewrite_rule( $signup_slug . $signup_root_rule,                                 'index.php?' . $signup_id, $priority );
		
		// Lost password
		add_rewrite_rule( $lost_password_slug . $lost_password_root_rule,                   'index.php?' . $lost_password_id, $priority );
		
		// Add category
		add_rewrite_rule( $add_category_slug . $add_category_root_rule,                     'index.php?' . $add_category_id, $priority );
		
		// Ask question
		add_rewrite_rule( $add_questions_slug . $add_questions_root_rule,                   'index.php?' . $add_questions_id, $priority );
		
		// Edit question
		add_rewrite_rule( $edit_questions_slug . $edit_questions_root_rule,                 'index.php?' . $edit_questions_id, $priority );
		
		// Add group
		add_rewrite_rule( $add_groups_slug . $add_groups_root_rule,                         'index.php?' . $add_groups_id, $priority );
		
		// Edit group
		add_rewrite_rule( $edit_groups_slug . $edit_groups_root_rule,                       'index.php?' . $edit_groups_id, $priority );
		
		// Group requests
		add_rewrite_rule( $group_requests_slug . $group_requests_root_rule,                 'index.php?' . $group_requests_id, $priority );
		
		// Group users
		add_rewrite_rule( $group_users_slug . $group_users_root_rule,                       'index.php?' . $group_users_id, $priority );
		
		// Group admins
		add_rewrite_rule( $group_admins_slug . $group_admins_root_rule,                     'index.php?' . $group_admins_id, $priority );
		
		// Group blocked users
		add_rewrite_rule( $blocked_users_slug . $blocked_users_root_rule,                   'index.php?' . $blocked_users_id, $priority );
		
		// Group posts
		add_rewrite_rule( $group_posts_slug . $posts_group_root_rule,                       'index.php?' . $posts_group_id, $priority );
		
		// View group post
		add_rewrite_rule( $view_group_posts_slug . $view_posts_group_root_rule,             'index.php?' . $view_posts_group_id, $priority );
		
		// Edit group post
		add_rewrite_rule( $edit_group_posts_slug . $edit_posts_group_root_rule,             'index.php?' . $edit_posts_group_id, $priority );
		
		// Add post
		add_rewrite_rule( $add_posts_slug . $add_posts_root_rule,                           'index.php?' . $add_posts_id, $priority );
		
		// Edit post
		add_rewrite_rule( $edit_posts_slug . $edit_posts_root_rule,                         'index.php?' . $edit_posts_id, $priority );
		
		// Edit comment
		add_rewrite_rule( $edit_comments_slug . $edit_comments_root_rule,                   'index.php?' . $edit_comments_id, $priority );
		
		// User profile rules
		$followers_rule                = $user_rule . $user_followers_slug      . '/?$';
		$followers_page_rule           = $user_rule . $user_followers_slug      . $one_parameter;
		$following_rule                = $user_rule . $user_following_slug      . '/?$';
		$following_page_rule           = $user_rule . $user_following_slug      . $one_parameter;
		$questions_rule                = $user_rule . $user_questions_slug      . '/?$';
		$questions_page_rule           = $user_rule . $user_questions_slug      . $one_parameter;
		$answers_rule                  = $user_rule . $user_answers_slug        . '/?$';
		$answers_page_rule             = $user_rule . $user_answers_slug        . $one_parameter;
		$best_answers_rule             = $user_rule . $best_answers_slug        . '/?$';
		$best_answers_page_rule        = $user_rule . $best_answers_slug        . $one_parameter;
		$groups_rule                   = $user_rule . $user_groups_slug         . '/?$';
		$groups_page_rule              = $user_rule . $user_groups_slug         . $one_parameter;
		$joined_groups_rule            = $user_rule . $user_joined_groups_slug  . '/?$';
		$joined_groups_page_rule       = $user_rule . $user_joined_groups_slug  . $one_parameter;
		$managed_groups_rule           = $user_rule . $user_managed_groups_slug . '/?$';
		$managed_groups_page_rule      = $user_rule . $user_managed_groups_slug . $one_parameter;
		$points_rule                   = $user_rule . $user_points_slug         . '/?$';
		$points_page_rule              = $user_rule . $user_points_slug         . $one_parameter;
		$polls_rule                    = $user_rule . $user_polls_slug          . '/?$';
		$polls_page_rule               = $user_rule . $user_polls_slug          . $one_parameter;
		$asked_rule                    = $user_rule . $user_asked_slug          . '/?$';
		$asked_page_rule               = $user_rule . $user_asked_slug          . $one_parameter;
		$asked_questions_rule          = $user_rule . $asked_questions_slug     . '/?$';
		$asked_questions_page_rule     = $user_rule . $asked_questions_slug     . $one_parameter;
		$my_asked_rule                 = $user_rule . $my_asked_slug            . '/?$';
		$my_asked_page_rule            = $user_rule . $my_asked_slug            . $one_parameter;
		$paid_questions_rule           = $user_rule . $paid_questions_slug      . '/?$';
		$paid_questions_page_rule      = $user_rule . $paid_questions_slug      . $one_parameter;
		$followed_rule                 = $user_rule . $user_followed_slug       . '/?$';
		$followed_page_rule            = $user_rule . $user_followed_slug       . $one_parameter;
		$favorites_rule                = $user_rule . $user_favorites_slug      . '/?$';
		$favorites_page_rule           = $user_rule . $user_favorites_slug      . $one_parameter;
		$posts_rule                    = $user_rule . $user_posts_slug          . '/?$';
		$posts_page_rule               = $user_rule . $user_posts_slug          . $one_parameter;
		$comments_rule                 = $user_rule . $user_comments_slug       . '/?$';
		$comments_page_rule            = $user_rule . $user_comments_slug       . $one_parameter;
		$followers_questions_rule      = $user_rule . $followers_questions_slug . '/?$';
		$followers_questions_page_rule = $user_rule . $followers_questions_slug . $one_parameter;
		$followers_answers_rule        = $user_rule . $followers_answers_slug   . '/?$';
		$followers_answers_page_rule   = $user_rule . $followers_answers_slug   . $one_parameter;
		$followers_posts_rule          = $user_rule . $followers_posts_slug     . '/?$';
		$followers_posts_page_rule     = $user_rule . $followers_posts_slug     . $one_parameter;
		$followers_comments_rule       = $user_rule . $followers_comments_slug  . '/?$';
		$followers_comments_page_rule  = $user_rule . $followers_comments_slug  . $one_parameter;

		// Filters
		$questions_rule                = apply_filters('wpqa_questions_rule',$questions_rule,$user_rule,$user_questions_slug);
		$questions_page_rule           = apply_filters('wpqa_questions_page_rule',$questions_page_rule,$user_rule,$user_questions_slug,$one_parameter);
		
		// User Pagination
		$user_pages = array(
			$followers_rule           => $user_followers_id,
			$following_rule           => $user_following_id,
			$questions_rule           => $user_questions_id,
			$answers_rule             => $user_answers_id,
			$best_answers_rule        => $best_answers_id,
			$groups_rule              => $user_groups_id,
			$joined_groups_rule       => $user_joined_groups_id,
			$managed_groups_rule      => $user_managed_groups_id,
			$points_rule              => $user_points_id,
			$polls_rule               => $user_polls_id,
			$asked_rule               => $user_wpqa_asked,
			$asked_questions_rule     => $asked_questions_id,
			$my_asked_rule            => $my_asked_id,
			$paid_questions_rule      => $paid_questions_id,
			$followed_rule            => $user_followed_id,
			$favorites_rule           => $user_favorites_id,
			$posts_rule               => $user_posts_id,
			$comments_rule            => $user_comments_id,
			$followers_questions_rule => $followers_questions_id,
			$followers_answers_rule   => $followers_answers_id,
			$followers_posts_rule     => $followers_posts_id,
			$followers_comments_rule  => $followers_comments_id,
		);
		foreach ($user_pages as $key => $value) {
			add_rewrite_rule( $user_slug . $key, 'index.php?' . $user_id  . '=$matches[1]&' . $value . '=1', $priority );
		}
		$user_paged_pages = array(
			$followers_page_rule           => $user_followers_id,
			$following_page_rule           => $user_following_id,
			$questions_page_rule           => $user_questions_id,
			$answers_page_rule             => $user_answers_id,
			$best_answers_page_rule        => $best_answers_id,
			$groups_page_rule              => $user_groups_id,
			$joined_groups_page_rule       => $user_joined_groups_id,
			$managed_groups_page_rule      => $user_managed_groups_id,
			$points_page_rule              => $user_points_id,
			$polls_page_rule               => $user_polls_id,
			$asked_page_rule               => $user_wpqa_asked,
			$asked_questions_page_rule     => $asked_questions_id,
			$my_asked_page_rule            => $my_asked_id,
			$paid_questions_page_rule      => $paid_questions_id,
			$followed_page_rule            => $user_followed_id,
			$favorites_page_rule           => $user_favorites_id,
			$posts_page_rule               => $user_posts_id,
			$comments_page_rule            => $user_comments_id,
			$followers_questions_page_rule => $followers_questions_id,
			$followers_answers_page_rule   => $followers_answers_id,
			$followers_posts_page_rule     => $followers_posts_id,
			$followers_comments_page_rule  => $followers_comments_id,
		);
		foreach ($user_paged_pages as $key => $value) {
			add_rewrite_rule( $user_slug . $key, 'index.php?' . $user_id . '=$matches[1]&' . $value . '=1&' . $page_id . '=$matches[3]', $priority );
		}
		add_rewrite_rule( $user_slug . $root_rule, 'index.php?' . $user_id . '=$matches[1]', $priority );
	}
endif;
/* Add permalink structures */
add_action('wpqa_init','wpqa_permastructs',2);
if (!function_exists('wpqa_permastructs')) :
	function wpqa_permastructs() {
		$user_id                = apply_filters('wpqa_user_id','wpqa_user_id');
		$user_slug              = wpqa_options('profile_slug','profile');
		$search_id              = apply_filters('wpqa_search_id','search');
		$search_slug            = wpqa_options('search_slug','search');
		$checkout_id            = apply_filters('wpqa_checkout_id','checkout');
		$checkout_slug          = (class_exists('woocommerce')?'wpqa-':'').wpqa_options('checkout_slug','checkout');
		$subscriptions_id       = apply_filters('wpqa_subscriptions','wpqa_subscriptions');
		$subscriptions_slug     = wpqa_options('subscriptions_slug','checkout');
		$buy_points_id          = apply_filters('wpqa_buy_points','wpqa_buy_points');
		$buy_points_slug        = wpqa_options('buy_points_slug','buy-points');
		$login_id               = apply_filters('wpqa_login','wpqa_login');
		$login_slug             = wpqa_options('login_slug','log-in');
		$signup_id              = apply_filters('wpqa_signup','wpqa_signup');
		$signup_slug            = wpqa_options('signup_slug','sign-up');
		$lost_password_id       = apply_filters('wpqa_lost_password','wpqa_lost_password');
		$lost_password_slug     = wpqa_options('lost_password_slug','lost-password');
		$settings_id            = apply_filters('wpqa_settings_id','settings');
		$settings_slug          = wpqa_options('settings_slug','settings');
		$settings_slug_rule     = ($settings_slug != ''?$settings_slug.'/':'');
		$edit_id                = apply_filters('wpqa_edit_id','edit');
		$edit_slug              = wpqa_options('edit_slug','edit');
		$password_id            = apply_filters('wpqa_password_id','password');
		$password_slug          = wpqa_options('password_slug','change-password');
		$privacy_id             = apply_filters('wpqa_privacy_id','privacy');
		$privacy_slug           = wpqa_options('privacy_slug','privacy');
		$financial_id           = apply_filters('wpqa_financial_id','financial');
		$financial_slug         = wpqa_options('financial_slug','financial');
		$withdrawals_id         = apply_filters('wpqa_withdrawals_id','withdrawals');
		$withdrawals_slug       = wpqa_options('withdrawals_slug','withdrawals');
		$transactions_id        = apply_filters('wpqa_transactions_id','transactions');
		$transactions_slug      = wpqa_options('transactions_slug','transactions');
		$mails_id               = apply_filters('wpqa_mails_id','mails');
		$mails_slug             = wpqa_options('mails_slug','mails');
		$delete_id              = apply_filters('wpqa_delete_id','delete');
		$delete_slug            = wpqa_options('delete_slug','delete');
		$blocking_id            = apply_filters('wpqa_blocking','blocking');
		$blocking_slug          = wpqa_options('blocking_slug','blocking');
		$pending_questions_id   = apply_filters('wpqa_pending_questions','pending_questions');
		$pending_questions_slug = wpqa_options('pending_questions_slug','pending-questions');
		$pending_posts_id       = apply_filters('wpqa_pending_posts','pending_posts');
		$pending_posts_slug     = wpqa_options('pending_posts_slug','pending-posts');
		$notifications_id       = apply_filters('wpqa_notifications','notifications');
		$notifications_slug     = wpqa_options('notifications_slug','notifications');
		$activities_id          = apply_filters('wpqa_activities','activities');
		$activities_slug        = wpqa_options('activities_slug','activities');
		$referrals_id           = apply_filters('wpqa_referrals','referrals');
		$referrals_slug         = wpqa_options('referrals_slug','referrals');
		$messages_id            = apply_filters('wpqa_messages','messages');
		$messages_slug          = wpqa_options('messages_slug','messages');
		$add_category_id        = apply_filters('wpqa_add_categories','add_category');
		$add_category_slug      = wpqa_options('add_category_slug','add-category');
		$add_questions_id       = apply_filters('wpqa_add_questions','add_question');
		$add_questions_slug     = wpqa_options('add_questions_slug','add-question');
		$edit_questions_id      = apply_filters('wpqa_edit_questions','edit_question');
		$edit_questions_slug    = wpqa_options('edit_questions_slug','edit-question');
		$add_groups_id          = apply_filters('wpqa_add_groups','add_group');
		$add_groups_slug        = wpqa_options('add_groups_slug','add-group');
		$edit_groups_id         = apply_filters('wpqa_edit_groups','edit_group');
		$edit_groups_slug       = wpqa_options('edit_groups_slug','edit-group');
		$group_requests_id      = apply_filters('wpqa_group_requests','group_request');
		$group_requests_slug    = wpqa_options('group_requests_slug','user-requests');
		$group_users_id         = apply_filters('wpqa_group_users','group_user');
		$group_users_slug       = wpqa_options('group_users_slug','group-users');
		$group_admins_id        = apply_filters('wpqa_group_admins','group_admin');
		$group_admins_slug      = wpqa_options('group_admins_slug','group-admins');
		$blocked_users_id       = apply_filters('wpqa_blocked_users','blocked_user');
		$blocked_users_slug     = wpqa_options('blocked_users_slug','blocked-users');
		$posts_group_id         = apply_filters('wpqa_posts_group','post_group');
		$group_posts_slug       = wpqa_options('group_posts_slug','group-pending-posts');
		$view_posts_group_id    = apply_filters('wpqa_view_posts_group','view_post_group');
		$view_group_posts_slug  = wpqa_options('view_group_posts_slug','view-post-group');
		$edit_posts_group_id    = apply_filters('wpqa_edit_posts_group','edit_post_group');
		$edit_group_posts_slug  = wpqa_options('edit_group_posts_slug','edit-post-group');
		$add_posts_id           = apply_filters('wpqa_add_posts','wpqa_add_post');
		$add_posts_slug         = wpqa_options('add_posts_slug','add-post');
		$edit_posts_id          = apply_filters('wpqa_edit_posts','edit_post');
		$edit_posts_slug        = wpqa_options('edit_posts_slug','edit-post');
		$edit_comments_id       = apply_filters('wpqa_edit_comments','edit_comment');
		$edit_comments_slug     = wpqa_options('edit_comments_slug','edit-comment');
		
		// User Permastruct
		add_permastruct( $user_id, $user_slug . '%' . $user_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Search Permastruct
		add_permastruct( $search_id, $search_slug . '/%' . $search_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Checkout Permastruct
		add_permastruct( $checkout_id, $checkout_slug . '/%' . $checkout_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Subscriptions Permastruct
		add_permastruct( $subscriptions_id, $subscriptions_slug . '/%' . $subscriptions_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Buy points Permastruct
		add_permastruct( $buy_points_id, $buy_points_slug . '/%' . $buy_points_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Login Permastruct
		add_permastruct( $login_id, $login_slug . '/%' . $login_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Signup Permastruct
		add_permastruct( $signup_id, $signup_slug . '/%' . $signup_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Lost password Permastruct
		add_permastruct( $lost_password_id, $lost_password_slug . '/%' . $lost_password_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Settings Permastruct
		add_permastruct( $settings_id, $settings_slug_rule . $settings_slug . '/%' . $settings_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Edit Permastruct
		add_permastruct( $edit_id, $settings_slug_rule . $edit_slug . '/%' . $edit_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Password Permastruct
		add_permastruct( $password_id, $settings_slug_rule . $password_slug . '/%' . $password_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Privacy Permastruct
		add_permastruct( $privacy_id, $settings_slug_rule . $privacy_slug . '/%' . $privacy_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Financial Permastruct
		add_permastruct( $financial_id, $settings_slug_rule . $financial_slug . '/%' . $financial_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Withdrawals Permastruct
		add_permastruct( $withdrawals_id, $settings_slug_rule . $withdrawals_slug . '/%' . $withdrawals_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Transactions Permastruct
		add_permastruct( $transactions_id, $transactions_slug . '/%' . $transactions_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Mails Permastruct
		add_permastruct( $mails_id, $settings_slug_rule . $mails_slug . '/%' . $mails_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Delete Permastruct
		add_permastruct( $delete_id, $settings_slug_rule . $delete_slug . '/%' . $delete_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Blocking Permastruct
		add_permastruct( $blocking_id, $blocking_slug . '/%' . $blocking_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Pending Questions Permastruct
		add_permastruct( $pending_questions_id, $pending_questions_slug . '/%' . $pending_questions_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Pending Posts Permastruct
		add_permastruct( $pending_posts_id, $pending_posts_slug . '/%' . $pending_posts_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Notifications Permastruct
		add_permastruct( $notifications_id, $notifications_slug . '/%' . $notifications_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Activities Permastruct
		add_permastruct( $activities_id, $activities_slug . '/%' . $activities_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Referrals Permastruct
		add_permastruct( $referrals_id, $referrals_slug . '/%' . $referrals_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Messages Permastruct
		add_permastruct( $messages_id, $messages_slug . '/%' . $messages_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Add category Permastruct
		add_permastruct( $add_category_id, $add_category_slug . '/%' . $add_category_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Ask question Permastruct
		add_permastruct( $add_questions_id, $add_questions_slug . '/%' . $add_questions_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Edit question Permastruct
		add_permastruct( $edit_questions_id, $edit_questions_slug . '/%' . $edit_questions_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Add group Permastruct
		add_permastruct( $add_groups_id, $add_groups_slug . '/%' . $add_groups_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Edit group Permastruct
		add_permastruct( $edit_groups_id, $edit_groups_slug . '/%' . $edit_groups_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Group requests Permastruct
		add_permastruct( $group_requests_id, $group_requests_slug . '/%' . $group_requests_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Group users Permastruct
		add_permastruct( $group_users_id, $group_users_slug . '/%' . $group_users_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Group admins Permastruct
		add_permastruct( $group_admins_id, $group_admins_slug . '/%' . $group_admins_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Group blocked users Permastruct
		add_permastruct( $blocked_users_id, $blocked_users_slug . '/%' . $blocked_users_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Group posts Permastruct
		add_permastruct( $posts_group_id, $group_posts_slug . '/%' . $posts_group_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// View group post Permastruct
		add_permastruct( $view_posts_group_id, $view_group_posts_slug . '/%' . $view_posts_group_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Edit group post Permastruct
		add_permastruct( $edit_posts_group_id, $edit_group_posts_slug . '/%' . $edit_posts_group_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Add post Permastruct
		add_permastruct( $add_posts_id, $add_posts_slug . '/%' . $add_posts_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Edit post Permastruct
		add_permastruct( $edit_posts_id, $edit_posts_slug . '/%' . $edit_posts_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
		
		// Edit comment Permastruct
		add_permastruct( $edit_comments_id, $edit_comments_slug . '/%' . $edit_comments_id . '%', array(
			'with_front'  => false,
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => false,
			'forcomments' => false,
			'walk_dirs'   => true,
			'endpoints'   => false,
		) );
	}
endif;
/* Get the file */
if (!function_exists('wpqa_get_template')) :
	function wpqa_get_template( $templates,$folder = '' ) {
		if (isset($templates) && is_array($templates)) {
			foreach ($templates as $template) {
				$located = wpqa_locate_template( $template, $folder );
				if ( file_exists( $located ) ) {
					$located = apply_filters( 'wpqa_get_template', $located, $template, $folder );
					return $located;
					break;
				}
			}
		}else if (isset($templates) && is_string($templates)) {
			$located = wpqa_locate_template( $templates, $folder );
			if ( file_exists( $located ) ) {
				$located = apply_filters( 'wpqa_get_template', $located, $templates, $folder );
				return $located;
			}
		}
	}
endif;
/* Locate template */
if (!function_exists('wpqa_locate_template')) :
	function wpqa_locate_template( $template_name, $folder = '' ) {
		global $wpqa;
		$template_path = 'wpqa/templates/' . ($folder != '' ? $folder : '');
		$template_path_2 = 'WPQA/templates/' . ($folder != '' ? $folder : '');
		$template_path_3 = 'wpqa/' . ($folder != '' ? $folder : '');
		$template_path_4 = 'WPQA/' . ($folder != '' ? $folder : '');
		$default_path = $wpqa->wpqa_main_path() . 'templates/' . ($folder != '' ? $folder : '');
		$child_template = locate_template(array(
		    trailingslashit($template_path) . $template_name,
		    trailingslashit($template_path_2) . $template_name,
		    trailingslashit($template_path_3) . $template_name,
		    trailingslashit($template_path_4) . $template_name
		));
		if ($child_template && file_exists($child_template)) {
		    $template = $child_template;
		}else {
		    $template = $default_path . $template_name;
		}
		return apply_filters( 'wpqa_locate_template', $template, $template_name, $folder );
	}
endif;
/* Templates content */
function wpqa_content() {
	if (wpqa_is_user_profile()) {
		$user_id  = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
		$nicename = esc_html(get_query_var('author_name'));
	}else {
		$user_id = get_current_user_id();
		$user = get_userdata($user_id);
		$nicename = (isset($user->user_nicename) && $user->user_nicename != ''?$user->user_nicename:(isset($user->user_login) && $user->user_login != ''?$user->user_login:''));
	}

	$templates = array();
	
	if (wpqa_is_user_followers()) {
		$templates = array(
			'user-followers-'.$nicename.'.php',
			'user-followers-'.$user_id.'.php',
			'user-followers.php',
			'profile.php',
		);
	}else if (wpqa_is_user_following()) {
		$templates = array(
			'user-following-'.$nicename.'.php',
			'user-following-'.$user_id.'.php',
			'user-following.php',
			'profile.php',
		);
	}else if (wpqa_is_user_questions()) {
		$templates = array(
			'user-questions-'.$nicename.'.php',
			'user-questions-'.$user_id.'.php',
			'user-questions.php',
			'profile.php',
		);
	}else if (wpqa_is_user_answers()) {
		$templates = array(
			'user-answers-'.$nicename.'.php',
			'user-answers-'.$user_id.'.php',
			'user-answers.php',
			'profile.php',
		);
	}else if (wpqa_is_best_answers()) {
		$templates = array(
			'user-best-answers-'.$nicename.'.php',
			'user-best-answers-'.$user_id.'.php',
			'user-best-answers.php',
			'profile.php',
		);
	}else if (wpqa_is_user_groups()) {
		$templates = array(
			'user-groups-'.$nicename.'.php',
			'user-groups-'.$user_id.'.php',
			'user-groups.php',
			'profile.php',
		);
	}else if (wpqa_is_user_joined_groups()) {
		$templates = array(
			'user-joined-groups-'.$nicename.'.php',
			'user-joined-groups-'.$user_id.'.php',
			'user-joined-groups.php',
			'profile.php',
		);
	}else if (wpqa_is_user_managed_groups()) {
		$templates = array(
			'user-managed-groups-'.$nicename.'.php',
			'user-managed-groups-'.$user_id.'.php',
			'user-managed-groups.php',
			'profile.php',
		);
	}else if (wpqa_is_user_points()) {
		$templates = array(
			'user-points-'.$nicename.'.php',
			'user-points-'.$user_id.'.php',
			'user-points.php',
			'profile.php',
		);
	}else if (wpqa_is_user_polls()) {
		$templates = array(
			'user-polls-'.$nicename.'.php',
			'user-polls-'.$user_id.'.php',
			'user-polls.php',
			'profile.php',
		);
	}else if (wpqa_is_user_asked()) {
		$templates = array(
			'user-asked-'.$nicename.'.php',
			'user-asked-'.$user_id.'.php',
			'user-asked.php',
			'profile.php',
		);
	}else if (wpqa_is_asked_questions()) {
		$templates = array(
			'user-asked-questions-'.$nicename.'.php',
			'user-asked-questions-'.$user_id.'.php',
			'user-asked-questions.php',
			'profile.php',
		);
	}else if (wpqa_is_my_asked()) {
		$templates = array(
			'user-my-asked-'.$nicename.'.php',
			'user-my-asked-'.$user_id.'.php',
			'user-my-asked.php',
			'profile.php',
		);
	}else if (wpqa_is_paid_questions()) {
		$templates = array(
			'user-paid-questions-'.$nicename.'.php',
			'user-paid-questions-'.$user_id.'.php',
			'user-paid-questions.php',
			'profile.php',
		);
	}else if (wpqa_is_user_followed()) {
		$templates = array(
			'user-followed-'.$nicename.'.php',
			'user-followed-'.$user_id.'.php',
			'user-followed.php',
			'profile.php',
		);
	}else if (wpqa_is_user_favorites()) {
		$templates = array(
			'user-favorites-'.$nicename.'.php',
			'user-favorites-'.$user_id.'.php',
			'user-favorites.php',
			'profile.php',
		);
	}else if (wpqa_is_user_posts()) {
		$templates = array(
			'user-posts-'.$nicename.'.php',
			'user-posts-'.$user_id.'.php',
			'user-posts.php',
			'profile.php',
		);
	}else if (wpqa_is_user_comments()) {
		$templates = array(
			'user-comments-'.$nicename.'.php',
			'user-comments-'.$user_id.'.php',
			'user-comments.php',
			'profile.php',
		);
	}else if (wpqa_is_followers_questions()) {
		$templates = array(
			'user-followers-questions-'.$nicename.'.php',
			'user-followers-questions-'.$user_id.'.php',
			'user-followers-questions.php',
			'profile.php',
		);
	}else if (wpqa_is_followers_answers()) {
		$templates = array(
			'user-followers-answers-'.$nicename.'.php',
			'user-followers-answers-'.$user_id.'.php',
			'user-followers-answers.php',
			'profile.php',
		);
	}else if (wpqa_is_followers_posts()) {
		$templates = array(
			'user-followers-posts-'.$nicename.'.php',
			'user-followers-posts-'.$user_id.'.php',
			'user-followers-posts.php',
			'profile.php',
		);
	}else if (wpqa_is_followers_comments()) {
		$templates = array(
			'user-followers-comments-'.$nicename.'.php',
			'user-followers-comments-'.$user_id.'.php',
			'user-followers-comments.php',
			'profile.php',
		);
	}else if ( wpqa_is_user_edit_profile() ) {
		$templates = array(
			'edit-'.$nicename.'.php',
			'edit-'.$user_id.'.php',
			'edit.php',
			'profile.php',
		);
	}else if ( wpqa_is_user_password_profile() ) {
		$templates = array(
			'password-'.$nicename.'.php',
			'password-'.$user_id.'.php',
			'password.php',
			'profile.php',
		);
	}else if ( wpqa_is_user_privacy_profile() ) {
		$templates = array(
			'privacy-'.$nicename.'.php',
			'privacy-'.$user_id.'.php',
			'privacy.php',
			'profile.php',
		);
	}else if ( wpqa_is_user_withdrawals_profile() ) {
		$templates = array(
			'withdrawals-'.$nicename.'.php',
			'withdrawals-'.$user_id.'.php',
			'withdrawals.php',
			'profile.php',
		);
	}else if ( wpqa_is_user_financial_profile() ) {
		$templates = array(
			'financial-'.$nicename.'.php',
			'financial-'.$user_id.'.php',
			'financial.php',
			'profile.php',
		);
	}else if ( wpqa_is_user_transactions_profile() ) {
		$templates = array(
			'transactions-'.$nicename.'.php',
			'transactions-'.$user_id.'.php',
			'transactions.php',
			'profile.php',
		);
	}else if ( wpqa_is_user_mails_profile() ) {
		$templates = array(
			'mails-'.$nicename.'.php',
			'mails-'.$user_id.'.php',
			'mails.php',
			'profile.php',
		);
	}else if ( wpqa_is_user_delete_profile() ) {
		$templates = array(
			'delete-'.$nicename.'.php',
			'delete-'.$user_id.'.php',
			'delete.php',
			'profile.php',
		);
	}else if ( wpqa_is_user_profile() ) {
		$templates = array(
			'profile-'.$nicename.'.php',
			'profile-'.$user_id.'.php',
			'profile.php',
		);
	}else if (wpqa_is_user_blocking()) {
		$templates = array(
			'user-blocking-'.$nicename.'.php',
			'user-blocking-'.$user_id.'.php',
			'user-blocking.php',
			'profile.php',
		);
	}else if (wpqa_is_pending_questions()) {
		$templates = array(
			'user-pending-questions-'.$nicename.'.php',
			'user-pending-questions-'.$user_id.'.php',
			'user-pending-questions.php',
			'profile.php',
		);
	}else if (wpqa_is_pending_posts()) {
		$templates = array(
			'user-pending-posts-'.$nicename.'.php',
			'user-pending-posts-'.$user_id.'.php',
			'user-pending-posts.php',
			'profile.php',
		);
	}else if (wpqa_is_user_notifications()) {
		$templates = array(
			'user-notifications-'.$nicename.'.php',
			'user-notifications-'.$user_id.'.php',
			'user-notifications.php',
			'profile.php',
		);
	}else if (wpqa_is_user_activities()) {
		$templates = array(
			'user-activities-'.$nicename.'.php',
			'user-activities-'.$user_id.'.php',
			'user-activities.php',
			'profile.php',
		);
	}else if (wpqa_is_user_referrals()) {
		$templates = array(
			'user-referrals-'.$nicename.'.php',
			'user-referrals-'.$user_id.'.php',
			'user-referrals.php',
			'profile.php',
		);
	}else if (wpqa_is_user_messages()) {
		$templates = array(
			'user-messages-'.$nicename.'.php',
			'user-messages-'.$user_id.'.php',
			'user-messages.php',
			'profile.php',
		);
	}else if ( wpqa_is_search() ) {
		$templates = array(
			'search.php',
		);
	}else if ( wpqa_is_checkout() ) {
		$templates = array(
			'checkout.php',
		);
	}else if ( wpqa_is_subscriptions() ) {
		$templates = array(
			'subscriptions.php',
		);
	}else if ( wpqa_is_buy_points() ) {
		$templates = array(
			'buy-points.php',
		);
	}else if ( wpqa_is_login() ) {
		$templates = array(
			'login.php',
		);
	}else if ( wpqa_is_signup() ) {
		$templates = array(
			'signup.php',
		);
	}else if ( wpqa_is_lost_password() ) {
		$templates = array(
			'lost-password.php',
		);
	}else if ( wpqa_is_add_category() ) {
		$templates = array(
			'add-category.php',
		);
	}else if ( wpqa_is_add_questions() ) {
		$templates = array(
			'add-question.php',
		);
	}else if ( wpqa_is_edit_questions() ) {
		$templates = array(
			'edit-question.php',
		);
	}else if ( wpqa_is_add_groups() ) {
		$templates = array(
			'add-group.php',
		);
	}else if ( wpqa_is_edit_groups() ) {
		$templates = array(
			'edit-group.php',
		);
	}else if ( wpqa_is_group_requests() ) {
		$templates = array(
			'group-requests.php',
		);
	}else if ( wpqa_is_group_users() ) {
		$templates = array(
			'group-users.php',
		);
	}else if ( wpqa_is_group_admins() ) {
		$templates = array(
			'group-admins.php',
		);
	}else if ( wpqa_is_blocked_users() ) {
		$templates = array(
			'blocked-users.php',
		);
	}else if ( wpqa_is_posts_group() ) {
		$templates = array(
			'pending-posts.php',
		);
	}else if ( wpqa_is_view_posts_group() ) {
		$templates = array(
			'view-post-group.php',
		);
	}else if ( wpqa_is_edit_posts_group() ) {
		$templates = array(
			'edit-post-group.php',
		);
	}else if ( wpqa_is_add_posts() ) {
		$templates = array(
			'add-post.php',
		);
	}else if ( wpqa_is_edit_posts() ) {
		$templates = array(
			'edit-post.php',
		);
	}else if ( wpqa_is_edit_comments() ) {
		$templates = array(
			'edit-comment.php',
		);
	}

	$templates = apply_filters('wpqa_filter_content',$templates);
	
	if (isset($templates) && is_array($templates) && !empty($templates)) {
		if (wpqa_is_user_profile() || wpqa_is_main_edit_profile_pages()) {
			$folder = 'profile';
		}
		$wpqa_get_template = wpqa_get_template($templates,(isset($folder) && $folder != ''?$folder.'/':''));
		if ($wpqa_get_template) {
			include $wpqa_get_template;
		}
	}
}
/* WPQA template */
function wpqa_is_template() {
	$wpqa_template_filter = apply_filters('wpqa_template_include',false);
	if (wpqa_is_user_profile() || wpqa_is_search() || wpqa_is_checkout() || wpqa_is_subscriptions() || wpqa_is_buy_points() || wpqa_is_login() || wpqa_is_signup() || wpqa_is_lost_password() || wpqa_is_main_edit_profile_pages() || wpqa_is_add_category() || wpqa_is_add_questions() || wpqa_is_edit_questions() || wpqa_is_add_groups() || wpqa_is_edit_groups() || wpqa_is_group_requests() || wpqa_is_group_users() || wpqa_is_group_admins() || wpqa_is_blocked_users() || wpqa_is_posts_group() || wpqa_is_view_posts_group() || wpqa_is_edit_posts_group() || wpqa_is_add_posts() || wpqa_is_edit_posts() || wpqa_is_edit_comments() || $wpqa_template_filter == true) {
		return true;
	}
	return;
}
/* Include templates */
add_filter('template_include','wpqa_template_include',10);
if (!function_exists('wpqa_template_include')) :
	function wpqa_template_include($new_template) {
		if (wpqa_is_template()) {
			global $wp_query;
			$wp_query->is_404 = false;
			status_header( 200 );
			
			$templates = array(
				'wpqa.php'
			);
			if (!empty($templates)) {
				include wpqa_get_template($templates);
			}
		}else {
			return $new_template;
		}
	}
endif;
/* Check if user in specific page */
if (!function_exists('wpqa_is_specific_page')) :
	function wpqa_is_specific_page() {
		global $wp_query;
		$return = '';
		if ( !empty( $wp_query->wpqa_specific_name ) && ( '' !== $wp_query->wpqa_specific_name ) )
			$return = $wp_query->wpqa_specific_name;
		return esc_html(apply_filters( 'wpqa_is_specific_page', $return ));
	}
endif;
/* Check if user in search */
if (!function_exists('wpqa_is_search')) :
	function wpqa_is_search() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_search ) && ( true === $wp_query->wpqa_is_search ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_search', $return );
	}
endif;
/* Check if user in checkout */
if (!function_exists('wpqa_is_checkout')) :
	function wpqa_is_checkout() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_checkout ) && ( true === $wp_query->wpqa_is_checkout ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_checkout', $return );
	}
endif;
/* Check if user in subscriptions */
if (!function_exists('wpqa_is_subscriptions')) :
	function wpqa_is_subscriptions() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_subscriptions ) && ( true === $wp_query->wpqa_is_subscriptions ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_subscriptions', $return );
	}
endif;
/* Check if user in buy points */
if (!function_exists('wpqa_is_buy_points')) :
	function wpqa_is_buy_points() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_buy_points ) && ( true === $wp_query->wpqa_is_buy_points ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_buy_points', $return );
	}
endif;
/* Check if user in login */
if (!function_exists('wpqa_is_login')) :
	function wpqa_is_login() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_login ) && ( true === $wp_query->wpqa_is_login ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_login', $return );
	}
endif;
/* Check if user in signup */
if (!function_exists('wpqa_is_signup')) :
	function wpqa_is_signup() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_signup ) && ( true === $wp_query->wpqa_is_signup ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_signup', $return );
	}
endif;
/* Check if user in lost password */
if (!function_exists('wpqa_is_lost_password')) :
	function wpqa_is_lost_password() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_lost_password ) && ( true === $wp_query->wpqa_is_lost_password ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_lost_password', $return );
	}
endif;
/* Check if user in add category */
if (!function_exists('wpqa_is_add_category')) :
	function wpqa_is_add_category() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_add_category ) && ( true === $wp_query->wpqa_is_add_category ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_add_category', $return );
	}
endif;
/* Check if user in ask question */
if (!function_exists('wpqa_is_add_questions')) :
	function wpqa_is_add_questions() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_add_questions ) && ( true === $wp_query->wpqa_is_add_questions ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_add_questions', $return );
	}
endif;
/* Check if user ask question to user */
if (!function_exists('wpqa_is_add_user_questions')) :
	function wpqa_is_add_user_questions() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_add_user_questions ) && ( true === $wp_query->wpqa_is_add_user_questions ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_add_user_questions', $return );
	}
endif;
/* Check if user in edit question */
if (!function_exists('wpqa_is_edit_questions')) :
	function wpqa_is_edit_questions() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_edit_questions ) && ( true === $wp_query->wpqa_is_edit_questions ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_edit_questions', $return );
	}
endif;
/* Check if user in edit tags */
if (!function_exists('wpqa_is_edit_tags')) :
	function wpqa_is_edit_tags() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_edit_tags ) && ( true === $wp_query->wpqa_is_edit_tags ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_edit_tags', $return );
	}
endif;
/* Check if user in add group */
if (!function_exists('wpqa_is_add_groups')) :
	function wpqa_is_add_groups() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_add_groups ) && ( true === $wp_query->wpqa_is_add_groups ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_add_groups', $return );
	}
endif;
/* Check if user in edit group */
if (!function_exists('wpqa_is_edit_groups')) :
	function wpqa_is_edit_groups() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_edit_groups ) && ( true === $wp_query->wpqa_is_edit_groups ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_edit_groups', $return );
	}
endif;
/* Check if user in group requests */
if (!function_exists('wpqa_is_group_requests')) :
	function wpqa_is_group_requests() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_group_requests ) && ( true === $wp_query->wpqa_is_group_requests ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_group_requests', $return );
	}
endif;
/* Check if user in group users */
if (!function_exists('wpqa_is_group_users')) :
	function wpqa_is_group_users() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_group_users ) && ( true === $wp_query->wpqa_is_group_users ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_group_users', $return );
	}
endif;
/* Check if user in group admins */
if (!function_exists('wpqa_is_group_admins')) :
	function wpqa_is_group_admins() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_group_admins ) && ( true === $wp_query->wpqa_is_group_admins ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_group_admins', $return );
	}
endif;
/* Check if user in blocked users */
if (!function_exists('wpqa_is_blocked_users')) :
	function wpqa_is_blocked_users() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_blocked_users ) && ( true === $wp_query->wpqa_is_blocked_users ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_blocked_users', $return );
	}
endif;
/* Check if user in group posts */
if (!function_exists('wpqa_is_posts_group')) :
	function wpqa_is_posts_group() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_posts_group ) && ( true === $wp_query->wpqa_is_posts_group ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_posts_group', $return );
	}
endif;
/* Check if user in view group post */
if (!function_exists('wpqa_is_view_posts_group')) :
	function wpqa_is_view_posts_group() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_view_posts_group ) && ( true === $wp_query->wpqa_is_view_posts_group ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_view_posts_group', $return );
	}
endif;
/* Check if user in edit group post */
if (!function_exists('wpqa_is_edit_posts_group')) :
	function wpqa_is_edit_posts_group() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_edit_posts_group ) && ( true === $wp_query->wpqa_is_edit_posts_group ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_edit_posts_group', $return );
	}
endif;
/* Check if user in add post */
if (!function_exists('wpqa_is_add_posts')) :
	function wpqa_is_add_posts() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_add_posts ) && ( true === $wp_query->wpqa_is_add_posts ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_add_posts', $return );
	}
endif;
/* Check if user in edit post */
if (!function_exists('wpqa_is_edit_posts')) :
	function wpqa_is_edit_posts() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_edit_posts ) && ( true === $wp_query->wpqa_is_edit_posts ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_edit_posts', $return );
	}
endif;
/* Check if user in edit comment */
if (!function_exists('wpqa_is_edit_comments')) :
	function wpqa_is_edit_comments() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_edit_comments ) && ( true === $wp_query->wpqa_is_edit_comments ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_edit_comments', $return );
	}
endif;
/* Check if the user in profile */
if (!function_exists('wpqa_is_user_profile')) :
	function wpqa_is_user_profile() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_profile ) && ( true === $wp_query->wpqa_is_user_profile ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_profile', $return );
	}
endif;
/* Check if the user in home profile */
if (!function_exists('wpqa_is_home_profile')) :
	function wpqa_is_home_profile() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_home_profile ) && ( true === $wp_query->wpqa_is_home_profile ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_home_profile', $return );
	}
endif;
/* Check if the user in owne profile */
if (!function_exists('wpqa_is_user_owner')) :
	function wpqa_is_user_owner() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_owner ) && ( true === $wp_query->wpqa_is_user_owner ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_owner', $return );
	}
endif;
/* Check if the user in main edit profile pages */
if (!function_exists('wpqa_is_main_edit_profile_pages')) :
	function wpqa_is_main_edit_profile_pages() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_main_edit_profile_pages ) && ( true === $wp_query->wpqa_is_main_edit_profile_pages ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_main_edit_profile_pages', $return );
	}
endif;
/* Check if the user in settings page */
if (!function_exists('wpqa_is_user_settings_page')) :
	function wpqa_is_user_settings_page() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_settings_page ) && ( true === $wp_query->wpqa_is_user_settings_page ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_settings_page', $return );
	}
endif;
/* Check if the user in edit profile */
if (!function_exists('wpqa_is_user_edit_profile')) :
	function wpqa_is_user_edit_profile() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_edit_profile ) && ( true === $wp_query->wpqa_is_user_edit_profile ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_edit_profile', $return );
	}
endif;
/* Check if the user in edit profile home */
if (!function_exists('wpqa_is_user_edit_home')) :
	function wpqa_is_user_edit_home() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_edit_home ) && ( true === $wp_query->wpqa_is_user_edit_home ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_edit_home', $return );
	}
endif;
/* Check if the user in password profile */
if (!function_exists('wpqa_is_user_password_profile')) :
	function wpqa_is_user_password_profile() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_password_profile ) && ( true === $wp_query->wpqa_is_user_password_profile ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_password_profile', $return );
	}
endif;
/* Check if the user in privacy profile */
if (!function_exists('wpqa_is_user_privacy_profile')) :
	function wpqa_is_user_privacy_profile() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_privacy_profile ) && ( true === $wp_query->wpqa_is_user_privacy_profile ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_privacy_profile', $return );
	}
endif;
/* Check if the user in withdrawals profile */
if (!function_exists('wpqa_is_user_withdrawals_profile')) :
	function wpqa_is_user_withdrawals_profile() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_withdrawals_profile ) && ( true === $wp_query->wpqa_is_user_withdrawals_profile ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_withdrawals_profile', $return );
	}
endif;
/* Check if the user in financial profile */
if (!function_exists('wpqa_is_user_financial_profile')) :
	function wpqa_is_user_financial_profile() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_financial_profile ) && ( true === $wp_query->wpqa_is_user_financial_profile ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_financial_profile', $return );
	}
endif;
/* Check if the user in transactions profile */
if (!function_exists('wpqa_is_user_transactions_profile')) :
	function wpqa_is_user_transactions_profile() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_transactions_profile ) && ( true === $wp_query->wpqa_is_user_transactions_profile ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_transactions_profile', $return );
	}
endif;
/* Check if the user in mails profile */
if (!function_exists('wpqa_is_user_mails_profile')) :
	function wpqa_is_user_mails_profile() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_mails_profile ) && ( true === $wp_query->wpqa_is_user_mails_profile ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_mails_profile', $return );
	}
endif;
/* Check if the user in delete profile */
if (!function_exists('wpqa_is_user_delete_profile')) :
	function wpqa_is_user_delete_profile() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_delete_profile ) && ( true === $wp_query->wpqa_is_user_delete_profile ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_delete_profile', $return );
	}
endif;
/* Check if the user has profile */
if (!function_exists('wpqa_user_has_profile')) :
	function wpqa_user_has_profile( $user_id = 0 ) {
		$return  = true;
		$user_id = wpqa_get_user_id( $user_id, true, true );
		$user    = get_userdata( $user_id );
		if ( empty( $user ) ) {
			$return = false;
		}
		return (bool) apply_filters( 'wpqa_show_user_profile', $return, $user_id );
	}
endif;
/* Check if the user in user blocking */
if (!function_exists('wpqa_is_user_blocking')) :
	function wpqa_is_user_blocking() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_blocking ) && ( true === $wp_query->wpqa_is_user_blocking ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_blocking', $return );
	}
endif;
/* Check if the user in user pending questions */
if (!function_exists('wpqa_is_pending_questions')) :
	function wpqa_is_pending_questions() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_pending_questions ) && ( true === $wp_query->wpqa_is_pending_questions ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_pending_questions', $return );
	}
endif;
/* Check if the user in user pending posts */
if (!function_exists('wpqa_is_pending_posts')) :
	function wpqa_is_pending_posts() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_pending_posts ) && ( true === $wp_query->wpqa_is_pending_posts ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_pending_posts', $return );
	}
endif;
/* Check if the user in user notifications */
if (!function_exists('wpqa_is_user_notifications')) :
	function wpqa_is_user_notifications() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_notifications ) && ( true === $wp_query->wpqa_is_user_notifications ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_notifications', $return );
	}
endif;
/* Check if the user in user activities */
if (!function_exists('wpqa_is_user_activities')) :
	function wpqa_is_user_activities() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_activities ) && ( true === $wp_query->wpqa_is_user_activities ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_activities', $return );
	}
endif;
/* Check if the user in user referrals */
if (!function_exists('wpqa_is_user_referrals')) :
	function wpqa_is_user_referrals() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_referrals ) && ( true === $wp_query->wpqa_is_user_referrals ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_referrals', $return );
	}
endif;
/* Check if the user in user messages */
if (!function_exists('wpqa_is_user_messages')) :
	function wpqa_is_user_messages() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_messages ) && ( true === $wp_query->wpqa_is_user_messages ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_messages', $return );
	}
endif;
/* Check if the user in user followers */
if (!function_exists('wpqa_is_user_followers')) :
	function wpqa_is_user_followers() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_followers ) && ( true === $wp_query->wpqa_is_user_followers ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_followers', $return );
	}
endif;
/* Check if the user in user following */
if (!function_exists('wpqa_is_user_following')) :
	function wpqa_is_user_following() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_following ) && ( true === $wp_query->wpqa_is_user_following ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_following', $return );
	}
endif;
/* Check if the user in user questions */
if (!function_exists('wpqa_is_user_questions')) :
	function wpqa_is_user_questions() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_questions ) && ( true === $wp_query->wpqa_is_user_questions ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_questions', $return );
	}
endif;
/* Check if the user in user answers */
if (!function_exists('wpqa_is_user_answers')) :
	function wpqa_is_user_answers() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_answers ) && ( true === $wp_query->wpqa_is_user_answers ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_answers', $return );
	}
endif;
/* Check if the user in user best answers */
if (!function_exists('wpqa_is_best_answers')) :
	function wpqa_is_best_answers() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_best_answers ) && ( true === $wp_query->wpqa_is_best_answers ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_best_answers', $return );
	}
endif;
/* Check if the user in user groups */
if (!function_exists('wpqa_is_user_groups')) :
	function wpqa_is_user_groups() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_groups ) && ( true === $wp_query->wpqa_is_user_groups ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_groups', $return );
	}
endif;
/* Check if the user in user joined groups */
if (!function_exists('wpqa_is_user_joined_groups')) :
	function wpqa_is_user_joined_groups() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_joined_groups ) && ( true === $wp_query->wpqa_is_user_joined_groups ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_joined_groups', $return );
	}
endif;
/* Check if the user in user managed groups */
if (!function_exists('wpqa_is_user_managed_groups')) :
	function wpqa_is_user_managed_groups() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_managed_groups ) && ( true === $wp_query->wpqa_is_user_managed_groups ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_managed_groups', $return );
	}
endif;
/* Check if the user in user points */
if (!function_exists('wpqa_is_user_points')) :
	function wpqa_is_user_points() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_points ) && ( true === $wp_query->wpqa_is_user_points ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_points', $return );
	}
endif;
/* Check if the user in user polls */
if (!function_exists('wpqa_is_user_polls')) :
	function wpqa_is_user_polls() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_polls ) && ( true === $wp_query->wpqa_is_user_polls ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_polls', $return );
	}
endif;
/* Check if the user in user asked */
if (!function_exists('wpqa_is_user_asked')) :
	function wpqa_is_user_asked() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_asked ) && ( true === $wp_query->wpqa_is_user_asked ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_asked', $return );
	}
endif;
/* Check if the user in user asked questions */
if (!function_exists('wpqa_is_asked_questions')) :
	function wpqa_is_asked_questions() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_asked_questions ) && ( true === $wp_query->wpqa_is_asked_questions ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_asked_questions', $return );
	}
endif;
/* Check if the user in user my asked */
if (!function_exists('wpqa_is_my_asked')) :
	function wpqa_is_my_asked() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_my_asked ) && ( true === $wp_query->wpqa_is_my_asked ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_my_asked', $return );
	}
endif;
/* Check if the user in user paid questions */
if (!function_exists('wpqa_is_paid_questions')) :
	function wpqa_is_paid_questions() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_paid_questions ) && ( true === $wp_query->wpqa_is_paid_questions ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_paid_questions', $return );
	}
endif;
/* Check if the user in user followed */
if (!function_exists('wpqa_is_user_followed')) :
	function wpqa_is_user_followed() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_followed ) && ( true === $wp_query->wpqa_is_user_followed ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_followed', $return );
	}
endif;
/* Check if the user in user favorites */
if (!function_exists('wpqa_is_user_favorites')) :
	function wpqa_is_user_favorites() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_favorites ) && ( true === $wp_query->wpqa_is_user_favorites ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_favorites', $return );
	}
endif;
/* Check if the user in user posts */
if (!function_exists('wpqa_is_user_posts')) :
	function wpqa_is_user_posts() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_posts ) && ( true === $wp_query->wpqa_is_user_posts ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_posts', $return );
	}
endif;
/* Check if the user in user comments */
if (!function_exists('wpqa_is_user_comments')) :
	function wpqa_is_user_comments() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_user_comments ) && ( true === $wp_query->wpqa_is_user_comments ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_user_comments', $return );
	}
endif;
/* Check if the user in user followers questions */
if (!function_exists('wpqa_is_followers_questions')) :
	function wpqa_is_followers_questions() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_followers_questions ) && ( true === $wp_query->wpqa_is_followers_questions ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_followers_questions', $return );
	}
endif;
/* Check if the user in user followers answers */
if (!function_exists('wpqa_is_followers_answers')) :
	function wpqa_is_followers_answers() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_followers_answers ) && ( true === $wp_query->wpqa_is_followers_answers ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_followers_answers', $return );
	}
endif;
/* Check if the user in user followers posts */
if (!function_exists('wpqa_is_followers_posts')) :
	function wpqa_is_followers_posts() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_followers_posts ) && ( true === $wp_query->wpqa_is_followers_posts ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_followers_posts', $return );
	}
endif;
/* Check if the user in user followers comments */
if (!function_exists('wpqa_is_followers_comments')) :
	function wpqa_is_followers_comments() {
		global $wp_query;
		$return = false;
		if ( !empty( $wp_query->wpqa_is_followers_comments ) && ( true === $wp_query->wpqa_is_followers_comments ) )
			$return = true;
		return (bool) apply_filters( 'wpqa_is_followers_comments', $return );
	}
endif;
/* Get user id */
if (!function_exists('wpqa_get_user_id')) :
	function wpqa_get_user_id( $user_id = 0, $displayed_user_fallback = true, $current_user_fallback = false ) {
		if ( !empty( $user_id ) && is_numeric( $user_id ) ) {
			$wpqa_user_id = $user_id;
		}else {
			$wpqa_user_id = 0;
		}
		return (int) apply_filters( 'wpqa_get_user_id', (int) $wpqa_user_id, $displayed_user_fallback, $current_user_fallback );
	}
endif;
/* Get profile title */
if (!function_exists('wpqa_user_title')) :
	function wpqa_user_title() {
		$title = '';
		if (wpqa_is_user_followers()) {
			$title = 'followers';
		}else if (wpqa_is_user_following()) {
			$title = 'following';
		}else if (wpqa_is_user_questions()) {
			$title = 'questions';
		}else if (wpqa_is_user_answers()) {
			$title = 'answers';
		}else if (wpqa_is_best_answers()) {
			$title = 'best-answers';
		}else if (wpqa_is_user_groups()) {
			$title = 'groups';
		}else if (wpqa_is_user_joined_groups()) {
			$title = 'joined_groups';
		}else if (wpqa_is_user_managed_groups()) {
			$title = 'managed_groups';
		}else if (wpqa_is_user_points()) {
			$title = 'points';
		}else if (wpqa_is_user_polls()) {
			$title = 'polls';
		}else if (wpqa_is_user_asked()) {
			$title = 'asked';
		}else if (wpqa_is_asked_questions()) {
			$title = 'asked-questions';
		}else if (wpqa_is_my_asked()) {
			$title = 'my-asked';
		}else if (wpqa_is_paid_questions()) {
			$title = 'paid-questions';
		}else if (wpqa_is_user_followed()) {
			$title = 'followed';
		}else if (wpqa_is_user_favorites()) {
			$title = 'favorites';
		}else if (wpqa_is_user_posts()) {
			$title = 'posts';
		}else if (wpqa_is_user_comments()) {
			$title = 'comments';
		}else if (wpqa_is_followers_questions()) {
			$title = 'followers-questions';
		}else if (wpqa_is_followers_answers()) {
			$title = 'followers-answers';
		}else if (wpqa_is_followers_posts()) {
			$title = 'followers-posts';
		}else if (wpqa_is_followers_comments()) {
			$title = 'followers-comments';
		}else if (wpqa_is_user_transactions_profile()) {
			$title = 'transactions';
		}else if (wpqa_is_user_edit_profile()) {
			$title = 'edit';
		}else if (wpqa_is_user_password_profile()) {
			$title = 'password';
		}else if (wpqa_is_user_privacy_profile()) {
			$title = 'privacy';
		}else if (wpqa_is_user_withdrawals_profile()) {
			$title = 'withdrawals';
		}else if (wpqa_is_user_financial_profile()) {
			$title = 'financial';
		}else if (wpqa_is_user_mails_profile()) {
			$title = 'mails';
		}else if (wpqa_is_user_delete_profile()) {
			$title = 'delete';
		}else if (wpqa_is_user_blocking()) {
			$title = 'blocking';
		}else if (wpqa_is_pending_questions()) {
			$title = 'pending-questions';
		}else if (wpqa_is_pending_posts()) {
			$title = 'pending-posts';
		}else if (wpqa_is_user_notifications()) {
			$title = 'notifications';
		}else if (wpqa_is_user_activities()) {
			$title = 'activities';
		}else if (wpqa_is_user_referrals()) {
			$title = 'referrals';
		}else if (wpqa_is_user_messages()) {
			$title = 'messages';
		}
		return apply_filters('wpqa_filter_user_title',$title);
	}
endif;
/* Parse query */
add_action('parse_query','wpqa_parse_query',2);
if (!function_exists('wpqa_parse_query')) :
	function wpqa_parse_query($posts_query) {
		if (!$posts_query->is_main_query() || true === $posts_query->get('suppress_filters') || is_admin() || is_page() || is_single()) {
			return;
		}

		$get_current_user     = get_current_user_id();

		$user_id              = apply_filters('wpqa_user_id','wpqa_user_id');
		$search_id            = apply_filters('wpqa_search_id','search');
		$search_type          = apply_filters('wpqa_type','search_type');
		$checkout_id          = apply_filters('wpqa_checkout_id','checkout');
		$checkout_item        = apply_filters('wpqa_checkout_item','checkout_item');
		$checkout_related     = apply_filters('wpqa_checkout_related','checkout_related');
		$subscriptions_id     = apply_filters('wpqa_subscriptions','wpqa_subscriptions');
		$buy_points_id        = apply_filters('wpqa_buy_points','wpqa_buy_points');
		$login_id             = apply_filters('wpqa_login','wpqa_login');
		$signup_id            = apply_filters('wpqa_signup','wpqa_signup');
		$lost_password_id     = apply_filters('wpqa_lost_password','wpqa_lost_password');
		$settings_id          = apply_filters('wpqa_settings_id','settings');
		$edit_id              = apply_filters('wpqa_edit_id','edit');
		$password_id          = apply_filters('wpqa_password_id','password');
		$privacy_id           = apply_filters('wpqa_privacy_id','privacy');
		$financial_id         = apply_filters('wpqa_financial_id','financial');
		$withdrawals_id       = apply_filters('wpqa_withdrawals_id','withdrawals');
		$transactions_id      = apply_filters('wpqa_transactions_id','transactions');
		$mails_id             = apply_filters('wpqa_mails_id','mails');
		$delete_id            = apply_filters('wpqa_delete_id','delete');
		$blocking_id          = apply_filters('wpqa_blocking','blocking');
		$pending_questions_id = apply_filters('wpqa_pending_questions','pending_questions');
		$pending_posts_id     = apply_filters('wpqa_pending_posts','pending_posts');
		$notifications_id     = apply_filters('wpqa_notifications','notifications');
		$activities_id        = apply_filters('wpqa_activities','activities');
		$referrals_id         = apply_filters('wpqa_referrals','referrals');
		$messages_id          = apply_filters('wpqa_messages','messages');
		$add_category_id      = apply_filters('wpqa_add_categories','add_category');
		$add_questions_id     = apply_filters('wpqa_add_questions','add_question');
		$edit_questions_id    = apply_filters('wpqa_edit_questions','edit_question');
		$add_groups_id        = apply_filters('wpqa_add_groups','add_group');
		$edit_groups_id       = apply_filters('wpqa_edit_groups','edit_group');
		$group_requests_id    = apply_filters('wpqa_group_requests','group_request');
		$group_users_id       = apply_filters('wpqa_group_users','group_user');
		$group_admins_id      = apply_filters('wpqa_group_admins','group_admin');
		$blocked_users_id     = apply_filters('wpqa_blocked_users','blocked_user');
		$posts_group_id       = apply_filters('wpqa_posts_group','post_group');
		$view_posts_group_id  = apply_filters('wpqa_view_posts_group','view_post_group');
		$edit_posts_group_id  = apply_filters('wpqa_edit_posts_group','edit_post_group');
		$add_posts_id         = apply_filters('wpqa_add_posts','wpqa_add_post');
		$edit_posts_id        = apply_filters('wpqa_edit_posts','edit_post');
		$edit_comments_id     = apply_filters('wpqa_edit_comments','edit_comment');
		
		$is_user              = $posts_query->get($user_id);
		$is_checkout          = $posts_query->get($checkout_id);
		$is_question          = $posts_query->get($edit_questions_id);
		$is_group             = $posts_query->get($edit_groups_id);
		$group_requests       = $posts_query->get($group_requests_id);
		$group_users          = $posts_query->get($group_users_id);
		$group_admins         = $posts_query->get($group_admins_id);
		$blocked_users        = $posts_query->get($blocked_users_id);
		$posts_group          = $posts_query->get($posts_group_id);
		$view_posts_group     = $posts_query->get($view_posts_group_id);
		$edit_posts_group     = $posts_query->get($edit_posts_group_id);
		$is_post              = $posts_query->get($edit_posts_id);
		$is_comment           = $posts_query->get($edit_comments_id);
		
		if ( !empty( $is_user ) || !empty( $is_checkout ) || !empty( $is_question ) || !empty( $is_group ) || !empty( $group_requests ) || !empty( $group_users ) || !empty( $group_admins ) || !empty( $blocked_users ) || !empty( $posts_group ) || !empty( $view_posts_group ) || !empty( $edit_posts_group ) || !empty( $is_post ) || !empty( $is_comment ) || isset($posts_query->query_vars[$search_id]) || isset($posts_query->query_vars[$subscriptions_id]) || isset($posts_query->query_vars[$buy_points_id]) || isset($posts_query->query_vars[$login_id]) || isset($posts_query->query_vars[$signup_id]) || isset($posts_query->query_vars[$lost_password_id]) || isset($posts_query->query_vars[$settings_id]) || isset($posts_query->query_vars[$edit_id]) || isset($posts_query->query_vars[$password_id]) || isset($posts_query->query_vars[$privacy_id]) || isset($posts_query->query_vars[$financial_id]) || isset($posts_query->query_vars[$withdrawals_id]) || isset($posts_query->query_vars[$transactions_id]) || isset($posts_query->query_vars[$mails_id]) || isset($posts_query->query_vars[$delete_id]) || isset($posts_query->query_vars[$blocking_id]) || isset($posts_query->query_vars[$pending_questions_id]) || isset($posts_query->query_vars[$pending_posts_id]) || isset($posts_query->query_vars[$notifications_id]) || isset($posts_query->query_vars[$activities_id]) || isset($posts_query->query_vars[$referrals_id]) || isset($posts_query->query_vars[$messages_id]) || isset($posts_query->query_vars[$add_category_id]) || isset($posts_query->query_vars[$add_questions_id]) || isset($posts_query->query_vars[$add_groups_id]) || isset($posts_query->query_vars[$add_posts_id]) ) {
			$posts_query->is_home = false;
			$posts_query->is_404  = false;
		}
		
		if ( !empty($is_question) ) {
			$the_question = false;
			if ( is_numeric( $is_question ) ) {
				$the_question = get_post( $is_question );
			}
		}
		
		if ( !empty($is_group) ) {
			$the_group = false;
			if ( is_numeric( $is_group ) ) {
				$the_group = get_post( $is_group );
			}
		}
		
		if ( !empty($group_requests) ) {
			$group_requests = false;
			if ( is_numeric( $group_requests ) ) {
				$the_group = get_post( $group_requests );
			}
		}
		
		if ( !empty($group_users) ) {
			$group_users = false;
			if ( is_numeric( $group_users ) ) {
				$the_group = get_post( $group_users );
			}
		}
		
		if ( !empty($group_admins) ) {
			$group_admins = false;
			if ( is_numeric( $group_admins ) ) {
				$the_group = get_post( $group_admins );
			}
		}
		
		if ( !empty($blocked_users) ) {
			$blocked_users = false;
			if ( is_numeric( $blocked_users ) ) {
				$the_group = get_post( $blocked_users );
			}
		}
		
		if ( !empty($posts_group) ) {
			$the_group = false;
			if ( is_numeric( $posts_group ) ) {
				$the_group = get_post( $posts_group );
			}
		}
		
		if ( !empty($view_posts_group) ) {
			$the_post = false;
			if ( is_numeric( $view_posts_group ) ) {
				$the_post = get_post( $view_posts_group );
			}
		}
		
		if ( !empty($edit_posts_group) ) {
			$the_post = false;
			if ( is_numeric( $edit_posts_group ) ) {
				$the_post = get_post( $edit_posts_group );
			}
		}
		
		if ( !empty($is_post) ) {
			$the_post = false;
			if ( is_numeric( $is_post ) ) {
				$the_post = get_post( $is_post );
			}
		}
		
		if ( !empty($is_comment) ) {
			$the_comment = false;
			if ( is_numeric( $is_comment ) ) {
				$the_comment = get_comment( $is_comment );
			}
		}
		
		if ( !empty( $is_user ) ) {
			$the_user = false;
			if ( get_option( 'permalink_structure' ) ) {
				$profile_type = wpqa_options('profile_type');
				$the_user = get_user_by(($profile_type == 'login'?'login':'slug'),$is_user);
				if (isset($is_user) && is_object($is_user)) {
					$the_user = get_userdata(esc_html($is_user->ID));
				}
				if (isset($the_user) && !is_object($the_user)) {
					$the_user = get_user_by(($profile_type == 'login'?'slug':'login'),urldecode($is_user));
				}
				if (isset($the_user) && !is_object($the_user)) {
					$the_user = get_user_by(($profile_type == 'login'?'slug':'login'),str_ireplace('-',' ',urldecode($is_user)));
				}
				if (isset($the_user) && !is_object($the_user)) {
					$the_user = get_user_by('login',str_ireplace('-',' ',urldecode($is_user)));
				}
				if ( is_numeric( $is_user ) ) {
					$the_user = get_user_by('id',$is_user);
					if (isset($the_user) && !is_object($the_user)) {
						$the_user = get_user_by('login',$is_user);
					}
					if (isset($the_user) && !is_object($the_user)) {
						$the_user = get_user_by('slug',urldecode($is_user));
					}
				}
			}else if ( is_numeric( $is_user ) ) {
				$the_user = get_user_by('id',$is_user);
			}
			
			if ( empty( $the_user->ID ) || ! wpqa_user_has_profile( $the_user->ID ) ) {
				$posts_query->set_404();
				return;
			}

			$user_followers_id        = apply_filters('wpqa_followers','followers');
			$user_following_id        = apply_filters('wpqa_following','following');
			$user_questions_id        = apply_filters('wpqa_questions','questions');
			$user_answers_id          = apply_filters('wpqa_answers','answers');
			$best_answers_id          = apply_filters('wpqa_best_answers','best_answers');
			$user_groups_id           = apply_filters('wpqa_groups','groups');
			$user_joined_groups_id    = apply_filters('wpqa_joined_groups','joined_groups');
			$user_managed_groups_id   = apply_filters('wpqa_managed_groups','managed_groups');
			$user_points_id           = apply_filters('wpqa_points','points');
			$user_polls_id            = apply_filters('wpqa_polls','polls');
			$user_wpqa_asked          = apply_filters('wpqa_asked','asked');
			$asked_questions_id       = apply_filters('wpqa_asked_questions','asked_questions');
			$my_asked_id              = apply_filters('wpqa_my_asked','my_asked');
			$paid_questions_id        = apply_filters('wpqa_paid_questions','paid_questions');
			$user_followed_id         = apply_filters('wpqa_followed','followed');
			$user_favorites_id        = apply_filters('wpqa_favorites','favorites');
			$user_posts_id            = apply_filters('wpqa_posts','posts');
			$user_comments_id         = apply_filters('wpqa_comments','comments');
			$followers_questions_id   = apply_filters('wpqa_followers_questions','followers_questions');
			$followers_answers_id     = apply_filters('wpqa_followers_answers','followers_answers');
			$followers_posts_id       = apply_filters('wpqa_followers_posts','followers_posts');
			$followers_comments_id    = apply_filters('wpqa_followers_comments','followers_comments');
			
			$is_followers           = $posts_query->get($user_followers_id);
			$is_following           = $posts_query->get($user_following_id);
			$is_questions           = $posts_query->get($user_questions_id);
			$is_answers             = $posts_query->get($user_answers_id);
			$is_best_answers        = $posts_query->get($best_answers_id);
			$is_groups              = $posts_query->get($user_groups_id);
			$is_joined_groups       = $posts_query->get($user_joined_groups_id);
			$is_managed_groups      = $posts_query->get($user_managed_groups_id);
			$is_points              = $posts_query->get($user_points_id);
			$is_polls               = $posts_query->get($user_polls_id);
			$is_asked               = $posts_query->get($user_wpqa_asked);
			$is_asked_questions     = $posts_query->get($asked_questions_id);
			$is_my_asked            = $posts_query->get($my_asked_id);
			$is_paid_questions      = $posts_query->get($paid_questions_id);
			$is_followed            = $posts_query->get($user_followed_id);
			$is_favorites           = $posts_query->get($user_favorites_id);
			$is_posts               = $posts_query->get($user_posts_id);
			$is_comments            = $posts_query->get($user_comments_id);
			$is_followers_questions = $posts_query->get($followers_questions_id);
			$is_followers_answers   = $posts_query->get($followers_answers_id);
			$is_followers_posts     = $posts_query->get($followers_posts_id);
			$is_followers_comments  = $posts_query->get($followers_comments_id);
			
			if ( ! empty( $is_followers ) ) {
				$posts_query->wpqa_is_user_followers = true;
			}else if ( ! empty( $is_following ) ) {
				$posts_query->wpqa_is_user_following = true;
			}else if ( ! empty( $is_questions ) ) {
				$posts_query->wpqa_is_user_questions = true;
			}else if ( ! empty( $is_answers ) ) {
				$posts_query->wpqa_is_user_answers = true;
			}else if ( ! empty( $is_best_answers ) ) {
				$posts_query->wpqa_is_best_answers = true;
			}else if ( ! empty( $is_groups ) ) {
				$posts_query->wpqa_is_user_groups = true;
			}else if ( ! empty( $is_joined_groups ) ) {
				$posts_query->wpqa_is_user_joined_groups = true;
			}else if ( ! empty( $is_managed_groups ) ) {
				$posts_query->wpqa_is_user_managed_groups = true;
			}else if ( ! empty( $is_points ) ) {
				$posts_query->wpqa_is_user_points = true;
			}else if ( ! empty( $is_polls ) ) {
				$posts_query->wpqa_is_user_polls = true;
			}else if ( ! empty( $is_asked ) ) {
				$posts_query->wpqa_is_user_asked = true;
			}else if ( ! empty( $is_asked_questions ) ) {
				$posts_query->wpqa_is_asked_questions = true;
			}else if ( ! empty( $is_my_asked ) ) {
				$posts_query->wpqa_is_my_asked = true;
			}else if ( ! empty( $is_paid_questions ) ) {
				$posts_query->wpqa_is_paid_questions = true;
			}else if ( ! empty( $is_followed ) ) {
				$posts_query->wpqa_is_user_followed = true;
			}else if ( ! empty( $is_favorites ) ) {
				$posts_query->wpqa_is_user_favorites = true;
			}else if ( ! empty( $is_posts ) ) {
				$posts_query->wpqa_is_user_posts = true;
			}else if ( ! empty( $is_comments ) ) {
				$posts_query->wpqa_is_user_comments = true;
			}else if ( ! empty( $is_followers_questions ) ) {
				$posts_query->wpqa_is_followers_questions = true;
			}else if ( ! empty( $is_followers_answers ) ) {
				$posts_query->wpqa_is_followers_answers = true;
			}else if ( ! empty( $is_followers_posts ) ) {
				$posts_query->wpqa_is_followers_posts = true;
			}else if ( ! empty( $is_followers_comments ) ) {
				$posts_query->wpqa_is_followers_comments = true;
			}else {
				$posts_query->wpqa_is_home_profile = true;
			}
			$posts_query->wpqa_is_user_profile = true;
			if ( $get_current_user === $the_user->ID ) {
				$posts_query->wpqa_is_user_owner = true;
			}
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$search_id]) ) {
			$search_terms = wpqa_search_terms();
			if ( !empty( $search_terms ) )
				$posts_query->wpqa_search_terms = $search_terms;
			$posts_query->wpqa_is_search = true;
			$posts_query->wpqa_specific_name = "search";
		}else if ( isset($posts_query->query_vars[$checkout_id]) ) {
			$checkout_term = wpqa_checkout_term();
			if ( !empty( $checkout_term ) )
				$posts_query->wpqa_checkout_term = $checkout_term;
			$checkout_item = wpqa_checkout_item();
			if ( !empty( $checkout_item ) )
				$posts_query->wpqa_checkout_item = $checkout_item;
			$checkout_related = wpqa_checkout_related();
			if ( !empty( $checkout_related ) )
				$posts_query->wpqa_checkout_related = $checkout_related;
			$posts_query->wpqa_is_checkout = true;
			$posts_query->wpqa_specific_name = "checkout";
		}else if ( isset($posts_query->query_vars[$subscriptions_id]) ) {
			$posts_query->wpqa_is_subscriptions = true;
			$posts_query->wpqa_specific_name = "subscriptions";
		}else if ( isset($posts_query->query_vars[$buy_points_id]) ) {
			$posts_query->wpqa_is_buy_points = true;
			$posts_query->wpqa_specific_name = "buy_points";
		}else if ( isset($posts_query->query_vars[$login_id]) ) {
			$posts_query->wpqa_is_login = true;
			$posts_query->wpqa_specific_name = "login";
		}else if ( isset($posts_query->query_vars[$signup_id]) ) {
			$posts_query->wpqa_is_signup = true;
			$posts_query->wpqa_specific_name = "signup";
		}else if ( isset($posts_query->query_vars[$lost_password_id]) ) {
			$posts_query->wpqa_is_lost_password = true;
			$posts_query->wpqa_specific_name = "lost_password";
		}else if ( isset($posts_query->query_vars[$settings_id]) ) {
			$posts_query->wpqa_is_user_settings_page = true;
			$posts_query->wpqa_is_user_profile = true;
			$posts_query->wpqa_is_user_edit_home = true;
			$posts_query->wpqa_is_home_profile = true;
			$posts_query->wpqa_is_main_edit_profile_pages = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$edit_id]) ) {
			$posts_query->wpqa_is_user_edit_profile = true;
			$posts_query->wpqa_is_user_profile = true;
			$posts_query->wpqa_is_user_edit_home = true;
			$posts_query->wpqa_is_home_profile = true;
			$posts_query->wpqa_is_main_edit_profile_pages = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$password_id]) ) {
			$posts_query->wpqa_is_user_password_profile = true;
			$posts_query->wpqa_is_user_profile = true;
			$posts_query->wpqa_is_user_edit_home = true;
			$posts_query->wpqa_is_home_profile = true;
			$posts_query->wpqa_is_main_edit_profile_pages = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$privacy_id]) ) {
			$posts_query->wpqa_is_user_privacy_profile = true;
			$posts_query->wpqa_is_user_profile = true;
			$posts_query->wpqa_is_user_edit_home = true;
			$posts_query->wpqa_is_home_profile = true;
			$posts_query->wpqa_is_main_edit_profile_pages = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$financial_id]) ) {
			$posts_query->wpqa_is_user_financial_profile = true;
			$posts_query->wpqa_is_user_profile = true;
			$posts_query->wpqa_is_user_edit_home = true;
			$posts_query->wpqa_is_home_profile = true;
			$posts_query->wpqa_is_main_edit_profile_pages = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$withdrawals_id]) ) {
			$posts_query->wpqa_is_user_withdrawals_profile = true;
			$posts_query->wpqa_is_user_profile = true;
			$posts_query->wpqa_is_user_edit_home = true;
			$posts_query->wpqa_is_home_profile = true;
			$posts_query->wpqa_is_main_edit_profile_pages = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$transactions_id]) ) {
			$posts_query->wpqa_is_user_transactions_profile = true;
			$posts_query->wpqa_is_user_profile = true;
			$posts_query->wpqa_is_main_edit_profile_pages = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$mails_id]) ) {
			$posts_query->wpqa_is_user_mails_profile = true;
			$posts_query->wpqa_is_user_profile = true;
			$posts_query->wpqa_is_user_edit_home = true;
			$posts_query->wpqa_is_home_profile = true;
			$posts_query->wpqa_is_main_edit_profile_pages = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$delete_id]) ) {
			$posts_query->wpqa_is_user_delete_profile = true;
			$posts_query->wpqa_is_user_profile = true;
			$posts_query->wpqa_is_user_edit_home = true;
			$posts_query->wpqa_is_home_profile = true;
			$posts_query->wpqa_is_main_edit_profile_pages = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$blocking_id]) ) {
			$posts_query->wpqa_is_user_blocking = true;
			$posts_query->wpqa_is_user_profile = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$pending_questions_id]) ) {
			$posts_query->wpqa_is_pending_questions = true;
			$posts_query->wpqa_is_user_profile = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$pending_posts_id]) ) {
			$posts_query->wpqa_is_pending_posts = true;
			$posts_query->wpqa_is_user_profile = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$notifications_id]) ) {
			$posts_query->wpqa_is_user_notifications = true;
			$posts_query->wpqa_is_user_profile = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$activities_id]) ) {
			$posts_query->wpqa_is_user_activities = true;
			$posts_query->wpqa_is_user_profile = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$referrals_id]) ) {
			$posts_query->wpqa_is_user_referrals = true;
			$posts_query->wpqa_is_user_profile = true;
			$add_user_values = true;
		}else if ( isset($posts_query->query_vars[$messages_id]) ) {
			$posts_query->wpqa_is_user_messages = true;
			$posts_query->wpqa_is_user_profile = true;
			$add_user_values = true;
			if (!is_user_logged_in()) {
				$posts_query->wpqa_is_user_not_logged = true;
			}
		}else if ( isset($posts_query->query_vars[$add_category_id]) ) {
			$posts_query->wpqa_is_add_category = true;
			$posts_query->wpqa_specific_name = "add_category";
		}else if ( isset($posts_query->query_vars[$add_questions_id]) ) {
			$posts_query->wpqa_is_add_questions = true;
			$posts_query->wpqa_specific_name = "add_question";
			if ($posts_query->query_vars[$add_questions_id] != '') {
				$posts_query->wpqa_is_add_user_questions = true;
			}
		}else if ( isset($posts_query->query_vars[$edit_questions_id]) ) {
			$posts_query->wpqa_is_edit_questions = true;
			$posts_query->wpqa_specific_name = "edit_question";
			if ( !empty( $the_question->ID ) ) {
				$posts_query->set('edit_question',$the_question->ID);
			}
		}else if ( isset($posts_query->query_vars[$add_groups_id]) ) {
			$posts_query->wpqa_is_add_groups = true;
			$posts_query->wpqa_specific_name = "add_group";
		}else if ( isset($posts_query->query_vars[$edit_groups_id]) ) {
			$posts_query->wpqa_is_edit_groups = true;
			$posts_query->wpqa_specific_name = "edit_group";
			if ( !empty( $the_group->ID ) ) {
				$posts_query->set('edit_group',$the_group->ID);
			}
		}else if ( isset($posts_query->query_vars[$group_requests_id]) ) {
			$posts_query->wpqa_is_group_requests = true;
			if ( !empty( $the_group->ID ) ) {
				$posts_query->set('group_request',$the_group->ID);
			}
		}else if ( isset($posts_query->query_vars[$group_users_id]) ) {
			$posts_query->wpqa_is_group_users = true;
			if ( !empty( $the_group->ID ) ) {
				$posts_query->set('group_user',$the_group->ID);
			}
		}else if ( isset($posts_query->query_vars[$group_admins_id]) ) {
			$posts_query->wpqa_is_group_admins = true;
			if ( !empty( $the_group->ID ) ) {
				$posts_query->set('group_admin',$the_group->ID);
			}
		}else if ( isset($posts_query->query_vars[$blocked_users_id]) ) {
			$posts_query->wpqa_is_blocked_users = true;
			if ( !empty( $the_group->ID ) ) {
				$posts_query->set('blocked_user',$the_group->ID);
			}
		}else if ( isset($posts_query->query_vars[$posts_group_id]) ) {
			$posts_query->wpqa_is_posts_group = true;
			if ( !empty( $the_group->ID ) ) {
				$posts_query->set('post_group',$the_group->ID);
			}
		}else if ( isset($posts_query->query_vars[$view_posts_group_id]) ) {
			$posts_query->wpqa_is_view_posts_group = true;
			if ( !empty( $the_post->ID ) ) {
				$posts_query->set('view_post_group',$the_post->ID);
			}
		}else if ( isset($posts_query->query_vars[$edit_posts_group_id]) ) {
			$posts_query->wpqa_is_edit_posts_group = true;
			if ( !empty( $the_post->ID ) ) {
				$posts_query->set('edit_post_group',$the_post->ID);
			}
		}else if ( isset($posts_query->query_vars[$add_posts_id]) ) {
			$posts_query->wpqa_is_add_posts = true;
			$posts_query->wpqa_specific_name = "add_post";
		}else if ( isset($posts_query->query_vars[$edit_posts_id]) ) {
			$posts_query->wpqa_is_edit_posts = true;
			$posts_query->wpqa_specific_name = "edit_post";
			if ( !empty( $the_post->ID ) ) {
				$posts_query->set('edit_post',$the_post->ID);
			}
		}else if ( isset($posts_query->query_vars[$edit_comments_id]) ) {
			$posts_query->wpqa_is_edit_comments = true;
			$posts_query->wpqa_specific_name = "edit_comment";
			if ( !empty( $the_comment->ID ) ) {
				$posts_query->set('edit_comment',$the_comment->ID);
			}
		}
		if (isset($add_user_values)) {
			if (!isset($the_user) || (isset($the_user) && !is_object($the_user))) {
				$the_user = get_userdata(esc_html($get_current_user));
			}
			if (isset($the_user->ID) && $the_user->ID > 0) {
				$posts_query->set('wpqa_user_id',$the_user->ID);
				$profile_type = wpqa_options('profile_type');
				$posts_query->set('author_name',trim(urldecode(esc_html($profile_type != 'login' && isset($the_user->user_nicename) && $the_user->user_nicename != ''?$the_user->user_nicename:$the_user->user_login))));
				$posts_query->set('wpqa_user',trim(urldecode(esc_html($profile_type != 'login' && isset($the_user->user_nicename) && $the_user->user_nicename != ''?$the_user->user_nicename:$the_user->user_login))));
			}
		}
		if ($get_current_user > 0 && isset($the_user->ID) && $get_current_user === $the_user->ID) {
			$posts_query->wpqa_is_user_owner = true;
		}
	}
endif;
/* Parse args */
if (!function_exists('wpqa_parse_args')) :
	function wpqa_parse_args( $args, $defaults = array(), $filter_key = '' ) {
		if ( is_object( $args ) ) {
			$r = get_object_vars( $args );
		}else if ( is_array( $args ) ) {
			$r =& $args;
		}else {
			wp_parse_str( $args, $r );
		}
		if ( !empty( $filter_key ) ) {
			$r = apply_filters( 'wpqa_before_' . $filter_key . '_parse_args', $r );
		}
		if ( is_array( $defaults ) && !empty( $defaults ) ) {
			$r = array_merge( $defaults, $r );
		}
		if ( !empty( $filter_key ) ) {
			$r = apply_filters( 'wpqa_after_' . $filter_key . '_parse_args', $r );
		}
		return $r;
	}
endif;
/* Search var */
if (!function_exists('wpqa_search')) :
	function wpqa_search($encode = '') {
		$search_value = (get_query_var(apply_filters('wpqa_search_id','search')) != ''?get_query_var(apply_filters('wpqa_search_id','search')):(get_query_var('s') != ''?get_query_var('s'):''));
		if ($search_value == '') {
			$search_value = (isset($_GET['search'])?$_GET['search']:$search_value);
		}
		if ($search_value == '') {
			$q = (isset($_GET['q'])?$_GET['q']:'');
			$search_slug = wpqa_options('search_slug','search');
			if (strpos($q,'?search=') !== false && strpos($q,$search_slug) !== false) {
				$search_value = str_replace(array('/','?search=',$search_slug),'',$q);
			}
		}
		if ($encode == 'encode') {
			$search_value = urlencode(urldecode(trim($search_value)));
		}else {
			$search_value = urldecode(trim($search_value));
		}
		$search_value = esc_js(esc_html($search_value));
		return apply_filters( 'wpqa_search', $search_value );
	}
endif;
/* Search terms */
if (!function_exists('wpqa_search_terms')) :
	function wpqa_search_terms( $passed_terms = '' ) {
		if ( !empty( $passed_terms ) ) {
			$search_terms = sanitize_title( $passed_terms );
		}else {
			$search_terms = esc_html(get_query_var(apply_filters('wpqa_search_id','search')));
		}
		$search_terms = esc_js(!empty($search_terms)?urldecode(trim($search_terms)): false);
		return apply_filters( 'wpqa_search_terms', $search_terms, $passed_terms );
	}
endif;
/* Get search link content */
if (!function_exists('wpqa_search_link')) :
	function wpqa_search_link($query = '',$type = '',$page = '') {
		$query = ($query != ''?$query:wpqa_search('encode'));
		$type  = ($type != ''?$type:wpqa_search_type());
		$o_paged = $paged = (get_query_var('paged') != ''?esc_html(get_query_var('paged')):(get_query_var('page') != ''?esc_html(get_query_var('page')):''));
		$paged = ($page != ''?$page:$paged);
		$user_filter = ($type == 'users' && isset($_GET['user_filter']) && $_GET['user_filter'] != ''?esc_html($_GET['user_filter']):'');
		if (get_option('permalink_structure')) {
			$search_slug = wpqa_options('search_slug','search');
			$paged = ($paged != ''?esc_html($paged):'');
			$url = home_url('/'.$search_slug.'/'.($query != ''?$query.'/':'').($query != '' && $type != ''?$type.'/':'').($user_filter != ''?'?user_filter='.$user_filter.($paged != '' && $o_paged > 0?'&'.$paged:''):($paged != '' && $o_paged > 0?$paged:'')));
		}else {
			$url = esc_url_raw(add_query_arg(array(apply_filters('wpqa_search_id','search') => $query,apply_filters('wpqa_type','search_type') => $type,'page' => ($paged != '' && $o_paged > 0?$paged:false),array('user_filter' => $user_filter)),home_url('/')));
		}
		return esc_url_raw(apply_filters( 'wpqa_filter_search_link', $url ));
	}
endif;
/* Search type */
if (!function_exists('wpqa_search_type')) :
	function wpqa_search_type() {
		$search_type = esc_html(get_query_var(apply_filters('wpqa_type','search_type')));
		$search_type = !empty($search_type) && $search_type != '-1'?esc_html($search_type):wpqa_options('default_search');
		return apply_filters( 'wpqa_filter_search_type', $search_type );
	}
endif;
/* Search type */
if (!function_exists('wpqa_search_type_without_default')) :
	function wpqa_search_type_without_default() {
		$search_type = esc_html(get_query_var(apply_filters('wpqa_type','search_type')));
		$search_type = !empty($search_type) && $search_type != '-1'?esc_html($search_type):'';
		return apply_filters( 'wpqa_filter_search_type', $search_type );
	}
endif;
/* Get search url */
function wpqa_get_search_permalink() {
	if (get_option('permalink_structure')) {
		$search_slug = wpqa_options('search_slug','search');
		$url = $search_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_search_id','search') => wpqa_search() ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_get_search_url', $url );
}
/* Checkout var */
if (!function_exists('wpqa_checkout')) :
	function wpqa_checkout() {
		$checkout_value = (get_query_var(apply_filters('wpqa_checkout_id','checkout')) != ''?get_query_var(apply_filters('wpqa_checkout_id','checkout')):'');
		$checkout_value = urldecode(trim($checkout_value));
		$checkout_value = esc_js(esc_html($checkout_value));
		return apply_filters( 'wpqa_checkout', $checkout_value );
	}
endif;
/* Checkout term */
if (!function_exists('wpqa_checkout_term')) :
	function wpqa_checkout_term( $passed_term = '' ) {
		if ( !empty( $passed_term ) ) {
			$checkout_term = sanitize_title( $passed_term );
		}else {
			$checkout_term = esc_html(get_query_var(apply_filters('wpqa_checkout_id','checkout')));
		}
		$checkout_term = esc_js(!empty( $checkout_term ) ? urldecode( trim( $checkout_term ) ) : false);
		return apply_filters( 'wpqa_checkout_term', $checkout_term, $passed_term );
	}
endif;
/* Checkout item */
if (!function_exists('wpqa_checkout_item')) :
	function wpqa_checkout_item( $passed_item = '' ) {
		if ( !empty( $passed_item ) ) {
			$checkout_item = sanitize_title( $passed_item );
		}else {
			$checkout_item = esc_html(get_query_var(apply_filters('wpqa_checkout_id','checkout')));
		}
		$checkout_item = esc_js(!empty( $checkout_item ) ? urldecode( trim( $checkout_item ) ) : false);
		return apply_filters( 'wpqa_checkout_item', $checkout_item, $passed_item );
	}
endif;
/* Checkout related */
if (!function_exists('wpqa_checkout_related')) :
	function wpqa_checkout_related( $passed_item = '' ) {
		if ( !empty( $passed_item ) ) {
			$checkout_related = sanitize_title( $passed_item );
		}else {
			$checkout_related = esc_html(get_query_var(apply_filters('wpqa_checkout_related','checkout_related')));
		}
		$checkout_related = esc_js(!empty( $checkout_related ) ? urldecode( trim( $checkout_related ) ) : false);
		return apply_filters( 'wpqa_checkout_related', $checkout_related, $passed_item );
	}
endif;
/* Get checkout link content */
if (!function_exists('wpqa_checkout_link')) :
	function wpqa_checkout_link($query = '',$type = '',$related = '') {
		$type = ($type != ''?$type:wpqa_checkout_get_item());
		$related = ($related != ''?$related:wpqa_checkout_get_related());
		if (get_option('permalink_structure')) {
			$checkout_slug = (class_exists('woocommerce')?'wpqa-':'').wpqa_options('checkout_slug','checkout');
			$url = home_url('/'.$checkout_slug.'/'.($query != ''?$query.'/':'').($query != '' && $type != ''?$type.'/':'').($query != '' && $related != ''?$related.'/':''));
		}else {
			$url = esc_url_raw(add_query_arg(array(apply_filters('wpqa_checkout_id','checkout') => $query,apply_filters('wpqa_checkout_item','checkout_item') => $type,apply_filters('wpqa_checkout_related','checkout_related') => $related),home_url('/')));
		}
		return esc_url_raw(apply_filters( 'wpqa_filter_checkout_link', $url ));
	}
endif;
/* Checkout get item */
if (!function_exists('wpqa_checkout_get_item')) :
	function wpqa_checkout_get_item() {
		$checkout_item = esc_html(get_query_var(apply_filters('wpqa_checkout_item','checkout_item')));
		$checkout_item = !empty($checkout_item)?esc_html($checkout_item):'';
		return apply_filters( 'wpqa_filter_checkout_item', $checkout_item );
	}
endif;
/* Checkout get related */
if (!function_exists('wpqa_checkout_get_related')) :
	function wpqa_checkout_get_related() {
		$checkout_related = esc_html(get_query_var(apply_filters('wpqa_checkout_related','checkout_related')));
		$checkout_related = !empty($checkout_related)?esc_html($checkout_related):'';
		return apply_filters( 'wpqa_filter_checkout_related', $checkout_related );
	}
endif;
/* Get checkout url */
function wpqa_get_checkout_permalink() {
	if (get_option('permalink_structure')) {
		$checkout_slug = (class_exists('woocommerce')?'wpqa-':'').wpqa_options('checkout_slug','checkout');
		$url = $checkout_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_checkout_id','checkout') => wpqa_checkout() ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_get_checkout_url', $url );
}
/* Get subscriptions url */
function wpqa_subscriptions_permalink() {
	if (get_option('permalink_structure')) {
		$subscriptions_slug = wpqa_options('subscriptions_slug','subscriptions');
		$url = $subscriptions_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_subscriptions','wpqa_subscriptions') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_subscriptions_url', $url );
}
/* Get buy points url */
function wpqa_buy_points_permalink() {
	if (get_option('permalink_structure')) {
		$buy_points_slug = wpqa_options('buy_points_slug','buy-points');
		$url = $buy_points_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_buy_points','wpqa_buy_points') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_buy_points_url', $url );
}
/* Get login url */
function wpqa_login_permalink() {
	if (get_option('permalink_structure')) {
		$login_slug = wpqa_options('login_slug','log-in');
		$url = $login_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_login','wpqa_login') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_login_url', $url );
}
/* Get signup url */
function wpqa_signup_permalink() {
	if (get_option('permalink_structure')) {
		$signup_slug = wpqa_options('signup_slug','sign-up');
		$url = $signup_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_signup','wpqa_signup') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_signup_url', $url );
}
/* Get lost password url */
function wpqa_lost_password_permalink() {
	if (get_option('permalink_structure')) {
		$lost_password_slug = wpqa_options('lost_password_slug','lost-password');
		$url = $lost_password_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_lost_password','wpqa_lost_password') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_lost_password_url', $url );
}
/* Get settings page url */
function wpqa_settings_page_permalink() {
	if (get_option('permalink_structure')) {
		$settings_slug = wpqa_options('settings_slug','settings');
		$url = $settings_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_settings_id','settings') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_edit_profile_url', $url );
}
/* Get edit profile url */
function wpqa_edit_profile_permalink() {
	if (get_option('permalink_structure')) {
		$edit_slug = wpqa_options('edit_slug','edit');
		$settings_slug = wpqa_options('settings_slug','settings');
		$settings_slug_rule = ($settings_slug != ''?$settings_slug.'/':'');
		$url = $settings_slug_rule . $edit_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_edit_id','edit') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_edit_profile_url', $url );
}
/* Get edit profile password url */
function wpqa_edit_password_permalink() {
	if (get_option('permalink_structure')) {
		$password_slug = wpqa_options('password_slug','change-password');
		$settings_slug = wpqa_options('settings_slug','settings');
		$settings_slug_rule = ($settings_slug != ''?$settings_slug.'/':'');
		$url = $settings_slug_rule . $password_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_password_id','password') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_edit_password_url', $url );
}
/* Get edit profile privacy url */
function wpqa_edit_privacy_permalink() {
	if (get_option('permalink_structure')) {
		$privacy_slug = wpqa_options('privacy_slug','privacy');
		$settings_slug = wpqa_options('settings_slug','settings');
		$settings_slug_rule = ($settings_slug != ''?$settings_slug.'/':'');
		$url = $settings_slug_rule . $privacy_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_privacy_id','privacy') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_edit_privacy_url', $url );
}
/* Get edit profile financial url */
function wpqa_financial_permalink() {
	if (get_option('permalink_structure')) {
		$financial_slug = wpqa_options('financial_slug','financial');
		$settings_slug = wpqa_options('settings_slug','settings');
		$settings_slug_rule = ($settings_slug != ''?$settings_slug.'/':'');
		$url = $settings_slug_rule . $financial_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_financial_id','financial') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_financial_url', $url );
}
/* Get edit profile withdrawals url */
function wpqa_withdrawals_permalink() {
	if (get_option('permalink_structure')) {
		$withdrawals_slug = wpqa_options('withdrawals_slug','withdrawals');
		$settings_slug = wpqa_options('settings_slug','settings');
		$settings_slug_rule = ($settings_slug != ''?$settings_slug.'/':'');
		$url = $settings_slug_rule . $withdrawals_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_withdrawals_id','withdrawals') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_withdrawals_url', $url );
}
/* Get transactions url */
function wpqa_transactions_permalink() {
	if (get_option('permalink_structure')) {
		$transactions_slug = wpqa_options('transactions_slug','transactions');
		$url = $transactions_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_transactions_id','transactions') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_transactions_url', $url );
}
/* Get edit profile mails url */
function wpqa_edit_mails_permalink() {
	if (get_option('permalink_structure')) {
		$mails_slug = wpqa_options('mails_slug','mails');
		$settings_slug = wpqa_options('settings_slug','settings');
		$settings_slug_rule = ($settings_slug != ''?$settings_slug.'/':'');
		$url = $settings_slug_rule . $mails_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_mails_id','mails') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_edit_mails_url', $url );
}
/* Get edit profile delete url */
function wpqa_edit_delete_permalink() {
	if (get_option('permalink_structure')) {
		$delete_slug = wpqa_options('delete_slug','delete');
		$settings_slug = wpqa_options('settings_slug','settings');
		$settings_slug_rule = ($settings_slug != ''?$settings_slug.'/':'');
		$url = $settings_slug_rule . $delete_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_delete_id','delete') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_edit_delete_url', $url );
}
/* Get blocking url */
function wpqa_blocking_permalink() {
	if (get_option('permalink_structure')) {
		$blocking_slug = wpqa_options('blocking_slug','blocking');
		$url = $blocking_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_blocking_id','blocking') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_blocking_url', $url );
}
/* Get pending_questions url */
function wpqa_pending_questions_permalink() {
	if (get_option('permalink_structure')) {
		$pending_questions_slug = wpqa_options('pending_questions_slug','pending-questions');
		$url = $pending_questions_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_pending_questions_id','pending_questions') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_pending_questions_url', $url );
}
/* Get pending_posts url */
function wpqa_pending_posts_permalink() {
	if (get_option('permalink_structure')) {
		$pending_posts_slug = wpqa_options('pending_posts_slug','pending-posts');
		$url = $pending_posts_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_pending_posts_id','pending_posts') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_pending_posts_url', $url );
}
/* Get notifications url */
function wpqa_notifications_permalink() {
	if (get_option('permalink_structure')) {
		$notifications_slug = wpqa_options('notifications_slug','notifications');
		$url = $notifications_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_notifications_id','notifications') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_notifications_url', $url );
}
/* Get activities url */
function wpqa_activities_permalink() {
	if (get_option('permalink_structure')) {
		$activities_slug = wpqa_options('activities_slug','activities');
		$url = $activities_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_activities_id','activities') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_activities_url', $url );
}
/* Get referrals url */
function wpqa_referrals_permalink() {
	if (get_option('permalink_structure')) {
		$referrals_slug = wpqa_options('referrals_slug','referrals');
		$url = $referrals_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_referrals_id','referrals') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_referrals_url', $url );
}
/* Get messages url */
function wpqa_messages_permalink() {
	if (get_option('permalink_structure')) {
		$messages_slug = wpqa_options('messages_slug','messages');
		$url = $messages_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_messages_id','messages') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_messages_url', $url );
}
/* Get add post url */
function wpqa_add_post_permalink() {
	if (get_option('permalink_structure')) {
		$add_posts_slug = wpqa_options('add_posts_slug','add-post');
		$url = $add_posts_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_add_posts','wpqa_add_post') => ''), home_url( '/' ) ));
	}

	if (is_category()) {
		$term_id = (int)get_query_var('wpqa_term_id');
		$url = esc_url_raw(add_query_arg('category',$term_id,$url));
	}
	
	return apply_filters( 'wpqa_add_post_url', $url );
}
/* Get add category url */
function wpqa_add_category_permalink() {
	if (get_option('permalink_structure')) {
		$add_category_slug = wpqa_options('add_category_slug','add-category');
		$url = $add_category_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array( apply_filters('wpqa_add_categories','add_category') => '' ), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_add_category_url', $url );
}
/* Get ask question url */
function wpqa_add_question_permalink($user = '',$user_id = '') {
	$ask_question_to_users = wpqa_options('ask_question_to_users');
	$get_query_var         = '';
	if ($ask_question_to_users == 'on' && ((wpqa_is_user_profile() && $user == 'user') || ($user == 'user' && $user_id != ''))) {
		$wpqa_user_id = (int)($user_id != ''?$user_id:get_query_var(apply_filters('wpqa_user_id','wpqa_user_id')));
		$user = get_userdata($wpqa_user_id);
		$nicename = (isset($user->user_nicename) && $user->user_nicename != ''?$user->user_nicename:(isset($user->user_login) && $user->user_login != ''?$user->user_login:''));
		$login = $user->user_login;
		$profile_type = wpqa_options('profile_type');
		if ($profile_type == 'login') {
			$user_name = esc_html(urlencode(trim($login)));
		}else {
			$user_name = esc_html(urlencode(trim($nicename)));
		}
		$get_query_var = ($user_name != ''?user_trailingslashit(esc_html($user_name)):'');
	}
	
	if (get_option('permalink_structure')) {
		$add_questions_slug    = wpqa_options('add_questions_slug','add-question');
		$url = $add_questions_slug;
		$url = rtrim(home_url(user_trailingslashit($url).$get_query_var),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg(array(apply_filters('wpqa_add_questions','add_question') => (isset($get_query_var) && $get_query_var != ''?$get_query_var:'')),home_url('/')));
	}

	if (is_tax(wpqa_question_categories)) {
		$term_id = (int)get_query_var('wpqa_term_id');
		$url = esc_url_raw(add_query_arg('category',$term_id,$url));
	}

	return apply_filters( 'wpqa_add_question_url', $url, $user ,$user_id );
}
/* Get user at ask question page */
add_filter('wpqa_add_question_user','wpqa_add_question_user');
if (!function_exists('wpqa_add_question_user')) :
	function wpqa_add_question_user() {
		$ask_question_to_users = wpqa_options('ask_question_to_users');
		if ($ask_question_to_users == 'on') {
			if (wpqa_is_user_profile()) {
				$get_user_name = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
			}else {
				$get_user_name = get_query_var(apply_filters('wpqa_add_questions','add_question'));
			}
			$user_login = get_user_by('login',urldecode($get_user_name));
			if (isset($user_login) && !is_object($user_login)) {
				$user_login = get_user_by('slug',urldecode($get_user_name));
			}
			if (isset($user_login) && !is_object($user_login)) {
				$user_login = get_user_by('id',(int)$get_user_name);
			}
			if (isset($user_login) && is_object($user_login)) {
				$get_user_id = $user_login->ID;
				return (int)$get_user_id;
			}
		}
	}
endif;
/* Get add group url */
function wpqa_add_group_permalink() {
	if (get_option('permalink_structure')) {
		$add_groups_slug = wpqa_options('add_groups_slug','add-group');
		$url = $add_groups_slug;
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg(array(apply_filters('wpqa_add_groups','add_group') => ''),home_url('/')));
	}
	return apply_filters( 'wpqa_add_group_url', $url );
}
/* Get edit url question - group - post - comment */
function wpqa_edit_permalink( $type_id = 0,$type = 'question' ) {
	$early_url = apply_filters( 'wpqa_pre_get_'.$type.'_permalink', (int) $type_id );
	if ( is_string( $early_url ) )
		return $early_url;
	if (get_option('permalink_structure')) {
		$edits_slug = wpqa_options('edit_'.$type.'s_slug');
		$url = $edits_slug . '/%' . apply_filters('wpqa_edit_'.$type.'s','edit_'.$type) . '%';
		$url = str_replace( '%' . apply_filters('wpqa_edit_'.$type.'s','edit_'.$type) . '%', $type_id, $url );
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array(
			apply_filters('wpqa_edit_'.$type.'s','edit_'.$type) => $type_id,
		), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_get_'.$type.'_permalink', $url, $type_id );
}
/* Get custom url group */
function wpqa_custom_permalink( $type_id,$types,$type ) {
	$early_url = apply_filters( 'wpqa_pre_get_'.$type.'_permalink', (int) $type_id );
	if ( is_string( $early_url ) )
		return $early_url;
	if (get_option('permalink_structure')) {
		$custom_links_slug = wpqa_options($types.'_slug');
		$url = $custom_links_slug . '/%' . apply_filters('wpqa_'.$types,$type) . '%';
		$url = str_replace( '%' . apply_filters('wpqa_'.$types,$type) . '%', $type_id, $url );
		$url = rtrim(home_url(user_trailingslashit($url)),'/').'/';
	}else {
		$url = esc_url_raw(add_query_arg( array(
			apply_filters('wpqa_'.$types,$type) => $type_id,
		), home_url( '/' ) ));
	}
	return apply_filters( 'wpqa_get_'.$type.'_permalink', $url, $type_id );
}
/* Get logout url */
function wpqa_get_logout() {
	$after_logout = wpqa_options('after_logout');
	$after_logout_link = wpqa_options('after_logout_link');
	$protocol = is_ssl() ? 'https' : 'http';
	if ($after_logout == 'same_page') {
		$redirect_to = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}else if ($after_logout == 'custom_link' && $after_logout_link != '') {
		$redirect_to = esc_url_raw($after_logout_link);
	}else {
		$redirect_to = esc_url_raw(home_url('/'));
	}
	$redirect_to = remove_query_arg('mobile_api_token',$redirect_to);
	$url = esc_url_raw(wp_logout_url($redirect_to));
	return apply_filters('wpqa_filter_get_logout',$url);
}
/* Get profile content url */
function wpqa_get_profile_permalink($user_id = 0,$type = 'questions') {
	$user_id = (int)$user_id;
	$user_id = wpqa_get_user_id($user_id);
	if (empty($user_id)) {
		return false;
	}
	$early_profile_url = apply_filters('wpqa_pre_get_'.$type.'_permalink',$user_id);
	if (is_string($early_profile_url)) {
		return $early_profile_url;
	}else {
		if (get_option('permalink_structure')) {
			$type_slug = explode('_',$type);
			$type_slug = $type_slug[0].(isset($type_slug[1])?'-'.$type_slug[1]:'');
			$type_slug = wpqa_options($type.'_slug');
			$url = rtrim(user_trailingslashit(wpqa_profile_url($user_id).$type_slug),'/').'/';
		}else {
			$url = add_query_arg( array(
				apply_filters('wpqa_'.$type,$type) => '1',
			), wpqa_profile_url($user_id) );
		}
	}
	return apply_filters('wpqa_get_'.$type.'_permalink',esc_url_raw($url),$user_id);
}
/* Filter profile url */
add_filter('author_link','wpqa_author_link',10,3);
if (!function_exists('wpqa_author_link')) :
	function wpqa_author_link($link,$user_id,$user_nicename) {
		$link = wpqa_profile_url($user_id,$user_nicename);
		return $link;
	}
endif;
/* Get user profile url */
if (!function_exists('wpqa_profile_url')) :
	function wpqa_profile_url($user_id = 0,$user_nicename = '') {
		$user_id = wpqa_get_user_id($user_id);
		if (empty($user_id))
			return false;
		$user = get_userdata($user_id);
		if (empty($user->ID))
			return false;
		$early_profile_url = apply_filters('wpqa_pre_get_user_profile_url',(int)$user_id);
		if (is_string($early_profile_url))
			return $early_profile_url;
		if (get_option('permalink_structure')) {
			$user_slug = wpqa_options('profile_slug','profile');
			$remove_profile_slug = wpqa_options('remove_profile_slug');
			$profile_slug_numbers = wpqa_options('profile_slug_numbers');
			$user_slug = ($remove_profile_slug == 'on'?'':$user_slug);
			$link = $user_slug.'/%'.apply_filters('wpqa_user_id','wpqa_user_id').'%';
			if ($profile_slug_numbers == 'on') {
				$user_link = $user_id;
			}else {
				$profile_type = wpqa_options('profile_type');
				$nicename = (isset($user->user_nicename) && $user->user_nicename != ''?$user->user_nicename:(isset($user->user_login) && $user->user_login != ''?$user->user_login:''));
				$login = $user->user_login;
				if ($nicename != "-" && (empty($user_nicename) || $profile_type == 'nicename')) {
					$user_nicename = esc_html(urldecode(urlencode(trim($nicename))));
				}else {
					$user_nicename = esc_html(urldecode(urlencode(trim($login))));
				}
				$user_nicename = str_ireplace('+','-',$user_nicename);
				$user_nicename = str_ireplace(' ','-',$user_nicename);
				$user_link = $user_nicename;
			}
			$link = str_replace( '%' . apply_filters('wpqa_user_id','wpqa_user_id') . '%', $user_link, $link );
			$link = rtrim(home_url(user_trailingslashit($link)),'/').'/';
		}else {
			$link = esc_url_raw(add_query_arg( array( apply_filters('wpqa_user_id','wpqa_user_id') => $user_id ), home_url( '/' ) ));
		}
		$link = apply_filters( 'wpqa_profile_url', $link, $user_id, $user_nicename );
		return esc_url_raw($link);
	}
endif;
/* Body classes */
add_filter('body_class','wpqa_body_classes');
function wpqa_body_classes($classes) {
	if (wpqa_is_search()) {
		$classes[] = 'wpqa-search';
		$classes[] = 'wpqa-search-'.wpqa_search_type();
	}else if (wpqa_is_checkout()) {
		$classes[] = 'wpqa-checkout';
		$classes[] = 'wpqa-checkout-'.wpqa_checkout_get_item();
	}else if (wpqa_is_subscriptions()) {
		$classes[] = 'wpqa-subscriptions';
	}else if (wpqa_is_buy_points()) {
		$classes[] = 'wpqa-buy-points';
	}else if (wpqa_is_login()) {
		$classes[] = 'wpqa-login';
	}else if (wpqa_is_signup()) {
		$classes[] = 'wpqa-signup';
	}else if (wpqa_is_lost_password()) {
		$classes[] = 'wpqa-lost-password';
	}else if (wpqa_is_add_category()) {
		$classes[] = 'wpqa-add-category';
	}else if (wpqa_is_add_questions()) {
		$classes[] = 'wpqa-add-question';
	}else if (wpqa_is_edit_questions()) {
		$classes[] = 'wpqa-edit-question';
	}else if (wpqa_is_edit_tags()) {
		$classes[] = 'wpqa-edit-tag';
	}else if (wpqa_is_add_groups()) {
		$classes[] = 'wpqa-add-group';
	}else if (wpqa_is_edit_groups()) {
		$classes[] = 'wpqa-edit-group';
	}else if (wpqa_is_group_requests()) {
		$classes[] = 'wpqa-group-requests';
	}else if (wpqa_is_group_users()) {
		$classes[] = 'wpqa-group-users';
	}else if (wpqa_is_group_admins()) {
		$classes[] = 'wpqa-group-admins';
	}else if (wpqa_is_blocked_users()) {
		$classes[] = 'wpqa-blocked-users';
	}else if (wpqa_is_posts_group()) {
		$classes[] = 'wpqa-pending-post';
	}else if (wpqa_is_view_posts_group()) {
		$classes[] = 'wpqa-view-post-group';
	}else if (wpqa_is_edit_posts_group()) {
		$classes[] = 'wpqa-edit-post-group';
	}else if (wpqa_is_add_posts()) {
		$classes[] = 'wpqa-add-post';
	}else if (wpqa_is_edit_posts()) {
		$classes[] = 'wpqa-edit-post';
	}else if (wpqa_is_edit_comments()) {
		$classes[] = 'wpqa-edit-comment';
	}else if (wpqa_is_user_profile()) {
		$cover_image = wpqa_options('cover_image');
		$classes[] = (wpqa_user_title() != ''?'wpqa-profile wpqa-'.wpqa_user_title():'wpqa-profile '.($cover_image == 'on'?'wpqa-body-cover':'wpqa-body-not-cover'));
	}
	return $classes;
}
/* Load the title */
add_filter('document_title_parts','wpqa_the_title',9);
function wpqa_the_title($title) {
	$wpqa_get_the_title = wpqa_get_the_title();
	if ($wpqa_get_the_title != '') {
		$title['title'] = $wpqa_get_the_title;
	}
	if (wpqa_is_user_profile()) {
		$wpqa_user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
		$wpseo_author_title = get_user_meta($wpqa_user_id,'wpseo_title',true);
		if ($wpseo_author_title != '') {
			$title['title'] = $wpseo_author_title;
		}
	}
	return $title;
}
/* Get profile title */
if (!function_exists('wpqa_profile_title')) :
	function wpqa_profile_title($get_the_title = '') {
		$title = '';
		if (wpqa_is_user_followers()) {
			$title = esc_html__('Followers','wpqa');
		}else if (wpqa_is_user_following()) {
			$title = esc_html__('Following','wpqa');
		}else if (wpqa_is_user_questions()) {
			$title = esc_html__('Questions','wpqa');
		}else if (wpqa_is_user_answers()) {
			$title = esc_html__('Answers','wpqa');
		}else if (wpqa_is_best_answers()) {
			$title = esc_html__('Best Answers','wpqa');
		}else if (wpqa_is_user_groups()) {
			$title = esc_html__('Groups','wpqa');
		}else if (wpqa_is_user_joined_groups()) {
			$title = esc_html__('Joined Groups','wpqa');
		}else if (wpqa_is_user_managed_groups()) {
			$title = esc_html__('Managed Groups','wpqa');
		}else if (wpqa_is_user_points()) {
			$title = esc_html__('Points','wpqa');
		}else if (wpqa_is_user_polls()) {
			$title = esc_html__('Polls','wpqa');
		}else if (wpqa_is_user_asked()) {
			$title = esc_html__('Asked Questions','wpqa');
		}else if (wpqa_is_asked_questions()) {
			$title = esc_html__('Waiting Questions','wpqa');
		}else if (wpqa_is_my_asked()) {
			$title = esc_html__('My Asked Questions','wpqa');
		}else if (wpqa_is_paid_questions()) {
			$title = esc_html__('Paid Questions','wpqa');
		}else if (wpqa_is_user_followed()) {
			$title = esc_html__('Followed','wpqa');
		}else if (wpqa_is_user_favorites()) {
			$title = esc_html__('Favorites','wpqa');
		}else if (wpqa_is_user_posts()) {
			$title = esc_html__('Posts','wpqa');
		}else if (wpqa_is_user_comments()) {
			$title = esc_html__('Comments','wpqa');
		}else if (wpqa_is_followers_questions()) {
			$title = esc_html__('Followers Questions','wpqa');
		}else if (wpqa_is_followers_answers()) {
			$title = esc_html__('Followers Answers','wpqa');
		}else if (wpqa_is_followers_posts()) {
			$title = esc_html__('Followers Posts','wpqa');
		}else if (wpqa_is_followers_comments()) {
			$title = esc_html__('Followers Comments','wpqa');
		}else if ($get_the_title == '') {
			$wpqa_get_the_title = wpqa_get_the_title();
			if ($wpqa_get_the_title != '') {
				$title = $wpqa_get_the_title;
			}
		}
		return apply_filters('wpqa_filter_profile_title',$title);
	}
endif;
/* Get the title */
add_filter('rank_math/frontend/title','wpqa_get_the_title');
function wpqa_get_the_title($title = '') {
	if (is_search() || wpqa_is_search()) {
		$search_value = wpqa_search();
		if ($search_value != '') {
			$out_data = esc_html__('Search results for ', 'wpqa') . '"' . $search_value . '"';
		}else {
			$out_data = esc_html__('Search', 'wpqa');
		}
		$title = $out_data;
	}else if (wpqa_is_checkout()) {
		$title = esc_html__('Checkout', 'wpqa');
	}else if (wpqa_is_subscriptions()) {
		$title = esc_html__('Subscriptions','wpqa');
	}else if (wpqa_is_buy_points()) {
		$title = esc_html__('Buy points','wpqa');
	}else if (wpqa_is_login()) {
		$title = esc_html__('Login','wpqa');
	}else if (wpqa_is_signup()) {
		$title = esc_html__('Signup','wpqa');
	}else if (wpqa_is_lost_password()) {
		$title = esc_html__('Lost password','wpqa');
	}else if (wpqa_is_add_category()) {
		$title = esc_html__('Add category','wpqa');
	}else if (wpqa_is_add_questions()) {
		$title = esc_html__('Ask question','wpqa');
	}else if (wpqa_is_edit_questions()) {
		$title = esc_html__('Edit question','wpqa');
	}else if (wpqa_is_edit_tags()) {
		$title = esc_html__('Edit tags','wpqa');
	}else if (wpqa_is_add_groups()) {
		$title = esc_html__('Add group','wpqa');
	}else if (wpqa_is_edit_groups()) {
		$title = esc_html__('Edit group','wpqa');
	}else if (wpqa_is_group_requests()) {
		$title = esc_html__('Group requests','wpqa');
	}else if (wpqa_is_group_users()) {
		$title = esc_html__('Group users','wpqa');
	}else if (wpqa_is_group_admins()) {
		$title = esc_html__('Group admins','wpqa');
	}else if (wpqa_is_blocked_users()) {
		$title = esc_html__('Blocked users','wpqa');
	}else if (wpqa_is_posts_group()) {
		$title = esc_html__('Group posts','wpqa');
	}else if (wpqa_is_view_posts_group()) {
		$title = esc_html__('View group post','wpqa');
	}else if (wpqa_is_edit_posts_group()) {
		$title = esc_html__('Edit group post','wpqa');
	}else if (wpqa_is_add_posts()) {
		$title = esc_html__('Add post','wpqa');
	}else if (wpqa_is_edit_posts()) {
		$title = esc_html__('Edit post','wpqa');
	}else if (wpqa_is_edit_comments()) {
		$title = esc_html__('Edit comment','wpqa');
	}else if (wpqa_is_user_edit_profile()) {
		$title = esc_html__('Edit profile','wpqa');
	}else if (wpqa_is_user_password_profile()) {
		$title = esc_html__('Change Password','wpqa');
	}else if (wpqa_is_user_privacy_profile()) {
		$title = esc_html__('Privacy','wpqa');
	}else if (wpqa_is_user_withdrawals_profile()) {
		$title = esc_html__('Withdrawals','wpqa');
	}else if (wpqa_is_user_financial_profile()) {
		$title = esc_html__('Financial','wpqa');
	}else if (wpqa_is_user_transactions_profile()) {
		$title = esc_html__('Transactions','wpqa');
	}else if (wpqa_is_user_mails_profile()) {
		$title = esc_html__('Mail settings','wpqa');
	}else if (wpqa_is_user_delete_profile()) {
		$title = esc_html__('Delete account','wpqa');
	}else if (wpqa_is_user_blocking()) {
		$title = esc_html__('Blocking users','wpqa');
	}else if (wpqa_is_pending_questions()) {
		$title = esc_html__('Pending Questions','wpqa');
	}else if (wpqa_is_pending_posts()) {
		$title = esc_html__('Pending Posts','wpqa');
	}else if (wpqa_is_user_notifications()) {
		$title = esc_html__('Notifications','wpqa');
	}else if (wpqa_is_user_activities()) {
		$title = esc_html__('Activities','wpqa');
	}else if (wpqa_is_user_referrals()) {
		$title = esc_html__('Referrals','wpqa');
	}else if (wpqa_is_user_messages()) {
		$title = esc_html__('Messages','wpqa');
	}else if (wpqa_is_user_profile()) {
		$wpqa_user_id = (int)get_query_var(apply_filters('wpqa_user_id','wpqa_user_id'));
		$display_name = get_the_author_meta('display_name',$wpqa_user_id);
		$wpqa_profile_title = wpqa_profile_title("get_the_title");
		if ($wpqa_profile_title != "") {
			$title = $display_name.' - '.$wpqa_profile_title;
		}else {
			$title = $display_name;
		}
	}
	return $title;
}
/* Profile links */
function wpqa_profile_links($user_id,$tab_item) {
	if ($tab_item == 'profile') {
		$last_url = wpqa_profile_url($user_id);
	}else if ($tab_item == 'all-questions') {
		$last_url = esc_url_raw(get_post_type_archive_link(wpqa_questions_type));
	}else if ($tab_item == 'poll') {
		$last_url = esc_url_raw(add_query_arg(array('type' => 'poll'),get_post_type_archive_link(wpqa_questions_type)));
	}else if ($tab_item == 'all-groups') {
		$last_url = esc_url_raw(get_post_type_archive_link('group'));
	}else if ($tab_item == 'login') {
		$last_url = wpqa_login_permalink();
	}else if ($tab_item == 'login-popup') {
		$last_url = wpqa_login_permalink();
		$tab_class = 'login-panel';
	}else if ($tab_item == 'signup') {
		$last_url = wpqa_signup_permalink();
	}else if ($tab_item == 'signup-popup') {
		$last_url = wpqa_signup_permalink();
		$tab_class = 'signup-panel';
	}else if ($tab_item == 'lost-password') {
		$last_url = wpqa_lost_password_permalink();
	}else if ($tab_item == 'lost-password-popup') {
		$last_url = wpqa_lost_password_permalink();
		$tab_class = 'lost-password';
	}else if ($tab_item == 'edit-profile') {
		$last_url = wpqa_edit_profile_permalink();
	}else if ($tab_item == 'password') {
		$last_url = wpqa_edit_password_permalink();
	}else if ($tab_item == 'privacy') {
		$last_url = wpqa_edit_privacy_permalink();
	}else if ($tab_item == 'financial') {
		$last_url = wpqa_financial_permalink();
	}else if ($tab_item == 'withdrawals') {
		$last_url = wpqa_withdrawals_permalink();
	}else if ($tab_item == 'transactions') {
		$last_url = wpqa_transactions_permalink();
	}else if ($tab_item == 'mail-settings') {
		$last_url = esc_url(wpqa_edit_mails_permalink());
	}else if ($tab_item == 'delete-account') {
		$last_url = esc_url(wpqa_edit_delete_permalink());
	}else if ($tab_item == 'blocking') {
		$last_url = esc_url(wpqa_blocking_permalink());
	}else if ($tab_item == 'pending-questions') {
		$last_url = esc_url(wpqa_pending_questions_permalink());
	}else if ($tab_item == 'pending-posts') {
		$last_url = esc_url(wpqa_pending_posts_permalink());
	}else if ($tab_item == 'notifications') {
		$last_url = esc_url(wpqa_notifications_permalink());
	}else if ($tab_item == 'activities') {
		$last_url = esc_url(wpqa_activities_permalink());
	}else if ($tab_item == 'referrals') {
		$last_url = esc_url(wpqa_referrals_permalink());
	}else if ($tab_item == 'messages') {
		$last_url = esc_url(wpqa_messages_permalink());
	}else if ($tab_item == 'add-category') {
		$last_url = wpqa_add_category_permalink();
	}else if ($tab_item == 'add-question') {
		$last_url = wpqa_add_question_permalink();
	}else if ($tab_item == 'add-question-popup') {
		$last_url = wpqa_add_question_permalink();
		$tab_class = 'wpqa-question';
	}else if ($tab_item == 'add-poll') {
		$last_url = esc_url_raw(add_query_arg(array('type' => 'poll'),wpqa_add_question_permalink()));
	}else if ($tab_item == 'add-group') {
		$last_url = wpqa_add_group_permalink();
	}else if ($tab_item == 'add-post') {
		$last_url = wpqa_add_post_permalink();
	}else if ($tab_item == 'add-post-popup') {
		$last_url = wpqa_add_post_permalink();
		$tab_class = 'wpqa-post';
	}else if ($tab_item == 'subscriptions') {
		$last_url = wpqa_subscriptions_permalink();
	}else if ($tab_item == 'buy-points') {
		$last_url = wpqa_buy_points_permalink();
	}else if ($tab_item == 'logout') {
		$last_url = wpqa_get_logout();
	}else {
		$tab_item = str_ireplace('-','_',$tab_item);
		$last_url = wpqa_get_profile_permalink($user_id,$tab_item);
	}
	return $last_url;
}
/* Edit profile layout */
add_filter(wpqa_prefix_theme.'_sidebars_where','wpqa_sidebars_dir_profile');
add_filter(wpqa_prefix_theme.'_sidebars_dir','wpqa_sidebars_dir_profile');
function wpqa_sidebars_dir_profile($sidebar_dir) {
	if (wpqa_is_user_edit_home()) {
		$sidebar_dir = 'main_full main_center col-boot-lg-8';
	}
	return $sidebar_dir;
}?>