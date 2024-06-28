<?php if (!$discy_thumbnail_id) {
	$discy_image = discy_image();
}
$post_id = (isset($post_id)?$post_id:(isset($knowledgebase_id)?$knowledgebase_id:0))?>
<figure class="featured-image post-img<?php if ((!$what_post || $what_post == "image" || $what_post == "image_lightbox" || $what_post == "audio") && (!$discy_thumbnail_id || ($featured_image != 0 && $featured_image == "on")) && (empty($discy_image) || (!empty($discy_image) && (is_single() || is_page())) || ($featured_image != 0 && $featured_image == "on"))) {echo " post-img-0";}?>">
	<?php if ((has_post_thumbnail() && ($what_post == "google" || $what_post == "soundcloud" || $what_post == "audio") && ($post_style == "style_2" || $post_style == "style_3") && !is_single()) || (has_post_thumbnail() && $what_post != "image_lightbox") && $what_post != "slideshow" && $what_post != "audio" && $what_post != "google" && $what_post != "video" && $what_post != "soundcloud" && $what_post != "twitter" && $what_post != "facebook" && $what_post != "instagram") {
		do_action("discy_before_featured_image",$post_id);?>
		<a href="<?php the_permalink();?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark"<?php echo ($post_style == "style_2" || $post_style == "style_3"?" class='post-img-link'":"")?>>
	<?php }
	$image_filter = "";
	if (is_singular(wpqa_questions_type) || is_singular(wpqa_asked_questions_type)) {
		$image_filter = "_question";
	}else if (is_singular("post") || is_page_template("template-blog.php")) {
		$image_filter = "_post";
	}else if (is_page()) {
		$image_filter = "_page";
	}
	$featured_image_width = apply_filters("discy_featured_image".$image_filter."_width",$featured_image_width);
	$featured_image_height = apply_filters("discy_featured_image".$image_filter."_height",$featured_image_height);
	$show_defult_image = apply_filters('discy_show_defult_image'.$image_filter,true);
	discy_head_post($post_style,"",$show_featured_image,$featured_image_style,$featured_image_width,$featured_image_height,(isset($blog_h)?$blog_h:""),$show_defult_image,$post_id);
	if ((has_post_thumbnail() && ($what_post == "google" || $what_post == "soundcloud" || $what_post == "audio") && ($post_style == "style_2" || $post_style == "style_3") && !is_single()) || (has_post_thumbnail() && $what_post != "image_lightbox") && $what_post != "slideshow" && $what_post != "audio" && $what_post != "google" && $what_post != "video" && $what_post != "soundcloud" && $what_post != "twitter" && $what_post != "facebook" && $what_post != "instagram") {?>
		</a>
	<?php }?>
</figure>