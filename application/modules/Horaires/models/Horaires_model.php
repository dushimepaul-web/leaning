<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Horaires_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_creneaux_cours()
    {
        $params = $this->_get_params();
        return $this->_compute_creneaux($params);
    }

    public function get_creneaux_mardi()
    {
        $params = $this->_get_params();
        return $this->_compute_creneaux_mardi($params);
    }

    private function _get_params()
    {
        $rows = $this->db->select('clef, valeur')
            ->where('deleted_at', null)
            ->get('parametres')
            ->result_array();
        $map = [];
        foreach ($rows as $r) {
            if (!empty($r['clef'])) {
                $map[$r['clef']] = $r['valeur'];
            }
        }
        return $map;
    }

    private function _compute_creneaux($params)
    {
        $nb = intval($params['nb_creneaux_jour'] ?? 8);
        $heure_debut = $params['heure_debut_journee'] ?? '07:30';
        $duree_cours = intval($params['duree_cours'] ?? 45);
        $duree_pause = intval($params['duree_pause'] ?? 20);
        $duree_vigie = intval($params['duree_vigie'] ?? 10);

        $h = intval(substr($heure_debut, 0, 2));
        $m = intval(substr($heure_debut, 3, 2));
        $pause_after = intdiv($nb, 2);
        $ordre = 0;
        $creneaux = [];

        for ($i = 1; $i <= $nb; $i++) {
            if ($duree_vigie > 0 && $i === 1) {
                $debut = sprintf('%02d:%02d', $h, $m);
                $m += $duree_vigie;
                $h += intdiv($m, 60);
                $m = $m % 60;
                $fin = sprintf('%02d:%02d', $h, $m);
                $ordre++;
                $creneaux[] = [
                    'id_creneau' => 'vigile',
                    'type' => 'vigile',
                    'type_creneau' => 'vigile',
                    'heure_debut' => $debut,
                    'heure_fin' => $fin,
                    'libelle' => 'Salut du drapeau',
                    'ordre' => $ordre,
                ];
            }

            $debut = sprintf('%02d:%02d', $h, $m);
            $m += $duree_cours;
            $h += intdiv($m, 60);
            $m = $m % 60;
            $fin = sprintf('%02d:%02d', $h, $m);
            $ordre++;
            $creneaux[] = [
                'id_creneau' => $i,
                'type' => 'cours',
                'type_creneau' => 'cours',
                'heure_debut' => $debut,
                'heure_fin' => $fin,
                'libelle' => "Cours $i",
                'ordre' => $ordre,
            ];

            if ($i === $pause_after && $i < $nb) {
                $debut = sprintf('%02d:%02d', $h, $m);
                $m += $duree_pause;
                $h += intdiv($m, 60);
                $m = $m % 60;
                $fin = sprintf('%02d:%02d', $h, $m);
                $ordre++;
                $creneaux[] = [
                    'id_creneau' => "pause$i",
                    'type' => 'pause',
                    'type_creneau' => 'pause',
                    'heure_debut' => $debut,
                    'heure_fin' => $fin,
                    'libelle' => 'Pause',
                    'ordre' => $ordre,
                ];
            }
        }

        return $creneaux;
    }

    public function get_horaires_by_enseignant($id_enseignant)
    {
        $creneaux = $this->get_creneaux_cours();
        $creneaux_map = [];
        foreach ($creneaux as $c) {
            if (($c['type'] ?? '') === 'cours') {
                $creneaux_map[$c['id_creneau']] = $c;
            }
        }

        $rows = $this->db->select('h.*, m.libelle as matiere_libelle, m.code as matiere_code,
                c.libelle as classe_libelle, j.libelle as jour_libelle,
                j.code as jour_code, j.ordre as jour_ordre')
            ->from('horaires h')
            ->join('matieres m', 'h.id_matiere = m.id_matiere', 'left')
            ->join('classes c', 'h.id_classe = c.id_classe', 'left')
            ->join('jours_semaine j', 'h.id_jour = j.id_jour', 'left')
            ->where('h.id_enseignant', $id_enseignant)
            ->where('h.deleted_at', null)
            ->order_by('j.ordre ASC, h.id_creneau ASC')
            ->get()
            ->result_array();

        $by_jour = [];
        foreach ($rows as &$h) {
            $c = $creneaux_map[$h['id_creneau']] ?? null;
            $h['heure_debut'] = $c['heure_debut'] ?? '';
            $h['heure_fin'] = $c['heure_fin'] ?? '';
            $h['creneau_libelle'] = $c['libelle'] ?? 'Cours ' . $h['id_creneau'];
            $by_jour[$h['id_jour']][] = $h;
        }
        unset($h);

        return ['horaires' => $rows, 'by_jour' => $by_jour];
    }

    private function _compute_creneaux_mardi($params)
    {
        $nb = intval($params['nb_creneaux_jour'] ?? 8);
        $heure_debut = $params['heure_debut_journee'] ?? '07:30';
        $duree_cours = intval($params['duree_cours'] ?? 45);
        $duree_culte = intval($params['duree_culte'] ?? 35);
        $duree_vigie = intval($params['duree_vigie'] ?? 10);

        $h = intval(substr($heure_debut, 0, 2));
        $m = intval(substr($heure_debut, 3, 2));
        $pause_after = intdiv($nb, 2);
        $ordre = 0;
        $creneaux = [];

        for ($i = 1; $i <= $nb; $i++) {
            if ($duree_vigie > 0 && $i === 1) {
                $debut = sprintf('%02d:%02d', $h, $m);
                $m += $duree_vigie;
                $h += intdiv($m, 60);
                $m = $m % 60;
                $fin = sprintf('%02d:%02d', $h, $m);
                $ordre++;
                $creneaux[] = [
                    'id_creneau' => 'vigile',
                    'type' => 'vigile',
                    'type_creneau' => 'vigile',
                    'heure_debut' => $debut,
                    'heure_fin' => $fin,
                    'libelle' => 'Salut du drapeau',
                    'ordre' => $ordre,
                ];
            }

            $debut = sprintf('%02d:%02d', $h, $m);
            $m += $duree_cours;
            $h += intdiv($m, 60);
            $m = $m % 60;
            $fin = sprintf('%02d:%02d', $h, $m);
            $ordre++;
            $creneaux[] = [
                'id_creneau' => $i,
                'type' => 'cours',
                'type_creneau' => 'cours',
                'heure_debut' => $debut,
                'heure_fin' => $fin,
                'libelle' => "Cours $i",
                'ordre' => $ordre,
            ];

            if ($i === $pause_after && $i < $nb) {
                $debut = sprintf('%02d:%02d', $h, $m);
                $m += $duree_culte;
                $h += intdiv($m, 60);
                $m = $m % 60;
                $fin = sprintf('%02d:%02d', $h, $m);
                $ordre++;
                $creneaux[] = [
                    'id_creneau' => "culte$i",
                    'type' => 'culte',
                    'type_creneau' => 'culte',
                    'heure_debut' => $debut,
                    'heure_fin' => $fin,
                    'libelle' => 'Culte',
                    'ordre' => $ordre,
                ];
            }
        }

        return $creneaux;
    }
}
