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
        $data['sections'] = $this->Model->read('sections', ['deleted_at' => null], 'libelle', 'ASC');
        $this->load->view('programmes', $data);
    }

    public function api_list() {
        $this->db->select("mc.*, m.libelle AS matiere_libelle, m.code AS matiere_code, cl.libelle AS classe_libelle, e.fullname AS enseignant_fullname");
        $this->db->from('matieres_classes mc');
        $this->db->join('matieres m', 'mc.id_matiere = m.id_matiere');
        $this->db->join('classes cl', 'mc.id_classe = cl.id_classe');
        $this->db->join('enseignants e', 'mc.id_enseignant = e.id_enseignant', 'left');
        $this->db->where('mc.deleted_at', null);
        $this->db->order_by('cl.libelle, m.libelle');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function capacite() {
        $data['title'] = 'Capacite des enseignants';
        $this->load->view('capacite', $data);
    }

    public function api_teacher_capacity() {
        $annee = $this->Model->readOne('annees_scolaires', ['est_en_cours' => 1]);
        $idAnnee = $annee ? $annee['id_annee'] : null;

        if (!$idAnnee) {
            $this->json_error('Aucune annee scolaire active trouvee');
            return;
        }

        $this->db->select("mc.*, m.libelle AS matiere_libelle, cl.libelle AS classe_libelle");
        $this->db->from('matieres_classes mc');
        $this->db->join('matieres m', 'mc.id_matiere = m.id_matiere');
        $this->db->join('classes cl', 'mc.id_classe = cl.id_classe');
        $this->db->where('mc.deleted_at', null);
        $matieresClasses = $this->db->get()->result_array();

        $this->db->select("ens.*, e.fullname AS enseignant_fullname");
        $this->db->from('enseignements ens');
        $this->db->join('enseignants e', 'ens.id_enseignant = e.id_enseignant', 'left');
        $this->db->where('ens.deleted_at', null);
        $enseignements = $this->db->get()->result_array();

        $this->db->where('deleted_at', null);
        $jours = $this->db->order_by('ordre', 'ASC')->get('jours_semaine')->result_array();

        $this->load->model('Horaires/Horaires_model');
        $allCreneaux = $this->Horaires_model->get_creneaux_cours();
        $creneaux = array_filter($allCreneaux, function($c) { return ($c['type'] ?? '') === 'cours'; });

        $this->db->where('deleted_at', null);
        $indisposRaw = $this->db->get('disponibilites_enseignants')->result_array();

        $indisponibilites = [];
        foreach ($indisposRaw as $ind) {
            $idEns = (int)$ind['id_enseignant'];
            $idJour = (int)$ind['id_jour'];
            $idCre = (int)$ind['id_creneau'];
            $type = $ind['type'] ?? 'indisponible';
            if ($type === 'indisponible') {
                $indisponibilites[$idEns][$idJour][$idCre] = true;
            }
        }

        $mapMC2Ens = [];
        foreach ($enseignements as $ens) {
            $mapMC2Ens[(int)$ens['id_matiere_classe']] = (int)$ens['id_enseignant'];
        }

        $teacherSessions = [];
        $teacherDetails = [];
        foreach ($matieresClasses as $mc) {
            $weekly = (int)$mc['nb_heures_par_semaine'];
            if ($weekly <= 0) continue;
            $mcId = (int)$mc['id_matiere_classe'];
            $teacherId = $mapMC2Ens[$mcId] ?? null;
            if (!$teacherId) continue;

            $teacherSessions[$teacherId] = ($teacherSessions[$teacherId] ?? 0) + $weekly;
            $teacherDetails[$teacherId][] = [
                'matiere' => $mc['matiere_libelle'] ?? "MC#$mcId",
                'classe' => $mc['classe_libelle'] ?? '',
                'heures' => $weekly,
            ];
        }

        $teacherNames = [];
        foreach ($enseignements as $ens) {
            $eid = (int)$ens['id_enseignant'];
            if (!isset($teacherNames[$eid])) {
                $teacherNames[$eid] = $ens['enseignant_fullname'] ?? "Enseignant#$eid";
            }
        }

        $table = [];
        foreach ($teacherSessions as $teacherId => $totalSessions) {
            $capacity = 0;
            $availableDays = [];
            foreach ($jours as $jour) {
                $daySlots = 0;
                foreach ($creneaux as $creneau) {
                    $jid = (int)$jour['id_jour'];
                    $cid = (int)$creneau['id_creneau'];
                    if (!isset($indisponibilites[$teacherId][$jid][$cid])) {
                        $daySlots++;
                    }
                }
                if ($daySlots > 0) {
                    $availableDays[] = [
                        'jour' => $jour['libelle'] ?? "Jour#{$jour['id_jour']}",
                        'slots' => $daySlots,
                    ];
                    $capacity += $daySlots;
                }
            }

            $marge = $capacity - $totalSessions;
            $status = 'ok';
            $statusLabel = 'OK';
            $statusColor = '#198754';
            if ($marge < 0) {
                $status = 'impossible';
                $statusLabel = 'IMPOSSIBLE';
                $statusColor = '#dc3545';
            } elseif ($marge === 0) {
                $status = 'marge_zero';
                $statusLabel = 'Marge zero';
                $statusColor = '#ffc107';
            } elseif ($marge <= 2) {
                $status = 'serré';
                $statusLabel = 'Tendu';
                $statusColor = '#fd7e14';
            }

            $table[] = [
                'id' => $teacherId,
                'nom' => $teacherNames[$teacherId] ?? "Enseignant#$teacherId",
                'sessions_semaine' => $totalSessions,
                'jours_dispo' => array_column($availableDays, 'jour'),
                'nb_jours' => count($availableDays),
                'creneaux_dispo' => $capacity,
                'marge' => $marge,
                'status' => $status,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
                'details' => $teacherDetails[$teacherId] ?? [],
            ];
        }

        usort($table, function($a, $b) {
            $order = ['impossible' => 0, 'marge_zero' => 1, 'serré' => 2, 'ok' => 3];
            $oa = $order[$a['status']] ?? 4;
            $ob = $order[$b['status']] ?? 4;
            if ($oa !== $ob) return $oa - $ob;
            return $a['marge'] - $b['marge'];
        });

        $this->json_success($table);
    }

    public function api_get($id) {
        $this->db->select("mc.*, m.libelle AS matiere_libelle, cl.libelle AS classe_libelle, e.fullname AS enseignant_fullname");
        $this->db->from('matieres_classes mc');
        $this->db->join('matieres m', 'mc.id_matiere = m.id_matiere');
        $this->db->join('classes cl', 'mc.id_classe = cl.id_classe');
        $this->db->join('enseignants e', 'mc.id_enseignant = e.id_enseignant', 'left');
        $this->db->where('mc.uuid', $id);
        $q_m = $this->db->get();
        $m = $q_m !== false ? $q_m->row_array() : null;
        if (!$m) { $this->json_error('Programme non trouvé', 404); return; }
        $this->json_success($m);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_matiere']) || empty($data['id_classe'])) {
            $this->json_error('Matière et classe sont obligatoires'); return;
        }

        $matiere = $this->Model->readOne('matieres', ['id_matiere' => $data['id_matiere'], 'deleted_at' => null]);
        if (!$matiere) { $this->json_error('Matière introuvable'); return; }
        $classe = $this->Model->readOne('classes', ['id_classe' => $data['id_classe'], 'deleted_at' => null]);
        if (!$classe) { $this->json_error('Classe introuvable'); return; }
        if (!empty($data['id_enseignant'])) {
            $ens = $this->Model->readOne('enseignants', ['id_enseignant' => $data['id_enseignant'], 'deleted_at' => null]);
            if (!$ens) { $this->json_error('Enseignant introuvable'); return; }
        }

        $note_max_matiere = isset($data['note_max_matiere']) ? floatval($data['note_max_matiere']) : 1.0;
        $nb_heures_jour = isset($data['nb_heures_par_jour']) ? floatval($data['nb_heures_par_jour']) : 0.0;
        $nb_heures_sem = isset($data['nb_heures_par_semaine']) ? floatval($data['nb_heures_par_semaine']) : 0.0;

        if ($note_max_matiere < 0 || $nb_heures_jour < 0 || $nb_heures_sem < 0) {
            $this->json_error('La note max matière et les volumes horaires ne peuvent pas être négatifs'); return;
        }

        $existing = $this->Model->readOne('matieres_classes', [
            'id_matiere' => $data['id_matiere'],
            'id_classe' => $data['id_classe'],
            'deleted_at' => null
        ]);
        if ($existing) {
            $this->json_error('Cette matière est déjà associée à cette classe'); return;
        }

        $softDeleted = $this->Model->readOne('matieres_classes', [
            'id_matiere' => $data['id_matiere'],
            'id_classe' => $data['id_classe']
        ]);
        if ($softDeleted) {
            $updateData = [
                'deleted_at' => null,
                'note_max_matiere' => $note_max_matiere,
                'nb_heures_par_jour' => $nb_heures_jour,
                'nb_heures_par_semaine' => $nb_heures_sem
            ];
            if (!empty($data['id_enseignant'])) {
                $updateData['id_enseignant'] = $data['id_enseignant'];
            }
            if ($this->Model->update('matieres_classes', ['id_matiere_classe' => $softDeleted['id_matiere_classe']], $updateData)) {
                $eff = $this->Model->readOne('matieres_classes', ['id_matiere_classe' => $softDeleted['id_matiere_classe']]);
                if ($eff && !empty($eff['id_enseignant'])) {
                    $this->_sync_enseignement($eff['id_matiere_classe'], $eff['id_enseignant'], $eff['id_matiere'], $eff['id_classe']);
                }
                $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'CREATION', 'Réactivation et mise à jour du programme matière-classe', 'matieres_classes', $softDeleted['id_matiere_classe'], null, $updateData);
                $this->json_success(['id_matiere_classe' => $softDeleted['id_matiere_classe']], 'Programme réactivé');
            } else {
                $this->json_error('Erreur lors de la réactivation');
            }
            return;
        }

        $insertData = [
            'id_matiere' => $data['id_matiere'],
            'id_classe' => $data['id_classe'],
            'note_max_matiere' => $note_max_matiere,
            'nb_heures_par_jour' => $nb_heures_jour,
            'nb_heures_par_semaine' => $nb_heures_sem
        ];
        if (!empty($data['id_enseignant'])) {
            $insertData['id_enseignant'] = $data['id_enseignant'];
        }

        $id = $this->Model->createLastId('matieres_classes', $insertData);
        if ($id) {
            if (!empty($data['id_enseignant'])) {
                $this->_sync_enseignement($id, $data['id_enseignant'], $data['id_matiere'], $data['id_classe']);
            }
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'CREATION', 'Création du programme matière-classe', 'matieres_classes', $id, null, $insertData);
            $this->json_success(['id_matiere_classe' => $id], 'Programme créé');
        } else {
            $this->json_error('Erreur lors de la création');
        }
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $record = $this->Model->readOne('matieres_classes', ['uuid' => $id]);
        if (!$record) { $this->json_error('Programme non trouvé', 404); return; }

        if (!empty($data['id_matiere'])) {
            $matiere = $this->Model->readOne('matieres', ['id_matiere' => $data['id_matiere'], 'deleted_at' => null]);
            if (!$matiere) { $this->json_error('Matière introuvable'); return; }
        }
        if (!empty($data['id_classe'])) {
            $classe = $this->Model->readOne('classes', ['id_classe' => $data['id_classe'], 'deleted_at' => null]);
            if (!$classe) { $this->json_error('Classe introuvable'); return; }
        }
        if (isset($data['id_enseignant']) && !empty($data['id_enseignant'])) {
            $ens = $this->Model->readOne('enseignants', ['id_enseignant' => $data['id_enseignant'], 'deleted_at' => null]);
            if (!$ens) { $this->json_error('Enseignant introuvable'); return; }
        }

        $target_matiere = isset($data['id_matiere']) ? $data['id_matiere'] : $record['id_matiere'];
        $target_classe = isset($data['id_classe']) ? $data['id_classe'] : $record['id_classe'];
        $dup = $this->Model->readOne('matieres_classes', [
            'id_matiere' => $target_matiere,
            'id_classe' => $target_classe
        ]);
        if ($dup && $dup['id_matiere_classe'] != $record['id_matiere_classe']) {
            $this->json_error('Cette matière est déjà associée à cette classe'); return;
        }

        $note_max_matiere = isset($data['note_max_matiere']) ? floatval($data['note_max_matiere']) : $record['note_max_matiere'];
        $nb_heures_jour = isset($data['nb_heures_par_jour']) ? floatval($data['nb_heures_par_jour']) : $record['nb_heures_par_jour'];
        $nb_heures_sem = isset($data['nb_heures_par_semaine']) ? floatval($data['nb_heures_par_semaine']) : $record['nb_heures_par_semaine'];

        if ($note_max_matiere < 0 || $nb_heures_jour < 0 || $nb_heures_sem < 0) {
            $this->json_error('La note max matière et les volumes horaires ne peuvent pas être négatifs'); return;
        }

        $allowed = ['id_matiere', 'id_classe', 'id_enseignant', 'note_max_matiere', 'nb_heures_par_jour', 'nb_heures_par_semaine'];
        $update = array_intersect_key($data, array_flip($allowed));
        $update['note_max_matiere'] = $note_max_matiere;
        $update['nb_heures_par_jour'] = $nb_heures_jour;
        $update['nb_heures_par_semaine'] = $nb_heures_sem;
        if (array_key_exists('id_enseignant', $data)) {
            $update['id_enseignant'] = !empty($data['id_enseignant']) ? $data['id_enseignant'] : null;
        }

        $this->db->trans_begin();
        $this->db->db_debug = false;
        $ok = $this->Model->update('matieres_classes', ['uuid' => $id], $update);
        if ($ok) {
            $updated = $this->Model->readOne('matieres_classes', ['id_matiere_classe' => $record['id_matiere_classe']]);
            if ($updated) {
                $changed = $updated['id_enseignant'] != $record['id_enseignant']
                    || $updated['id_matiere'] != $record['id_matiere']
                    || $updated['id_classe'] != $record['id_classe'];
                if ($changed) {
                    if (empty($updated['id_enseignant'])) {
                        $this->db->where('id_matiere_classe', $record['id_matiere_classe'])
                            ->where('deleted_at', null)
                            ->update('enseignements', ['deleted_at' => date('Y-m-d H:i:s')]);
                    } else {
                        $this->_sync_enseignement($record['id_matiere_classe'], $updated['id_enseignant'], $updated['id_matiere'], $updated['id_classe']);
                    }
                }
            }
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'MODIFICATION', 'Mise à jour du programme matière-classe', 'matieres_classes', $record['id_matiere_classe'], $record, $update);
        }
        $this->db->db_debug = true;
        if (!$ok) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de la mise à jour'); return;
        }
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de la mise à jour'); return;
        }
        $this->db->trans_commit();
        $this->json_success(null, 'Programme mis à jour');
    }

    public function api_delete($id) {
        $record = $this->Model->readOne('matieres_classes', ['uuid' => $id]);
        if (!$record) { $this->json_error('Programme non trouvé', 404); return; }

        $now = date('Y-m-d H:i:s');
        $this->db->trans_begin();
        $this->Model->update('matieres_classes', ['uuid' => $id], ['deleted_at' => $now]);
        $this->db->where('id_matiere_classe', $record['id_matiere_classe'])
            ->where('deleted_at', null)
            ->update('enseignements', ['deleted_at' => $now]);

        $ens_rows = $this->db->select('id_enseignement')
            ->where('id_matiere_classe', $record['id_matiere_classe'])
            ->get('enseignements')
            ->result_array();
        $ens_ids = array_column($ens_rows, 'id_enseignement');
        if (!empty($ens_ids)) {
            $this->db->where_in('id_enseignement', $ens_ids)
                ->where('deleted_at', null)
                ->update('horaires', ['deleted_at' => $now]);
        }

        $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'SUPPRESSION', 'Suppression logique du programme matière-classe', 'matieres_classes', $record['id_matiere_classe']);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de la suppression'); return;
        }
        $this->db->trans_commit();
        $this->json_success(null, 'Programme supprimé');
    }

    private function _sync_enseignement($id_mc, $id_enseignant, $id_matiere, $id_classe) {
        $this->db->db_debug = false;
        $row = $this->db->where('id_matiere_classe', $id_mc)
            ->get('enseignements')
            ->row_array();
        if (!$row) {
            $row = $this->db->where('id_enseignant', $id_enseignant)
                ->where('id_matiere', $id_matiere)
                ->where('id_classe', $id_classe)
                ->get('enseignements')
                ->row_array();
        }
        if ($row) {
            $dup = $this->db->where('id_enseignant', $id_enseignant)
                ->where('id_matiere', $id_matiere)
                ->where('id_classe', $id_classe)
                ->where('id_enseignement !=', $row['id_enseignement'])
                ->get('enseignements')
                ->row_array();
            if ($dup) {
                $this->db->where('id_enseignement', $dup['id_enseignement'])
                    ->delete('enseignements');
            }
            $this->db->where('id_enseignement', $row['id_enseignement'])
                ->update('enseignements', [
                    'id_enseignant' => $id_enseignant,
                    'id_matiere' => $id_matiere,
                    'id_classe' => $id_classe,
                    'id_matiere_classe' => $id_mc,
                    'deleted_at' => null
                ]);
        } else {
            $this->load->helper('uuid');
            $this->db->insert('enseignements', [
                'uuid' => generate_uuid(),
                'id_enseignant' => $id_enseignant,
                'id_matiere' => $id_matiere,
                'id_classe' => $id_classe,
                'id_matiere_classe' => $id_mc
            ]);
        }
        $this->db->db_debug = true;
    }
}
