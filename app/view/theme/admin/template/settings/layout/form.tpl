<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<button>{{Save}}</button>
			</div>
		</div>
		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> {{Layout Name:}}</td>
						<td><input type="text" name="name" value="<?= $name; ?>"/></td>
					</tr>
				</table>
				<table id="route" class="list">
					<thead>
					<tr>
						<td class="left">{{Route:}}</td>
						<td></td>
					</tr>
					</thead>

					<tbody id="route_list">
					<? foreach ($routes as $row => $route) { ?>
						<tr class="route" data-row="<?= $row; ?>">
							<td class="left">
								<input type="text" name="routes[<?= $row; ?>][route]" value="<?= $route['route']; ?>"/></td>
							<td class="left">
								<a onclick="$(this).closest('.route').remove();" class="button delete">{{Remove}}</a></td>
						</tr>
					<? } ?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="2"></td>
						<td class="left"><a id="add_route" class="button">{{Add Route}}</a></td>
					</tr>
					</tfoot>
				</table>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#route_list').ac_template('route_list');

	$('#add_route').click(function () {
		$.ac_template('route_list', 'add');
	});
</script>
<?= $is_ajax ? '' : call('admin/footer'); ?>
