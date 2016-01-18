<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<form action="<?= site_url('admin/settings/url-alias/save', 'url_alias_id=' . $url_alias_id); ?>" class="box form ctrl-save" method="post" enctype="multipart/form-data">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<button>{{Save}}</button>
			</div>
		</div>

		<div class="section">
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
								'select' => $status,
							)); ?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</form>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
