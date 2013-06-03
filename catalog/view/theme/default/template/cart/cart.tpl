<?= $header; ?>
<?= $this->builder->display_errors($errors); ?>
<?= $column_left; ?><?= $column_right; ?>
<div class="content"><?= $content_top; ?>
	<h1><?= $heading_title; ?>
	<? if (isset($weight)) { ?>
		<span id='cart_weight'>(<?= $weight; ?>)</span>
	<? } ?>
	</h1>
	<? if($block_cart){?>
		<div class="buttons">
			<div class="right"><a href="<?= $checkout; ?>" class="button"><?= $button_checkout; ?></a></div>
			<div class="center"><a href="<?= $continue; ?>" class="button"><?= $button_shopping; ?></a></div>
		</div>
		
		<?= $block_cart; ?>
		
		<div id='cart_actions'>
			<h2><?= $text_next; ?></h2>
		<? if(isset($block_coupon)){ ?>
			<div>
					<a id='text_block_coupon' onclick="$('#toggle_block_coupon').slideToggle();"><?= $text_use_coupon; ?></a>
					<div id='toggle_block_coupon' class='content'>
						<?= $block_coupon; ?>
					</div>
			</div>
		<? }?>
		
		<? if(isset($block_voucher)){ ?>
			<div>
					<a id='text_block_voucher' onclick="$('#toggle_block_voucher').slideToggle();"><?= $text_use_voucher; ?></a>
					<div id='toggle_block_voucher' class='content'>
						<?= $block_voucher; ?>
					</div>
			</div>
		<? }?>
		
		<? if(isset($block_reward)){ ?>
			<div>
					<a id='text_block_reward' onclick="$('#toggle_block_reward').slideToggle();"><?= $text_use_reward; ?></a>
					<div id='toggle_block_reward' class='content'>
						<?= $block_reward; ?>
					</div>
			</div>
		<? }?>
		
		<? if(isset($block_shipping)){ ?>
			<div>
					<a id='text_block_shipping' onclick="$('#toggle_block_shipping').slideToggle();"><?= $text_use_shipping; ?></a>
					<div id='toggle_block_shipping' class='content'>
						<?= $block_shipping; ?>
					</div>
			</div>
		<? }?>
		</div>
		
		<? if(isset($block_total)){ ?>
				<div id='cart_block_total'>
				<?= $block_total; ?>
				</div>
		<? }?>
		
		<div class="buttons">
			<div class="right"><a href="<?= $checkout; ?>" class="button"><?= $button_checkout; ?></a></div>
			<div class="center"><a href="<?= $continue; ?>" class="button"><?= $button_shopping; ?></a></div>
		</div>
	<? } else {?>
		<h3><?= $text_cart_empty; ?></h3>
		<div class="center"><a href="<?= $continue; ?>" class="button"><?= $button_shopping; ?></a></div>
	<? }?>
	<?= $content_bottom; ?>
</div>
<?= $footer; ?>

<script type="text/javascript">//<!--
<? if(!$show_coupon) {?>
	$('#toggle_block_coupon').hide();
<? }?>
<? if(!$show_voucher) {?>
	$('#toggle_block_voucher').hide();
<? }?>
<? if(!$show_reward) {?>
	$('#toggle_block_reward').hide();
<? }?>
<? if(!$show_shipping) {?>
	$('#toggle_block_shipping').hide();
<? }?>

function handle_ajax_cart_load(action, data){
	load_block($('#cart_block_total'), 'block/cart/total');
}
//--></script>




