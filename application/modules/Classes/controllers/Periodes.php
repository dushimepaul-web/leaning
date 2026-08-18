<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Periodes extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des périodes';
        $data['annees'] = $this->Model->read('annees_scolaires', ['deleted_at' => null], 'id_annee', 'DESC');
        $this->load->view('periodes', $data);
    }

    public function api_list() {
        $this->db->select('p.*, a.libelle as annee_libelle');
        $this->db->from('periodes p');
        $this->db->join('annees_scolaires a', 'p.id_annee = a.id_annee', 'left');
        $this->db->order_by('p.date_debut', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_get($id) {
        $p = $this->Model->readOne('periodes', ['uuid' => $id]);
        if (!$p) { $this->json_error('Période non trouvée', 404); return; }
        $this->json_success($p);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['libelle'])) { $this->json_error('Libellé obligatoire'); return; }
        if (empty($data['id_annee'])) { $this->json_error('Année scolaire obligatoire'); return; }
        $allowed = ['libelle', 'id_annee', 'date_debut', 'date_fin', 'est_en_cours'];
        $insert = array_intersect_key($data, array_flip($allowed));

        $exists = $this->db
            ->where('libelle', $insert['libelle'])
            ->where('id_annee', $insert['id_annee'])
            ->where('deleted_at IS NOT NULL')
            ->get('periodes')
            ->row_array();
        if ($exists) {
            $update = array_intersect_key($insert, array_flip(['date_debut', 'date_fin', 'est_en_cours']));
            $update['deleted_at'] = null;
            $this->Model->update('periodes', ['id_periode' => $exists['id_periode']], $update);
            $this->json_success(['id_periode' => $exists['id_periode'], 'restored' => true], 'Période restaurée');
            return;
        }

        $id = $this->Model->createLastId('periodes', $insert);
        if ($id) $this->json_success(['id_periode' => $id], 'Période créée');
        else $this->json_error('Erreur lors de la création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (!$this->Model->readOne('periodes', ['uuid' => $id])) {
            $this->json_error('Période non trouvée', 404); return;
        }
        $allowed = ['libelle', 'id_annee', 'date_debut', 'date_fin', 'est_en_cours'];
        $update = array_intersect_key($data, array_flip($allowed));
        if ($this->Model->update('periodes', ['uuid' => $id], $update))
            $this->json_success(null, 'Période mise à jour');
        else $this->json_error('Erreur lors de la mise à jour');
    }

    public function api_delete($id) {
        if (!$this->Model->readOne('periodes', ['uuid' => $id])) {
            $this->json_error('Période non trouvée', 404); return;
        }
        if ($this->Model->update('periodes', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Période supprimée');
        else $this->json_error('Erreur lors de la suppression');
    }

    public function api_activate($id) {
        if (!$this->Model->readOne('periodes', ['uuid' => $id])) {
            $this->json_error('Période non trouvée', 404); return;
        }
        if ($this->Model->update('periodes', ['uuid' => $id], ['deleted_at' => null]))
            $this->json_success(null, 'Période activée');
        else $this->json_error('Erreur lors de l\'activation');
    }

    public function api_deactivate($id) {
        if (!$this->Model->readOne('periodes', ['uuid' => $id])) {
            $this->json_error('Période non trouvée', 404); return;
        }
        if ($this->Model->update('periodes', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Période désactivée');
        else $this->json_error('Erreur lors de la désactivation');
    }

    public function api_set_active($id) {
        $periode = $this->Model->readOne('periodes', ['uuid' => $id, 'deleted_at' => null]);
        if (!$periode) { $this->json_error('Période introuvable ou inactive', 404); return; }
        $this->db->trans_start();
        $this->db->where('deleted_at', null)->update('periodes', ['est_en_cours' => 0]);
        $this->db->where('uuid', $id)->update('periodes', ['est_en_cours' => 1]);
        $this->db->trans_complete();
        if ($this->db->trans_status())
            $this->json_success(null, 'Période définie en cours');
        else $this->json_error('Erreur lors de l\'opération');
    }
}
