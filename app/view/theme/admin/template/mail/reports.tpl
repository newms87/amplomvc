<?= call('mail/header', $header); ?>

<p style="margin-top: 0px; margin-bottom: 20px; font-size: 2em">
	<?= _l("%s", $subject); ?>
</p>

<table style="width: 600px" cellpadding="20">

<? foreach ($views as $view) { ?>
	<tr>
		<td style="text-align: center">
			<h3><?= $view['title']; ?></h3><br />
			<img src="<?= cast_http($view['image']); ?>" width="600" height="400" />
			<br /><br />
		</td>
	</tr>
<? } ?>

</table>

<?= call('mail/footer'); ?>
