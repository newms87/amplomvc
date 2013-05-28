<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs();?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'information.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<div id="tabs" class="htabs"><a href="#tab-general"><?= $tab_general; ?></a><a href="#tab-data"><?= $tab_data; ?></a><a href="#tab-design"><?= $tab_design; ?></a></div>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<div id="languages" class="htabs">
						<? foreach ($languages as $language) { ?>
						<a href="#language<?= $language['language_id']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /> <?= $language['name']; ?></a>
						<? } ?>
					</div>
					<? foreach ($languages as $language) { ?>
					<div id="language<?= $language['language_id']; ?>">
						<table class="form">
							<tr>
								<td><span class="required"></span> <?= $entry_title; ?></td>
								<td><input type="text" name="information_description[<?= $language['language_id']; ?>][title]" size="100" value="<?= isset($information_description[$language['language_id']]) ? $information_description[$language['language_id']]['title'] : ''; ?>" />
									<? if (isset($error_title[$language['language_id']])) { ?>
									<span class="error"><?= $error_title[$language['language_id']]; ?></span>
									<? } ?></td>
							</tr>
							<tr>
								<td><span class="required"></span> <?= $entry_description; ?></td>
								<td><textarea class='ckedit' name="information_description[<?= $language['language_id']; ?>][description]"><?= isset($information_description[$language['language_id']]) ? $information_description[$language['language_id']]['description'] : ''; ?></textarea>
									<? if (isset($error_description[$language['language_id']])) { ?>
									<span class="error"><?= $error_description[$language['language_id']]; ?></span>
									<? } ?></td>
							</tr>
						</table>
					</div>
					<? } ?>
				</div>
				<div id="tab-data">
					<table class="form">
						<tr>
							<td><?= $entry_store; ?></td>
							<? $this->builder->set_config('store_id', 'name');?>
							<td><?= $this->builder->build('multiselect', $data_stores, "information_store", $information_store);?></td>
						</tr>
						<tr>
							<td><?= $entry_keyword; ?></td>
							<td><input type="text" name="keyword" value="<?= $keyword; ?>" /></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><select name="status">
									<? if ($status) { ?>
									<option value="1" selected="selected"><?= $text_enabled; ?></option>
									<option value="0"><?= $text_disabled; ?></option>
									<? } else { ?>
									<option value="1"><?= $text_enabled; ?></option>
									<option value="0" selected="selected"><?= $text_disabled; ?></option>
									<? } ?>
								</select></td>
						</tr>
						<tr>
							<td><?= $entry_sort_order; ?></td>
							<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1" /></td>
						</tr>
					</table>
				</div>
				<div id="tab-design">
					<table class="list">
						<thead>
							<tr>
								<td class="left"><?= $entry_store; ?></td>
								<td class="left"><?= $entry_layout; ?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="left"><?= $text_default; ?></td>
								<td class="left"><select name="information_layout[0][layout_id]">
										<option value=""></option>
										<? foreach ($layouts as $layout) { ?>
										<? if (isset($information_layout[0]) && $information_layout[0] == $layout['layout_id']) { ?>
										<option value="<?= $layout['layout_id']; ?>" selected="selected"><?= $layout['name']; ?></option>
										<? } else { ?>
										<option value="<?= $layout['layout_id']; ?>"><?= $layout['name']; ?></option>
										<? } ?>
										<? } ?>
									</select></td>
							</tr>
						</tbody>
						<? foreach ($stores as $store) { ?>
						<tbody>
							<tr>
								<td class="left"><?= $store['name']; ?></td>
								<td class="left"><select name="information_layout[<?= $store['store_id']; ?>][layout_id]">
										<option value=""></option>
										<? foreach ($layouts as $layout) { ?>
										<? if (isset($information_layout[$store['store_id']]) && $information_layout[$store['store_id']] == $layout['layout_id']) { ?>
										<option value="<?= $layout['layout_id']; ?>" selected="selected"><?= $layout['name']; ?></option>
										<? } else { ?>
										<option value="<?= $layout['layout_id']; ?>"><?= $layout['name']; ?></option>
										<? } ?>
										<? } ?>
									</select></td>
							</tr>
						</tbody>
						<? } ?>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('ckeditor');?>
 
<script type="text/javascript"><!--
$('#tabs a').tabs();
$('#languages a').tabs();
//--></script>
<?= $footer; ?>