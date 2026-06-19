<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paie_bulletin_detail extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Détails des bulletins';
        $data['bulletins'] = $this->Model->read('paie_bulletins', ['deleted_at' => null]);
        $data['rubriques'] = $this->Model->read('paie_rubriques', ['deleted_at' => null, 'actif' => 1]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->select('d.*, b.mois, b.annee, r.libelle as rubrique_libelle, r.code as rubrique_code, r.type as rubrique_type');
        $this->db->from('paie_bulletins_details d');
        $this->db->join('paie_bulletins b', 'd.id_bulletin_paie = b.id_bulletin_paie', 'left');
        $this->db->join('paie_rubriques r', 'd.id_rubrique = r.id_rubrique', 'left');
        $this->db->order_by('d.id_detail', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_get($id) {
        $this->db->where('d.uuid', $id);
        $this->db->select('d.*, b.mois, b.annee, r.libelle as rubrique_libelle, r.code as rubrique_code');
        $this->db->from('paie_bulletins_details d');
        $this->db->join('paie_bulletins b', 'd.id_bulletin_paie = b.id_bulletin_paie', 'left');
        $this->db->join('paie_rubriques r', 'd.id_rubrique = r.id_rubrique', 'left');
        $d = $this->db->get()->row_array();
        if (!$d) { $this->json_error('Détail non trouvé', 404); return; }
        $this->json_success($d);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_bulletin_paie']) || empty($data['id_rubrique']) || !isset($data['montant'])) {
            $this->json_error('Bulletin, rubrique et montant obligatoires'); return;
        }
        $id = $this->Model->createLastId('paie_bulletins_details', $data);
        if ($id) $this->json_success(['id_detail' => $id], 'Détail ajouté');
        else $this->json_error('Erreur de création');
    }

    public function api_delete($id) {
        $d = $this->Model->readOne('paie_bulletins_details', ['uuid' => $id]);
        if (!$d) { $this->json_error('Détail non trouvé', 404); return; }
        if ($this->Model->delete('paie_bulletins_details', ['uuid' => $id]))
            $this->json_success(null, 'Détail supprimé');
        else $this->json_error('Erreur de suppression');
    }
}
