<?php
// Configuration
if (is_file('../config.php')) {
	require_once('../config.php');
}

$url = HTTP_SERVER . "index.php?route=extension/payment/alipay_direct/callback";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);

$output = curl_exec($ch);
curl_close($ch);

echo($output);