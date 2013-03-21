<? if(isset($no_payment_address)) {?>
   <h2><?= $no_payment_address;?></h2>
<? } else {?>
   <?= $this->builder->display_errors($errors);?>
   <? if ($payment_methods) { ?>
   <p><?= $text_payment_method; ?></p>
   <table class="radio">
     <? foreach ($payment_methods as $payment_method) { ?>
     <tr class="highlight">
       <td>
          <input type="radio" name="payment_method" value="<?= $payment_method['code']; ?>" id="<?= $payment_method['code']; ?>" <?= $payment_method['code'] == $code ? 'checked="checked"' : '';?> />
       </td>
       <td><label for="<?= $payment_method['code']; ?>"><?= $payment_method['title']; ?></label></td>
     </tr>
     <? } ?>
   </table>
   <br />
   <? } ?>
   <div id='add_comment'>
      <div><?= $text_comments; ?></div>
      <textarea name="comment" rows="8"><?= $comment; ?></textarea>
   </div>
   <div class="buttons">
     <div class="right">
       <? if ($text_agree) { ?>
         <span><?= $text_agree; ?></span>
         <input type="checkbox" name="agree" value="1" <?= $agree ? 'checked="checked"' : '';?> />
       <? } ?>
     </div>
   </div>
<? }?>

<script type="text/javascript">//<!--
$('#payment_method').bind('loaded', function (){
   <? if($code && !$guest_checkout){ ?>
   if($('#payment_address').hasClass('valid')){
      validate_info_item('payment_method');
   }
   <? }?>
});

$('#add_comment div').click(function(){$('#add_comment textarea').slideToggle()});
//--></script> 