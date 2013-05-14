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
      <h1><img src="<?= HTTP_THEME_IMAGE . 'order.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left"><? if ($sort == 'agd.name') { ?>
                <a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= $column_name; ?></a>
                <? } else { ?>
                <a href="<?= $sort_name; ?>"><?= $column_name; ?></a>
                <? } ?></td>
              <td class="right"><? if ($sort == 'ag.sort_order') { ?>
                <a href="<?= $sort_sort_order; ?>" class="<?= strtolower($order); ?>"><?= $column_sort_order; ?></a>
                <? } else { ?>
                <a href="<?= $sort_sort_order; ?>"><?= $column_sort_order; ?></a>
                <? } ?></td>
              <td class="right"><?= $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <? if ($attribute_groups) { ?>
            <? foreach ($attribute_groups as $attribute_group) { ?>
            <tr>
              <td style="text-align: center;"><? if ($attribute_group['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?= $attribute_group['attribute_group_id']; ?>" checked="checked" />
                <? } else { ?>
                <input type="checkbox" name="selected[]" value="<?= $attribute_group['attribute_group_id']; ?>" />
                <? } ?></td>
              <td class="left"><?= $attribute_group['name']; ?></td>
              <td class="right"><?= $attribute_group['sort_order']; ?></td>
              <td class="right"><? foreach ($attribute_group['action'] as $action) { ?>
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