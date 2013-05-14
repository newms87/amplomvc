<? if ($error_warning) { ?>
<div class="message_box warning"><?= $error_warning; ?></div>
<? } ?>
<? if ($success) { ?>
<div class="message_box success"><?= $success; ?></div>
<? } ?>
<table class="list">
  <thead>
    <tr>
      <td class="left"><b><?= $column_date_added; ?></b></td>
      <td class="left"><b><?= $column_comment; ?></b></td>
      <td class="left"><b><?= $column_status; ?></b></td>
      <td class="left"><b><?= $column_notify; ?></b></td>
    </tr>
  </thead>
  <tbody>
    <? if ($histories) { ?>
    <? foreach ($histories as $history) { ?>
    <tr>
      <td class="left"><?= $history['date_added']; ?></td>
      <td class="left"><?= $history['comment']; ?></td>
      <td class="left"><?= $history['status']; ?></td>
      <td class="left"><?= $history['notify']; ?></td>
    </tr>
    <? } ?>
    <? } else { ?>
    <tr>
      <td class="center" colspan="4"><?= $text_no_results; ?></td>
    </tr>
    <? } ?>
  </tbody>
</table>
<div class="pagination"><?= $pagination; ?></div>
