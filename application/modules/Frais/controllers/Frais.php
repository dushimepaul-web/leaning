<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Frais extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des frais';
        $data['types_frais'] = $this->Model->read('types_frais', ['deleted_at' => null]);
        $data['sections'] = $this->Model->read('sections', ['deleted_at' => null]);
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['id_annee_active'] = $this->id_annee_active;
        $data['annee_active'] = $this->Model->readOne('annees_scolaires', ['id_annee' => $this->id_annee_active, 'deleted_at' => null]);
        $data['periode_active'] = $this->_periode_active();
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('f.deleted_at', null);
        $id_annee = $this->input->get('id_annee');
        if ($id_annee) {
            $this->db->where('f.id_annee', (int)$id_annee);
        }
        $id_periode = $this->input->get('id_periode');
        if ($id_periode) {
            $this->db->where('f.id_periode', (int)$id_periode);
        }
        $this->db->select('f.*, tf.libelle as type_libelle, tf.code as type_code, c.libelle as classe_libelle, a.libelle as annee_libelle, p.libelle as periode_libelle');
        $this->db->from('frais f');
        $this->db->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left');
        $this->db->join('classes c', 'f.id_classe = c.id_classe', 'left');
        $this->db->join('annees_scolaires a', 'f.id_annee = a.id_annee', 'left');
        $this->db->join('periodes p', 'f.id_periode = p.id_periode', 'left');
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

    private function _periode_active() {
        if (empty($this->id_periode_active)) return null;
        return $this->Model->readOne('periodes', [
            'id_periode' => $this->id_periode_active,
            'id_annee' => $this->id_annee_active,
            'deleted_at' => null
        ]);
    }

    private function _where_classe($id_classe) {
        if ($id_classe === null || $id_classe === '') {
            $this->db->where('id_classe', null);
        } else {
            $this->db->where('id_classe', (int)$id_classe);
        }
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_type_frais']) || !isset($data['montant']) || $data['montant'] === '') {
            $this->json_error('Type de frais et montant obligatoires'); return;
        }
        $montant = floatval($data['montant']);
        if ($montant <= 0) { $this->json_error('Montant invalide'); return; }

        $montant_interne = null;
        if (isset($data['montant_interne']) && $data['montant_interne'] !== '') {
            $montant_interne = floatval($data['montant_interne']);
            if ($montant_interne < 0) { $this->json_error('Montant interne invalide'); return; }
        }

        $classes_cibles = [];
        $selection_classes = !empty($data['id_classes']) && is_array($data['id_classes']);
        if ($selection_classes) {
            foreach ($data['id_classes'] as $id_classe) {
                if ($id_classe === '' || $id_classe === null) continue;
                $id_classe = (int)$id_classe;
                if ($id_classe > 0 && !in_array($id_classe, $classes_cibles, true)) {
                    $classes_cibles[] = $id_classe;
                }
            }
        } elseif (!empty($data['id_classe']) && $data['id_classe'] !== 'all') {
            $classes_cibles[] = (int)$data['id_classe'];
        }
        if ($selection_classes && empty($classes_cibles)) {
            $this->json_error('Aucune classe valide sélectionnée'); return;
        }
        if (empty($classes_cibles)) {
            $classes_cibles = [null];
        } else {
            foreach ($classes_cibles as $id_classe) {
                if (!$this->Model->readOne('classes', ['id_classe' => $id_classe, 'deleted_at' => null])) {
                    $this->json_error('Classe introuvable'); return;
                }
            }
        }

        if (isset($data['echeance']) && $data['echeance'] !== '' && strtotime($data['echeance']) === false) {
            $this->json_error('Échéance invalide'); return;
        }
        $type_frais = $this->Model->readOne('types_frais', ['id_type_frais' => $data['id_type_frais'], 'deleted_at' => null]);
        if (!$type_frais) {
            $this->json_error('Type de frais introuvable'); return;
        }

        $periode = $this->_periode_active();
        if (!$periode) {
            $this->json_error('Aucun trimestre actif pour l\'année en cours'); return;
        }
        $id_periode = (int)$periode['id_periode'];

        foreach ($classes_cibles as $id_classe) {
            $this->db->where('id_type_frais', (int)$data['id_type_frais']);
            $this->db->where('id_annee', $this->id_annee_active);
            $this->db->where('id_periode', $id_periode);
            $this->db->where('deleted_at', null);
            $this->_where_classe($id_classe);
            if ($this->db->count_all_results('frais') > 0) {
                $this->json_error('Un frais identique existe déjà pour au moins une des classes sélectionnées et ce trimestre'); return;
            }
        }

        $base = [
            'id_type_frais' => (int)$data['id_type_frais'],
            'id_annee' => $this->id_annee_active,
            'id_periode' => $id_periode,
            'montant' => $montant,
            'montant_interne' => $montant_interne,
            'echeance' => !empty($data['echeance']) ? $data['echeance'] : null
        ];

        $total_attendu = count($classes_cibles);
        $this->db->trans_start();
        $created = [];
        foreach ($classes_cibles as $id_classe) {
            $insert = $base;
            $insert['id_classe'] = $id_classe;
            $id = $this->Model->createLastId('frais', $insert);
            if ($id) $created[] = (int)$id;
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === false || count($created) !== $total_attendu) {
            $this->json_error('Erreur de création'); return;
        }
        $this->json_success(['id_frais' => end($created), 'count' => count($created)], 'Frais créé');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $existing = $this->Model->readOne('frais', ['uuid' => $id, 'deleted_at' => null]);
        if (!$existing) { $this->json_error('Frais introuvable', 404); return; }
        if ((int)$existing['id_annee'] !== (int)$this->id_annee_active) {
            $this->json_error('Seuls les frais de l\'année active peuvent être modifiés'); return;
        }

        $allowed = ['id_type_frais', 'id_classe', 'montant', 'montant_interne', 'echeance'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }

        if (array_key_exists('id_type_frais', $update)) {
            if (!$this->Model->readOne('types_frais', ['id_type_frais' => $update['id_type_frais'], 'deleted_at' => null])) {
                $this->json_error('Type de frais introuvable'); return;
            }
            $update['id_type_frais'] = (int)$update['id_type_frais'];
        }
        if (array_key_exists('id_classe', $update)) {
            if ($update['id_classe'] === '' || $update['id_classe'] === 'all' || $update['id_classe'] === null) {
                $update['id_classe'] = null;
            } else {
                $update['id_classe'] = (int)$update['id_classe'];
                if (!$this->Model->readOne('classes', ['id_classe' => $update['id_classe'], 'deleted_at' => null])) {
                    $this->json_error('Classe introuvable'); return;
                }
            }
        }
        if (array_key_exists('montant', $update)) {
            $montant = floatval($update['montant']);
            if ($montant <= 0) { $this->json_error('Montant invalide'); return; }
            $update['montant'] = $montant;
        }
        if (array_key_exists('montant_interne', $update)) {
            if ($update['montant_interne'] === '' || $update['montant_interne'] === null) {
                $update['montant_interne'] = null;
            } else {
                $montant_interne = floatval($update['montant_interne']);
                if ($montant_interne < 0) { $this->json_error('Montant interne invalide'); return; }
                $update['montant_interne'] = $montant_interne;
            }
        }
        if (array_key_exists('echeance', $update)) {
            if ($update['echeance'] === '') $update['echeance'] = null;
            elseif (strtotime($update['echeance']) === false) { $this->json_error('Échéance invalide'); return; }
        }

        $eff_type = array_key_exists('id_type_frais', $update) ? (int)$update['id_type_frais'] : (int)$existing['id_type_frais'];
        $eff_classe = array_key_exists('id_classe', $update) ? $update['id_classe'] : $existing['id_classe'];

        $this->db->where('id_type_frais', $eff_type);
        $this->db->where('id_annee', (int)$existing['id_annee']);
        $this->db->where('id_periode', (int)$existing['id_periode']);
        $this->db->where('deleted_at', null);
        $this->db->where('id_frais !=', (int)$existing['id_frais']);
        $this->_where_classe($eff_classe);
        if ($this->db->count_all_results('frais') > 0) {
            $this->json_error('Un frais identique existe déjà pour cette classe et ce trimestre'); return;
        }

        $ok = $this->Model->update('frais', ['uuid' => $id], $update);
        if ($ok) $this->json_success(null, 'Frais mis à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        $f = $this->Model->readOne('frais', ['uuid' => $id, 'deleted_at' => null]);
        if (!$f) { $this->json_error('Frais introuvable', 404); return; }
        if ((int)$f['id_annee'] !== (int)$this->id_annee_active) {
            $this->json_error('Les frais des années précédentes sont conservés dans l\'historique.'); return;
        }

        if ($this->Model->readOne('echeances', ['id_frais' => $f['id_frais'], 'deleted_at' => null])) {
            $this->json_error('Suppression impossible : des échéances sont liées à ce frais.'); return;
        }
        if ($this->Model->readOne('paiements', ['id_frais' => $f['id_frais'], 'deleted_at' => null])) {
            $this->json_error('Suppression impossible : des paiements sont liés à ce frais.'); return;
        }
        if ($this->db->table_exists('dettes') && $this->Model->readOne('dettes', ['id_frais' => $f['id_frais'], 'deleted_at' => null])) {
            $this->json_error('Suppression impossible : des dettes Minervale sont liées à ce frais.'); return;
        }

        if ($this->db->update('frais', ['deleted_at' => date('Y-m-d H:i:s')], ['id_frais' => $f['id_frais']])) {
            $this->json_success(null, 'Frais supprimé');
        } else {
            $this->json_error('Erreur de suppression');
        }
    }
}
