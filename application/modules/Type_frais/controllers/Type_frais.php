<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Type_frais extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des types de frais';
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->json_success($this->Model->read('types_frais', ['deleted_at' => null]));
    }

    public function api_get($id) {
        $d = $this->Model->readOne('types_frais', ['uuid' => $id]);
        if (!$d) { $this->json_error('Type de frais non trouvé', 404); return; }
        $this->json_success($d);
    }

    private function _slug_code($libelle) {
        $code = trim((string)$libelle);
        if (function_exists('iconv')) {
            $translit = @iconv('UTF-8', 'ASCII//TRANSLIT', $code);
            if ($translit !== false) $code = $translit;
        }
        $code = strtoupper($code);
        $code = preg_replace('/[^A-Z0-9]+/', '_', $code);
        $code = trim($code, '_');
        if ($code === '') $code = 'TYPE';
        return substr($code, 0, 30);
    }

    private function _generate_code($libelle) {
        $base = $this->_slug_code($libelle);
        $code = $base;
        $suffixe = 2;
        while ($this->db->where('code', $code)->count_all_results('types_frais') > 0) {
            $suffixe_txt = '_' . $suffixe;
            $code = substr($base, 0, 30 - strlen($suffixe_txt)) . $suffixe_txt;
            $suffixe++;
        }
        return $code;
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle'])) {
            $this->json_error('Le libellé est obligatoire'); return;
        }
        $allowed = ['libelle', 'description'];
        $insert = array_intersect_key($data, array_flip($allowed));
        $insert['code'] = $this->_generate_code($data['libelle']);
        $id = $this->Model->createLastId('types_frais', $insert);
        if ($id) $this->json_success(['id_type_frais' => $id], 'Type de frais créé');
        else $this->json_error('Erreur de création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (!$this->Model->readOne('types_frais', ['uuid' => $id])) {
            $this->json_error('Type de frais non trouvé', 404); return;
        }
        $allowed = ['libelle', 'description'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if ($this->Model->update('types_frais', ['uuid' => $id], $update))
            $this->json_success(null, 'Type de frais mis à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('types_frais', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Type de frais supprimé');
        else $this->json_error('Erreur de suppression');
    }
}
