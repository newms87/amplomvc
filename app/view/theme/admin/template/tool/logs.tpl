<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/log.png'); ?>" alt=""/> <?= _l("%s Log", $log_name); ?></h1>

			<div class="change_log right">
				<? foreach ($data_log_files as $file) { ?>
					<a href="<?= $file['href']; ?>" class="log_file <?= $file['selected'] ? 'selected' : ''; ?>"><?= $file['name']; ?></a>
				<? } ?>
			</div>

			<div class="buttons">
				<? if (!empty($prev) || !empty($next)) { ?>
					{{Limit}}
					<input type="text" id="limit" value="<?= $limit; ?>" onchange="update_limit();"/>
					<? if (!empty($prev)) { ?>
						<a id="button_prev" href="<?= $prev; ?>" class="button">{{Previous}}</a>
					<? } ?>
					<? if (!empty($next)) { ?>
						<a id="button_next" href="<?= $next; ?>" class="button">{{Next}}</a>
					<? } ?>
				<? } ?>
			</div>
		</div>
		<div class="section">
			<table class="form">
				<tr>
					<td>{{Remove}}</td>
					<td>
						<form action="<?= $remove; ?>" method="post" style="float:left;">
							<input id="remove_entries" type="text" name="entries" value=""/>
							<a class="button" onclick="$(this).closest('form').submit();">{{Remove}}</a>
						</form>
						<a href="<?= $clear; ?>" class="button"
						   style="float:left;margin-left:20px">{{Clear Log Entries}}</a>
					</td>
				</tr>
			</table>
			<table class="list" width="100%">
				<thead>
					<tr>
						<td width="2%">{{Remove}}</td>
						<td width="2%">{{Line}}</td>
						<td width="6%">{{Date}}</td>
						<td width="3%">{{IP}}</td>
						<td width="65%">{{Message}}</td>
						<td width="8%">{{URL}}</td>
						<td width="8%">{{Query}}</td>
						<td width="8%">{{Store}}</td>
						<td width="8%">{{User Agent}}</td>
					</tr>
				</thead>
				<tbody>
					<? foreach ($entries as $e) { ?>
						<tr data-line="<?= $e['line']; ?>">
							<td>
								<a class="button remove" onclick="remove_entry($(this).closest('tr'));">X</a>
							</td>
							<td><?= $e['line']; ?></td>
							<td><?= $e['date']; ?></td>
							<td><?= $e['ip']; ?></td>
							<td width="65%"><?= $e['message']; ?></td>
							<td><?= $e['uri']; ?></td>
							<td><?= $e['query']; ?></td>
							<td><?= $e['store']; ?></td>
							<td><?= $e['agent']; ?></td>
						</tr>
					<? } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript">
	var remove_tpl = $('#log_template').remove().removeAttr('id');

	function remove_entry(context) {
		$.post("<?= $remove; ?>", {entries: context.attr('data-line'), no_page: 1}, function (msg) {
			context.parent().ac_msg('warning', msg);

			setTimeout(function () {
				context.fadeOut(300);
			}, 1000);
		}, 'json');

		context.loading();
	}

	$('#button_prev, #button_next').trigger('change');

	function update_limit() {
		limit = $('#limit').val();
		$('#button_prev, #button_next').each(function (i, e) {
			$(e).attr('href', $(e).attr('href').replace(/&limit=\d*/gi, '') + '&limit=' + limit);
		});
	}
</script>
<?= $is_ajax ? '' : call('admin/footer'); ?>
