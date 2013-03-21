<div id='featured_articles' class="box sidebar_box">
  <div class="box-heading"><?= $heading_title; ?></div>
  <div class="box-content  rss_sidebar">
      <ul>
        <? foreach ($featured_articles as $a) { ?>
        <li>
           <a target='_blank' href='<?=$a['url'];?>' class='article_link'><?=$a['title'];?></a>
        </li>
      <? }?>
      </ul>
  </div>
</div>
