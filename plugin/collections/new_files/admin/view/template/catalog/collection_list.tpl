<?= $header; ?>
<div class="content">
  <?=$this->builder->display_breadcrumbs();?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/setting.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="batch_actions">
         <?= $this->builder->build_batch_actions($text_batch_action, $batch_actions, $batch_action_values, $batch_action_go);?>
      </div>
      <div class="buttons">
         <a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a>
         <a onclick="$('form').attr('action', '<?= $copy;?>').submit()" class="button"><?= $button_copy;?></a>
      </div>
    </div>
    <div class="content">
      <form action="" method="post" enctype="multipart/form-data" id="form">
        <?= $list_view;?>
      </form>
       <div class="pagination"><?= $pagination; ?></div>
    </div>
  </div>
</div>

<?= $footer; ?> 