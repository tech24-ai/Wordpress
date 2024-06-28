<?php
/**
 * Plugin Name: WPQA - The WordPress Questions And Answers Plugin
 * Plugin URI: https://2code.info/wpqa/
 * Description: Question and answer plugin with point and badges system.
 * Version: 6.1.1
 * Author: 2code
 * Author URI: https://2code.info/
 * License: GPL2
 *
 * Text Domain: wpqa
 * Domain Path: /languages/
 */


// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

final class WPQA {

	public $version = "6.1.1";

	protected $plugin_url;

	protected $site_url;

	protected $wpqa_main_path;

	private $wpqa_questions_type;

	private $wpqa_asked_questions_type;

	private $wpqa_question_categories;

	private $wpqa_question_tags;

	private $wpqa_knowledgebase_type;

	private $wpqa_knowledgebase_categories;

	private $wpqa_knowledgebase_tags;

	/* Name capital */
	public function super_plugin_name() {
		return strtoupper($this->plugin_name);
	}

	/* Plugin URL */
	public function plugin_url() {
		return $this->plugin_url;
	}

	/* URL */
	public function site_url() {
		return $this->site_url;
	}

	/* The php main path */
	public function wpqa_main_path() {
		return $this->wpqa_main_path;
	}
	
	/* Define the core functionality of the plugin */
	public function __construct() {
		$this->wpqa_questions_type = $this->wpqa_questions_type();
		$this->wpqa_asked_questions_type = $this->wpqa_asked_questions_type();
		$this->wpqa_question_categories = $this->wpqa_question_categories();
		$this->wpqa_question_tags = $this->wpqa_question_tags();
		$this->wpqa_knowledgebase_type = $this->wpqa_knowledgebase_type();
		$this->wpqa_knowledgebase_categories = $this->wpqa_knowledgebase_categories();
		$this->wpqa_knowledgebase_tags = $this->wpqa_knowledgebase_tags();
		add_action('wp_enqueue_scripts',array($this,'enqueue_scripts'));
		add_action('admin_enqueue_scripts',array($this,'admin_enqueue_scripts'));
		$this->plugin_url = "https://2code.info/WPQA/";
		$this->site_url = "https://2code.info/";
		$this->wpqa_main_path = plugin_dir_path(__FILE__);
	}

	/* Make it works */
	public function init() {
		$this->constants();
		$this->includes();
		$wpqa_plugin_version = get_option("wpqa_plugin_version");
		if ($wpqa_plugin_version == "") {
			$add_it = true;
		}else if ($wpqa_plugin_version != "" && $wpqa_plugin_version != wpqa_plugin_version) {
			$add_it = true;
		}
		if (isset($add_it)) {
			update_option("wpqa_plugin_version",wpqa_plugin_version);
			update_option("FlushRewriteRules",true);
		}
	}

	/* Post type question */
	public function wpqa_questions_type($type = "question") {
		$this->wpqa_questions_type = $type;
		$this->wpqa_questions_type = apply_filters("wpqa_questions_type",$this->wpqa_questions_type);
		$this->wpqa_questions_type = sanitize_key($this->wpqa_questions_type);
		if ($this->wpqa_questions_type == "" || $this->wpqa_questions_type == "post") {
			$this->wpqa_questions_type = "question";
		}
		return $this->wpqa_questions_type;
	}

	/* Post type asked question */
	public function wpqa_asked_questions_type($type = "asked-question") {
		$this->wpqa_asked_questions_type = $type;
		$this->wpqa_asked_questions_type = apply_filters("wpqa_asked_questions_type",$this->wpqa_asked_questions_type);
		$this->wpqa_asked_questions_type = sanitize_key($this->wpqa_asked_questions_type);
		if ($this->wpqa_asked_questions_type == "" || $this->wpqa_asked_questions_type == "post" || $this->wpqa_asked_questions_type == $this->wpqa_questions_type()) {
			$this->wpqa_asked_questions_type = "asked-question";
		}
		return $this->wpqa_asked_questions_type;
	}

	/* Question categories */
	public function wpqa_question_categories($type = "question-category") {
		$this->wpqa_question_categories = $type;
		$this->wpqa_question_categories = apply_filters("wpqa_question_categories",$this->wpqa_question_categories);
		$this->wpqa_question_categories = sanitize_key($this->wpqa_question_categories);
		if ($this->wpqa_question_categories == "" || $this->wpqa_question_categories == "category" || $this->wpqa_question_categories == "post_tag") {
			$this->wpqa_question_categories = "question-category";
		}
		return $this->wpqa_question_categories;
	}

	/* Question tags */
	public function wpqa_question_tags($type = "question_tags") {
		$this->wpqa_question_tags = $type;
		$this->wpqa_question_tags = apply_filters("wpqa_question_tags",$this->wpqa_question_tags);
		$this->wpqa_question_tags = sanitize_key($this->wpqa_question_tags);
		if ($this->wpqa_question_tags == "" || $this->wpqa_question_tags == "category" || $this->wpqa_question_tags == "post_tag" || $this->wpqa_question_tags == $this->wpqa_question_categories()) {
			$this->wpqa_question_tags = "question_tags";
		}
		return $this->wpqa_question_tags;
	}

	/* Post type knowledgebase */
	public function wpqa_knowledgebase_type($type = "knowledgebase") {
		$this->wpqa_knowledgebase_type = $type;
		$this->wpqa_knowledgebase_type = apply_filters("wpqa_knowledgebase_type",$this->wpqa_knowledgebase_type);
		$this->wpqa_knowledgebase_type = sanitize_key($this->wpqa_knowledgebase_type);
		if ($this->wpqa_knowledgebase_type == "" || $this->wpqa_knowledgebase_type == "post" || $this->wpqa_knowledgebase_type == $this->wpqa_questions_type() || $this->wpqa_knowledgebase_type == $this->wpqa_asked_questions_type()) {
			$this->wpqa_knowledgebase_type = "knowledgebase";
		}
		return $this->wpqa_knowledgebase_type;
	}

	/* Knowledgebase categories */
	public function wpqa_knowledgebase_categories($type = "kb") {
		$this->wpqa_knowledgebase_categories = $type;
		$this->wpqa_knowledgebase_categories = apply_filters("wpqa_knowledgebase_categories",$this->wpqa_knowledgebase_categories);
		$this->wpqa_knowledgebase_categories = sanitize_key($this->wpqa_knowledgebase_categories);
		if ($this->wpqa_knowledgebase_categories == "" || $this->wpqa_knowledgebase_categories == "category" || $this->wpqa_knowledgebase_categories == "post_tag" || $this->wpqa_knowledgebase_categories == $this->wpqa_question_categories() || $this->wpqa_question_tags == $this->wpqa_questions_type()) {
			$this->wpqa_knowledgebase_categories = "kb";
		}
		return $this->wpqa_knowledgebase_categories;
	}

	/* Knowledgebase tags */
	public function wpqa_knowledgebase_tags($type = "kb_tags") {
		$this->wpqa_knowledgebase_tags = $type;
		$this->wpqa_knowledgebase_tags = apply_filters("wpqa_knowledgebase_tags",$this->wpqa_knowledgebase_tags);
		$this->wpqa_knowledgebase_tags = sanitize_key($this->wpqa_knowledgebase_tags);
		if ($this->wpqa_knowledgebase_tags == "" || $this->wpqa_knowledgebase_tags == "category" || $this->wpqa_knowledgebase_tags == "post_tag" || $this->wpqa_knowledgebase_tags == $this->wpqa_question_categories() || $this->wpqa_knowledgebase_tags == $this->wpqa_question_tags() || $this->wpqa_knowledgebase_tags == $this->wpqa_knowledgebase_categories()) {
			$this->wpqa_knowledgebase_tags = "kb_tags";
		}
		return $this->wpqa_knowledgebase_tags;
	}

	/* The defines */
	protected function constants() {
		define("WPQA_URL",plugin_dir_url(__FILE__));
		define("WPQA_DIR",plugin_dir_path(__FILE__));
		define("wpqa_plugin_version",$this->version);
		define("wpqa_widgets","WPQA");
		require_once WPQA_DIR.'includes/themes.php';
		define("wpqa_prefix_theme",get_option("get_theme_name"));
		define("wpqa_name_theme",wpqa_name_theme());
		define("wpqa_meta",wpqa_prefix_theme);
		define("wpqa_terms",wpqa_prefix_theme);
		define("wpqa_author",wpqa_prefix_theme);
		define("wpqa_options",wpqa_prefix_theme."_options");
		define("wpqa_styling_options",wpqa_prefix_theme."_styling_options");
		define("wpqa_mobile_options",wpqa_prefix_theme."_mobile_options");
		define("wpqa_theme_url_tf",wpqa_theme_url());
		if (!defined("prefix_meta")) {
			define("prefix_meta",wpqa_meta."_");
		}
		if (!defined("prefix_terms")) {
			define("prefix_terms",wpqa_terms."_");
		}
		if (!defined("prefix_author")) {
			define("prefix_author",wpqa_author."_");
		}
		if (!defined("wpqa_questions_type")) {
			define("wpqa_questions_type",$this->wpqa_questions_type);
		}
		if (!defined("wpqa_asked_questions_type")) {
			define("wpqa_asked_questions_type",$this->wpqa_asked_questions_type);
		}
		if (!defined("wpqa_question_categories")) {
			define("wpqa_question_categories",$this->wpqa_question_categories);
		}
		if (!defined("wpqa_question_tags")) {
			define("wpqa_question_tags",$this->wpqa_question_tags);
		}
		if (!defined("wpqa_knowledgebase_type")) {
			define("wpqa_knowledgebase_type",$this->wpqa_knowledgebase_type);
		}
		if (!defined("wpqa_knowledgebase_categories")) {
			define("wpqa_knowledgebase_categories",$this->wpqa_knowledgebase_categories);
		}
		if (!defined("wpqa_knowledgebase_tags")) {
			define("wpqa_knowledgebase_tags",$this->wpqa_knowledgebase_tags);
		}
	}

	/* Include the files */
	private function includes() {
		/* Load the core */
		require_once WPQA_DIR.'includes/core.php';
		require_once WPQA_DIR.'includes/updates.php';
		/* Mobile */
		require_once WPQA_DIR.'/framework/mobile-options.php';

		/* Demo */
		if (is_admin()) {
			require_once WPQA_DIR.'/framework/demos/import-demo.php';
			require_once WPQA_DIR.'/framework/demos/demos.php';
		}

		/* Post types */
		require_once WPQA_DIR.'CPT/activities.php';
		require_once WPQA_DIR.'CPT/asked-questions.php';
		require_once WPQA_DIR.'CPT/checkouts.php';
		require_once WPQA_DIR.'CPT/groups.php';
		require_once WPQA_DIR.'CPT/knowledgebase.php';
		require_once WPQA_DIR.'CPT/messages.php';
		require_once WPQA_DIR.'CPT/notifications.php';
		require_once WPQA_DIR.'CPT/points.php';
		require_once WPQA_DIR.'CPT/posts.php';
		require_once WPQA_DIR.'CPT/questions.php';
		require_once WPQA_DIR.'CPT/reports.php';
		require_once WPQA_DIR.'CPT/requests.php';
		require_once WPQA_DIR.'CPT/statements.php';

		/* Functions */
		require_once WPQA_DIR.'functions/actions.php';
		require_once WPQA_DIR.'functions/ads.php';
		require_once WPQA_DIR.'functions/ajax-action.php';
		require_once WPQA_DIR.'functions/author.php';
		require_once WPQA_DIR.'functions/avatar.php';
		require_once WPQA_DIR.'functions/breadcrumbs.php';
		require_once WPQA_DIR.'functions/categories.php';
		require_once WPQA_DIR.'functions/check-account.php';
		require_once WPQA_DIR.'functions/comments.php';
		require_once WPQA_DIR.'functions/cover.php';
		require_once WPQA_DIR.'functions/filters.php';
		require_once WPQA_DIR.'functions/functions.php';
		require_once WPQA_DIR.'functions/group.php';
		require_once WPQA_DIR.'functions/mails.php';
		require_once WPQA_DIR.'functions/menu.php';
		require_once WPQA_DIR.'functions/meta-box.php';
		require_once WPQA_DIR.'functions/pagination.php';
		require_once WPQA_DIR.'functions/points.php';
		require_once WPQA_DIR.'functions/popup.php';
		require_once WPQA_DIR.'functions/posts.php';
		require_once WPQA_DIR.'functions/private.php';
		require_once WPQA_DIR.'functions/questions.php';
		require_once WPQA_DIR.'functions/rate.php';
		require_once WPQA_DIR.'functions/reactions.php';
		require_once WPQA_DIR.'functions/referrals.php';
		require_once WPQA_DIR.'functions/resizer.php';
		require_once WPQA_DIR.'functions/review.php';
		require_once WPQA_DIR.'functions/rewrite.php';
		require_once WPQA_DIR.'functions/subscriptions.php';
		require_once WPQA_DIR.'functions/tabs.php';

		/* Editor */
		require_once WPQA_DIR.'editor/editor.php';

		/* Payments */
		require_once WPQA_DIR.'payments/answer.php';
		require_once WPQA_DIR.'payments/form.php';
		require_once WPQA_DIR.'payments/checkout.php';
		require_once WPQA_DIR.'payments/packages.php';
		require_once WPQA_DIR.'payments/payments.php';
		require_once WPQA_DIR.'payments/paypal.php';
		require_once WPQA_DIR.'payments/stripe.php';

		/* Shortcodes */
		require_once WPQA_DIR.'shortcodes/shortcodes.php';

		/* Widgets */
		require_once WPQA_DIR.'widgets/widgets.php';

		/* Load the options */
		require_once WPQA_DIR.'framework/options-class.php';
		require_once WPQA_DIR.'framework/widgets_settings.php';
		require_once WPQA_DIR.'options/widgets.php';

		if (is_admin()) {
			/* Options */
			require_once WPQA_DIR.'framework/admin_ajax.php';
			require_once WPQA_DIR.'options/options.php';
			require_once WPQA_DIR.'options/styling.php';
			require_once WPQA_DIR.'options/mobile.php';
			require_once WPQA_DIR.'options/settings.php';
			require_once WPQA_DIR.'options/terms.php';
			require_once WPQA_DIR.'options/meta.php';
			require_once WPQA_DIR.'options/author.php';
		}
	}

	/* The code that runs during plugin activation */
	public static function activate() {
		global $wp_version,$wpdb;
		$wpdb->query($wpdb->prepare("ALTER TABLE ".$wpdb->users." CHANGE `user_nicename` `user_nicename` VARCHAR(255) NOT NULL DEFAULT %s;",''));
		$wp_compatible_version = '4.0';
		if (version_compare($wp_version,$wp_compatible_version,'<')) {
			deactivate_plugins(basename(__FILE__));
			wp_die('<p>'.sprintf(esc_html__('This plugin can not be activated because it requires a WordPress version at least %1$s (or later). Please go to Dashboard &#9656; Updates to get the latest version of WordPress.','wpqa'),$wp_compatible_version).'</p><a href="'.admin_url('plugins.php').'">'.esc_html__('go back','wpqa').'</a>');
		}
		update_option("FlushRewriteRules",true);
	}

	/* The code that runs during plugin deactivation */
	public static function deactivate() {
		update_option("FlushRewriteRules",true);
		delete_option("wpqa_plugin_version");
		wp_clear_scheduled_hook("wpqa_scheduled_mails_daily");
		wp_clear_scheduled_hook("wpqa_scheduled_mails_weekly");
		wp_clear_scheduled_hook("wpqa_scheduled_mails_monthly");
		wp_clear_scheduled_hook("wpqa_scheduled_mails_daily_post");
		wp_clear_scheduled_hook("wpqa_scheduled_mails_weekly_post");
		wp_clear_scheduled_hook("wpqa_scheduled_mails_monthly_post");
		wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_daily_question");
		wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicedaily_question");
		wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_daily_post");
		wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicedaily_post");
		wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_daily_answer");
		wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicedaily_answer");
		wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_hourly");
		wp_clear_scheduled_hook("wpqa_scheduled_notification_mails_twicehourly");
	}

	/* Load plugin textdomain */
	public static function localization() {
		load_plugin_textdomain('wpqa',false,plugin_basename(dirname(__FILE__)).'/languages/');
	}

	/* The code that runs the enqueue style */
	public function enqueue_scripts() {
		$activate_knowledgebase = apply_filters("wpqa_activate_knowledgebase",false);
		wp_enqueue_style('select2-css',WPQA_URL.'assets/css/select2.min.css',array(),wpqa_plugin_version);
		wp_enqueue_style('wpqa-custom-css',WPQA_URL.'assets/css/custom.css',array(),wpqa_plugin_version);
		$captcha_answer    = wpqa_options("captcha_answer");
		$poll_image        = wpqa_options("poll_image");
		$poll_image_title  = wpqa_options("poll_image_title");
		$comment_limit     = (int)wpqa_options("comment_limit");
		$comment_min_limit = (int)wpqa_options("comment_min_limit");
		$answer_limit      = (int)wpqa_options("answer_limit");
		$answer_min_limit  = (int)wpqa_options("answer_min_limit");
		$ajax_file         = wpqa_options("ajax_file");
		$ajax_file         = ($ajax_file == "theme"?WPQA_URL.'includes/ajax.php':admin_url("admin-ajax.php"));
		wp_enqueue_script("wpqa-scripts-js",WPQA_URL.'assets/js/scripts.js',array("jquery"),wpqa_plugin_version,true);
	    wp_enqueue_script('select2-js',WPQA_URL.'assets/js/select2.min.js',array("jquery"),wpqa_plugin_version);
		wp_enqueue_script("wpqa-custom-js",WPQA_URL.'assets/js/custom.js',array("jquery","jquery-ui-core","jquery-ui-datepicker","jquery-ui-sortable"),wpqa_plugin_version,true);
		$wpqa_js = array(
			'admin_url'              => $ajax_file,
			'poll_image'             => $poll_image,
			'poll_image_title'       => $poll_image_title,
			'comment_limit'          => $comment_limit,
			'comment_min_limit'      => $comment_min_limit,
			'answer_limit'           => $answer_limit,
			'answer_min_limit'       => $answer_min_limit,
			'question'               => wpqa_questions_type,
			'asked_question'         => wpqa_asked_questions_type,
			'home_url'               => esc_url(home_url('/')),
			'wpqa_error_text'        => esc_html__('Please fill the required field.','wpqa'),
			'wpqa_error_min_limit'   => esc_html__('Sorry, The minimum characters is','wpqa'),
			'wpqa_error_limit'       => esc_html__('Sorry, The maximum characters is','wpqa'),
			'sure_delete_comment'    => esc_html__('Are you sure you want to delete the comment?','wpqa'),
			'sure_delete_answer'     => esc_html__('Are you sure you want to delete the answer?','wpqa'),
			'wpqa_remove_image'      => esc_html__('Are you sure you want to delete the image?','wpqa'),
			'wpqa_remove_attachment' => esc_html__('Are you sure you want to delete the attachment?','wpqa'),
			'no_vote_question'       => esc_html__('Sorry, you cannot vote your question.','wpqa'),
			'no_vote_more'           => esc_html__('Sorry, you cannot vote on the same question more than once.','wpqa'),
			'no_vote_user'           => esc_html__('Voting is available to members only.','wpqa'),
			'no_vote_answer'         => esc_html__('Sorry, you cannot vote your answer.','wpqa'),
			'no_vote_more_answer'    => esc_html__('Sorry, you cannot vote on the same answer more than once.','wpqa'),
			'no_vote_comment'        => esc_html__('Sorry, you cannot vote your comment.','wpqa'),
			'no_vote_more_comment'   => esc_html__('Sorry, you cannot vote on the same comment more than once.','wpqa'),
			'follow_question_attr'   => esc_html__('Follow the question','wpqa'),
			'unfollow_question_attr' => esc_html__('Unfollow the question','wpqa'),
			'follow'                 => esc_html__('Follow','wpqa'),
			'unfollow'               => esc_html__('Unfollow','wpqa'),
			'select_file'            => esc_html__('Select file','wpqa'),
			'browse'                 => esc_html__('Browse','wpqa'),
			'reported'               => esc_html__('Thank you, your report will be reviewed shortly.','wpqa'),
			'wpqa_error_comment'     => esc_html__('Please type a comment.','wpqa'),
			'click_continue'         => esc_html__('Click here to continue.','wpqa'),
			'click_not_finish'       => esc_html__('Complete your following above to continue.','wpqa'),
			'ban_user'               => esc_html__('Ban user','wpqa'),
			'unban_user'             => esc_html__('Unban user','wpqa'),
			'block_user'             => esc_html__('Block user','wpqa'),
			'unblock_user'           => esc_html__('Unblock user','wpqa'),
			'no_poll_more'           => esc_html__('Sorry, you cannot poll on the same question more than once.','wpqa'),
			'must_login'             => esc_html__('Please login to vote and see the results.','wpqa'),
			'insert_image'           => esc_html__('Insert Image','wpqa'),
			'error_uploading_image'  => esc_html__('Attachment Error! Please upload image only.','wpqa'),
			'add_favorite'           => esc_html__('Add this question to favorites','wpqa'),
			'remove_favorite'        => esc_html__('Remove this question of my favorites','wpqa'),
			'add_emoji'              => esc_html__('React','wpqa'),
		);
		wp_localize_script('wpqa-custom-js','wpqa_custom',$wpqa_js);
		if ($activate_knowledgebase == true) {
			wp_enqueue_script("wpqa-rate-js",WPQA_URL.'assets/js/rate.js',array("jquery"),wpqa_plugin_version,true);
			$wpqa_rate = array(
				'admin_url' => $ajax_file,
			);
			wp_localize_script('wpqa-rate-js','wpqa_rate',$wpqa_rate);
		}
		if (wpqa_is_user_edit_profile() || wpqa_is_user_financial_profile() || wpqa_is_user_withdrawals_profile() || wpqa_is_user_password_profile()) {
			wp_enqueue_script("wpqa-edit-js",WPQA_URL.'assets/js/edit.js',array("jquery"),wpqa_plugin_version,true);
			$wpqa_js = array(
				'admin_url'         => $ajax_file,
				'not_min_points'    => esc_html__("You don't get the minimum points to can request your payment.","wpqa"),
				'not_enough_points' => esc_html__("You don't have these points.","wpqa"),
				'not_enough_money'  => esc_html__("You don't meet the minimum balance withdrawal amount requirement.","wpqa"),
			);
			wp_localize_script('wpqa-edit-js','wpqa_edit',$wpqa_js);
		}
		if (wpqa_is_user_password_profile()) {
			wp_enqueue_script("wpqa-passwrod-js",WPQA_URL.'assets/js/password.js',array("jquery"),wpqa_plugin_version,true);
		}
		if (wpqa_is_user_mails_profile()) {
			wp_enqueue_script("wpqa-mails-js",WPQA_URL.'assets/js/mails.js',array("jquery"),wpqa_plugin_version,true);
		}
		if (wpqa_is_user_delete_profile()) {
			wp_enqueue_script("wpqa-delete-js",WPQA_URL.'assets/js/delete.js',array("jquery"),wpqa_plugin_version,true);
			$wpqa_js = array(
				'delete_account' => esc_html__('Are you sure you want to delete your account?','wpqa'),
			);
			wp_localize_script('wpqa-delete-js','wpqa_delete',$wpqa_js);
		}
		if (wpqa_is_user_messages()) {
			wp_enqueue_script("wpqa-message-js",WPQA_URL.'assets/js/message.js',array("jquery"),wpqa_plugin_version,true);
			$wpqa_js = array(
				'admin_url'            => $ajax_file,
				'sure_delete_message'  => esc_html__('Are you sure you want to delete the message?','wpqa'),
				'block_message_text'   => esc_html__('Block Message','wpqa'),
				'unblock_message_text' => esc_html__('Unblock Message','wpqa'),
			);
			wp_localize_script('wpqa-message-js','wpqa_message',$wpqa_js);
		}
		if (wpqa_is_checkout()) {
			$checkout_value = wpqa_checkout();
			$unlogged_pay = wpqa_options("unlogged_pay");
			if ($unlogged_pay == "on" || is_user_logged_in()) {
				$payment_methods = wpqa_options("payment_methods");
				$stripe_test = wpqa_options("stripe_test");
				$stripe_style_input = wpqa_options("stripe_style_input");
				$stripe_style_input = ($stripe_style_input != ""?$stripe_style_input:"1_input");
				$publishable_key = wpqa_options(($stripe_test == "on"?"test_":"")."publishable_key");
				if (isset($payment_methods["stripe"]["value"]) && $payment_methods["stripe"]["value"] == "stripe") {
					wp_enqueue_script("wpqa-stripe","https://js.stripe.com/v3/",array("jquery"),wpqa_plugin_version,true);
				}
				wp_enqueue_script("wpqa-payment-js",WPQA_URL.'assets/js/payment.js',array("jquery"),wpqa_plugin_version,true);
				$wpqa_js = array(
					'admin_url'          => $ajax_file,
					'publishable_key'    => $publishable_key,
					'stripe_style_input' => $stripe_style_input,
				);
				wp_localize_script('wpqa-payment-js','wpqa_payment',$wpqa_js);
			}
		}
		if (wpqa_is_user_referrals()) {
			wp_enqueue_script("wpqa-referral-js",WPQA_URL.'assets/js/referral.js',array("jquery"),wpqa_plugin_version,true);
			$wpqa_js = array(
				'admin_url'       => $ajax_file,
				'email_exist'     => esc_html__('This email is already exists.','wpqa'),
				'sent_invitation' => esc_html__('The invitation was sent.','wpqa'),
			);
			wp_localize_script('wpqa-referral-js','wpqa_referral',$wpqa_js);
		}
		if (wpqa_is_pending_questions() || wpqa_is_pending_posts()) {
			wp_enqueue_script("wpqa-review-js",WPQA_URL.'assets/js/review.js',array("jquery"),wpqa_plugin_version,true);
			$wpqa_js = array(
				'admin_url'        => $ajax_file,
				'sure_ban'         => esc_html__('Are you sure you want to ban this user?','wpqa'),
				'sure_delete'      => esc_html__('Are you sure you want to delete the question?','wpqa'),
				'sure_delete_post' => esc_html__('Are you sure you want to delete the post?','wpqa'),
				'ban_user'         => esc_html__('Ban user','wpqa'),
				'unban_user'       => esc_html__('Unban user','wpqa'),
				'no_questions'     => esc_html__('There are no questions yet.','wpqa'),
				'no_posts'         => esc_html__('There are no posts yet.','wpqa'),
			);
			wp_localize_script('wpqa-review-js','wpqa_review',$wpqa_js);
		}
		if (is_single()) {
			$require_name_email    = get_option("require_name_email");
			$comment_editor        = wpqa_options((is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)?"answer_editor":"comment_editor"));
			$attachment_answer     = wpqa_options("attachment_answer");
			$featured_image_answer = wpqa_options("featured_image_answer");
			$terms_active_comment  = wpqa_options("terms_active_comment");
			$activate_editor_reply = wpqa_options("activate_editor_reply");
			$popup_share_seconds   = wpqa_options("popup_share_seconds");
			$user_id               = get_current_user_id();
			$is_logged             = ($user_id > 0?"logged":"unlogged");
			$display_name          = ($user_id > 0?get_the_author_meta('display_name',$user_id):"");
			$profile_url           = ($user_id > 0?wpqa_profile_url($user_id):"");
			$logout_url            = ($user_id > 0?wpqa_get_logout():"");
			wp_enqueue_script("wpqa-single-js",WPQA_URL.'assets/js/single.js',array("jquery"),wpqa_plugin_version,true);
			$wpqa_js = array(
				'wpqa_dir'               => WPQA_URL,
				'wpqa_best_answer_nonce' => wp_create_nonce('wpqa_best_answer_nonce'),
				'require_name_email'     => ($require_name_email == 1?'require_name_email':''),
				'admin_url'              => $ajax_file,
				'comment_limit'          => $comment_limit,
				'comment_min_limit'      => $comment_min_limit,
				'answer_limit'           => $answer_limit,
				'answer_min_limit'       => $answer_min_limit,
				'captcha_answer'         => $captcha_answer,
				'attachment_answer'      => $attachment_answer,
				'featured_image_answer'  => $featured_image_answer,
				'terms_active_comment'   => $terms_active_comment,
				'comment_editor'         => $comment_editor,
				'activate_editor_reply'  => $activate_editor_reply,
				'is_logged'              => $is_logged,
				'display_name'           => $display_name,
				'profile_url'            => $profile_url,
				'logout_url'             => $logout_url,
				'popup_share_seconds'    => $popup_share_seconds,
				'comment_action'         => esc_url(site_url('/wp-comments-post.php')),
				'wpqa_error_name'        => esc_html__('Please fill the required fields (name).','wpqa'),
				'wpqa_error_email'       => esc_html__('Please fill the required fields (email).','wpqa'),
				'wpqa_valid_email'       => esc_html__('Please enter a valid email address.','wpqa'),
				'wpqa_error_comment'     => esc_html__('Please type a comment.','wpqa'),
				'wpqa_error_min_limit'   => esc_html__('Sorry, The minimum characters is','wpqa'),
				'wpqa_error_limit'       => esc_html__('Sorry, The maximum characters is','wpqa'),
				'wpqa_error_terms'       => esc_html__('There are required fields (Agree of the terms).','wpqa'),
				'cancel_reply'           => esc_html__('Cancel reply.','wpqa'),
				'logged_as'              => esc_html__('Logged in as','wpqa'),
				'logout_title'           => esc_html__('Log out of this account','wpqa'),
				'logout'                 => esc_html__('Log out','wpqa'),
				'reply'                  => esc_html__('Reply','wpqa'),
				'submit'                 => esc_html__('Submit','wpqa'),
				'choose_best_answer'     => esc_html__('Select as best answer','wpqa'),
				'cancel_best_answer'     => esc_html__('Cancel the best answer','wpqa'),
				'best_answer'            => esc_html__('Best answer','wpqa'),
				'best_answer_selected'   => esc_html__('There is another one select this a best answer','wpqa'),
				'wpqa_error_captcha'     => esc_html__('The captcha is incorrect, Please try again.','wpqa'),
				'sure_delete'            => esc_html__('Are you sure you want to delete the question?','wpqa'),
				'sure_delete_post'       => esc_html__('Are you sure you want to delete the post?','wpqa'),
				'get_points'             => esc_html__('You have bumped your question.','wpqa'),
			);
			wp_localize_script('wpqa-single-js','wpqa_single',$wpqa_js);
		}
		if (wpqa_is_subscriptions() || wpqa_is_user_transactions_profile()) {
			wp_enqueue_script("wpqa-subscriptions-js",WPQA_URL.'assets/js/subscriptions.js',array("jquery"),wpqa_plugin_version,true);
			$wpqa_js = array(
				'admin_url'           => $ajax_file,
				'cancel_subscription' => esc_html__('Are you sure you want to cancel your subscription?','wpqa'),
				'trial_subscription'  => esc_html__('Are you sure you want to get the trial subscription?','wpqa'),
			);
			wp_localize_script('wpqa-subscriptions-js','wpqa_subscriptions',$wpqa_js);
		}
		if (!is_user_logged_in()) {
			wp_enqueue_script("wpqa-unlogged-js",WPQA_URL.'assets/js/unlogged.js',array("jquery"),wpqa_plugin_version,true);
			$wpqa_js = array(
				'wpqa_dir'           => WPQA_URL,
				'admin_url'          => $ajax_file,
				'captcha_answer'     => $captcha_answer,
				'wpqa_error_text'    => esc_html__('Please fill the required field.','wpqa'),
				'wpqa_error_captcha' => esc_html__('The captcha is incorrect, Please try again.','wpqa'),
			);
			wp_localize_script('wpqa-unlogged-js','wpqa_unlogged',$wpqa_js);
		}
		$active_groups = wpqa_options("active_groups");
		$search_type = (wpqa_is_search()?wpqa_search_type():"");
		if ($active_groups == "on" && (wpqa_is_add_groups() || wpqa_is_edit_groups() || is_singular("group") || wpqa_is_view_posts_group() || wpqa_is_edit_posts_group() || wpqa_is_group_requests() || wpqa_is_group_users() || wpqa_is_group_admins() || wpqa_is_blocked_users() || wpqa_is_posts_group() || wpqa_is_user_groups() || wpqa_is_user_joined_groups() || wpqa_is_user_managed_groups() || is_post_type_archive("group") || is_page_template("template-groups.php") || $search_type == "groups")) {
			wp_enqueue_script("wpqa-groups-js",WPQA_URL.'assets/js/groups.js',array("jquery"),wpqa_plugin_version,true);
			$wpqa_js = array(
				'admin_url'         => $ajax_file,
				'comment_action'    => esc_url(site_url('/wp-comments-post.php')),
				'like_posts_attr'   => esc_html__('Like','wpqa'),
				'unlike_posts_attr' => esc_html__('Unlike','wpqa'),
				'cancel_reply'      => esc_html__('Cancel reply.','wpqa'),
				'reply'             => esc_html__('Reply','wpqa'),
				'submit'            => esc_html__('Submit','wpqa'),
				'sure_delete_group' => esc_html__('Are you sure you want to delete the group?','wpqa'),
				'sure_delete_posts' => esc_html__('Are you sure you want to delete the group post?','wpqa'),
				'sure_block_user'   => esc_html__('Are you sure you want to block the user from the group?','wpqa'),
				'sure_remove_user'  => esc_html__('Are you sure you want to remove the user from the group?','wpqa'),
				'remove_moderator'  => esc_html__('Are you sure you want to remove the moderator from the group?','wpqa'),
			);
			wp_localize_script('wpqa-groups-js','wpqa_groups',$wpqa_js);
		}
	}

	/* Admin Scripts & Styles */
	public function admin_enqueue_scripts($hook) {
		if (is_admin()) {
			wp_enqueue_script("wpqa-admin-custom-js",WPQA_URL.'assets/js/admin-custom.js',array("jquery"),wpqa_plugin_version,true);
			$new_payments = (int)get_option("new_payments");
			$new_requests = (int)get_option("new_requests");
			$new_reports = (int)get_option("new_reports");
			$new_question_reports = (int)get_option("new_question_reports");
			$new_answer_reports = (int)get_option("new_answer_reports");
			$new_user_reports = (int)get_option("new_user_reports");
			$ajax_file = wpqa_options("ajax_file");
			$ajax_file = ($ajax_file == "theme"?WPQA_URL.'includes/ajax.php':admin_url("admin-ajax.php"));
			$option_js = array(
				'ajax_a'                    => $ajax_file,
				'comment_status'            => (isset($_GET['comment_status'])?esc_js($_GET['comment_status']):''),
				'report_type'               => (isset($_GET['types'])?esc_js($_GET['types']):''),
				'statement'                 => (isset($_GET['statement'])?esc_js($_GET['statement']):''),
				'request'                   => (isset($_GET['request'])?esc_js($_GET['request']):''),
				'user_roles'                => (isset($_GET['role'])?esc_js($_GET['role']):''),
				'new_payments'              => $new_payments,
				'new_requests'              => $new_requests,
				'new_reports'               => $new_reports,
				'new_question_reports'      => $new_question_reports,
				'new_answer_reports'        => $new_answer_reports,
				'new_user_reports'          => $new_user_reports,
				'confirm_refund'            => esc_html__('Are you sure you want to refund?','wpqa'),
				'refunded'                  => esc_html__('Refunded','wpqa'),
				'confirm_delete'            => esc_html__('Are you sure you want to delete?','wpqa'),
				'confirm_delete_attachment' => esc_html__('If you press will delete the attachment!','wpqa'),
				'confirm_delete_history'    => esc_html__('Are you sure you want to delete the history?','wpqa'),
				'deleting'                  => esc_html__('Deleting...','wpqa'),
				'fixing'                    => esc_html__('Fixing...','wpqa'),
				'creating'                  => esc_html__('Creating...','wpqa'),
				'approving'                 => esc_html__('Approving...','wpqa'),
				'removing'                  => esc_html__('Removing...','wpqa'),
				'activating'                => esc_html__('Activating...','wpqa'),
				'confirm_creating'          => esc_html__('Are you sure you want to delete the two menus and recreate them again?','wpqa'),
				'confirm_approving'         => esc_html__('Are you sure you want to approve this user?','wpqa'),
				'confirm_activating'        => esc_html__('Are you sure you want to activate this user?','wpqa'),
				'confirm_removing'          => esc_html__('Are you sure you want to remove the subscription of this user?','wpqa'),
			);
			wp_localize_script('wpqa-admin-custom-js','option_js',$option_js);
			if (wpqa_screen_belongs_to_theme_options()) {
				$enqueue = false;
				$hook_arrs = array(
					'widgets.php',
					'customize.php',
					'post.php',
					'post-new.php',
					'term.php',
					'edit-tags.php',
					'toplevel_page_options',
					'toplevel_page_registration',
					'wpqa_page_registration',
					'wpqa_page_demo-import',
					wpqa_prefix_theme.'_page_styling',
					wpqa_prefix_theme.'_page_mobile',
					wpqa_prefix_theme.'_page_registration',
					wpqa_prefix_theme.'_page_demo-import',
					'toplevel_page_wpqa_new_categories',
					'toplevel_page_'.wpqa_prefix_theme.'_new_categories',
					'profile.php',
					'user-new.php',
					'user-edit.php',
					'edit-comments.php',
					'_wpqa-registration',
					'_'.wpqa_prefix_theme.'-registration',
					'_registration-apps'
				);
				$hook_array = apply_filters("wpqa_settings_hook_arr",array(),$hook_arrs,$hook);
				$hook_new_array = array();
				if (is_array($hook_array) && !empty($hook_array)) {
					foreach ($hook_array as $value) {
						$hook_new_array[] = wpqa_prefix_theme.'_page_'.$value;
					}
				}
				$hook_arrs = array_merge($hook_arrs,$hook_new_array);
				$hook_arrs = apply_filters(wpqa_prefix_theme."_hook_arr",$hook_arrs,$hook);
				foreach ($hook_arrs as $hook_arr) {
					if ($hook_arr == $hook || strpos($hook,$hook_arr) !== false) {
						$enqueue = true;
					}
				}
				
				if ( $hook == 'post.php' ) {
					global $post;
					$post_type = $post->post_type;
				}else if ( array_key_exists( 'post_type', $_GET ) ) {
					$post_type = $_GET['post_type'];
				}

				$allow_post_type = apply_filters(wpqa_prefix_theme."_allow_post_type",array('post','page',wpqa_questions_type,wpqa_asked_questions_type,wpqa_knowledgebase_type,'group','posts','checkout'));
				if ( isset( $post_type ) && ! in_array( $post_type, $allow_post_type ) ) {
					$enqueue = false;
				}

				if ( ( $hook == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'statement' ) || ( $hook == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'group' ) || ( $hook == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'posts' ) || ( $hook == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'report' ) || ( $hook == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'request' ) || ( $hook == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'notification' ) || ( $hook == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'activity' ) || ( $hook == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'point' ) ) {
					$enqueue = true;
				}
				
				if ( $enqueue ) {
					wp_enqueue_style('select2-css',WPQA_URL.'assets/css/select2.min.css',array(),wpqa_plugin_version);
					if (is_rtl()) {
						wp_enqueue_style("admin-style",WPQA_URL.'framework/css/admin_style_ar.css',array(),wpqa_plugin_version);
					}else {
						wp_enqueue_style("admin-style",WPQA_URL.'framework/css/admin_style.css',array(),wpqa_plugin_version);
					}
					do_action("wpqa_framework_admin_css");
					
					wp_enqueue_style("wp-color-picker");
					wp_enqueue_script("jquery-ui-datepicker");
					wp_enqueue_script("wpqa-fontselect",WPQA_URL.'framework/js/fontselect.js',array("jquery"));
					wp_enqueue_script("custom_options",WPQA_URL.'framework/js/custom_options.js',array("jquery","wp-color-picker","jquery-ui-sortable","jquery-ui-datepicker"),wpqa_plugin_version);
					$activate_knowledgebase = apply_filters("wpqa_activate_knowledgebase",false);
					$js_activate_knowledgebase = ($activate_knowledgebase == true?"yes":"no");
					$theme_js_var = array(
						"ajax_a"                    => admin_url("admin-ajax.php"),
						"framework_name"            => wpqa_prefix_theme,
						"framework_theme"           => wpqa_options,
						'question'                  => wpqa_questions_type,
						'asked_question'            => wpqa_asked_questions_type,
						'activate_knowledgebase'    => $js_activate_knowledgebase,
						"confirm_reset"             => esc_html__("Click OK to reset. Any theme settings will be lost.","wpqa"),
						"confirm_delete"            => esc_html__("Are you sure you want to delete?","wpqa"),
						"confirm_reports"           => esc_html__("If you press the report will be deleted!","wpqa"),
						"confirm_delete_attachment" => esc_html__("If you press the attachment will be deleted!","wpqa"),
						"choose_image"              => esc_html__("Choose Image","wpqa"),
						"edit_image"                => esc_html__("Edit","wpqa"),
						"upload_image"              => esc_html__("Upload","wpqa"),
						"image_url"                 => esc_html__("Image URL","wpqa"),
						"remove_image"              => esc_html__("Remove","wpqa"),
						"answers"                   => esc_html__("Answers","wpqa"),
						"add_answer_button"         => esc_html__("Add answer","wpqa"),
						"no_answers"                => esc_html__("No answers yet.","wpqa"),
						"on"                        => esc_html__("ON","wpqa"),
						"off"                       => esc_html__("OFF","wpqa"),
						"questions_month"           => esc_html__("Questions per month.","wpqa"),
						"questions_day"             => esc_html__("Questions per day.","wpqa"),
						"answers_month"             => esc_html__("Answers per month.","wpqa"),
						"answers_day"               => esc_html__("Answers per day.","wpqa"),
						"insert_image"              => esc_html__("Insert Image","wpqa"),
						"error_uploading_image"     => esc_html__("Attachment Error! Please upload image only.","wpqa"),
					);
					$wpqa_roles_array = wpqa_roles_array();
					$theme_js_var = array_merge($theme_js_var,$wpqa_roles_array);
					wp_localize_script("custom_options","theme_js_var",$theme_js_var);
					if (function_exists("wp_enqueue_media")) {
						wp_enqueue_media();
					}
					wp_enqueue_script("tipsy-js",WPQA_URL.'framework/js/tipsy.js',array("jquery"));
					wp_enqueue_script('select2-js',WPQA_URL.'assets/js/select2.min.js',array("jquery"),wpqa_plugin_version);
				}
			}
		}
	}
}

/* Class */
$wpqa = new WPQA();
$wpqa->init();
register_activation_hook(__FILE__,array('WPQA','activate'));
register_deactivation_hook(__FILE__,array('WPQA','deactivate'));
add_action('plugins_loaded',array('WPQA','localization'));

/* Check if current screen belongs to theme options */
function wpqa_screen_belongs_to_theme_options() {
	if (is_admin()) {
		global $pagenow;
		$white_label = array(
			'post.php',
			'post-new.php',
			'term.php',
			'edit-tags.php',
			'widgets.php',
			'customize.php',
			'profile.php',
			'user-new.php',
			'user-edit.php',
			'edit-comments.php'
		);
		$white_label = apply_filters(wpqa_prefix_theme."_white_label",$white_label,$pagenow);
		$show_admin_scripts = apply_filters(wpqa_prefix_theme."_show_admin_scripts",false,$pagenow);
		$white_label_array = apply_filters("wpqa_white_label_array",array(),$pagenow);
		$white_label_settings = false;
		if (is_array($white_label_array) && !empty($white_label_array)) {
			foreach ($white_label_array as $value) {
				if ((($pagenow == 'themes.php' || $pagenow == 'admin.php') && array_key_exists('page',$_GET) && $_GET['page'] == $value)) {
					$white_label_settings = true;
				}
			}
		}
		if ( ( $pagenow == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'group' ) || ( $pagenow == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'posts' ) || ( $pagenow == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'statement' ) || ( ($pagenow == 'themes.php' || $pagenow == 'admin.php') && array_key_exists( 'page', $_GET ) && $_GET['page'] == 'options' ) || ( ($pagenow == 'themes.php' || $pagenow == 'admin.php') && array_key_exists( 'page', $_GET ) && $_GET['page'] == 'styling' ) || ( ($pagenow == 'themes.php' || $pagenow == 'admin.php') && array_key_exists( 'page', $_GET ) && $_GET['page'] == 'mobile' ) || ( ($pagenow == 'themes.php' || $pagenow == 'admin.php') && array_key_exists( 'page', $_GET ) && $_GET['page'] == 'demo-import' ) || ( ($pagenow == 'themes.php' || $pagenow == 'admin.php') && array_key_exists( 'page', $_GET ) && $_GET['page'] == 'registration' ) || ( ($pagenow == 'themes.php' || $pagenow == 'admin.php') && array_key_exists( 'page', $_GET ) && $_GET['page'] == 'wpqa-registration' ) || ( ($pagenow == 'themes.php' || $pagenow == 'admin.php') && array_key_exists( 'page', $_GET ) && $_GET['page'] == 'registration-apps' ) || ( $pagenow == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'request' ) || ( $pagenow == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'notification' ) || ( $pagenow == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'activity' ) || ( $pagenow == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'point' ) || ( $pagenow == 'edit.php' && array_key_exists( 'post_type', $_GET ) && $_GET['post_type'] == 'report' ) || in_array( $pagenow, $white_label ) || $show_admin_scripts == true || $white_label_settings == true ) {
			return true;
		}
	}
	return false;
}

/* preload wp editor */
add_action('admin_footer','wpqa_preload_wp_editor');
function wpqa_preload_wp_editor() {
	if (wpqa_screen_belongs_to_theme_options()) {
		echo '<div style="display: none;">';
		wp_editor( '', 'framework_preloaded_editor_id', array(
			'textarea_name'    => 'wpqa_preloaded_editor_name',
			'textarea_rows'    => 20,
			'editor_class'     => 'framework-form-control',
			'drag_drop_upload' => true
		));
		echo '</div>';
	}
}

/* Move all "advanced" metaboxes above the default editor */
add_action('edit_form_after_title','wpqa_move_advanced_meta_boxes');
function wpqa_move_advanced_meta_boxes() {
	if (is_admin()) {
		global $post, $wp_meta_boxes;
		do_meta_boxes( get_current_screen(), 'advanced', $post );
		unset( $wp_meta_boxes[ get_post_type( $post ) ]['advanced'] );
	}
}?>