<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <?= $this->builder->display_errors($errors);?>
  <div class="box">
    <div class="heading">
      <h1><img src="<?= HTTP_THEME_IMAGE . 'category.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
    </div>
    <div class="content">
      <div id="tabs" class="htabs"><a href="#tab-general"><?= $tab_general; ?></a><a href="#tab-data"><?= $tab_data; ?></a><a href="#tab-design"><?= $tab_design; ?></a></div>
      <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-general">
          <div id="languages" class="htabs">
            <? foreach ($languages as $language) { ?>
            <a href="#language<?= $language['language_id']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /> <?= $language['name']; ?></a>
            <? } ?>
          </div>
          <? foreach ($languages as $language) { ?>
          <div id="language<?= $language['language_id']; ?>">
            <table class="form">
              <tr>
                <td><span class="required">*</span> <?= $entry_name; ?></td>
                <td><input type="text" name="category_description[<?= $language['language_id']; ?>][name]" size="100" value="<?= isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['name'] : ''; ?>" /></td>
              </tr>
              <tr>
                <td><?= $entry_page_title; ?></td>
                <td><input type="text" name="category_description[<?= $language['language_id']; ?>][page_title]" size="100" value="<?= isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['page_title'] : ''; ?>" /></td>
              </tr>
              <tr>
                <td><?= $entry_meta_description; ?></td>
                <td><textarea name="category_description[<?= $language['language_id']; ?>][meta_description]" cols="40" rows="5"><?= isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['meta_description'] : ''; ?></textarea></td>
              </tr>
              <tr>
                <td><?= $entry_meta_keyword; ?></td>
                <td><textarea name="category_description[<?= $language['language_id']; ?>][meta_keyword]" cols="40" rows="5"><?= isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea></td>
              </tr>
              <tr>
                <td><?= $entry_description; ?></td>
                <td><textarea name="category_description[<?= $language['language_id']; ?>][description]" id="description<?= $language['language_id']; ?>"><?= isset($category_description[$language['language_id']]) ? $category_description[$language['language_id']]['description'] : ''; ?></textarea></td>
              </tr>
            </table>
          </div>
          <? } ?>
        </div>
        <div id="tab-data">
          <table class="form">
            <tr>
              <td><?= $entry_parent; ?></td>
              <td><?=$this->builder->build('select',$categories, 'parent_id', (int)$parent_id);?></td>
            </tr>
            <tr>
              <td><?= $entry_store; ?></td>
              <td><div class="scrollbox">
                  <? $class = 'even'; ?>
                  <div class="<?= $class; ?>">
                    <? if (in_array(0, $category_store)) { ?>
                    <input type="checkbox" name="category_store[]" value="0" checked="checked" />
                    <?= $text_default; ?>
                    <? } else { ?>
                    <input type="checkbox" name="category_store[]" value="0" />
                    <?= $text_default; ?>
                    <? } ?>
                  </div>
                  <? foreach ($stores as $store) { ?>
                  <? $class = ($class == 'even' ? 'odd' : 'even'); ?>
                  <div class="<?= $class; ?>">
                    <? if (in_array($store['store_id'], $category_store)) { ?>
                    <input type="checkbox" name="category_store[]" value="<?= $store['store_id']; ?>" checked="checked" />
                    <?= $store['name']; ?>
                    <? } else { ?>
                    <input type="checkbox" name="category_store[]" value="<?= $store['store_id']; ?>" />
                    <?= $store['name']; ?>
                    <? } ?>
                  </div>
                  <? } ?>
                </div></td>
            </tr>
            <tr>
              <td><?= $entry_keyword; ?></td>
              <td>
                 <input type="text" onfocus='generate_url_warning(this)' name="keyword" value="<?= $keyword; ?>" />
                 <a class='gen_url' onclick='generate_url(this)'><?=$button_generate_url;?></a>
              </td>
            </tr>
            <tr>
              <td><?= $entry_image; ?></td>
              <td valign="top"><div class="image"><img src="<?= $thumb; ?>" alt="" id="thumb" />
                <input type="hidden" name="image" value="<?= $image; ?>" id="image" />
                <br /><a onclick="image_upload('image', 'thumb');"><?= $text_browse; ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="$('#thumb').attr('src', '<?= $no_image; ?>'); $('#image').attr('value', '');"><?= $text_clear; ?></a></div></td>
            </tr>
            <tr>
              <td><?= $entry_sort_order; ?></td>
              <td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1" /></td>
            </tr>
            <tr>
              <td><?= $entry_status; ?></td>
              <td><?=$this->builder->build('select',$statuses, 'status', (int)$status);?></td>
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
            <tbody>
              <tr>
                <td class="left"><?= $text_default; ?></td>
                <td class="left">
                   <?=$this->builder->build('select',$layouts, 'category_layout[0][layout_id]', isset($category_layout[0])?(int)$category_layout[0]:'');?>
                 </td>
              </tr>
            </tbody>
            <? foreach ($stores as $store) { ?>
            <tbody>
              <tr>
                <td class="left"><?= $store['name']; ?></td>
                <td class="left">
                    <?=$this->builder->build('select',$layouts, "category_layout[$store[store_id]][layout_id]", isset($category_layout[$store['store_id']])?(int)$category_layout[$store['store_id']]:'');?>
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

<?=$this->builder->js('ckeditor');?>
<script type="text/javascript">//<!--
<? foreach ($languages as $language) { ?>
   init_ckeditor_for('description<?=$language['language_id'];?>');
<? } ?>
//--></script>
<script type="text/javascript">//<!--
function generate_url_warning(field){
   if($('#gen_warn').length == 0)
      $(field).parent().append('<span id="gen_warn" style="color:red"><?=$warning_generate_url;?></span>');
}
function generate_url(c){
   $(c).fadeOut(500,function(){$(this).show();});
   $('#gen_warn').remove();
   name =$('input[name="category_description[1][name]"]').val();
   if(!name)
      alert("Please make a name for this Category before generating the URL");
   $.post('index.php?route=catalog/category/generate_url',{name:name},function(json){$('input[name="keyword"]').val(json);},'json');
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
//--></script>

<?=$this->builder->js('errors',$errors);?>
<?= $footer; ?>