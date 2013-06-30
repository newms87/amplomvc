<?= $header; ?>
<div class="content">
	<?= $breadcrumbs; ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td valign="top"><a id="add_reason" class="button"><?= $button_add; ?></a></td>
						<td>
							<ul id="return_reason_list" class="easy_list">
							
								<? $max_row = 0; ?>
								<? foreach ($return_reasons as $key => $reason) { ?>
									<li class="return_reason <?= $key; ?>">
										<input class="title" size="50" type="text" name="return_reasons[<?= $key; ?>][title]" value="<?= $reason['title']; ?>" /><br />
										<? if (empty($reason['no_delete'])) { ?>
											<a class="delete_button text" onclick="$(this).closest('li').remove()"><?= $button_delete; ?></a>
										<? } ?>
									</li>
									<? if (is_integer($key)) { $max_row = max($max_row, $key); } ?>
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

<?= $this->builder->js('template_rows', '#return_reason_list', '#add_reason', $max_row+1, $template_row_defaults); ?>

<?= $this->builder->js('errors',$errors); ?>

<script type="text/javascript">//<!--
$('#return_reason_list').sortable();
//--></script>

<?= $footer; ?>