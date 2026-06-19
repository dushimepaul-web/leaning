<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Horaires extends MY_Controller {
    public function __construct() { parent::__construct(); }

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
        $this->db->where('h.deleted_at', null);
        $this->db->select('h.*, j.libelle as jour, c.libelle as creneau, c.heure_debut, c.heure_fin, cl.libelle as classe, CONCAT(e.nom, " ", e.prenom) as enseignant, m.libelle as matiere');
        $this->db->from('horaires h');
        $this->db->join('jours_semaine j', 'h.id_jour = j.id_jour', 'left');
        $this->db->join('creneaux c', 'h.id_creneau = c.id_creneau', 'left');
        $this->db->join('classes cl', 'h.id_classe = cl.id_classe', 'left');
        $this->db->join('enseignants e', 'h.id_enseignant = e.id_enseignant', 'left');
        $this->db->join('enseignements en', 'h.id_enseignement = en.id_enseignement', 'left');
        $this->db->join('matieres m', 'en.id_matiere = m.id_matiere', 'left');
        $this->db->order_by('h.id_jour, h.id_creneau');
        $this->json_success($this->db->get()->result_array());
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
        $this->load->helper('uuid');
        $gen = $this->_getOrCreateGeneration();
        if (!$gen) { $this->json_error('Erreur création génération'); return; }

        $enseignements = $this->db
            ->select('en.*, mc.nb_heures_par_semaine, mc.nb_heures_par_jour')
            ->from('enseignements en')
            ->join('matieres_classes mc', 'en.id_matiere = mc.id_matiere AND en.id_classe = mc.id_classe AND mc.deleted_at IS NULL', 'left')
            ->where('en.deleted_at', null)
            ->get()->result_array();

        if (empty($enseignements)) { $this->json_error('Aucun enseignement trouvé'); return; }

        $creneaux = $this->db
            ->where('type_creneau', 'cours')
            ->order_by('ordre')
            ->get('creneaux')->result_array();

        $jours = $this->Model->read('jours_semaine', ['actif' => 1], 'ordre');

        if (empty($creneaux) || empty($jours)) { $this->json_error('Créneaux ou jours manquants'); return; }

        $dispos = [];
        $rawDispos = $this->db
            ->where('type', 'indisponible')
            ->where('deleted_at', null)
            ->get('disponibilites_enseignants')->result_array();
        foreach ($rawDispos as $d) {
            $dispos[$d['id_enseignant']][$d['id_jour']][$d['id_creneau']] = true;
        }

        $contraintes = $this->db
            ->where('id_annee', $this->id_annee_active)
            ->where('deleted_at', null)
            ->get('contraintes_horaires')->result_array();

        // Supprimer définitivement les anciens horaires de cette génération
        $this->db->where('id_generation', $gen['id_generation'])->delete('horaires');

        $slots = [];
        $teacherDayCount = [];
        $classDaySlots = [];
        $created = 0;

        // Préparer les enseignements avec leurs besoins
        $needs = [];
        foreach ($enseignements as $ens) {
            $hoursPerWeek = floatval(isset($ens['nb_heures_par_semaine']) ? $ens['nb_heures_par_semaine'] : 3);
            $maxPerDay = intval(isset($ens['nb_heures_par_jour']) ? $ens['nb_heures_par_jour'] : 2);
            if ($maxPerDay < 1) $maxPerDay = 1;
            if ($hoursPerWeek < 1) $hoursPerWeek = 3;
            $needs[] = [
                'enseignement' => $ens,
                'hours_total' => $hoursPerWeek,
                'max_per_day' => $maxPerDay,
                'assigned' => 0,
            ];
        }

        // Algorithme : greedy round-robin par jour
        for ($round = 0; $round < 5; $round++) {
            foreach ($jours as $jour) {
                foreach ($creneaux as $creneau) {
                    foreach ($needs as &$need) {
                        if ($need['assigned'] >= $need['hours_total']) continue;
                        $ens = $need['enseignement'];

                        $classKey = $ens['id_classe'] . '_' . $jour['id_jour'] . '_' . $creneau['id_creneau'];
                        $teacherKey = $ens['id_enseignant'] . '_' . $jour['id_jour'] . '_' . $creneau['id_creneau'];
                        $ensKey = $ens['id_enseignement'] . '_' . $jour['id_jour'] . '_' . $creneau['id_creneau'];
                        $teacherDayKey = $ens['id_enseignant'] . '_' . $jour['id_jour'];

                        // Vérifier conflit classe
                        if (isset($classDaySlots[$classKey])) continue;
                        // Vérifier conflit enseignant
                        if (isset($classDaySlots[$teacherKey])) continue;
                        // Vérifier conflit enseignement
                        if (isset($classDaySlots[$ensKey])) continue;
                        // Vérifier indisponibilité enseignant
                        if (isset($dispos[$ens['id_enseignant']][$jour['id_jour']][$creneau['id_creneau']])) continue;
                        // Vérifier max par jour pour cet enseignement
                        $teacherDaySlots = isset($teacherDayCount[$teacherDayKey]) ? count($teacherDayCount[$teacherDayKey]) : 0;
                        $classSlotsToday = 0;
                        foreach ($classDaySlots as $k => $v) {
                            if (strpos($k, $ens['id_classe'] . '_' . $jour['id_jour']) === 0) $classSlotsToday++;
                        }
                        if ($classSlotsToday >= $need['max_per_day']) continue;

                        // Créer le slot
                        $insert = [
                            'uuid' => generate_uuid(),
                            'id_generation' => $gen['id_generation'],
                            'id_enseignement' => $ens['id_enseignement'],
                            'id_classe' => $ens['id_classe'],
                            'id_jour' => $jour['id_jour'],
                            'id_creneau' => $creneau['id_creneau'],
                            'id_enseignant' => $ens['id_enseignant'],
                        ];
                        $this->db->insert('horaires', $insert);

                        $classDaySlots[$classKey] = true;
                        $classDaySlots[$teacherKey] = true;
                        $classDaySlots[$ensKey] = true;
                        $teacherDayCount[$teacherDayKey][] = $creneau['id_creneau'];
                        $need['assigned']++;
                        $created++;
                    }
                }
            }
        }

        $this->json_success([
            'created' => $created,
            'generation' => $gen['libelle'],
            'total_enseignements' => count($enseignements)
        ], 'Génération terminée : ' . $created . ' créneaux créés');
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
            return $this->Model->readOne('enseignements', [
                'id_classe' => $data['id_classe'],
                'id_matiere' => $data['id_matiere'],
                'deleted_at' => null
            ]);
        }
        return null;
    }
}
