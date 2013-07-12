<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="batch_actions">
				Batch Action: <?= $this->builder->build('select',$update_actions, 'action','',array('id'=>'update_action')); ?>
				<a class="button" onclick="$('#form').attr('action', '<?= $list_update; ?>'.replace(/%action%/,$('#update_action').val())).submit();" >Go</a>
		</div>
			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
							<td class="right"><?= $column_action; ?></td>
							<td class="left"><?= "<a href='$sort_name' " . ($sort=='name'?"class='$order'":'').'>'. $column_name . '</a>'; ?></td>
							<td class="left"><?= "<a href='$sort_keyword' " . ($sort=='keyword'?"class='$order'":'').'>'. $column_keyword . '</a>'; ?></td>
							<td class="left"><?= $column_image; ?></a></td>
							<td class="left"><?= $column_designers; ?></a></td>
							<td class="left"><?= "<a href='$sort_discount' " . ($sort=='discount'?"class='$order'":'').'>'. $column_discount . '</a>'; ?></td>
							<td class="left"><?= "<a href='$sort_date_start' " . ($sort=='date_start'?"class='$order'":'').'>'. $column_date_start . '</a>'; ?></td>
							<td class="left"><?= "<a href='$sort_date_end' " . ($sort=='date_end'?"class='$order'":'').'>'. $column_date_end . '</a>'; ?></td>
							<td class="left"><?= "<a href='$sort_status' " . ($sort=='status'?"class='$order'":'').'>'. $column_status . '</a>'; ?></td>
							<td class="right"><?= $column_action; ?></td>
						</tr>
					</thead>
					<tbody>
						<tr id="filter_list">
							<td></td>
							<td align="right"><a onclick="filter();" class="button"><?= $button_filter; ?></a></td>
							<td><input type="text" name="filter_name" value="<?= $filter_name; ?>" /></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="left">
								<?= $this->builder->build('select', $data_prefixes, 'date_start_prefix', $date_start_prefix); ?>
								<input type="text" name="filter_date_start" value="<?= $filter_date_start; ?>" class='datetime' size="8"/>
							</td>
							<td align="left">
								<?= $this->builder->build('select', $data_prefixes, 'date_end_prefix', $date_end_prefix); ?>
								<input type="text" name="filter_date_end" value="<?= $filter_date_end; ?>" class='datetime' size="8"/>
							</td>
							<td><?= $this->builder->build('select', $data_statuses_blank, "filter_status", $filter_status); ?></td>
							<td align="right"><a onclick="filter();" class="button"><?= $button_filter; ?></a></td>
						</tr>
						<? if ($flashsales) { ?>
						<? foreach ($flashsales as $fs) { ?>
						<tr>
							<td style="text-align: center;"><input type="checkbox" name="selected[]" value="<?= $fs['flashsale_id']; ?>" <?= $fs['selected']?"checked='checked'":""; ?> /></td>
							<td class="right">[ <a href="<?= $fs['action']['href']; ?>"><?= $fs['action']['text']; ?></a> ]</td>
							<td class="left"><?= $fs['name']; ?></td>
							<td class="left"><?= $fs['keyword']; ?></td>
							<td class="left"><img src='<?= $fs['image']; ?>' /></td>
							<td class="left"><? if($fs['designers'])foreach($fs['designers'] as $d) echo $d['name'] . '<br>';else echo "No Designers";?></td>
							<td class="left"><?= $fs['discount']; ?></td>
							<td class="left"><?= $fs['date_start']; ?></td>
							<td class="left"><?= $fs['date_end']; ?></td>
							<td class="left"><?= $statuses[(int)$fs['status']]; ?></td>
							<td class="right">[ <a href="<?= $fs['action']['href']; ?>"><?= $fs['action']['text']; ?></a> ]</td>
						</tr>
						<? } ?>
						<? } else { ?>
						<tr>
							<td class="center" colspan="11"><?= $text_no_results; ?></td>
						</tr>
						<? } ?>
					</tbody>
				</table>
			</form>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>

<?= $this->builder->js('filter_url', '#filter_list', 'catalog/flashsale'); ?>

<?= $this->builder->js('datepicker'); ?>

<?= $footer; ?> 