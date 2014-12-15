<?= $is_ajax ? '' : call('admin/header'); ?>

<div id="admin-permissions" class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form action="<?= site_url('admin/setting/role/save', 'user_role_id=' . $user_role_id); ?>" method="post" enctype="multipart/form-data" class="box ctrl-save">

		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/user-group.png'); ?>" alt=""/> {{User Group}}</h1>

			<div class="buttons">
				<button>{{Save}}</button>
				<a href="<?= site_url('admin/setting/role'); ?>" class="button">{{Cancel}}</a>
			</div>
		</div>

		<? function recursive_multiselect($data, $name = 'permissions', $data_perms)
		{ ?>
			<ul class="multiselect-list">
				<? foreach ($data as $key => $value) {
					if ($key === '*') {
						continue;
					}

					$id = 'cb-perms-access-' . slug($name . '-' . $key); ?>

					<li>
						<input id="<?= $id; ?>" type="checkbox" name="<?= $name . "[$key][*]"; ?>" <?= $value['*'] ? 'checked' : ''; ?> data-multistate="r;w" value="<?= $value['*'] === 'w' ? 'w' : 'r'; ?>">
						<label for="<?= $id; ?>">
							<? if (count($value) > 1) { ?>
							<a class="expand"></a>
							<? } ?>
							<span class="permissions">
								<? foreach ($data_perms as $p => $perm) { ?>
									<span class="perm <?= $p; ?>"><?= $perm; ?></span>
								<? } ?>
							</span>
							<span class="title"><?= $key; ?></span>
						</label>

						<? if (count($value) > 1) {
							recursive_multiselect($value, $name . "[$key]", $data_perms);
						} ?>
					</li>
				<? } ?>
			</ul>
		<? } ?>

		<div class="section">
			<table class="form">
				<tr>
					<td class="required"> {{User Group Name:}}</td>
					<td>
						<input type="text" name="name" value="<?= $name; ?>"/>
					</td>
				</tr>
				<tr>
					<td>{{User Permissions:}}</td>
					<td>
						<div class="builder-multiselect tall user-permissions">
							<? recursive_multiselect($data_areas, 'permissions', $data_perms); ?>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>

<script type="text/javascript">
	$('.multiselect-list .expand').click(function (event) {
		$(this).closest('li').toggleClass('expanded');
		event.preventDefault();
		return false;
	});
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
