<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Base.php';

class Minervale_audit extends Minervale_base
{
    public function log($action, $table, $record_id = null, $old = null, $new = null, $motif = null)
    {
        $nouvelles = $new;
        if ($motif !== null && $motif !== '') {
            if (!is_array($nouvelles)) {
                $nouvelles = $nouvelles === null ? array() : array('valeur' => $nouvelles);
            }
            $nouvelles['_motif'] = $motif;
        }

        $data = array(
            'uuid' => self::uuid(),
            'id_utilisateur' => $this->userId(),
            'action' => substr((string)$action, 0, 50),
            'table_concernee' => substr((string)$table, 0, 50),
            'id_enregistrement' => $record_id !== null ? (int)$record_id : null,
            'anciennes_valeurs' => $old !== null ? json_encode($old, JSON_UNESCAPED_UNICODE) : null,
            'nouvelles_valeurs' => $nouvelles !== null ? json_encode($nouvelles, JSON_UNESCAPED_UNICODE) : null,
            'adresse_ip' => $this->ip(),
            'date_action' => $this->now()
        );

        try {
            return $this->db->insert('audit_logs', $data);
        } catch (Throwable $e) {
            if (function_exists('log_message')) {
                log_message('error', 'Minervale audit: ' . $e->getMessage());
            }
            return false;
        }
    }
}
