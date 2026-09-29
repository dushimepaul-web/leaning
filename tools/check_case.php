<?php
/**
 * tools/check_case.php â€” dÃ©tecte les Ã©carts de casse entre le code et le disque.
 *
 * CodeIgniter/HMVC rÃ©sout les modÃ¨les, vues, bibliothÃ¨ques et helpers par
 * concatÃ©nation de chaÃ®nes. Sous Windows le systÃ¨me de fichiers ignore la
 * casse : tout Â« passe Â». Sous Linux, la moindre diffÃ©rence de casse provoque
 * un fatal error du type Â« Unable to locate the model you have specified Â».
 *
 * Ce script scanne application/ (routes, load->model/view/library/helper,
 * Modules::run, require/include) et vÃ©rifie qu'un fichier au nom EXACTEMENT
 * identique existe.
 *
 * Usage : php tools/check_case.php
 * Code retour : 0 = aucun Ã©cart de casse, 1 = au moins un Ã©cart.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$ROOT = dirname(__DIR__);
$APPPATH = $ROOT . '/application/';
$SYSTEMPATH = $ROOT . '/system/';

/* ------------------------------------------------------------------ */
/* Utilitaires de rÃ©solution sensible Ã  la casse                       */
/* ------------------------------------------------------------------ */

/**
 * VÃ©rifie qu'un chemin relatif existe AVEC LA CASSE EXACTE.
 *
 * @param string $anchor dossier absolu de dÃ©part (dÃ©jÃ  en casse rÃ©elle)
 * @param string $rel   chemin relatif sÃ©parÃ© par /
 */
function exact_exists($anchor, $rel)
{
    $rel = str_replace('\\', '/', $rel);
    $cur = rtrim($anchor, '/');
    foreach (explode('/', $rel) as $part) {
        if ($part === '' || $part === '.') continue;
        if ($part === '..') { $cur = dirname($cur); continue; }
        $entries = @scandir($cur);
        if ($entries === false) return false;
        if (!in_array($part, $entries, true)) return false;
        $cur .= '/' . $part;
    }
    return file_exists($cur);
}

/** VÃ©rifie qu'un chemin existe, la casse Ã©tant ignorÃ©e. */
function ci_exists($anchor, $rel)
{
    $rel = str_replace('\\', '/', $rel);
    $cur = rtrim($anchor, '/');
    foreach (explode('/', $rel) as $part) {
        if ($part === '' || $part === '.') continue;
        if ($part === '..') { $cur = dirname($cur); continue; }
        $entries = @scandir($cur);
        if ($entries === false) return false;
        $found = false;
        foreach ($entries as $e) {
            if ($e === '.' || $e === '..') continue;
            if (strcasecmp($e, $part) === 0) { $cur .= '/' . $e; $found = true; break; }
        }
        if (!$found) return false;
    }
    return file_exists($cur);
}

/** Retourne le nom rÃ©el (casse exacte) d'un chemin, ou null. */
function real_path($anchor, $rel)
{
    $rel = str_replace('\\', '/', $rel);
    $cur = rtrim($anchor, '/');
    $out = '';
    foreach (explode('/', $rel) as $part) {
        if ($part === '' || $part === '.') continue;
        if ($part === '..') { $cur = dirname($cur); $out = dirname($out); continue; }
        $entries = @scandir($cur);
        if ($entries === false) return null;
        if (!in_array($part, $entries, true)) return null;
        $cur .= '/' . $part;
        $out .= '/' . $part;
    }
    return $out;
}

/** Chemin relatif au projet, séparé par / (affichage du rapport). */
function short_path($file)
{
    $root = str_replace('\\', '/', dirname(__DIR__)) . '/';
    $file = str_replace('\\', '/', $file);
    return (strpos($file, $root) === 0) ? substr($file, strlen($root)) : $file;
}

/* ------------------------------------------------------------------ */
/* Collecte des fichiers sources                                       */
/* ------------------------------------------------------------------ */

function list_php_files($dir, array &$out = array())
{
    if (!is_dir($dir)) return $out;
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $file) {
        if ($file->isDir()) continue;
        $path = str_replace('\\', '/', $file->getPathname());
        if (substr($path, -4) !== '.php') continue;
        $out[] = $path;
    }
    return $out;
}

/**
 * Module HMVC auquel appartient un fichier (null sinon).
 *
 * @return string|null
 */
function module_of($file)
{
    $p = str_replace('\\', '/', $file);
    $marker = '/application/modules/';
    $pos = strpos($p, $marker);
    if ($pos === false) return null;
    $rest = substr($p, $pos + strlen($marker));
    $slash = strpos($rest, '/');
    return $slash === false ? null : substr($rest, 0, $slash);
}

/* ------------------------------------------------------------------ */
/* RÃ©solution des rÃ©fÃ©rences                                           */
/* ------------------------------------------------------------------ */

$gaps = array();   // Ã©carts de casse avÃ©rÃ©s
$missing = array(); // rÃ©fÃ©rences non rÃ©solubles (souvent dynamiques)
$seen = array();

function add_gap(&$gaps, $file, $line, $type, $ref, $expected)
{
    global $seen;
    $key = $file . '|' . $line . '|' . $ref;
    if (isset($seen[$key])) return;
    $seen[$key] = true;
    $gaps[] = array(
        'file'     => $file,
        'line'     => $line,
        'type'     => $type,
        'ref'      => $ref,
        'expected' => $expected,
    );
}

function add_missing(&$missing, $file, $line, $type, $ref, $tried)
{
    global $seen;
    $key = 'M|' . $file . '|' . $line . '|' . $ref;
    if (isset($seen[$key])) return;
    $seen[$key] = true;
    $missing[] = array(
        'file'  => $file,
        'line'  => $line,
        'type'  => $type,
        'ref'   => $ref,
        'tried' => $tried,
    );
}

/**
 * Teste une liste de chemins candidats :
 *  - au moins un en casse exacte  => OK
 *  - existe mais mauvaise casse    => Ã‰CART
 *  - introuvable partout           => non rÃ©solu
 */
function check_candidates($file, $line, $type, $ref, array $candidates)
{
    global $gaps, $missing;

    $exact = false;
    $ciOnly = array();
    foreach ($candidates as $candidate) {
        $root = $candidate[0]; // chemin absolu ancre
        $rel = $candidate[1];
        if (exact_exists($root, $rel)) { $exact = true; break; }
        if (ci_exists($root, $rel)) {
            $real = real_path($root, $rel);
            $ciOnly[] = $rel . ($real !== null ? '  (rÃ©el : ' . $real . ')' : '');
        }
    }
    if ($exact) return;
    if ($ciOnly) {
        add_gap($gaps, $file, $line, $type, $ref, $ciOnly[0]);
        return;
    }
    add_missing($missing, $file, $line, $type, $ref, count($candidates));
}

/* ------------------------------------------------------------------ */
/* Scan                                                                */
/* ------------------------------------------------------------------ */

$files = array_merge(
    list_php_files($APPPATH),
    list_php_files($ROOT . '/tools')
);

$patterns = array(
    'model'   => '/->model\s*\(\s*([\'"])((?:[A-Za-z0-9_\/\-])+)\1/',
    'view'    => '/->view\s*\(\s*([\'"])((?:[A-Za-z0-9_\/\-])+)\1/',
    'library' => '/->library\s*\(\s*([\'"])((?:[A-Za-z0-9_\/\-])+)\1/',
    'helper'  => '/->helper\s*\(\s*(?:array\s*\(\s*)?([\'"])((?:[A-Za-z0-9_\/\-])+)\1/',
    'run'     => '/Modules::run\s*\(\s*([\'"])((?:[A-Za-z0-9_\/\-])+)\1/',
    'render'  => '/->render_view\s*\(\s*([\'"])((?:[A-Za-z0-9_\/\-])+)\1/',
    'include' => '/(?<![A-Za-z0-9_\'">])(?:require_once|require|include_once|include)\s*\(?\s*([\'"])([^\'"\n]+)\1/',
);

foreach ($files as $file) {
    $src = @file_get_contents($file);
    if ($src === false) continue;
    $lines = preg_split('/\r\n|\r|\n/', $src);
    $module = module_of($file);

    foreach ($lines as $i => $line) {
        $lineno = $i + 1;

        /* ---- routes : "Module/Controller/method" ou "Controller/method" ---- */
        if (preg_match('/\\$route\\s*\\[[^\\]]*\\]\\s*=\\s*([\'"])([^\'"]+)\\1/', $line, $m)) {
            $target = trim($m[2]);
            if ($target !== ''
                && $target[0] !== '$'
                && strpos($target, '$') === false
                && strpos($target, '://') === false
                && strpos($target, '(') === false) {
                $segs = explode('/', $target);
                $candidates = array();
                if (count($segs) >= 3) {
                    $candidates[] = array($ROOT . '/', 'application/modules/' . $segs[0] . '/controllers/' . $segs[1] . '.php');
                } elseif (count($segs) === 2) {
                    // MX : Module/Controller[/method]  ou  Controller/method
                    $candidates[] = array($ROOT . '/', 'application/modules/' . $segs[0] . '/controllers/' . $segs[1] . '.php');
                    $candidates[] = array($ROOT . '/', 'application/controllers/' . $segs[0] . '.php');
                } else {
                    $candidates[] = array($ROOT . '/', 'application/controllers/' . $segs[0] . '.php');
                }
                check_candidates($file, $lineno, 'route', $target, $candidates);
            }
        }

        foreach ($patterns as $type => $pattern) {
            if (!preg_match($pattern, $line, $m)) continue;
            $ref = $m[2];
            if ($ref === '' || $ref[0] === '$' || strpos($ref, '{') !== false) continue;

            $candidates = array();
            $slash = strpos($ref, '/');
            $sub = '';

            switch ($type) {
                case 'model':
                    if ($slash !== false) {
                        list($mod, $name) = explode('/', $ref, 2);
                        $candidates[] = array($ROOT . '/', 'application/modules/' . $mod . '/models/' . $name . '.php');
                    } else {
                        if ($module !== null) {
                            $candidates[] = array($ROOT . '/', 'application/modules/' . $module . '/models/' . $ref . '.php');
                        }
                        $candidates[] = array($ROOT . '/', 'application/models/' . $ref . '.php');
                    }
                    break;

                case 'view':
                case 'render':
                    if ($module !== null) {
                        $candidates[] = array($ROOT . '/', 'application/modules/' . $module . '/views/' . $ref . '.php');
                    }
                    if ($slash !== false) {
                        list($mod, $name) = explode('/', $ref, 2);
                        $candidates[] = array($ROOT . '/', 'application/modules/' . $mod . '/views/' . $name . '.php');
                    }
                    $candidates[] = array($ROOT . '/', 'application/views/' . $ref . '.php');
                    break;

                case 'library':
                    if ($slash !== false) {
                        list($mod, $name) = explode('/', $ref, 2);
                        $candidates[] = array($ROOT . '/', 'application/modules/' . $mod . '/libraries/' . $name . '.php');
                        $candidates[] = array($ROOT . '/', 'application/libraries/' . $name . '.php');
                        $candidates[] = array($ROOT . '/', 'system/libraries/' . ucfirst($name) . '.php');
                    } else {
                        if ($module !== null) {
                            $candidates[] = array($ROOT . '/', 'application/modules/' . $module . '/libraries/' . $ref . '.php');
                        }
                        $candidates[] = array($ROOT . '/', 'application/libraries/' . $ref . '.php');
                        $candidates[] = array($ROOT . '/', 'system/libraries/' . ucfirst($ref) . '.php');
                        $candidates[] = array($ROOT . '/', 'system/libraries/' . $ref . '.php');
                        $candidates[] = array($ROOT . '/', 'application/third_party/MX/' . $ref . '.php');
                    }
                    break;

                case 'helper':
                    foreach (explode('/', $ref) as $h) {
                        if ($module !== null) {
                            $candidates[] = array($ROOT . '/', 'application/modules/' . $module . '/helpers/' . $h . '_helper.php');
                        }
                        $candidates[] = array($ROOT . '/', 'application/helpers/' . $h . '_helper.php');
                        $candidates[] = array($ROOT . '/', 'system/helpers/' . $h . '_helper.php');
                    }
                    break;

                case 'run':
                    $candidates[] = array($ROOT . '/', 'application/modules/' . $ref . '/controllers/');
                    break;

                case 'include':
                    $dir = dirname($file);
                    $candidates[] = array($dir . '/', $ref);
                    $candidates[] = array($ROOT . '/', $ref);
                    $candidates[] = array($APPPATH, $ref);
                    break;
            }

            if (!$candidates) continue;
            check_candidates($file, $lineno, $type . '("' . $ref . '")', $ref, $candidates);
        }
    }
}

/* ------------------------------------------------------------------ */
/* Rapport                                                             */
/* ------------------------------------------------------------------ */

echo str_repeat('=', 78) . "\n";
echo "VERIFICATION DE LA CASSE â€” " . count($files) . " fichiers PHP scannÃ©s\n";
echo str_repeat('=', 78) . "\n\n";

if ($gaps) {
    echo "Ã‰CARTS DE CASSE (" . count($gaps) . ") â€” cassera sous Linux :\n\n";
    foreach ($gaps as $g) {
        printf("  %s:%d\n    type    : %s\n    rÃ©f.    : %s\n    attendu : %s\n\n",
            short_path($g['file']),
            $g['line'],
            $g['type'],
            $g['ref'],
            $g['expected']
        );
    }
} else {
    echo "OK : aucun Ã©cart de casse dÃ©tectÃ©.\n\n";
}

if ($missing) {
    echo "RÃ‰FÃ‰RENCES NON RÃ‰SOLUBLES STATIQUEMENT (" . count($missing) . ") â€” souvent dynamiques :\n";
    foreach ($missing as $m) {
        printf("  %s:%d  %s  (%s)\n",
            short_path($m['file']),
            $m['line'],
            $m['type'],
            $m['ref']
        );
    }
    echo "\n";
}

echo str_repeat('-', 78) . "\n";
printf("Ã‰carts de casse : %d\n", count($gaps));
exit($gaps ? 1 : 0);
