<?= $header; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'backup.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a href="<?= $return; ?>" class="button"><?= $button_return; ?></a>
				</div>
			</div>
			<div class="content">
				<form action='' method="post">
					<table class="form">
						<tr>
							<td><label for="domain"><?= $entry_domain; ?></label></td>
							<td><input id="domain" type="text" name="domain" value="<?= $domain; ?>" size="100"/></td>
						</tr>
						<tr>
							<td><label for="username"><?= $entry_username; ?></label></td>
							<td><input id="username" type="text" name="username" value="<?= $username; ?>"/></td>
						</tr>
						<tr>
							<td><label for="status"><?= $entry_status; ?></label></td>
							<td>
								<?= $this->builder->build('select', $data_site_status, 'status', $status); ?>
							</td>
						</tr>
						<tr>
							<td>
								<input type="submit" class="button" name="add_site" value="<?= $button_add_site; ?>"/>
							</td>
						</tr>
					</table>
				</form>

				<table class="list">
					<thead>
					<tr>
						<td class="center"><?= $column_domain; ?></td>
						<td class="center"><?= $column_username; ?></td>
						<td class="center"><?= $column_status; ?></td>
						<td></td>
					</tr>
					</thead>
					<? foreach ($dev_sites as $site) { ?>
						<tr>
							<td class="center"><?= $site['domain']; ?></td>
							<td class="center"><?= $site['username']; ?></td>
							<td class="center"><?= $data_site_status[$site['status']]; ?></td>
							<td class="center">
								<form action='' method='post'>
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

<?= $footer; ?>