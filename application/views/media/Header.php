<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
      
      
      <link rel="icon" href="<?= base_url('attachments/Other/' . $this->Model->get_setting('site_favicon', 'logo.png')) ?>" type="image/png">
      
    <!-- Bootstrap CSS -->
    <link href="<?=base_url()?>assets/admin/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/admin/css/bootstrap-extended.css" rel="stylesheet">

    <link href="<?=base_url()?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?=base_url()?>assets/cdn/fontawesome/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts -->
    <link href="<?=base_url()?>assets/cdn/fonts/roboto-condensed.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/cdn/fonts/poppins.css" rel="stylesheet">

    <!-- Icons -->
    <link href="<?=base_url()?>assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

    <!-- Slick CSS -->
    <link rel="stylesheet" type="text/css" 
          href="<?=base_url()?>assets/cdn/slick/slick.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="<?=base_url()?>assets/cdn/slick/slick-theme.min.css"/>

    <!-- Ton style personnel -->
    <link href="<?=base_url()?>assets/media/css/style.css" rel="stylesheet">

    <title><?= e($this->Model->get_setting('site_name', 'AbeLab')) ?></title>
</head>
<body>
    <div class="wrapper">