<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'user.png'; ?>" alt=""/> <?= _l("User"); ?></h1>

			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= _l("Insert"); ?></a><a onclick="$('form').submit();" class="button"><?= _l("Delete"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
							</td>
							<td class="left"><? if ($sort == 'username') { ?>
									<a href="<?= $sort_username; ?>" class="<?= strtolower($order); ?>"><?= _l("Username"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_username; ?>"><?= _l("Username"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'email') { ?>
									<a href="<?= $sort_email; ?>" class="<?= strtolower($order); ?>"><?= _l("Email"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_email; ?>"><?= _l("Email"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'status') { ?>
									<a href="<?= $sort_status; ?>" class="<?= strtolower($order); ?>"><?= _l("Status"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_status; ?>"><?= _l("Status"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'date_added') { ?>
									<a href="<?= $sort_date_added; ?>"
										class="<?= strtolower($order); ?>"><?= _l("Date Added"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_date_added; ?>"><?= _l("Date Added"); ?></a>
								<? } ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($users) { ?>
							<? foreach ($users as $user) { ?>
								<tr>
									<td style="text-align: center;"><? if ($user['selected']) { ?>
											<input type="checkbox" name="batch[]" value="<?= $user['user_id']; ?>"
												checked="checked"/>
										<? } else { ?>
											<input type="checkbox" name="batch[]" value="<?= $user['user_id']; ?>"/>
										<? } ?></td>
									<td class="left"><?= $user['username']; ?></td>
									<td class="left"><?= $user['email']; ?></td>
									<td class="left"><?= $user['status']; ?></td>
									<td class="left"><?= $user['date_added']; ?></td>
									<td class="right"><? foreach ($user['action'] as $action) { ?>
											[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
										<? } ?></td>
								</tr>
							<? } ?>
						<? } else { ?>
							<tr>
								<td class="center" colspan="5"><?= _l("There are no results to display."); ?></td>
							</tr>
						<? } ?>
					</tbody>
				</table>
			</form>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<?= _call('common/footer'); ?>
