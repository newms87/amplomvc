<div class="buttons">
  <div class="right">
    <input type="button" value="<?= $button_confirm; ?>" id="button-confirm" class="button" />
    <div id='submit_payment' style='display:none'><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /><span><?= $text_submit_payment;?></span></div>
  </div>
</div>
<script type="text/javascript">
//<!--
$('#button-confirm').bind('click', function() {
   $(this).hide();
   $('#submit_payment').fadeIn(500);
	$.ajax({ 
		type: 'GET',
		url: 'index.php?route=payment/cod/confirm',
		success: function() {
		   $('#submit_payment').html('<?=$text_submit_payment_done;?>');
			location = '<?= $continue; ?>';
		}		
	});
});
//--></script> 
