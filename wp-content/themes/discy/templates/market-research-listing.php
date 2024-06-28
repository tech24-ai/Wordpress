<?php
    $post_id = get_the_ID();
    $postmeta_detail = get_all_post_meta_details($post_id);
    $post_industries = get_post_industries($post_id);
    $post_market_research_post_categories = get_market_research_post_categories($post_id);
    $post_tags = get_post_tags($post_id);
    $post_details = get_post_details($post_id); 
?>

<div class="market-researches-listing">
	<div class="img__section">
		<a class="page-url" href="<?=$post_details['page_url']?>">
			<img src="<?=$post_details['featured_image_url']?>" class="market_researches_img_identity" alt="<?=$post_details['title']?>">
		</a>
	</div>
    <div class="otherdetails">

        <?php if (count($post_market_research_post_categories)): ?>
        <div class="market-researches-section">
            <a class="researh-catgeory-name" href="/market-research-category/<?php echo $post_market_research_post_categories[0]['slug'];?>/">
                <?php echo $post_market_research_post_categories[0]['name']; ?>
            </a>
        </div>
        <?php endif; ?>

        <div class="market-researches-title">
            <a class="market-researches-name-title" href="<?=$post_details['page_url']?>"><?=$post_details['title']?></a>
        </div>

        <div class="market-researches-creation-and-tags">
            <div class="market-researches-created-date">
                <?=$post_details['created_date']?>
            </div>
            
            <!-- <?php if (count($post_tags)): ?>

            <div class="sepeartor">|</div>
            <div class="market-researches-tags-section">
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
            <span class="market_research_post_author_name">
                <?php echo get_custom_user_display_name(get_post_field('post_author', $post_id));?>
            </span>
        </div>
    </div>
</div>