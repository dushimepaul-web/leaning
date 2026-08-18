<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Conduite extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Conduite_model', 'ConduiteModel');
    }

    public function index() {
        $data['title'] = 'Points de conduite';
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null], 'libelle', 'ASC');
        $data['periodes'] = $this->Model->read('periodes', ['deleted_at' => null], 'id_periode', 'ASC');
        $data['annees'] = $this->Model->read('annees_scolaires', ['deleted_at' => null], 'id_annee', 'DESC');
        $data['id_annee_active'] = $this->id_annee_active;
        $data['id_periode_active'] = $this->id_periode_active;
        $data['points_defaut_ecole'] = $this->ConduiteModel->get_points_initial_defaut(60);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $id_classe = $this->input->get('classe');
        $id_annee = $this->input->get('annee') ?: $this->id_annee_active;
        $id_periode = $this->input->get('periode') ?: $this->id_periode_active;
        if (!$id_classe) { $this->json_error('Classe requise'); return; }
        $this->json_success($this->ConduiteModel->get_eleves_conduite($id_classe, $id_annee, $id_periode));
    }

    public function api_initialiser() {
        $data = $this->get_json_input();
        if (empty($data['id_classe']) || empty($data['id_annee']) || empty($data['id_periode'])) {
            $this->json_error('Classe, année et période obligatoires'); return;
        }
        $points_initial = (isset($data['points_initial']) && $data['points_initial'] !== null && $data['points_initial'] !== '') ? floatval($data['points_initial']) : null;
        $result = $this->ConduiteModel->initialiser_classe($data['id_classe'], $data['id_annee'], $data['id_periode'], $points_initial);
        if (empty($result['total'])) { $this->json_error('Aucun élève trouvé dans cette classe'); return; }
        $this->json_success($result, "Initialisé : {$result['created']} créés, {$result['updated']} mis à jour");
    }

    public function api_update() {
        $data = $this->get_json_input();
        if (empty($data['id_point_conduite'])) { $this->json_error('Point de conduite requis'); return; }
        $allowed = ['points_initial', 'observation'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if (isset($update['points_initial'])) {
            $update['points_initial'] = floatval($update['points_initial']);
        }
        if ($this->Model->update('points_conduite', ['id_point_conduite' => $data['id_point_conduite']], $update)) {
            $this->json_success(null, 'Points de conduite mis à jour');
        } else {
            $this->json_error('Erreur');
        }
    }

    public function api_sanctions() {
        $id_point_conduite = $this->input->get('id_point_conduite');
        if (!$id_point_conduite) {
            $id_etudiant = $this->input->get('id_etudiant');
            $id_annee = $this->input->get('annee');
            $id_periode = $this->input->get('periode');
            if (!$id_etudiant || !$id_annee || !$id_periode) {
                $this->json_error('Point de conduite requis'); return;
            }
            $id_point_conduite = $this->_ensure_point($id_etudiant, $id_annee, $id_periode);
            if (!$id_point_conduite) { $this->json_error('Élève introuvable'); return; }
        }
        $point = $this->ConduiteModel->get_point_conduite($id_point_conduite);
        if (!$point) { $this->json_error('Point de conduite introuvable', 404); return; }
        $point['points_initial'] = $this->ConduiteModel->get_points_initial_defaut(60);
        $this->json_success([
            'point' => $point,
            'sanctions' => $this->ConduiteModel->get_sanctions($id_point_conduite)
        ]);
    }

    private function _ensure_point($id_etudiant, $id_annee, $id_periode)
    {
        $this->load->helper('uuid');
        $existing = $this->Model->readOne('points_conduite', [
            'id_etudiant' => $id_etudiant,
            'id_annee' => $id_annee,
            'id_periode' => $id_periode,
            'deleted_at' => null
        ]);
        if ($existing) return $existing['id_point_conduite'];
        $student = $this->Model->readOne('etudiants', ['id_etudiant' => $id_etudiant]);
        if (!$student) return null;
        $points_initial = $this->ConduiteModel->get_points_initial_defaut(60);
        $id = $this->Model->createLastId('points_conduite', [
            'uuid' => generate_uuid(),
            'id_etudiant' => $id_etudiant,
            'id_annee' => $id_annee,
            'id_periode' => $id_periode,
            'points_initial' => $points_initial,
            'points_retires' => 0,
        ]);
        return $id;
    }

    public function api_sanction_create() {
        $data = $this->get_json_input();
        if (empty($data['id_point_conduite']) || empty($data['motif']) || empty($data['points_retires'])) {
            $this->json_error('Point de conduite, motif et points à retirer obligatoires'); return;
        }
        $point = $this->ConduiteModel->get_point_conduite($data['id_point_conduite']);
        if (!$point) { $this->json_error('Point de conduite introuvable'); return; }

        $id = $this->Model->createLastId('sanctions_conduite', [
            'id_point_conduite' => $data['id_point_conduite'],
            'motif' => trim($data['motif']),
            'points_retires' => floatval($data['points_retires']),
            'date_sanction' => !empty($data['date_sanction']) ? $data['date_sanction'] : date('Y-m-d'),
            'id_utilisateur' => $this->session->userdata('id_utilisateur'),
        ]);
        if (!$id) { $this->json_error('Erreur création'); return; }

        $total = $this->ConduiteModel->recalc_points($data['id_point_conduite']);
        $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'create', 'Sanction de conduite ajoutée',
            'sanctions_conduite', $id, null, ['motif' => $data['motif'], 'points_retires' => $data['points_retires']]);
        $this->json_success(['id_sanction' => $id, 'points_retires' => $total], 'Sanction enregistrée');
    }

    public function api_sanction_delete($uuid) {
        $this->load->helper('uuid');
        $sanction = $this->Model->readOne('sanctions_conduite', ['uuid' => $uuid]);
        if (!$sanction) { $this->json_error('Sanction introuvable', 404); return; }
        if ($this->db->where('uuid', $uuid)->delete('sanctions_conduite')) {
            $this->ConduiteModel->recalc_points($sanction['id_point_conduite']);
            $this->json_success(null, 'Sanction supprimée');
        } else {
            $this->json_error('Erreur');
        }
    }
}