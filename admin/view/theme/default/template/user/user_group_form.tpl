<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'user-group.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_name; ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_access; ?></td>
				<td><?= $this->builder->build('multiselect', $data_controllers, "permissions[access]", $permissions['access']); ?></td>
					</tr>
					<tr>
						<td><?= $entry_modify; ?></td>
						<td><?= $this->builder->build('multiselect', $data_controllers, "permissions[modify]", $permissions['modify']); ?></td>
			</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>

<?= $this->builder->js('errors',$errors); ?>