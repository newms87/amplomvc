<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{URL Aliases}}</h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button">{{Save}}</a><a
					href="<?= $cancel; ?>" class="button">{{Cancel}}</a></div>
		</div>
		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> {{URL Alias:}}</td>
							<td><input type="text" name="alias" value="<?= $alias; ?>" size="40"/></td>
						</tr>
						<tr>
							<td class="required"> {{Path:}}</td>
							<td><input type="text" name="path" value="<?= $path; ?>" size="40"/></td>
						</tr>
						<tr>
							<td class="required"> {{Query:}}</td>
							<td><input type="text" name="query" value="<?= $query; ?>" size="40"/></td>
						</tr>
						<tr>
							<td class="required"> {{Redirect:}}</td>
							<td><input type="text" name="redirect" value="<?= $redirect; ?>" size="40"/></td>
						</tr>
						<tr>
							<td>{{Status:}}</td>
							<td><?= build(array(
									'type'   => 'select',
									'name'   => 'status',
									'data'   => $data_statuses,
									'select' => $status
								)); ?></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
