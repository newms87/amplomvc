<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section user-account">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form action="<?= $user_id === user_info('user_id') ? site_url('admin/user/save-my-account') : site_url('admin/user/save', 'user_id=' . $user_id); ?>" method="post" enctype="multipart/form-data" class="box ctrl-save">
		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/user.png'); ?>" alt=""/>
				{{User}}
			</h1>

			<div class="buttons">
				<button>{{Save}}</button>
				<a href="<?= site_url('admin/user'); ?>" class="button cancel">{{Cancel}}</a>
			</div>
		</div>

		<div class="section">
			<div class="user-tabs htabs">
				<a href="#tab-general">{{General}}</a>
				<a href="#tab-meta">{{Meta}}</a>
			</div>

			<div id="tab-general">
				<table class="form">
					<tr>
						<td class="required"> {{Username:}}</td>
						<td>
							<? if (user_can('w', 'admin/user/form')) { ?>
								<input type="text" name="username" value="<?= $username; ?>"/>
							<? } else { ?>
								<span class="username-readonly"><?= $username; ?></span>
							<? } ?>
						</td>
					</tr>
					<tr>
						<td class="required"> {{First Name:}}</td>
						<td>
							<input type="text" name="first_name" value="<?= $first_name; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Last Name:}}</td>
						<td>
							<input type="text" name="last_name" value="<?= $last_name; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{E-Mail:}}</td>
						<td>
							<input type="text" name="email" value="<?= $email; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{User Group:}}</td>
						<td>
							<? if (user_can('w', 'admin/user/form')) { ?>
								<?= build(array(
									'type'   => 'select',
									'name'   => 'user_role_id',
									'data'   => $data_user_roles,
									'select' => $user_role_id,
									'value'  => 'user_role_id',
									'label'  => 'name',
								)); ?>
							<? } else { ?>
								<span class="user-role-id-readonly"><?= isset($data_user_roles[$user_role_id]) ? $data_user_roles[$user_role_id]['name'] : '{{(No Role Assigned)}}'; ?></span>
							<? } ?>
						</td>
					</tr>
					<tr class="password">
						<td>{{Password:}}</td>
						<td>
							<div class="enter-password">
								<input type="password" autocomplete="off" data-name="password" placeholder="{{New Password}}"/>
								<br/>
								<input type="password" autocomplete="off" name="confirm" placeholder="{{Confirm Password}}"/>
							</div>
							<a class="change-password">
								<span class="change">{{Change Password}}</span>
								<span class="cancel">{{Cancel}}</span>
							</a>
						</td>
					</tr>
					<tr>
						<td>{{Status:}}</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'status',
								'data'   => $data_statuses,
								'select' => $status,
							)); ?>
						</td>
					</tr>
				</table>
			</div>

			<div id="tab-meta">
				<table class="meta-table form">
					<? foreach ($meta as $row => $m) { ?>
						<tr class="meta-row" data-row="<?= $row; ?>">
							<td>
								<div class="meta-key" contenteditable><?= $row; ?></div>
							</td>
							<td>
								<? if (_is_object($m)) { ?>
									<? if (isset($m['build'])) { ?>
										<?= build($m['build']); ?>
									<? } else { ?>
										{{(Object cannot be edited)}}
									<? } ?>
								<? } else { ?>
									<input class="meta-value" type="text" name="meta[<?= $row; ?>]" value="<?= $m; ?>"/>
								<? } ?>
							</td>
							<td>
								<a class="button remove">{{X}}</a>
							</td>
						</tr>
					<? } ?>
				</table>

				<div class="add-meta-button">
					<a class="add-meta button">{{Add Meta}}</a>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
	$('.change-password').click(function () {
		$('tr.password').toggleClass('edit');

		if ($('tr.password').hasClass('edit')) {
			$('input[data-name=password]').attr('name', 'password');
		} else {
			$('input[data-name=password]').removeAttr('name', '');
		}
	});

	$('.meta-key').keyup(function () {
		var $this = $(this);
		$this.closest('.meta-row').find('.meta-value').attr('name', 'meta[' + $this.html() + ']');
	});

	$('#tab-meta .remove').click(function () {
		$(this).closest('.meta-row').remove();
	});

	$("#tab-meta .meta-table").ac_template('meta-table');

	$('.add-meta').click(function () {
		$.ac_template('meta-table', 'add');
	});

	$('.user-tabs a').tabs();


</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
