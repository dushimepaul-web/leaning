<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Library_suppliers extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->load->model('Library_model', 'library_model');
    }

    public function index()
    {
        $data['title'] = 'Librairie - Fournisseurs';
        $this->load->view('suppliers', $data);
    }

    public function api_list()
    {
        $this->db->where('s.deleted_at', null);
        $this->db->select('s.*, (SELECT COUNT(*) FROM library_purchases p WHERE p.id_supplier = s.id_supplier AND p.deleted_at IS NULL) AS nb_achats');
        $this->db->from('library_suppliers s');
        $q = $this->input->get('q');
        if ($q) {
            $this->db->group_start()->like('s.nom', $q)->or_like('s.code', $q)->or_like('s.telephone', $q)->group_end();
        }
        $this->db->order_by('s.nom', 'ASC');
        $rows = $this->db->get();
        $this->json_success($rows !== false ? $rows->result_array() : []);
    }

    public function api_get($uuid)
    {
        $row = $this->db->get_where('library_suppliers', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$row) { $this->json_error('Fournisseur introuvable', 404); return; }
        $this->json_success($row);
    }

    public function api_create()
    {
        $data = $this->get_json_input();
        $nom = trim($data['nom'] ?? '');
        if ($nom === '') { $this->json_error('Le nom du fournisseur est obligatoire'); return; }
        $code = $this->library_model->next_number('SUP', 'library_suppliers', 'code');
        $base = $code;
        $i = 2;
        while ($this->db->get_where('library_suppliers', ['code' => $code])->row_array()) {
            $code = $base . '-' . $i++;
        }
        $id = $this->Model->createLastId('library_suppliers', [
            'code' => $code,
            'nom' => $nom,
            'contact_nom' => trim($data['contact_nom'] ?? '') ?: null,
            'telephone' => trim($data['telephone'] ?? '') ?: null,
            'email' => trim($data['email'] ?? '') ?: null,
            'adresse' => trim($data['adresse'] ?? '') ?: null,
            'notes' => trim($data['notes'] ?? '') ?: null,
            'actif' => isset($data['actif']) ? (int)(bool)$data['actif'] : 1,
            'id_utilisateur' => $this->session->userdata('id_utilisateur') ?: null,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        if (!$id) { $this->json_error('Erreur lors de la creation'); return; }
        $this->library_model->audit('create', 'library_suppliers', $id, null, ['nom' => $nom, 'code' => $code]);
        $this->json_success(['id_supplier' => $id, 'code' => $code], 'Fournisseur cree');
    }

    public function api_update($uuid)
    {
        $data = $this->get_json_input();
        $row = $this->db->get_where('library_suppliers', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$row) { $this->json_error('Fournisseur introuvable', 404); return; }
        $allowed = ['nom', 'contact_nom', 'telephone', 'email', 'adresse', 'notes', 'actif'];
        $update = [];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $update[$field] = is_string($data[$field]) ? trim($data[$field]) : $data[$field];
                if ($update[$field] === '') $update[$field] = null;
            }
        }
        if (array_key_exists('nom', $update) && !$update['nom']) { $this->json_error('Le nom est obligatoire'); return; }
        if (array_key_exists('actif', $update)) $update['actif'] = (int)(bool)$update['actif'];
        if (empty($update)) { $this->json_error('Aucune donnee a modifier'); return; }
        $update['modifie_le'] = date('Y-m-d H:i:s');
        if (!$this->Model->update('library_suppliers', ['id_supplier' => $row['id_supplier']], $update)) {
            $this->json_error('Erreur lors de la mise a jour'); return;
        }
        $this->library_model->audit('update', 'library_suppliers', $row['id_supplier'], $row, $update);
        $this->json_success(null, 'Fournisseur mis a jour');
    }

    public function api_delete($uuid)
    {
        $row = $this->db->get_where('library_suppliers', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$row) { $this->json_error('Fournisseur introuvable', 404); return; }
        $linked = $this->db->where('id_supplier', $row['id_supplier'])->where('deleted_at', null)->count_all_results('library_purchases');
        if ($linked > 0) { $this->json_error('Suppression impossible : ce fournisseur a des approvisionnements.'); return; }
        if (!$this->Model->update('library_suppliers', ['id_supplier' => $row['id_supplier']], ['deleted_at' => date('Y-m-d H:i:s')])) {
            $this->json_error('Erreur lors de la suppression'); return;
        }
        $this->library_model->audit('delete', 'library_suppliers', $row['id_supplier'], $row, null);
        $this->json_success(null, 'Fournisseur supprime');
    }
}
