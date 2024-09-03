<?php



function underscores_child_enqueue_scripts() {
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], null, 'all');

    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], null, true);
}

add_action('wp_enqueue_scripts', 'underscores_child_enqueue_scripts');



function custom_post_type_news() {
    $labels = array(
        'name'               => 'News',
        'singular_name'      => 'News',
        'menu_name'          => 'News',
        'name_admin_bar'     => 'News',
        'add_new'            => 'Add New News',
        'add_new_item'       => 'Add New News',
        'new_item'           => 'New News',
        'edit_item'          => 'Edit News',
        'view_item'          => 'View News',
        'all_items'          => 'All News',
        'search_items'       => 'Search News',
        'not_found'          => 'No news found.',
        'not_found_in_trash' => 'No news found in Trash.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'rewrite'            => array('slug' => 'news', 'with_front' => false),
        'supports'           => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments'),
        'show_in_rest'       => true,
        'taxonomies'         => array('category'),  
    );

    register_post_type('news', $args);
}

add_action('init', 'custom_post_type_news');



function news_shortcode($atts) {

    $atts = shortcode_atts(array(
        'number'   => 3,
        'category' => '',
    ), $atts, 'news');

    $args = array(
        'post_type'      => 'news',
        'posts_per_page' => intval($atts['number']),
        'category_name'  => sanitize_text_field($atts['category']),
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $output = '<div class="news-posts">';
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $image = get_the_post_thumbnail($post_id, array(490, 328));
            if(!$image){
                $image = '<img src="'.get_stylesheet_directory_uri().'/assets/images/Default-News-Post.webp" alt="'.$title.' thumbnail">';
            }
            $title = get_the_title();
            if( has_excerpt() ){
                $excerpt = get_the_excerpt();
            }else{
                $excerpt = wp_trim_words(get_the_content(), 20);
            }
            $link = get_permalink();
            $output .= '<div class="news-post">';
            $output .= '<div class="news-post-image">' . $image . '</div>';
            $output .= '<div class="news-post-content">';
            $output .= '<h2>' . $title . '</h2>';
            $output .= '<p>' . $excerpt . '</p>';
            $output .= '<a href="' . $link . '">Read More</a>';
            $output .= '</div>';
            $output .= '</div>';
        }
        $output .= '</div>';
        wp_reset_postdata();
    } else {
        //$output = '<p>No news</p>';
    }

    return $output;
}

add_shortcode('news', 'news_shortcode');



?>