<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model extends CI_Model
{
    function create($table, $data)
    {
        if (!isset($data['uuid'])) {
            $this->load->helper('uuid');
            $data['uuid'] = generate_uuid();
        }
        $query = $this->db->insert($table, $data);
        return ($query) ? true : false;
    }

    function read($table, $criteres = array(), $order_by_column = null, $order = 'DESC')
    {
        if (!empty($criteres)) {
            $this->db->where($criteres);
        }
        if (!empty($order_by_column)) {
            $this->db->order_by($order_by_column, $order);
        }
        $query = $this->db->get($table);
        if ($query === false) return array();
        return $query->result_array();
    }

    function readWhereIdIn($table, $ids = array(), $id_column = 'id')
    {
        if (empty($ids)) return array();
        $this->db->where('deleted_at', null);
        $this->db->where_in($id_column, $ids);
        $query = $this->db->get($table);
        if ($query === false) return array();
        return $query->result_array();
    }

    function update($table, $criteres, $data)
    {
        $this->db->where($criteres);
        $query = $this->db->update($table, $data);
        return ($query) ? true : false;
    }

    function updateReturnAffectedRow($table, $criteres, $data)
    {
        $this->db->where($criteres);
        $this->db->update($table, $data);
        $affected_rows = $this->db->affected_rows();
        if ($affected_rows > 0) {
            $query = $this->db->get_where($table, $criteres);
            if ($query === false) return null;
            return $query->row_array();
        }
        return null;
    }

    function delete($table, $criteres)
    {
        $this->db->where($criteres);
        $query = $this->db->delete($table);
        return ($query) ? true : false;
    }

    function readOne($table, $criteres)
    {
        $this->db->where($criteres);
        $query = $this->db->get($table);
        if ($query === false) return null;
        return $query->row_array();
    }

    function readQuery($query, $bindings = null)
    {
        if (!is_null($bindings) && !empty($bindings)) {
            $query = $this->db->query($query, $bindings);
        } else {
            $query = $this->db->query($query);
        }
        if ($query === false) return array();
        return $query->result_array();
    }

    function readQueryOne($query, $bindings = null)
    {
        if (!is_null($bindings) && !empty($bindings)) {
            $query = $this->db->query($query, $bindings);
        } else {
            $query = $this->db->query($query);
        }
        if ($query === false) return null;
        return $query->row_array();
    }

    function createLastId($table, $data)
    {
        if (!isset($data['uuid'])) {
            $this->load->helper('uuid');
            $data['uuid'] = generate_uuid();
        }
        $this->db->db_debug = false;
        $query = $this->db->insert($table, $data);
        if ($query) {
            return $this->db->insert_id();
        }
        return false;
    }

    function createBatch($table, $data)
    {
        if (empty($data)) return false;
        $query = $this->db->insert_batch($table, $data);
        return ($query) ? true : false;
    }

    function readLimit($table, $limit)
    {
        $this->db->limit($limit);
        $query = $this->db->get($table);
        if ($query === false) return array();
        return $query->result_array();
    }

    function updateBatch($table, $data, $where_key = 'uuid')
    {
        if (empty($data)) return false;
        $query = $this->db->update_batch($table, $data, $where_key);
        return ($query) ? true : false;
    }

    function checkValue($table, $criteres)
    {
        $this->db->where($criteres);
        $query = $this->db->get($table);
        if ($query === false) return false;
        return ($query->num_rows() > 0);
    }

    function count($table)
    {
        try {
            return $this->db->count_all($table);
        } catch (Exception $e) {
            log_message('error', 'count() failed for table: ' . $table . ' - ' . $e->getMessage());
            return 0;
        } catch (Throwable $e) {
            log_message('error', 'count() failed for table: ' . $table . ' - ' . $e->getMessage());
            return 0;
        }
    }

    function countWhere($table, $where)
    {
        try {
            $this->db->where($where);
            return $this->db->count_all_results($table);
        } catch (Exception $e) {
            log_message('error', 'countWhere() failed for table: ' . $table . ' - ' . $e->getMessage());
            return 0;
        } catch (Throwable $e) {
            log_message('error', 'countWhere() failed for table: ' . $table . ' - ' . $e->getMessage());
            return 0;
        }
    }

    // ---- Authentification vip_school ----
    public function login($username, $password)
    {
        if ($username && $password) {
            $sql = "SELECT * FROM utilisateurs WHERE email = ? AND actif = 1 AND deleted_at IS NULL";
            $query = $this->db->query($sql, array($username));
            if ($query !== false && $query->num_rows() == 1) {
                $result = $query->row_array();
                if (password_verify($password, $result['mot_de_passe'])) {
                    return $result;
                }
                if (md5($password) === $result['mot_de_passe']) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $this->db->where('id_utilisateur', $result['id_utilisateur']);
                    $this->db->update('utilisateurs', ['mot_de_passe' => $hash]);
                    return $result;
                }
            }
        }
        return false;
    }

    public function check_email($email)
    {
        if ($email) {
            $sql = 'SELECT * FROM utilisateurs WHERE email = ? AND actif = 1 AND deleted_at IS NULL';
            $query = $this->db->query($sql, array($email));
            return ($query !== false && $query->num_rows() == 1);
        }
        return false;
    }

    public function Set_History($idUser, $action, $description, $table = '', $id_enregistrement = null, $old_values = null, $new_values = null)
    {
        if (!function_exists('generate_uuid')) {
            $this->load->helper('uuid');
        }
        $data = array(
            'uuid' => generate_uuid(),
            'id_utilisateur' => $idUser,
            'action' => $action,
            'adresse_ip' => $this->input->ip_address()
        );

        if (!empty($table)) $data['table_concernee'] = $table;
        if (!is_null($id_enregistrement)) $data['id_enregistrement'] = $id_enregistrement;
        if (!is_null($old_values)) $data['anciennes_valeurs'] = is_string($old_values) ? $old_values : json_encode($old_values);
        if (!is_null($new_values)) $data['nouvelles_valeurs'] = is_string($new_values) ? $new_values : json_encode($new_values);

        if (empty($data['table_concernee']) && empty($data['nouvelles_valeurs'])) {
            $data['nouvelles_valeurs'] = json_encode(['description' => $description]);
        }

        $this->db->insert('audit_logs', $data);
    }

    public function get_setting($key, $default = null)
    {
        try {
            $this->db->select('valeur');
            $this->db->from('parametres');
            $this->db->where('clef', $key);
            $query = $this->db->get();
            if ($query !== false && $query->num_rows() > 0) {
                return $query->row()->valeur;
            }
        } catch (Exception $e) {
            log_message('error', 'get_setting() failed for key: ' . $key . ' - ' . $e->getMessage());
        } catch (Throwable $e) {
            log_message('error', 'get_setting() failed for key: ' . $key . ' - ' . $e->getMessage());
        }
        return $default;
    }

    public function setValueStore($key, $value)
    {
        $this->load->helper('uuid');
        $this->db->where('clef', $key);
        $query = $this->db->get('parametres');
        if ($query !== false && $query->num_rows() > 0) {
            $this->db->where('clef', $key);
            $this->db->update('parametres', array('valeur' => $value));
        } else {
            $data = array('valeur' => $value, 'clef' => $key, 'uuid' => generate_uuid());
            $this->db->insert('parametres', $data);
        }
    }

    public function get_statistics()
    {
        $data['total_etudiants'] = $this->countWhere('etudiants', ['deleted_at' => null]);
        $data['total_enseignants'] = $this->countWhere('enseignants', ['deleted_at' => null]);
        $data['total_classes'] = $this->countWhere('classes', ['deleted_at' => null]);
        $data['total_inscriptions'] = $this->countWhere('inscriptions', ['deleted_at' => null]);
        $data['total_paiements'] = $this->countWhere('paiements', ['deleted_at' => null]);
        return $data;
    }
}
