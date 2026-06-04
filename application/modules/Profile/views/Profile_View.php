<?php include VIEWPATH.'includes/Header.php' ;?>
<?php include VIEWPATH.'includes/Sidebar.php' ;?>

<div class="page-wrapper">
<div class="page-content">
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
<div class="breadcrumb-title pe-3">Admin</div>
<div class="ps-3">
<nav aria-label="breadcrumb">
<ol class="breadcrumb mb-0 p-0">
    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
    <li class="breadcrumb-item active" aria-current="page">Mon Profil</li>
</ol>
</nav>
</div>
</div>

<?php if ($this->session->flashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= e($this->session->flashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($this->session->flashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= e($this->session->flashdata('error')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <img src="<?= base_url($user['image'] ? 'attachments/Users/' . $user['image'] : $this->Model->get_setting('default_avatar', 'assets/admin/images/user.png')) ?>"
                         class="rounded-circle shadow" width="150" height="150"
                         style="object-fit: cover;"
                         alt="Avatar">
                </div>
                <h5 class="mb-1"><?= e($user['firstName'] . ' ' . $user['lastName']) ?></h5>
                <p class="text-muted small mb-3"><?= e($group['group_name'] ?? '') ?></p>

                <form action="<?= base_url('Profile/update_avatar') ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="file" name="avatar" class="form-control form-control-sm" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Changer l'avatar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Informations personnelles</h5></div>
            <div class="card-body">
                <form action="<?= base_url('Profile/update_info') ?>" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Prénom</label>
                            <input type="text" name="firstName" class="form-control"
                                   value="<?= e($user['firstName']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nom</label>
                            <input type="text" name="lastName" class="form-control"
                                   value="<?= e($user['lastName']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= e($user['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control"
                                   value="<?= e($user['telephone']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nom d'utilisateur</label>
                            <input type="text" class="form-control" value="<?= e($user['username']) ?>" readonly>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Changer le mot de passe</h5></div>
            <div class="card-body">
                <form action="<?= base_url('Profile/update_password') ?>" method="POST">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Mot de passe actuel</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nouveau mot de passe</label>
                            <input type="password" name="new_password" class="form-control" required minlength="6">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Confirmer</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-warning">Modifier le mot de passe</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</div>
</div>

<?php include VIEWPATH.'includes/Footer.php' ;?>
