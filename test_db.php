<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "PHP Version: " . phpversion() . "<br>";
echo "SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'N/A') . "<br>";

$_env_file = FCPATH . '.env';
echo ".env exists: " . (is_file($_env_file) ? 'YES' : 'NO') . "<br>";

if (is_file($_env_file)) {
    echo ".env content:<br><pre>" . htmlspecialchars(file_get_contents($_env_file)) . "</pre>";
}

$server_name = strtolower($_SERVER['SERVER_NAME'] ?? '');
$is_local = in_array($server_name, ['localhost', '127.0.0.1', '']);
echo "is_local: " . ($is_local ? 'YES (local)' : 'NO (remote)') . "<br>";

$host = $is_local ? 'localhost' : 'localhost';
$user = $is_local ? 'root' : 'abemarket_vipschool';
$pass = $is_local ? '' : 'Abe@@2028';
$db   = $is_local ? 'vip_school' : 'abemarket_vipschool';

echo "Trying: user=$user, db=$db<br>";

$conn = @new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo "<b style='color:red'>DB ERROR: " . $conn->connect_error . "</b><br>";
} else {
    echo "<b style='color:green'>DB OK!</b><br>";
    $conn->close();
}
