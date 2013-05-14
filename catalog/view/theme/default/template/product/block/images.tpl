<? if ($thumb || $images) { ?>
<div class="left">
  <? if ($thumb) { ?>
  <div id='the_zoombox' class="image"><a href="<?= $popup; ?>" title="<?= $heading_title; ?>" class="zoombox" rel='gal1'><img src="<?= $thumb; ?>" title="<?= $heading_title; ?>" alt="<?= $heading_title; ?>" id="image" /></a></div>
  <? } ?>
  <? if ($images) { ?>
  <div class="image-additional">
    <? foreach ($images as $image) { ?>
    <a href="javscript:void(0);" title="<?= $heading_title; ?>" rel="<?=$image['rel'];?>" ><img src="<?= $image['thumb']; ?>" title="<?= $heading_title; ?>" alt="<?= $heading_title; ?>" /></a>
    <? } ?>
  </div>
  <? } ?>
</div>
<? } ?>


<script type="text/javascript" src="<?= HTTP_THEME_JS . "jqzoom/js/jqzoom-core-min.js"; ?>"></script>
<script type="text/javascript">//<!--
$(document).ready(function(){
   $('.image-additional a img, .option_image a img').click(function(){
      if($(this).attr('src').replace(/-\d+x\d+/,'') == $('#the_zoombox .zoomPad > img').attr('src').replace(/-\d+x\d+/,'')){
         event.preventDefault();
         return false;
      }
   });
   $('.zoombox').jqzoom({zoomWidth:<?=$zoombox_width;?>,zoomHeight:<?=$zoombox_height;?>,position:'<?=$zoombox_position;?>',xOffset:<?=$zoombox_x;?>, yOffset:<?=$zoombox_y;?>,preloadText:'<?=$text_zoombox_load;?>'});
   
   <? //TODO: Make the zoombox have option to click for popup colorbox. ;?>
   
   //$('#the_zoombox').colorbox({});
   //$('#the_zoombox').click(function(){console.log('hmm');});
});
//--></script>