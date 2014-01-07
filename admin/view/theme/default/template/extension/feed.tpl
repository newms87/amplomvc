<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<? if ($success) { ?>
			<div class="message_box success"><?= $success; ?></div>
		<? } ?>
		<? if ($error) { ?>
			<div class="message_box warning"><?= $error; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'feed.png'; ?>" alt=""/> <?= _l("Product Feeds"); ?></h1>
			</div>
			<div class="section">
				<table class="list">
					<thead>
					<tr>
						<td class="left"><?= _l("Product Feed Name"); ?></td>
						<td class="left"><?= _l("Status"); ?></td>
						<td class="right"><?= _l("Action"); ?></td>
					</tr>
					</thead>
					<tbody>
					<? if ($extensions) { ?>
						<? foreach ($extensions as $extension) { ?>
							<tr>
								<td class="left"><?= $extension['name']; ?></td>
								<td class="left"><?= $extension['status'] ?></td>
								<td class="right"><? foreach ($extension['action'] as $action) { ?>
										[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
									<? } ?></td>
							</tr>
						<? } ?>
					<? } else { ?>
						<tr>
							<td class="center" colspan="8"><?= _l("No results!"); ?></td>
						</tr>
					<? } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?= $footer; ?>