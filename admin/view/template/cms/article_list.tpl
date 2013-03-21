<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <?= $this->builder->display_errors($errors);?>
  <div class="box">
    <div class="heading">
      <div class="actions">
         <?=$this->builder->build_batch_actions($text_batch_action,$batch_actions,$batch_action_values, $batch_action_go);?>
      </div>
      <h1><img src="view/image/shipping.png" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left">
                 <a href="<?= $sort_aid;?>" <?= $sort=='a.aid'?'class="'.strtolower($order).'"':'';?>><?= $column_aid; ?></a>
              </td>
              <td class="left">
                <a href="<?= $sort_title;?>" <?= $sort=='ad.title'?'class="'.strtolower($order).'"':'';?>><?= $column_title; ?></a>
              </td>
              <td class="left">
                 <a href="<?= $sort_author;?>" <?= $sort=='a.author'?'class="'.strtolower($order).'"':'';?>><?= $column_author; ?></a>
              </td>
              <td class="left">
                 <span><?= $column_category; ?></div>
              </td>
              <td class="left">
                 <a href="<?= $sort_date_active;?>" <?= $sort=='a.date_active'?'class="'.strtolower($order).'"':'';?>><?= $column_date_active; ?></a>
              </td>
              <td class="left">
                 <a href="<?= $sort_date_expires;?>" <?= $sort=='a.date_expires'?'class="'.strtolower($order).'"':'';?>><?= $column_date_expires; ?></a>
              </td>
              <td class="left">
                 <a href="<?= $sort_status;?>" <?= $sort=='a.status'?'class="'.strtolower($order).'"':'';?>><?= $column_status; ?></a>
              </td>
              <td class="right"><?= $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <? if ($articles) { ?>
            <? foreach ($articles as $article) { ?>
            <tr>
              <td style="text-align: center;">
                <input type="checkbox" name="selected[]" value="<?= $article['article_id']; ?>" <?= $article['selected']?'checked="checked"':'';?> />
              </td>
              <td class="left"><?= $article['aid']; ?></td>
              <td class="left"><?= $article['title']; ?></td>
              <td class="left"><?= $article['author']; ?></td>
              <td class="left"><? foreach($article['categories'] as $cat)echo"<div>".$categories[$cat]."</div>"; ?></td>
              <td class="left"><?= $article['date_active']; ?></td>
              <td class="left"><?= $article['date_expires']; ?></td>
              <td class="left"><?= $article['status']?$text_enabled:$text_disabled; ?></td>
              <td class="right">
                <? foreach ($article['action'] as $action) { ?>
                [ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
                <? } ?>
              </td>
            </tr>
            <? } ?>
            <? } else { ?>
            <tr>
              <td class="center" colspan="4"><?= $text_no_results; ?></td>
            </tr>
            <? } ?>
          </tbody>
        </table>
      </form>
      <div class="pagination"><?= $pagination; ?></div>
    </div>
  </div>
</div>
<?= $footer; ?>

<?=$this->builder->js('errors',$errors);?>
<?=$this->builder->js('datepicker');?>