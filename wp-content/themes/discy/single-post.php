<?php get_header();
	include locate_template("includes/header-part.php");
	if (has_wpqa() && wpqa_plugin_version >= "5.9.8") {
		include wpqa_get_template("logged-only.php","theme-parts/");
	}
	$page_id = $post_id_main = $post->ID;
	$theme_sidebar_all = $theme_sidebar = (has_wpqa() && wpqa_plugin_version >= "5.7"?wpqa_sidebars("sidebar_where"):"");
	$remove_question_slug = discy_options("remove_question_slug");
	$remove_asked_question_slug = discy_options("remove_asked_question_slug");
	if (($remove_question_slug == "on" || $remove_asked_question_slug == "on") && is_singular("post")) {
		$array_data = array("p" => $page_id);
		$discy_query = new WP_Query($array_data);
	}
	$its_post_type = $post->post_type;
	if (has_wpqa() && wpqa_plugin_version >= "5.9.8") {
		$loop_page = "loop-setting.php";
		if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) {
			$loop_page = "question-setting.php";
		}else if (is_singular(wpqa_knowledgebase_type)) {
			$loop_page = "knowledgebase-setting.php";
		}
		include wpqa_get_template($loop_page,"includes/");
	}else {
		include locate_template("includes/loop-setting.php");
	}
	if ( (($remove_question_slug == "on" || $remove_asked_question_slug == "on") && isset($discy_query) && $discy_query->have_posts()) || have_posts() ) :?>
		<div class="post-articles bc <?php echo (has_wpqa() && (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type))?" question-articles".(isset($question_columns) && $question_columns == "style_2" && isset($masonry_style) && $masonry_style == "on"?" isotope":""):"")?>">
			<?php if (($remove_question_slug == "on" || $remove_asked_question_slug == "on") && isset($discy_query) && $discy_query->have_posts()) :
				while ($discy_query->have_posts()) : $discy_query->the_post();
					do_action("discy_action_before_post_content",$post->ID,$post->post_author);
					do_action("wpqa_action_before_post_content",$post->ID,$post->post_author);
					include locate_template("theme-parts/content.php");
					do_action("discy_action_after_post_content",$post->ID,$post->post_author);
					do_action("wpqa_action_after_post_content",$post->ID,$post->post_author);
				endwhile;
			else :
				while ( have_posts() ) : the_post();
					do_action("discy_action_before_post_content",$post->ID,$post->post_author);
					do_action("wpqa_action_before_post_content",$post->ID,$post->post_author);
					$has_have_posts = true;
					// include locate_template("theme-parts/loop.php");
					/* Page content start 
					 include locate_template("theme-parts/loop.php");
					*/ ?>
					 
					<div class="col col5" id="mr-meta"> 
						
						<?php 
							$post_catgeories = get_custom_post_categories($post->ID);

							if (count($post_catgeories) > 1)
							{
								echo '<div class="post-categories-data">';
									foreach ($post_catgeories as $index => $category)
									{
										echo '<h5><a href="/category/'. $category['slug'] .'" style="text-transform: uppercase;">'.$category['name'].'</a></h5>';
										if ($index < count($post_catgeories) - 1)
										{
											echo '&nbsp, ';
										}
									}
								echo '</div>';
							}
							else if(count($post_catgeories)==1)
							{
								echo '<div class="post-categories-data">';
									echo '<h5><a href="/category/'. $post_catgeories[0]['slug'] .'" style="text-transform: uppercase;">'.$post_catgeories[0]['name'].'</a></h5>';
								echo '</div>';
							}

							echo '<h1>' . get_the_title() . '</h1>'; 
							$url = get_author_posts_url(get_the_author_meta('ID'));
							$author = get_the_author();
							$post_date = get_the_date( 'F j, Y' ); 
							echo '<h6 class="entry-title">By <strong><a href="'. $url . '">'. $author .'</a></strong></h6>';
							echo '<h6>' . $post_date . '</h6>';
							echo do_shortcode('[Sassy_Social_Share]'); 
						?>
					</div>
					
					<div class="col col7" id="mr-image"> 
						<?php echo get_the_post_thumbnail( $post_id, 'full' ); ?>
					</div>
					
					<div class="clearfix"></div>
					
					<div class="col col8" id="mr-content"> 
						<?php echo get_the_content(); 
							$post_tags = get_the_tags();
							if($post_tags)
							{
								echo '<hr>';
								echo '</br>';
								echo '<span>TAGS  </span>';
								if ( $post_tags ) {
									foreach( $post_tags as $tag ) {
										echo '<a class="mrkt-tag" href="' . esc_attr( get_tag_link( $tag->term_id ) ) . '">' . $tag->name . ' </a> '; 
									}
								}
							}
						?>
					</div>
					
					<div class="col col4" id="recom-title"> 
						<?php
							echo '<b><hr></b>';
							echo '<h3> Recommended Content </h3>';  
						?>

						<?php if(count(fetch_similar_related_post_custom($post->ID))):?>
						<div class="recommended-data-related-post-cat">
							<?php foreach(fetch_similar_related_post_custom($post->ID) as $i=>$related_post_content):?>
								<u><b><a href="<?=$related_post_content['slug']?>"><?=$related_post_content['name']?></a></b></u><br>
							<?php endforeach;?>
						</div>
						<?php endif;?>
					</div>
					
					<div class="clearfix"></div>
					<br/>
					<br/>
					<?php 
					if (!is_page_template("template-blog.php") && !is_page_template("template-home.php") && !is_page_template("template-users.php") && !is_page_template("template-contact.php") && !is_page_template("template-faqs.php") && !is_page_template("template-categories.php") && !is_page_template("template-tags.php") && $value_r["value"] == "comments" && (comments_open() || $count_post_all > 0)) :
						comments_template();
					endif;
					/* Page content end */

					do_action("discy_action_after_post_content",$post->ID,$post->post_author);
					do_action("wpqa_action_after_post_content",$post->ID,$post->post_author);
				endwhile;
			endif;?>
		</div><!-- End post-articles -->
	<?php elseif (has_wpqa() && wpqa_plugin_version >= "5.9.8") :
		include wpqa_get_template("content-none.php","theme-parts/");
	endif;
	if (($remove_question_slug == "on" || $remove_asked_question_slug == "on") && isset($discy_query)) {
		wp_reset_postdata();
	}
	include locate_template("includes/footer-part.php");
get_footer();?>