<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'log.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<? if($prev || $next){?>
						<?= $text_limit; ?> <input type='text' id='limit' value='<?= $limit; ?>' onchange='update_limit();' />
				<? }?>
				<? if($prev){?>
						<a id='button_prev' href="<?= $prev; ?>" class="button"><?= $button_prev; ?></a>
				<? }?>
				<? if($next){?>
						<a id='button_next' href="<?= $next; ?>" class="button"><?= $button_next; ?></a>
				<? }?>
			</div>
		</div>
		<div class="content">
			<table class='form'>
				<tr>
					<td><?= $entry_remove; ?></td>
					<td>
						<form action="<?= $remove; ?>" method="post" style='float:left;'>
							<input id='remove_entries' type='text' name='entries' value="" />
							<a class='button' onclick="$(this).closest('form').submit();"><?= $button_remove; ?></a>
							</form>
							<a href="<?= $clear; ?>" class="button" style='float:left;margin-left:20px'><?= $button_clear; ?></a>
					</td>
				</tr>
				<tr>
					<td><?= $entry_filter_by; ?></td>
					<td id='filter_types'>
							<? $this->builder->set_config('store_id', 'name','string');?>
							<?= $this->builder->build('select', $stores, 'filter_store', $filter_store); ?>
							<a id='filter_link' class='button' href="<?= $filter_url; ?>" onclick="return set_filter_url();"><?= $button_filter; ?></a>
					</td>
				</tr>
			</table>
			<table class="list" width='100%'>
					<thead>
						<tr>
							<td width="2%"><?= $text_remove; ?></td>
								<td width="2%"><?= $column_line; ?></td>
								<td width="6%"><?= $column_date; ?></td>
								<td width="3%"><?= $column_ip; ?></td>
								<td width="65%"><?= $column_message; ?></td>
								<td width="8%"><?= $column_url; ?></td>
								<td width="8%"><?= $column_query; ?></td>
								<td width="8%"><?= $column_store; ?></td>
								<td width="8%"><?= $column_user_agent; ?></td>
						</tr>
					</thead>
					<tbody>
						<? foreach($log as $l){?>
								<tr id='entry<?= $l['line']; ?>'>
									<td><a class='button' onclick="remove_entry(<?= (int)$l['line']; ?>);"><?= 'X'; ?></a></td>
									<td><?= $l['line']; ?></td>
									<td><?= $l['date']; ?></td>
									<td><?= $l['ip']; ?></td>
									<td width="65%"><?= $l['message']; ?></td>
									<td><?= $l['uri']; ?></td>
									<td><?= $l['query']; ?></td>
									<td><?= $l['store']; ?></td>
									<td><?= $l['agent']; ?></td>
								</tr>
						<? }?>
					</tbody>
			</table>
		</div>
	</div>
</div>
<script type='text/javascript'>
function remove_entry(line){
	$.post("<?= $remove; ?>", {entries: line, no_page: 1},
		function(msg){
			$('#entry'+line).css({height: 50})
			.html("<td colspan='7' style='position:relative;padding:5px'><span style='position:absolute;top:5px;' class='message_box success'>"+msg+"</span></td>");
			setTimeout(function(){$('#entry'+line).fadeOut(300);}, 1000);
		}
	);
	$('#entry'+line).html("<td colspan='7' style='height:50px;'><img src='<?= $loading; ?>' /></td>");
}

$('#button_prev, #button_next').trigger('change');
function update_limit(){
	limit = $('#limit').val();
	$('#button_prev, #button_next').each(function(i,e){
			$(e).attr('href',$(e).attr('href').replace(/&limit=\d*/gi,'') + '&limit=' + limit);
	});
}

function set_filter_url(){
	filters = '';
	
	$('#filter_types select').each(function(i,e){
			filters += $(e).val() ? '&' + $(e).attr('name') + '=' + $(e).val() : '';
	});
	
	$('#filter_link').attr('href','<?= $filter_url; ?>' + filters);
	return true;
}

</script>
<?= $footer; ?>