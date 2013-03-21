<? if (count($languages) > 1) { ?>
<form action="<?= $action; ?>" method="post" enctype="multipart/form-data">
  <div id="language"><?= $text_language; ?><br />
    <? foreach ($languages as $language) { ?>
    &nbsp;<img src="image/flags/<?= $language['image']; ?>" alt="<?= $language['name']; ?>" title="<?= $language['name']; ?>" onClick="$('input[name=\'language_code\']').attr('value', '<?= $language['code']; ?>').submit(); $(this).parent().parent().submit();" />
    <? } ?>
    <input type="hidden" name="language_code" value="" />
    <input type="hidden" name="redirect" value="<?= $redirect; ?>" />
  </div>
</form>
<? } ?>
