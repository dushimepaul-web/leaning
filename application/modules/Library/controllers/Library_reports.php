<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Library_reports extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->load->model('Library_model', 'library_model');
    }

    public function index()
    {
        $data['title'] = 'Librairie - Rapports';
        $this->load->view('reports', $data);
    }

    public function api_summary()
    {
        $from = $this->input->get('date_from') ?: date('Y-m-01');
        $to = $this->input->get('date_to') ?: date('Y-m-d');

        $sales = $this->db
            ->select('COUNT(*) AS nb, COALESCE(SUM(total),0) AS total, COALESCE(SUM(remise),0) AS remise, COALESCE(SUM(sous_total),0) AS sous_total')
            ->where('DATE(date_vente) >=', $from)
            ->where('DATE(date_vente) <=', $to)
            ->where_not_in('statut', ['annulee'])
            ->where('deleted_at', null)
            ->get('library_sales')->row_array();

        $returns = $this->db
            ->select('COUNT(*) AS nb, COALESCE(SUM(montant_total),0) AS total')
            ->where_in('statut', ['validee', 'remboursee'])
            ->where('DATE(date_demande) >=', $from)
            ->where('DATE(date_demande) <=', $to)
            ->where('deleted_at', null)
            ->get('library_returns')->row_array();

        $cogs = $this->db
            ->select('COALESCE(SUM(i.quantite * i.prix_achat),0) AS total')
            ->from('library_sale_items i')
            ->join('library_sales s', 's.id_sale = i.id_sale')
            ->where('DATE(s.date_vente) >=', $from)
            ->where('DATE(s.date_vente) <=', $to)
            ->where_not_in('s.statut', ['annulee'])
            ->where('s.deleted_at', null)
            ->get()->row_array();

        $expenses = $this->db
            ->select('COALESCE(SUM(montant),0) AS total')
            ->where('date_expense >=', $from)
            ->where('date_expense <=', $to)
            ->where('deleted_at', null)
            ->get('library_expenses')->row_array();

        $stock = $this->db
            ->select('COALESCE(SUM(stock_actuel * prix_achat),0) AS valeur, COALESCE(SUM(stock_actuel),0) AS quantite')
            ->where('deleted_at', null)
            ->get('produits')->row_array();

        $by_day = $this->db
            ->select('DATE(date_vente) AS jour, COUNT(*) AS nb, SUM(total) AS total')
            ->where('DATE(date_vente) >=', $from)
            ->where('DATE(date_vente) <=', $to)
            ->where_not_in('statut', ['annulee'])
            ->where('deleted_at', null)
            ->group_by('DATE(date_vente)')
            ->order_by('jour', 'ASC')
            ->get('library_sales')->result_array();

        $by_product = $this->db
            ->select('p.id_produit, p.libelle AS produit, SUM(i.quantite) AS quantite,
                SUM(i.total_ligne) AS ca, SUM(i.quantite * i.prix_achat) AS cogs')
            ->from('library_sale_items i')
            ->join('library_sales s', 's.id_sale = i.id_sale')
            ->join('produits p', 'p.id_produit = i.id_produit', 'left')
            ->where('DATE(s.date_vente) >=', $from)
            ->where('DATE(s.date_vente) <=', $to)
            ->where_not_in('s.statut', ['annulee'])
            ->where('s.deleted_at', null)
            ->group_by('p.id_produit')
            ->order_by('ca', 'DESC')
            ->get()->result_array();

        $by_user = $this->db
            ->select('COALESCE(u.nom_complet, \'Non renseigne\') AS utilisateur, COUNT(*) AS nb, SUM(s.total) AS total')
            ->from('library_sales s')
            ->join('utilisateurs u', 'u.id_utilisateur = s.id_utilisateur', 'left')
            ->where('DATE(s.date_vente) >=', $from)
            ->where('DATE(s.date_vente) <=', $to)
            ->where_not_in('s.statut', ['annulee'])
            ->where('s.deleted_at', null)
            ->group_by('s.id_utilisateur')
            ->order_by('total', 'DESC')
            ->get()->result_array();

        $by_mode = $this->db
            ->select('mode_paiement, COUNT(*) AS nb, SUM(montant) AS total')
            ->where('DATE(date_paiement) >=', $from)
            ->where('DATE(date_paiement) <=', $to)
            ->where('statut', 'valide')
            ->where('deleted_at', null)
            ->group_by('mode_paiement')
            ->get('library_payments')->result_array();

        $ca = (float)$sales['total'];
        $retours = (float)$returns['total'];
        $cogs_total = (float)$cogs['total'];
        $charges = (float)$expenses['total'];

        $this->json_success([
            'periode' => ['du' => $from, 'au' => $to],
            'resume' => [
                'nb_ventes' => (int)$sales['nb'],
                'chiffre_affaires' => $ca,
                'remises' => (float)$sales['remise'],
                'retours' => $retours,
                'ca_net' => round($ca - $retours, 2),
                'cogs' => $cogs_total,
                'marge_brute' => round($ca - $retours - $cogs_total, 2),
                'charges' => $charges,
                'benefice_net' => round($ca - $retours - $cogs_total - $charges, 2),
                'valeur_stock' => (float)$stock['valeur'],
                'quantite_stock' => (float)$stock['quantite'],
            ],
            'par_jour' => $by_day,
            'par_produit' => $by_product,
            'par_utilisateur' => $by_user,
            'par_mode' => $by_mode,
        ]);
    }
}
