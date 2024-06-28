<?php if ((isset($blog_h) && $blog_h == "blog_h" && !is_array($sort_meta_title_image)) || !is_array($sort_meta_title_image) || (empty($blog_h) && is_single()) || $post_style != "style_3") {
	$sort_meta_title_image = array(array("value" => "meta_title",'name' => esc_html__('Meta and title','discy'),"default" => "yes"),array("value" => "image",'name' => esc_html__('Image','discy'),"default" => "yes"));
}

if (empty($post) && isset($GLOBALS['post'])) {
	$post_data = $post = $GLOBALS['post'];
}else {
	$post_data = $post;
}
$post_author = $post_data->post_author;
$post_id = $post_data->ID;
$post_type = $post_data->post_type;

$user_id                = get_current_user_id();
$is_super_admin         = is_super_admin($user_id);
$active_moderators      = discy_options("active_moderators");
$moderators_permissions = (has_wpqa()?wpqa_user_moderator($user_id):"");
$pending_posts          = (has_wpqa() && wpqa_is_pending_posts() && ($is_super_admin || $active_moderators == "on") && wpqa_is_user_owner() && ($is_super_admin || (isset($moderator_categories) && is_array($moderator_categories) && !empty($moderator_categories)))?true:false);
$moderator_categories   = (has_wpqa()?wpqa_user_moderator_categories($user_id,$post_id):"");
$pending_posts_page     = (has_wpqa() && wpqa_is_pending_posts()?true:false);
$post_link_target       = apply_filters("discy_post_link_target","");

$questions_position = discy_options("between_questions_position");
$adv_type_repeat = discy_options("between_adv_type_repeat");
if (has_wpqa() && wpqa_plugin_version >= "5.8" && !isset($blog_h) && isset($k_ad_p) && (($k_ad_p == $questions_position) || ($adv_type_repeat == "on" && $k_ad_p != 0 && $k_ad_p % $questions_position == 0))) {
	echo wpqa_ads("between_adv_type","between_adv_link","between_adv_code","between_adv_href","between_adv_img","","","aalan-inside".($post_style == "style_3"?" adv-style-3".$post_columns:""),"on");
}

$count_post_all = (int)(has_wpqa()?wpqa_count_comments($post_id):get_comments_number());
$what_post = discy_post_meta("what_post","",false);
$discy_thumbnail_id = discy_post_meta("_thumbnail_id","",false);

$show_featured_image  = "";
if (has_post_thumbnail()) {
	$show_featured_image = 1;
	if ($featured_image == "on" && empty($blog_h) && isset($wp_page_template) && ($wp_page_template == "template-blog.php" || $wp_page_template == "template-home.php")) {
		$show_featured_image = 0;
	}else if ($featured_image == "on" && is_singular()) {
		$show_featured_image = 0;
	}else if ($featured_image == "on" && is_category()) {
		$show_featured_image = 0;
	}else if ($featured_image == "on") {
		$show_featured_image = 0;
	}
}else {
	$show_featured_image = 1;
	$discy_image = discy_image();
	if (!empty($discy_image) && $featured_image == "on") {
		$show_featured_image = 0;
	}
}

if ((is_single() && empty($blog_h)) || (is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php")))) {
	$post_style = "";
}

$featured_style = "";
if (((is_single() && empty($blog_h)) || (is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php")))) && isset($featured_image_style) && $featured_image_style != "" && $featured_image_style != "default") {
	$featured_style = " featured_style_2".(isset($featured_image_width) && $featured_image_style == "custom_size" && $featured_image_width > 350?" featured_style_350":"");
}

$custom_permission = discy_options("custom_permission");
$show_post = discy_options("show_post");
if (is_user_logged_in()) {
	$user_is_login = get_userdata($user_id);
	$roles = (isset($user_is_login->allcaps)?$user_is_login->allcaps:array());
}
if ('post' !== $post_type || ('post' === $post_type && ($custom_permission != "on" || $is_super_admin || (is_user_logged_in() && isset($roles["show_post"]) && $roles["show_post"] == 1) || (!is_user_logged_in() && $show_post == "on") || ($user_id > 0 && $user_id == $post_author)))) {
	$post_class = 'article-post article-post-only clearfix';
	$post_class .= $featured_style;
	$post_class .= ($post_data->post_content == " post-no-content"?" post--content":"");
	$post_class .= ($post_style != "style_2" && $post_style != "style_3"?" post-style-1":"");
	$post_class .= ($post_style == "style_2"?" post-style-2":"");
	$post_class .= ($post_style == "style_3"?" post-style-3 post-with-columns".$post_columns.($masonry_style == "on"?" post-masonry":""):"");
	if (isset($blog_h) && $blog_h == "blog_h") {?>
		<div id="post-<?php echo (int)$post_id?>" <?php post_class($post_class);?>>
	<?php }else {?>
		<article id="post-<?php echo (int)$post_id?>" <?php post_class($post_class);?>>
			<?php do_action("wpqa_post_article",$post_id,$post_type,(is_sticky()?"sticky":""),$user_id,$post_author);
			if (is_singular("post")) {
				do_action("wpqa_post_content",$post_id,$user_id,$post_author);
			}else {
				do_action("wpqa_post_content_loop",$post_id,$user_id,$post_author);
			}
	}
		if ($pending_posts) {?>
			<div class="load_span"><span class="loader_2"></span></div>
		<?php }?>
		<div class="single-inner-content">
			<?php if (isset($sort_meta_title_image) && is_array($sort_meta_title_image)) {
				foreach ($sort_meta_title_image as $sort_meta_title_image_key => $sort_meta_title_image_value) {
					if (isset($sort_meta_title_image_value["value"]) && $sort_meta_title_image_value["value"] == "image") {
						if ($post_style == "style_2" || $post_style == "style_3") {
							if ($what_post != "none") {
								include locate_template("theme-parts/banner.php");
							}
							do_action("wpqa_before_post_list",$post_id,$post_data,(isset($blog_h)?$blog_h:""));?>
							<div class="post-list<?php echo ($what_post == "none" || (!$what_post || $what_post == "image" || $what_post == "image_lightbox" || $what_post == "audio") && (!$discy_thumbnail_id || ($featured_image != 0 && $featured_image == "on"))?" post-list-0":"")?>">
						<?php }
					}else if (isset($sort_meta_title_image_value["value"]) && $sort_meta_title_image_value["value"] == "meta_title") {?>
						<header class="article-header<?php echo (((empty($blog_h) && isset($wp_page_template) && ($wp_page_template == "template-blog.php" || $wp_page_template == "template-home.php")) || !is_page()) && !is_attachment() && $author_by == "on"?"":" header-no-author").($author_by != "on" && $post_date != "on" && $title_post != "on" && $category_post != "on" && $post_comment != "on" && $post_views != "on"?" header-no-meta":"")?>">
							<?php if ((isset($blog_h) && $blog_h == "blog_h") || ((isset($wp_page_template) && ($wp_page_template == "template-blog.php" || $wp_page_template == "template-home.php")) || !is_page()) && !is_attachment()) {
								if ($post_style == "style_2" || $post_style == "style_3") {
									$category_post = $author_by = "";
									if (empty($blog_h) && $post_style == "style_3") {
										$read_more = $post_share = "";
									}
								}
								if ($post_date == "on" || $category_post == "on" || $post_comment == "on" || $post_views == "on") {
									do_action("discy_before_post_meta",$post_data);?>
									<div class="post-meta">
										<?php discy_meta($post_date,$category_post,$post_comment,"","",$post_views,$post_id,$post_data)?>
									</div>
									<?php do_action("discy_after_post_meta",$post_data);
								}
							}
							
							if ($title_post == "on" && !is_attachment()) {
								if ( (is_single() && empty($blog_h)) || (is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php"))) ) {
									$custom_page_setting = discy_post_meta("custom_page_setting");
									if ($custom_page_setting == "on") {
										$breadcrumbs = discy_post_meta("breadcrumbs");
									}else {
										$breadcrumbs = discy_options("breadcrumbs");
									}
									$breadcrumbs_style = discy_options("breadcrumbs_style");
									$breadcrumbs_content_title = discy_options("breadcrumbs_content_title");
									if ($breadcrumbs != "on" || ($breadcrumbs == "on" && ($breadcrumbs_style != "style_2" || ($breadcrumbs_content_title != "on" && $breadcrumbs_style == "style_2")))) {
										the_title( '<'.($breadcrumbs == "on" && $breadcrumbs_style == "style_2"?"h2":"h1").' class="'.(isset($title_post_style) && $title_post_style == "style_2"?"post-title-2":"post-title").'">'.(isset($title_post_style) && $title_post_style == "style_2" && isset($title_post_icon) && $title_post_icon != ""?"<i class='".$title_post_icon."'></i>":"").(is_sticky()?"<i class='icon-pencil'></i>":""), '</'.($breadcrumbs == "on" && $breadcrumbs_style == "style_2"?"h2":"h1").'>' );
									}
								}else {
									the_title( '<h2 class="post-title"><a'.$post_link_target.' class="post-title" href="' . esc_url(get_permalink($post_id)) . '" rel="bookmark">'.(is_sticky()?"<i class='icon-pencil'></i>":""), '</a></h2>' );
								}
							}
							do_action("discy_after_title",$post_data);
							if ( ((empty($blog_h) && isset($wp_page_template) && ($wp_page_template == "template-blog.php" || $wp_page_template == "template-home.php")) || !is_page()) && !is_attachment() && $author_by == "on" ) {
								if ($post_author > 0) {
									echo '<a class="post-author" rel="author" href="' . esc_url(has_wpqa()?wpqa_profile_url($post_author):"") . '">'.esc_html(get_the_author()).'</a>';
								}else {
									$post_username = discy_post_meta("post_username","",false);
									echo esc_html($post_username);
								}
							}
							if ($post_style != "style_2" && $post_style != "style_3" && $what_post != "none") {
								include locate_template("theme-parts/banner.php");
							}
							do_action("discy_content_before_header");?>
						</header>
					<?php }
				}
			}?>
			
			<div class="post-wrap-content<?php echo ((is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php")))?"":" post-content ").(is_attachment()?" post-attachment ":"")?>">
				<div class="<?php echo (empty($blog_h) && isset($wp_page_template) && $wp_page_template == "template-contact.php"?"post-contact":"post-content-text")?>">
					<?php do_action("discy_before_post_content",$post_id,$post_data);
					$get_the_content = get_the_content();
					$get_the_content = apply_filters('the_content',$get_the_content);
					if ((is_single() && empty($blog_h)) || (is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php")))) {
						if (is_attachment()) {
							$site_width = (int)discy_options("site_width");
							$mins_width = ($site_width > 1170?$site_width-1170:0);
							if (wp_attachment_is_image()) {
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
								$image = discy_get_aq_resize_img_url($img_width,$img_height,$post_data->ID);?>
								<div class="wp-caption aligncenter">
									<img width="<?php echo esc_attr($img_width)?>" height="<?php echo esc_attr($img_height)?>" class="attachment-<?php echo esc_attr($img_width)?>x<?php echo esc_attr($img_height)?>" alt="<?php echo esc_attr( get_the_title() ); ?>" src="<?php echo esc_url($image)?>">
									<?php if (!empty($post_data->post_excerpt)) {?>
										<p class="wp-caption-text"><?php echo get_the_excerpt(); ?></p>
									<?php }?>
								</div>
							<?php }else {?>
								<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment"><?php echo basename(get_permalink($post_id)); ?></a><br>
								<p><?php if ( !empty( $post_data->post_excerpt ) ) the_excerpt(); ?></p>
							<?php }?>
							<div class="post-inner">
								<div class="post-inner-content"><?php echo make_clickable($get_the_content)?></div>
							</div><!-- End post-inner -->
						<?php }else {
							$show_post_filter = apply_filters('discy_show_post_filter',true);
							if ($show_post_filter == true) {
								do_action("discy_before_content",$post_id);
								echo make_clickable($get_the_content);
								do_action("discy_after_content",$post_id);
							}
						}
					}else {
						$show_full_text = apply_filters("discy_show_full_text",true);
						if ($show_full_text == true && strpos($get_the_content,'more-link') === false && $post_data->post_content != "") {?>
							<div class="all_not_single_post_content"><p><?php discy_excerpt($post_excerpt,$excerpt_type);?></p></div>
							<?php if ($pending_posts) {?>
								<div class='all_single_post_content discy_hide'>
									<?php echo make_clickable($get_the_content)?>
								</div>
							<?php }
						}else {
							echo make_clickable($get_the_content);
						}
					}?>
				</div>
				<?php // if (!empty($post->post_password)) {?>
				<?php if (empty($blog_h) && isset($wp_page_template) && $wp_page_template == "template-faqs.php" && has_wpqa() && wpqa_plugin_version >= "5.9.8") {
					$custom_faqs = discy_post_meta("faqs");
					include wpqa_get_template("faqs.php","theme-parts/");
				}
				if (empty($blog_h) && isset($wp_page_template) && $wp_page_template == "template-categories.php") {
					include locate_template("theme-parts/categories.php");
				}
				if (empty($blog_h) && isset($wp_page_template) && $wp_page_template == "template-tags.php") {
					include locate_template("theme-parts/tags.php");
				}
				if (empty($blog_h) && isset($wp_page_template) && $wp_page_template == "template-users.php") {
					include locate_template("theme-parts/users.php");
				}
				if ( (is_single() && empty($blog_h)) || (is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php"))) ) {
					wp_link_pages(array('before' => '<div class="pagination post-pagination">','after' => '</div>','link_before' => '<span>','link_after' => '</span>'));
					
					if ( $post_tags == "on" && 'post' === $post_type ) {
						$terms = wp_get_object_terms( $post_id, 'post_tag' );
						if (isset($terms) && is_array($terms) && !empty($terms)) {
							echo '<div class="tagcloud">';
								$terms_array = array();
								foreach ($terms as $term) :
									if (isset($term->slug) && isset($term->name)) {
										$get_term_link = get_term_link($term);
										if (is_string($get_term_link)) {
											echo '<a href="'.$get_term_link.'">'.$term->name.'</a>';
										}
									}
								endforeach;
							echo "</div>";
						}
					}
				}
				do_action("wpqa_after_post_tags",$post_id,$post_data);?>
			</div>
			
			<?php do_action("wpqa_before_post_footer",$post_id,$post_data,(isset($blog_h)?$blog_h:""));

			if (!is_page_template("template-users.php") && !is_page_template("template-contact.php") && !is_page_template("template-faqs.php") && !is_page_template("template-categories.php") && !is_page_template("template-tags.php")) {?>
				<footer<?php echo ($pending_posts?" class='pending-post-footer'":"")?>>
					<?php do_action("discy_action_before_edit_post",$post_id,(isset($blog_h)?$blog_h:""));

					if ( ((is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php")))) && get_edit_post_link() ) {
						edit_post_link(sprintf(esc_html__( 'Edit %s', 'discy' ),the_title( '<span class="screen-reader-text">"', '"</span>', false )),'<span class="edit-link">','</span>');
					}
					
					if (is_single() && empty($blog_h) && !is_attachment()) {
						$post_delete   = discy_options("post_delete");
						$can_edit_post = discy_options("can_edit_post");
						$edit = ($is_super_admin || ((($user_id == $post_author && $post_author > 0)) && $can_edit_post == "on") || ($moderator_categories == true && isset($moderators_permissions['edit']) && $moderators_permissions['edit'] == "edit")?true:false);
						$delete = ($is_super_admin || ((($user_id == $post_author && $post_author > 0)) && $post_delete == "on") || ($moderator_categories == true && isset($moderators_permissions['delete']) && $moderators_permissions['delete'] == "delete")?true:false);
						if (has_wpqa() && ($edit == true || $delete == true)) {
							if ($edit == true) {
								echo '<span class="edit-link"><a href="'.wpqa_edit_permalink($post_id,"post").'">'.esc_html__("Edit","discy").'</a></span>';
							}
							if ($delete == true) {
								echo '<span class="delete-link post-delete"><a href="'.esc_url_raw(add_query_arg(array("activate_delete" => true,"delete" => $post_id,"wpqa_delete_nonce" => wp_create_nonce("wpqa_delete_nonce")),get_permalink($post_id))).'">'.esc_html__("Delete","discy").'</a></span>';
							}
						}
						do_action("discy_content_after_links");
					}
					if (has_wpqa() && $pending_posts) {
						wpqa_review_post($post_data,$is_super_admin,$moderators_permissions);
					}else {
						if ( (strpos(get_the_content(),'more-link') !== false || $read_more == "on") && !is_single() && ((isset($wp_page_template) && ($wp_page_template == "template-blog.php" || $wp_page_template == "template-home.php")) || !is_page()) ) {?>
							<a<?php echo esc_attr($post_link_target)?> class="post-read-more" href="<?php echo esc_url(has_wpqa() && $post_type == "posts"?wpqa_custom_permalink($post_id,"view_group_posts","view_group_post"):get_permalink($post_id))?>" rel="bookmark" title="<?php esc_attr_e('Read','discy')?> <?php the_title()?>"><?php esc_html_e('Read more','discy')?></a>
						<?php }
						if (has_wpqa() && empty($blog_h)) {
							$share_facebook = (isset($post_share["share_facebook"]["value"])?$post_share["share_facebook"]["value"]:"");
							$share_twitter  = (isset($post_share["share_twitter"]["value"])?$post_share["share_twitter"]["value"]:"");
							$share_linkedin = (isset($post_share["share_linkedin"]["value"])?$post_share["share_linkedin"]["value"]:"");
							$share_whatsapp = (isset($post_share["share_whatsapp"]["value"])?$post_share["share_whatsapp"]["value"]:"");
							wpqa_share($post_share,$share_facebook,$share_twitter,$share_linkedin,$share_whatsapp);
						}
					}?>
				</footer>
			<?php }
			if ($post_style == "style_2" || $post_style == "style_3") {?>
				</div><!-- End post-list -->
				<?php do_action("wpqa_after_post_list",$post_id,$post_data);
			}?>
		</div><!-- End single-inner-content -->
	<?php if (isset($blog_h) && $blog_h == "blog_h") {?>
		</div>
	<?php }else {
		if (is_single()) {
			do_action('discy_after_post_article',$post_id);
		}?>
		</article><!-- End article -->
	<?php }

	if ( ( (is_single() && empty($blog_h)) || (is_page() && empty($blog_h) && (empty($wp_page_template) || ($wp_page_template != "template-blog.php" && $wp_page_template != "template-home.php"))) ) && !is_attachment() ) :
		if (empty($order_sections)) :
			$order_sections = array(
				"author"        => array("sort" => esc_html__("About the author","discy"),"value" => "author"),
				"next_previous" => array("sort" => esc_html__("Next and Previous articles","discy"),"value" => "next_previous"),
				"advertising"   => array("sort" => esc_html__("Advertising","discy"),"value" => "advertising"),
				"related"       => array("sort" => esc_html__("Related articles","discy"),"value" => "related"),
				"comments"      => array("sort" => esc_html__("Comments","discy"),"value" => "comments"),
			);
		endif;
		foreach ($order_sections as $key_r => $value_r) :
			if ($value_r["value"] == "") :
				unset($order_sections[$key_r]);
			else :
				if (!is_page_template("template-blog.php") && !is_page_template("template-home.php") && !is_page_template("template-users.php") && !is_page_template("template-contact.php") && !is_page_template("template-faqs.php") && !is_page_template("template-categories.php") && !is_page_template("template-tags.php") && $value_r["value"] == "author" && isset($post_author)) :
					$the_author_meta_description = get_the_author_meta("description",$post_author);
					if ($the_author_meta_description != "") :
						do_action("wpqa_author",array("user_id" => $post_author,"author_page" => "single-author","owner" => "","type_post" => "","widget" => "single-author"));
					endif;
				elseif (is_single() && $value_r["value"] == "next_previous") :
					if ($post_nav_category == "on") {
						$previous_post = get_previous_post(true,'','category');
						$next_post = get_next_post(true,'','category');
					}else {
						$previous_post = get_previous_post();
						$next_post = get_next_post();
					}
					if ((isset($previous_post) && is_object($previous_post)) || (isset($next_post) && is_object($next_post))) :?>
						<div class="page-navigation page-navigation-single clearfix">
							<?php do_action("discy_content_before_previous")?>
							<div class="row row-warp">
								<?php if (isset($previous_post) && is_object($previous_post)) {?>
									<div class="col col6 col-nav-previous">
										<div class="nav-previous">
											<div class="navigation-content">
												<span class="navigation-i"><i class="icon-left-thin"></i></span>
												<span class="navigation-text"><?php esc_html_e('Previous article',"discy");?></span>
												<div class="clearfix"></div>
												<?php previous_post_link('%link');?>
											</div>
										</div>
									</div>
								<?php }
								if (isset($next_post) && is_object($next_post)) {?>
									<div class="col col6 col-nav-next">
										<div class="nav-next">
											<div class="navigation-content">
												<span class="navigation-i"><i class="icon-right-thin"></i></span>
												<span class="navigation-text"><?php esc_html_e('Next article',"discy");?></span>
												<div class="clearfix"></div>
												<?php next_post_link('%link')?>
											</div>
										</div>
									</div>
								<?php }?>
							</div>
						</div><!-- End page-navigation -->
					<?php endif;
				elseif (has_wpqa() && wpqa_plugin_version >= "5.8" && !is_page_template("template-blog.php") && !is_page_template("template-home.php") && !is_page_template("template-users.php") && !is_page_template("template-contact.php") && !is_page_template("template-faqs.php") && !is_page_template("template-categories.php") && !is_page_template("template-tags.php") && $value_r["value"] == "advertising") :
					echo wpqa_ads("share_adv_type","share_adv_link","share_adv_code","share_adv_href","share_adv_img","","on","aalan-inside");
				elseif (is_single() && $value_r["value"] == "related") :
					include locate_template("theme-parts/related.php");
				elseif (!is_page_template("template-blog.php") && !is_page_template("template-home.php") && !is_page_template("template-users.php") && !is_page_template("template-contact.php") && !is_page_template("template-faqs.php") && !is_page_template("template-categories.php") && !is_page_template("template-tags.php") && $value_r["value"] == "comments" && (comments_open() || $count_post_all > 0)) :
					comments_template();
				endif;
			endif;
		endforeach;
	endif;
}else {
	echo '<article class="private-post article-post clearfix">
		<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("Sorry, you do not have permission to view posts.","discy").'</p></div>
		'.(has_wpqa()?wpqa_paid_subscriptions(true):'').'
	</article>';
}?>