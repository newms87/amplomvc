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
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td valign="top"><a id="add_status" class="button"><?= $button_add; ?></a></td>
						<td>
							<ul id="return_status_list" class="easy_list">
							
								<? $max_row = 0; ?>
								<? foreach ($return_statuses as $key => $status) { ?>
									<li class="return_status <?= $key; ?>">
										<input class="title" size="50" type="text" name="return_statuses[<?= $key; ?>][title]" value="<?= $status['title']; ?>" /><br />
										<? if (empty($status['no_delete'])) { ?>
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

<? foreach ($return_statuses as $key => $status) { ?>
	<?= $this->builder->js('translations', $status['translations'], "return_statuses[$key][%name%]"); ?>
<? } ?>

<?= $this->builder->js('template_rows', '#return_status_list', '#add_status', $max_row+1, $template_row_defaults); ?>

<?= $this->builder->js('errors',$errors); ?>

<script type="text/javascript">//<!--
$('#return_status_list').sortable();
//--></script>
<?= $footer; ?>