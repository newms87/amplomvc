<div id='featured_designer_sidebar' class="box sidebar_box">
	<div class="box_heading"><?= $heading_title; ?></div>
	<div class="box_content">
			<ul>
				<? foreach ($designers as $fs) { ?>
				<li>
					<a href='<?= $fs['href']; ?>' class='designer_menu_link'><?= $fs['name']; ?></a>
				</li>
			<? }?>
				<li>
						<a href='<?= $view_all_designers; ?>' class='designer_menu_link'><?= $text_view_all_designers; ?></a>
				</li>
			</ul>
	</div>
</div>
