<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= $head_title; ?></h1>
		</div>
		<div class="section">
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

<script type="text/javascript">//<!--
$('.action-uninstall').click(function(){
	keep_data = 0;

	if(confirm("<?= $text_keep_data; ?>")){
		keep_data = 1;
	}

	$(this).attr('href', $(this).attr('href') + '&keep_data=' + keep_data);
});
//--></script>

<?= $footer; ?>