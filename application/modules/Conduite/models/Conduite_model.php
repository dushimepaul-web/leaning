<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Conduite_model extends Model
{
    public function get_eleves_conduite($id_classe, $id_annee, $id_periode)
    {
        if (empty($id_classe) || empty($id_annee) || empty($id_periode)) return array();

        $sql = "
            SELECT
                e.id_etudiant, e.matricule, e.fullname,
                pc.id_point_conduite, pc.uuid AS point_uuid,
                pc.points_initial, pc.points_retires, pc.observation,
                (SELECT COUNT(*) FROM sanctions_conduite sc WHERE sc.id_point_conduite = pc.id_point_conduite) AS nb_sanctions
            FROM inscriptions i
            JOIN etudiants e ON e.id_etudiant = i.id_etudiant AND e.deleted_at IS NULL
            LEFT JOIN points_conduite pc
                ON pc.id_etudiant = e.id_etudiant
                AND pc.id_annee = ?
                AND pc.id_periode = ?
                AND pc.deleted_at IS NULL
            WHERE i.id_classe = ? AND i.id_annee = ? AND i.deleted_at IS NULL
            ORDER BY e.fullname ASC
        ";
        $rows = $this->db->query($sql, array($id_annee, $id_periode, $id_classe, $id_annee))->result_array();

        $points_defaut = $this->get_points_initial_defaut(60);
        foreach ($rows as &$row) {
            $row['points_initial'] = $row['points_initial'] !== null ? floatval($row['points_initial']) : floatval($points_defaut);
            $row['points_retires'] = $row['points_retires'] !== null ? floatval($row['points_retires']) : 0;
            $row['points'] = ($row['points_initial'] !== null) ? $row['points_initial'] - $row['points_retires'] : null;
        }
        unset($row);
        return $rows;
    }

    public function get_point_conduite($id_point_conduite)
    {
        return $this->db->where('id_point_conduite', $id_point_conduite)->get('points_conduite')->row_array();
    }

    public function get_sanctions($id_point_conduite)
    {
        $this->db->where('id_point_conduite', $id_point_conduite);
        $this->db->order_by('date_sanction', 'DESC');
        $this->db->order_by('id_sanction', 'DESC');
        $rows = $this->db->get('sanctions_conduite')->result_array();
        foreach ($rows as &$r) {
            $r['points_retires'] = floatval($r['points_retires']);
        }
        unset($r);
        return $rows;
    }

    public function get_points_initial_defaut($default = 60)
    {
        $v = $this->get_setting('points_conduite_defaut', null);
        return ($v === null || $v === '') ? floatval($default) : floatval($v);
    }

    public function ensure_inscription_points($id_etudiant, $id_annee, $points_initial = null)
    {
        if (empty($id_etudiant) || empty($id_annee)) return 0;
        if ($points_initial === null) {
            $points_initial = $this->get_points_initial_defaut();
        }
        $this->load->helper('uuid');
        $periodes = $this->db
            ->where('id_annee', $id_annee)
            ->where('deleted_at', null)
            ->order_by('id_periode', 'ASC')
            ->get('periodes')
            ->result_array();

        $created = 0;
        foreach ($periodes as $p) {
            $existing = $this->db
                ->where('id_etudiant', $id_etudiant)
                ->where('id_annee', $id_annee)
                ->where('id_periode', $p['id_periode'])
                ->where('deleted_at', null)
                ->get('points_conduite')
                ->row_array();
            if ($existing) continue;
            $this->db->insert('points_conduite', array(
                'uuid' => generate_uuid(),
                'id_etudiant' => $id_etudiant,
                'id_annee' => $id_annee,
                'id_periode' => $p['id_periode'],
                'points_initial' => $points_initial,
                'points_retires' => 0,
            ));
            $created++;
        }
        return $created;
    }

    public function recalc_points($id_point_conduite)
    {
        $row = $this->db
            ->select('COALESCE(SUM(points_retires),0) AS total')
            ->where('id_point_conduite', $id_point_conduite)
            ->get('sanctions_conduite')
            ->row_array();
        $total = $row ? floatval($row['total']) : 0;
        $this->db->where('id_point_conduite', $id_point_conduite);
        $this->db->update('points_conduite', array('points_retires' => $total));
        return $total;
    }

    public function initialiser_classe($id_classe, $id_annee, $id_periode, $points_initial = null)
    {
        if ($points_initial === null) {
            $points_initial = $this->get_points_initial_defaut();
        }
        $this->load->helper('uuid');
        $eleves = $this->db
            ->select('e.id_etudiant')
            ->from('inscriptions i')
            ->join('etudiants e', 'e.id_etudiant = i.id_etudiant AND e.deleted_at IS NULL')
            ->where('i.id_classe', $id_classe)
            ->where('i.id_annee', $id_annee)
            ->where('i.deleted_at', null)
            ->get()->result_array();

        $created = 0;
        $updated = 0;
        foreach ($eleves as $el) {
            $existing = $this->db
                ->where('id_etudiant', $el['id_etudiant'])
                ->where('id_annee', $id_annee)
                ->where('id_periode', $id_periode)
                ->where('deleted_at', null)
                ->get('points_conduite')
                ->row_array();
            if ($existing) {
                $this->db->where('id_point_conduite', $existing['id_point_conduite']);
                $this->db->update('points_conduite', array('points_initial' => $points_initial));
                $updated++;
            } else {
                $this->db->insert('points_conduite', array(
                    'uuid' => generate_uuid(),
                    'id_etudiant' => $el['id_etudiant'],
                    'id_annee' => $id_annee,
                    'id_periode' => $id_periode,
                    'points_initial' => $points_initial,
                    'points_retires' => 0,
                ));
                $created++;
            }
        }
        return array('created' => $created, 'updated' => $updated, 'total' => count($eleves));
    }
}
