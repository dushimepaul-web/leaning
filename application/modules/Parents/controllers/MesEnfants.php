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

        $enfants = [];
        if ($id_utilisateur) {
            $etudiants = $this->db
                ->where('id_utilisateur', $id_utilisateur)
                ->where('deleted_at', null)
                ->get('etudiants')
                ->result_array();

            $ids_etudiant = array_column($etudiants, 'id_etudiant');

            $insc_map = [];
            $classe_ids = [];
            if (!empty($ids_etudiant)) {
                $inscriptions = $this->db
                    ->where_in('id_etudiant', $ids_etudiant)
                    ->where('deleted_at', null)
                    ->get('inscriptions')
                    ->result_array();
                foreach ($inscriptions as $ins) {
                    $insc_map[$ins['id_etudiant']] = $ins;
                    if (!empty($ins['id_classe'])) {
                        $classe_ids[] = $ins['id_classe'];
                    }
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