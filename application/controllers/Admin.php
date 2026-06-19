<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller
{
    public function index()
    {
        $this->load->view('Login_View');
    }

    public function register()
    {
        $this->load->view('Register_View');
    }

    public function Login()
    {
        redirect(base_url('Admin'));
    }

    public function do_login()
    {
        $username = $this->input->post('email');
        $password = $this->input->post('password');

        // Rate limiting: max 5 tentatives en 15 min
        $ip = $this->input->ip_address();
        $attempts_key = 'login_attempts_' . $ip;
        $attempts = $this->session->userdata($attempts_key) ?: ['count' => 0, 'time' => time()];
        if ($attempts['count'] >= 5 && (time() - $attempts['time']) < 900) {
            $sms['sms'] = '<div id="message" class="alert alert-danger text-center"><strong>Trop de tentatives!</strong> Veuillez réessayer dans 15 minutes.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin'));
            return;
        }

        $checkUsername = $this->Model->check_email($username);

        if ($checkUsername) {
            $login = $this->Model->login($username, $password);
            if ($login) {
                // Reset login attempts on success
                $this->session->unset_userdata($attempts_key);
                $result = $this->Model->readOne('utilisateurs', ['id_utilisateur' => $login['id_utilisateur']]);
                if (!$result) {
                    $sms['sms'] = '<div id="message" class="alert alert-danger text-center"><strong>Oups!</strong> Utilisateur non trouvé.</div>';
                    $this->session->set_flashdata($sms);
                    redirect(base_url('Admin'));
                    return;
                }
                $role = $this->Model->readOne('roles', ['id_role' => $result['id_role']]);

                $session = array(
                    'id_utilisateur' => $result['id_utilisateur'],
                    'uuid' => $result['uuid'],
                    'email' => $result['email'],
                    'nom_complet' => $result['nom_complet'],
                    'user' => $result['nom_complet'],
                    'id_role' => $result['id_role'],
                    'role_code' => $role ? $role['code'] : '',
                    'role_libelle' => $role ? $role['libelle'] : '',
                    'logged_in' => TRUE
                );
                $this->session->set_userdata($session);
                redirect(base_url('Dashboard'));
            } else {
                $attempts['count']++;
                $attempts['time'] = time();
                $this->session->set_userdata($attempts_key, $attempts);
                $sms['sms'] = '<div id="message" class="alert alert-danger text-center">
                    <strong>Oups!</strong> Mot de passe incorrect ou compte non activé.
                </div>';
                $this->session->set_flashdata($sms);
                redirect(base_url('Admin'));
            }
        } else {
            $attempts['count']++;
            $attempts['time'] = time();
            $this->session->set_userdata($attempts_key, $attempts);
            $sms['sms'] = '<div id="message" class="alert alert-danger text-center">
                <strong>Oups!</strong> Email incorrect ou compte désactivé.
            </div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin'));
        }
    }

    public function forgot_password()
    {
        $this->load->view('Forgot_Password_View');
    }

    public function do_forgot_password()
    {
        $email = $this->input->post('email');
        if (!$email) {
            $sms['sms'] = '<div id="message" class="alert alert-danger text-center"><strong>Erreur!</strong> Veuillez fournir un email.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin/forgot_password'));
            return;
        }

        $user = $this->Model->readOne('utilisateurs', ['email' => $email, 'actif' => 1, 'deleted_at' => null]);
        if (!$user) {
            $sms['sms'] = '<div id="message" class="alert alert-danger text-center"><strong>Erreur!</strong> Aucun compte trouvé avec cet email.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin/forgot_password'));
            return;
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        if (!function_exists('generate_uuid')) { $this->load->helper('uuid'); }
        $this->db->insert('password_resets', [
            'uuid' => generate_uuid(),
            'id_utilisateur' => $user['id_utilisateur'],
            'code' => $code,
            'token' => $token,
            'expires_at' => $expires,
        ]);

        // Envoyer l'email avec le code OTP
        $this->load->library('Cpanel_email');
        $email_result = $this->cpanel_email->send_otp_code($email, $user['nom_complet'], $code, 'reset');

        if ($email_result['success']) {
            $sms['sms'] = '<div id="message" class="alert alert-success text-center">
                <strong>Succès!</strong> Un code de vérification a été envoyé à ' . htmlspecialchars($email) . '.
            </div>';
        } else {
            $sms['sms'] = '<div id="message" class="alert alert-warning text-center">
                <strong>Attention!</strong> Le code a été généré mais l\'envoi par email a échoué.
                Code: <strong>' . $code . '</strong> (valable 15 min).
            </div>';
        }
        $this->session->set_userdata('reset_email', $email);
        $this->session->set_flashdata($sms);
        redirect(base_url('Admin/verify_otp'));
    }

    public function verify_otp()
    {
        $email = $this->session->userdata('reset_email');
        if (!$email) {
            redirect(base_url('Admin/forgot_password'));
            return;
        }
        $this->load->view('Verify_Otp_View', ['email' => $email]);
    }

    public function do_verify_otp()
    {
        $email = $this->input->post('email');
        $code = $this->input->post('code');

        if (!$email || !$code) {
            $sms['sms'] = '<div id="message" class="alert alert-danger text-center"><strong>Erreur!</strong> Paramètres manquants.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin/forgot_password'));
            return;
        }

        $user = $this->Model->readOne('utilisateurs', ['email' => $email, 'actif' => 1, 'deleted_at' => null]);
        if (!$user) {
            $sms['sms'] = '<div id="message" class="alert alert-danger text-center"><strong>Erreur!</strong> Utilisateur introuvable.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin/forgot_password'));
            return;
        }

        $reset = $this->db->where('id_utilisateur', $user['id_utilisateur'])
            ->where('code', $code)
            ->where('utilise', 0)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->order_by('id_reset', 'DESC')
            ->get('password_resets')
            ->row_array();

        if (!$reset) {
            $sms['sms'] = '<div id="message" class="alert alert-danger text-center"><strong>Erreur!</strong> Code invalide ou expiré.</div>';
            $this->session->set_flashdata($sms);
            $this->session->set_userdata('reset_email', $email);
            redirect(base_url('Admin/verify_otp'));
            return;
        }

        $this->db->where('id_reset', $reset['id_reset'])->update('password_resets', ['utilise' => 1]);
        $this->session->set_userdata('reset_token', $reset['token']);
        redirect(base_url('Admin/reset_password'));
    }

    public function reset_password()
    {
        $token = $this->session->userdata('reset_token');
        if (!$token) {
            redirect(base_url('Admin/forgot_password'));
            return;
        }
        $this->load->view('Reset_Password_View', ['token' => $token]);
    }

    public function do_reset_password()
    {
        $token = $this->input->post('token');
        $password = $this->input->post('password');

        if (!$token || !$password) {
            $sms['sms'] = '<div id="message" class="alert alert-danger text-center"><strong>Erreur!</strong> Paramètres manquants.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin/forgot_password'));
            return;
        }

        $reset = $this->db->where('token', $token)
            ->where('utilise', 1)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->order_by('id_reset', 'DESC')
            ->get('password_resets')
            ->row_array();

        if (!$reset) {
            $sms['sms'] = '<div id="message" class="alert alert-danger text-center"><strong>Erreur!</strong> Token invalide ou expiré.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin/forgot_password'));
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->db->where('id_utilisateur', $reset['id_utilisateur'])->update('utilisateurs', ['mot_de_passe' => $hash]);

        $this->session->unset_userdata('reset_token');

        $sms['sms'] = '<div id="message" class="alert alert-success text-center"><strong>Succès!</strong> Votre mot de passe a été réinitialisé. Connectez-vous avec votre nouveau mot de passe.</div>';
        $this->session->set_flashdata($sms);
        redirect(base_url('Admin'));
    }

    public function do_register()
    {
        $nom_complet = $this->input->post('nom_complet');
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $confirm = $this->input->post('confirm_password');
        $role_code = $this->input->post('role');

        if (!$nom_complet || !$email || !$password || !$confirm || !$role_code) {
            $sms['sms'] = '<div id="message" class="alert alert-danger text-center"><strong>Erreur!</strong> Tous les champs sont requis.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin/register'));
            return;
        }

        if ($password !== $confirm) {
            $sms['sms'] = '<div id="message" class="alert alert-danger text-center"><strong>Erreur!</strong> Les mots de passe ne correspondent pas.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin/register'));
            return;
        }

        if (strlen($password) < 6) {
            $sms['sms'] = '<div id="message" class="alert alert-danger text-center"><strong>Erreur!</strong> Le mot de passe doit contenir au moins 6 caractères.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin/register'));
            return;
        }

        $existing = $this->Model->readOne('utilisateurs', ['email' => $email, 'deleted_at' => null]);
        if ($existing) {
            $sms['sms'] = '<div id="message" class="alert alert-danger text-center"><strong>Erreur!</strong> Cet email est déjà utilisé.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin/register'));
            return;
        }

        // Tous les nouveaux inscrits reçoivent le rôle minimal (Lecture seule)
        $id_role = 5;

        $inserted = $this->Model->createLastId('utilisateurs', [
            'id_role' => $id_role,
            'nom_complet' => $nom_complet,
            'email' => $email,
            'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
            'actif' => 1
        ]);

        if ($inserted) {
            $sms['sms'] = '<div id="message" class="alert alert-success text-center"><strong>Succès!</strong> Votre compte a été créé. Connectez-vous.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin'));
        } else {
            $sms['sms'] = '<div id="message" class="alert alert-danger text-center"><strong>Erreur!</strong> Une erreur est survenue lors de la création du compte.</div>';
            $this->session->set_flashdata($sms);
            redirect(base_url('Admin/register'));
        }
    }

    public function Logout()
    {
        $this->session->sess_destroy();
        redirect(base_url('Admin'));
    }
}
