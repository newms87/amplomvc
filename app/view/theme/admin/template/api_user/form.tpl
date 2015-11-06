<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form action="<?= site_url('admin/api_user/save', 'api_user_id=' . $api_user_id); ?>" method="post" enctype="multipart/form-data" class="box ctrl-save">
		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/user.png'); ?>" alt=""/>
				{{API User}}
			</h1>

			<div class="buttons">
				<button data-loading="{{Saving...}}">{{Save}}</button>
				<a href="<?= site_url('admin/api_user'); ?>" class="button cancel">{{Cancel}}</a>
				<? if ($api_user_id) { ?>
				<a href="<?= site_url('admin/api_user/remove', 'api_user_id=' . $api_user_id); ?>" class="button remove" data-confirm="{{Confirm Delete}}" data-confirm-modal="{{Are you sure you want to delete this API User?}}">{{Delete}}</a>
				<? } ?>
			</div>
		</div>

		<div class="section">
			<div id="tab-general">
				<table class="form">
					<tr>
						<td>{{Status:}}</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'status',
								'data'   => array(
									0 => '{{Deactivated}}',
									1 => '{{Active}}',
								),
								'select' => $status,
							)); ?>
						</td>
					</tr>
					<? if (!empty($data_users)) { ?>
						<tr>
							<td class="required"> {{User:}}</td>
							<td>
								<?= build(array(
									'type'   => 'select',
									'name'   => 'user_id',
									'select' => $user_id,
									'data'   => $data_users,
									'value'  => 'user_id',
									'label'  => 'username',
								)); ?>
							</td>
						</tr>
					<? } ?>
					<tr>
						<td class="required"> {{Username:}}</td>
						<td>
							<input type="text" name="username" value="<?= $username; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{User Group:}}</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'user_role_id',
								'data'   => $data_user_roles,
								'select' => $user_role_id,
								'value'  => 'user_role_id',
								'label'  => 'name',
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{API Key}}</td>
						<td><?= $api_key; ?></td>
					</tr>
					<tr>
						<td>{{Public Key}}</td>
						<td><?= $public_key; ?></td>
					</tr>
					<tr>
						<td>{{Private Key}}</td>
						<td><?= $private_key; ?></td>
					</tr>
				</table>
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
