<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recu extends MY_Controller {
    public function __construct() { parent::__construct(); }

    public function index() {
        $data['title'] = 'Gestion des reçus';
        $data['etudiants'] = $this->Model->read('etudiants', ['deleted_at' => null]);
        $this->load->view('index', $data);
    }

    public function api_list() {
        $this->db->where('r.deleted_at', null);
        $this->db->select('r.*, e.nom, e.prenom, e.matricule');
        $this->db->from('recus r');
        $this->db->join('etudiants e', 'r.id_etudiant = e.id_etudiant', 'left');
        $this->db->order_by('r.id_recu', 'DESC');
        $this->json_success($this->db->get()->result_array());
    }

    public function api_get($id) {
        $this->db->where('r.uuid', $id);
        $this->db->select('r.*, e.nom, e.prenom, e.matricule');
        $this->db->from('recus r');
        $this->db->join('etudiants e', 'r.id_etudiant = e.id_etudiant', 'left');
        $d = $this->db->get()->row_array();
        if (!$d) { $this->json_error('Reçu non trouvé', 404); return; }
        $this->json_success($d);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['numero_recu']) || empty($data['id_etudiant'])) {
            $this->json_error('Numéro de reçu et étudiant obligatoires'); return;
        }
        $data['id_annee'] = $this->id_annee_active;
        $id = $this->Model->createLastId('recus', $data);
        if ($id) $this->json_success(['id_recu' => $id], 'Reçu créé');
        else $this->json_error('Erreur de création');
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if ($this->Model->update('recus', ['uuid' => $id], $data))
            $this->json_success(null, 'Reçu mis à jour');
        else $this->json_error('Erreur de mise à jour');
    }

    public function api_delete($id) {
        if ($this->Model->update('recus', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')]))
            $this->json_success(null, 'Reçu supprimé');
        else $this->json_error('Erreur de suppression');
    }

    public function imprimer($uuid) {
        $recu = $this->Model->readOne('recus', ['uuid' => $uuid, 'deleted_at' => null]);
        if (!$recu) { show_404(); return; }

        $etudiant = $this->Model->readOne('etudiants', ['id_etudiant' => $recu['id_etudiant']]);
        $recu['etudiant_nom'] = $etudiant ? trim($etudiant['nom'] . ' ' . ($etudiant['postnom'] ?? '') . ' ' . ($etudiant['prenom'] ?? '')) : '-';
        $recu['matricule'] = $etudiant['matricule'] ?? '-';

        $inscription = $this->Model->readOne('inscriptions', ['id_etudiant' => $recu['id_etudiant'], 'id_annee' => $recu['id_annee'], 'deleted_at' => null]);
        if ($inscription) {
            $classe = $this->Model->readOne('classes', ['id_classe' => $inscription['id_classe']]);
            $recu['classe_libelle'] = $classe ? $classe['libelle'] : '-';
        } else {
            $recu['classe_libelle'] = '-';
        }
        $annee = $this->Model->readOne('annees_scolaires', ['id_annee' => $recu['id_annee']]);
        $recu['annee_libelle'] = $annee ? $annee['libelle'] : '-';

        if (!empty($recu['id_utilisateur'])) {
            $user = $this->Model->readOne('utilisateurs', ['id_utilisateur' => $recu['id_utilisateur']]);
            $recu['utilisateur_nom'] = $user ? $user['nom_complet'] : '-';
        } else {
            $recu['utilisateur_nom'] = '-';
        }

        $paiements = $this->db
            ->select('p.*, tf.libelle as type_frais')
            ->from('paiements p')
            ->join('paiements_recus pr', 'p.id_paiement = pr.id_paiement')
            ->join('frais f', 'p.id_frais = f.id_frais', 'left')
            ->join('types_frais tf', 'f.id_type_frais = tf.id_type_frais', 'left')
            ->where('pr.id_recu', $recu['id_recu'])
            ->where('p.deleted_at', null)
            ->get()->result_array();

        if (empty($paiements)) {
            $paiements = [[
                'type_frais' => 'Frais de scolarité',
                'mode_paiement' => $recu['mode_paiement'] ?? 'especes',
                'reference' => $recu['reference'] ?? '-',
                'montant' => $recu['montant_total']
            ]];
        }

        $ecole = [
            'nom' => $this->Model->get_setting('nom_ecole', 'VIP SCHOOL'),
            'adresse' => $this->Model->get_setting('adresse_ecole', ''),
            'telephone' => $this->Model->get_setting('telephone_ecole', ''),
            'email' => $this->Model->get_setting('email_ecole', ''),
        ];

        $data['recu'] = $recu;
        $data['paiements'] = $paiements;
        $data['ecole'] = $ecole;
        $this->load->view('print', $data);
    }
}
