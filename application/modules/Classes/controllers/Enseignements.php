<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enseignements extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des enseignements';
        $this->load->view('enseignements', $data);
    }

    public function api_list() {
        $this->load->model('Classes_model');
        $filters = [];
        if ($this->input->get('id_classe')) $filters['id_classe'] = (int)$this->input->get('id_classe');
        if ($this->input->get('id_enseignant')) $filters['id_enseignant'] = (int)$this->input->get('id_enseignant');
        $this->json_success($this->Classes_model->get_enseignements($filters));
    }

    public function api_get($id) {
        $e = $this->Model->readOne('enseignements', ['uuid' => $id]);
        if (!$e) { $this->json_error('Enseignement non trouvé', 404); return; }
        $this->json_success($e);
    }

    public function api_create() {
        $data = $this->get_json_input();
        foreach (['id_enseignant', 'id_matiere', 'id_classe'] as $f) {
            if (empty($data[$f]) || !is_numeric($data[$f])) {
                $this->json_error('Enseignant, matière et classe sont obligatoires'); return;
            }
        }
        $id_enseignant = (int)$data['id_enseignant'];
        $id_matiere = (int)$data['id_matiere'];
        $id_classe = (int)$data['id_classe'];

        if (!$this->Model->readOne('enseignants', ['id_enseignant' => $id_enseignant, 'deleted_at' => null])) {
            $this->json_error('Enseignant introuvable'); return;
        }
        if (!$this->Model->readOne('matieres', ['id_matiere' => $id_matiere, 'deleted_at' => null])) {
            $this->json_error('Matière introuvable'); return;
        }
        if (!$this->Model->readOne('classes', ['id_classe' => $id_classe, 'deleted_at' => null])) {
            $this->json_error('Classe introuvable'); return;
        }

        $dup = $this->Model->readOne('enseignements', [
            'id_enseignant' => $id_enseignant,
            'id_matiere' => $id_matiere,
            'id_classe' => $id_classe,
            'deleted_at' => null
        ]);
        if ($dup) { $this->json_error('Cet enseignement existe déjà'); return; }

        $insert = [
            'id_enseignant' => $id_enseignant,
            'id_matiere' => $id_matiere,
            'id_classe' => $id_classe,
        ];
        if (!empty($data['id_matiere_classe']) && is_numeric($data['id_matiere_classe'])) {
            if (!$this->Model->readOne('matieres_classes', ['id_matiere_classe' => $data['id_matiere_classe'], 'deleted_at' => null])) {
                $this->json_error('Association matière/classe introuvable'); return;
            }
            $insert['id_matiere_classe'] = (int)$data['id_matiere_classe'];
        } else {
            $mc = $this->Model->readOne('matieres_classes', [
                'id_matiere' => $id_matiere,
                'id_classe' => $id_classe,
                'deleted_at' => null
            ]);
            if ($mc) $insert['id_matiere_classe'] = $mc['id_matiere_classe'];
        }

        if (empty($insert['id_matiere_classe'])) {
            $this->json_error('Aucune association matière/classe trouvée pour cet enseignement'); return;
        }

        $id = $this->Model->createLastId('enseignements', $insert);
        if ($id) $this->json_success(null, 'Enseignement créé');
        else $this->json_error('Erreur lors de la création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (!$this->Model->readOne('enseignements', ['uuid' => $id])) {
            $this->json_error('Enseignement non trouvé', 404); return;
        }
        $update = [];
        foreach (['id_enseignant', 'id_matiere', 'id_classe', 'id_matiere_classe'] as $f) {
            if (isset($data[$f]) && is_numeric($data[$f])) $update[$f] = (int)$data[$f];
        }
        if (isset($update['id_enseignant'])) {
            if (!$this->Model->readOne('enseignants', ['id_enseignant' => $update['id_enseignant'], 'deleted_at' => null])) {
                $this->json_error('Enseignant introuvable'); return;
            }
        }
        if (isset($update['id_matiere'])) {
            if (!$this->Model->readOne('matieres', ['id_matiere' => $update['id_matiere'], 'deleted_at' => null])) {
                $this->json_error('Matière introuvable'); return;
            }
        }
        if (isset($update['id_classe'])) {
            if (!$this->Model->readOne('classes', ['id_classe' => $update['id_classe'], 'deleted_at' => null])) {
                $this->json_error('Classe introuvable'); return;
            }
        }
        if (isset($update['id_matiere_classe'])) {
            $mc = $this->Model->readOne('matieres_classes', ['id_matiere_classe' => $update['id_matiere_classe'], 'deleted_at' => null]);
            if (!$mc) { $this->json_error('Association matière/classe introuvable'); return; }
            $id_matiere = $update['id_matiere'] ?? null;
            $id_classe = $update['id_classe'] ?? null;
            if ($id_matiere && $id_matiere != $mc['id_matiere']) { $this->json_error('Association incompatible avec la matière'); return; }
            if ($id_classe && $id_classe != $mc['id_classe']) { $this->json_error('Association incompatible avec la classe'); return; }
        }
        if ($this->Model->update('enseignements', ['uuid' => $id], $update))
            $this->json_success(null, 'Enseignement mis à jour');
        else $this->json_error('Erreur lors de la mise à jour');
    }

    public function api_delete($id) {
        if (!$this->Model->readOne('enseignements', ['uuid' => $id])) {
            $this->json_error('Enseignement non trouvé', 404); return;
        }
        if ($this->Model->update('enseignements', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Enseignement supprimé');
        else $this->json_error('Erreur lors de la suppression');
    }
}