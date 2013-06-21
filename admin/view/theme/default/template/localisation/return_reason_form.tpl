<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'order.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_name; ?></td>
						<td><? foreach ($languages as $language) { ?>
							<input type="text" name="return_reason[<?= $language['language_id']; ?>][name]" value="<?= isset($return_reason[$language['language_id']]) ? $return_reason[$language['language_id']]['name'] : ''; ?>" />
							<img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /><br />
							<? if (isset($error_name[$language['language_id']])) { ?>
							<span class="error"><?= $error_name[$language['language_id']]; ?></span><br />
							<? } ?>
							<? } ?></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>