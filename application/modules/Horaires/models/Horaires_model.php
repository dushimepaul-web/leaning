<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Horaires_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all($filters = [])
    {
        $this->db->where('h.deleted_at', null);
        $this->db->select('h.*, j.libelle as jour, c.libelle as creneau, c.heure_debut, c.heure_fin, cl.libelle as classe, e.fullname as enseignant, m.libelle as matiere, m.code as matiere_code');
        $this->db->from('horaires h');
        $this->db->join('jours_semaine j', 'h.id_jour = j.id_jour', 'left');
        $this->db->join('creneaux c', 'h.id_creneau = c.id_creneau', 'left');
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
        return $q->result_array();
    }

    public function get_jours_actifs()
    {
        $q = $this->db->where('actif', 1)->order_by('ordre')->get('jours_semaine');
        return $q !== false ? $q->result_array() : [];
    }

    public function get_creneaux_cours()
    {
        $q = $this->db->where('type_creneau', 'cours')->order_by('ordre')->get('creneaux');
        return $q !== false ? $q->result_array() : [];
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
