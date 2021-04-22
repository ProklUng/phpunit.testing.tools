<?php

namespace Prokl\TestingTools\Tools;

use Exception;
use stdClass;
use WP_Post;

/**
 * Class PHPUnitWPMocks
 * @package Prokl\TestingTools\Tools
 */
class PHPUnitWPMocks
{
    /**
     * Фэйковый WP_Post.
     *
     * @param string $title
     * @param string $content
     * @param string $post_type
     * @param string $post_status
     *
     * @return WP_Post
     * @throws Exception
     */
    public static function getWpPost(
        $title = '',
        $content = '',
        $post_type = 'post',
        $post_status = 'publish'
    ) : WP_Post {
        $post_id = -99; // negative ID, to avoid clash with a valid post
        $post = new stdClass();
        $post->ID = $post_id;
        $post->post_author = 1;
        $post->post_date = current_time('mysql');
        $post->post_date_gmt = current_time('mysql', 1);
        $post->post_modified_gmt = current_time('mysql', 1);
        $post->post_title = $title;
        $post->post_content = $content;
        $post->post_status = $post_status;
        $post->comment_status = 'closed';
        $post->ping_status = 'closed';
        $post->post_name = 'fake-page-' . random_int(1, 99999); // append random number to avoid clash
        $post->post_type = $post_type;
        $post->filter = 'raw'; // important!

        return new WP_Post($post);
    }

    /**
     * Фэйковый WP Query.
     *
     * @param WP_Post $fakeWpPost
     *
     * @return mixed
     */
    public static function fakeWpQuery(WP_Post $fakeWpPost)
    {
        global $wp, $wp_query;

        $backupWpQuery =  $wp_query;

        // Update the main query
        $wp_query->post = $fakeWpPost;
        $wp_query->posts = [$fakeWpPost];
        $wp_query->queried_object = $fakeWpPost;
        $wp_query->queried_object_id = $fakeWpPost->ID;
        $wp_query->found_posts = 1;
        $wp_query->post_count = 1;
        $wp_query->max_num_pages = 1;
        $wp_query->is_page = true;
        $wp_query->is_singular = true;
        $wp_query->is_single = false;
        $wp_query->is_attachment = false;
        $wp_query->is_archive = false;
        $wp_query->is_category = false;
        $wp_query->is_tag = false;
        $wp_query->is_tax = false;
        $wp_query->is_author = false;
        $wp_query->is_date = false;
        $wp_query->is_year = false;
        $wp_query->is_month = false;
        $wp_query->is_day = false;
        $wp_query->is_time = false;
        $wp_query->is_search = false;
        $wp_query->is_feed = false;
        $wp_query->is_comment_feed = false;
        $wp_query->is_trackback = false;
        $wp_query->is_home = false;
        $wp_query->is_embed = false;
        $wp_query->is_404 = false;
        $wp_query->is_paged = false;
        $wp_query->is_admin = false;
        $wp_query->is_preview = false;
        $wp_query->is_robots = false;
        $wp_query->is_posts_page = false;
        $wp_query->is_post_type_archive = false;

        // Update globals
        $GLOBALS['wp_query'] = $wp_query;
        $wp->register_globals();

        return $backupWpQuery;
    }

    /**
     * Восстановить WP_Query из бэкапа.
     *
     * @param $backupWpQuery
     */
    public static function resetFakeWpQuery($backupWpQuery): void
    {
        global $wp;

        // Update globals
        $GLOBALS['wp_query'] = $backupWpQuery;
        $wp->register_globals();
    }
}
