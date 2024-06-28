<?php

/* @author    2codeThemes
*  @package   WPQA/functions
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Paged */
function wpqa_paged() {
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
/* Pagination */
if (!function_exists('wpqa_pagination')) :
	function wpqa_pagination($args = array(),$max_num_pages = '',$query = '') {
		global $wp_rewrite,$wp_query;
		do_action('wpqa_pagination_start');
		if (isset($query)) {
			$o_wp_query = $wp_query;
			$wp_query = $query;
		}
		if ($max_num_pages == "" && isset($wp_query->max_num_pages)) {
			$max_num_pages = $wp_query->max_num_pages;
		}
		/* If there's not more than one page,return nothing. */
		if (1 >= $max_num_pages) {
			if (isset($o_wp_query)) {
				$wp_query = $o_wp_query;
			}
			return;
		}
		/* Get the current page. */
		$paged = $current = wpqa_paged();
		$page_paged = (get_query_var("paged") != ""?"paged":(get_query_var("page") != ""?"page":"paged"));
		$current = $paged;
		/* Get the max number of pages. */
		$max_num_pages = ($max_num_pages != ""?$max_num_pages:intval($wp_query->max_num_pages));
		/* Set up some default arguments for the paginate_links() function. */
		$defaults = array(
			'base'         => esc_url_raw(add_query_arg($page_paged,'%#%')),
			'format'       => '',
			'total'        => $max_num_pages,
			'current'      => esc_html($current),
			'prev_next'    => true,
			'prev_text'    => '<i class="icon-left-open"></i>',
			'next_text'    => '<i class="icon-right-open"></i>',
			'show_all'     => false,
			'end_size'     => 1,
			'mid_size'     => 1,
			'add_fragment' => '',
			'type'         => 'plain',
			'before'       => '<div class="main-pagination"><div class="pagination">',
			'after'        => '</div></div>',
			'echo'         => true,
		);
		/* Add the $base argument to the array if the user is using permalinks. */
		if (!wpqa_is_search() && $wp_rewrite->using_permalinks()) {
			if (isset($o_wp_query->wpqa_is_home_profile)) {
				$author_widget = wpqa_options("author_widget");
				if ($author_widget == "on") {
					$wpqa_user_id = (int)(isset($o_wp_query->query_vars) && isset($o_wp_query->query_vars["wpqa_user_id"])?$o_wp_query->query_vars["wpqa_user_id"]:0);
					$first_one = get_user_meta($wpqa_user_id,"wpqa_first_profile_tab",true);
					if ($first_one != "") {
						$get_first_page = wpqa_options($first_one.'_slug');
					}
				}
			}
			$defaults['base'] = user_trailingslashit(trailingslashit(get_pagenum_link()).(isset($get_first_page) && $get_first_page != ""?$get_first_page."/":"").'page/%#%');
		}
		/* If we're on a search results page,we need to change this up a bit. */
		if (!wpqa_is_search() && is_search()) {
			/* If we're in BuddyPress,use the default "unpretty" URL structure. */
			if (class_exists('BP_Core_User')) {
				$search_query = esc_html(get_query_var('s'));
				$base = user_trailingslashit(esc_url(home_url('/'))) . '?s=' . $search_query . '&paged=%#%';
				$defaults['base'] = $base;
			}
		}
		/* Merge the arguments input with the defaults. */
		$args = wp_parse_args($args,$defaults);
		/* Allow developers to overwrite the arguments with a filter. */
		$args = apply_filters('wpqa_pagination_args',$args);
		/* Don't allow the user to set this to an array. */
		if ('array' == $args['type'])
			$args['type'] = 'plain';
		/* Make sure raw querystrings are displayed at the end of the URL,if using pretty permalinks. */
		$pattern = '/\?(.*?)\//i';
		preg_match($pattern,$args['base'],$raw_querystring);
		if ($wp_rewrite->using_permalinks() && $raw_querystring)
			$raw_querystring[0] = str_replace('','',$raw_querystring[0]);
			if (!empty($raw_querystring)) {
				$args['base'] = str_replace($raw_querystring[0],'',$args['base']);
				$args['base'] .= substr($raw_querystring[0],0,-1);
			}
		/* Get the paginated links. */
		$page_links = paginate_links($args);
		/* Remove 'page/1' from the entire output since it's not needed. */
		$page_links = str_replace(array('&#038;paged=1\'','/page/1\''),'\'',$page_links);
		/* Wrap the paginated links with the $before and $after elements. */
		$page_links = $args['before'] . $page_links . $args['after'];
		/* Allow devs to completely overwrite the output. */
		$page_links = apply_filters('wpqa_pagination',$page_links);
		do_action('wpqa_pagination_end');
		/* Return the paginated links for use in themes. */
		if (isset($o_wp_query)) {
			$wp_query = $o_wp_query;
		}
		if ($args['echo']) {
			echo ($page_links);
		}else {
			return $page_links;
		}
	}
endif;
/* Search load more */
add_filter("get_pagenum_link","wpqa_get_pagenum_link");
function wpqa_get_pagenum_link($result) {
	if (wpqa_is_search() || wpqa_is_user_questions() || wpqa_is_user_posts()) {
		global $wp_rewrite;
		$paged = wpqa_paged();
		$pagenum = $paged + 1;
		$request = remove_query_arg('page');
		$home_root = parse_url(home_url());
		$home_root = (isset($home_root['path']))?$home_root['path']:'';
		$home_root = preg_quote($home_root,'|');
		$request = preg_replace('|^'.$home_root.'|i','',$request);
		$request = preg_replace('|^/+|','',$request);
		if (wpqa_is_user_questions()) {
			$request = str_ireplace('page/'.$pagenum.'/','',$request);
		}
		$base = trailingslashit(get_bloginfo('url'));
		if ($pagenum > 1) {
			$request = str_ireplace("/page/".$paged."/","/page/".$pagenum."/",$request);
		}else {
			$result = $base.$request;
		}
	}
	return $result;
}
/* Get the next page */
function wpqa_next_posts_page_link( $label = null, $max_page = 0 ) {
	$paged = wpqa_paged();
	if ( ! $paged ) {
		$paged = 1;
	}
	$nextpage = (int) $paged + 1;
	if ( $max_page >= $nextpage ) {
		return '<a href="' . get_pagenum_link( $nextpage ) . "\">" . preg_replace( '/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label ) . '</a>';
	}
}
/* Get the previous page */
function wpqa_previous_posts_page_link( $label = null ) {
	$paged = wpqa_paged();
	if ( ! $paged ) {
		$paged = 1;
	}
	$previous_page = (int) $paged - 1;
	if ( $paged > 1 ) {
		return '<a href="' . get_pagenum_link( $previous_page ) . "\">" . preg_replace( '/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label ) . '</a>';
	}
}
/* Pagination load */
if (!function_exists('wpqa_load_pagination')) :
	function wpqa_load_pagination($args_array = array()) {
		global $wp_query;
		$defaults = array(
			'post_pagination' => 'pagination',
			'max_num_pages' => '',
			'it_answer_pagination' => false,
			'its_post_type' => false,
			'wpqa_query' => null,
			'it_comment_pagination' => false,
			'args' => array(),
			'comment' => '',
			'search_type' => '',
			'its_answer' => false,
		);
		
		$args_array = wp_parse_args($args_array,$defaults);

		$post_pagination = $args_array['post_pagination'];
		$max_num_pages = $args_array['max_num_pages'];
		$it_answer_pagination = $args_array['it_answer_pagination'];
		$its_post_type = $args_array['its_post_type'];
		$wpqa_query = $args_array['wpqa_query'];
		$it_comment_pagination = $args_array['it_comment_pagination'];
		$args = $args_array['args'];
		$comment = $args_array['comment'];
		$search_type = $args_array['search_type'];
		$its_answer = $args_array['its_answer'];

		if ($post_pagination != "none") {
			$get_post_type = get_post_type();
			if (isset($it_answer_pagination) && $it_answer_pagination == true) {
				if (isset($it_comment_pagination) && $it_comment_pagination == true) {
					$nomore_text = esc_html__("No more comments","wpqa");
					$load_text = esc_html__("Load More Comments","wpqa");
					$old_posts = esc_html__('Old Comments','wpqa');
					$new_posts = esc_html__('New Comments','wpqa');
				}else {
					$nomore_text = esc_html__("No more answers","wpqa");
					$load_text = esc_html__("Load More Answers","wpqa");
					$old_posts = esc_html__('Old Answers','wpqa');
					$new_posts = esc_html__('New Answers','wpqa');
				}
				$wrap_main_content = 'commentlist';
				$pagination_class = 'pagination-answer';
			}else if (isset($its_post_type) && ($its_post_type == wpqa_questions_type || $its_post_type == wpqa_asked_questions_type)) {
				$nomore_text = esc_html__("No more questions","wpqa");
				$load_text = esc_html__("Load More Questions","wpqa");
				$wrap_main_content = 'post-articles.question-articles';
				$pagination_class = 'pagination-question';
				$old_posts = esc_html__('Old Questions','wpqa');
				$new_posts = esc_html__('New Questions','wpqa');
			}else if (isset($its_post_type) && $its_post_type == wpqa_knowledgebase_type) {
				$nomore_text = esc_html__("No more knowledgebases","wpqa");
				$load_text = esc_html__("Load More knowledgebases","wpqa");
				$wrap_main_content = 'post-articles.knowledgebase-articles';
				$pagination_class = 'pagination-knowledgebase';
				$old_posts = esc_html__('Old Knowledgebases','wpqa');
				$new_posts = esc_html__('New Knowledgebases','wpqa');
			}else if (isset($its_post_type) && $its_post_type == "group") {
				$nomore_text = esc_html__("No more groups","wpqa");
				$load_text = esc_html__("Load More Groups","wpqa");
				$wrap_main_content = 'post-articles.group-articles';
				$pagination_class = 'pagination-group';
				$old_posts = esc_html__('Old Groups','wpqa');
				$new_posts = esc_html__('New Groups','wpqa');
			}else if (isset($its_post_type) && $its_post_type == "group_posts") {
				$nomore_text = esc_html__("No more posts","wpqa");
				$load_text = esc_html__("Load More Posts","wpqa");
				$wrap_main_content = 'post-articles.group-posts-articles';
				$pagination_class = 'pagination-group-posts';
				$old_posts = esc_html__('Old Posts','wpqa');
				$new_posts = esc_html__('New Posts','wpqa');
			}else {
				$nomore_text = esc_html__("No more posts","wpqa");
				$load_text = esc_html__("Load More Posts","wpqa");
				$wrap_main_content = 'post-articles';
				$pagination_class = 'pagination-post';
				$old_posts = esc_html__('Old Entries','wpqa');
				$new_posts = esc_html__('New Entries','wpqa');
			}
			if ($post_pagination == "infinite_scroll" || $post_pagination == "load_more") {
				if ($its_answer == true) {
					$default_comments_page = get_option("default_comments_page");
					$more_link = ($default_comments_page == "newest"?get_previous_comments_link(' '):get_next_comments_link(' ',$max_num_pages));
				}else if (isset($its_post_type) && $its_post_type == "group_posts") {
					$more_link = wpqa_next_posts_page_link(' ',$max_num_pages);
				}else {
					$more_link = wpqa_next_posts_page_link(' ',$max_num_pages);
				}
				$get_theme_name = wpqa_prefix_theme;
				if (!empty($more_link)) :?>
					<script type="text/javascript">
						(function($) {
							jQuery(document).ready(function() {
								/* Load more */
								function wpqa_load_more(load_type,j_this,ajax_count) {
									var main_content = ".the-main-inner";
									if (load_type == "infinite-scroll") {
										var $link = jQuery('.posts-infinite-scroll a');
									}else {
										var $link = j_this;
									}
									var page_url = $link.attr("href");
									if (page_url != undefined) {
										if (load_type == "infinite-scroll") {
											$link.parent().parent().animate({ opacity: 1}, 300).css('padding', '10px');
										}else {
											$link.closest(main_content).find(".posts-"+load_type+" a").hide();
										}
										$link.closest(main_content).find(".posts-"+load_type+" .load_span").addClass("load_span_display");
										jQuery("<div>").load(page_url, function() {
											var p_count = ajax_count.toString();
											var $wrap = $link.closest(main_content).find('.<?php echo esc_js($wrap_main_content)?>');
											var $wrap_row = $wrap.find(" > .row, > .row-boot");
											$wrap = ($wrap_row.length > 0?$wrap_row:$wrap);
											<?php if ('post' === $get_post_type) {?>
												var $share = $link.closest(main_content).find('.post-articles article.post .post-share > ul').attr("style");
											<?php }?>
											var $this_div = jQuery(this);
											var $new = $this_div.find('.<?php echo (isset($it_answer_pagination) && $it_answer_pagination == true?"commentlist > li.comment":"post-articles article.".esc_js($get_post_type))?>,.aalan.aalan-inside').addClass('<?php echo (isset($it_answer_pagination) && $it_answer_pagination == true?"comment":"post-section")?>-new-'+p_count);
											<?php if ('post' === $get_post_type) {?>
												$new.find('.post-share > ul').attr("style",$share);
											<?php }?>
											$new.imagesLoaded(function() {
												$new.hide().appendTo($wrap).fadeIn(400);
												/* Lightbox */
												var lightboxArgs = {
													animation_speed: "fast",
													overlay_gallery: true,
													autoplay_slideshow: false,
													slideshow: 5000,
													theme: "pp_default",
													opacity: 0.8,
													show_title: false,
													social_tools: "",
													deeplinking: false,
													allow_resize: true,
													counter_separator_label: "/",
													default_width: 940,
													default_height: 529
												};
												jQuery("a[href$=jpg],a[href$=JPG],a[href$=jpeg],a[href$=JPEG],a[href$=png],a[href$=gif],a[href$=bmp]:has(img)").prettyPhoto(lightboxArgs);
												jQuery("a[class^='prettyPhoto'],a[rel^='prettyPhoto']").prettyPhoto(lightboxArgs);
												/* Facebook */
												jQuery(".facebook-remove").remove();
												/* Owl */
												jQuery(".post-section-new-"+p_count+" .slider-owl").each(function () {
													var $slider = jQuery(this);
													var $slider_item = $slider.find('.slider-item').length;
													$slider.find('.slider-item').css({"height":"auto"});
													if ($slider.find('img').length) {
														var $slider = jQuery(this).imagesLoaded(function() {
															$slider.owlCarousel({
																autoPlay: 5000,
																margin: 10,
																responsive: {
																	0: {
																		items: 1
																	}
																},
																stopOnHover: true,
																navText : ["", ""],
																nav: ($slider_item > 1)?true:false,
																rtl: jQuery('body.rtl').length?true:false,
																loop: ($slider_item > 1)?true:false,
															});
														});
													}else {
														$slider.owlCarousel({
															autoPlay: 5000,
															margin: 10,
															responsive: {
																0: {
																	items: 1
																}
															},
															stopOnHover: true,
															navText : ["", ""],
															nav: ($slider_item > 1)?true:false,
															rtl: jQuery('body.rtl').length?true:false,
															loop: ($slider_item > 1)?true:false,
														});
													}
												});
												/* Question masonry */
												if (jQuery(".post-section-new-"+p_count+".post-with-columns").length) {
													if ($new.eq(0).is('.question-masonry')) {
														var newItems = jQuery('.post-section-new-'+p_count);
														jQuery('.question-articles').isotope( 'insert', newItems );
														jQuery('.question-articles').isotope({
															filter: "*",
															animationOptions: {
																duration: 750,
																itemSelector: '.question-masonry',
																easing: "linear",
																queue: false,
															}
														});
														setTimeout(function() {
															if ($new.eq(0).is('.post-masonry')) {
																jQuery('.question-articles').isotope({
																	filter: "*",
																	animationOptions: {
																		duration: 750,
																		itemSelector: '.question-masonry',
																		easing: "linear",
																		queue: false,
																	}
																});
															}
														}, 1000);
													}else if (!$new.eq(0).is('.post-masonry')) {
														jQuery(".post-section-new-"+p_count+".post-with-columns").matchHeight();
														jQuery(".post-section-new-"+p_count+".post-with-columns > .article-question").matchHeight();
													}
												}
												/* Post masonry */
												if (jQuery(".post-section-new-"+p_count+".post-with-columns").length) {
													if ($new.eq(0).is('.post-masonry')) {
														var newItems = jQuery('.post-section-new-'+p_count);
														jQuery('.post-articles').isotope( 'insert', newItems );
														jQuery('.post-articles').isotope({
															filter: "*",
															animationOptions: {
																duration: 750,
																itemSelector: '.post-masonry',
																easing: "linear",
																queue: false,
															}
														});
														setTimeout(function() {
															if ($new.eq(0).is('.post-masonry')) {
																jQuery('.post-articles').isotope({
																	filter: "*",
																	animationOptions: {
																		duration: 750,
																		itemSelector: '.post-masonry',
																		easing: "linear",
																		queue: false,
																	}
																});
															}
														}, 1000);
													}
												}
												/* Poll */
												if (jQuery(".post-section-new-"+p_count+" .progressbar-percent").length) {
													jQuery(".post-section-new-"+p_count+" .progressbar-percent").each(function() {
														var $this = jQuery(this);
														var percent = $this.attr("attr-percent");
														$this.on("inview", function(event,isInView,visiblePartX,visiblePartY) {
															if (isInView) {
																$this.animate({"width": percent + "%"},700);
															}
														});
													});
												}
												/* Audio */
												if ($new.eq(0).find('.wp-audio-shortcode')) {
													<?php wp_enqueue_style('wp-mediaelement');
													wp_enqueue_script('wp-playlist');?>
													mejs.plugins.silverlight[0].types.push('video/x-ms-wmv');
													mejs.plugins.silverlight[0].types.push('audio/x-ms-wma');
													jQuery(function () {
														var settings = {};
														if (typeof _wpmejsSettings !== 'undefined') {
															settings = _wpmejsSettings;
														}
														settings.success = settings.success || function (mejs) {
															var autoplay, loop;
															if ('flash' === mejs.pluginType) {
																autoplay = mejs.attributes.autoplay && 'false' !== mejs.attributes.autoplay;
																loop = mejs.attributes.loop && 'false' !== mejs.attributes.loop;
																autoplay && mejs.addEventListener('canplay',function() {
																	mejs.play();
																},false);
																loop && mejs.addEventListener('ended',function() {
																	mejs.play();
																},false);
															}
														};
														jQuery('.post-section-new-'+p_count+' .wp-audio-shortcode').mediaelementplayer(settings);
													});
												}
												$link.closest(main_content).find(".posts-"+load_type+" .load_span").removeClass("load_span_display");
												if (load_type == "load-more") {
													$link.closest(main_content).find(".posts-"+load_type+" a").show();
												}
												/* Content */
												setTimeout(function () {
													jQuery(window).trigger('sticky_recalc');
												},1000);
												/* load more */
												if ($this_div.find(".posts-"+load_type).length) {
													if (load_type == "infinite-scroll") {
														$link.closest(main_content).find(".posts-infinite-scroll").html($this_div.find(".posts-infinite-scroll").html()).animate({opacity: 0}, 300).css("padding","0");
													}else {
														$link.closest(main_content).find(".posts-"+load_type).html($this_div.find(".posts-"+load_type).html());
													}
												}else {
													$link.closest(main_content).find(".pagination-wrap").html('<p class="no-comments"><?php echo esc_js($nomore_text)?></p>');
													$link.closest(main_content).find(".posts-"+load_type).fadeOut("fast").remove();
												}
												jQuery("<?php echo (isset($it_answer_pagination) && $it_answer_pagination == true?"comment":"post-section")?>-new-"+p_count).removeClass("<?php echo (isset($it_answer_pagination) && $it_answer_pagination == true?"comment":"post-section")?>-new-"+p_count);
												return false;
											});
										});
									}
								}
								var ajax_count = 0;
								/* infinite scroll */
								jQuery(".posts-infinite-scroll").each (function () {
									jQuery(this).on("inview",function(event,isInView,visiblePartX,visiblePartY) {
										if  (jQuery(".posts-infinite-scroll").length && isInView) {
											/* wpqa_load_more */
											ajax_count++;
											wpqa_load_more("infinite-scroll","",ajax_count);
										}
									});
								});
								/* load more */
								jQuery("body").on("click",".posts-load-more a",function(e) {
									e.preventDefault();
									/* wpqa_load_more */
									ajax_count++;
									wpqa_load_more("load-more",jQuery(this),ajax_count);
								});
							});
						})(jQuery);
					</script>
				<?php endif;
			}?>
			<div class="clearfix"></div>
			<div class="pagination-wrap <?php echo esc_attr($pagination_class).(empty($more_link)?" no-pagination-wrap":"")?>">
				<?php if (isset($wp_query->wpqa_is_home_profile)) {
					$author_widget = wpqa_options("author_widget");
					if ($author_widget == "on") {
						$wpqa_user_id = (int)(isset($wp_query->query_vars) && isset($wp_query->query_vars["wpqa_user_id"])?$wp_query->query_vars["wpqa_user_id"]:0);
						$first_one = get_user_meta($wpqa_user_id,"wpqa_first_profile_tab",true);
						if ($first_one != "") {
							$get_first_page = wpqa_options($first_one.'_slug');
						}
					}
				}
				if ($post_pagination == "load_more" || $post_pagination == "infinite_scroll") {
					if ($its_answer == true) {
						$more_link = ($post_pagination == "load_more"?($default_comments_page == "newest"?get_previous_comments_link($load_text):get_next_comments_link($load_text,$max_num_pages)):$more_link);
					}else if (isset($its_post_type) && $its_post_type == "group_posts") {
						$more_link = ($post_pagination == "load_more"?wpqa_next_posts_page_link($load_text,$max_num_pages):$more_link);
					}else {
						$more_link = ($post_pagination == "load_more"?wpqa_next_posts_page_link($load_text,$max_num_pages):$more_link);
					}
					if (isset($get_first_page) && $get_first_page != "" && $more_link != "") {
						$more_link = str_replace("/page/","/".$get_first_page."/page/",$more_link);
					}
					if (!empty($more_link)) {?>
						<div class="pagination-nav <?php echo ($post_pagination == "infinite_scroll"?"posts-infinite-scroll":"posts-load-more")?>"<?php (is_array($args) && !empty($args)?" data-query='".json_encode($args)."'":"")?>>
							<span class="load_span"><span class="loader_2"></span></span>
							<div class="load-more"><?php echo ($more_link)?></div>
						</div><!-- End pagination-nav -->
					<?php }
				}else if ($post_pagination == "pagination") {
					wpqa_pagination(array("search_type" => $search_type),$max_num_pages,(isset($wpqa_query)?$wpqa_query:null));
				}else {?>
					<div class="page-navigation page-navigation-before clearfix">
						<div class="row row-boot row-warp">
							<div class="col col6 col-boot-sm-6 nav-more-next">
								<div class="nav-custom-link nav-next<?php echo ($its_answer == true?' nav-next-comment':'')?>">
									<?php if ($its_answer == true) {
										$paged_link = get_next_comments_link('<i class="icon-left-thin"></i><span>'.$old_posts.'</span>',$max_num_pages);
									}else {
										$paged_link = wpqa_next_posts_page_link('<i class="icon-left-thin"></i><span>'.$old_posts.'</span>',$max_num_pages);
									}
									if (isset($get_first_page) && $get_first_page != "" && $paged_link != "") {
										$paged_link = str_replace("/page/","/".$get_first_page."/page/",$paged_link);
									}
									echo ($paged_link);?>
								</div>
							</div>
							<div class="col col6 col-boot-sm-6 nav-more-previous">
								<div class="nav-custom-link nav-previous<?php echo ($its_answer == true?' nav-previous-comment':'')?>">
									<?php if ($its_answer == true) {
										$paged_link = get_previous_comments_link('<span>'.$new_posts.'</span><i class="icon-right-thin"></i>');
									}else {
										$paged_link = wpqa_previous_posts_page_link('<span>'.$new_posts.'</span><i class="icon-right-thin"></i>');
									}
									if (isset($get_first_page) && $get_first_page != "" && $paged_link != "") {
										$paged_link = str_replace("/page/","/".$get_first_page."/page/",$paged_link);
									}
									echo ($paged_link);?>
								</div>
							</div>
						</div>
					</div>
				<?php }?>
			</div>
		<?php }
	}
endif;?>