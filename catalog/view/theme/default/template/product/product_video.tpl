<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div id='content'>
	<?= $this->breadcrumb->render(); ?>
	<?= $content_top; ?>
	
	<h1><?= $heading_title; ?></h1>
 <OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" WIDTH="0" HEIGHT="0" id="Yourfilename" ALIGN=""><PARAM NAME=movie VALUE="Yourfilename.swf"> <PARAM NAME=quality VALUE=high> <PARAM NAME=bgcolor VALUE=#333399> <EMBED src="Yourfilename.swf" quality=high bgcolor=#333399 WIDTH="320" HEIGHT="240" NAME="Yourfilename" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED> </OBJECT>

	<div class='product-info'>
		
		<div class="product_video">
				<div class="left">
				<?= $product_video; ?>
				</div>
				<div class="right"><?= $description; ?></div>
		</div>
		<div class="product_video_helper">
				<?= $text_video_helper; ?>
		</div>
		
		<div class="description">
		<? if (isset($manufacturer) && $manufacturer) { ?>
				<div class="description_manufacturer"><span><?= $text_more_from_designer; ?></span><a href='<?= $manufacturer_url; ?>' class='manufacturer_link'><?= $manufacturer; ?></a><span style="margin-left:7px"><?= $text_on_store; ?></span></div>
		<? } ?>
		
		<? if(isset($block_sharing)) { ?>
				<?= $block_sharing; ?>
		<? } ?>
		</div>
	</div>
		
	<? if ($tags) { ?>
	<div class="tags"><b><?= $text_tags; ?></b>
		<? foreach($tags as $i => $tag) {?>
		<a href="<?= $tags[$i]['href']; ?>"><?= $tags[$i]['tag']; ?></a> <?= $i == (count($tags) -1) ? '':','; ?>
		<? } ?>
	</div>
	<? } ?>
	
	<?= $content_bottom; ?>
</div>

<?= $footer; ?>