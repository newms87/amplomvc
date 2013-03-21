<div id="reward" class="content">
   <form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
     <?= $entry_reward; ?>&nbsp;
     <input type="text" name="reward" value="<?= $reward; ?>" />
     <input type="hidden" name="next" value="reward" />
     &nbsp;
     <input type="submit" value="<?= $button_reward; ?>" class="button" />
   </form>
</div>