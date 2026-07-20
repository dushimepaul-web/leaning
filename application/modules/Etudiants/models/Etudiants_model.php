<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Etudiants_model extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($filters = [])
    {
        $this->db->where('e.deleted_at', null);
        $this->db->select('e.*, i.id_classe, i.id_section, c.libelle as classe_libelle, s.libelle as section_libelle, a.libelle as annee_libelle');
        $this->db->from('etudiants e');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL AND i.id_annee = ' . (int)$this->id_annee_active, 'left');
        $this->db->join('classes c', 'i.id_classe = c.id_classe', 'left');
        $this->db->join('sections s', 'i.id_section = s.id_section', 'left');
        $this->db->join('annees_scolaires a', 'i.id_annee = a.id_annee', 'left');

        if (!empty($filters['id_classe'])) {
            $this->db->where('i.id_classe', $filters['id_classe']);
        }
        if (!empty($filters['id_section'])) {
            $this->db->where('i.id_section', $filters['id_section']);
        }
        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('e.nom', $filters['search'])
                ->or_like('e.prenom', $filters['search'])
                ->or_like('e.matricule', $filters['search'])
                ->or_like('e.email', $filters['search'])
                ->group_end();
        }

        $this->db->order_by('e.nom', 'ASC');
        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->where('e.deleted_at', null);
        $this->db->where('e.uuid', $id);
        $this->db->select('e.*, i.id_classe, i.id_section, c.libelle as classe_libelle, s.libelle as section_libelle, a.libelle as annee_libelle, u.email as user_email, u.role_id');
        $this->db->from('etudiants e');
        $this->db->join('inscriptions i', 'e.id_etudiant = i.id_etudiant AND i.deleted_at IS NULL AND i.id_annee = ' . (int)$this->id_annee_active, 'left');
        $this->db->join('classes c', 'i.id_classe = c.id_classe', 'left');
        $this->db->join('sections s', 'i.id_section = s.id_section', 'left');
        $this->db->join('annees_scolaires a', 'i.id_annee = a.id_annee', 'left');
        $this->db->join('utilisateurs u', 'e.id_utilisateur = u.id_utilisateur', 'left');
        $q = $this->db->get();
        if ($q === false) return null;
        return $q->row_array();
    }

    public function create_record($data)
    {
        $required = ['nom', 'prenom', 'date_naissance', 'sexe', 'id_classe', 'id_section', 'id_annee'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Champ requis manquant: $field"];
            }
        }

        $data['uuid'] = $data['uuid'] ?? generate_uuid();
        $data['cree_le'] = date('Y-m-d H:i:s');
        $data['modifie_le'] = date('Y-m-d H:i:s');

        if ($this->db->insert('etudiants', $data)) {
            $id_etudiant = $this->db->insert_id();

            // Créer inscription
            $inscription = [
                'uuid' => generate_uuid(),
                'id_etudiant' => $id_etudiant,
                'id_classe' => $data['id_classe'],
                'id_section' => $data['id_section'],
                'id_annee' => $data['id_annee'],
                'statut' => 'inscrit',
                'cree_le' => date('Y-m-d H:i:s'),
                'modifie_le' => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('inscriptions', $inscription);

            return ['success' => true, 'id_etudiant' => $id_etudiant];
        }
        return ['success' => false, 'message' => 'Erreur insertion étudiant'];
    }

    public function update_record($id, $data)
    {
        $this->db->where('uuid', $id);
        $data['modifie_le'] = date('Y-m-d H:i:s');
        return $this->db->update('etudiants', $data);
    }

    public function delete_record($id)
    {
        return $this->update_record($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }

    public function count_records($filters = [])
    {
        $this->db->where('deleted_at', null);
        if (!empty($filters['id_classe'])) $this->db->where('id_classe', $filters['id_classe']);
        return $this->db->count_all_results('etudiants');
    }

    // --- Inscriptions ---
    public function get_inscriptions($filters = [])
    {
        $this->db->where('i.deleted_at', null);
        $this->db->select('i.*, e.nom, e.prenom, e.matricule, c.libelle as classe_libelle, s.libelle as section_libelle');
        $this->db->from('inscriptions i');
        $this->db->join('etudiants e', 'i.id_etudiant = e.id_etudiant', 'left');
        $this->db->join('classes c', 'i.id_classe = c.id_classe', 'left');
        $this->db->join('sections s', 'i.id_section = s.id_section', 'left');

        if (!empty($filters['id_classe'])) $this->db->where('i.id_classe', $filters['id_classe']);
        if (!empty($filters['id_section'])) $this->db->where('i.id_section', $filters['id_section']);
        if (!empty($filters['id_annee'])) $this->db->where('i.id_annee', $filters['id_annee']);

        $q = $this->db->get();
        if ($q === false) return array();
        return $q->result_array();
    }

    public function create_inscription($data)
    {
        $required = ['id_etudiant', 'id_classe', 'id_section', 'id_annee'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'message' => "Champ requis manquant: $field"];
            }
        }

        // Vérifier si déjà inscrit
        $exist = $this->readOne('inscriptions', [
            'id_etudiant' => $data['id_etudiant'],
            'id_annee' => $data['id_annee'],
            'deleted_at' => null
        ]);
        if ($exist) {
            return ['success' => false, 'message' => 'Étudiant déjà inscrit cette année'];
        }

        $data['uuid'] = generate_uuid();
        $data['cree_le'] = date('Y-m-d H:i:s');
        $data['modifie_le'] = date('Y-m-d H:i:s');

        if ($this->db->insert('inscriptions', $data)) {
            return ['success' => true, 'id_inscription' => $this->db->insert_id()];
        }
        return ['success' => false, 'message' => 'Erreur inscription'];
    }

    public function update_inscription($id, $data)
    {
        $this->db->where('uuid', $id);
        $data['modifie_le'] = date('Y-m-d H:i:s');
        return $this->db->update('inscriptions', $data);
    }

    public function delete_inscription($id)
    {
        return $this->update_record($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}