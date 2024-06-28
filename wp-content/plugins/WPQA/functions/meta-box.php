<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*-----------------------------------------------------------------------------------*/
/* Add meta boxes */
/*-----------------------------------------------------------------------------------*/
add_action('add_meta_boxes','wpqa_meta_boxes');
function wpqa_meta_boxes($post_type) {
	global $post;
	$allow_post_type = apply_filters(wpqa_prefix_theme."_allow_post_type",array('post','page',wpqa_questions_type,wpqa_asked_questions_type,wpqa_knowledgebase_type,'group','checkout'));
	if (in_array($post_type,$allow_post_type)) {
		add_meta_box('framework_meta_tabs',esc_html__('Page settings',"wpqa"),'wpqa_meta_tabs',$post_type,'normal','high');
	}
}
/*-----------------------------------------------------------------------------------*/
/* Page settings */
/*-----------------------------------------------------------------------------------*/
function wpqa_meta_tabs() {
	global $post;
	wp_nonce_field('wpqa_builder_save_meta','wpqa_save_meta_nonce');
	$wpqa_admin_meta = wpqa_admin_meta();
	if (is_array($wpqa_admin_meta) && !empty($wpqa_admin_meta)) {?>
		<div id="framework-admin-wrap" class="framework-admin">
			<div class="framework-admin-header">
				<a href="<?php echo wpqa_theme_url_tf?>" target="_blank"><i class="dashicons-before dashicons-admin-tools"></i><?php echo wpqa_prefix_theme?></a>
				<div class="framework_social">
					<ul>
						<li class="framework_social_facebook"><a class="framework_social_f" href="https://www.facebook.com/2code.info" target="_blank"><i class="dashicons dashicons-facebook"></i></a></li>
						<li class="framework_social_twitter"><a class="framework_social_t" href="https://www.twitter.com/2codeThemes" target="_blank"><i class="dashicons dashicons-twitter"></i></a></li>
						<li class="framework_social_site"><a class="framework_social_e" href="https://2code.info/" target="_blank"><i class="dashicons dashicons-email-alt"></i></a></li>
						<li class="framework_social_docs"><a class="framework_social_s" href="https://2code.info/docs/<?php echo wpqa_prefix_theme?>/" target="_blank"><i class="dashicons dashicons-sos"></i></a></li>
					</ul>
				</div>
				<div class="clear"></div>
			</div>
			<div class="framework-admin-content">
			    <h2 class="nav-tab-wrapper">
			        <?php echo wpqa_admin_fields_class::wpqa_admin_tabs("meta",$wpqa_admin_meta,$post->ID);?>
			    </h2>
			    <?php settings_errors( 'options-framework' ); ?>
			    <div id="framework-admin-metabox" class="metabox-holder">
				    <div id="framework-admin" class="framework-main postbox">
				    	<?php wpqa_admin_fields_class::wpqa_admin_fields("meta",prefix_meta,"meta",$post->ID,$wpqa_admin_meta);?>
					</div><!-- End container -->
				</div>
			</div>
			<div class="clear"></div>
		</div><!-- End wrap -->
	<?php }
}
/*-----------------------------------------------------------------------------------*/
/* Process save meta box */
/*-----------------------------------------------------------------------------------*/
add_action('save_post','wpqa_save_post',10,3);
if (!function_exists('wpqa_save_post')) :
	function wpqa_save_post($post_id,$post_data,$update) {
		$post_type = (isset($post_data->post_type)?$post_data->post_type:"");
		if (is_admin()) {
			if (!isset($_POST)) return $post_id;
			$allow_post_type = apply_filters(wpqa_prefix_theme."_allow_post_type",array('post','page',wpqa_questions_type,wpqa_asked_questions_type,wpqa_knowledgebase_type,'group','checkout'));
			if (!in_array($post_data->post_type,$allow_post_type)) return $post_id;
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
			if (!isset($_POST['wpqa_save_meta_nonce']) || !wp_verify_nonce ($_POST['wpqa_save_meta_nonce'],'wpqa_builder_save_meta')) return $post_id;
			if (!current_user_can ('edit_post',$post_id)) return $post_id;

			do_action("wpqa_action_meta_save",$_POST,$post_data);

			$post_id = $post_data->ID;

			$options = wpqa_admin_meta();
			foreach ($options as $value) {
				if (!isset($value['unset']) && $value['type'] != 'heading' && $value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != 'info' && $value['type'] != 'group' && $value['type'] != 'html' && $value['type'] != 'content') {
					$val = "";
					
					if (isset($value['std'])) {
						$val = $value['std'];
					}
					
					$field_name = $value['id'];
					
					if (isset($_POST[$field_name])) {
						$val = $_POST[$field_name];
					}
					
					if (!isset($_POST[$field_name]) && $value['type'] == "checkbox") {
						$val = 0;
					}
					
					if (array() === $val) {
						if (isset($value['save']) && $value['save'] == "option") {
							delete_option($field_name);
						}else {
							delete_post_meta($post_data->ID,$field_name);
						}
					}else if (isset($_POST[$field_name]) || $value['type'] == "checkbox") {
						if ($value['id'] == "question_poll" && $val != "on") {
							update_post_meta($post_data->ID,'question_poll',2);
						}else {
							if (isset($_POST["private_question"]) && ($_POST["private_question"] == "on" || $_POST["private_question"] == 1)) {
								$anonymously_user = get_post_meta($post_data->ID,"anonymously_user",true);
								update_post_meta($post_data->ID,'private_question_author',($anonymously_user > 0?$anonymously_user:$post_data->post_author));
							}
							if (isset($value['save']) && $value['save'] == "option") {
								$wp_page_template = get_post_meta($post_data->ID,"_wp_page_template",true);
								if ($wp_page_template == "template-home.php") {
									if (has_himer() || has_knowly()) {
										delete_option("tabs_menu");
									}else {
										delete_option("tabs_menu_select");
									}
								}
								update_option($field_name,$val);
								update_post_meta($post_data->ID,$field_name,$val);
								if ($wp_page_template == "template-home.php" && (isset($_POST["tabs_menu"]) || (isset($_POST["tabs_menu_select"]) && $_POST["tabs_menu_select"] != "default"))) {
									update_option("home_page_id",$post_data->ID);
								}
							}else {
								update_post_meta($post_data->ID,$field_name,$val);
							}
						}
					}
				}
			}
			do_action(wpqa_prefix_theme."_action_after_meta_save",$_POST,$post_data);
			
			if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type || $post_type == wpqa_knowledgebase_type || $post_type == "post" || $post_type == "message") {
				if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
					// category
					$get_question_user_id = get_post_meta($post_id,"user_id",true);
					if (empty($get_question_user_id) && isset($_POST[prefix_meta.'question_category'])) {
						$get_term_by = get_term_by('id',(isset($_POST[prefix_meta.'question_category'])?stripslashes($_POST[prefix_meta.'question_category']):""),wpqa_question_categories);
						if (isset($get_term_by->slug)) {
							$new_term_slug = $get_term_by->slug;
							wp_set_object_terms( $post_id, $new_term_slug, wpqa_question_categories );
						}
					}
					
					$sticky_questions = get_option('sticky_questions');
					$sticky_posts = get_option('sticky_posts');
					if (isset($_POST['sticky_question']) && $_POST['sticky_question'] == "sticky" && isset($_POST['sticky']) && $_POST['sticky'] == "sticky") {
						update_post_meta($post_id,'sticky',1);
						if (is_array($sticky_questions)) {
							if (!in_array($post_id,$sticky_questions)) {
								$array_merge = array_merge($sticky_questions,array($post_id));
								update_option("sticky_questions",$array_merge);
							}
						}else {
							update_option("sticky_questions",array($post_id));
						}
						if (is_array($sticky_posts)) {
							if (!in_array($post_id,$sticky_posts)) {
								$array_merge = array_merge($sticky_posts,array($post_id));
								update_option("sticky_posts",$array_merge);
							}
						}else {
							update_option("sticky_posts",array($post_id));
						}
					}else {
						delete_post_meta($post_id,"end_sticky_time");
						if (is_array($sticky_questions) && in_array($post_id,$sticky_questions)) {
							$sticky_questions = wpqa_remove_item_by_value($sticky_questions,$post_id);
							update_option('sticky_questions',$sticky_questions);
						}
						if (is_array($sticky_posts) && in_array($post_id,$sticky_posts)) {
							$sticky_posts = wpqa_remove_item_by_value($sticky_posts,$post_id);
							update_option('sticky_posts',$sticky_posts);
						}
						delete_post_meta($post_id,'sticky');
					}
					$question_username = get_post_meta($post_id,'question_username',true);
					$question_email = get_post_meta($post_id,'question_email',true);
					$anonymously_user = get_post_meta($post_id,'anonymously_user',true);
					if ($question_username == "") {
						$question_no_username = get_post_meta($post_id,'question_no_username',true);
					}
				}
				if ($post_type == wpqa_knowledgebase_type) {
					$sticky_knowledgebases = get_option('sticky_knowledgebases');
					$sticky_posts = get_option('sticky_posts');
					if (isset($_POST['sticky_knowledgebase']) && $_POST['sticky_knowledgebase'] == "sticky" && isset($_POST['sticky']) && $_POST['sticky'] == "sticky") {
						update_post_meta($post_id,'sticky',1);
						if (is_array($sticky_knowledgebases)) {
							if (!in_array($post_id,$sticky_knowledgebases)) {
								$array_merge = array_merge($sticky_knowledgebases,array($post_id));
								update_option("sticky_knowledgebases",$array_merge);
							}
						}else {
							update_option("sticky_knowledgebases",array($post_id));
						}
						if (is_array($sticky_posts)) {
							if (!in_array($post_id,$sticky_posts)) {
								$array_merge = array_merge($sticky_posts,array($post_id));
								update_option("sticky_posts",$array_merge);
							}
						}else {
							update_option("sticky_posts",array($post_id));
						}
					}else {
						if (is_array($sticky_knowledgebases) && in_array($post_id,$sticky_knowledgebases)) {
							$sticky_knowledgebases = wpqa_remove_item_by_value($sticky_knowledgebases,$post_id);
							update_option('sticky_knowledgebases',$sticky_knowledgebases);
						}
						if (is_array($sticky_posts) && in_array($post_id,$sticky_posts)) {
							$sticky_posts = wpqa_remove_item_by_value($sticky_posts,$post_id);
							update_option('sticky_posts',$sticky_posts);
						}
						delete_post_meta($post_id,'sticky');
					}
				}
				if ($post_type == "post") {
					$post_username = get_post_meta($post_id,'post_username',true);
					$post_email = get_post_meta($post_id,'post_email',true);
				}
				if ($post_type == "message") {
					$message_username = get_post_meta($post_id,'message_username',true);
					$message_email = get_post_meta($post_id,'message_email',true);
				}
				
				if ((isset($anonymously_user) && $anonymously_user != "") || (isset($question_no_username) && $question_no_username == "no_user") || (isset($question_username) && $question_username != "" && isset($question_email) && $question_email != "") || (isset($post_username) && $post_username != "" && isset($post_email) && $post_email != "") || (isset($message_username) && $message_username != "" && isset($message_email) && $message_email != "")) {
					$update_data = array(
						'ID' => $post_id,
						'post_author' => 0,
					);
					remove_action('save_post','wpqa_save_post');
					$post_id = wp_update_post($update_data);
					add_action('save_post','wpqa_save_post',10,3);
				}
			}
		}else {
			if ($post_type == wpqa_questions_type || $post_type == wpqa_asked_questions_type) {
				if ($post_data->post_status == "draft") {
					$update_data = array(
						'ID' => $post_id,
						'post_status' => $post_data->post_status,
					);
					remove_action('save_post','wpqa_save_post');
					$post_id = wp_update_post($update_data);
					add_action('save_post','wpqa_save_post',10,3);
				}
			}
		}
	}
endif;?>