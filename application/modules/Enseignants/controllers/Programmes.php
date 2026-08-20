<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Programmes extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des programmes';
        $matieres = $this->db->where('deleted_at', null)->order_by('libelle')->get('matieres')->result_array();
        $data['matieres'] = array_map(function($m) { $m['id'] = (int)$m['id_matiere']; return $m; }, $matieres);
        $classes = $this->db->where('deleted_at', null)->order_by('libelle')->get('classes')->result_array();
        $data['classes'] = array_map(function($c) { $c['id'] = (int)$c['id_classe']; return $c; }, $classes);
        $data['sections'] = $this->Model->read('sections', ['deleted_at' => null], 'libelle', 'ASC');
        $this->load->view('programmes', $data);
    }

    public function api_list() {
        $this->db->select("mc.*, m.libelle AS matiere_libelle, m.code AS matiere_code, cl.libelle AS classe_libelle, e.fullname AS enseignant_fullname");
        $this->db->from('matieres_classes mc');
        $this->db->join('matieres m', 'mc.id_matiere = m.id_matiere');
        $this->db->join('classes cl', 'mc.id_classe = cl.id_classe');
        $this->db->join('enseignants e', 'mc.id_enseignant = e.id_enseignant', 'left');
        $this->db->where('mc.deleted_at', null);
        $this->db->order_by('cl.libelle, m.libelle');
        $q = $this->db->get();
        $this->json_success($q !== false ? $q->result_array() : array());
    }

    public function api_get($id) {
        $this->db->select("mc.*, m.libelle AS matiere_libelle, cl.libelle AS classe_libelle, e.fullname AS enseignant_fullname");
        $this->db->from('matieres_classes mc');
        $this->db->join('matieres m', 'mc.id_matiere = m.id_matiere');
        $this->db->join('classes cl', 'mc.id_classe = cl.id_classe');
        $this->db->join('enseignants e', 'mc.id_enseignant = e.id_enseignant', 'left');
        $this->db->where('mc.uuid', $id);
        $q_m = $this->db->get();
        $m = $q_m !== false ? $q_m->row_array() : null;
        if (!$m) { $this->json_error('Programme non trouvé', 404); return; }
        $this->json_success($m);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_matiere']) || empty($data['id_classe'])) {
            $this->json_error('Matière et classe sont obligatoires'); return;
        }

        $matiere = $this->Model->readOne('matieres', ['id_matiere' => $data['id_matiere'], 'deleted_at' => null]);
        if (!$matiere) { $this->json_error('Matière introuvable'); return; }
        $classe = $this->Model->readOne('classes', ['id_classe' => $data['id_classe'], 'deleted_at' => null]);
        if (!$classe) { $this->json_error('Classe introuvable'); return; }
        if (!empty($data['id_enseignant'])) {
            $ens = $this->Model->readOne('enseignants', ['id_enseignant' => $data['id_enseignant'], 'deleted_at' => null]);
            if (!$ens) { $this->json_error('Enseignant introuvable'); return; }
        }

        $coefficient = isset($data['coefficient']) ? floatval($data['coefficient']) : 1.0;
        $nb_heures_jour = isset($data['nb_heures_par_jour']) ? floatval($data['nb_heures_par_jour']) : 0.0;
        $nb_heures_sem = isset($data['nb_heures_par_semaine']) ? floatval($data['nb_heures_par_semaine']) : 0.0;

        if ($coefficient < 0 || $nb_heures_jour < 0 || $nb_heures_sem < 0) {
            $this->json_error('Les coefficients et les volumes horaires ne peuvent pas être négatifs'); return;
        }

        $existing = $this->Model->readOne('matieres_classes', [
            'id_matiere' => $data['id_matiere'],
            'id_classe' => $data['id_classe'],
            'deleted_at' => null
        ]);
        if ($existing) {
            $this->json_error('Cette matière est déjà associée à cette classe'); return;
        }

        $softDeleted = $this->Model->readOne('matieres_classes', [
            'id_matiere' => $data['id_matiere'],
            'id_classe' => $data['id_classe']
        ]);
        if ($softDeleted) {
            $updateData = [
                'deleted_at' => null,
                'coefficient' => $coefficient,
                'nb_heures_par_jour' => $nb_heures_jour,
                'nb_heures_par_semaine' => $nb_heures_sem
            ];
            if (!empty($data['id_enseignant'])) {
                $updateData['id_enseignant'] = $data['id_enseignant'];
            }
            if ($this->Model->update('matieres_classes', ['id_matiere_classe' => $softDeleted['id_matiere_classe']], $updateData)) {
                $eff = $this->Model->readOne('matieres_classes', ['id_matiere_classe' => $softDeleted['id_matiere_classe']]);
                if ($eff && !empty($eff['id_enseignant'])) {
                    $this->_sync_enseignement($eff['id_matiere_classe'], $eff['id_enseignant'], $eff['id_matiere'], $eff['id_classe']);
                }
                $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'CREATION', 'Réactivation et mise à jour du programme matière-classe', 'matieres_classes', $softDeleted['id_matiere_classe'], null, $updateData);
                $this->json_success(['id_matiere_classe' => $softDeleted['id_matiere_classe']], 'Programme réactivé');
            } else {
                $this->json_error('Erreur lors de la réactivation');
            }
            return;
        }

        $insertData = [
            'id_matiere' => $data['id_matiere'],
            'id_classe' => $data['id_classe'],
            'coefficient' => $coefficient,
            'nb_heures_par_jour' => $nb_heures_jour,
            'nb_heures_par_semaine' => $nb_heures_sem
        ];
        if (!empty($data['id_enseignant'])) {
            $insertData['id_enseignant'] = $data['id_enseignant'];
        }

        $id = $this->Model->createLastId('matieres_classes', $insertData);
        if ($id) {
            if (!empty($data['id_enseignant'])) {
                $this->_sync_enseignement($id, $data['id_enseignant'], $data['id_matiere'], $data['id_classe']);
            }
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'CREATION', 'Création du programme matière-classe', 'matieres_classes', $id, null, $insertData);
            $this->json_success(['id_matiere_classe' => $id], 'Programme créé');
        } else {
            $this->json_error('Erreur lors de la création');
        }
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $record = $this->Model->readOne('matieres_classes', ['uuid' => $id]);
        if (!$record) { $this->json_error('Programme non trouvé', 404); return; }

        if (!empty($data['id_matiere'])) {
            $matiere = $this->Model->readOne('matieres', ['id_matiere' => $data['id_matiere'], 'deleted_at' => null]);
            if (!$matiere) { $this->json_error('Matière introuvable'); return; }
        }
        if (!empty($data['id_classe'])) {
            $classe = $this->Model->readOne('classes', ['id_classe' => $data['id_classe'], 'deleted_at' => null]);
            if (!$classe) { $this->json_error('Classe introuvable'); return; }
        }
        if (isset($data['id_enseignant']) && !empty($data['id_enseignant'])) {
            $ens = $this->Model->readOne('enseignants', ['id_enseignant' => $data['id_enseignant'], 'deleted_at' => null]);
            if (!$ens) { $this->json_error('Enseignant introuvable'); return; }
        }

        $target_matiere = isset($data['id_matiere']) ? $data['id_matiere'] : $record['id_matiere'];
        $target_classe = isset($data['id_classe']) ? $data['id_classe'] : $record['id_classe'];
        $dup = $this->Model->readOne('matieres_classes', [
            'id_matiere' => $target_matiere,
            'id_classe' => $target_classe
        ]);
        if ($dup && $dup['id_matiere_classe'] != $record['id_matiere_classe']) {
            $this->json_error('Cette matière est déjà associée à cette classe'); return;
        }

        $coefficient = isset($data['coefficient']) ? floatval($data['coefficient']) : $record['coefficient'];
        $nb_heures_jour = isset($data['nb_heures_par_jour']) ? floatval($data['nb_heures_par_jour']) : $record['nb_heures_par_jour'];
        $nb_heures_sem = isset($data['nb_heures_par_semaine']) ? floatval($data['nb_heures_par_semaine']) : $record['nb_heures_par_semaine'];

        if ($coefficient < 0 || $nb_heures_jour < 0 || $nb_heures_sem < 0) {
            $this->json_error('Les coefficients et les volumes horaires ne peuvent pas être négatifs'); return;
        }

        $allowed = ['id_matiere', 'id_classe', 'id_enseignant', 'coefficient', 'nb_heures_par_jour', 'nb_heures_par_semaine'];
        $update = array_intersect_key($data, array_flip($allowed));
        $update['coefficient'] = $coefficient;
        $update['nb_heures_par_jour'] = $nb_heures_jour;
        $update['nb_heures_par_semaine'] = $nb_heures_sem;
        if (array_key_exists('id_enseignant', $data)) {
            $update['id_enseignant'] = !empty($data['id_enseignant']) ? $data['id_enseignant'] : null;
        }

        $this->db->trans_begin();
        $this->db->db_debug = false;
        $ok = $this->Model->update('matieres_classes', ['uuid' => $id], $update);
        if ($ok) {
            $updated = $this->Model->readOne('matieres_classes', ['id_matiere_classe' => $record['id_matiere_classe']]);
            if ($updated) {
                $changed = $updated['id_enseignant'] != $record['id_enseignant']
                    || $updated['id_matiere'] != $record['id_matiere']
                    || $updated['id_classe'] != $record['id_classe'];
                if ($changed) {
                    if (empty($updated['id_enseignant'])) {
                        $this->db->where('id_matiere_classe', $record['id_matiere_classe'])
                            ->where('deleted_at', null)
                            ->update('enseignements', ['deleted_at' => date('Y-m-d H:i:s')]);
                    } else {
                        $this->_sync_enseignement($record['id_matiere_classe'], $updated['id_enseignant'], $updated['id_matiere'], $updated['id_classe']);
                    }
                }
            }
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'MODIFICATION', 'Mise à jour du programme matière-classe', 'matieres_classes', $record['id_matiere_classe'], $record, $update);
        }
        $this->db->db_debug = true;
        if (!$ok) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de la mise à jour'); return;
        }
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de la mise à jour'); return;
        }
        $this->db->trans_commit();
        $this->json_success(null, 'Programme mis à jour');
    }

    public function api_delete($id) {
        $record = $this->Model->readOne('matieres_classes', ['uuid' => $id]);
        if (!$record) { $this->json_error('Programme non trouvé', 404); return; }

        $now = date('Y-m-d H:i:s');
        $this->db->trans_begin();
        $this->Model->update('matieres_classes', ['uuid' => $id], ['deleted_at' => $now]);
        $this->db->where('id_matiere_classe', $record['id_matiere_classe'])
            ->where('deleted_at', null)
            ->update('enseignements', ['deleted_at' => $now]);

        $ens_rows = $this->db->select('id_enseignement')
            ->where('id_matiere_classe', $record['id_matiere_classe'])
            ->get('enseignements')
            ->result_array();
        $ens_ids = array_column($ens_rows, 'id_enseignement');
        if (!empty($ens_ids)) {
            $this->db->where_in('id_enseignement', $ens_ids)
                ->where('deleted_at', null)
                ->update('horaires', ['deleted_at' => $now]);
        }

        $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'SUPPRESSION', 'Suppression logique du programme matière-classe', 'matieres_classes', $record['id_matiere_classe']);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $this->json_error('Erreur lors de la suppression'); return;
        }
        $this->db->trans_commit();
        $this->json_success(null, 'Programme supprimé');
    }

    private function _sync_enseignement($id_mc, $id_enseignant, $id_matiere, $id_classe) {
        $this->db->db_debug = false;
        $row = $this->db->where('id_matiere_classe', $id_mc)
            ->get('enseignements')
            ->row_array();
        if (!$row) {
            $row = $this->db->where('id_enseignant', $id_enseignant)
                ->where('id_matiere', $id_matiere)
                ->where('id_classe', $id_classe)
                ->get('enseignements')
                ->row_array();
        }
        if ($row) {
            $dup = $this->db->where('id_enseignant', $id_enseignant)
                ->where('id_matiere', $id_matiere)
                ->where('id_classe', $id_classe)
                ->where('id_enseignement !=', $row['id_enseignement'])
                ->get('enseignements')
                ->row_array();
            if ($dup) {
                $this->db->where('id_enseignement', $dup['id_enseignement'])
                    ->delete('enseignements');
            }
            $this->db->where('id_enseignement', $row['id_enseignement'])
                ->update('enseignements', [
                    'id_enseignant' => $id_enseignant,
                    'id_matiere' => $id_matiere,
                    'id_classe' => $id_classe,
                    'id_matiere_classe' => $id_mc,
                    'deleted_at' => null
                ]);
        } else {
            $this->load->helper('uuid');
            $this->db->insert('enseignements', [
                'uuid' => generate_uuid(),
                'id_enseignant' => $id_enseignant,
                'id_matiere' => $id_matiere,
                'id_classe' => $id_classe,
                'id_matiere_classe' => $id_mc
            ]);
        }
        $this->db->db_debug = true;
    }
}
