<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paie_bulletin extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Bulletins de paie';
        $data['contrats'] = $this->Model->read('paie_contrats', ['deleted_at' => null, 'actif' => 1]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('b.deleted_at', null);
        $this->db->select('b.*, e.nom_complet, e.matricule');
        $this->db->from('paie_bulletins b');
        $this->db->join('paie_contrats c', 'b.id_contrat = c.id_contrat', 'left');
        $this->db->join('employes e', 'c.id_employe = e.id_employe', 'left');
        $this->db->order_by('b.annee DESC, b.mois DESC');
        $result = $this->db->get()->result_array();
        foreach ($result as &$r) {
            $r['salaire_base'] = $r['salaire_base'] ?? $r['salaire_brut'] ?? 0;
            $r['net_a_payer'] = $r['net_a_payer'] ?? $r['salaire_net'] ?? 0;
            $r['total_gains'] = $r['total_gains'] ?? 0;
            $r['total_retenues'] = $r['total_retenues'] ?? $r['deductions'] ?? 0;
            $r['statut'] = $r['statut'] ?? 'brouillon';
        }
        $this->json_success($result);
    }

    public function api_get($id) {
        $this->db->where('b.uuid', $id);
        $this->db->select('b.*, e.nom_complet, e.matricule');
        $this->db->from('paie_bulletins b');
        $this->db->join('paie_contrats c', 'b.id_contrat = c.id_contrat', 'left');
        $this->db->join('employes e', 'c.id_employe = e.id_employe', 'left');
        $d = $this->db->get()->row_array();
        if (!$d) { $this->json_error('Bulletin non trouvé', 404); return; }
        $d['salaire_base'] = $d['salaire_base'] ?? $d['salaire_brut'] ?? 0;
        $d['net_a_payer'] = $d['net_a_payer'] ?? $d['salaire_net'] ?? 0;
        $d['total_gains'] = $d['total_gains'] ?? 0;
        $d['total_retenues'] = $d['total_retenues'] ?? $d['deductions'] ?? 0;
        if ($this->db->table_exists('paie_bulletins_details')) {
            $d['details'] = $this->db->where('id_bulletin_paie', $d['id_bulletin_paie'])->get('paie_bulletins_details')->result_array();
        }
        $this->json_success($d);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['id_contrat'])) { $this->json_error('Contrat obligatoire'); return; }
        $contrat = $this->Model->readOne('paie_contrats', ['id_contrat' => $data['id_contrat']]);
        if (!$contrat) { $this->json_error('Contrat non trouvé', 404); return; }
        $this->load->helper('uuid');
        $salaire = floatval($contrat['salaire_base'] ?? $contrat['salaire_brut'] ?? 0);
        $insert = [
            'uuid' => generate_uuid(),
            'id_contrat' => $data['id_contrat'],
            'mois' => !empty($data['mois']) ? $data['mois'] : date('m'),
            'annee' => !empty($data['annee']) ? $data['annee'] : date('Y'),
            'salaire_brut' => $salaire,
            'salaire_base' => $salaire,
            'salaire_net' => $salaire,
            'net_a_payer' => $salaire,
            'deductions' => 0,
            'total_gains' => 0,
            'total_retenues' => 0,
            'date_edition' => date('Y-m-d'),
            'cree_le' => date('Y-m-d H:i:s'),
            'modifie_le' => date('Y-m-d H:i:s'),
        ];
        $id = $this->Model->createLastId('paie_bulletins', $insert);
        if ($id) $this->json_success(['id_bulletin_paie' => $id], 'Bulletin créé');
        else $this->json_error('Erreur de création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $data['modifie_le'] = date('Y-m-d H:i:s');
        if ($this->Model->update('paie_bulletins', ['uuid' => $id], $data))
            $this->json_success(null, 'Bulletin mis à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('paie_bulletins', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s'), 'modifie_le' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Bulletin supprimé');
        else $this->json_error('Erreur de suppression');
    }
}
