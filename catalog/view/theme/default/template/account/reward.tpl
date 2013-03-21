<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
  <?= $this->builder->display_breadcrumbs();?>
  <h1><?= $heading_title; ?></h1>
  <p><?= $text_total; ?><b> <?= $total; ?></b>.</p>
  <table class="list">
    <thead>
      <tr>
        <td class="left"><?= $column_date_added; ?></td>
        <td class="left"><?= $column_description; ?></td>
        <td class="right"><?= $column_points; ?></td>
      </tr>
    </thead>
    <tbody>
      <? if ($rewards) { ?>
      <? foreach ($rewards  as $reward) { ?>
      <tr>
        <td class="left"><?= $reward['date_added']; ?></td>
        <td class="left"><? if ($reward['order_id']) { ?>
          <a href="<?= $reward['href']; ?>"><?= $reward['description']; ?></a>
          <? } else { ?>
          <?= $reward['description']; ?>
          <? } ?></td>
        <td class="right"><?= $reward['points']; ?></td>
      </tr>
      <? } ?>
      <? } else { ?>
      <tr>
        <td class="center" colspan="5"><?= $text_empty; ?></td>
      </tr>
      <? } ?>
    </tbody>
  </table>
  <div class="pagination"><?= $pagination; ?></div>
  <div class="buttons">
    <div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
  </div>
  <?= $content_bottom; ?></div>
<?= $footer; ?>