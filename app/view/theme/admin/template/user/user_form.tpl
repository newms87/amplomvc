<?= call('admin/common/header'); ?>

	<div class="section">
		<?= breadcrumbs(); ?>

		<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" class="box">
			<div class="heading">
				<h1>
					<img src="<?= theme_url('image/user.png'); ?>" alt=""/> <?= _l("User"); ?></h1>

				<div class="buttons">
					<button><?= _l("Save"); ?></button>
					<a href="<?= site_url('admin/user/user'); ?>" class="button"><?= _l("Cancel"); ?></a>
				</div>
			</div>

			<div class="section">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Username:"); ?></td>
						<td>
							<input type="text" name="username" value="<?= $username; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required"> <?= _l("First Name:"); ?></td>
						<td>
							<input type="text" name="firstname" value="<?= $firstname; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Last Name:"); ?></td>
						<td>
							<input type="text" name="lastname" value="<?= $lastname; ?>"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("E-Mail:"); ?></td>
						<td>
							<input type="text" name="email" value="<?= $email; ?>"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("User Group:"); ?></td>
						<td>
							<? $build = array(
								'name'   => 'user_role_id',
								'data'   => $data_user_roles,
								'select' => $user_role_id,
								'key'    => 'user_role_id',
								'value'  => 'name',
							); ?>

							<?= build('select', $build); ?>
						</td>
					</tr>
					<tr>
						<td><?= _l("Password:<span class=\"help\">Leave blank to keep same password.</span>"); ?></td>
						<td>
							<input type="password" autocomplete="off" name="password" value=""/>
						</td>
					</tr>
					<tr>
						<td><?= _l("Confirm:"); ?></td>
						<td>
							<input type="password" autocomplete="off" name="confirm" value=""/>
						</td>
					</tr>
					<tr>
						<td><?= _l("Status:"); ?></td>
						<td>
							<? $build = array(
								'name'   => 'status',
								'data'   => $data_statuses,
								'select' => $status,
							); ?>

							<?= build('select', $build); ?>
						</td>
					</tr>
				</table>
			</div>
		</form>
	</div>

	<script type="text/javascript">
		$.ac_errors(<?= json_encode($errors); ?>);
	</script>

<?= call('admin/common/footer'); ?>