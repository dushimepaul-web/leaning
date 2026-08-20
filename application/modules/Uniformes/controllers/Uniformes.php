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
        $this->json_success($u);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle'])) { $this->json_error('Libellé obligatoire'); return; }
        $prix = $data['prix'] ?? $data['prix_unitaire'] ?? 0;
        $stock = $data['stock_actuel'] ?? $data['quantite_stock'] ?? 0;
        $stock_min = $data['stock_minimum'] ?? 5;
        if (!is_numeric($prix) || $prix < 0) { $this->json_error('Prix invalide'); return; }
        if (!is_numeric($stock) || $stock < 0) { $this->json_error('Stock invalide'); return; }
        if (!is_numeric($stock_min) || $stock_min < 0) { $this->json_error('Stock minimum invalide'); return; }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'libelle' => $data['libelle'],
            'taille' => $data['taille'] ?? null,
            'prix' => $prix,
            'stock_actuel' => $stock,
            'stock_minimum' => $stock_min,
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
        if (isset($data['prix_unitaire'])) { $update['prix'] = $data['prix_unitaire']; unset($data['prix_unitaire']); }
        if (isset($data['quantite_stock'])) { $update['stock_actuel'] = $data['quantite_stock']; unset($data['quantite_stock']); }
        $allowed = ['libelle', 'taille', 'prix', 'stock_actuel', 'stock_minimum'];
        foreach ($allowed as $col) {
            if (isset($data[$col])) {
                if (($col === 'prix' || $col === 'stock_actuel' || $col === 'stock_minimum') && (!is_numeric($data[$col]) || $data[$col] < 0)) {
                    $this->json_error('Valeur invalide pour ' . $col);
                    return;
                }
                $update[$col] = $data[$col];
            }
        }
        if (count($update) <= 1) { $this->json_error('Aucune donnée à modifier'); return; }
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
