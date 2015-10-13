<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<form action="<?= site_url('admin/settings/url-alias/save', 'url_alias_id=' . $url_alias_id); ?>" class="box form ctrl-save" method="post" enctype="multipart/form-data">
		<div class="heading">
			<h1>
				<?= img(theme_dir('settings/alias.png')); ?>
				{{URL Aliases}}
			</h1>

			<div class="buttons">
				<button data-loading="{{Saving...}}">{{Save}}</button>
				<a href="<?= site_url('admin/settings/url-alias'); ?>" class="button cancel">{{Cancel}}</a>
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
