<? if ($modules || $blocks) { ?>
<div id="column-right">
  <? foreach ($modules as $module) { ?>
  <?= $module; ?>
  <? } ?>
  <? foreach($blocks as $block){ ?>
  	<?= $block;?>
  <? } ?>
</div>
<? } ?>
