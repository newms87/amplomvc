<div id='sidebar_menu' class="box sidebar_box">
	<div class="box-heading"><?= $heading_title; ?></div>
	<div class="box-content">
		<div id='sidebar_menu_links'>
			<div class="sidebar_main">
				<!--<div class="label"><?= $main_menu['label']; ?></div>-->
					<div class="links"><?= $this->builder->build_links($main_menu['menu']); ?></div>
				</div>
				
				<div class="sidebar_additional">
					<div class="label"><?= $page_menu['label']; ?></div>
					<div class="links"><?= $this->builder->build_links($page_menu['menu']); ?></div>
				</div>
				
			<? if(!empty($attribute_menu)) { ?>
					<? foreach($attribute_menu as $attr_menu) { ?>
					<div class="attribute_menu">
						<div class="label"><?= $attr_menu['label']; ?></div>
						<div class="links"><?= $this->builder->build_links($attr_menu['menu']); ?></div>
					</div>
					<? } ?>
			<? } ?>
		</div>
	</div>
</div>
