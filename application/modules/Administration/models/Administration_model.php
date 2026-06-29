<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administration_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_tables()
    {
        $tables = [];
        $result = $this->db->query("SHOW TABLES");
        foreach ($result->result_array() as $row) {
            $tables[] = array_values($row)[0];
        }
        return $tables;
    }

    public function get_table_structure($table)
    {
        return $this->db->query("DESCRIBE `$table`")->result_array();
    }

    public function export_table($table)
    {
        return $this->db->get($table)->result_array();
    }

    public function import_table($table, $data)
    {
        $this->db->truncate($table);
        return $this->db->insert_batch($table, $data);
    }

    public function create_backup($table)
    {
        $data = $this->export_table($table);
        $filename = $table . '_' . date('Ymd_His') . '.json';
        $path = FCPATH . 'backups/' . $filename;
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
        return ['success' => true, 'file' => $filename];
    }
}