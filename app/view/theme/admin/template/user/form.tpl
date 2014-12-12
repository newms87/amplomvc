<?= IS_AJAX ? '' : call('admin/header'); ?>

<div class="section">
	<?= IS_AJAX ? '' : breadcrumbs(); ?>

	<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" class="box">
		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/user.png'); ?>" alt=""/> <?= _l("User"); ?></h1>

			<div class="buttons">
				<button><?= _l("Save"); ?></button>
				<a href="<?= site_url('admin/user'); ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>

		<div class="section">
			<div class="user-tabs htabs">
				<a href="#tab-general"><?= _l("General"); ?></a>
				<a href="#tab-meta"><?= _l("Meta"); ?></a>
			</div>

			<div id="tab-general">
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
					<tr class="password">
						<td><?= _l("Password:"); ?></td>
						<td>
							<div class="enter-password">
								<input type="password" autocomplete="off" data-name="password" placeholder="<?= _l("New Password"); ?>" /><br />
								<input type="password" autocomplete="off" name="confirm" placeholder="<?= _l("Confirm Password"); ?>" />
							</div>
							<a class="change-password">
								<span class="change"><?= _l("Change Password"); ?></span>
								<span class="cancel"><?= _l("Cancel"); ?></span>
							</a>
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

			<div id="tab-meta">
				<input type="hidden" name="meta_exactly" value="1"/>

				<table class="meta-table form">
					<? foreach ($meta as $row => $m) { ?>
					<tr class="meta-row" data-row="<?= $row; ?>">
						<td>
							<div class="meta-key" contenteditable><?= $row; ?></div>
						</td>
						<td>
							<? if (_is_object($m)) { ?>
								<? if (isset($m['build'])){ ?>
									<?= build($m['build']); ?>
								<? } else { ?>
									<?= _l("(Object cannot be edited)"); ?>
								<? } ?>
							<? } else { ?>
								<input class="meta-value" type="text" name="meta[<?= $row; ?>]" value="<?= $m; ?>"/>
							<? } ?>
						</td>
						<td>
							<a class="button remove"><?= _l("X"); ?></a>
						</td>
					</tr>
					<? } ?>
				</table>

				<div class="add-meta-button">
					<a class="add-meta button"><?= _l("Add Meta"); ?></a>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
	$('.change-password').click(function() {
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

	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= IS_AJAX ? '' : call('admin/footer'); ?>
