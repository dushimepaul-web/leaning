<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cpanel_email {
    
    protected $CI;
    protected $from_email;
    protected $from_name;
    
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('email');
        $this->_load_config();
    }

    private function _load_config() {
        $nom_ecole = $this->CI->Model->get_setting('nom_ecole', 'VIP School');
        $email_ecole = $this->CI->Model->get_setting('email_ecole', '');
        
        $this->from_email = $this->CI->Model->get_setting('email_smtp_user', $email_ecole);
        $this->from_email = $this->from_email ?: $email_ecole;
        $this->from_name = $nom_ecole;
    }

    private function _get_mail_config() {
        $protocol = $this->CI->Model->get_setting('email_protocol', 'mail');
        
        $config = array(
            'protocol' => $protocol,
            'charset' => 'utf-8',
            'mailtype' => 'html',
            'newline' => "\r\n",
            'crlf' => "\r\n",
            'wordwrap' => TRUE
        );

        if ($protocol === 'smtp') {
            $config['smtp_host'] = $this->CI->Model->get_setting('email_smtp_host', '');
            $config['smtp_user'] = $this->CI->Model->get_setting('email_smtp_user', '');
            $config['smtp_pass'] = $this->CI->Model->get_setting('email_smtp_pass', '');
            $config['smtp_port'] = $this->CI->Model->get_setting('email_smtp_port', 587);
            $config['smtp_crypto'] = $this->CI->Model->get_setting('email_smtp_crypto', 'tls');
        } elseif ($protocol === 'sendmail') {
            $config['mailpath'] = $this->CI->Model->get_setting('email_sendmail_path', '/usr/sbin/sendmail');
        }

        return $config;
    }
    
    public function send_email($to, $subject, $message) {
        $config = $this->_get_mail_config();
        
        $this->CI->email->initialize($config);
        $this->CI->email->clear();
        $this->CI->email->from($this->from_email, $this->from_name);
        $this->CI->email->to($to);
        $this->CI->email->subject($subject);
        $this->CI->email->message($message);
        
        $start = microtime(true);
        if ($this->CI->email->send()) {
            $this->_log_email($to, $subject, 'succes', null, $start);
            return ['success' => true, 'status' => 200];
        } else {
            $error = $this->CI->email->print_debugger(['headers']);
            $this->_log_email($to, $subject, 'echec', $error, $start);
            return ['success' => false, 'message' => $error];
        }
    }

    private function _log_email($to, $subject, $status, $error = null, $start = null) {
        $duration = $start ? round(microtime(true) - $start, 4) : 0;
        $data = [
            'recipient'  => $to,
            'subject'    => mb_substr($subject, 0, 255),
            'status'     => $status,
            'error_msg'  => $error ? mb_substr($error, 0, 500) : null,
            'duration'   => $duration,
            'sent_at'    => date('Y-m-d H:i:s')
        ];
        if ($this->CI->db->table_exists('email_logs')) {
            $this->CI->db->insert('email_logs', $data);
        }
    }

    public function send_otp_code($to, $user_name, $otp_code, $type = 'reset')
    {
        $site_name = $this->CI->Model->get_setting('nom_ecole', 'VIP School');
        $logo = $this->CI->Model->get_setting('logo_ecole', '');
        $logo_url = !empty($logo) ? base_url($logo) : '';

        if ($type == 'reset') {
            $subject = 'Code de verification - ' . $site_name;
            $action_text = 'Nous avons recu une demande de reinitialisation de votre mot de passe.';
            $warning_text = "Si vous n'etes pas a l'origine de cette demande, ignorez cet email. Aucune modification ne sera apportee a votre compte.";
        } else {
            $subject = 'Confirmez votre adresse email - ' . $site_name;
            $action_text = 'Merci de confirmer votre adresse email.';
            $warning_text = "Si vous n'etes pas a l'origine de cette inscription, ignorez cet email.";
        }

        $message = '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . htmlspecialchars($subject) . '</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    background-color: #f4f6f9; margin: 0; padding: 20px; line-height: 1.5;
                }
                .container {
                    max-width: 520px; margin: 0 auto; background: #ffffff;
                    border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.05);
                }
                .header {
                    background: #0a2540; padding: 32px 24px; text-align: center;
                }
                .logo { max-height: 55px; width: auto; margin-bottom: 12px; }
                .site-title { color: #ffffff; font-size: 20px; font-weight: 600; letter-spacing: -0.3px; }
                .content { padding: 32px 28px; }
                .greeting { font-size: 24px; font-weight: 700; color: #1a2a3a; margin-bottom: 12px; }
                .message-text { color: #5a6a7a; font-size: 15px; margin-bottom: 28px; line-height: 1.6; }
                .code-box {
                    background: #f7f9fc; border-radius: 14px; padding: 28px 20px;
                    text-align: center; margin: 24px 0; border: 1px solid #e8ecf0;
                }
                .code-label {
                    font-size: 13px; color: #8a9aaa; text-transform: uppercase;
                    letter-spacing: 1.5px; font-weight: 600; margin-bottom: 16px;
                }
                .code {
                    font-size: 48px; font-weight: 800; color: #0a66c2; letter-spacing: 12px;
                    font-family: "Courier New", monospace; background: #ffffff;
                    padding: 16px 20px; border-radius: 12px; display: inline-block; border: 1px solid #e0e7ed;
                }
                .expiry { font-size: 13px; color: #8a9aaa; margin-top: 16px; }
                .warning {
                    background: #fef8e7; border-left: 3px solid #f5a623; padding: 14px 18px;
                    font-size: 13px; color: #856404; margin-top: 24px; border-radius: 8px;
                }
                .footer {
                    background: #f8fafc; padding: 24px; text-align: center; border-top: 1px solid #eef2f6;
                }
                .footer-text { font-size: 12px; color: #9aaab9; }
                @media (max-width: 560px) {
                    body { padding: 12px; }
                    .content { padding: 24px 20px; }
                    .code { font-size: 36px; letter-spacing: 8px; padding: 12px 16px; }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">';
        if (!empty($logo_url)) {
            $message .= '<img src="' . $logo_url . '" alt="' . htmlspecialchars($site_name) . '" class="logo">';
        }
        $message .= '
                    <div class="site-title">' . htmlspecialchars($site_name) . '</div>
                </div>
                <div class="content">
                    <div class="greeting">Bonjour ' . htmlspecialchars($user_name) . '</div>
                    <div class="message-text">' . $action_text . '</div>
                    <div class="code-box">
                        <div class="code-label">CODE DE VERIFICATION</div>
                        <div class="code">' . $otp_code . '</div>
                        <div class="expiry">Ce code expire dans 15 minutes</div>
                    </div>
                    <div class="warning">' . $warning_text . '</div>
                </div>
                <div class="footer">
                    <div class="footer-text">&copy; ' . date('Y') . ' ' . htmlspecialchars($site_name) . ' - Tous droits reserves</div>
                </div>
            </div>
        </body>
        </html>';
        
        return $this->send_email($to, $subject, $message);
    }

    public function validate_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public function test_email($to) {
        $site_name = $this->CI->Model->get_setting('nom_ecole', 'VIP School');
        $test_subject = 'Test de configuration - ' . $site_name;
        $test_message = '<h2>Test reussi !</h2><p>Votre serveur email est correctement configure.</p>';
        return $this->send_email($to, $test_subject, $test_message);
    }
}