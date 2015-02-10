<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<? if ($error_warning) { ?>
		<div class="message warning"><?= $error_warning; ?></div>
	<? } ?>
	<? if ($success) { ?>
		<div class="message success"><?= $success; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/country.png'); ?>" alt=""/> {{Zones}}</h1>

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
						<td class="left"><? if ($sort == 'c.name') { ?>
								<a href="<?= $sort_country; ?>"
									class="<?= strtolower($order); ?>">{{Country}}</a>
							<? } else { ?>
								<a href="<?= $sort_country; ?>">{{Country}}</a>
							<? } ?></td>
						<td class="left"><? if ($sort == 'z.name') { ?>
								<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>">{{Zone Name}}</a>
							<? } else { ?>
								<a href="<?= $sort_name; ?>">{{Zone Name}}</a>
							<? } ?></td>
						<td class="left"><? if ($sort == 'z.code') { ?>
								<a href="<?= $sort_code; ?>" class="<?= strtolower($order); ?>">{{Zone Code}}</a>
							<? } else { ?>
								<a href="<?= $sort_code; ?>">{{Zone Code}}</a>
							<? } ?></td>
						<td class="right">{{Action}}</td>
					</tr>
					</thead>
					<tbody>
					<? if ($zones) { ?>
						<? foreach ($zones as $zone) { ?>
							<tr>
								<td style="text-align: center;"><? if ($zone['selected']) { ?>
										<input type="checkbox" name="batch[]" value="<?= $zone['zone_id']; ?>"
											checked="checked"/>
									<? } else { ?>
										<input type="checkbox" name="batch[]" value="<?= $zone['zone_id']; ?>"/>
									<? } ?></td>
								<td class="left"><?= $zone['country']; ?></td>
								<td class="left"><?= $zone['name']; ?></td>
								<td class="left"><?= $zone['code']; ?></td>
								<td class="right"><? foreach ($zone['action'] as $action) { ?>
										[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
									<? } ?></td>
							</tr>
						<? } ?>
					<? } else { ?>
						<tr>
							<td class="center" colspan="5">{{There are no results to display.}}</td>
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
