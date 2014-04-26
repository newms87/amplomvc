<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
	<? if ($error_warning) { ?>
		<div class="message warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'stock-status.png'; ?>" alt=""/> <?= _l("Stock Status"); ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Stock Status Name:"); ?></td>
						<td><? foreach ($languages as $language) { ?>
								<input type="text" name="stock_status[<?= $language['language_id']; ?>][name]" value="<?= isset($stock_status[$language['language_id']]) ? $stock_status[$language['language_id']]['name'] : ''; ?>"/>
								<img src="<?= URL_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>"
									title="<?= $language['name']; ?>"/><br/>
								<? if (isset(_l("Stock Status Name must be between 3 and 32 characters!")[$language['language_id']])) { ?>
									<span class="error"><?= _l("Stock Status Name must be between 3 and 32 characters!")[$language['language_id']]; ?></span><br/>
								<? } ?>
							<? } ?></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= _call('common/footer'); ?>
