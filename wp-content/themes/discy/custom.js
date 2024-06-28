jQuery(window).on('load',function(){
    if( jQuery('.breadcrumb-left').length)
    {
        let url = window.location.href;
        let market_research_pattern = /market-research\/([a-zA-Z0-9-]+)/;
        let question_tag_page_pattern = /question-tag\/([a-zA-Z0-9-]+)/
        let blog_post_page_pattern = /([a-zA-Z0-9-]+)/;
        let blog_tag_page_pattern = /^(https?:\/\/[^\/]+)\/tag\/([a-zA-Z0-9-]+)\/$/;
        let market_research_tag_page_pattern = /^(https?:\/\/[^\/]+)\/market_research_tag\/([a-zA-Z0-9-]+)\/$/;
        let question_page_pattern = /^(https?:\/\/[^\/]+)\/question\/([a-zA-Z0-9-]+)\/$/;

        let last_content_count = jQuery('.breadcrumb-left').find('meta[itemprop="position"]:last').attr('content');
        let next_content_count = parseInt(last_content_count)+1;

        let market_research_page_pattern_match = url.match(market_research_pattern);
        let blog_post_page_pattern_match = url.match(blog_post_page_pattern);
        let question_tag_page_pattern_match = url.match(question_tag_page_pattern);
        let blog_tag_page_pattern_match = url.match(blog_tag_page_pattern);
        let market_research_tag_page_pattern_match = url.match(market_research_tag_page_pattern);
        let question_page_pattern_match = url.match(question_page_pattern);

        // market research page
        if (market_research_page_pattern_match) {
            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: my_ajax_object.ajax_url,
                data: { action: 'get_breadcrumbs_post_category_data',slug: last_part_url_in_array(),post_type: 'market_research' },
                dataType:'json',
                success: function (response) {
                    if(response.status=="success")
                    {
                        jQuery('.breadcrumb-left').find('meta[itemprop="position"]:last').parent().find('span[itemprop="name"]').remove();
                        jQuery('.breadcrumb-left').find('meta[itemprop="position"]:last').parent().removeClass('current');
                        let market_research_page_breadcrumb_html = '<a itemprop="item" href="/market-research/" title="Market Research"><span itemprop="name"><i class="icon-newspaper font-xl mr-2"></i>Market Research</span></a>';
                        jQuery(market_research_page_breadcrumb_html).insertAfter(jQuery('.breadcrumb-left').find('meta[itemprop="position"]:last'));


                        let extra_bread_crumb_html = '<span class="crumbs-span">/</span><span class="current" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">';
                        extra_bread_crumb_html+='<meta itemprop="position" content="'+next_content_count+'"><a itemprop="item" href="/market-research-category/'+response.first_category.slug+'/"';
                        extra_bread_crumb_html+='title="'+response.first_category.name+'"><span itemprop="name"><i class="icon-book font-xl mr-2"></i>'+response.first_category.name+'</span></a></span>';
                        jQuery(extra_bread_crumb_html).insertBefore(jQuery('.breadcrumb-left').find('span.crumbs-span:last'));

                        jQuery('.breadcrumb-left').prop('style').setProperty('display', 'block', 'important')
                    }
                    else{
                        jQuery('.breadcrumb-left').prop('style').setProperty('display', 'block', 'important');
                    }
                }
            });
        }
        // question tag page
        else if(question_tag_page_pattern_match)
        {
            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: my_ajax_object.ajax_url,
                data: { action: 'get_breadcrumbs_post_category_data',slug: last_part_url_in_array(),post_type: 'question' },
                dataType:'json',
                success: function (response) {
                    if(response.status=="success")
                    {
                        let extra_bread_crumb_html = '<span class="crumbs-span">/</span><span class="current" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">';
                        extra_bread_crumb_html+='<meta itemprop="position" content="2"><a itemprop="item" href="/communities/"';
                        extra_bread_crumb_html+='title="communities"><span itemprop="name"><i class="icon-users font-xl mr-2"></i>Communities</span></a></span>';

                        extra_bread_crumb_html+= '<span class="crumbs-span">/</span><span class="current" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">';
                        extra_bread_crumb_html+='<meta itemprop="position" content="3"><a itemprop="item" href="/question-category/'+response.catgeory.slug+'/"'
                        extra_bread_crumb_html+='title="'+response.catgeory.name+'"><span itemprop="name">';
                        if(response.catgeory.category_icon=="")
                        {
                            extra_bread_crumb_html+='<i class="icon-user font-xl mr-2"></i>';
                        }
                        else{
                            extra_bread_crumb_html+='<i class="'+response.catgeory.category_icon+' font-xl mr-2"></i>';
                        }
                        extra_bread_crumb_html+=response.catgeory.name+'</span></a></span>';

                        jQuery(extra_bread_crumb_html).insertAfter(jQuery('.breadcrumb-left').find('span[itemprop="itemListElement"]:last'));
                        jQuery('.breadcrumb-left').prop('style').setProperty('display', 'block', 'important');
                    }
                    else{
                        jQuery('.breadcrumb-left').prop('style').setProperty('display', 'block', 'important');
                    }
                }
            });
        }
        // blog tag page
        else if(blog_tag_page_pattern_match)
        {
            let extra_bread_crumb_html = '<span class="crumbs-span">/</span><span class="current" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">';
            extra_bread_crumb_html+='<meta itemprop="position" content="2"><a itemprop="item" href="/blogs/"';
            extra_bread_crumb_html+='title="blogs"><span itemprop="name"><i class="icon-book-open font-xl mr-2"></i>Blogs</span></a></span>';

            jQuery(extra_bread_crumb_html).insertAfter(jQuery('.breadcrumb-left').find('span[itemprop="itemListElement"]:last'));
            jQuery('.breadcrumb-left').prop('style').setProperty('display', 'block', 'important');
        }
        // market research tag page
        else if(market_research_tag_page_pattern_match)
        {
            let extra_bread_crumb_html = '<span class="crumbs-span">/</span><span class="current" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">';
            extra_bread_crumb_html+='<meta itemprop="position" content="2"><a itemprop="item" href="/market-research/"';
            extra_bread_crumb_html+='title="Market Research"><span itemprop="name"><i class="icon-newspaper font-xl mr-2"></i>Market Research</span></a></span>';

            jQuery(extra_bread_crumb_html).insertAfter(jQuery('.breadcrumb-left').find('span[itemprop="itemListElement"]:last'));
            jQuery('.breadcrumb-left').prop('style').setProperty('display', 'block', 'important');
        }
        // question page
        else if(question_page_pattern_match)
        {
            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: my_ajax_object.ajax_url,
                data: { action: 'get_breadcrumbs_post_category_data',slug: last_part_url_in_array(),post_type: 'question',purpose: 1 },
                dataType:'json',
                success: function (response) {
                    if(response.status=="success")
                    {
                        // jQuery('.breadcrumb-left').find('meta[itemprop="position"]:last').attr('content',next_content_count);

                        // let extra_bread_crumb_html = '<span class="current" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">';
                        // extra_bread_crumb_html+='<meta itemprop="position" content="'+last_content_count+'"><a itemprop="item" href="/blogs/"';
                        // extra_bread_crumb_html+='title="blogs"><span itemprop="name"><i class="icon-book-open font-xl mr-2"></i>blogs</span></a></span><span class="crumbs-span">/</span>';

                        // jQuery(extra_bread_crumb_html).insertBefore(jQuery('.breadcrumb-left').find('meta[itemprop="position"]:last').parent());

                        // jQuery('.breadcrumb-left').prop('style').setProperty('display', 'block', 'important')
                    }
                    else{
                        jQuery('.breadcrumb-left').prop('style').setProperty('display', 'block', 'important');
                    }
                }
            });
        }
        // blog post page
        else if(blog_post_page_pattern_match)
        {
            jQuery.ajax({
                type: "post",
                dataType: "json",
                url: my_ajax_object.ajax_url,
                data: { action: 'get_breadcrumbs_post_category_data',slug: last_part_url_in_array(),post_type: 'post' },
                dataType:'json',
                success: function (response) {
                    if(response.status=="success")
                    {
                        jQuery('.breadcrumb-left').find('meta[itemprop="position"]:last').attr('content',next_content_count);

                        let extra_bread_crumb_html = '<span class="current" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem">';
                        extra_bread_crumb_html+='<meta itemprop="position" content="'+last_content_count+'"><a itemprop="item" href="/blogs/"';
                        extra_bread_crumb_html+='title="blogs"><span itemprop="name"><i class="icon-book-open font-xl mr-2"></i>blogs</span></a></span><span class="crumbs-span">/</span>';

                        jQuery(extra_bread_crumb_html).insertBefore(jQuery('.breadcrumb-left').find('meta[itemprop="position"]:last').parent());

                        jQuery('.breadcrumb-left').prop('style').setProperty('display', 'block', 'important')
                    }
                    else{
                        jQuery('.breadcrumb-left').prop('style').setProperty('display', 'block', 'important');
                    }
                }
            });
        }
        else {
            jQuery('.breadcrumb-left').prop('style').setProperty('display', 'block', 'important');
        }
        
    }
})


function last_part_url_in_array()
{
    // Split the window.location.href by '/'
    var urlArray = window.location.href.split('/');

    urlArray.splice(1, 1); // This will remove the second element in the array

    // Optionally, join the array back into a string
    var modifiedUrl = urlArray.join('/');
    var lastPart = urlArray[urlArray.length - 2];
    return lastPart;
}