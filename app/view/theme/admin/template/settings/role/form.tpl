<?= $is_ajax ? '' : call('admin/header'); ?>

<div id="admin-permissions" class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form action="<?= site_url('admin/settings/role/save', 'user_role_id=' . $user_role_id); ?>" method="post" enctype="multipart/form-data" class="box ctrl-save">

		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/user-group.png'); ?>" alt=""/> {{User Group}}</h1>

			<div class="buttons">
				<button>{{Save}}</button>
				<a href="<?= site_url('admin/settings/role'); ?>" class="button">{{Cancel}}</a>
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

		<div class="section form-section">
			<div class="row ug-name">
				<label for="ug-name" class="col xs-3 md-2 left">{{Group Name:}}</label>

				<div class="col xs-9 md-10 left value">
					<input id="ug-name" type="text" name="name" value="<?= $name; ?>"/>
				</div>
			</div>

			<div class="row ug-type">
				<label for="ug-type" class="col xs-3 md-2 left">{{Group Type:}}</label>

				<div class="col xs-9 md-10 left value">
					<?= build(array(
						'type'   => 'select',
						'name'   => 'type',
						'data'   => App_Model_UserRole::$types,
						'select' => $type,
					)); ?>
				</div>
			</div>

			<div class="row ug-level">
				<label for="ug-level" class="col xs-3 md-2 left">{{Group Level:}}</label>

				<div class="col xs-9 md-10 left value">
					<input id="ug-level" type="text" name="level" value="<?= $level; ?>"/>
				</div>
			</div>

			<div class="row ug-permissions">
				<label for="ug-permissions" class="col xs-3 md-2 left">{{Permissions:}}</label>

				<div class="col xs-9 md-10 left value">
					<div class="builder-multiselect tall user-permissions">
						<? recursive_multiselect($data_areas, 'permissions', $data_perms); ?>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
	$('.multiselect-list .expand').click(function (event) {
		$(this).closest('li').toggleClass('expanded');
		event.preventDefault();
		return false;
	});
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
