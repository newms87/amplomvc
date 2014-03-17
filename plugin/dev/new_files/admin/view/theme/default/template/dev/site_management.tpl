<?= $common_header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'backup.png'; ?>" alt=""/> <?= _l("Site Management"); ?></h1>

			<div class="buttons">
				<a href="<?= $return; ?>" class="button"><?= _l("Return to Dev Console"); ?></a>
			</div>
		</div>
		<div class="section">
			<form action="" method="post">
				<table class="form">
					<tr>
						<td><label for="domain"><?= _l("Domain"); ?></label></td>
						<td><input id="domain" type="text" name="domain" value="<?= $domain; ?>" size="100"/></td>
					</tr>
					<tr>
						<td><label for="username"><?= _l("Username"); ?></label></td>
						<td><input id="username" type="text" name="username" value="<?= $username; ?>"/></td>
					</tr>
					<tr>
						<td><label for="status"><?= _l("Status"); ?></label></td>
						<td>
							<?= $this->builder->build('select', $data_site_status, 'status', $status); ?>
						</td>
					</tr>
					<tr>
						<td>
							<input type="submit" class="button" name="add_site" value="<?= _l("Add Site"); ?>"/>
						</td>
					</tr>
				</table>
			</form>

			<table class="list">
				<thead>
					<tr>
						<td class="center"><?= _l("Domain"); ?></td>
						<td class="center"><?= _l("Username"); ?></td>
						<td class="center"><?= _l("Status"); ?></td>
						<td></td>
					</tr>
				</thead>
				<? foreach ($dev_sites as $site) { ?>
					<tr>
						<td class="center"><?= $site['domain']; ?></td>
						<td class="center"><?= $site['username']; ?></td>
						<td class="center"><?= $data_site_status[$site['status']]; ?></td>
						<td class="center">
							<form action="" method="post">
								<input type="hidden" name="domain" value="<?= $site['domain']; ?>"/>
								<input type="submit" name="delete_site" class="button" value="Delete"/>
							</form>
						</td>
					</tr>
				<? } ?>
			</table>
		</div>
	</div>
</div>

<?= $common_footer; ?>
