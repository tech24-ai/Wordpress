<?php

function dd($array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
    wp_die();
}

// Function to get all post meta details of a specific post in an array
function get_all_post_meta_details($post_id) {
    // Check if the post ID is valid
    if (empty($post_id) || !is_numeric($post_id)) {
        return []; // Return an empty array if the post ID is invalid
    }

    // Retrieve all post meta data for the given post ID
    $post_meta = get_post_meta($post_id);

    // Check if post meta data exists and return it, else return an empty array
    if (!empty($post_meta)) {
        return $post_meta; // Return the post meta details array
    } else {
        return []; // Return an empty array if no post meta data is found
    }
}

// Function to get category names of a specific post
function get_post_categories($post_id) {
    // Check if the post ID is valid
    if (empty($post_id) || !is_numeric($post_id)) {
        return []; // Return an empty array if the post ID is invalid
    }

    // Get the categories for the given post ID
    $categories = get_the_category($post_id);

    // Initialize an array to store category names
    $category_names = [];

    // Loop through each category and add the name to the array
    foreach ($categories as $category) {
        $category_names[] = $category->name;
    }

    // Return the array of category names
    return $category_names;
}

// Function to get tag names of a specific post
function get_post_tags($post_id) {
    // Check if the post ID is valid
    if (empty($post_id) || !is_numeric($post_id)) {
        return []; // Return an empty array if the post ID is invalid
    }

    // Get the tags for the given post ID
    $tags = get_the_tags($post_id);

    // Initialize an array to store tag names
    $tag_names = [];

    // Loop through each tag and add the name to the array
    if (!empty($tags)) {
        foreach ($tags as $tag) {
            $tag_names[] = $tag->name;
        }
    }

    // Return the array of tag names
    return $tag_names;
}

// market_research industries 
function get_post_industries($post_id) {
    // Check if the post ID is valid
    if (empty($post_id) || !is_numeric($post_id)) {
        return []; // Return an empty array if the post ID is invalid
    }

    // Get the post object to check post type
    $post = get_post($post_id);
    
    // Check if the post type is 'market_research'
    if ($post->post_type !== 'market_research') {
        return []; // Return an empty array if the post type is not 'market_research'
    }

    // Get the industries for the given post ID
    $industries = wp_get_post_terms($post_id, 'industry');

    // Initialize an array to store industry names
    $industry_names = [];

    // Loop through each industry and add the name to the array
    foreach ($industries as $industry) {
        $industry_names[] = $industry->name;
    }

    // Return the array of industry names
    return $industry_names;
}

// market_research categories 
function get_market_research_post_categories($post_id) {
    // Check if the post ID is valid
    if (empty($post_id) || !is_numeric($post_id)) {
        return []; // Return an empty array if the post ID is invalid
    }

    // Get the post object to check post type
    $post = get_post($post_id);
    
    // Check if the post type is 'market_research'
    if ($post->post_type !== 'market_research') {
        return []; // Return an empty array if the post type is not 'market_research'
    }

    // Get the market_research_category_terms for the given post ID
    $market_research_categories = wp_get_post_terms($post_id, 'market_research_category');

    // Initialize an array to store category details
    $market_research_category_details = [];

    // Loop through each category and add the details to the array
    foreach ($market_research_categories as $market_research_category) {
        $market_research_category_details[] = [
            'id' => $market_research_category->term_id,
            'name' => $market_research_category->name,
            'slug' => $market_research_category->slug,
        ];
    }

    // Return the array of category details
    return $market_research_category_details;
}

// function to get post categories
function get_custom_post_categories($post_id)
{
    // Check if the post ID is valid
    if (empty($post_id) || !is_numeric($post_id)) {
        return []; // Return an empty array if the post ID is invalid
    }

    // Get the post object to check post type
    $post = get_post($post_id);
    
    // Check if the post type is 'post'
    if ($post->post_type !== 'post') {
        return []; // Return an empty array if the post type is not 'post'
    }

    // Get the categories for the given post ID
    $market_research_categories = wp_get_post_terms($post_id, 'category');

    // Initialize an array to store category details
    $post_category_details = [];

    // Loop through each category and add the details to the array
    foreach ($market_research_categories as $post_category) {
        $post_category_details[] = [
            'id' => $post_category->term_id,
            'name' => $post_category->name,
            'slug' => $post_category->slug,
        ];
    }

    // Return the array of category details
    return $post_category_details;
}

// Function to get additional post details
function get_post_details($post_id) {
    // Check if the post ID is valid
    if (empty($post_id) || !is_numeric($post_id)) {
        return []; // Return an empty array if the post ID is invalid
    }

    // Get the post object
    $post = get_post($post_id);

    // Check if the post object exists
    if (empty($post)) {
        return []; // Return an empty array if the post does not exist
    }

    // Get post title
    $post_title = get_the_title($post_id);

    // Get post excerpt (description)
    $post_description = get_the_excerpt($post_id);

    // Get post created date in the specified format
    $created_date = get_the_date('M j, Y', $post_id);

    // Get the featured image URL
    $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');

    // Get the page URL
    $page_url = get_permalink($post_id);

    // Return an array of post details
    return [
        'title' => $post_title,
        'description' => $post_description,
        'created_date' => $created_date,
        'featured_image_url' => $featured_image_url,
        'page_url' => $page_url
    ];
}

// on facet html filter
add_filter( 'facetwp_template_html', function( $output, $class ) {
    $GLOBALS['wp_query'] = $class->query;

    if ($class->template[ 'name' ] == 'blogs_listing') {
        ob_start();
        while ( have_posts() ): the_post();
        get_template_part( 'templates/blogs-listing' );
        endwhile;
        return ob_get_clean();
    }

    if ($class->template[ 'name' ] == 'market_research_listing') {
        ob_start();
        while ( have_posts() ): the_post();
        get_template_part( 'templates/market-research-listing' );
        endwhile;
        return ob_get_clean();
    }

    return $output;
}, 10, 2 );

// fetch similar market researhes post
function fetch_similar_related_market_researches($post_id) {
    $final_related_data = [];

    // Check if the post ID is valid
    if (empty($post_id) || !is_numeric($post_id)) {
        return []; // Return an empty array if the post ID is invalid
    }

    // Get the post object to check post type
    $post = get_post($post_id);
    
    // Check if the post type is 'market_research'
    if ($post->post_type !== 'market_research') {
        return []; // Return an empty array if the post type is not 'market_research'
    }

    // Get the market_research_category_terms for the given post ID
    $post_market_research_categories = wp_get_post_terms($post_id, 'market_research_category', ['fields' => 'ids']);

    // If no categories found, return empty array
    if (empty($post_market_research_categories)) {
        return [];
    }

    // Query posts without using tax_query
    $args = [
        'post_type' => 'market_research',
        'posts_per_page' => -1,
        'post__not_in' => [$post_id], // Exclude the current post
        'post_status'    => 'publish',  // Only get published posts
    ];

    $related_posts = get_posts($args);

    // Extract post details including research category IDs
    foreach ($related_posts as $related_post) {
        $related_post_category_ids = wp_get_post_terms($related_post->ID, 'market_research_category', ['fields' => 'ids']);

        // Check if any of the related post's category IDs match with current post's category IDs
        foreach ($post_market_research_categories as $cat_id) {
            if (in_array($cat_id, $related_post_category_ids)) {
                $final_related_data[] = [
                    'ID' => $related_post->ID,
                    'name' => $related_post->post_title,
                    'slug' => get_permalink($related_post->ID),
                    'research_category_ids' => $related_post_category_ids,
                ];
            }
        }
    }

    return $final_related_data;
}

// fetch similar  post
function fetch_similar_related_post_custom($post_id) {
    $final_related_data = [];

    // Check if the post ID is valid
    if (empty($post_id) || !is_numeric($post_id)) {
        return []; // Return an empty array if the post ID is invalid
    }

    // Get the post object to check post type
    $post = get_post($post_id);
    
    // Check if the post type is 'post'
    if ($post->post_type !== 'post') {
        return []; // Return an empty array if the post type is not 'post'
    }

    // Get the category for the given post ID
    $post_categories = wp_get_post_terms($post_id, 'category', ['fields' => 'ids']);

    // If no categories found, return empty array
    if (empty($post_categories)) {
        return [];
    }

    // Query posts without using tax_query
    $args = [
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post__not_in' => [$post_id], // Exclude the current post
        'post_status'    => 'publish',  // Only get published posts
    ];

    $related_posts = get_posts($args);

    // Extract post details including research category IDs
    foreach ($related_posts as $related_post) {
        $related_post_category_ids = wp_get_post_terms($related_post->ID, 'category', ['fields' => 'ids']);

        // Check if any of the related post's category IDs match with current post's category IDs
        foreach ($post_categories as $cat_id) {
            if (in_array($cat_id, $related_post_category_ids)) {
                $final_related_data[] = [
                    'ID' => $related_post->ID,
                    'name' => $related_post->post_title,
                    'slug' => get_permalink($related_post->ID),
                    'post_category_ids' => $related_post_category_ids,
                ];
            }
        }
    }

    return $final_related_data;
}

// get user name
function get_custom_user_display_name($user_id) {
    // Get the user object
    $user = get_userdata($user_id);

    // Check if the user exists
    if (!$user) {
        return null;
    }

    // Get the display name and nicename
    $display_name = $user->display_name;
    $nicename = $user->user_nicename;

    // Return display_name if it's not empty, otherwise return nicename
    return !empty($display_name) ? $display_name : $nicename;
}

function enqueue_custom_assets() {
    // Get the current time to use as version
    $version = time();

    // Register and enqueue the custom CSS file with the current time as version
    wp_register_style('custom-css', get_template_directory_uri() . '/custom.css', array(), $version, 'all');
    wp_enqueue_style('custom-css');

    // Register and enqueue the custom JS file with the current time as version
    wp_register_script('custom-js', get_template_directory_uri() . '/custom.js', array(), $version, true);
    // Localize the script with new data
    wp_localize_script('custom-js', 'my_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
    wp_enqueue_script('custom-js');
}
add_action('wp_enqueue_scripts', 'enqueue_custom_assets');

function get_breadcrumbs_post_category_data()
{   
    if(isset($_POST['slug']) && isset($_POST['post_type']) && !empty($_POST['slug']) && !empty($_POST['post_type']))
    {
        $post_id ='';
        $slug = $_POST['slug'];
        $post_type = $_POST['post_type'];
        $json_data =[
            'status'=>'error'
        ];
        // Query to get the post ID by slug

        // market research
        if($post_type=="market_research")
        {
            $args = array(
                'name'           => $slug,
                'post_type'      => 'market_research',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
            );
            
            $query = new WP_Query($args);
            
            if ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                wp_reset_postdata(); // Reset the global post object
            }

            if(isset($post_id) &&!empty($post_id))
            {
                // Get the categories associated with the post
                $post_market_research_categories = wp_get_post_terms($post_id, 'market_research_category', array('fields' => 'all'));

                $categories = array();

                if (!is_wp_error($post_market_research_categories) && !empty($post_market_research_categories)) {
                    foreach ($post_market_research_categories as $category) {
                        $categories[] = array(
                            'id' => $category->term_id,
                            'name' => $category->name,
                            'slug' => $category->slug,
                        );
                    }
                }
                
                if(count($categories))
                {
                    $json_data =[
                        'catgeories'=> $categories,
                        'first_category'=> count($categories)?$categories[0]:'',
                        'status'=>'success'
                    ];
                }
                else{
                    $json_data =[
                        'status'=>'error'
                    ];
                }
            }
        }
        // questions and tags
        else if(($post_type=="question") && !isset($_POST['purpose']))
        {
            $terms_data = get_term_id_and_name_by_taxonomy_and_slug('question_tags',$slug);
            $question_posts_arr =[];
            $question_tag_cat_arr =[];
            $args = array(
                'post_type'      => 'question',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
            );
            
            // Querying posts
            $query = new WP_Query($args);
            
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
            
                    // Get post ID
                    $custom_post_id = get_the_ID();
            
                    // Get terms for 'question-category' taxonomy
                    $question_category_terms = wp_get_post_terms($custom_post_id, 'question-category', array('fields' => 'ids'));
            
                    // Get terms for 'question_tags' taxonomy
                    $question_tags_terms = wp_get_post_terms($custom_post_id, 'question_tags', array('fields' => 'ids'));
            
                    // Build array for current post
                    $post_arr = array(
                        'post_id' => $custom_post_id,
                        'question-category' => $question_category_terms,
                        'question_tags' => $question_tags_terms,
                    );
            
                    // Add to the main array
                    $question_posts_arr[] = $post_arr;
                }
                wp_reset_postdata(); // Reset the global post object
            }
            
            // run loop on posts then find where tag exits, then again find category and assign in array
            if($terms_data && isset($terms_data['term_id']) && count($question_posts_arr))
            {
                foreach($question_posts_arr as $k=>$question_post)
                {
                    if(isset($question_post['question_tags']) && in_array($terms_data['term_id'],$question_post['question_tags']))
                    {
                        foreach($question_post['question-category'] as $q=>$question_cat)
                        {
                            $question_tag_cat_arr[]=$question_cat;
                        }
                    }
                }
            }

            
            if(count($question_tag_cat_arr))
            {
                $first_cat = $question_tag_cat_arr[0];
                if(get_term_details_by_taxonomy_and_id('question-category',$first_cat))
                {
                    $json_data =[
                        'catgeory'=> get_term_details_by_taxonomy_and_id('question-category',$first_cat),
                        'status'=>'success'
                    ];
                }
            }
        }
        else if(($post_type=="question") && isset($_POST['purpose']) && ($_POST['purpose']==1))
        {
            $post_id = ''; // Initialize $post_id variable

            $args = array(
                'post_type'      => 'question',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'name'           => $slug, // Replace $slug with the actual post_name (slug)
                'fields'         => 'ids', // Retrieve only post IDs for efficiency
            );

            // Querying posts
            $query = new WP_Query($args);

            // Check if there are any posts
            if ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID(); // Retrieve post ID
                // Additional processing or output
                wp_reset_postdata(); // Reset post data
            }

            if (!empty($post_id)) {
                $research_categories = wp_get_post_terms($post_id, 'research_category', array('fields' => 'all'));
        
                if (!empty($research_categories)) {
                    // $research_categories now contains an array of category names
                    dd($research_categories);
                }
            }
        }
        // blogs
        else if($post_type=="post")
        {
            $args = array(
                'name'           => $slug,
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
            );
            
            $query = new WP_Query($args);
            
            if ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                wp_reset_postdata(); // Reset the global post object
            }

            if(isset($post_id) &&!empty($post_id))
            {

                // Get the categories associated with the post
                $post_categories = wp_get_post_terms($post_id, 'category', array('fields' => 'all'));

                $categories = array();

                if (!is_wp_error($post_categories) && !empty($post_categories)) {
                    foreach ($post_categories as $category) {
                        $categories[] = array(
                            'id' => $category->term_id,
                            'name' => $category->name,
                            'slug' => $category->slug,
                        );
                    }
                }
                
                if(count($categories))
                {
                    $json_data =[
                        'catgeories'=> $categories,
                        'first_category'=> count($categories)?$categories[0]:'',
                        'status'=>'success'
                    ];
                }
                else{
                    $json_data =[
                        'status'=>'error'
                    ];
                }
            }
        }
        
        echo json_encode($json_data);
        wp_die(); // Terminate AJAX request
    }
}
add_action('wp_ajax_get_breadcrumbs_post_category_data', 'get_breadcrumbs_post_category_data');
add_action('wp_ajax_nopriv_get_breadcrumbs_post_category_data', 'get_breadcrumbs_post_category_data');

/**
 * Function to get term ID and name by taxonomy and slug.
 *
 * @param string $taxonomy The taxonomy of the term.
 * @param string $slug The slug of the term.
 * @return array|false Array with 'term_id' and 'name' if found, false otherwise.
 */
function get_term_id_and_name_by_taxonomy_and_slug($taxonomy, $slug) {
    $term = get_term_by('slug', $slug, $taxonomy);

    if ($term && !is_wp_error($term)) {
        return array(
            'term_id' => $term->term_id,
            'name' => $term->name,
        );
    }

    return false;
}

/**
 * Function to get term ID and name by taxonomy and term ID.
 *
 * @param string $taxonomy The taxonomy of the term.
 * @param int $term_id The ID of the term.
 * @return array|false Array with 'term_id' and 'name' if found, false otherwise.
 */
function get_term_details_by_taxonomy_and_id($taxonomy, $term_id) {
    $term = get_term_by('id', $term_id, $taxonomy);

    if ($term && !is_wp_error($term)) {
        $term_details = array(
            'term_id' => $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
        );

        // Get category_icon value if it exists
        $category_icon = get_term_meta($term->term_id, 'discy_category_icon', true);
        if (!empty($category_icon)) {
            $term_details['category_icon'] = $category_icon;
        } else {
            $term_details['category_icon'] = '';
        }

        return $term_details;
    }

    return false;
}

// Register Custom Taxonomy
function custom_market_research_tag_taxonomy() {

    $labels = array(
        'name'                       => _x( 'Market Research Tags', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Market Research Tag', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Market Research Tags', 'text_domain' ),
        'all_items'                  => __( 'All Market Research Tags', 'text_domain' ),
        'parent_item'                => __( 'Parent Market Research Tag', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Market Research Tag:', 'text_domain' ),
        'new_item_name'              => __( 'New Market Research Tag Name', 'text_domain' ),
        'add_new_item'               => __( 'Add New Market Research Tag', 'text_domain' ),
        'edit_item'                  => __( 'Edit Market Research Tag', 'text_domain' ),
        'update_item'                => __( 'Update Market Research Tag', 'text_domain' ),
        'view_item'                  => __( 'View Market Research Tag', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate market research tags with commas', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove market research tags', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
        'popular_items'              => __( 'Popular Market Research Tags', 'text_domain' ),
        'search_items'               => __( 'Search Market Research Tags', 'text_domain' ),
        'not_found'                  => __( 'Not Found', 'text_domain' ),
        'no_terms'                   => __( 'No market research tags', 'text_domain' ),
        'items_list'                 => __( 'Market Research Tags list', 'text_domain' ),
        'items_list_navigation'      => __( 'Market Research Tags list navigation', 'text_domain' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'market_research_tag', array( 'market_research' ), $args );

}
add_action( 'init', 'custom_market_research_tag_taxonomy', 0 );

/**
 * Retrieve market research tags for a post.
 *
 * @param int $post_id The post ID.
 * @return array|WP_Error Array of WP_Term objects on success, WP_Error on failure.
 */
function get_market_research_tags( $post_id ) {
    // Ensure the post ID is valid.
    if ( ! $post_id ) {
        return [];
    }

    // Get the terms for the 'market_research_tag' taxonomy.
    $terms = wp_get_post_terms( $post_id, 'market_research_tag' );

    // Return the terms or an empty array if none found.
    if ( is_wp_error( $terms ) ) {
        return $terms;
    }

    return ! empty( $terms ) ? $terms : array();
}


?>