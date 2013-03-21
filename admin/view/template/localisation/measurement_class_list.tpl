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
      <h1><img src="view/image/measurement.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="location='<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
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
              <td class="right"><?= $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <? if ($measurement_classes) { ?>
            <? foreach ($measurement_classes as $measurement_class) { ?>
            <tr>
              <td style="align: center;"><? if ($measurement_class['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?= $measurement_class['measurement_class_id']; ?>" checked="checked" />
                <? } else { ?>
                <input type="checkbox" name="selected[]" value="<?= $measurement_class['measurement_class_id']; ?>" />
                <? } ?></td>
              <td class="left"><?= $measurement_class['title']; ?></td>
              <td class="left"><?= $measurement_class['unit']; ?></td>
              <td class="right"><? foreach ($measurement_class['action'] as $action) { ?>
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