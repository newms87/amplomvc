<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td valign="top"><a id="add_reason" class="button"><?= $button_add; ?></a></td>
						<td>
							<ul id="return_reason_list" class="easy_list">
								<? foreach ($return_reasons as $row => $reason) { ?>
									<li class="return_reason" data-row="<?= $row; ?>">
										<input class="title" size="50" type="text" name="return_reasons[<?= $row; ?>][title]" value="<?= $reason['title']; ?>" /><br />
										<? if (empty($reason['no_delete'])) { ?>
											<a class="delete_button text" onclick="$(this).closest('li').remove()"><?= $button_delete; ?></a>
										<? } ?>
									</li>
								<? } ?>
							</ul>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<? foreach ($return_reasons as $key => $reason) { ?>
	<?= $this->builder->js('translations', $reason['translations'], "return_reasons[$key][%name%]"); ?>
<? } ?>

<script type="text/javascript">//<!--
$('#return_reason_list').ac_template('rr_list', {defaults: <?= json_encode($return_reasons['__ac_template__']); ?>});
$('#add_reason').click(function(){ $.ac_template('rr_list', 'add') });

$('#return_reason_list').sortable();
//--></script>

<?= $this->builder->js('errors',$errors); ?>

<?= $footer; ?>