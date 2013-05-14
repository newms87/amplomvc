<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
  <?= $this->builder->display_breadcrumbs();?>
  <h1><?= $heading_title; ?></h1>
  <? if ($orders) { ?>
  <? foreach ($orders as $order) { ?>
  <div class="order-list">
    <div class="order-id"><b><?= $text_order_id; ?></b> #<?= $order['order_id']; ?></div>
    <div class="order-status"><b><?= $text_status; ?></b> <?= $order['status']; ?></div>
    <div class="order-content">
      <div><b><?= $text_date_added; ?></b> <?= $order['date_added']; ?><br />
        <b><?= $text_products; ?></b> <?= $order['products']; ?></div>
      <div><b><?= $text_customer; ?></b> <?= $order['name']; ?><br />
        <b><?= $text_total; ?></b> <?= $order['total']; ?></div>
      <div class="order-info"><a href="<?= $order['href']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'info.png'; ?>" alt="<?= $button_view; ?>" title="<?= $button_view; ?>" /></a>&nbsp;&nbsp;<a href="<?= $order['reorder']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'reorder.png'; ?>" alt="<?= $button_reorder; ?>" title="<?= $button_reorder; ?>" /></a></div>
    </div>
  </div>
  <? } ?>
  <div class="pagination"><?= $pagination; ?></div>
  <? } else { ?>
  <div class="content"><?= $text_empty; ?></div>
  <? } ?>
  <div class="buttons">
    <div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
  </div>
  <?= $content_bottom; ?></div>
<?= $footer; ?>