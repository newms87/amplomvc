<h1><?= $x_response_reason_text; ?></h1>
<? if($x_response_code == '1') { ?>
<p>Your payment was processed successfully. Here is your receipt:</p>
<pre>
<?= $exact_ctr; ?></pre>
<? if(!empty($exact_issname)) { ?>
<p>Issuer: <?= $exact_issname; ?><br/>
	Confirmation Number: <?= $exact_issconf; ?> </p>
<? } ?>
<div class="buttons">
	<table>
		<tr>
			<td align="left"></td>
			<td align="right"><a href="<?= $confirm; ?>" class="button"><?= $button_confirm; ?></a></td>
		</tr>
	</table>
</div>
<? } elseif($_REQUEST['x_response_code'] == '2') { ?>
<p>Your payment failed.	Here is your receipt.</p>
<pre>
<?= $exact_ctr; ?></pre>
<div class="buttons">
	<table>
		<tr>
			<td align="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></td>
			<td align="right"></td>
		</tr>
	</table>
</div>
<? } else { ?>
<p>An error occurred while processing your payment. Please try again later.</p>
<div class="buttons">
	<table>
		<tr>
			<td align="left"><a href="<?= $back; ?>" class="button"><?= $button_back; ?></a></td>
			<td align="right"></td>
		</tr>
	</table>
</div>
<? } ?>
