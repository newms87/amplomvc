<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<form action="<?= site_url('admin/api_user/save', 'api_user_id=' . $record_id); ?>" method="post" enctype="multipart/form-data" class="box ctrl-save">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<? if ($record_id) { ?>
					<button>{{Save}}</button>
					<a href="<?= site_url('admin/api_user/remove', 'api_user_id=' . $record_id); ?>" class="button remove" data-confirm="{{Confirm Delete}}" data-confirm-modal="{{Are you sure you want to delete this API User?}}">{{Delete}}</a>
				<? } else { ?>
					<button>{{Create}}</button>
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
								'select' => $record['status'],
							)); ?>
						</td>
					</tr>
					<? if (!empty($data['users'])) { ?>
						<tr>
							<td class="required"> {{User:}}</td>
							<td>
								<?= build(array(
									'type'   => 'select',
									'name'   => 'user_id',
									'select' => $record['user_id'],
									'data'   => $data['users'],
									'value'  => 'user_id',
									'label'  => 'username',
								)); ?>
							</td>
						</tr>
					<? } ?>
					<tr>
						<td class="required"> {{Username:}}</td>
						<td>
							<input type="text" name="username" value="<?= $record['username']; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{User Group:}}</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'user_role_id',
								'data'   => $data['user_roles'],
								'select' => $record['user_role_id'],
								'value'  => 'user_role_id',
								'label'  => 'name',
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{API Key}}</td>
						<td><?= $record['api_key']; ?></td>
					</tr>
					<tr>
						<td>{{Public Key}}</td>
						<td><?= $record['public_key']; ?></td>
					</tr>
					<tr>
						<td>{{Private Key}}</td>
						<td><?= $record['private_key']; ?></td>
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
