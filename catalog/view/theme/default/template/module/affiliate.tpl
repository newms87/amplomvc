<div class="box">
	<div class="box_heading"><?= $head_title; ?></div>
	<div class="box_content">
		<ul>
			<? if (!$logged) { ?>
				<li><a href="<?= $login; ?>"><?= $text_login; ?></a> / <a
						href="<?= $register; ?>"><?= $text_register; ?></a></li>
				<li><a href="<?= $forgotten; ?>"><?= $text_forgotten; ?></a></li>
			<? } ?>
			<li><a href="<?= $account; ?>"><?= $text_account; ?></a></li>
			<? if ($logged) { ?>
				<li><a href="<?= $edit; ?>"><?= $text_edit; ?></a></li>
				<li><a href="<?= $password; ?>"><?= $text_password; ?></a></li>
			<? } ?>
			<li><a href="<?= $payment; ?>"><?= $text_payment; ?></a></li>
			<li><a href="<?= $tracking; ?>"><?= $text_tracking; ?></a></li>
			<li><a href="<?= $transaction; ?>"><?= $text_transaction; ?></a></li>
			<? if ($logged) { ?>
				<li><a href="<?= $logout; ?>"><?= $text_logout; ?></a></li>
			<? } ?>
		</ul>
	</div>
</div>
