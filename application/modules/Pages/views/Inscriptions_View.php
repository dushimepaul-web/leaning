
<?php include VIEWPATH . 'media/Header.php'; ?>
<?php include VIEWPATH . 'media/navbar.php'; ?>


<?php
$tc = [];

if (!empty($timetable_course) && is_array($timetable_course)) {
    $tc = $timetable_course;
} elseif (!empty($timetable_courses) && is_array($timetable_courses)) {
    $tc = $timetable_courses[0];
}

$cd = !empty($course_details) && is_array($course_details) ? $course_details : [];
?>



<section class="form-section py-5">
  <div class="container-fluid px-3 px-md-4 px-lg-5">
    <div class="row g-4">




<div class="col-12 col-lg-8">

  <a class="btn btn-danger mb-4" href="<?= base_url(); ?>">← Maison</a>

  <h2 class="mb-4">Inscrivez-vous pour le cours</h2>

  <!-- INFOS COURS -->
  <div class="alert alert-info">
    <h5 class="mb-3">Détails de l'inscription</h5>
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <p><strong>Cours :</strong> <?= htmlspecialchars($tc['nom_course'] ?? $cd['nom_course'] ?? 'Non spécifié'); ?></p>
        <p><strong>Durée :</strong>
          <?php
          if (!empty($tc['date_debut']) && !empty($tc['date_defin'])) {
              echo (new DateTime($tc['date_debut']))
                  ->diff(new DateTime($tc['date_defin']))->days . ' jours';
          } else {
              echo 'Non spécifié';
          }
          ?>
        </p>
      </div>
      <div class="col-12 col-md-6">
        <p><strong>Prix :</strong> <?= htmlspecialchars($tc['price'] ?? 'Non spécifié'); ?> €</p>
        <p><strong>Localisation :</strong> <?= htmlspecialchars($tc['localisation'] ?? 'Non spécifié'); ?></p>
      </div>
    </div>
  </div>



<form action="<?= base_url('Pages/Inscriptions/save_inscription'); ?>" method="POST">

<input type="hidden" name="uuid_course" value="<?= e($tc['course_uuid'] ?? $cd['uuid'] ?? '')?>">
<input type="hidden" name="uuid_timetable_course" value="<?= e($tc['uuid'] ?? '')?>">

<div class="row g-3">

  <div class="col-12 col-md-6">
    <label class="form-label">Nom complet *</label>
    <input type="text" name="student_fullname" class="form-control" required>
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label">Email *</label>
    <input type="email" name="student_email" class="form-control" required>
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label">Téléphone *</label>
    <input type="tel" name="student_phone" class="form-control" required>
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label">Adresse *</label>
    <input type="text" name="student_address" class="form-control" required>
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label">Entreprise</label>
    <input type="text" name="company" class="form-control">
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label">Nationalité *</label>
    <select name="your_country" class="form-select" required>
      <option value="">Sélectionner</option>
      <option>Burundi</option>
      <option>Rwanda</option>
      <option>RDC</option>
      <option>Tanzanie</option>
    </select>
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label">Mode de présence *</label>
    <select name="id_attendance" class="form-select" required>
      <?php foreach ($attendance_modes as $m): ?>
        <option value="<?= e($m['id_attendance'])?>">
          <?= htmlspecialchars($m['nom_attendance']); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label">Mode de paiement *</label>
    <div class="d-flex flex-wrap gap-3">
      <?php foreach ($mode_payements as $m): ?>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="id_mode_payement"
                 value="<?= e($m['id_mode_payement'])?>" required>
          <label class="form-check-label"><?= htmlspecialchars($m['description']); ?></label>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label">Facturation *</label>
    <select name="invoice_type" class="form-select" required>
      <option value="individual">Individuelle</option>
      <option value="company">Entreprise</option>
    </select>
  </div>

  <div class="col-12 col-md-6">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" required>
      <label class="form-check-label">
        J’accepte les conditions et la politique de confidentialité
      </label>
    </div>
  </div>

  <div class="col-12 d-flex flex-column flex-md-row gap-3 mt-3">
    <button type="submit" style="width: 50px;" class="btn btn-success w-50">
      <i class="fas fa-paper-plane me-2"></i>Soumettre
    </button>
    <button type="reset" class="btn btn-danger w-50">
      Réinitialiser
    </button>
  </div>

</div>
</form>
</div>



<div class="col-12 col-lg-4">
  <div class="card shadow-sm sticky-top" style="top:100px">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Résumé</h5>
    </div>
    <div class="card-body">
      <ul class="list-unstyled">
        <li><strong>Cours :</strong> <?= htmlspecialchars($tc['nom_course'] ?? ''); ?></li>
        <li><strong>Durée :</strong> <?= !empty($tc['date_debut']) ? 'Voir planning' : ''; ?></li>
        <li><strong>Prix :</strong> <span class="text-success fw-bold"><?= htmlspecialchars($tc['price'] ?? ''); ?> €</span></li>
        <li><strong>Lieu :</strong> <?= htmlspecialchars($tc['localisation'] ?? ''); ?></li>
      </ul>
      <hr>
      <p class="small text-muted">
        Après soumission, un email de confirmation vous sera envoyé.
      </p>
    </div>
  </div>
</div>



</div>
  </div>
</section>




<?php include VIEWPATH . 'media/Footer.php'; ?>

