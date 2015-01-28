<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/backup.png'); ?>" alt=""/> {{Site Management}}</h1>

			<div class="buttons">
				<a href="<?= $return; ?>" class="button">{{Return to Dev Console}}</a>
			</div>
		</div>
		<div class="section">
			<form action="" method="post">
				<table class="form">
					<tr>
						<td><label for="domain">{{Domain}}</label></td>
						<td><input id="domain" type="text" name="domain" value="<?= $domain; ?>" size="100"/></td>
					</tr>
					<tr>
						<td><label for="username">{{Username}}</label></td>
						<td><input id="username" type="text" name="username" value="<?= $username; ?>"/></td>
					</tr>
					<tr>
						<td><label for="status">{{Status}}</label></td>
						<td><?= build(array(
								'type' => 'select',
								'name'  => 'status',
								'data'   => $data_site_status,
								'select' => $status
							)); ?>
						</td>
					</tr>
					<tr>
						<td>
							<input type="submit" class="button" name="add_site" value="{{Add Site}}"/>
						</td>
					</tr>
				</table>
			</form>

			<table class="list">
				<thead>
					<tr>
						<td class="center">{{Domain}}</td>
						<td class="center">{{Username}}</td>
						<td class="center">{{Status}}</td>
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

<?= $is_ajax ? '' : call('admin/footer'); ?>
