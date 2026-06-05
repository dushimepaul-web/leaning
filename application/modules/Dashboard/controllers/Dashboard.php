<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Admin'));
            exit;
        }
        
    }

    public function index() {
        $data = [];
        
        // Statistiques principales
        $data['total_students'] = $this->Model->count('students');
        $data['total_teachers'] = $this->Model->count('teachers');
        $data['total_courses'] = $this->Model->count('courses');
        $data['total_inscriptions'] = $this->Model->count('inscriptions');
        
        // Statistiques paiements
        $data['paid_inscriptions'] = $this->Model->count_paid_inscriptions();
        $data['pending_inscriptions'] = $this->Model->count_pending_inscriptions();
        $data['failed_inscriptions'] = $this->Model->count_failed_inscriptions();
        
        // Validations email
        $data['email_confirmed'] = $this->Model->count_email_confirmed();
        $data['email_not_confirmed'] = $data['total_inscriptions'] - $data['email_confirmed'];

        // Statistiques emails envoyés
        $data['email_total_sent'] = $this->Model->count_email_logs();
        $data['email_success'] = $this->Model->count_email_logs('succès');
        $data['email_failed'] = $this->Model->count_email_logs('échec');
        $data['email_today'] = $this->Model->count_email_logs_today();
        $data['email_month'] = $this->Model->count_email_logs_month();
        
        // Cours par catégorie
        $data['courses_by_category'] = $this->Model->get_courses_by_category();
        
        // Inscriptions par mois
        $data['inscriptions_by_month'] = $this->Model->get_inscriptions_by_month();
        
        // Dernières inscriptions
        $data['recent_inscriptions'] = $this->Model->get_recent_inscriptions(10);
        
        // Derniers étudiants
        $data['recent_students'] = $this->Model->get_recent_students(5);
        
        // Statistiques avancées
        $data['top_courses'] = $this->Model->get_top_courses(5);
        $data['payment_methods_stats'] = $this->Model->get_payment_methods_stats();
        $data['attendance_mode_stats'] = $this->Model->get_attendance_mode_stats();
        
        // Formateurs avec plus d'étudiants
        $data['top_teachers'] = $this->Model->get_top_teachers(5);
        
        // Évolution des inscriptions
        $data['inscription_trend'] = $this->Model->get_inscription_trend(6);
        
        $this->load->view('Dashboard_view', $data);
    }

    public function get_chart_data() {
        $data = [];
        
        // Données pour le graphique en anneau (paiements)
        $payment_data = [
            'paid' => $this->Model->count_paid_inscriptions(),
            'pending' => $this->Model->count_pending_inscriptions(),
            'failed' => $this->Model->count_failed_inscriptions()
        ];
        
        // Données pour le graphique à barres (inscriptions par mois)
        $monthly_data = $this->Model->get_inscriptions_by_month_chart();
        
        // Données pour le graphique radar (modes de présence)
        $attendance_data = $this->Model->get_attendance_mode_chart();
        
        echo json_encode([
            'success' => true,
            'payment_data' => $payment_data,
            'monthly_data' => $monthly_data,
            'attendance_data' => $attendance_data
        ]);
    }
}
?>