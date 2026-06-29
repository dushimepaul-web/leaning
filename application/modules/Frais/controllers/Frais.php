<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Frais extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des frais';
        $data['types_frais'] = $this->Model->read('types_frais', ['deleted_at' => null]);
        $data['etudiants'] = $this->Model->read('etudiants', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_types() {
        $this->json_success($this->Model->read('types_frais', ['deleted_at' => null]));
    }

    public function api_list() {
        $this->db->where('f.deleted_at', null);
        $this->db->select('f.*, tf.libelle as type_libelle');
        $this->db->from('frais f');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->order_by('f.id_frais', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_get($id) {
        $f = $this->Model->readOne('frais', ['uuid' => $id]);
        if (!$f) { $this->json_error('Frais non trouvé', 404); return; }
        $f['echeances'] = $this->Model->read('echeances', ['id_frais' => $f['id_frais']]);
        $this->json_success($f);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_type_frais']) || empty($data['montant']) || empty($data['id_classe'])) {
            $this->json_error('Type de frais, classe et montant obligatoires'); return;
        }
        $id = $this->Model->createLastId('frais', $data);
        if ($id) $this->json_success(['id_frais' => $id], 'Frais créé');
        else $this->json_error('Erreur de création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if ($this->Model->update('frais', ['uuid' => $id], $data))
            $this->json_success(null, 'Frais mis à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('frais', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Frais supprimé');
        else $this->json_error('Erreur de suppression');
    }

    // Paiements
    public function api_paiements() {
        $this->db->where('p.deleted_at', null);
        $this->db->select('p.*, e.nom, e.prenom, e.matricule, tf.libelle as type_frais');
        $this->db->from('paiements p');
        $this->db->join('etudiants e', 'p.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('frais f', 'p.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->order_by('p.id_paiement', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_recus() {
        $this->json_success($this->Model->read('recus', ['deleted_at' => null]));
    }

    public function api_create_paiement() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant']) || empty($data['montant']) || empty($data['id_frais'])) {
            $this->json_error('Étudiant, frais et montant obligatoires'); return;
        }

        // Vérifier que le frais existe
        $frais_existe = $this->Model->readOne('frais', ['id_frais' => $data['id_frais'], 'deleted_at' => null]);
        if (!$frais_existe) {
            $this->json_error('Frais sélectionné inexistant (id_frais=' . $data['id_frais'] . ')'); return;
        }

        $id_utilisateur = $this->session->userdata('id_utilisateur') ?? null;
        $insert = [
            'id_etudiant' => $data['id_etudiant'],
            'id_frais' => $data['id_frais'],
            'id_annee' => $this->id_annee_active,
            'montant' => $data['montant'],
            'mode_paiement' => $data['mode_paiement'] ?? 'especes',
            'reference' => $data['reference'] ?? null,
            'date_paiement' => $data['date_paiement'] ?? date('Y-m-d'),
            'id_utilisateur' => $id_utilisateur
        ];

        $id = $this->Model->createLastId('paiements', $insert);
        if (!$id) { $this->json_error('Erreur d\'enregistrement'); return; }

        $this->load->helper('uuid');
        $numero_recu = $this->_generate_numero_recu();
        $recu_uuid = generate_uuid();
        $id_recu = $this->Model->createLastId('recus', [
            'uuid' => $recu_uuid,
            'numero_recu' => $numero_recu,
            'id_etudiant' => $data['id_etudiant'],
            'id_annee' => $this->id_annee_active,
            'montant_total' => $data['montant'],
            'id_utilisateur' => $id_utilisateur,
            'date_edition' => date('Y-m-d H:i:s'),
        ]);

        if ($id_recu) {
            $this->Model->create('paiements_recus', [
                'uuid' => generate_uuid(),
                'id_recu' => $id_recu,
                'id_paiement' => $id,
            ]);
        }

        $this->json_success([
            'id_paiement' => $id,
            'id_recu' => $id_recu,
            'recu_uuid' => $recu_uuid,
            'numero_recu' => $numero_recu
        ], 'Paiement enregistré avec reçu');
    }

    private function _generate_numero_recu() {
        $prefix = 'RECU-' . date('Ymd') . '-';
        $this->db->select('numero_recu');
        $this->db->from('recus');
        $this->db->like('numero_recu', $prefix, 'after');
        $this->db->order_by('numero_recu', 'DESC');
        $this->db->limit(1);
        $q = $this->db->get()->row_array();
        if ($q && preg_match('/RECU-\d{8}-(\d+)$/', $q['numero_recu'], $m)) {
            $next = (int)$m[1] + 1;
        } else {
            $next = 1;
        }
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
