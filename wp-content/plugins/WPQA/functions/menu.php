<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action('after_setup_theme','wpqa_create_menus');
function wpqa_create_menus() {
	register_nav_menus(array(
		//'home_tabs_menu'      => esc_html__('Home Tabs Menu','wpqa'),
		'profile_page_menu'   => esc_html__('Profile Page Tabs','wpqa'),
		'header_profile_menu' => esc_html__('Header Profile Menu','wpqa')
	));
}
add_action('wpqa_init','wpqa_create_menu_items');
function wpqa_create_menu_items() {
	/* Profile Page Tabs */
	$wpqa_custom_queries = get_option("wpqa_custom_queries");
	$wpqa_custom_queries = (is_array($wpqa_custom_queries) && !empty($wpqa_custom_queries)?$wpqa_custom_queries:array());
	if (!isset($wpqa_custom_queries["profile_page_menu"])) {
		$user_profile_pages = wpqa_options("user_profile_pages");
		if (!array($user_profile_pages) || (array($user_profile_pages) && empty($user_profile_pages))) {
			$user_profile_pages = array(
				"questions"       => array("value" => "questions"),
				"polls"           => array("value" => "polls"),
				"answers"         => array("value" => "answers"),
				"best-answers"    => array("value" => "best-answers"),
				"followed"        => array("value" => "followed"),
				"favorites"       => array("value" => "favorites"),
				"my-asked"        => array("value" => "my-asked"),
				"asked-questions" => array("value" => "asked-questions"),
				"asked"           => array("value" => "asked"),
				"groups"          => array("value" => "groups"),
				"joined_groups"   => array("value" => "joined_groups"),
				"managed_groups"  => array("value" => "managed_groups"),
			);
		}
		$menu_profile_tabs = array();
		if (is_array($user_profile_pages) && !empty($user_profile_pages)) {
			foreach ($user_profile_pages as $key => $value) {
				if ($key != "" && isset($user_profile_pages[$key]["value"]) && $user_profile_pages[$key]["value"] == $key) {
					$name = ucwords(str_ireplace(array("-","_")," ",$key));
					$key_url = $key;
					if ($key == "my-asked" || $key == "asked-questions" || $key == "paid-questions" || $key == "best-answers" || $key == "followers-questions" || $key == "followers-answers" || $key == "followers-posts" || $key == "followers-comments") {
						$key_url = str_ireplace("-","_",$key);
						if ($key == "my-asked") {
							$name = esc_html__("My Asked Questions","wpqa");
						}else if ($key == "asked-questions") {
							$name = esc_html__("Waiting Questions","wpqa");
						}
					}else if ($key == "asked") {
						$name = esc_html__("Asked Questions","wpqa");
					}
					$menu_profile_tabs[$name] = array('url' => '#wpqa-'.$key_url);
				}
			}
		}
		$wpqa_custom_queries["profile_page_menu"] = true;
		update_option("wpqa_custom_queries",$wpqa_custom_queries);
		wpqa_generate_nav_menu('Profile Page Tabs',$menu_profile_tabs,'profile_page_menu');
	}
	/* Header Profile Menu */
	if (!isset($wpqa_custom_queries["header_profile_menu"])) {
		$user_login_links = wpqa_options("user_login_links");
		if (!array($user_login_links) || (array($user_login_links) && empty($user_login_links))) {
			$user_login_links = array(
				"user-profile"      => array("value" => "user-profile"),
				"edit-profile"      => array("value" => "edit-profile"),
				"pending-questions" => array("value" => "pending-questions"),
				"pending-posts"     => array("value" => "pending-posts"),
				"referrals"         => array("value" => "referrals"),
				"messages"          => array("value" => "messages"),
				"questions"         => array("value" => "questions"),
				"my-asked"          => array("value" => "my-asked"),
				"asked-questions"   => array("value" => "asked-questions"),
				"asked"             => array("value" => "asked"),
				"best-answers"      => array("value" => "best-answers"),
				"points"            => array("value" => "points"),
				"activities"        => array("value" => "activities"),
				"log-out"           => array("value" => "log-out"),
			);
		}
		$menu_profile_header = array();
		if (is_array($user_login_links) && !empty($user_login_links)) {
			foreach ($user_login_links as $key => $value) {
				if ($key != "" && isset($user_login_links[$key]["value"]) && $user_login_links[$key]["value"] == $key) {
					$name = ucwords(str_ireplace(array("-","_")," ",$key));
					$key_url = $key;
					if ($key == "question") {
						$name = esc_html__("Ask A Question","wpqa");
						$key_url = "add-question-popup";
					}else if ($key == "poll") {
						$name = esc_html__("Add A Poll","wpqa");
						$key_url = "add-poll";
					}else if ($key == "post") {
						$name = esc_html__("Post An Article","wpqa");
						$key_url = "add-post";
					}else if ($key == "group") {
						$name = esc_html__("Create A Group","wpqa");
						$key_url = "add-group";
					}else if ($key == "user-profile") {
						$key_url = "profile";
					}else if ($key == "log-out") {
						$key_url = "logout";
					}else if ($key == "my-asked" || $key == "asked-questions" || $key == "paid-questions" || $key == "best-answers" || $key == "followers-questions" || $key == "followers-answers" || $key == "followers-posts" || $key == "followers-comments") {
						$key_url = str_ireplace("-","_",$key);
						if ($key == "my-asked") {
							$name = esc_html__("My Asked Questions","wpqa");
						}else if ($key == "asked-questions") {
							$name = esc_html__("Waiting Questions","wpqa");
						}
					}else if ($key == "asked") {
						$name = esc_html__("Asked Questions","wpqa");
					}
					$menu_profile_header[$name] = array('url' => '#wpqa-'.$key_url);
				}
			}
		}
		$wpqa_custom_queries["header_profile_menu"] = true;
		update_option("wpqa_custom_queries",$wpqa_custom_queries);
		wpqa_generate_nav_menu('Header Profile Menu',$menu_profile_header,'header_profile_menu');
	}
}
function wpqa_generate_nav_menu($menu_name,$menu_items_array,$location_target) {
	wp_create_nav_menu($menu_name);
	$menu_obj = get_term_by('name',$menu_name,'nav_menu');
	foreach ($menu_items_array as $page_name => $page_data) {
		wpqa_generate_nav_menu_item($menu_obj->term_id,$page_name,$page_data);
	}
	$locations_arr = get_theme_mod('nav_menu_locations');
	$locations_arr[$location_target] = $menu_obj->term_id;
	set_theme_mod('nav_menu_locations',$locations_arr);
}
function wpqa_generate_nav_menu_item($term_id,$title,$data,$parent = false) {
	if (!$data['url']) {
		return;
	}

	$args = array(
		'menu-item-title'   => $title,
		'menu-item-url'     => ($data['url'] == '/'?home_url('/'):$data['url']),
		'menu-item-classes' => (isset($data['class'])?$data['class']:''),
		'menu-item-status'  => 'publish'
	);
	if ($parent) {
		$args['menu-item-parent-id'] = $parent;
	}

	$item_id = wp_update_nav_menu_item($term_id,0,$args);
	if (isset($data['icon'])) {
		update_post_meta($item_id,'_menu_meta_icon',sanitize_text_field($data['icon']));
	}
	if (!empty($data['sub-items']) && !is_wp_error($item_id)) {
		foreach ($data['sub-items'] as $sub_item_title => $item) {
			wpqa_generate_nav_menu_item($term_id,$sub_item_title,$item,$item_id);
		}
	}
}
add_action('load-nav-menus.php','wpqa_admin_wp_nav_menu_meta_box');
function wpqa_admin_wp_nav_menu_meta_box() {
	add_meta_box('wpqa-nav-menu',esc_html__('WPQA - Custom Links','wpqa'),'wpqa_nav_menu_meta_box','nav-menus','side','core');
	add_action('admin_print_footer_scripts','wpqa_nav_menu_script');
}
function wpqa_nav_menu_meta_box() {
	global $nav_menu_selected_id;
	
	$walker      = new WPQA_Walker_Nav_Menu_Checklist(false);
	$args        = array('walker' => $walker);
	$tabs        = array();
	$current_tab = 'all';
	
	$tabs['profile']['label'] = esc_html__('Profile links','wpqa');
	$tabs['profile']['pages'] = wpqa_nav_menu_get_profile_links();
	
	$removed_args = array('action','customlink-tab','edit-menu-item','menu-item','page-tab','_wpnonce');?>
	
	<div id="wpqa-menu" class="posttypediv">
		<ul id="wpqa-menu-tabs" class="wpqa-menu-tabs add-menu-item-tabs">
			<li <?php echo ('all' == $current_tab?' class="tabs"':'')?>>
				<a class="nav-tab-link" data-type="tabs-panel-wpqa-menu-all" href="<?php echo ($nav_menu_selected_id?esc_url_raw(add_query_arg('wpqa-menu-tab','all',remove_query_arg($removed_args))):"")?>#tabs-panel-wpqa-menu-all">
					<?php esc_html_e('View All')?>
				</a>
			</li>
		</ul>
		<div id="tabs-panel-wpqa-menu-all" class="tabs-panel tabs-panel-active">
			<ul id="wpqa-menu-checklist-profile" class="categorychecklist form-no-clear">
				<?php echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item',$tabs['profile']['pages']),0,(object)$args);?>
			</ul>
		</div>
		
		<p class="button-controls">
			<span class="list-controls hide-if-no-js">
				<input type="checkbox"<?php if (function_exists('wp_nav_menu_disabled_check')):wp_nav_menu_disabled_check($nav_menu_selected_id);endif;?> id="wpqa-tab" class="select-all" />
				<label for="wpqa-tab"><?php esc_html_e('Select All','wpqa');?></label>
			</span>
			<span class="add-to-menu">
				<input type="submit"<?php if (function_exists('wp_nav_menu_disabled_check')):wp_nav_menu_disabled_check($nav_menu_selected_id);endif;?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu','wpqa')?>" name="add-custom-menu-item" id="submit-wpqa-menu" />
				<span class="spinner"></span>
			</span>
		</p>
	</div>
	<?php
}
function wpqa_nav_menu_script() {?>
	<script type="text/javascript">
		jQuery(document).on("click","#wpqa-tab",function () {
			var selectAll = jQuery(this);
			var wpqaMenu  = jQuery('#wpqa-menu ul.categorychecklist');
			if (jQuery('#wpqa-tab').is(':checked')) {
		        jQuery("input[type='checkbox']",wpqaMenu).prop("checked", true);
		        selectAll.attr("checked", "checked");
		    }else {
		        jQuery("input[type='checkbox']",wpqaMenu).prop("checked", false);
		        selectAll.removeAttr("checked");
		    }
		});
		jQuery('#menu-to-edit').on('click','a.item-edit',function() {
			var settings  = jQuery(this).closest('.menu-item-bar').next('.menu-item-settings');
			var css_class = settings.find('.edit-menu-item-classes');
			
			if (css_class.val().indexOf('wpqa-menu') == 0) {
				css_class.attr('readonly','readonly');
				settings.find('.field-url').css('display','none');
			}
		});
	</script>
<?php
}
function wpqa_nav_menu_get_profile_links() {
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Login','wpqa'),
		'slug' => 'login',
		'link' => "#wpqa-login",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Login Pop Up','wpqa'),
		'slug' => 'login-popup',
		'link' => "#wpqa-login-popup",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Signup','wpqa'),
		'slug' => 'signup',
		'link' => "#wpqa-signup",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Signup Pop Up','wpqa'),
		'slug' => 'signup-popup',
		'link' => "#wpqa-signup-popup",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Lost Password','wpqa'),
		'slug' => 'lost-password',
		'link' => "#wpqa-lost-password",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Lost Password Pop Up','wpqa'),
		'slug' => 'lost-password-popup',
		'link' => "#wpqa-lost-password-popup",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Polls','wpqa'),
		'slug' => 'poll',
		'link' => "#wpqa-poll",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Add category','wpqa'),
		'slug' => 'add-category',
		'link' => "#wpqa-add-category",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Ask Question','wpqa'),
		'slug' => 'add-question',
		'link' => "#wpqa-add-question",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Ask Question Pop Up','wpqa'),
		'slug' => 'add-question-popup',
		'link' => "#wpqa-add-question-popup",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Add Poll','wpqa'),
		'slug' => 'add-poll',
		'link' => "#wpqa-add-poll",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Add group','wpqa'),
		'slug' => 'add-group',
		'link' => "#wpqa-add-group",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Add Post','wpqa'),
		'slug' => 'add-post',
		'link' => "#wpqa-add-post",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Add Post Pop Up','wpqa'),
		'slug' => 'add-post-popup',
		'link' => "#wpqa-add-post-popup",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Subscriptions','wpqa'),
		'slug' => 'subscriptions',
		'link' => "#wpqa-subscriptions",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Buy Points','wpqa'),
		'slug' => 'buy-points',
		'link' => "#wpqa-buy-points",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Polls','wpqa'),
		'slug' => 'polls',
		'link' => "#wpqa-polls",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Questions','wpqa'),
		'slug' => 'all-questions',
		'link' => "#wpqa-all-questions",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Groups','wpqa'),
		'slug' => 'all-groups',
		'link' => "#wpqa-all-groups",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Profile','wpqa'),
		'slug' => 'profile',
		'link' => "#wpqa-profile",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Edit Profile','wpqa'),
		'slug' => 'edit-profile',
		'link' => "#wpqa-edit-profile",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Change password','wpqa'),
		'slug' => 'password',
		'link' => "#wpqa-password",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Privacy','wpqa'),
		'slug' => 'privacy',
		'link' => "#wpqa-privacy",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Mail Settings','wpqa'),
		'slug' => 'mail-settings',
		'link' => "#wpqa-mail-settings",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Delete Account','wpqa'),
		'slug' => 'delete-account',
		'link' => "#wpqa-delete-account",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Transactions','wpqa'),
		'slug' => 'transactions',
		'link' => "#wpqa-transactions",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Withdrawals','wpqa'),
		'slug' => 'withdrawals',
		'link' => "#wpqa-withdrawals",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Financial','wpqa'),
		'slug' => 'financial',
		'link' => "#wpqa-financial",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Followers','wpqa'),
		'slug' => 'followers',
		'link' => "#wpqa-followers",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Following','wpqa'),
		'slug' => 'following',
		'link' => "#wpqa-following",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Blocking users','wpqa'),
		'slug' => 'blocking',
		'link' => "#wpqa-blocking",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Pending questions','wpqa'),
		'slug' => 'pending_questions',
		'link' => "#wpqa-pending-questions",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Pending posts','wpqa'),
		'slug' => 'pending_posts',
		'link' => "#wpqa-pending-posts",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Notifications','wpqa'),
		'slug' => 'notifications',
		'link' => "#wpqa-notifications",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Activities','wpqa'),
		'slug' => 'activities',
		'link' => "#wpqa-activities",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Referrals','wpqa'),
		'slug' => 'referrals',
		'link' => "#wpqa-referrals",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Messages','wpqa'),
		'slug' => 'messages',
		'link' => "#wpqa-messages",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Points','wpqa'),
		'slug' => 'points',
		'link' => "#wpqa-points",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('User Questions','wpqa'),
		'slug' => 'questions',
		'link' => "#wpqa-questions",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('My Asked Questions','wpqa'),
		'slug' => 'my-asked',
		'link' => "#wpqa-my_asked",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Waiting Questions','wpqa'),
		'slug' => 'asked-questions',
		'link' => "#wpqa-asked_questions",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Asked Questions','wpqa'),
		'slug' => 'asked',
		'link' => "#wpqa-asked",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Paid Questions','wpqa'),
		'slug' => 'paid-questions',
		'link' => "#wpqa-paid_questions",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Answers','wpqa'),
		'slug' => 'answers',
		'link' => "#wpqa-answers",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Best Answers','wpqa'),
		'slug' => 'best-answers',
		'link' => "#wpqa-best_answers",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('User Groups','wpqa'),
		'slug' => 'groups',
		'link' => "#wpqa-groups",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Joined Groups','wpqa'),
		'slug' => 'joined_groups',
		'link' => "#wpqa-joined_groups",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Managed Groups','wpqa'),
		'slug' => 'managed_groups',
		'link' => "#wpqa-managed_groups",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Followed','wpqa'),
		'slug' => 'followed',
		'link' => "#wpqa-followed",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Favorites','wpqa'),
		'slug' => 'favorites',
		'link' => "#wpqa-favorites",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Posts','wpqa'),
		'slug' => 'posts',
		'link' => "#wpqa-posts",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Comments','wpqa'),
		'slug' => 'comments',
		'link' => "#wpqa-comments",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Followers Questions','wpqa'),
		'slug' => 'followers-questions',
		'link' => "#wpqa-followers_questions",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Followers Answers','wpqa'),
		'slug' => 'followers-answers',
		'link' => "#wpqa-followers_answers",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Followers Posts','wpqa'),
		'slug' => 'followers-posts',
		'link' => "#wpqa-followers_posts",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Followers Comments','wpqa'),
		'slug' => 'followers-comments',
		'link' => "#wpqa-followers_comments",
	);
	$wpqa_menu_items[] = array(
		'name' => esc_html__('Log out','wpqa'),
		'slug' => 'logout',
		'link' => "#wpqa-logout",
	);

	$wpqa_menu_items = apply_filters("wpqa_menu_items",$wpqa_menu_items);
	
	if (count($wpqa_menu_items) < 1) {
		return false;
	}
	
	$page_args = array();
	foreach ($wpqa_menu_items as $wpqa_item) {
		$page_args[$wpqa_item['slug']] = (object) array(
			'ID'             => -1,
			'post_title'     => $wpqa_item['name'],
			'post_author'    => 0,
			'post_date'      => 0,
			'post_excerpt'   => $wpqa_item['slug'],
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'guid'           => $wpqa_item['link']
		);
	}
	
	return $page_args;
}

class WPQA_Walker_Nav_Menu_Checklist extends Walker_Nav_Menu {
	public function __construct($fields = false) {
		if ($fields) {
			$this->db_fields = $fields;
		}
	}
	
	public function start_lvl(&$output,$depth = 0,$args = array()) {
		$indent = str_repeat("\t",$depth);
		$output .= "\n$indent<ul class='children'>\n";
	}
	
	public function end_lvl(&$output,$depth = 0,$args = array()) {
		$indent = str_repeat("\t",$depth);
		$output .= "\n$indent</ul>";
	}
	
	function start_el(&$output,$item,$depth = 0,$args = array(),$id = 0) {
		global $_nav_menu_placeholder;
		
		$_nav_menu_placeholder = (0 > $_nav_menu_placeholder)?intval($_nav_menu_placeholder) - 1:-1;
		$possible_object_id = isset($item->post_type) && 'nav_menu_item' == $item->post_type?$item->object_id:$_nav_menu_placeholder;
		$possible_db_id = (!empty($item->ID)) && (0 < $possible_object_id)?(int)$item->ID:0;
		
		$indent = ($depth)?str_repeat("\t",$depth):'';
		
		$output .= $indent.'<li>';
		$output .= '<label class="menu-item-title">';
		$output .= '<input type="checkbox" class="menu-item-checkbox';
		
		if (property_exists($item,'label')) {
			$title = $item->label;
		}
		
		$output .= '" name="menu-item['.$possible_object_id.'][menu-item-object-id]" value="'.esc_attr($item->object_id).'" />';
		$output .= isset($title)?esc_html($title):esc_html($item->title);
		$output .= '</label>';
		
		if (empty($item->url)) {
			$item->url = $item->guid;
		}
		
		if (!in_array(array('wpqa-menu','wpqa-'.$item->post_excerpt.'-nav'),$item->classes)) {
			$item->classes[] = 'wpqa-menu';
			$item->classes[] = 'wpqa-'.$item->post_excerpt.'-nav';
		}
		
		// Menu item hidden fields.
		$output .= '<input type="hidden" class="menu-item-db-id" name="menu-item['.$possible_object_id.'][menu-item-db-id]" value="'.$possible_db_id.'" />';
		$output .= '<input type="hidden" class="menu-item-object" name="menu-item['.$possible_object_id.'][menu-item-object]" value="'.esc_attr($item->object).'" />';
		$output .= '<input type="hidden" class="menu-item-parent-id" name="menu-item['.$possible_object_id.'][menu-item-parent-id]" value="'.esc_attr($item->menu_item_parent).'" />';
		$output .= '<input type="hidden" class="menu-item-type" name="menu-item['.$possible_object_id.'][menu-item-type]" value="custom" />';
		$output .= '<input type="hidden" class="menu-item-title" name="menu-item['.$possible_object_id.'][menu-item-title]" value="'.esc_attr($item->title).'" />';
		$output .= '<input type="hidden" class="menu-item-url" name="menu-item['.$possible_object_id.'][menu-item-url]" value="'.esc_attr($item->url).'" />';
		$output .= '<input type="hidden" class="menu-item-target" name="menu-item['.$possible_object_id.'][menu-item-target]" value="'.esc_attr($item->target).'" />';
		$output .= '<input type="hidden" class="menu-item-attr_title" name="menu-item['.$possible_object_id.'][menu-item-attr_title]" value="'.esc_attr($item->attr_title).'" />';
		$output .= '<input type="hidden" class="menu-item-classes" name="menu-item['.$possible_object_id.'][menu-item-classes]" value="'.esc_attr(implode(' ',$item->classes)).'" />';
		$output .= '<input type="hidden" class="menu-item-xfn" name="menu-item['.$possible_object_id.'][menu-item-xfn]" value="'.esc_attr($item->xfn).'" />';
	}
}
// Add Menu Class to FrontEnd
add_filter('nav_menu_css_class','wpqa_menu_class',10,2);
function wpqa_menu_class( $classes, $item ) {
	if (isset($item->url) && $item->url != "" && is_user_logged_in() && ($item->url == "#wpqa-profile" || $item->url == "#wpqa-edit-profile" || $item->url == "#wpqa-password" || $item->url == "#wpqa-privacy" || $item->url == "#wpqa-mail-settings" || $item->url == "#wpqa-delete-account" || $item->url == "#wpqa-transactions" || $item->url == "#wpqa-withdrawals" || $item->url == "#wpqa-financial" || $item->url == "#wpqa-followers" || $item->url == "#wpqa-following" || $item->url == "#wpqa-blocking" || $item->url == "#wpqa-pending-questions" || $item->url == "#wpqa-pending-posts" || $item->url == "#wpqa-notifications" || $item->url == "#wpqa-activities" || $item->url == "#wpqa-referrals" || $item->url == "#wpqa-messages" || $item->url == "#wpqa-points" || $item->url == "#wpqa-questions" || $item->url == "#wpqa-polls" || $item->url == "#wpqa-my_asked" || $item->url == "#wpqa-asked_questions" || $item->url == "#wpqa-asked" || $item->url == "#wpqa-paid_questions" || $item->url == "#wpqa-answers" || $item->url == "#wpqa-best_answers" || $item->url == "#wpqa-groups" || $item->url == "#wpqa-joined_groups" || $item->url == "#wpqa-managed_groups" || $item->url == "#wpqa-followed" || $item->url == "#wpqa-favorites" || $item->url == "#wpqa-posts" || $item->url == "#wpqa-comments" || $item->url == "#wpqa-followers_questions" || $item->url == "#wpqa-followers_answers" || $item->url == "#wpqa-followers_posts" || $item->url == "#wpqa-followers_comments" || $item->url == "#wpqa-logout")) {
		if ($item->url == "#wpqa-profile") {
			$classes[] = 'li-profile';
			if (wpqa_is_home_profile()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-edit-profile") {
			$classes[] = 'li-edit-profile';
			if (wpqa_is_user_edit_profile()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-password") {
			$classes[] = 'li-password';
			if (wpqa_is_user_password_profile()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-privacy") {
			$classes[] = 'li-privacy';
			if (wpqa_is_user_privacy_profile()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-mail-settings") {
			$classes[] = 'li-mail-settings';
			if (wpqa_is_user_mails_profile()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-delete-account") {
			$classes[] = 'li-delete-account';
			if (wpqa_is_user_delete_profile()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-transactions") {
			$classes[] = 'li-transactions';
			if (wpqa_is_user_transactions_profile()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-withdrawals") {
			$classes[] = 'li-withdrawals';
			if (wpqa_is_user_withdrawals_profile()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-financial") {
			$classes[] = 'li-financial';
			if (wpqa_is_user_financial_profile()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-followers") {
			$classes[] = 'li-followers';
			if (wpqa_is_user_followers()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-following") {
			$classes[] = 'li-following';
			if (wpqa_is_user_following()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-blocking") {
			$classes[] = 'li-blocking';
			if (wpqa_is_user_blocking()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-pending-questions") {
			$classes[] = 'li-pending-questions';
			if (wpqa_is_pending_questions()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-pending-posts") {
			$classes[] = 'li-pending-posts';
			if (wpqa_is_pending_posts()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-notifications") {
			$classes[] = 'li-notifications menu-item-has-children';
			if (wpqa_is_user_notifications()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-activities") {
			$classes[] = 'li-activities';
			if (wpqa_is_user_activities()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-referrals") {
			$classes[] = 'li-referrals';
			if (wpqa_is_user_referrals()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-messages") {
			$classes[] = 'li-messages';
			if (wpqa_is_user_messages()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-points") {
			$classes[] = 'li-points';
			if (wpqa_is_user_points()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-questions") {
			$classes[] = 'li-questions';
			if (wpqa_is_user_questions()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-polls") {
			$classes[] = 'li-polls';
			if (wpqa_is_user_polls()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-my_asked") {
			$classes[] = 'li-my-asked';
			if (wpqa_is_my_asked()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-asked_questions") {
			$classes[] = 'li-asked-questions';
			if (wpqa_is_asked_questions()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-asked") {
			$classes[] = 'li-asked';
			if (wpqa_is_user_asked()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-paid_questions") {
			$classes[] = 'li-paid-questions';
			if (wpqa_is_paid_questions()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-answers") {
			$classes[] = 'li-answers';
			if (wpqa_is_user_answers()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-best_answers") {
			$classes[] = 'li-best-answers';
			if (wpqa_is_best_answers()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-groups") {
			$classes[] = 'li-groups';
			if (wpqa_is_user_groups()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-joined_groups") {
			$classes[] = 'li-joined_groups';
			if (wpqa_is_user_joined_groups()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-managed_groups") {
			$classes[] = 'li-managed_groups';
			if (wpqa_is_user_managed_groups()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-followed") {
			$classes[] = 'li-followed';
			if (wpqa_is_user_followed()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-favorites") {
			$classes[] = 'li-favorites';
			if (wpqa_is_user_favorites()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-posts") {
			$classes[] = 'li-posts';
			if (wpqa_is_user_posts()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-comments") {
			$classes[] = 'li-comments';
			if (wpqa_is_user_comments()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-followers_questions") {
			$classes[] = 'li-followers-questions';
			if (wpqa_is_followers_questions()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-followers_answers") {
			$classes[] = 'li-followers-answers';
			if (wpqa_is_followers_answers()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-followers_posts") {
			$classes[] = 'li-followers-posts';
			if (wpqa_is_followers_posts()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-followers_comments") {
			$classes[] = 'li-followers-comments';
			if (wpqa_is_followers_comments()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}else if ($item->url == "#wpqa-logout") {
			$classes[] = 'li-logout';
		}
	}else if (isset($item->url) && $item->url != "" && ($item->url == "#wpqa-subscriptions" || $item->url == "#wpqa-buy-points" || $item->url == "#wpqa-all-questions" || $item->url == "#wpqa-all-groups" || $item->url == "#wpqa-poll" || $item->url == "#wpqa-login" || $item->url == "#wpqa-signup" || $item->url == "#wpqa-lost-password" || $item->url == "#wpqa-add-category" || $item->url == "#wpqa-add-question" || $item->url == "#wpqa-add-poll" || $item->url == "#wpqa-add-group" || $item->url == "#wpqa-add-post" || $item->url == "#wpqa-login-popup" || $item->url == "#wpqa-signup-popup" || $item->url == "#wpqa-lost-password-popup" || $item->url == "#wpqa-add-question-popup" || $item->url == "#wpqa-add-post-popup")) {
		if ($item->url == "#wpqa-poll") {
			$classes[] = 'li-poll';
			if (is_post_type_archive(wpqa_questions_type) && isset($_GET["type"]) && $_GET["type"] == "poll") {
				$classes[] = 'current-menu-item current_page_item';
			}
		}
		if ($item->url == "#wpqa-login") {
			$classes[] = 'li-login';
			if (wpqa_is_login()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}
		if ($item->url == "#wpqa-login-popup") {
			$classes[] = 'li-login-popup login-panel';
		}
		if ($item->url == "#wpqa-signup") {
			$classes[] = 'li-signup';
			if (wpqa_is_signup()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}
		if ($item->url == "#wpqa-signup-popup") {
			$classes[] = 'li-signup-popup signup-panel';
		}
		if ($item->url == "#wpqa-lost-password") {
			$classes[] = 'li-lost-password';
			if (wpqa_is_lost_password()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}
		if ($item->url == "#wpqa-lost-password-popup") {
			$classes[] = 'li-lost-password-popup lost-password';
		}
		if ($item->url == "#wpqa-add-category") {
			$classes[] = 'li-add-category';
			if (wpqa_is_add_category()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}
		if ($item->url == "#wpqa-add-question") {
			$classes[] = 'li-add-question';
			if (wpqa_is_add_questions()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}
		if ($item->url == "#wpqa-add-question-popup") {
			$classes[] = 'li-add-question-popup wpqa-question';
		}
		if ($item->url == "#wpqa-add-poll") {
			$classes[] = 'li-add-poll';
			if (wpqa_is_add_questions()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}
		if ($item->url == "#wpqa-add-group") {
			$classes[] = 'li-add-group';
			if (wpqa_is_add_groups()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}
		if ($item->url == "#wpqa-add-post") {
			$classes[] = 'li-add-post';
			if (wpqa_is_add_posts()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}
		if ($item->url == "#wpqa-add-post-popup") {
			$classes[] = 'li-add-post-popup wpqa-post';
		}
		if ($item->url == "#wpqa-subscriptions") {
			$classes[] = 'li-subscriptions';
			if (wpqa_is_subscriptions()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}
		if ($item->url == "#wpqa-buy-points") {
			$classes[] = 'li-buy-points';
			if (wpqa_is_buy_points()) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}
		if ($item->url == "#wpqa-all-questions") {
			$classes[] = 'li-all-questions';
			if (is_post_type_archive(wpqa_questions_type)) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}
		if ($item->url == "#wpqa-all-groups") {
			$classes[] = 'li-all-groups';
			if (is_post_type_archive("group")) {
				$classes[] = 'current-menu-item current_page_item';
			}
		}
	}

	$classes = apply_filters("wpqa_menu_classes",$classes,$item);

	return $classes;
}
// Menu FrontEnd
add_filter( 'walker_nav_menu_start_el', 'wpqa_walker_nav_menu_start_el', 10, 4 );
function wpqa_walker_nav_menu_start_el( $item_output, $item, $depth, $args ) {
	$wpqa_user_id = get_current_user_id();
	$is_super_admin = is_super_admin($wpqa_user_id);
	// link attributes
	$attributes  = ! empty($item->attr_title)? ' title="'.esc_attr($item->attr_title).'"' : '';
	$attributes .= ! empty($item->target)    ? ' target="'.esc_attr($item->target  ).'"' : '';
	$attributes .= ! empty($item->xfn)       ? ' rel="'  .esc_attr($item->xfn     ).'"' : '';
	$attributes  = apply_filters("wpqa_menu_attributes",$attributes,$item);
	
	if (isset($item->url) && $item->url != "" && is_user_logged_in() && ($item->url == "#wpqa-profile" || $item->url == "#wpqa-edit-profile" || $item->url == "#wpqa-password" || $item->url == "#wpqa-privacy" || $item->url == "#wpqa-mail-settings" || $item->url == "#wpqa-delete-account" || $item->url == "#wpqa-transactions" || $item->url == "#wpqa-withdrawals" || $item->url == "#wpqa-financial" || $item->url == "#wpqa-followers" || $item->url == "#wpqa-following" || $item->url == "#wpqa-blocking" || $item->url == "#wpqa-pending-questions" || $item->url == "#wpqa-pending-posts" || $item->url == "#wpqa-notifications" || $item->url == "#wpqa-activities" || $item->url == "#wpqa-referrals" || $item->url == "#wpqa-messages" || $item->url == "#wpqa-points" || $item->url == "#wpqa-polls" || $item->url == "#wpqa-questions" || $item->url == "#wpqa-my_asked" || $item->url == "#wpqa-asked_questions" || $item->url == "#wpqa-asked" || $item->url == "#wpqa-paid_questions" || $item->url == "#wpqa-answers" || $item->url == "#wpqa-best_answers" || $item->url == "#wpqa-groups" || $item->url == "#wpqa-joined_groups" || $item->url == "#wpqa-managed_groups" || $item->url == "#wpqa-followed" || $item->url == "#wpqa-favorites" || $item->url == "#wpqa-posts" || $item->url == "#wpqa-comments" || $item->url == "#wpqa-followers_questions" || $item->url == "#wpqa-followers_answers" || $item->url == "#wpqa-followers_posts" || $item->url == "#wpqa-followers_comments" || $item->url == "#wpqa-logout")) {
		if ($item->url == "#wpqa-profile") {
			$attributes .= ' href="'.esc_url(wpqa_profile_url($wpqa_user_id)).'"';
		}else if ($item->url == "#wpqa-edit-profile") {
			$attributes .= ' href="'.esc_url(wpqa_edit_profile_permalink()).'"';
		}else if ($item->url == "#wpqa-password") {
			$attributes .= ' href="'.esc_url(wpqa_edit_password_permalink()).'"';
		}else if ($item->url == "#wpqa-privacy") {
			$attributes .= ' href="'.esc_url(wpqa_edit_privacy_permalink()).'"';
		}else if ($item->url == "#wpqa-financial") {
			$attributes .= ' href="'.esc_url(wpqa_financial_permalink()).'"';
		}else if ($item->url == "#wpqa-withdrawals") {
			$attributes .= ' href="'.esc_url(wpqa_withdrawals_permalink()).'"';
		}else if ($item->url == "#wpqa-transactions") {
			$attributes .= ' href="'.esc_url(wpqa_transactions_permalink()).'"';
		}else if ($item->url == "#wpqa-mail-settings") {
			$attributes .= ' href="'.esc_url(wpqa_edit_mails_permalink()).'"';
		}else if ($item->url == "#wpqa-delete-account") {
			$attributes .= ' href="'.esc_url(wpqa_edit_delete_permalink()).'"';
		}else if ($item->url == "#wpqa-blocking") {
			$attributes .= ' href="'.esc_url(wpqa_blocking_permalink()).'"';
		}else if ($item->url == "#wpqa-pending-questions") {
			$attributes .= ' href="'.esc_url(wpqa_pending_questions_permalink()).'"';
		}else if ($item->url == "#wpqa-pending-posts") {
			$attributes .= ' href="'.esc_url(wpqa_pending_posts_permalink()).'"';
		}else if ($item->url == "#wpqa-notifications") {
			$attributes .= ' href="'.esc_url(wpqa_notifications_permalink()).'"';
		}else if ($item->url == "#wpqa-activities") {
			$attributes .= ' href="'.esc_url(wpqa_activities_permalink()).'"';
		}else if ($item->url == "#wpqa-referrals") {
			$attributes .= ' href="'.esc_url(wpqa_referrals_permalink()).'"';
		}else if ($item->url == "#wpqa-messages") {
			$attributes .= ' href="'.esc_url(wpqa_messages_permalink()).'"';
		}else if ($item->url == "#wpqa-followers") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"followers")).'"';
		}else if ($item->url == "#wpqa-following") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"following")).'"';
		}else if ($item->url == "#wpqa-points") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"points")).'"';
		}else if ($item->url == "#wpqa-questions") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"questions")).'"';
		}else if ($item->url == "#wpqa-polls") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"polls")).'"';
		}else if ($item->url == "#wpqa-my_asked") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"my_asked")).'"';
		}else if ($item->url == "#wpqa-asked_questions") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"asked_questions")).'"';
		}else if ($item->url == "#wpqa-asked") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"asked")).'"';
		}else if ($item->url == "#wpqa-paid_questions") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"paid_questions")).'"';
		}else if ($item->url == "#wpqa-answers") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"answers")).'"';
		}else if ($item->url == "#wpqa-best_answers") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"best_answers")).'"';
		}else if ($item->url == "#wpqa-groups") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"groups")).'"';
		}else if ($item->url == "#wpqa-joined_groups") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"joined_groups")).'"';
		}else if ($item->url == "#wpqa-managed_groups") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"managed_groups")).'"';
		}else if ($item->url == "#wpqa-followed") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"followed")).'"';
		}else if ($item->url == "#wpqa-favorites") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"favorites")).'"';
		}else if ($item->url == "#wpqa-posts") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"posts")).'"';
		}else if ($item->url == "#wpqa-comments") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"comments")).'"';
		}else if ($item->url == "#wpqa-followers_questions") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"followers_questions")).'"';
		}else if ($item->url == "#wpqa-followers_answers") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"followers_answers")).'"';
		}else if ($item->url == "#wpqa-followers_posts") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"followers_posts")).'"';
		}else if ($item->url == "#wpqa-followers_comments") {
			$attributes .= ' href="'.esc_url(wpqa_get_profile_permalink($wpqa_user_id,"followers_comments")).'"';
		}else if ($item->url == "#wpqa-logout") {
			$attributes .= ' href="'.wpqa_get_logout().'"';
		}
	}else if (isset($item->url) && $item->url != "" && ($item->url == "#wpqa-subscriptions" || $item->url == "#wpqa-buy-points" || $item->url == "#wpqa-all-questions" || $item->url == "#wpqa-poll" || $item->url == "#wpqa-all-groups" || $item->url == "#wpqa-login" || $item->url == "#wpqa-signup" || $item->url == "#wpqa-lost-password" || $item->url == "#wpqa-add-category" || $item->url == "#wpqa-add-question" || $item->url == "#wpqa-add-poll" || $item->url == "#wpqa-add-group" || $item->url == "#wpqa-add-post" || $item->url == "#wpqa-login-popup" || $item->url == "#wpqa-signup-popup" || $item->url == "#wpqa-lost-password-popup" || $item->url == "#wpqa-add-question-popup" || $item->url == "#wpqa-add-post-popup")) {
		if ($item->url == "#wpqa-all-questions") {
			$attributes .= ' href="'.esc_url_raw(get_post_type_archive_link(wpqa_questions_type)).'"';
		}else if ($item->url == "#wpqa-poll") {
			$attributes .= ' href="'.esc_url_raw(add_query_arg(array("type" => "poll"),get_post_type_archive_link(wpqa_questions_type))).'"';
		}else if ($item->url == "#wpqa-all-groups") {
			$attributes .= ' href="'.esc_url_raw(get_post_type_archive_link("group")).'"';
		}else if ($item->url == "#wpqa-login" || $item->url == "#wpqa-login-popup") {
			$activate_login = wpqa_options("activate_login");
			if ($activate_login != 'disabled') {
				$attributes .= ' href="'.wpqa_login_permalink().'"';
			}
		}else if ($item->url == "#wpqa-signup" || $item->url == "#wpqa-signup-popup") {
			$activate_register = wpqa_options("activate_register");
			if ($activate_register != 'disabled') {
				$attributes .= ' href="'.wpqa_signup_permalink().'"';
			}
		}else if ($item->url == "#wpqa-lost-password" || $item->url == "#wpqa-lost-password-popup") {
			$attributes .= ' href="'.wpqa_lost_password_permalink().'"';
		}else if ($item->url == "#wpqa-add-category") {
			$attributes .= ' href="'.wpqa_add_category_permalink().'"';
		}else if ($item->url == "#wpqa-add-question" || $item->url == "#wpqa-add-question-popup") {
			$attributes .= ' href="'.wpqa_add_question_permalink().'"';
		}else if ($item->url == "#wpqa-add-poll") {
			$attributes .= ' href="'.esc_url_raw(add_query_arg(array("type" => "poll"),wpqa_add_question_permalink())).'"';
		}else if ($item->url == "#wpqa-add-group") {
			$attributes .= ' href="'.wpqa_add_group_permalink().'"';
		}else if ($item->url == "#wpqa-add-post" || $item->url == "#wpqa-add-post-popup") {
			$attributes .= ' href="'.wpqa_add_post_permalink().'"';
		}else if ($item->url == "#wpqa-subscriptions") {
			$attributes .= ' href="'.wpqa_subscriptions_permalink().'"';
		}else if ($item->url == "#wpqa-buy-points") {
			$attributes .= ' href="'.wpqa_buy_points_permalink().'"';
		}
	}else {
		$attributes .= ' href="'.esc_url($item->url).'"';
	}
	
	$item_output = '';
	if (isset($args->before)) {
		$item_output = $args->before;
	}
	
	$item_output .= '<a class="" '.$attributes.'>';
	$item_output .= apply_filters('wpqa_menu_title',$item->title,$item,$args->theme_location);
	$item_output .= '</a>';
	if ($item->url == "#wpqa-notifications") {
		$notifications_number = wpqa_options("notifications_number");
		$item_output .= wpqa_get_notifications($wpqa_user_id,$notifications_number,"on",false,true,'sub-menu list-unstyled mb-0');
	}
	
	if (isset($args->after)) {
		$item_output .= $args->after;
	}
	
	return $item_output;
}
add_filter("wp_nav_menu_items","wpqa_wp_nav_menu_items",1,2);
function wpqa_wp_nav_menu_items($items,$args) {
	if (is_user_logged_in() && (isset($args->theme_location) && strpos($args->theme_location,'_explore_login') !== false)) {
		$custom_left_menu = wpqa_options("custom_left_menu");
		if ($custom_left_menu == "on") {
			$user_id = get_current_user_id();
			$categories_left_menu = get_the_author_meta("categories_left_menu",$user_id);
			$prefix = '<%wpqa%>';
			$inserted = array($prefix);
			$tax_filter = apply_filters("wpqa_before_question_category",false);
			$tax_question = apply_filters("wpqa_question_category",wpqa_question_categories);
			$category_id = (is_tax(wpqa_question_categories) || $tax_filter == true?(int)get_query_var('wpqa_term_id'):0);
			if (is_array($categories_left_menu) && !empty($categories_left_menu)) {
				foreach ($categories_left_menu as $key => $value) {
					$cat_id = (isset($value["value"]) && $value["value"] != ""?(int)$value["value"]:0);
					if ($cat_id > 0) {
						$get_term = get_term($cat_id,$tax_question);
						if (isset($get_term->name)) {
							$category_icon = get_term_meta($cat_id,prefix_terms."category_icon",true);
							$inserted[] = '<li class="'.($category_id == $cat_id?"current-menu-item ":"").'menu-item menu-item-type-taxonomy menu-item-object-'.$tax_question.' menu-cat-'.$cat_id.'"><a href="'.get_term_link($get_term).'"><i class="'.($category_icon != ""?$category_icon:"icon-folder").'"></i>'.$get_term->name.'</a></li>';
						}
					}
				}
				$left_menu_category_after = wpqa_options("left_menu_category_after");
				$left_menu_category_after = (int)($left_menu_category_after != ""?$left_menu_category_after:2);
				$items = str_replace('</li>','</li>'.$prefix,$items);
				$items = explode($prefix,$items);
				$inserted = array_merge($inserted,array($prefix));
				array_splice($items,$left_menu_category_after,0,$inserted);
				$items = str_replace($prefix,'',$items);
				$items = implode('</li>',$items);
			}
		}
	}
	return $items;
}
/* Custom menu fields */
function wpqa_menu_custom_fields($item_id,$item) {
	wp_nonce_field('menu_meta_icon_nonce','_menu_meta_icon_nonce_name');
	$menu_meta_icon = get_post_meta($item_id,'_menu_meta_icon',true)?>
	<input type="hidden" name="menu-meta-icon-nonce" value="<?php echo wp_create_nonce('menu-meta-icon-name')?>">
	<div class="field-menu_meta_icon description-wide" style="margin: 5px 0;">
		<p class="field-menu-meta-icon description description-thin">
			<label for="menu-meta-icon-<?php echo esc_attr($item_id)?>">
				<?php esc_html_e("Icon class",'wpqa')?><br>
				<input type="text" id="menu-meta-icon-<?php echo esc_attr($item_id)?>" class="widefat code menu-meta-icon" name="menu_meta_icon[<?php echo esc_attr($item_id)?>]" value="<?php echo esc_attr($menu_meta_icon)?>">
			</label>
		</p>
	</div>
<?php }
add_action('wp_nav_menu_item_custom_fields','wpqa_menu_custom_fields',10,2);
/* Save menu fields */
function wpqa_nav_update($menu_id,$menu_item_id) {
	if (!isset($_POST['_menu_meta_icon_nonce_name']) || !wp_verify_nonce($_POST['_menu_meta_icon_nonce_name'],'menu_meta_icon_nonce')) {
		return $menu_id;
	}
	if (isset($_POST['menu_meta_icon'][$menu_item_id])) {
		$menu_meta_icon = sanitize_text_field($_POST['menu_meta_icon'][$menu_item_id]);
		update_post_meta($menu_item_id,'_menu_meta_icon',$menu_meta_icon);
	}else {
		delete_post_meta($menu_item_id,'_menu_meta_icon');
	}
}
add_action('wp_update_nav_menu_item','wpqa_nav_update',10,2);
/* Show menu title */
if (!is_admin()) {
	function wpqa_custom_menu_title($title,$item,$theme_location) {
		if (isset($item->ID)) {
			$menu_meta_icon = get_post_meta($item->ID,'_menu_meta_icon',true);
			if (!empty($menu_meta_icon)) {
				$title = '<i class="'.$menu_meta_icon.'"></i>'.$title;
			}else {
				$title = apply_filters("wpqa_menu_title_filter",$title,$item,$theme_location);
			}
		}
		return $title;
	}
	add_filter('wpqa_menu_title','wpqa_custom_menu_title',10,3);
}
/* Show WPQA menu by default */
add_action('current_screen','wpqa_hidden_meta_boxes',10,2);
if (!function_exists('wpqa_hidden_meta_boxes')) :
	function wpqa_hidden_meta_boxes($screen) {
		if ($screen->id == "nav-menus") {
			$user_id = get_current_user_id();
			$show_nav_menu = get_user_meta($user_id,"wpqa_show_nav_menu",true);
			if ($show_nav_menu != "done") {
				$hidden = get_user_option("metaboxhidden_nav-menus");
				if (is_array($hidden) && !empty($hidden)) {
					foreach ($hidden as $key => $value) {
						if ($value == "wpqa-nav-menu") {
							unset($hidden[$key]);
							update_user_option($user_id,"metaboxhidden_nav-menus",$hidden);
							update_user_meta($user_id,"wpqa_show_nav_menu","done");
						}
					}
				}
			}
		}
	}
endif;?>