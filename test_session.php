<?php
define('BASEPATH', 'true');
$mysqli = new mysqli('localhost', 'root', '', 'vip_school');
if ($mysqli->connect_error) { die($mysqli->connect_error); }

// Reprend une session admin encore valide, sinon en crée une au format CI3
$res = $mysqli->query("SELECT id, timestamp, data FROM ci_sessions WHERE data LIKE '%logged_in|b:1;%' ORDER BY timestamp DESC LIMIT 5");
$now = time();
while ($r = $res->fetch_assoc()) {
    if ($now - (int)$r['timestamp'] < 7000) {
        echo "SESSION_ID={$r['id']}\n";
        $mysqli->close();
        exit;
    }
}

$id = bin2hex(random_bytes(16));
$data = 'logged_in|b:1;id_utilisateur|s:1:"1";uuid|s:36:"795185c3-6369-11f1-9d55-9c7bef735b1f";email|s:20:"admin@vip-school.com";nom_complet|s:14:"Administrateur";user|s:14:"Administrateur";id_role|s:1:"1";role_code|s:5:"admin";role_libelle|s:14:"Administrateur";';
$stmt = $mysqli->prepare("INSERT INTO ci_sessions (id, ip_address, timestamp, data) VALUES (?, '', ?, ?)");
$stmt->bind_param('sis', $id, $now, $data);
$stmt->execute();
echo "SESSION_ID=$id\n";
$mysqli->close();
?>
