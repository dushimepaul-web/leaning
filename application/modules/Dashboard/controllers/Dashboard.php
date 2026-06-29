<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->not_logged_in();
    }

    public function index() {
        $data['title'] = 'Tableau de bord';
        $data['total_etudiants'] = $this->Model->countWhere('etudiants', ['deleted_at' => null]);
        $data['total_enseignants'] = $this->Model->countWhere('enseignants', ['deleted_at' => null]);
        $data['total_classes'] = $this->Model->countWhere('classes', ['deleted_at' => null]);
        $data['total_paiements'] = $this->Model->countWhere('paiements', ['deleted_at' => null]);
        $data['total_produits'] = $this->Model->countWhere('produits', ['deleted_at' => null]);
        $data['total_mouvements'] = $this->Model->count('mouvements_stock');

        $periode = $this->Model->readOne('periodes', ['est_en_cours' => 1]);
        $data['periode_active_name'] = $periode ? $periode['libelle'] : 'Non défini';
        $annee = $this->Model->readOne('annees_scolaires', ['id_annee' => $this->id_annee_active]);
        $data['annee_active_name'] = $annee ? $annee['libelle'] : 'Non définie';

        $view = $this->_resolve_dashboard_view();
        $this->render_view($view, $data);
    }

    private function _resolve_dashboard_view()
    {
        $role_code = $this->session->userdata('role_code') ?? '';
        $id_role = $this->session->userdata('id_role');
        $admin_role_ids = [1, 2, 3, 4, 5];

        if (in_array($id_role, $admin_role_ids)) {
            return 'Dashboard_School';
        }

        if ($role_code === 'enseignant') {
            return 'Dashboard_Teacher';
        }

        if (in_array($role_code, ['eleve', 'parent'])) {
            return 'Dashboard_Student';
        }

        $codes = $this->permission_codes;
        if (in_array('notes', $codes) && !in_array('paiements', $codes)) {
            return 'Dashboard_Teacher';
        }
        if (in_array('bulletins', $codes)) {
            return 'Dashboard_Student';
        }

        return 'Dashboard_School';
    }
}
