<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <? if ($error_warning) { ?>
  <div class="message_box warning"><?= $error_warning; ?></div>
  <? } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/customer.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?= $entry_ip; ?></td>
            <td><input type="text" name="ip" value="<?= $ip; ?>" />
              <? if ($error_ip) { ?>
              <span class="error"><?= $error_ip; ?></span>
              <? } ?></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?= $footer; ?>