<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content">
	<?= $this->builder->display_breadcrumbs();?>
	<h1><?= $heading_title;?></h1>
	<?= $content_top; ?>
	<? if(empty($designers)){?>
			<div class="content"><?= $no_designers_text;?></div>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
		</div>
	<? }else{ ?>
			<div id='sort_by'><span><?=$text_sort_by;?></span><?=$this->builder->build('select',$sort_list, '');?></div>
			<div id='designer_list'>
			<? foreach($designers as $d){?>
				<a	href='<?= $d['href'];?>' name='<?=$d['name'];?>' time='<?=$d['flashsale']['date_end'];?>' style='display:block;text-decoration:none!important'>
				<div class='flashsale_item'>
						<img src='<?= $polaroid;?>' class='polaroid_front' />
						<div class='fs_item'>
							<img class='fs_image' src='<?= $d['image'];?>' />
							<div class='fs_info'>
									<div class='fs_title'><?=$d['name'];?></div>
									<? if($d['flashsale']){?>
									<div class='fs_countdown'><div class='flash_countdown' id='designer-<?=$d['designer_id'];?>' flashid='<?=$d['flashsale']['flashsale_id'];?>'></div></div>
									<? }?>
							</div>
						</div>
						<img class='fs_tac' src='<?=$fs_tac;?>' />
				</div>
				</a>
			<? } ?>
			</div>
	<? } ?>
	<?= $content_bottom;?>
</div>
<?= $footer; ?>


<script type="text/javascript">
// <!--
jQuery.fn.sortElements = (function(){
		var sort = [].sort;
		return function(comparator, getSortable) {
				getSortable = getSortable || function(){return this;};
				var placements = this.map(function(){
						var sortElement = getSortable.call(this),
								parentNode = sortElement.parentNode,
								nextSibling = parentNode.insertBefore(
										document.createTextNode(''),
										sortElement.nextSibling
								);
						return function() {
								if (parentNode === this) {
										throw new Error(
												"You can't sort elements if any one is a descendant of another."
										);
								}
								parentNode.insertBefore(this, nextSibling);
								parentNode.removeChild(nextSibling);
						};
				});
				return sort.call(this, comparator).each(function(i){
						placements[i].call(getSortable.call(this));
				});
		};
})();
// -->
</script>

<script type="text/javascript">
// <!--
$(document).ready(function(){
	$('#sort_by select').change(function(){
			switch($(this).val()){
				case 'a-z':
						$('#designer_list a').sortElements(function(a,b){ return $(a).attr('name') > $(b).attr('name')?1:-1;});
						break;
				case 'z-a':
						$('#designer_list a').sortElements(function(a,b){ return $(a).attr('name') < $(b).attr('name')?1:-1;});
						break;
				case 'ending_soon':
						$('#designer_list a').sortElements(function(a,b){ max=100000000*86400000; return (new Date($(a).attr('time') || null).getTime() || max) > (new Date($(b).attr('time') || null).getTime() || max)?1:-1;});
						break;
				default:
						return;
			}
	});
});
// -->
</script>