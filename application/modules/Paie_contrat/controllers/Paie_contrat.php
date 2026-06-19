<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paie_contrat extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Contrats de paie';
        $data['employes'] = $this->Model->read('employes', ['deleted_at' => null, 'statut' => 'actif']);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('c.deleted_at', null);
        $this->db->select('c.*, e.nom_complet, e.matricule');
        $this->db->from('paie_contrats c');
        $this->db->join('employes e', 'c.id_employe = e.id_employe', 'left');
        $this->db->order_by('c.id_contrat', 'DESC');
        $result = $this->db->get()->result_array();
        foreach ($result as &$r) {
            $r['type_employe'] = $r['type_contrat'];
            $r['salaire_base'] = $r['salaire_brut'] ?? $r['salaire_base'] ?? 0;
            $r['statut'] = $r['actif'] ? 'actif' : 'termine';
        }
        $this->json_success($result);
    }

    public function api_get($id) {
        $this->db->where('c.uuid', $id);
        $this->db->select('c.*, e.nom_complet, e.matricule');
        $this->db->from('paie_contrats c');
        $this->db->join('employes e', 'c.id_employe = e.id_employe', 'left');
        $d = $this->db->get()->row_array();
        if (!$d) { $this->json_error('Contrat non trouvé', 404); return; }
        $d['type_employe'] = $d['type_contrat'];
        $d['salaire_base'] = $d['salaire_brut'] ?? $d['salaire_base'] ?? 0;
        $d['statut'] = $d['actif'] ? 'actif' : 'termine';
        $this->json_success($d);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_employe']) || empty($data['salaire_base'])) {
            $this->json_error('Employé et salaire de base obligatoires'); return;
        }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'id_employe' => $data['id_employe'],
            'type_contrat' => !empty($data['type_employe']) ? $data['type_employe'] : 'cdi',
            'salaire_base' => $data['salaire_base'],
            'salaire_brut' => $data['salaire_base'],
            'date_debut' => !empty($data['date_debut']) ? $data['date_debut'] : date('Y-m-d'),
            'date_fin' => !empty($data['date_fin']) ? $data['date_fin'] : null,
            'actif' => 1,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ];
        $id = $this->Model->createLastId('paie_contrats', $insert);
        if ($id) $this->json_success(['id_contrat' => $id], 'Contrat créé');
        else $this->json_error('Erreur de création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $update = ['modifie_le' => date('Y-m-d H:i:s')];
        if (isset($data['type_employe'])) $update['type_contrat'] = $data['type_employe'];
        unset($data['type_employe']);
        if (isset($data['salaire_base'])) { $update['salaire_base'] = $data['salaire_base']; $update['salaire_brut'] = $data['salaire_base']; unset($data['salaire_base']); }
        $update = array_merge($update, $data);
        if ($this->Model->update('paie_contrats', ['uuid' => $id], $update))
            $this->json_success(null, 'Contrat mis à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('paie_contrats', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s'), 'modifie_le' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Contrat supprimé');
        else $this->json_error('Erreur de suppression');
    }
}
