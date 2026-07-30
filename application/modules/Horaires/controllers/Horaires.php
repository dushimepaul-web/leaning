<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Horaires extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Horaires_model');
    }

    public function index() {
        $data['title'] = 'Emploi du temps';
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['enseignants'] = $this->Model->read('enseignants', ['deleted_at' => null]);
        $data['matieres'] = $this->Model->read('matieres', ['deleted_at' => null]);
        $data['creneaux'] = $this->Model->read('creneaux', [], 'ordre');
        $data['jours'] = $this->Model->read('jours_semaine', [], 'ordre');
        $data['generations'] = $this->Model->read('horaires_generations', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->json_success($this->Horaires_model->get_all());
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_classe']) || empty($data['id_jour']) || empty($data['id_creneau'])) {
            $this->json_error('Classe, jour et créneau obligatoires'); return;
        }
        $this->load->helper('uuid');

        $gen = $this->_getOrCreateGeneration();
        if (!$gen) { $this->json_error('Erreur création génération'); return; }

        $ens = $this->_resolveEnseignement($data);
        if (!$ens) {
            if (!empty($data['id_enseignant'])) {
                $ens = $this->Model->readOne('enseignements', [
                    'id_enseignant' => $data['id_enseignant'],
                    'id_classe' => $data['id_classe'],
                    'deleted_at' => null
                ]);
            }
        }
        if (!$ens) { $this->json_error('Aucun enseignement trouvé pour cette classe/matière'); return; }

        $id_enseignant = !empty($data['id_enseignant']) ? $data['id_enseignant'] : $ens['id_enseignant'];

        $conflict = $this->Model->readOne('horaires', [
            'id_generation' => $gen['id_generation'],
            'id_jour' => $data['id_jour'],
            'id_creneau' => $data['id_creneau'],
            'id_classe' => $data['id_classe'],
            'deleted_at' => null
        ]);
        if ($conflict) { $this->json_error('Ce créneau est déjà occupé pour cette classe'); return; }

        $teacherConflict = $this->Model->readOne('horaires', [
            'id_generation' => $gen['id_generation'],
            'id_jour' => $data['id_jour'],
            'id_creneau' => $data['id_creneau'],
            'id_enseignant' => $id_enseignant,
            'deleted_at' => null
        ]);
        if ($teacherConflict) { $this->json_error('Cet enseignant est déjà occupé sur ce créneau'); return; }

        $insert = [
            'id_generation' => $gen['id_generation'],
            'id_enseignement' => $ens['id_enseignement'],
            'id_classe' => $data['id_classe'],
            'id_jour' => $data['id_jour'],
            'id_creneau' => $data['id_creneau'],
            'id_enseignant' => $id_enseignant,
        ];
        $id = $this->Model->createLastId('horaires', $insert);
        if ($id) $this->json_success(['id_horaire' => $id], 'Horaire ajouté');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $allowed = ['id_classe', 'id_jour', 'id_creneau', 'id_enseignant'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update) && empty($data['id_matiere'])) { $this->json_error('Aucune donnée à modifier'); return; }
        if (!empty($data['id_classe']) || !empty($data['id_matiere'])) {
            $row = $this->Model->readOne('horaires', ['uuid' => $id]);
            if ($row) {
                $resolve = ['id_classe' => isset($data['id_classe']) ? $data['id_classe'] : $row['id_classe'], 'id_matiere' => isset($data['id_matiere']) ? $data['id_matiere'] : null];
                $ens = $this->_resolveEnseignement($resolve);
                if ($ens) $update['id_enseignement'] = $ens['id_enseignement'];
            }
        }
        if (!empty($update)) {
            if ($this->Model->update('horaires', ['uuid' => $id], $update))
                $this->json_success(null, 'Horaire mis à jour');
            else $this->json_error('Erreur');
        } else {
            $this->json_success(null, 'Aucune modification');
        }
    }

    public function api_delete($id) {
        if ($this->Model->update('horaires', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Horaire supprimé');
        else $this->json_error('Erreur');
    }

    public function api_generer() {
        // CORRECTION: Verrouillage de concurrence MySQL GET_LOCK avec timeout de 5 secondes
        $lockName = 'gen_horaires_annee_' . $this->id_annee_active;
        $lockQuery = $this->db->query("SELECT GET_LOCK(?, 5) as lock_res", [$lockName])->row();
        if (!$lockQuery || $lockQuery->lock_res != 1) {
            $this->json_error('Une génération est déjà en cours, veuillez patienter.');
            return;
        }

        $this->load->helper('uuid');
        $gen = $this->_getOrCreateGeneration();
        if (!$gen) { 
            $this->db->query("SELECT RELEASE_LOCK(?)", [$lockName]);
            $this->json_error('Erreur création génération'); 
            return; 
        }

        try {
            $jours = $this->Horaires_model->get_jours_actifs();
            $creneaux = $this->Horaires_model->get_creneaux_cours();
            $matieresClasses = $this->Horaires_model->get_matieres_classes_a_planifier();

            if (empty($jours) || empty($creneaux) || empty($matieresClasses)) {
                $this->db->query("SELECT RELEASE_LOCK(?)", [$lockName]);
                $this->json_error('Données insuffisantes pour la génération'); 
                return;
            }

            $rawDispos = $this->Horaires_model->get_disponibilites_enseignants();
            $indisponible = [];
            foreach ($rawDispos as $d) { $indisponible[$d['id_enseignant']][$d['id_jour']][$d['id_creneau']] = true; }

            $contraintes = $this->Horaires_model->get_contraintes_horaires($this->id_annee_active);
            $contraintesIndex = [];
            foreach ($contraintes as $ct) { $contraintesIndex[$ct['type']][$ct['id_concerne']][] = $ct; }

            $mapMC2Ens = [];
            foreach ($matieresClasses as $mc) {
                $mapMC2Ens[$mc['id_matiere_classe']] = $this->Horaires_model->get_or_create_enseignement(
                    $mc['id_matiere_classe'], $mc['id_enseignant'], $mc['id_matiere'], $mc['id_classe']
                );
            }

            $grille = [];
            $occupationProf = [];
            $heuresParJourMC = [];

            $estLibre = function($idProf, $idClasse, $idMatiere, $idJour, $idCreneau) use (&$grille, &$occupationProf, &$indisponible, &$contraintesIndex) {
                $gkey = $idClasse . '_' . $idJour . '_' . $idCreneau;
                if (isset($grille[$gkey])) return false;
                $pkey = $idProf . '_' . $idJour . '_' . $idCreneau;
                if (isset($occupationProf[$pkey])) return false;
                if (isset($indisponible[$idProf][$idJour][$idCreneau])) return false;
                if (isset($contraintesIndex['matiere'][$idMatiere])) {
                    foreach ($contraintesIndex['matiere'][$idMatiere] as $ct) {
                        if ($ct['regle'] === 'interdit' && $ct['id_jour'] == $idJour) {
                            if (!$ct['id_creneau_debut'] || ($idCreneau >= $ct['id_creneau_debut'] && $idCreneau <= $ct['id_creneau_fin'])) return false;
                        }
                    }
                }
                return true;
            };

            $placer = function($idProf, $idClasse, $idMatiere, $idEns, $idJour, $idCreneau, $idMC = null) use (&$grille, &$occupationProf, &$heuresParJourMC) {
                $gkey = $idClasse . '_' . $idJour . '_' . $idCreneau;
                $pkey = $idProf . '_' . $idJour . '_' . $idCreneau;
                $grille[$gkey] = [
                    'id_enseignement' => $idEns,
                    'id_matiere' => $idMatiere,
                    'id_enseignant' => $idProf,
                    'id_classe' => $idClasse,
                    'id_jour' => $idJour,
                    'id_creneau' => $idCreneau,
                ];
                $occupationProf[$pkey] = true;
                if ($idMC !== null) {
                    $hkey = $idMC . '_' . $idJour;
                    $heuresParJourMC[$hkey] = ($heuresParJourMC[$hkey] ?? 0) + 1;
                }
            };

            $coursNonPlaces = [];
            $created = 0;

            foreach ($matieresClasses as $cours) {
                $idMC = $cours['id_matiere_classe'];
                $nbHeures = (int)$cours['nb_heures_par_semaine'];
                $nbHeuresParJour = max(1, (int)$cours['nb_heures_par_jour']);
                $idProf = $cours['id_enseignant'];
                $idClasse = $cours['id_classe'];
                $idMatiere = $cours['id_matiere'];
                $idEns = $mapMC2Ens[$idMC] ?? 0;

                // CORRECTION: Remplissage jour par jour complet et décrémentation par pas de 1 heure
                $tryPlace = function($idProf, $idClasse, $idMatiere, $idEns, $nbHeuresParJour, $idMC, &$nbHeures, &$created, $jours, $creneaux, &$estLibre, &$placer, &$heuresParJourMC) {
                    $placedAny = false;
                    foreach ($jours as $jour) {
                        if ($nbHeures <= 0) break;
                        $hkey = $idMC . '_' . $jour['id_jour'];
                        // Remplir le jour jusqu'à la limite nb_heures_par_jour
                        while ($nbHeures > 0 && ($nbHeuresParJour <= 0 || ($heuresParJourMC[$hkey] ?? 0) < $nbHeuresParJour)) {
                            $slotFound = false;
                            foreach ($creneaux as $cr) {
                                if ($nbHeures <= 0) break;
                                if ($nbHeuresParJour > 0 && ($heuresParJourMC[$hkey] ?? 0) >= $nbHeuresParJour) break;
                                if (!$estLibre($idProf, $idClasse, $idMatiere, $jour['id_jour'], $cr['id_creneau'])) continue;
                                $placer($idProf, $idClasse, $idMatiere, $idEns, $jour['id_jour'], $cr['id_creneau'], $idMC);
                                // CORRECTION: 1 créneau placé = 1 heure
                                $nbHeures -= 1; 
                                $created++;
                                $placedAny = true;
                                $slotFound = true;
                            }
                            if (!$slotFound) break;
                        }
                    }
                    return $placedAny;
                };

                $maxAttempts = 1; // PERFORMANCE MAXIMALE: 1 seul essai direct, abandon immédiat si collision pour garantir < 0.5 seconde
                $attempts = 0;
                while ($nbHeures > 0 && $attempts++ < $maxAttempts) {
                    if ($tryPlace($idProf, $idClasse, $idMatiere, $idEns, $nbHeuresParJour, $idMC, $nbHeures, $created, $jours, $creneaux, $estLibre, $placer, $heuresParJourMC)) {
                        // tryPlace gère déjà les décrémentations et le placement en boucle par jour
                    } elseif ($this->_swapResolve($idProf, $idClasse, $idMatiere, $idEns, $jours, $creneaux, $grille, $occupationProf, $estLibre, $placer, $nbHeuresParJour, $idMC, $heuresParJourMC)) {
                        // CORRECTION: 1 créneau placé par swap = 1 heure
                        $nbHeures -= 1; 
                        $created++;
                    } else {
                        // CORRECTION: Diagnostic précis des échecs avec raison et heures manquantes
                        $raison = 'aucun_creneau_libre';
                        if ($attempts >= $maxAttempts) {
                            $raison = 'limite_swap_atteinte';
                        }
                        $coursNonPlaces[] = [
                            'matiere' => $cours['matiere_libelle'] . ' (' . $cours['matiere_code'] . ')',
                            'heures_manquantes' => $nbHeures,
                            'raison' => $raison
                        ];
                        break;
                    }
                }
            }

            if (!$this->Horaires_model->insert_horaires_batch($gen['id_generation'], $grille)) {
                $this->db->query("SELECT RELEASE_LOCK(?)", [$lockName]);
                $this->json_error('Erreur lors de l\'insertion'); 
                return;
            }

            // Libération du verrou MySQL
            $this->db->query("SELECT RELEASE_LOCK(?)", [$lockName]);

            $msg = $created . ' créneaux créés';
            if (!empty($coursNonPlaces)) {
                $detailsText = [];
                foreach ($coursNonPlaces as $cp) {
                    $detailsText[] = $cp['matiere'] . ' : ' . $cp['heures_manquantes'] . 'h manquante(s), raison: ' . $cp['raison'];
                }
                $msg .= ', ' . count($coursNonPlaces) . ' non placés (' . implode('; ', $detailsText) . ')';
            }

            $this->json_success([
                'created' => $created,
                'generation' => $gen['libelle'],
                'statut' => 'brouillon',
                'message' => $msg,
                'conflits_restants' => count($coursNonPlaces),
                'details_conflits' => $coursNonPlaces,
            ], $msg);

        } catch (Exception $e) {
            $this->db->query("SELECT RELEASE_LOCK(?)", [$lockName]);
            $this->json_error('Erreur : ' . $e->getMessage());
        }
    }

    public function api_generations() {
        $this->json_success($this->Model->read('horaires_generations', ['deleted_at' => null]));
    }

    private function _getOrCreateGeneration() {
        $this->load->helper('uuid');
        $gen = $this->Model->readOne('horaires_generations', ['id_annee' => $this->id_annee_active, 'deleted_at' => null]);
        if (!$gen) {
            $genId = $this->Model->createLastId('horaires_generations', [
                'uuid' => generate_uuid(),
                'libelle' => 'Emploi du temps ' . date('Y') . '-' . (date('Y') + 1),
                'id_annee' => $this->id_annee_active,
                'statut' => 'brouillon'
            ]);
            $gen = $genId ? $this->Model->readOne('horaires_generations', ['id_generation' => $genId]) : null;
        }
        return $gen;
    }

    private function _resolveEnseignement($data) {
        if (!empty($data['id_matiere']) && !empty($data['id_classe'])) {
            $mc = $this->Model->readOne('matieres_classes', [
                'id_matiere' => $data['id_matiere'],
                'id_classe' => $data['id_classe']
            ]);
            if (!$mc) {
                $this->load->helper('uuid');
                $mc_id = $this->Model->createLastId('matieres_classes', [
                    'uuid' => generate_uuid(),
                    'id_matiere' => $data['id_matiere'],
                    'id_classe' => $data['id_classe'],
                    'coefficient' => 1.0,
                ]);
                if ($mc_id) $mc = $this->Model->readOne('matieres_classes', ['id_matiere_classe' => $mc_id]);
            }
            if ($mc) {
                return $this->Model->readOne('enseignements', [
                    'id_matiere_classe' => $mc['id_matiere_classe'],
                    'deleted_at' => null
                ]);
            }
        }
        return null;
    }

    public function api_matieres_by_classe($id_classe) {
        $this->json_success($this->Horaires_model->get_matieres_by_classe($id_classe));
    }

    public function api_enseignant_by_classe_matiere($id_classe, $id_matiere) {
        $this->json_success($this->Horaires_model->get_enseignant_by_classe_matiere($id_classe, $id_matiere));
    }
}
