<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'error.png'; ?>" alt="" /> <?= $head_title; ?></h1>
		</div>
		<div class="content">
			<div style="border: 1px solid #DDDDDD; background: #F7F7F7; text-align: center; padding: 15px;"><?= $text_permission; ?></div>
		</div>
	</div>
</div>
<?= $footer; ?>