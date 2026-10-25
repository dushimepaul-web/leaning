<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Library extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->load->model('Library_model', 'library_model');
    }

    public function index()
    {
        $data['title'] = 'Librairie - Tableau de bord';
        $this->load->view('dashboard', $data);
    }

    public function api_stats()
    {
        $today = $this->db
            ->select('COUNT(*) AS nb, COALESCE(SUM(total),0) AS total')
            ->where('DATE(date_vente)', date('Y-m-d'))
            ->where_not_in('statut', ['annulee'])
            ->where('deleted_at', null)
            ->get('library_sales')->row_array();

        $month = $this->db
            ->select('COUNT(*) AS nb, COALESCE(SUM(total),0) AS total')
            ->where('YEAR(date_vente)', date('Y'))
            ->where('MONTH(date_vente)', date('m'))
            ->where_not_in('statut', ['annulee'])
            ->where('deleted_at', null)
            ->get('library_sales')->row_array();

        $cogs = $this->db
            ->select('COALESCE(SUM(i.quantite * i.prix_achat),0) AS total')
            ->from('library_sale_items i')
            ->join('library_sales s', 's.id_sale = i.id_sale')
            ->where('YEAR(s.date_vente)', date('Y'))
            ->where('MONTH(s.date_vente)', date('m'))
            ->where_not_in('s.statut', ['annulee'])
            ->where('s.deleted_at', null)
            ->get()->row_array();

        $returns = $this->db
            ->select('COALESCE(SUM(montant_total),0) AS total')
            ->where_in('statut', ['validee', 'remboursee'])
            ->where('YEAR(date_demande)', date('Y'))
            ->where('MONTH(date_demande)', date('m'))
            ->where('deleted_at', null)
            ->get('library_returns')->row_array();

        $expenses = $this->db
            ->select('COALESCE(SUM(montant),0) AS total')
            ->where('YEAR(date_expense)', date('Y'))
            ->where('MONTH(date_expense)', date('m'))
            ->where('deleted_at', null)
            ->get('library_expenses')->row_array();

        $stock = $this->db
            ->select('COALESCE(SUM(stock_actuel * prix_achat),0) AS valeur, COUNT(*) AS produits')
            ->where('deleted_at', null)
            ->get('produits')->row_array();

        $low = $this->db
            ->where('deleted_at', null)
            ->where('stock_actuel <= stock_mini', null, false)
            ->count_all_results('produits');

        $pending = $this->db
            ->where('statut', 'brouillon')->where('deleted_at', null)
            ->count_all_results('library_purchases');

        $unpaid = $this->db
            ->select('COUNT(*) AS nb')
            ->from('library_sales s')
            ->where('s.deleted_at', null)
            ->where_not_in('s.statut', ['annulee', 'retournee', 'payee'])
            ->get()->row_array();

        $recent = $this->db
            ->select("s.uuid, s.numero_vente, s.date_vente, s.total, s.statut,
                COALESCE(e.fullname, s.client_nom, 'Client comptant') AS client")
            ->from('library_sales s')
            ->join('etudiants e', 'e.id_etudiant = s.id_etudiant', 'left')
            ->where('s.deleted_at', null)
            ->order_by('s.date_vente', 'DESC')->order_by('s.id_sale', 'DESC')
            ->limit(8)->get()->result_array();

        $low_products = $this->db
            ->select('id_produit, libelle, stock_actuel, stock_mini, unite')
            ->where('deleted_at', null)
            ->where('stock_actuel <= stock_mini', null, false)
            ->order_by('stock_actuel', 'ASC')
            ->limit(8)->get('produits')->result_array();

        $top = $this->db
            ->select('p.libelle AS produit, SUM(i.quantite) AS quantite, SUM(i.total_ligne) AS montant')
            ->from('library_sale_items i')
            ->join('library_sales s', 's.id_sale = i.id_sale')
            ->join('produits p', 'p.id_produit = i.id_produit', 'left')
            ->where('YEAR(s.date_vente)', date('Y'))
            ->where('MONTH(s.date_vente)', date('m'))
            ->where_not_in('s.statut', ['annulee'])
            ->where('s.deleted_at', null)
            ->group_by('p.id_produit')
            ->order_by('quantite', 'DESC')
            ->limit(5)->get()->result_array();

        $ventes = (float)$month['total'];
        $retours = (float)$returns['total'];
        $cogs_total = (float)$cogs['total'];
        $charges = (float)$expenses['total'];

        $this->json_success([
            'aujourdhui' => ['nb' => (int)$today['nb'], 'total' => (float)$today['total']],
            'mois' => [
                'nb' => (int)$month['nb'],
                'ventes' => $ventes,
                'retours' => $retours,
                'cogs' => $cogs_total,
                'marge_brute' => round($ventes - $retours - $cogs_total, 2),
                'charges' => $charges,
                'benefice_net' => round($ventes - $retours - $cogs_total - $charges, 2),
            ],
            'stock' => ['valeur' => (float)$stock['valeur'], 'produits' => (int)$stock['produits'], 'alertes' => (int)$low],
            'approvisionnements_en_attente' => (int)$pending,
            'ventes_non_soldees' => (int)$unpaid['nb'],
            'recentes' => $recent,
            'alertes_stock' => $low_products,
            'top_produits' => $top,
        ]);
    }
}
