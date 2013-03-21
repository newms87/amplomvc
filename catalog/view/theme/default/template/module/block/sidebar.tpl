<div id='sidebar_menu' class="box sidebar_box">
  <div class="box-heading"><?= $heading_title; ?></div>
  <div class="box-content">
    <div id='sidebar_menu_links'>
       <?= $this->builder->build_links($menu_items);?>
    </div>
  </div>
</div>
