<div id='sidebar_menu' class="box sidebar_box">
	<div class="box_heading"><?= $heading_title; ?></div>
	<div class="box_content">
		<div id='sidebar_menu_links'>
			<div class="sidebar_main">
				<!--<div class="label"><?= $main_menu['label']; ?></div>-->
					<div class="links"><?= $this->document->renderLinks($main_menu['menu']); ?></div>
				</div>
				
				<div class="sidebar_additional">
					<div class="label"><?= $page_menu['label']; ?></div>
					<div class="links"><?= $this->document->renderLinks($page_menu['menu']); ?></div>
				</div>
				
			<? if(!empty($attribute_menu)) { ?>
					<? foreach($attribute_menu as $attr_menu) { ?>
					<div class="attribute_menu">
						<div class="label"><?= $attr_menu['label']; ?></div>
						<div class="links"><?= $this->document->renderLinks($attr_menu['menu']); ?></div>
					</div>
					<? } ?>
			<? } ?>
		</div>
	</div>
</div>
