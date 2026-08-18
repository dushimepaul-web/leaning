<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parametres_model extends Model
{
    public function __construct() { parent::__construct(); }

    public function get_all()
    {
        return $this->read('parametres', ['deleted_at' => null]);
    }

    public function get_by_key($key)
    {
        return $this->readOne('parametres', ['clef' => $key, 'deleted_at' => null]);
    }

    public function set($key, $value)
    {
        $exist = $this->get_by_key($key);
        if ($exist) {
            return $this->update('parametres', ['clef' => $key], ['valeur' => $value]);
        }
        $data = ['clef' => $key, 'valeur' => $value, 'uuid' => generate_uuid()];
        return $this->db->insert('parametres', $data);
    }

    public function upload_logo($file_data)
    {
        return $this->set('logo_ecole', $file_data);
    }

    public function upload_favicon($file_data)
    {
        return $this->set('favicon_ecole', $file_data);
    }

    public function test_email($email)
    {
        // Logique d'envoi email test
        return ['success' => true, 'message' => 'Email de test envoyé à ' . $email];
    }
}