<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paiements extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des paiements';
        $this->db->select('e.*, i.id_classe, i.id_section, c.libelle as classe_libelle, s.libelle as section_libelle');
        $this->db->from('etudiants e');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL AND i.id_annee = '.(int)$this->id_annee_active, 'left');
        $this->db->join('classes c', 'i.id_classe = c.id_classe', 'left');
        $this->db->join('sections s', 'i.id_section = s.id_section', 'left');
        $this->db->where('e.deleted_at', null);
        $data['etudiants'] = $this->db->get()->result_array();
        $data['types_frais'] = $this->Model->read('types_frais', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('p.deleted_at', null);
        $this->db->select('p.*, e.nom, e.prenom, e.matricule, tf.libelle as type_frais, tf.code as type_code, i.id_classe, i.id_section');
        $this->db->from('paiements p');
        $this->db->join('etudiants e', 'p.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('frais f', 'p.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->join('inscriptions i', 'p.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL AND i.id_annee = '.(int)$this->id_annee_active, 'left');
        $this->db->order_by('p.id_paiement', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_get($id) {
        $this->db->where('p.uuid', $id);
        $this->db->select('p.*, e.nom, e.prenom, e.matricule, tf.libelle as type_frais');
        $this->db->from('paiements p');
        $this->db->join('etudiants e', 'p.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('frais f', 'p.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $d = $this->db->get()->row_array();
        if (!$d) { $this->json_error('Paiement non trouvé', 404); return; }
        $this->json_success($d);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant']) || empty($data['montant']) || empty($data['id_frais'])) {
            $this->json_error('Étudiant, frais et montant obligatoires'); return;
        }

        // Vérifier que le frais existe
        $frais_existe = $this->Model->readOne('frais', ['id_frais' => $data['id_frais'], 'deleted_at' => null]);
        if (!$frais_existe) {
            $this->json_error('Frais sélectionné inexistant (id_frais=' . $data['id_frais'] . ')'); return;
        }

        $frais_id = $data['id_frais'];

        $id_utilisateur = $this->session->userdata('id_utilisateur') ?? null;
        $insert = [
            'id_etudiant' => $data['id_etudiant'],
            'id_frais' => $frais_id,
            'id_annee' => $this->id_annee_active,
            'montant' => $data['montant'],
            'mode_paiement' => $data['mode_paiement'] ?? 'especes',
            'reference' => $data['reference'] ?? null,
            'date_paiement' => $data['date_paiement'] ?? date('Y-m-d'),
            'statut' => $data['statut'] ?? 'partiel',
            'notes' => $data['notes'] ?? null,
            'id_utilisateur' => $id_utilisateur
        ];

        $id = $this->Model->createLastId('paiements', $insert);
        if (!$id) { $this->json_error('Erreur d\'enregistrement'); return; }

        $numero_recu = $this->_generate_numero_recu();
        $this->load->helper('uuid');
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

    public function api_update($id) {
        $data = $this->get_json_input();
        if ($this->Model->update('paiements', ['uuid' => $id], $data))
            $this->json_success(null, 'Paiement mis à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('paiements', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Paiement supprimé');
        else $this->json_error('Erreur de suppression');
    }
}
