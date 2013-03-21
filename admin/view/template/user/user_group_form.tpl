<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <? if ($error_warning) { ?>
  <div class="message_box warning"><?= $error_warning; ?></div>
  <? } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/user-group.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?= $entry_name; ?></td>
            <td><input type="text" name="name" value="<?= $name; ?>" />
              <? if ($error_name) { ?>
              <span class="error"><?= $error_name; ?></span>
              <?  } ?></td>
          </tr>
          <tr>
            <td><?= $entry_access; ?></td>
            <td><div class="scrollbox">
                <? $class = 'odd'; ?>
                <? foreach ($permissions as $permission) { ?>
                <? $class = ($class == 'even' ? 'odd' : 'even'); ?>
                <div class="<?= $class; ?>">
                  <? if (in_array($permission, $access)) { ?>
                  <input type="checkbox" name="permission[access][]" value="<?= $permission; ?>" checked="checked" />
                  <?= $permission; ?>
                  <? } else { ?>
                  <input type="checkbox" name="permission[access][]" value="<?= $permission; ?>" />
                  <?= $permission; ?>
                  <? } ?>
                </div>
                <? } ?>
              </div>
              <a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?= $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?= $text_unselect_all; ?></a></td>
          </tr>
          <tr>
            <td><?= $entry_modify; ?></td>
            <td><div class="scrollbox">
                <? $class = 'odd'; ?>
                <? foreach ($permissions as $permission) { ?>
                <? $class = ($class == 'even' ? 'odd' : 'even'); ?>
                <div class="<?= $class; ?>">
                  <? if (in_array($permission, $modify)) { ?>
                  <input type="checkbox" name="permission[modify][]" value="<?= $permission; ?>" checked="checked" />
                  <?= $permission; ?>
                  <? } else { ?>
                  <input type="checkbox" name="permission[modify][]" value="<?= $permission; ?>" />
                  <?= $permission; ?>
                  <? } ?>
                </div>
                <? } ?>
              </div>
              <a onclick="$(this).parent().find(':checkbox').attr('checked', true);"><?= $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').attr('checked', false);"><?= $text_unselect_all; ?></a></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?= $footer; ?> 