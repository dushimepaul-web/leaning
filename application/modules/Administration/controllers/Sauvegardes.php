<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sauvegardes extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->_require_superadmin();
    }

    private function _require_superadmin() {
        if ((int)$this->session->userdata('id_role') !== 1) {
            if ($this->input->is_ajax_request() || $this->uri->segment(1) === 'api') {
                $this->json_response(array('success' => false, 'message' => 'Accès refusé : réservé au super-administrateur.'), 403);
            } else {
                redirect(base_url('Dashboard'));
            }
        }
    }

    public function index() {
        $data['title'] = 'Sauvegardes';
        $this->load->view('sauvegardes/index', $data);
    }

    public function api_create() {
        $dir = FCPATH . 'backups';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $db = $this->load->database('default', true);
        $host_raw = $db->hostname;
        $user = $db->username;
        $password = $db->password ?? '';
        $name = $db->database;

        if (strpos($host_raw, ':') !== false) {
            list($host, $port) = explode(':', $host_raw, 2);
        } else {
            $host = $host_raw;
            $port = '3306';
        }

        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $dir . '/' . $filename;

        $dump = $this->_find_mysqldump();
        if (!$dump) {
            $this->json_error('mysqldump introuvable. Vérifiez que MySQL/MariaDB est installé.');
            return;
        }
        $errfile = $filepath . '.err';
        // MYSQL_PWD évite d'exposer le mot de passe dans la ligne de commande
        $cmd = sprintf('set MYSQL_PWD=%s&& "%s" --protocol=TCP -h "%s" --port=%s -u "%s" --default-character-set=utf8mb4 "%s" > "%s" 2> "%s"',
            str_replace(['%', '&'], ['%%', '^&'], $password),
            $dump,
            $host,
            $port,
            $user,
            $name,
            $filepath,
            $errfile
        );
        $output = shell_exec($cmd);
        $error = is_file($errfile) ? trim((string)file_get_contents($errfile)) : '';
        @unlink($errfile);

        $valid = false;
        if (is_file($filepath) && filesize($filepath) > 0) {
            $head = (string)file_get_contents($filepath, false, null, 0, 128);
            $valid = (stripos($head, 'MySQL dump') !== false || stripos($head, 'MariaDB dump') !== false);
        }

        if ($valid) {
            $info = ['file' => $filename, 'size' => filesize($filepath), 'date' => date('Y-m-d H:i:s')];
            $this->json_success($info, 'Sauvegarde créée');
        } else {
            @unlink($filepath);
            $detail = $error !== '' ? $error : trim((string)$output);
            $this->json_error('Erreur lors de la sauvegarde' . ($detail !== '' ? ': ' . $detail : ''));
        }
    }

    public function api_list() {
        $dir = FCPATH . 'backups';
        $files = [];
        if (is_dir($dir)) {
            foreach (scandir($dir) as $f) {
                if ($f === '.' || $f === '..') continue;
                if (!preg_match('/^backup_[A-Za-z0-9_\-]+\.sql$/', $f)) continue;
                $fp = $dir . '/' . $f;
                $files[] = ['file' => $f, 'size' => filesize($fp), 'date' => date('Y-m-d H:i:s', filemtime($fp))];
            }
        }
        rsort($files);
        $this->json_success($files);
    }

    private function _backup_file($filename) {
        if (!is_string($filename) || !preg_match('/^backup_[A-Za-z0-9_\-]+\.sql$/', $filename)) return null;
        $path = FCPATH . 'backups/' . $filename;
        return is_file($path) ? $path : null;
    }

    public function api_download($filename) {
        $filepath = $this->_backup_file($filename);
        if (!$filepath) { $this->json_error('Fichier introuvable', 404); return; }
        $this->load->helper('download');
        force_download(basename($filepath), file_get_contents($filepath));
    }

    public function api_delete($filename) {
        $filepath = $this->_backup_file($filename);
        if (!$filepath) { $this->json_error('Sauvegarde introuvable', 404); return; }
        unlink($filepath);
        $this->json_success(null, 'Sauvegarde supprimée');
    }

    private function _find_mysqldump() {
        $paths = [
            'mysqldump',
        ];
        $base = 'C:\wamp64\bin';
        if (is_dir($base)) {
            foreach (scandir($base) as $dir) {
                $variant = null;
                if (is_dir("$base\\$dir") && in_array($dir, ['mysql', 'mariadb'])) {
                    foreach (scandir("$base\\$dir") as $ver) {
                        $exe = "$base\\$dir\\$ver\\bin\\mysqldump.exe";
                        if (file_exists($exe)) $variant = $exe;
                    }
                    if ($variant) $paths[] = $variant;
                }
            }
        }
        foreach ($paths as $p) {
            $test = sprintf('"%s" --version 2>&1', $p);
            $out = shell_exec($test);
            if ($out && (strpos($out, 'Ver') !== false || strpos($out, 'Distrib') !== false)) {
                return $p;
            }
        }
        return null;
    }
}
