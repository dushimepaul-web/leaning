<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Library_stock extends MY_Controller
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
        $data['title'] = 'Librairie - Stock';
        $data['categories'] = $this->Model->read('categories_produits', ['deleted_at' => null]);
        $this->load->view('stock', $data);
    }

    public function api_list()
    {
        $this->db->where('p.deleted_at', null);
        $this->db->select('p.*, c.libelle AS categorie, c.code AS code_categorie,
            (p.stock_actuel * p.prix_achat) AS valeur_stock');
        $this->db->from('produits p');
        $this->db->join('categories_produits c', 'c.id_categorie = p.id_categorie', 'left');
        if ($this->input->get('categorie')) $this->db->where('p.id_categorie', (int)$this->input->get('categorie'));
        if ($this->input->get('q')) {
            $q = $this->input->get('q');
            $this->db->group_start()->like('p.libelle', $q)->or_like('p.editeur', $q)->group_end();
        }
        $this->db->order_by('p.libelle', 'ASC');
        $rows = $this->db->get()->result_array();
        foreach ($rows as &$row) {
            $row['alerte'] = ((int)$row['stock_actuel'] <= (int)$row['stock_mini']) ? 1 : 0;
        }
        unset($row);
        $this->json_success($rows);
    }

    public function api_movements()
    {
        $this->db->select('m.*, p.libelle AS produit, u.nom_complet AS utilisateur');
        $this->db->from('mouvements_stock m');
        $this->db->join('produits p', 'p.id_produit = m.id_produit', 'left');
        $this->db->join('utilisateurs u', 'u.id_utilisateur = m.id_utilisateur', 'left');
        if ($this->input->get('id_produit')) $this->db->where('m.id_produit', (int)$this->input->get('id_produit'));
        if ($this->input->get('type')) $this->db->where('m.type', $this->input->get('type'));
        if ($this->input->get('source_type')) $this->db->where('m.source_type', $this->input->get('source_type'));
        if ($this->input->get('date_from')) $this->db->where('DATE(m.date_mvt) >=', $this->input->get('date_from'));
        if ($this->input->get('date_to')) $this->db->where('DATE(m.date_mvt) <=', $this->input->get('date_to'));
        $this->db->order_by('m.date_mvt', 'DESC')->order_by('m.id_mouvement', 'DESC')->limit(500);
        $this->json_success($this->db->get()->result_array());
    }

    public function api_create()
    {
        $data = $this->get_json_input();
        $action = $data['action'] ?? 'adjust';
        if ($action === 'adjust') return $this->api_adjust($data);
        if ($action === 'inventory') return $this->api_inventory($data);
        $this->json_error('Action invalide');
    }

    private function api_adjust($data)
    {
        $id_produit = (int)($data['id_produit'] ?? 0);
        $quantite = (int)($data['quantite'] ?? 0);
        $motif = trim($data['motif'] ?? '');
        if ($id_produit <= 0 || $quantite === 0) { $this->json_error('Produit et quantite non nulle requis'); return; }
        if ($motif === '') { $this->json_error('Le motif est obligatoire'); return; }
        $produit = $this->db->get_where('produits', ['id_produit' => $id_produit, 'deleted_at' => null])->row_array();
        if (!$produit) { $this->json_error('Produit introuvable'); return; }
        $type = $quantite > 0 ? 'entree' : 'sortie';
        $this->db->trans_begin();
        $ok = $this->library_model->move_stock($id_produit, $type, abs($quantite), $produit['prix_unitaire'], $produit['prix_achat'], 'Ajustement: ' . $motif, 'correction', null);
        if (!$ok) { $this->db->trans_rollback(); $this->json_error('Stock insuffisant pour cette sortie'); return; }
        if ($this->db->trans_status() === false) { $this->db->trans_rollback(); $this->json_error('Erreur lors de l\'ajustement'); return; }
        $this->db->trans_commit();
        $this->library_model->audit('adjust_stock', 'produits', $id_produit, ['stock' => $produit['stock_actuel']], ['quantite' => $quantite, 'motif' => $motif]);
        $this->json_success(null, 'Stock ajuste');
    }

    private function api_inventory($data)
    {
        $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : [];
        if (empty($items)) { $this->json_error('Ajoutez au moins un produit a inventorier'); return; }
        $lines = [];
        foreach ($items as $item) {
            $id_produit = (int)($item['id_produit'] ?? 0);
            $reel = isset($item['stock_reel']) ? (int)$item['stock_reel'] : null;
            if ($id_produit <= 0 || $reel === null || $reel < 0) { $this->json_error('Ligne d\'inventaire invalide'); return; }
            $p = $this->db->get_where('produits', ['id_produit' => $id_produit, 'deleted_at' => null])->row_array();
            if (!$p) { $this->json_error('Produit introuvable'); return; }
            $lines[$id_produit] = [
                'id_produit' => $id_produit,
                'stock_theorique' => (int)$p['stock_actuel'],
                'stock_reel' => $reel,
                'prix_achat' => (float)$p['prix_achat'],
            ];
        }
        $this->db->trans_begin();
        $id = $this->Model->createLastId('library_inventories', [
            'numero_inventaire' => $this->library_model->next_number('INV', 'library_inventories', 'numero_inventaire'),
            'date_inventaire' => !empty($data['date_inventaire']) ? $data['date_inventaire'] : date('Y-m-d'),
            'statut' => 'brouillon',
            'notes' => trim($data['notes'] ?? '') ?: null,
            'id_utilisateur' => $this->session->userdata('id_utilisateur') ?: null,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        if (!$id) { $this->db->trans_rollback(); $this->json_error('Erreur lors de la creation'); return; }
        foreach ($lines as $line) {
            $this->db->insert('library_inventory_items', [
                'uuid' => generate_uuid(),
                'id_inventory' => $id,
                'id_produit' => $line['id_produit'],
                'stock_theorique' => $line['stock_theorique'],
                'stock_reel' => $line['stock_reel'],
                'prix_achat' => $line['prix_achat'],
                'cree_le' => date('Y-m-d H:i:s'),
            ]);
        }
        if ($this->db->trans_status() === false) { $this->db->trans_rollback(); $this->json_error('Erreur lors de l\'enregistrement'); return; }
        $this->db->trans_commit();
        $this->library_model->audit('create', 'library_inventories', $id, null, ['lignes' => count($lines)]);
        $this->json_success(['id_inventory' => $id], 'Inventaire cree');
    }

    public function api_update($uuid)
    {
        $data = $this->get_json_input();
        $inv = $this->db->get_where('library_inventories', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$inv) { $this->json_error('Inventaire introuvable', 404); return; }
        $new = $data['statut'] ?? null;
        if (!in_array($new, ['valide', 'annule'], true)) { $this->json_error('Statut invalide'); return; }
        if ($inv['statut'] !== 'brouillon') { $this->json_error('Cet inventaire est deja traite'); return; }

        $this->db->trans_begin();
        if ($new === 'valide') {
            $items = $this->db->get_where('library_inventory_items', ['id_inventory' => $inv['id_inventory']])->result_array();
            foreach ($items as $item) {
                $ecart = (int)$item['stock_reel'] - (int)$item['stock_theorique'];
                if ($ecart === 0) continue;
                $type = $ecart > 0 ? 'entree' : 'sortie';
                if (!$this->library_model->move_stock($item['id_produit'], $type, abs($ecart), null, $item['prix_achat'], 'Inventaire ' . $inv['numero_inventaire'], 'inventaire', $inv['id_inventory'])) {
                    $this->db->trans_rollback();
                    $this->json_error('Erreur lors de la correction du stock'); return;
                }
            }
        }
        $this->db->where('id_inventory', $inv['id_inventory'])->update('library_inventories', [
            'statut' => $new,
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        if ($this->db->trans_status() === false) { $this->db->trans_rollback(); $this->json_error('Erreur lors de la validation'); return; }
        $this->db->trans_commit();
        $this->library_model->audit('update_statut', 'library_inventories', $inv['id_inventory'], ['statut' => 'brouillon'], ['statut' => $new]);
        $this->json_success(null, $new === 'valide' ? 'Inventaire valide' : 'Inventaire annule');
    }

    public function api_inventories()
    {
        $this->db->select('i.*, u.nom_complet AS utilisateur,
            (SELECT COUNT(*) FROM library_inventory_items x WHERE x.id_inventory = i.id_inventory) AS nb_lignes,
            (SELECT COALESCE(SUM(ABS(x.ecart)),0) FROM library_inventory_items x WHERE x.id_inventory = i.id_inventory) AS nb_ecarts');
        $this->db->from('library_inventories i');
        $this->db->join('utilisateurs u', 'u.id_utilisateur = i.id_utilisateur', 'left');
        $this->db->where('i.deleted_at', null);
        $this->db->order_by('i.date_inventaire', 'DESC')->order_by('i.id_inventory', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_get_inventory($uuid)
    {
        $inv = $this->db->get_where('library_inventories', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$inv) { $this->json_error('Inventaire introuvable', 404); return; }
        $inv['items'] = $this->db
            ->select('x.*, p.libelle AS produit, p.unite')
            ->from('library_inventory_items x')
            ->join('produits p', 'p.id_produit = x.id_produit', 'left')
            ->where('x.id_inventory', $inv['id_inventory'])
            ->order_by('p.libelle', 'ASC')
            ->get()->result_array();
        $this->json_success($inv);
    }

    public function api_delete($uuid)
    {
        $inv = $this->db->get_where('library_inventories', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$inv) { $this->json_error('Inventaire introuvable', 404); return; }
        if ($inv['statut'] !== 'brouillon') { $this->json_error('Seul un inventaire brouillon peut etre supprime'); return; }
        $this->db->where('id_inventory', $inv['id_inventory'])->update('library_inventories', ['deleted_at' => date('Y-m-d H:i:s')]);
        $this->library_model->audit('delete', 'library_inventories', $inv['id_inventory'], $inv, null);
        $this->json_success(null, 'Inventaire supprime');
    }
}
