<?php

/* @author    2codeThemes
*  @package   WPQA/options
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Style taxonomy */
$args = apply_filters(wpqa_prefix_theme."_term_options",array('category',wpqa_question_categories,wpqa_knowledgebase_categories));
wpqa_term_options($args);
function wpqa_term_options( $args ) {
	if (is_array($args) && !empty($args)) {
		foreach ($args as $taxonomy) {
			add_action( $taxonomy .'_add_form_fields', 'wpqa_term_add_form_fields', 1 );
			add_action( $taxonomy .'_edit_form_fields', 'wpqa_term_edit_form_fields', 1 );
			add_action( 'edited_'. $taxonomy, 'wpqa_save_term', 10 );
			add_action( 'create_'. $taxonomy, 'wpqa_save_term', 10 );
		}
	}
}
function wpqa_term_add_form_fields( $tag ) {?>
	<div class="form-terms">
		<div class="framework-main">
			<?php wpqa_admin_fields_class::wpqa_admin_fields("term_add",wpqa_terms,"term",null,wpqa_admin_terms($tag));?>
		</div>
	</div>
	<?php 
}
function wpqa_term_edit_form_fields( $tag ) {?>
	<tr class="form-terms">
		<th colspan="2" scope="row" valign="top">
			<div class="framework-main">
				<?php wpqa_admin_fields_class::wpqa_admin_fields("term_edit",wpqa_terms,"term",$tag->term_id,wpqa_admin_terms($tag->taxonomy,$tag->term_id));?>
			</div>
		</th>
	</tr>
	<?php
}
function wpqa_save_term( $term_id ) {
	$term = get_term($term_id);
	if (!function_exists('wpqa_admin_terms')) {
		require_once plugin_dir_path(__FILE__)."terms.php";
	}
	$options = wpqa_admin_terms($term->taxonomy,$term_id);
	foreach ($options as $value) {
		if ($value['type'] != 'heading' && $value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != 'info' && $value['type'] != 'group' && $value['type'] != 'html' && $value['type'] != 'content') {
			$val = '';
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
				delete_term_meta($term_id,$field_name);
			}else {
				update_term_meta($term_id,$field_name,$val);
			}
		}
	}
}
/* Term options */
function wpqa_admin_terms($tax = "",$term_id = "") {
	global $pagenow;
	$options = array();
	$show_custom_terms_settings = apply_filters("wpqa_show_custom_terms_settings",false,$tax);
	if (is_admin() && (isset($_REQUEST['taxonomy']) && ($_REQUEST['taxonomy'] == "category" || $_REQUEST['taxonomy'] == wpqa_question_categories || $_REQUEST['taxonomy'] == wpqa_knowledgebase_categories) || $show_custom_terms_settings == true) && ($pagenow == "term.php" || $pagenow == "edit-tags.php" || (isset($_REQUEST["action"]) && $_REQUEST["action"] == "add-tag"))) {
		$options = apply_filters('wpqa_the_term_options',$options,$tax,$term_id,$pagenow);
	}
	return $options;
}
/* Options */
add_filter("wpqa_the_term_options","wpqa_the_term_options",1,4);
function wpqa_the_term_options($options,$tax,$term_id,$pagenow) {
	if (is_admin() && (isset($_REQUEST['taxonomy']) && ($_REQUEST['taxonomy'] == "category" || $_REQUEST['taxonomy'] == wpqa_question_categories || $_REQUEST['taxonomy'] == wpqa_knowledgebase_categories)) && ($pagenow == "term.php" || $pagenow == "edit-tags.php" || (isset($_REQUEST["action"]) && $_REQUEST["action"] == "add-tag"))) {
		// Background Defaults
		$background_defaults = array(
			'color'      => '',
			'image'      => '',
			'repeat'     => 'repeat',
			'position'   => 'top center',
			'attachment' => 'scroll'
		);
		
		// Pull all the sidebars into an array
		$new_sidebars = wpqa_registered_sidebars();
		
		// Share
		$share_array = array(
			"share_facebook" => array("sort" => "Facebook","value" => "share_facebook"),
			"share_twitter"  => array("sort" => "Twitter","value" => "share_twitter"),
			"share_linkedin" => array("sort" => "LinkedIn","value" => "share_linkedin"),
			"share_whatsapp" => array("sort" => "WhatsApp","value" => "share_whatsapp"),
		);
		
		// If using image radio buttons, define a directory path
		$imagepath =  get_template_directory_uri(). '/admin/images/';
		$imagepath_theme =  get_template_directory_uri(). '/images/';

		$options = apply_filters(wpqa_prefix_theme.'_before_terms_options',$options,$tax,$term_id);
		
		if (isset($tax) && ($tax == wpqa_question_categories || $tax == wpqa_knowledgebase_categories)) {
			$options[] = array(
				'name' => ($tax == wpqa_question_categories?esc_html__("Question Category Setting","wpqa"):esc_html__("Knowledgebase Category Setting","wpqa")),
				'type' => 'heading-2'
			);

			$options = apply_filters(wpqa_prefix_theme.'_terms_before_setting',$options,$tax);

			$options[] = array(
				'name' => esc_html__('Choose a custom setting for the cover','wpqa'),
				'id'   => prefix_terms.'custom_cat_cover',
				'type' => 'checkbox'
			);

			$options[] = array(
				'type'      => 'heading-2',
				'div'       => 'div',
				'condition' => prefix_terms.'custom_cat_cover:not(0)'
			);

			$options[] = array(
				'name' => esc_html__('Acivate the cover or not','wpqa'),
				'id'   => prefix_terms.'cat_cover',
				'type' => 'checkbox'
			);
		
			$options[] = array(
				'name'    => esc_html__('Select the share options','wpqa'),
				'id'      => prefix_terms.'cat_share',
				'type'    => 'multicheck',
				'sort'    => 'yes',
				'std'     => $share_array,
				'options' => $share_array
			);

			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end',
				'div'  => 'div'
			);

			$options[] = array(
				'name' => esc_html__('Category image','wpqa'),
				'id'   => prefix_terms.'category_image',
				'type' => 'upload'
			);
			
			$options[] = array(
				'name' => esc_html__('Category icon','wpqa'),
				'id'   => prefix_terms.'category_icon',
				'type' => 'text'
			);
			
			$options[] = array(
				'name' => esc_html__('Category small image','wpqa'),
				'id'   => prefix_terms.'category_small_image',
				'type' => 'upload'
			);
			
			$options[] = array(
				'name' => esc_html__('The color for the category to show it for the icon','wpqa'),
				'id'   => prefix_terms.'category_color',
				'type' => 'color'
			);

			$options = apply_filters(wpqa_prefix_theme.'_terms_after_setting',$options,$tax);

			$options = apply_filters('wpqa_terms_after_setting',$options,$tax);
			
			$options[] = array(
				'name' => esc_html__('Private category?','wpqa'),
				'desc' => ($tax == wpqa_question_categories?esc_html__("Select 'On' to enable private category. (In private categories questions can only be seen by the author of the question and the admin).","wpqa"):esc_html__("Select 'On' to enable private category. (In private categories Knowledgebase, you need to login to see the knowledgebases).","wpqa")),
				'id'   => prefix_terms.'private',
				'type' => 'checkbox'
			);
			
			if ($tax == wpqa_question_categories) {
				$options[] = array(
					'name' => esc_html__('Special category?','wpqa'),
					'desc' => esc_html__("Select 'On' to enable special category. (In a special category, the admin must answer the question before anyone else).","wpqa"),
					'id'   => prefix_terms.'special',
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'name' => esc_html__('New category?','wpqa'),
					'desc' => esc_html__("Select 'On' to enable new category. (In the new category, admin must answer the question before anyone else and the user has asked question and only admin can answer).","wpqa"),
					'id'   => prefix_terms.'new',
					'type' => 'checkbox'
				);
			}
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
		}

		$tax_logo = apply_filters(wpqa_prefix_theme.'_tax_logo',false,$tax,$term_id);
		if ($tax == "category" || $tax == wpqa_question_categories || $tax == wpqa_knowledgebase_categories || $tax_logo == true) {
			$options[] = array(
				'name' => esc_html__("Logo Setting","wpqa"),
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Custom logo','wpqa'),
				'id'   => prefix_terms.'custom_logo',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'type'      => 'heading-2',
				'div'       => 'div',
				'condition' => prefix_terms.'custom_logo:not(0)'
			);
			
			$options[] = array(
				'name'    => esc_html__('Logo display','wpqa'),
				'desc'    => esc_html__('choose Logo display.','wpqa'),
				'id'      => prefix_terms.'logo_display',
				'std'     => 'display_title',
				'type'    => 'radio',
				'options' => array("display_title" => esc_html__("Display site title","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Logo upload','wpqa'),
				'desc'      => esc_html__('Upload your custom logo. ','wpqa'),
				'id'        => prefix_terms.'logo_img',
				'type'      => 'upload',
				'condition' => prefix_terms.'logo_display:is(custom_image)',
				'options'   => array("height" => prefix_terms."logo_height","width" => prefix_terms."logo_width"),
			);
			
			$options[] = array(
				'name'      => esc_html__('Logo retina upload','wpqa'),
				'desc'      => esc_html__('Upload your custom logo retina.','wpqa'),
				'id'        => prefix_terms.'retina_logo',
				'type'      => 'upload',
				'condition' => prefix_terms.'logo_display:is(custom_image)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Logo height','wpqa'),
				"id"        => prefix_terms."logo_height",
				"type"      => "sliderui",
				'std'       => '45',
				"step"      => "1",
				"min"       => "0",
				"max"       => "80",
				'condition' => prefix_terms.'logo_display:is(custom_image)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Logo width','wpqa'),
				"id"        => prefix_terms."logo_width",
				"type"      => "sliderui",
				'std'       => '137',
				"step"      => "1",
				"min"       => "0",
				"max"       => "170",
				'condition' => prefix_terms.'logo_display:is(custom_image)'
			);

			if (isset($tax)) {
				if ($tax == wpqa_question_categories) {
					$logo_single = esc_html__('Enable logo at questions','wpqa');
					$setting_single_name = esc_html__('Enable the setting at questions','wpqa');
					$setting_single_desc = esc_html__('Select ON to enable the setting at inner questions','wpqa');
				}else if ($tax == wpqa_knowledgebase_categories) {
					$logo_single = esc_html__('Enable logo at knowledgebases','wpqa');
					$setting_single_name = esc_html__('Enable the setting at knowledgebases','wpqa');
					$setting_single_desc = esc_html__('Select ON to enable the setting at inner knowledgebases','wpqa');
				}else {
					$logo_single = esc_html__('Enable logo at posts','wpqa');
					$setting_single_name = esc_html__('Enable the setting at posts','wpqa');
					$setting_single_desc = esc_html__('Select ON to enable the setting at inner posts','wpqa');
				}
			}
			
			$options[] = array(
				'name' => (isset($logo_single)?$logo_single:""),
				'id'   => prefix_terms.'logo_single',
				'std'  => 'on',
				'type' => 'checkbox',
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end',
				'div'  => 'div'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
		}
		
		if (isset($tax) && $tax == "category") {
			$options[] = array(
				'name' => esc_html__("Loop Setting","wpqa"),
				'type' => 'heading-2'
			);

			$options[] = array(
				'name' => esc_html__('Choose a custom setting for the posts','wpqa'),
				'id'   => prefix_terms.'custom_blog_setting',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'type'      => 'heading-2',
				'div'       => 'div',
				'condition' => prefix_terms.'custom_blog_setting:not(0)'
			);

			$options[] = array(
				'name'    => esc_html__('Post style','wpqa'),
				'desc'    => esc_html__('Choose post style from here.','wpqa'),
				'id'      => prefix_terms.'post_style',
				'options' => array(
					'style_1' => esc_html__('1 column','wpqa'),
					'style_2' => esc_html__('List style','wpqa'),
					'style_3' => esc_html__('Columns','wpqa'),
				),
				'std'   => 'style_1',
				'type'  => 'radio',
				'class' => 'radio',
			);
			
			$options[] = array(
				'name'      => esc_html__("Activate the masonry style?","wpqa"),
				'id'        => prefix_terms.'masonry_style',
				'type'      => 'checkbox',
				'condition' => prefix_terms.'post_style:is(style_3)',
			);
			
			$options[] = array(
				'name' => esc_html__('Hide the featured image in the loop','wpqa'),
				'desc' => esc_html__('Select ON to hide the featured image in the loop.','wpqa'),
				'id'   => prefix_terms.'featured_image_loop_post',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'id'        => prefix_terms."sort_meta_title_image",
				'condition' => prefix_terms.'post_style:is(style_3)',
				'std'       => array(
								array("value" => "image",'name' => esc_html__('Image','wpqa'),"default" => "yes"),
								array("value" => "meta_title",'name' => esc_html__('Meta and title','wpqa'),"default" => "yes"),
							),
				'type'      => "sort",
				'options'   => array(
								array("value" => "image",'name' => esc_html__('Image','wpqa'),"default" => "yes"),
								array("value" => "meta_title",'name' => esc_html__('Meta and title','wpqa'),"default" => "yes"),
							)
			);
			
			$options[] = array(
				'name'    => esc_html__('Select the meta options','wpqa'),
				'id'      => prefix_terms.'post_meta',
				'type'    => 'multicheck',
				'std'     => array(
					"category_post" => "category_post",
					"title_post"    => "title_post",
					"author_by"     => "author_by",
					"post_date"     => "post_date",
					"post_comment"  => "post_comment",
					"post_views"    => "post_views",
				),
				'options' => array(
					"category_post" => esc_html__("Category post - Work at 1 column only","wpqa"),
					"title_post"    => esc_html__("Title post","wpqa"),
					"author_by"     => esc_html__("Author by - Work at 1 column only","wpqa"),
					"post_date"     => esc_html__("Date meta","wpqa"),
					"post_comment"  => esc_html__("Comment meta","wpqa"),
					"post_views"    => esc_html__("Views stats","wpqa"),
				)
			);
			
			$options[] = array(
				'name' => esc_html__('Read more enable or disable','wpqa'),
				'id'   => prefix_terms.'read_more',
				'std'  => 'on',
				'type' => 'checkbox',
			);
			
			$options[] = array(
				'name'    => esc_html__('Select the share options','wpqa'),
				'id'      => prefix_terms.'post_share',
				'type'    => 'multicheck',
				'condition' => prefix_terms.'post_style:not(style_3)',
				'sort'    => 'yes',
				'std'     => $share_array,
				'options' => $share_array
			);
			
			$options[] = array(
				'name' => esc_html__('Excerpt post','wpqa'),
				'desc' => esc_html__('Put here the excerpt post.','wpqa'),
				'id'   => prefix_terms.'post_excerpt',
				'std'  => 40,
				'type' => 'text',
			);
			
			$options[] = array(
				'name'    => esc_html__('Pagination style','wpqa'),
				'desc'    => esc_html__('Choose pagination style from here.','wpqa'),
				'id'      => prefix_terms.'post_pagination',
				'options' => array(
					'standard'        => esc_html__('Standard','wpqa'),
					'pagination'      => esc_html__('Pagination','wpqa'),
					'load_more'       => esc_html__('Load more','wpqa'),
					'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
					'none'            => esc_html__('None','wpqa'),
				),
				'std'     => 'pagination',
				'type'    => 'radio',
				'class'   => 'radio',
			);
			
			$options[] = array(
				'name' => esc_html__("Post number","wpqa"),
				'desc' => esc_html__("put the post number","wpqa"),
				'id'   => prefix_terms.'post_number',
				'type' => 'text',
				'std'  => "5"
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end',
				'div'  => 'div'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
		}
		
		if (isset($tax) && $tax == wpqa_question_categories) {
			$options[] = array(
				'name' => esc_html__("Loop Setting","wpqa"),
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Choose a custom setting for the questions','wpqa'),
				'id'   => prefix_terms.'custom_question_setting',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'type'      => 'heading-2',
				'div'       => 'div',
				'condition' => prefix_terms.'custom_question_setting:not(0)'
			);

			$options[] = array(
				'name'    => esc_html__('Question style','wpqa'),
				'desc'    => esc_html__('Choose question style from here.','wpqa'),
				'id'      => prefix_terms.'question_columns',
				'options' => array(
					'style_1' => esc_html__('1 column','wpqa'),
					'style_2' => esc_html__('2 columns','wpqa')." - ".esc_html__('Works with sidebar, and full width only.','wpqa'),
				),
				'std'   => 'style_1',
				'type'  => 'radio',
				'class' => 'radio',
			);
			
			$options[] = array(
				'name'      => esc_html__("Activate the masonry style?","wpqa"),
				'id'        => prefix_terms.'masonry_style',
				'type'      => 'checkbox',
				'condition' => prefix_terms.'question_columns:is(style_2)',
			);
			
			$options[] = array(
				'name'    => esc_html__('Select the meta options','wpqa'),
				'id'      => prefix_terms.'question_meta',
				'type'    => 'multicheck',
				'std'     => array(
					"author_by"         => "author_by",
					"question_date"     => "question_date",
					"category_question" => "category_question",
					"question_answer"   => "question_answer",
					"question_views"    => "question_views",
					"bump_meta"         => "bump_meta",
				),
				'options' => array(
					"author_by"         => esc_html__('Author by','wpqa'),
					"question_date"     => esc_html__("Date meta","wpqa"),
					"category_question" => esc_html__("Category question","wpqa"),
					"question_answer"   => esc_html__("Answer meta","wpqa"),
					"question_views"    => esc_html__("Views stats","wpqa"),
					"bump_meta"         => esc_html__('Bump question meta','wpqa'),
				)
			);
			
			$options[] = array(
				'name' => esc_html__("Activate the author image in questions loop?","wpqa"),
				'id'   => prefix_terms.'author_image',
				'type' => 'checkbox',
				'std'  => 'on'
			);
			
			$options[] = array(
				'name' => esc_html__("Activate the vote in loop?","wpqa"),
				'id'   => prefix_terms.'vote_question_loop',
				'type' => 'checkbox',
				'std'  => 'on'
			);
			
			$options[] = array(
				'name'      => esc_html__("Select ON to hide the dislike at questions loop","wpqa"),
				'id'        => prefix_terms.'question_loop_dislike',
				'type'      => 'checkbox',
				'condition' => prefix_terms.'vote_question_loop:not(0)'
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to show the poll in questions loop','wpqa'),
				'id'   => prefix_terms.'question_poll_loop',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name' => esc_html__("Select ON to hide the excerpt in questions","wpqa"),
				'id'   => prefix_terms.'excerpt_questions',
				'type' => 'checkbox',
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_terms.'excerpt_questions:is(0)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name'      => esc_html__('Excerpt question','wpqa'),
				'desc'      => esc_html__('Put here the excerpt question.','wpqa'),
				'id'        => prefix_terms.'question_excerpt',
				'std'       => 40,
				'type'      => 'text',
				'condition' => prefix_terms.'excerpt_questions:is(0)',
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to active the read more button in questions','wpqa'),
				'id'   => prefix_terms.'read_more_question_h',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'name'      => esc_html__('Select ON to activate the read more by jQuery in questions','wpqa'),
				'id'        => prefix_terms.'read_jquery_question_h',
				'type'      => 'checkbox',
				'condition' => prefix_terms.'read_more_question_h:not(0)',
			);
			
			$options[] = array(
				'name' => esc_html__('Select ON to activate to see some answers and add a new answer by jQuery in questions','wpqa'),
				'id'   => prefix_terms.'answer_question_jquery_h',
				'type' => 'checkbox',
			);
			
			$options[] = array(
				'name' => esc_html__('Activate the follow button at questions loop','wpqa'),
				'desc' => esc_html__('Select ON if you want to activate the follow button at questions loop.','wpqa'),
				'id'   => prefix_terms.'question_follow_loop',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name' => esc_html__("Tags at loop enable or disable","wpqa"),
				'id'   => prefix_terms.'question_tags_loop',
				'type' => 'checkbox',
				'std'  => 'on'
			);
			
			$options[] = array(
				'name' => (has_himer() || has_knowly() || has_questy()?esc_html__('Activate the answer at the loop by best answer, most voted, most reacted, last answer or first answer','wpqa'):esc_html__('Activate the answer at the loop by best answer, most voted, last answer or first answer','wpqa')),
				'id'   => prefix_terms.'question_answer_loop',
				'type' => 'checkbox'
			);
			
			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_terms.'question_answer_loop:not(0)',
				'type'      => 'heading-2'
			);

			$answer_show = array(
				'best'   => esc_html__('Best answer','wpqa'),
				'vote'   => esc_html__('Most voted','wpqa'),
				'last'   => esc_html__('Last answer','wpqa'),
				'oldest' => esc_html__('First answer','wpqa'),
			);

			if (has_himer() || has_knowly() || has_questy()) {
				$answer_show['reacted'] = esc_html__('Most reacted','wpqa');
			}
			
			$options[] = array(
				'name'    => esc_html__('Answer type','wpqa'),
				'desc'    => esc_html__("Choose what's the answer you need to show from here.","wpqa"),
				'id'      => prefix_terms.'question_answer_show',
				'options' => $answer_show,
				'std'     => 'best',
				'type'    => 'radio'
			);

			$options[] = array(
				'name'    => esc_html__('Answer place','wpqa'),
				'desc'    => esc_html__("Choose where's the answer to be placed - before or after question meta.","wpqa"),
				'id'      => prefix_terms.'question_answer_place',
				'options' => array(
					'before' => esc_html__('Before question meta','wpqa'),
					'after'  => esc_html__('After question meta','wpqa'),
				),
				'std'     => 'after',
				'type'    => 'radio'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);
			
			$options[] = array(
				'name'    => esc_html__('Pagination style','wpqa'),
				'desc'    => esc_html__('Choose pagination style from here.','wpqa'),
				'id'      => prefix_terms.'question_pagination',
				'options' => array(
					'standard'        => esc_html__('Standard','wpqa'),
					'pagination'      => esc_html__('Pagination','wpqa'),
					'load_more'       => esc_html__('Load more','wpqa'),
					'infinite_scroll' => esc_html__('Infinite scroll','wpqa'),
					'none'            => esc_html__('None','wpqa'),
				),
				'std'     => 'pagination',
				'type'    => 'radio',
				'class'   => 'radio',
			);
			
			$options[] = array(
				'name' => esc_html__("Question number","wpqa"),
				'desc' => esc_html__("put the question number","wpqa"),
				'id'   => prefix_terms.'question_number',
				'type' => 'text',
				'std'  => "5"
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end',
				'div'  => 'div'
			);

			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
		}

		$tax_activate = apply_filters(wpqa_prefix_theme.'_tax_activate',false,$tax,$term_id);
		if ($tax == "category" || $tax == wpqa_question_categories || $tax == wpqa_knowledgebase_categories || $tax_activate == true) {
			$options[] = array(
				'name' => esc_html__("Category Setting","wpqa"),
				'type' => 'heading-2'
			);
			
			$options[] = array(
				'name' => (isset($setting_single_name)?$setting_single_name:""),
				'desc' => (isset($setting_single_desc)?$setting_single_desc:""),
				'id'   => prefix_terms.'setting_single',
				'type' => 'checkbox'
			);

			$options_sidebar = array(
				'default'      => $imagepath.'sidebar_default.jpg',
				'menu_sidebar' => $imagepath.'menu_sidebar.jpg',
				'right'        => $imagepath.'sidebar_right.jpg',
				'full'         => $imagepath.'sidebar_no.jpg',
				'left'         => $imagepath.'sidebar_left.jpg',
				'centered'     => $imagepath.'centered.jpg',
				'menu_left'    => $imagepath.'menu_left.jpg',
			);
			
			$options[] = array(
				'name'    => esc_html__("Category sidebar layout","wpqa"),
				'id'      => prefix_terms."cat_sidebar_layout",
				'std'     => "default",
				'type'    => "images",
				'options' => $options_sidebar
			);
			
			$options[] = array(
				'name'      => esc_html__("Category Page sidebar","wpqa"),
				'id'        => prefix_terms."cat_sidebar",
				'std'       => '',
				'options'   => $new_sidebars,
				'type'      => 'select',
				'condition' => prefix_terms.'cat_sidebar_layout:not(full),'.prefix_terms.'cat_sidebar_layout:not(centered),'.prefix_terms.'cat_sidebar_layout:not(menu_left)'
			);
			
			$options[] = array(
				'name'      => esc_html__("Category Page sidebar 2","wpqa"),
				'id'        => prefix_terms."cat_sidebar_2",
				'std'       => '',
				'options'   => $new_sidebars,
				'type'      => 'select',
				'operator'  => 'or',
				'condition' => prefix_terms.'sidebar:is(menu_sidebar),'.prefix_terms.'sidebar:is(menu_left)'
			);

			$options[] = array(
				'name'    => esc_html__("Light/dark",'wpqa'),
				'desc'    => esc_html__("Light/dark for the category.",'wpqa'),
				'id'      => prefix_terms."cat_skin_l",
				'std'     => "default",
				'type'    => "images",
				'options' => array(
					'default' => $imagepath.'sidebar_default.jpg',
					'light'   => $imagepath.'light.jpg',
					'dark'    => $imagepath.'dark.jpg'
				)
			);
			
			$options[] = array(
				'name'    => esc_html__("Choose Your Skin","wpqa"),
				'class'   => "site_skin",
				'id'      => prefix_terms."cat_skin",
				'std'     => "default",
				'type'    => "images",
				'options' => array(
					'default'    => $imagepath.'default_color.jpg',
					'skin'       => $imagepath.'default.jpg',
					'violet'     => $imagepath.'violet.jpg',
					'bright_red' => $imagepath.'bright_red.jpg',
					'green'      => $imagepath.'green.jpg',
					'red'        => $imagepath.'red.jpg',
					'cyan'       => $imagepath.'cyan.jpg',
					'blue'       => $imagepath.'blue.jpg',
				)
			);
			
			$options[] = array(
				'name' => esc_html__("Primary Color","wpqa"),
				'id'   => prefix_terms.'cat_primary_color',
				'type' => 'color' );
			
			$options[] = array(
				'name'    => esc_html__("Background Type","wpqa"),
				'id'      => prefix_terms.'cat_background_type',
				'std'     => 'default',
				'type'    => 'radio',
				'options' => 
					array(
						"default"           => esc_html__("Default","wpqa"),
						"none"              => esc_html__("None","wpqa"),
						"patterns"          => esc_html__("Patterns","wpqa"),
						"custom_background" => esc_html__("Custom Background","wpqa")
					)
			);

			$options[] = array(
				'name'      => esc_html__("Background Color","wpqa"),
				'id'        => prefix_terms.'cat_background_color',
				'std'       => "#FFF",
				'type'      => 'color',
				'condition' => prefix_terms.'cat_background_type:is(patterns)'
			);
				
			$options[] = array(
				'name'      => esc_html__("Choose Pattern","wpqa"),
				'id'        => prefix_terms."cat_background_pattern",
				'std'       => "bg13",
				'type'      => "images",
				'class'     => "pattern_images",
				'condition' => prefix_terms.'cat_background_type:is(patterns)',
				'options'   => array(
					'bg1'  => $imagepath.'bg1.jpg',
					'bg2'  => $imagepath.'bg2.jpg',
					'bg3'  => $imagepath.'bg3.jpg',
					'bg4'  => $imagepath.'bg4.jpg',
					'bg5'  => $imagepath.'bg5.jpg',
					'bg6'  => $imagepath.'bg6.jpg',
					'bg7'  => $imagepath.'bg7.jpg',
					'bg8'  => $imagepath.'bg8.jpg',
					'bg9'  => $imagepath_theme.'patterns/bg9.png',
					'bg10' => $imagepath_theme.'patterns/bg10.png',
					'bg11' => $imagepath_theme.'patterns/bg11.png',
					'bg12' => $imagepath_theme.'patterns/bg12.png',
					'bg13' => $imagepath.'bg13.jpg',
					'bg14' => $imagepath.'bg14.jpg',
					'bg15' => $imagepath_theme.'patterns/bg15.png',
					'bg16' => $imagepath_theme.'patterns/bg16.png',
					'bg17' => $imagepath.'bg17.jpg',
					'bg18' => $imagepath.'bg18.jpg',
					'bg19' => $imagepath.'bg19.jpg',
					'bg20' => $imagepath.'bg20.jpg',
					'bg21' => $imagepath_theme.'patterns/bg21.png',
					'bg22' => $imagepath.'bg22.jpg',
					'bg23' => $imagepath_theme.'patterns/bg23.png',
					'bg24' => $imagepath_theme.'patterns/bg24.png',
				)
			);

			$options[] = array(
				'name'      => esc_html__( "Custom Background","wpqa"),
				'id'        => prefix_terms.'cat_custom_background',
				'std'       => $background_defaults,
				'options'   => $background_defaults,
				'type'      => 'background',
				'condition' => prefix_terms.'cat_background_type:is(custom_background)'
			);
				
			$options[] = array(
				'name'      => esc_html__("Full Screen Background","wpqa"),
				'desc'      => esc_html__("Select ON to enable Full Screen Background","wpqa"),
				'id'        => prefix_terms.'cat_full_screen_background',
				'type'      => 'checkbox',
				'condition' => prefix_terms.'cat_background_type:is(custom_background)'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);
		}

		$tax_adv = apply_filters(wpqa_prefix_theme.'_tax_adv',false,$tax,$term_id);
		if ($tax == "category" || $tax == wpqa_question_categories || $tax == wpqa_knowledgebase_categories || $tax_adv == true) {
			$options[] = array(
				'name' => esc_html__('Advertising','wpqa'),
				'type' => 'heading-2'
			);

			$options[] = array(
				'type' => 'info',
				'name' => esc_html__('Advertising after header 1','wpqa')
			);
			
			$options[] = array(
				'name'    => esc_html__('Advertising type','wpqa'),
				'id'      => prefix_terms.'header_adv_type_1',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => prefix_terms.'header_adv_img_1',
				'condition' => prefix_terms.'header_adv_type_1:is(custom_image)',
				'type'      => 'upload'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising url','wpqa'),
				'id'        => prefix_terms.'header_adv_href_1',
				'std'       => '#',
				'condition' => prefix_terms.'header_adv_type_1:is(custom_image)',
				'type'      => 'text'
			);

			$options[] = array(
				'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'        => prefix_terms.'header_adv_link_1',
				'std'       => "new_page",
				'type'      => 'select',
				'condition' => prefix_terms.'header_adv_type_1:is(custom_image)',
				'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
				'id'        => prefix_terms.'header_adv_code_1',
				'condition' => prefix_terms.'header_adv_type_1:is(display_code)',
				'type'      => 'textarea'
			);
			
			$options[] = array(
				'type' => 'info',
				'name' => esc_html__('Advertising after left menu','wpqa')
			);
			
			$options[] = array(
				'name'    => esc_html__('Advertising type','wpqa'),
				'id'      => prefix_terms.'left_menu_adv_type',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => prefix_terms.'left_menu_adv_img',
				'type'      => 'upload',
				'condition' => prefix_terms.'left_menu_adv_type:is(custom_image)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising url','wpqa'),
				'id'        => prefix_terms.'left_menu_adv_href',
				'std'       => '#',
				'type'      => 'text',
				'condition' => prefix_terms.'left_menu_adv_type:is(custom_image)'
			);

			$options[] = array(
				'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'        => prefix_terms.'left_menu_adv_link',
				'std'       => "new_page",
				'type'      => 'select',
				'condition' => prefix_terms.'left_menu_adv_type:is(custom_image)',
				'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
				'id'        => prefix_terms.'left_menu_adv_code',
				'type'      => 'textarea',
				'condition' => prefix_terms.'left_menu_adv_type:is(display_code)'
			);
			
			$options[] = array(
				'type' => 'info',
				'name' => esc_html__('Advertising after content','wpqa')
			);
			
			$options[] = array(
				'name'    => esc_html__('Advertising type','wpqa'),
				'id'      => prefix_terms.'content_adv_type',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => prefix_terms.'content_adv_img',
				'type'      => 'upload',
				'condition' => prefix_terms.'content_adv_type:is(custom_image)'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising url','wpqa'),
				'id'        => prefix_terms.'content_adv_href',
				'std'       => '#',
				'type'      => 'text',
				'condition' => prefix_terms.'content_adv_type:is(custom_image)'
			);

			$options[] = array(
				'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'        => prefix_terms.'content_adv_link',
				'std'       => "new_page",
				'type'      => 'select',
				'condition' => prefix_terms.'content_adv_type:is(custom_image)',
				'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html ( Ex: Google ads)','wpqa'),
				'id'        => prefix_terms.'content_adv_code',
				'type'      => 'textarea',
				'condition' => prefix_terms.'content_adv_type:is(display_code)'
			);
			
			$options[] = array(
				'name' => esc_html__('Between questions or posts','wpqa'),
				'type' => 'info'
			);
			
			$options[] = array(
				'name'    => esc_html__('Advertising type','wpqa'),
				'id'      => prefix_terms.'between_adv_type',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","wpqa"),"custom_image" => esc_html__("Custom Image","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','wpqa'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','wpqa'),
				'id'        => prefix_terms.'between_adv_img',
				'condition' => prefix_terms.'between_adv_type:is(custom_image)',
				'type'      => 'upload'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising url','wpqa'),
				'id'        => prefix_terms.'between_adv_href',
				'std'       => '#',
				'condition' => prefix_terms.'between_adv_type:is(custom_image)',
				'type'      => 'text'
			);

			$options[] = array(
				'name'      => esc_html__('Open the page in same page or a new page?','wpqa'),
				'id'        => prefix_terms.'between_adv_link',
				'std'       => "new_page",
				'type'      => 'select',
				'condition' => prefix_terms.'between_adv_type:is(custom_image)',
				'options'   => array("same_page" => esc_html__("Same page","wpqa"),"new_page" => esc_html__("New page","wpqa"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html (Ex: Google ads)','wpqa'),
				'id'        => prefix_terms.'between_adv_code',
				'condition' => prefix_terms.'between_adv_type:not(custom_image)',
				'type'      => 'textarea'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end'
			);

			$options = apply_filters('wpqa_options_after_terms_ads',$options);
		}
	}
	return $options;
}?>