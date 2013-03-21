<div id='press_entries' class="box">
  <h1><?= $heading_title; ?></h1>
  <div class="box-content">
    <ul id='press-list'>
       <? foreach($press_list as $press){ ?>
       	<li>
       		<img src="<?= $press['thumb'];?>" />
       		<div class="press-item">
       			<div class="press-description">
       				<?= $press['description'];?>
       			</div>
       		</div>
       	</li>
		 <? } ?>
    </ul>
  </div>
</div>
