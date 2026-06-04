<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Favicon -->
    <link rel="icon" href="<?= base_url('attachments/Other/' . $this->Model->get_setting('site_favicon', 'logo.png')) ?>" type="image/png">
    
    <!-- Google Fonts -->
    <link href="<?=base_url()?>assets/cdn/fonts/roboto.css" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="<?=base_url()?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?=base_url()?>assets/cdn/fontawesome/all.min.css">
    
    <!-- Plugins CSS -->
    <link href="<?=base_url()?>assets/admin/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/admin/plugins/simplebar/css/simplebar.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/admin/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/admin/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/admin/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/vendor/quill/quill.snow.css" rel="stylesheet">
    
    <!-- Loader -->
    <link href="<?=base_url()?>assets/admin/css/pace.min.css" rel="stylesheet"/>    
    <script src="<?=base_url()?>assets/admin/js/pace.min.js"></script>
    
    <!-- Main CSS -->
    <link href="<?=base_url()?>assets/admin/css/bootstrap-extended.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/admin/sass/app.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/admin/css/icons.css" rel="stylesheet">
    
    <!-- Additional Icons -->
    <link href="<?=base_url()?>assets/vendor/boxicons/css/boxicons.min.css" rel='stylesheet'>
    
    <!-- Theme CSS -->
    <link rel="stylesheet" href="<?=base_url()?>assets/admin/sass/dark-theme.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/admin/sass/semi-dark.css">
    <link rel="stylesheet" href="<?=base_url()?>assets/admin/sass/bordered-theme.css">
    
    <!-- Custom CSS (si vous avez un fichier personnalisé) -->
    <!-- <link href="<?=base_url()?>assets/admin/css/custom.css" rel="stylesheet"> -->
    
    
    <!-- Title -->
    <title><?= isset($page_title) ? e($page_title) . ' — ' . e($this->Model->get_setting('site_name', 'AbeLab')) : e($this->Model->get_setting('site_name', 'Admin Dashboard')) ?></title>
    
    <!-- Additional Meta Tags -->
    <meta name="description" content="<?= isset($page_description) ? $page_description : 'Admin Dashboard' ?>">
    <meta name="author" content="<?= e($this->Model->get_setting('site_name', 'AbeLab')) ?>">
    
    <!-- Open Graph Tags (pour les réseaux sociaux) -->
    <meta property="og:title" content="<?= isset($page_title) ? $page_title : 'Admin Dashboard' ?>">
    <meta property="og:description" content="<?= isset($page_description) ? $page_description : 'Admin Dashboard' ?>">
    <meta property="og:image" content="<?= base_url($this->Model->get_setting('site_og_image', 'assets/admin/images/og-image.jpg')) ?>">
    <meta property="og:url" content="<?= current_url() ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= isset($page_title) ? $page_title : 'Admin Dashboard' ?>">
    <meta name="twitter:description" content="<?= isset($page_description) ? $page_description : 'Admin Dashboard' ?>">
    <meta name="twitter:image" content="<?= base_url($this->Model->get_setting('site_twitter_image', 'assets/admin/images/twitter-image.jpg')) ?>">
    
    <!-- Additional Styles -->
    <style>
        /* Custom styles can go here */
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fc;
        }
        
        /* Smooth transitions */
        * {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>
</head>
	<!--wrapper-->
	<div class="wrapper">