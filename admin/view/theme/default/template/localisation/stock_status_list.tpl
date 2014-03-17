<?= $common_header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
		<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<? if ($success) { ?>
		<div class="message_box success"><?= $success; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'stock-status.png'; ?>" alt=""/> <?= _l("Stock Status"); ?></h1>

			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= _l("Insert"); ?></a><a onclick="$('form').submit();" class="button"><?= _l("Delete"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
							</td>
							<td class="left"><? if ($sort == 'name') { ?>
									<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= _l("Stock Status Name"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_name; ?>"><?= _l("Stock Status Name"); ?></a>
								<? } ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($stock_statuses) { ?>
							<? foreach ($stock_statuses as $stock_status) { ?>
								<tr>
									<td style="text-align: center;"><? if ($stock_status['selected']) { ?>
											<input type="checkbox" name="selected[]" value="<?= $stock_status['stock_status_id']; ?>" checked="checked"/>
										<? } else { ?>
											<input type="checkbox" name="selected[]" value="<?= $stock_status['stock_status_id']; ?>"/>
										<? } ?></td>
									<td class="left"><?= $stock_status['name']; ?></td>
									<td class="right"><? foreach ($stock_status['action'] as $action) { ?>
											[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
										<? } ?></td>
								</tr>
							<? } ?>
						<? } else { ?>
							<tr>
								<td class="center" colspan="3"><?= _l("There are no results to display."); ?></td>
							</tr>
						<? } ?>
					</tbody>
				</table>
			</form>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<?= $common_footer; ?>
