<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Horaires extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Horaires_model');
        $this->load->library('HorairesGenerator');
    }

    public function index() {
        $parametres = $this->Horaires_model->get_parametres_map();
        $data['classes'] = $this->db->where('deleted_at IS NULL')->get('classes')->result_array();
        $data['creneaux'] = $this->Horaires_model->get_creneaux_cours();
        $data['creneaux_mardi'] = $this->Horaires_model->get_creneaux_mardi();
        $data['jours'] = $this->db->where('deleted_at IS NULL')->order_by('id_jour', 'ASC')->get('jours_semaine')->result_array();
        $data['jour_special'] = $parametres['jour_special'] ?? 'mardi';
        $data['jour_special_actif'] = $parametres['jour_special_actif'] ?? '1';
        $data['annee_label'] = '';
        $annee = $this->Model->readOne('annees_scolaires', ['est_en_cours' => 1]);
        if ($annee) {
            $data['annee_label'] = $annee['libelle'] ?? ($annee['date_debut'] . '-' . $annee['date_fin']);
        }
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->json_success($this->Horaires_model->list_horaires());
    }

    public function api_get($id) {
        $this->db->where('h.deleted_at', null);
        $this->db->select('h.*, e.fullname as enseignant, m.code as matiere_code, m.libelle as matiere_libelle, c.libelle as classe_libelle');
        $this->db->from('horaires h');
        $this->db->join('enseignants e', 'h.id_enseignant = e.id_enseignant', 'left');
        $this->db->join('matieres m', 'h.id_matiere = m.id_matiere', 'left');
        $this->db->join('classes c', 'h.id_classe = c.id_classe', 'left');
        $this->db->where('h.uuid', $id);
        $q = $this->db->get();
        $row = $q !== false ? $q->row_array() : null;
        if (!$row) { $this->json_error('Horaire non trouvé', 404); return; }
        $this->json_success($row);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_enseignement']) || empty($data['id_matiere']) || empty($data['id_enseignant']) ||
            empty($data['id_classe']) || empty($data['id_creneau']) || empty($data['id_jour'])) {
            $this->json_error('Tous les champs sont obligatoires'); return;
        }
        $gen = $this->Horaires_model->get_latest_generation();
        if (!$gen) { $this->json_error('Aucune génération existante'); return; }
        $insert = [
            'uuid' => function_exists('random_string') ? random_string('alnum', 36) : md5(uniqid(rand(), true)),
            'id_generation' => $gen['id_generation'],
            'id_enseignement' => $data['id_enseignement'],
            'id_matiere' => $data['id_matiere'],
            'id_enseignant' => $data['id_enseignant'],
            'id_classe' => $data['id_classe'],
            'id_creneau' => $data['id_creneau'],
            'id_jour' => $data['id_jour'],
            'deleted_at' => null
        ];
        $id = $this->Model->createLastId('horaires', $insert);
        if ($id) $this->json_success(['id_horaire' => $id], 'Horaire créé');
        else $this->json_error('Erreur lors de la création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $existing = $this->Model->readOne('horaires', ['uuid' => $id]);
        if (!$existing) { $this->json_error('Horaire non trouvé', 404); return; }
        $allowed = ['id_enseignement', 'id_matiere', 'id_enseignant', 'id_classe', 'id_creneau', 'id_jour'];
        $update = array_intersect_key($data ?? [], array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if ($this->Model->update('horaires', ['uuid' => $id], $update))
            $this->json_success(null, 'Horaire mis à jour');
        else $this->json_error('Erreur lors de la mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('horaires', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Horaire supprimé');
        else $this->json_error('Erreur lors de la suppression');
    }

    public function api_generations() {
        $this->db->order_by('id_generation', 'DESC');
        $q = $this->db->get('horaires_generations');
        $this->json_success($q !== false ? $q->result_array() : []);
    }

    public function api_matieres_by_classe($id_classe) {
        $this->db->select('mc.*, m.code as matiere_code, m.libelle as matiere_libelle, e.fullname as enseignant');
        $this->db->from('matieres_classes mc');
        $this->db->join('matieres m', 'mc.id_matiere = m.id_matiere', 'left');
        $this->db->join('enseignants e', 'mc.id_enseignant = e.id_enseignant', 'left');
        $this->db->where('mc.id_classe', $id_classe);
        $this->db->where('mc.deleted_at', null);
        $this->db->where('mc.nb_heures_par_semaine >', 0);
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : []);
    }

    public function api_enseignant_by_classe_matiere($id_classe, $id_matiere) {
        $this->db->select('e.*, ens.id_enseignement');
        $this->db->from('enseignements ens');
        $this->db->join('enseignants e', 'ens.id_enseignant = e.id_enseignant', 'left');
        $this->db->where('ens.id_classe', $id_classe);
        $this->db->where('ens.id_matiere', $id_matiere);
        $this->db->where('ens.deleted_at', null);
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : []);
    }

    public function fixes() {
        $data['title'] = 'Sessions Fixes';
        $annee = $this->Model->readOne('annees_scolaires', ['est_en_cours' => 1]);
        $data['annee_label'] = $annee ? ($annee['libelle'] ?? '') : '';
        $data['id_annee'] = $annee ? $annee['id_annee'] : 0;
        $data['classes'] = $this->db->where('deleted_at IS NULL')->get('classes')->result_array();
        $data['creneaux'] = $this->Horaires_model->get_creneaux_cours();
        $data['jours'] = $this->db->where('deleted_at IS NULL')->order_by('id_jour', 'ASC')->get('jours_semaine')->result_array();
        $data['enseignants'] = $this->db->where('deleted_at IS NULL')->get('enseignants')->result_array();
        $this->load->view('fixes', $data);
    }

    public function api_fixes_list() {
        $annee = $this->Model->readOne('annees_scolaires', ['est_en_cours' => 1]);
        if (!$annee) { $this->json_error('Aucune année active'); return; }
        $this->json_success($this->Horaires_model->get_fixes($annee['id_annee']));
    }

    public function api_fixes_create() {
        $data = $this->get_json_input();
        if (empty($data['id_classe']) || empty($data['id_matiere_classe']) || empty($data['id_enseignant']) ||
            empty($data['id_jour']) || empty($data['id_creneau'])) {
            $this->json_error('Tous les champs sont obligatoires'); return;
        }
        $annee = $this->Model->readOne('annees_scolaires', ['est_en_cours' => 1]);
        if (!$annee) { $this->json_error('Aucune année active'); return; }

        $insert = [
            'uuid' => generate_uuid(),
            'id_annee' => $annee['id_annee'],
            'id_classe' => (int)$data['id_classe'],
            'id_matiere_classe' => (int)$data['id_matiere_classe'],
            'id_enseignant' => (int)$data['id_enseignant'],
            'id_jour' => (int)$data['id_jour'],
            'id_creneau' => (int)$data['id_creneau'],
        ];
        $id = $this->Horaires_model->add_fixe($insert);
        if ($id) $this->json_success(['id_horaire_fixe' => $id], 'Session fixe créée');
        else $this->json_error('Erreur lors de la création');
    }

    public function api_fixes_delete($uuid) {
        if ($this->Horaires_model->remove_fixe($uuid))
            $this->json_success(null, 'Session fixe supprimée');
        else $this->json_error('Erreur lors de la suppression');
    }

    public function api_fixes_clear() {
        $annee = $this->Model->readOne('annees_scolaires', ['est_en_cours' => 1]);
        if (!$annee) { $this->json_error('Aucune année active'); return; }
        $this->Horaires_model->clear_fixes($annee['id_annee']);
        $this->json_success(null, 'Toutes les sessions fixes ont été supprimées');
    }

    public function api_diagnostiquer() {
        $annee = $this->Model->readOne('annees_scolaires', ['est_en_cours' => 1]);
        $idAnnee = $annee ? $annee['id_annee'] : $this->id_annee_active;

        $payload = $this->Horaires_model->get_generation_payload();
        $payload['fixes'] = $this->Horaires_model->get_fixes_by_annee($idAnnee);
        $preflight = $this->horairesgenerator->preflight($payload);

        $warnings = 0;
        foreach ($preflight['diagnostics'] as $d) {
            if (empty($d['blocking'])) $warnings++;
        }

        return $this->json_success([
            'success' => $preflight['success'],
            'diagnostics' => $preflight['diagnostics'],
            'warnings' => $warnings,
            'placed' => 0,
            'expected' => 0,
        ]);
    }

    public function api_generer() {
        $this->generer();
    }

    public function generer() {
        $lockName = 'vip_school_horaires_generation_lock';
        $lockAcquired = false;

        try {
            $lockQuery = $this->db->query("SELECT GET_LOCK(?, 10) as lk", [$lockName]);
            $lockAcquired = (bool)$lockQuery->row()->lk;

            if (!$lockAcquired) {
                return $this->json_error("Une génération est déjà en cours. Veuillez patienter.", 423);
            }

            $annee = $this->Model->readOne('annees_scolaires', ['est_en_cours' => 1]);
            $idAnnee = $annee ? $annee['id_annee'] : $this->id_annee_active;

            $payload = $this->Horaires_model->get_generation_payload();
            $payload['fixes'] = $this->Horaires_model->get_fixes_by_annee($idAnnee);
            $preflight = $this->horairesgenerator->preflight($payload);
            if (empty($preflight['success'])) {
                $blockingMessages = [];
                foreach ($preflight['diagnostics'] as $d) {
                    if (!empty($d['blocking'])) $blockingMessages[] = $d['message'];
                }
                return $this->json_response([
                    'success' => false,
                    'message' => 'Génération impossible : corrigez les contraintes suivantes.',
                    'diagnostics' => $preflight['diagnostics'],
                    'blocking_messages' => $blockingMessages
                ], 422);
            }
            $result = $this->horairesgenerator->generate($payload);

            log_message('error', 'GENERATE RESULT: ' . json_encode($result['validation']));

            if (!$result['success'] || $result['validation']['missing'] > 0 || $result['validation']['conflicts_prof'] > 0 || $result['validation']['conflicts_classe'] > 0 || $result['validation']['daily_limit_violations'] > 0 || $result['validation']['availability_violations'] > 0) {
                $validation = $result['validation'];
                $detail = 'Placés: ' . $validation['placed'] . '/' . $validation['expected']
                    . ' | Manquants: ' . $validation['missing']
                    . ' | Conflits prof: ' . $validation['conflicts_prof']
                    . ' | Conflits classe: ' . $validation['conflicts_classe']
                    . ' | Limites quotidiennes: ' . $validation['daily_limit_violations']
                    . ' | Indisponibilités: ' . $validation['availability_violations'];
                $messages = [];
                if (!empty($validation['conflict_details'])) {
                    foreach ($validation['conflict_details'] as $cd) {
                        $messages[] = $cd['message'];
                    }
                }
                if ($validation['missing'] > 0) {
                    $messages[] = "{$validation['missing']} cours non placé(s).";
                }
                return $this->json_response([
                    'success' => false,
                    'message' => 'Génération échouée : conflits détectés.',
                    'detail' => $detail,
                    'messages' => $messages,
                    'validation' => $validation
                ], 422);
            }

            $this->db->truncate('horaires');

            $this->db->trans_begin();

            $generationId = $this->Horaires_model->create_generation_record([
                'uuid' => function_exists('random_string') ? random_string('alnum', 36) : md5(uniqid(rand(), true)),
                'id_annee' => $idAnnee,
                'statut' => 'brouillon'
            ]);

            $batchRows = [];
            $sessionsToSave = !empty($result['sessionsPlaced']) ? $result['sessionsPlaced'] : $result['grid'];
            foreach ($sessionsToSave as $session) {
                $isFixe = !empty($session['type']) && $session['type'] === 'fixe';
                $batchRows[] = [
                    'uuid' => function_exists('random_string') ? random_string('alnum', 36) : md5(uniqid(rand(), true)),
                    'id_generation' => $generationId,
                    'id_enseignement' => $session['id_enseignement'],
                    'id_matiere' => $session['id_matiere'],
                    'id_enseignant' => $session['id_enseignant'],
                    'id_classe' => $session['id_classe'],
                    'id_creneau' => $session['id_creneau'],
                    'id_jour' => $session['id_jour'],
                    'deleted_at' => null
                ];
            }

            if (!empty($batchRows)) {
                $this->Horaires_model->insert_horaires_batch($batchRows);
            }

            $insertedCount = $this->db->where('id_generation', $generationId)->where('deleted_at IS NULL')->count_all_results('horaires');
            $expectedTotal = $result['validation']['expected'];
            if ($insertedCount !== (int)$expectedTotal) {
                throw new Exception("Erreur de double validation post-insertion : Lignes insérées ($insertedCount) != Attendues ($expectedTotal).");
            }

            if ($this->db->trans_status() === FALSE) {
                throw new Exception("Erreur critique lors de la transaction BDD.");
            }

            $this->db->trans_commit();

            $v = $result['validation'];
            return $this->json_success([
                'success' => true,
                'message' => 'Génération réussie à 100% (' . $v['placed'] . '/' . $v['expected'] . ')',
                'created' => $v['placed'],
                'conflits_restants' => $v['conflicts_prof'] + $v['conflicts_classe'] + $v['missing'],
                'pass5' => ['swap_logistique' => 0],
                'cre' => ['placees' => 0, 'log' => []],
                'placements_swap_logistique' => [],
                'validation' => $v
            ]);

        } catch (Exception $e) {
            if ($this->db->trans_status() !== NULL) {
                $this->db->trans_rollback();
            }
            return $this->json_error("Erreur critique : " . $e->getMessage(), 500);

        } finally {
            if ($lockAcquired) {
                $this->db->query("SELECT RELEASE_LOCK(?)", [$lockName]);
            }
        }
    }
}
