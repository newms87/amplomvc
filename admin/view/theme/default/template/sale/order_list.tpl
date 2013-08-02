<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a href="<?= $insert; ?>" class="button"><?= $button_insert; ?></a>
				<a onclick="do_batch_action('copy')" class="button"><?= $button_copy; ?></a>
			</div>
		</div>
		<div class="content">
			<div class="limits">
				<?= $limits; ?>
			</div>
			
			<div id="listing">
				<?= $list_view; ?>
			</div>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>

<?= $footer; ?>