<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Frais_model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = [])
    {
        $this->db->where('f.deleted_at', null);
        $this->db->select('f.*, tf.libelle as type_libelle, tf.code as type_code, c.libelle as classe_libelle, a.libelle as annee_libelle, p.libelle as periode_libelle');
        $this->db->select("COALESCE(c.libelle, 'Toutes les classes') as classe_label", false);
        $this->db->from('frais f');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->join('classes c', 'f.id_classe = c.id_classe', 'left');
        $this->db->join('annees_scolaires a', 'f.id_annee = a.id_annee', 'left');
        $this->db->join('periodes p', 'f.id_periode = p.id_periode', 'left');

        if (!empty($filters['id_type_frais'])) $this->db->where('f.id_type_frais', $filters['id_type_frais']);
        if (!empty($filters['id_classe'])) $this->db->where('f.id_classe', $filters['id_classe']);
        if (!empty($filters['id_annee'])) $this->db->where('f.id_annee', $filters['id_annee']);
        if (!empty($filters['id_periode'])) $this->db->where('f.id_periode', $filters['id_periode']);

        $this->db->order_by('f.id_frais', 'DESC');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->where('f.deleted_at', null);
        $this->db->where('f.uuid', $id);
        $this->db->select('f.*, tf.libelle as type_libelle, c.libelle as classe_libelle, a.libelle as annee_libelle, p.libelle as periode_libelle');
        $this->db->from('frais f');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->join('classes c', 'f.id_classe = c.id_classe', 'left');
        $this->db->join('annees_scolaires a', 'f.id_annee = a.id_annee', 'left');
        $this->db->join('periodes p', 'f.id_periode = p.id_periode', 'left');
        $q = $this->db->get();
        if ($q === false) return null;
        return $q->row_array();
    }

    public function create_record($data)
    {
        $required = ['id_type_frais', 'id_annee', 'id_periode', 'montant'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Champ requis manquant: $field"];
            }
        }
        if (!array_key_exists('id_classe', $data)) {
            $data['id_classe'] = null;
        }

        $data['uuid'] = generate_uuid();
        $data['cree_le'] = date('Y-m-d H:i:s');
        $data['modifie_le'] = date('Y-m-d H:i:s');

        if ($this->db->insert('frais', $data)) {
            return ['success' => true, 'id_frais' => $this->db->insert_id()];
        }
        return ['success' => false, 'message' => 'Erreur insertion frais'];
    }

    public function update_record($id, $data)
    {
        $this->db->where('uuid', $id);
        $data['modifie_le'] = date('Y-m-d H:i:s');
        return $this->db->update('frais', $data);
    }

    public function delete_record($id)
    {
        return $this->update_record($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    public function get_echeances($id_frais)
    {
        return $this->read('echeances', ['id_frais' => $id_frais, 'deleted_at' => null], 'date_echeance');
    }
}