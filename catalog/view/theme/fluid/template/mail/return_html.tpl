<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=320, target-densitydpi=device-dpi">
	</head>
	<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
		<div style="width: 680px;">
			<a href="<?= $store['url']; ?>" title="<?= $store['name']; ?>">
				<img src="<?= $logo; ?>" alt="<?= $store['name']; ?>" style="margin-bottom: 20px; border: none;"/>
			</a>

			<p style="margin-top: 0px; margin-bottom: 20px;">
				<?= _l("We have received your return request. Please do not ship your product(s) back to us until we confirm your request. We will notify you when your product is eligible for return."); ?>
			</p>
			<p>
				<?= _l("Your RMA # to reference this return transaction:"); ?><br />
				<? foreach ($rmas as $rma) { ?>
					<span style="font-size: 14px;font-weight:bold;margin-left: 10px;"><?= $rma; ?></span><br />
				<? } ?>
			</p>

			<p style="margin-top: 0px; margin-bottom: 20px;"><?= _l("Please reply to this email if you have any questions."); ?></p>
		</div>
	</body>
</html>
