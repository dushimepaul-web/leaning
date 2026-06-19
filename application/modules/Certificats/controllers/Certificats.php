<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Certificats extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Certificats';
        $data['etudiants'] = $this->Model->read('etudiants', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('c.deleted_at', null);
        $this->db->select('c.*, e.nom, e.prenom, e.matricule');
        $this->db->from('certificats c');
        $this->db->join('etudiants e', 'c.id_etudiant = e.id_etudiant', 'left');
        $this->db->order_by('c.id_certificat', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant']) || empty($data['type_certificat'])) {
            $this->json_error('Étudiant et type obligatoires'); return;
        }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'id_etudiant' => $data['id_etudiant'],
            'type_certificat' => $data['type_certificat'],
            'numero_certificat' => !empty($data['numero_certificat']) ? $data['numero_certificat'] : 'CERT-'.strtoupper(uniqid()),
            'date_emission' => !empty($data['date_emission']) ? $data['date_emission'] : date('Y-m-d'),
            'statut' => $data['statut'] ?? 'actif',
            'signataire' => $data['signataire'] ?? null,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ];
        $id = $this->Model->createLastId('certificats', $insert);
        if ($id) $this->json_success(['id_certificat' => $id], 'Certificat créé');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $allowed = ['type_certificat', 'motif', 'date_emission', 'numero_certificat', 'statut', 'signataire'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        $update['modifie_le'] = date('Y-m-d H:i:s');
        if ($this->Model->update('certificats', ['uuid' => $id], $update))
            $this->json_success(null, 'Certificat mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('certificats', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s'), 'modifie_le' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Certificat supprimé');
        else $this->json_error('Erreur');
    }
}
