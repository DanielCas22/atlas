<?php
require __DIR__ . '/../helpers/EmailHelper.php';
$result = EmailHelper::testConnection();
var_export($result);
echo PHP_EOL;
