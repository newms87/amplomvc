<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<? if ($error_warning) { ?>
		<div class="message warning"><?= $error_warning; ?></div>
	<? } ?>
	<? if ($success) { ?>
		<div class="message success"><?= $success; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<h1><img src="<?= theme_url('image/country.png'); ?>" alt=""/> {{Geo Zones}}</h1>

			<div class="buttons">
				<a onclick="location = '<?= $insert; ?>'" class="button">{{Insert}}</a><a onclick="$('form').submit();" class="button">{{Delete}}</a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
					<tr>
						<td width="1" style="text-align: center;">
							<input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
						</td>
						<td class="left"><? if ($sort == 'name') { ?>
								<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>">{{Geo Zone Name}}</a>
							<? } else { ?>
								<a href="<?= $sort_name; ?>">{{Geo Zone Name}}</a>
							<? } ?></td>
						<td class="left"><? if ($sort == 'description') { ?>
								<a href="<?= $sort_description; ?>"
									class="<?= strtolower($order); ?>">{{Description}}</a>
							<? } else { ?>
								<a href="<?= $sort_description; ?>">{{Description}}</a>
							<? } ?></td>
						<td class="right">{{Action}}</td>
					</tr>
					</thead>
					<tbody>
					<? if ($geo_zones) { ?>
						<? foreach ($geo_zones as $geo_zone) { ?>
							<tr>
								<td style="text-align: center;"><? if ($geo_zone['selected']) { ?>
										<input type="checkbox" name="batch[]" value="<?= $geo_zone['geo_zone_id']; ?>"
											checked="checked"/>
									<? } else { ?>
										<input type="checkbox" name="batch[]" value="<?= $geo_zone['geo_zone_id']; ?>"/>
									<? } ?></td>
								<td class="left"><?= $geo_zone['name']; ?></td>
								<td class="left"><?= $geo_zone['description']; ?></td>
								<td class="right"><? foreach ($geo_zone['action'] as $action) { ?>
										[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
									<? } ?></td>
							</tr>
						<? } ?>
					<? } else { ?>
						<tr>
							<td class="center" colspan="4">{{There are no results to display.}}</td>
						</tr>
					<? } ?>
					</tbody>
				</table>
			</form>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<?= $is_ajax ? '' : call('admin/footer'); ?>
