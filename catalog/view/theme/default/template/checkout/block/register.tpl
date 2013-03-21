<div id='register-account'>
 <form action='<?= $register_url;?>' method="post">
   <div class="left">
     <h2><?= $text_your_details; ?></h2>
     <?= $form_register;?>
     
     <h2><?= $text_your_password; ?></h2>
     <?= $form_password;?>
   </div>
   <div class="right">
     <h2><?= $text_your_address; ?></h2>
     <?= $form_address;?>
   </div>
   <div style="clear: both; padding-top: 15px; border-top: 1px solid #EEEEEE;">
     <input type="checkbox" name="newsletter" value="1" id="newsletter" checked='checked' />
     <label for="newsletter"><?= $entry_newsletter; ?></label>
   </div>
   <? if ($text_agree) { ?>
   <div class="buttons">
     <div class="right"><?= $text_agree; ?>
       <input type="checkbox" name="agree" value="1" />
       <input type="button" value="<?= $button_continue; ?>" id="button-register" class="button" />
     </div>
   </div>
   <? } else { ?>
   <div class="buttons">
     <div class="right"><input type="submit" value="<?= $button_continue; ?>" onclick="submit_checkout_item($(this)); return false;" id="button-register" class="button" /></div>
   </div>
   <? } ?>
 </form>
</div>

<?=$this->builder->js('load_zones', 'table.form', '.country_select', '.zone_select');?>
