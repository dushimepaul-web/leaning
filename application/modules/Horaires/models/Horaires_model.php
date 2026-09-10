<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Horaires_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_generation_payload() {
        $classes = $this->db->where('deleted_at IS NULL')->get('classes')->result_array();
        $matieres_classes = $this->db->where('deleted_at IS NULL')->get('matieres_classes')->result_array();
        $enseignements = $this->db->where('deleted_at IS NULL')->get('enseignements')->result_array();
        $jours = $this->db->where('deleted_at IS NULL')->order_by('id_jour', 'ASC')->get('jours_semaine')->result_array();

        $parametres = $this->get_parametres_map();
        $allCreneaux = $this->compute_creneaux($parametres);
        $creneauxGenerateur = array_values(array_filter($allCreneaux, function($c) {
            return $c['type'] === 'cours';
        }));

        $indispoRows = $this->db->where('deleted_at IS NULL')->get('disponibilites_enseignants')->result_array();
        $indisponibilites = [];
        $disponibilites = [];
        $teachersWithWhitelist = [];
        foreach ($indispoRows as $row) {
            if (isset($row['type']) && $row['type'] === 'indisponible') {
                $indisponibilites[(int)$row['id_enseignant']][(int)$row['id_jour']][(int)$row['id_creneau']] = true;
            } elseif (isset($row['type']) && $row['type'] === 'disponible') {
                // Dès qu'un enseignant possède une disponibilité explicite, la grille
                // devient une liste blanche : tous les autres créneaux sont fermés.
                $idEns = (int)$row['id_enseignant'];
                $teachersWithWhitelist[$idEns] = true;
                $disponibilites[$idEns][(int)$row['id_jour']][(int)$row['id_creneau']] = true;
            }
        }

        // Convertit les listes blanches en indisponibilités, afin que tout le moteur
        // applique une unique règle : un créneau présent ici est interdit.
        foreach (array_keys($teachersWithWhitelist) as $idEns) {
            foreach ($jours as $jour) {
                foreach ($creneauxGenerateur as $creneau) {
                    $idJour = (int)$jour['id_jour'];
                    $idCreneau = (int)$creneau['id_creneau'];
                    if (empty($disponibilites[$idEns][$idJour][$idCreneau])) {
                        $indisponibilites[$idEns][$idJour][$idCreneau] = true;
                    }
                }
            }
        }

        return [
            'classes' => $classes,
            'matieres_classes' => $matieres_classes,
            'enseignements' => $enseignements,
            'jours' => $jours,
            'creneaux' => $creneauxGenerateur,
            'indisponibilites' => $indisponibilites,
            'creneaux_exclus' => [],
            'enseignants_liste_blanche' => array_keys($teachersWithWhitelist),
            'parametres' => $parametres
        ];
    }

    /** Valide une séance fixe avant son enregistrement. */
    public function validate_fixe($data, $idAnnee) {
        $idClasse = (int)$data['id_classe'];
        $idMc = (int)$data['id_matiere_classe'];
        $idEns = (int)$data['id_enseignant'];
        $idJour = (int)$data['id_jour'];
        $idCreneau = (int)$data['id_creneau'];

        $mc = $this->db->where('id_matiere_classe', $idMc)->where('deleted_at IS NULL', null, false)->get('matieres_classes')->row_array();
        if (!$mc || (int)$mc['id_classe'] !== $idClasse) return ['success' => false, 'message' => 'La matière choisie ne correspond pas à cette classe.'];

        $enseignement = $this->db->where('id_matiere_classe', $idMc)->where('id_enseignant', $idEns)
            ->where('deleted_at IS NULL', null, false)->get('enseignements')->row_array();
        if (!$enseignement) return ['success' => false, 'message' => 'Cet enseignant n\'est pas affecté à cette matière dans cette classe.'];

        $payload = $this->get_generation_payload();
        if (isset($payload['indisponibilites'][$idEns][$idJour][$idCreneau])) {
            return ['success' => false, 'message' => 'L\'enseignant est indisponible pour ce créneau fixe.'];
        }

        $conflict = $this->db->group_start()
            ->where('id_classe', $idClasse)
            ->or_where('id_enseignant', $idEns)
            ->group_end()->where('id_jour', $idJour)->where('id_creneau', $idCreneau)
            ->where('deleted_at IS NULL', null, false)->get('horaires_fixes')->row_array();
        if ($conflict) return ['success' => false, 'message' => 'Ce créneau fixe est déjà occupé par la classe ou l\'enseignant.'];

        $maxJour = (int)$mc['nb_heures_par_jour'];
        if ($maxJour < 1) return ['success' => false, 'message' => 'Le maximum d\'heures par jour doit être supérieur à zéro.'];
        $sameCourse = $this->db->where('id_annee', $idAnnee)->where('id_matiere_classe', $idMc)
            ->where('id_jour', $idJour)->where('deleted_at IS NULL', null, false)->count_all_results('horaires_fixes');
        if ($sameCourse >= $maxJour) return ['success' => false, 'message' => 'Le maximum quotidien de cette matière est déjà atteint.'];

        return ['success' => true, 'enseignement' => $enseignement];
    }

    public function get_parametres_map() {
        $rows = $this->db->get('parametres')->result_array();
        $map = [];
        foreach ($rows as $r) {
            $key = $r['clef'] ?? $r['key'] ?? '';
            $val = $r['valeur'] ?? $r['value'] ?? '';
            if ($key) $map[$key] = $val;
        }
        return $map;
    }

    public function compute_creneaux($params) {
        $nbCreneaux = isset($params['nb_creneaux_jour']) ? (int)$params['nb_creneaux_jour'] : 8;
        $heureDebut = $params['heure_debut_journee'] ?? '07:30';
        $dureeCours = (int)($params['duree_cours'] ?? 45);
        $dureePause = (int)($params['duree_pause'] ?? 20);
        $dureeVigie = (int)($params['duree_vigie'] ?? 10);

        $creneaux = [];
        $currentHour = (int)substr($heureDebut, 0, 2);
        $currentMin = (int)substr($heureDebut, 3, 2);

        $hasVigie = ($dureeVigie > 0);
        $pauseAfter = (int)floor($nbCreneaux / 2);

        for ($i = 1; $i <= $nbCreneaux; $i++) {
            if ($hasVigie && $i === 1) {
                $debut = sprintf('%02d:%02d', $currentHour, $currentMin);
                $totalMin = $currentMin + $dureeVigie;
                $currentHour += (int)floor($totalMin / 60);
                $currentMin = $totalMin % 60;
                $fin = sprintf('%02d:%02d', $currentHour, $currentMin);
                $creneaux[] = [
                    'id_creneau' => 'vigile',
                    'type' => 'vigile',
                    'type_creneau' => 'vigile',
                    'heure_debut' => $debut,
                    'heure_fin' => $fin,
                    'libelle' => 'Salut du drapeau',
                    'ordre' => count($creneaux) + 1
                ];
            }

            $debut = sprintf('%02d:%02d', $currentHour, $currentMin);
            $totalMin = $currentMin + $dureeCours;
            $currentHour += (int)floor($totalMin / 60);
            $currentMin = $totalMin % 60;
            $fin = sprintf('%02d:%02d', $currentHour, $currentMin);

            $creneaux[] = [
                'id_creneau' => $i,
                'type' => 'cours',
                'type_creneau' => 'cours',
                'heure_debut' => $debut,
                'heure_fin' => $fin,
                'libelle' => 'Cours ' . $i,
                'ordre' => count($creneaux) + 1
            ];

            if ($i === $pauseAfter && $i < $nbCreneaux) {
                $debut = sprintf('%02d:%02d', $currentHour, $currentMin);
                $totalMin = $currentMin + $dureePause;
                $currentHour += (int)floor($totalMin / 60);
                $currentMin = $totalMin % 60;
                $fin = sprintf('%02d:%02d', $currentHour, $currentMin);
                $creneaux[] = [
                    'id_creneau' => 'pause' . $i,
                    'type' => 'pause',
                    'type_creneau' => 'pause',
                    'heure_debut' => $debut,
                    'heure_fin' => $fin,
                    'libelle' => 'Pause',
                    'ordre' => count($creneaux) + 1
                ];
            }
        }

        return $creneaux;
    }

    public function compute_creneaux_mardi($params) {
        $nbCreneaux = isset($params['nb_creneaux_jour']) ? (int)$params['nb_creneaux_jour'] : 8;
        $heureDebut = $params['heure_debut_journee'] ?? '07:30';
        $dureeCours = (int)($params['duree_cours'] ?? 45);
        $dureeCulte = (int)($params['duree_culte'] ?? 35);
        $dureeVigie = (int)($params['duree_vigie'] ?? 10);

        $creneaux = [];
        $currentHour = (int)substr($heureDebut, 0, 2);
        $currentMin = (int)substr($heureDebut, 3, 2);

        $hasVigie = ($dureeVigie > 0);
        $pauseAfter = (int)floor($nbCreneaux / 2);

        for ($i = 1; $i <= $nbCreneaux; $i++) {
            if ($hasVigie && $i === 1) {
                $debut = sprintf('%02d:%02d', $currentHour, $currentMin);
                $totalMin = $currentMin + $dureeVigie;
                $currentHour += (int)floor($totalMin / 60);
                $currentMin = $totalMin % 60;
                $fin = sprintf('%02d:%02d', $currentHour, $currentMin);
                $creneaux[] = [
                    'id_creneau' => 'vigile',
                    'type' => 'vigile',
                    'type_creneau' => 'vigile',
                    'heure_debut' => $debut,
                    'heure_fin' => $fin,
                    'libelle' => 'Salut du drapeau',
                    'ordre' => count($creneaux) + 1
                ];
            }

            $debut = sprintf('%02d:%02d', $currentHour, $currentMin);
            $totalMin = $currentMin + $dureeCours;
            $currentHour += (int)floor($totalMin / 60);
            $currentMin = $totalMin % 60;
            $fin = sprintf('%02d:%02d', $currentHour, $currentMin);

            $creneaux[] = [
                'id_creneau' => $i,
                'type' => 'cours',
                'type_creneau' => 'cours',
                'heure_debut' => $debut,
                'heure_fin' => $fin,
                'libelle' => 'Cours ' . $i,
                'ordre' => count($creneaux) + 1
            ];

            if ($i === $pauseAfter && $i < $nbCreneaux) {
                $debut = sprintf('%02d:%02d', $currentHour, $currentMin);
                $totalMin = $currentMin + $dureeCulte;
                $currentHour += (int)floor($totalMin / 60);
                $currentMin = $totalMin % 60;
                $fin = sprintf('%02d:%02d', $currentHour, $currentMin);
                $creneaux[] = [
                    'id_creneau' => 'culte',
                    'type' => 'culte',
                    'type_creneau' => 'culte',
                    'heure_debut' => $debut,
                    'heure_fin' => $fin,
                    'libelle' => 'Culte',
                    'ordre' => count($creneaux) + 1
                ];
            }
        }

        return $creneaux;
    }

    public function get_creneaux_cours() {
        $parametres = $this->get_parametres_map();
        return $this->compute_creneaux($parametres);
    }

    public function get_creneaux_mardi() {
        $parametres = $this->get_parametres_map();
        return $this->compute_creneaux_mardi($parametres);
    }

    public function create_generation_record($data) {
        $data['cree_le'] = date('Y-m-d H:i:s');
        $this->db->insert('horaires_generations', $data);
        return $this->db->insert_id();
    }

    public function insert_horaires_batch($batch) {
        if (!empty($batch)) {
            $this->db->insert_batch('horaires', $batch);
        }
    }

    public function get_latest_generation() {
        return $this->db->order_by('id_generation', 'DESC')->limit(1)->get('horaires_generations')->row_array();
    }

    public function get_horaires_by_enseignant($id_enseignant) {
        $this->db->select('h.*, m.libelle as matiere_libelle, m.code as matiere_code, c.libelle as classe_libelle, j.libelle as jour_libelle, j.code as jour_code, j.ordre as jour_ordre');
        $this->db->from('horaires h');
        $this->db->join('matieres m', 'h.id_matiere = m.id_matiere', 'left');
        $this->db->join('classes c', 'h.id_classe = c.id_classe', 'left');
        $this->db->join('jours_semaine j', 'h.id_jour = j.id_jour', 'left');
        $this->db->where('h.id_enseignant', $id_enseignant);
        $this->db->where('h.deleted_at IS NULL', null, false);
        $this->db->order_by('j.ordre', 'ASC');
        $this->db->order_by('h.id_creneau', 'ASC');
        $rows = $this->db->get()->result_array();

        $parametres = $this->get_parametres_map();
        $allCreneaux = $this->compute_creneaux($parametres);
        $creneauxMap = [];
        foreach ($allCreneaux as $cr) {
            if ($cr['type'] === 'cours') {
                $creneauxMap[$cr['id_creneau']] = $cr;
            }
        }

        $by_jour = [];
        foreach ($rows as &$h) {
            $c = $creneauxMap[$h['id_creneau']] ?? null;
            $h['heure_debut'] = $c ? $c['heure_debut'] : '';
            $h['heure_fin'] = $c ? $c['heure_fin'] : '';
            $h['creneau_libelle'] = $c ? $c['libelle'] : "Cours {$h['id_creneau']}";
            $jid = $h['id_jour'];
            $by_jour[$jid][] = $h;
        }
        unset($h);

        return ['horaires' => $rows, 'by_jour' => $by_jour];
    }

    public function list_horaires() {
        $this->db->select('h.*, e.fullname as enseignant, m.code as matiere_code, m.libelle as matiere_libelle, c.libelle as classe_libelle, j.libelle as jour_libelle, j.code as jour_code');
        $this->db->from('horaires h');
        $this->db->join('enseignants e', 'h.id_enseignant = e.id_enseignant', 'left');
        $this->db->join('matieres m', 'h.id_matiere = m.id_matiere', 'left');
        $this->db->join('classes c', 'h.id_classe = c.id_classe', 'left');
        $this->db->join('jours_semaine j', 'h.id_jour = j.id_jour', 'left');
        $this->db->where('h.deleted_at', null);
        $this->db->order_by('c.libelle', 'ASC');
        $this->db->order_by('j.ordre', 'ASC');
        $this->db->order_by('h.id_creneau', 'ASC');
        $q = $this->db->get();
        return $q !== false ? $q->result_array() : [];
    }

    public function get_fixes($id_annee) {
        $this->db->select('f.*, e.fullname as enseignant, m.code as matiere_code, m.libelle as matiere_libelle, c.libelle as classe_libelle, j.libelle as jour_libelle');
        $this->db->from('horaires_fixes f');
        $this->db->join('enseignants e', 'f.id_enseignant = e.id_enseignant', 'left');
        $this->db->join('matieres_classes mc', 'f.id_matiere_classe = mc.id_matiere_classe', 'left');
        $this->db->join('matieres m', 'mc.id_matiere = m.id_matiere', 'left');
        $this->db->join('classes c', 'f.id_classe = c.id_classe', 'left');
        $this->db->join('jours_semaine j', 'f.id_jour = j.id_jour', 'left');
        $this->db->where('f.id_annee', $id_annee);
        $this->db->where('f.deleted_at IS NULL', null, false);
        $this->db->order_by('c.libelle', 'ASC');
        $this->db->order_by('j.ordre', 'ASC');
        $this->db->order_by('f.id_creneau', 'ASC');
        $q = $this->db->get();
        return $q !== false ? $q->result_array() : [];
    }

    public function get_fixes_by_annee($id_annee) {
        $this->db->select('f.*, mc.id_matiere');
        $this->db->from('horaires_fixes f');
        $this->db->join('matieres_classes mc', 'f.id_matiere_classe = mc.id_matiere_classe', 'left');
        $this->db->where('f.id_annee', $id_annee);
        $this->db->where('f.deleted_at IS NULL', null, false);
        $q = $this->db->get();
        return $q !== false ? $q->result_array() : [];
    }

    public function add_fixe($data) {
        $this->db->insert('horaires_fixes', $data);
        return $this->db->insert_id();
    }

    public function remove_fixe($uuid) {
        $this->db->where('uuid', $uuid);
        return $this->db->update('horaires_fixes', ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    public function clear_fixes($id_annee) {
        $this->db->where('id_annee', $id_annee);
        $this->db->update('horaires_fixes', ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}
