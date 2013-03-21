<? if ($reviews) { ?>
<? foreach ($reviews as $review) { ?>
<div class="content"><b><?= $review['author']; ?></b> | <img src="catalog/view/theme/default/image/stars-<?= $review['rating'] . '.png'; ?>" alt="<?= $review['reviews']; ?>" /><br />
  <?= $review['date_added']; ?><br />
  <br />
  <?= $review['text']; ?></div>
<? } ?>
<div class="pagination"><?= $pagination; ?></div>
<? } else { ?>
<div class="content"><?= $text_no_reviews; ?></div>
<? } ?>
