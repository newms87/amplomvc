<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'backup.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
				</div>
			</div>
			<div class="section">
				<form action="<?= $clear_cache; ?>" method="post" enctype="multipart/form-data" id="clear_cache">
					<table class="form">
						<tr>
							<td><?= $entry_clear_cache; ?></td>
							<td>
								<label for="cache_tables"><?= $text_cache_tables; ?></label>
								<input type="text" name="cache_tables" value="<?= $cache_tables; ?>"/>
								<input type="submit" class="button" value="<?= $button_clear_cache; ?>"/>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
<?= $footer; ?>