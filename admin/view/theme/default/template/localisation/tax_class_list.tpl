<?= $header; ?>
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
				<h1><img src="<?= HTTP_THEME_IMAGE . 'tax.png'; ?>" alt=""/> <?= _l("Tax Class"); ?></h1>

				<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= _l("Delete"); ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="list">
						<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
							</td>
							<td class="left"><? if ($sort == 'title') { ?>
									<a href="<?= $sort_title; ?>" class="<?= strtolower($order); ?>"><?= _l("Tax Class Title"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_title; ?>"><?= _l("Tax Class Title"); ?></a>
								<? } ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
						</thead>
						<tbody>
						<? if ($tax_classes) { ?>
							<? foreach ($tax_classes as $tax_class) { ?>
								<tr>
									<td style="text-align: center;"><? if ($tax_class['selected']) { ?>
											<input type="checkbox" name="selected[]" value="<?= $tax_class['tax_class_id']; ?>"
											       checked="checked"/>
										<? } else { ?>
											<input type="checkbox" name="selected[]" value="<?= $tax_class['tax_class_id']; ?>"/>
										<? } ?></td>
									<td class="left"><?= $tax_class['title']; ?></td>
									<td class="right"><? foreach ($tax_class['action'] as $action) { ?>
											[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
										<? } ?></td>
								</tr>
							<? } ?>
						<? } else { ?>
							<tr>
								<td class="center" colspan="3"><?= $text_no_results; ?></td>
							</tr>
						<? } ?>
						</tbody>
					</table>
				</form>
				<div class="pagination"><?= $pagination; ?></div>
			</div>
		</div>
	</div>
<?= $footer; ?>