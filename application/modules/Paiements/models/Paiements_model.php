<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paiements_model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = [])
    {
        $this->db->where('p.deleted_at', null);
        $this->db->select('p.*, e.nom, e.prenom, e.matricule, tf.libelle as type_frais, tf.code as type_code, i.id_classe, i.id_section');
        $this->db->from('paiements p');
        $this->db->join('etudiants e', 'p.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('frais f', 'p.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->join('inscriptions i', 'p.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL AND i.id_annee = ' . (int)$this->id_annee_active, 'left');

        if (!empty($filters['id_etudiant'])) $this->db->where('p.id_etudiant', $filters['id_etudiant']);
        if (!empty($filters['id_frais'])) $this->db->where('p.id_frais', $filters['id_frais']);
        if (!empty($filters['id_classe'])) $this->db->where('i.id_classe', $filters['id_classe']);
        if (!empty($filters['date_from'])) $this->db->where('p.date_paiement >=', $filters['date_from']);
        if (!empty($filters['date_to'])) $this->db->where('p.date_paiement <=', $filters['date_to']);

        $this->db->order_by('p.id_paiement', 'DESC');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->where('p.deleted_at', null);
        $this->db->where('p.uuid', $id);
        $this->db->select('p.*, e.nom, e.prenom, e.matricule, tf.libelle as type_frais, i.id_classe, i.id_section, r.numero_recu, r.uuid as recu_uuid');
        $this->db->from('paiements p');
        $this->db->join('etudiants e', 'p.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('frais f', 'p.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->join('inscriptions i', 'p.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL AND i.id_annee = ' . (int)$this->id_annee_active, 'left');
        $this->db->join('paiements_recus pr', 'p.id_paiement = pr.id_paiement', 'left');
        $this->db->join('recus r', 'pr.id_recu = r.id_recu', 'left');
        $q = $this->db->get();
        if ($q === false) return null;
        return $q->row_array();
    }

    public function create_record($data)
    {
        $required = ['id_etudiant', 'id_frais', 'montant', 'id_annee'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Champ requis manquant: $field"];
            }
        }

        // Vérifier que le frais existe
        $frais = $this->readOne('frais', ['id_frais' => $data['id_frais'], 'deleted_at' => null]);
        if (!$frais) {
            return ['success' => false, 'message' => 'Frais inexistant'];
        }

        $data['uuid'] = $data['uuid'] ?? generate_uuid();
        $data['date_paiement'] = $data['date_paiement'] ?? date('Y-m-d');
        $data['mode_paiement'] = $data['mode_paiement'] ?? 'especes';
        $data['statut'] = $data['statut'] ?? 'partiel';

        if ($this->db->insert('paiements', $data)) {
            return ['success' => true, 'id_paiement' => $this->db->insert_id()];
        }
        return ['success' => false, 'message' => 'Erreur insertion paiement'];
    }

    public function update_record($id, $data)
    {
        $this->db->where('uuid', $id);
        return $this->db->update('paiements', $data);
    }

    public function delete_record($id)
    {
        return $this->update_record($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    public function get_stats($id_annee = null)
    {
        $id_annee = $id_annee ?? $this->id_annee_active;
        $this->db->where('deleted_at', null);
        $this->db->where('id_annee', $id_annee);
        $total = $this->db->count_all_results('paiements');
        $this->db->where('statut', 'solde');
        $solde = $this->db->count_all_results('paiements');
        $this->db->where('statut', 'partiel');
        $partiel = $this->db->count_all_results('paiements');

        $this->db->select_sum('montant');
        $this->db->where('deleted_at', null);
        $this->db->where('id_annee', $id_annee);
        $montant_total = $this->db->get('paiements')->row()->montant_total ?? 0;

        return [
            'total' => $total,
            'solde' => $solde,
            'partiel' => $partiel,
            'montant_total' => $montant_total
        ];
    }
}