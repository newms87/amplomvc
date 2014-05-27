<?= call('admin/common/header'); ?>

<div class="section">
	<?= breadcrumbs(); ?>

	<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" class="box">

		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/user-group.png'); ?>" alt=""/> <?= _l("User Group"); ?></h1>

			<div class="buttons">
				<button><?= _l("Save"); ?></button>
				<a href="<?= site_url('admin/user/role'); ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>

		<div class="section">
			<table class="form">
				<tr>
					<td class="required"> <?= _l("User Group Name:"); ?></td>
					<td>
						<input type="text" name="name" value="<?= $name; ?>"/>
					</td>
				</tr>
				<tr>
					<td><?= _l("Access Permission:"); ?></td>
					<td>
						<? $build = array(
							'name'   => 'permissions[access]',
							'data'   => $data_controllers,
							'select' => $permissions['access'],
						); ?>

						<?= build("multiselect", $build); ?>
					</td>
				</tr>
				<tr>
					<td><?= _l("Modify Permission:"); ?></td>
					<td>
						<? $build = array(
							'name'   => 'permissions[modify]',
							'data'   => $data_controllers,
							'select' => $permissions['modify'],
						); ?>

						<?= build("multiselect", $build); ?>
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
