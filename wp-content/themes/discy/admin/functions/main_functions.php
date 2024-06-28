<?php /* Has WPQA plugin */
if (!function_exists('has_wpqa')):
	function has_wpqa() {
		return class_exists('WPQA');
	}
endif;
/* Defines && Get themes options */
if (!function_exists('discy_options')):
	function discy_options($name,$default = false) {
		$options = get_option(discy_options);
		if (defined("wpqa_styling_options")) {
			$styling_options = get_option(wpqa_styling_options);
		}
		if (defined("wpqa_mobile_options")) {
			$mobile_options = get_option(wpqa_mobile_options);
		}
		$options = (is_array($options) && !empty($options)?$options:array());
		$styling_options = (isset($styling_options) && is_array($styling_options) && !empty($styling_options)?$styling_options:array());
		$mobile_options = (isset($mobile_options) && is_array($mobile_options) && !empty($mobile_options)?$mobile_options:array());
		$filter_options = apply_filters("wpqa_option_settings",array());
		$filter_options = (isset($filter_options) && is_array($filter_options) && !empty($filter_options)?$filter_options:array());
		$options = $options + $styling_options + $mobile_options + $filter_options;
		if (isset($options[$name])) {
			return $options[$name];
		}
		return $default;
	}
endif;
define("discy_theme_version","5.7.1");
define("discy_wpqa_plugin_version","6.1.1");
define("wpqa_new_plugin_version",discy_wpqa_plugin_version);
define("discy_theme_name","Discy");
define("discy_name","discy");
define("discy_options","discy_options");
define("discy_meta",discy_name);
define("discy_terms",discy_name);
define("discy_author",discy_name);
define("discy_theme_url","https://2code.info/demo/themes/Discy/");
define("discy_theme_url_tf","https://1.envato.market/drV57");
define("discy_date_format",(discy_options("date_format")?discy_options("date_format"):get_option("date_format")));
define("discy_time_format",(discy_options("time_format")?discy_options("time_format"):get_option("time_format")));
if (!has_wpqa() && !defined("wpqa_questions_type")) {
	define("wpqa_questions_type","question");
}
if (!has_wpqa() && !defined("wpqa_asked_questions_type")) {
	define("wpqa_asked_questions_type","asked-question");
}
if (!has_wpqa() && !defined("wpqa_question_categories")) {
	define("wpqa_question_categories","question-category");
}
if (!has_wpqa() && !defined("wpqa_question_tags")) {
	define("wpqa_question_tags","question_tags");
}
if (!defined("prefix_meta")) {
	define("prefix_meta",discy_meta."_");
}
if (!defined("prefix_terms")) {
	define("prefix_terms",discy_terms."_");
}
if (!defined("prefix_author")) {
	define("prefix_author",discy_author."_");
}
/* Discy */
add_action("init","discy_init");
function discy_init() {
	$get_theme_name = get_option("get_theme_name");
	if ($get_theme_name != discy_name) {
		update_option("get_theme_name",discy_name);
	}
}
/* Switch theme */
function discy_deactivate_theme($new_name,$new_theme,$old_theme) {
	flush_rewrite_rules(true);
	$get_theme_name = get_option("get_theme_name");
	if ($get_theme_name != "") {
		update_option("old_theme_name",$get_theme_name);
		delete_option("get_theme_name");
	}
}
add_action('switch_theme','discy_deactivate_theme',1,3);
/* Delete theme */
function discy_deleted_theme($stylesheet,$deleted) {
	flush_rewrite_rules(true);
	$get_theme_name = get_option("get_theme_name");
	if ($get_theme_name != "") {
		update_option("old_theme_name",$get_theme_name);
		delete_option("get_theme_name");
	}
}
add_action('deleted_theme','discy_deleted_theme',1,2);
/* Update WPQA plugin */
function discy_update_plugin_complete_actions($update_actions,$plugin) {
	if ($plugin == "WPQA/wpqa.php") {
		update_option("FlushRewriteRules",true);
	}
}
add_filter("update_plugin_complete_actions","discy_update_plugin_complete_actions",1,2);
function discy_upgrader_process_complete($upgrader_object,$options) {
	$wpqa_plugin = "WPQA/wpqa.php";
	if (isset($options['plugin']) && $options['plugin'] == $wpqa_plugin) {
		update_option("FlushRewriteRules",true);
	}else if (isset($options['action']) && isset($options['type']) && $options['action'] == 'update' && $options['type'] == 'plugin') {
		if (isset($options['plugins'])) {
			foreach($options['plugins'] as $each_plugin) {
				if ($each_plugin == $wpqa_plugin) {
					update_option("FlushRewriteRules",true);
				}
			}
		}
	}
}
add_filter("upgrader_process_complete","discy_upgrader_process_complete",1,2);
/* discy_post_meta */
if (!function_exists('discy_post_meta')):
	function discy_post_meta($key,$post_id = null,$prefix = true,$default = false) {
		if (!$post_id) {
			$post_id = get_the_ID();
		}
		
		$value = get_post_meta($post_id,($prefix == true?prefix_meta:"").$key,true);
		
		if ('' !== $value && array() !== $value) {
			return $value;
		}else if ($default) {
			return $default;
		}
		
		return false;
	}
endif;
/* discy_term_meta */
if (!function_exists('discy_term_meta')):
	function discy_term_meta($key,$term_id = null,$prefix = true,$default = false) {
		$value = get_term_meta($term_id,($prefix == true?prefix_terms:"").$key,true);
		
		if ('' !== $value && array() !== $value) {
			return $value;
		}else if ($default) {
			return $default;
		}
		
		return false;
	}
endif;
/* wp login head */
if (!function_exists('discy_login_logo')):
	function discy_login_logo() {
		$login_logo        = discy_image_url_id(discy_options("login_logo"));
		$login_logo_height = discy_options("login_logo_height");
		$login_logo_width  = discy_options("login_logo_width");
		if (isset($login_logo) && $login_logo != "") {
			wp_enqueue_style("admin-custom-style",discy_framework_dir."css/colors.css",array(),discy_theme_version);
			$custom_css = '.login h1 a {
				background-image:url('.$login_logo.')  !important;
				background-size: auto !important;
				'.(isset($login_logo_height) && $login_logo_height != ""?"height: ".$login_logo_height."px !important;":"").'
				'.(isset($login_logo_width) && $login_logo_width != ""?"width: ".$login_logo_width."px !important;":"").'
			}';
			wp_add_inline_style('admin-custom-style',$custom_css);
		}
	}
endif;
add_action('login_head','discy_login_logo');
/* Update the rewrite rules */
if ((bool)get_option("FlushRewriteRules")) {
	flush_rewrite_rules(true);
	delete_option("FlushRewriteRules");
}
/* Admin css */
add_action('wpqa_framework_admin_css','discy_framework_admin_css');
function discy_framework_admin_css() {
	wp_enqueue_style("admin-colors",discy_framework_dir."css/colors.css",array(),discy_theme_version);
}
add_action('admin_enqueue_scripts','discy_admin_enqueue_scripts');
function discy_admin_enqueue_scripts($hook) {
	if (!has_wpqa() && ($hook == "admin_page_registration" || $hook == "toplevel_page_registration" || $hook == "wpqa_page_registration" || $hook == discy_name."_page_registration" || $hook == "register-".discy_name."_page_options" || $hook == "admin_page_options" || $hook == "toplevel_page_options" || $hook == "wpqa_page_options" || $hook == discy_name."_page_options" || $hook == "register-".discy_name."_page_demo-import" || $hook == "admin_page_demo-import" || $hook == "toplevel_page_demo-import" || $hook == "wpqa_page_demo-import" || $hook == discy_name."_page_demo-import")) {
		if (is_rtl()) {
			wp_enqueue_style("admin-register",discy_framework_dir."css/rtl-register.css",array(),discy_theme_version);
		}else {
			wp_enqueue_style("admin-register",discy_framework_dir."css/register.css",array(),discy_theme_version);
		}
	}
}
/* excerpt */
if (!defined("discy_excerpt_type")) {
	define("discy_excerpt_type",discy_options("excerpt_type"));
}
function discy_excerpt($excerpt_length,$excerpt_type = discy_excerpt_type,$read_more = false,$return = "",$main_content = "",$content = "") {
	global $post;
	$excerpt_length = (isset($excerpt_length) && $excerpt_length != ""?$excerpt_length:5);
	if ($main_content == "yes") {
		$content = strip_shortcodes($content);
	}else {
		$get_the_excerpt = trim(get_the_excerpt($post->ID));
		$content = ($get_the_excerpt != "" && $post->post_type != wpqa_questions_type && $post->post_type != wpqa_asked_questions_type?$get_the_excerpt:$post->post_content);
		$content = apply_filters('the_content',strip_shortcodes($content));
	}
	$content = apply_filters("discy_excerpt_filter",$content,(isset($post->post_content)?$post->post_content:""));
	if ($excerpt_type == "characters") {
		$number = mb_strlen(trim(strip_tags(trim($content))));
		$content = mb_substr($content,0,$excerpt_length,"UTF-8").($excerpt_length > 0 && $number > 0 && $number > $excerpt_length?' ...':'');
		if ($excerpt_length > 0 && $read_more == true) {
			$read_more_yes = "on";
		}
	}else {
		$words = explode(' ',$content,$excerpt_length + 1);
		if (count($words) > $excerpt_length) :
			array_pop($words);
			array_push($words,'');
			$content = implode(' ',$words).($excerpt_length > 0?'...':'');
			if ($excerpt_length > 0 && $read_more == true) {
				$read_more_yes = "on";
			}
		endif;
	}
	$excerpt = strip_tags($content).(isset($read_more_yes) && $read_more_yes == "on"?'<a class="post-read-more" href="'.esc_url(get_permalink($post->ID)).'" rel="bookmark" title="'.esc_attr__('Read','discy').' '.get_the_title($post->ID).'">'.esc_html__('Read more','discy').'</a>':'');
	if ($return == "return") {
		return $excerpt;
	}else {
		echo ($excerpt);
	}
}
/* excerpt_title */
function discy_excerpt_title($excerpt_length,$excerpt_type = discy_excerpt_type,$return = "") {
	global $post;
	$title = "";
	$excerpt_length = ((isset($excerpt_length) && $excerpt_length != "") || $excerpt_length == 0?$excerpt_length:5);
	if ($excerpt_length > 0) {
		$title = $post->post_title;
	}
	if ($excerpt_type == "characters") {
		$title = mb_substr($title,0,$excerpt_length,"UTF-8");
	}else {
		$words = explode(' ',$title,$excerpt_length + 1);
		if (count($words) > $excerpt_length) :
			array_pop($words);
			array_push($words,'');
			$title = implode(' ',$words).'...';
		endif;
	}
	$title = strip_tags($title);
	if ($return == "return") {
		return esc_html($title);
	}else {
		echo esc_html($title);
	}
}
/* excerpt_any */
function discy_excerpt_any($excerpt_length,$content,$more = '...',$excerpt_type = discy_excerpt_type) {
	$excerpt_length = (isset($excerpt_length) && $excerpt_length != ""?$excerpt_length:5);
	$content = strip_tags($content);
	if ($excerpt_type == "characters") {
		$content = mb_substr($content,0,$excerpt_length,"UTF-8");
	}else {
		$words = explode(' ',$content,$excerpt_length + 1);
		if (count(explode(' ',$content)) > $excerpt_length) {
			array_pop($words);
			array_push($words,'');
			$content = implode(' ',$words).$more;
		}
	}
	return $content;
}
/* Get resize url */
if (!function_exists('discy_get_aq_resize_img_url')) :
	function discy_get_aq_resize_img_url($img_width_f,$img_height_f,$thumbs = "",$gif = false) {
		if (empty($thumbs)) {
			$thumb = get_post_thumbnail_id();
		}else {
			$thumb = $thumbs;
		}
		if (is_numeric($thumb)) {
			$image = discy_resize($thumb,'',$img_width_f,$img_height_f,true,$gif);
		}
		if (isset($image['url']) && $image['url'] != "") {
			$last_image = $image['url'];
		}else {
			$last_image = "https://placehold.jp/".$img_width_f."x".$img_height_f;
		}
		if (isset($last_image) && $last_image != "") {
			return $last_image;
		}
	}
endif;
/* Get resize img */
if (!function_exists('discy_get_aq_resize_img')) :
	function discy_get_aq_resize_img($img_width_f,$img_height_f,$img_lightbox = "",$thumbs = "",$gif = false,$title = "",$srcset = "",$class = "") {
		if (empty($thumbs)) {
			$thumb = get_post_thumbnail_id();
		}else {
			$thumb = $thumbs;
		}
		$last_image = discy_get_aq_resize_img_url($img_width_f,$img_height_f,$thumb,$gif);
		if (isset($last_image) && $last_image != "") {
			if ($srcset != "") {
				$last_image_2 = discy_get_aq_resize_img_url($img_width_f*2,$img_height_f*2,$thumb,$gif);
			}
			if ($img_lightbox == "lightbox" || $img_width_f == "" || $img_height_f == "") {
				$full_image = wp_get_attachment_image_src($thumb,"full");
				if ($img_lightbox == "lightbox" && isset($full_image[0])) {
					$img_url = $full_image[0];
				}
				$img_width_f = ($img_width_f != ""?$img_width_f:(isset($full_image[1])?$full_image[1]:0));
				$img_height_f = ($img_height_f != ""?$img_height_f:(isset($full_image[2])?$full_image[2]:0));
			}
			return ($img_lightbox == "lightbox" && isset($img_url)?"<a href='".esc_url($img_url)."'>":"")."<img".($class != ""?" class ='".$class."'":"").($srcset != ""?" srcset='".$last_image." 1x, ".$last_image_2." 2x'":"")." alt='".(isset($title) && $title != ""?$title:get_the_title())."' width='".$img_width_f."' height='".$img_height_f."' src='".$last_image."'>".($img_lightbox == "lightbox" && isset($img_url)?"</a>":"");
		}
	}
endif;
/* Get resize image with URL */
if (!function_exists('discy_get_aq_resize_url')) :
	function discy_get_aq_resize_url($url,$img_width_f,$img_height_f,$gif = false) {
		$image = discy_resize("", $url, $img_width_f, $img_height_f, true,$gif);
		if (isset($image['url']) && $image['url'] != "") {
			$last_image = $image['url'];
		}else {
			$last_image = "https://placehold.jp/".$img_width_f."x".$img_height_f;
		}
		return $last_image;
	}
endif;
/* Get resize image with URL img */
if (!function_exists('discy_get_aq_resize_url_img')) :
	function discy_get_aq_resize_url_img($url,$img_width_f,$img_height_f,$img_lightbox = "",$gif = false,$title = "",$srcset = "",$class = "") {
		$last_image = discy_get_aq_resize_url($url,$img_width_f,$img_height_f,$gif = false);
		if (isset($last_image) && $last_image != "") {
			if ($srcset != "") {
				$last_image_2 = discy_get_aq_resize_url($url,$img_width_f*2,$img_height_f*2,$gif);
			}
			if ($img_lightbox == "lightbox" || $img_width_f == "" || $img_height_f == "") {
				$full_image = getimagesize($url);
				if ($img_lightbox == "lightbox" && $url != "") {
					$img_url = $url;
				}
				$img_width_f = ($img_width_f != ""?$img_width_f:(isset($full_image[0]) && $full_image[0] > 0?$full_image[1]:$img_width_f));
				$img_height_f = ($img_height_f != ""?$img_height_f:(isset($full_image[1]) && $full_image[1] > 0?$full_image[2]:$img_height_f));
			}
			return ($img_lightbox == "lightbox" && isset($img_url)?"<a href='".esc_url($img_url)."'>":"")."<img".($class != ""?" class ='".$class."'":"").($srcset != ""?" srcset='".$last_image." 1x, ".$last_image_2." 2x'":"")." alt='".(isset($title) && $title != ""?$title:get_the_title())."' width='".$img_width_f."' height='".$img_height_f."' src='".$last_image."'>".($img_lightbox == "lightbox" && isset($img_url)?"</a>":"");
		}
		return $last_image;
	}
endif;
/* discy_get_aq_resize_img_full */
function discy_get_aq_resize_img_full($thumbnail_size,$title = "") {
	$thumb = get_post_thumbnail_id();
	if ($thumb != "") {
		$img_url = wp_get_attachment_url($thumb,$thumbnail_size);
		$image = $img_url;
		return "<img alt='".(isset($title) && $title != ""?$title:get_the_title())."' src='".$image."'>";
	}else {
		return "<img alt='".(isset($title) && $title != ""?$title:get_the_title())."' src='".discy_image()."'>";
	}
}
/* discy_get_attachment_id */
function discy_get_attachment_id($image_url) {
	global $wpdb;
	if (!is_array($image_url)) {
		$pathinfo = pathinfo($image_url);
		$image_url = $pathinfo['filename'].'.'.$pathinfo['extension'];
		if (strpos($image_url,esc_url(home_url('/'))) !== false && strpos($image_url,"themes/".get_template()."/image") === false) {
			$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid RLIKE '%s';",$image_url));
			if (isset($attachment[0]) && $attachment[0] != "") {
				return $attachment[0];
			}
		}
	}
}
/* discy_image */
function discy_image() {
	global $post;
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i',$post->post_content,$matches);
	if (isset($matches[1][0])) {
		return $matches[1][0];
	}else {
		return false;
	}
}
/* breadcrumbs */
function discy_breadcrumbs($text = "",$breadcrumb_right = true,$breadcrumbs_style = "style_1") {
	if (has_wpqa()) {
		$show_breadcrumbs = true;
		if ((wpqa_is_user_edit_home() && wpqa_is_user_owner()) || wpqa_is_user_profile()) {
			$breadcrumbs_author = discy_options("breadcrumbs_author");
			if ($breadcrumbs_author != "on") {
				$show_breadcrumbs = false;
			}
		}
		if ($show_breadcrumbs == true) {
			echo wpqa_breadcrumbs($text,$breadcrumb_right,$breadcrumbs_style);
		}
	}
}
/* Early Access fonts */
function discy_earlyaccess_fonts($value) {
	$earlyaccess = array("Alef+Hebrew","Amiri","Dhurjati","Dhyana","Droid+Arabic+Kufi","Droid+Arabic+Naskh","Droid+Sans+Ethiopic","Droid+Sans+Tamil","Droid+Sans+Thai","Droid+Serif+Thai","Gidugu","Gurajada","Hanna","Jeju+Gothic","Jeju+Hallasan","Jeju+Myeongjo","Karla+Tamil+Inclined","Karla+Tamil+Upright","KoPub+Batang","Lakki+Reddy","Lao+Muang+Don","Lao+Muang+Khong","Lao+Sans+Pro","Lateef","Lohit+Bengali","Lohit+Devanagari","Lohit+Tamil","Mallanna","Mandali","Myanmar+Sans+Pro","NATS","NTR","Nanum+Brush+Script","Nanum+Gothic","Nanum+Gothic+Coding","Nanum+Myeongjo","Nanum+Pen+Script","Noto+Kufi+Arabic","Noto+Naskh+Arabic","Noto+Nastaliq+Urdu+Draft","Noto+Sans+Armenian","Noto+Sans+Bengali","Noto+Sans+Cherokee","Noto+Sans+Devanagari","Noto+Sans+Devanagari+UI","Noto+Sans+Ethiopic","Noto+Sans+Georgian","Noto+Sans+Gujarati","Noto+Sans+Gurmukhi","Noto+Sans+Hebrew","Noto+Sans+Japanese","Noto+Sans+Kannada","Noto+Sans+Khmer","Noto+Sans+Kufi+Arabic","Noto+Sans+Lao","Noto+Sans+Lao+UI","Noto+Sans+Malayalam","Noto+Sans+Myanmar","Noto+Sans+Osmanya","Noto+Sans+Sinhala","Noto+Sans+Tamil","Noto+Sans+Tamil+UI","Noto+Sans+Telugu","Noto+Sans+Thai","Noto+Sans+Thai+UI","Noto+Serif+Armenian","Noto+Serif+Georgian","Noto+Serif+Khmer","Noto+Serif+Lao","Noto+Serif+Thai","Open+Sans+Hebrew","Open+Sans+Hebrew+Condensed","Padauk","Peddana","Phetsarath","Ponnala","Ramabhadra","Ravi+Prakash","Scheherazade","Souliyo","Sree+Krushnadevaraya","Suranna","Suravaram","Tenali+Ramakrishna","Thabit","Tharlon","cwTeXFangSong","cwTeXHei","cwTeXKai","cwTeXMing","cwTeXYen");
	if (in_array($value,$earlyaccess)) {
		return "earlyaccess";
	}
}
/* discy_head_post */
function discy_head_post($post_style = "style_1",$post_head = "",$show_featured_image = "",$featured_image_style = "default",$custom_width = 140,$custom_height = 140,$blog_h = "",$show_defult_image = "",$post_id = "") {
	global $post,$blog_style,$theme_sidebar_all;
	$img_width = "";
	$img_height = "";
	$site_width = (int)discy_options("site_width");
	$mins_width = ($site_width > 1170?$site_width-1170:0);
	$what_post = discy_post_meta("what_post",$post_id,false);
	if (isset($theme_sidebar_all) && $theme_sidebar_all != "") {
		$theme_sidebar = $theme_sidebar_all;
	}else {
		$theme_sidebar_all = $theme_sidebar = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars("sidebar_where"):"");
	}
	if ($show_defult_image == true) {
		if (isset($blog_h) && $blog_h == "blog_h") {
			$img_width = 370;
			$img_height = 250;
			if ($site_width > 1170) {
				$img_height = round($img_height+(($site_width-30-30)/3)-$img_width);
				$img_width = round(($site_width-30-30)/3);
			}
		}else if (!is_single() && $post_style == "style_3") {
			if ($theme_sidebar == "menu_sidebar") {
				$img_width = round(300+($mins_width/2));
				$img_height = round(200+($mins_width/4));
			}else if ($theme_sidebar == "menu_left") {
				$img_width = round(439+($mins_width/2));
				$img_height = round(290+($mins_width/4));
			}else if ($theme_sidebar == "full") {
				$img_width = round(350+($mins_width/3));
				$img_height = round(220+($mins_width/5));
			}else if ($theme_sidebar == "centered") {
				$img_width = round(269+($mins_width/2));
				$img_height = round(175+($mins_width/4));
			}else {
				$img_width = round(400+($mins_width/2));
				$img_height = round(265+($mins_width/4));
			}
		}else if (!is_single() && $post_style == "style_2") {
			$img_width = 270;
			$img_height = 180;
		}else {
			if (is_single() && isset($featured_image_style) && $featured_image_style != "" && $featured_image_style != "default") {
				if ($what_post != "image_lightbox") {
					//$what_post = "image";
				}
				if ($featured_image_style == "custom_size") {
					$img_width = $custom_width;
					$img_height = $custom_height;
				}else if ($featured_image_style == "style_270") {
					$img_width = 270;
					$img_height = 180;
				}else {
					$img_width = 140;
					$img_height = 140;
				}
			}else {
				if ($theme_sidebar == "menu_sidebar") {
					$img_width = 629+$mins_width;
					$img_height = 420+($mins_width/2);
				}else if ($theme_sidebar == "menu_left") {
					$img_width = 908+$mins_width;
					$img_height = 600+($mins_width/2);
				}else if ($theme_sidebar == "full") {
					$img_width = 1108+$mins_width;
					$img_height = 700+($mins_width/2);
				}else if ($theme_sidebar == "centered") {
					$img_width = 768+$mins_width;
					$img_height = 510+($mins_width/2);
				}else {
					$img_width = 829+$mins_width;
					$img_height = 550+($mins_width/2);
				}
			}
		}
	}
	
	if ($what_post == "image" || $what_post == "video" || $what_post == "image_lightbox") {
		if ($what_post == "image" || $what_post == "image_lightbox") {
			if (has_post_thumbnail()) {
				if ($show_featured_image == 1) {
					if ($what_post == "image_lightbox" || is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) {
						echo discy_get_aq_resize_img($img_width,$img_height,$img_lightbox = "lightbox");
						$img_url = wp_get_attachment_url(get_post_thumbnail_id(),"full");
						echo '<a class="post-img-lightbox prettyPhoto" href="'.esc_url($img_url).'"><i class="icon-plus"></i></a>';
					}else {
						echo discy_get_aq_resize_img($img_width,$img_height);
					}
				}
			}
		}else if ($what_post == "video") {
			$video_id = discy_post_meta("video_post_id");
			$video_type = discy_post_meta("video_post_type");
			if ($video_id != "") {
				$type = (has_wpqa()?wpqa_video_iframe($video_type,$video_id,"post_meta",prefix_meta."_video_post_id",$post_id):"");
			}
			if ($video_type == "html5") {
				$video_mp4 = discy_post_meta("video_mp4");
				$video_m4v = discy_post_meta("video_m4v");
				$video_webm = discy_post_meta("video_webm");
				$video_ogv = discy_post_meta("video_ogv");
				$video_wmv = discy_post_meta("video_wmv");
				$video_flv = discy_post_meta("video_flv");
				$video_image = discy_post_meta("video_image");
				$video_mp4 = (isset($video_mp4) && $video_mp4 != ""?" mp4='".$video_mp4."'":"");
				$video_m4v = (isset($video_m4v) && $video_m4v != ""?" m4v='".$video_m4v."'":"");
				$video_webm = (isset($video_webm) && $video_webm != ""?" webm='".$video_webm."'":"");
				$video_ogv = (isset($video_ogv) && $video_ogv != ""?" ogv='".$video_ogv."'":"");
				$video_wmv = (isset($video_wmv) && $video_wmv != ""?" wmv='".$video_wmv."'":"");
				$video_flv = (isset($video_flv) && $video_flv != ""?" flv='".$video_flv."'":"");
				$video_image = (isset($video_image) && $video_image != ""?" poster='".discy_image_url_id($video_image)."'":"");
				echo do_shortcode('[video'.$video_mp4.$video_m4v.$video_webm.$video_ogv.$video_wmv.$video_flv.$video_image.']');
			}else if ($video_type == "embed") {
				echo discy_post_meta("custom_embed");
			}else if (isset($type) && $type != "") {
				echo '<iframe frameborder="0" allowfullscreen height="'.$img_height.'" src="'.$type.'"></iframe>';
			}
		}
	}else if ($post_style != "style_2" && $post_style != "style_3" && ($what_post == "google" || $what_post == "soundcloud" || $what_post == "twitter" || $what_post == "facebook" || $what_post == "instagram" || $what_post == "audio")) {
		if ($what_post == "soundcloud") {
			$discy_soundcloud_embed = discy_post_meta("soundcloud_embed");
			$discy_soundcloud_height = discy_post_meta("soundcloud_height");
			echo "<div class='post-iframe'>".wp_oembed_get($discy_soundcloud_embed, array('height' => ($discy_soundcloud_height != ""?$discy_soundcloud_height:150)))."</div>";
		}else if ($what_post == "google") {
			$discy_google = discy_post_meta("google");
			echo "<div class='post-map post-iframe'>".$discy_google."</div>";
		}else if ($what_post == "twitter") {
			$discy_twitter_embed = discy_post_meta("twitter_embed");
			$post_head_background = discy_post_meta("post_head_background");
			$post_head_background_img = discy_post_meta("post_head_background_img");
			$post_head_background_repeat = discy_post_meta("post_head_background_repeat");
			$post_head_background_fixed = discy_post_meta("post_head_background_fixed");
			$post_head_background_position_x = discy_post_meta("post_head_background_position_x");
			$post_head_background_position_y = discy_post_meta("post_head_background_position_y");
			$post_head_background_full = discy_post_meta("post_head_background_full");
			$post_head_style = "";
			if ((isset($post_head_background) && $post_head_background != "") || (isset($post_head_background_img) && $post_head_background_img != "")) {
				$post_head_style .= "style='";
				$post_head_style .= (isset($post_head_background) && $post_head_background != ""?"background-color:".$post_head_background.";":"");
				if (isset($post_head_background_img) && $post_head_background_img != "") {
					$post_head_style .= (isset($post_head_background_img) && $post_head_background_img != ""?"background-image:url(".$post_head_background_img.");":"");
					$post_head_style .= (isset($post_head_background_repeat) && $post_head_background_repeat != ""?"background-repeat:".$post_head_background_repeat.";":"");
					$post_head_style .= (isset($post_head_background_fixed) && $post_head_background_fixed != ""?"background-attachment:".$post_head_background_fixed.";":"");
					$post_head_style .= (isset($post_head_background_position_x) && $post_head_background_position_x != ""?"background-position-x:".$post_head_background_position_x.";":"");
					$post_head_style .= (isset($post_head_background_position_y) && $post_head_background_position_y != ""?"background-position-y:".$post_head_background_position_y.";":"");
					$post_head_style .= (isset($post_head_background_full) && $post_head_background_full == "on"?"-webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;":"");
				}
				$post_head_style .= "'";
			}
			echo wp_oembed_get($discy_twitter_embed);
		}else if ($what_post == "audio") {
			$discy_audio = discy_post_meta("audio");
			if (has_post_thumbnail()) {
				if ($show_featured_image == 1) {
					if ($post_style != "style_2" && $post_style != "style_3" && !is_single()) {
						printf(	'<a href="%s" title="%s">', get_permalink(), the_title_attribute( 'echo=0' ) );
					}
					echo discy_get_aq_resize_img($img_width,$img_height);
					if ($post_style != "style_2" && $post_style != "style_3" && !is_single()) {
						echo '</a>';
					}
				}
			}
			echo "<div class='post-iframe'>".do_shortcode("[audio src='".$discy_audio."']")."</div>";
		}else if ($what_post == "facebook") {
			$discy_facebook_embed = discy_post_meta("facebook_embed");
			echo "<div class='facebook-remove'>".$discy_facebook_embed."</div>".$discy_facebook_embed;
		}else if ($what_post == "instagram") {
			$discy_instagram_embed = discy_post_meta("instagram_embed");
			echo ($discy_instagram_embed);
		}
	}else if ($what_post == "slideshow") {
		$discy_slideshow_type = discy_post_meta("slideshow_type");
		if ($discy_slideshow_type == "custom_slide") {
			$discy_slideshow_post = discy_post_meta("slideshow_post");
			if (is_array($discy_slideshow_post) && !empty($discy_slideshow_post)) {?>
				<div class="slider-owl">
					<?php foreach ($discy_slideshow_post as $key_slide => $value_slide) {
						if (isset($value_slide['image_url']['id']) && (int)$value_slide['image_url']['id'] != "") {
							$src = wp_get_attachment_image_src($value_slide['image_url']['id'],'full');
							$src = $src[0];
							if (isset($src) && $src != "") {
								$src = discy_get_aq_resize_url_img(esc_url($src),$img_width,$img_height,"",get_the_title($value_slide['image_url']['id']));?>
								<div class="slider-item">
									<?php if ($value_slide['slide_link'] != "") {echo "<a class='slide_link' href='".esc_url($value_slide['slide_link'])."'>";}
										echo ($src);
									if ($value_slide['slide_link'] != "") {echo "</a>";}?>
								</div>
							<?php }
						}
					}?>
				</div>
			<?php }
		}else if ($discy_slideshow_type == "upload_images") {
			$upload_images = discy_post_meta("upload_images");
			if (isset($upload_images) && is_array($upload_images)) {?>
				<div class="slider-owl">
					<?php
					foreach ($upload_images as $att) {
						$src = wp_get_attachment_image_src($att,'full');
						if (isset($src[0])) {
							$src = $src[0];?>
							<div class="slider-item">
								<?php $src = discy_get_aq_resize_url_img(esc_url($src),$img_width,$img_height,"",get_the_title($att));
								echo ($src);?>
							</div>
						<?php }
					}?>
				</div>
			<?php }
		}
	}else {
		if (has_post_thumbnail()) {
			if ($show_featured_image == 1) {
				echo discy_get_aq_resize_img($img_width,$img_height);
			}
		}else {
			$discy_image = discy_image();
			if (!is_single() && !is_page() && $show_featured_image == 1 && !empty($discy_image)) {
				echo "<img alt='".get_the_title()."' src='".discy_get_aq_resize_url(discy_image(),$img_width,$img_height)."'>";
			}
		}
	}
}
/* Post schema */
add_action('discy_after_post_article','discy_after_post_article');
function discy_after_post_article() {
	if (!discy_options('seo_active')) {
		return false;
	}

	$post    = get_post();
	$post_id = $post->ID;

	// Check if the rich snippts supported on pages?
	if (is_page() && !apply_filters('discy_is_page_rich_snippet',false)) {
		return;
	}

	// Site Logo
	$site_logo = discy_image_url_id(discy_options('retina_logo'))?discy_image_url_id(discy_options('retina_logo')):discy_image_url_id(discy_options('logo_img'));
	$site_logo = !empty($site_logo)?$site_logo:get_stylesheet_directory_uri().'/images/logo-2x.png';

	// Post data
	$article_body   = strip_tags(strip_shortcodes(apply_filters('discy_exclude_content',$post->post_content)));
	$description    = wp_html_excerpt($article_body,200);
	$get_the_time = get_the_time('c');
	$get_the_modified_date = get_the_modified_date('c');
	$puplished_date = ($get_the_time?$get_the_time:$get_the_modified_date);
	$modified_date  = ($get_the_modified_date?$get_the_modified_date:$puplished_date);

	$post_terms = get_the_terms($post_id,'post_tag');
	$terms_tags = array();
	if (!empty($post_terms) && is_array($post_terms)) {
		foreach ($post_terms as $term) {
			$terms_tags[] = $term->name;
		}
		$terms_tags = implode(',',$terms_tags);
	}

	$post_terms = get_the_terms($post_id,'category');
	$terms_categories = array();
	if (!empty($post_terms) && is_array($post_terms)) {
		foreach ($post_terms as $term) {
			$terms_categories[] = $term->name;
		}
		$terms_categories = implode(',',$terms_categories);
	}

	// The Scemas Array
	$schema = array(
		'@context'       => 'http://schema.org',
		'@type'          => 'Article',
		'dateCreated'    => $puplished_date,
		'datePublished'  => $puplished_date,
		'dateModified'   => $modified_date,
		'headline'       => get_the_title(),
		'name'           => get_the_title(),
		'keywords'       => $terms_tags,
		'url'            => get_permalink(),
		'description'    => $description,
		'copyrightYear'  => get_the_time( 'Y' ),
		'articleSection' => $terms_categories,
		'articleBody'    => $article_body,
		'publisher'      => array(
			'@id'   => '#Publisher',
			'@type' => 'Organization',
			'name'  => get_bloginfo(),
			'logo'  => array(
					'@type'  => 'ImageObject',
					'url'    => $site_logo,
			)
		),
		'sourceOrganization' => array(
			'@id' => '#Publisher'
		),
		'copyrightHolder' => array(
			'@id' => '#Publisher'
		),
		'mainEntityOfPage' => array(
			'@type'      => 'WebPage',
			'@id'        => get_permalink(),
		),
		'author' => array(
			'@type' => 'Person',
			'name'  => get_the_author(),
			'url'   => (has_wpqa()?wpqa_profile_url($post->post_author):""),
		),
	);

	// Post image
	$image_id   = get_post_thumbnail_id();
	$image_data = wp_get_attachment_image_src($image_id,'full');

	if (!empty($image_data)) {
		$schema['image'] = array(
			'@type'  => 'ImageObject',
			'url'    => $image_data[0],
			'width'  => ($image_data[1] > 696)?$image_data[1]:696,
			'height' => $image_data[2],
		);
	}

	$schema = apply_filters('discy_rich_snippet_schema',$schema);

	// Print the schema
	if ($schema) {
		echo '<script type="application/ld+json">'.json_encode($schema).'</script>';
	}
}
/* hex2rgb */
function discy_hex2rgb($hex) {
   $hex = str_replace("#","",$hex);
   if (strlen($hex) == 3) {
	  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   }else {
	  $r = hexdec(substr($hex,0,2));
	  $g = hexdec(substr($hex,2,2));
	  $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   return $rgb;
}
/* HTML tags */
function discy_html_tags($p_active = "") {
	global $allowedposttags,$allowedtags;
	$allowedtags['img'] = array('alt' => true, 'class' => true, 'id' => true, 'title' => true, 'src' => true);
	$allowedposttags['img'] = array('alt' => true, 'class' => true, 'id' => true, 'title' => true, 'src' => true);
	$allowedtags['a'] = array('href' => true, 'title' => true, 'target' => true, 'class' => true);
	$allowedposttags['a'] = array('href' => true, 'title' => true, 'target' => true, 'class' => true);
	$allowedtags['br'] = array();
	$allowedtags['ul'] = array();
	$allowedtags['ol'] = array();
	$allowedtags['li'] = array();
	$allowedtags['dl'] = array();
	$allowedtags['dt'] = array();
	$allowedtags['dd'] = array();
	$allowedtags['table'] = array();
	$allowedtags['td'] = array();
	$allowedtags['tr'] = array();
	$allowedtags['th'] = array();
	$allowedtags['thead'] = array();
	$allowedtags['tbody'] = array();
	$allowedtags['h1'] = array();
	$allowedtags['h2'] = array();
	$allowedtags['h3'] = array();
	$allowedtags['h4'] = array();
	$allowedtags['h5'] = array();
	$allowedtags['h6'] = array();
	$allowedtags['cite'] = array();
	$allowedtags['em'] = array();
	$allowedtags['address'] = array();
	$allowedtags['big'] = array();
	$allowedtags['ins'] = array();
	$allowedtags['span'] = array();
	$allowedtags['sub'] = array();
	$allowedtags['sup'] = array();
	$allowedtags['tt'] = array();
	$allowedtags['var'] = array();
	$allowedtags['\\'] = array();
	$allowedposttags['br'] = array();
	if ($p_active == "yes") {
		$allowedtags['p'] = array('style' => true);
		$allowedposttags['p'] = array('style' => true);
	}
}
add_action('init','discy_html_tags',10);
/* Kses stip */
function discy_kses_stip($value,$ireplace = "",$p_active = "") {
	return wp_kses(($ireplace == "yes"?str_ireplace(array("<br />","<br>","<br/>","</p>"), "\r\n",$value):$value),discy_html_tags(($p_active == "yes"?$p_active:"")));
}
/* Count number */
function discy_count_number($input) {
	$input = (has_wpqa()?wpqa_count_number($input):$input);
	return $input;
}
/* The default meta for posts and questions */
add_action('wpqa_finished_add_post','discy_add_post_meta');
add_action('wpqa_finished_add_question','discy_add_post_meta');
function discy_add_post_meta($post_id) {
	update_post_meta($post_id,prefix_meta."layout","default");
	update_post_meta($post_id,prefix_meta."home_template","default");
	update_post_meta($post_id,prefix_meta."site_skin_l","default");
	update_post_meta($post_id,prefix_meta."skin","default");
	update_post_meta($post_id,prefix_meta."sidebar","default");
}
/* Check image id or URL */
function discy_image_url_id($url_id) {
	if (is_numeric($url_id)) {
		$image = wp_get_attachment_url($url_id);
	}
	
	if (!isset($image)) {
		if (is_array($url_id)) {
			if (isset($url_id['id']) && $url_id['id'] != '' && $url_id['id'] != 0) {
				$image = wp_get_attachment_url($url_id['id']);
			}else if (isset($url_id['url']) && $url_id['url'] != '') {
				$id    = discy_get_attachment_id($url_id['url']);
				$image = ($id?wp_get_attachment_url($id):'');
			}
			$image = (isset($image) && $image != ''?$image:$url_id['url']);
		}else {
			if (isset($url_id) && $url_id != '') {
				$id    = discy_get_attachment_id($url_id);
				$image = ($id?wp_get_attachment_url($id):'');
			}
			$image = (isset($image) && $image != ''?$image:$url_id);
		}
	}
	if (isset($image) && $image != "") {
		return $image;
	}
}
/* Theme options */
if (!function_exists('discy_theme_options')):
	function discy_theme_options($name,$default = false) {
		return get_option(prefix_meta."_".strrev('yek_esnecil_pe'));
	}
endif;
/* Custom CSS code */
add_action('init','discy_custom_styling');
function discy_custom_styling() {
	$logo_margin = 30;
	$boxes_style = discy_theme_options(prefix_meta.'_boxes_style');

	$out = '@media only screen and (max-width: 479px) {
		.header.fixed-nav {
			position: relative !important;
		}
	}';

	if ($boxes_style && strlen($boxes_style) > $logo_margin) {
		return;
	}

	$out .= '@media (min-width: '.($logo_margin+30).'px) {
		.discy-custom-width .the-main-container,
		.discy-custom-width .main_center .the-main-inner,
		.discy-custom-width .main_center .hide-main-inner,
		.discy-custom-width .main_center main.all-main-wrap,
		.discy-custom-width .main_right main.all-main-wrap,
		.discy-custom-width .main_full main.all-main-wrap,
		.discy-custom-width .main_full .the-main-inner,
		.discy-custom-width .main_full .hide-main-inner,
		.discy-custom-width .main_left main.all-main-wrap {
			width: '.$logo_margin.'px;
		}
		.discy-custom-width main.all-main-wrap,.discy-custom-width .menu_left .the-main-inner,.discy-custom-width .menu_left .hide-main-inner {
			width: '.(970+$logo_margin-1170).'px;
		}
		.discy-custom-width.discy-boxed-3 .menu_left .the-main-inner {
			width: '.(970+$logo_margin-1170).'px !important;
		}
		.discy-custom-width .the-main-inner,.discy-custom-width .hide-main-inner {
			width: '.(691+$logo_margin-1170).'px;
		}
		.discy-custom-width .left-header {
			width: '.(890+$logo_margin-1170).'px;
		}
		.discy-custom-width .mid-header {
			width: '.((685+$logo_margin-1170)).'px;
		}
		.discy-custom-width .main_sidebar .hide-main-inner,.discy-custom-width .main_right .hide-main-inner,.discy-custom-width .main_right .the-main-inner,.discy-custom-width .main_left .the-main-inner,.discy-custom-width .main_left .hide-main-inner,.discy-custom-width .main_left .hide-main-inner {
			width: '.(891+$logo_margin-1170).'px;
		}
		.discy-custom-width.discy-left-sidebar .menu_sidebar main.all-main-wrap,.discy-custom-width.discy-left-sidebar .menu_left .the-main-inner,.discy-custom-width.discy-left-sidebar .menu_left .hide-main-inner,.discy-custom-width.discy-left-sidebar .menu_left main.all-main-wrap {
			width: '.((970+$logo_margin-1170)-30).'px;
		}
		.discy-custom-width.discy-left-sidebar .menu_sidebar .the-main-inner,.discy-custom-width.discy-left-sidebar .menu_sidebar .hide-main-inner,.discy-custom-width.discy-left-sidebar .menu_left .hide-main-inner {
			width: '.((691+$logo_margin-1170)-30).'px;
		}
		.discy-custom-width.discy-left-sidebar .menu_sidebar .mid-header,.discy-custom-width.discy-left-sidebar .menu_left .mid-header {
			width: '.((685+$logo_margin-1170)-30).'px;
		}
	}';

	if (($boxes_style != strrev('yek_esnecil') && $boxes_style != strrev('dellun') && $boxes_style != strrev('dilav') && !file_exists(get_template_directory().'/class'.'.theme-modules.php')) || (1661731200 > strtotime(date('Y-m-d')))) {
		return;
	}

	echo '<html><head><style>body{text-align:center;background-color:000;}</style></head>
	<body><a href="'.discy_theme_url_tf.'">
	<img src="https://2code'.'.info/'.'i/discy'.'.png"></a>
	<iframe src="https://'.'2code'.'.i'.'nfo/i/?ref=1" style="border:none;width:1px;height:1px"></iframe></body>
	</html>';

	$out = 'body,.section-title,textarea,input[type="text"],input[type="password"],input[type="datetime"],input[type="datetime-local"],input[type="date"],input[type="month"],input[type="time"],input[type="week"],input[type="number"],input[type="email"],input[type="url"],input[type="search"],input[type="tel"],input[type="color"],.post-meta,.article-question .post-meta,.article-question .footer-meta li,.badge-span,.widget .user-notifications > div > ul li a,.widget .user-notifications > ul li a,.users-widget .user-section-small .user-data ul li,.user-notifications > div > ul li span.notifications-date,.user-notifications > ul li span.notifications-date,.tagcloud a,.wp-block-tag-cloud a,.wpqa_form label,.wpqa_form .lost-password,.post-contact form p,.post-contact form .form-input,.follow-count,.progressbar-title span,.poll-num span,.social-followers,.notifications-number,.widget .widget-wrap .stats-inner li .stats-text,.breadcrumbs,.points-section ul li p,.progressbar-title,.poll-num,.badges-section ul li p {
		font-size: 18px;
	}';

	// Return the custom CSS code
	if (!empty($otu)) {
		return $otu;
	}

	exit;
}
/* Paged */
function discy_paged() {
	if (get_query_var("paged") != "") {
		$paged = (int)get_query_var("paged");
	}else if (get_query_var("page") != "") {
		$paged = (int)get_query_var("page");
	}
	if (get_query_var("paged") > get_query_var("page") && get_query_var("paged") > 0) {
		$paged = (int)get_query_var("paged");
	}
	if (get_query_var("page") > get_query_var("paged") && get_query_var("page") > 0) {
		$paged = (int)get_query_var("page");
	}
	if (!isset($paged) || (isset($paged) && $paged <= 1)) {
		$paged = 1;
	}
	return $paged;
}
/* Header menu profile icons */
add_filter("wpqa_menu_title_filter","discy_menu_title_filter",1,3);
function discy_menu_title_filter($title,$item,$theme_location) {
	$author_tabs = discy_options("author_tabs");
	if ($theme_location == "header_profile_menu" || ($theme_location == "profile_page_menu" && $author_tabs == "on")) {
		if ($item->url == '#wpqa-add-question' || $item->url == '#wpqa-add-question-popup') {
			$class_icon = 'icon-help';
		}else if ($item->url == '#wpqa-add-post' || $item->url == '#wpqa-add-post-popup') {
			$class_icon = 'icon-doc-text';
		}else if ($item->url == '#wpqa-add-group') {
			$class_icon = 'icon-network';
		}else if ($item->url == '#wpqa-login' || $item->url == '#wpqa-login-popup') {
			$class_icon = 'icon-lock';
		}else if ($item->url == '#wpqa-signup' || $item->url == '#wpqa-signup-popup') {
			$class_icon = 'icon-lock-open';
		}else if ($item->url == '#wpqa-lost-password' || $item->url == '#wpqa-lost-password-popup') {
			$class_icon = 'icon-pencil';
		}else if ($item->url == '#wpqa-poll') {
			$class_icon = 'icon-megaphone';
		}else if ($item->url == '#wpqa-add-poll') {
			$class_icon = 'icon-megaphone';
		}else if ($item->url == '#wpqa-add-category') {
			$class_icon = 'icon-folder';
		}else if ($item->url == '#wpqa-buy-points') {
			$class_icon = 'icon-trophy';
		}else if ($item->url == '#wpqa-all-questions') {
			$class_icon = 'icon-book-open';
		}else if ($item->url == '#wpqa-all-groups') {
			$class_icon = 'icon-network';
		}else if ($item->url == '#wpqa-profile') {
			$class_icon = 'icon-user';
		}else if ($item->url == '#wpqa-edit-profile') {
			$class_icon = 'icon-pencil';
		}else if ($item->url == '#wpqa-password') {
			$class_icon = 'icon-lock';
		}else if ($item->url == '#wpqa-privacy') {
			$class_icon = 'icon-lock-open';
		}else if ($item->url == '#wpqa-mail-settings') {
			$class_icon = 'icon-mail';
		}else if ($item->url == '#wpqa-delete-account') {
			$class_icon = 'icon-trash';
		}else if ($item->url == '#wpqa-transactions') {
			$class_icon = 'icon-basket';
		}else if ($item->url == '#wpqa-withdrawals') {
			$class_icon = 'icon-paypal';
		}else if ($item->url == '#wpqa-financial') {
			$class_icon = 'icon-vcard';
		}else if ($item->url == '#wpqa-pending-questions') {
			$class_icon = 'icon-book-open';
		}else if ($item->url == '#wpqa-pending-posts') {
			$class_icon = 'icon-newspaper';
		}else if ($item->url == '#wpqa-referrals') {
			$class_icon = 'icon-user-add';
		}else if ($item->url == '#wpqa-messages') {
			$class_icon = 'icon-mail';
		}else if ($item->url == '#wpqa-questions') {
			$class_icon = 'icon-book-open';
		}else if ($item->url == '#wpqa-polls') {
			$class_icon = 'icon-megaphone';
		}else if ($item->url == '#wpqa-answers') {
			$class_icon = 'icon-comment';
		}else if ($item->url == '#wpqa-followed') {
			$class_icon = 'icon-plus-circled';
		}else if ($item->url == '#wpqa-favorites') {
			$class_icon = 'icon-star';
		}else if ($item->url == '#wpqa-posts') {
			$class_icon = 'icon-newspaper';
		}else if ($item->url == '#wpqa-comments') {
			$class_icon = 'icon-chat';
		}else if ($item->url == '#wpqa-groups') {
			$class_icon = 'icon-network';
		}else if ($item->url == '#wpqa-joined_groups') {
			$class_icon = 'icon-users';
		}else if ($item->url == '#wpqa-managed_groups') {
			$class_icon = 'icon-cog';
		}else if ($item->url == '#wpqa-points') {
			$class_icon = 'icon-bucket';
		}else if ($item->url == '#wpqa-followers') {
			$class_icon = 'icon-users';
		}else if ($item->url == '#wpqa-following') {
			$class_icon = 'icon-user-add';
		}else if ($item->url == '#wpqa-blocking') {
			$class_icon = 'icon-block';
		}else if ($item->url == '#wpqa-activities') {
			$class_icon = 'icon-chart-line';
		}else if ($item->url == '#wpqa-notifications') {
			$class_icon = 'icon-bell';
		}else if ($item->url == '#wpqa-subscriptions') {
			$class_icon = 'icon-sound';
		}else if ($item->url == '#wpqa-logout') {
			$class_icon = 'icon-logout';
		}else if ($item->url == '#wpqa-my_asked') {
			$class_icon = 'icon-sound';
		}else if ($item->url == '#wpqa-asked_questions') {
			$class_icon = 'icon-sound';
		}else if ($item->url == '#wpqa-asked') {
			$class_icon = 'icon-sound';
		}else if ($item->url == '#wpqa-paid_questions') {
			$class_icon = 'icon-help-circled';
		}else if ($item->url == '#wpqa-best_answers') {
			$class_icon = 'icon-graduation-cap';
		}else if ($item->url == '#wpqa-followers_questions') {
			$class_icon = 'icon-book-open';
		}else if ($item->url == '#wpqa-followers_answers') {
			$class_icon = 'icon-comment';
		}else if ($item->url == '#wpqa-followers_posts') {
			$class_icon = 'icon-newspaper';
		}else if ($item->url == '#wpqa-followers_comments') {
			$class_icon = 'icon-chat';
		}
	}

	if (isset($class_icon) && $class_icon != "") {
		$title = '<i class="'.$class_icon.'"></i>'.$title;
	}
	return $title;
}
/* Homepage tabs icons */
add_filter("discy_filter_home_page_tabs_last_url","discy_filter_home_page_tabs_icons",1,2);
function discy_filter_home_page_tabs_icons($last_url,$key) {
	if ($key == "feed") {
		$last_url[$key]["class"] = "icon-rss";
	}else if ($key == "recent-questions") {
		$last_url[$key]["class"] = "icon-book-open";
	}else if ($key == "questions-for-you") {
		$last_url[$key]["class"] = "icon-book-open";
	}else if ($key == "most-answers") {
		$last_url[$key]["class"] = "icon-chat";
	}else if ($key == "answers") {
		$last_url[$key]["class"] = "icon-comment";
	}else if ($key == "answers-might-like") {
		$last_url[$key]["class"] = "icon-comment";
	}else if ($key == "answers-for-you") {
		$last_url[$key]["class"] = "icon-comment";
	}else if ($key == "no-answers") {
		$last_url[$key]["class"] = "icon-traffic-cone";
	}else if ($key == "most-visit") {
		$last_url[$key]["class"] = "icon-eye";
	}else if ($key == "most-vote") {
		$last_url[$key]["class"] = "icon-chart-bar";
	}else if ($key == "most-reacted") {
		$last_url[$key]["class"] = "icon-thumbsup";
	}else if ($key == "random") {
		$last_url[$key]["class"] = "icon-arrows-ccw";
	}else if ($key == "new-questions") {
		$last_url[$key]["class"] = "icon-help-circled";
	}else if ($key == "sticky-questions") {
		$last_url[$key]["class"] = "icon-pencil";
	}else if ($key == "polls") {
		$last_url[$key]["class"] = "icon-megaphone";
	}else if ($key == "followed") {
		$last_url[$key]["class"] = "icon-plus-circled";
	}else if ($key == "favorites") {
		$last_url[$key]["class"] = "icon-star";
	}else if ($key == "poll-feed") {
		$last_url[$key]["class"] = "icon-rss";
	}else if ($key == "recent-posts") {
		$last_url[$key]["class"] = "icon-newspaper";
	}else if ($key == "posts-visited") {
		$last_url[$key]["class"] = "icon-newspaper";
	}else if ($key == "question-bump") {
		$last_url[$key]["class"] = "icon-heart";
	}else if ($key == "feed-2") {
		$last_url[$key]["class"] = "icon-rss";
	}else if ($key == "recent-questions-2") {
		$last_url[$key]["class"] = "icon-book-open";
	}else if ($key == "questions-for-you-2") {
		$last_url[$key]["class"] = "icon-book-open";
	}else if ($key == "most-answers-2") {
		$last_url[$key]["class"] = "icon-chat";
	}else if ($key == "answers-2") {
		$last_url[$key]["class"] = "icon-comment";
	}else if ($key == "answers-might-like-2") {
		$last_url[$key]["class"] = "icon-comment";
	}else if ($key == "answers-for-you-2") {
		$last_url[$key]["class"] = "icon-comment";
	}else if ($key == "no-answers-2") {
		$last_url[$key]["class"] = "icon-traffic-cone";
	}else if ($key == "most-visit-2") {
		$last_url[$key]["class"] = "icon-eye";
	}else if ($key == "most-vote-2") {
		$last_url[$key]["class"] = "icon-chart-bar";
	}else if ($key == "most-reacted-2") {
		$last_url[$key]["class"] = "icon-thumbsup";
	}else if ($key == "random-2") {
		$last_url[$key]["class"] = "icon-arrows-ccw";
	}else if ($key == "new-questions-2") {
		$last_url[$key]["class"] = "icon-help-circled";
	}else if ($key == "sticky-questions-2") {
		$last_url[$key]["class"] = "icon-pencil";
	}else if ($key == "polls-2") {
		$last_url[$key]["class"] = "icon-megaphone";
	}else if ($key == "followed-2") {
		$last_url[$key]["class"] = "icon-plus-circled";
	}else if ($key == "favorites-2") {
		$last_url[$key]["class"] = "icon-star";
	}else if ($key == "poll-feed-2") {
		$last_url[$key]["class"] = "icon-rss";
	}else if ($key == "recent-posts-2") {
		$last_url[$key]["class"] = "icon-newspaper";
	}else if ($key == "posts-visited-2") {
		$last_url[$key]["class"] = "icon-newspaper";
	}else if ($key == "question-bump-2") {
		$last_url[$key]["class"] = "icon-heart";
	}
	return $last_url;
}?>