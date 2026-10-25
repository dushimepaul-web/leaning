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
        $this->db->where('f.id_periode', $this->id_periode_active);
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
                'id_periode' => $this->id_periode_active,
                'deleted_at' => null
            ]);
            if (!$frais_candidat) { $this->json_error('Aucun frais configuré pour ce type, cette classe et ce trimestre'); return; }
            $frais_id = $frais_candidat['id_frais'];
        }

        $echeance_id = null;
        if (!empty($data['id_echeance'])) {
            $ech = $this->Model->readOne('echeances', ['id_echeance' => $data['id_echeance'], 'deleted_at' => null]);
            if (!$ech) { $this->json_error('Échéance introuvable'); return; }
            if ((int)$ech['id_etudiant'] !== (int)$data['id_etudiant']) { $this->json_error('Cette échéance n\'appartient pas à cet étudiant'); return; }
            $echeance_id = $ech['id_echeance'];
            if (empty($frais_id) && !empty($ech['id_frais'])) { $frais_id = $ech['id_frais']; }
        }

        if (!$frais_id) {
            $this->json_error('Aucun frais sélectionné. Veuillez choisir un frais.'); return;
        }

        $frais_existe = $this->Model->readOne('frais', [
            'id_frais' => $frais_id,
            'id_annee' => $this->id_annee_active,
            'id_periode' => $this->id_periode_active,
            'deleted_at' => null
        ]);
        if (!$frais_existe) {
            $this->json_error('Frais inexistant pour cette année et ce trimestre.'); return;
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

        // Statut : jamais "solde" si le montant total attendu n'est pas atteint
        $statut = isset($data['statut']) && in_array($data['statut'], ['partiel', 'solde', 'annule'], true) ? $data['statut'] : 'partiel';
        if ($statut !== 'annule') {
            $attendu = $this->_attendu($data['id_etudiant'], $frais_id, $echeance_id);
            $deja_paye = $this->_total_paye($data['id_etudiant'], $frais_id, $echeance_id);
            $reste = $attendu - $deja_paye;
            if ($montant > $reste + 0.009 && empty($data['depassement_autorise'])) {
                $this->json_error('Montant supérieur au solde restant (' . number_format(max(0, $reste), 2, ',', ' ') . ').');
                return;
            }
            $statut = ($deja_paye + $montant + 0.001 >= $attendu) ? 'solde' : 'partiel';
        }

        $this->db->trans_begin();
        $id_utilisateur = $this->session->userdata('id_utilisateur') ?? null;
        $insert = [
            'id_etudiant' => $data['id_etudiant'],
            'id_frais' => $frais_id,
            'id_echeance' => $echeance_id,
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

        if (!$id_recu) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de la génération du reçu'); return;
        }
        $this->Model->create('paiements_recus', [
            'uuid' => generate_uuid(),
            'id_recu' => $id_recu,
            'id_paiement' => $id,
        ]);

        $this->_sync_echeance($echeance_id);

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

    private function _exoneration_totale($id_etudiant, $id_frais) {
        return $this->Model->exoneration_totale($id_etudiant, $id_frais, $this->id_annee_active);
    }

    private function _attendu($id_etudiant, $id_frais, $id_echeance = null) {
        if ($id_echeance) {
            $ech = $this->Model->readOne('echeances', ['id_echeance' => $id_echeance]);
            if ($ech) return floatval($ech['montant']);
        }
        $f = $this->Model->readOne('frais', ['id_frais' => $id_frais]);
        if (!$f) return 0;
        return max(0, floatval($f['montant']) - $this->_exoneration_totale($id_etudiant, $id_frais));
    }

    private function _total_paye($id_etudiant, $id_frais, $id_echeance = null, $exclude_id = null) {
        $this->db->select_sum('montant');
        $this->db->where('id_etudiant', $id_etudiant);
        $this->db->where('deleted_at', null);
        $this->db->where('statut !=', 'annule');
        if ($id_echeance) $this->db->where('id_echeance', $id_echeance);
        else $this->db->where('id_frais', $id_frais);
        if ($exclude_id) $this->db->where('id_paiement !=', $exclude_id);
        $r = $this->db->get('paiements')->row_array();
        return floatval($r['montant'] ?? 0);
    }

    private function _sync_echeance($id_echeance) {
        if (empty($id_echeance)) return;
        $ech = $this->Model->readOne('echeances', ['id_echeance' => $id_echeance]);
        if (!$ech) return;
        $paye = $this->_total_paye($ech['id_etudiant'], $ech['id_frais'], $id_echeance);
        if ($paye <= 0) $statut = 'impaye';
        elseif ($paye + 0.001 >= floatval($ech['montant'])) $statut = 'paye';
        else $statut = 'partiel';
        $this->Model->update('echeances', ['id_echeance' => $id_echeance], ['statut' => $statut]);
    }

    private function _sync_recu($id_paiement, $update = array()) {
        if (empty($update)) return;
        $links = $this->db->get_where('paiements_recus', ['id_paiement' => $id_paiement])->result_array();
        foreach ($links as $l) {
            $this->Model->update('recus', ['id_recu' => $l['id_recu']], $update);
        }
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $paiement = $this->Model->readOne('paiements', ['uuid' => $id]);
        if (!$paiement) { $this->json_error('Paiement non trouvé', 404); return; }
        $allowed = ['id_etudiant', 'id_frais', 'id_echeance', 'montant', 'mode_paiement', 'reference', 'preuve_paiement', 'statut', 'notes'];
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
            if ((int)$frais['id_annee'] !== (int)$this->id_annee_active || (int)$frais['id_periode'] !== (int)$this->id_periode_active) {
                $this->json_error('Ce frais n\'appartient pas à l\'année et au trimestre en cours'); return;
            }
            $inscription = $this->Model->readOne('inscriptions', [
                'id_etudiant' => $update['id_etudiant'] ?? $paiement['id_etudiant'],
                'id_annee' => $frais['id_annee'],
                'deleted_at' => null
            ]);
            if (!$inscription || (int)$inscription['id_classe'] !== (int)$frais['id_classe']) {
                $this->json_error('Ce frais ne correspond pas à la classe de l\'étudiant.'); return;
            }
        }
        if (isset($update['id_echeance']) && $update['id_echeance'] !== '' && $update['id_echeance'] !== null) {
            $ech = $this->Model->readOne('echeances', ['id_echeance' => $update['id_echeance'], 'deleted_at' => null]);
            if (!$ech) { $this->json_error('Échéance introuvable'); return; }
            if ((int)$ech['id_etudiant'] !== (int)($update['id_etudiant'] ?? $paiement['id_etudiant'])) {
                $this->json_error('Cette échéance n\'appartient pas à cet étudiant'); return;
            }
        }
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if (isset($update['montant'])) {
            $eff_etu = $update['id_etudiant'] ?? $paiement['id_etudiant'];
            $eff_frais = $update['id_frais'] ?? $paiement['id_frais'];
            $eff_ech = array_key_exists('id_echeance', $update) ? $update['id_echeance'] : $paiement['id_echeance'];
            $attendu = $this->_attendu($eff_etu, $eff_frais, $eff_ech);
            $deja = $this->_total_paye($eff_etu, $eff_frais, $eff_ech, $paiement['id_paiement']);
            if ($update['montant'] > ($attendu - $deja) + 0.009 && empty($data['depassement_autorise'])) {
                $this->json_error('Montant supérieur au solde restant.'); return;
            }
        }

        $old_echeance = $paiement['id_echeance'];
        $this->db->trans_start();
        if ($this->Model->update('paiements', ['uuid' => $id], $update)) {
            if (isset($update['montant']) || isset($update['id_etudiant']) || isset($update['id_frais']) || isset($update['id_echeance'])) {
                $new_montant = $update['montant'] ?? $paiement['montant'];
                $new_etudiant = $update['id_etudiant'] ?? $paiement['id_etudiant'];
                $this->_sync_recu($paiement['id_paiement'], [
                    'montant_total' => $new_montant,
                    'id_etudiant' => $new_etudiant,
                ]);
            }
            $this->_sync_echeance($old_echeance);
            $this->_sync_echeance($update['id_echeance'] ?? $old_echeance);
            $this->db->trans_complete();
            if ($this->db->trans_status() === false) { $this->json_error('Erreur de mise à jour'); return; }
            $this->json_success(null, 'Paiement mis à jour');
        } else {
            $this->db->trans_rollback();
            $this->json_error('Erreur de mise à jour');
        }
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
        $paiement = $this->Model->readOne('paiements', ['uuid' => $id]);
        if (!$paiement) { $this->json_error('Paiement non trouvé', 404); return; }
        $this->db->trans_start();
        $this->Model->update('paiements', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]);
        // Soft-delete the linked receipt(s) if no other active payment references them
        $links = $this->db->get_where('paiements_recus', ['id_paiement' => $paiement['id_paiement']])->result_array();
        foreach ($links as $l) {
            $others = $this->db->where('id_recu', $l['id_recu'])->where('id_paiement !=', $paiement['id_paiement'])->get('paiements_recus')->num_rows();
            if ($others == 0) {
                $this->Model->update('recus', ['id_recu' => $l['id_recu']], ['deleted_at' => date('Y-m-d H:i:s')]);
            }
        }
        $this->_sync_echeance($paiement['id_echeance']);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->json_error('Erreur de suppression');
        } else {
            $this->json_success(null, 'Paiement supprimé');
        }
    }

    // ---------- Relevé de compte & impayés ----------
    public function api_releve($id_etudiant) {
        $id_etudiant = (int)$id_etudiant;
        $etudiant = $this->Model->readOne('etudiants', ['id_etudiant' => $id_etudiant, 'deleted_at' => null]);
        if (!$etudiant) { $this->json_error('Étudiant introuvable', 404); return; }
        $insc = $this->Model->readOne('inscriptions', ['id_etudiant' => $id_etudiant, 'id_annee' => $this->id_annee_active, 'deleted_at' => null]);

        $frais_list = array();
        if ($insc) {
            $this->db->select('f.*, tf.libelle as type_libelle, tf.code as type_code');
            $this->db->from('frais f');
            $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
            $this->db->where('f.id_classe', $insc['id_classe']);
            $this->db->where('f.id_annee', $this->id_annee_active);
            $this->db->where('f.id_periode', $this->id_periode_active);
            $this->db->where('f.deleted_at', null);
            $frais_list = $this->db->get()->result_array();
        }

        $total_attendu = 0; $total_paye = 0; $lignes = array();
        foreach ($frais_list as $f) {
            $exo = $this->Model->exoneration_totale($id_etudiant, $f['id_frais'], $this->id_annee_active);
            $attendu = max(0, floatval($f['montant']) - $exo);
            $paye = $this->_total_paye($id_etudiant, $f['id_frais']);
            $reste = max(0, $attendu - $paye);
            $total_attendu += $attendu; $total_paye += $paye;
            $lignes[] = array(
                'id_frais' => (int)$f['id_frais'],
                'type_libelle' => $f['type_libelle'] ?? '',
                'montant' => floatval($f['montant']),
                'exoneration' => $exo,
                'attendu' => $attendu,
                'paye' => $paye,
                'reste' => $reste,
                'statut' => ($reste <= 0.009) ? 'solde' : (($paye > 0) ? 'partiel' : 'impaye'),
            );
        }

        $this->db->select('e.*, tf.libelle as type_libelle');
        $this->db->from('echeances e');
        $this->db->join('frais f', 'e.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->where('e.id_etudiant', $id_etudiant);
        $this->db->where('e.deleted_at', null);
        $this->db->where('e.statut !=', 'annule');
        $this->db->order_by('e.date_echeance', 'ASC');
        $echeances = $this->db->get()->result_array();

        $today = date('Y-m-d');
        $retard = array();
        foreach ($echeances as $e) {
            if ($e['statut'] !== 'paye' && substr($e['date_echeance'], 0, 10) < $today) { $retard[] = $e; }
        }

        $this->json_success(array(
            'etudiant' => array('id_etudiant' => $id_etudiant, 'nom' => $etudiant['fullname'] ?? '', 'matricule' => $etudiant['matricule'] ?? ''),
            'lignes' => $lignes,
            'totaux' => array('attendu' => $total_attendu, 'paye' => $total_paye, 'reste' => max(0, $total_attendu - $total_paye)),
            'echeances' => $echeances,
            'retards' => $retard,
        ));
    }

    public function api_impayes() {
        $this->db->select('i.id_etudiant, e.fullname AS nom, e.matricule, i.id_classe, c.libelle as classe_libelle, i.id_section, s.libelle as section_libelle');
        $this->db->from('inscriptions i');
        $this->db->join('etudiants e', 'i.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('classes c', 'i.id_classe = c.id_classe', 'left');
        $this->db->join('sections s', 'i.id_section = s.id_section', 'left');
        $this->db->where('i.id_annee', $this->id_annee_active);
        $this->db->where('i.deleted_at', null);
        $this->db->where('e.deleted_at', null);
        $insc = $this->db->get()->result_array();

        $out = array();
        foreach ($insc as $ins) {
            $etu = (int)$ins['id_etudiant'];
            $this->db->select('f.id_frais, f.montant');
            $this->db->from('frais f');
            $this->db->where('f.id_classe', $ins['id_classe']);
            $this->db->where('f.id_annee', $this->id_annee_active);
            $this->db->where('f.id_periode', $this->id_periode_active);
            $this->db->where('f.deleted_at', null);
            $frais_list = $this->db->get()->result_array();
            $attendu = 0; $paye = 0;
            foreach ($frais_list as $f) {
                $attendu += max(0, floatval($f['montant']) - $this->Model->exoneration_totale($etu, $f['id_frais'], $this->id_annee_active));
                $paye += $this->_total_paye($etu, $f['id_frais']);
            }
            $reste = max(0, $attendu - $paye);
            if ($reste > 0.009) {
                $out[] = array(
                    'id_etudiant' => $etu,
                    'nom' => $ins['nom'] ?? '',
                    'matricule' => $ins['matricule'] ?? '',
                    'classe_libelle' => $ins['classe_libelle'] ?? '',
                    'section_libelle' => $ins['section_libelle'] ?? '',
                    'attendu' => $attendu,
                    'paye' => $paye,
                    'reste' => $reste,
                );
            }
        }
        usort($out, function ($a, $b) { return $b['reste'] <=> $a['reste']; });
        $this->json_success($out);
    }

    // ---------- Exonérations / bourses ----------
    public function api_exonerations_list() {
        $id_etudiant = $this->input->get('id_etudiant');
        $this->db->where('ex.deleted_at', null);
        $this->db->where('ex.id_annee', $this->id_annee_active);
        if ($id_etudiant) $this->db->where('ex.id_etudiant', $id_etudiant);
        $this->db->select('ex.*, e.fullname AS nom, e.matricule, tf.libelle as type_libelle');
        $this->db->from('exonerations ex');
        $this->db->join('etudiants e', 'ex.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('frais f', 'ex.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->order_by('ex.id_exoneration', 'DESC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_exonerations_create() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant']) || !isset($data['valeur']) || $data['valeur'] === '') {
            $this->json_error('Étudiant et valeur obligatoires'); return;
        }
        $mode = in_array($data['mode'] ?? '', ['pourcentage', 'montant'], true) ? $data['mode'] : 'montant';
        $valeur = floatval($data['valeur']);
        if ($valeur <= 0) { $this->json_error('Valeur invalide'); return; }
        if ($mode === 'pourcentage' && $valeur > 100) { $this->json_error('Le pourcentage ne peut dépasser 100'); return; }
        if (!$this->Model->readOne('etudiants', ['id_etudiant' => $data['id_etudiant'], 'deleted_at' => null])) {
            $this->json_error('Étudiant introuvable'); return;
        }
        if (!empty($data['id_frais'])) {
            $frais = $this->Model->readOne('frais', ['id_frais' => $data['id_frais'], 'deleted_at' => null]);
            if (!$frais) { $this->json_error('Frais introuvable'); return; }
            if ((int)$frais['id_annee'] !== (int)$this->id_annee_active || (int)$frais['id_periode'] !== (int)$this->id_periode_active) {
                $this->json_error('Ce frais n\'appartient pas à l\'année et au trimestre en cours'); return;
            }
            if ($mode === 'montant' && $valeur > floatval($frais['montant'])) { $this->json_error('Montant supérieur au frais'); return; }
        }
        $allowed = ['id_etudiant', 'id_frais', 'mode', 'valeur', 'motif'];
        $insert = array_intersect_key($data, array_flip($allowed));
        $insert['id_annee'] = $this->id_annee_active;
        $insert['mode'] = $mode;
        $insert['valeur'] = $valeur;
        $insert['statut'] = 'active';
        $insert['id_utilisateur'] = $this->session->userdata('id_utilisateur');
        $id = $this->Model->createLastId('exonerations', $insert);
        if ($id) $this->json_success(['id_exoneration' => $id], 'Exonération enregistrée');
        else $this->json_error('Erreur de création');
    }

    public function api_exonerations_update($id) {
        $data = $this->get_json_input();
        $exo = $this->Model->readOne('exonerations', ['uuid' => $id, 'deleted_at' => null]);
        if (!$exo) { $this->json_error('Exonération introuvable', 404); return; }
        $allowed = ['mode', 'valeur', 'motif', 'id_frais', 'statut'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if (isset($update['mode']) && !in_array($update['mode'], ['pourcentage', 'montant'], true)) { $this->json_error('Mode invalide'); return; }
        if (isset($update['valeur'])) {
            $update['valeur'] = floatval($update['valeur']);
            if ($update['valeur'] <= 0) { $this->json_error('Valeur invalide'); return; }
            $mode = $update['mode'] ?? $exo['mode'];
            if ($mode === 'pourcentage' && $update['valeur'] > 100) { $this->json_error('Le pourcentage ne peut dépasser 100'); return; }
        }
        if (isset($update['statut']) && !in_array($update['statut'], ['active', 'annulee'], true)) { $this->json_error('Statut invalide'); return; }
        if ($this->Model->update('exonerations', ['uuid' => $id], $update)) $this->json_success(null, 'Exonération mise à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_exonerations_delete($id) {
        $exo = $this->Model->readOne('exonerations', ['uuid' => $id, 'deleted_at' => null]);
        if (!$exo) { $this->json_error('Exonération introuvable', 404); return; }
        if ($this->Model->update('exonerations', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')])) $this->json_success(null, 'Exonération supprimée');
        else $this->json_error('Erreur de suppression');
    }
}
