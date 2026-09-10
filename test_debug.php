<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(60);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli('localhost', 'root', '', 'vip_school');
$mysqli->set_charset('utf8mb4');

// Let's include our test logic or run the algorithm code
echo "Database connected successfully.\n";
$mysqli->close();
