
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!--favicon-->
<link rel="icon" href="<?= base_url('attachments/Other/' . $this->Model->get_setting('site_favicon', 'assets/admin/images/favicon-32x32.png')) ?>" type="image/png">
<!--plugins-->
<link href="<?=base_url()?>assets/admin/plugins/simplebar/css/simplebar.css" rel="stylesheet">
<link href="<?=base_url()?>assets/admin/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
<link href="<?=base_url()?>assets/admin/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet">
<!-- loader-->
<link href="<?=base_url()?>assets/admin/css/pace.min.css" rel="stylesheet">
<script src="<?=base_url()?>assets/admin/js/pace.min.js"></script>
<!-- Bootstrap CSS -->
<link href="<?=base_url()?>assets/admin/css/bootstrap.min.css" rel="stylesheet">
<link href="<?=base_url()?>assets/admin/css/bootstrap-extended.css" rel="stylesheet">
<link href="<?=base_url()?>assets/cdn/fonts/roboto.css" rel="stylesheet">
<link href="<?=base_url()?>assets/admin/sass/app.css" rel="stylesheet">
<link rel="stylesheet" href="<?=base_url()?>assets/admin/sass/dark-theme.css">
<link href="<?=base_url()?>assets/admin/css/icons.css" rel="stylesheet">
<title><?= e($this->Model->get_setting('site_name', 'AbeLab')) ?></title>
</head>

<body class="">
<!--wrapper-->
<div class="wrapper">
<div class="section-authentication-cover">
<div class="">
<div class="row g-0">

	<div class="col-12 col-xl-7 col-xxl-8 auth-cover-left align-items-center justify-content-center d-none d-xl-flex">

        <div class="card shadow-none bg-transparent shadow-none rounded-0 mb-0" style="">
			<div class="card-body">
                 <img src="<?= base_url($this->Model->get_setting('login_cover', 'assets/admin/images/login-images/login-cover.svg')) ?>" class="img-fluid auth-img-cover-login" style="max-height: 500px;" width="600" alt=""/>
			</div>
		</div>
		
	</div>

	<div class="col-12 col-xl-5 col-xxl-4 auth-cover-right align-items-center justify-content-center">
		<div class="card rounded-0 m-3 shadow-none bg-transparent mb-0">
			<div class="card-body p-sm-5">
				<div class="">
					<div class="mb-3 text-center">
						<img src="<?= base_url('attachments/Other/' . $this->Model->get_setting('site_logo', 'logo.png')) ?>" width="60" alt="">
					</div>
					<div class="text-center mb-4">
						<h5 class=""><?= e($this->Model->get_setting('site_name', 'AbeLab')) ?></h5>
						<p class="mb-0">Please log in to your account</p>
					</div>
					<?php if (!empty($this->session->flashdata('sms'))) {
                            echo $this->session->flashdata('sms');
                        } ?>
					<div class="form-body">
						<form class="row g-3" action="<?=base_url('Admin/do_login')?>" method="POST">
							<div class="col-12">
								<label for="inputEmailAddress" class="form-label">Email</label>
								<input type="text" class="form-control" id="inputEmailAddress" name="Username" placeholder="jhon@example.com">
							</div>
							<div class="col-12">
								<label for="inputChoosePassword" class="form-label">Password</label>
								<div class="input-group" id="show_hide_password">
									<input type="password" class="form-control border-end-0" id="inputChoosePassword"  name="password" placeholder="Enter Password" > <a href="javascript:;" class="input-group-text bg-transparent"><i class="bx bx-hide"></i></a>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-check form-switch">
									<input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked">
									<label class="form-check-label" for="flexSwitchCheckChecked">Remember Me</label>
								</div>
							</div>
							<div class="col-md-6 text-end">	<a href="auth-cover-forgot-password.html">Forgot Password ?</a>
							</div>
							<div class="col-12">
								<div class="d-grid">
									<button type="submit" class="btn btn-primary">Sign in</button>
								</div>
							</div>
							<div class="col-12">
								<div class="text-center">
									<p class="mb-0">Don't have an account yet? <a href="javascript:;">Contact Admin</a>
									</p>
								</div>
							</div>
						</form>
					</div>

				</div>
			</div>
		</div>
	</div>

</div>
<!--end row-->
</div>
</div>
</div>
<!--end wrapper-->
<!-- Bootstrap JS -->
<script src="<?=base_url()?>assets/admin/js/bootstrap.bundle.min.js"></script>
<!--plugins-->
<script src="<?=base_url()?>assets/admin/js/jquery.min.js"></script>
<script src="<?=base_url()?>assets/admin/plugins/simplebar/js/simplebar.min.js"></script>
<script src="<?=base_url()?>assets/admin/plugins/metismenu/js/metisMenu.min.js"></script>
<script src="<?=base_url()?>assets/admin/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
<!--Password show & hide js -->
<script>
$(document).ready(function () {
$("#show_hide_password a").on('click', function (event) {
event.preventDefault();
if ($('#show_hide_password input').attr("type") == "text") {
	$('#show_hide_password input').attr('type', 'password');
	$('#show_hide_password i').addClass("bx-hide");
	$('#show_hide_password i').removeClass("bx-show");
} else if ($('#show_hide_password input').attr("type") == "password") {
	$('#show_hide_password input').attr('type', 'text');
	$('#show_hide_password i').removeClass("bx-hide");
	$('#show_hide_password i').addClass("bx-show");
}
});
});
</script>
<!--app JS-->
<script src="<?=base_url()?>assets/admin/js/app.js"></script>
</body>
</html>