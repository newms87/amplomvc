<?= $header; ?>
<? if ($error_warning) { ?>
<div class="message_box warning"><?= $error_warning; ?></div>
<? } ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
  <?= $this->builder->display_breadcrumbs();?>
  <h1><?= $heading_title; ?></h1>
  <p><?= $text_description; ?></p>
  <form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
    <table class="form">
      <tr>
        <td><span class="required">*</span> <?= $entry_to_name; ?></td>
        <td><input type="text" name="to_name" value="<?= $to_name; ?>" />
          <? if ($error_to_name) { ?>
          <span class="error"><?= $error_to_name; ?></span>
          <? } ?></td>
      </tr>
      <tr>
        <td><span class="required">*</span> <?= $entry_to_email; ?></td>
        <td><input type="text" name="to_email" value="<?= $to_email; ?>" />
          <? if ($error_to_email) { ?>
          <span class="error"><?= $error_to_email; ?></span>
          <? } ?></td>
      </tr>
      <tr>
        <td><span class="required">*</span> <?= $entry_from_name; ?></td>
        <td><input type="text" name="from_name" value="<?= $from_name; ?>" />
          <? if ($error_from_name) { ?>
          <span class="error"><?= $error_from_name; ?></span>
          <? } ?></td>
      </tr>
      <tr>
        <td><span class="required">*</span> <?= $entry_from_email; ?></td>
        <td><input type="text" name="from_email" value="<?= $from_email; ?>" />
          <? if ($error_from_email) { ?>
          <span class="error"><?= $error_from_email; ?></span>
          <? } ?></td>
      </tr>
      <tr>
        <td><span class="required">*</span> <?= $entry_theme; ?></td>
        <td><? foreach ($voucher_themes as $voucher_theme) { ?>
          <? if ($voucher_theme['voucher_theme_id'] == $voucher_theme_id) { ?>
          <input type="radio" name="voucher_theme_id" value="<?= $voucher_theme['voucher_theme_id']; ?>" id="voucher-<?= $voucher_theme['voucher_theme_id']; ?>" checked="checked" />
          <label for="voucher-<?= $voucher_theme['voucher_theme_id']; ?>"><?= $voucher_theme['name']; ?></label>
          <? } else { ?>
          <input type="radio" name="voucher_theme_id" value="<?= $voucher_theme['voucher_theme_id']; ?>" id="voucher-<?= $voucher_theme['voucher_theme_id']; ?>" />
          <label for="voucher-<?= $voucher_theme['voucher_theme_id']; ?>"><?= $voucher_theme['name']; ?></label>
          <? } ?>
          <br />
          <? } ?>
          <? if ($error_theme) { ?>
          <span class="error"><?= $error_theme; ?></span>
          <? } ?></td>
      </tr>
      <tr>
        <td><?= $entry_message; ?></td>
        <td><textarea name="message" cols="40" rows="5"><?= $message; ?></textarea></td>
      </tr>
      <tr>
        <td><span class="required">*</span> <?= $entry_amount; ?></td>
        <td><input type="text" name="amount" value="<?= $amount; ?>" size="5" />
          <? if ($error_amount) { ?>
          <span class="error"><?= $error_amount; ?></span>
          <? } ?></td>
      </tr>
    </table>
    <div class="buttons">
      <div class="right"><?= $text_agree; ?>
        <? if ($agree) { ?>
        <input type="checkbox" name="agree" value="1" checked="checked" />
        <? } else { ?>
        <input type="checkbox" name="agree" value="1" />
        <? } ?>
        <input type="submit" value="<?= $button_continue; ?>" class="button" />
      </div>
    </div>
  </form>
  <?= $content_bottom; ?></div>
<?= $footer; ?>