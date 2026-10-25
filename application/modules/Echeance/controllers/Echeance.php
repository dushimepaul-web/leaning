<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Echeance extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des échéances';
        $this->db->select('e.*, i.id_classe, i.id_section');
        $this->db->from('etudiants e');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL AND i.id_annee = ' . (int)$this->id_annee_active, 'left');
        $this->db->where('e.deleted_at', null);
        $q_e = $this->db->get();
        $data['etudiants'] = $q_e !== false ? $q_e->result_array() : array();
        $data['frais'] = $this->Model->read('frais', [
            'deleted_at' => null,
            'id_annee' => $this->id_annee_active,
            'id_periode' => $this->id_periode_active
        ]);
        $data['id_annee_active'] = $this->id_annee_active;
        $data['id_periode_active'] = $this->id_periode_active;
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('e.deleted_at', null);
        $this->db->select("e.*, et.fullname AS nom, '' AS prenom, et.matricule, tf.libelle as type_frais");
        $this->db->from('echeances e');
        $this->db->join('etudiants et', 'e.id_etudiant = et.id_etudiant', 'left');
        $this->db->join('frais f', 'e.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->where('((f.id_annee = ' . (int)$this->id_annee_active . ' AND f.id_periode = ' . (int)$this->id_periode_active . ') OR e.id_frais IS NULL)', null, false);
        $id_etudiant = (int)$this->input->get('id_etudiant');
        if ($id_etudiant > 0) {
            $this->db->where('e.id_etudiant', $id_etudiant);
        }
        $id_frais = (int)$this->input->get('id_frais');
        if ($id_frais > 0) {
            $this->db->where('e.id_frais', $id_frais);
        }
        $this->db->order_by('e.date_echeance', 'ASC');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_get($id) {
        $this->db->where('e.uuid', $id);
        $this->db->select("e.*, et.fullname AS nom, '' AS prenom, et.matricule, tf.libelle as type_frais");
        $this->db->from('echeances e');
        $this->db->join('etudiants et', 'e.id_etudiant = et.id_etudiant', 'left');
        $this->db->join('frais f', 'e.id_frais = f.id_frais', 'left');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $q = $this->db->get();
        $d = $q !== false ? $q->row_array() : null;
        if (!$d) { $this->json_error('Échéance non trouvée', 404); return; }
        $this->json_success($d);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_etudiant']) || !isset($data['montant']) || $data['montant'] === '' || empty($data['date_echeance'])) {
            $this->json_error('Étudiant, montant et date d\'échéance obligatoires'); return;
        }
        $montant = floatval($data['montant']);
        if ($montant <= 0) { $this->json_error('Montant invalide'); return; }
        if (strtotime($data['date_echeance']) === false) { $this->json_error('Date d\'échéance invalide'); return; }
        $etudiant = $this->Model->readOne('etudiants', ['id_etudiant' => $data['id_etudiant'], 'deleted_at' => null]);
        if (!$etudiant) { $this->json_error('Étudiant introuvable'); return; }
        if (!empty($data['id_frais'])) {
            $frais = $this->Model->readOne('frais', ['id_frais' => $data['id_frais'], 'deleted_at' => null]);
            if (!$frais) { $this->json_error('Frais introuvable'); return; }
            if (!empty($frais['id_annee']) && (int)$frais['id_annee'] !== (int)$this->id_annee_active) {
                $this->json_error('Ce frais n\'appartient pas à l\'année en cours'); return;
            }
            if ((int)$frais['id_periode'] !== (int)$this->id_periode_active) {
                $this->json_error('Ce frais n\'appartient pas au trimestre en cours'); return;
            }
            $insc = $this->Model->readOne('inscriptions', ['id_etudiant' => $data['id_etudiant'], 'id_annee' => $this->id_annee_active, 'deleted_at' => null]);
            if ($insc && !empty($frais['id_classe']) && (int)$frais['id_classe'] !== (int)$insc['id_classe']) {
                $this->json_error('Ce frais ne correspond pas à la classe de l\'étudiant'); return;
            }
        }
        $allowed = ['id_etudiant', 'id_frais', 'montant', 'date_echeance'];
        $clean = array_intersect_key($data, array_flip($allowed));
        $clean['statut'] = 'impaye';
        $clean['montant'] = $montant;
        $id = $this->Model->createLastId('echeances', $clean);
        if ($id) $this->json_success(['id_echeance' => $id], 'Échéance créée');
        else $this->json_error('Erreur de création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (isset($data['id_etudiant']) && !empty($data['id_etudiant'])) {
            $etudiant = $this->Model->readOne('etudiants', ['id_etudiant' => $data['id_etudiant'], 'deleted_at' => null]);
            if (!$etudiant) { $this->json_error('Étudiant introuvable'); return; }
        }
        if (isset($data['id_frais']) && !empty($data['id_frais'])) {
            $frais = $this->Model->readOne('frais', ['id_frais' => $data['id_frais'], 'deleted_at' => null]);
            if (!$frais) { $this->json_error('Frais introuvable'); return; }
            if ((int)$frais['id_annee'] !== (int)$this->id_annee_active || (int)$frais['id_periode'] !== (int)$this->id_periode_active) {
                $this->json_error('Ce frais n\'appartient pas à l\'année et au trimestre en cours'); return;
            }
        }
        if (isset($data['montant']) && $data['montant'] !== '') {
            $montant = floatval($data['montant']);
            if ($montant <= 0) { $this->json_error('Montant invalide'); return; }
            $data['montant'] = $montant;
        }
        if (!empty($data['date_echeance']) && strtotime($data['date_echeance']) === false) {
            $this->json_error('Date d\'échéance invalide'); return;
        }
        $allowed = ['id_etudiant', 'id_frais', 'montant', 'date_echeance'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if ($this->Model->update('echeances', ['uuid' => $id], $update))
            $this->json_success(null, 'Échéance mise à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_generer() {
        $data = $this->get_json_input();
        if (empty($data['id_frais'])) {
            $this->json_error('Frais obligatoire'); return;
        }
        $frais = $this->Model->readOne('frais', [
            'id_frais' => $data['id_frais'],
            'id_annee' => $this->id_annee_active,
            'id_periode' => $this->id_periode_active,
            'deleted_at' => null
        ]);
        if (!$frais) { $this->json_error('Frais introuvable pour l\'année et le trimestre en cours'); return; }
        $nb = isset($data['nb_echeances']) ? intval($data['nb_echeances']) : 0;
        if ($nb < 1 || $nb > 24) { $this->json_error('Nombre d\'échéances invalide (1 à 24)'); return; }
        $date_premiere = !empty($data['date_premiere']) ? $data['date_premiere'] : (!empty($frais['echeance']) ? $frais['echeance'] : '');
        if (empty($date_premiere)) { $this->json_error('Date de la 1ère échéance obligatoire'); return; }
        if (strtotime($date_premiere) === false) { $this->json_error('Date de départ invalide'); return; }
        $intervalle = isset($data['intervalle_mois']) ? max(1, intval($data['intervalle_mois'])) : 1;
        $remplacer = !empty($data['remplacer']);

        $this->db->select('i.id_etudiant, e.is_interne');
        $this->db->from('inscriptions i');
        $this->db->join('etudiants e', 'e.id_etudiant = i.id_etudiant AND e.deleted_at IS NULL', 'inner');
        $this->db->where('i.id_annee', $this->id_annee_active);
        $this->db->where('i.deleted_at', null);
        if (!empty($frais['id_classe'])) {
            $this->db->where('i.id_classe', (int)$frais['id_classe']);
        }
        $this->db->group_by('i.id_etudiant');
        $inscrits = $this->db->get()->result_array();
        if (!$inscrits) {
            $this->json_error(!empty($frais['id_classe']) ? 'Aucun étudiant inscrit dans cette classe' : 'Aucun étudiant inscrit pour cette année');
            return;
        }

        $base_ts = strtotime($date_premiere);
        $dates = array();
        for ($k = 0; $k < $nb; $k++) {
            $dates[] = date('Y-m-d', strtotime('+' . ($k * $intervalle) . ' month', $base_ts));
        }

        $created = 0; $skipped = 0;
        $this->db->trans_begin();
        foreach ($inscrits as $ins) {
            $etu = (int)$ins['id_etudiant'];
            $existing = $this->Model->readOne('echeances', ['id_etudiant' => $etu, 'id_frais' => $frais['id_frais'], 'deleted_at' => null]);
            if ($existing) {
                if (!$remplacer) { $skipped++; continue; }
                if ($this->Model->readOne('paiements', ['id_etudiant' => $etu, 'id_frais' => $frais['id_frais'], 'deleted_at' => null])) { $skipped++; continue; }
                $this->Model->update('echeances', ['id_etudiant' => $etu, 'id_frais' => $frais['id_frais'], 'deleted_at' => null], ['deleted_at' => date('Y-m-d H:i:s')]);
            }
            $montant_frais = floatval($frais['montant']);
            if (!empty($ins['is_interne']) && $frais['montant_interne'] !== null) {
                $montant_frais = floatval($frais['montant_interne']);
            }
            $total = $montant_frais - $this->Model->exoneration_totale($etu, $frais['id_frais'], $this->id_annee_active, $montant_frais);
            if ($total < 0) $total = 0;
            $part = floor(($total / $nb) * 100) / 100;
            for ($k = 0; $k < $nb; $k++) {
                $m = ($k === $nb - 1) ? round($total - $part * ($nb - 1), 2) : $part;
                $this->Model->create('echeances', [
                    'id_etudiant' => $etu,
                    'id_frais' => $frais['id_frais'],
                    'montant' => max(0, $m),
                    'date_echeance' => $dates[$k],
                    'statut' => 'impaye',
                ]);
                $created++;
            }
        }
        if ($this->db->trans_status() === false) { $this->db->trans_rollback(); $this->json_error('Erreur lors de la génération'); return; }
        $this->db->trans_commit();
        $this->json_success(['created' => $created, 'skipped' => $skipped], "$created échéance(s) créée(s)" . ($skipped ? ", $skipped étudiant(s) ignoré(s)" : ''));
    }

    public function api_delete($id) {
        $e = $this->Model->readOne('echeances', ['uuid' => $id, 'deleted_at' => null]);
        if (!$e) { $this->json_error('Échéance introuvable', 404); return; }
        if ($this->Model->readOne('paiements', ['id_echeance' => $e['id_echeance'], 'deleted_at' => null])) {
            $this->json_error('Suppression impossible : des paiements sont rattachés à cette échéance.'); return;
        }
        if ($this->Model->update('echeances', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Échéance supprimée');
        else $this->json_error('Erreur de suppression');
    }
}
