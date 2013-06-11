<div class="box featured_flashsale">
	<ul class='dncarousel'>
			<? foreach($flashsales as $fs){?>
				<li>
				<a	href='<?= $fs['href']; ?>' style='display:block;text-decoration:none!important'>
				<div class='flashsale_item'>
						<img src='<?= $polaroid; ?>' class='polaroid_front' />
						<div class='fs_item'>
							<img class='fs_image' src='<?= $fs['image']; ?>' />
							<div class='fs_info'>
									<div class='fs_title'><?= $fs['name']; ?></div>
									<div class='fs_teaser'><?= $fs['teaser']; ?></div>
									<div class='fs_countdown'><div class='flash_countdown' id='flash-<?= $flashid; ?>'flashid='<?= $fs['flashsale_id']; ?>'></div></div>
							</div>
						</div>
						<img class='fs_tac' src='<?= $fs_tac; ?>' />
				</div>
				</a>
				</li>
			<? } ?>
	</ul>
</div>
<script type='text/javascript' src='catalog/view/javascript/dncarousel.js'></script>
<script type='text/javascript'>
//<!--
$(document).ready(function(){
	$('.dncarousel').dncarousel({display:3,scroll:1, page_spacing:5, intervaltime:3000, interval:true});
});
//--></script>