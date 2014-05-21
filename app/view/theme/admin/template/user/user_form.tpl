<?= call('admin/common/header'); ?>

<div class="section">
	<?= breadcrumbs(); ?>

	<div class="box">
		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
			<div class="heading">
				<h1>
					<img src="<?= theme_url('image/user.png'); ?>" alt=""/> <?= _l("User"); ?></h1>

				<div class="buttons">
					<button><?= _l("Save"); ?></button>
					<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
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
							<? if ($this->user->isTopAdmin()) { ?>
								<? $this->builder->setConfig('user_group_id', 'name'); ?>
								<?= $this->builder->build('select', $user_groups, "user_group_id", (int)$user_group_id); ?>
							<? } else { ?>
								<? foreach ($user_groups as $ug) {
									if ($ug['user_group_id'] == $user_group_id) {
										?>
										<input type="hidden" name="user_group_id" value="<?= $user_group_id; ?>"/>
										<div><?= $ug['name']; ?></div>
									<?
									}
								} ?>
							<? } ?>
						</td>
					</tr>
					<tr>
						<td><?= _l("Password:<span class=\"help\">Leave blank to keep same password.</span>"); ?></td>
						<td>
							<input type="password" autocomplete="off" name="password" value="<?= $password; ?>"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("Confirm:"); ?></td>
						<td>
							<input type="password" autocomplete="off" name="confirm" value="<?= $confirm; ?>"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("Status:"); ?></td>
						<td><?= $this->builder->build('select', $data_statuses, 'status', (int)$status); ?></td>
					</tr>
				</table>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= call('admin/common/footer'); ?>