<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Horaires_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('h.deleted_at', null);
        $this->db->select('h.*, j.libelle as jour, cl.libelle as classe, e.fullname as enseignant, m.libelle as matiere, m.code as matiere_code');
        $this->db->from('horaires h');
        $this->db->join('jours_semaine j', 'h.id_jour = j.id_jour', 'left');
        $this->db->join('classes cl', 'h.id_classe = cl.id_classe', 'left');
        $this->db->join('enseignants e', 'h.id_enseignant = e.id_enseignant', 'left');
        $this->db->join('enseignements en', 'h.id_enseignement = en.id_enseignement', 'left');
        $this->db->join('matieres_classes mc', 'en.id_matiere_classe = mc.id_matiere_classe', 'left');
        $this->db->join('matieres m', 'mc.id_matiere = m.id_matiere', 'left');
        if (!empty($filters['id_classe'])) $this->db->where('h.id_classe', $filters['id_classe']);
        if (!empty($filters['id_generation'])) $this->db->where('h.id_generation', $filters['id_generation']);
        $this->db->order_by('h.id_jour, h.id_creneau');
        $q = $this->db->get();
        if ($q === false) return array();
        $rows = $q->result_array();

        // Injecter dynamiquement les heures de début et fin du créneau généré
        $creneaux = $this->get_creneaux_cours();
        $creneauxMap = [];
        foreach ($creneaux as $cr) {
            $creneauxMap[$cr['id_creneau']] = $cr;
        }

        foreach ($rows as &$r) {
            $cid = $r['id_creneau'];
            if (isset($creneauxMap[$cid])) {
                $r['creneau'] = $creneauxMap[$cid]['libelle'];
                $r['heure_debut'] = $creneauxMap[$cid]['heure_debut'];
                $r['heure_fin'] = $creneauxMap[$cid]['heure_fin'];
            } else {
                $r['creneau'] = 'Cours ' . $cid;
                $r['heure_debut'] = '';
                $r['heure_fin'] = '';
            }
        }
        return $rows;
    }

    public function get_jours_actifs()
    {
        $q = $this->db->where('actif', 1)->order_by('ordre')->get('jours_semaine');
        return $q !== false ? $q->result_array() : [];
    }

    public function get_creneaux_cours()
    {
        /**
         * 1. Simplifier et automatiser la génération des Horaires (via les Paramètres)
         * - Action : Calcule automatiquement les tranches de cours de la journée en utilisant
         *   les valeurs configurées dans les Paramètres (heure_debut_journee, duree_cours, 
         *   duree_pause, duree_vigie, nb_creneaux_jour) au lieu de dépendre d'une saisie 
         *   manuelle dans une table de créneaux.
         * 
         * Logique horaire :
         * - Le vigile / rassemblement matinal se place au tout début avant les cours.
         * - Les cours s'enchaînent selon le nombre de créneaux définis.
         * - La pause (récréation) intervient exactement après la moitié de ces créneaux.
         */
        $heure_debut = get_setting('heure_debut_journee', '07:30');
        $duree_cours = (int)get_setting('duree_cours', 45);
        $duree_pause = (int)get_setting('duree_pause', 20);
        $duree_vigie = (int)get_setting('duree_vigie', 10);
        $nb_creneaux = (int)get_setting('nb_creneaux_jour', 8);

        // Convertir l'heure de début en minutes
        list($h, $m) = explode(':', $heure_debut);
        $current_minutes = (int)$h * 60 + (int)$m;

        // Vigile / Rassemblement matinal (avant que les cours ne commencent)
        if ($duree_vigie > 0) {
            $current_minutes += $duree_vigie;
        }

        $creneaux = [];
        $milieu = ceil($nb_creneaux / 2); // Point de la grande pause (ex: après 4 cours sur 8)

        for ($i = 1; $i <= $nb_creneaux; $i++) {
            $start_h = sprintf('%02d:%02d', floor($current_minutes / 60), $current_minutes % 60);
            $current_minutes += $duree_cours;
            $end_h = sprintf('%02d:%02d', floor($current_minutes / 60), $current_minutes % 60);

            $creneaux[] = [
                'id_creneau' => $i,
                'libelle' => 'Cours ' . $i,
                'heure_debut' => $start_h,
                'heure_fin' => $end_h,
                'type_creneau' => 'cours',
                'ordre' => $i
            ];

            // La pause intervient exactement après la moitié des cours définis
            if ($i == $milieu && $i < $nb_creneaux) {
                $pause_start = sprintf('%02d:%02d', floor($current_minutes / 60), $current_minutes % 60);
                $current_minutes += $duree_pause;
                $pause_end = sprintf('%02d:%02d', floor($current_minutes / 60), $current_minutes % 60);
                $creneaux[] = [
                    'id_creneau' => 'pause' . $i,
                    'libelle' => 'PAUSE / RÉCRÉATION',
                    'heure_debut' => $pause_start,
                    'heure_fin' => $pause_end,
                    'type_creneau' => 'pause',
                    'ordre' => $i + 0.5
                ];
            }
        }
        return $creneaux;
    }

    public function get_matieres_classes_a_planifier()
    {
        $this->db->select('mc.*, m.code as matiere_code, m.libelle as matiere_libelle')
            ->from('matieres_classes mc')
            ->join('matieres m', 'mc.id_matiere = m.id_matiere')
            ->where('mc.deleted_at', null)
            ->where('mc.nb_heures_par_semaine >', 0)
            ->where('mc.id_enseignant IS NOT NULL')
            ->order_by('mc.nb_heures_par_semaine DESC');
        $q = $this->db->get();
        return $q !== false ? $q->result_array() : [];
    }

    public function get_disponibilites_enseignants()
    {
        $q = $this->db->where('type', 'indisponible')->where('deleted_at', null)->get('disponibilites_enseignants');
        return $q !== false ? $q->result_array() : [];
    }

    public function get_contraintes_horaires($id_annee)
    {
        $q = $this->db->where('id_annee', $id_annee)->where('deleted_at', null)->get('contraintes_horaires');
        return $q !== false ? $q->result_array() : [];
    }

    public function get_or_create_enseignement($id_matiere_classe, $id_enseignant, $id_matiere, $id_classe)
    {
        $ens = $this->db->where('id_matiere_classe', $id_matiere_classe)
            ->where('deleted_at', null)
            ->get('enseignements')
            ->row_array();
        if ($ens) return $ens['id_enseignement'];

        $this->load->helper('uuid');
        $eid = $this->db->insert('enseignements', [
            'uuid' => generate_uuid(),
            'id_enseignant' => $id_enseignant,
            'id_matiere' => $id_matiere,
            'id_classe' => $id_classe,
            'id_matiere_classe' => $id_matiere_classe,
        ]);
        if (!$eid) return 0;
        $ens = $this->db->where('id_enseignement', $this->db->insert_id())
            ->get('enseignements')
            ->row_array();
        return $ens ? $ens['id_enseignement'] : 0;
    }

    public function insert_horaires_batch($id_generation, $grille)
    {
        $this->db->trans_begin();
        $this->db->where('id_generation', $id_generation)->delete('horaires');

        if (!empty($grille)) {
            // OPTIMISATION SQL: Insertion par chunks de 500 lignes pour éviter la surcharge mémoire et les timeouts
            $chunks = array_chunk($grille, 500);
            foreach ($chunks as $chunk) {
                $batch = [];
                foreach ($chunk as $g) {
                    $batch[] = [
                        'uuid' => sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)),
                        'id_generation' => $id_generation,
                        'id_enseignement' => $g['id_enseignement'],
                        'id_matiere' => $g['id_matiere'],
                        'id_enseignant' => $g['id_enseignant'],
                        'id_classe' => $g['id_classe'],
                        'id_creneau' => $g['id_creneau'],
                        'id_jour' => $g['id_jour'],
                    ];
                }
                $this->db->insert_batch('horaires', $batch);
            }
        }

        $this->db->where('id_generation', $id_generation)->update('horaires_generations', ['statut' => 'brouillon']);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->trans_commit();
        return true;
    }

    public function get_matieres_by_classe($id_classe)
    {
        $this->db->select('m.id_matiere, m.libelle, m.code');
        $this->db->from('matieres m');
        $this->db->join('matieres_classes mc', 'm.id_matiere = mc.id_matiere AND mc.deleted_at IS NULL', 'inner');
        $this->db->where('mc.id_classe', $id_classe);
        $this->db->where('m.deleted_at', null);
        $this->db->order_by('m.libelle');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function get_enseignant_by_classe_matiere($id_classe, $id_matiere)
    {
        $this->db->select('e.id_enseignant, e.fullname, e.matricule');
        $this->db->from('enseignements en');
        $this->db->join('matieres_classes mc', 'en.id_matiere_classe = mc.id_matiere_classe', 'inner');
        $this->db->join('enseignants e', 'en.id_enseignant = e.id_enseignant');
        $this->db->where('mc.id_classe', $id_classe);
        $this->db->where('mc.id_matiere', $id_matiere);
        $this->db->where('en.deleted_at', null);
        $this->db->where('e.deleted_at', null);
        $q = $this->db->get();
        if ($q === false) return null;
        return $q->row_array();
    }
}
