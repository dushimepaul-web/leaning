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
        $this->db->select('f.*, tf.libelle as type_libelle, tf.code as type_code, c.libelle as classe_libelle, a.libelle as annee_libelle');
        $this->db->from('frais f');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->join('classes c', 'f.id_classe = c.id_classe', 'left');
        $this->db->join('annees_scolaires a', 'f.id_annee = a.id_annee', 'left');

        if (!empty($filters['id_type_frais'])) $this->db->where('f.id_type_frais', $filters['id_type_frais']);
        if (!empty($filters['id_classe'])) $this->db->where('f.id_classe', $filters['id_classe']);
        if (!empty($filters['id_annee'])) $this->db->where('f.id_annee', $filters['id_annee']);

        $this->db->order_by('f.id_frais', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->where('f.deleted_at', null);
        $this->db->where('f.uuid', $id);
        $this->db->select('f.*, tf.libelle as type_libelle, c.libelle as classe_libelle, a.libelle as annee_libelle');
        $this->db->from('frais f');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->join('classes c', 'f.id_classe = c.id_classe', 'left');
        $this->db->join('annees_scolaires a', 'f.id_annee = a.id_annee', 'left');
        return $this->db->get()->row_array();
    }

    public function create($data)
    {
        $required = ['id_type_frais', 'id_classe', 'id_annee', 'montant'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Champ requis manquant: $field"];
            }
        }

        $data['uuid'] = generate_uuid();
        $data['cree_le'] = date('Y-m-d H:i:s');
        $data['modifie_le'] = date('Y-m-d H:i:s');

        if ($this->db->insert('frais', $data)) {
            return ['success' => true, 'id_frais' => $this->db->insert_id()];
        }
        return ['success' => false, 'message' => 'Erreur insertion frais'];
    }

    public function update($id, $data)
    {
        $this->db->where('uuid', $id);
        $data['modifie_le'] = date('Y-m-d H:i:s');
        return $this->db->update('frais', $data);
    }

    public function delete($id)
    {
        return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    public function get_echeances($id_frais)
    {
        return $this->read('echeances', ['id_frais' => $id_frais, 'deleted_at' => null], 'date_echeance');
    }
}