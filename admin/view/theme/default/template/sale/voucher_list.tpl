<?= $header; ?>
<div class="content">
  <?= $this->builder->display_breadcrumbs();?>
  <? if ($error_warning) { ?>
  <div class="message_box warning"><?= $error_warning; ?></div>
  <? } ?>
  <? if ($success) { ?>
  <div class="message_box success"><?= $success; ?></div>
  <? } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="<?= HTTP_THEME_IMAGE . 'payment.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
      <div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="document.getElementById('form').submit();" class="button"><?= $button_delete; ?></a></div>
    </div>
    <div class="content">
      <form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="list">
          <thead>
            <tr>
              <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
              <td class="left"><? if ($sort == 'v.code') { ?>
                <a href="<?= $sort_code; ?>" class="<?= strtolower($order); ?>"><?= $column_code; ?></a>
                <? } else { ?>
                <a href="<?= $sort_code; ?>"><?= $column_code; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'v.from_name') { ?>
                <a href="<?= $sort_from; ?>" class="<?= strtolower($order); ?>"><?= $column_from; ?></a>
                <? } else { ?>
                <a href="<?= $sort_from; ?>"><?= $column_from; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'v.to_name') { ?>
                <a href="<?= $sort_to; ?>" class="<?= strtolower($order); ?>"><?= $column_to; ?></a>
                <? } else { ?>
                <a href="<?= $sort_to; ?>"><?= $column_to; ?></a>
                <? } ?></td>
              <td class="right"><? if ($sort == 'v.amount') { ?>
                <a href="<?= $sort_amount; ?>" class="<?= strtolower($order); ?>"><?= $column_amount; ?></a>
                <? } else { ?>
                <a href="<?= $sort_amount; ?>"><?= $column_amount; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'theme') { ?>
                <a href="<?= $sort_theme; ?>" class="<?= strtolower($order); ?>"><?= $column_theme; ?></a>
                <? } else { ?>
                <a href="<?= $sort_theme; ?>"><?= $column_theme; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'v.status') { ?>
                <a href="<?= $sort_status; ?>" class="<?= strtolower($order); ?>"><?= $column_status; ?></a>
                <? } else { ?>
                <a href="<?= $sort_status; ?>"><?= $column_status; ?></a>
                <? } ?></td>
              <td class="left"><? if ($sort == 'v.date_added') { ?>
                <a href="<?= $sort_date_added; ?>" class="<?= strtolower($order); ?>"><?= $column_date_added; ?></a>
                <? } else { ?>
                <a href="<?= $sort_date_added; ?>"><?= $column_date_added; ?></a>
                <? } ?></td>
              <td class="right"><?= $column_action; ?></td>
            </tr>
          </thead>
          <tbody>
            <? if ($vouchers) { ?>
            <? foreach ($vouchers as $voucher) { ?>
            <tr>
              <td style="text-align: center;"><? if ($voucher['selected']) { ?>
                <input type="checkbox" name="selected[]" value="<?= $voucher['voucher_id']; ?>" checked="checked" />
                <? } else { ?>
                <input type="checkbox" name="selected[]" value="<?= $voucher['voucher_id']; ?>" />
                <? } ?></td>
              <td class="left"><?= $voucher['code']; ?></td>
              <td class="left"><?= $voucher['from']; ?></td>
              <td class="left"><?= $voucher['to']; ?></td>
              <td class="right"><?= $voucher['amount']; ?></td>
              <td class="left"><?= $voucher['theme']; ?></td>
              <td class="left"><?= $voucher['status']; ?></td>
              <td class="left"><?= $voucher['date_added']; ?></td>
              <td class="right">[ <a onclick="sendVoucher('<?= $voucher['voucher_id']; ?>');"><?= $text_send; ?></a> ]
                <? foreach ($voucher['action'] as $action) { ?>
                [ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
                <? } ?></td>
            </tr>
            <? } ?>
            <? } else { ?>
            <tr>
              <td class="center" colspan="9"><?= $text_no_results; ?></td>
            </tr>
            <? } ?>
          </tbody>
        </table>
      </form>
      <div class="pagination"><?= $pagination; ?></div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
function sendVoucher(voucher_id) {
	$.ajax({
		url: "<?= HTTP_ADMIN . "index.php?route=sale/voucher/send"; ?>" + '&voucher_id=' + voucher_id,
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('.success, .warning').remove();
			$('.box').before('<div class="attention"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> <?= $text_wait; ?></div>');
		},
		complete: function() {
			$('.attention').remove();
		},
		success: function(json) {
			if (json['error']) {
				$('.box').before('<div class="message_box warning">' + json['error'] + '</div>');
			}
			
			if (json['success']) {
				$('.box').before('<div class="message_box success">' + json['success'] + '</div>');
			}		
		}
	});
}
//--></script> 
<?= $footer; ?>