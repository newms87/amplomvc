<a href='<?=$flashsale_link;?>' class='countdown_clock_link'>
	<div id='product_countdown'>
			<img src='<?=$flashsale_image;?>' />
			<span class='message'>
				<span class='before_msg_start'><?= $text_time_left;?></span>
				<div class='flash_countdown' id='designer-top-countdown' callback='display_sale_ended' msg_start='in' flashid='<?=$flashsale_id;?>'></div>
			</span>
	</div>
</a>

<script type='text/javascript'>//<!--
function display_sale_ended(context, op){
	if(op == 'ended'){
			$('#product_countdown .message').html("<span class='before_msg_start'>this sale has ended</span>");
			old = $('.retail');
			if(old.length > 0)
				$('.special').html(old.html().replace(/retail/,''));
			old.remove();
	}
}
//--></script>