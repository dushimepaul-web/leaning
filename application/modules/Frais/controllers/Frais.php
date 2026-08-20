<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Frais extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des frais';
        $data['types_frais'] = $this->Model->read('types_frais', ['deleted_at' => null]);
        $this->db->select('e.*, i.id_classe, i.id_section');
        $this->db->from('etudiants e');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL AND i.id_annee = '.(int)$this->id_annee_active, 'left');
        $this->db->where('e.deleted_at', null);
        $q_e = $this->db->get();
        $data['etudiants'] = $q_e !== false ? $q_e->result_array() : array();
        $data['sections'] = $this->Model->read('sections', ['deleted_at' => null]);
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $this->db->select('f.*, tf.libelle as type_libelle, c.libelle as classe_libelle, a.libelle as annee_libelle');
        $this->db->from('frais f');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->join('classes c', 'f.id_classe = c.id_classe', 'left');
        $this->db->join('annees_scolaires a', 'f.id_annee = a.id_annee', 'left');
        $this->db->where('f.deleted_at', null);
        $this->db->order_by('f.id_frais', 'DESC');
        $q_f = $this->db->get();
        $data['frais'] = $q_f !== false ? $q_f->result_array() : array();
        $this->load->view('index', $data);
    }

    public function api_types() {
        $this->json_success($this->Model->read('types_frais', ['deleted_at' => null]));
    }

    public function api_list() {
        $this->db->where('f.deleted_at', null);
        $this->db->select('f.*, tf.libelle as type_libelle, tf.code as type_code, c.libelle as classe_libelle, a.libelle as annee_libelle');
        $this->db->from('frais f');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->join('classes c', 'f.id_classe = c.id_classe', 'left');
        $this->db->join('annees_scolaires a', 'f.id_annee = a.id_annee', 'left');
        $this->db->order_by('f.id_frais', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
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
        $montant = floatval($data['montant']);
        if ($montant <= 0) { $this->json_error('Montant invalide'); return; }
        if (isset($data['echeance']) && $data['echeance'] !== '' && floatval($data['echeance']) < 0) {
            $this->json_error('Échéance invalide'); return;
        }
        if (!$this->Model->readOne('types_frais', ['id_type_frais' => $data['id_type_frais'], 'deleted_at' => null])) {
            $this->json_error('Type de frais introuvable'); return;
        }
        if (!$this->Model->readOne('classes', ['id_classe' => $data['id_classe'], 'deleted_at' => null])) {
            $this->json_error('Classe introuvable'); return;
        }
        $id_annee = !empty($data['id_annee']) ? $data['id_annee'] : $this->id_annee_active;
        if (!$this->Model->readOne('annees_scolaires', ['id_annee' => $id_annee, 'deleted_at' => null])) {
            $this->json_error('Année scolaire introuvable'); return;
        }
        $allowed = ['id_type_frais', 'id_classe', 'montant', 'echeance'];
        $insert = array_intersect_key($data, array_flip($allowed));
        $insert['id_annee'] = $id_annee;
        $insert['montant'] = $montant;
        $existe = $this->Model->readOne('frais', [
            'id_type_frais' => $insert['id_type_frais'],
            'id_classe' => $insert['id_classe'],
            'id_annee' => $insert['id_annee'],
            'deleted_at' => null
        ]);
        if ($existe) { $this->json_error('Un frais identique existe déjà pour cette classe et cette année'); return; }
        $id = $this->Model->createLastId('frais', $insert);
        if ($id) $this->json_success(['id_frais' => $id], 'Frais créé');
        else $this->json_error('Erreur de création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $allowed = ['id_type_frais', 'id_classe', 'id_annee', 'montant', 'echeance'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if (isset($update['id_type_frais']) && !$this->Model->readOne('types_frais', ['id_type_frais' => $update['id_type_frais'], 'deleted_at' => null])) {
            $this->json_error('Type de frais introuvable'); return;
        }
        if (isset($update['id_classe']) && !$this->Model->readOne('classes', ['id_classe' => $update['id_classe'], 'deleted_at' => null])) {
            $this->json_error('Classe introuvable'); return;
        }
        if (isset($update['id_annee']) && !$this->Model->readOne('annees_scolaires', ['id_annee' => $update['id_annee'], 'deleted_at' => null])) {
            $this->json_error('Année scolaire introuvable'); return;
        }
        if (isset($update['montant'])) {
            $montant = floatval($update['montant']);
            if ($montant <= 0) { $this->json_error('Montant invalide'); return; }
            $update['montant'] = $montant;
        }
        if (isset($update['echeance']) && floatval($update['echeance']) < 0) { $this->json_error('Échéance invalide'); return; }
        if ($this->Model->update('frais', ['uuid' => $id], $update))
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
        $this->db->select("p.*, e.fullname AS nom, '' AS prenom, e.matricule, tf.libelle as type_frais");
        $this->db->from('paiements p');
        $this->db->join('etudiants e', 'p.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('frais f', 'p.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->order_by('p.id_paiement', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_recus() {
        $this->json_success($this->Model->read('recus', ['deleted_at' => null]));
    }

    public function api_create_paiement() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant']) || !isset($data['montant']) || $data['montant'] === '') {
            $this->json_error('Étudiant et montant obligatoires'); return;
        }
        $montant = floatval($data['montant']);
        if ($montant <= 0) { $this->json_error('Montant invalide'); return; }
        $mode_paiement = $data['mode_paiement'] ?? 'especes';
        $modes = ['especes', 'banque', 'mobile_money', 'cheque'];
        if (!in_array($mode_paiement, $modes, true)) { $this->json_error('Mode de paiement invalide'); return; }
        if (!empty($data['date_paiement']) && strtotime($data['date_paiement']) === false) {
            $this->json_error('Date de paiement invalide'); return;
        }
        $etudiant = $this->Model->readOne('etudiants', ['id_etudiant' => $data['id_etudiant'], 'deleted_at' => null]);
        if (!$etudiant) { $this->json_error('Étudiant introuvable'); return; }

        $frais_id = null;
        if (!empty($data['id_frais'])) {
            $frais_id = $data['id_frais'];
        } elseif (!empty($data['id_type_frais'])) {
            $inscription = $this->Model->readOne('inscriptions', [
                'id_etudiant' => $data['id_etudiant'],
                'id_annee' => $this->id_annee_active,
                'deleted_at' => null
            ]);
            if (!$inscription) { $this->json_error('Aucune inscription pour cette année'); return; }
            $frais_candidat = $this->Model->readOne('frais', [
                'id_type_frais' => $data['id_type_frais'],
                'id_classe' => $inscription['id_classe'],
                'id_annee' => $this->id_annee_active,
                'deleted_at' => null
            ]);
            if (!$frais_candidat) { $this->json_error('Aucun frais configuré pour ce type et cette classe'); return; }
            $frais_id = $frais_candidat['id_frais'];
        }

        if (!$frais_id) { $this->json_error('Frais sélectionné obligatoire'); return; }

        // Vérifier que le frais existe et correspond à la classe de l'étudiant
        $frais_existe = $this->Model->readOne('frais', ['id_frais' => $frais_id, 'deleted_at' => null]);
        if (!$frais_existe) {
            $this->json_error('Frais sélectionné inexistant (id_frais=' . $frais_id . ')'); return;
        }
        if ((int)$frais_existe['id_annee'] !== (int)$this->id_annee_active) {
            $this->json_error('Ce frais ne correspond pas à l\'année en cours.'); return;
        }
        $inscription = $this->Model->readOne('inscriptions', [
            'id_etudiant' => $data['id_etudiant'],
            'id_annee' => $this->id_annee_active,
            'deleted_at' => null
        ]);
        if (!$inscription) { $this->json_error('Aucune inscription pour cette année'); return; }
        if ((int)$frais_existe['id_classe'] !== (int)$inscription['id_classe']) {
            $this->json_error('Ce frais ne correspond pas à la classe de l\'étudiant.'); return;
        }

        $this->db->trans_begin();
        $id_utilisateur = $this->session->userdata('id_utilisateur') ?? null;
        $insert = [
            'id_etudiant' => $data['id_etudiant'],
            'id_frais' => $frais_id,
            'id_annee' => $this->id_annee_active,
            'montant' => $montant,
            'mode_paiement' => $mode_paiement,
            'reference' => $data['reference'] ?? null,
            'date_paiement' => $data['date_paiement'] ?? date('Y-m-d'),
            'id_utilisateur' => $id_utilisateur
        ];

        $id = $this->Model->createLastId('paiements', $insert);
        if (!$id) { $this->db->trans_rollback(); $this->json_error('Erreur d\'enregistrement'); return; }

        $this->load->helper('uuid');
        $recu_uuid = generate_uuid();
        $id_recu = null;
        // Contrainte UNIQUE sur numero_recu : réessayer en cas de collision
        for ($tentative = 0; $tentative < 5; $tentative++) {
            $numero_recu = $this->_generate_numero_recu();
            $id_recu = $this->Model->createLastId('recus', [
                'uuid' => $recu_uuid,
                'numero_recu' => $numero_recu,
                'id_etudiant' => $data['id_etudiant'],
                'id_annee' => $this->id_annee_active,
                'montant_total' => $montant,
                'id_utilisateur' => $id_utilisateur,
                'date_edition' => date('Y-m-d H:i:s'),
            ]);
            if ($id_recu) break;
            $recu_uuid = generate_uuid();
        }

        if ($id_recu) {
            $this->Model->create('paiements_recus', [
                'uuid' => generate_uuid(),
                'id_recu' => $id_recu,
                'id_paiement' => $id,
            ]);
        }

        if ($this->db->trans_status() === false) { $this->db->trans_rollback(); $this->json_error('Erreur d\'enregistrement'); return; }
        $this->db->trans_commit();

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
