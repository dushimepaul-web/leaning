<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_stats()
    {
        $this->id_annee_active = $this->get_active_annee();
        
        return [
            'total_etudiants' => $this->countWhere('etudiants', ['deleted_at' => null]),
            'total_enseignants' => $this->countWhere('enseignants', ['deleted_at' => null]),
            'total_employes' => $this->countWhere('employes', ['deleted_at' => null]),
            'total_classes' => $this->countWhere('classes', ['deleted_at' => null]),
            'total_paiements_mois' => $this->get_paiements_mois(),
            'paiements_en_attente' => $this->get_paiements_en_attente(),
            'inscriptions_annee' => $this->countWhere('inscriptions', ['id_annee' => $this->id_annee_active, 'deleted_at' => null]),
            'stock_alerte' => $this->get_stock_alerte(),
        ];
    }

    public function get_paiements_mois()
    {
        $mois = date('m');
        $annee = date('Y');
        $this->db->where('MONTH(date_paiement)', $mois);
        $this->db->where('YEAR(date_paiement)', $annee);
        $this->db->where('deleted_at', null);
        return $this->db->count_all_results('paiements');
    }

    public function get_paiements_en_attente()
    {
        $this->id_annee_active = $this->get_active_annee();
        $this->db->select('e.id_etudiant, SUM(f.montant) as total_du');
        $this->db->from('etudiants e');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL AND i.id_annee = ' . $this->id_annee_active);
        $this->db->join('frais f', 'f.id_annee = ' . $this->id_annee_active . ' AND f.deleted_at IS NULL', 'left');
        $this->db->join('paiements p', 'p.id_etudiant = e.id_etudiant AND p.id_frais = f.id_frais AND p.deleted_at IS NULL', 'left');
        $this->db->where('e.deleted_at', null);
        $this->db->group_by('e.id_etudiant');
        $this->db->having('COALESCE(SUM(p.montant), 0) < COALESCE(SUM(f.montant), 0)');
        $q = $this->db->get();
        if ($q === false) return 0;
        return $q->num_rows();
    }

    public function get_stock_alerte()
    {
        $this->db->where('stock_actuel <= stock_mini');
        $this->db->where('deleted_at', null);
        return $this->db->count_all_results('produits');
    }

    private function get_active_annee()
    {
        $annee = $this->readOne('annees_scolaires', ['est_en_cours' => 1, 'deleted_at' => null]);
        return $annee ? $annee['id_annee'] : 1;
    }
}