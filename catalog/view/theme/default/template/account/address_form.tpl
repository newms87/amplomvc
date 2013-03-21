<?= $header; ?>
<?= $this->builder->display_errors($errors);?>

<?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
  <?= $this->builder->display_breadcrumbs();?>
  <h1><?= $heading_title; ?></h1>
  <form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
    <h2><?= $text_edit_address; ?></h2>
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
          <td><?= $entry_company; ?></td>
          <td><input type="text" name="company" value="<?= $company; ?>" /></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?= $entry_address_1; ?></td>
          <td><input type="text" name="address_1" value="<?= $address_1; ?>" /></td>
        </tr>
        <tr>
          <td><?= $entry_address_2; ?></td>
          <td><input type="text" name="address_2" value="<?= $address_2; ?>" /></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?= $entry_city; ?></td>
          <td><input type="text" name="city" value="<?= $city; ?>" /></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?= $entry_postcode; ?></td>
          <td><input type="text" name="postcode" value="<?= $postcode; ?>" /></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?= $entry_country; ?></td>
          <td>
             <?= $this->builder->set_builder_config('country_id', 'name');?>
             <?= $this->builder->build('select', $countries, "country_id", $country_id, array('class'=>"country_select"));?>
          </td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?= $entry_zone; ?></td>
          <td><select name="zone_id" class="zone_select" zone_id="<?=$zone_id;?>"></select></td>
        </tr>
        <tr>
          <td><?= $entry_default; ?></td>
          <td><? if ($default) { ?>
            <input type="radio" name="default" value="1" checked="checked" />
            <?= $text_yes; ?>
            <input type="radio" name="default" value="0" />
            <?= $text_no; ?>
            <? } else { ?>
            <input type="radio" name="default" value="1" />
            <?= $text_yes; ?>
            <input type="radio" name="default" value="0" checked="checked" />
            <?= $text_no; ?>
            <? } ?></td>
        </tr>
      </table>
    </div>
    <div class="buttons">
      <div class="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></div>
      <div class="right"><input type="submit" value="<?= $button_continue; ?>" class="button" /></div>
    </div>
  </form>
  <?= $content_bottom; ?></div>

<?=$this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select');?>

<?= $footer; ?>