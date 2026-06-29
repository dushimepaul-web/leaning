<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Messages_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('m.deleted_at', null);
        $this->db->select('m.*, u1.nom as expediteur_nom, u1.prenom as expediteur_prenom, u2.nom as destinataire_nom, u2.prenom as destinataire_prenom');
        $this->db->from('messages m');
        $this->db->join('utilisateurs u1', 'm.id_expediteur = u1.id_utilisateur', 'left');
        $this->db->join('utilisateurs u2', 'm.id_destinataire = u2.id_utilisateur', 'left');
        if (!empty($filters['id_expediteur'])) $this->db->where('m.id_expediteur', $filters['id_expediteur']);
        if (!empty($filters['id_destinataire'])) $this->db->where('m.id_destinataire', $filters['id_destinataire']);
        $this->db->order_by('m.date_envoi', 'DESC');
        return $this->db->get()->result_array();
    }

    public function create($data)
    {
        $data['uuid'] = generate_uuid();
        $data['date_envoi'] = date('Y-m-d H:i:s');
        $data['lu'] = 0;
        return $this->db->insert('messages', $data) ? ['success' => true, 'id' => $this->db->insert_id()] : ['success' => false];
    }

    public function marquer_lu($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('messages', ['lu' => 1, 'date_lecture' => date('Y-m-d H:i:s')]);
    }

    public function delete($id)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('messages', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}