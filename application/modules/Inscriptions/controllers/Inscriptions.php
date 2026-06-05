<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *@author:    Dushime Paul
 * Email:    dushimeyesupaulin@gmail.com
*/

class Inscriptions extends MY_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('Model');

        if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Admin'));
            exit;
        }
    }

    
    public function index()
    {
        // Récupérer les inscriptions avec toutes les informations des tables liées
        $data['inscriptions'] = $this->Model->readQuery("
            SELECT 
                i.*,
                s.fullname as student_name,
                s.email as student_email,
                s.phone as student_phone,
                c.nom_course as course_name,
                tc.localisation,
                tc.price,
                t.date_debut,
                t.date_defin,
                acm.nom_attendance as attendance_mode,
                mp.description as payment_mode_name,
                i.status_payement,
                i.status_started_course,
                i.status_ended_course
            FROM inscriptions i
            LEFT JOIN students s ON s.id_student = i.id_student
            LEFT JOIN courses c ON c.id_course = i.id_course
            LEFT JOIN timetable_courses tc ON tc.id_timetable_course = i.id_timetable_course
            LEFT JOIN timetable t ON t.id_timetable = tc.id_timetable
            LEFT JOIN attendace_course_mode acm ON acm.id_attendance = i.id_attendance
            LEFT JOIN mode_payement mp ON mp.id_mode_payement = i.id_mode_payement
            ORDER BY i.date_insertion DESC
        ");
        
        $data['stats'] = $this->GetStatistics();
        $data['courses'] = $this->Model->read('courses', null, 'id_course');
        
        $data['timetable_courses'] = $this->Model->readQuery("
            SELECT tc.*, c.nom_course 
            FROM timetable_courses tc 
            LEFT JOIN courses c ON c.id_course = tc.id_course
        ");
        
        $data['attendance_modes'] = $this->Model->read('attendace_course_mode', null, 'id_attendance');
        $data['payment_modes'] = $this->Model->read('mode_payement', null, 'id_mode_payement');
        $data['students'] = $this->Model->read('students', null, 'id_student');
        
        $this->load->view('Inscriptions_View', $data);
    }


    private function GetStatistics()
{
    $total = $this->Model->count('inscriptions'); // Déjà un entier

    // Paiements validés
    $paid_res = $this->Model->readQuery("
        SELECT COUNT(*) as count 
        FROM inscriptions 
        WHERE status_payement = 'paid'
    ");
    $paid = isset($paid_res[0]['count']) ? (int)$paid_res[0]['count'] : 0;

    // Paiements en attente
    $pending_res = $this->Model->readQuery("
        SELECT COUNT(*) as count 
        FROM inscriptions 
        WHERE status_payement = 'pending'
    ");
    $pending = isset($pending_res[0]['count']) ? (int)$pending_res[0]['count'] : 0;

    // Cours terminés
    $completed_res = $this->Model->readQuery("
        SELECT COUNT(*) as count 
        FROM inscriptions 
        WHERE email_confirmed = 1
    ");
    $completed = isset($completed_res[0]['count']) ? (int)$completed_res[0]['count'] : 0;

    return [
        'total_inscriptions' => $total ?? 0,
        'paid_inscriptions' => $paid,
        'pending_inscriptions' => $pending,
        'email_confirmed' => $completed
    ];
}

    function CreateInscription(){
        $id_course = $this->input->post('id_course');
        $id_timetable_course = $this->input->post('id_timetable_course');
        $id_attendance = $this->input->post('id_attendance'); // Changé: id_attendance au lieu de id_attendance_course_mode
        $id_mode_payement = $this->input->post('id_mode_payement');
        $your_country = $this->input->post('your_country');
        $invoice_type = $this->input->post('invoice_type');
        $id_student = $this->input->post('id_student');

        // Validation des données
        if (empty($id_course) || empty($id_timetable_course) || empty($id_student)) {
            $sms = '<div class="alert alert-danger fade show mt-1 message" role="alert">
                    <strong>Erreur!</strong> Les champs obligatoires sont manquants.
                </div>';
            $this->session->set_flashdata('sms', $sms);
            redirect(base_url('Inscriptions'));
            return;
        }

        $data = array(
            'id_course' => $id_course,
            'id_timetable_course' => $id_timetable_course,
            'id_attendance' => $id_attendance, // Changé: id_attendance
            'id_mode_payement' => $id_mode_payement,
            'your_country' => $your_country,
            'invoice_type' => $invoice_type,
            'status_payement' => 'pending', // Valeur par défaut
            'status_started_course' => 0, // Valeur par défaut
            'status_ended_course' => 0, // Valeur par défaut
            'id_student' => $id_student,
            'date_insertion' => date('Y-m-d H:i:s')
        );
        
        $rsp = $this->Model->create('inscriptions', $data);

        if ($rsp) {
            $sms = '<div class="alert alert-success fade show mt-1 message" role="alert">
                    Inscription créée avec succès.
                </div>';
        } else {
            $sms = '<div class="alert alert-danger fade show mt-1 message" role="alert">
                    <strong>Oups!</strong> Une erreur est survenue, contactez l\'administrateur.
                </div>';
        }
        
        $this->session->set_flashdata('sms', $sms);
        redirect(base_url('Inscriptions'));
    }


    function UpdateInscription(){
        $uuid = $this->input->post('uuid');
        $id_course = $this->input->post('id_course');
        $id_timetable_course = $this->input->post('id_timetable_course');
        $id_attendance = $this->input->post('id_attendance'); // Changé
        $id_mode_payement = $this->input->post('id_mode_payement');
        $your_country = $this->input->post('your_country');
        $invoice_type = $this->input->post('invoice_type');
        $status_payement = $this->input->post('status_payement');
        $status_started_course = $this->input->post('status_started_course');
        $status_ended_course = $this->input->post('status_ended_course');
        $id_student = $this->input->post('id_student');

        $data = array(
            'id_course' => $id_course,
            'id_timetable_course' => $id_timetable_course,
            'id_attendance' => $id_attendance, // Changé
            'id_mode_payement' => $id_mode_payement,
            'your_country' => $your_country,
            'invoice_type' => $invoice_type,
            'status_payement' => $status_payement,
            'status_started_course' => $status_started_course,
            'status_ended_course' => $status_ended_course,
            'id_student' => $id_student
        );
        
        $rsp = $this->Model->update('inscriptions', ['uuid' => $uuid], $data);

        if ($rsp) {
            $sms = '<div class="alert alert-success fade show mt-1 message" role="alert">
                    Inscription modifiée avec succès.
                </div>';
        } else {
            $sms = '<div class="alert alert-danger fade show mt-1 message" role="alert">
                    <strong>Oups!</strong> Une erreur est survenue, contactez l\'administrateur.
                </div>';
        }
        
        $this->session->set_flashdata('sms', $sms);
        redirect(base_url('Inscriptions'));
    }


    function DeleteInscription(){
        $uuid = $this->input->post('uuid');
        $rsp = $this->Model->delete('inscriptions', ['uuid' => $uuid]);

        if ($rsp) {
            $sms = '<div class="alert alert-success fade show mt-1 message" role="alert">
                    Inscription supprimée avec succès.
                </div>';
        } else {
            $sms = '<div class="alert alert-danger fade show mt-1 message" role="alert">
                    <strong>Oups!</strong> Une erreur est survenue, contactez l\'administrateur.
                </div>';
        }
        
        $this->session->set_flashdata('sms', $sms);
        redirect(base_url('Inscriptions'));
    }

    // Fonction pour marquer le paiement comme effectué
    function MarkAsPaid(){
        $uuid = $this->input->post('uuid');
        
        $data = array(
            'status_payement' => 'paid'
        );
        
        $rsp = $this->Model->update('inscriptions', ['uuid' => $uuid], $data);

        if ($rsp) {
            $sms = '<div class="alert alert-success fade show mt-1 message" role="alert">
                    Paiement marqué comme effectué avec succès.
                </div>';
        } else {
            $sms = '<div class="alert alert-danger fade show mt-1 message" role="alert">
                    <strong>Oups!</strong> Une erreur est survenue, contactez l\'administrateur.
                </div>';
        }
        
        $this->session->set_flashdata('sms', $sms);
        redirect(base_url('Inscriptions'));
    }

    function MarkAsCompleted(){
        $uuid = $this->input->post('uuid');
        
        $rsp = $this->Model->update('inscriptions', ['uuid' => $uuid], ['status_ended_course' => 1]);

        if ($rsp) {
            $this->load->library('Cpanel_email');
            redirect(base_url("Certificate/send/$uuid"));
            return;
        } else {
            $sms = '<div class="alert alert-danger fade show mt-1 message" role="alert">
                    <strong>Oups!</strong> Une erreur est survenue, contactez l\'administrateur.
                </div>';
        }
        
        $this->session->set_flashdata('sms', $sms);
        redirect(base_url('Inscriptions'));
    }

   

    // Fonction pour générer une facture
    function GenerateInvoice($uuid){
        // Récupérer les détails de l'inscription
        $inscription = $this->Model->readQuery("
            SELECT 
                i.*,
                s.fullname as student_name,
                s.email as student_email,
                s.phone as student_phone,
                s.address as student_address,
                c.nom_course as course_name,
                tc.price,
                mp.description as payment_mode,
                acm.nom_attendance as attendance_mode
            FROM inscriptions i
            LEFT JOIN students s ON s.id_student = i.id_student
            LEFT JOIN courses c ON c.id_course = i.id_course
            LEFT JOIN timetable_courses tc ON tc.id_timetable_course = i.id_timetable_course
            LEFT JOIN mode_payement mp ON mp.id_mode_payement = i.id_mode_payement
            LEFT JOIN attendace_course_mode acm ON acm.id_attendance = i.id_attendance
            WHERE i.uuid = ?
        ", [$uuid]);
        
        if ($inscription) {
            // Charger la librairie PDF (TCPDF ou DOMPDF)
            $this->load->library('Pdf');
            
            // Créer une nouvelle instance de PDF
            $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
            
            // Configuration du document
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('CERFOP');
            $pdf->SetTitle('Facture N°' . $uuid);
            
            // Ajouter une page
            $pdf->AddPage();
            
            // Contenu HTML de la facture
            $html = '
            <h1>FACTURE</h1>
            <table border="1" cellpadding="5">
                <tr>
                    <td width="50%"><strong>Client:</strong><br>
                        ' . $inscription['student_name'] . '<br>
                        ' . $inscription['student_address'] . '<br>
                        Tél: ' . $inscription['student_phone'] . '<br>
                        Email: ' . $inscription['student_email'] . '
                    </td>
                    <td width="50%">
                        <strong>Facture N°:</strong> ' . $uuid . '<br>
                        <strong>Date:</strong> ' . date('d/m/Y') . '<br>
                        <strong>Cours:</strong> ' . $inscription['course_name'] . '<br>
                        <strong>Prix:</strong> ' . $inscription['price'] . ' €<br>
                    </td>
                </tr>
            </table>
            <br><br>
            <p>Type de facturation: ' . ($inscription['invoice_type'] == 'individual' ? 'Individuel' : 'Entreprise') . '</p>
            <p>Mode de paiement: ' . $inscription['payment_mode'] . '</p>
            <p>Mode de présence: ' . $inscription['attendance_mode'] . '</p>
            <p>Statut: ' . ($inscription['status_payement'] == 'paid' ? 'Payé' : 'En attente') . '</p>
            ';
            
            // Écrire le contenu HTML
            $pdf->writeHTML($html, true, false, true, false, '');
            
            // Output du PDF
            $pdf->Output('facture_' . $uuid . '.pdf', 'D');
            
        } else {
            $sms = '<div class="alert alert-danger fade show mt-1 message" role="alert">
                    <strong>Erreur!</strong> Inscription non trouvée.
                </div>';
            $this->session->set_flashdata('sms', $sms);
            redirect(base_url('Inscriptions'));
        }
    }

    // Fonction pour exporter les inscriptions
    

    public function ExportInscriptions() {
    $inscriptions = $this->Model->readQuery("
        SELECT 
            i.*,
            s.fullname as student_name,
            s.phone as student_phone,
            s.email as student_email,
            c.nom_course as course_name,
            tc.price,
            mp.description as payment_mode,
            acm.nom_attendance as attendance_mode
        FROM inscriptions i
        LEFT JOIN students s ON s.id_student = i.id_student
        LEFT JOIN courses c ON c.id_course = i.id_course
        LEFT JOIN timetable_courses tc ON tc.id_timetable_course = i.id_timetable_course
        LEFT JOIN mode_payement mp ON mp.id_mode_payement = i.id_mode_payement
        LEFT JOIN attendace_course_mode acm ON acm.id_attendance = i.id_attendance
        ORDER BY i.date_insertion DESC
    ");

    // Entête CSV (séparateur ;)
    $output = "Numéro;Étudiant;Email;Téléphone;Cours;Prix;Statut Paiement;Cours Débuté;Cours Terminé;Date Inscription;Pays;Type Facturation\n";

    foreach ($inscriptions as $inscription) {
        $line = [];
        $line[] = $inscription['id_inscription'] ?? '';
        $line[] = $inscription['student_name'] ?? '';
        $line[] = $inscription['student_email'] ?? '';
        $line[] = $inscription['student_phone'] ?? '';
        $line[] = $inscription['course_name'] ?? '';
        $line[] = $inscription['price'] ?? '';
        $line[] = ($inscription['status_payement'] ?? '') == 'paid' ? 'Payé' : (($inscription['status_payement'] ?? '') == 'pending' ? 'En attente' : 'Échoué');
        $line[] = ($inscription['status_started_course'] ?? 0) ? 'Oui' : 'Non';
        $line[] = ($inscription['status_ended_course'] ?? 0) ? 'Oui' : 'Non';
        $line[] = $inscription['date_insertion'] ?? '';
        $line[] = $inscription['your_country'] ?? '';
        $line[] = ($inscription['invoice_type'] ?? '') == 'individual' ? 'Individuel' : 'Entreprise';

        // Joindre les valeurs par point-virgule
        $output .= implode(';', $line) . "\n";
    }

    // Headers pour Excel
    $filename = 'inscriptions_' . date('Y-m-d') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // BOM UTF-8 pour Excel
    echo "\xEF\xBB\xBF"; 
    echo $output;
    exit;
}

    // Fonction pour obtenir les détails d'une inscription
    function GetInscriptionDetails($uuid){
        return $this->Model->readQuery("
            SELECT 
                i.*,
                s.fullname as student_name,
                s.email as student_email,
                s.phone as student_phone,
                s.address as student_address,
                c.nom_course as course_name,
                tc.price,
                mp.description as payment_mode,
                acm.nom_attendance as attendance_mode
            FROM inscriptions i
            LEFT JOIN students s ON s.id_student = i.id_student
            LEFT JOIN courses c ON c.id_course = i.id_course
            LEFT JOIN timetable_courses tc ON tc.id_timetable_course = i.id_timetable_course
            LEFT JOIN mode_payement mp ON mp.id_mode_payement = i.id_mode_payement
            LEFT JOIN attendace_course_mode acm ON acm.id_attendance = i.id_attendance
            WHERE i.uuid = ?
        ", [$uuid]);
    }
}