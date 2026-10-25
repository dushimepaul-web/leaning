<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Library_payments extends MY_Controller
{
    private $modes = ['especes', 'banque', 'mobile_money', 'cheque', 'autre'];

    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->load->model('Library_model', 'library_model');
        $this->load->helper('uuid');
    }

    public function index()
    {
        $data['title'] = 'Librairie - Paiements';
        $this->load->view('payments', $data);
    }

    public function api_list()
    {
        $this->db->where('p.deleted_at', null);
        $this->db->select("p.*, s.numero_vente, s.total, s.statut AS statut_vente,
            COALESCE(e.fullname, s.client_nom, 'Client comptant') AS client,
            u.nom_complet AS caissier");
        $this->db->from('library_payments p');
        $this->db->join('library_sales s', 's.id_sale = p.id_sale', 'left');
        $this->db->join('etudiants e', 'e.id_etudiant = s.id_etudiant', 'left');
        $this->db->join('utilisateurs u', 'u.id_utilisateur = p.id_utilisateur', 'left');
        if ($this->input->get('date_from')) $this->db->where('DATE(p.date_paiement) >=', $this->input->get('date_from'));
        if ($this->input->get('date_to')) $this->db->where('DATE(p.date_paiement) <=', $this->input->get('date_to'));
        if ($this->input->get('mode')) $this->db->where('p.mode_paiement', $this->input->get('mode'));
        $this->db->order_by('p.date_paiement', 'DESC')->order_by('p.id_payment', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : []);
    }

    public function api_sales()
    {
        $this->db->select("s.id_sale, s.uuid, s.numero_vente, s.total, s.statut,
            COALESCE(e.fullname, s.client_nom, 'Client comptant') AS client,
            (s.total - COALESCE((SELECT SUM(p.montant) FROM library_payments p WHERE p.id_sale = s.id_sale AND p.statut = 'valide' AND p.deleted_at IS NULL), 0)) AS solde");
        $this->db->from('library_sales s');
        $this->db->join('etudiants e', 'e.id_etudiant = s.id_etudiant', 'left');
        $this->db->where('s.deleted_at', null);
        $this->db->where_not_in('s.statut', ['annulee', 'retournee']);
        $this->db->having('solde >', 0.009);
        $this->db->order_by('s.date_vente', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : []);
    }

    public function api_create()
    {
        $data = $this->get_json_input();
        $uuid = trim($data['uuid'] ?? '');
        $montant = isset($data['montant']) ? (float)$data['montant'] : 0;
        $mode = $data['mode_paiement'] ?? 'especes';
        if ($uuid === '' || $montant <= 0) { $this->json_error('Vente et montant valides requis'); return; }
        if (!in_array($mode, $this->modes, true)) { $this->json_error('Mode de paiement invalide'); return; }
        $sale = $this->db->get_where('library_sales', ['uuid' => $uuid, 'deleted_at' => null])->row_array();
        if (!$sale) { $this->json_error('Vente introuvable', 404); return; }
        if (in_array($sale['statut'], ['annulee', 'retournee'], true)) { $this->json_error('Cette vente est annulee ou retournee'); return; }
        $solde = $this->library_model->sale_balance($sale['id_sale']);
        if ($solde === null) { $this->json_error('Vente introuvable'); return; }
        if ($montant > $solde + 0.009) { $this->json_error('Le montant depasse le solde restant (' . number_format($solde, 2) . ')'); return; }

        $this->db->trans_begin();
        $id = $this->Model->createLastId('library_payments', [
            'id_sale' => $sale['id_sale'],
            'montant' => round($montant, 2),
            'mode_paiement' => $mode,
            'reference' => trim($data['reference'] ?? '') ?: null,
            'date_paiement' => !empty($data['date_paiement']) ? $data['date_paiement'] : date('Y-m-d H:i:s'),
            'statut' => 'valide',
            'notes' => trim($data['notes'] ?? '') ?: null,
            'id_utilisateur' => $this->session->userdata('id_utilisateur') ?: null,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        if (!$id) { $this->db->trans_rollback(); $this->json_error('Erreur lors de l\'enregistrement'); return; }
        $this->library_model->refresh_sale_status($sale['id_sale']);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de l\'enregistrement'); return;
        }
        $this->db->trans_commit();
        $this->library_model->audit('create', 'library_payments', $id, null, ['id_sale' => $sale['id_sale'], 'montant' => $montant, 'mode' => $mode]);
        $this->json_success(['id_payment' => $id, 'solde' => $this->library_model->sale_balance($sale['id_sale'])], 'Paiement enregistre');
    }

    public function api_delete($uuid)
    {
        $payment = $this->db->get_where('library_payments', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$payment) { $this->json_error('Paiement introuvable', 404); return; }
        if ($payment['statut'] === 'annule') { $this->json_error('Paiement deja annule'); return; }
        $this->db->trans_begin();
        $this->db->where('id_payment', $payment['id_payment'])->update('library_payments', [
            'statut' => 'annule',
            'deleted_at' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        $this->library_model->refresh_sale_status($payment['id_sale']);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de l\'annulation'); return;
        }
        $this->db->trans_commit();
        $this->library_model->audit('cancel', 'library_payments', $payment['id_payment'], $payment, ['statut' => 'annule']);
        $this->json_success(null, 'Paiement annule');
    }
}
