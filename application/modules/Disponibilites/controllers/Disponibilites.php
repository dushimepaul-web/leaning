<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Disponibilites extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Disponibilités enseignants';
        $data['enseignants'] = $this->Model->read('enseignants', ['deleted_at' => null]);
        $this->load->model('Horaires/Horaires_model');
        $data['creneaux'] = $this->Horaires_model->get_creneaux_cours();
        $data['jours'] = $this->Model->read('jours_semaine', [], 'ordre', 'ASC');
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('d.deleted_at', null);
        $this->db->select('d.*, e.fullname as enseignant, j.libelle as jour');
        $this->db->from('disponibilites_enseignants d');
        $this->db->join('enseignants e', 'd.id_enseignant = e.id_enseignant', 'left');
        $this->db->join('jours_semaine j', 'd.id_jour = j.id_jour', 'left');
        $this->db->order_by('d.id_disponibilite', 'DESC');
        $q = $this->db->get();
        $rows = $q !== false ? $q->result_array() : array();

        $this->load->model('Horaires/Horaires_model');
        $creneaux = $this->Horaires_model->get_creneaux_cours();
        $creneauxMap = [];
        foreach ($creneaux as $cr) {
            $creneauxMap[$cr['id_creneau']] = $cr;
        }

        foreach ($rows as &$r) {
            $cid = $r['id_creneau'];
            if (isset($creneauxMap[$cid])) {
                $r['creneau'] = $creneauxMap[$cid]['libelle'] . ' (' . $creneauxMap[$cid]['heure_debut'] . ' - ' . $creneauxMap[$cid]['heure_fin'] . ')';
            } else {
                $r['creneau'] = 'Cours ' . $cid;
            }
        }

        $this->json_success($rows);
    }

    public function api_get($id) {
        $d = $this->Model->readOne('disponibilites_enseignants', ['uuid' => $id]);
        if (!$d) { $this->json_error('Disponibilité non trouvée', 404); return; }
        $this->json_success($d);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_enseignant']) || empty($data['id_creneau']) || empty($data['id_jour']) ||
            !is_numeric($data['id_enseignant']) || !is_numeric($data['id_creneau']) || !is_numeric($data['id_jour'])) {
            $this->json_error('Enseignant, créneau et jour obligatoires'); return;
        }
        if (!$this->Model->readOne('enseignants', ['id_enseignant' => $data['id_enseignant'], 'deleted_at' => null])) {
            $this->json_error('Enseignant introuvable'); return;
        }
        if (!$this->Model->readOne('jours_semaine', ['id_jour' => $data['id_jour']])) {
            $this->json_error('Jour invalide'); return;
        }
        $this->load->model('Horaires/Horaires_model');
        $creneaux = $this->Horaires_model->get_creneaux_cours();
        if (!in_array((int)$data['id_creneau'], array_column($creneaux, 'id_creneau'))) {
            $this->json_error('Créneau invalide'); return;
        }
        $data['type'] = !empty($data['type']) ? $data['type'] : 'disponible';
        $existing = $this->Model->readOne('disponibilites_enseignants', [
            'id_enseignant' => $data['id_enseignant'],
            'id_creneau' => $data['id_creneau'],
            'id_jour' => $data['id_jour'],
            'deleted_at' => null
        ]);
        if ($existing) {
            $this->json_error('Cette disponibilité existe déjà pour cet enseignant');
            return;
        }
        $allowed = ['id_enseignant', 'id_creneau', 'id_jour', 'type'];
        $insert = array_intersect_key($data, array_flip($allowed));
        $id = $this->Model->createLastId('disponibilites_enseignants', $insert);
        if ($id) $this->json_success(['id_disponibilite' => $id], 'Disponibilité créée');
        else $this->json_error('Erreur');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (!$this->Model->readOne('disponibilites_enseignants', ['uuid' => $id])) {
            $this->json_error('Disponibilité non trouvée', 404); return;
        }
        $allowed = ['id_enseignant', 'id_creneau', 'id_jour', 'type'];
        $update = array_intersect_key($data, array_flip($allowed));
        if (empty($update)) { $this->json_error('Aucune donnée à modifier'); return; }
        if ($this->Model->update('disponibilites_enseignants', ['uuid' => $id], $update))
            $this->json_success(null, 'Disponibilité mise à jour');
        else $this->json_error('Erreur');
    }

    public function api_delete($id) {
        if ($this->Model->update('disponibilites_enseignants', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Disponibilité supprimée');
        else $this->json_error('Erreur');
    }

    public function api_bulk_create() {
        $data = $this->get_json_input();
        if (empty($data['id_enseignant']) || empty($data['id_jour']) ||
            !is_numeric($data['id_enseignant']) || !is_numeric($data['id_jour'])) {
            $this->json_error('Enseignant et jour obligatoires'); return;
        }
        if (!$this->Model->readOne('enseignants', ['id_enseignant' => $data['id_enseignant'], 'deleted_at' => null])) {
            $this->json_error('Enseignant introuvable'); return;
        }
        if (!$this->Model->readOne('jours_semaine', ['id_jour' => $data['id_jour']])) {
            $this->json_error('Jour invalide'); return;
        }

        $type = !empty($data['type']) ? $data['type'] : 'disponible';
        if (!in_array($type, ['disponible', 'indisponible'])) {
            $this->json_error('Type invalide'); return;
        }

        $this->load->model('Horaires/Horaires_model');
        $creneaux = $this->Horaires_model->get_creneaux_cours();
        $creneauIds = [];
        foreach ($creneaux as $cr) {
            $id = (int)$cr['id_creneau'];
            if ($id > 0) $creneauIds[] = $id;
        }

        $idEns = (int)$data['id_enseignant'];
        $idJour = (int)$data['id_jour'];

        $existingQuery = $this->db->query(
            "SELECT id_creneau, type FROM disponibilites_enseignants WHERE id_enseignant = ? AND id_jour = ? AND deleted_at IS NULL",
            [$idEns, $idJour]
        );
        $existingMap = [];
        if ($existingQuery && $existingQuery->num_rows() > 0) {
            foreach ($existingQuery->result_array() as $row) {
                $existingMap[(int)$row['id_creneau']] = $row['type'];
            }
        }

        $toInsert = [];
        $toUpdate = [];
        $skipped = 0;
        $created = 0;

        foreach ($creneauIds as $crId) {
            if (isset($existingMap[$crId])) {
                if ($existingMap[$crId] !== $type) {
                    $toUpdate[] = $crId;
                } else {
                    $skipped++;
                }
                continue;
            }
            $toInsert[] = [
                'uuid' => generate_uuid(),
                'id_enseignant' => $idEns,
                'id_creneau' => $crId,
                'id_jour' => $idJour,
                'type' => $type
            ];
        }

        if (!empty($toInsert)) {
            $this->db->insert_batch('disponibilites_enseignants', $toInsert);
            $created += count($toInsert);
        }

        foreach ($toUpdate as $crId) {
            $this->db->where('id_enseignant', $idEns)->where('id_creneau', $crId)->where('id_jour', $idJour)->where('deleted_at', null);
            $this->db->update('disponibilites_enseignants', ['type' => $type]);
            $created++;
        }

        $this->json_success([
            'created' => $created,
            'skipped' => $skipped,
            'total' => count($creneauIds)
        ], "$created disponibilité(s) créée(s), $skipped déjà existante(s)");
    }

    public function api_bulk_range_create() {
        $data = $this->get_json_input();
        if (empty($data['id_enseignant']) || empty($data['id_jour']) || empty($data['id_creneau_debut']) ||
            !is_numeric($data['id_enseignant']) || !is_numeric($data['id_jour']) || !is_numeric($data['id_creneau_debut'])) {
            $this->json_error('Enseignant, jour et créneau début obligatoires'); return;
        }
        if (!$this->Model->readOne('enseignants', ['id_enseignant' => $data['id_enseignant'], 'deleted_at' => null])) {
            $this->json_error('Enseignant introuvable'); return;
        }
        if (!$this->Model->readOne('jours_semaine', ['id_jour' => $data['id_jour']])) {
            $this->json_error('Jour invalide'); return;
        }

        $type = !empty($data['type']) ? $data['type'] : 'disponible';
        if (!in_array($type, ['disponible', 'indisponible'])) {
            $this->json_error('Type invalide'); return;
        }

        $debut = (int)$data['id_creneau_debut'];
        $fin = !empty($data['id_creneau_fin']) ? (int)$data['id_creneau_fin'] : $debut;
        if ($fin < $debut) $fin = $debut;

        $idEns = (int)$data['id_enseignant'];
        $idJour = (int)$data['id_jour'];

        $existingQuery = $this->db->query(
            "SELECT id_creneau, type FROM disponibilites_enseignants WHERE id_enseignant = ? AND id_jour = ? AND deleted_at IS NULL",
            [$idEns, $idJour]
        );
        $existingMap = [];
        if ($existingQuery && $existingQuery->num_rows() > 0) {
            foreach ($existingQuery->result_array() as $row) {
                $existingMap[(int)$row['id_creneau']] = $row['type'];
            }
        }

        $toInsert = [];
        $toUpdate = [];
        $skipped = 0;
        $created = 0;

        for ($crId = $debut; $crId <= $fin; $crId++) {
            if (isset($existingMap[$crId])) {
                if ($existingMap[$crId] !== $type) {
                    $toUpdate[] = $crId;
                } else {
                    $skipped++;
                }
                continue;
            }
            $toInsert[] = [
                'uuid' => generate_uuid(),
                'id_enseignant' => $idEns,
                'id_creneau' => $crId,
                'id_jour' => $idJour,
                'type' => $type
            ];
        }

        if (!empty($toInsert)) {
            $this->db->insert_batch('disponibilites_enseignants', $toInsert);
            $created += count($toInsert);
        }

        foreach ($toUpdate as $crId) {
            $this->db->where('id_enseignant', $idEns)->where('id_creneau', $crId)->where('id_jour', $idJour)->where('deleted_at', null);
            $this->db->update('disponibilites_enseignants', ['type' => $type]);
            $created++;
        }

        $this->json_success([
            'created' => $created,
            'skipped' => $skipped,
            'total' => ($fin - $debut + 1)
        ], "$created disponibilité(s) créée(s), $skipped déjà existante(s)");
    }
}
