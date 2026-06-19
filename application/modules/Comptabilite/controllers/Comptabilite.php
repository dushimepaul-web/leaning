<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comptabilite extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Comptabilité';
        $data['plans'] = $this->Model->read('comptabilite_plans', ['actif' => 1, 'deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_plans() {
        $this->json_success($this->Model->read('comptabilite_plans', ['deleted_at' => null]));
    }

    public function api_list() {
        $this->db->where('e.deleted_at', null);
        $this->db->select('e.*, p.code_compte, p.libelle as plan_libelle');
        $this->db->from('comptabilite_ecritures e');
        $this->db->join('comptabilite_plans p', 'e.id_plan = p.id_plan', 'left');
        $this->db->order_by('e.date_ecriture', 'DESC');
        $result = $this->db->get()->result_array();
        foreach ($result as &$r) {
            $r['debit'] = $r['type'] === 'debit' ? floatval($r['montant']) : 0;
            $r['credit'] = $r['type'] === 'credit' ? floatval($r['montant']) : 0;
        }
        $this->json_success($result);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_plan']) || empty($data['libelle'])) {
            $this->json_error('Plan comptable et libellé obligatoires'); return;
        }
        $this->load->helper('uuid');
        $debit = floatval($data['debit'] ?? 0);
        $credit = floatval($data['credit'] ?? 0);
        $insert = [
            'uuid' => generate_uuid(),
            'id_plan' => $data['id_plan'],
            'libelle' => $data['libelle'],
            'description' => $data['description'] ?? null,
            'montant' => $debit > 0 ? $debit : $credit,
            'type' => $debit > 0 ? 'debit' : 'credit',
            'date_ecriture' => !empty($data['date_ecriture']) ? $data['date_ecriture'] : date('Y-m-d'),
            'id_etudiant' => $data['id_etudiant'] ?? null,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ];
        $id = $this->Model->createLastId('comptabilite_ecritures', $insert);
        if ($id) $this->json_success(['id_ecriture' => $id], 'Écriture comptable créée');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $update = ['modifie_le' => date('Y-m-d H:i:s')];
        if (isset($data['debit']) || isset($data['credit'])) {
            $debit = floatval($data['debit'] ?? 0);
            $credit = floatval($data['credit'] ?? 0);
            $update['montant'] = $debit > 0 ? $debit : $credit;
            $update['type'] = $debit > 0 ? 'debit' : 'credit';
            unset($data['debit'], $data['credit']);
        }
        $allowed = ['id_plan', 'libelle', 'description', 'date_ecriture', 'id_etudiant'];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) $update[$key] = $data[$key];
        }
        if (count($update) <= 1) { $this->json_error('Aucune donnée à modifier'); return; }
        if ($this->Model->update('comptabilite_ecritures', ['uuid' => $id], $update))
            $this->json_success(null, 'Écriture mise à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('comptabilite_ecritures', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s'), 'modifie_le' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Écriture supprimée');
        else $this->json_error('Erreur');
    }
}
