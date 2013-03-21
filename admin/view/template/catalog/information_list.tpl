<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <? if ($error_warning) { ?>
  <div class="message_box warning"><?= $error_warning; ?></div>
  <? } ?>
  <? if ($success) { ?>
  <div class="message_box success"><?= $success; ?></div>
  <? } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/information.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left"><? if ($sort == 'id.title') { ?>
                <a href="<?= $sort_title; ?>" class="<?= strtolower($order); ?>"><?= $column_title; ?></a>
                <? } else { ?>
                <a href="<?= $sort_title; ?>"><?= $column_title; ?></a>
                <? } ?></td>
              <td class="right"><? if ($sort == 'i.sort_order') { ?>
                <a href="<?= $sort_sort_order; ?>" class="<?= strtolower($order); ?>"><?= $column_sort_order; ?></a>
                <? } else { ?>
                <a href="<?= $sort_sort_order; ?>"><?= $column_sort_order; ?></a>
                <? } ?></td>
              <td class="right"><?= $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <? if ($informations) { ?>
            <? foreach ($informations as $information) { ?>
            <tr>
              <td style="text-align: center;"><? if ($information['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?= $information['information_id']; ?>" checked="checked" />
                <? } else { ?>
                <input type="checkbox" name="selected[]" value="<?= $information['information_id']; ?>" />
                <? } ?></td>
              <td class="left"><?= $information['title']; ?></td>
              <td class="right"><?= $information['sort_order']; ?></td>
              <td class="right"><? foreach ($information['action'] as $action) { ?>
                [ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
                <? } ?></td>
            </tr>
            <? } ?>
            <? } else { ?>
            <tr>
              <td class="center" colspan="4"><?= $text_no_results; ?></td>
            </tr>
            <? } ?>
          </tbody>
        </table>
      </form>
      <div class="pagination"><?= $pagination; ?></div>
    </div>
  </div>
</div>
<?= $footer; ?>