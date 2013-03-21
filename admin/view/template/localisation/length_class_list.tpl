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
      <h1><img src="view/image/length.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left"><? if ($sort == 'title') { ?>
                <a href="<?= $sort_title; ?>" class="<?= strtolower($order); ?>"><?= $column_title; ?></a>
                <? } else { ?>
                <a href="<?= $sort_title; ?>"><?= $column_title; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'unit') { ?>
                <a href="<?= $sort_unit; ?>" class="<?= strtolower($order); ?>"><?= $column_unit; ?></a>
                <? } else { ?>
                <a href="<?= $sort_unit; ?>"><?= $column_unit; ?></a>
                <? } ?></td>
              <td class="right"><? if ($sort == 'value') { ?>
                <a href="<?= $sort_value; ?>" class="<?= strtolower($order); ?>"><?= $column_value; ?></a>
                <? } else { ?>
                <a href="<?= $sort_value; ?>"><?= $column_value; ?></a>
                <? } ?></td>
              <td class="right"><?= $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <? if ($length_classes) { ?>
            <? foreach ($length_classes as $length_class) { ?>
            <tr>
              <td style="text-align: center;"><? if ($length_class['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?= $length_class['length_class_id']; ?>" checked="checked" />
                <? } else { ?>
                <input type="checkbox" name="selected[]" value="<?= $length_class['length_class_id']; ?>" />
                <? } ?></td>
              <td class="left"><?= $length_class['title']; ?></td>
              <td class="left"><?= $length_class['unit']; ?></td>
              <td class="right"><?= $length_class['value']; ?></td>
              <td class="right"><? foreach ($length_class['action'] as $action) { ?>
                [ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
                <? } ?></td>
            </tr>
            <? } ?>
            <? } else { ?>
            <tr>
              <td class="center" colspan="5"><?= $text_no_results; ?></td>
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