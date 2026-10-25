<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_Categories extends MY_Controller {
    public function __construct() { parent::__construct(); $this->not_logged_in(); }

    public function index() {
        $data['title'] = 'Gestion des Catégories';
        $data['categories'] = $this->Model->read('categories_produits', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('deleted_at', null);
        $this->db->order_by('libelle');
        $this->json_success($this->db->get('categories_produits')->result_array());
    }

    public function api_get($id) {
        $c = $this->Model->readOne('categories_produits', ['uuid' => $id]);
        if (!$c) { $this->json_error('Catégorie non trouvée', 404); return; }
        $this->json_success($c);
    }

    public function api_create() {
        $data = $this->get_json_input();
        $libelle = trim($data['libelle'] ?? '');
        if ($libelle === '') { $this->json_error('Le libellé est obligatoire'); return; }
        $insert = [
            'code' => $this->_generate_code($libelle),
            'libelle' => $libelle,
        ];
        $id = $this->Model->createLastId('categories_produits', $insert);
        if ($id) $this->json_success(null, 'Catégorie créée');
        else $this->json_error('Erreur lors de la création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (!$this->Model->readOne('categories_produits', ['uuid' => $id])) {
            $this->json_error('Catégorie non trouvée', 404); return;
        }
        $update = [];
        if (array_key_exists('libelle', $data)) {
            $libelle = trim($data['libelle']);
            if ($libelle === '') { $this->json_error('Le libellé est obligatoire'); return; }
            $update['libelle'] = $libelle;
        }
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if ($this->Model->update('categories_produits', ['uuid' => $id], $update))
            $this->json_success(null, 'Catégorie mise à jour');
        else $this->json_error('Erreur lors de la mise à jour');
    }

    private function _generate_code($libelle) {
        $code = strtoupper(strtr($libelle, [
            'À'=>'A','Á'=>'A','Â'=>'A','Ã'=>'A','Ä'=>'A','Å'=>'A',
            'Ç'=>'C','È'=>'E','É'=>'E','Ê'=>'E','Ë'=>'E',
            'Ì'=>'I','Í'=>'I','Î'=>'I','Ï'=>'I','Ñ'=>'N',
            'Ò'=>'O','Ó'=>'O','Ô'=>'O','Õ'=>'O','Ö'=>'O',
            'Ù'=>'U','Ú'=>'U','Û'=>'U','Ü'=>'U','Ý'=>'Y','Ÿ'=>'Y',
        ]));
        $code = preg_replace('/[^A-Z0-9]+/', '_', $code);
        $code = trim($code, '_');
        if ($code === '') $code = 'CAT';
        $base = substr($code, 0, 20);
        $code = $base;
        $i = 2;
        while ($this->db->get_where('categories_produits', ['code' => $code])->row_array()) {
            $code = substr($base, 0, 17) . '_' . $i++;
        }
        return $code;
    }

    public function api_delete($id) {
        if ($this->Model->update('categories_produits', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Catégorie supprimée');
        else $this->json_error('Erreur lors de la suppression');
    }
}
