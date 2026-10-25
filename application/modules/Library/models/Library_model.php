<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Library_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('uuid');
    }

    public function next_number($prefix, $table, $column)
    {
        $prefix = strtoupper($prefix) . '-' . date('Ymd') . '-';
        $this->db->like($column, $prefix, 'after');
        $count = $this->db->count_all_results($table);
        return $prefix . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    public function audit($action, $table, $record_id = null, $old = null, $new = null)
    {
        $this->db->insert('audit_logs', [
            'uuid' => generate_uuid(),
            'id_utilisateur' => $this->session->userdata('id_utilisateur') ?: null,
            'action' => $action,
            'table_concernee' => $table,
            'id_enregistrement' => $record_id ? (int)$record_id : null,
            'anciennes_valeurs' => $old !== null ? json_encode($old) : null,
            'nouvelles_valeurs' => $new !== null ? json_encode($new) : null,
            'adresse_ip' => $this->input->ip_address(),
            'date_action' => date('Y-m-d H:i:s'),
        ]);
    }

    public function move_stock($id_produit, $type, $quantite, $prix_vente = null, $prix_achat = null, $motif = '', $source_type = null, $source_id = null, $id_etudiant = null)
    {
        $id_produit = (int)$id_produit;
        $quantite = (int)$quantite;
        if ($id_produit <= 0 || $quantite <= 0 || !in_array($type, ['entree', 'sortie'], true)) {
            return false;
        }
        $produit = $this->db->get_where('produits', ['id_produit' => $id_produit])->row_array();
        if (!$produit) return false;
        $stock = (int)$produit['stock_actuel'];
        $nouveau = $type === 'entree' ? $stock + $quantite : $stock - $quantite;
        if ($nouveau < 0) return false;

        $this->db->where('id_produit', $id_produit)->update('produits', [
            'stock_actuel' => $nouveau,
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        $this->db->insert('mouvements_stock', [
            'uuid' => generate_uuid(),
            'id_produit' => $id_produit,
            'type' => $type,
            'quantite' => $quantite,
            'prix_unitaire' => $prix_vente !== null ? $prix_vente : null,
            'prix_achat_unitaire' => $prix_achat !== null ? $prix_achat : null,
            'motif' => $motif,
            'source_type' => $source_type,
            'source_id' => $source_id,
            'date_mvt' => date('Y-m-d H:i:s'),
            'id_utilisateur' => $this->session->userdata('id_utilisateur') ?: null,
            'id_etudiant' => $id_etudiant ?: null,
            'cree_le' => date('Y-m-d H:i:s'),
        ]);
        return true;
    }

    public function sale_paid($id_sale)
    {
        $row = $this->db
            ->select('COALESCE(SUM(montant),0) AS paid')
            ->from('library_payments')
            ->where('id_sale', (int)$id_sale)
            ->where('statut', 'valide')
            ->where('deleted_at', null)
            ->get()->row_array();
        return $row ? (float)$row['paid'] : 0.0;
    }

    public function refresh_sale_status($id_sale)
    {
        $sale = $this->db->get_where('library_sales', ['id_sale' => (int)$id_sale])->row_array();
        if (!$sale) return false;
        if (in_array($sale['statut'], ['annulee', 'retournee'], true)) return true;
        $paid = $this->sale_paid($id_sale);
        $total = (float)$sale['total'];
        if ($total <= 0 || $paid + 0.009 >= $total) {
            $statut = 'payee';
        } elseif ($paid > 0) {
            $statut = 'partielle';
        } elseif ($sale['mode_paiement'] === 'credit') {
            $statut = 'credit';
        } else {
            $statut = 'en_attente';
        }
        return $this->db->where('id_sale', (int)$id_sale)->update('library_sales', [
            'statut' => $statut,
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
    }

    public function sale_balance($id_sale)
    {
        $sale = $this->db->get_where('library_sales', ['id_sale' => (int)$id_sale])->row_array();
        if (!$sale) return null;
        return round((float)$sale['total'] - $this->sale_paid($id_sale), 2);
    }
}
