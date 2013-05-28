<div id="coupon_block">
	<form action="" method="post" <?= $ajax ? "onclick=\"return apply_coupon();\"" : '';?>>
		<span><?= $entry_coupon; ?></span>
		<input id='coupon_code' type="text" name="coupon_code" value="" />
		<input type="submit" value="<?= $button_coupon; ?>" class="button" />
	</form>
</div>

<? if($ajax) { ?>
<script type="text/javascript">//<!--
function apply_coupon(){
	if($('#coupon_code').val()){
			submit_block('coupon', '<?= $ajax_url;?>', $('#coupon_block form'));
	}
	
	return false;
}

$('body').bind('coupon_success', function(){
	$('input[name=coupon_code]').val('');
});
$('body').bind('coupon_error', function(){
});
//--></script>
<? } ?>