<?= $header; ?>
<div class="content">
  <?=$this->builder->display_breadcrumbs();?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/setting.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="batch_actions">
         <a href="<?= $download_email_list;?>" class="button"><?= $button_email_list;?></a>
         <span><?= $text_batch_action;?></span> <?= $this->builder->build('select',$update_actions, 'action','',array('id'=>'update_action'));?>
         <a class="button" onclick="$('#form').attr('action', '<?= $list_update;?>'.replace(/%action%/,$('#update_action').val())).submit();" ><?= $text_batch_action_go;?></a>
     </div>
      <div class="buttons">
         <a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a>
         <a onclick="$('form').attr('action', '<?= $copy;?>').submit()" class="button"><?= $button_copy;?></a>
         <a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a>
      </div>
    </div>
    <div class="content">
      <form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <?= $newsletter_view;?>
      </form>
       <div class="pagination"><?= $pagination; ?></div>
    </div>
  </div>
</div>

<?= $footer; ?> 