<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Uniformes extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des uniformes';
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('deleted_at', null);
        $this->db->order_by('id_uniforme', 'DESC');
        $this->json_success($this->db->get('uniformes')->result_array());
    }

    public function api_get($id) {
        $u = $this->Model->readOne('uniformes', ['uuid' => $id]);
        if (!$u) { $this->json_error('Uniforme non trouvé', 404); return; }
        $u['stock_actuel'] = $u['quantite_stock'];
        $u['prix'] = $u['prix_unitaire'];
        $this->json_success($u);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle'])) { $this->json_error('Libellé obligatoire'); return; }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'libelle' => $data['libelle'],
            'taille' => $data['taille'] ?? null,
            'prix_unitaire' => $data['prix'] ?? $data['prix_unitaire'] ?? 0,
            'quantite_stock' => $data['stock_actuel'] ?? $data['quantite_stock'] ?? 0,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ];
        $id = $this->Model->createLastId('uniformes', $insert);
        if ($id) $this->json_success(['id_uniforme' => $id], 'Uniforme créé');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $update = ['modifie_le' => date('Y-m-d H:i:s')];
        if (isset($data['prix'])) { $update['prix_unitaire'] = $data['prix']; unset($data['prix']); }
        if (isset($data['stock_actuel'])) { $update['quantite_stock'] = $data['stock_actuel']; unset($data['stock_actuel']); }
        if (isset($data['prix_unitaire'])) { $update['prix_unitaire'] = $data['prix_unitaire']; unset($data['prix_unitaire']); }
        unset($data['stock_minimum']);
        $update = array_merge($update, $data);
        if ($this->Model->update('uniformes', ['uuid' => $id], $update))
            $this->json_success(null, 'Uniforme mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('uniformes', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s'), 'modifie_le' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Uniforme supprimé');
        else $this->json_error('Erreur');
    }
}
