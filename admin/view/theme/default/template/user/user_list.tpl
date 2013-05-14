<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <?= $this->builder->display_errors($errors);?>
  <div class="box">
    <div class="heading">
      <h1><img src="<?= HTTP_THEME_IMAGE . 'user.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left"><? if ($sort == 'username') { ?>
                <a href="<?= $sort_username; ?>" class="<?= strtolower($order); ?>"><?= $column_username; ?></a>
                <? } else { ?>
                <a href="<?= $sort_username; ?>"><?= $column_username; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'email') { ?>
                <a href="<?= $sort_email; ?>" class="<?= strtolower($order); ?>"><?= $column_email; ?></a>
                <? } else { ?>
                <a href="<?= $sort_email; ?>"><?= $column_email; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'status') { ?>
                <a href="<?= $sort_status; ?>" class="<?= strtolower($order); ?>"><?= $column_status; ?></a>
                <? } else { ?>
                <a href="<?= $sort_status; ?>"><?= $column_status; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'date_added') { ?>
                <a href="<?= $sort_date_added; ?>" class="<?= strtolower($order); ?>"><?= $column_date_added; ?></a>
                <? } else { ?>
                <a href="<?= $sort_date_added; ?>"><?= $column_date_added; ?></a>
                <? } ?></td>
              <td class="right"><?= $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <? if ($users) { ?>
            <? foreach ($users as $user) { ?>
            <tr>
              <td style="text-align: center;"><? if ($user['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?= $user['user_id']; ?>" checked="checked" />
                <? } else { ?>
                <input type="checkbox" name="selected[]" value="<?= $user['user_id']; ?>" />
                <? } ?></td>
              <td class="left"><?= $user['username']; ?></td>
              <td class="left"><?= $user['email']; ?></td>
              <td class="left"><?= $user['status']; ?></td>
              <td class="left"><?= $user['date_added']; ?></td>
              <td class="right"><? foreach ($user['action'] as $action) { ?>
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