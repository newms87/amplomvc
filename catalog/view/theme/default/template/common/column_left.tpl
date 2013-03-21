<? if ($modules || $blocks) { ?>
<div id="column-left">
  <? foreach ($modules as $module) { ?>
  <?= $module; ?>
  <? } ?>
  
  <? foreach($blocks as $block){ ?>
  	<?= $block;?>
  <? } ?>
</div>
<? } ?>
