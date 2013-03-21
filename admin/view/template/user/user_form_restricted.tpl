<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <?= $this->builder->display_errors($errors);?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/user.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <div id="tabs" class="htabs"><a href="#tab-general"><?= $tab_general; ?></a><a href="#tab-contact"><?= $tab_contact; ?></a></div>
      <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
       <div id='tab-general'>
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?= $entry_username; ?></td>
            <td><span><?= $username; ?></span></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?= $entry_firstname; ?></td>
            <td><input type="text" name="firstname" value="<?= $firstname; ?>" /></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?= $entry_lastname; ?></td>
            <td><input type="text" name="lastname" value="<?= $lastname; ?>" /></td>
          </tr>
          <tr>
            <td><?= $entry_email; ?></td>
            <td><input type="text" name="email" value="<?= $email; ?>" /></td>
          </tr>
          <tr>
            <td><?= $entry_password; ?></td>
            <td><input type="password" autocomplete='off' name="password" value="<?= $password; ?>"  /></td>
          </tr>
          <tr>
            <td><?= $entry_confirm; ?></td>
            <td><input type="password" autocomplete='off' name="confirm" value="<?= $confirm; ?>" /></td>
          </tr>
        </table>
       </div>
       <div id='tab-contact'>
         <?= $contact_template;?>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $footer; ?>

<script type="text/javascript">//<!--
$('#tabs a').tabs();
//--></script>

<?=$this->builder->js('errors',$errors);?>