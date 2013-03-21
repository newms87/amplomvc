<h2><?= $text_instruction; ?></h2>
<p><b><?= $text_payable; ?></b></p>
<p><?= $payable; ?></p>
<b><?= $text_address; ?></b><br />
<p><?= $address; ?></p>
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
		url: 'index.php?route=payment/cheque/confirm',
		success: function() {
			location = '<?= $continue; ?>';
		}		
	});
});
//--></script> 
