<? if ($modules || $blocks) { ?>
<div id="above-content">
  <? foreach ($modules as $module) { ?>
  <?= $module; ?>
  <? } ?>
  <? foreach($blocks as $block){ ?>
  	<?= $block;?>
  <? } ?>
</div>
<? } ?> 
