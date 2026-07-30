<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rapports extends MY_Controller {
    public function __construct() { parent::__construct(); $this->not_logged_in(); }

    public function index() {
        $data['title'] = 'Rapports';
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['sections'] = $this->Model->read('sections', ['deleted_at' => null]);
        $data['annees'] = $this->Model->read('annees_scolaires', [], 'id_annee', 'DESC');
        $this->render_view('index', $data);
    }

    public function api_eleves_par_classe() {
        $this->db->select('c.libelle as classe, COUNT(DISTINCT i.id_etudiant) as total');
        $this->db->from('classes c');
        $this->db->join('inscriptions i', 'i.id_classe = c.id_classe AND i.deleted_at IS NULL AND i.id_annee = '.(int)$this->id_annee_active, 'left');
        $this->db->where('c.deleted_at', null);
        $this->db->group_by('c.id_classe');
        $this->db->order_by('c.libelle');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_eleves_par_section() {
        $this->db->select('s.libelle as section, COUNT(DISTINCT i.id_etudiant) as total');
        $this->db->from('sections s');
        $this->db->join('classes c', 'c.id_section = s.id_section AND c.deleted_at IS NULL', 'left');
        $this->db->join('inscriptions i', 'i.id_classe = c.id_classe AND i.deleted_at IS NULL AND i.id_annee = '.(int)$this->id_annee_active, 'left');
        $this->db->where('s.deleted_at', null);
        $this->db->group_by('s.id_section');
        $this->db->order_by('s.libelle');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_paiements_par_classe() {
        $this->db->select('c.libelle as classe, COUNT(p.id_paiement) as total, COALESCE(SUM(p.montant), 0) as montant_total');
        $this->db->from('classes c');
        $this->db->join('inscriptions i', 'i.id_classe = c.id_classe AND i.deleted_at IS NULL AND i.id_annee = '.(int)$this->id_annee_active, 'left');
        $this->db->join('paiements p', 'p.id_etudiant = i.id_etudiant AND p.deleted_at IS NULL AND p.id_annee = '.(int)$this->id_annee_active, 'left');
        $this->db->where('c.deleted_at', null);
        $this->db->group_by('c.id_classe');
        $this->db->order_by('c.libelle');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_paiements_par_section() {
        $this->db->select('s.libelle as section, COUNT(p.id_paiement) as total, COALESCE(SUM(p.montant), 0) as montant_total');
        $this->db->from('sections s');
        $this->db->join('classes c', 'c.id_section = s.id_section AND c.deleted_at IS NULL', 'left');
        $this->db->join('inscriptions i', 'i.id_classe = c.id_classe AND i.deleted_at IS NULL AND i.id_annee = '.(int)$this->id_annee_active, 'left');
        $this->db->join('paiements p', 'p.id_etudiant = i.id_etudiant AND p.deleted_at IS NULL AND p.id_annee = '.(int)$this->id_annee_active, 'left');
        $this->db->where('s.deleted_at', null);
        $this->db->group_by('s.id_section');
        $this->db->order_by('s.libelle');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_paiements_statuts() {
        $soldes = $this->Model->countWhere('paiements', ['statut' => 'solde', 'deleted_at' => null, 'id_annee' => $this->id_annee_active]);
        $partiels = $this->Model->countWhere('paiements', ['statut' => 'partiel', 'deleted_at' => null, 'id_annee' => $this->id_annee_active]);
        $annules = $this->Model->countWhere('paiements', ['statut' => 'annule', 'deleted_at' => null, 'id_annee' => $this->id_annee_active]);
        $this->json_success([
            ['statut' => 'Soldés', 'total' => (int)$soldes],
            ['statut' => 'Partiels', 'total' => (int)$partiels],
            ['statut' => 'Annulés', 'total' => (int)$annules]
        ]);
    }

    public function api_produits_par_classe() {
        $this->db->select('c.libelle as classe, COUNT(DISTINCT cd.id_produit) as produits_count, COALESCE(SUM(cd.quantite), 0) as quantite_totale');
        $this->db->from('classes c');
        $this->db->join('inscriptions i', 'i.id_classe = c.id_classe AND i.deleted_at IS NULL AND i.id_annee = ' . (int)$this->id_annee_active, 'left');
        $this->db->join('commandes cmd', 'cmd.id_etudiant = i.id_etudiant AND cmd.deleted_at IS NULL', 'left');
        $this->db->join('commandes_details cd', 'cd.id_commande = cmd.id_commande', 'left');
        $this->db->where('c.deleted_at', null);
        $this->db->group_by('c.id_classe');
        $this->db->order_by('c.libelle');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_produits_par_section() {
        $this->db->select('s.libelle as section, COUNT(DISTINCT cd.id_produit) as produits_count, COALESCE(SUM(cd.quantite), 0) as quantite_totale');
        $this->db->from('sections s');
        $this->db->join('classes c', 'c.id_section = s.id_section AND c.deleted_at IS NULL', 'left');
        $this->db->join('inscriptions i', 'i.id_classe = c.id_classe AND i.deleted_at IS NULL AND i.id_annee = ' . (int)$this->id_annee_active, 'left');
        $this->db->join('commandes cmd', 'cmd.id_etudiant = i.id_etudiant AND cmd.deleted_at IS NULL', 'left');
        $this->db->join('commandes_details cd', 'cd.id_commande = cmd.id_commande', 'left');
        $this->db->where('s.deleted_at', null);
        $this->db->group_by('s.id_section');
        $this->db->order_by('s.libelle');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_consommation_stock() {
        $this->db->select('p.libelle, p.stock_mini, p.stock_actuel, c.libelle as categorie');
        $this->db->from('produits p');
        $this->db->join('categories_produits c', 'p.id_categorie = c.id_categorie', 'left');
        $this->db->where('p.deleted_at', null);
        $this->db->order_by('p.stock_actuel', 'ASC');
        $this->db->limit(10);
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_ventes_par_mois() {
        $annee = $this->input->get('annee') ?: date('Y');
        $this->db->select("DATE_FORMAT(c.date_commande, '%Y-%m') as mois, COUNT(DISTINCT c.id_commande) as nb_commandes, SUM(c.total) as total_ventes, COALESCE(SUM((cd.prix_unitaire - cd.prix_achat) * cd.quantite), 0) as benefice");
        $this->db->from('commandes c');
        $this->db->join('commandes_details cd', 'cd.id_commande = c.id_commande', 'left');
        $this->db->where('c.deleted_at', null);
        $this->db->where("c.statut !=", 'annulee');
        $this->db->where("DATE_FORMAT(c.date_commande, '%Y')", $annee);
        $this->db->group_by("DATE_FORMAT(c.date_commande, '%Y-%m')");
        $this->db->order_by('mois', 'ASC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_achats_par_mois() {
        $annee = $this->input->get('annee') ?: date('Y');
        $this->db->select("DATE_FORMAT(m.date_mvt, '%Y-%m') as mois, COUNT(*) as nb_achats, SUM(m.quantite * COALESCE(m.prix_unitaire, 0)) as total_achats");
        $this->db->from('mouvements_stock m');
        $this->db->where('m.type', 'entree');
        $this->db->where("DATE_FORMAT(m.date_mvt, '%Y')", $annee);
        $this->db->group_by("DATE_FORMAT(m.date_mvt, '%Y-%m')");
        $this->db->order_by('mois', 'ASC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_resume_ventes() {
        $this->db->select("COALESCE(SUM(c.total), 0) as ca_total, COALESCE(SUM((cd.prix_unitaire - cd.prix_achat) * cd.quantite), 0) as benefice_total");
        $this->db->from('commandes c');
        $this->db->join('commandes_details cd', 'cd.id_commande = c.id_commande', 'left');
        $this->db->where('c.deleted_at', null);
        $this->db->where("c.statut !=", 'annulee');
        $q = $this->db->get();
        $ventes = $q !== false ? $q->row_array() : ['ca_total' => 0, 'benefice_total' => 0];

        $this->db->select("COALESCE(SUM(m.quantite * COALESCE(m.prix_unitaire, 0)), 0) as achats_total");
        $this->db->from('mouvements_stock m');
        $this->db->where('m.type', 'entree');
        $q2 = $this->db->get();
        $achats = $q2 !== false ? $q2->row_array() : ['achats_total' => 0];

        $this->json_success(array_merge($ventes, $achats));
    }
}
