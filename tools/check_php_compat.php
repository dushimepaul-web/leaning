<?php
/**
 * tools/check_php_compat.php — verifie la compatibilite PHP 8.0 a 8.4.
 *
 * Analyse statique (regex, sans AST) du code PHP pour detecter :
 *   - les fonctions/constantes retirees dans PHP 8.0 (erreur immediate),
 *   - les constructions apparues apres PHP 8.0 (qui feraient un parse error
 *     sur un serveur encore en 8.0),
 *   - les fonctions et comportements deprecies entre 8.1 et 8.4 (messages
 *     de deprecation -> erreurs 500 si display_errors/monitorentes les montent),
 *   - les comparateurs de tri qui ne retournent pas un int (PHP 8 refuse
 *     le bool -> avertissement "Comparison function must return an int").
 *
 * Usage :
 *   php tools/check_php_compat.php                     # application/ + tools/
 *   php tools/check_php_compat.php --path=application/modules/Horaires
 *   php tools/check_php_compat.php --json              # sortie machine
 *   php tools/check_php_compat.php --path=FICHIER.php
 *
 * Code retour :
 *   0 = aucun ecart
 *   1 = au moins un ecart "interdit" ou "deprecie" (ou lecture impossible)
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$ROOT = dirname(__DIR__);

/* ------------------------------------------------------------------ */
/* Regles : [identifiant, severite, version, message, regex]            */
/* ------------------------------------------------------------------ */

$SEVERITY_RANK = [
    'interdit'   => 0,
    'deprecie'   => 1,
    'avertissement' => 2,
];

$RULES = [
    /* ---- PHP 8.0 : retire, erreur immediate ------------------------ */
    ['create_function',      'interdit', '8.0', "retiree en PHP 8.0 (remplacer par une fonction/fermeture nommee)", '/\bcreate_function\s*\(/u'],
    ['each',                 'interdit', '8.0', "retiree en PHP 8.0 (remplacer par foreach)", '/(?<![\w$>\])])each\s*\(/u'],
    ['ereg',                 'interdit', '8.0', "retiree en PHP 8.0 (remplacer par preg_* )", '/\b(?:ereg|eregi|ereg_replace|eregi_replace|split|sql_regcase)\s*\(/u'],
    ['curly_offset',         'interdit', '8.0', "acces par accolades retire en PHP 8.0 (utiliser [0])", '/\$[A-Za-z_]\w*\s*\{[0-9$]/u'],
    ['libxml_loader',        'interdit', '8.0', "libxml_disable_entity_loader() depreciee en PHP 8.0", '/\blibxml_disable_entity_loader\s*\(/u'],

    /* ---- Apparu apres PHP 8.0 (parse error sur un serveur 8.0) ----- */
    ['enum',                 'interdit', '8.1', "enum : incompatible avec PHP 8.0", '/\benum\s+[A-Za-z_]\w*\s*(?:\{|:\s*[A-Za-z_])/u'],
    ['readonly',             'interdit', '8.1', "readonly : incompatible avec PHP 8.0", '/\breadonly\s+(?:class\b|\$|\??[A-Za-z_][\w\\\\]*\s+&?\$)/u'],
    ['never',                'interdit', '8.1', "type de retour never : incompatible avec PHP 8.0", '/\)\s*:\s*never\b/u'],
    ['fiber',                'interdit', '8.1', "Fiber : incompatible avec PHP 8.0", '/\bnew\s+Fiber\s*\(/u'],
    ['array_is_list',        'interdit', '8.1', "array_is_list() : incompatible avec PHP 8.0", '/\barray_is_list\s*\(/u'],
    ['fsync',                'interdit', '8.1', "fsync()/fdatasync() : incompatible avec PHP 8.0", '/\b(?:fsync|fdatasync)\s*\(/u'],
    ['globals_offset',       'interdit', '8.1', 'acces $GLOBALS[i] retire en PHP 8.1', '/\$GLOBALS\s*\[/u'],
    ['typed_const',          'interdit', '8.3', "constante de classe typee : incompatible avec PHP 8.0", '/\bconst\s+\??[A-Za-z_]\w*(?:\\\\[A-Za-z_]\w*)*\s+[A-Za-z_]\w*\s*=/u'],
    ['override',             'interdit', '8.3', "#[Override] : incompatible avec PHP 8.0", '/#\[\s*Override\s*\]/u'],
    ['json_validate',        'interdit', '8.3', "json_validate() : incompatible avec PHP 8.0", '/\bjson_validate\s*\(/u'],
    ['array_find',           'interdit', '8.4', "array_find()/array_any()/... : incompatible avec PHP 8.0", '/\b(?:array_find|array_find_key|array_any|array_all|request_parse_body)\s*\(/u'],
    ['new_chain',            'interdit', '8.4', "new Foo()->methode() sans parentheses : incompatible avec PHP 8.0", '/\bnew\s+[A-Za-z_]\w*\s*(?:\([^;]*?\))?\s*->\s*[A-Za-z_]/u'],
    ['asym_visibility',      'interdit', '8.4', "visibilite asymetrique public(set) : incompatible avec PHP 8.0", '/\b(?:public|protected|private)\s*\(\s*set\s*\)/u'],

    /* ---- Deprecies entre 8.1 et 8.4 -------------------------------- */
    ['strftime',             'deprecie', '8.1', "strftime()/gmstrftime() : depreciees en PHP 8.1", '/\b(?:gm)?strftime\s*\(/u'],
    ['filter_sanitize',      'deprecie', '8.1', "FILTER_SANITIZE_STRING/STRIPPED : deprecies en PHP 8.1", '/FILTER_SANITIZE_(?:STRING|STRIPPED)/u'],
    ['utf8_encode',          'deprecie', '8.2', "utf8_encode()/utf8_decode() : depreciees en PHP 8.2 (mb_convert_encoding)", '/\butf8_(?:encode|decode)\s*\(/u'],
    ['dollar_brace',         'deprecie', '8.2', 'syntaxe "${var}" depreciee en PHP 8.2 (utiliser "{$var}")', '/(?<![\\$])\$\{[A-Za-z_]/u'],
    ['e_strict',             'deprecie', '8.4', "E_STRICT : deprecie en PHP 8.4", '/\bE_STRICT\b/u'],

    /* ---- Avertissements PHP 8 (comportement) ----------------------- */
    ['implicit_nullable',    'avertissement', '8.4', "parametre implicite nullable (Type \$x = null) : deprecie en PHP 8.4 (ecrire ?Type)", ''],
    ['new_in_default',       'avertissement', '8.1', "new en valeur par defaut d'un parametre : incompatible avec PHP 8.0", ''],
    ['intersection_type',    'avertissement', '8.1', "type intersection A&B : incompatible avec PHP 8.0", ''],
    ['sort_comparator',      'avertissement', '8.0', "comparateur de tri ne retournant pas un int (PHP 8 exige <=> )", ''],
];

/* ------------------------------------------------------------------ */
/* Utilitaires                                                          */
/* ------------------------------------------------------------------ */

function pc_list_files($target, $allowedExt)
{
    if (is_file($target)) {
        return [$target];
    }
    if (!is_dir($target)) {
        return [];
    }
    $files = [];
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($target, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $f) {
        if ($f->isFile() && in_array(strtolower(pathinfo($f->getFilename(), PATHINFO_EXTENSION)), $allowedExt, true)) {
            $files[] = $f->getPathname();
        }
    }
    sort($files, SORT_STRING);
    return $files;
}

function pc_line_of($content, $offset)
{
    return substr_count(substr($content, 0, $offset), "\n") + 1;
}

function pc_line_text($lines, $lineno)
{
    $t = isset($lines[$lineno - 1]) ? $lines[$lineno - 1] : '';
    return trim($t);
}

/**
 * Masque tout ce qui n'est PAS du code PHP dans un fichier .php
 * (HTML, JavaScript dans <script>, CSS dans <style>).
 *
 * Les vues CI3 sont du melange : appliquer les regles PHP sur une template
 * literale JS revient a signaler "${x}" ou ".each(" comme etant du PHP.
 *
 * Les octets hors PHP sont remplaces par des espaces (les sauts de ligne sont
 * conserves) : la longueur du fichier est donc identique et les offsets
 * restent valables pour remonter au numero de ligne.
 */
function pc_mask_non_php($content)
{
    $len = strlen($content);
    $out = $content;
    $pos = 0;

    $blank = function ($from, $to) use (&$out) {
        for ($i = $from; $i < $to; $i++) {
            if ($out[$i] !== "\n") { $out[$i] = ' '; }
        }
    };

    while ($pos < $len) {
        $open = strpos($content, '<?', $pos);
        if ($open === false) {
            $blank($pos, $len);
            $pos = $len;
            break;
        }
        $blank($pos, $open);
        $prefix = substr($content, $open, 5);
        if ($prefix === '<?php') {
            $tagEnd = $open + 5;
        } elseif (substr($content, $open, 3) === '<?=') {
            $tagEnd = $open + 3;
        } else {
            $tagEnd = $open + 2;   // tag court <?
        }
        $close = strpos($content, '?>', $tagEnd);
        if ($close === false) {
            // balise ouvrante non fermee : tout le reste est du PHP
            $pos = $len;
            break;
        }
        $pos = $close + 2;
    }

    return $out;
}

/**
 * Extrait les signatures de fonction/methodes d'un fichier :
 * "function nom(params) [ : type ]"
 */
function pc_signatures($content)
{
    $out = [];
    if (preg_match_all(
        '/function\s+&?[A-Za-z_]\w*\s*\((?:[^(){}]|\([^(){}]*\))*\)(?:\s*:\s*\??[A-Za-z_][\w\\\\|]*)?/s',
        $content,
        $m,
        PREG_OFFSET_CAPTURE
    )) {
        foreach ($m[0] as $hit) {
            $out[] = ['text' => $hit[0], 'offset' => $hit[1]];
        }
    }
    return $out;
}

/**
 * Extrait les corps de callback de comparaison de tri :
 * usort($x, function ($a, $b) { ... });
 */
function pc_sort_comparators($content)
{
    $out = [];
    if (!preg_match_all('/\b(?:usort|uasort|uksort)\s*\(/', $content, $m, PREG_OFFSET_CAPTURE)) {
        return $out;
    }
    foreach ($m[0] as $hit) {
        $start = $hit[1];
        $win = substr($content, $start, 800);
        if (!preg_match('/function\s*\(\s*&?\$\w+\s*,\s*&?\$\w+\s*\)\s*(?::\s*\??[\w\\\\|]+\s*)?\{/', $win, $fm, PREG_OFFSET_CAPTURE)) {
            continue;
        }
        $openAt = $fm[0][1] + strlen($fm[0][0]) - 1;
        $depth = 0;
        $end = null;
        $len = strlen($win);
        for ($i = $openAt; $i < $len; $i++) {
            $ch = $win[$i];
            if ($ch === '{') { $depth++; }
            elseif ($ch === '}') {
                $depth--;
                if ($depth === 0) { $end = $i; break; }
            }
        }
        if ($end === null) { continue; }
        $body = substr($win, $openAt + 1, $end - $openAt - 1);
        // un comparateur doit retourner un int : "return $a < $b;" renvoie un bool
        // (PHP 8 emet alors "Comparison function must return an int").
        $cmp = '/return\s+\$\w+(?:\s*\[[^\]]*\])?\s*(?:!==|===|!=|==|>=|<=|>|<)\s*\$\w+(?:\s*\[[^\]]*\])?\s*;/';
        if (preg_match($cmp, $body) && strpos($body, '<=>') === false) {
            $out[] = ['text' => substr($win, 0, $end + 1), 'offset' => $start];
        }
    }
    return $out;
}

/* ------------------------------------------------------------------ */
/* Analyse                                                              */
/* ------------------------------------------------------------------ */

$optPath = null;
$optJson = false;
foreach (array_slice($argv, 1) as $arg) {
    if (strpos($arg, '--path=') === 0) {
        $optPath = substr($arg, 7);
    } elseif ($arg === '--json') {
        $optJson = true;
    } elseif ($arg === '--help' || $arg === '-h') {
        fwrite(STDOUT, "Usage : php tools/check_php_compat.php [--path=CHEMIN] [--json]\n");
        exit(0);
    } else {
        fwrite(STDERR, "Argument inconnu : {$arg}\n");
        exit(1);
    }
}

$targets = $optPath !== null
    ? [$optPath]
    : [$ROOT . '/application', $ROOT . '/tools'];

$files = [];
foreach ($targets as $t) {
    $abs = (strpos($t, DIRECTORY_SEPARATOR) === 0 || preg_match('#^[A-Za-z]:[\\\\/]#', $t)) ? $t : $ROOT . '/' . $t;
    $found = pc_list_files($abs, ['php']);
    if (empty($found) && !file_exists($abs)) {
        fwrite(STDERR, "Cible introuvable : {$t}\n");
        exit(1);
    }
    $files = array_merge($files, $found);
}
$files = array_values(array_unique($files));

$findings = [];
$readErrors = [];
$scanned = 0;

foreach ($files as $file) {
    $raw = @file_get_contents($file);
    if ($raw === false) {
        $readErrors[] = $file;
        continue;
    }
    $scanned++;
    $rel = ltrim(str_replace('\\', '/', substr($file, strlen($ROOT))), '/');
    $lines = explode("\n", str_replace("\r\n", "\n", $raw));
    $content = str_replace("\r\n", "\n", $raw);

    $push = function ($ruleId, $offset, $excerpt) use (&$findings, $RULES, $rel, $content, $lines) {
        foreach ($RULES as $r) {
            if ($r[0] !== $ruleId) { continue; }
            $lineno = pc_line_of($content, $offset);
            $findings[] = [
                'file'        => $rel,
                'line'        => $lineno,
                'rule'        => $r[0],
                'severity'    => $r[1],
                'php'         => $r[2],
                'message'     => $r[3],
                'source'      => pc_line_text($lines, $lineno),
                'excerpt'     => trim($excerpt),
            ];
            return;
        }
    };

    // BOM : casse la sortie HTTP de tout fichier CI3
    if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
        $findings[] = [
            'file' => $rel, 'line' => 1, 'rule' => 'bom',
            'severity' => 'interdit', 'php' => '-',
            'message' => "BOM UTF-8 : produit un espace parasite avant tout en-tete HTTP",
            'source' => '(debut du fichier)', 'excerpt' => 'EF BB BF',
        ];
    }

    foreach ($RULES as $r) {
        if ($r[4] === '') { continue; }
        if (preg_match_all($r[4], $content, $m, PREG_OFFSET_CAPTURE)) {
            foreach ($m[0] as $hit) {
                // exclut les occurrences qui ne sont que des commentaires
                $before = substr($content, max(0, $hit[1] - 200), 200);
                if (preg_match('/\*\//', $before) === 1
                    && substr_count($before, '*/') > substr_count($before, '/*')) {
                    continue;
                }
                $push($r[0], $hit[1], $hit[0]);
            }
        }
    }

    // regles "signature"
    foreach (pc_signatures($content) as $sig) {
        $text = $sig['text'];
        if (preg_match_all('/(function\s+&?[A-Za-z_]\w*\s*\()([^)]*)\)/s', $text, $pm, PREG_OFFSET_CAPTURE)) {
            $params = $pm[2][0][0];
            $off = $sig['offset'] + $pm[2][0][1];
            // parametre implicite nullable : "Type $x = null" sans "?"
            if (preg_match('/(^|,)\s*((?:readonly\s+)?)([A-Za-z_][\w\\\\]*)\s+(&?\s*\$[A-Za-z_]\w*)\s*=\s*null\b/', $params, $im, PREG_OFFSET_CAPTURE)) {
                $type = $im[3][0];
                if ($type !== 'mixed') {
                    $push('implicit_nullable', $sig['offset'], $text);
                }
            }
            // new en valeur par defaut
            if (preg_match('/=\s*new\s+[A-Za-z_]/', $params)) {
                $push('new_in_default', $sig['offset'], $text);
            }
            // type intersection "A&B $x" (mais pas "&$ref")
            if (preg_match('/[A-Za-z_]\w*\s*&\s*[A-Za-z_]\w*\s+&?\$/', $params)) {
                $push('intersection_type', $sig['offset'], $text);
            }
        }
        if (preg_match('/\)\s*:\s*[A-Za-z_]\w*\s*&\s*[A-Za-z_]/', $text)) {
            $push('intersection_type', $sig['offset'], $text);
        }
    }

    // comparateurs de tri
    foreach (pc_sort_comparators($content) as $cmp) {
        $push('sort_comparator', $cmp['offset'], $cmp['text']);
    }
}

/* ------------------------------------------------------------------ */
/* Rapport                                                              */
/* ------------------------------------------------------------------ */

$counts = ['interdit' => 0, 'deprecie' => 0, 'avertissement' => 0];
foreach ($findings as $f) {
    $counts[$f['severity']] = isset($counts[$f['severity']]) ? $counts[$f['severity']] + 1 : 1;
}
usort($findings, function ($a, $b) use ($SEVERITY_RANK) {
    $d = $SEVERITY_RANK[$a['severity']] <=> $SEVERITY_RANK[$b['severity']];
    return ($d !== 0) ? $d : (strcmp($a['file'], $b['file']) ?: ($a['line'] <=> $b['line']));
});

$exit = (count($readErrors) > 0 || $counts['interdit'] > 0 || $counts['deprecie'] > 0) ? 1 : 0;

if ($optJson) {
    echo json_encode([
        'scanned' => $scanned,
        'counts' => $counts,
        'exit' => $exit,
        'findings' => $findings,
        'unreadable' => $readErrors,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    exit($exit);
}

$bar = str_repeat('=', 78);
echo "{$bar}\n";
echo "COMPATIBILITE PHP 8.0 -> 8.4   |   {$scanned} fichiers analyses\n";
echo "{$bar}\n";

$byFile = [];
foreach ($findings as $f) {
    $byFile[$f['file']][] = $f;
}
ksort($byFile, SORT_STRING);

foreach ($byFile as $file => $rows) {
    echo "\n{$file}\n";
    foreach ($rows as $f) {
        printf(
            "  %5d  [%-14s] %-11s PHP>=%-4s %s\n",
            $f['line'],
            $f['severity'],
            $f['rule'],
            $f['php'],
            $f['message']
        );
        if ($f['source'] !== '') {
            echo '          | ' . substr($f['source'], 0, 120) . "\n";
        }
    }
}

if (!empty($readErrors)) {
    echo "\nFICHIERS ILLISIBLES (" . count($readErrors) . ") :\n";
    foreach ($readErrors as $e) { echo "  {$e}\n"; }
}

echo "\n{$bar}\n";
printf(
    "INTERDIT: %d   DEPRECIE: %d   AVERTISSEMENT: %d   TOTAL: %d\n",
    $counts['interdit'],
    $counts['deprecie'],
    $counts['avertissement'],
    count($findings)
);
echo "RESULT : " . ($exit === 0 ? "OK - aucun ecart bloquant" : "ECART DETECTE") . "\n";
echo "{$bar}\n";

exit($exit);
