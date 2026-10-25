<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Library_expenses extends MY_Controller
{
    private $modes = ['especes', 'banque', 'mobile_money', 'cheque', 'autre'];

    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->load->model('Library_model', 'library_model');
    }

    public function index()
    {
        $data['title'] = 'Librairie - Charges';
        $this->load->view('expenses', $data);
    }

    public function api_list()
    {
        $this->db->where('e.deleted_at', null);
        $this->db->select('e.*, u.nom_complet AS utilisateur');
        $this->db->from('library_expenses e');
        $this->db->join('utilisateurs u', 'u.id_utilisateur = e.id_utilisateur', 'left');
        if ($this->input->get('categorie')) $this->db->where('e.categorie', $this->input->get('categorie'));
        if ($this->input->get('date_from')) $this->db->where('e.date_expense >=', $this->input->get('date_from'));
        if ($this->input->get('date_to')) $this->db->where('e.date_expense <=', $this->input->get('date_to'));
        $this->db->order_by('e.date_expense', 'DESC')->order_by('e.id_expense', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_get($uuid)
    {
        $row = $this->db->get_where('library_expenses', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$row) { $this->json_error('Charge introuvable', 404); return; }
        $this->json_success($row);
    }

    public function api_create()
    {
        $data = $this->get_json_input();
        $libelle = trim($data['libelle'] ?? '');
        $montant = isset($data['montant']) ? (float)$data['montant'] : 0;
        $date = $data['date_expense'] ?? '';
        $mode = $data['mode_paiement'] ?? 'especes';
        if ($libelle === '' || $montant <= 0 || $date === '') { $this->json_error('Libelle, montant et date requis'); return; }
        if (!in_array($mode, $this->modes, true)) { $this->json_error('Mode de paiement invalide'); return; }
        $id = $this->Model->createLastId('library_expenses', [
            'libelle' => $libelle,
            'categorie' => trim($data['categorie'] ?? 'autre') ?: 'autre',
            'montant' => round($montant, 2),
            'date_expense' => $date,
            'mode_paiement' => $mode,
            'reference' => trim($data['reference'] ?? '') ?: null,
            'beneficiaire' => trim($data['beneficiaire'] ?? '') ?: null,
            'notes' => trim($data['notes'] ?? '') ?: null,
            'id_utilisateur' => $this->session->userdata('id_utilisateur') ?: null,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        if (!$id) { $this->json_error('Erreur lors de l\'enregistrement'); return; }
        $this->library_model->audit('create', 'library_expenses', $id, null, ['libelle' => $libelle, 'montant' => $montant]);
        $this->json_success(['id_expense' => $id], 'Charge enregistree');
    }

    public function api_update($uuid)
    {
        $data = $this->get_json_input();
        $row = $this->db->get_where('library_expenses', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$row) { $this->json_error('Charge introuvable', 404); return; }
        $update = [];
        foreach (['libelle', 'categorie', 'reference', 'beneficiaire', 'notes'] as $field) {
            if (array_key_exists($field, $data)) $update[$field] = trim($data[$field]) ?: null;
        }
        if (array_key_exists('montant', $data)) {
            $montant = (float)$data['montant'];
            if ($montant <= 0) { $this->json_error('Montant invalide'); return; }
            $update['montant'] = round($montant, 2);
        }
        if (array_key_exists('date_expense', $data)) {
            if (!$data['date_expense']) { $this->json_error('Date invalide'); return; }
            $update['date_expense'] = $data['date_expense'];
        }
        if (array_key_exists('mode_paiement', $data)) {
            if (!in_array($data['mode_paiement'], $this->modes, true)) { $this->json_error('Mode de paiement invalide'); return; }
            $update['mode_paiement'] = $data['mode_paiement'];
        }
        if (empty($update)) { $this->json_error('Aucune donnee a modifier'); return; }
        if (array_key_exists('libelle', $update) && !$update['libelle']) { $this->json_error('Le libelle est obligatoire'); return; }
        $update['modifie_le'] = date('Y-m-d H:i:s');
        if (!$this->Model->update('library_expenses', ['id_expense' => $row['id_expense']], $update)) {
            $this->json_error('Erreur lors de la mise a jour'); return;
        }
        $this->library_model->audit('update', 'library_expenses', $row['id_expense'], $row, $update);
        $this->json_success(null, 'Charge mise a jour');
    }

    public function api_delete($uuid)
    {
        $row = $this->db->get_where('library_expenses', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$row) { $this->json_error('Charge introuvable', 404); return; }
        if (!$this->Model->update('library_expenses', ['id_expense' => $row['id_expense']], ['deleted_at' => date('Y-m-d H:i:s')])) {
            $this->json_error('Erreur lors de la suppression'); return;
        }
        $this->library_model->audit('delete', 'library_expenses', $row['id_expense'], $row, null);
        $this->json_success(null, 'Charge supprimee');
    }
}
