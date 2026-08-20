<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paiements extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des paiements';
        $this->db->select('e.*, e.fullname, i.id_classe, i.id_section, c.libelle as classe_libelle, s.libelle as section_libelle');
        $this->db->from('etudiants e');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL AND i.id_annee = '.(int)$this->id_annee_active, 'left');
        $this->db->join('classes c', 'i.id_classe = c.id_classe', 'left');
        $this->db->join('sections s', 'i.id_section = s.id_section', 'left');
        $this->db->where('e.deleted_at', null);
        $q_e = $this->db->get();
        $data['etudiants'] = $q_e !== false ? $q_e->result_array() : array();
        $data['types_frais'] = $this->Model->read('types_frais', ['deleted_at' => null]);
        $this->db->select('f.*, tf.libelle as type_libelle, c.libelle as classe_libelle, a.libelle as annee_libelle');
        $this->db->from('frais f');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->join('classes c', 'f.id_classe = c.id_classe', 'left');
        $this->db->join('annees_scolaires a', 'f.id_annee = a.id_annee', 'left');
        $this->db->where('f.deleted_at', null);
        $this->db->where('f.id_annee', $this->id_annee_active);
        $this->db->order_by('f.id_frais', 'DESC');
        $q_f = $this->db->get();
        $data['frais'] = $q_f !== false ? $q_f->result_array() : array();
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('p.deleted_at', null);
        $this->db->select("p.*, e.fullname AS nom, '' AS prenom, e.matricule, tf.libelle as type_frais, tf.code as type_code, i.id_classe, i.id_section");
        $this->db->from('paiements p');
        $this->db->join('etudiants e', 'p.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('frais f', 'p.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->join('inscriptions i', 'p.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL AND i.id_annee = '.(int)$this->id_annee_active, 'left');
        $this->db->order_by('p.id_paiement', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_get($id) {
        $this->db->where('p.uuid', $id);
        $this->db->select("p.*, e.fullname AS nom, '' AS prenom, e.matricule, tf.libelle as type_frais");
        $this->db->from('paiements p');
        $this->db->join('etudiants e', 'p.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('frais f', 'p.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $q = $this->db->get();
        $d = $q !== false ? $q->row_array() : null;
        if (!$d) { $this->json_error('Paiement non trouvé', 404); return; }
        $this->json_success($d);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant']) || !isset($data['montant']) || $data['montant'] === '') {
            $this->json_error('Étudiant et montant obligatoires'); return;
        }
        $montant = floatval($data['montant']);
        if ($montant <= 0) { $this->json_error('Montant invalide'); return; }
        $mode_paiement = $data['mode_paiement'] ?? 'especes';
        if (!in_array($mode_paiement, ['especes', 'banque', 'mobile_money', 'cheque'], true)) {
            $this->json_error('Mode de paiement invalide'); return;
        }
        $statut = $data['statut'] ?? 'partiel';
        if (!in_array($statut, ['partiel', 'solde', 'annule'], true)) {
            $this->json_error('Statut invalide'); return;
        }
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

        if (!$frais_id) {
            $this->json_error('Aucun frais sélectionné. Veuillez choisir un frais.'); return;
        }

        $frais_existe = $this->Model->readOne('frais', ['id_frais' => $frais_id, 'id_annee' => $this->id_annee_active, 'deleted_at' => null]);
        if (!$frais_existe) {
            $this->json_error('Frais inexistant pour cette année.'); return;
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
            'preuve_paiement' => $data['preuve_paiement'] ?? null,
            'date_paiement' => $data['date_paiement'] ?? date('Y-m-d'),
            'statut' => $statut,
            'notes' => $data['notes'] ?? null,
            'id_utilisateur' => $id_utilisateur
        ];

        $id = $this->Model->createLastId('paiements', $insert);
        if (!$id) { $this->db->trans_rollback(); $this->json_error('Erreur d\'enregistrement'); return; }

        $numero_recu = $this->_generate_numero_recu();
        $this->load->helper('uuid');
        $recu_uuid = generate_uuid();
        $id_recu = null;
        // Contrainte UNIQUE sur numero_recu : réessayer en cas de collision
        for ($tentative = 0; $tentative < 5; $tentative++) {
            $id_recu = $this->Model->createLastId('recus', [
                'uuid' => $recu_uuid,
                'numero_recu' => $numero_recu,
                'id_etudiant' => $data['id_etudiant'],
                'id_annee' => $this->id_annee_active,
                'montant_total' => $data['montant'],
                'id_utilisateur' => $id_utilisateur,
                'date_edition' => date('Y-m-d H:i:s'),
            ]);
            if ($id_recu) break;
            $numero_recu = $this->_generate_numero_recu();
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

    public function api_update($id) {
        $data = $this->get_json_input();
        $paiement = $this->Model->readOne('paiements', ['uuid' => $id]);
        if (!$paiement) { $this->json_error('Paiement non trouvé', 404); return; }
        $allowed = ['id_etudiant', 'id_frais', 'montant', 'mode_paiement', 'reference', 'preuve_paiement', 'statut', 'notes'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (isset($update['statut']) && !in_array($update['statut'], ['partiel', 'solde', 'annule'], true)) {
            $this->json_error('Statut invalide'); return;
        }
        if (isset($update['mode_paiement']) && !in_array($update['mode_paiement'], ['especes', 'banque', 'mobile_money', 'cheque'], true)) {
            $this->json_error('Mode de paiement invalide'); return;
        }
        if (isset($update['montant'])) {
            $update['montant'] = floatval($update['montant']);
            if ($update['montant'] <= 0) { $this->json_error('Montant invalide'); return; }
        }
        if (isset($update['id_etudiant'])) {
            if (!$this->Model->readOne('etudiants', ['id_etudiant' => $update['id_etudiant'], 'deleted_at' => null])) {
                $this->json_error('Étudiant introuvable'); return;
            }
        }
        if (isset($update['id_frais'])) {
            $frais = $this->Model->readOne('frais', ['id_frais' => $update['id_frais'], 'deleted_at' => null]);
            if (!$frais) { $this->json_error('Frais introuvable'); return; }
            $inscription = $this->Model->readOne('inscriptions', [
                'id_etudiant' => $update['id_etudiant'] ?? $paiement['id_etudiant'],
                'id_annee' => $frais['id_annee'],
                'deleted_at' => null
            ]);
            if (!$inscription || (int)$inscription['id_classe'] !== (int)$frais['id_classe']) {
                $this->json_error('Ce frais ne correspond pas à la classe de l\'étudiant.'); return;
            }
        }
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if ($this->Model->update('paiements', ['uuid' => $id], $update))
            $this->json_success(null, 'Paiement mis à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_upload_preuve() {
        $config['upload_path'] = FCPATH . 'assets/uploads/paiements/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif|webp|pdf';
        $config['max_size'] = 5120;
        $config['encrypt_name'] = true;
        if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('preuve')) {
            $this->json_error($this->upload->display_errors('', ''));
            return;
        }
        $data = $this->upload->data();
        $path = 'assets/uploads/paiements/' . $data['file_name'];
        $is_image = in_array($data['file_type'], ['image/jpeg','image/png','image/gif','image/webp']);
        $this->json_success(['path' => $path, 'is_image' => $is_image], 'Fichier uploadé');
    }

    public function api_delete($id) {
        if ($this->Model->update('paiements', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Paiement supprimé');
        else $this->json_error('Erreur de suppression');
    }
}
