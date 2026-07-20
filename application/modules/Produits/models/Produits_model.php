<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produits_model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = [])
    {
        $this->db->where('p.deleted_at', null);
        $this->db->select('p.*, c.libelle as categorie_libelle');
        $this->db->from('produits p');
        $this->db->join('categories_produits c', 'p.id_categorie = c.id_categorie', 'left');
        if (!empty($filters['id_categorie'])) $this->db->where('p.id_categorie', $filters['id_categorie']);
        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('p.libelle', $filters['search'])
                ->or_like('p.code', $filters['search'])
                ->group_end();
        }
        $this->db->order_by('p.libelle', 'ASC');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->where('p.deleted_at', null);
        $this->db->where('p.uuid', $id);
        $this->db->select('p.*, c.libelle as categorie_libelle');
        $this->db->from('produits p');
        $this->db->join('categories_produits c', 'p.id_categorie = c.id_categorie', 'left');
        $q = $this->db->get();
        if ($q === false) return null;
        return $q->row_array();
    }

    public function create_record($data)
    {
        $required = ['code', 'libelle', 'id_categorie', 'prix_unitaire'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Champ requis manquant: $field"];
            }
        }
        $data['uuid'] = generate_uuid();
        $data['cree_le'] = date('Y-m-d H:i:s');
        $data['modifie_le'] = date('Y-m-d H:i:s');
        if ($this->db->insert('produits', $data)) {
            return ['success' => true, 'id_produit' => $this->db->insert_id()];
        }
        return ['success' => false, 'message' => 'Erreur insertion'];
    }

    public function update_record($id, $data)
    {
        $this->db->where('uuid', $id);
        $data['modifie_le'] = date('Y-m-d H:i:s');
        return $this->db->update('produits', $data);
    }

    public function delete_record($id)
    {
        return $this->update_record($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    public function get_categories($filters = [])
    {
        $this->db->where('deleted_at', null);
        $q = $this->db->get('categories_produits');
        if ($q === false) return array();
        return $q->result_array();
    }

    // Mouvements stock
    public function get_mouvements($filters = [])
    {
        $this->db->where('m.deleted_at', null);
        $this->db->select('m.*, p.libelle as produit_libelle, p.code as produit_code, u.nom, u.prenom');
        $this->db->from('mouvements_stock m');
        $this->db->join('produits p', 'm.id_produit = p.id_produit', 'left');
        $this->db->join('utilisateurs u', 'm.id_utilisateur = u.id_utilisateur', 'left');
        if (!empty($filters['id_produit'])) $this->db->where('m.id_produit', $filters['id_produit']);
        if (!empty($filters['type'])) $this->db->where('m.type', $filters['type']);
        if (!empty($filters['date_from'])) $this->db->where('m.date_mouvement >=', $filters['date_from']);
        if (!empty($filters['date_to'])) $this->db->where('m.date_mouvement <=', $filters['date_to']);
        $this->db->order_by('m.id_mouvement', 'DESC');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function create_mouvement($data)
    {
        $required = ['id_produit', 'type', 'quantite'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Champ requis: $field"];
            }
        }
        $data['uuid'] = generate_uuid();
        $data['date_mouvement'] = $data['date_mouvement'] ?? date('Y-m-d H:i:s');
        if ($this->db->insert('mouvements_stock', $data)) {
            // Mettre à jour stock produit
            $produit = $this->readOne('produits', ['id_produit' => $data['id_produit']]);
            if ($produit) {
                $new_stock = ($produit['stock_actuel'] ?? 0) + ($data['type'] === 'entree' ? $data['quantite'] : -$data['quantite']);
                $this->update('produits', ['id_produit' => $data['id_produit']], ['stock_actuel' => max(0, $new_stock)]);
            }
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Erreur mouvement'];
    }
}