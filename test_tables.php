<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(60);

$mysqli = new mysqli('localhost', 'root', '', 'vip_school');
$mysqli->set_charset('utf8mb4');

// List ALL tables
echo "=== ALL TABLES IN vip_school ===\n";
$r = $mysqli->query("SHOW TABLES");
while ($row = $r->fetch_row()) {
    echo "  $row[0]\n";
}

$mysqli->close();
