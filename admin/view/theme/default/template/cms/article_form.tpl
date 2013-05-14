<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <?= $this->builder->display_errors($errors);?>
  <div class="box">
    <div class="heading">
      <h1><img src="<?= HTTP_THEME_IMAGE . 'shipping.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <div id="tabs" class="htabs"><a href="#tab-general"><?= $tab_general; ?></a><a href="#tab-related"><?= $tab_related; ?></a><a href='#tab-design'><?=$tab_design;?></a></div>
      <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-general">
          <table class="form">
           <div id="languages" class="htabs">
               <? foreach ($languages as $language) { ?>
               <a href="#language<?= $language['language_id']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /> <?= $language['name']; ?></a>
               <? } ?>
           </div>
            <tr>
              <td><span class="required">*</span> <?= $entry_title; ?></td>
              <? foreach ($languages as $language) {?>
                  <td id='language<?= $language['language_id'];?>'><input type="text" name="article_description[<?=$language['language_id'];?>][title]" value="<?= isset($article_description[$language['language_id']]) ? $article_description[$language['language_id']]['title'] : '';?>" size="100" /></td>
              <? }?>
            </tr>
            <tr>
              <td><span class="required">*</span> <?= $entry_author; ?></td>
              <td><input type="text" name="author" value="<?= $author; ?>" size="100" />
              </td>
            </tr>
            <tr>
              <td><span class="required">*</span><?= $entry_keyword; ?></td>
              <td>
                 <input type="text" onfocus='generate_url_warning(this)' name="keyword" value="<?= $keyword; ?>" />
                 <a class='gen_url' onclick='generate_url(this)'><?=$button_generate_url;?></a>
              </td>
            </tr>
           <tr>
                <td><?= $entry_intro; ?></td>
                <? foreach ($languages as $language) {?>
                	<td id='language<?= $language['language_id'];?>'><textarea name="article_description[<?= $language['language_id']; ?>][intro]" class='ckedit' id="intro<?= $language['language_id']; ?>"><?= isset($article_description[$language['language_id']]) ? $article_description[$language['language_id']]['intro'] : ''; ?></textarea></td>
                <? } ?>
           </tr>
           <tr>
                <td><?= $entry_description; ?></td>
                <? foreach ($languages as $language) {?>
                  <td id='language<?= $language['language_id'];?>'><textarea name="article_description[<?= $language['language_id']; ?>][description]" class='ckedit' id="description<?= $language['language_id']; ?>"><?= isset($article_description[$language['language_id']]) ? $article_description[$language['language_id']]['description'] : ''; ?></textarea></td>
                <? } ?>
           </tr>
           <tr>
                <td><?= $entry_meta_description; ?></td>
                <? foreach ($languages as $language) {?>
                  <td id='language<?= $language['language_id'];?>'><textarea cols='80' rows='5' name="article_description[<?= $language['language_id']; ?>][meta_description]"><?= isset($article_description[$language['language_id']]) ? $article_description[$language['language_id']]['meta_description'] : ''; ?></textarea></td>
                <? } ?>
           </tr>
           <tr>
                <td><?= $entry_meta_keyword; ?></td>
                <? foreach ($languages as $language) {?>
                  <td id='language<?= $language['language_id'];?>'><textarea cols='80' rows='5' name="article_description[<?= $language['language_id']; ?>][meta_keyword]"><?= isset($article_description[$language['language_id']]) ? $article_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea></td>
                <? } ?>
           </tr>
           
            <tr>
              <td><?= $entry_image; ?></td>
              <td valign="top"><div class="image"><img src="<?= $thumb; ?>" alt="" id="thumb" />
                <input type="hidden" name="image" value="<?= $image; ?>" id="image" />
                <br /><a onclick="image_upload('image', 'thumb');"><?= $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?= $no_image; ?>'); $('#image').attr('value', '');"><?= $text_clear; ?></a></div></td>
            </tr>
            <tr>
               <td><?=$entry_date_active;?></td>
               <td><input type='text' class='datetime' name='date_active' value='<?=$date_active;?>' /></td>
            </tr>
            <tr>
               <td><?=$entry_date_expires;?></td>
               <td><input type='text' class='datetime' name='date_expires' value='<?=$date_expires;?>' /></td>
            </tr>
            <tr>
               <td><?=$entry_syndicator;?></td>
               <td><?= $this->builder->build('select',$syndicators, 'syndicator',$syndicator);?></td>
            </tr>
            <tr>
               <td><?=$entry_pagination;?></td>
               <td><?= $this->builder->build('select',$pagination_types, 'pagination_type',$pagination_type);?></td>
            </tr>
            <tr>
               <td><?=$entry_members_only;?></td>
               <td><?= $this->builder->build('select',$yes_no, 'members_only',$members_only);?></td>
            </tr>
            <tr>
               <td><?=$entry_gads;?></td>
               <td><?= $this->builder->build('select',$yes_no, 'gads',$gads);?></td>
            </tr>
            <tr>
               <td><?=$entry_status;?></td>
               <td><?= $this->builder->build('select',$statuses, 'status',$status);?></td>
            </tr>
          </table>
        </div>
        <div id='tab-related'>
          <table class='form'>
          <tr>
             <td><?= $entry_category; ?></td>
             <td>
                <? $this->builder->set_config('cms_category_id','name');?>
                <?=$this->builder->build_select_list($categories, 'article_category[]',$article_category);?>
             </td>
           </tr>
           <tr>
              <td><?= $entry_store; ?></td>
              <td>
                 <? $this->builder->set_config('store_id','name');?>
                 <?=$this->builder->build_select_list($stores, 'article_store[]',$article_store);?>
              </td>
            </tr>
            <tr>
               <td>
                  <div id="tag-languages" class="htabs">
                     <? foreach ($languages as $language) { ?>
                     <a href="#tag-language<?= $language['language_id']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /> <?= $language['name']; ?></a>
                     <? } ?>
                  </div>
                  <?=$entry_tags;?>
               </td>
               <? foreach ($languages as $language) {?>
               <td id='tag-language<?=$language['language_id'];?>'>
                  <input type='text' name='article_tag[<?=$language['language_id'];?>]' value='<?= isset($article_tag[$language['language_id']]) && $article_tag[$language['language_id']]?implode(',',$article_tag[$language['language_id']]):'';?>' size='80' />
               </td>
               <? }?>
            </tr>
           </table>
        </div>
        <div id="tab-design">
          <table class="list">
            <thead>
              <tr>
                <td class="left"><?= $entry_store; ?></td>
                <td class="left"><?= $entry_layout; ?></td>
              </tr>
            </thead>
            <? $this->builder->set_config('layout_id','name');?>
            <? foreach ($stores as $store) { ?>
            <tbody>
              <tr>
                <td class="left"><?= $store['name']; ?></td>
                <td class="left">
                    <?=$this->builder->build('select',$layouts, "article_layout[$store[store_id]]", (int)isset($article_layout[$store['store_id']])?$article_layout[$store['store_id']]['layout_id']:0);?>
                </td>
              </tr>
            </tbody>
            <? } ?>
          </table>
        </div>
      </form>
    </div>
  </div>
</div>


<?= $this->builder->js('ckeditor');?>

<script type="text/javascript">//<!--
function generate_url_warning(field){
   if($('#gen_warn').length == 0)
      $(field).parent().append('<span id="gen_warn" style="color:red"><?=$warning_generate_url;?></span>');
}
function generate_url(c){
   $(c).fadeOut(500,function(){$(this).show();});
   $('#gen_warn').remove();
   title =$('input[name="title"]').val();
   if(!title)
      alert("Please make a title for this Article before generating the URL");
   $.post('index.php?route=cms/article/generate_url',{title:title},function(json){$('input[name="keyword"]').val(json);},'json');
}
 //--></script>

<script type="text/javascript">//<!--
function image_upload(field, thumb) {
	$('#dialog').remove();
	
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: '<?= $text_image_manager; ?>',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&image=' + encodeURIComponent($('#' + field).val()),
					dataType: 'text',
					success: function(data) {
						$('#' + thumb).replaceWith('<img src="' + data + '" alt="" id="' + thumb + '" />');
					}
				});
			}
		},	
		bgiframe: false,
		width: 800,
		height: 400,
		resizable: false,
		modal: false
	});
};
//--></script>

<script type="text/javascript">//<!--
$('#tabs a').tabs();
$('#languages a').tabs();
$('#tag-languages a').tabs();
//--></script>

<?= $this->builder->js('datepicker');?>
<?= $this->builder->js('errors', $errors);?>

<?= $footer; ?>