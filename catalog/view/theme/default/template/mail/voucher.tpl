<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?= _l("Gift Voucher"); ?></title>
	</head>
	<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
		<div style="width: 680px;">
			<div style="float: right; margin-left: 20px;">
				<a href="<?= $store_url; ?>" title="<?= $store_name; ?>">
					<img width="<?= $image_width; ?>" height="<?= $image_height; ?>" src="<?= $image; ?>" alt="<?= $store_name; ?>"
						style="margin-bottom: 20px; border: none;"/>
				</a>
			</div>
			<div>
				<p style="margin-top: 0px; margin-bottom: 20px;"><?= _l("Congratulations, You have received a Gift Certificate!"); ?></p>

				<p style="margin-top: 0px; margin-bottom: 20px;"><?= _l("This Gift Certificate has been sent to you by %s", $from_name); ?></p>
				<? if ($message) { ?>
					<p style="margin-top: 0px; margin-bottom: 20px;"><?= $message; ?></p>
				<? } ?>
				<div style="margin-top: 0px; margin-bottom: 20px;">
					<span style="font-size: 16px;"><?= _l("Voucher Value:"); ?></span>
					<span style="font-size: 20px; color: green;"><?= $amount; ?></span>
				</div>
				<div style="margin-top: 0px; margin-bottom: 20px;">
					<span style="font-size: 16px;"><?= _l("Voucher Code:"); ?></span>
					<span style="font-size: 20px; font-weight: bold;"><?= $code; ?></span>
				</div>

				<p style="margin-top: 0px; margin-bottom: 20px;">
					<?= _l("To redeem this Voucher, visit"); ?>
					<a href="<?= $redeem_url; ?>"><?= $store_name; ?></a>
					<?= _l(", find something you like and proceed to checkout. During the checkout you may enter the voucher code."); ?>
				</p>

				<p style="margin-top: 0px; margin-bottom: 20px;"><a href="<?= $store_url; ?>" title="<?= $store_name; ?>"><?= $store_url; ?></a></p>

				<p style="margin-top: 0px; margin-bottom: 20px;"><?= _l("Please reply to this email if you have any questions."); ?></p>
			</div>
		</div>
	</body>
</html>
