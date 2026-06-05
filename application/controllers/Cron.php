<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('Cpanel_email');
        $this->load->model('Model');
    }

    public function reminder() {
        // Étudiants dont la formation commence dans 3 jours
        $students = $this->_get_students_for_reminder(3);

        $sent = 0;
        $errors = 0;

        foreach ($students as $student) {
            $site_name = $this->Model->get_setting('site_name', 'CERFOP');
            $date = date('d/m/Y', strtotime($student->date_debut));
            $message = "
            <h2>Rappel : Votre formation commence bientôt !</h2>
            <p>Bonjour <strong>$student->fullname</strong>,</p>
            <p>Nous vous rappelons que votre formation <strong>$student->nom_course</strong> commence le <strong>$date</strong>.</p>
            <p>Merci de vous préparer et d'arriver à l'heure.</p>
            <p>Pour toute question, contactez-nous à <strong>infos@cerfop.bi</strong>.</p>
            <br>
            <p>Cordialement,<br><strong>$site_name</strong></p>
            ";

            $result = $this->cpanel_email_lib->send_email(
                $student->email,
                "Rappel : Formation \"$student->nom_course\" commence le $date",
                $message
            );

            if ($result['success']) {
                $this->Model->update('inscriptions', ['id_inscription' => $student->id_inscription], ['reminder_sent' => 1]);
                $sent++;
            } else {
                $errors++;
            }
        }

        echo "Rappels envoyés : $sent | Erreurs : $errors";
    }

    private function _get_students_for_reminder($days = 3) {
        $sql = "
            SELECT s.fullname, s.email, c.nom_course, t.date_debut, i.id_inscription
            FROM inscriptions i
            JOIN students s ON i.id_student = s.id_student
            JOIN courses c ON i.id_course = c.id_course
            JOIN timetable_courses tc ON i.id_timetable_course = tc.id_timetable_course
            JOIN timetable t ON tc.id_timetable = t.id_timetable
            WHERE i.email_confirmed = 1
            AND i.reminder_sent = 0
            AND t.date_debut BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL ? DAY)
        ";
        return $this->db->query($sql, [$days])->result();
    }
}
