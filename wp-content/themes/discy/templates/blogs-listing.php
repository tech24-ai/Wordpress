<?php
    $post_id = get_the_ID();
    $postmeta_detail = get_all_post_meta_details($post_id);
    $post_categories = get_custom_post_categories($post_id);
    $post_tags = get_post_tags($post_id);
    $post_details = get_post_details($post_id); 
?>

<div class="blogs-listing">
	<div class="img__section">
		<a class="page-url" href="<?=$post_details['page_url']?>">
			<img src="<?=$post_details['featured_image_url']?>" class="blog_img_identity" alt="<?=$post_details['title']?>">
		</a>
	</div>
    <div class="otherdetails">

        <?php if (count($post_categories)): ?>
        <div class="blog-categories-section">
            <a href="/cateory/<?php echo $post_categories[0]['slug'];?>/" class="category_name"><?php echo $post_categories[0]['name']; ?></a>
        </div>
        <?php endif; ?>

        <div class="blog-title">
            <a class="blog-name-title" href="<?=$post_details['page_url']?>"><?=$post_details['title']?></a>
        </div>

        <div class="blog-creation-and-tags">
            <div class="blog-created-date">
                <?=$post_details['created_date']?>
            </div>
            
            <!-- <?php if (count($post_tags)): ?>

            <div class="sepeartor">|</div>
            <div class="blog-tags-section">
                <?php if (count($post_tags) > 1): ?>
                    <?php foreach ($post_tags as $tindex => $tag): ?>
                        <span class="tag_name"><?php echo $tag; ?></span>
                        <?php if ($tindex < count($post_tags) - 1): ?>
                            <span>, </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="tag_name"><?php echo $post_tags[0]; ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?> -->
            <div class="seperator">|</div>
            <span class="blogpost_author_name">
                <?php echo get_custom_user_display_name(get_post_field('post_author', $post_id));?>
            </span>
        </div>
    </div>
</div>