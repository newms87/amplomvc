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
						<form action="<?= site_url('admin/tool/logs/remove', 'log=' . $log); ?>" method="post" style="float:left;">
							<input id="remove_entries" type="text" name="entries" value=""/>
							<a class="button" onclick="$(this).closest('form').submit();">{{Remove}}</a>
						</form>
						<a href="<?= site_url('admin/tool/logs/clear', 'log=' . $log); ?>" class="button" style="float:left;margin-left:20px">{{Clear Log Entries}}</a>
					</td>
				</tr>
			</table>
			<table class="list" width="100%">
				<thead>
					<tr>
						<td width="2%">{{Remove}}</td>
						<? foreach (array_keys($entries[0]) as $ekey) { ?>
							<td class="<?= $ekey; ?>"><?= !empty($fields[$ekey]) ? $fields[$ekey] : cast_title($ekey); ?></td>
						<? } ?>
					</tr>
				</thead>
				<tbody class="entry-list">
					<? foreach ($entries as $e) { ?>
						<tr data-line="<?= $e['line']; ?>">
							<td>
								<a class="button remove">X</a>
							</td>
							<? foreach ($e as $key => $value) { ?>
								<td class="<?= $key; ?>"><?= $value; ?></td>
							<? } ?>
						</tr>
					<? } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript">
	var remove_tpl = $('#log_template').remove().removeAttr('id');

	$('.entry-list .remove').click(function() {
		var $tr = $(this).closest('tr');

		$tr.find('a').loading({text: 'O'});

		$.post("<?= site_url('admin/tool/logs/remove', 'log=' . $log); ?>", {entries: $tr.attr('data-line'), no_page: 1}, function (msg) {
			$tr.html($('<td colspan="'+$tr.children().length+'">Removed</td>').ac_msg(msg));
			setTimeout(function(){$tr.remove()}, 2000);
		}, 'json');
	});

	$('#button_prev, #button_next').trigger('change');

	function update_limit() {
		limit = $('#limit').val();
		$('#button_prev, #button_next').each(function (i, e) {
			$(e).attr('href', $(e).attr('href').replace(/&limit=\d*/gi, '') + '&limit=' + limit);
		});
	}
</script>
<?= $is_ajax ? '' : call('admin/footer'); ?>
