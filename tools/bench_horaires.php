<?php
/**
 * Banc de mesure du moteur d'emplois du temps.
 *
 * Appelle le solveur directement : aucune requête HTTP, aucune session CodeIgniter.
 * Le contexte et le moteur proviennent de application/libraries/Horaires_solver.php,
 * exactement les mêmes méthodes que celles utilisées par le contrôleur Horaires.
 *
 * Usage :
 *   php tools/bench_horaires.php [--seeds=20] [--from=0] [--annee=0] [--label=baseline]
 *
 * Sortie : une ligne par graine, puis une ligne RESULT comparable d'une exécution
 * à l'autre :
 *   RESULT <label> min=<n> avg=<x> max=<n> zeros=<k>/<N> time_avg=<s> time_total=<s>
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(0);

$root = dirname(__DIR__);
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development');
}
if (!defined('FCPATH')) {
    define('FCPATH', $root . DIRECTORY_SEPARATOR);
}
if (!defined('BASEPATH')) {
    define('BASEPATH', FCPATH . 'system' . DIRECTORY_SEPARATOR);
}
if (!defined('APPPATH')) {
    define('APPPATH', FCPATH . 'application' . DIRECTORY_SEPARATOR);
}

require_once APPPATH . 'libraries/Horaires_solver.php';

/**
 * Adaptateur minimal : même contrat que CI_DB_driver pour le solveur,
 *   $db->query($sql) → objet exposant result_array()
 */
class Bench_db
{
    /** @var mysqli */
    private $mysqli;

    public function __construct(array $cfg)
    {
        mysqli_report(MYSQLI_REPORT_OFF);
        $this->mysqli = new mysqli(
            $cfg['hostname'],
            $cfg['username'],
            $cfg['password'],
            $cfg['database']
        );
        if ($this->mysqli->connect_errno) {
            fwrite(STDERR, 'Connexion MySQL impossible : ' . $this->mysqli->connect_error . PHP_EOL);
            exit(1);
        }
        $this->mysqli->set_charset($cfg['char_set'] ?? 'utf8mb4');
    }

    public function query($sql)
    {
        $res = $this->mysqli->query($sql);
        if ($res === false) {
            throw new RuntimeException('Requête invalide : ' . $this->mysqli->error . ' — ' . $sql);
        }
        return new Bench_result($res);
    }
}

class Bench_result
{
    /** @var mysqli_result|bool */
    private $res;

    public function __construct($res)
    {
        $this->res = $res;
    }

    public function result_array()
    {
        if ($this->res === true) {
            return array();
        }
        $rows = array();
        while (($row = $this->res->fetch_assoc()) !== null) {
            $rows[] = $row;
        }
        return $rows;
    }
}

// ── Arguments ───────────────────────────────────────────────────────
$nb_seeds = 20;
$from = 0;
$annee_opt = -1;
$label = 'sol';

foreach (array_slice($argv, 1) as $arg) {
    if (preg_match('/^--seeds=(\d+)$/', $arg, $m)) {
        $nb_seeds = max(1, (int)$m[1]);
    } elseif (preg_match('/^--from=(\d+)$/', $arg, $m)) {
        $from = (int)$m[1];
    } elseif (preg_match('/^--annee=(\d+)$/', $arg, $m)) {
        $annee_opt = (int)$m[1];
    } elseif (preg_match('/^--label=(.+)$/', $arg, $m)) {
        $label = $m[1];
    } else {
        fwrite(STDERR, 'Argument inconnu : ' . $arg . PHP_EOL);
        exit(1);
    }
}

// ── Connexion (mêmes réglages que application/config/database.php) ─
require FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
if (empty($db['default'])) {
    fwrite(STDERR, 'Groupe de connexion "default" introuvable dans database.php' . PHP_EOL);
    exit(1);
}
$connection = new Bench_db($db['default']);

// ── Année scolaire active (règle identique à MY_Controller) ────────
$id_annee = $annee_opt;
if ($id_annee < 0) {
    $rows = $connection->query('SELECT id_annee FROM annees_scolaires WHERE est_en_cours = 1 LIMIT 1')->result_array();
    if ($rows) {
        $id_annee = (int)$rows[0]['id_annee'];
    } else {
        $rows = $connection->query('SELECT id_annee FROM annees_scolaires WHERE deleted_at IS NULL ORDER BY id_annee DESC LIMIT 1')->result_array();
        $id_annee = $rows ? (int)$rows[0]['id_annee'] : 0;
    }
}

// ── Contexte (construit une seule fois, identique à l'application) ──
$solver = new Horaires_solver();
$ctx = $solver->context($connection, $id_annee);

$total_sessions = count($ctx['sessions']) + count($ctx['fix_rows']);
printf(
    "bench_horaires label=%s php=%s annee=%d classes_slots=%d sessions=%d fixes=%d seeds=%d..%d%s%s",
    $label,
    PHP_VERSION,
    $id_annee,
    $ctx['nb_slots'],
    count($ctx['sessions']),
    count($ctx['fix_rows']),
    $from,
    $from + $nb_seeds - 1,
    PHP_EOL,
    PHP_EOL
);

if (empty($ctx['sessions'])) {
    fwrite(STDERR, 'Aucune session à placer.' . PHP_EOL);
    exit(1);
}
printf("%-6s %-12s %-10s %-10s%s", 'graine', 'non_placees', 'placees', 'temps_s', PHP_EOL);

$unplaced_list = array();
$times = array();
$zeros = 0;

for ($seed = $from; $seed < $from + $nb_seeds; $seed++) {
    $t0 = microtime(true);
    $solution = $solver->solve($ctx, $seed);
    $elapsed = microtime(true) - $t0;

    $n = count($solution['unplaced']);
    $placed = count($solution['rows']);
    $unplaced_list[] = $n;
    $times[] = $elapsed;
    if ($n === 0) {
        $zeros++;
    }
    printf("%-6d %-12d %-10d %-10.3f%s", $seed, $n, $placed, $elapsed, PHP_EOL);
    flush();
}

$min = min($unplaced_list);
$max = max($unplaced_list);
$avg = array_sum($unplaced_list) / count($unplaced_list);
$time_avg = array_sum($times) / count($times);
$time_total = array_sum($times);

printf(
    "%sRESULT %s min=%d avg=%.2f max=%d zeros=%d/%d time_avg=%.3f time_total=%.3f sessions=%d%s",
    PHP_EOL,
    $label,
    $min,
    $avg,
    $max,
    $zeros,
    count($unplaced_list),
    $time_avg,
    $time_total,
    $total_sessions,
    PHP_EOL
);
