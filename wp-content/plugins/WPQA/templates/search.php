<?php

/* @author    2codeThemes
*  @package   WPQA/templates
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$search_type     = wpqa_search_type();
$search_value    = wpqa_search();
$paged           = wpqa_paged();
$post_pagination = 'pagination';
$search_attrs    = wpqa_options("search_attrs");?>
<div class="section-all-search">
	<?php $show_search_form_filter = apply_filters("wpqa_show_search_form_filter",true,$search_type);
	if ($show_search_form_filter == true) {
		include wpqa_get_template(array('search-form.php'));
	}else {
		do_action("wpqa_action_search_form",$search_type);
	}
	$user_id = get_current_user_id();

	if ($search_value != "" && ($search_type == "answers" && isset($search_attrs["answers"]["value"]) && $search_attrs["answers"]["value"] == "answers") || ($search_type == "comments" && isset($search_attrs["comments"]["value"]) && $search_attrs["comments"]["value"] == "comments")) {
		$current = max(1,$paged);
		$post_number = apply_filters('wpqa_search_per_page',get_option("posts_per_page"));
		$offset = ($paged -1) * $post_number;
		$block_users = wpqa_options("block_users");
		$author__not_in = array();
		if ($block_users == "on") {
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = array("post_author__not_in" => $get_block_users,"author__not_in" => $get_block_users);
				}
			}
		}
		$args = array_merge($author__not_in,array("status" => "approve",'search' => $search_value,'number' => $post_number,'offset' => $offset,"meta_query" => array(array("key" => "answer_question_private","compare" => "NOT EXISTS")),'post_type' => ($search_type == "answers"?wpqa_questions_type:"post")));
		$comments_all = get_comments($args);
		if (!empty($comments_all)) {
			$total = wpqa_count_the_comments($search_type,array(),"",$search_value,(isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)?$get_block_users:array()));
			$max_num_pages = ceil($total/$post_number);
			$k_ad = -1;
			$zero_comment_index = 0;
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
			?>
			<div class="page-content commentslist block-section-div">
				<ol class="commentlist clearfix<?php echo ($pagination_args["total"] > 1?"":" commentlist-no-pagination")?>">
					<?php foreach ($comments_all as $comment) {$k_ad++;
						$yes_private = wpqa_private($comment->comment_post_ID,get_post($comment->comment_post_ID)->post_author,get_current_user_id());
						if ($yes_private == 1) {
								$comment_id = esc_html($comment->comment_ID);
								$get_post_type = get_post_type($comment->comment_post_ID);
								wpqa_comment($comment,"","",($get_post_type == wpqa_questions_type || $get_post_type == wpqa_asked_questions_type?"answer":"comment"),"",$k_ad,"",array("comment_with_title" => true,"show_replies" => false));?>
							</li>
						<?php }else {?>
							<li class="comment">
								<div class="comment-body clearfix">
									<?php echo '<div class="alert-message alert-message-warning"><i class="icon-flag"></i><p>'.esc_html__("Sorry it is a private answer.","wpqa").'</p></div>';?>
								</div>
							</li>
						<?php }
					}?>
				</ol>
			</div>
		<?php }else {
			include wpqa_get_template("search-none.php","theme-parts/");
		}
		if ($comments_all && $pagination_args["total"] > 1) {?>
			<div class="main-pagination"><div class='pagination'><?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?></div></div>
		<?php }
	}else if ($search_value != "" && $search_type == "users" && isset($search_attrs["users"]["value"]) && $search_attrs["users"]["value"] == "users") {
		include locate_template("theme-parts/users.php");
	}else if ($search_value != "" && (($search_type == wpqa_question_categories && isset($search_attrs[wpqa_question_categories]["value"]) && $search_attrs[wpqa_question_categories]["value"] == wpqa_question_categories) || ($search_type == wpqa_knowledgebase_categories && isset($search_attrs[wpqa_knowledgebase_categories]["value"]) && $search_attrs[wpqa_knowledgebase_categories]["value"] == wpqa_knowledgebase_categories) || ($search_type == "category" && isset($search_attrs["category"]["value"]) && $search_attrs["category"]["value"] == "category"))) {
		include locate_template("theme-parts/categories.php");
	}else if ($search_value != "" && (($search_type == wpqa_question_tags && isset($search_attrs[wpqa_question_tags]["value"]) && $search_attrs[wpqa_question_tags]["value"] == wpqa_question_tags) || ($search_type == wpqa_knowledgebase_tags && isset($search_attrs[wpqa_knowledgebase_tags]["value"]) && $search_attrs[wpqa_knowledgebase_tags]["value"] == wpqa_knowledgebase_tags) || ($search_type == "post_tag" && isset($search_attrs["post_tag"]["value"]) && $search_attrs["post_tag"]["value"] == "post_tag"))) {
		include locate_template("theme-parts/tags.php");
	}else if ($search_value != "" && $search_type == "groups" && isset($search_attrs["groups"]["value"]) && $search_attrs["groups"]["value"] == "groups") {
		$group_search = array('s' => $search_value);
		include locate_template("theme-parts/loop-groups.php");
	}else {
		$search_value = apply_filters("wpqa_filter_search_value",$search_value,$search_type);
		if ($search_value != "") {
			$meta_query = array();
			$filter_search_type = apply_filters("wpqa_search_type_filter",false,$search_type);
			if ($filter_search_type == true) {
				$post_type_array = apply_filters("wpqa_search_post_type_array","",$search_type);
			}else if ($search_type == "posts" && isset($search_attrs["posts"]["value"]) && $search_attrs["posts"]["value"] == "posts") {
				$its_post_type = "post";
				$post_type_array = array('post');
			}else if ($search_type == "knowledgebases" && isset($search_attrs["knowledgebases"]["value"]) && $search_attrs["knowledgebases"]["value"] == "knowledgebases") {
				$its_post_type = wpqa_knowledgebase_type;
				$post_type_array = array(wpqa_knowledgebase_type);
			}else {
				$its_post_type = wpqa_questions_type;
				$asked_questions_search = wpqa_options("asked_questions_search");
				$meta_query = ($asked_questions_search == "on"?array("meta_query" => array("relation" => "or",array("key" => "user_id","compare" => "EXISTS"),array("key" => "question_private","compare" => "NOT EXISTS"),array("key" => "user_id","compare" => "NOT EXISTS"))):array("meta_query" => array(array("key" => "question_private","compare" => "NOT EXISTS"))));
				$post_type_array = array(wpqa_questions_type);
				$post_type_array = ($asked_questions_search == "on"?array(wpqa_questions_type,wpqa_asked_questions_type):$post_type_array);
			}
			$block_users = wpqa_options("block_users");
			$author__not_in = array();
			if ($block_users == "on") {
				$user_id = get_current_user_id();
				if ($user_id > 0) {
					$get_block_users = get_user_meta($user_id,"wpqa_block_users",true);
					if (is_array($get_block_users) && !empty($get_block_users)) {
						$author__not_in = array("author__not_in" => $get_block_users);
					}
				}
			}
			$array_data = array_merge(array('s' => $search_value,'paged' => $paged,'post_type' => $post_type_array),$meta_query,$author__not_in);
			$array_data = apply_filters("wpqa_array_data_filter",$array_data,$search_type);
			$wpqa_query = new WP_Query($array_data);
			$pagination_show = "yes";
			if ($filter_search_type != true) {
				$loop_page = "loop-setting.php";
				if (isset($its_post_type) && ($its_post_type == wpqa_questions_type || $its_post_type == wpqa_asked_questions_type)) {
					$loop_page = "question-setting.php";
				}else if (isset($its_post_type) && $its_post_type == wpqa_knowledgebase_type) {
					$loop_page = "knowledgebase-setting.php";
				}
				if (!isset($loop_setting) || (isset($loop_setting) && $loop_setting != str_replace(".php","",$loop_page))) {
					include wpqa_get_template($loop_page,"includes/");
				}
			}?>
			<section<?php do_action("wpqa_search_section_tag",$search_type)?> class="loop-section<?php echo ((isset($post_style) && $post_style == "style_3") || (isset($question_columns) && $question_columns == "style_2")?" section-post-with-columns":"")?>">
				<?php if ( isset($wpqa_query) && $wpqa_query->have_posts() ) :
					$k_ad_p = 0;
					$zero_index = $post_count = 0;
					global $post;
					$more_link = get_next_posts_link("");?>
					<h2 class="screen-reader-text"><?php echo esc_html__("Search results", "wpqa")?></h2>
					<div class="post-articles<?php do_action("wpqa_post_articles_tag",$search_type);echo (isset($its_post_type) && ($its_post_type == wpqa_questions_type || $its_post_type == wpqa_asked_questions_type)?" question-articles".(isset($question_columns) && $question_columns == "style_2"?" row row-boot row-warp":""):"").(isset($its_post_type) && $its_post_type  == wpqa_knowledgebase_type?" knowledgebase-articles":"").(isset($its_post_type) && $its_post_type  == "post"?(isset($post_style) && $post_style == "style_3"?" row row-boot row-warp":""):"").(isset($masonry_style) && $masonry_style == "on"?" isotope":"").($post_pagination == "none"?" no-pagination":"").(empty($more_link)?" articles-no-pagination":"")?>">
						<?php while ($wpqa_query->have_posts()) : $wpqa_query->the_post();
							if ($filter_search_type == true) {
								do_action("wpqa_search_include_loop",$search_type);
							}else {
								include wpqa_get_template("loop-action.php","theme-parts/");
							}
							$k_ad_p++;
						endwhile;?>
					</div><!-- End post-articles -->
					<?php wpqa_load_pagination(array(
						"post_pagination" => (isset($post_pagination)?$post_pagination:"pagination"),
						"max_num_pages" => (isset($max_num_pages)?$max_num_pages:(isset($wpqa_query->max_num_pages)?$wpqa_query->max_num_pages:"")),
						"it_answer_pagination" => (isset($it_answer_pagination)?$it_answer_pagination:false),
						"its_post_type" => (isset($its_post_type)?$its_post_type:false),
						"wpqa_query" => (isset($wpqa_query)?$wpqa_query:null),
						"search_type" => $search_type,
						"args" => $array_data,
					));
					wp_reset_postdata();
				else :
					include wpqa_get_template("search-none.php","theme-parts/");
				endif;?>
			</section><!-- End section -->
		<?php }
	}
	do_action("wpqa_after_search_results");?>
</div>