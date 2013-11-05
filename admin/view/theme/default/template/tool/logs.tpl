<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'log.png'; ?>" alt=""/> <?= _l("%s Log", $log_name); ?></h1>

			<div class="buttons">
				<? if ($prev || $next) { ?>
					<?= $text_limit; ?> <input type='text' id='limit' value='<?= $limit; ?>' onchange='update_limit();'/>
				<? } ?>
				<? if ($prev) { ?>
					<a id='button_prev' href="<?= $prev; ?>" class="button"><?= _l("Previous"); ?></a>
				<? } ?>
				<? if ($next) { ?>
					<a id='button_next' href="<?= $next; ?>" class="button"><?= _l("Next"); ?></a>
				<? } ?>
			</div>
		</div>
		<div class="section">
			<table class='form'>
				<tr>
					<td><?= _l("Remove"); ?></td>
					<td>
						<form action="<?= $remove; ?>" method="post" style='float:left;'>
							<input id='remove_entries' type='text' name='entries' value=""/>
							<a class='button' onclick="$(this).closest('form').submit();"><?= _l("Remove"); ?></a>
						</form>
						<a href="<?= $clear; ?>" class="button"
						   style='float:left;margin-left:20px'><?= _l("Clear Log Entries"); ?></a>
					</td>
				</tr>
			</table>
			<table class="list" width='100%'>
				<thead>
				<tr>
					<td width="2%"><?= _l("Remove"); ?></td>
					<td width="2%"><?= _l("Line"); ?></td>
					<td width="6%"><?= _l("Date"); ?></td>
					<td width="3%"><?= _l("IP"); ?></td>
					<td width="65%"><?= _l("Message"); ?></td>
					<td width="8%"><?= _l("URL"); ?></td>
					<td width="8%"><?= _l("Query"); ?></td>
					<td width="8%"><?= _l("Store"); ?></td>
					<td width="8%"><?= _l("User Agent"); ?></td>
				</tr>
				</thead>
				<tbody>
				<? foreach ($entries as $e) { ?>
					<tr id='entry<?= $e['line']; ?>'>
						<td><a class='button' onclick="remove_entry(<?= (int)$e['line']; ?>);"><?= 'X'; ?></a></td>
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
<script type='text/javascript'>
	function remove_entry(line) {
		$.post("<?= $remove; ?>", {entries: line, no_page: 1},
			function (msg) {
				$('#entry' + line).css({height: 50})
					.html("<td colspan='7' style='position:relative;padding:5px'><span style='position:absolute;top:5px;' class='message_box success'>" + msg + "</span></td>");
				setTimeout(function () {
					$('#entry' + line).fadeOut(300);
				}, 1000);
			}
		);
		$('#entry' + line).loading();
	}

	$('#button_prev, #button_next').trigger('change');
	function update_limit() {
		limit = $('#limit').val();
		$('#button_prev, #button_next').each(function (i, e) {
			$(e).attr('href', $(e).attr('href').replace(/&limit=\d*/gi, '') + '&limit=' + limit);
		});
	}

	function set_filter_url() {
		filters = '';

		$('#filter_types select').each(function (i, e) {
			filters += $(e).val() ? '&' + $(e).attr('name') + '=' + $(e).val() : '';
		});

		$('#filter_link').attr('href', '<?= $filter_url; ?>' + filters);
		return true;
	}

</script>
<?= $footer; ?>
