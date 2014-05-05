<html dir="ltr" lang="en">
	<head>
		<title><?= _l("Shipping / Return Policies"); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	</head>
	<body>
		<? if (!empty($shipping_policy)) { ?>
			<h1><?= $shipping_policy['title']; ?></h1>
			<p><?= $shipping_policy['description']; ?></p>
		<? } ?>

		<? if (!empty($return_policy)) { ?>
			<h1><?= $return_policy['title']; ?></h1>
			<p><?= $return_policy['description']; ?></p>
		<? } ?>
	</body>
</html>
