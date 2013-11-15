<div class="cart-total">
	<table id="total">
		<? foreach ($totals as $total) { ?>
			<tr>
				<td class="total_title"><b><?= $total['title']; ?>:</b></td>
				<td class="total_text"><?= $total['text']; ?></td>
			</tr>
		<? } ?>
	</table>
</div>
