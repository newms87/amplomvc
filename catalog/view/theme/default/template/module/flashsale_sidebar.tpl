<div id='featured_flashsale_sidebar' class="box sidebar_box">
	<div class="box-heading"><?= $heading_title; ?></div>
	<div class="box-content">
			<ul>
				<? foreach ($flashsales as $fs) { ?>
				<li>
					<a href='<?=$fs['href'];?>' class='flashsale_menu_link'><?=$fs['name'];?></a>
				</li>
			<? }?>
			</ul>
	</div>
</div>
