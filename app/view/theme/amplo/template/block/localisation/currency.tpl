<? if (count($currencies) > 1) { ?>
	<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<div id="currency">{{Currency}}<br/>
			<? foreach ($currencies as $currency) { ?>
				<? if ($currency['code'] == $currency_code) { ?>
					<? if ($currency['symbol_left']) { ?>
						<a title="<?= $currency['title']; ?>"><b><?= $currency['symbol_left']; ?></b></a>
					<? } else { ?>
						<a title="<?= $currency['title']; ?>"><b><?= $currency['symbol_right']; ?></b></a>
					<? } ?>
				<? } else { ?>
					<? if ($currency['symbol_left']) { ?>
						<a title="<?= $currency['title']; ?>" onclick="$('input[name=\'currency_code\']').attr('value', '<?= $currency['code']; ?>').submit(); $(this).parent().parent().submit();"><?= $currency['symbol_left']; ?></a>
					<? } else { ?>
						<a title="<?= $currency['title']; ?>" onclick="$('input[name=\'currency_code\']').attr('value', '<?= $currency['code']; ?>').submit(); $(this).parent().parent().submit();"><?= $currency['symbol_right']; ?></a>
					<? } ?>
				<? } ?>
			<? } ?>
			<input type="hidden" name="currency_code" value=""/>
			<input type="hidden" name="redirect" value="<?= $redirect; ?>"/>
		</div>
	</form>
<? } ?>
