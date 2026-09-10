<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HorairesGenerator {

    protected $CI;

    public function __construct() {
        if (function_exists('get_instance')) {
            $this->CI =& get_instance();
        }
    }

    // ══════════════════════════════════════════════════════════════════════
    // PREFLIGHT — Validation structurelle avant génération
    // ══════════════════════════════════════════════════════════════════════

    public function preflight($data) {
        $jours = $data['jours'] ?? [];
        $creneaux = $data['creneaux'] ?? [];
        $indispos = $data['indisponibilites'] ?? [];
        $mcRows = $data['matieres_classes'] ?? [];
        $enseignements = $data['enseignements'] ?? [];
        $fixes = $data['fixes'] ?? [];
        $diagnostics = [];

        $teachingByMc = [];
        foreach ($enseignements as $ens) $teachingByMc[(int)$ens['id_matiere_classe']] = $ens;

        $mcById = [];
        $teacherLoad = [];
        $teacherMcDetails = [];

        // 1. Parcourir chaque cours (matiere_classe)
        foreach ($mcRows as $mc) {
            $mcId = (int)$mc['id_matiere_classe'];
            $weekly = (int)$mc['nb_heures_par_semaine'];
            if ($weekly <= 0) continue;
            $mcById[$mcId] = $mc;
            $maxDaily = (int)($mc['nb_heures_par_jour'] ?? 1);
            $ens = $teachingByMc[$mcId] ?? null;

            if (!$ens) {
                $diagnostics[] = ['type' => 'affectation_manquante', 'blocking' => true, 'message' => "Aucun enseignant n'est affecté au cours #$mcId."];
                continue;
            }
            if ($maxDaily < 1) {
                $diagnostics[] = ['type' => 'limite_quotidienne_invalide', 'blocking' => true, 'message' => "Le cours #$mcId doit avoir au moins 1 heure/jour."];
                continue;
            }

            $idProf = (int)$ens['id_enseignant'];
            $teacherLoad[$idProf] = ($teacherLoad[$idProf] ?? 0) + $weekly;
            $teacherMcDetails[$idProf][] = [
                'mcId' => $mcId,
                'weekly' => $weekly,
                'maxDaily' => $maxDaily,
                'className' => $mc['classe_libelle'] ?? "Classe#{$mc['id_classe']}",
                'matiereName' => $mc['matiere_libelle'] ?? "Matiere#{$mcId}",
            ];

            $requiredDays = (int)ceil($weekly / $maxDaily);
            $daysWithSlots = $this->countDaysWithSlots($idProf, $jours, $creneaux, $indispos);
            if ($daysWithSlots < $requiredDays) {
                $diagnostics[] = ['type' => 'jours_insuffisants', 'blocking' => true, 'message' => "Cours #$mcId ($weeklyh, max {$maxDaily}h/jour) exige $requiredDays jour(s) mais $daysWithSlots jour(s) disponible(s) pour l'enseignant #$idProf."];
            }
        }

        // 2. Par enseignant : vérifier capacité totale + marge
        foreach ($teacherLoad as $idProf => $load) {
            $capacity = $this->countTeacherCapacity($idProf, $jours, $creneaux, $indispos);
            if ($load > $capacity) {
                $diagnostics[] = ['type' => 'capacite_enseignant', 'blocking' => true, 'message' => "Enseignant #$idProf : $load heure(s) demandées mais seulement $capacity créneau(x) autorisé(s). IMPOSSIBLE."];
            }
            if ($load === $capacity && $capacity > 0) {
                $diagnostics[] = ['type' => 'marge_zero', 'blocking' => false, 'message' => "Enseignant #$idProf : MARGE ZÉRO — $load sessions = $capacity créneaux autorisés. Pré-placement automatique."];
            }
        }

        // 3. Vérifier chaque créneau fixe
        $fixedClass = $fixedProf = $fixedCourseDay = [];
        foreach ($fixes as $fixe) {
            $idProf = (int)$fixe['id_enseignant'];
            $jour = (int)$fixe['id_jour'];
            $cr = (int)$fixe['id_creneau'];
            $classe = (int)$fixe['id_classe'];
            $mcId = (int)$fixe['id_matiere_classe'];

            if (isset($indispos[$idProf][$jour][$cr])) {
                $diagnostics[] = ['type' => 'fixe_indisponible', 'blocking' => true, 'message' => "Créneau fixe : l'enseignant #$idProf est indisponible le jour $jour créneau $cr."];
            }
            $classKey = $classe . '_' . $jour . '_' . $cr;
            $profKey = $idProf . '_' . $jour . '_' . $cr;
            if (isset($fixedClass[$classKey]) || isset($fixedProf[$profKey])) {
                $diagnostics[] = ['type' => 'conflit_fixe', 'blocking' => true, 'message' => "Conflit de créneau fixe : classe ou enseignant déjà occupé jour $jour créneau $cr."];
            }
            $fixedClass[$classKey] = true;
            $fixedProf[$profKey] = true;

            $courseDayKey = $mcId . '_' . $jour;
            $fixedCourseDay[$courseDayKey] = ($fixedCourseDay[$courseDayKey] ?? 0) + 1;
            $mc = $mcById[$mcId] ?? null;
            if ($mc) {
                $maxD = (int)($mc['nb_heures_par_jour'] ?? 1);
                if ($fixedCourseDay[$courseDayKey] > $maxD) {
                    $diagnostics[] = ['type' => 'fixe_depasse_limite', 'blocking' => true, 'message' => "Cours #$mcId : $fixedCourseDay[$courseDayKey] fixe(s) le jour $jour, limite=$maxD."];
                }
                if ((int)$mc['id_classe'] !== $classe) {
                    $diagnostics[] = ['type' => 'fixe_classe_invalide', 'blocking' => true, 'message' => "Créneau fixe : cours #$mcId n'appartient pas à la classe #$classe."];
                }
            }
        }

        $hasBlocking = false;
        foreach ($diagnostics as $d) {
            if (!empty($d['blocking'])) { $hasBlocking = true; break; }
        }
        return ['success' => !$hasBlocking, 'diagnostics' => $diagnostics];
    }

    // ══════════════════════════════════════════════════════════════════════
    // HELPERS — Calculs de capacité
    // ══════════════════════════════════════════════════════════════════════

    private function countTeacherCapacity($idProf, $jours, $creneaux, $indispos) {
        $capacity = 0;
        foreach ($jours as $jour) {
            foreach ($creneaux as $creneau) {
                if (!isset($indispos[$idProf][(int)$jour['id_jour']][(int)$creneau['id_creneau']])) {
                    $capacity++;
                }
            }
        }
        return $capacity;
    }

    private function countDaysWithSlots($idProf, $jours, $creneaux, $indispos) {
        $days = 0;
        foreach ($jours as $jour) {
            foreach ($creneaux as $creneau) {
                if (!isset($indispos[$idProf][(int)$jour['id_jour']][(int)$creneau['id_creneau']])) {
                    $days++;
                    break;
                }
            }
        }
        return $days;
    }

    // ══════════════════════════════════════════════════════════════════════
    // KEYS — Clés de grille
    // ══════════════════════════════════════════════════════════════════════

    public function makeGridKey($classe, $jour, $creneau) {
        return (int)$classe . '_' . (int)$jour . '_' . (string)$creneau;
    }

    public function makeProfKey($prof, $jour, $creneau) {
        return (int)$prof . '_' . (int)$jour . '_' . (string)$creneau;
    }

    public function makeDailyKey($matiereClasse, $jour) {
        return (int)$matiereClasse . '_' . (int)$jour;
    }

    // ══════════════════════════════════════════════════════════════════════
    // PLACEMENT — Place / Remove / Validate
    // ══════════════════════════════════════════════════════════════════════

    public function isPlacementValid(&$state, $session, $jour, $creneau) {
        $idProf = (int)$session['id_enseignant'];
        $idMatiereClasse = (int)$session['id_matiere_classe'];
        $maxJour = (int)($session['nb_heures_par_jour'] ?? 1);
        $idClasse = (int)$session['id_classe'];

        $gkey = $this->makeGridKey($idClasse, $jour, $creneau);
        $pkey = $this->makeProfKey($idProf, $jour, $creneau);
        $jkey = $this->makeDailyKey($idMatiereClasse, $jour);

        if (isset($state['grid'][$gkey])) return false;
        if (isset($state['occupationProf'][$pkey])) return false;
        if (isset($state['indisponibilites'][$idProf][$jour][$creneau])) return false;
        if (($state['chargeMatiereJour'][$jkey] ?? 0) + 1 > $maxJour) return false;
        if (isset($state['creneauxExclus'][$jour][$creneau])) return false;

        return true;
    }

    public function placeSession(&$state, &$session, $jour, $creneau) {
        if (!$this->isPlacementValid($state, $session, $jour, $creneau)) return false;

        $idClasse = (int)$session['id_classe'];
        $idProf = (int)$session['id_enseignant'];
        $idMatiereClasse = (int)$session['id_matiere_classe'];

        $sessionPlaced = $session;
        $sessionPlaced['id_jour'] = (int)$jour;
        $sessionPlaced['id_creneau'] = $creneau;

        $state['grid'][$this->makeGridKey($idClasse, $jour, $creneau)] = $sessionPlaced;
        $state['occupationProf'][$this->makeProfKey($idProf, $jour, $creneau)] = true;
        $jkey = $this->makeDailyKey($idMatiereClasse, $jour);
        $state['chargeMatiereJour'][$jkey] = ($state['chargeMatiereJour'][$jkey] ?? 0) + 1;
        $state['sessionsPlaced'][] = $sessionPlaced;
        return true;
    }

    private function removeSession(&$state, $sp) {
        if (!isset($sp['id_jour']) || !isset($sp['id_creneau'])) return;
        $idClasse = (int)$sp['id_classe'];
        $idProf = (int)$sp['id_enseignant'];
        $idMatiereClasse = (int)$sp['id_matiere_classe'];
        $jour = (int)$sp['id_jour'];
        $creneau = $sp['id_creneau'];

        unset($state['grid'][$this->makeGridKey($idClasse, $jour, $creneau)]);
        unset($state['occupationProf'][$this->makeProfKey($idProf, $jour, $creneau)]);
        $jkey = $this->makeDailyKey($idMatiereClasse, $jour);
        if (isset($state['chargeMatiereJour'][$jkey]) && $state['chargeMatiereJour'][$jkey] > 0) {
            $state['chargeMatiereJour'][$jkey]--;
        }
        foreach ($state['sessionsPlaced'] as $idx => $s) {
            if ((int)$s['session_id'] === (int)$sp['session_id']) {
                unset($state['sessionsPlaced'][$idx]);
                $state['sessionsPlaced'] = array_values($state['sessionsPlaced']);
                break;
            }
        }
    }

    // ══════════════════════════════════════════════════════════════════════
    // GENERATION — Point d'entrée
    // ══════════════════════════════════════════════════════════════════════

    public function generate($data) {
        $bestOverall = null;
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $result = $this->generateOnce($data);
            if ($result['success']) return $result;
            if (!$bestOverall || $result['validation']['placed'] > $bestOverall['validation']['placed']) {
                $bestOverall = $result;
            }
        }
        return $bestOverall ?? $this->generateOnce($data);
    }

    public function generateOnce($data) {
        $matieresClasses = $data['matieres_classes'] ?? [];
        $enseignements = $data['enseignements'] ?? [];
        $jours = $data['jours'] ?? [];
        $creneaux = $data['creneaux'] ?? [];
        $indisponibilites = $data['indisponibilites'] ?? [];
        $creneauxExclus = $data['creneaux_exclus'] ?? [];
        $fixesData = $data['fixes'] ?? [];

        // Map MC → Enseignement
        $mapMC2Ens = [];
        foreach ($enseignements as $ens) {
            $mapMC2Ens[(int)$ens['id_matiere_classe']] = [
                'id_enseignement' => (int)$ens['id_enseignement'],
                'id_enseignant' => (int)$ens['id_enseignant']
            ];
        }

        // Construire les sessions (1 session = 1 heure/semaine)
        $sessions = [];
        $matieresClassesById = [];
        $sessionId = 0;
        foreach ($matieresClasses as $mc) {
            $nbSemaine = (int)$mc['nb_heures_par_semaine'];
            if ($nbSemaine <= 0) continue;
            $mcId = (int)$mc['id_matiere_classe'];
            $matieresClassesById[$mcId] = $mc;
            $ensData = $mapMC2Ens[$mcId] ?? null;
            if (!$ensData) continue;
            for ($i = 0; $i < $nbSemaine; $i++) {
                $sessions[] = [
                    'session_id' => $sessionId++,
                    'id_matiere_classe' => $mcId,
                    'id_matiere' => (int)$mc['id_matiere'],
                    'id_enseignement' => $ensData['id_enseignement'],
                    'id_classe' => (int)$mc['id_classe'],
                    'id_enseignant' => $ensData['id_enseignant'],
                    'nb_heures_par_jour' => (int)($mc['nb_heures_par_jour'] ?? 2),
                    'matiere_code' => $mc['matiere_code'] ?? 'M',
                    'matiere_libelle' => $mc['matiere_libelle'] ?? '',
                    'type' => 'auto',
                ];
            }
        }

        $jourIds = array_column($jours, 'id_jour');
        $creneauIds = array_column($creneaux, 'id_creneau');

        // Construire les sessions fixes
        $fixeSessions = [];
        $fixeCounts = [];
        foreach ($fixesData as $f) {
            $mcId = (int)$f['id_matiere_classe'];
            $ensData = $mapMC2Ens[$mcId] ?? null;
            $fixeSessions[] = [
                'session_id' => $sessionId++,
                'id_matiere_classe' => $mcId,
                'id_matiere' => (int)$f['id_matiere'],
                'id_enseignement' => $ensData ? $ensData['id_enseignement'] : 0,
                'id_classe' => (int)$f['id_classe'],
                'id_enseignant' => (int)$f['id_enseignant'],
                'nb_heures_par_jour' => (int)($matieresClassesById[$mcId]['nb_heures_par_jour'] ?? 1),
                'matiere_code' => '',
                'matiere_libelle' => '',
                'type' => 'fixe',
                'id_jour' => (int)$f['id_jour'],
                'id_creneau' => (int)$f['id_creneau'],
            ];
            $fkey = (int)$f['id_classe'] . '_' . $mcId;
            $fixeCounts[$fkey] = ($fixeCounts[$fkey] ?? 0) + 1;
        }

        // Retirer les sessions auto correspondant aux fixes
        $filteredSessions = [];
        $removedCounts = [];
        foreach ($sessions as $s) {
            $fkey = (int)$s['id_classe'] . '_' . (int)$s['id_matiere_classe'];
            $maxRemove = $fixeCounts[$fkey] ?? 0;
            if ($maxRemove > 0 && ($removedCounts[$fkey] ?? 0) < $maxRemove) {
                $removedCounts[$fkey] = ($removedCounts[$fkey] ?? 0) + 1;
                continue;
            }
            $filteredSessions[] = $s;
        }
        $sessions = $filteredSessions;

        $totalAutoSessions = count($sessions);
        $totalSessions = $totalAutoSessions + count($fixeSessions);

        // Pré-placer les enseignants tight (marge = 0 ou quasi-nulle)
        $tightResult = $this->prePlaceTightTeachers($sessions, $jourIds, $creneauIds, $indisponibilites, $creneauxExclus, $sessionId);
        $sessions = $tightResult[0];
        $sessionId = $tightResult[1];
        $tightFixe = $tightResult[2] ?? [];
        if (!empty($tightFixe)) {
            $fixeSessions = array_merge($fixeSessions, $tightFixe);
        }

        // Grouper les sessions restantes par (classe, matière)
        $groups = [];
        foreach ($sessions as $s) {
            $key = (int)$s['id_classe'] . '_' . (int)$s['id_matiere_classe'];
            $groups[$key][] = $s;
        }

        $bestState = null;
        $bestPlaced = 0;

        // Essayer plusieurs ordonnancements
        $groupOrders = [
            $this->orderGroupsMostSessionsFirst($groups),
            $this->orderGroupsMostConstrainedFirst($groups, $jourIds, $creneauIds, $indisponibilites, $creneauxExclus),
            $this->orderGroupsByTeacherLoad($groups, $jourIds, $creneauIds, $indisponibilites, $creneauxExclus),
            $groups,
        ];

        foreach ($groupOrders as $orderedGroups) {
            $state = $this->tryGenerateByGroups($orderedGroups, $jourIds, $creneauIds, $indisponibilites, $creneauxExclus, $fixeSessions);
            $placed = count($state['sessionsPlaced']);
            if ($placed > $bestPlaced) {
                $bestPlaced = $placed;
                $bestState = $state;
            }
            if ($placed === $totalSessions) break;
        }

        // Tentatives aléatoires
        if ($bestPlaced < $totalSessions) {
            for ($attempt = 0; $attempt < 50 && $bestPlaced < $totalSessions; $attempt++) {
                $shuffled = $groups;
                shuffle($shuffled);
                $state = $this->tryGenerateByGroups($shuffled, $jourIds, $creneauIds, $indisponibilites, $creneauxExclus, $fixeSessions);
                $placed = count($state['sessionsPlaced']);
                if ($placed > $bestPlaced) { $bestPlaced = $placed; $bestState = $state; }
                if ($placed === $totalSessions) break;
            }
        }

        // Rattrapage par swap
        if ($bestPlaced < $totalSessions) {
            $allSessionsList = array_merge($sessions, $fixeSessions);
            for ($rescuePass = 0; $rescuePass < 20; $rescuePass++) {
                $placedIds = [];
                foreach ($bestState['sessionsPlaced'] as $sp) $placedIds[(int)$sp['session_id']] = true;
                $stillUnplaced = [];
                foreach ($allSessionsList as $es) {
                    if (!isset($placedIds[(int)$es['session_id']])) $stillUnplaced[] = $es;
                }
                if (empty($stillUnplaced)) break;
                foreach ($stillUnplaced as $session) {
                    $this->tryPlaceWithSwap($bestState, $session, $jourIds, $creneauIds);
                }
            }

            // Brute force dernier recours
            $placedIds = [];
            foreach ($bestState['sessionsPlaced'] as $sp) $placedIds[(int)$sp['session_id']] = true;
            $finalUnplaced = [];
            foreach ($allSessionsList as $es) {
                if (!isset($placedIds[(int)$es['session_id']])) $finalUnplaced[] = $es;
            }
            for ($bfRound = 0; $bfRound < 5 && !empty($finalUnplaced); $bfRound++) {
                $still = [];
                foreach ($finalUnplaced as $session) {
                    if (!$this->bruteForcePlace($bestState, $session, $jourIds, $creneauIds, $indisponibilites, $creneauxExclus, 0, 6)) {
                        $still[] = $session;
                    }
                }
                $finalUnplaced = $still;
            }
        }

        // Optimisation consécutive
        $this->optimizeConsecutive($bestState, $jourIds, $creneauIds);

        // Log unplaced
        $allPlacedForValidation = array_merge($sessions, $fixeSessions);
        if (count($bestState['sessionsPlaced']) !== count($allPlacedForValidation)) {
            $placedIds = [];
            foreach ($bestState['sessionsPlaced'] as $sp) $placedIds[(int)$sp['session_id']] = true;
            $unplaced = [];
            foreach ($allPlacedForValidation as $es) {
                if (!isset($placedIds[(int)$es['session_id']])) $unplaced[] = $es;
            }
            log_message('error', 'UNPLACED: ' . json_encode($unplaced));
        }

        // Validation finale complète
        $validation = $this->validateCompleteSchedule($bestState, $allPlacedForValidation);

        return [
            'success' => $validation['success'],
            'validation' => $validation,
            'grid' => $bestState['grid'],
            'sessionsPlaced' => $bestState['sessionsPlaced']
        ];
    }

    // ══════════════════════════════════════════════════════════════════════
    // PRE-PLACEMENT — Enseignants tight (marge = 0)
    // ══════════════════════════════════════════════════════════════════════

    public function prePlaceTightTeachers($sessions, $jourIds, $creneauIds, $indisponibilites, $creneauxExclus, $sessionIdCounter) {
        // 1. Comptage par enseignant
        $teacherSessions = [];
        foreach ($sessions as $s) {
            $teacherSessions[(int)$s['id_enseignant']][] = $s;
        }

        $teacherSlots = [];
        foreach ($teacherSessions as $idProf => $tsList) {
            $slots = 0;
            foreach ($jourIds as $j) {
                foreach ($creneauIds as $c) {
                    if (!isset($indisponibilites[$idProf][$j][$c]) && !isset($creneauxExclus[$j][$c])) $slots++;
                }
            }
            $teacherSlots[$idProf] = $slots;
        }

        // 2. Détecter les tight teachers (marge <= 1)
        $tightTeachers = [];
        foreach ($teacherSessions as $idProf => $tsList) {
            $total = count($tsList);
            $available = $teacherSlots[$idProf];
            if ($available > 0 && $total >= $available - 1) {
                $tightTeachers[$idProf] = $tsList;
            }
        }
        if (empty($tightTeachers)) return [$sessions, $sessionIdCounter, []];

        // 3. Trier par marge croissante (0 d'abord)
        $tightWithMarge = [];
        foreach ($tightTeachers as $idProf => $tsList) {
            $tightWithMarge[$idProf] = [
                'sessions' => $tsList,
                'marge' => $teacherSlots[$idProf] - count($tsList),
            ];
        }
        uasort($tightWithMarge, function ($a, $b) { return $a['marge'] - $b['marge']; });

        // 4. Placer chaque enseignant tight
        $prePlaced = [];
        $remainingSessions = $sessions;
        $usedGrid = [];
        $usedProf = [];
        $usedClassSlot = [];

        foreach ($tightWithMarge as $idProf => $info) {
            $tsList = $info['sessions'];
            $availableSlots = [];
            foreach ($jourIds as $j) {
                foreach ($creneauIds as $c) {
                    if (!isset($indisponibilites[$idProf][$j][$c]) && !isset($creneauxExclus[$j][$c])) {
                        $availableSlots[] = ['jour' => (int)$j, 'creneau' => $c];
                    }
                }
            }

            // Grouper par matière/classe
            $mcSessions = [];
            foreach ($tsList as $s) {
                $mcSessions[(int)$s['id_matiere_classe']][] = $s;
            }

            $slotsByJour = [];
            foreach ($availableSlots as $slot) {
                $slotsByJour[$slot['jour']][] = $slot['creneau'];
            }
            foreach ($slotsByJour as $j => $cs) sort($slotsByJour[$j]);

            // Trier les jours par nombre de slots disponibles (plus d'abord)
            $daysSorted = $jourIds;
            $jourSlotCount = [];
            foreach ($jourIds as $j) $jourSlotCount[$j] = count($slotsByJour[$j] ?? []);
            usort($daysSorted, function ($a, $b) use ($jourSlotCount) {
                return ($jourSlotCount[$b] ?? 0) - ($jourSlotCount[$a] ?? 0);
            });

            $jourCounts = [];
            foreach ($jourIds as $j) $jourCounts[$j] = 0;
            $assignments = [];

            foreach ($mcSessions as $mcId => $mcList) {
                $nbTotal = count($mcList);
                $maxPerDay = (int)($mcList[0]['nb_heures_par_jour'] ?? 3);
                $perDay = [];
                $remaining = $nbTotal;
                foreach ($daysSorted as $j) {
                    $slotsAvail = count($slotsByJour[$j] ?? []);
                    $canPut = min($maxPerDay, $remaining, $slotsAvail);
                    $perDay[$j] = $canPut;
                    $remaining -= $canPut;
                }

                foreach ($daysSorted as $j) {
                    if (($perDay[$j] ?? 0) <= 0) continue;
                    $availableForDay = $slotsByJour[$j] ?? [];
                    $usedOnDay = $jourCounts[$j] ?? 0;
                    $freeSlots = array_slice($availableForDay, $usedOnDay);
                    $toPlace = min($perDay[$j], count($freeSlots));
                    for ($i = 0; $i < $toPlace; $i++) {
                        $s = array_shift($mcList);
                        if (!$s) break;
                        $gk = $this->makeGridKey((int)$s['id_classe'], $j, $freeSlots[$i]);
                        $pk = $this->makeProfKey($idProf, $j, $freeSlots[$i]);
                        $ck = (int)$s['id_classe'] . '_' . $j . '_' . $freeSlots[$i];
                        if (!isset($usedGrid[$gk]) && !isset($usedProf[$pk]) && !isset($usedClassSlot[$ck])) {
                            $assignments[] = ['session' => $s, 'jour' => $j, 'creneau' => $freeSlots[$i]];
                            $usedGrid[$gk] = true;
                            $usedProf[$pk] = true;
                            $usedClassSlot[$ck] = true;
                        }
                    }
                    $jourCounts[$j] = ($jourCounts[$j] ?? 0) + $toPlace;
                }

                // Sessions restantes non placées
                while (!empty($mcList)) {
                    $s = array_shift($mcList);
                    $placed = false;
                    foreach ($availableSlots as $slot) {
                        $j = $slot['jour'];
                        $c = $slot['creneau'];
                        $gk = $this->makeGridKey((int)$s['id_classe'], $j, $c);
                        $pk = $this->makeProfKey($idProf, $j, $c);
                        $ck = (int)$s['id_classe'] . '_' . $j . '_' . $c;
                        if (!isset($usedGrid[$gk]) && !isset($usedProf[$pk]) && !isset($usedClassSlot[$ck])) {
                            $assignments[] = ['session' => $s, 'jour' => $j, 'creneau' => $c];
                            $usedGrid[$gk] = true;
                            $usedProf[$pk] = true;
                            $usedClassSlot[$ck] = true;
                            $jourCounts[$j] = ($jourCounts[$j] ?? 0) + 1;
                            $placed = true;
                            break;
                        }
                    }
                    if (!$placed) {
                        foreach ($jourIds as $j) {
                            foreach ($creneauIds as $c) {
                                $gk = $this->makeGridKey((int)$s['id_classe'], $j, $c);
                                $pk = $this->makeProfKey($idProf, $j, $c);
                                $ck = (int)$s['id_classe'] . '_' . $j . '_' . $c;
                                if (!isset($indisponibilites[$idProf][$j][$c]) && !isset($creneauxExclus[$j][$c])
                                    && !isset($usedGrid[$gk]) && !isset($usedProf[$pk]) && !isset($usedClassSlot[$ck])) {
                                    $assignments[] = ['session' => $s, 'jour' => $j, 'creneau' => $c];
                                    $usedGrid[$gk] = true;
                                    $usedProf[$pk] = true;
                                    $usedClassSlot[$ck] = true;
                                    $jourCounts[$j] = ($jourCounts[$j] ?? 0) + 1;
                                    $placed = true;
                                    break 2;
                                }
                            }
                        }
                    }
                    if (!$placed) break;
                }
            }

            // Convertir en fixe et retirer du pool
            foreach ($assignments as $a) {
                $s = $a['session'];
                $fixeSession = $s;
                $fixeSession['session_id'] = $sessionIdCounter++;
                $fixeSession['id_jour'] = $a['jour'];
                $fixeSession['id_creneau'] = $a['creneau'];
                $fixeSession['type'] = 'fixe';
                $prePlaced[] = $fixeSession;
                $remainingSessions = array_values(array_filter($remainingSessions, function ($r) use ($s) {
                    return (int)$r['session_id'] !== (int)$s['session_id'];
                }));
            }
        }

        return [$remainingSessions, $sessionIdCounter, $prePlaced];
    }

    // ══════════════════════════════════════════════════════════════════════
    // PLACEMENT PAR GROUPES
    // ══════════════════════════════════════════════════════════════════════

    private function tryGenerateByGroups($groups, $jourIds, $creneauIds, $indisponibilites, $creneauxExclus, $fixeSessions = []) {
        $state = [
            'grid' => [],
            'occupationProf' => [],
            'chargeMatiereJour' => [],
            'sessionsPlaced' => [],
            'indisponibilites' => $indisponibilites,
            'creneauxExclus' => $creneauxExclus,
            'fixeKeys' => [],
        ];

        // 1. Placer les fixes d'abord
        foreach ($fixeSessions as $fs) {
            $gkey = $this->makeGridKey((int)$fs['id_classe'], (int)$fs['id_jour'], (int)$fs['id_creneau']);
            if ($this->placeSession($state, $fs, (int)$fs['id_jour'], (int)$fs['id_creneau'])) {
                $state['fixeKeys'][$gkey] = true;
            }
        }

        // 2. Placer les groupes consécutivement
        foreach ($groups as $group) {
            $this->placeGroupConsecutive($state, $group, $jourIds, $creneauIds);
        }

        // 3. Rattrapage swap
        $allSessions = [];
        foreach ($groups as $g) foreach ($g as $s) $allSessions[] = $s;

        for ($pass = 0; $pass < 10; $pass++) {
            $currentUnplaced = [];
            foreach ($allSessions as $session) {
                $found = false;
                foreach ($state['sessionsPlaced'] as $sp) {
                    if ((int)$sp['session_id'] === (int)$session['session_id']) { $found = true; break; }
                }
                if (!$found) $currentUnplaced[] = $session;
            }
            if (empty($currentUnplaced)) break;
            foreach ($currentUnplaced as $session) {
                $this->tryPlaceWithSwap($state, $session, $jourIds, $creneauIds);
            }
        }

        // 4. Dernière chance : placement libre
        foreach ($allSessions as $session) {
            $found = false;
            foreach ($state['sessionsPlaced'] as $sp) {
                if ((int)$sp['session_id'] === (int)$session['session_id']) { $found = true; break; }
            }
            if (!$found) {
                foreach ($jourIds as $jour) {
                    foreach ($creneauIds as $creneau) {
                        if ($this->placeSession($state, $session, $jour, $creneau)) break 2;
                    }
                }
            }
        }

        return $state;
    }

    private function placeGroupConsecutive(&$state, $group, $jourIds, $creneauIds) {
        $maxPerDay = (int)($group[0]['nb_heures_par_jour'] ?? 1);
        $remaining = $group;

        foreach ($jourIds as $jour) {
            if (empty($remaining)) break;

            $canPlaceToday = 0;
            foreach ($creneauIds as $cr) {
                if ($this->isPlacementValid($state, $remaining[0], $jour, $cr)) {
                    $canPlaceToday++;
                }
            }
            if ($canPlaceToday === 0) continue;

            $candidates = [];
            foreach ($creneauIds as $cr) {
                if ($this->isPlacementValid($state, $remaining[0], $jour, $cr)) {
                    $candidates[] = $cr;
                }
            }

            $bestRun = $this->longestConsecutiveRun($candidates);
            $slotsToUse = min(count($bestRun), count($remaining), $maxPerDay);

            for ($i = 0; $i < $slotsToUse; $i++) {
                $this->placeSession($state, $remaining[0], $jour, $bestRun[$i]);
                array_shift($remaining);
                if (empty($remaining)) break;
            }
        }

        foreach ($remaining as $session) {
            foreach ($jourIds as $jour) {
                foreach ($creneauIds as $creneau) {
                    if ($this->placeSession($state, $session, $jour, $creneau)) break 2;
                }
            }
        }
    }

    private function longestConsecutiveRun($candidates) {
        if (empty($candidates)) return [];
        $best = [];
        $current = [$candidates[0]];
        for ($i = 1; $i < count($candidates); $i++) {
            if ((int)$candidates[$i] === (int)$candidates[$i-1] + 1) {
                $current[] = $candidates[$i];
            } else {
                if (count($current) > count($best)) $best = $current;
                $current = [$candidates[$i]];
            }
        }
        if (count($current) > count($best)) $best = $current;
        return $best;
    }

    // ══════════════════════════════════════════════════════════════════════
    // SWAP — Déplacement inter-classes
    // ══════════════════════════════════════════════════════════════════════

    private function tryPlaceWithSwap(&$state, $targetSession, $jourIds, $creneauIds) {
        $tIdClasse = (int)$targetSession['id_classe'];
        $tIdProf = (int)$targetSession['id_enseignant'];

        foreach ($jourIds as $tJour) {
            foreach ($creneauIds as $tCreneau) {
                if (isset($state['indisponibilites'][$tIdProf][$tJour][$tCreneau])) continue;
                if (isset($state['creneauxExclus'][$tJour][$tCreneau])) continue;

                $tKeyG = $this->makeGridKey($tIdClasse, $tJour, $tCreneau);
                $cellFree = !isset($state['grid'][$tKeyG]);
                $profFree = !isset($state['occupationProf'][$this->makeProfKey($tIdProf, $tJour, $tCreneau)]);

                if ($cellFree && $profFree) {
                    $this->placeSession($state, $targetSession, $tJour, $tCreneau);
                    return true;
                }
                if (!$cellFree && $profFree) {
                    $existing = $state['grid'][$tKeyG];
                    if ((int)$existing['id_classe'] === $tIdClasse) continue;
                    if ($this->doSwap($state, $existing, $targetSession, $tJour, $tCreneau, $jourIds, $creneauIds)) return true;
                }
                if (!$profFree) {
                    foreach ($state['sessionsPlaced'] as $sp) {
                        if ((int)$sp['id_enseignant'] === $tIdProf && (int)$sp['id_jour'] === (int)$tJour && (string)$sp['id_creneau'] === (string)$tCreneau) {
                            if ($this->doSwap($state, $sp, $targetSession, $tJour, $tCreneau, $jourIds, $creneauIds)) return true;
                            break;
                        }
                    }
                }
            }
        }
        return false;
    }

    private function doSwap(&$state, $existing, $target, $tJour, $tCreneau, $jourIds, $creneauIds, $depth = 0) {
        if ($depth >= 3) return false;
        if (!isset($existing['id_jour']) || !isset($existing['id_creneau'])) return false;
        $existingKey = $this->makeGridKey((int)$existing['id_classe'], (int)$existing['id_jour'], (int)$existing['id_creneau']);
        if (isset($state['fixeKeys'][$existingKey])) return false;

        $oldJour = (int)$existing['id_jour'];
        $oldCreneau = $existing['id_creneau'];

        foreach ($jourIds as $sJour) {
            foreach ($creneauIds as $sCreneau) {
                if ($sJour === $tJour && $sCreneau === $tCreneau) continue;
                $sIdClasse = (int)$existing['id_classe'];
                $sIdProf = (int)$existing['id_enseignant'];
                if (isset($state['indisponibilites'][$sIdProf][$sJour][$sCreneau])) continue;
                if (isset($state['creneauxExclus'][$sJour][$sCreneau])) continue;
                $sMcKey = $this->makeDailyKey((int)$existing['id_matiere_classe'], $sJour);
                $sMax = (int)($existing['nb_heures_par_jour'] ?? 1);
                if (($state['chargeMatiereJour'][$sMcKey] ?? 0) >= $sMax) continue;

                $slotOccupied = isset($state['grid'][$this->makeGridKey($sIdClasse, $sJour, $sCreneau)]);
                $slotProfOccupied = isset($state['occupationProf'][$this->makeProfKey($sIdProf, $sJour, $sCreneau)]);

                if ($slotOccupied || $slotProfOccupied) {
                    if ($depth >= 1) continue;
                    $occupant = null;
                    if ($slotOccupied) {
                        $occupant = $state['grid'][$this->makeGridKey($sIdClasse, $sJour, $sCreneau)];
                    } elseif ($slotProfOccupied) {
                        foreach ($state['sessionsPlaced'] as $sp) {
                            if ((int)$sp['id_enseignant'] === $sIdProf && (int)$sp['id_jour'] === (int)$sJour && (string)$sp['id_creneau'] === (string)$sCreneau) {
                                $occupant = $sp;
                                break;
                            }
                        }
                    }
                    if (!$occupant || (int)$occupant['id_classe'] === $sIdClasse) continue;
                    if (!isset($occupant['id_jour']) || !isset($occupant['id_creneau'])) continue;
                    $occKey = $this->makeGridKey((int)$occupant['id_classe'], (int)$occupant['id_jour'], (int)$occupant['id_creneau']);
                    if (isset($state['fixeKeys'][$occKey])) continue;

                    $occOrigJour = (int)$occupant['id_jour'];
                    $occOrigCreneau = $occupant['id_creneau'];

                    $this->removeSession($state, $existing);
                    $this->removeSession($state, $occupant);
                    $existingAtNew = $this->placeSession($state, $existing, $sJour, $sCreneau);
                    $occupantAtOld = false;
                    if ($existingAtNew) {
                        $occupantAtOld = $this->placeSession($state, $occupant, $oldJour, $oldCreneau);
                    }
                    $targetPlaced = false;
                    if ($existingAtNew && $occupantAtOld) {
                        $targetPlaced = $this->placeSession($state, $target, $tJour, $tCreneau);
                    }
                    if ($targetPlaced) return true;
                    if ($occupantAtOld) {
                        $occupant['id_jour'] = $oldJour;
                        $occupant['id_creneau'] = $oldCreneau;
                        $this->removeSession($state, $occupant);
                    }
                    if ($existingAtNew) {
                        $existing['id_jour'] = $sJour;
                        $existing['id_creneau'] = $sCreneau;
                        $this->removeSession($state, $existing);
                    }
                    $existing['id_jour'] = $oldJour;
                    $existing['id_creneau'] = $oldCreneau;
                    $this->placeSession($state, $existing, $oldJour, $oldCreneau);
                    $occupant['id_jour'] = $occOrigJour;
                    $occupant['id_creneau'] = $occOrigCreneau;
                    $this->placeSession($state, $occupant, $occOrigJour, $occOrigCreneau);
                    continue;
                }

                $this->removeSession($state, $existing);
                $existingAtNew = $this->placeSession($state, $existing, $sJour, $sCreneau);
                if ($existingAtNew && $this->placeSession($state, $target, $tJour, $tCreneau)) return true;
                if ($existingAtNew) {
                    $existing['id_jour'] = $sJour;
                    $existing['id_creneau'] = $sCreneau;
                    $this->removeSession($state, $existing);
                }
                $existing['id_jour'] = $oldJour;
                $existing['id_creneau'] = $oldCreneau;
                $this->placeSession($state, $existing, $oldJour, $oldCreneau);
            }
        }
        return false;
    }

    // ══════════════════════════════════════════════════════════════════════
    // BRUTE FORCE — Dernier recours
    // ══════════════════════════════════════════════════════════════════════

    private function bruteForcePlace(&$state, $target, $jourIds, $creneauIds, $indisponibilites, $creneauxExclus, $depth, $maxDepth) {
        if ($depth >= $maxDepth) return false;
        $tIdProf = (int)$target['id_enseignant'];
        $tIdClasse = (int)$target['id_classe'];

        foreach ($jourIds as $j) {
            foreach ($creneauIds as $c) {
                if (isset($indisponibilites[$tIdProf][$j][$c])) continue;
                if (isset($creneauxExclus[$j][$c])) continue;

                $gk = $this->makeGridKey($tIdClasse, $j, $c);
                $pk = $this->makeProfKey($tIdProf, $j, $c);
                $jkey = $this->makeDailyKey((int)$target['id_matiere_classe'], $j);
                $maxJour = (int)($target['nb_heures_par_jour'] ?? 1);
                $dailyCount = $state['chargeMatiereJour'][$jkey] ?? 0;
                $dailyOk = ($dailyCount + 1) <= $maxJour;

                $cellFree = !isset($state['grid'][$gk]);
                $profFree = !isset($state['occupationProf'][$pk]);

                if ($cellFree && $profFree && $dailyOk) {
                    return $this->placeSession($state, $target, $j, $c);
                }
                if (!$dailyOk) continue;

                $toRemove = [];
                if (!$cellFree) {
                    $existing = $state['grid'][$gk];
                    if ((int)$existing['id_classe'] === $tIdClasse) continue;
                    if (isset($state['fixeKeys'][$gk])) continue;
                    $toRemove[] = $existing;
                }
                if (!$profFree) {
                    $foundProf = false;
                    foreach ($toRemove as $tr) {
                        if ((int)$tr['id_enseignant'] === $tIdProf && (int)$tr['id_jour'] === (int)$j && (string)$tr['id_creneau'] === (string)$c) {
                            $foundProf = true;
                            break;
                        }
                    }
                    if (!$foundProf) {
                        foreach ($state['sessionsPlaced'] as $sp) {
                            if ((int)$sp['id_enseignant'] === $tIdProf && (int)$sp['id_jour'] === (int)$j && (string)$sp['id_creneau'] === (string)$c) {
                                if (isset($state['fixeKeys'][$this->makeGridKey((int)$sp['id_classe'], (int)$sp['id_jour'], $sp['id_creneau'])])) {
                                    $toRemove = [];
                                    break;
                                }
                                $toRemove[] = $sp;
                                break;
                            }
                        }
                    }
                }
                if (empty($toRemove)) continue;

                foreach ($toRemove as $tr) $this->removeSession($state, $tr);

                if ($this->placeSession($state, $target, $j, $c)) {
                    $relocOk = true;
                    foreach ($toRemove as $tr) {
                        if (!$this->relocateSession($state, $tr, $jourIds, $creneauIds, $indisponibilites, $creneauxExclus, 0, $maxDepth - 1)) {
                            $relocOk = false;
                            break;
                        }
                    }
                    if ($relocOk) return true;

                    foreach ($toRemove as $tr) $this->removeSession($state, $tr);
                    $this->removeSession($state, $target);
                }

                foreach ($toRemove as $tr) {
                    if (isset($tr['id_jour']) && isset($tr['id_creneau'])) {
                        $this->placeSession($state, $tr, (int)$tr['id_jour'], $tr['id_creneau']);
                    }
                }
            }
        }
        return false;
    }

    private function relocateSession(&$state, $session, $jourIds, $creneauIds, $indisponibilites, $creneauxExclus, $depth, $maxDepth) {
        if ($depth >= $maxDepth) return false;
        if (!isset($session['id_jour']) || !isset($session['id_creneau'])) return false;
        $idProf = (int)$session['id_enseignant'];
        $idClasse = (int)$session['id_classe'];
        $origJour = (int)$session['id_jour'];
        $origCreneau = $session['id_creneau'];

        foreach ($jourIds as $j) {
            foreach ($creneauIds as $c) {
                if ((int)$j === $origJour && (string)$c === (string)$origCreneau) continue;
                if (isset($indisponibilites[$idProf][$j][$c])) continue;
                if (isset($creneauxExclus[$j][$c])) continue;

                $gk = $this->makeGridKey($idClasse, $j, $c);
                $pk = $this->makeProfKey($idProf, $j, $c);
                $jkey = $this->makeDailyKey((int)$session['id_matiere_classe'], $j);
                $maxJour = (int)($session['nb_heures_par_jour'] ?? 1);
                $dailyCount = $state['chargeMatiereJour'][$jkey] ?? 0;
                if (($dailyCount + 1) > $maxJour) continue;
                if (isset($state['occupationProf'][$pk])) continue;

                if (!isset($state['grid'][$gk])) {
                    return $this->placeSession($state, $session, $j, $c);
                }

                if ($depth + 1 >= $maxDepth) continue;
                $occupant = $state['grid'][$gk];
                if ((int)$occupant['id_classe'] === $idClasse) continue;
                if (isset($state['fixeKeys'][$gk])) continue;
                if (!isset($occupant['id_jour']) || !isset($occupant['id_creneau'])) continue;

                $occOrigJour = (int)$occupant['id_jour'];
                $occOrigCreneau = $occupant['id_creneau'];
                $this->removeSession($state, $occupant);
                if ($this->placeSession($state, $session, $j, $c)) {
                    if ($this->relocateSession($state, $occupant, $jourIds, $creneauIds, $indisponibilites, $creneauxExclus, $depth + 1, $maxDepth)) return true;
                    $this->removeSession($state, $session);
                }
                $this->placeSession($state, $occupant, $occOrigJour, $occOrigCreneau);
            }
        }
        return false;
    }

    // ══════════════════════════════════════════════════════════════════════
    // OPTIMISATION — Consécutive
    // ══════════════════════════════════════════════════════════════════════

    private function optimizeConsecutive(&$state, $jourIds, $creneauIds) {
        $changed = true;
        $maxIter = 200;
        while ($changed && $maxIter-- > 0) {
            $changed = false;
            foreach ($jourIds as $jour) {
                $classesOnJour = [];
                foreach ($state['sessionsPlaced'] as $sp) {
                    if ((int)$sp['id_jour'] === $jour) {
                        $classesOnJour[(int)$sp['id_classe']][] = $sp;
                    }
                }
                foreach ($classesOnJour as $classeId => $classeSessions) {
                    $byMC = [];
                    foreach ($classeSessions as $sp) $byMC[(int)$sp['id_matiere_classe']][] = $sp;
                    foreach ($byMC as $mcId => $mcSessions) {
                        if (count($mcSessions) < 2) continue;
                        $hasFixe = false;
                        foreach ($mcSessions as $ms) {
                            $fk = $this->makeGridKey((int)$ms['id_classe'], (int)$ms['id_jour'], (int)$ms['id_creneau']);
                            if (isset($state['fixeKeys'][$fk])) { $hasFixe = true; break; }
                        }
                        if ($hasFixe) continue;

                        usort($mcSessions, function ($a, $b) { return (int)$a['id_creneau'] - (int)$b['id_creneau']; });
                        $isConsec = true;
                        for ($i = 1; $i < count($mcSessions); $i++) {
                            if ((int)$mcSessions[$i]['id_creneau'] !== (int)$mcSessions[$i-1]['id_creneau'] + 1) { $isConsec = false; break; }
                        }
                        if ($isConsec) continue;

                        $currentSlots = array_map(function($s) { return (int)$s['id_creneau']; }, $mcSessions);
                        $needed = count($currentSlots);
                        $bestSlots = null;
                        $bestGap = PHP_INT_MAX;

                        for ($start = 0; $start <= count($creneauIds) - $needed; $start++) {
                            $cand = [];
                            for ($k = 0; $k < $needed; $k++) $cand[] = (int)$creneauIds[$start + $k];
                            if (count(array_unique($cand)) !== $needed) continue;
                            $gap = 0;
                            for ($k = 0; $k < $needed; $k++) $gap += abs($cand[$k] - $currentSlots[$k]);
                            if ($gap === 0) continue;
                            $ok = true;
                            foreach ($cand as $cs) {
                                if (in_array($cs, $currentSlots)) continue;
                                $gk = $this->makeGridKey($classeId, $jour, $cs);
                                if (isset($state['grid'][$gk]) && (int)$state['grid'][$gk]['id_matiere_classe'] !== $mcId) { $ok = false; break; }
                                $pk = $this->makeProfKey((int)$mcSessions[0]['id_enseignant'], $jour, $cs);
                                if (isset($state['occupationProf'][$pk])) {
                                    $owned = false;
                                    foreach ($mcSessions as $ms) { if ((int)$ms['id_creneau'] === $cs) { $owned = true; break; } }
                                    if (!$owned) { $ok = false; break; }
                                }
                            }
                            if ($ok && $gap < $bestGap) { $bestGap = $gap; $bestSlots = $cand; }
                        }
                        if ($bestSlots) {
                            $removed = [];
                            foreach ($mcSessions as $s) { $removed[] = $s; $this->removeSession($state, $s); }
                            $allOk = true;
                            $newPositions = [];
                            for ($k = 0; $k < count($removed) && $allOk; $k++) {
                                if (!$this->placeSession($state, $removed[$k], $jour, $bestSlots[$k])) {
                                    $allOk = false;
                                } else {
                                    $newPositions[$k] = ['jour' => $jour, 'creneau' => $bestSlots[$k]];
                                }
                            }
                            if ($allOk) { $changed = true; } else {
                                for ($k = 0; $k < count($removed); $k++) {
                                    if (isset($newPositions[$k])) {
                                        $removed[$k]['id_jour'] = $newPositions[$k]['jour'];
                                        $removed[$k]['id_creneau'] = $newPositions[$k]['creneau'];
                                        $this->removeSession($state, $removed[$k]);
                                    }
                                }
                                foreach ($removed as $s) $this->placeSession($state, $s, (int)$s['id_jour'], (int)$s['id_creneau']);
                            }
                        }
                    }
                }
            }
        }
    }

    // ══════════════════════════════════════════════════════════════════════
    // ORDONNANCEMENTS
    // ══════════════════════════════════════════════════════════════════════

    private function orderGroupsMostSessionsFirst($groups) {
        $arr = [];
        foreach ($groups as $key => $g) $arr[] = ['key' => $key, 'group' => $g, 'count' => count($g)];
        usort($arr, function ($a, $b) { return $b['count'] - $a['count']; });
        $result = [];
        foreach ($arr as $item) $result[$item['key']] = $item['group'];
        return $result;
    }

    private function orderGroupsMostConstrainedFirst($groups, $jourIds, $creneauIds, $indisponibilites, $creneauxExclus) {
        $scored = [];
        foreach ($groups as $key => $group) {
            $flex = 0;
            foreach ($jourIds as $j) {
                foreach ($creneauIds as $c) {
                    $idProf = (int)$group[0]['id_enseignant'];
                    if (!isset($indisponibilites[$idProf][$j][$c]) && !isset($creneauxExclus[$j][$c])) $flex++;
                }
            }
            $scored[] = ['key' => $key, 'group' => $group, 'flex' => $flex, 'count' => count($group)];
        }
        usort($scored, function ($a, $b) {
            $ratioA = $a['count'] / max($a['flex'], 1);
            $ratioB = $b['count'] / max($b['flex'], 1);
            if ($ratioA != $ratioB) return $ratioB <=> $ratioA;
            return $a['flex'] <=> $b['flex'];
        });
        $result = [];
        foreach ($scored as $item) $result[$item['key']] = $item['group'];
        return $result;
    }

    private function orderGroupsByTeacherLoad($groups, $jourIds, $creneauIds, $indisponibilites, $creneauxExclus) {
        $teacherLoad = [];
        foreach ($groups as $group) {
            $idProf = (int)$group[0]['id_enseignant'];
            $teacherLoad[$idProf] = ($teacherLoad[$idProf] ?? 0) + count($group);
        }
        $teacherSlots = [];
        foreach ($teacherLoad as $idProf => $load) {
            $slots = 0;
            foreach ($jourIds as $j) {
                foreach ($creneauIds as $c) {
                    if (!isset($indisponibilites[$idProf][$j][$c]) && !isset($creneauxExclus[$j][$c])) $slots++;
                }
            }
            $teacherSlots[$idProf] = $slots;
        }
        $scored = [];
        foreach ($groups as $key => $group) {
            $idProf = (int)$group[0]['id_enseignant'];
            $load = $teacherLoad[$idProf];
            $slots = $teacherSlots[$idProf];
            $scored[] = ['key' => $key, 'group' => $group, 'ratio' => $load / max($slots, 1)];
        }
        usort($scored, function ($a, $b) {
            return $b['ratio'] <=> $a['ratio'];
        });
        $result = [];
        foreach ($scored as $item) $result[$item['key']] = $item['group'];
        return $result;
    }

    // ══════════════════════════════════════════════════════════════════════
    // VALIDATION — Contrôle final complet
    // ══════════════════════════════════════════════════════════════════════

    public function validateCompleteSchedule($state, $expectedSessions) {
        $expectedCount = count($expectedSessions);
        $expectedAuto = 0;
        foreach ($expectedSessions as $es) {
            if (empty($es['type']) || $es['type'] !== 'fixe') $expectedAuto++;
        }
        $placedCount = count($state['sessionsPlaced']);
        $conflictsProf = $conflictsClasse = $duplicateSessions = 0;
        $dailyLimitViolations = $availabilityViolations = 0;
        $seenGrid = $seenProf = $seenSessionIds = $dailyCounts = [];

        foreach ($state['sessionsPlaced'] as $session) {
            if (!isset($session['id_jour']) || !isset($session['id_creneau'])) continue;
            $ck = $this->makeGridKey((int)$session['id_classe'], (int)$session['id_jour'], $session['id_creneau']);
            $pk = $this->makeProfKey((int)$session['id_enseignant'], (int)$session['id_jour'], $session['id_creneau']);
            if (isset($seenGrid[$ck])) $conflictsClasse++; else $seenGrid[$ck] = true;
            if (isset($seenProf[$pk])) $conflictsProf++; else $seenProf[$pk] = true;
            if (isset($seenSessionIds[$session['session_id']])) $duplicateSessions++; else $seenSessionIds[$session['session_id']] = true;
            if (isset($state['indisponibilites'][(int)$session['id_enseignant']][(int)$session['id_jour']][$session['id_creneau']])) {
                $availabilityViolations++;
            }
            $dailyKey = $this->makeDailyKey((int)$session['id_matiere_classe'], (int)$session['id_jour']);
            $dailyCounts[$dailyKey] = ($dailyCounts[$dailyKey] ?? 0) + 1;
            if ($dailyCounts[$dailyKey] > (int)($session['nb_heures_par_jour'] ?? 1)) $dailyLimitViolations++;
        }

        $missing = max(0, $expectedCount - $placedCount);
        $unexpected = max(0, $placedCount - $expectedCount);

        return [
            'success' => ($missing === 0 && $unexpected === 0 && $conflictsProf === 0 && $conflictsClasse === 0 && $duplicateSessions === 0 && $dailyLimitViolations === 0 && $availabilityViolations === 0),
            'expected' => $expectedCount,
            'expected_auto' => $expectedAuto,
            'placed' => $placedCount,
            'missing' => $missing,
            'unexpected' => $unexpected,
            'conflicts_prof' => $conflictsProf,
            'conflicts_classe' => $conflictsClasse,
            'duplicate_sessions' => $duplicateSessions,
            'daily_limit_violations' => $dailyLimitViolations,
            'availability_violations' => $availabilityViolations,
        ];
    }

    private function sessionInList($session, $list) {
        foreach ($list as $s) {
            if ((int)$s['session_id'] === (int)$session['session_id']) return true;
        }
        return false;
    }
}
