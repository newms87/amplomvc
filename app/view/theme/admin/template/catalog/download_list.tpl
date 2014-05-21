<?= call('admin/common/header'); ?>
<div class="section">
	<?= breadcrumbs(); ?>
	<? if ($error_warning) { ?>
		<div class="message warning"><?= $error_warning; ?></div>
	<? } ?>
	<? if ($success) { ?>
		<div class="message success"><?= $success; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/download.png'); ?>" alt=""/> <?= _l("Downloads"); ?></h1>

			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= _l("Insert"); ?></a><a onclick="$('form').submit();" class="button"><?= _l("Delete"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
							</td>
							<td class="left"><? if ($sort == 'dd.name') { ?>
									<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= _l("Download Name"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_name; ?>"><?= _l("Download Name"); ?></a>
								<? } ?></td>
							<td class="right"><? if ($sort == 'd.remaining') { ?>
									<a href="<?= $sort_remaining; ?>"
										class="<?= strtolower($order); ?>"><?= _l("Total Downloads Allowed"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_remaining; ?>"><?= _l("Total Downloads Allowed"); ?></a>
								<? } ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($downloads) { ?>
							<? foreach ($downloads as $download) { ?>
								<tr>
									<td style="text-align: center;"><? if ($download['selected']) { ?>
											<input type="checkbox" name="batch[]" value="<?= $download['download_id']; ?>"
												checked="checked"/>
										<? } else { ?>
											<input type="checkbox" name="batch[]" value="<?= $download['download_id']; ?>"/>
										<? } ?></td>
									<td class="left"><?= $download['name']; ?></td>
									<td class="right"><?= $download['remaining']; ?></td>
									<td class="right"><? foreach ($download['action'] as $action) { ?>
											[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
										<? } ?></td>
								</tr>
							<? } ?>
						<? } else { ?>
							<tr>
								<td class="center" colspan="6"><?= _l("There are no downloads to list"); ?></td>
							</tr>
						<? } ?>
					</tbody>
				</table>
			</form>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<?= call('admin/common/footer'); ?>
