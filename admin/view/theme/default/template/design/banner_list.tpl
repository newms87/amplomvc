<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'banner.png'; ?>" alt=""/> <?= _l("Banners"); ?></h1>

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
									<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>"><?= _l("Banner Name"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_name; ?>"><?= _l("Banner Name"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'status') { ?>
									<a href="<?= $sort_status; ?>" class="<?= strtolower($order); ?>"><?= _l("Status"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_status; ?>"><?= _l("Status"); ?></a>
								<? } ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($banners) { ?>
							<? foreach ($banners as $banner) { ?>
								<tr>
									<td style="text-align: center;"><? if ($banner['selected']) { ?>
											<input type="checkbox" name="selected[]" value="<?= $banner['banner_id']; ?>"
												checked="checked"/>
										<? } else { ?>
											<input type="checkbox" name="selected[]" value="<?= $banner['banner_id']; ?>"/>
										<? } ?></td>
									<td class="left"><?= $banner['name']; ?></td>
									<td class="left"><?= $banner['status']; ?></td>
									<td class="right"><? foreach ($banner['action'] as $action) { ?>
											[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
										<? } ?></td>
								</tr>
							<? } ?>
						<? } else { ?>
							<tr>
								<td class="center" colspan="4"><?= _l("There are no results to display."); ?></td>
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
