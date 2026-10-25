<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Library_sales extends MY_Controller
{
    private $modes = ['especes', 'banque', 'mobile_money', 'cheque', 'credit'];

    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->load->model('Library_model', 'library_model');
        $this->load->helper('uuid');
    }

    public function index()
    {
        $data['title'] = 'Librairie - Ventes';
        $data['etudiants'] = $this->Model->read('etudiants', ['deleted_at' => null]);
        $this->load->view('sales', $data);
    }

    public function api_list()
    {
        $this->db->where('s.deleted_at', null);
        $this->db->select("s.*, COALESCE(e.fullname, s.client_nom, 'Client comptant') AS client,
            e.matricule, u.nom_complet AS caissier,
            COALESCE((SELECT SUM(p.montant) FROM library_payments p WHERE p.id_sale = s.id_sale AND p.statut = 'valide' AND p.deleted_at IS NULL), 0) AS paye,
            (s.total - COALESCE((SELECT SUM(p.montant) FROM library_payments p WHERE p.id_sale = s.id_sale AND p.statut = 'valide' AND p.deleted_at IS NULL), 0)) AS solde,
            (SELECT COUNT(*) FROM library_sale_items i WHERE i.id_sale = s.id_sale) AS nb_lignes");
        $this->db->from('library_sales s');
        $this->db->join('etudiants e', 'e.id_etudiant = s.id_etudiant', 'left');
        $this->db->join('utilisateurs u', 'u.id_utilisateur = s.id_utilisateur', 'left');
        if ($this->input->get('statut')) $this->db->where('s.statut', $this->input->get('statut'));
        if ($this->input->get('date_from')) $this->db->where('DATE(s.date_vente) >=', $this->input->get('date_from'));
        if ($this->input->get('date_to')) $this->db->where('DATE(s.date_vente) <=', $this->input->get('date_to'));
        $this->db->order_by('s.date_vente', 'DESC')->order_by('s.id_sale', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : []);
    }

    public function api_get($uuid)
    {
        $this->db->select("s.*, COALESCE(e.fullname, s.client_nom, 'Client comptant') AS client, e.matricule,
            u.nom_complet AS caissier");
        $this->db->from('library_sales s');
        $this->db->join('etudiants e', 'e.id_etudiant = s.id_etudiant', 'left');
        $this->db->join('utilisateurs u', 'u.id_utilisateur = s.id_utilisateur', 'left');
        $this->db->where('s.uuid', (string)$uuid)->where('s.deleted_at', null);
        $row = $this->db->get()->row_array();
        if (!$row) { $this->json_error('Vente introuvable', 404); return; }
        $row['items'] = $this->db
            ->select('i.*, pr.libelle AS produit, pr.unite')
            ->from('library_sale_items i')
            ->join('produits pr', 'pr.id_produit = i.id_produit', 'left')
            ->where('i.id_sale', $row['id_sale'])
            ->get()->result_array();
        $row['payments'] = $this->db
            ->select('p.*, u.nom_complet AS utilisateur')
            ->from('library_payments p')
            ->join('utilisateurs u', 'u.id_utilisateur = p.id_utilisateur', 'left')
            ->where('p.id_sale', $row['id_sale'])
            ->where('p.deleted_at', null)
            ->order_by('p.date_paiement', 'ASC')
            ->get()->result_array();
        $row['returns'] = $this->db
            ->where('id_sale', $row['id_sale'])->where('deleted_at', null)
            ->order_by('date_demande', 'DESC')->get('library_returns')->result_array();
        $row['paye'] = $this->library_model->sale_paid($row['id_sale']);
        $row['solde'] = round((float)$row['total'] - $row['paye'], 2);
        $this->json_success($row);
    }

    public function api_create()
    {
        $data = $this->get_json_input();
        $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : [];
        if (empty($items)) { $this->json_error('Ajoutez au moins un produit'); return; }
        $mode = $data['mode_paiement'] ?? 'especes';
        if (!in_array($mode, $this->modes, true)) { $this->json_error('Mode de paiement invalide'); return; }
        $id_etudiant = !empty($data['id_etudiant']) ? (int)$data['id_etudiant'] : null;
        if ($id_etudiant) {
            $etu = $this->db->get_where('etudiants', ['id_etudiant' => $id_etudiant, 'deleted_at' => null])->row_array();
            if (!$etu) { $this->json_error('Eleve introuvable'); return; }
        }
        $client_nom = trim($data['client_nom'] ?? '');
        if (!$id_etudiant && $client_nom === '') $client_nom = 'Client comptant';

        $lines = [];
        $need = [];
        $sous_total = 0;
        foreach ($items as $item) {
            $id_produit = (int)($item['id_produit'] ?? 0);
            $qty = (int)($item['quantite'] ?? 0);
            if ($id_produit <= 0 || $qty <= 0) { $this->json_error('Ligne invalide : produit et quantite requis'); return; }
            $produit = $this->db->get_where('produits', ['id_produit' => $id_produit, 'deleted_at' => null])->row_array();
            if (!$produit) { $this->json_error('Produit introuvable'); return; }
            $price = isset($item['prix_unitaire']) && $item['prix_unitaire'] !== '' ? (float)$item['prix_unitaire'] : (float)$produit['prix_unitaire'];
            $line_discount = isset($item['remise']) && $item['remise'] !== '' ? (float)$item['remise'] : 0;
            if ($price < 0 || $line_discount < 0 || $line_discount > $qty * $price) {
                $this->json_error('Prix ou remise invalide pour "' . $produit['libelle'] . '"'); return;
            }
            $total_ligne = round($qty * $price - $line_discount, 2);
            $sous_total += $total_ligne;
            $need[$id_produit] = ($need[$id_produit] ?? 0) + $qty;
            $lines[] = [
                'id_produit' => $id_produit,
                'quantite' => $qty,
                'prix_unitaire' => $price,
                'prix_achat' => (float)$produit['prix_achat'],
                'remise' => $line_discount,
                'total_ligne' => $total_ligne,
                'libelle' => $produit['libelle'],
            ];
        }
        $shortages = [];
        foreach ($need as $pid => $qty) {
            $row = $this->db->select('libelle, stock_actuel')->get_where('produits', ['id_produit' => $pid])->row_array();
            if (!$row) continue;
            $stock = (int)$row['stock_actuel'];
            if ($stock < $qty) {
                $shortages[] = '« ' . $row['libelle'] . ' » : disponible ' . $stock . ', demandé ' . $qty;
            }
        }
        if ($shortages) {
            $this->json_error('Stock insuffisant. ' . implode(' ; ', $shortages));
            return;
        }
        $remise = isset($data['remise']) && $data['remise'] !== '' ? (float)$data['remise'] : 0;
        if ($remise < 0 || $remise > $sous_total) { $this->json_error('Remise invalide'); return; }
        $total = round($sous_total - $remise, 2);
        $montant_paye = isset($data['montant_paye']) && $data['montant_paye'] !== '' ? (float)$data['montant_paye'] : ($mode === 'credit' ? 0 : $total);
        if ($montant_paye < 0 || $montant_paye > $total) { $this->json_error('Montant paye invalide'); return; }
        if ($mode === 'credit' && $montant_paye > 0) { $this->json_error('Le mode credit ne doit pas encaisser un montant initial'); return; }
        $id_user = $this->session->userdata('id_utilisateur') ?: null;

        $this->db->trans_begin();
        $id = $this->Model->createLastId('library_sales', [
            'numero_vente' => $this->library_model->next_number('VNT', 'library_sales', 'numero_vente'),
            'id_etudiant' => $id_etudiant,
            'client_nom' => $client_nom ?: null,
            'client_telephone' => trim($data['client_telephone'] ?? '') ?: null,
            'date_vente' => !empty($data['date_vente']) ? $data['date_vente'] : date('Y-m-d H:i:s'),
            'sous_total' => $sous_total,
            'remise' => $remise,
            'total' => $total,
            'mode_paiement' => $mode,
            'statut' => 'en_attente',
            'notes' => trim($data['notes'] ?? '') ?: null,
            'id_utilisateur' => $id_user,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        if (!$id) { $this->db->trans_rollback(); $this->json_error('Erreur lors de la creation'); return; }

        foreach ($lines as $line) {
            $this->db->insert('library_sale_items', [
                'uuid' => generate_uuid(),
                'id_sale' => $id,
                'id_produit' => $line['id_produit'],
                'quantite' => $line['quantite'],
                'prix_unitaire' => $line['prix_unitaire'],
                'prix_achat' => $line['prix_achat'],
                'remise' => $line['remise'],
                'total_ligne' => $line['total_ligne'],
                'cree_le' => date('Y-m-d H:i:s'),
            ]);
        }
        if (!$this->_apply_sale_stock($id, $lines, $id_etudiant, $id_user)) {
            $this->db->trans_rollback();
            $this->json_error('Stock insuffisant ou erreur lors de la sortie de stock'); return;
        }
        if ($montant_paye > 0) {
            $this->db->insert('library_payments', [
                'uuid' => generate_uuid(),
                'id_sale' => $id,
                'montant' => $montant_paye,
                'mode_paiement' => $mode,
                'reference' => trim($data['reference_paiement'] ?? '') ?: null,
                'date_paiement' => date('Y-m-d H:i:s'),
                'statut' => 'valide',
                'id_utilisateur' => $id_user,
                'cree_le' => date('Y-m-d H:i:s'),
                'modifie_le' => date('Y-m-d H:i:s'),
            ]);
        }
        $statut = $total <= 0 || $montant_paye >= $total ? 'payee' : ($montant_paye > 0 ? 'partielle' : ($mode === 'credit' ? 'credit' : 'en_attente'));
        $this->db->where('id_sale', $id)->update('library_sales', ['statut' => $statut]);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de l\'enregistrement de la vente'); return;
        }
        $this->db->trans_commit();
        $this->library_model->audit('create', 'library_sales', $id, null, ['total' => $total, 'statut' => $statut, 'lignes' => count($lines)]);
        $this->json_success(['id_sale' => $id, 'statut' => $statut], 'Vente enregistree');
    }

    public function api_update($uuid)
    {
        $data = $this->get_json_input();
        $sale = $this->db->get_where('library_sales', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$sale) { $this->json_error('Vente introuvable', 404); return; }
        $new = $data['statut'] ?? null;
        if ($new !== 'annulee') { $this->json_error('Seule l\'annulation est autorisee'); return; }
        if ($sale['statut'] === 'annulee') { $this->json_success(null, 'Vente deja annulee'); return; }
        $returned = $this->db->where('id_sale', $sale['id_sale'])->where_in('statut', ['validee', 'remboursee'])->where('deleted_at', null)->count_all_results('library_returns');
        if ($returned > 0) { $this->json_error('Annulation impossible : des retours ont deja ete valides.'); return; }

        $this->db->trans_begin();
        $items = $this->db->get_where('library_sale_items', ['id_sale' => $sale['id_sale']])->result_array();
        foreach ($items as $item) {
            if (!$this->library_model->move_stock($item['id_produit'], 'entree', $item['quantite'], $item['prix_unitaire'], $item['prix_achat'], 'Annulation vente ' . $sale['numero_vente'], 'annulation', $sale['id_sale'], $sale['id_etudiant'])) {
                $this->db->trans_rollback();
                $this->json_error('Erreur lors de la restauration du stock'); return;
            }
        }
        $this->db->where('id_sale', $sale['id_sale'])->update('library_payments', ['statut' => 'annule', 'modifie_le' => date('Y-m-d H:i:s')]);
        $this->db->where('id_sale', $sale['id_sale'])->update('library_sales', ['statut' => 'annulee', 'modifie_le' => date('Y-m-d H:i:s')]);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de l\'annulation'); return;
        }
        $this->db->trans_commit();
        $this->library_model->audit('cancel', 'library_sales', $sale['id_sale'], ['statut' => $sale['statut']], ['statut' => 'annulee']);
        $this->json_success(null, 'Vente annulee');
    }

    public function api_delete($uuid)
    {
        $sale = $this->db->get_where('library_sales', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$sale) { $this->json_error('Vente introuvable', 404); return; }
        if ($sale['statut'] !== 'annulee') { $this->json_error('Annulez la vente avant de la supprimer.'); return; }
        if (!$this->Model->update('library_sales', ['id_sale' => $sale['id_sale']], ['deleted_at' => date('Y-m-d H:i:s')])) {
            $this->json_error('Erreur lors de la suppression'); return;
        }
        $this->library_model->audit('delete', 'library_sales', $sale['id_sale'], $sale, null);
        $this->json_success(null, 'Vente supprimee');
    }

    private function _apply_sale_stock($id_sale, $lines, $id_etudiant, $id_user)
    {
        foreach ($lines as $line) {
            $produit = $this->db->query('SELECT * FROM produits WHERE id_produit = ? FOR UPDATE', [$line['id_produit']])->row_array();
            if (!$produit) return false;
            $stock = (int)$produit['stock_actuel'];
            if ($stock < (int)$line['quantite']) return false;
            $this->db->where('id_produit', $line['id_produit'])->update('produits', [
                'stock_actuel' => $stock - (int)$line['quantite'],
                'modifie_le' => date('Y-m-d H:i:s'),
            ]);
            $this->db->insert('mouvements_stock', [
                'uuid' => generate_uuid(),
                'id_produit' => $line['id_produit'],
                'type' => 'sortie',
                'quantite' => (int)$line['quantite'],
                'prix_unitaire' => $line['prix_unitaire'],
                'prix_achat_unitaire' => $line['prix_achat'],
                'motif' => 'Vente #' . $id_sale,
                'source_type' => 'vente',
                'source_id' => $id_sale,
                'date_mvt' => date('Y-m-d H:i:s'),
                'id_utilisateur' => $id_user,
                'id_etudiant' => $id_etudiant,
                'cree_le' => date('Y-m-d H:i:s'),
            ]);
        }
        return true;
    }
}
