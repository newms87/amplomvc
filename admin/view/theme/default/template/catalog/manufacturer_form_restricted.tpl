<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <? if($errors){?>
     <div class="message_box warning">
     <? $br=false; foreach($errors as $e){ echo ($br?'<br>':'') . $e; $br=true;}?>
     </div>
  <? }?>
  
  <div class="box">
    <div class="heading">
      <h1><img src="<?= HTTP_THEME_IMAGE . 'shipping.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><? if($editable){?><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><? }?><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <? if(!$editable){?>
         <span><?= $text_not_editable;?></span>
      <? }else{?>
      <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-general">
          <table class="form">
            <tr>
              <td><span class="required">*</span> <?= $entry_name; ?></td>
              <td><input type="text" name="name" value="<?= $name; ?>" size="100" /></td>
            </tr>
            <div id="languages" class="htabs">
	            <? foreach ($languages as $language) { ?>
	            <a href="#language<?= $language['language_id']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /> <?= $language['name']; ?></a>
	            <? } ?>
	         </div>
            <tr>
                <td><?= $entry_description; ?></td>
                <? foreach ($languages as $language) {?>
                	<td id='language<?= $language['language_id'];?>'><textarea class='ckedit' name="manufacturer_description[<?= $language['language_id']; ?>][description]" id="description<?= $language['language_id']; ?>"><?= isset($manufacturer_description[$language['language_id']]) ? $manufacturer_description[$language['language_id']]['description'] : ''; ?></textarea></td>
                <? } ?>
            </tr>
            <tr>
                <td><?= $entry_shipping_return; ?></td>
                <? foreach ($languages as $language) {?>
                  <td id='language<?= $language['language_id'];?>'><textarea class='ckedit' name="manufacturer_description[<?= $language['language_id']; ?>][shipping_return]" id="ship-ret<?= $language['language_id']; ?>"><?= isset($manufacturer_description[$language['language_id']]) ? $manufacturer_description[$language['language_id']]['shipping_return'] : ''; ?></textarea></td>
                <? } ?>
            </tr>
            <tr>
              <td><?= $entry_image; ?></td>
               <td>
               	<?= $this->builder->set_builder_template('click_image');?>
						<?= $this->builder->image_input("image", $image, $thumb);?>
					</td>
            </tr>
          </table>
        </div>
      </form>
      <? }?>
    </div>
  </div>
</div>
<?= $footer; ?>

<?= $this->builder->js('ckeditor');?>

<script type="text/javascript">//<!--
$('#tabs a').tabs();
$('#languages a').tabs();
//--></script> 
<?=$this->builder->js('errors',$errors)?>
