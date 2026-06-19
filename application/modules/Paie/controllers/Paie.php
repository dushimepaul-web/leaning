<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paie extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion de la paie';
        $data['employes'] = $this->Model->read('employes', ['deleted_at' => null, 'statut' => 'actif']);
        $this->load->view('index', $data);
    }

    public function api_contrats() {
        $this->db->where('c.deleted_at', null);
        $this->db->select('c.*, e.nom_complet, e.matricule');
        $this->db->from('paie_contrats c');
        $this->db->join('employes e', 'c.id_employe = e.id_employe', 'left');
        $this->db->order_by('c.id_contrat', 'DESC');
        $result = $this->db->get()->result_array();
        foreach ($result as &$r) {
            $r['salaire_base'] = $r['salaire_base'] ?? $r['salaire_brut'] ?? 0;
            $r['statut'] = $r['statut'] ?? ($r['actif'] ? 'actif' : 'termine');
            $r['type_employe'] = $r['type_employe'] ?? $r['type_contrat'] ?? 'cdi';
        }
        $this->json_success($result);
    }

    public function api_bulletins() {
        $this->db->where('b.deleted_at', null);
        $this->db->select('b.*, e.nom_complet, e.matricule');
        $this->db->from('paie_bulletins b');
        $this->db->join('paie_contrats c', 'b.id_contrat = c.id_contrat', 'left');
        $this->db->join('employes e', 'c.id_employe = e.id_employe', 'left');
        $this->db->order_by('b.annee DESC, b.mois DESC');
        $result = $this->db->get()->result_array();
        foreach ($result as &$r) {
            $r['salaire_base'] = $r['salaire_base'] ?? $r['salaire_brut'] ?? 0;
            $r['net_a_payer'] = $r['net_a_payer'] ?? $r['salaire_net'] ?? 0;
            $r['total_gains'] = $r['total_gains'] ?? 0;
            $r['total_retenues'] = $r['total_retenues'] ?? $r['deductions'] ?? 0;
            $r['statut'] = $r['statut'] ?? 'brouillon';
        }
        $this->json_success($result);
    }

    public function api_create_contrat() {
        $data = $this->get_json_input();
        if (empty($data['id_employe']) || empty($data['salaire_base'])) {
            $this->json_error('Employé et salaire de base obligatoires'); return;
        }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'id_employe' => $data['id_employe'],
            'type_contrat' => $data['type_employe'] ?? 'cdi',
            'salaire_base' => $data['salaire_base'],
            'date_debut' => !empty($data['date_debut']) ? $data['date_debut'] : date('Y-m-d'),
            'date_fin' => !empty($data['date_fin']) ? $data['date_fin'] : null,
            'actif' => ($data['statut'] ?? 'actif') === 'actif' ? 1 : 0,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ];
        $id = $this->Model->createLastId('paie_contrats', $insert);
        if ($id) $this->json_success(['id_contrat' => $id], 'Contrat créé');
        else $this->json_error('Erreur');
    }

    public function api_create_bulletin() {
        $data = $this->get_json_input();
        if (empty($data['id_contrat'])) { $this->json_error('Contrat obligatoire'); return; }
        $contrat = $this->Model->readOne('paie_contrats', ['id_contrat' => $data['id_contrat']]);
        if (!$contrat) { $this->json_error('Contrat non trouvé', 404); return; }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'id_contrat' => $data['id_contrat'],
            'mois' => !empty($data['mois']) ? $data['mois'] : date('m'),
            'annee' => !empty($data['annee']) ? $data['annee'] : date('Y'),
            'salaire_brut' => $contrat['salaire_base'],
            'salaire_net' => $contrat['salaire_base'],
            'date_edition' => date('Y-m-d'),
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ];
        $id = $this->Model->createLastId('paie_bulletins', $insert);
        if ($id) $this->json_success(['id_bulletin_paie' => $id], 'Bulletin créé');
        else $this->json_error('Erreur');
    }
}
