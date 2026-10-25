<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Minervale_base
{
    protected $db;
    protected $ci;

    public function __construct($db, $ci = null)
    {
        $this->db = $db;
        $this->ci = $ci;
    }

    protected function query($sql, $binds = null)
    {
        $result = $this->db->query($sql, is_array($binds) ? $binds : false);
        if ($result === false) {
            $error = method_exists($this->db, 'error') ? $this->db->error() : array();
            $message = isset($error['message']) ? $error['message'] : 'Erreur SQL';
            throw new RuntimeException($message);
        }
        return $result;
    }

    protected function rows($sql, $binds = null)
    {
        $result = $this->query($sql, $binds);
        return $result->result_array();
    }

    protected function row($sql, $binds = null)
    {
        $result = $this->query($sql, $binds);
        $row = $result->row_array();
        return $row ? $row : null;
    }

    protected function scalar($sql, $binds = null, $default = 0)
    {
        $row = $this->row($sql, $binds);
        if (!$row) {
            return $default;
        }
        $value = reset($row);
        return $value === null ? $default : $value;
    }

    protected function inClause(array $values)
    {
        if (empty($values)) {
            return array('sql' => 'NULL', 'binds' => array());
        }
        return array(
            'sql' => implode(',', array_fill(0, count($values), '?')),
            'binds' => array_values($values)
        );
    }

    protected function insert($table, array $data)
    {
        return $this->db->insert($table, $data);
    }

    protected function insertId()
    {
        return (int)$this->db->insert_id();
    }

    protected function update($table, array $where, array $data)
    {
        return $this->db->where($where)->update($table, $data);
    }

    protected function begin()
    {
        $this->db->trans_begin();
    }

    protected function commit()
    {
        $this->db->trans_commit();
    }

    protected function rollback()
    {
        $this->db->trans_rollback();
    }

    protected function transactionOk()
    {
        return $this->db->trans_status() !== false;
    }

    protected function now()
    {
        return date('Y-m-d H:i:s');
    }

    protected function today()
    {
        return date('Y-m-d');
    }

    protected function userId()
    {
        if ($this->ci) {
            try {
                $id = $this->ci->session->userdata('id_utilisateur');
                return $id ? (int)$id : null;
            } catch (Throwable $e) {
                return null;
            }
        }
        return null;
    }

    protected function ip()
    {
        if ($this->ci) {
            try {
                if (method_exists($this->ci->input, 'ip_address')) {
                    return $this->ci->input->ip_address();
                }
            } catch (Throwable $e) {
                return 'cli';
            }
        }
        return 'cli';
    }

    public static function uuid()
    {
        if (function_exists('generate_uuid')) {
            return generate_uuid();
        }
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    protected function fail($message, $code = 400)
    {
        throw new Minervale_exception($message, $code);
    }

    protected function activeYearId($fallback = 0)
    {
        if ($this->ci && !empty($this->ci->id_annee_active)) {
            return (int)$this->ci->id_annee_active;
        }
        $id = $this->scalar('SELECT id_annee FROM annees_scolaires WHERE est_en_cours = 1 AND deleted_at IS NULL ORDER BY id_annee DESC LIMIT 1', null, 0);
        return $id ? (int)$id : (int)$fallback;
    }

    protected function activePeriodId($fallback = 0)
    {
        if ($this->ci && !empty($this->ci->id_periode_active)) {
            return (int)$this->ci->id_periode_active;
        }
        $id = $this->scalar('SELECT id_periode FROM periodes WHERE est_en_cours = 1 AND deleted_at IS NULL ORDER BY id_periode DESC LIMIT 1', null, 0);
        return $id ? (int)$id : (int)$fallback;
    }
}

class Minervale_exception extends RuntimeException
{
}
