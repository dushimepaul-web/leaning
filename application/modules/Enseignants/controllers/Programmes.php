<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Programmes extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des programmes';
        $matieres = $this->db->where('deleted_at', null)->order_by('libelle')->get('matieres')->result_array();
        $data['matieres'] = array_map(function($m) { $m['id'] = (int)$m['id_matiere']; return $m; }, $matieres);
        $classes = $this->db->where('deleted_at', null)->order_by('libelle')->get('classes')->result_array();
        $data['classes'] = array_map(function($c) { $c['id'] = (int)$c['id_classe']; return $c; }, $classes);
        $this->db->select("*, CONCAT(nom, ' ', postnom, ' ', prenom) AS nom_complet");
        $this->db->where('deleted_at', null)->order_by('nom', 'ASC');
        $enseignants = $this->db->get('enseignants')->result_array();
        $data['enseignants'] = array_map(function($e) { $e['id'] = (int)$e['id_enseignant']; return $e; }, $enseignants);
        $this->load->view('programmes', $data);
    }

    public function api_list() {
        $this->db->select("mc.*, m.libelle AS matiere_libelle, m.code AS matiere_code, cl.libelle AS classe_libelle, CONCAT(e.nom, ' ', e.postnom, ' ', e.prenom) AS enseignant_nom");
        $this->db->from('matieres_classes mc');
        $this->db->join('matieres m', 'mc.id_matiere = m.id_matiere');
        $this->db->join('classes cl', 'mc.id_classe = cl.id_classe');
        $this->db->join('enseignants e', 'mc.id_enseignant = e.id_enseignant', 'left');
        $this->db->where('mc.deleted_at', null);
        $this->db->order_by('cl.libelle, m.libelle');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_get($id) {
        $this->db->select("mc.*, m.libelle AS matiere_libelle, cl.libelle AS classe_libelle, CONCAT(e.nom, ' ', e.postnom, ' ', e.prenom) AS enseignant_nom");
        $this->db->from('matieres_classes mc');
        $this->db->join('matieres m', 'mc.id_matiere = m.id_matiere');
        $this->db->join('classes cl', 'mc.id_classe = cl.id_classe');
        $this->db->join('enseignants e', 'mc.id_enseignant = e.id_enseignant', 'left');
        $this->db->where('mc.uuid', $id);
        $m = $this->db->get()->row_array();
        if (!$m) { $this->json_error('Programme non trouvé', 404); return; }
        $this->json_success($m);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_matiere']) || empty($data['id_classe'])) {
            $this->json_error('Matière et classe sont obligatoires'); return;
        }

        $existing = $this->Model->readOne('matieres_classes', [
            'id_matiere' => $data['id_matiere'],
            'id_classe' => $data['id_classe']
        ]);
        if ($existing) {
            $this->json_error('Cette matière est déjà associée à cette classe'); return;
        }

        $data['coefficient'] = $data['coefficient'] ?? 1.0;
        $data['nb_heures_par_jour'] = $data['nb_heures_par_jour'] ?? 0.0;
        $data['nb_heures_par_semaine'] = $data['nb_heures_par_semaine'] ?? 0.0;

        $id = $this->Model->createLastId('matieres_classes', $data);
        if ($id) $this->json_success(['id_matiere_classe' => $id], 'Programme créé');
        else $this->json_error('Erreur lors de la création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $record = $this->Model->readOne('matieres_classes', ['uuid' => $id]);
        if (!$record) { $this->json_error('Programme non trouvé', 404); return; }

        if (isset($data['id_matiere']) && isset($data['id_classe'])) {
            $existing = $this->Model->readOne('matieres_classes', [
                'id_matiere' => $data['id_matiere'],
                'id_classe' => $data['id_classe']
            ]);
            if ($existing && $existing['id_matiere_classe'] != $record['id_matiere_classe']) {
                $this->json_error('Cette matière est déjà associée à cette classe'); return;
            }
        }

        $allowed = ['id_matiere', 'id_classe', 'id_enseignant', 'coefficient', 'nb_heures_par_jour', 'nb_heures_par_semaine'];
        $update = array_intersect_key($data, array_flip($allowed));

        if ($this->Model->update('matieres_classes', ['uuid' => $id], $update))
            $this->json_success(null, 'Programme mis à jour');
        else $this->json_error('Erreur lors de la mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('matieres_classes', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Programme supprimé');
        else $this->json_error('Erreur lors de la suppression');
    }
}
