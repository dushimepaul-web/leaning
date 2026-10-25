<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Commandes extends MY_Controller {
    private $statuts_valides = ['en_attente', 'prete', 'distribuee', 'annulee'];

    public function __construct() { parent::__construct(); $this->not_logged_in(); }

    public function index() {
        $data['title'] = 'Gestion des commandes';
        $data['etudiants'] = $this->Model->read('etudiants', ['deleted_at' => null]);
        $data['produits'] = $this->Model->read('produits', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('c.deleted_at', null);
        $this->db->select("c.*, e.fullname AS nom, '' AS prenom, e.matricule, CASE WHEN c.statut = 'annulee' THEN 0 ELSE COALESCE((SELECT SUM((cd.prix_unitaire - cd.prix_achat) * cd.quantite) FROM commandes_details cd WHERE cd.id_commande = c.id_commande), 0) END AS benefice");
        $this->db->from('commandes c');
        $this->db->join('etudiants e', 'c.id_etudiant = e.id_etudiant', 'left');
        if ($this->input->get('statut')) $this->db->where('c.statut', $this->input->get('statut'));
        if ($this->input->get('date')) $this->db->where('DATE(c.date_commande)', $this->input->get('date'));
        $this->db->order_by('c.date_commande', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_get($id) {
        $this->db->where('c.uuid', $id);
        $this->db->where('c.deleted_at', null);
        $this->db->select("c.*, e.fullname AS nom, '' AS prenom, e.matricule");
        $this->db->from('commandes c');
        $this->db->join('etudiants e', 'c.id_etudiant = e.id_etudiant', 'left');
        $row = $this->db->get()->row_array();
        if (!$row) { $this->json_error('Commande introuvable'); return; }
        $q_d = $this->db
            ->select('cd.*, p.libelle as produit_libelle')
            ->from('commandes_details cd')
            ->join('produits p', 'cd.id_produit = p.id_produit', 'left')
            ->where('cd.id_commande', $row['id_commande'])
            ->get();
        $row['details'] = $q_d !== false ? $q_d->result_array() : array();
        $this->json_success($row);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant'])) { $this->json_error('Étudiant requis'); return; }
        if (empty($data['details']) || !is_array($data['details'])) { $this->json_error('Aucun produit sélectionné'); return; }

        $statut = !empty($data['statut']) ? $data['statut'] : 'en_attente';
        if (!in_array($statut, $this->statuts_valides, true)) { $this->json_error('Statut invalide'); return; }

        $etudiant = $this->Model->readOne('etudiants', ['id_etudiant' => $data['id_etudiant'], 'deleted_at' => null]);
        if (!$etudiant) { $this->json_error('Étudiant introuvable'); return; }
        $this->load->helper('uuid');

        $lines = [];
        foreach ($data['details'] as $d) {
            if (empty($d['id_produit']) || empty($d['quantite'])) continue;
            $produit = $this->Model->readOne('produits', ['id_produit' => $d['id_produit'], 'deleted_at' => null]);
            if (!$produit) { $this->json_error('Produit introuvable (#' . $d['id_produit'] . ')'); return; }
            $qty = intval($d['quantite']);
            if ($qty <= 0) { $this->json_error('Quantité invalide pour "' . $produit['libelle'] . '"'); return; }
            $price = (isset($d['prix_unitaire']) && $d['prix_unitaire'] !== '')
                ? floatval($d['prix_unitaire'])
                : floatval($produit['prix_unitaire'] ?? 0);
            if ($price < 0) { $this->json_error('Prix invalide pour "' . $produit['libelle'] . '"'); return; }
            $lines[] = [
                'id_produit' => $produit['id_produit'],
                'libelle' => $produit['libelle'],
                'qty' => $qty,
                'price' => $price,
                'prix_achat' => floatval($produit['prix_achat'] ?? 0),
                'stock' => intval($produit['stock_actuel']),
            ];
        }
        if (empty($lines)) { $this->json_error('Aucun produit valide sélectionné'); return; }

        $deduct = ($statut !== 'annulee');
        if ($deduct) {
            $need = [];
            foreach ($lines as $l) { $need[$l['id_produit']] = ($need[$l['id_produit']] ?? 0) + $l['qty']; }
            $stock_map = [];
            foreach ($lines as $l) { $stock_map[$l['id_produit']] = ['libelle' => $l['libelle'], 'stock' => $l['stock']]; }
            foreach ($need as $pid => $q) {
                if ($stock_map[$pid]['stock'] < $q) {
                    $this->json_error('Stock insuffisant pour "' . $stock_map[$pid]['libelle'] . '" (stock: ' . $stock_map[$pid]['stock'] . ', demandé: ' . $q . ')');
                    return;
                }
            }
        }

        $id_user = $this->session->userdata('id_utilisateur');
        $this->db->trans_start();
        $insert = [
            'uuid' => generate_uuid(),
            'id_etudiant' => $data['id_etudiant'],
            'date_commande' => !empty($data['date_commande']) ? $data['date_commande'] : date('Y-m-d'),
            'statut' => $statut,
            'total' => 0,
        ];
        $id = $this->Model->createLastId('commandes', $insert);
        if (!$id) { $this->db->trans_rollback(); $this->json_error('Erreur création'); return; }

        $total = 0;
        foreach ($lines as $l) {
            $this->db->insert('commandes_details', [
                'uuid' => generate_uuid(),
                'id_commande' => $id,
                'id_produit' => $l['id_produit'],
                'quantite' => $l['qty'],
                'prix_unitaire' => $l['price'],
                'prix_achat' => $l['prix_achat'],
            ]);
            if ($deduct) {
                $produit = $this->Model->readOne('produits', ['id_produit' => $l['id_produit']]);
                if ($produit) {
                    $this->Model->update('produits', ['id_produit' => $l['id_produit']], [
                        'stock_actuel' => intval($produit['stock_actuel']) - $l['qty'],
                    ]);
                }
                $this->Model->create('mouvements_stock', [
                    'id_produit' => $l['id_produit'],
                    'type' => 'sortie',
                    'quantite' => $l['qty'],
                    'prix_unitaire' => $l['price'],
                    'motif' => 'Commande #' . $id,
                    'date_mvt' => date('Y-m-d H:i:s'),
                    'id_utilisateur' => $id_user,
                    'id_etudiant' => $data['id_etudiant'],
                ]);
            }
            $total += $l['qty'] * $l['price'];
        }
        $this->db->where('id_commande', $id)->update('commandes', ['total' => $total]);

        $id_recu = null;
        if ($statut !== 'annulee') {
            $id_recu = $this->_creer_recu($id, $data['id_etudiant'], $total);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de la création de la commande');
        } else {
            $this->json_success(['id_commande' => $id, 'id_recu' => $id_recu], 'Commande créée');
        }
    }

    private function restore_stock($id_commande) {
        $commande = $this->Model->readOne('commandes', ['id_commande' => $id_commande]);
        $id_etudiant = $commande ? $commande['id_etudiant'] : null;
        $details = $this->Model->read('commandes_details', ['id_commande' => $id_commande]);
        $id_user = $this->session->userdata('id_utilisateur');
        foreach ($details as $d) {
            $qty = intval($d['quantite']);
            $produit = $this->Model->readOne('produits', ['id_produit' => $d['id_produit']]);
            if ($produit) {
                $this->Model->update('produits', ['id_produit' => $d['id_produit']], [
                    'stock_actuel' => intval($produit['stock_actuel']) + $qty,
                ]);
            }
            $this->Model->create('mouvements_stock', [
                'id_produit' => $d['id_produit'],
                'type' => 'entree',
                'quantite' => $qty,
                'prix_unitaire' => $d['prix_unitaire'] ?? null,
                'motif' => 'Annulation commande #' . $id_commande,
                'date_mvt' => date('Y-m-d H:i:s'),
                'id_utilisateur' => $id_user,
                'id_etudiant' => $id_etudiant,
            ]);
        }
    }

    private function deduct_stock($id_commande) {
        $commande = $this->Model->readOne('commandes', ['id_commande' => $id_commande]);
        $id_etudiant = $commande ? $commande['id_etudiant'] : null;
        $details = $this->Model->read('commandes_details', ['id_commande' => $id_commande]);
        $id_user = $this->session->userdata('id_utilisateur');
        foreach ($details as $d) {
            $qty = intval($d['quantite']);
            $produit = $this->Model->readOne('produits', ['id_produit' => $d['id_produit']]);
            if ($produit) {
                $this->Model->update('produits', ['id_produit' => $d['id_produit']], [
                    'stock_actuel' => max(0, intval($produit['stock_actuel']) - $qty),
                ]);
            }
            $this->Model->create('mouvements_stock', [
                'id_produit' => $d['id_produit'],
                'type' => 'sortie',
                'quantite' => $qty,
                'prix_unitaire' => $d['prix_unitaire'] ?? 0,
                'motif' => 'Réactivation commande #' . $id_commande,
                'date_mvt' => date('Y-m-d H:i:s'),
                'id_utilisateur' => $id_user,
                'id_etudiant' => $id_etudiant,
            ]);
        }
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $commande = $this->Model->readOne('commandes', ['uuid' => $id, 'deleted_at' => null]);
        if (!$commande) { $this->json_error('Commande introuvable'); return; }

        if (!isset($data['statut'])) { $this->json_error('Aucune donnée à modifier'); return; }
        $new = $data['statut'];
        if (!in_array($new, $this->statuts_valides, true)) { $this->json_error('Statut invalide'); return; }
        $old = $commande['statut'];
        if ($new === $old) { $this->json_success(null, 'Aucun changement'); return; }

        $this->db->trans_start();
        if ($new === 'annulee' && $old !== 'annulee') {
            $this->restore_stock($commande['id_commande']);
            $this->_sync_recu($commande['id_commande'], false, $commande['id_etudiant'], $commande['total']);
        } elseif ($old === 'annulee' && $new !== 'annulee') {
            $this->deduct_stock($commande['id_commande']);
            $this->_sync_recu($commande['id_commande'], true, $commande['id_etudiant'], $commande['total']);
        }
        $this->Model->update('commandes', ['uuid' => $id], ['statut' => $new, 'modifie_le' => date('Y-m-d H:i:s')]);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->json_error('Erreur lors du changement de statut');
        } else {
            $this->json_success(null, 'Commande mise à jour');
        }
    }

    public function api_delete($id) {
        $commande = $this->Model->readOne('commandes', ['uuid' => $id, 'deleted_at' => null]);
        if (!$commande) { $this->json_error('Commande introuvable'); return; }

        $this->db->trans_start();
        if (in_array($commande['statut'], ['en_attente', 'prete'], true)) {
            $this->restore_stock($commande['id_commande']);
        }
        $this->_sync_recu($commande['id_commande'], false, $commande['id_etudiant'], $commande['total']);
        $this->Model->update('commandes', ['uuid' => $id], [
            'deleted_at' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ]);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->json_error('Erreur lors de la suppression');
        } else {
            $this->json_success(null, 'Commande supprimée');
        }
    }

    private function _sync_recu($id_commande, $active, $id_etudiant, $montant) {
        $recu = $this->Model->readOne('recus', ['id_commande' => $id_commande]);
        if ($active) {
            if ($recu) {
                $this->Model->update('recus', ['id_recu' => $recu['id_recu']], [
                    'deleted_at' => null,
                    'id_etudiant' => $id_etudiant,
                    'montant_total' => $montant,
                ]);
            } else {
                $this->_creer_recu($id_commande, $id_etudiant, $montant);
            }
        } elseif ($recu) {
            $this->Model->update('recus', ['id_recu' => $recu['id_recu']], ['deleted_at' => date('Y-m-d H:i:s')]);
        }
    }

    private function _creer_recu($id_commande, $id_etudiant, $montant) {
        $this->load->helper('uuid');
        $numero = $this->_generate_numero_recu();
        $uuid = generate_uuid();
        for ($tentative = 0; $tentative < 5; $tentative++) {
            $id_recu = $this->Model->createLastId('recus', [
                'uuid' => $uuid,
                'numero_recu' => $numero,
                'id_etudiant' => $id_etudiant,
                'id_annee' => $this->id_annee_active,
                'montant_total' => $montant,
                'id_utilisateur' => $this->session->userdata('id_utilisateur'),
                'id_commande' => $id_commande,
                'date_edition' => date('Y-m-d H:i:s'),
            ]);
            if ($id_recu) return $id_recu;
            $numero = $this->_generate_numero_recu();
            $uuid = generate_uuid();
        }
        return null;
    }

    private function _generate_numero_recu() {
        $prefix = 'RECU-' . date('Ymd') . '-';
        $this->db->select('numero_recu');
        $this->db->from('recus');
        $this->db->like('numero_recu', $prefix, 'after');
        $this->db->order_by('numero_recu', 'DESC');
        $this->db->limit(1);
        $q_r = $this->db->get();
        $q = $q_r !== false ? $q_r->row_array() : null;
        if ($q && preg_match('/RECU-\d{8}-(\d+)$/', $q['numero_recu'], $m)) {
            $next = (int)$m[1] + 1;
        } else {
            $next = 1;
        }
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
