<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Library_price_history extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->load->model('Library_model', 'library_model');
    }

    public function index()
    {
        $data['title'] = 'Librairie - Historique des prix';
        $this->load->view('price_history', $data);
    }

    public function api_list()
    {
        $this->db->select('h.*, p.libelle AS produit, p.unite, u.nom_complet AS utilisateur');
        $this->db->from('library_price_history h');
        $this->db->join('produits p', 'p.id_produit = h.id_produit', 'left');
        $this->db->join('utilisateurs u', 'u.id_utilisateur = h.id_utilisateur', 'left');
        if ($this->input->get('id_produit')) $this->db->where('h.id_produit', (int)$this->input->get('id_produit'));
        if ($this->input->get('source')) $this->db->where('h.source', $this->input->get('source'));
        if ($this->input->get('date_from')) $this->db->where('DATE(h.date_changement) >=', $this->input->get('date_from'));
        if ($this->input->get('date_to')) $this->db->where('DATE(h.date_changement) <=', $this->input->get('date_to'));
        $this->db->order_by('h.date_changement', 'DESC')->order_by('h.id_price', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }
}
