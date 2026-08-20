<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Etudiants extends MY_Controller {
    public function __construct() {
        parent::__construct();
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
        $e['nom'] = $e['fullname'] ?? '';
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
        $e['nom'] = $e['fullname'] ?? '';
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
        if ($this->input->get('classe')) $this->db->where('i.id_classe', (int)$this->input->get('classe'));
        if ($this->input->get('section')) $this->db->where('i.id_section', (int)$this->input->get('section'));
        if ($this->input->get('sexe')) $this->db->where('e.sexe', $this->input->get('sexe'));
        if ($this->input->get('statut')) $this->db->where('i.statut', $this->input->get('statut'));
        if ($this->input->get('q')) {
            $q = trim($this->input->get('q'));
            $this->db->group_start();
            $this->db->like('e.fullname', $q);
            $this->db->or_like('e.matricule', $q);
            $this->db->or_like('e.numero_ordre', $q);
            $this->db->group_end();
        }
        $this->db->order_by('e.id_etudiant', 'DESC');
        $q_e = $this->db->get();
        $etudiants = $q_e !== false ? $q_e->result_array() : array();

        foreach ($etudiants as &$et) {
            $et['nom_complet'] = $et['fullname'] ?? '';
        }
        $this->json_success($etudiants);
    }

    public function api_get($id) {
        $e = $this->Model->readOne('etudiants', ['uuid' => $id]);
        if (!$e) { $this->json_error('Étudiant non trouvé', 404); return; }
        $insc = $this->Model->readOne('inscriptions', ['id_etudiant' => $e['id_etudiant'], 'deleted_at' => null]);
        $e['inscription'] = $insc;
        $e['nom_complet'] = $e['fullname'] ?? '';
        $this->json_success($e);
    }

    public function api_create() {
        $data = $this->get_json_input();
        if (!$this->_rate_limit_write()) return;
        if (empty($data['nom'])) {
            $this->json_error('Le nom complet est obligatoire');
            return;
        }
        $err = $this->_valider_champs($data);
        if ($err) { $this->json_error($err); return; }
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

        $section_finale = null;
        if (!empty($id_classe)) {
            $err = $this->Model->valider_section_classe($id_classe, $id_section, $section_finale);
            if ($err) { $this->json_error($err); return; }
        } elseif (!empty($id_section)) {
            $section = $this->Model->readOne('sections', ['id_section' => $id_section, 'deleted_at' => null]);
            if (!$section) { $this->json_error('Section introuvable'); return; }
            $section_finale = $id_section;
        }
        $annee_existe = $this->Model->readOne('annees_scolaires', ['id_annee' => $id_annee]);
        if (!$annee_existe) { $this->json_error('Année scolaire introuvable'); return; }
        $data['fullname'] = $data['nom'] ?? '';
        $cols_etudiant = ['fullname','date_naissance','sexe','telephone','email','adresse','adresse_permanente','photo','matricule','lieu_naissance','pere_nom','pere_telephone','pere_profession','pere_adresse','mere_nom','mere_telephone','mere_profession','mere_adresse'];
        $clean = [];
        foreach ($cols_etudiant as $col) {
            if (isset($data[$col]) && $data[$col] !== '') {
                $clean[$col] = $data[$col];
            }
        }

        $id_user_log = $this->session->userdata('id_utilisateur');

        $this->db->trans_start();
        $id = $this->Model->createLastId('etudiants', $clean);
        if ($id) {
            if (!empty($id_classe)) {
                $this->Model->create('inscriptions', [
                    'id_etudiant' => $id,
                    'id_classe' => $id_classe,
                    'id_section' => $section_finale,
                    'id_annee' => $id_annee,
                    'date_inscription' => date('Y-m-d')
                ]);
                $this->_ensure_conduite_points($id, $id_annee);
            }
            $data['id_etudiant'] = $id;
            $account = $this->_create_linked_user('etudiants', $id, $data, 'eleve');
            $result = ['id_etudiant' => $id];
            if ($account) {
                $result['id_utilisateur'] = $account['id_utilisateur'];
                $result['default_password'] = $account['default_password'];
            }
            $this->Model->recalculer_numero_ordre($this->id_annee_active);
        }
        $this->db->trans_complete();

        if ($id && $this->db->trans_status() !== false) {
            $this->Model->Set_History($id_user_log, 'create', 'Création étudiant #' . $id, 'etudiants', $id, null, $clean);
            if (!empty($id_classe)) {
                $this->Model->Set_History($id_user_log, 'create', 'Inscription étudiant #' . $id, 'inscriptions', $id);
            }
            $this->json_success($result, 'Étudiant créé avec succès');
        } else {
            $this->json_error('Erreur lors de la création');
        }
    }

    public function api_update($id) {
        $data = $this->get_json_input();
        if (!$this->_rate_limit_write()) return;
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
        $err = $this->_valider_champs($data);
        if ($err) { $this->json_error($err); return; }

        $updateData = $data;
        if (isset($updateData['nom'])) {
            $updateData['fullname'] = $updateData['nom'];
            unset($updateData['nom']);
        }
        unset($updateData['id_classe'], $updateData['id_section'], $updateData['id_annee'], $updateData['parents'], $updateData['parent_nom_old']);
        $cols_etudiant = ['fullname','matricule','numero_ordre','date_naissance','lieu_naissance','sexe','adresse','adresse_permanente','telephone','email','parent_nom','parent_telephone','parent_profession','parent_adresse','pere_nom','pere_telephone','pere_profession','pere_adresse','mere_nom','mere_telephone','mere_profession','mere_adresse','tuteur_nom','tuteur_telephone','actif'];
        $clean = [];
        foreach ($cols_etudiant as $col) {
            if (isset($updateData[$col])) {
                $clean[$col] = $updateData[$col];
            }
        }
        $id_user_log = $this->session->userdata('id_utilisateur');

        $this->db->trans_start();
        $etudiant_ok = empty($clean) ? true : $this->Model->update('etudiants', ['uuid' => $id], $clean);
        if ($etudiant_ok) {
            if (!empty($data['id_classe'])) {
                $section_finale = null;
                $err = $this->Model->valider_section_classe($data['id_classe'], $data['id_section'] ?? null, $section_finale);
                if ($err) {
                    $this->db->trans_rollback();
                    $this->json_error($err); return;
                }
                $data['id_section'] = $data['id_section'] ?? $section_finale;
                if (!empty($data['id_annee'])) {
                    $annee_existe = $this->Model->readOne('annees_scolaires', ['id_annee' => $data['id_annee']]);
                    if (!$annee_existe) {
                        $this->db->trans_rollback();
                        $this->json_error('Année scolaire introuvable'); return;
                    }
                }
                $insc = $this->Model->readOne('inscriptions', ['id_etudiant' => $etudiant['id_etudiant'], 'deleted_at' => null]);
                if ($insc) {
                    $nouvelle_annee = $data['id_annee'] ?? $insc['id_annee'];
                    if ($nouvelle_annee != $insc['id_annee']) {
                        $autre = $this->Model->readOne('inscriptions', ['id_etudiant' => $etudiant['id_etudiant'], 'id_annee' => $nouvelle_annee, 'deleted_at' => null]);
                        if ($autre && $autre['id_inscription'] != $insc['id_inscription']) {
                            $this->db->trans_rollback();
                            $this->json_error('Cet étudiant est déjà inscrit pour cette année scolaire'); return;
                        }
                    }
                    $this->Model->update('inscriptions', ['id_inscription' => $insc['id_inscription']], [
                        'id_classe' => $data['id_classe'],
                        'id_section' => $data['id_section'] ?? $insc['id_section'],
                        'id_annee' => $nouvelle_annee
                    ]);
                    $this->_ensure_conduite_points($etudiant['id_etudiant'], $nouvelle_annee);
                } else {
                    $nouvelle_annee = $data['id_annee'] ?? $this->id_annee_active;
                    $this->Model->create('inscriptions', [
                        'id_etudiant' => $etudiant['id_etudiant'], 'id_classe' => $data['id_classe'],
                        'id_section' => $data['id_section'] ?? null,
                        'id_annee' => $nouvelle_annee,
                        'date_inscription' => date('Y-m-d')
                    ]);
                    $this->_ensure_conduite_points($etudiant['id_etudiant'], $nouvelle_annee);
                }
            } else {
                $insc = $this->Model->readOne('inscriptions', ['id_etudiant' => $etudiant['id_etudiant'], 'deleted_at' => null]);
                if (!$insc) {
                    $this->db->trans_rollback();
                    $this->json_error('Sélectionnez une classe pour l\'inscription de l\'étudiant');
                    return;
                }
            }
            $this->_sync_linked_user('etudiants', $etudiant['id_etudiant'], $data);
            $this->Model->recalculer_numero_ordre($this->id_annee_active);
            $this->db->trans_complete();

            if ($this->db->trans_status() !== false) {
                $this->Model->Set_History($id_user_log, 'update', 'Modification étudiant #' . $etudiant['id_etudiant'], 'etudiants', $etudiant['id_etudiant'], $etudiant, $clean);
                $this->json_success(null, 'Étudiant mis à jour');
            } else {
                $this->json_error('Erreur de mise à jour');
            }
        } else {
            $this->db->trans_rollback();
            $this->json_error('Erreur de mise à jour');
        }
    }

    public function api_delete($id) {
        if (!$this->_rate_limit_write()) return;
        $etudiant = $this->Model->readOne('etudiants', ['uuid' => $id]);
        if (!$etudiant) { $this->json_error('Étudiant non trouvé', 404); return; }
        if ($this->Model->update('etudiants', ['uuid' => $id], ['deleted_at' => date('Y-m-d H:i:s')])) {
            $insc = $this->Model->readOne('inscriptions', ['id_etudiant' => $etudiant['id_etudiant'], 'deleted_at' => null]);
            $this->Model->update('inscriptions', ['id_etudiant' => $etudiant['id_etudiant']], ['deleted_at' => date('Y-m-d H:i:s')]);
            if ($insc) {
                $this->_remove_conduite_points($etudiant['id_etudiant'], $insc['id_annee']);
            }
            if (!empty($etudiant['id_utilisateur'])) {
                $this->Model->update('utilisateurs', ['id_utilisateur' => $etudiant['id_utilisateur']], ['deleted_at' => date('Y-m-d H:i:s'), 'actif' => 0]);
            }
            $this->Model->recalculer_numero_ordre($this->id_annee_active);
            $this->Model->Set_History($this->session->userdata('id_utilisateur'), 'delete', 'Suppression étudiant #' . $etudiant['id_etudiant'], 'etudiants', $etudiant['id_etudiant'], $etudiant, ['deleted_at' => date('Y-m-d H:i:s')]);
            $this->json_success(null, 'Étudiant supprimé');
        } else {
            $this->json_error('Erreur de suppression');
        }
    }

    public function api_upload_photo() {
        $this->_cleanup_old_chunks();
        $file_id = $this->input->post('file_id');
        $is_chunked = !empty($file_id);
        if ($is_chunked) {
            if (!preg_match('/^[A-Za-z0-9_-]{1,128}$/', $file_id)) { $this->json_error('Identifiant de téléversement invalide'); return; }
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
        $this->upload->initialize($config);
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
        $this->load->helper('uuid');
        $key = 'compteur_matricule_' . $year;

        $compteur = $this->db->query("SELECT valeur FROM parametres WHERE clef = '" . $key . "'");
        if ($compteur === false || $compteur->num_rows() === 0) {
            $max = $this->db->query(
                "SELECT MAX(CAST(SUBSTRING_INDEX(matricule, '/', -1) AS UNSIGNED)) AS m
                 FROM etudiants WHERE matricule LIKE " . $this->db->escape($year . '/%')
            );
            $row_max = $max !== false ? $max->row_array() : null;
            $start = ($row_max && $row_max['m'] !== null) ? (int)$row_max['m'] : 0;
            // Insertion initiale : le compteur vaut déjà le premier numéro à attribuer (start+1)
            $this->db->query(
                "INSERT INTO parametres (clef, valeur, uuid) VALUES ('" . $key . "', " . ($start + 1) . ", '" . generate_uuid() . "')
                 ON DUPLICATE KEY UPDATE valeur = valeur + 1"
            );
        } else {
            $this->db->query(
                "INSERT INTO parametres (clef, valeur, uuid) VALUES ('" . $key . "', 0, '" . generate_uuid() . "')
                 ON DUPLICATE KEY UPDATE valeur = valeur + 1"
            );
        }
        $row = $this->db->query("SELECT valeur FROM parametres WHERE clef = '" . $key . "'")->row_array();
        $next = $row ? (int)$row['valeur'] : 1;
        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    private function _valider_champs($data) {
        if (isset($data['sexe']) && $data['sexe'] !== '' && !in_array($data['sexe'], ['M', 'F'], true)) {
            return 'Le sexe doit être M ou F';
        }
        if (!empty($data['date_naissance'])) {
            $d = DateTime::createFromFormat('Y-m-d', $data['date_naissance']);
            if (!$d || $d->format('Y-m-d') !== $data['date_naissance']) {
                return 'Date de naissance invalide (format attendu : AAAA-MM-JJ)';
            }
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Adresse email invalide';
        }
        if (isset($data['telephone']) && $data['telephone'] !== '' && !preg_match('/^[0-9+\s\-().]+$/', $data['telephone'])) {
            return 'Numéro de téléphone invalide';
        }
        return null;
    }

    private function _rate_limit_write() {
        $key = 'api_write_limit';
        $now = time();
        $d = $this->session->userdata($key);
        if (!is_array($d) || (int)$d['t'] < $now - 3600) {
            $d = array('t' => $now, 'n' => 0);
        }
        $d['n']++;
        $this->session->set_userdata($key, $d);
        if ($d['n'] > 300) {
            $this->json_error('Trop de requêtes. Veuillez réessayer plus tard.', 429);
            return false;
        }
        return true;
    }

    private function _cleanup_old_chunks($max_age = 86400) {
        $tmp_base = rtrim(sys_get_temp_dir(), '\\/');
        $dirs = glob($tmp_base . DIRECTORY_SEPARATOR . 'lrn_*');
        if ($dirs === false) return;
        foreach ($dirs as $d) {
            if (is_dir($d) && (time() - @filemtime($d) > $max_age)) {
                foreach (glob($d . DIRECTORY_SEPARATOR . '*') as $f) { @unlink($f); }
                @rmdir($d);
            }
        }
    }
}
