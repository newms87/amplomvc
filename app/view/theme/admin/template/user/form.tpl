<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" class="box">
		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/user.png'); ?>" alt=""/> {{User}}</h1>

			<div class="buttons">
				<button>{{Save}}</button>
				<a href="<?= site_url('admin/user'); ?>" class="button">{{Cancel}}</a>
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
							<input type="text" name="username" value="<?= $username; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{First Name:}}</td>
						<td>
							<input type="text" name="firstname" value="<?= $firstname; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Last Name:}}</td>
						<td>
							<input type="text" name="lastname" value="<?= $lastname; ?>"/>
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
						<td>{{Password:}}</td>
						<td>
							<div class="enter-password">
								<input type="password" autocomplete="off" data-name="password" placeholder="{{New Password}}" /><br />
								<input type="password" autocomplete="off" name="confirm" placeholder="{{Confirm Password}}" />
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

<?= $is_ajax ? '' : call('admin/footer'); ?>
