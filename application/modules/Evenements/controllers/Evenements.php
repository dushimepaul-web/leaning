<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Evenements extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Événements';
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('deleted_at', null);
        $this->db->order_by('date_debut', 'DESC');
        $this->json_success($this->db->get('evenements')->result_array());
    }

    private function _normalize_date($value) {
        if (empty($value)) return null;
        $v = trim($value);
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}(\s+\d{2}:\d{2})?$/', $v)) {
            $fmt = 'd/m/Y' . (strpos($v, ':') !== false ? ' H:i' : '');
            $dt = DateTime::createFromFormat($fmt, $v);
            if ($dt === false) return null;
            $time = strpos($v, ':') !== false ? ' H:i:s' : ' 00:00:00';
            return $dt->format('Y-m-d' . $time);
        }
        return $v;
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['titre']) || empty($data['date_debut'])) {
            $this->json_error('Titre et date début obligatoires'); return;
        }
        $allowed = ['titre', 'description', 'date_debut', 'date_fin', 'lieu', 'type', 'couleur', 'statut'];
        $insert = array_intersect_key($data, array_flip($allowed));
        $insert['type'] = in_array($insert['type'] ?? '', ['scolaire', 'reunion', 'activite', 'examen', 'autre'], true) ? $insert['type'] : 'scolaire';
        $insert['statut'] = in_array($insert['statut'] ?? '', ['planifie', 'en_cours', 'termine', 'annule'], true) ? $insert['statut'] : 'planifie';
        $date_debut = $this->_normalize_date($insert['date_debut'] ?? '');
        if (!$date_debut || strtotime($date_debut) === false) { $this->json_error('Date début invalide'); return; }
        $insert['date_debut'] = $date_debut;
        if (isset($insert['date_fin']) && $insert['date_fin'] !== '' && $insert['date_fin'] !== null) {
            $date_fin = $this->_normalize_date($insert['date_fin']);
            if (!$date_fin || strtotime($date_fin) === false) { $this->json_error('Date fin invalide'); return; }
            if (strtotime($date_fin) < strtotime($date_debut)) { $this->json_error('La date de fin précède la date de début'); return; }
            $insert['date_fin'] = $date_fin;
        } else {
            unset($insert['date_fin']);
        }
        $insert['id_utilisateur_createur'] = $this->session->userdata('id_utilisateur');
        $id = $this->Model->createLastId('evenements', $insert);
        if ($id) $this->json_success(['id_evenement' => $id], 'Événement créé');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $allowed = ['titre', 'description', 'date_debut', 'date_fin', 'lieu', 'type', 'couleur', 'statut'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if (isset($update['type']) && !in_array($update['type'], ['scolaire', 'reunion', 'activite', 'examen', 'autre'], true)) {
            $this->json_error('Type invalide'); return;
        }
        if (isset($update['statut']) && !in_array($update['statut'], ['planifie', 'en_cours', 'termine', 'annule'], true)) {
            $this->json_error('Statut invalide'); return;
        }
        if (isset($update['date_debut'])) {
            $date_debut = $this->_normalize_date($update['date_debut']);
            if (!$date_debut || strtotime($date_debut) === false) { $this->json_error('Date début invalide'); return; }
            $update['date_debut'] = $date_debut;
        }
        if (isset($update['date_fin']) && $update['date_fin'] !== '' && $update['date_fin'] !== null) {
            $date_fin = $this->_normalize_date($update['date_fin']);
            if (!$date_fin || strtotime($date_fin) === false) { $this->json_error('Date fin invalide'); return; }
            $update['date_fin'] = $date_fin;
        } else {
            unset($update['date_fin']);
        }
        if ($this->Model->update('evenements', ['uuid' => $id], $update))
            $this->json_success(null, 'Événement mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('evenements', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Événement supprimé');
        else $this->json_error('Erreur');
    }
}
