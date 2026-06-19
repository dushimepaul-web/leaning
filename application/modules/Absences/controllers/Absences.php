<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absences extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des absences';
        $data['etudiants'] = $this->Model->read('etudiants', ['deleted_at' => null]);
        $data['enseignants'] = $this->Model->read('enseignants', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('a.deleted_at', null);
        $this->db->select('a.*, e.nom, e.prenom, e.matricule');
        $this->db->from('absences a');
        $this->db->join('etudiants e', 'a.id_etudiant = e.id_etudiant', 'left');
        $this->db->order_by('a.date_absence', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant']) || empty($data['date_absence'])) {
            $this->json_error('Étudiant et date obligatoires'); return;
        }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'id_etudiant' => $data['id_etudiant'],
            'date_absence' => $data['date_absence'],
            'motif' => $data['motif'] ?? null,
            'justifiee' => $data['justifiee'] ?? 0,
            'type_absence' => 'etudiant',
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ];
        $id = $this->Model->createLastId('absences', $insert);
        if ($id) $this->json_success(['id_absence' => $id], 'Absence enregistrée');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $allowed = ['date_absence', 'motif', 'justifiee', 'type_absence'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        $update['modifie_le'] = date('Y-m-d H:i:s');
        if ($this->Model->update('absences', ['uuid' => $id], $update))
            $this->json_success(null, 'Absence mise à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('absences', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Absence supprimée');
        else $this->json_error('Erreur');
    }
}
