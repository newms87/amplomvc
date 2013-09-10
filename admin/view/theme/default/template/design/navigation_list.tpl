<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="batch_actions">
					<?= $this->builder->batch_action('#listing [name="selected[]"]', $batch_actions, $batch_update); ?>
				</div>
				<div class="buttons">
					<a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a>
					<a onclick="do_batch_action('copy')" class="button"><?= $button_copy; ?></a>
				</div>
			</div>
			<div class="section">
				<div id="listing">
					<?= $list_view; ?>
				</div>
				<div class="pagination"><?= $pagination; ?></div>
			</div>
		</div>
	</div>

	<script type="text/javascript">//<!--
		$('.actions a.reset').click(function () {
			return confirm("<?= $text_admin_nav_reset; ?>");
		});
//--></script>
<?= $footer; ?>