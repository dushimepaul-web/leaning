<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Galleries extends MY_Controller {

    function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect(base_url('Admin'));
            exit;
        }
    }

    public function index()
    {
        $data['galleries'] = $this->Model->read('gallery', null, 'IdGallery');
        $this->load->view('galleries_views', $data);
    }

    /* ================== CREATE ================== */
    public function create()
    {
        $type        = $this->input->post('TypeMedia', true);
        $description = $this->input->post('Description', true);
        $media       = $this->input->post('Media', true);

        $isAjax = $this->input->post('ajax');

        if (!in_array($type, ['image','video','link'])) {
            if ($isAjax) $this->_json(['error' => 'Type invalide']);
            redirect('galleries');
        }

        /* ===== IMAGE ===== */
        if ($type == 'image') {
            $config = [
                'upload_path'   => './attachments/gallery/',
                'allowed_types' => 'jpg|jpeg|png|gif',
                'max_size'      => 20480,
                'encrypt_name'  => true
            ];

            $folder = FCPATH . 'attachments/gallery/';
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            $this->upload->initialize($config);

            if (!$this->upload->do_upload('Media')) {
                $errors = $this->upload->display_errors();
                if ($isAjax) $this->_json(['error' => strip_tags($errors)]);
                $this->session->set_flashdata('sms',
                    '<div class="alert alert-danger">'.$errors.'</div>'
                );
                redirect('galleries');
            }

            $media = $this->upload->data('file_name');
        }

        /* ===== VIDEO (chunked — filename already in $media) ===== */
        if ($type == 'video') {
            if (empty($media)) {
                if ($isAjax) $this->_json(['error' => 'Aucune vidéo reçue.']);
                $this->session->set_flashdata('sms',
                    '<div class="alert alert-danger">Aucune vidéo reçue.</div>'
                );
                redirect('galleries');
            }
        }

        /* ===== LINK ===== */
        if ($type == 'link') {
            $media = $this->input->post('Link', true);
        }

        $this->Model->create('gallery', [
            'TypeMedia'   => $type,
            'Media'       => $media,
            'Description' => $description
        ]);

        if ($isAjax) {
            $this->_json(['success' => true, 'message' => 'Média ajouté avec succès.']);
        }

        $this->session->set_flashdata('sms',
            '<div class="alert alert-success">Média ajouté avec succès.</div>'
        );

        redirect('galleries');
    }

    private function _json($data)
    {
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /* ================== CHUNKED VIDEO UPLOAD ================== */
    public function upload_chunk()
    {
        $targetDir  = FCPATH . 'attachments/gallery/tmp/';
        $finalDir   = FCPATH . 'attachments/gallery/';

        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        if (!is_dir($finalDir))  mkdir($finalDir, 0777, true);

        $identifier  = $this->input->post('identifier');
        $chunkIndex  = (int) $this->input->post('chunkIndex');
        $totalChunks = (int) $this->input->post('totalChunks');
        $filename    = $this->input->post('filename');

        $chunkFile = $targetDir . $identifier . '_part_' . $chunkIndex;

        if (!empty($_FILES['chunk']['tmp_name']) && is_uploaded_file($_FILES['chunk']['tmp_name'])) {
            move_uploaded_file($_FILES['chunk']['tmp_name'], $chunkFile);
        } else {
            $input = file_get_contents('php://input');
            file_put_contents($chunkFile, $input);
        }

        $uploadedChunks = glob($targetDir . $identifier . '_part_*');
        $received       = count($uploadedChunks);

        if ($received >= $totalChunks) {
            $ext  = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $name = date('YmdHis') . uniqid() . '.' . $ext;
            $final = $finalDir . $name;

            $fp = fopen($final, 'wb');
            for ($i = 0; $i < $totalChunks; $i++) {
                $part = $targetDir . $identifier . '_part_' . $i;
                if (file_exists($part)) {
                    fwrite($fp, file_get_contents($part));
                    unlink($part);
                }
            }
            fclose($fp);

            $this->_json(['status' => 'done', 'filename' => $name]);
        }

        $this->_json(['status' => 'in_progress', 'received' => $received, 'total' => $totalChunks]);
    }
    /* ================== UPDATE ================== */
    public function update()
    {
        $uuid           = $this->input->post('uuid');
        $type         = $this->input->post('TypeMedia', true);
        $description  = $this->input->post('Description', true);
        $hiddenMedia  = $this->input->post('HiddenMedia');

        $media = $hiddenMedia;

        if ( ($type == 'image' || $type == 'video') && !empty($_FILES['Media']['name']) ) {

            $config = [
                'upload_path'   => './attachments/gallery/',
                'allowed_types' => ($type == 'image') ? 'jpg|jpeg|png|gif' : 'mp4|avi|mov|mkv',
                'encrypt_name'  => true
            ];

            $this->upload->initialize($config);

            if ($this->upload->do_upload('Media')) {

                /* supprimer ancien fichier */
                if (!empty($hiddenMedia) && file_exists('./attachments/gallery/'.$hiddenMedia)) {
                    unlink('./attachments/gallery/'.$hiddenMedia);
                }

                $media = $this->upload->data('file_name');
            }
        }

        if ($type == 'link') {
            $media = $this->input->post('Link', true);
        }

        $this->Model->update(
            'gallery',
            ['uuid' => $uuid],
            [
                'TypeMedia'   => $type,
                'Media'       => $media,
                'Description' => $description
            ]
        );

        $this->session->set_flashdata('sms',
            '<div class="alert alert-success">Média modifié avec succès.</div>'
        );

        redirect('galleries');
    }

    /* ================== DELETE ================== */
    public function delete()
    {
        $uuid = $this->input->post('uuid');

        $gallery = $this->Model->readOne('gallery', ['uuid'=>$uuid]);

        if ($gallery && $gallery->TypeMedia != 'link') {
            if (file_exists('./attachments/gallery/'.$gallery->Media)) {
                unlink('./attachments/gallery/'.$gallery->Media);
            }
        }

        $this->Model->delete('gallery', ['uuid'=>$uuid]);

        $this->session->set_flashdata('sms',
            '<div class="alert alert-success">Média supprimé avec succès.</div>'
        );

        redirect('galleries');
    }
}