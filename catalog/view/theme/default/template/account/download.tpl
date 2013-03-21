<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
  <?= $this->builder->display_breadcrumbs();?>
  <h1><?= $heading_title; ?></h1>
  <? foreach ($downloads as $download) { ?>
  <div class="download-list">
    <div class="download-id"><b><?= $text_order; ?></b> <?= $download['order_id']; ?></div>
    <div class="download-status"><b><?= $text_size; ?></b> <?= $download['size']; ?></div>
    <div class="download-content">
      <div><b><?= $text_name; ?></b> <?= $download['name']; ?><br />
        <b><?= $text_date_added; ?></b> <?= $download['date_added']; ?></div>
      <div><b><?= $text_remaining; ?></b> <?= $download['remaining']; ?></div>
      <div class="download-info">
        <? if ($download['remaining'] > 0) { ?>
        <a href="<?= $download['href']; ?>"><img src="catalog/view/theme/default/image/download.png" alt="<?= $button_download; ?>" title="<?= $button_download; ?>" /></a>
        <? } ?>
      </div>
    </div>
  </div>
  <? } ?>
  <div class="pagination"><?= $pagination; ?></div>
  <div class="buttons">
    <div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
  </div>
  <?= $content_bottom; ?></div>
<?= $footer; ?>