<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'customer.png'; ?>" alt=""/> <?= _l("Product Returns"); ?></h1>

			<div class="buttons"><a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<div class="vtabs"><a href="#tab-return"><?= $tab_return; ?></a><a
					href="#tab-product"><?= $tab_product; ?></a><a href="#tab-history"><?= $tab_return_history; ?></a>
			</div>
			<div id="tab-return" class="vtabs-content">
				<table class="form">
					<tr>
						<td><?= _l("Return ID:"); ?></td>
						<td><?= $return_id; ?></td>
					</tr>
					<? if ($order) { ?>
						<tr>
							<td><?= _l("Order ID:"); ?></td>
							<td><a href="<?= $order; ?>"><?= $order_id; ?></a></td>
						</tr>
					<? } else { ?>
						<tr>
							<td><?= _l("Order ID:"); ?></td>
							<td><?= $order_id; ?></td>
						</tr>
					<? } ?>
					<tr>
						<td><?= _l("Order Date:"); ?></td>
						<td><?= $date_ordered; ?></td>
					</tr>
					<? if ($customer) { ?>
						<tr>
							<td><?= _l("Customer:"); ?></td>
							<td><a href="<?= $customer; ?>"><?= $firstname; ?> <?= $lastname; ?></a></td>
						</tr>
					<? } else { ?>
						<tr>
							<td><?= _l("Customer:"); ?></td>
							<td><?= $firstname; ?> <?= $lastname; ?></td>
						</tr>
					<? } ?>
					<tr>
						<td><?= _l("E-Mail:"); ?></td>
						<td><?= $email; ?></td>
					</tr>
					<tr>
						<td><?= _l("Telephone:"); ?></td>
						<td><?= $telephone; ?></td>
					</tr>
					<? if ($return_status) { ?>
						<tr>
							<td><?= _l("Return Status:"); ?></td>
							<td id="return-status"><?= $return_status; ?></td>
						</tr>
					<? } ?>
					<tr>
						<td><?= _l("Date Added:"); ?></td>
						<td><?= $date_added; ?></td>
					</tr>
					<tr>
						<td><?= _l("Date Modified:"); ?></td>
						<td><?= $date_modified; ?></td>
					</tr>
				</table>
			</div>
			<div id="tab-product" class="vtabs-content">
				<table class="form">
					<tr>
						<td><?= _l("Product:"); ?></td>
						<td><?= $subscription; ?></td>
					</tr>
					<tr>
						<td><?= _l("Model:"); ?></td>
						<td><?= $model; ?></td>
					</tr>
					<tr>
						<td><?= _l("Quantity:"); ?></td>
						<td><?= $quantity; ?></td>
					</tr>
					<tr>
						<td><?= _l("Return Reason:"); ?></td>
						<td><?= $return_reason; ?></td>
					</tr>
					<tr>
						<td><?= _l("Opened"); ?></td>
						<td><?= $opened; ?></td>
					</tr>
					<tr>
						<td><?= _l("Return Action:"); ?></td>
						<td><select name="return_action_id">
								<option value="0"></option>
								<? foreach ($return_actions as $return_action) { ?>
									<? if ($return_action['return_action_id'] == $return_action_id) { ?>
										<option value="<?= $return_action['return_action_id']; ?>"
											selected="selected"><?= $return_action['name']; ?></option>
									<? } else { ?>
										<option value="<?= $return_action['return_action_id']; ?>"><?= $return_action['name']; ?></option>
									<? } ?>
								<? } ?>
							</select></td>
					</tr>
					<? if ($comment) { ?>
						<tr>
							<td><?= _l("Comment:"); ?></td>
							<td><?= $comment; ?></td>
						</tr>
					<? } ?>
				</table>
			</div>
			<div id="tab-history" class="vtabs-content">
				<div id="history"></div>
				<table class="form">
					<tr>
						<td><?= _l("Return Status:"); ?></td>
						<td>
						<td><?= $this->builder->build('select', $data_return_statuses, 'return_status_id', $return_status_id); ?></td>
					</tr>
					<tr>
						<td><?= _l("Notify Customer:"); ?></td>
						<td><input type="checkbox" name="notify" value="1"/></td>
					</tr>
					<tr>
						<td><?= _l("Comment:"); ?></td>
						<td><textarea name="comment" cols="40" rows="8" style="width: 99%"></textarea>

							<div style="margin-top: 10px; text-align: right;"><a onclick="history();" id="button-history"
									class="button"><?= $button_add_history; ?></a>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript"><
	!--
		$('select[name=\'return_action_id\']').bind('change', function () {
			$.ajax({
				url: "<?= HTTP_ADMIN . "index.php?route=sale/return/action"; ?>" + '&return_id=<?= $return_id; ?>',
				type: 'post',
				dataType: 'json',
				data: 'return_action_id=" + this.value,
				beforeSend: function () {
					$(".success, .warning, .attention').remove();

					$('.box').before('<div class="attention"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> <?= _l("Please Wait!"); ?></div>');
				},
				success: function (json) {
					$('.success, .warning, .attention').remove();

					if (json['error']) {
						$('.box').before('<div class="message_box warning" style="display: none;">' + json['error'] + '</div>');

						$('.warning').fadeIn('slow');
					}

					if (json['success']) {
						$('.box').before('<div class="message_box success" style="display: none;">' + json['success'] + '</div>');

						$('.success').fadeIn('slow');
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		});

	$('#history .pagination a').live('click', function () {
		$('#history').load(this.href);

		return false;
	});

	$('#history').load("<?= HTTP_ADMIN . "index.php?route=sale/return/history"; ?>" + '&return_id=<?= $return_id; ?>');

	function history() {
		$.ajax({
			url: "<?= HTTP_ADMIN . "index.php?route=sale/return/history"; ?>" + '&return_id=<?= $return_id; ?>',
			type: 'post',
			dataType: 'html',
			data: 'return_status_id=" + encodeURIComponent($("select[name=\'return_status_id\']').val()
	)
		+'&notify=" + encodeURIComponent($("input[name=\'notify\']'
	).
		attr('checked') ? 1 : 0
	)
		+'&append=" + encodeURIComponent($("input[name=\'append\']'
	).
		attr('checked') ? 1 : 0
	)
		+'&comment=" + encodeURIComponent($("textarea[name=\'comment\']'
	).
		val()
	),
		beforeSend: function () {
			$('.success, .warning').remove();
			$('#button-history').attr('disabled', true);
			$('#history').prepend('<div class="attention"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> <?= _l("Please Wait!"); ?></div>');
		}
	,
		complete: function () {
			$('#button-history').attr('disabled', false);
			$('.attention').remove();
		}
	,
		success: function (html) {
			$('#history').html(html);

			$('textarea[name=\'comment\']').val('');

			$('#return-status').html($('select[name=\'return_status_id\'] option:selected').text());
		}
	}
	)
	;
	}
</script>
<script type="text/javascript"><
	!--
		$('.vtabs a').tabs();
</script>
<?= $footer; ?>
