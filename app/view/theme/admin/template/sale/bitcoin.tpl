<?= '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?= _l("Bitcoin Payments"); ?></title>

		<?= $styles; ?>
		<?= $scripts; ?>

	</head>
	<body>
		<div id="container">

			<div class="content">
				<div class="form">
					<label for="send_amount">Total Bill:</label>
					<input type="text" name="amount" value=".0004"/>

					<div id="tip_amount">
						<input id="tip_10" type="radio" class="tip" name="tip" value="10"><label for="tip_10">10%</label><br/>
						<input id="tip_15" type="radio" class="tip" name="tip" value="15"><label for="tip_15">15%</label><br/>
						<input id="tip_20" type="radio" class="tip" name="tip" checked="checked" value="20"><label for="tip_20">20%</label><br/>
						<input id="other" type="radio" class="tip" name="tip" value="other">BTC: <input type="text" id="tip_other" value=""/>
					</div>

					<a class="button" onclick="generate_qr();">Generate QR Code</a>
				</div>


				<br/>
				<br/>
				<br/>

				<div id="total_due">
					<div>Total: <span id="total_bill"></span></div>
					<div>Tip: <span id="total_tip"></span></div>
					<div>Grand Total: <span id="grand_total"></span></div>
				</div>
				<br/>
				<br/>
				<br/>

				<div id="qr_code"></div>
				<div id="string_code"></div>
			</div>
		</div>

		<script type="text/javascript">
			function generate_qr() {
				var total = parseFloat($('[name=amount]').val());
				var tip = $('.tip:checked');

				if (!tip.length) {
					tip = 0;
				}
				else if (tip.val() == 'other') {
					tip = parseFloat($("#tip_other").val());
				} else {
					tip = total * parseInt(tip.val()) / 100;
				}

				var grand_total = total + tip;

				var string = "bitcoin:<?= $bitcoin_address; ?>?amount=" + grand_total.toFixed(8);
				$('#qr_code').html('').qrcode(string);
				$('#string_code').html(string);

				$('#total_bill').html(total);
				$('#total_tip').html(tip);
				$('#grand_total').html(grand_total);
			}
		</script>
	</body>
</html>
