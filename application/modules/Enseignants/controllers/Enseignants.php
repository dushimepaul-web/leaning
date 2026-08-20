<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enseignants extends MY_Controller {
    public function __construct() {
        parent::__construct();
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
        $data['matieres_classes'] = $this->Model->read('matieres_classes', ['deleted_at' => null]);
        $this->load->view('form', $data);
    }

    public function edit($id) {
        $e = $this->Model->readOne('enseignants', ['uuid' => $id]);
        if (!$e) { show_404(); return; }
        $e['fullname'] = $e['fullname'] ?? '';
        $e['enseignements'] = $this->Model->read('matieres_classes', ['id_enseignant' => $e['id_enseignant'], 'deleted_at' => null]);
        $data['title'] = 'Modifier l\'enseignant';
        $data['teacher'] = $e;
        $data['matieres'] = $this->Model->read('matieres', ['deleted_at' => null]);
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['matieres_classes'] = $this->Model->read('matieres_classes', ['deleted_at' => null]);
        $this->load->view('form', $data);
    }

    public function details($id) {
        $e = $this->Model->readOne('enseignants', ['uuid' => $id]);
        if (!$e) { show_404(); return; }
        $enseignements = $this->Model->read('matieres_classes', ['id_enseignant' => $e['id_enseignant'], 'deleted_at' => null]);
        foreach ($enseignements as &$mc) {
            $m = $this->Model->readOne('matieres', ['id_matiere' => $mc['id_matiere']]);
            $c = $this->Model->readOne('classes', ['id_classe' => $mc['id_classe']]);
            $mc['matiere_libelle'] = $m ? $m['libelle'] : '-';
            $mc['classe_libelle'] = $c ? $c['libelle'] : '-';
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
        $data['title'] = 'Détails de l\'enseignant';
        $data['teacher'] = $e;
        $this->load->view('details', $data);
    }

    public function timetable($id) {
        $e = $this->Model->readOne('enseignants', ['uuid' => $id]);
        if (!$e) { show_404(); return; }
        $data['title'] = 'Teacher Timetable';
        $data['teacher'] = $e;
        $data['jours'] = $this->Model->read('jours_semaine', ['actif' => 1], 'ordre');
        $this->load->model('Horaires/Horaires_model');
        $data['creneaux'] = $this->Horaires_model->get_creneaux_cours();
        $this->load->view('timetable', $data);
    }

    public function api_list() {
        $this->db->select('e.*');
        $this->db->from('enseignants e');
        $this->db->where('e.deleted_at', null);
        if ($this->input->get('sexe')) $this->db->where('e.sexe', $this->input->get('sexe'));
        if ($this->input->get('statut') !== null && $this->input->get('statut') !== '') $this->db->where('e.actif', (int)$this->input->get('statut'));
        if ($this->input->get('q')) {
            $q = trim($this->input->get('q'));
            $this->db->group_start();
            $this->db->like('e.fullname', $q);
            $this->db->or_like('e.matricule', $q);
            $this->db->or_like('e.email', $q);
            $this->db->group_end();
        }
        $this->db->order_by('e.id_enseignant', 'DESC');
        $enseignants = $this->db->get()->result_array();

        if (!empty($enseignants)) {
            $ids_enseignant = array_column($enseignants, 'id_enseignant');
            $ens = $this->db->query(
                "SELECT mc.id_enseignant, m.libelle AS matiere, c.libelle AS classe
                 FROM matieres_classes mc
                 JOIN matieres m ON m.id_matiere = mc.id_matiere
                 JOIN classes c ON c.id_classe = mc.id_classe
                 WHERE mc.id_enseignant IN (" . implode(',', array_fill(0, count($ids_enseignant), '?')) . ")
                 AND mc.deleted_at IS NULL",
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
        $e['enseignements'] = $this->Model->read('matieres_classes', ['id_enseignant' => $e['id_enseignant'], 'deleted_at' => null]);
        foreach ($e['enseignements'] as &$ens) {
            $m = $this->Model->readOne('matieres', ['id_matiere' => $ens['id_matiere']]);
            $c = $this->Model->readOne('classes', ['id_classe' => $ens['id_classe']]);
            $ens['matiere_libelle'] = $m ? $m['libelle'] : '-';
            $ens['classe_libelle'] = $c ? $c['libelle'] : '-';
        }
        $this->json_success($e);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['fullname'])) {
            $this->json_error('Le nom complet est obligatoire'); return;
        }
        $err = $this->_valider_champs($data);
        if ($err) { $this->json_error($err); return; }
        $data['matricule'] = $this->_normaliser_matricule($data['matricule'] ?? '');
        if (isset($data['experience'])) $data['experience'] = $this->_normaliser_experience($data['experience']);

        if (!empty($data['email'])) {
            $existing = $this->Model->readOne('utilisateurs', ['email' => $data['email']]);
            if ($existing) {
                $this->json_error('Cet email est déjà utilisé par un autre compte');
                return;
            }
        }

        $enseignements = $data['enseignements'] ?? [];
        if (!$this->_valider_enseignements($enseignements)) return;
        if (!$this->_verifier_conflits_cours($enseignements, null)) return;

        $allowed = ['matricule', 'fullname', 'sexe', 'date_naissance', 'telephone', 'email', 'adresse', 'specialite', 'qualification', 'experience', 'date_embauche', 'photo'];
        $teacher_data = array_intersect_key($data, array_flip($allowed));

        $this->db->trans_begin();
        $id = $this->Model->createLastId('enseignants', $teacher_data);
        if (!$id) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de la création'); return;
        }
        $this->_sync_enseignements($id, $enseignements);
        $account = $this->_create_linked_user('enseignants', $id, $data, 'enseignant');
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de la création'); return;
        }
        $this->db->trans_commit();
        $result = ['id_enseignant' => $id];
        if ($account) {
            $result['id_utilisateur'] = $account['id_utilisateur'];
            $result['default_password'] = $account['default_password'];
        }
        $this->json_success($result, 'Enseignant créé avec succès');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $enseignant = $this->Model->readOne('enseignants', ['uuid' => $id]);
        if (!$enseignant) {
            $this->json_error('Enseignant non trouvé', 404); return;
        }

        $err = $this->_valider_champs($data);
        if ($err) { $this->json_error($err); return; }
        if (array_key_exists('matricule', $data)) {
            $data['matricule'] = $this->_normaliser_matricule($data['matricule']);
        }
        if (isset($data['experience'])) $data['experience'] = $this->_normaliser_experience($data['experience']);

        if (!empty($data['email']) && $data['email'] !== $enseignant['email']) {
            $existing = $this->Model->readOne('utilisateurs', ['email' => $data['email']]);
            if ($existing && $existing['id_utilisateur'] != $enseignant['id_utilisateur']) {
                $this->json_error('Cet email est déjà utilisé par un autre compte');
                return;
            }
        }

        $enseignements = null;
        if (isset($data['enseignements']) && is_array($data['enseignements'])) {
            if (!$this->_valider_enseignements($data['enseignements'])) return;
            if (!$this->_verifier_conflits_cours($data['enseignements'], $enseignant['id_enseignant'])) return;
            $enseignements = $data['enseignements'];
        }

        $allowed = ['matricule', 'fullname', 'sexe', 'date_naissance', 'telephone', 'email', 'adresse', 'specialite', 'qualification', 'experience', 'date_embauche', 'actif', 'photo'];
        $teacher_data = array_intersect_key($data, array_flip($allowed));

        $this->db->trans_begin();
        $this->Model->update('enseignants', ['uuid' => $id], $teacher_data);
        if ($enseignements !== null) {
            $this->_sync_enseignements($enseignant['id_enseignant'], $enseignements);
        }
        if (array_key_exists('actif', $teacher_data) && !empty($enseignant['id_utilisateur'])) {
            $this->Model->update('utilisateurs', ['id_utilisateur' => $enseignant['id_utilisateur']], ['actif' => (int)$teacher_data['actif']]);
        }
        $this->_sync_linked_user('enseignants', $enseignant['id_enseignant'], $data);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->json_error('Erreur de mise à jour'); return;
        }
        $this->db->trans_commit();
        $this->json_success(null, 'Enseignant mis à jour');
    }

    private function _valider_enseignements($enseignements) {
        if (!is_array($enseignements)) return true;
        foreach ($enseignements as $ens) {
            if (empty($ens['id_matiere']) || empty($ens['id_classe'])) continue;
            $m = $this->Model->readOne('matieres', ['id_matiere' => $ens['id_matiere'], 'deleted_at' => null]);
            if (!$m) { $this->json_error('Matière introuvable dans les enseignements'); return false; }
            $c = $this->Model->readOne('classes', ['id_classe' => $ens['id_classe'], 'deleted_at' => null]);
            if (!$c) { $this->json_error('Classe introuvable dans les enseignements'); return false; }
        }
        return true;
    }

    private function _valider_champs($data) {
        if (isset($data['sexe']) && $data['sexe'] !== '' && !in_array($data['sexe'], ['M', 'F'], true)) {
            return 'Le sexe doit être M ou F';
        }
        foreach (['date_naissance' => 'Date de naissance', 'date_embauche' => 'Date d\'embauche'] as $champ => $libelle) {
            if (!empty($data[$champ])) {
                $d = DateTime::createFromFormat('Y-m-d', $data[$champ]);
                if (!$d || $d->format('Y-m-d') !== $data[$champ]) {
                    return $libelle . ' invalide (format attendu : AAAA-MM-JJ)';
                }
            }
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Adresse email invalide';
        }
        if (isset($data['telephone']) && $data['telephone'] !== '' && !preg_match('/^[0-9+\s\-().]+$/', $data['telephone'])) {
            return 'Numéro de téléphone invalide';
        }
        if (isset($data['experience']) && $data['experience'] !== ''
            && !is_numeric($data['experience'])
            && !preg_match('/^\s*\d+\s*ans?(?:\s+d[’\']exp(?:érience|erience))?/i', $data['experience'])) {
            return 'Expérience invalide (nombre d\'années attendu)';
        }
        return null;
    }

    private function _normaliser_matricule($matricule) {
        $matricule = trim((string)$matricule);
        if ($matricule !== '') return $matricule;
        do {
            $matricule = 'ENS-' . strtoupper(uniqid());
        } while ($this->Model->readOne('enseignants', ['matricule' => $matricule]));
        return $matricule;
    }

    private function _normaliser_experience($experience) {
        $experience = trim((string)$experience);
        if ($experience === '') return '';
        if (is_numeric($experience)) return $experience;
        if (preg_match('/^\s*(\d+)/', $experience, $m)) return $m[1];
        return $experience;
    }

    private function _verifier_conflits_cours($enseignements, $id_enseignant_exclu = null) {
        if (!is_array($enseignements)) return true;
        foreach ($enseignements as $ens) {
            if (empty($ens['id_matiere']) || empty($ens['id_classe'])) continue;
            $mc = $this->Model->readOne('matieres_classes', [
                'id_matiere' => $ens['id_matiere'],
                'id_classe' => $ens['id_classe'],
                'deleted_at' => null
            ]);
            if ($mc && !empty($mc['id_enseignant']) && $mc['id_enseignant'] != $id_enseignant_exclu) {
                $m = $this->Model->readOne('matieres', ['id_matiere' => $ens['id_matiere']]);
                $c = $this->Model->readOne('classes', ['id_classe' => $ens['id_classe']]);
                $prof = $this->Model->readOne('enseignants', ['id_enseignant' => $mc['id_enseignant']]);
                $this->json_error('Le cours « ' . ($m['libelle'] ?? '?') . ' » en classe « ' . ($c['libelle'] ?? '?') . ' » est déjà attribué à ' . ($prof['fullname'] ?? 'un autre enseignant'));
                return false;
            }
        }
        return true;
    }

    private function _sync_enseignements($id_enseignant, $enseignements) {
        // 1. Retirer les anciennes attributions de cet enseignant
        $this->db->where('id_enseignant', $id_enseignant)
            ->update('matieres_classes', ['id_enseignant' => null]);

        // 2. Attribuer l'enseignant aux matieres_classes sélectionnées
        $cibles = [];
        foreach ($enseignements as $ens) {
            if (empty($ens['id_matiere']) || empty($ens['id_classe'])) continue;

            $mc = $this->Model->readOne('matieres_classes', [
                'id_matiere' => $ens['id_matiere'],
                'id_classe' => $ens['id_classe'],
                'deleted_at' => null
            ]);

            if ($mc) {
                $this->Model->update('matieres_classes', [
                    'id_matiere_classe' => $mc['id_matiere_classe']
                ], ['id_enseignant' => $id_enseignant]);
                $cibles[] = [
                    'id_matiere_classe' => $mc['id_matiere_classe'],
                    'id_matiere' => $mc['id_matiere'],
                    'id_classe' => $mc['id_classe']
                ];
            } else {
                // Si la matière/classe n'existe pas encore dans matieres_classes, la créer
                $this->load->helper('uuid');
                $id_mc = $this->Model->createLastId('matieres_classes', [
                    'id_matiere' => $ens['id_matiere'],
                    'id_classe' => $ens['id_classe'],
                    'id_enseignant' => $id_enseignant,
                    'coefficient' => 1.0,
                    'nb_heures_par_jour' => 0.0,
                    'nb_heures_par_semaine' => 0.0
                ]);
                if ($id_mc) {
                    $cibles[] = [
                        'id_matiere_classe' => $id_mc,
                        'id_matiere' => $ens['id_matiere'],
                        'id_classe' => $ens['id_classe']
                    ];
                }
            }
        }

        // 3. Synchroniser la table shadow "enseignements" (utilisée par les horaires)
        $ids_cibles = array_column($cibles, 'id_matiere_classe');
        $this->db->where('id_enseignant', $id_enseignant)
            ->where('deleted_at', null);
        if (!empty($ids_cibles)) $this->db->where_not_in('id_matiere_classe', $ids_cibles);
        $this->db->update('enseignements', ['deleted_at' => date('Y-m-d H:i:s')]);

        foreach ($cibles as $mc) {
            $row = $this->db->where('id_matiere_classe', $mc['id_matiere_classe'])
                ->get('enseignements')
                ->row_array();
            if (!$row) {
                $row = $this->db->where('id_enseignant', $id_enseignant)
                    ->where('id_matiere', $mc['id_matiere'])
                    ->where('id_classe', $mc['id_classe'])
                    ->get('enseignements')
                    ->row_array();
            }
            if ($row) {
                $dup = $this->db->where('id_enseignant', $id_enseignant)
                    ->where('id_matiere', $mc['id_matiere'])
                    ->where('id_classe', $mc['id_classe'])
                    ->where('id_enseignement !=', $row['id_enseignement'])
                    ->get('enseignements')
                    ->row_array();
                if ($dup) {
                    // Libérer le triplet UNIQUE (id_enseignant, id_matiere, id_classe)
                    $this->db->where('id_enseignement', $dup['id_enseignement'])
                        ->delete('enseignements');
                }
                $this->db->where('id_enseignement', $row['id_enseignement'])
                    ->update('enseignements', ['id_enseignant' => $id_enseignant, 'deleted_at' => null]);
            } else {
                $this->load->helper('uuid');
                $this->db->insert('enseignements', [
                    'uuid' => generate_uuid(),
                    'id_enseignant' => $id_enseignant,
                    'id_matiere' => $mc['id_matiere'],
                    'id_classe' => $mc['id_classe'],
                    'id_matiere_classe' => $mc['id_matiere_classe']
                ]);
            }
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

        $this->upload->initialize($config);
        if ($this->upload->do_upload('file')) {
            $data = $this->upload->data();
            $this->json_success(['path' => 'assets/uploads/teachers/' . $data['file_name']]);
        } else {
            $this->json_error($this->upload->display_errors('', ''));
        }
    }

    public function api_delete($id) {
        $enseignant = $this->Model->readOne('enseignants', ['uuid' => $id]);
        if (!$enseignant) {
            $this->json_error('Enseignant non trouvé', 404); return;
        }

        $now = date('Y-m-d H:i:s');
        $this->db->trans_begin();
        $this->Model->update('enseignants', ['uuid' => $id], ['deleted_at' => $now]);
        $this->db->where('id_enseignant', $enseignant['id_enseignant'])
            ->update('matieres_classes', ['id_enseignant' => null]);
        $this->db->where('id_enseignant', $enseignant['id_enseignant'])
            ->where('deleted_at', null)
            ->update('enseignements', ['deleted_at' => $now]);
        if (!empty($enseignant['id_utilisateur'])) {
            $this->Model->update('utilisateurs', ['id_utilisateur' => $enseignant['id_utilisateur']], ['deleted_at' => $now, 'actif' => 0]);
        }
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->json_error('Erreur de suppression'); return;
        }
        $this->db->trans_commit();
        $this->json_success(null, 'Enseignant supprimé');
    }

}
