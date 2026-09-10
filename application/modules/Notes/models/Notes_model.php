<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notes_model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function generer_bulletins($id_classe, $id_periode, $id_annee)
    {
        // Logique de génération des bulletins
        // Régénération : soft-delete des bulletins existants pour éviter les doublons
        $this->db->where('id_classe', $id_classe);
        $this->db->where('id_periode', $id_periode);
        $this->db->where('id_annee', $id_annee);
        $this->db->where('deleted_at', null);
        $this->db->update('bulletins', ['deleted_at' => date('Y-m-d H:i:s'), 'modifie_le' => date('Y-m-d H:i:s')]);

        $this->db->where('e.deleted_at', null);
        $this->db->where('i.id_classe', $id_classe);
        $this->db->where('i.id_annee', $id_annee);
        $this->db->where('i.deleted_at', null);
        $this->db->select('e.id_etudiant');
        $this->db->from('etudiants e');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant', 'left');
        $q = $this->db->get();
        $etudiants = $q === false ? array() : $q->result_array();

        $created = 0;
        foreach ($etudiants as $et) {
            $id_etudiant = $et['id_etudiant'];
            
            // Calculer moyennes
            $this->db->where('n.deleted_at', null);
            $this->db->where('n.id_etudiant', $id_etudiant);
            $this->db->join('evaluations ev', 'n.id_evaluation = ev.id_evaluation', 'left');
            $this->db->join('matieres_classes mc', 'mc.id_matiere = ev.id_matiere AND mc.id_classe = ev.id_classe AND mc.deleted_at IS NULL', 'left');
            $this->db->where('ev.id_periode', $id_periode);
            $this->db->where('ev.deleted_at', null);
            $this->db->select('n.note, mc.note_max_matiere, ev.ponderee_sur AS ev_ponderee_sur');
            $q = $this->db->get('notes');
            $notes = $q === false ? array() : $q->result_array();

            if (empty($notes)) continue;

            $somme_ponderee = 0;
            $somme_coef = 0;
            foreach ($notes as $n) {
                $coef = (float)($n['note_max_matiere'] ?? 1);
                $note_max = (float)($n['ev_ponderee_sur'] ?? 20);
                $note = (float)$n['note'];
                $note_sur_20 = $note_max > 0 ? ($note / $note_max) * 20 : 0;
                $somme_ponderee += $note_sur_20 * $coef;
                $somme_coef += $coef;
            }
            $moyenne = $somme_coef > 0 ? round($somme_ponderee / $somme_coef, 2) : 0;
            $admis = floatval($this->get_setting('regle_admis_moy', 12));
            $ajourne = floatval($this->get_setting('regle_ajourne_moy', 10));
            $decision = $moyenne >= $admis ? 'admis' : ($moyenne >= $ajourne ? 'ajourne' : 'echoue');

            $bulletin = [
                'uuid' => generate_uuid(),
                'id_etudiant' => $id_etudiant,
                'id_classe' => $id_classe,
                'id_periode' => $id_periode,
                'id_annee' => $id_annee,
                'moyenne' => $moyenne,
                'rang' => 0,
                'decision' => $decision,
                'date_edition' => date('Y-m-d'),
                'cree_le' => date('Y-m-d H:i:s'),
                'modifie_le' => date('Y-m-d H:i:s'),
            ];

            if ($this->db->insert('bulletins', $bulletin)) {
                $created++;
            }
        }

        // Calculer rangs
        $this->db->where('id_classe', $id_classe);
        $this->db->where('id_periode', $id_periode);
        $this->db->where('id_annee', $id_annee);
        $this->db->order_by('moyenne', 'DESC');
        $q = $this->db->get('bulletins');
        $bulletins = $q === false ? array() : $q->result_array();
        
        $rang = 1;
        $prev_moyenne = null;
        foreach ($bulletins as $b) {
            if ($b['moyenne'] !== $prev_moyenne) {
                $this->db->where('uuid', $b['uuid']);
                $this->db->update('bulletins', ['rang' => $rang]);
                $prev_moyenne = $b['moyenne'];
            }
            $rang++;
        }

        return ['success' => true, 'created' => $created];
    }

    private function _get_appreciation($moyenne)
    {
        // Mentions dynamiques configurées dans Paramètres (seuils en %)
        return get_mention($moyenne, 20);
    }
}