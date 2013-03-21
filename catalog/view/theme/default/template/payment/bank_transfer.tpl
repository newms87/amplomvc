<h2><?= $text_instruction; ?></h2>
<p><?= $text_description; ?></p>
<p><?= $bank; ?></p>
<p><?= $text_payment; ?></p>
<div class="buttons">
  <div class="right">
    <input type="button" value="<?= $button_confirm; ?>" id="button-confirm" class="button" />
  </div>
</div>
<script type="text/javascript">
//<!--
$('#button-confirm').bind('click', function() {
	$.ajax({ 
		type: 'GET',
		url: 'index.php?route=payment/bank_transfer/confirm',
		success: function() {
			location = '<?= $continue; ?>';
		}		
	});
});
//--></script> 
