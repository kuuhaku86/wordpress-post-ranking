<?php
if (!class_exists('Post_Ranking_Widget')) {
  class Post_Ranking_Widget extends WP_Widget
  {
    public function __construct()
    {
      $widget_options = array(
        'description' => __('Your post ranking widgets', 'post-ranking')
      );

      parent::__construct(
        'post-ranking',
        'Post Ranking',
        $widget_options,
      );

      add_action(
        'widgets_init',
        function () {
          register_widget(
            'Post_Ranking_Widget'
          );
        }
      );
    }

    public function widget($args, $instance)
    {
      $title = 'Post Ranking';

      echo $args['before_title'] . $title . $args['after_title'];
      require(POST_RANKING_PATH . 'views/post-ranking_widget.php');
    }
  }
}
