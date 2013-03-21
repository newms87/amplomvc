<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <?= $this->builder->display_errors($errors);?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/setting.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons">
         <a onclick="$('#form').submit();" class="button"><?= $button_send; ?></a>
         <a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a>
      </div>
    </div>
    <div class="content">
      <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
       <table class="form" style="width:50%; margin-left:10%">
         <tr>
           <td class='mail_info'>
              <label for='mail_sender'><?=$entry_mail_sender;?></label>
              <input id='mail_sender' type='text' name='sender' value='<?= $sender;?>' size='40' />
              <label for='mail_from'><span class='required'>*</span><?=$entry_mail_from;?></label>
              <input id='mail_from' type='text' name='from' value='<?= $from;?>' size='100' />
              <label for='mail_to'><span class='required'>*</span><?=$entry_mail_to;?></label>
              <input id='mail_to' type='text' name='to' value='<?= $to;?>' size='100' />
              <label for='mail_cc'><?=$entry_mail_cc;?></label>
              <input id='mail_cc' type='text' name='cc' value='<?= $cc;?>' size='100' />
              <label for='mail_bcc'><?=$entry_mail_bcc;?></label>
              <input id='mail_bcc' type='text' name='bcc' value='<?= $bcc;?>' size='100' />
              <label for='mail_subject'><span class='required'>*</span><?=$entry_mail_subject;?></label>
              <input id='mail_subject' type='text' name='subject' value='<?= $subject;?>' size='100'/>
              <label for='mail_message'><span class='required'>*</span><?=$entry_mail_message;?></label>
              <textarea id='mail_message' rows='15' cols='120' name='message'><?= $message;?></textarea>
              <label for="allow_html"><input type="checkbox" <?= $allow_html ? 'checked':'';?> name='allow_html' id='allow_html' /><?= $entry_allow_html;?></label>
              <label for='mail_attachment'><?=$entry_mail_attachment;?></label>
              <input id='mail_attachment' type='file' multiple name='attachment[]' value='<?= $attachment;?>' size='100' />
           </td>
         </tr>
       </table>
      </form>
    </div>
  </div>
</div>

<?= $this->builder->js('ckeditor');?>

<script type="text/javascript">//<!--
$('#allow_html').change(function(){
   message = $('#mail_message');
   
   if($(this).is(':checked') && !message.hasClass('ckedit')){
      message.addClass('ckedit');
      init_ckeditor_for('mail_message');
   }
   else if(!$(this).is(':checked') && message.hasClass('ckedit')){
      message.removeClass('ckedit');
      remove_ckeditor_for('mail_message');
   }
}).change();
//--></script>

<?=$this->builder->js('errors',$errors);?>

<?= $footer; ?>