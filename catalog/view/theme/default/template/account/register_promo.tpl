<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id="content">
  <?= $this->builder->display_errors($errors);?>
  <h1><?= $heading_title; ?></h1>
  <form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
     <?=$text_promo;?>
    <h2><?= $text_your_details; ?></h2>
    <div class="content">
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?= $entry_firstname; ?></td>
          <td><input type="text" name="firstname" value="<?= $firstname; ?>" /></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?= $entry_lastname; ?></td>
          <td><input type="text" name="lastname" value="<?= $lastname; ?>" /></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?= $entry_email; ?></td>
          <td><input type="text" name="email" value="<?= $email; ?>" /></td>
        </tr>
      </table>
    </div>
    <h2><?= $text_your_password; ?></h2>
    <div class="content">
      <table class="form">
        <tr>
          <td><span class="required">*</span> <?= $entry_password; ?></td>
          <td><input type="password" autocomplete='off' name="password" value="<?= $password; ?>" /></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?= $entry_confirm; ?></td>
          <td><input type="password" autocomplete='off' name="confirm" value="<?= $confirm; ?>" /></td>
        </tr>
      </table>
    </div>
    <h2><?= $text_your_address; ?> </h2>
    <div class="content">
      <table class="form">
        <tr>
          <td><?= $entry_company; ?></td>
          <td><input type="text" name="company" value="<?= $company; ?>" /></td>
        </tr>
        <tr>
          <td><?= $entry_address_1; ?></td>
          <td><input type="text" name="address_1" value="<?= $address_1; ?>" /></td>
        </tr>
        <tr>
          <td><?= $entry_address_2; ?></td>
          <td><input type="text" name="address_2" value="<?= $address_2; ?>" /></td>
        </tr>
        <tr>
          <td><?= $entry_city; ?></td>
          <td><input type="text" name="city" value="<?= $city; ?>" /></td>
        </tr>
        <tr>
          <td><?= $entry_postcode; ?></td>
          <td><input type="text" name="postcode" value="<?= $postcode; ?>" /></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?= $entry_country; ?></td>
          <td>
             <?= $this->builder->set_config('country_id', 'name');?>
             <?= $this->builder->build('select', $countries, "country_id", $country_id, array('class'=>"country_select"));?>
          </td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?= $entry_zone; ?></td>
          <td><select name="zone_id" class="zone_select" zone_id="<?=$zone_id;?>"></select></td>
        </tr>
      </table>
    </div>
    <h2><?= $text_newsletter; ?></h2>
    <div class="content">
      <table class="form">
        <tr>
          <td><?= $entry_newsletter; ?></td>
          <td><? if ($newsletter == 1) { ?>
            <input type="radio" name="newsletter" value="1" checked="checked" />
            <?= $text_yes; ?>
            <input type="radio" name="newsletter" value="0" />
            <?= $text_no; ?>
            <? } else { ?>
            <input type="radio" name="newsletter" value="1" />
            <?= $text_yes; ?>
            <input type="radio" name="newsletter" value="0" checked="checked" />
            <?= $text_no; ?>
            <? } ?></td>
        </tr>
      </table>
    </div>
    <? if ($text_agree) { ?>
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
    <? } else { ?>
    <div class="buttons">
      <div class="right">
        <input type="submit" value="<?= $button_continue; ?>" class="button" />
      </div>
    </div>
    <? } ?>
  </form>
  </div>

<?=$this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select');?>
 
<?= $this->builder->js('errors', $errors);?>
<?= $footer; ?>