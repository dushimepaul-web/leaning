<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Certificate extends MY_Controller {

    public function index($uuid = null) {
        if (!$uuid) {
            show_404();
        }

        $sql = "
            SELECT s.fullname, c.nom_course, i.date_insertion, i.status_ended_course,
                   t.date_debut, t.date_defin
            FROM inscriptions i
            JOIN students s ON i.id_student = s.id_student
            JOIN courses c ON i.id_course = c.id_course
            JOIN timetable_courses tc ON i.id_timetable_course = tc.id_timetable_course
            JOIN timetable t ON tc.id_timetable = t.id_timetable
            WHERE i.uuid = ?
        ";
        $result = $this->db->query($sql, [$uuid])->row();

        if (!$result) {
            show_404();
        }

        $data['student'] = $result;
        $data['site_name'] = $this->Model->get_setting('site_name', 'CERFOP');
        $data['certificate_no'] = strtoupper(substr(md5($uuid), 0, 8));

        $this->load->view('Certificate_View', $data);
    }

    public function send($uuid = null) {
        if (!$uuid) {
            show_404();
        }

        $sql = "
            SELECT s.fullname, s.email, c.nom_course, i.date_insertion,
                   t.date_debut, t.date_defin
            FROM inscriptions i
            JOIN students s ON i.id_student = s.id_student
            JOIN courses c ON i.id_course = c.id_course
            JOIN timetable_courses tc ON i.id_timetable_course = tc.id_timetable_course
            JOIN timetable t ON tc.id_timetable = t.id_timetable
            WHERE i.uuid = ? AND i.status_ended_course = 1
        ";
        $result = $this->db->query($sql, [$uuid])->row();

        if (!$result) {
            show_404();
        }

        $site_name = $this->Model->get_setting('site_name', 'CERFOP');
        $cert_no = strtoupper(substr(md5($uuid), 0, 8));
        $link = base_url("Pages/Certificate/$uuid");

        $message = "
        <h2>Félicitations ! Votre certificat est prêt</h2>
        <p>Bonjour <strong>$result->fullname</strong>,</p>
        <p>Nous avons le plaisir de vous informer que vous avez terminé avec succès la formation <strong>$result->nom_course</strong>.</p>
        <p>Votre certificat est disponible ici :</p>
        <p style='text-align:center;margin:30px 0'>
            <a href='$link' style='background:#0d6efd;color:#fff;padding:12px 30px;text-decoration:none;border-radius:6px;font-weight:bold'>
                Voir mon certificat
            </a>
        </p>
        <p>Numéro de certificat : <strong>$cert_no</strong></p>
        <p>Cordialement,<br><strong>$site_name</strong></p>
        ";

        $this->load->library('Cpanel_email');
        $this->cpanel_email_lib->send_email(
            $result->email,
            "Votre certificat - $result->nom_course",
            $message
        );

        echo "Certificat envoyé à $result->email";
    }
}
