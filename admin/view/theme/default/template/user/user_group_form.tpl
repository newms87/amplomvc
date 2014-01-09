<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'user-group.png'; ?>" alt=""/> <?= _l("User Group"); ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
						href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("User Group Name:"); ?></td>
							<td><input type="text" name="name" value="<?= $name; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Access Permission:"); ?></td>
							<td><?= $this->builder->build('multiselect', $data_controllers, "permissions[access]", $permissions['access']); ?></td>
						</tr>
						<tr>
							<td><?= _l("Modify Permission:"); ?></td>
							<td><?= $this->builder->build('multiselect', $data_controllers, "permissions[modify]", $permissions['modify']); ?></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
<?= $footer; ?>

<?= $this->builder->js('errors', $errors); ?>