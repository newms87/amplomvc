<div id='press_entries' class="box">
	<h1><?= $heading_title; ?></h1>
	<div class="box-content">
		<ul id='press_list'>
		<? foreach($press_list as $press){ ?>
			<li>
				<div class="press_item">
					<a <?= !empty($press['href']) ? "href=\"$press[href]\"" : ''; ?> class="press_title">
						<? if (!empty($press['description'])) { ?>
						<span class="description"><?= $press['description']; ?></span>, 
						<? } ?>
						<? if(!empty($press['author'])) { ?> 
						<span class="author"><?= $press['author']; ?></span>, 
						<? } ?>
						<? if(!empty($press['date'])) { ?>
						<span class="date"><?= $press['date']; ?></span>
						<? } ?>
					</a>
					<? if(!empty($press['images'])) { ?>
					<div class="press_images">
						<? foreach($press['images'] as $image) { ?>
							<? if($press['href']) { ?><a href="<?= $press['href']; ?>"><? } ?>
							<img src="<?= $image; ?>" />
							<? if($press['href']) { ?></a><? } ?>
						<? } ?>
					</div>
					<? } ?>
				</div>
			</li>
		<? } ?>
		</ul>
	</div>
</div>
