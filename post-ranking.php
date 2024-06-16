<?php

/**
 * Plugin Name: Post Ranking
 * Plugin URI: https://www.wordpress.org/post-ranking
 * Description: My plugin's description
 * Version: 1.0
 * Requires at least: 5.6
 * Requires PHP: 7.0
 * Author: Yohan Ardiansyah
 * Author URI: https://www.codigowp.net
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: post-ranking
 * Domain Path: /languages
 */
/*
Post Ranking is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Post Ranking is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Post Ranking. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

if (!class_exists('Post_Ranking')) {

  class Post_Ranking
  {

    public function __construct()
    {

      $this->define_constants();

      add_action('the_post', array($this, 'add_post_view_count'), 999);

      require_once(POST_RANKING_PATH . 'widgets/class.post-ranking-widget.php');
      $PostRankingWidget = new Post_Ranking_Widget;
    }

    public function define_constants()
    {
      define('POST_RANKING_PATH', plugin_dir_path(__FILE__));
      define('POST_RANKING_URL', plugin_dir_url(__FILE__));
      define('POST_RANKING_VERSION', '1.0.0');
      define('POST_RANKING_POST_VIEW_TABLE_NAME', 'post_views');
    }

    /**
     * Activate the plugin
     */
    public static function activate()
    {
      update_option('rewrite_rules', '');

      global $wpdb;

      $table_name = $wpdb->prefix . POST_RANKING_POST_VIEW_TABLE_NAME;

      $post_ranking_db_version = get_option('post_ranking_db_version');

      if (empty($post_ranking_db_version)) {
        $query = "
                    CREATE TABLE $table_name (
                        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                        post_id bigint(20) NOT NULL default '0',
                        count bigint(20) DEFAULT NULL,
                        KEY id (id)
                    )
                    ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($query);

        $post_ranking_db_version = POST_RANKING_VERSION;
        add_option('post_ranking_db_version', $post_ranking_db_version);
      }
    }

    /**
     * Deactivate the plugin
     */
    public static function deactivate()
    {
      flush_rewrite_rules();
    }

    /**
     * Uninstall the plugin
     */
    public static function uninstall()
    {
      delete_option('post_ranking_db_version');

      global $wpdb;

      $table_name = $wpdb->prefix . POST_RANKING_POST_VIEW_TABLE_NAME;

      $wpdb->query($wpdb->prepare(
        "
                    DROP TABLE IF EXIST %s
                ",
        $table_name
      ));
    }

    public function add_post_view_count($post)
    {
      global $wpdb;

      $table_name = $wpdb->prefix . POST_RANKING_POST_VIEW_TABLE_NAME;

      $results = $wpdb->get_results("SELECT * FROM $table_name WHERE post_id= '" . $post->ID . "'");

      if ($wpdb->num_rows > 0) {
        $data = array(
          'count' => $results[0]->count + 1
        );

        $wpdb->update($table_name, $data, array(
          'post_id' => $post->ID
        ));
      } else {
        $wpdb->insert(
          $table_name,
          array(
            'post_id' => $post->ID,
            'count'   => 1,
          )
        );
      }
    }
  }
}

// Plugin Instantiation
if (class_exists('Post_Ranking')) {

  // Installation and uninstallation hooks
  register_activation_hook(__FILE__, array('Post_Ranking', 'activate'));
  register_deactivation_hook(__FILE__, array('Post_Ranking', 'deactivate'));
  register_uninstall_hook(__FILE__, array('Post_Ranking', 'uninstall'));

  // Instatiate the plugin class
  $post_ranking = new Post_Ranking();
}
