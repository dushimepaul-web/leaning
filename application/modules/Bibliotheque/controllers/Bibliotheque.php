<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bibliotheque extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Bibliothèque';
        $this->load->view('index', $data);
    }

    public function api_get($id) {
        $r = $this->Model->readOne('bibliotheque_livres', ['uuid' => $id]);
        if (!$r) { $this->json_error('Livre non trouvé', 404); return; }
        $this->json_success($r);
    }

    public function api_list() {
        $this->db->where('deleted_at', null);
        $this->db->order_by('id_livre', 'DESC');
        $this->json_success($this->db->get('bibliotheque_livres')->result_array());
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['titre'])) { $this->json_error('Titre obligatoire'); return; }
        $this->load->helper('uuid');
        $insert = [
            'uuid' => generate_uuid(),
            'titre' => $data['titre'],
            'auteur' => $data['auteur'] ?? null,
            'isbn' => $data['isbn'] ?? null,
            'editeur' => $data['editeur'] ?? null,
            'annee' => $data['annee_publication'] ?? null,
            'quantite' => $data['quantite_totale'] ?? 1,
            'disponible' => $data['quantite_disponible'] ?? $data['quantite_totale'] ?? 1,
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ];
        $id = $this->Model->createLastId('bibliotheque_livres', $insert);
        if ($id) $this->json_success(['id_livre' => $id], 'Livre ajouté');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $update = ['modifie_le' => date('Y-m-d H:i:s')];
        if (isset($data['annee_publication'])) { $update['annee'] = $data['annee_publication']; unset($data['annee_publication']); }
        if (isset($data['quantite_totale'])) { $update['quantite'] = $data['quantite_totale']; unset($data['quantite_totale']); }
        if (isset($data['quantite_disponible'])) { $update['disponible'] = $data['quantite_disponible']; unset($data['quantite_disponible']); }
        $update = array_merge($update, $data);
        if ($this->Model->update('bibliotheque_livres', ['uuid' => $id], $update))
            $this->json_success(null, 'Livre mis à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('bibliotheque_livres', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s'), 'modifie_le' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Livre supprimé');
        else $this->json_error('Erreur');
    }
}
