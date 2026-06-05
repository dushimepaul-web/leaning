<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inscriptions extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    
    public function index()
    {
        $this->load->view('notifications');
    }

    
    public function save_inscription()
    {
        // Récupérer les données du formulaire
        $fullname = $this->input->post('student_fullname', true);
        $email    = $this->input->post('student_email', true);
        $phone    = $this->input->post('student_phone', true);
        $address  = $this->input->post('student_address', true);
        $company  = $this->input->post('company', true) ?: '';

        $uuid_course            = $this->input->post('uuid_course', true);
        $uuid_timetable_course  = $this->input->post('uuid_timetable_course', true);
        $id_attendance_course = $this->input->post('id_attendance', true);

        // Convert UUIDs to integer IDs for FK columns
        $course_arr = $this->Model->read('courses', ['uuid' => $uuid_course]);
        $id_course = !empty($course_arr) ? $course_arr[0]['id_course'] : null;
        $tc_arr = $this->Model->read('timetable_courses', ['uuid' => $uuid_timetable_course]);
        $id_timetable_course = !empty($tc_arr) ? $tc_arr[0]['id_timetable_course'] : null;
        $id_mode_payement     = $this->input->post('id_mode_payement', true);
        $your_country         = $this->input->post('your_country', true);
        $invoice_type         = $this->input->post('invoice_type', true);

        // Valeurs par défaut
        $status_payement       = 'pending';
        $status_started_course = 0;
        $status_ended_course   = 0;

        // Vérifier si l'étudiant existe déjà via l'email
        $existing_student = $this->Model->read('students', ['email' => $email]);
        if (!empty($existing_student)) {
            $id_student = $existing_student[0]['id_student'];
        } else {
            // Créer un nouvel étudiant
            $student_data = [
                'fullname' => $fullname,
                'email'    => $email,
                'phone'    => $phone,
                'address'  => $address,
            ];
            $id_student = $this->Model->createLastId('students', $student_data);
        }

        // Vérifier si l'étudiant est déjà inscrit au cours
        $existing_inscr = $this->Model->read('inscriptions', ['id_student' => $id_student, 'id_course' => $id_course]);
        if (!empty($existing_inscr)) {
            $this->session->set_flashdata('error', 'Vous êtes déjà inscrit à ce cours.');
            redirect('Pages/Inscriptions');
        }

        // Générer le token de confirmation
        $token = bin2hex(random_bytes(32));
        $token_expiration = date('Y-m-d H:i:s', strtotime('+1 day')); // lien valide 24h

        // Préparer les données à insérer dans inscriptions
        $inscription_data = [
            'id_student'               => $id_student,
            'id_course'                => $id_course,
            'id_timetable_course'      => $id_timetable_course,
            'id_attendance'            => $id_attendance_course,
            'id_mode_payement'         => $id_mode_payement,
            'your_country'             => $your_country,
            'invoice_type'             => $invoice_type,
            'status_payement'          => $status_payement,
            'status_started_course'    => $status_started_course,
            'status_ended_course'      => $status_ended_course,
            'email_confirmed'          => 0,
            'email_confirmation_token' => $token,
            'token_expired_at'         => $token_expiration,
            'date_insertion'           => date('Y-m-d H:i:s')
        ];

        // Insérer l'inscription
        $rsp = $this->Model->create('inscriptions', $inscription_data);

        if ($rsp) {
        // Récupération des informations détaillées (Cours + Dates + Modes)
        $sql = "
            SELECT c.nom_course, t.date_debut, t.date_defin, am.nom_attendance, mp.description
            FROM inscriptions i
            JOIN courses c ON i.id_course = c.id_course
            JOIN timetable_courses tc ON i.id_timetable_course = tc.id_timetable_course
            JOIN timetable t ON tc.id_timetable = t.id_timetable
            JOIN attendace_course_mode am ON i.id_attendance = am.id_attendance
            JOIN mode_payement mp ON i.id_mode_payement = mp.id_mode_payement
            WHERE i.email_confirmation_token = ?
        ";
        $info = $this->Model->readQuery($sql, [$token]);
        $details = !empty($info) ? $info[0] : [];
        $logo_url = base_url('attachments/Other/' . $this->Model->get_setting('site_logo', 'logo.png'));
        $confirmation_link = base_url("Inscriptions/confirm_email/$token");

      $message = "
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        /* Media Queries pour simuler le comportement responsive de Bootstrap */
        @media only screen and (max-width: 600px) {
            .container-email { width: 100% !important; margin: 0 !important; }
            .col-stack { display: block !important; width: 100% !important; text-align: left !important; }
            .p-mobile { padding: 20px !important; }
            .btn-mobile { width: 100% !important; display: block !important; }
        }
    </style>
</head>
<body style='margin: 0; padding: 0; background-color: #f8f9fa; font-family: system-ui, -apple-system, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial;'>

    <table width='100%' border='0' cellspacing='0' cellpadding='0' style='background-color: #f8f9fa; padding: 20px 0;'>
        <tr>
            <td align='center'>
                
                <table class='container-email' width='600' border='0' cellspacing='0' cellpadding='0' style='background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 0.5rem; overflow: hidden; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);'>
                    
                    <tr>
                        <td align='center' style='padding: 1.5rem; background-color: #ffffff; border-bottom: 1px solid #dee2e6;'>
                            <img src='$logo_url' alt='".e($this->Model->get_setting('site_name','AbeLab'))." Logo' style='max-height: 60px; height: auto;'>
                        </td>
                    </tr>

                    <tr>
                        <td class='p-mobile' style='padding: 3rem;'>
                            <h1 style='font-size: 1.75rem; font-weight: 700; color: #212529; margin-bottom: 1rem; text-align: center;'>
                                Confirmation d'inscription
                            </h1>
                            
                            <p style='font-size: 1rem; color: #495057; line-height: 1.5; margin-bottom: 2rem;'>
                                Bonjour <span style='font-weight: 600;'>$fullname</span>,<br>
                                Nous sommes ravis de vous accueillir. Voici un récapitulatif des détails de votre formation :
                            </p>

                            <div style='background-color: #f8f9fa; border-radius: 0.375rem; padding: 1.5rem; margin-bottom: 2rem;'>
                                <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                                    <tr>
                                        <td class='col-stack' style='padding: 8px 0; color: #6c757d; font-size: 0.875rem; text-transform: uppercase; font-weight: 600;'>Formation</td>
                                        <td class='col-stack' style='padding: 8px 0; color: #212529; font-weight: 700; text-align: right;'>{$details['nom_course']}</td>
                                    </tr>
                                    <tr>
                                        <td class='col-stack' style='padding: 8px 0; border-top: 1px solid #dee2e6; color: #6c757d; font-size: 0.875rem; text-transform: uppercase; font-weight: 600;'>Période</td>
                                        <td class='col-stack' style='padding: 8px 0; border-top: 1px solid #dee2e6; color: #212529; font-weight: 700; text-align: right;'>".date('d/m/Y', strtotime($details['date_debut']))." - ".date('d/m/Y', strtotime($details['date_defin']))."</td>
                                    </tr>
                                    <tr>
                                        <td class='col-stack' style='padding: 8px 0; border-top: 1px solid #dee2e6; color: #6c757d; font-size: 0.875rem; text-transform: uppercase; font-weight: 600;'>Mode</td>
                                        <td class='col-stack' style='padding: 8px 0; border-top: 1px solid #dee2e6; color: #212529; font-weight: 700; text-align: right;'>{$details['nom_attendance']}</td>
                                    </tr>
                                    <tr>
                                        <td class='col-stack' style='padding: 8px 0; border-top: 1px solid #dee2e6; color: #6c757d; font-size: 0.875rem; text-transform: uppercase; font-weight: 600;'>Paiement</td>
                                        <td class='col-stack' style='padding: 8px 0; border-top: 1px solid #dee2e6; color: #212529; font-weight: 700; text-align: right;'>{$details['description']}</td>
                                    </tr>
                                </table>
                            </div>

                            <div style='text-align: center;'>
                                <p style='color: #6c757d; font-size: 0.9rem; margin-bottom: 1.5rem;'>Merci de confirmer votre email pour activer votre place :</p>
                                <a href='$confirmation_link' class='btn-mobile' style='background-color: #0d6efd; color: #ffffff; padding: 0.8rem 2rem; text-decoration: none; border-radius: 0.375rem; font-weight: 600; font-size: 1rem; display: inline-block;'>
                                    Confirmer mon inscription
                                </a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style='background-color: #212529; color: #adb5bd; padding: 2rem; text-align: center; font-size: 0.8rem;'>
                            <p style='color: #ffffff; font-weight: 600; margin-bottom: 0.5rem;'>".e($this->Model->get_setting('site_name','CERFOP'))."</p>
                            <p style='margin-bottom: 1.5rem;'>".e($this->Model->get_setting('site_description','Centre de Formation Professionnelle d\'Excellence'))."</p>
                            <hr style='border: 0; border-top: 1px solid #495057; margin-bottom: 1.5rem;'>
                            <p>&copy; ".date('Y')." Tous droits réservés. <br> Ce lien expire dans 24 heures.</p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</body>
</html>";

        $this->load->library('Cpanel_email');
        $subject = 'Validation de votre inscription - '.$details['nom_course'];
        $result = $this->cpanel_email_lib->send_email($email, $subject, $message);

            if($result['success']){
                // Notification admin
                $msg_admin = "
                <h2>Nouvelle inscription</h2>
                <p><strong>Étudiant :</strong> $fullname</p>
                <p><strong>Email :</strong> $email</p>
                <p><strong>Téléphone :</strong> $phone</p>
                <p><strong>Formation :</strong> {$details['nom_course']}</p>
                <p><strong>Période :</strong> " . date('d/m/Y', strtotime($details['date_debut'])) . " - " . date('d/m/Y', strtotime($details['date_defin'])) . "</p>
                <p><strong>Mode :</strong> {$details['nom_attendance']}</p>
                <p><strong>Paiement :</strong> {$details['description']}</p>
                ";
                $this->cpanel_email_lib->send_email('infos@cerfop.bi', 'Nouvelle inscription - ' . $details['nom_course'], $msg_admin);

                $data = [
            'fullname'     => $fullname,
            'email'        => $email,
            'course_name'  => $details['nom_course'],
            'date_debut'   => $details['date_debut'],
            'date_defin'   => $details['date_defin'],
            'attendance'   => $details['nom_attendance'],
            'payement'     => $details['description']
        ];

                $this->load->view('Success_View', $data);

            } else {
                $this->session->set_flashdata('error', 'L\'email de confirmation n\'a pas pu être envoyé. Contactez l\'administration.');
                redirect('Pages/Inscriptions');
            }
        } else {
            $this->session->set_flashdata('error', 'Une erreur est survenue lors de l’inscription.');
            redirect('Pages/Inscriptions');
        }
    }

    
   
    public function confirm_email($token){

    if (!$token) {
        $this->session->set_flashdata('error', 'Lien de confirmation invalide.');
        redirect('Pages/Inscriptions');
        return;
    }

    // Récupérer l'inscription avec les détails du cours
    $sql = "SELECT * FROM inscriptions  
            WHERE email_confirmation_token = ?";
    $result = $this->Model->readQuery($sql, [$token]);

    if (empty($result)) {
        $this->session->set_flashdata('error', 'Lien invalide.');
        redirect('Pages/Inscriptions');
        return;
    }

    $row = $result[0];

    // Vérification expiration
    if (strtotime($row['token_expired_at']) < time()) {
        $this->session->set_flashdata('error', 'Le lien de confirmation a expiré.');
        redirect('Pages/Inscriptions');
        return;
    }

    // Mise à jour
    $update_data = [
        'email_confirmed'          => 1,
        'email_confirmed_at'       => date('Y-m-d H:i:s'),
        'email_confirmation_token' => NULL,
        'token_expired_at'         => NULL
    ];

    $rps=$this->Model->update('inscriptions', ['id_inscription' => $row['id_inscription']], $update_data);

    if($rps){
       $this->session->set_flashdata('success', 'votre email a ete verifie avec success.');
       redirect('Pages/Inscriptions');
    }else{
         $this->session->set_flashdata('error', 'erreur.');
         redirect('Pages/Inscriptions');
    }
}


    public function test_openssl()
{
    if (extension_loaded('openssl')) {
        echo "✅ OpenSSL est activé. Vous pouvez envoyer des emails via Gmail SMTP.";
    } else {
        echo "❌ OpenSSL n'est pas activé. Activez-le dans php.ini avant de continuer.";
    }
}

}