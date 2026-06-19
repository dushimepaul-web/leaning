<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MesEnfants extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->not_logged_in();
    }

    public function index() {
        $uuid = $this->session->userdata('uuid');
        $user = $this->Model->readOne('utilisateurs', ['uuid' => $uuid]);
        $id_utilisateur = $user ? $user['id_utilisateur'] : 0;
        $parents = $this->Model->read('parents', ['id_utilisateur' => $id_utilisateur, 'deleted_at' => null]);

        $enfants = [];
        $ids_etudiant = array_unique(array_column($parents, 'id_etudiant'));
        $parent_type_map = [];
        foreach ($parents as $p) {
            $parent_type_map[$p['id_etudiant']] = $p['type'];
        }

        if (!empty($ids_etudiant)) {
            $etudiants = $this->db
                ->where_in('id_etudiant', $ids_etudiant)
                ->where('deleted_at', null)
                ->get('etudiants')
                ->result_array();

            $inscriptions = $this->db
                ->where_in('id_etudiant', $ids_etudiant)
                ->where('deleted_at', null)
                ->get('inscriptions')
                ->result_array();
            $insc_map = [];
            $classe_ids = [];
            foreach ($inscriptions as $ins) {
                $insc_map[$ins['id_etudiant']] = $ins;
                if (!empty($ins['id_classe'])) {
                    $classe_ids[] = $ins['id_classe'];
                }
            }

            $classes_map = [];
            if (!empty($classe_ids)) {
                $classes = $this->db
                    ->where_in('id_classe', array_unique($classe_ids))
                    ->get('classes')
                    ->result_array();
                $classes_map = array_column($classes, 'libelle', 'id_classe');
            }

            foreach ($etudiants as $e) {
                $e['parent_type'] = $parent_type_map[$e['id_etudiant']] ?? '';
                $insc = $insc_map[$e['id_etudiant']] ?? null;
                $e['classe_libelle'] = $insc ? ($classes_map[$insc['id_classe']] ?? '') : '';
                $enfants[] = $e;
            }
        }

        $data['title'] = 'Mes enfants';
        $data['enfants'] = $enfants;
        $this->render_view('index', $data);
    }
}
