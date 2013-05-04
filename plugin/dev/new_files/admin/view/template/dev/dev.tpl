<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs();?>
	<?= $this->builder->display_errors($errors);?>
	<div class="box">
		<div class="heading">
			<h1><img src="view/image/backup.png" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a href="<?=$cancel;?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form id="request_sync_table">
				<table class="form">
					<tr>
						<td>
							<label for="sync_site"><?= $text_sync_site;?></label>
							<? $this->builder->set_config('site_url', 'site_name');?>
							<?= $this->builder->build('select', $data_sites, 'site_url', $site_url, array('id' => 'sync_site'));?> 
						</td>
					</tr>
					<tr>
						<td><?= $entry_sync_table; ?></td>
						<td>
							<label for="sync_table"><?=$text_sync_tables;?></label>
							<input id="sync_table" type="text" name="tables" value="<?= $tables; ?>" />
						</td>
					</tr>
					<tr>
						<td><input type="button" class="button" value="<?= $button_sync; ?>" onclick="request_sync_table()" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">//!<--
function request_sync_table(){
	console.log('reqeusting sync from site ' + $('#sync_site').val());
	form = $('#request_sync_table');
	
	if($('#sync_site').val()){
		$.post($('#sync_site').val(), data, update_sync_table);
	}
}

function update_sync_table(data){
	console.log('updating with data' + data);
	
	
}

//--></script>
<?= $footer; ?>