<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id='content'>
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>
	
	<div class="product-info">
		<div class="left">
			<?= $block_product_images; ?>
			
			<? if (!empty($block_product_related)) { ?>
				<?= $block_product_related; ?>
			<? } ?>
		</div>
		
		<div class="right">
			<?= $block_product_information; ?>
		</div>
	</div>
	
	<? if (!empty($block_product_additional) ) { ?>
		<?= $block_product_additional; ?>
	<? } ?>
	
	<? if (!empty($tags)) { ?>
	<div class="tags"><b><?= $text_tags; ?></b>
		<? foreach($tags as $i => $tag) {?>
		<a href="<?= $tags[$i]['href']; ?>"><?= $tags[$i]['text']; ?></a> <?= $i == (count($tags) -1) ? '':','; ?>
		<? } ?>
	</div>
	<? } ?>
	
	<?= $content_bottom; ?>
</div>

<?= $footer; ?>