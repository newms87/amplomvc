<?= $header .$column_left .$column_right; ?>
<div id='content'>
	<?= $this->builder->display_breadcrumbs(); ?>
	<?= $content_top; ?>
<? if(!isset($section_products)){?>
	<div class="content">
			<h1><?= $heading_title; ?></h1>
			<?= $no_product_text; ?>
	</div>
	<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
	</div>
<? }else{?>
		<div id='designer_heading'>
		<div class='left'>
				<img src='<?= $designer_image; ?>' />
		</div>
		<div class='right'>
				<? if(isset($flashsale_id)){?>
				<a href='<?= $flashsale_link; ?>' class='countdown_clock_link'><div id='designer_countdown'><img src='<?= $flashsale_clock; ?>' /><span class='message'><span class='before_msg_start'>sale ends</span><div class='flash_countdown' id='designer-top-countdown' callback='display_sale_ended' msg_start='in' flashid='<?= $flashsale_id; ?>'></div></span></div></a>
				<? }?>
				<h1><?= $heading_title; ?></h1>
				<div class='description'><?= $description; ?></div>
				<? if($share_status) {?>
				<div id='share_block'>
					<div class="share">
							<!-- AddThis Button BEGIN -->
						<div class="addthis_toolbox addthis_default_style ">
							<a class="addthis_button_pinterest_pinit"></a>
							<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
							<a class="addthis_button_tweet"></a>
							<a class="addthis_button_tumblr"></a>
							<a class="addthis_counter addthis_pill_style"></a>
							</div>
							<script type="text/javascript">//<!--
						$(document).ready(function(){
							$('.addthis_button_tumblr').append("<img src='<?= HTTPS_IMAGE . 'data/tumblr_pill.png'; ?>' />");
							$.getScript("http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4febc0f66808d769");
						});
					//--></script>
							<!-- AddThis Button END -->
					</div>
				</div>
				<? }?>
		</div>
	</div>
	<div id='sort_product'>
			<?= $this->builder->build('select',$sort_list,'sort', $d_sort_by, array('onchange'=>"update_sort_url()")); ?>
			<a h='<?= $sort_url; ?>' id='sort_go' class='button'>Go</a>
	</div>
	<ul id='section_nav'>
			<?
			if(count($section_products) > 1){
				foreach($section_products as $section_id=>$section){
						$onclick = "show_section($section_id);";
						echo "<li><a id='section_filter_$section_id' filter='$section_id' onclick=\"$onclick\">$section[section_name]</a></li>";
				}
			}
			?>
	</ul>
<? $count = 1; foreach($section_products as $section_id=>$section){?>
			<? //Display empty product sections at top for when sorting and filtering
						//we can still display section headings
			if(count($section['products'])==0){?>
			<div id='section-<?= $section_id; ?>' class='product_section hide_on_all'>
				<div class='product_section_title'><?= $section['section_name']; ?></div>
			</div>
			<script type='text/javascript'>$('#section-0').before($('#section-<?= $section_id; ?>'));</script>
			<? continue; }?>
			
			<div id='section-<?= $section_id; ?>' class='product_section'>
			<? if($section_id!=0){?>
				<div class='product_section_title'><?= $section['section_name']; ?></div>
			<? }?>
			
			<? foreach($section['products'] as $product){?>
				<? if(isset($product['article_id'])){ ?>
						<? extract($product);?>
						<a class='designer_article_details' href='<?= $link; ?>' target='_blank' type='<?= $section_id; ?>'>
							<img class='open_quote' src='<?= $open_quote; ?>' />
							
							<div class='designer_article'>
									<div class='designer_article_teaser'><?= $description; ?><span class='read_more'>read more</span></div>
							</div>
							<img class='close_quote' src='<?= $close_quote; ?>' />
							<div style='clear:both'></div>
						</a>
				<? } else {?>
				<? extract($product);?>
				<a class='product_details' href='<?= $href; ?>' type='<?= $product['section_id']; ?>'>
						<div class='product_images'>
							<img src='<?= $thumb; ?>' alt='<?= $name; ?>' />
						</div>
						<div class='product_info'>
							<div class='product_info_title'><?= $name; ?></div>
							<? if($special){ ?>
									<div class='product_info_price'><?= $special; ?></div>
									<div class='product_info_orig_price'><?= $price; ?> retail</div>
							<? }else{?>
									<div class='product_info_price'><?= $price; ?></div>
							<? }?>
							<? if(isset($flashsale_id) && $flashsale_id > 0){?>
									<div class='fs_countdown'><div class='flash_countdown' id='designer-prod-<?= $product_id; ?>' callback='end_product_sale' flashid='<?= $flashsale_id; ?>'></div></div>
							<? }?>
						</div>
						<div style='clear:both'></div>
				</a>
				<? }?>
			<? }?>
	</div>
	<? if($count++ < count($section_products)-1){?>
			<div class='section_split'></div>
	<? }?>
<? }} ?>
<?= $content_bottom; ?>
</div>


<script type='text/javascript'>//<!--
$(document).ready(function(){
	update_sort_url();
	<? if(isset($filter)) { ?>
	show_section(<?= $filter; ?>);
	<? } ?>
});
function update_sort_url(){
	filter = $('#section_nav .active').attr('filter') || false;
	filter = filter? '&filter='+filter:'';
	$('#sort_go').attr('href',$('#sort_go').attr('h') + '?d_sort_by=' + encodeURIComponent($('#sort_product select').val()) + filter)
}
function show_section(id){
	context = $('#section_filter_'+id);
	$('#section_nav a').removeClass('active');
	$(context).addClass('active');
	if(id == 0){
			$('.product_details').show();
			$('.product_section').show();
			$('.hide_on_all').hide();
			$('.section_split').show();
	}
	else{
			$('.product_details').hide();
			$('.product_section').hide();
			$('#section-0').show();
			$('#section-'+id).show();
			$('.product_details[type="'+id+'"]').show();
			$('.section_split').hide();
	}
	
	update_sort_url();
}

function end_product_sale(context, op){
	if(op == 'ended'){
			p = context.closest('.product_info');
			orig = p.find('.product_info_orig_price');
			if(orig.length > 0)
				p.find('.product_info_price').html(orig.html().replace(/retail/,''));
			orig.remove();
			p.find('.fs_countdown').remove();
	}
}
function display_sale_ended(context, op){
	if(op == 'ended')
			$('#designer_countdown .message').html("<span class='before_msg_start'>this sale has ended</span>");
}
--></script>
 
<?= $footer; ?>
