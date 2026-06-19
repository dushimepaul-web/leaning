<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enseignants extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->not_logged_in();
    }

    public function index() {
        $data['title'] = 'Teacher List';
        $this->load->view('list', $data);
    }

    public function add() {
        $data['title'] = 'Add Teacher';
        $data['teacher'] = null;
        $data['matieres'] = $this->Model->read('matieres', ['deleted_at' => null]);
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $this->load->view('form', $data);
    }

    public function edit($id) {
        $e = $this->Model->readOne('enseignants', ['uuid' => $id]);
        if (!$e) { show_404(); return; }
        $e['enseignements'] = $this->Model->read('enseignements', ['id_enseignant' => $e['id_enseignant']]);
        foreach ($e['enseignements'] as &$ens) {
            $mc = $this->Model->readOne('matieres_classes', [
                'id_matiere' => $ens['id_matiere'],
                'id_classe' => $ens['id_classe']
            ]);
            if ($mc) {
                $ens['coefficient'] = $mc['coefficient'];
                $ens['nb_heures_par_jour'] = $mc['nb_heures_par_jour'];
                $ens['nb_heures_par_semaine'] = $mc['nb_heures_par_semaine'];
            }
        }
        $data['title'] = 'Edit Teacher';
        $data['teacher'] = $e;
        $data['matieres'] = $this->Model->read('matieres', ['deleted_at' => null]);
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $this->load->view('form', $data);
    }

    public function details($id) {
        $e = $this->Model->readOne('enseignants', ['uuid' => $id]);
        if (!$e) { show_404(); return; }
        $enseignements = $this->Model->read('enseignements', ['id_enseignant' => $e['id_enseignant'], 'deleted_at' => null]);
        foreach ($enseignements as &$ens) {
            $m = $this->Model->readOne('matieres', ['id_matiere' => $ens['id_matiere']]);
            $ens['matiere_libelle'] = $m ? $m['libelle'] : '-';
            $c = $this->Model->readOne('classes', ['id_classe' => $ens['id_classe']]);
            $ens['classe_libelle'] = $c ? $c['libelle'] : '-';
            $mc = $this->Model->readOne('matieres_classes', [
                'id_matiere' => $ens['id_matiere'],
                'id_classe' => $ens['id_classe']
            ]);
            $ens['id_matiere_classe'] = $mc['id_matiere_classe'] ?? null;
            $ens['coefficient'] = $mc['coefficient'] ?? '-';
            $ens['nb_heures_par_jour'] = $mc['nb_heures_par_jour'] ?? '-';
            $ens['nb_heures_par_semaine'] = $mc['nb_heures_par_semaine'] ?? '-';
        }
        $e['enseignements'] = $enseignements;
        $matiere_ids = array_unique(array_column($e['enseignements'], 'id_matiere'));
        $e['matieres_list'] = [];
        foreach ($matiere_ids as $mid) {
            $m = $this->Model->readOne('matieres', ['id_matiere' => $mid]);
            if ($m) $e['matieres_list'][] = $m['libelle'];
        }
        $classe_ids = array_unique(array_column($e['enseignements'], 'id_classe'));
        $e['classes_list'] = [];
        foreach ($classe_ids as $cid) {
            $c = $this->Model->readOne('classes', ['id_classe' => $cid]);
            if ($c) $e['classes_list'][] = $c['libelle'];
        }
        $data['title'] = 'Teacher Details';
        $data['teacher'] = $e;
        $this->load->view('details', $data);
    }

    public function timetable($id) {
        $e = $this->Model->readOne('enseignants', ['uuid' => $id]);
        if (!$e) { show_404(); return; }
        $data['title'] = 'Teacher Timetable';
        $data['teacher'] = $e;
        $data['jours'] = $this->Model->read('jours_semaine', ['actif' => 1], 'ordre');
        $data['creneaux'] = $this->Model->read('creneaux', [], 'ordre');
        $this->load->view('timetable', $data);
    }

    public function api_list() {
        $this->db->select('e.*');
        $this->db->from('enseignants e');
        $this->db->where('e.deleted_at', null);
        $this->db->order_by('e.id_enseignant', 'DESC');
        $enseignants = $this->db->get()->result_array();

        if (!empty($enseignants)) {
            $ids_enseignant = array_column($enseignants, 'id_enseignant');
            $ens = $this->db->query(
                "SELECT ens.id_enseignant, m.libelle AS matiere, c.libelle AS classe
                 FROM enseignements ens
                 JOIN matieres m ON m.id_matiere = ens.id_matiere
                 JOIN classes c ON c.id_classe = ens.id_classe
                 WHERE ens.id_enseignant IN (" . implode(',', array_fill(0, count($ids_enseignant), '?')) . ")
                 AND ens.deleted_at IS NULL",
                $ids_enseignant
            )->result_array();

            $matieres_map = [];
            $classes_map = [];
            foreach ($ens as $row) {
                $matieres_map[$row['id_enseignant']][] = $row['matiere'];
                $classes_map[$row['id_enseignant']][] = $row['classe'];
            }
        }

        foreach ($enseignants as &$e) {
            $e['matieres'] = !empty($matieres_map[$e['id_enseignant']])
                ? implode(', ', array_unique($matieres_map[$e['id_enseignant']]))
                : '';
            $e['classes'] = !empty($classes_map[$e['id_enseignant']])
                ? implode(', ', array_unique($classes_map[$e['id_enseignant']]))
                : '';
        }
        $this->json_success($enseignants);
    }

    public function api_get($id) {
        $e = $this->Model->readOne('enseignants', ['uuid' => $id]);
        if (!$e) { $this->json_error('Enseignant non trouvé', 404); return; }
        $e['enseignements'] = $this->Model->read('enseignements', ['id_enseignant' => $e['id_enseignant'], 'deleted_at' => null]);
        foreach ($e['enseignements'] as &$ens) {
            $mc = $this->Model->readOne('matieres_classes', [
                'id_matiere' => $ens['id_matiere'],
                'id_classe' => $ens['id_classe']
            ]);
            if ($mc) {
                $ens['coefficient'] = $mc['coefficient'];
                $ens['nb_heures_par_jour'] = $mc['nb_heures_par_jour'];
                $ens['nb_heures_par_semaine'] = $mc['nb_heures_par_semaine'];
            }
        }
        $this->json_success($e);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['nom']) || empty($data['prenom'])) {
            $this->json_error('Nom et prénom obligatoires'); return;
        }
        $data['matricule'] = $data['matricule'] ?? 'ENS-' . strtoupper(uniqid());
        if (!empty($data['email'])) {
            $existing = $this->Model->readOne('utilisateurs', ['email' => $data['email']]);
            if ($existing) {
                $this->json_error('Cet email est déjà utilisé par un autre compte');
                return;
            }
        }

        $allowed = ['matricule', 'nom', 'postnom', 'prenom', 'sexe', 'date_naissance', 'telephone', 'email', 'adresse', 'specialite', 'qualification', 'experience', 'date_embauche'];
        $teacher_data = array_intersect_key($data, array_flip($allowed));
        $id = $this->Model->createLastId('enseignants', $teacher_data);
        if ($id) {
            $this->_sync_enseignements($id, $data['enseignements'] ?? []);
            $account = $this->_create_linked_user('enseignants', $id, $data, 'enseignant');
            $result = ['id_enseignant' => $id];
            if ($account) {
                $result['id_utilisateur'] = $account['id_utilisateur'];
                $result['default_password'] = $account['default_password'];
            }
            $this->json_success($result, 'Enseignant créé avec succès');
        } else {
            $this->json_error('Erreur lors de la création');
        }
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $enseignant = $this->Model->readOne('enseignants', ['uuid' => $id]);
        if (!$enseignant) {
            $this->json_error('Enseignant non trouvé', 404); return;
        }

        if (!empty($data['email']) && $data['email'] !== $enseignant['email']) {
            $existing = $this->Model->readOne('utilisateurs', ['email' => $data['email']]);
            if ($existing && $existing['id_utilisateur'] != $enseignant['id_utilisateur']) {
                $this->json_error('Cet email est déjà utilisé par un autre compte');
                return;
            }
        }

        $allowed = ['matricule', 'nom', 'postnom', 'prenom', 'sexe', 'date_naissance', 'telephone', 'email', 'adresse', 'specialite', 'qualification', 'experience', 'date_embauche', 'actif'];
        $teacher_data = array_intersect_key($data, array_flip($allowed));
        if ($this->Model->update('enseignants', ['uuid' => $id], $teacher_data)) {
            if (isset($data['enseignements']) && is_array($data['enseignements'])) {
                $this->_sync_enseignements($enseignant['id_enseignant'], $data['enseignements']);
            }
            $this->_sync_linked_user('enseignants', $enseignant['id_enseignant'], $data);
            $this->json_success(null, 'Enseignant mis à jour');
        } else {
            $this->json_error('Erreur de mise à jour');
        }
    }

    private function _sync_enseignements($id_enseignant, $enseignements) {
        $this->db->where('id_enseignant', $id_enseignant)->delete('enseignements');
        foreach ($enseignements as $ens) {
            if (!empty($ens['id_matiere'])) {
                $this->Model->create('enseignements', [
                    'id_enseignant' => $id_enseignant,
                    'id_matiere' => $ens['id_matiere'],
                    'id_classe' => $ens['id_classe'] ?? null
                ]);
                $this->_sync_matieres_classes($id_enseignant, $ens);
            }
        }
    }

    private function _sync_matieres_classes($id_enseignant, $ens) {
        $id_matiere = $ens['id_matiere'];
        $id_classe = $ens['id_classe'] ?? null;
        if (!$id_classe) return;

        $existing = $this->Model->readOne('matieres_classes', [
            'id_matiere' => $id_matiere,
            'id_classe' => $id_classe
        ]);

        $data = [
            'id_enseignant' => $id_enseignant,
            'coefficient' => $ens['coefficient'] ?? 1.0,
            'nb_heures_par_jour' => $ens['nb_heures_par_jour'] ?? 0.0,
            'nb_heures_par_semaine' => $ens['nb_heures_par_semaine'] ?? 0.0,
        ];

        if ($existing) {
            $this->Model->update('matieres_classes', ['id_matiere_classe' => $existing['id_matiere_classe']], $data);
        } else {
            $data['id_matiere'] = $id_matiere;
            $data['id_classe'] = $id_classe;
            $this->Model->createLastId('matieres_classes', $data);
        }
    }

    public function api_upload_photo() {
        $upload_path = realpath(FCPATH . 'assets') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'teachers' . DIRECTORY_SEPARATOR;
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
        $config['max_size'] = 20480;
        $config['encrypt_name'] = true;

        if (!is_dir($upload_path)) {
            @mkdir($upload_path, 0755, true);
        }

        $this->load->library('upload', $config);
        if ($this->upload->do_upload('file')) {
            $data = $this->upload->data();
            $this->json_success(['path' => 'assets/uploads/teachers/' . $data['file_name']]);
        } else {
            $this->json_error($this->upload->display_errors('', ''));
        }
    }

    public function api_delete($id) {
        if ($this->Model->update('enseignants', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')])) {
            $enseignant = $this->Model->readOne('enseignants', ['uuid' => $id]);
            if (!empty($enseignant['id_utilisateur'])) {
                $this->Model->update('utilisateurs', ['id_utilisateur' => $enseignant['id_utilisateur']], ['deleted_at' => date('Y-m-d H:i:s'), 'actif' => 0]);
            }
            $this->json_success(null, 'Enseignant supprimé');
        } else {
            $this->json_error('Erreur de suppression');
        }
    }

}
