<?= $header; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<? if ($error_warning) { ?>
			<div class="message_box warning"><?= $error_warning; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'measurement.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location='<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
			</div>
			<div class="content">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<div class="tabs">
						<? foreach ($languages as $language) { ?>
							<a href="#language<?= $language['language_id']; ?>"><img
									src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>"
									title="<?= $language['name']; ?>"/> <?= $language['name']; ?></a>
						<? } ?>
					</div>
					<? foreach ($languages as $language) { ?>
						<div id="language<?= $language['language_id']; ?>">
							<table class="form">
								<tr>
									<td class="required"> <?= $entry_title; ?></td>
									<td><input type="text" name="measurement_class[<?= $language['language_id']; ?>][title]" value="<?= isset($measurement_class[$language['language_id']]) ? $measurement_class[$language['language_id']]['title'] : ''; ?>"/>
										<? if (isset($error_title[$language['language_id']])) { ?>
											<span class="error"><?= $error_title[$language['language_id']]; ?></span>
										<? } ?></td>
								</tr>
								<tr>
									<td class="required"> <?= $entry_unit; ?></td>
									<td><input type="text" name="measurement_class[<?= $language['language_id']; ?>][unit]" value="<?= isset($measurement_class[$language['language_id']]) ? $measurement_class[$language['language_id']]['unit'] : ''; ?>"/>
										<? if (isset($error_unit[$language['language_id']])) { ?>
											<span class="error"><?= $error_unit[$language['language_id']]; ?></span>
										<? } ?></td>
								</tr>
							</table>
						</div>
					<? } ?>
					<table class="form">
						<? foreach ($measurement_tos as $measurement_to) { ?>
							<tr>
								<td><?= $measurement_to['title']; ?>:</td>
								<td><input type="text" name="measurement_rule[<?= $measurement_to['measurement_class_id']; ?>]" value="<?= isset($measurement_rule[$measurement_to['measurement_class_id']]) ? $measurement_rule[$measurement_to['measurement_class_id']]['rule'] : ''; ?>"/>
								</td>
							</tr>
						<? } ?>
					</table>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript"><!--
		$('.tabs a').tabs();
//--></script>
<?= $footer; ?>