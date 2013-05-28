<?php
echo "$text_received\n\n";

echo "$text_order_id $order_id\n";
echo "$text_date_added $date_added\n";
echo "$text_order_status $order_status\n\n";

echo "$text_products\n";

foreach ($order_products as $product) {
	echo "$product[quantity]x $product[name] ($product[model]) - $product[total]\n";
	
	foreach ($product['option'] as $option) {
			echo chr(9) . "- $option[name]: $option[value]\n";
	}
}

foreach ($order_vouchers as $voucher) {
	echo "1x $voucher[description] $voucher[amount]\n";
}

echo "\n";

echo "$text_order_total\n";

foreach ($order_totals as $total) {
	echo "$total[title]: $total[text]\n";
}

echo "\n";

if ($comment) {
	echo "$text_comment\n\n$comment\n\n";
}