<?= $header; ?>
<div class="content">
  <?=$this->builder->display_breadcrumbs();?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/setting.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons">
         <a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a>
         <a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a>
      </div>
    </div>
    <div class="content">
      <form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <?= $block_list;?>
      </form>
    </div>
  </div>
</div>

<?= $footer; ?> 