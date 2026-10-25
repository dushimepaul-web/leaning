<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Library_purchases extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->not_logged_in();
        $this->load->model('Library_model', 'library_model');
        $this->load->helper('uuid');
    }

    public function index()
    {
        $data['title'] = 'Librairie - Approvisionnements';
        $suppliers = $this->Model->read('library_suppliers', ['deleted_at' => null]) ?: [];
        usort($suppliers, function ($a, $b) {
            $da = ($a['code'] ?? '') === 'FOURN-DEFAUT' ? 0 : 1;
            $db = ($b['code'] ?? '') === 'FOURN-DEFAUT' ? 0 : 1;
            return $da === $db ? strcasecmp($a['nom'] ?? '', $b['nom'] ?? '') : $da <=> $db;
        });
        $data['suppliers'] = $suppliers;
        $this->load->view('purchases', $data);
    }

    public function api_list()
    {
        $this->db->where('p.deleted_at', null);
        $this->db->select('p.*, s.nom AS fournisseur, u.nom_complet AS utilisateur,
            (SELECT COUNT(*) FROM library_purchase_items i WHERE i.id_purchase = p.id_purchase) AS nb_lignes');
        $this->db->from('library_purchases p');
        $this->db->join('library_suppliers s', 's.id_supplier = p.id_supplier', 'left');
        $this->db->join('utilisateurs u', 'u.id_utilisateur = p.id_utilisateur', 'left');
        if ($this->input->get('statut')) $this->db->where('p.statut', $this->input->get('statut'));
        if ($this->input->get('date_from')) $this->db->where('DATE(p.date_purchase) >=', $this->input->get('date_from'));
        if ($this->input->get('date_to')) $this->db->where('DATE(p.date_purchase) <=', $this->input->get('date_to'));
        $this->db->order_by('p.date_purchase', 'DESC')->order_by('p.id_purchase', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : []);
    }

    public function api_get($uuid)
    {
        $this->db->select('p.*, s.nom AS fournisseur, s.telephone AS fournisseur_telephone, u.nom_complet AS utilisateur');
        $this->db->from('library_purchases p');
        $this->db->join('library_suppliers s', 's.id_supplier = p.id_supplier', 'left');
        $this->db->join('utilisateurs u', 'u.id_utilisateur = p.id_utilisateur', 'left');
        $this->db->where('p.uuid', (string)$uuid)->where('p.deleted_at', null);
        $row = $this->db->get()->row_array();
        if (!$row) { $this->json_error('Approvisionnement introuvable', 404); return; }
        $row['items'] = $this->db
            ->select('i.*, pr.libelle AS produit, pr.unite, c.libelle AS categorie')
            ->from('library_purchase_items i')
            ->join('produits pr', 'pr.id_produit = i.id_produit', 'left')
            ->join('categories_produits c', 'c.id_categorie = pr.id_categorie', 'left')
            ->where('i.id_purchase', $row['id_purchase'])
            ->get()->result_array();
        $this->json_success($row);
    }

    public function api_create()
    {
        $data = $this->get_json_input();
        $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : [];
        if (empty($items)) { $this->json_error('Ajoutez au moins une ligne de produit'); return; }
        $statut = $data['statut'] ?? 'brouillon';
        if (!in_array($statut, ['brouillon', 'validee'], true)) { $this->json_error('Statut invalide'); return; }
        if (!empty($data['id_supplier'])) {
            $sup = $this->db->get_where('library_suppliers', ['id_supplier' => (int)$data['id_supplier'], 'deleted_at' => null])->row_array();
            if (!$sup) { $this->json_error('Fournisseur introuvable'); return; }
        }

        $lines = [];
        $sous_total = 0;
        foreach ($items as $item) {
            $id_produit = (int)($item['id_produit'] ?? 0);
            $qty = (int)($item['quantite'] ?? 0);
            $unit = isset($item['prix_achat_unitaire']) && $item['prix_achat_unitaire'] !== ''
                ? (float)$item['prix_achat_unitaire'] : null;
            if ($id_produit <= 0 || $qty <= 0) { $this->json_error('Ligne invalide : produit et quantite requis'); return; }
            $produit = $this->db->get_where('produits', ['id_produit' => $id_produit, 'deleted_at' => null])->row_array();
            if (!$produit) { $this->json_error('Produit introuvable (#'.$id_produit.')'); return; }
            if ($unit === null) $unit = (float)$produit['prix_achat'];
            if ($unit < 0) { $this->json_error('Le prix d\'achat ne peut pas etre negatif'); return; }
            $total_ligne = round($qty * $unit, 2);
            $sous_total += $total_ligne;
            $lines[] = [
                'id_produit' => $id_produit,
                'quantite' => $qty,
                'prix_achat_unitaire' => $unit,
                'total_ligne' => $total_ligne,
                'libelle' => $produit['libelle'],
            ];
        }
        $total = round($sous_total, 2);
        $id_user = $this->session->userdata('id_utilisateur') ?: null;

        $numero_bon = $this->library_model->next_number('ACH', 'library_purchases', 'numero_bon');
        $reference = trim($data['reference'] ?? '');
        if ($reference === '') $reference = 'REF-' . preg_replace('/^ACH-/', '', $numero_bon);

        $this->db->trans_begin();
        $id = $this->Model->createLastId('library_purchases', [
            'numero_bon' => $numero_bon,
            'id_supplier' => !empty($data['id_supplier']) ? (int)$data['id_supplier'] : null,
            'reference' => $reference,
            'date_purchase' => !empty($data['date_purchase']) ? $data['date_purchase'] : date('Y-m-d H:i:s'),
            'sous_total' => $sous_total,
            'frais' => 0,
            'total' => $total,
            'statut' => $statut,
            'notes' => trim($data['notes'] ?? '') ?: null,
            'id_utilisateur' => $id_user,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        if (!$id) { $this->db->trans_rollback(); $this->json_error('Erreur lors de la creation'); return; }

        foreach ($lines as $line) {
            $this->db->insert('library_purchase_items', [
                'uuid' => generate_uuid(),
                'id_purchase' => $id,
                'id_produit' => $line['id_produit'],
                'quantite' => $line['quantite'],
                'prix_achat_unitaire' => $line['prix_achat_unitaire'],
                'total_ligne' => $line['total_ligne'],
                'cree_le' => date('Y-m-d H:i:s'),
            ]);
        }
        if ($statut === 'validee' && !$this->_apply_purchase($id, $lines, $id_user)) {
            $this->db->trans_rollback();
            $this->json_error('Stock insuffisant ou erreur lors de la validation');
            return;
        }
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de l\'enregistrement');
            return;
        }
        $this->db->trans_commit();
        $this->library_model->audit('create', 'library_purchases', $id, null, ['total' => $total, 'statut' => $statut, 'lignes' => count($lines)]);
        $this->json_success(['id_purchase' => $id], 'Approvisionnement enregistre');
    }

    public function api_update($uuid)
    {
        $data = $this->get_json_input();
        $purchase = $this->db->get_where('library_purchases', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$purchase) { $this->json_error('Approvisionnement introuvable', 404); return; }
        $new = $data['statut'] ?? null;
        if (!in_array($new, ['brouillon', 'validee', 'annulee'], true)) { $this->json_error('Statut invalide'); return; }
        if ($new === $purchase['statut']) { $this->json_success(null, 'Aucun changement'); return; }
        if ($purchase['statut'] === 'validee' && $new === 'annulee') {
            $this->json_error('Un approvisionnement valide ne peut pas etre annule. Utilisez un retour fournisseur.'); return;
        }
        if ($purchase['statut'] !== 'brouillon') { $this->json_error('Transition non autorisee'); return; }

        $this->db->trans_begin();
        if ($new === 'validee') {
            $items = $this->db->get_where('library_purchase_items', ['id_purchase' => $purchase['id_purchase']])->result_array();
            $lines = [];
            foreach ($items as $i) {
                $lines[] = [
                    'id_produit' => $i['id_produit'],
                    'quantite' => $i['quantite'],
                    'prix_achat_unitaire' => $i['prix_achat_unitaire'],
                    'total_ligne' => $i['total_ligne'],
                ];
            }
            if (empty($lines) || !$this->_apply_purchase($purchase['id_purchase'], $lines, $this->session->userdata('id_utilisateur'))) {
                $this->db->trans_rollback();
                $this->json_error('Erreur lors de la validation du stock'); return;
            }
        }
        $this->Model->update('library_purchases', ['id_purchase' => $purchase['id_purchase']], [
            'statut' => $new,
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de la mise a jour'); return;
        }
        $this->db->trans_commit();
        $this->library_model->audit('update_statut', 'library_purchases', $purchase['id_purchase'], ['statut' => $purchase['statut']], ['statut' => $new]);
        $this->json_success(null, 'Approvisionnement mis a jour');
    }

    public function api_delete($uuid)
    {
        $purchase = $this->db->get_where('library_purchases', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$purchase) { $this->json_error('Approvisionnement introuvable', 404); return; }
        if ($purchase['statut'] === 'validee') { $this->json_error('Un approvisionnement valide ne peut pas etre supprime.'); return; }
        if (!$this->Model->update('library_purchases', ['id_purchase' => $purchase['id_purchase']], ['deleted_at' => date('Y-m-d H:i:s')])) {
            $this->json_error('Erreur lors de la suppression'); return;
        }
        $this->library_model->audit('delete', 'library_purchases', $purchase['id_purchase'], $purchase, null);
        $this->json_success(null, 'Approvisionnement supprime');
    }

    private function _apply_purchase($id_purchase, $lines, $id_user)
    {
        foreach ($lines as $line) {
            $id_produit = (int)$line['id_produit'];
            $qty = (int)$line['quantite'];
            $unit = (float)$line['prix_achat_unitaire'];
            $produit = $this->db->query('SELECT * FROM produits WHERE id_produit = ? FOR UPDATE', [$id_produit])->row_array();
            if (!$produit) return false;
            $old_stock = (int)$produit['stock_actuel'];
            $old_cost = (float)$produit['prix_achat'];
            $new_stock = $old_stock + $qty;
            $new_cost = round($unit, 2);
            $this->db->where('id_produit', $id_produit)->update('produits', [
                'stock_actuel' => $new_stock,
                'prix_achat' => $new_cost,
                'modifie_le' => date('Y-m-d H:i:s'),
            ]);
            $this->db->insert('mouvements_stock', [
                'uuid' => generate_uuid(),
                'id_produit' => $id_produit,
                'type' => 'entree',
                'quantite' => $qty,
                'prix_unitaire' => $produit['prix_unitaire'],
                'prix_achat_unitaire' => $unit,
                'motif' => 'Approvisionnement ' . $id_purchase,
                'source_type' => 'approvisionnement',
                'source_id' => $id_purchase,
                'date_mvt' => date('Y-m-d H:i:s'),
                'id_utilisateur' => $id_user,
                'cree_le' => date('Y-m-d H:i:s'),
            ]);
            if (abs($new_cost - $old_cost) >= 0.005) {
                $this->db->insert('library_price_history', [
                    'uuid' => generate_uuid(),
                    'id_produit' => $id_produit,
                    'ancien_prix_achat' => $old_cost,
                    'nouveau_prix_achat' => $new_cost,
                    'source' => 'approvisionnement',
                    'id_utilisateur' => $id_user,
                    'date_changement' => date('Y-m-d H:i:s'),
                    'cree_le' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        return true;
    }
}
