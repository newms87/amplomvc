<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
	<?= $this->breadcrumb->render(); ?>
	<h1><?= $heading_title; ?></h1>
	<h3><?= $text_are_you_a_designer; ?></h3>
	
	<form id='contact_form' action="<?= $action; ?>" method="post" enctype="multipart/form-data">
		<div class="content">
			<b><?= $entry_firstname; ?></b><br />
			<input type="text" name="firstname" value="<?= $firstname; ?>" />
			<br />
			<br />
			<b><?= $entry_lastname; ?></b><br />
			<input type="text" name="lastname" value="<?= $lastname; ?>" />
			<br />
			<br />
			<b><?= $entry_email; ?></b><br />
			<input size='40' type="text" name="email" value="<?= $email; ?>" />
			<br />
			<br />
			<b><?= $entry_phone; ?></b><br />
			<input type="text" name="phone" value="<?= $phone; ?>" />
			<br />
			<br />
			<b><?= $entry_brand; ?></b><br />
			<input type="text" name="brand" value="<?= $brand; ?>" />
			<br />
			<br />
			<b><?= $entry_website; ?></b><br />
			<input size='80' type="text" name="website" value="<?= $website; ?>" />
			<br />
			<br />
			<b><?= $entry_lookbook; ?></b><br />
			<input size='80' type="text" name="lookbook" value="<?= $lookbook; ?>" />
			<br />
			<br />
			<b><?= $entry_description; ?></b><br />
			<textarea name="description" id='description' class='ckedit' cols="40" rows="10" style="width: 99%;"><?= $description; ?></textarea>
			<br />
			<br />
			<div id='category_list'>
					<b><?= $entry_category; ?></b><br />
					
					<? $this->builder->set_config('category_id','name');?>
					
					<? foreach($category as $key=>$cat){?>
						<div class='category_item'>
							<?
							if(strpos($key,'other') === false){
									echo $this->builder->build('select',$categories,'category[]',$cat, array('onchange'=>"check_for_other(this)"));
							} else{
									echo $this->builder->build('select',$categories,'category[]',0, array('onchange'=>"check_for_other(this)", 'other_value'=>$cat));
							} ?>
							<a onclick='$(this).parent().remove()'><?= $button_remove; ?></a>
						</div>
					<? }?>
					<a onclick='add_category(this)'><?= $button_add_category; ?></a>
			</div>
			<br />
			<br />
			<b><?= $entry_captcha; ?></b><br />
			<input type="text" name="captcha" value="<?= $captcha; ?>" />
			<br />
			<img src="<?= $captcha_url; ?>" alt="" />
		</div>
		<div class="buttons">
			<div class="right"><input type="submit" value="<?= $button_submit; ?>" class="button" /></div>
		</div>
	</form>
	
	<h3>You can also follow us on:</h3>
		<table border="0">
	<tbody>
		<tr>
		<td><a title="FaceBook" href="http://www.facebook.com/bettyconfidential" target="_blank"><img title="FaceBook" src="http://images.bettyconfidential.com/img/icons/facebook_52x51.gif" alt="FaceBook" width="52" height="51"></a></td>
		<td><a title="Twitter" href="http://twitter.com/bettybuzz" target="_blank"><img title="Twitter" src="http://images.bettyconfidential.com/img/icons/twitter_52x51.gif" alt="Twitter" width="52" height="51"></a></td>
		<td><a title="YouTube" href="http://www.youtube.com/bettyconfidential" target="_blank"><img title="YouTube" src="http://images.bettyconfidential.com/img/icons/youtube_52x51.gif" alt="YouTube" width="52" height="51"></a></td>
		<td><a title="RSS" href="http://www.bettyconfidential.com/cbc/rss.html"><img title="RSS" src="http://images.bettyconfidential.com/img/icons/rss_52x51.gif" alt="RSS" width="52" height="51"></a></td>
		<td><a title="Apple App Store" href="http://itunes.apple.com/app/bettyconfidential/id342264269?mt=8" target="_blank"><img title="Apple App Store" src="http://s2.bettyconfidential.com/img/icons/iphone_icon_1.gif" alt="Apple App Store" height="51"></a></td>
		<td>&nbsp;</td>
		</tr>
		<tr>
		<td style="text-align: center;"><span style="font-size: x-small;"><a title="FaceBook" href="http://www.bettyconfidential.com/facebook" target="_blank">FaceBook</a></span></td>
		<td style="text-align: center;"><span style="font-size: x-small;"><a title="Twitter" href="http://www.bettyconfidential.com/twitter/bettybuzz" target="_blank">Twitter</a></span></td>
		<td style="text-align: center;"><span style="font-size: x-small;"><a title="YouTube" href="http://www.youtube.com/bettyconfidential" target="_blank">YouTube</a></span></td>
		<td style="text-align: center;"><span style="font-size: x-small;"><a title="RSS" href="/cbc/rss.html">RSS</a></span></td>
		<td style="text-align: center;"><span style="font-size: x-small;"><a title="Apple App Store" href="http://itunes.apple.com/app/bettyconfidential/id342264269?mt=8" target="_blank">App </a></span></td>
		</tr>
	</tbody>
	</table>
		<div class="buttons">
			<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
		</div>
	
	<?= $content_bottom; ?></div>
	
<script type='text/javascript'>//<!--
function add_category(context){
	html =	'<div class="category_item">';
			<? $this->builder->set_config('category_id','name');?>
	html += "	<?= $this->builder->build('select',$categories,'category[]','', array('onchange'=>"check_for_other(this)"),true); ?>";
	html += '	<a onclick="$(this).parent().remove()"><?= $button_remove; ?></a>';
	html += '</div>';
	
	$(context).before(html);
}

function check_for_other(context){
	if($(context).val() === '0' || $(context).val() === 0){
			html = "<span class='other_cat'><span style='margin-left:10px;'>Please Specify: </span><input type='text' name='category[other][]' value='' /></span>";
			$(context).after(html);
	}
	else{
			$(context).siblings('.other_cat').remove();
	}
}

$('[other_value]').each(function(i,e){
	$(e).trigger('change');
	$(e).siblings('.other_cat').find('input').val($(e).attr('other_value'));
});
//--></script>

	<?= $this->builder->js('ckeditor'); ?>
	<?= $this->builder->js('errors',$errors); ?>
<?= $footer; ?>