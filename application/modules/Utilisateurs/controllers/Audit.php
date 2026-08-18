<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Audit extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = "Journal d'audit";
        $this->load->view('audit', $data);
    }

    public function api_list() {
        $this->db->select('a.*, u.nom_complet');
        $this->db->from('audit_logs a');
        $this->db->join('utilisateurs u', 'a.id_utilisateur = u.id_utilisateur', 'left');
        $this->db->order_by('a.date_action', 'DESC');
        $this->db->limit(500);
        $ql = $this->db->get();
        $logs = $ql !== false ? $ql->result_array() : array();
        foreach ($logs as &$log) {
            if (isset($log['nouvelles_valeurs']) && is_string($log['nouvelles_valeurs'])) {
                $log['nouvelles_valeurs'] = json_decode($log['nouvelles_valeurs'], true);
            }
            if (isset($log['anciennes_valeurs']) && is_string($log['anciennes_valeurs'])) {
                $log['anciennes_valeurs'] = json_decode($log['anciennes_valeurs'], true);
            }
            $log['nom_utilisateur'] = $log['nom_complet'] ?? 'Inconnu';
        }
        $this->json_success($logs);
    }
}
