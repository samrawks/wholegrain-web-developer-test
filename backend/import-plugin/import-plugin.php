<?php
/**
 * Plugin Name: Import Plugin
 * Description: Wholegrain's boilerplate for the Import Plugin part of the Web Developer Test.
 * Version: 0.2
 * Author: Wholegrain Digital
*/
 
        // Register new custom post type.
        function wgd_custom_post_type() {
            register_post_type('wholegrain_news',
                array(
                    'labels'      => array(
                        'name'          => __('Wholegrain News', 'textdomain'),
                        'singular_name' => __('News item', 'textdomain')                        
                    ),
                    'public'      => true,
                    'has_archive' => true,
                    'show_ui' => true,
                    'show_in_menu' => true,
                    'show_in_admin_bar'    => true,
                    'show_in_nav_menus'    => true,
                    'menu_position' => 4, // Stick it under Posts.
                    'menu_icon' => 'dashicons-megaphone', // I like this one.
                    'supports' => array('title', 'editor', 'page-attributes')
                )
            );
        }
        add_action('init', 'wgd_custom_post_type');



        // Register new taxonomy.
        function wholegrain_categories_hook() {
            $labels = array(
                'name'              => _x( 'Wholegrain Categories', 'taxonomy general name' ),
                'singular_name'     => _x( 'Wholegrain Categories', 'taxonomy singular name' ),
                'search_items'      => __( 'Search Wholegrain Categories' ),
                'all_items'         => __( 'All Wholegrain Categories' ),
                'parent_item'       => __( 'Parent Wholegrain Category' ),
                'parent_item_colon' => __( 'Parent Wholegrain Category:' ),
                'edit_item'         => __( 'Edit Wholegrain Categories' ),
                'update_item'       => __( 'Update Wholegrain Categories' ),
                'add_new_item'      => __( 'Add New Wholegrain Category' ),
                'new_item_name'     => __( 'New Wholegrain Category' ),
                'menu_name'         => __( 'Wholegrain Categories' ),
            );
        
            $args = array(
                'labels' => $labels,
                'public' => true,
            );
        
            register_taxonomy( 'wholegrain_categories', 'wholegrain_news', $args );
        }
        add_action( 'init', 'wholegrain_categories_hook', 0 );



        // Import posts and set their post type & taxonomy.
        function wgd_insert_posts() {
            $imported_posts = file_get_contents('https://www.wholegraindigital.com/wp-json/wp/v2/posts');
            $decoded_posts = json_decode($imported_posts);

            // Loop over the posts.
            foreach($decoded_posts as $post) {
                // Add post args.
                $new_post = array(
                    'ID' => 0,
                    'post_author' => 1,
                    'date' => $post->date,
                    'post_name' => $post->slug,
                    'post_title' => $post->title->rendered,
                    'post_content' => $post->content->rendered,
                    'post_excerpt' => $post->excerpt->rendered,
                    'post_status' => $post->status,
                    'post_type' => 'wholegrain_news',
                    'post_category' => 'wholegrain_categories',
                    'comment_status' =>	'closed',
                    'ping_status' => 'closed',
                );

                // TODO: Add some validation here to check if post exists already.
                $post_ID = wp_insert_post( $new_post, $wp_error );
            }
        }
        add_action( 'init', 'wgd_insert_posts');