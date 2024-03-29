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

			<h1><img src="<?= theme_url('image/country.png'); ?>" alt=""/> {{Country}}</h1>

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
								<a href="<?= $sort_name; ?>" class="<?= strtolower($order); ?>">{{Country Name}}</a>
							<? } else { ?>
								<a href="<?= $sort_name; ?>">{{Country Name}}</a>
							<? } ?></td>
						<td class="left"><? if ($sort == 'iso_code_2') { ?>
								<a href="<?= $sort_iso_code_2; ?>"
									class="<?= strtolower($order); ?>">{{ISO Code (2)}}</a>
							<? } else { ?>
								<a href="<?= $sort_iso_code_2; ?>">{{ISO Code (2)}}</a>
							<? } ?></td>
						<td class="left"><? if ($sort == 'iso_code_3') { ?>
								<a href="<?= $sort_iso_code_3; ?>"
									class="<?= strtolower($order); ?>">{{ISO Code (3)}}</a>
							<? } else { ?>
								<a href="<?= $sort_iso_code_3; ?>">{{ISO Code (3)}}</a>
							<? } ?></td>
						<td class="right">{{Action}}</td>
					</tr>
					</thead>
					<tbody>
					<? if ($countries) { ?>
						<? foreach ($countries as $country) { ?>
							<tr>
								<td style="text-align: center;"><? if ($country['selected']) { ?>
										<input type="checkbox" name="batch[]" value="<?= $country['country_id']; ?>"
											checked="checked"/>
									<? } else { ?>
										<input type="checkbox" name="batch[]" value="<?= $country['country_id']; ?>"/>
									<? } ?></td>
								<td class="left"><?= $country['name']; ?></td>
								<td class="left"><?= $country['iso_code_2']; ?></td>
								<td class="left"><?= $country['iso_code_3']; ?></td>
								<td class="right"><? foreach ($country['action'] as $action) { ?>
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
