<div class="box">
		<div class="box-designer">
			
			<?
			$cols = 3;
		$count = 0;
			foreach ($designers as $designer) {
				extract($designer['featured_product']);
			?>
			<div class='designer_preview'>
				<a class='preview_link_overlay' href="<?= $designer['href']; ?>">
					<div class='link_overlay_text'>
						<div class='designer_name'><?= $designer['name']; ?></div>
						<div class='featured_category_name'><?= $designer['cat_name'];?></div>
					</div>
					<div class='link_overlay_go'><div class='link_overlay_arrow'></div></div>
			</a>
			<div class="designer_image"><a href="<?= $designer['href']; ?>"><img src="<?= $designer['image']; ?>" alt="<?= $designer['name']; ?>" /></a></div>
			<div class='preview_info'>
				<div class='gradient_a'></div>
				<div class='gradient_b'></div>
				<div class='info_images'>
					<? foreach($images as $image)
						echo "<img src='$image' />";
				?>
				</div>
				<div class='info_bottom'>
					<div class='info_price'>
						
						<? if(isset($sale_price)) {?>
							<div class='orig_price'><?= $price; ?></div>
							<div class='sale_text'><?= $entry_price; ?></div>
							<div class='sale_price'><?= $sale_price; ?></div>
						<? } else{
							echo $price;
								} ?>
					</div>
					<div class='info_links'>
						<a href='<?= $designer['href'];?>' class='info_link_collection'><?= $entry_collection;?></a>
						<div class='pink_split'></div>
						<a href='<?= $designer['interview_href'];?>' class='info_link_story'><?= $entry_story;?></a>
					</div>
				</div>
			</div>
		</div>
		<?	echo ($count++ % $cols == ($cols-1)) ? "<div style='clear:both'></div>":""; ?>
			<?
} ?>
	</div>
</div>
