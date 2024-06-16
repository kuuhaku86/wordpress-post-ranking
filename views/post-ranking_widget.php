<?php
require_once(POST_RANKING_PATH . 'functions/functions.php');
$posts = post_ranking_get_posts();
$index = 1;

foreach ($posts as $post) {
?>
  <div class="post-ranking-item">
    <a href="<?= esc_url(get_permalink($post)) ?>">
      <?= $index ?>. <?= $post->post_title ?>
    </a>
  </div>
<?php

  $index++;
}
