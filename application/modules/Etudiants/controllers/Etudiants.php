<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Etudiants extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->not_logged_in();
    }

    public function index() {
        $data['title'] = 'Liste des étudiants';
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['sections'] = $this->Model->read('sections', ['deleted_at' => null]);
        $this->load->view('list', $data);
    }

    public function add() {
        $data['title'] = 'Ajouter un étudiant';
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['sections'] = $this->Model->read('sections', ['deleted_at' => null]);
        $data['annees'] = $this->Model->read('annees_scolaires');
        $data['etudiant'] = null;
        $this->load->view('form', $data);
    }

    public function edit($id) {
        $e = $this->Model->readOne('etudiants', ['uuid' => $id, 'deleted_at' => null]);
        if (!$e) { show_404(); return; }
        $insc = $this->Model->readOne('inscriptions', ['id_etudiant' => $e['id_etudiant'], 'deleted_at' => null]);
        $e['inscription'] = $insc;
        $data['title'] = 'Modifier étudiant';
        $data['classes'] = $this->Model->read('classes', ['deleted_at' => null]);
        $data['sections'] = $this->Model->read('sections', ['deleted_at' => null]);
        $data['annees'] = $this->Model->read('annees_scolaires');
        $data['etudiant'] = $e;
        $this->load->view('form', $data);
    }

    public function details($id) {
        $e = $this->Model->readOne('etudiants', ['uuid' => $id, 'deleted_at' => null]);
        if (!$e) { show_404(); return; }
        $insc = $this->Model->readOne('inscriptions', ['id_etudiant' => $e['id_etudiant'], 'deleted_at' => null]);
        $e['inscription'] = $insc;
        if ($insc) {
            $classe = $this->Model->readOne('classes', ['id_classe' => $insc['id_classe']]);
            $e['classe_libelle'] = $classe ? $classe['libelle'] : '';
            $section = $this->Model->readOne('sections', ['id_section' => $insc['id_section']]);
            $e['section_libelle'] = $section ? $section['libelle'] : '';
            $annee = $this->Model->readOne('annees_scolaires', ['id_annee' => $insc['id_annee']]);
            $e['annee_libelle'] = $annee ? $annee['libelle'] : '';
        }
        $data['title'] = 'Détails étudiant';
        $data['etudiant'] = $e;
        $this->load->view('details', $data);
    }

    public function api_list() {
        $this->db->where('e.deleted_at', null);
        $this->db->select('e.*, i.id_classe, i.id_section, i.id_annee, i.statut as inscription_statut, c.libelle as classe_libelle, s.libelle as section_libelle');
        $this->db->from('etudiants e');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL AND i.id_annee = ' . (int)$this->id_annee_active, 'left');
        $this->db->join('classes c', 'i.id_classe = c.id_classe', 'left');
        $this->db->join('sections s', 'i.id_section = s.id_section', 'left');
        $this->db->order_by('e.id_etudiant', 'DESC');
        $q_e = $this->db->get();
        $etudiants = $q_e !== false ? $q_e->result_array() : array();

        foreach ($etudiants as &$et) {
            $et['nom_complet'] = trim(($et['nom'] ?? '') . ' ' . ($et['postnom'] ?? '') . ' ' . ($et['prenom'] ?? ''));
        }
        $this->json_success($etudiants);
    }

    public function api_get($id) {
        $e = $this->Model->readOne('etudiants', ['uuid' => $id]);
        if (!$e) { $this->json_error('Étudiant non trouvé', 404); return; }
        $insc = $this->Model->readOne('inscriptions', ['id_etudiant' => $e['id_etudiant'], 'deleted_at' => null]);
        $e['inscription'] = $insc;
        $e['nom_complet'] = trim(($e['nom'] ?? '') . ' ' . ($e['postnom'] ?? '') . ' ' . ($e['prenom'] ?? ''));
        $this->json_success($e);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (empty($data['nom']) || empty($data['prenom'])) {
            $this->json_error('Nom et prénom obligatoires');
            return;
        }
        $data['matricule'] = $data['matricule'] ?? $this->_generate_matricule();

        if (!empty($data['email'])) {
            $existing = $this->Model->readOne('utilisateurs', ['email' => $data['email']]);
            if ($existing) {
                $this->json_error('Cet email est déjà utilisé par un autre compte');
                return;
            }
        }

        $id_classe = $data['id_classe'] ?? null;
        $id_section = $data['id_section'] ?? null;
        $id_annee = $data['id_annee'] ?? $this->id_annee_active;
        $parent_nom = $data['parent_nom'] ?? null;
        $parent_telephone = $data['parent_telephone'] ?? null;
        $parent_profession = $data['parent_profession'] ?? null;
        $parent_adresse = $data['parent_adresse'] ?? null;

        $cols_etudiant = ['nom','postnom','prenom','date_naissance','sexe','telephone','email','adresse','adresse_permanente','photo','matricule','lieu_naissance','parent_nom','parent_telephone','parent_profession','parent_adresse'];
        $clean = [];
        foreach ($cols_etudiant as $col) {
            if (isset($data[$col]) && $data[$col] !== '') {
                $clean[$col] = $data[$col];
            }
        }

        $id = $this->Model->createLastId('etudiants', $clean);
        if ($id) {
            if (!empty($id_classe)) {
                $this->Model->create('inscriptions', [
                    'id_etudiant' => $id,
                    'id_classe' => $id_classe,
                    'id_section' => $id_section,
                    'id_annee' => $id_annee,
                    'date_inscription' => date('Y-m-d')
                ]);
            }
            $data['id_etudiant'] = $id;
            $account = $this->_create_linked_user('etudiants', $id, $data, 'eleve');
            $result = ['id_etudiant' => $id];
            if ($account) {
                $result['id_utilisateur'] = $account['id_utilisateur'];
                $result['default_password'] = $account['default_password'];
            }
            $this->_recalculer_numero_ordre();
            $this->json_success($result, 'Étudiant créé avec succès');
        } else {
            $this->json_error('Erreur lors de la création');
        }
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        $etudiant = $this->Model->readOne('etudiants', ['uuid' => $id]);
        if (!$etudiant) {
            $this->json_error('Étudiant non trouvé', 404);
            return;
        }

        if (!empty($data['email']) && $data['email'] !== $etudiant['email']) {
            $existing = $this->Model->readOne('utilisateurs', ['email' => $data['email']]);
            if ($existing && $existing['id_utilisateur'] != $etudiant['id_utilisateur']) {
                $this->json_error('Cet email est déjà utilisé par un autre compte');
                return;
            }
        }

        $updateData = $data;
        unset($updateData['id_classe'], $updateData['id_section'], $updateData['id_annee'], $updateData['parents'], $updateData['parent_nom_old']);
        if ($this->Model->update('etudiants', ['uuid' => $id], $updateData)) {
            if (isset($data['id_classe'])) {
                $insc = $this->Model->readOne('inscriptions', ['id_etudiant' => $etudiant['id_etudiant'], 'deleted_at' => null]);
                if ($insc) {
                    $this->Model->update('inscriptions', ['id_inscription' => $insc['id_inscription']], [
                        'id_classe' => $data['id_classe'],
                        'id_section' => $data['id_section'] ?? $insc['id_section'],
                        'id_annee' => $data['id_annee'] ?? $insc['id_annee']
                    ]);
                } else {
                    $this->Model->create('inscriptions', [
                        'id_etudiant' => $etudiant['id_etudiant'], 'id_classe' => $data['id_classe'],
                        'id_section' => $data['id_section'] ?? null,
                        'id_annee' => $data['id_annee'] ?? $this->id_annee_active,
                        'date_inscription' => date('Y-m-d')
                    ]);
                }
            }
            $this->_sync_linked_user('etudiants', $etudiant['id_etudiant'], $data);
            $this->_recalculer_numero_ordre();
            $this->json_success(null, 'Étudiant mis à jour');
        } else {
            $this->json_error('Erreur de mise à jour');
        }
    }

    public function api_delete($id) {
        $etudiant = $this->Model->readOne('etudiants', ['uuid' => $id]);
        if (!$etudiant) { $this->json_error('Étudiant non trouvé', 404); return; }
        if ($this->Model->update('etudiants', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')])) {
            $this->Model->update('inscriptions', ['id_etudiant' => $etudiant['id_etudiant']], ['deleted_at' => date('Y-m-d H:i:s')]);
            if (!empty($etudiant['id_utilisateur'])) {
                $this->Model->update('utilisateurs', ['id_utilisateur' => $etudiant['id_utilisateur']], ['deleted_at' => date('Y-m-d H:i:s'), 'actif' => 0]);
            }
            $this->_recalculer_numero_ordre();
            $this->json_success(null, 'Étudiant supprimé');
        } else {
            $this->json_error('Erreur de suppression');
        }
    }

    public function api_upload_photo() {
        $file_id = $this->input->post('file_id');
        $is_chunked = !empty($file_id);
        if ($is_chunked) {
            $this->_handle_chunked_upload($file_id);
        } else {
            $this->_handle_simple_upload();
        }
    }

    private function _handle_simple_upload() {
        $upload_path = realpath(FCPATH . 'assets') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'students' . DIRECTORY_SEPARATOR;
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|jpeg|png|gif|webp';
        $config['max_size'] = 20480;
        $config['encrypt_name'] = true;
        if (!is_dir($upload_path)) { @mkdir($upload_path, 0755, true); }
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('file')) {
            $data = $this->upload->data();
            $this->json_success(['path' => 'assets/uploads/students/' . $data['file_name']]);
        } else {
            $this->json_error($this->upload->display_errors('', ''));
        }
    }

    private function _handle_chunked_upload($file_id) {
        $chunk_index = (int)$this->input->post('chunk_index');
        $total_chunks = (int)$this->input->post('total_chunks');
        $original_name = $this->input->post('original_name');
        if (empty($_FILES['file'])) { $this->json_error('Aucun fichier reçu'); return; }
        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) { $this->json_error('Erreur chunk ' . $chunk_index); return; }
        $tmp_dir = rtrim(sys_get_temp_dir(), '\\/') . DIRECTORY_SEPARATOR . 'lrn_' . $file_id;
        if (!is_dir($tmp_dir)) { @mkdir($tmp_dir, 0755, true); }
        $chunk_path = $tmp_dir . DIRECTORY_SEPARATOR . 'chunk_' . $chunk_index;
        move_uploaded_file($_FILES['file']['tmp_name'], $chunk_path);
        $all_received = true;
        for ($i = 0; $i < $total_chunks; $i++) {
            if (!file_exists($tmp_dir . DIRECTORY_SEPARATOR . 'chunk_' . $i)) { $all_received = false; break; }
        }
        if ($all_received) {
            $upload_dir = FCPATH . 'assets/uploads/students/';
            if (!is_dir($upload_dir)) { @mkdir($upload_dir, 0755, true); }
            $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) { $this->_cleanup_chunks($tmp_dir); $this->json_error('Format non autorisé'); return; }
            $new_name = md5(uniqid()) . '.' . $ext;
            $final_path = $upload_dir . $new_name;
            $fp = fopen($final_path, 'wb');
            for ($i = 0; $i < $total_chunks; $i++) {
                $cp = $tmp_dir . DIRECTORY_SEPARATOR . 'chunk_' . $i;
                fwrite($fp, file_get_contents($cp));
                @unlink($cp);
            }
            fclose($fp);
            @rmdir($tmp_dir);
            $this->json_success(['path' => 'assets/uploads/students/' . $new_name, 'completed' => true]);
        } else {
            $this->json_success(['completed' => false, 'received' => $chunk_index + 1, 'total' => $total_chunks]);
        }
    }

    private function _cleanup_chunks($dir) {
        if (!is_dir($dir)) return;
        foreach (glob($dir . DIRECTORY_SEPARATOR . '*') as $f) { @unlink($f); }
        @rmdir($dir);
    }

    private function _generate_matricule() {
        $year = date('y');
        $prefix = $year . '/';
        $this->db->select('matricule');
        $this->db->from('etudiants');
        $this->db->like('matricule', $prefix, 'after');
        $this->db->order_by('matricule', 'DESC');
        $this->db->limit(1);
        $q_m = $this->db->get();
        $q = $q_m !== false ? $q_m->row_array() : null;
        if ($q && preg_match('/^(\d+)\/(\d+)$/', $q['matricule'], $m)) {
            $next = (int)$m[2] + 1;
        } else {
            $next = 1;
        }
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function _recalculer_numero_ordre() {
        $this->db->select('i.id_classe, e.id_etudiant, e.nom, e.postnom, e.prenom');
        $this->db->from('inscriptions i');
        $this->db->join('etudiants e', 'e.id_etudiant = i.id_etudiant');
        $this->db->where('i.deleted_at', null);
        $this->db->where('e.deleted_at', null);
        $this->db->where('i.id_annee', $this->id_annee_active);
        $this->db->order_by('i.id_classe ASC, e.nom ASC, e.postnom ASC, e.prenom ASC');
        $q_r = $this->db->get();
        $rows = $q_r !== false ? $q_r->result_array() : array();
        $current_classe = null;
        $seq = 0;
        foreach ($rows as $r) {
            if ($r['id_classe'] !== $current_classe) {
                $current_classe = $r['id_classe'];
                $seq = 1;
            }
            $this->db->where('id_etudiant', $r['id_etudiant']);
            $this->db->update('etudiants', ['numero_ordre' => $seq]);
            $seq++;
        }
    }
}
