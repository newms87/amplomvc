<h2><?= $text_credit_card; ?></h2>
<div id="payment">
  <table class="form">
    <tr>
      <td><?= $entry_cc_owner; ?></td>
      <td><input type="text" name="cc_owner" value="" /></td>
    </tr>
    <tr>
      <td><?= $entry_cc_number; ?></td>
      <td><input type="text" name="cc_number" value="" /></td>
    </tr>
    <tr>
      <td><?= $entry_cc_expire_date; ?></td>
      <td><select name="cc_expire_date_month">
          <? foreach ($months as $month) { ?>
          <option value="<?= $month['value']; ?>"><?= $month['text']; ?></option>
          <? } ?>
        </select>
        /
        <select name="cc_expire_date_year">
          <? foreach ($year_expire as $year) { ?>
          <option value="<?= $year['value']; ?>"><?= $year['text']; ?></option>
          <? } ?>
        </select></td>
    </tr>
    <tr>
      <td><?= $entry_cc_cvv2; ?></td>
      <td><input type="text" name="cc_cvv2" value="" size="3" /></td>
    </tr>
  </table>
</div>
<div class="buttons">
  <div class="right">
    <input type="button" value="<?= $button_confirm; ?>" id="button-confirm" class="button" />
  </div>
</div>
<script type="text/javascript">
$('#button-confirm').bind('click', function() {
	$.ajax({
		url: "<?= HTTP_CATALOG . "index.php?route=payment/web_payment_software/send"; ?>",
		type: 'post',
		data: $('#payment :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm').attr('disabled', true);
			
			$('#payment').before('<div class="attention"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> <?= $text_wait; ?></div>');
		},
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
				
				$('#button-confirm').attr('disabled', false);
			}
			
			$('.attention').remove();
			
			if (json['success']) {
				location = json['success'];
			}
		}
	});
});
</script> 
