<?php

if (!function_exists('post_ranking_get_posts')) {
  function post_ranking_get_posts()
  {
    global $wpdb;

    $table_name = $wpdb->prefix . POST_RANKING_POST_VIEW_TABLE_NAME;

    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY count DESC LIMIT 10");
    $posts = [];


    foreach ($results as $result) {
      $posts[] = get_post($result->post_id);
    }

    return  $posts;
  }
}
