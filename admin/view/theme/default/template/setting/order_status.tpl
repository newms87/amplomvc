<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
					<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
				</div>
			</div>
			<div class="section">
				<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td valign="top"><a id="add_status" class="button"><?= $button_add; ?></a></td>
							<td>
								<ul id="order_status_list" class="easy_list">
									<? foreach ($order_statuses as $row => $status) { ?>
										<li class="order_status" data-row="<?= $row; ?>">
											<input class="title" size="50" type="text" name="order_statuses[<?= $row; ?>][title]" value="<?= $status['title']; ?>"/><br/>
											<? if (empty($status['no_delete'])) { ?>
												<a class="delete button text" onclick="$(this).closest('li').remove()"><?= $button_delete; ?></a>
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

<? foreach ($order_statuses as $key => $status) { ?>
	<?= $this->builder->js('translations', $status['translations'], "order_statuses[$key][%name%]"); ?>
<? } ?>

	<script type="text/javascript">
		$('#order_status_list').ac_template('os_list', {defaults: <?= json_encode($order_statuses['__ac_template__']); ?>});
		$('#add_status').click(function () {
			$.ac_template('os_list', 'add')
		});

		$('#order_status_list').sortable();
</script>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
