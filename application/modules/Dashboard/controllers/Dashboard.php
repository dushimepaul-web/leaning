<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->not_logged_in();
    }

    public function index() {
        $data['title'] = 'Tableau de bord';
        $id_role = (int)($this->session->userdata('id_role') ?? 0);
        $role_code = $this->session->userdata('role_code') ?? '';
        
        // Année scolaire active
        $stmtAnnee = $this->db->query("SELECT * FROM annees_scolaires WHERE deleted_at IS NULL ORDER BY est_en_cours DESC, id_annee DESC");
        $annees = $stmtAnnee->result_array();
        $id_annee_courante = $annees[0]['id_annee'] ?? 1;
        foreach ($annees as $a) {
            if ($a['est_en_cours'] == 1) { $id_annee_courante = $a['id_annee']; break; }
        }
        $id_annee_sel = isset($_GET['id_annee']) ? (int)$_GET['id_annee'] : $id_annee_courante;

        $isAdminOrDirection = ($id_role === 1 || $id_role === 2 || $role_code === 'admin' || $role_code === 'direction');
        
        $data['isAdminOrDirection'] = $isAdminOrDirection;
        $data['annees'] = $annees;
        $data['id_annee_sel'] = $id_annee_sel;
        $data['role_code'] = $role_code;

        // Échelle des notes (20 ou 100) - paramétrable dans Paramètres -> Mentions
        $echelle = (int)$this->Model->get_setting('echelle_notes', 100);
        if ($echelle !== 20 && $echelle !== 100) $echelle = 100;
        $data['echelle_notes'] = $echelle;

        if ($isAdminOrDirection) {
            $data['total_etudiants'] = $this->db->where(['actif' => 1, 'deleted_at' => null])->count_all_results('etudiants');
            $data['total_enseignants'] = $this->db->where(['actif' => 1, 'deleted_at' => null])->count_all_results('enseignants');
            
            $encaisseRow = $this->db->select('SUM(montant) as total')
                ->from('paiements')
                ->where('id_annee', $id_annee_sel)
                ->where('deleted_at IS NULL')
                ->get()->row();
            $encaisse = ($encaisseRow && $encaisseRow->total) ? floatval($encaisseRow->total) : 0;
            $data['total_encaisse'] = $encaisse;

            $fraisRow = $this->db->select('SUM(montant) as total')->where(['id_annee' => $id_annee_sel, 'deleted_at' => null])->get('frais')->row();
            $frais = ($fraisRow && $fraisRow->total) ? floatval($fraisRow->total) : 1;
            $data['taux_recouvrement'] = min(100, round(($encaisse / $frais) * 100, 1));

            $data['paiements_mois'] = $this->db->select("DATE_FORMAT(date_paiement, '%Y-%m') as mois, SUM(montant) as total")
                ->from('paiements')
                ->where('id_annee', $id_annee_sel)
                ->where('deleted_at IS NULL')
                ->group_by("DATE_FORMAT(date_paiement, '%Y-%m')")
                ->order_by('mois', 'ASC')->limit(12)->get()->result_array();

            $data['eleves_classe'] = $this->db->select("c.libelle as classe, COUNT(i.id_etudiant) as total")
                ->from('classes c')
                ->join('inscriptions i', 'i.id_classe = c.id_classe AND i.id_annee = ' . $id_annee_sel . ' AND i.deleted_at IS NULL', 'left')
                ->where('c.deleted_at IS NULL')
                ->group_by('c.id_classe')->get()->result_array();

            $data['stocks'] = $this->db->select('libelle, stock_actuel, stock_mini')->where('deleted_at', null)->limit(10)->get('produits')->result_array();

            // Performance des classes (moyennes par classe)
            $data['moyennes_classes'] = $this->db->select('c.libelle as classe, ROUND(AVG(b.moyenne), 2) as moyenne, COUNT(DISTINCT b.id_etudiant) as eleves')
                ->from('bulletins b')
                ->join('classes c', 'c.id_classe = b.id_classe', 'left')
                ->where('b.id_annee', $id_annee_sel)
                ->where('b.deleted_at IS NULL')
                ->where('b.moyenne IS NOT NULL')
                ->group_by('b.id_classe')
                ->order_by('moyenne', 'DESC')
                ->get()->result_array();

            // Évolution des classes par période (quelles classes progressent/reculent)
            $data['evolution_classes'] = $this->db->select('c.libelle as classe, p.libelle as periode, p.id_periode, ROUND(AVG(b.moyenne), 2) as moyenne')
                ->from('bulletins b')
                ->join('classes c', 'c.id_classe = b.id_classe', 'left')
                ->join('periodes p', 'p.id_periode = b.id_periode', 'left')
                ->where('b.id_annee', $id_annee_sel)
                ->where('b.deleted_at IS NULL')
                ->where('b.moyenne IS NOT NULL')
                ->group_by('b.id_classe, b.id_periode')
                ->order_by('p.id_periode', 'ASC')
                ->get()->result_array();

            // Élèves brillants (meilleure moyenne de l'année)
            $data['top_eleves'] = $this->db->select('e.fullname, e.matricule, c.libelle as classe, MAX(b.moyenne) as moyenne')
                ->from('bulletins b')
                ->join('etudiants e', 'e.id_etudiant = b.id_etudiant', 'left')
                ->join('classes c', 'c.id_classe = b.id_classe', 'left')
                ->where('b.id_annee', $id_annee_sel)
                ->where('b.deleted_at IS NULL')
                ->where('b.moyenne IS NOT NULL')
                ->group_by('b.id_etudiant')
                ->order_by('moyenne', 'DESC')
                ->limit(5)
                ->get()->result_array();

            // Répartition des décisions (admis / ajourné / échoué)
            $data['repartition_decision'] = $this->db->select('decision, COUNT(*) as total')
                ->from('bulletins')
                ->where('id_annee', $id_annee_sel)
                ->where('deleted_at IS NULL')
                ->where('decision IS NOT NULL')
                ->group_by('decision')
                ->get()->result_array();
        }

        $this->render_view('Dashboard_Analytics', $data);
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
