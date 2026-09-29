<?php
define('BASEPATH', 'true');
$mysqli = new mysqli('localhost', 'root', '', 'vip_school');
if ($mysqli->connect_error) { die($mysqli->connect_error); }
$res = $mysqli->query("SELECT id, ip_address, timestamp, LEFT(data, 300) as d, LENGTH(data) as len FROM ci_sessions ORDER BY timestamp DESC LIMIT 5");
while ($r = $res->fetch_assoc()) echo json_encode($r) . "\n";
echo "\ncount: ";
$res = $mysqli->query("SELECT COUNT(*) c FROM ci_sessions");
echo $res->fetch_assoc()['c'] . "\n";
$mysqli->close();
?>
