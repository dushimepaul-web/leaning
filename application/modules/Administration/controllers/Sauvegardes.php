<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sauvegardes extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Sauvegardes';
        $this->load->view('sauvegardes/index', $data);
    }

    public function api_create() {
        $dir = FCPATH . 'backups';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $db = $this->load->database('default', true);
        $host_raw = $db->hostname;
        $user = $db->username;
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
        $cmd = sprintf('"%s" --protocol=TCP -h %s --port=%s -u %s %s > "%s" 2>&1',
            $dump,
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            escapeshellarg($name),
            $filepath
        );
        $output = shell_exec($cmd);

        if (file_exists($filepath) && filesize($filepath) > 0) {
            $info = ['file' => $filename, 'size' => filesize($filepath), 'date' => date('Y-m-d H:i:s')];
            $this->json_success($info, 'Sauvegarde créée');
        } else {
            $this->json_error('Erreur lors de la sauvegarde' . ($output ? ': ' . $output : ''));
        }
    }

    public function api_list() {
        $dir = FCPATH . 'backups';
        $files = [];
        if (is_dir($dir)) {
            foreach (scandir($dir) as $f) {
                if ($f === '.' || $f === '..') continue;
                $fp = $dir . '/' . $f;
                $files[] = ['file' => $f, 'size' => filesize($fp), 'date' => date('Y-m-d H:i:s', filemtime($fp))];
            }
        }
        rsort($files);
        $this->json_success($files);
    }

    public function api_download($filename) {
        $filepath = FCPATH . 'backups/' . basename($filename);
        if (!file_exists($filepath)) {
            $this->json_error('Fichier introuvable', 404); return;
        }
        $this->load->helper('download');
        force_download(basename($filename), file_get_contents($filepath));
    }

    public function api_delete($filename) {
        $filepath = FCPATH . 'backups/' . basename($filename);
        if (file_exists($filepath)) unlink($filepath);
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
