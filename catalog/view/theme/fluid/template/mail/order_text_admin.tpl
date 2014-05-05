<?php
echo _l("You have received an order.\n\n");

echo _l("Order ID: ") . $order_id . "\n";
echo _l("Date Added: ") . $date_added . "\n";
echo _l("Order Status: ") . $order_status['title'] . "\n\n";

echo _l("Products\n");

foreach ($order_products as $product) {
	echo "$product[quantity]x $product[name] ($product[model]) - $product[total]\n";

	foreach ($product['options'] as $option) {
		echo chr(9) . "- $option[name]: " . ($option['display_value'] ? $option['display_value'] : $option['value']) . "\n";
	}
}

foreach ($order_vouchers as $voucher) {
	echo "1x $voucher[description] $voucher[amount]\n";
}

echo "\n";

echo _l("Order Total: ") . "\n";

foreach ($order_totals as $total) {
	echo "$total[title]: $total[text]\n";
}

echo "\n";

if ($comment) {
	echo _l("Order Comments: \n\n") . $comment . "\n\n";
}
