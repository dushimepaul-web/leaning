<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Annees extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des années scolaires';
        $this->load->view('annees', $data);
    }

    public function api_list() {
        $status = $this->input->get('status');
        if ($status === 'active') {
            $this->db->where('deleted_at', null);
        } elseif ($status === 'deleted') {
            $this->db->where('deleted_at IS NOT NULL', null, false);
        }
        $this->db->order_by('id_annee', 'DESC');
        $this->json_success($this->db->get('annees_scolaires')->result_array());
    }

    public function api_get($id) {
        $a = $this->Model->readOne('annees_scolaires', ['uuid' => $id, 'deleted_at' => null]);
        if (!$a) { $this->json_error('Année non trouvée', 404); return; }
        $this->json_success($a);
    }

    public function api_create() {
        $input = $this->get_json_input();
        $libelle = trim($input['libelle'] ?? '');
        if (empty($libelle)) { $this->json_error('Libellé obligatoire'); return; }
        if (!preg_match('/^\d{4}-\d{4}$/', $libelle)) { $this->json_error('Format de libellé invalide. Utilisez le format AAAA-AAAA (ex: 2024-2025)'); return; }

        $dup = $this->Model->readOne('annees_scolaires', ['libelle' => $libelle]);
        if ($dup) { $this->json_error('Ce libellé d\'année existe déjà'); return; }

        $data = [
            'libelle' => $libelle,
            'debut' => !empty($input['debut']) ? $input['debut'] : null,
            'fin' => !empty($input['fin']) ? $input['fin'] : null,
            'est_en_cours' => !empty($input['est_en_cours']) ? 1 : 0
        ];
        if (empty($data['debut']) || empty($data['fin'])) {
            $this->json_error('Les dates de début et de fin sont obligatoires'); return;
        }
        if ($data['debut'] > $data['fin']) {
            $this->json_error('La date de début doit être antérieure à la date de fin'); return;
        }

        $this->db->trans_begin();
        if ($data['est_en_cours'] == 1) {
            $this->db->update('annees_scolaires', ['est_en_cours' => 0]);
        }

        $id = $this->Model->createLastId('annees_scolaires', $data);
        if ($id && $this->db->trans_status()) {
            $this->db->trans_commit();
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'CREATION', 'Création de l\'année scolaire ' . $data['libelle'], 'annees_scolaires', $id, null, $data);
            $this->json_success(['id_annee' => $id], 'Année scolaire créée');
        } else {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de la création ou libellé déjà existant');
        }
    }

    public function api_update($id) {
        $existing = $this->Model->readOne('annees_scolaires', ['uuid' => $id, 'deleted_at' => null]);
        if (!$existing) {
            $this->json_error('Année non trouvée', 404); return;
        }
        $input = $this->get_json_input();
        $libelle = trim($input['libelle'] ?? $existing['libelle']);
        if (empty($libelle)) { $this->json_error('Libellé obligatoire'); return; }
        if (!preg_match('/^\d{4}-\d{4}$/', $libelle)) { $this->json_error('Format de libellé invalide. Utilisez le format AAAA-AAAA (ex: 2024-2025)'); return; }

        $dup = $this->Model->readOne('annees_scolaires', ['libelle' => $libelle]);
        if ($dup && $dup['id_annee'] != $existing['id_annee']) { $this->json_error('Ce libellé d\'année existe déjà'); return; }

        if ($existing['est_en_cours'] == 1 && isset($input['est_en_cours']) && empty($input['est_en_cours'])) {
            $this->json_error('Impossible de désactiver l\'année en cours. Définissez d\'abord une autre année en cours.');
            return;
        }

        $data = [
            'libelle' => $libelle,
            'debut' => isset($input['debut']) ? ($input['debut'] ?: null) : $existing['debut'],
            'fin' => isset($input['fin']) ? ($input['fin'] ?: null) : $existing['fin'],
            'est_en_cours' => isset($input['est_en_cours']) ? ($input['est_en_cours'] ? 1 : 0) : $existing['est_en_cours']
        ];
        if (!empty($data['debut']) && !empty($data['fin']) && $data['debut'] > $data['fin']) {
            $this->json_error('La date de début doit être antérieure à la date de fin'); return;
        }

        $this->db->trans_begin();
        if ($data['est_en_cours'] == 1 && $existing['est_en_cours'] != 1) {
            $this->db->update('annees_scolaires', ['est_en_cours' => 0]);
        }

        if ($this->Model->update('annees_scolaires', ['uuid' => $id], $data) && $this->db->trans_status()) {
            $this->db->trans_commit();
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'MODIFICATION', 'Mise à jour de l\'année scolaire ' . $data['libelle'], 'annees_scolaires', $existing['id_annee'], $existing, $data);
            $this->json_success(null, 'Année scolaire mise à jour');
        } else {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de la mise à jour');
        }
    }

    public function api_delete($id) {
        $existing = $this->Model->readOne('annees_scolaires', ['uuid' => $id, 'deleted_at' => null]);
        if (!$existing) { $this->json_error('Année non trouvée', 404); return; }

        if ($existing['est_en_cours'] == 1) {
            $this->json_error('Impossible de supprimer l\'année en cours. Définissez d\'abord une autre année en cours.');
            return;
        }

        if ($this->Model->update('annees_scolaires', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s'), 'est_en_cours' => 0])) {
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'SUPPRESSION', 'Suppression logique de l\'année scolaire ' . $existing['libelle'], 'annees_scolaires', $existing['id_annee']);
            $this->json_success(null, 'Année scolaire supprimée');
        } else {
            $this->json_error('Erreur');
        }
    }

    public function api_activate($id) {
        $existing = $this->Model->readOne('annees_scolaires', ['uuid' => $id]);
        if (!$existing) { $this->json_error('Année non trouvée', 404); return; }

        if ($this->Model->update('annees_scolaires', ['uuid' => $id], ['deleted_at' => null])) {
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'ACTIVATION', 'Activation de l\'année scolaire ' . $existing['libelle'], 'annees_scolaires', $existing['id_annee']);
            $this->json_success(null, 'Année activée');
        } else {
            $this->json_error('Erreur');
        }
    }

    public function api_set_active($id) {
        $existing = $this->Model->readOne('annees_scolaires', ['uuid' => $id]);
        if (!$existing) { $this->json_error('Année non trouvée', 404); return; }

        $this->db->trans_start();
        $this->db->update('annees_scolaires', ['est_en_cours' => 0]);
        $this->db->where('uuid', $id);
        $this->db->update('annees_scolaires', ['est_en_cours' => 1, 'deleted_at' => null]);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->json_error('Erreur lors de la définition de l\'année en cours');
        } else {
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'ANNEE_EN_COURS', 'Définition de l\'année active : ' . $existing['libelle'], 'annees_scolaires', $existing['id_annee']);
            $this->json_success(null, 'Année définie en cours');
        }
    }

    private function get_param($clef, $defaut) {
        $row = $this->db->get_where('parametres', ['clef' => $clef, 'deleted_at' => null])->row_array();
        return $row ? $row['valeur'] : $defaut;
    }

    private function _calculer_report($id_annee_source) {
        $seuil_moyenne = floatval($this->get_param('seuil_moyenne', 50));
        $seuil_matiere = floatval($this->get_param('seuil_matiere', 50));
        $max_repechage = intval($this->get_param('max_repechage', 3));

        $inscriptions = $this->db->get_where('inscriptions', ['id_annee' => $id_annee_source, 'deleted_at' => null])->result_array();
        $resultats = [];

        foreach ($inscriptions as $insc) {
            $etudiant = $this->Model->readOne('etudiants', ['id_etudiant' => $insc['id_etudiant']]);
            $classe_actuelle = $this->Model->readOne('classes', ['id_classe' => $insc['id_classe']]);
            
            $bulletin = $this->db->order_by('id_periode', 'DESC')
                ->get_where('bulletins', ['id_etudiant' => $insc['id_etudiant'], 'id_annee' => $id_annee_source, 'deleted_at' => null])
                ->row_array();

            $decision = 'inconnu';
            $moyenne = 0;
            $matieres_echec = [];
            $classe_dest_id = $insc['id_classe'];
            $classe_dest_libelle = $classe_actuelle['libelle'] ?? '-';

            if ($bulletin) {
                $moyenne = floatval($bulletin['moyenne']);
                $notes_matieres = $this->db->select('m.id_matiere, m.libelle, mc.note_max_matiere, SUM(n.note) as total_note')
                    ->from('notes n')
                    ->join('evaluations ev', 'ev.id_evaluation = n.id_evaluation')
                    ->join('matieres_classes mc', 'mc.id_matiere = ev.id_matiere AND mc.id_classe = ev.id_classe')
                    ->join('matieres m', 'm.id_matiere = mc.id_matiere')
                    ->where('n.id_etudiant', $insc['id_etudiant'])
                    ->where('ev.id_classe', $insc['id_classe'])
                    ->where('n.deleted_at', null)
                    ->group_by('m.id_matiere')
                    ->get()->result_array();

                $nb_echecs = 0;
                foreach ($notes_matieres as $nm) {
                    $max_matiere = floatval($nm['note_max_matiere']) * 6;
                    $pct_matiere = $max_matiere > 0 ? (floatval($nm['total_note']) / $max_matiere) * 100 : 0;
                    if ($pct_matiere < $seuil_matiere) {
                        $matieres_echec[] = $nm['libelle'] . ' (' . round($pct_matiere, 1) . '%)';
                        $nb_echecs++;
                    }
                }

if ($moyenne < $seuil_moyenne) {
                    $decision = 'ajourne';
                } elseif ($nb_echecs == 0) {
                    $decision = 'admis';
                } elseif ($nb_echecs <= $max_repechage) {
                    $decision = 'repechage';
                } else {
                    $decision = 'ajourne';
                }
            } else {
                // Pas de bulletin = élève non en ordre → il REDOUBLE toujours
                $decision = 'rester_sans_bulletin';
            }

            // Classes de destination possibles pour la sélection manuelle
            $classe_suivante = null;
            if ($classe_actuelle && isset($classe_actuelle['id_section'])) {
                $classe_suivante = $this->db->where('id_section', $classe_actuelle['id_section'])
                    ->where('ordre >', $classe_actuelle['ordre'])
                    ->where('deleted_at', null)
                    ->order_by('ordre', 'ASC')
                    ->limit(1)
                    ->get('classes')->row_array();
            }
            $classes_dispo = [];
            if ($classe_actuelle) {
                $classes_dispo[] = ['id' => $classe_actuelle['id_classe'], 'libelle' => $classe_actuelle['libelle'] . ' (Redouble)'];
            }
            if ($classe_suivante) {
                $classes_dispo[] = ['id' => $classe_suivante['id_classe'], 'libelle' => $classe_suivante['libelle']];
            }
            $classes_dispo[] = ['id' => 0, 'libelle' => 'Sortant / Diplômé'];

            // Promotion automatique uniquement pour ADMIS / SANS BULLETIN (passer)
            // Le REPÊCHAGE n'est PAS promu automatiquement : le passage se décide MANUELLEMENT dans l'aperçu.
            if (in_array($decision, ['admis', 'admis_sans_bulletin'])) {
                if ($classe_actuelle && isset($classe_actuelle['est_dernier_niveau']) && $classe_actuelle['est_dernier_niveau'] == 1) {
                    $decision = 'sortant';
                    $classe_dest_libelle = 'Sortant / Diplômé';
                } elseif ($classe_suivante) {
                    $classe_dest_id = $classe_suivante['id_classe'];
                    $classe_dest_libelle = $classe_suivante['libelle'];
                } else {
                    $decision = 'sortant';
                    $classe_dest_libelle = 'Fin de cycle';
                }
            } else {
                $classe_dest_id = $insc['id_classe'];
                $classe_dest_libelle = ($classe_actuelle['libelle'] ?? '-') . ($decision === 'repechage' ? ' (Repêchage)' : ' (Redouble)');
            }

            $resultats[] = [
                'id_etudiant' => $insc['id_etudiant'],
                'nom' => ($etudiant['nom'] ?? '') . ' ' . ($etudiant['prenom'] ?? ''),
                'matricule' => $etudiant['matricule'] ?? '',
                'classe_actuelle' => $classe_actuelle['libelle'] ?? '-',
                'moyenne' => $moyenne,
                'matieres_echec' => $matieres_echec,
                'decision' => $decision,
                'classe_dest_id' => $classe_dest_id,
                'classe_dest_libelle' => $classe_dest_libelle,
                'classes_dispo' => $classes_dispo
            ];
        }
        return $resultats;
    }

    public function api_apercu_cloture() {
        $input = $this->get_json_input();
        $id_annee_source = $input['id_annee_source'] ?? null;
        $id_annee_cible = $input['id_annee_cible'] ?? null;

        if (!$id_annee_source || !$id_annee_cible || $id_annee_source == $id_annee_cible) {
            $this->json_error('Veuillez sélectionner une année source et une année cible différentes.');
            return;
        }

        $apercu = $this->_calculer_report($id_annee_source);
        $this->json_success($apercu);
    }

    public function api_cloturer() {
        $input = $this->get_json_input();
        $id_annee_source = $input['id_annee_source'] ?? null;
        $id_annee_cible = $input['id_annee_cible'] ?? null;
        $reporter_eleves = isset($input['reporter_eleves']) ? (bool)$input['reporter_eleves'] : true;
        $decisions_override = $input['decisions_override'] ?? [];

        if (!$id_annee_source || !$id_annee_cible || $id_annee_source == $id_annee_cible) {
            $this->json_error('Veuillez sélectionner une année source et une année cible différentes.');
            return;
        }

        $source = $this->Model->readOne('annees_scolaires', ['id_annee' => $id_annee_source]);
        $cible = $this->Model->readOne('annees_scolaires', ['id_annee' => $id_annee_cible]);

        if (!$source || !$cible) {
            $this->json_error('Année source ou cible introuvable.');
            return;
        }
        if ($source['deleted_at'] !== null) {
            $this->json_error('L\'année source est supprimée. Activez-la avant de clôturer.');
            return;
        }
        if ($cible['deleted_at'] !== null) {
            $this->json_error('L\'année cible est supprimée. Activez-la avant de clôturer.');
            return;
        }

        $this->db->trans_start();
        $this->db->db_debug = false;

        $this->db->where('id_annee', $id_annee_source)->update('annees_scolaires', ['est_en_cours' => 0]);
        $this->db->update('annees_scolaires', ['est_en_cours' => 0]);
        $this->db->where('id_annee', $id_annee_cible)->update('annees_scolaires', ['est_en_cours' => 1, 'deleted_at' => null]);

        $inscriptions_reportees = 0;
        if ($reporter_eleves) {
            $apercu_calc = $this->_calculer_report($id_annee_source);
            $apercu_map = [];
            foreach ($apercu_calc as $item) {
                $apercu_map[$item['id_etudiant']] = $item['classe_dest_id'];
            }
            // Override manuel éventuel
            if (!empty($decisions_override)) {
                foreach ($decisions_override as $ov) {
                    if (isset($ov['id_etudiant']) && isset($ov['classe_dest_id'])) {
                        $apercu_map[$ov['id_etudiant']] = $ov['classe_dest_id'];
                    }
                }
            }

            foreach ($apercu_map as $id_etudiant => $id_classe_dest) {
                $deja = $this->Model->readOne('inscriptions', [
                    'id_etudiant' => $id_etudiant,
                    'id_annee' => $id_annee_cible,
                    'deleted_at' => null
                ]);

                if (!$deja) {
                    $nouvelle = $this->Model->readOne('inscriptions', [
                        'id_etudiant' => $id_etudiant,
                        'id_annee' => $id_annee_cible
                    ]);
                    if ($nouvelle) {
                        $this->db->where('id_inscription', $nouvelle['id_inscription'])
                            ->update('inscriptions', [
                                'id_classe' => $id_classe_dest,
                                'date_inscription' => date('Y-m-d'),
                                'statut' => 'actif',
                                'deleted_at' => null
                            ]);
                    } else {
                        $nouvelle_inscription = [
                            'uuid' => generate_uuid(),
                            'id_etudiant' => $id_etudiant,
                            'id_classe' => $id_classe_dest,
                            'id_annee' => $id_annee_cible,
                            'date_inscription' => date('Y-m-d'),
                            'statut' => 'actif'
                        ];
                        $this->db->insert('inscriptions', $nouvelle_inscription);
                    }
                    $inscriptions_reportees++;
                }
            }
        }

        $this->db->trans_complete();
        $this->db->db_debug = true;

        if ($this->db->trans_status() === FALSE) {
            $this->json_error('Erreur lors de la clôture et du report intelligent des inscriptions.');
        } else {
            $msg = "Année '{$source['libelle']}' clôturée. Année '{$cible['libelle']}' activée.";
            if ($reporter_eleves) {
                $msg .= $inscriptions_reportees == 1 ? " 1 élève reporté." : " $inscriptions_reportees élèves reportés.";
            }
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'CLOTURE_ANNEE', $msg, 'annees_scolaires', $id_annee_source);
            $this->json_success(null, $msg);
        }
    }
}
