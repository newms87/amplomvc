<?= $header; ?>
<div class="content">
  <?=$this->builder->display_breadcrumbs();?>
  <?= $this->builder->display_errors($errors);?>
  <div class="box">
    <div class="heading">
      <h1><img src="<?= HTTP_THEME_IMAGE . 'language.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left"><? if ($sort == 'name') { ?>
                <a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= $column_name; ?></a>
                <? } else { ?>
                <a href="<?= $sort_name; ?>"><?= $column_name; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'code') { ?>
                <a href="<?= $sort_code; ?>" class="<?= strtolower($order); ?>"><?= $column_code; ?></a>
                <? } else { ?>
                <a href="<?= $sort_code; ?>"><?= $column_code; ?></a>
                <? } ?></td>
              <td class="right"><? if ($sort == 'sort_order') { ?>
                <a href="<?= $sort_sort_order; ?>" class="<?= strtolower($order); ?>"><?= $column_sort_order; ?></a>
                <? } else { ?>
                <a href="<?= $sort_sort_order; ?>"><?= $column_sort_order; ?></a>
                <? } ?></td>
              <td class="right"><?= $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <? if ($languages) { ?>
            <? foreach ($languages as $language) { ?>
            <tr>
              <td style="text-align: center;"><? if ($language['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?= $language['language_id']; ?>" checked="checked" />
                <? } else { ?>
                <input type="checkbox" name="selected[]" value="<?= $language['language_id']; ?>" />
                <? } ?></td>
              <td class="left"><?= $language['name']; ?></td>
              <td class="left"><?= $language['code']; ?></td>
              <td class="right"><?= $language['sort_order']; ?></td>
              <td class="right"><? foreach ($language['action'] as $action) { ?>
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