<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <?= $this->builder->display_errors($errors);?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/category.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('#form').submit();" class="button"><?= $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left"><?= $column_name; ?></td>
              <td class="right"><?= $column_sort_order; ?></td>
              <td class="right"><?= $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <? if ($categories) { ?>
            <? foreach ($categories as $category) { ?>
            <tr>
              <td style="text-align: center;"><? if ($category['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?= $category['category_id']; ?>" checked="checked" />
                <? } else { ?>
                <input type="checkbox" name="selected[]" value="<?= $category['category_id']; ?>" />
                <? } ?></td>
              <td class="left"><?= $category['name']; ?></td>
              <td class="right"><?= $category['sort_order']; ?></td>
              <td class="right"><? foreach ($category['action'] as $action) { ?>
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
    </div>
  </div>
</div>
<?= $footer; ?>