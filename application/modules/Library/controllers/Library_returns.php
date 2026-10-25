<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Library_returns extends MY_Controller
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
        $data['title'] = 'Librairie - Retours';
        $this->load->view('returns', $data);
    }

    public function api_list()
    {
        $this->db->where('r.deleted_at', null);
        $this->db->select("r.*, s.numero_vente, s.total AS total_vente,
            COALESCE(e.fullname, s.client_nom, 'Client comptant') AS client,
            u.nom_complet AS demandeur, v.nom_complet AS valideur,
            (SELECT COUNT(*) FROM library_return_items i WHERE i.id_return = r.id_return) AS nb_lignes");
        $this->db->from('library_returns r');
        $this->db->join('library_sales s', 's.id_sale = r.id_sale', 'left');
        $this->db->join('etudiants e', 'e.id_etudiant = s.id_etudiant', 'left');
        $this->db->join('utilisateurs u', 'u.id_utilisateur = r.id_utilisateur', 'left');
        $this->db->join('utilisateurs v', 'v.id_utilisateur = r.id_valideur', 'left');
        if ($this->input->get('statut')) $this->db->where('r.statut', $this->input->get('statut'));
        if ($this->input->get('date_from')) $this->db->where('DATE(r.date_demande) >=', $this->input->get('date_from'));
        if ($this->input->get('date_to')) $this->db->where('DATE(r.date_demande) <=', $this->input->get('date_to'));
        $this->db->order_by('r.date_demande', 'DESC')->order_by('r.id_return', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_sales()
    {
        $this->db->select("s.id_sale, s.uuid, s.numero_vente, s.total, s.statut,
            COALESCE(e.fullname, s.client_nom, 'Client comptant') AS client");
        $this->db->from('library_sales s');
        $this->db->join('etudiants e', 'e.id_etudiant = s.id_etudiant', 'left');
        $this->db->where('s.deleted_at', null);
        $this->db->where_not_in('s.statut', ['annulee', 'retournee']);
        $this->db->order_by('s.date_vente', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_sale_items($uuid)
    {
        $sale = $this->db->get_where('library_sales', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$sale) { $this->json_error('Vente introuvable', 404); return; }
        $items = $this->db
            ->select('i.*, p.libelle AS produit, p.unite')
            ->from('library_sale_items i')
            ->join('produits p', 'p.id_produit = i.id_produit', 'left')
            ->where('i.id_sale', $sale['id_sale'])
            ->get()->result_array();
        foreach ($items as &$item) {
            $item['deja_retourne'] = $this->_returned_qty($item['id_sale_item']);
            $item['retournable'] = max(0, (int)$item['quantite'] - (int)$item['deja_retourne']);
        }
        unset($item);
        $this->json_success(['sale' => $sale, 'items' => $items]);
    }

    public function api_get($uuid)
    {
        $row = $this->db->get_where('library_returns', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$row) { $this->json_error('Retour introuvable', 404); return; }
        $row['items'] = $this->db
            ->select('x.*, p.libelle AS produit, p.unite')
            ->from('library_return_items x')
            ->join('produits p', 'p.id_produit = x.id_produit', 'left')
            ->where('x.id_return', $row['id_return'])
            ->get()->result_array();
        $sale = $this->db->get_where('library_sales', ['id_sale' => $row['id_sale']])->row_array();
        $row['numero_vente'] = $sale ? $sale['numero_vente'] : null;
        $this->json_success($row);
    }

    public function api_create()
    {
        $data = $this->get_json_input();
        $uuid = trim($data['uuid'] ?? '');
        $type = $data['type'] ?? 'retour';
        $motif = trim($data['motif'] ?? '');
        $items = isset($data['items']) && is_array($data['items']) ? $data['items'] : [];
        if ($uuid === '' || $motif === '') { $this->json_error('Vente et motif requis'); return; }
        if (!in_array($type, ['retour', 'annulation', 'remboursement'], true)) { $this->json_error('Type invalide'); return; }
        if (empty($items)) { $this->json_error('Ajoutez au moins un produit a retourner'); return; }
        $sale = $this->db->get_where('library_sales', ['uuid' => $uuid, 'deleted_at' => null])->row_array();
        if (!$sale) { $this->json_error('Vente introuvable', 404); return; }
        if ($sale['statut'] === 'annulee') { $this->json_error('Cette vente est annulee'); return; }

        $lines = [];
        $montant_total = 0;
        $seen = [];
        foreach ($items as $item) {
            $id_sale_item = (int)($item['id_sale_item'] ?? 0);
            $qty = (int)($item['quantite'] ?? 0);
            if ($id_sale_item <= 0 || $qty <= 0) { $this->json_error('Ligne de retour invalide'); return; }
            if (isset($seen[$id_sale_item])) { $this->json_error('Un produit est present plusieurs fois'); return; }
            $seen[$id_sale_item] = true;
            $si = $this->db->get_where('library_sale_items', ['id_sale_item' => $id_sale_item, 'id_sale' => $sale['id_sale']])->row_array();
            if (!$si) { $this->json_error('Ligne de vente introuvable'); return; }
            $deja = $this->_returned_qty($id_sale_item);
            $restant = (int)$si['quantite'] - $deja;
            if ($qty > $restant) { $this->json_error('Quantite retournee superieure au restant (' . $restant . ')'); return; }
            $unit_net = (int)$si['quantite'] > 0 ? ((float)$si['total_ligne'] / (int)$si['quantite']) : (float)$si['prix_unitaire'];
            $line_total = round($unit_net * $qty, 2);
            $montant_total += $line_total;
            $lines[] = [
                'id_sale_item' => $id_sale_item,
                'id_produit' => $si['id_produit'],
                'quantite' => $qty,
                'prix_unitaire' => $si['prix_unitaire'],
                'prix_achat' => $si['prix_achat'],
                'montant' => $line_total,
            ];
        }

        $this->db->trans_begin();
        $id = $this->Model->createLastId('library_returns', [
            'numero_retour' => $this->library_model->next_number('RET', 'library_returns', 'numero_retour'),
            'id_sale' => $sale['id_sale'],
            'type' => $type,
            'motif' => $motif,
            'montant_total' => round($montant_total, 2),
            'statut' => 'demande',
            'id_utilisateur' => $this->session->userdata('id_utilisateur') ?: null,
            'date_demande' => date('Y-m-d H:i:s'),
            'notes' => trim($data['notes'] ?? '') ?: null,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        if (!$id) { $this->db->trans_rollback(); $this->json_error('Erreur lors de la creation'); return; }
        foreach ($lines as $line) {
            $this->db->insert('library_return_items', [
                'uuid' => generate_uuid(),
                'id_return' => $id,
                'id_sale_item' => $line['id_sale_item'],
                'id_produit' => $line['id_produit'],
                'quantite' => $line['quantite'],
                'prix_unitaire' => $line['prix_unitaire'],
                'prix_achat' => $line['prix_achat'],
                'montant' => $line['montant'],
                'cree_le' => date('Y-m-d H:i:s'),
            ]);
        }
        if ($this->db->trans_status() === false) { $this->db->trans_rollback(); $this->json_error('Erreur lors de l\'enregistrement'); return; }
        $this->db->trans_commit();
        $this->library_model->audit('create', 'library_returns', $id, null, ['id_sale' => $sale['id_sale'], 'montant' => $montant_total]);
        $this->json_success(['id_return' => $id], 'Demande de retour enregistree');
    }

    public function api_update($uuid)
    {
        $data = $this->get_json_input();
        $ret = $this->db->get_where('library_returns', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$ret) { $this->json_error('Retour introuvable', 404); return; }
        $new = $data['statut'] ?? null;
        if (!in_array($new, ['validee', 'refusee', 'remboursee'], true)) { $this->json_error('Statut invalide'); return; }
        if ($new === $ret['statut']) { $this->json_success(null, 'Aucun changement'); return; }

        if ($new === 'validee') {
            if ($ret['statut'] !== 'demande') { $this->json_error('Seule une demande peut etre validee'); return; }
            $this->db->trans_begin();
            $items = $this->db->get_where('library_return_items', ['id_return' => $ret['id_return']])->result_array();
            foreach ($items as $item) {
                if (!$this->library_model->move_stock($item['id_produit'], 'entree', $item['quantite'], $item['prix_unitaire'], $item['prix_achat'], 'Retour ' . $ret['numero_retour'], 'retour', $ret['id_return'])) {
                    $this->db->trans_rollback();
                    $this->json_error('Erreur lors de la restauration du stock'); return;
                }
            }
            $this->db->where('id_return', $ret['id_return'])->update('library_returns', [
                'statut' => 'validee',
                'id_valideur' => $this->session->userdata('id_utilisateur') ?: null,
                'date_validation' => date('Y-m-d H:i:s'),
                'modifie_le' => date('Y-m-d H:i:s'),
            ]);
            if ($this->db->trans_status() === false) { $this->db->trans_rollback(); $this->json_error('Erreur lors de la validation'); return; }
            $this->db->trans_commit();
            $this->_maybe_close_sale($ret['id_sale']);
        } elseif ($new === 'remboursee') {
            if ($ret['statut'] !== 'validee') { $this->json_error('Le retour doit etre valide avant le remboursement'); return; }
            $this->db->where('id_return', $ret['id_return'])->update('library_returns', [
                'statut' => 'remboursee',
                'modifie_le' => date('Y-m-d H:i:s'),
            ]);
        } else {
            if ($ret['statut'] !== 'demande') { $this->json_error('Seule une demande peut etre refusee'); return; }
            $this->db->where('id_return', $ret['id_return'])->update('library_returns', [
                'statut' => 'refusee',
                'id_valideur' => $this->session->userdata('id_utilisateur') ?: null,
                'date_validation' => date('Y-m-d H:i:s'),
                'modifie_le' => date('Y-m-d H:i:s'),
            ]);
        }
        $this->library_model->audit('update_statut', 'library_returns', $ret['id_return'], ['statut' => $ret['statut']], ['statut' => $new]);
        $this->json_success(null, 'Retour mis a jour');
    }

    public function api_delete($uuid)
    {
        $ret = $this->db->get_where('library_returns', ['uuid' => (string)$uuid, 'deleted_at' => null])->row_array();
        if (!$ret) { $this->json_error('Retour introuvable', 404); return; }
        if ($ret['statut'] !== 'demande') { $this->json_error('Seule une demande peut etre supprimee'); return; }
        $this->db->where('id_return', $ret['id_return'])->update('library_returns', ['deleted_at' => date('Y-m-d H:i:s')]);
        $this->library_model->audit('delete', 'library_returns', $ret['id_return'], $ret, null);
        $this->json_success(null, 'Demande de retour supprimee');
    }

    private function _returned_qty($id_sale_item)
    {
        $row = $this->db
            ->select('COALESCE(SUM(i.quantite),0) AS q')
            ->from('library_return_items i')
            ->join('library_returns r', 'r.id_return = i.id_return')
            ->where('i.id_sale_item', (int)$id_sale_item)
            ->where_in('r.statut', ['demande', 'validee', 'remboursee'])
            ->where('r.deleted_at', null)
            ->get()->row_array();
        return $row ? (int)$row['q'] : 0;
    }

    private function _maybe_close_sale($id_sale)
    {
        $sale = $this->db->get_where('library_sales', ['id_sale' => (int)$id_sale])->row_array();
        if (!$sale) return;
        $returned = $this->db
            ->select('COALESCE(SUM(montant_total),0) AS total')
            ->get_where('library_returns', ['id_sale' => (int)$id_sale, 'deleted_at' => null])
            ->where_in('statut', ['validee', 'remboursee'])->row_array();
        if ($returned && (float)$returned['total'] + 0.009 >= (float)$sale['total']) {
            $this->db->where('id_sale', (int)$id_sale)->update('library_sales', ['statut' => 'retournee', 'modifie_le' => date('Y-m-d H:i:s')]);
        }
    }
}
