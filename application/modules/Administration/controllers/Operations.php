<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operations extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Opérations groupées';
        $this->load->view('operations/index', $data);
    }

    public function api_tables() {
        $tables = $this->db->list_tables();
        $exclude = ['audit_logs', 'roles_menus', 'ci_sessions', 'parametres'];
        $result = [];
        foreach ($tables as $t) {
            if (in_array($t, $exclude)) continue;
            $result[] = ['name' => $t, 'label' => ucfirst(str_replace('_', ' ', $t))];
        }
        $this->json_success($result);
    }

    public function api_export($table) {
        $data = $this->db->get($table)->result_array();
        if (empty($data)) {
            $this->json_error('Aucune donnée'); return;
        }

        $filename = $table . '_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, array_keys($data[0]));
        $hasPwd = isset($data[0]['mot_de_passe']);
        foreach ($data as $row) {
            if ($hasPwd) $row['mot_de_passe'] = '***HIDDEN***';
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    public function api_preview($table) {
        $data = $this->db->get($table)->result_array();
        if (empty($data)) {
            $this->json_error('Aucune donnée'); return;
        }
        $headers = array_keys($data[0]);
        $rows = array_slice($data, 0, 10);
        $hasPwd = isset($data[0]['mot_de_passe']);
        if ($hasPwd) {
            foreach ($rows as &$row) $row['mot_de_passe'] = '***HIDDEN***';
        }
        $this->json_success([
            'headers' => $headers,
            'rows' => $rows,
            'total' => count($data)
        ]);
    }

    public function api_import_preview() {
        $file = $_FILES['file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->json_error('Fichier requis'); return;
        }

        $handle = fopen($file['tmp_name'], 'r');
        $headers = fgetcsv($handle);
        $rows = [];
        $maxRows = 10;
        while (($row = fgetcsv($handle)) !== false && count($rows) < $maxRows) {
            $rows[] = $row;
        }
        fclose($handle);

        $this->json_success([
            'headers' => $headers,
            'preview' => $rows,
            'total_rows' => count(file($file['tmp_name'])) - 1
        ]);
    }

    public function api_import() {
        $table = $this->input->post('table');
        $file = $_FILES['file'] ?? null;

        if (!$table || !$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->json_error('Table et fichier requis'); return;
        }

        $handle = fopen($file['tmp_name'], 'r');
        $headers = fgetcsv($handle);
        $columns = $this->db->list_fields($table);
        $colMap = [];
        foreach ($headers as $h) {
            if (in_array($h, $columns)) $colMap[] = $h;
        }

        $count = 0;
        $this->db->trans_start();
        while (($row = fgetcsv($handle)) !== false) {
            $data = [];
            foreach ($colMap as $i => $col) {
                if (isset($row[$i])) $data[$col] = $row[$i];
            }
            if (!empty($data)) {
                $this->Model->create($table, $data);
                $count++;
            }
        }
        fclose($handle);
        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $this->json_success(['imported' => $count], "$count enregistrements importés");
        } else {
            $this->json_error('Erreur lors de l\'import');
        }
    }
}
